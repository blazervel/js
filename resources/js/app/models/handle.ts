import Model from './model'
import Container from '../helpers/container'
import { snake } from '../../utils'

let container

export default () => (
    new Proxy({}, {
        get(target, prop, receiver) {
          
          const model = prop

          if ( ! container) {
              container = new Container(Model)
              Model.setContainer(container)
          }
        
          if ( ! container.items.get(model)) {
              return container.register(model, {
                  endpoint: snake(model)
              })
          }
        
          return container.make(model)
      
        }
    })
)