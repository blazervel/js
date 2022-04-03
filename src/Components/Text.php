<?php

namespace Blazervel\Components;

use Blazervel\Component;

class Text extends Component
{
  public string $tagName = 'span';
  
  protected function render(): Component
  {
    return $this;
  }
}