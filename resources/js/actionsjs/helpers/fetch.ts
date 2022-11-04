import axios from '@pckg/axios'
import { debounce } from '@pckg/lodash'
import cache, { cacheKey } from './cache'

const debounceFetchWait: number = 500,
      maxQueueItems: number = 20

export const requestTimeout: number = 20 * 1000 // 20s

/**
 * Fetch/send data via Axios using cache adapter
 */
export const makeRequest = (url, options) => {

    const instance = axios.create({
        adapter: cache.adapter,
        timeout: requestTimeout
    })

    const request = instance({
        url,
        ...options,
        //onUploadProgress: (progressEvent) => {},
        //onDownloadProgress: (progressEvent) => {},
    })

    if (options.allowStaleCache !== true) {
        return request
    }

    request.then(response => {

        console.log(response.request)

        if (!response.request.fromCache) {
            return
        }

        // Remove item from store and queue a fresh response
        cache.store.removeItem(
            cacheKey(url, options)
        )

        queueMakeRequest(url, options)
    })

    return request
}

export const queueMakeRequest = async (url, options) => {

    const key = cacheKey(url, options)

    // Return cached response (and queue refresh) if exists
    if (cache.store.getItem(key)) {
        return await makeRequest(url, options)
    }

    // Add to queue
    queue.push({ key, url, options, resolve: () => {} })

    queueResponse = new Promise((resolve, reject) => {
        getQueueItem(key).resolve = resolve
        queueReject = reject
    })

    // Run queue
    debounceFetch()

    return await queueResponse
}

interface QueueItemProps {
    key: string
    url: string
    options: object
    resolve: Function
}

const getQueueItem = (key: string): QueueItemProps => queue.filter(q => q.key === key)[0]

let queue: Array<QueueItemProps> = [], queueReject: Function, queueResponse: Promise<object>

const debounceFetch = debounce(() => {
    console.log('gp')
    // TODO: Throttle/chunk queue into groups of maxQueueItems
    makeRequest('api/blazervel/batch', {method: 'post', data: {queue: JSON.stringify(queue)}})
        .then(response => response.data.batch.map(response => {
            // Cache individual request responses
            cache.store.setItem(response.key, response)

            getQueueItem(response.key).resolve(response)
            console.log(response)
        }))
        .catch(error => queueReject(error))
        .then(() => queue = [])

}, debounceFetchWait)