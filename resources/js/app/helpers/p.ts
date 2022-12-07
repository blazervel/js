import { snake } from '../../utils'

/**
 * Reuseable proxy
 * @docs https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Proxy
 */
export default (
  data?: object,
  _get?: (name: string, stack: Array<string>) => void,
  _set?: (name: string, stack: Array<string>, value: any) => void,
) => {

  const target = {
      _stack: [],
      snake,
      ...data
  }

  const handlers = {

      set(target: object, prop: string, newValue: any, receiver: ProxyConstructor) {

          const property = target[prop]

          if (typeof property !== 'undefined') {

              // Reserve props & methods which are prefixed with underscore (e.g. _stack)
              if (prop[0] === '_') {
                  throw new Error(`Cannot set protected property ${prop}`)
              }

              if (property instanceof Function) {
                  throw new Error(`Cannot set value to function ${prop}()`) 
              }

              if (target[prop] !== newValue) {
                  target[prop] = newValue
              }
                  
              return true
          }

          // Catch setter for non-defined props
          if (_set instanceof Function) {
              return _set.apply(receiver, [prop, this._stack, newValue])
          }

          return null
      },

      get(target: {_stack: Array<string>, snake: Function}, prop: string, receiver: ProxyConstructor) {

          const propertyName = prop,
                property = target[propertyName],
                context = receiver === this ? target : receiver

          // If pre-defined prop is a function then call it 
          if (typeof property !== 'undefined') {

              const isFunc = property instanceof Function

              if (isFunc) {
                  return (...params) => property.apply(context, params)
              }

              return property
          }

          // If "_get" method was passed and called method is not pre-defined
          if (_get instanceof Function) {
              const value = _get.apply(context, [prop, this._stack])

              if (value !== null) {
                  return value
              }
          }

          target._stack.push(prop)

          if (prop !== '_stack') {
              console.log(prop, target._stack)
          }

          return receiver
      },

      has(target, key) {

          if (typeof target[key] !== 'undefined') {
              return false
          }

          return key in target
      }
  }

  return new Proxy(target, handlers)
}