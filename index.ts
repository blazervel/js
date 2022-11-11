import { loadEnv } from 'vite'
import { homedir } from 'os'
import path from 'path'
import lodash from 'lodash'
import loadPlugin from './resources/js/vite/plugin'

export default loadPlugin({
  certsPath: path.resolve(homedir(), '.config/valet/Certificates/'),
  packagePath: path.resolve(__dirname),
  basePath: path.resolve(''),
  loadEnv,
  lodash
})