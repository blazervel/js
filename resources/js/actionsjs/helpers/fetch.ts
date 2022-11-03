import axios from '@deps/axios'
import cache, { cacheKey } from './cache'
import _ from 'lodash'

const debounceFetchWait: number = 500,
      maxQueueItems: number = 20

export const requestTimeout: number = 20 * 1000 // 20s

/**
 * Fetch/send data via Axios using cache adapter
 */
export const actionFetch = (url, options) => {

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

    request.then(response => {
        if (!response.request.fromCache) {
            return
        }

        cache.store.removeItem(
            cacheKey(url, options)
        )

        actionFetch(url, options)
    })

    return request
}

interface QueueItemProps {
    key: string
    url: string
    options: object
    resolve: Function
}

const getQueueItem = (key: string): QueueItemProps => queue.filter(q => q.key === key)[0]

let queue: Array<QueueItemProps> = [], queueReject: Function, queueResponse: Promise<object>

const debounceFetch = _.debounce(() => {

    // TODO: Throttle/chunk queue into groups of maxQueueItems

    actionFetch('api/blazervel/batch', {method: 'post', data: {queue: JSON.stringify(queue)}})
        .then(response => response.data.batch.map(response => {
            // Cache individual request responses
            cache.store.setItem(response.key, response)

            getQueueItem(response.key).resolve(response)
        }))
        .catch(error => queueReject(error))
        .then(() => queue = [])

}, debounceFetchWait)

export async function actionBatchFetch (url, options) {

    const key = cacheKey(url, options)

    // Return cached response (and queue refresh) if exists
    if (cache.store.getItem(key)) {
        return actionFetch(url, options)
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

export default actionBatchFetch