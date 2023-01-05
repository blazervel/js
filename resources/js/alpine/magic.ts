import modelsConfig from '@blazervel/../../dist/config/models'
import actionsConfig from '@blazervel/../../dist/config/actions'

class Model {
  
  constructor() {
    console.log('instantiated new model!')
  }

}

export default () => {

  const models = modelsConfig.models,
        modelsSharedMethods = modelsConfig.shared.methods,
        { actions } = actionsConfig

  let Models, Actions
  
  Object.values(actions).map(({ key,  }) => {
    //
  })
  
  return {
    Actions: new Proxy({}, {
      get(target: object, prop: string, receiver: ProxyConstructor) {
        
      }
    }),
  }
}