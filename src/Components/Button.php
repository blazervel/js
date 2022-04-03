<?php

namespace Blazervel\Blazervel\Components;

use Blazervel\Blazervel\Component;

class Button extends Component
{
  public string $tagName = 'button';
  public ?string $type = 'button';

  protected function render(): Component
  {
    return $this;
  }
}