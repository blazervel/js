export default (Config): object => {

  const basePath = Config.basePath,
        vendorPath = `${basePath}/vendor`,
        packagesPath = Config.packagePath.replace('blazervel/blazervel', 'blazervel')

  Config.merge('server.fs.allow', [
    basePath,
    packagesPath
  ])

  Config.merge('resolve.alias', {
    '@blazervel-ui': `${packagesPath}/ui/resources/js`,
    '@blazervel':    `${packagesPath}/blazervel/resources/js`,
    '@vendor':       vendorPath,
    '@pckg':         `${basePath}/node_modules`,
    '@':             `${basePath}/resources/js`,
  })

  if (packagesPath !== `${vendorPath}/blazervel`) {
    if (Config.get.preserveSymlinks === false) {

      Config.log(
        'Blazervel package is being symlinked to (not using ./vendor version), ',
        'but vite config has set \'preserveSymlinks\' to false',
        '...you may run into issues with Blazervel'
      )

    } else {

      Config.config.preserveSymlinks = true
      
    }
  }

  return Config.config
}