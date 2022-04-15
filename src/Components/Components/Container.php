<?php

namespace Blazervel\Blazervel\Components\Components;

use Blazervel\Blazervel\Component;

class Container extends Component
{
  public bool $lg;
  public bool $sm;

  public function __construct(bool $lg = null, bool $sm = null)
  {
    $this->lg = !!$lg;
    $this->sm = !!$sm;
  }

  public function render()
  {
    return <<<'blade'
      <div class="w-full px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
          {{ $slot }}
        </div>
      </div>
    blade;
  }
}