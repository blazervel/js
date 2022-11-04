import Connection from '../helpers/connection'
import Resolver from '../helpers/resolver'

interface ConfigProps {
  localization: Promise<object>
  routes: Promise<object>
  load: Function
}

const configResolver = new Resolver

const config: ConfigProps = {

  localization: configResolver.create('localization'),

  routes: configResolver.create('routes'),

  async load() {
    const conn = new Connection('actions/config-app'),
          resolveWithConfigs = (
            await conn._get({ namespace: 'blazervel' }, { allowStaleCache: true })
          )

    configResolver.runAll(resolveWithConfigs)
  }

}

export default config