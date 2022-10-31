import { setupCache } from '@deps/axios-cache-adapter'

const cache = setupCache({
  maxAge: 15 * 60 * 1000,
})

export default cache