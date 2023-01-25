import localForage from '@pckg/localforage'
import { Request, Response } from '../types'

// LF uses localStorage in browsers with no IndexedDB or WebSQL support
// browser support: https://github.com/localForage/localForage/wiki/Supported-Browsers-Platforms

localForage.config({
  name: 'BlazervelJS',
  version: 1.0,
  storeName: 'blazerveljs',
  description : 'Local storage for BlazervelJS query caching',
})

const store: Storage = localForage.createInstance({ name: 'default' })

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

export default store