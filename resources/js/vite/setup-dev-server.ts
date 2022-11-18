import { UserConfig } from 'vite'
import fs from 'fs'

export default (config: UserConfig, appUrl: string, certsPath: string): object => {

	const hmrHost = appUrl.split('//').reverse()[0],
				host = config.server.hmr.host || config.server.host || config.host || hmrHost,
				port = config.server.port || 3025

	// Set HMR host
	config.server = config.server || {}
	config.server.hmr = config.server.hmr || {}

	config.server.hmr.host = hmrHost

	// Set dev server port & host
	config.server.port = config.server.port || port
  config.server.host = config.server.host || host

	// Configure certs

	// If key and cert are set at Config point
	// then it was by the dev (always trust the dev)
	if (config.server.https?.key || config.server.https?.cert) {
		return config
	}

	const key = `${certsPath}/${host}.key`,
				cert = `${certsPath}/${host}.crt`

	try {

		const creds = {
			key: fs.readFileSync(key),
			cert: fs.readFileSync(cert),
		}

		config.server.https = creds

	} catch {
		//
	}

	return config
}