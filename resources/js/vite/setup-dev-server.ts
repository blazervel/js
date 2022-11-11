import fs from 'fs'

export default (Config): object => {

	const hmrHost = Config.env.APP_URL.split('//').reverse()[0],
				host = Config.cascade(hmrHost, 'server.hmr.host', 'server.host', 'host'),
				port = Config.get('server.port', 3000)

	// Set HMR host
	Config.set('server.hmr.host', hmrHost)

	// Set dev server port & host
	Config.set('server.port', port)
  Config.set('server.host', host)

	// Configure certs

	// If key and cert are set at Config point
	// then it was by the dev (always trust the dev)
	if (Config.has('server.https.key', 'server.https.cert')) {
		return Config.config
	}

	const key = `${Config.certsPath}/${host}.key`,
				cert = `${Config.certsPath}/${host}.crt`

	try {

		const creds = {
			key: fs.readFileSync(key),
			cert: fs.readFileSync(cert),
		}

		Config.set('server.https', creds)

	} catch {

		// If the dev was hoping for https, then tell them what happened
		// config.server.host was set in last step
		if (Config.config.server?.https === true) {

			Config.log(
				'{theme}[Blazervel]',
				`No key/cert at {green}${valetDefaultCredsPath}`,
				`Have you run {blue}{underline}'valet secure'{theme} yet? 🤔`
			)
			
		}

	}

	return Config.config
}