import Builder from '../helpers/models/builder'
import Container from '../helpers/container'
import Connection from '../helpers/connection'

import Auth from './auth'
import Actions from './actions'
import Models from './models'
import action from '../helpers/action'

const ActionsJS = {
    Builder,
    Container,
    Connection,

    // Resources
    Models,
    Actions,
    Auth,

    page: (callback: Function) => {
        // $app.page((page) => page.getElementById()...)
        return callback.apply(this, window.document)
    }
}

const App = action({
    ...ActionsJS,
    get: (target, prop, receiver) => {

        const propertyValue = target[prop]

        if (typeof propertyValue !== 'undefined') {

            if (
                propertyValue instanceof Function &&
                ['Actions', 'Auth', 'Models'].includes(prop)
            ) {
                return propertyValue(receiver)
            }

            return propertyValue
        }

        return null
    }
})

export default App