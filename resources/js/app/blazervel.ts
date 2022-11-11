import Container from './helpers/container'
import Connection from './helpers/connection'
import Builder from './models/builder'
import Auth from './actions/auth'
import Actions from './actions/handle'
import Models from './models/handle'
import Page from './page'

import { cache } from './helpers/cache'

const _resources = ['Actions', 'Auth', 'Models', 'Page']

const App = new Proxy({
    Builder,
    Container,
    Connection,
    Models,
    Actions,
    Auth,
    Page,
    Store: cache.store,
}, {
    get(target: object, prop: string, receiver: ProxyConstructor) {

        const propertyValue = target[prop]

        if (typeof propertyValue !== 'undefined') {

            if (
                propertyValue instanceof Function &&
                _resources.includes(prop)
            ) {
                return propertyValue(receiver)
            }

            return propertyValue
        }

        return null
    }
})

export default App