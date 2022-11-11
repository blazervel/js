// import render from '@pckg/preact-render-to-string/jsx'
// import { h } from '@pckg/preact'
import { render, createElement } from '@pckg/preact'
import App from './app'
import '../../css/tailwind.css'

render(createElement(App), document.body)