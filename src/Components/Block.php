<?php

namespace Blazervel\Blazervel\Components;

use Blazervel\Blazervel\Component;

class Block extends Component
{
  protected function render(...$params): Component
  {
    return (
      new Component(
        tag: 'div', 
        className: 'block', 
        params: $params
      )
    );
  }

}