import route from '@vendor/tightenco/ziggy/src/js'

export default function (config: object) {
  return (name: string, params: object, absolute: boolean) => (
    route(name, params, absolute, config)
  )
}