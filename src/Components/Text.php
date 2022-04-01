<?php

namespace Blazervel\Blazervel\Components;

use Blazervel\Blazervel\Component;

class Text extends Component
{
  public string $tagName = 'span';
  
  protected function render(): Component
  {
    return $this;
  }
}