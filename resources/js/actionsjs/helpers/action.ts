import { snake } from '../helpers/utils'

/**
 * Reuseable proxy
 * @docs https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Proxy
 */
export default (
    data?: object,
    _get?: (name: string) => null,
    _set?: (name: string, value:any) => null,
    _call?: (name: string, params?: object) => null
) => {
    const target = {
        _stack: [],
        snake,
        ...data
    }

    const handlers = {

        set(target:object, prop: string, newValue: any, receiver: ProxyConstructor) {

            const property = target[prop]

            // Reserve props & methods which are prefixed with underscore (e.g. _stack)
            if (
                prop[0] === '_',
                typeof property !== 'undefined'
            ) {
                throw new Error(`Cannot call protected property ${prop}`)
            }

            if (
                typeof property !== 'undefined' &&
                !(property instanceof Function) &&
                !Object.isFrozen(property)
            ) {
                this[prop] = newValue
            }

            // Catch setter for non-defined props
            if (_set instanceof Function) {
                return _set.apply(this, prop, newValue)
            }

            return null
        },

        get(target: object, prop: string, receiver: ProxyConstructor) {
              
            const property = target[prop],
                  context = this === receiver ? target : this

            // If pre-defined prop is a function then call it 
            if (typeof property !== 'undefined') {

                if (property instanceof Function) {
                    return (...params) => property.apply(context, params)
                }

                return property
            }

            // If "_get" method was passed and called method is not pre-defined
            if (_get instanceof Function) {
                return _get.apply(context, prop)
            }

            return null
        },

        apply(target, prop, params, receiver) {

            console.log(`Calling ${prop}(${params})`)

            const property = target[prop],
                  context = this === receiver ? target : this

            // Reserve props & methods which are prefixed with underscore (e.g. _stack)
            if (
                prop[0] === '_',
                typeof property !== 'undefined'
            ) {
                throw new Error(`Cannot call protected property ${prop}`)
            }

            if (typeof property !== 'undefined') {

                if (property instanceof Function) {
                    return property.apply(context, {name: prop, ...params})
                }

                return property
            }

            // If "_call" method was passed and called method is not pre-defined
            if (_call instanceof Function) {
                return _call.apply(this, {name: prop, ...params})
            }
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