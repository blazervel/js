import Builder from './builder'
import Connection from '../helpers/connection'

export default class Model {

    constructor(attributes = {}) {

        this.bootIfNotBooted();

        // Create non-enumerable properties for metadata
        Object.defineProperties(this, {
            original: {
                writable: true
            },
            exists: {
                writable: true
            }
        });

        this.exists = false;

        this.fill(attributes);

        this._syncOriginal();
    }

    bootIfNotBooted() {
        if ( ! this.constructor.booted) {
            this.constructor.boot();
        }
    }

    static boot() {
        if ( ! Model.booted) {
            this._bootBaseModel();
        }

        this._bootSelf();
    }

    static _bootSelf() {

        this.booted = true;

        this.dates = (this.dates || []);

        this.relations = (this.relations || {});

        this.scopes = this.scopes || [];

        // Create connection if one doesn't already exist
        if ( ! this.prototype.connection) {
            this.prototype.connection = new Connection(`models/${this.endpoint}`);
        }

        this._bootScopes(this.scopes);
    }

    static _bootBaseModel() {

        this.events = {};

        /*
         * Laravel uses the __call() and __callStatic() magic methods
         * to provide easy access to a new query builder instance from
         * the model. The proxies feature of ES6 would allow us to do
         * something similar here, but the browser support isn't there
         * yet. Instead, we'll programmatically add our own proxy functions
         * for every method we want to support.
         *
         * While we *could* add the proxy methods to the base Model class
         * definition, adding at runtime reduces the footprint of our
         * library and should be easier to maintain.
         */
        let builder = Object.getPrototypeOf(new Builder);

        Object.getOwnPropertyNames(builder)
            .filter(function (name) {
                return (
                    name.charAt(0) !== '_'
                    && name !== 'constructor'
                    && typeof builder[name] === 'function'
                );
            })
            .forEach(function (methodName) {
                // Add to the prototype to handle instance calls
                addMethod(Model.prototype, methodName, function () {
                    let builder = this.newQuery();
                    return builder[methodName].apply(builder, arguments);
                });

                // Add to the Model class directly to handle static calls
                addMethod(Model, methodName, function () {
                    let builder = this.query();
                    return builder[methodName].apply(builder, arguments);
                });
            });
    }

    static _bootScopes(scopes) {
        scopes.forEach(function (scope) {

            // Add to the prototype for access by model instances
            addMethod(this, scope, function (...args) {
                return this.newQuery().scope(scope, args);
            });

            // Add to the class for static access
            addMethod(this.constructor, scope, function (...args) {
                return this.query().scope(scope, args);
            });

        }, this.prototype);
    }

    fill(attributes) {
        for (let key in attributes) {
            this.setAttribute(key, attributes[key]);
        }
        return this;
    }

    _syncOriginal() {
        /**
         * The original attributes of this instance.
         *
         * @protected
         * @type {Object}
         */
        this.original = this.getAttributes();
    }

    getAttribute(key) {
        return this[key];
    }

    setAttribute(key, value) {
        if (value !== null && this.isDate(key)) {
            value = new Date(value);
            value.toJSON = asUnixTimestamp;
        }

        if (this._isRelation(key)) {
            value = this._makeRelated(key, value);
        }

        this[key] = value;
        return this;
    }

    getAttributes() {
        let cloned = Object.assign({}, this);

        for (var prop in cloned) {
            // We've only have a shallow clone at the moment,
            // so let's copy the dates separately.
            if (this.isDate(prop)) {
                cloned[prop] = new Date(this[prop]);
                cloned[prop].toJSON = asUnixTimestamp;
            }

            if (this._isRelation(prop)) {
                delete cloned[prop];
            }
        }

        return cloned;
    }

    getDirty() {
        let attributes = this.getAttributes();

        for (let prop in attributes) {

            if (
                typeof this.original[prop] !== 'undefined'
                &&
                (
                    (this.original[prop] === null && attributes[prop] === null) 
                    || (this.original[prop] !== null && attributes[prop] !== null && this.original[prop].valueOf() === attributes[prop].valueOf())
                )
            ) {
                delete attributes[prop];
            }
        }

        return attributes;
    }

    getKey() {
        return this[this.getKeyName()];
    }

    getKeyName() {
        return this.constructor.primaryKey || 'id';
    }

