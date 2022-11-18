import { loadEnv, UserConfig } from 'vite'
import { homedir } from 'os'
import path from 'path'
import setupAliases from './resources/js/vite/setup-aliases'
import setupDevServer from './resources/js/vite/setup-dev-server'

export interface BlazerelConfigProps {
  certsPath?: string
}

export default (options: BlazerelConfigProps) => ({

  name: 'blazervel',
  
  config: (config: UserConfig, { mode, command }: { mode: string, command: string }) => {

    if (!['build', 'serve'].includes(command)) {
      return config
    }

    const basePath = process.cwd()
  
    // Add default aliases (e.g. alias @ -> ./resources/js)
    config = setupAliases(
      config,
      basePath,
      path.resolve(__dirname)
    )

    if (mode !== 'development') {
      return config
    }

    // Configure dev server (e.g. valet https, HMR, etc.)
    config = setupDevServer(
      config,
      loadEnv(mode, basePath, '').APP_URL || '',
      options.certsPath || path.resolve(homedir(), '.config/valet/Certificates/')
    )
    
    return config
  }
})