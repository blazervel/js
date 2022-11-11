
import setupAliases from './setup-aliases'
import setupDevServer from "./setup-dev-server"
import { BlazervelLoaderProps } from "../types"

export default class ConfigUtil {

  constructor(config: object, mode: string, { certsPath, basePath, packagePath, loadEnv, lodash }: BlazervelLoaderProps) {
    this.config      = config
    this._           = lodash
    this.basePath    = basePath
    this.packagePath = packagePath
    this.certsPath   = certsPath
    this.env         = loadEnv(mode, this.basePath, '')
  }

  // lodash instance
  _

  // Config object
  config: object

  // path.resolve method
  resolvePath: Function

  // Full path to this package
  packagePath: string

  // Full path to base of project
  basePath: string

  // Path to certificates
  certsPath: string

  env: object

  mode: string
  
  hasPlugin(config: {plugins: Array<object>}, search: string, key?: string): boolean {
    return config.plugins.map(plugin => plugin[key || 'name']).includes(search)
  }

  set(path: string, value: any): object {
    return this._.set(this.config, path, value)
  }

  has(path: string, ...paths: Array<string>): boolean {
    return ! ! paths.push(path) && paths.every(pth => this._.has(this.config, pth))
  }

  get(path: string, defaultValue: any): any {
    return this._.get(this.config, path, defaultValue)
  }

  merge(path: string, merge: object|Array<any>): object {
    const value = this.get(path, null)
    
    if (value === null) {
      return this.set(path, merge)
    }
    
    if (Array.isArray(value)) {
      return this.set(path, value.concat(merge))
    }

    return this.set(path, {
      ...value,
      ...merge
    })
  }

  cascade(fallback: any, ...paths: Array<string>): any {
    const hasPaths = paths.filter(path => this.has(path))

    if (hasPaths.length === 0) {
      return fallback
    }

    return hasPaths[0]
  }
  
  log(...messages: Array<string>): void {
    console.log(STYLE.start, (
      messages
        .map(m => {
          Object.entries(STYLE).map(([name, code]) => (
            m = m.replace(`{${name}}`, code)
          ))
          return m
        })
        .join(STYLE.eol)
    ), STYLE.reset)
  }

  setupAliases(): void {
    this.config = setupAliases(this)
  }

  setupDevServer(): void {
    this.config = setupDevServer(this)
  }
  
}

const themeColor = "\x1b[35m",
      nl = "\n",
      reset =  "\x1b[0m"

const STYLE = {
  reset,
  start:     `${nl}${reset}${themeColor}`,
  theme:     `${reset}${themeColor}`,
  eol:       `${nl}  ${reset}${themeColor}`,

  bright:    "\x1b[1m",
  dim:       "\x1b[2m",
  underline: "\x1b[4m",
  blink:     "\x1b[5m",
  reverse:   "\x1b[7m",
  hidden:    "\x1b[8m",
  black:     "\x1b[30m",

  magenta:   themeColor,
  red:       "\x1b[31m",
  green:     "\x1b[32m",
  yellow:    "\x1b[33m",
  blue:      "\x1b[34m",
  cyan:      "\x1b[36m",
  white:     "\x1b[37m",
  bgBlack:   "\x1b[40m",
  bgRed:     "\x1b[41m",
  bgGreen:   "\x1b[42m",
  bgYellow:  "\x1b[43m",
  bgBlue:    "\x1b[44m",
  bgMagenta: "\x1b[45m",
  bgCyan:    "\x1b[46m",
  bgWhite:   "\x1b[47m",
}