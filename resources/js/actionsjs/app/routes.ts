import route from '@vendor/tightenco/ziggy/src/js'
import config from './config'

config.load()

const routesConfig = await config.routes

export default function (name: string, params: object, absolute: boolean) {
  return route(
    name,
    params,
    absolute,
    routesConfig
  )
}