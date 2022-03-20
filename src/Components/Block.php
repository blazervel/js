<?php

namespace Blazervel\Blazervel\Components;

use Blazervel\Blazervel\Components\Component;

class Block extends Component
{

  protected function render(): Component
  {
    return (
      new Component(
        className: 'block relative', 
      )
    );
  }

}