    isDate(column) {
        return this.constructor
            .dates
            .concat('created_at', 'updated_at', 'deleted_at')
            .indexOf(column) > -1;
    }

    _isRelation(attribute) {
        return Object.keys(this.constructor.relations).indexOf(attribute) > -1;
    }

    static query() {
        return (new this()).newQuery();
    }

    newQuery() {
        return new Builder(this.connection, this);
    }

    newInstance(attributes = {}, exists = false) {
        let instance = new this.constructor(attributes);
        instance.exists = exists;
        return instance;
    }

    hydrate(items) {
        return items.map(attributes => this.newInstance(attributes, true));
    }

    static create(attributes = {}) {
        let instance = new this(attributes);
        return instance.save().then(() => instance);
    }

    save() {
        let request;

        if (this.triggerEvent('saving') === false) {
            return Promise.reject('saving.cancelled');
        }

        if (this.exists) {
            request = this._performUpdate();
        } else {
            request = this._performInsert();
        }

        return request.then(newAttributes => {
            this.exists = true;
            this.triggerEvent('saved', false);
            return this.fill(newAttributes) && this._syncOriginal();
        });
    }

    _performInsert() {
        if (this.triggerEvent('creating') === false) {
            return Promise.reject('creating.cancelled');
        }

        return this.newQuery()
            .insert(this.getAttributes())
            .then(response => {
                this.triggerEvent('created', false);
                return response;
            });
    }

    _performUpdate() {
        if (this.triggerEvent('updating') === false) {
            return Promise.reject('updating.cancelled');
        }

        return this.connection
            .update(this.getKey(), this.getDirty())
            .then(response => {
                this.triggerEvent('updated', false);
                return response;
            });
    }

    update(attributes) {
        if ( ! this.exists) { // provides shortcut to an update on the query builder
            return this.newQuery().update(attributes);
        }

        this.fill(attributes);

        return this.save();
    }

    delete() {
        if (this.triggerEvent('deleting') === false) {
            return Promise.reject('deleting.cancelled');
        }

        return this.connection
            .delete(this.getKey())
            .then(success => {
                if (success) {
                    this.exists = false;
                }

                this.triggerEvent('deleted', false);
                return success;
            });
    }

    static all(columns) {
        return (new this()).newQuery().get(columns);
    }

    load(...relations) {
        return this.newQuery()
            .with(relations)
            .first()
            .then(attributes => {

                // Fill in the relations, leave everything else in tact
                relations.forEach(relatedName => {
                    this.setAttribute(relatedName, attributes[relatedName]);
                });

                return this;
            });
    }

    _makeRelated(name, attributes) {
        let relatedClass = this._getRelatedClass(this.constructor.relations[name]);
        let related = new relatedClass;

        if (Array.isArray(attributes)) {
            return related.hydrate(attributes);
        }

        return related.fill(attributes);
    }

    _getRelatedClass(name) {
        throw new Error(`Cannot make related class [${name}]`);
    }

    static setContainer(container) {
        this.prototype._getRelatedClass = (name) => container.make(name);
    }

    static creating(callback) {
        this.registerEventHandler('creating', callback);
    }

    static created(callback) {
        this.registerEventHandler('created', callback);
    }

    static updating(callback) {
        this.registerEventHandler('updating', callback);
    }

    static updated(callback) {
        this.registerEventHandler('updated', callback);
    }

    static saving(callback) {
        this.registerEventHandler('saving', callback);
    }

    static saved(callback) {
        this.registerEventHandler('saved', callback);
    }

    static deleting(callback) {
        this.registerEventHandler('deleting', callback);
    }

    static deleted(callback) {
        this.registerEventHandler('deleted', callback);
    }

    static registerEventHandler(name, handler) {
        if ( ! this.events[name]) this.events[name] = [];
        this.events[name].push(handler);
    }

    triggerEvent(name, halt = true) {
        let events = this.constructor.events;

        for (let i = 0, length = (events[name] || []).length; i < length; ++i) {
            let response = events[name][i](this);
            if (halt && typeof response !== 'undefined') {
                return response;
            }
        }
    }
}

function addMethod(obj, name, method, force)
{
    if (typeof obj[name] !== 'undefined' && ! force) {
        return obj;
    }

    return Object.defineProperty(obj, name, {
        value: method
    });
}

function asUnixTimestamp()
{
    return Math.round(this.valueOf() / 1000);
}
