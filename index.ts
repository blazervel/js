import { loadEnv, UserConfig } from 'vite'
import { homedir } from 'os'
import path from 'path'
import tailwindcss from 'tailwindcss'
import setupAliases from './resources/js/vite/setup-aliases'
import setupDevServer from './resources/js/vite/setup-dev-server'

import { BlazerelConfigProps } from '../types'

export default (options: BlazerelConfigProps) => ({

  name: 'blazervel',
  
  config: (config: UserConfig, { mode, command }: { mode: string, command: string }) => {

    if (!['build', 'serve'].includes(command)) {
      return config
    }

    if (options.tailwind === true) {
      config.plugins = config.plugins || []
      
      config.plugins.push(
        tailwindcss()
      )
    }
  
    // Add default aliases (e.g. alias @ -> ./resources/js)
    config = setupAliases(
      config,
      path.resolve(__dirname),
      path.resolve('.')
    )

    if (mode !== 'development') {
      return config
    }
  
    // Configure dev server (e.g. valet https, HMR, etc.)
    config = setupDevServer(
      config,
      loadEnv('APP_URL', mode, ''),
      path.resolve(homedir(), '.config/valet/Certificates/')
    )
    
    return config
  }
})