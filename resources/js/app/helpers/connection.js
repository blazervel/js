import { makeRequest, queueMakeRequest } from './make-request'

export default class Connection {

    constructor(endpoint) {

        if (!endpoint) {
            throw new Error('Missing endpoint');
        }

        this.endpoint = `/api/blazervel/${endpoint}`
    }

    async _get(data = {}, options = {}) {
        const response = await this.sendRequest(null, 'get', data, options)
        return await this.unwrap(response, data)
    }

    async _post(data = {}, options = {}) {
        const response = await this.sendRequest(null, 'post', data, options)
        return await this.unwrap(response, data)
    }

    create(data) {
        return this
            .sendRequest(null, 'post', data)
            .then(response => this.unwrap(response, data));
    }

    read(idOrQuery) {
        return this
            .sendRequest(idOrQuery)
            .then(response => this.unwrap(response, data));
    }

    update(idOrQuery, data) {
        return this
            .sendRequest(idOrQuery, 'put', data)
            .then(response => this.unwrap(response, data));
    }

    delete(idOrQuery) {
        return this
            .sendRequest(idOrQuery, 'delete')
            .then(response => response.status === 200);
    }

    sendRequest(urlSuffix, method, data = {}, options = {}) {
        return makeRequest( //queueMakeRequest(
            this.buildUrl(urlSuffix),
            this.buildOptions(method, data, options)
        )
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

    buildOptions(method, data = {}, { headers = {}, ...options }) {
        
        let request = {
            headers: {}
        }

        if (method) {
            request.method = method;
        }

        return {
            ...request,
            ...options
        };
    }

    unwrap(response, data) {
        return {
            status: response.status,
            ...response.data
        }
    }

    url(id, query) {
        if ( ! this.endpoint) {
            throw new Error('Endpoint must be set before using this connection')
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
}