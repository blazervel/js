export function resolvePage(name: string) {

  let components,
      alias = '@'

  if (name.includes('@blazervel-ui')) {

    components = import.meta.glob('@blazervel-ui/**/Pages/**/*.*')
    alias = '@blazervel-ui'

  } else if (name.includes('@blazervel')) {

    components = import.meta.glob('@blazervel/**/Pages/**/*.*')
    alias = '@blazervel'

  } else {

    components = import.meta.glob('@/**/Pages/**/*.*')

  }

  const page = componentLookup(components, name.replace(alias, ''))

  if (page) {
    return page
  }

  throw new Error(`Page not found: ${name}`)
}

export function resolveComponent(name: string) {

  let components,
      alias = '@'

  if (name.includes('@blazervel-ui')) {

    components = import.meta.glob('@blazervel-ui/**/components/**/*.*')
    alias = '@blazervel-ui'

  } else if (name.includes('@blazervel')) {

    components = import.meta.glob('@blazervel/**/components/**/*.*')
    alias = '@blazervel'

  } else {

    components = import.meta.glob('@/**/components/**/*.*')

  }

  const page = componentLookup(components, name.replace(alias, ''))

  if (page) {
    return page
  }

  throw new Error(`Page not found: ${name}`)
}

const componentLookup = (components, name) => {
  
  let page = null

  for (const path in components) {

    const ext = path.split('.').reverse()[0]
    
    if (!path.endsWith(`${name}.${ext}`)) {
      continue
    }

    page = components[path]
    page = typeof page === 'function' ? page() : page

    break
  }

  return page
}