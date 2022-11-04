import render from '@pckg/preact-render-to-string/jsx'
import { h } from '@pckg/preact'
import blazervel from '../actionsjs/app/boot'
import route from '../actionsjs/app/routes'
import lang from '../actionsjs/app/translations'

export default async function () {
  
  const page = blazervel.loadPage()

  return (
    render(
      <>
        {/* <!DOCTYPE html/> */}
        <html lang="en" class="dark:bg-black">
          <head>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <title>Blazervel</title>
          </head>
          <body class="font-sans antialiased">
            <page.component
              $b={blazervel}
              route={route}
              lang={lang}
              {...page.props}
            />
          </body>
        </html>
      </>
    )
  )

}