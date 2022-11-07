import { defineConfig as viteDefineConfig } from 'vite'
import tailwindcss from 'tailwindcss'

import {
  envConfig,
  aliasConfig,
  devServerConfig,
  _envIs,
  _set,
  _merge
} from './resources/js/vite'

interface BlazerelConfigProps {
  tailwind: boolean|undefined
}

export const defineConfig = (
  config: object = {},
  blazervel: BlazerelConfigProps = { tailwind: true }
) => {

  const isDev = _envIs(['local', 'development'], config.mode)

	if (blazervel.tailwind) {
    config = _merge(config, 'plugins', [
      tailwindcss()
    ])
  }

  // Set default env variables
	config = envConfig(config)

  // Add default aliases (e.g. alias @ -> ./resources/js)
	config = aliasConfig(config)

  // Configure dev server (e.g. valet https, HMR, etc.)
	if (isDev) {
		config = devServerConfig(config)
	}

	// Pass to vite
	return viteDefineConfig(
    config
  )
}