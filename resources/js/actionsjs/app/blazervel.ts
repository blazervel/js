import Builder from '../helpers/models/builder'
import Container from '../helpers/container'
import Connection from '../helpers/connection'

import Auth from './auth'
import Actions from './actions'
import Models from './models'
import Page from './page'

const App = new Proxy({
    Builder,
    Container,
    Connection,

    // Resources
    Models,
    Actions,
    Auth,
    Page,
}, {
    get(target: object, prop: string, receiver: ProxyConstructor) {

        const propertyValue = target[prop]

        if (typeof propertyValue !== 'undefined') {

            if (
                propertyValue instanceof Function &&
                ['Actions', 'Auth', 'Models', 'Page'].includes(prop)
            ) {
                return propertyValue(receiver)
            }

            return propertyValue
        }

        return null
    }
})

export default App

// import Builder from '../helpers/models/builder'
// import Container from '../helpers/container'
// import Connection from '../helpers/connection'

// import auth from './auth'
// import actions from './actions'
// import models from './models'
// import page from './page'
// import p from '../helpers/proxyable'

// const App = p({
//     Builder,
//     Container,
//     Connection,
//     models,
//     actions,
//     auth,
//     page,
// })

// App.models(App)
// App.actions(App)
// App.auth(App)
// App.page(App)

// export default App