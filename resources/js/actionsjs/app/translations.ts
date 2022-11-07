export default function (langConfig: {translations: object, locale: string}) {
  return (
    key: string,
    replace: object = {},
    fallback: boolean = true,
    count: number|null = null,
    config: object
  ) => (
    lang(key, replace, fallback, count, config || langConfig)
  )
}

function lang(
  key: string,
  replace: object = {},
  fallback: boolean = true,
  count: number|null = null,
  config: object
) {

  const translations: object = config.translations,
        keys: Array<string> = key.split('.')

  let translation: string|null = null

  keys.map(k => translation = translations[k] || '')

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