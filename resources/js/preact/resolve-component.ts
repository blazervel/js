export default async function(name: string) {
  let page = componentLookup(name)

  if (page !== null) {
    page = typeof page === 'function'
      ? await page()
      : page

    if (page.default) {
      return page.default
    }

    return page
  }

  throw new Error(`Page not found: ${name}`)
}

const componentLookup = (name: string) => {

  let components,
      alias = ''

  if (name.includes('@blazervel')) {

    components = import.meta.glob('@blazervel/**/*.jsx')
    alias = '@blazervel'

  } else if (name.includes('@blazervel-ui')) {

    components = import.meta.glob('@blazervel-ui/**/*.jsx')
    alias = '@blazervel-ui'

  } else {

    components = import.meta.glob('@/**/*.jsx')
    alias = '@'

  }

  for (const path in components) {

    if (
      !path.endsWith(`${name.replace(alias, '').replace('.', '/')}.jsx`)
    ) continue

    return components[path]
  }

  return null
  
}