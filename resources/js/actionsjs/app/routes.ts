import Connection from '../helpers/connection'
import route from '@/../../vendor/tightenco/ziggy/src/js'

const ziggyConfig = await (new Connection('actions/routes-config'))._get({ namespace: 'blazervel-actionsjs' })

export default function (name: string, params: object, absolute: boolean) {
  return route(name, params, absolute, ziggyConfig)
}