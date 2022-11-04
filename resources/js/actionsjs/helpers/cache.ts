import { setupCache } from '@pckg/axios-cache-adapter'
import { md5 } from './utils'

export const cacheKey = (url: string, options: object|null) => {
  let orderedOptions = {}

  url = url.replace('/api/blazervel/', '')

  if (options !== null) {
    Object.keys(options).sort().map(key => {
      orderedOptions[key] = options[key]
    })
  }

  return md5(`${url}${JSON.stringify(orderedOptions)}`)
}

const cache = setupCache({
  maxAge: 15 * 60 * 1000,
  key: req => cacheKey(req.url, req.params || null),
  invalidate: null,
  exclude: {
    query: false,
    paths: [],
    filter: null, // (): boolean => {},
    methods: ['post', 'patch', 'put', 'delete']
  },
  clearOnStale: false,
  clearOnError: true,
})

export default cache