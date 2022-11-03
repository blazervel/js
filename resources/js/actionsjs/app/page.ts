import resolveComponent from '../../preact/resolve-component'
import Connection from '../helpers/connection'
import { requestTimeout } from '../helpers/fetch'

interface PageProps {
  action: string
  props: object
  Component?: Function
  componentName: string
}

let onLoadingHooks: Array<Function> = [], onLoadHooks: Array<Function> = []

const onLoading: Function = (percentage) => {
  onLoadingHooks.map(callback => callback(percentage))
}

const onLoad: Function = (page) => {
  onLoadHooks.map(callback => callback(page))
}

export default ($app) => ({

  async load(url: string): Promise<object> {

    let pageData: Promise<PageProps>,
        msPassed: number = 0
  
    const clearRequestTimer = () => {
            clearInterval(requestTimer)
            onLoading(100)
          },
          requestTimer = setInterval(() => {
            msPassed = msPassed + 1
            
            onLoading(
              (msPassed / requestTimeout) * 100
            )
          }, 1)
    
    pageData = await (new Connection('actions/pages-data'))._get({
      url,
      namespace: 'blazervel'
    })
    .then(response => { clearRequestTimer(); return response })
    .catch(() => clearRequestTimer())
    
    const page: object = {
      ...pageData,
      Component: await resolveComponent(pageData.componentName)
    }
  
    onLoadHooks.map(callback => callback(page))
    
    return page
  },

  onLoading(callback) {
    onLoadingHooks.push(callback)
  },

  onLoad(callback) {
    onLoadHooks.push(callback)
  },

})