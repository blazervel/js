import route from '@tightenco/ziggy'

export default function (config: object) {
  return (name: string, params: object, absolute: boolean) => (
    route(name, params, absolute, config)
  )
}