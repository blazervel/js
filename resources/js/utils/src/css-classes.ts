export function mergeCssClasses(...props: Array<string|Array<string>> ) {
  
  let cssClasses: Array<string> = []

  props.filter(prop => ['object', 'array', 'string'].indexOf(typeof prop) >= 0).forEach(prop => {

    if (typeof prop == 'string') {
      prop = prop.split(' ')
    }

    cssClasses = cssClasses.concat(prop)

  })

  return cssClasses.join(' ').trim()

}

export function conditionalClassNames(...classes: Array<string|Array<string>>) {
  
  return classes.filter(Boolean).join(' ')
  
}