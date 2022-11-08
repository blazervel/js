import Connection from '../helpers/connection'
import Resolver from '../helpers/resolver'

interface ConfigProps {
  initialized: boolean
  localization: Promise<object>
  routes: Promise<object>
  load: Function
}

const configResolver = new Resolver

const config: ConfigProps = {

  initialized: false,

  localization: configResolver.create('localization'),

  routes: configResolver.create('routes'),

  async load() {

    if (this.initialized !== false) {
      return {
        localization: await this.localization,
        routes: await this.routes
      }
    }

    this.initialized = true

    const conn = new Connection('actions/config-app'),
          resolveWithConfigs = (
            await conn._get({ namespace: 'blazervel' }, { allowStaleCache: true })
          )

    return configResolver.runAll(
      await resolveWithConfigs
    )
  }

}

config.load()

export default config