<?php

namespace Blazervel\Blazervel\Components;

use Blazervel\Blazervel\Component as ComponentAbstract;

class Component extends ComponentAbstract
{

  protected function render(): Component
  {
    return $this;
  }

}