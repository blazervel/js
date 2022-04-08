<?php

namespace Blazervel\Blazervel\Blade\Traits;

use Blazervel\Blazervel\Web\Attributes\ClassName\Traits\WithTailwind;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait WithMergeClassNames
{
  use WithTailwind;

  public function mergeClasses(array $attributes, array|string ...$arguments): string
  {
    $classNames = [];
    $overrideWithClassNames = $attributes['class'] ? explode(' ', $attributes['class']) : [];

    foreach($arguments as $classNamesParam) :

      if (is_string($classNamesParam) && !empty(trim($classNamesParam))) :
        $classNamesParam = explode(' ', $classNamesParam);
      endif;

      $classNamesParam = $this->classNameArrayFlip(
        $classNamesParam
      );

      $classNames = array_merge(
        $classNames, 
        $classNamesParam
      );

    endforeach;

    $overrideWithClassNames = $this->classNameArrayFlip(
      $overrideWithClassNames
    );

    $classNames = array_merge(
      $classNames, 
      $overrideWithClassNames
    );

    if ($attributes['disabled'] ?? false) :
      $classNames = $this->tailwindRemoveEffectsClassNames(
        $classNames
      );
    endif;

    // $classNames = $this->tailwindMergeClassNames(
    //   $classNames
    // );

    return Arr::toCssClasses($classNames);
  }

  public function classNameArrayFlip(array $classNames)
  {
    // Convert ['bg-cover', 'text-black'] 
    // to ['bg-cover' => true, 'text-black' => true]
    foreach ($classNames as $indexOrClassName => $boolOrClassName) :

      if (is_string($indexOrClassName) && is_bool($boolOrClassName)) : 
        continue;
      endif;

      unset($classNames[$indexOrClassName]);

      if (!is_string($boolOrClassName)) : 
        continue;
      endif;

      $classNames[$boolOrClassName] = true;

    endforeach;

    return $classNames;
  }
}