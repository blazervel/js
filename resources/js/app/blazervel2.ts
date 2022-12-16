import axios from '@pckg/axios'
import { debounce } from '@pckg/lodash'


/*

Example:


b().app.users.first().then(response => console.log(response))

*/


interface ActionRequestProps {
  key: string
  name: string
  data: object
  resolve: Function|null
}

interface BuilderProps {
  action: Array<string>
  run: (name: string, options: { params: Array<any>, bypassStore?: boolean }) => Promise<any>
}

interface RunnerOptionsProps {
  route: string
  method: string
  debounceDelay: number
}

const store = (window as any).localStorage

const runnerOptions: RunnerOptionsProps = {
  route: '/api/blazervel/run-actions',
  method: 'get', //'post'
  debounceDelay: 500,
}

let actionsQueue: Array<ActionRequestProps> = []

let rejectActions: Function

const buildRequest = () => {

  const data = {
    actions: JSON.stringify(actionsQueue)
  }

  let options: any = {
    url: runnerOptions.route,
    method: runnerOptions.method,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-XSRF-TOKEN': csrf(),
    },
    withCredentials: true,
  }

  if (options.method === 'get') {
    options.params = data
  } else {
    options.data = data
  }

  return axios(options)
}

const buildResponse = ({ actionName, actionMethods, ...props }: any) => {
  return new Proxy({
    name: actionName,
    methods: actionMethods,
    ...props
  }, {
    get(target, p, receiver) {
      const name = target.actionName,
            prop = target[p] || null,
            method = target.methods[p] || null
      
      if (prop !== null) {
        return prop
      }

      if (method !== null) {
        return (...params) => runAction(`${name}.${method}`, { params, bypassStore: true })
      }
    }
  })
}

const requestActions = debounce(() => {
    
  buildRequest()
    .then(response => Object.entries(response.data.responses).map(([key, response]) => {

      // Cache individual request responses
      store.setItem(key, JSON.stringify(response))

      const queueItem = actionsQueue.filter(ac => ac.key === key)[0]

      if (queueItem.resolve instanceof Function) {
        console.log('response', response)
        queueItem.resolve(
          buildResponse(response)
        )
      }

    }))
    .catch(error => {
      rejectActions(error)
      store.clear()
    })
    .then(() => actionsQueue = [])

}, runnerOptions.debounceDelay)

const target: BuilderProps = {

  action: [],

  async run(name, options) {
    return await runAction(name, options)
  }

}

async function runAction(name, { params, bypassStore = false }) {

  const data = params && params.length ? { params } : {},
        key = actionKey(name, data),
        fromStore = store.getItem(key)

  let resolveActions

  const promise = new Promise((resolve, reject) => {
    resolveActions = resolve
    rejectActions = reject
  })

  if (!bypassStore && fromStore !== null) {
    
    setTimeout(() => {
      resolveActions(
        buildResponse(JSON.parse(fromStore))
      )
      resolveActions = null // Force rerequest to just setItem on store
      store.removeItem(key)
    }, 3)

  }
    
  actionsQueue.push({
    key,
    name,
    data,
    resolve: resolveActions
  })

  requestActions()

  return await promise

}

const csrf = () => {
    if (typeof document === 'undefined') {
        return
    }

    let xsrfToken
    
    xsrfToken = document.cookie.match('(^|; )XSRF-TOKEN=([^;]*)') || 0
    xsrfToken = xsrfToken[2]
    xsrfToken = decodeURIComponent(xsrfToken)
        
    return xsrfToken
}

const actionKey = (name: string, data: object) => {

  const sortData = (data: object): object => {
    let sorted = {}
    Object.keys(data).sort().map(key => sorted[key] = data[key])
    return sorted
  }

  const serializeData = (data: object): string => {
    const query = new URLSearchParams(data)
    return query.toString()
  }

  if (!data) {
    return name
  }

  return `${name}${serializeData(sortData(data))}`
}

const builderProxy = () => {
  
  let namespace: Array<string> = []

  return new Proxy(target, {

    // apply: (target, thisArg, argumentsList) => {
    //   console.log('handle apply', target, thisArg, argumentsList)
    // },
    
    get: (target: BuilderProps, p: string, receiver: object) => {

      namespace.push(p)

      if ([
        'run',
        'send',
        'get',
        'find',
        'first',
      ].includes(p)) {
        return (...params) => target.run(
          namespace.join('.'),
          { params }
        )
      }
    
      if ([
        'create',
        'update',
        'delete'
      ].includes(p)) {
        return (...params) => target.run(
          namespace.join('.'),
          {
            params,
            bypassStore: true 
          }
        )
      }

      return receiver

    } 
  })
}

export default builderProxy