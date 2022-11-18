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
    '@blazervel-ui': `${packagesPath}/ui/resources/js`,
    '@blazervel':    `${packagesPath}/blazervel/resources/js`,
    '@vendor':       vendorPath,
    '@pckg':         `${basePath}/node_modules`,
    '@':             `${basePath}/resources/js`,
  }

  return config
}