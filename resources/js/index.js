import App from './actionsjs/app/boot'
import routeMethod from './actionsjs/app/routes'
import translateMethod from './actionsjs/app/translations'

export const route = routeMethod
export const lang = translateMethod
export const __ = translateMethod

export default App