import { UserConfig, searchForWorkspaceRoot } from 'vite'
import { cwd } from 'node:process'
import { exec } from 'node:child_process'
import path from 'path'

interface Props {

}

export default (options: Props) => ({

  name: '@blazervel/ql',
  
  config: (config: UserConfig, { mode, command }: { mode: string, command: string }) => {

    if (!['build', 'serve'].includes(command)) {
      return config
    }

    const basePath = searchForWorkspaceRoot(cwd()),
          packagePath = path.resolve(__dirname)

    // Generate static config/schema files
    // exec('php artisan blazerveljs:build-config')

    // Alias blazerveljs utilities
    config.resolve = {
      alias: {
        ...(config.resolve?.alias || {}),
        '@tightenco/ziggy': `${basePath}/vendor/tightenco/ziggy/src/js`,
        '@blazervel/ql': `${packagePath}/resources/js`,
        '@pckg': path.resolve('./node_modules'),
      }
    }
    
    // Allow importing from package directory in ./vendor (or ../packages when in development)
    config.server = {
      fs: {
        allow: [
          ...(config.server?.fs?.allow || []),
          path.relative(basePath, packagePath),
          basePath
        ]
      }
    }

    // Preserve symlinks (e.g. when using ../packages folder in development)
    if (mode === 'development') {
      config.preserveSymlinks = true
    }

    return config
  }
})