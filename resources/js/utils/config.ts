import translations from '@blazervel/../../dist/config/translations'
import routes from '@blazervel/../../dist/config/routes'
import models from '@blazervel/../../dist/config/models'
import notifications from '@blazervel/../../dist/config/notifications'
import actions from '@blazervel/../../dist/config/actions'
import jobs from '@blazervel/../../dist/config/jobs'
import { ConfigProps } from '../types'

const config: ConfigProps = {
  translations,
  routes,
  models,
  notifications,
  actions,
  jobs
}

export default config