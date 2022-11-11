export * from './dates'
export * from './resolve-component'

export const snake = camelString => (
  camelString
      .replace(/[A-Z]/g, char => ` ${char.toLowerCase()}`)
      .trim()
      .split(' ')
      .join('-')
)