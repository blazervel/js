import { defineConfig as viteDefineConfig } from 'vite'
import aliasConfig from './vite/alias'
import devServerConfig from './vite/dev-server'
import { _env, _set, _merge } from './vite/utils'
import tailwindcss from 'tailwindcss'

export const defineConfig = (config: object = {}) => {

	const appEnv = _env('APP_ENV', null, config.mode)

	if (config.preserveSymlinks !== false) {
		config.preserveSymlinks = true
	}

	config = _merge(config, 'define', {
		__APP_NAME__: JSON.stringify(_env('APP_NAME', null, config.mode)),
		__APP_URL__: JSON.stringify(_env('APP_URL', config.host || null, config.mode)),
		__APP_ENV__: JSON.stringify(appEnv),
	})

	// Add tailwind to plugins
	// (even if not used - will not be added to output in that case)
	config = _merge(config, 'plugins', [tailwindcss()])

	// Add blazervel & project aliases
	config = aliasConfig(config)

	// Configure https and HMR host/port etc.
	if (['local', 'development'].includes(String(appEnv).toLowerCase())) {
		config = devServerConfig(config)
	}

	// Define vite config
	config = viteDefineConfig(config)

	return config
}
