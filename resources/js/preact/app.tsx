import { render } from '@deps/preact'
import Home from '@/Home.jsx'
import $app from '../actionsjs/app/boot'
import route from '../actionsjs/app/routes'
import translate from '../actionsjs/app/translations'

import '../../css/tailwind.css'

render(
  <Home
    $app={$app}
    $r={route}
    __={translate}
  />,
  document.body
)