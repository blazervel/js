<?php

namespace Blazervel\Blazervel\BladeComponents\Traits;

use Blazervel\Blazervel\Web\Attributes\ClassName;

trait WithMergeClassNames
{
  public function mergeClasses(array $attributes, array ...$params): string
  {
    return ClassName::merge($attributes, $params);
  }
}
