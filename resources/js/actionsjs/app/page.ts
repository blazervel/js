import Connection from '../helpers/connection'
import { resolveComponent } from '../../utils'
import progress from '../helpers/progress'

interface PageProps {
  action: string
  props: object
  Component?: Function
  componentName: string
}

export default ($app) => ({

  async load(url: string): Promise<PageProps> {

    progress.start()

    const conn = new Connection('actions/pages-data'),
          pageData: Promise<PageProps> = (
            await conn
              ._get({ url, namespace: 'blazervel' })
              .then(response => { progress.done(); return response })
              .catch(() => progress.done())
          )

    return {
      ...pageData,
      Component: await resolveComponent(pageData.componentName)
    }
  },

})