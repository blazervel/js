import Connection from '../helpers/connection'
import { snake } from '../../utils'

export default () => p({
    
    run: async function (action, data) {
        const response = await (new Connection(`actions/${action}`))._get(data)
        return response
    },

    get: (target, prop, receiver) => {
        if (typeof target[prop] === 'undefined') {

            receiver.stack.push(prop)

            return receiver
        }

        if (prop === 'run') {
            const action = receiver.stack.map(s => snake(s)).join('-')
            receiver.stack = []

            return (...data) => target.run(action, data)
        }

        return target[prop]
    }
})

/**
 * Reuseable proxy
 * @docs https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Proxy
 */
const p = (
    data?: object,
    _get?: (name: string) => null,
    _set?: (name: string, value:any) => null,
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
                return _set.apply(receiver, [prop, newValue])
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
                const value = _get.apply(context, prop)

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