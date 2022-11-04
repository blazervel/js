import route from '@vendor/tightenco/ziggy/src/js'
import config from './config'

export default async function (name: string, params: object, absolute: boolean) {
  return route(
    name,
    params,
    absolute,
    await config.routes
  )
}