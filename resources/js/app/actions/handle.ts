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