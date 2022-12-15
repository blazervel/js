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
  controllers: object,
  actions: object,
  jobs: object
}