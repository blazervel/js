<?php

namespace Blazervel\Blazervel\Components\Components\Input;

use Blazervel\Blazervel\Component;

class Text extends Component
{
  protected string $tag = 'input';
  protected string $type = 'text';
  public ?string $model; 

  public function __construct(string $model = null)
  {
    $this->model = $model;
  }

  public function render()
  {
    return <<<'blade'
      <input type="text" v-model="{{ $model }}" class="block w-full max-w-lg border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm"/>
    blade;
  }
}