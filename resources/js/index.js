import blazervel from './actionsjs/app/blazervel'
import routeMethod from './actionsjs/app/routes'
import translateMethod from './actionsjs/app/translations'

export { default as config } from './actionsjs/app/config'

export const route  = routeMethod
export const lang   = translateMethod
export const __     = translateMethod

export default blazervel