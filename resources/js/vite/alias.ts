import { searchForWorkspaceRoot } from 'vite'
import { _env, _set, _merge } from './utils'
import path from 'path'

export default (config: object = {}) => {
	
	const vendorPath = (
		_env('BLAZERVELOPMENT', false, config.mode)
      ? '../packages'
      : './vendor'
	)

  // Support symlinks for aliasing vendor packages
	if (config.preserveSymlinks !== false) {
		config.preserveSymlinks = true
	}

  config = _merge(config, 'server.fs.allow', [
    path.resolve(`${vendorPath}/blazervel`),
    searchForWorkspaceRoot(process.cwd())
  ])

  config = _merge(config, 'resolve.alias', {
    '@blazervel-ui': path.resolve(`${vendorPath}/blazervel/ui/resources/js`),
    '@blazervel':    path.resolve(`${vendorPath}/blazervel/blazervel/resources/js`),
    '@vendor':       path.resolve('./vendor'),
    '@pckg':         path.resolve('./node_modules'),
    '@':             path.resolve('./resources/js'),
  })

  return config;
}