import actionFetch from './fetch'

export default class Connection {

    constructor(endpoint) {

        if (!endpoint) {
            throw new Error('Missing endpoint');
        }

        const endpointPrefix = import.meta.env.VITE_ACTIONSJS_PREFIX || 'api/actionsjs'
        this.endpoint = `${endpointPrefix}/${endpoint}`
    }

    async _get(data = {}) {
        const response = await this.sendRequest(null, 'get', data)
        return await response.data
    }

    async _post(data = {}) {
        const response = await this.sendRequest(null, 'post', data)
        return await response.data
    }

    create(data) {
        return this
            .sendRequest(null, 'post', data)
            .then(response => this.unwrap(response));
    }

    read(idOrQuery) {
        return this
            .sendRequest(idOrQuery)
            .then(response => this.unwrap(response));
    }

    update(idOrQuery, data) {
        return this
            .sendRequest(idOrQuery, 'put', data)
            .then(response => this.unwrap(response));
    }

    delete(idOrQuery) {
        return this
            .sendRequest(idOrQuery, 'delete')
            .then(response => response.status === 200);
    }

    sendRequest(urlSuffix, method, data) {
        return actionFetch(
            this.buildUrl(urlSuffix),
            this.buildOptions(method, data)
        );
    }

    buildUrl(suffix, type) {

        let url = this.endpoint

        if (Array.isArray(suffix)) {

            if (suffix.length) {
                url += '?query='+JSON.stringify(suffix);
            }

        } else if (suffix) {

            url += '/'+suffix;

        }

        return url;
    }

    buildOptions(method, data, options = {}) {
        let request = {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': getCsrfToken()
            },
            withCredentials: true,
            ignoreCache: (options.ignoreCache || null) === true,
        };

        if (method) {
            request.method = method;
        }

        if (data) {
            if (method === 'get') {
                request.params = data
            } else {
                request.data = data
                request.headers['Content-Type'] = 'application/json'
            }
        }

        if (options.ignoreCache) {
            request.ignoreCache = options.ignoreCache
        }

        return {
            ...request,
            ...options
        };
    }

    unwrap(response) {
        return response.data;
    }

    url(id, query) {
        if ( ! this.endpoint) {
            throw new Error('Endpoint must be set before using this connection');
        }

        let url = this.endpoint;

        if (id) {
            url += '/'+id;
        }

        if (query && query.length) {
            return `${url}?query=${JSON.stringify(query)}`;
        }

        return url;
    }

    _makeInit(method, data, options) {

        let defaults = {
            credentials: 'same-origin', // to send our session cookie
            headers: {
                'Accept': 'application/json',
                'X-XSRF-TOKEN': getCsrfToken()
            }
        };

        if (method) {
            defaults.method = method;
        }

        if (data) {
            defaults.headers['Content-Type'] = 'application/json';
            defaults.data = data;
        }

        return Object.assign(defaults, options || {});
    }
}

function getCsrfToken()
{
    if (typeof document === 'undefined') return;

    return decodeURIComponent((document.cookie.match('(^|; )XSRF-TOKEN=([^;]*)') || 0)[2]);
}
