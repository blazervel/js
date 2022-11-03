import { render } from '@deps/preact'
import { useEffect, useState } from '@deps/preact/compat'
import blazervel from '../actionsjs/app/blazervel'
import route from '../actionsjs/app/routes'
import lang from '../actionsjs/app/translations'

import progress from '../actionsjs/helpers/progress/progress'
import '../actionsjs/helpers/progress/progress.css'

const Body = ({ children }) => {

  const [page, setPage] = useState(null)

  useEffect(() => {

    window.route = route
    window.lang = lang

    const linkElements = document.querySelectorAll('a:not([target="_blank"])'),
          handleClick = (event, link) => {
            event.preventDefault()
    
            const state = {
              url: link.getAttribute('href')
            }
    
            // Update history state
            window.history.pushState(state, null, state.url)
    
            window.dispatchEvent(
              new PopStateEvent('popstate', {state})
            )
    
            return false
          },
          handlePopstate = (event) => {
            progress.start()
            blazervel.Page.load(event.state.url).then(page => {
              setPage(page)
              progress.done()
            })
          }

    linkElements.forEach(link => link.addEventListener('click', (event) => handleClick(event, link)))

    window.addEventListener('popstate', handlePopstate)

    if (!page) {
      progress.start()
      blazervel.Page.load(window.location.href).then(page => {
        setPage(page)
        progress.done()
      })
    }

    return () => {
      linkElements.forEach(link => link.removeEventListener('click', (event) => handleClick(event, link)))
      window.removeEventListener('popstate', handlePopstate)
    }

  }, [page])

  if (!page) {
    return <></>
  }

  const { Component, props } = page

  return (
    <div className="z-0 relative">
      <Component
        $b={blazervel}
        {...props}
      />
    </div>
  )
}

export default () => render(<Body />, document.body)