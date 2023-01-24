import axios from '@pckg/axios'
import { debounce } from '@pckg/lodash'
import { Request, RequestConfig, Response, QueueItem, AxiosRequestOptions } from '../types'
import store, { storeKey } from './cache'
import { findOrNew } from './store'

const debounceQueueMakeRequestWait: number = 500
//const maxQueueItems: number = 20

let queue: Array<QueueItem> = []

export const queueMakeRequest = (request: Request) => {

    const key = storeKey(request)

    let queueItem: QueueItem

    const queuedItemResolver = new Promise((resolve, reject) => (
        queue.push(queueItem = { key, request, resolve, reject })
    ))

    // Get item from store or queue request
    store.getItem(key)
        .then(res => queueItem.resolve(res))
        .catch(() => debounceQueueMakeRequest())

    return queuedItemResolver
}

export const makeRequest = (request: Request): Promise<Response> => {

    const key = storeKey(request),
          { url, config } = request,
          instance = axios.create(),
          options = getRequestOptions(config || {})
    
    const axiosRequest = instance({
        url,
        ...options,
        //onUploadProgress: (progressEvent) => {},
        //onDownloadProgress: (progressEvent) => {},
    })

    axiosRequest.catch(error => {
        console.log('error', error)
        store.removeItem(key)
    })

    return axiosRequest
}

const debounceQueueMakeRequest = debounce(() => (

    makeRequest({
        url: '/api/blazervel/batch',
        data: {
            queue: JSON.stringify(queue)
        },
        config: {
            method: 'post'
        }
    })
    .then(({ data }: Response) => (
        (data.batch || []).forEach(itemRes => {
            const { key, error = null } = itemRes,
                  queueItem = queue.filter(qi => qi.key === key)[0]

            if (error) {
                store.removeItem(key)
                queueItem.reject(error)
            } else {
                store.setItem(key, itemRes)
                queueItem.resolve(itemRes)
            }
        })
    ))
    .catch(error => (
        queue.forEach(({ key, reject }) => {
            store.removeItem(key)
            reject(error)
        })
    ))
    .then(() => queue = [])

), debounceQueueMakeRequestWait)

const getRequestOptions = ({ method = 'get', data, headers = {} }: RequestConfig): AxiosRequestOptions => {

    let options: AxiosRequestOptions = {
        method,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': csrfToken(),
            ...headers
        },
        withCredentials: true,
    }

    if (!data) return options

    if (method === 'get') {
        options.params = data
    } else {
        options.data = data
        headers['Content-Type'] = 'application/json'
    }

    return options
}

const csrfToken = () => {
    if (typeof document === 'undefined') {
        return
    }

    let xsrfToken
    
    xsrfToken = document.cookie.match('(^|; )XSRF-TOKEN=([^;]*)') || 0
    xsrfToken = xsrfToken[2]
    xsrfToken = decodeURIComponent(xsrfToken)
        
    return xsrfToken
}