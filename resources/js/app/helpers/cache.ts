import { setupCache, serializeQuery } from '@pckg/axios-cache-adapter'
import { md5 } from './utils'

export const cacheKey = ({ url, params = null }: {url: string, params?: object|null}) => {
  let orderedParams = {}

  if (params) {
    Object.keys(params).sort().map(key => {
      orderedParams[key] = params[key]
    })
  }

  return `${url}${serializeQuery(orderedParams)}`
}

export const cache = setupCache({
  maxAge: 15 * 60 * 1000,
  key: cacheKey,
  // invalidate: async (cfg, req) => {
  //   // const method = req.method.toLowerCase()
  //   // if (method !== 'get') {
  //   //   await cfg.store.removeItem(cfg.uuid)
  //   // }
  //   // console.log(cfg, req)
  // },
  // exclude: {
  //   query: false,
  //   paths: [],
  //   filter: null, // (): boolean => {},
  //   methods: ['post', 'patch', 'put', 'delete']
  // },
  // clearOnStale: false,
  // clearOnError: true,
})

export default cache