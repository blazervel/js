import axios from '@deps/axios'
import cache from './cache'

/**
 * Fetch/send data via Axios using cache adapter
 */
export default (url, options) => {

    const instance = axios.create({
        adapter: cache.adapter
    })

    return instance({
        url,
        ...options
    })
}