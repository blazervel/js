import ConfigUtil from './config-util'
import { BlazerelConfigProps, BlazervelLoaderProps } from './types'

export default (options: BlazervelLoaderProps) => (blazervel: BlazerelConfigProps = {}) => ({

  name: 'blazervel',
  
  config: (config: object, { mode, command }: { mode: string, command: string }) => {

    if (!['build', 'serve'].includes(command)) {
      return config
    }

    const Config = new ConfigUtil(config, mode, options)

    // Add default aliases (e.g. alias @ -> ./resources/js)
    Config.setupAliases()

    if (mode !== 'development') {
      return config
    }

    // Configure dev server (e.g. valet https, HMR, etc.)
    Config.setupDevServer()
    
    return Config.config
  }
})