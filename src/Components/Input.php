<?php

namespace Blazervel\Blazervel\Components;

use Blazervel\Blazervel\Div as Component;

class Input extends Component
{
  public string $tagName = 'input';
  public ?string $type = 'text';

  protected function render(): Component
  {
    return $this;
  }
}