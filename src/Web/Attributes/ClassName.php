<?php

namespace Blazervel\Blazervel\Web\Attributes;

// use Blazervel\Blazervel\Web\Attributes\Traits\WithTailwind;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ClassName
{
  // use WithTailwind;

  private array $attributes;
  private array $params;
  private string $appCssPublicPath = 'css/app.css';

  private array $effectsPseudos = ['hover:', 'focus:', 'active:', 'group-hover:', 'transition'];

  private function tailwindRemoveEffectsClassNames(array $classNames): array
  {
    $effectlessClassNames = [];

    foreach($classNames as $key => $val) :
      $className = is_string($key) ? $key : $val;

      // if (
      //   Str::contains(
      //     $className,
      //     $this->effectsPseudos
      //   )
      // ) :
      //   continue;
      // endif;

      $effectlessClassNames = $val;
    endforeach;

    return $effectlessClassNames;
  }

  /** 
   * Merge TailwindCSS classes here
   * - e.g. if h-4 is in array before h-6, then h-4 gets bumped
   */
  private function tailwindMergeClassNames(array $classNames): array
  {
    return $classNames;

    $mergedClassNames = [];

    // $appCssFile = base_path() . '/public/{$this->appCssPublicPath}';
    // Actually reference available class names 
    // (including custom ones in tailwind.config.js)
    // if (!File::exists($appCssFile)) :
    //   return $classNames;
    // endif;
    // $css = File::get($appCssFile);
    // $css = Str::of($css)->matchAll('/.(.*)\n/');

    foreach($classNames as $className) :
      $key = explode('px-', $className)[0];

      $mergedClassNames[$key] = $className;
    endforeach;

    return array_values($mergedClassNames);
  }

  public function __construct(array $attributes, array ...$params)
  {
    $this->attributes = $attributes;
    $this->params = $params;
  }

  public static function merge(array $attributes, array ...$params): string
  {
    return (new Self($attributes, $params))->toString();
  }

  public function toString()
  {
    return $this->mergeClasses();
  }

  public function mergeClasses(): string
  {
    $attributes = $this->attributes;
    $params = $this->params;
    $classNames = [];
    $overrideWithClassNames = $attributes['class'] ?? '';

    foreach($params as $classNamesParam) :
      if (is_string($classNamesParam)) :
        $classNamesParam = explode(' ', $classNamesParam);
      endif;

      // Convert ['bg-cover', 'text-black'] 
      // to ['bg-cover' => true, 'text-black' => true]
      if (is_string(array_values($classNamesParam)[0])) :
        $classNamesParam = array_flip($classNamesParam);
        $classNamesParam = array_fill_keys(array_keys($classNamesParam), true);
      endif;

      $classNames = array_merge($classNames, $classNamesParam);
    endforeach;

    $classNames = array_merge(
      $classNames, 
      explode(' ', $overrideWithClassNames)
    );

    if ($attributes['disabled'] ?? false) :
      $classNames = $this->tailwindRemoveEffectsClassNames(
        $classNames
      );
    endif;

    $classNames = $this->tailwindMergeClassNames(
      $classNames
    );

    return Arr::toCssClasses($classNames);
  }
}