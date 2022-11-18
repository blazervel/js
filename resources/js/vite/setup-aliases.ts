export default (config, basePath, packagePath): object => {

  const vendorPath = `${basePath}/vendor`,
        packagesPath = packagePath.replace('blazervel/blazervel', 'blazervel')

  config.server          = config.server || {}
  config.server.fs       = config.server.fs || {}
  config.server.fs.allow = config.server.fs.allow || []

  config.server.fs.allow.concat([
    basePath,
    packagePath
  ])

  config.resolve = config.resolve || {}
  
  config.resolve.alias = {
    ...(config.resolve.alias || {}),
    '@tightenco/ziggy': `${vendorPath}/tightenco/ziggy/src/js`,
    '@blazervel': `${packagesPath}/blazervel/resources/js`,
    '@pckg': `${basePath}/node_modules`
  }

  return config
}