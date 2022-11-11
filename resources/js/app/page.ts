import Connection from './helpers/connection'
import { resolveComponent } from '../utils'
import progress from './helpers/progress'
import Error from '../preact/error'

interface PageProps {
  status: number
  action: string
  props: object
  config: object
  Component?: Function
  componentName?: string
}

const formatError = (error: string): {status: number} => ({
  status: parseInt(String(error).split('with status code ')[1])
})

export default ($app) => ({

  errorPage(error: {status: number, heading?: string, message?: string }) {
    return {
      props: error,
      Component: Error
    }
  },

  async load(url: string): Promise<PageProps> {

    progress.start()

    const conn = new Connection('actions/pages-data'),
          response: PageProps = (
            await conn
              ._get({ url, namespace: 'blazervel' })
              .then(response => {
                progress.done()
                return response 
              })
              .catch(error => {
                progress.done()
                return formatError(error)
              })
          )

    if (!response.componentName) {
      return this.errorPage(response)
    }

    let Component = await resolveComponent(response.componentName)

    if (Component === null) {
      return this.errorPage({ status: 404 })
    }

    if (Component.default) {
      Component = Component.default
    }

    return {
      ...response,
      Component
    }
  },

})