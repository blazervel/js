export interface TranslationsConfigProps {
  translations: object
  locale: string
}

export interface RoutesConfigProps {
  routes: object
}

export interface ModelsConfigProps {
  shared: {
    methods: object
  }
  models: object
}

export interface ConfigProps {
  translations: object, //TranslationsConfigProps
  routes: object, //RoutesConfigProps
  models: object, //ModelsConfigProps
  notifications: object,
  actions: object,
  jobs: object
}

export interface AxiosRequestOptions {
  method: string
  params?: object
  data?: object
  headers: object
  withCredentials: boolean
}

export interface RequestConfig {
  method?: string
  data?: object
  headers?: object
  ignoreCache?: boolean
  allowStaleCache?: boolean
}

export interface Request {
  url: string
  data?: object
  config?: RequestConfig
}

export interface Response {
  data: any
}

export interface QueueItem {
  key: string
  request: Request
  resolve: (value: unknown) => void
  reject: (reason?: any) => void
}