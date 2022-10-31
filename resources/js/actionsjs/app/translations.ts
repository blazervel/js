import Connection from '../helpers/connection'

const langConfig = await (new Connection('actions/translations-config'))._get({ namespace: 'blazervel-actionsjs' })

export default function (
  key: string,
  replace: object = {},
  fallback: boolean = true,
  count: number|null = null,
  locale: string|null = null
) {

  const translations = langConfig.translations || {},
        keys = key.split('.')

  locale = (
    locale ||
    langConfig.locale ||
    document.getElementsByTagName('html')[0].getAttribute('lang') ||
    'en'
  )

  let translation = null,
      localeTranslations = translations[locale] || {}

  keys.map(k => translation = localeTranslations[k] || '')

  if (!translation && fallback) {
    for (var localeKey in translations) {
      keys.map(k => translation = translations[localeKey][k] || '')

      if (translation) {
        break
      }
    }
  }

  count = count !== null && (Array.isArray(count) || count === Object(count)) 
            ? Object.values(count).length 
            : (typeof count === 'number' ? count : null)
  
  if (count !== null) {
    // Support pluralization (https://laravel.com/docs/9.x/localization#pluralization)
  }

  if (!translation) {
    translation = key
  }

  for (var key in replace) {
    translation = translation.replace(':' + key, replace[key])
  }

  return translation
}