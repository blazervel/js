type LangProps = {
  key: string,
  count?: number,
  language?: string
}

export const lang = (): LangProps => {

  const { translations } = typeof BlazervelLang !== 'undefined' ? BlazervelLang : globalThis?.BlazervelLang
  
  const keys = key.split('.')
  
  let translation = translations

  keys.map(k => translation = translation[k] || key)

  return translation
}