<?php

namespace Blazervel\Blazervel\Components;

use Blazervel\Blazervel\Component;

class Input extends Component
{
  public string $tagName = 'input';
  public ?string $type = 'text';

  protected function render(): Component
  {
    return $this;
  }
}