import React, { PropsWithChildren } from '@pckg/preact/compat';

interface LayoutProps {
  className: string
  locale: string
  page: {title: string}
  children
}

export default function ({ className = '', locale = 'en', page, children }: PropsWithChildren<LayoutProps>) {
  return (
    <html className={className} lang={locale}>
      <head>
        <meta charSet="UTF-8" />
        <link rel="icon" type="image/svg+xml" href="/vite.svg" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{page.title}</title>
      </head>
      <body className="font-sans antialiased">{children}</body>
    </html>
  )
}