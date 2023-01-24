import localForage from '@pckg/localforage'
import { Request, Response } from '../types'

// LF uses localStorage in browsers with no IndexedDB or WebSQL support
// browser support: https://github.com/localForage/localForage/wiki/Supported-Browsers-Platforms

localForage.config({
  name: 'BlazervelQL',
  version: 1.0,
  storeName: 'blazervelql',
  description : 'Local storage for BlazervelQL query caching',
})

const defaultStore: Storage = localForage.createInstance({
  name: 'default'
})

const httpStore: Storage = localForage.createInstance({
  name: 'httpRequests'
})

export const storeKey = ({ url, params = null }: {url: string, params?: object|null}) => {
  let orderedParams = {}

  if (params) {
    Object.keys(params).sort().map(key => {
      orderedParams[key] = params[key]
    })
  }

  return `${url}${JSON.stringify(orderedParams)}`
}

export const findOrNew = async (req: Request, handleRequest: (req: Request) => Promise<Response>): Promise<Response> => {

  const key = storeKey(req),
        store = httpStore,
        fromStore = await store.getItem(key)

  let res: Response


  if (fromStore !== null) {

    res = JSON.parse(fromStore)

    // Make new req for next time
    handleRequest(req).then((res: Response) => (
      store.setItem(key, JSON.stringify(res)
    )))

    return res
  }

  return await handleRequest(req).then(res => {
    store.setItem(key, JSON.stringify(res))
    return res
  })
}

export const refesh = () => {

}

export default {
  default: defaultStore.store,
  http: httpStore.store
}