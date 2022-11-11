import blazervel from './app/blazervel'
import routeMethod from './app/routes'
import translateMethod from './app/translations'

export { default as config } from './app/config'

export const route  = routeMethod
export const lang   = translateMethod
export const __     = translateMethod

export default blazervel