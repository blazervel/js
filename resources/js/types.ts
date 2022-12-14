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
  translations: TranslationsConfigProps
  routes: RoutesConfigProps
  models: ModelsConfigProps
}