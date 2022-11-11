import Connection from './helpers/connection'

interface ConfigProps {
  localization: object
  routes: object
}

export async function loadConfig(): Promise<ConfigProps> {

  const conn = new Connection('actions/config-app'),
        response = (
          await conn._get({ namespace: 'blazervel' }, { allowStaleCache: true })
        )

  return {
    localization: await response.localization,
    routes: await response.routes
  }
}

export default (config: ConfigProps): ConfigProps => config