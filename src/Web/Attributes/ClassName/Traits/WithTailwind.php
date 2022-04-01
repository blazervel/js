<?php

namespace Blazervel\Blazervel\Web\Attributes\ClassName\Traits;

use Illuminate\Support\Str;

trait WithTailwind
{
  private string $appCssPublicPath = 'css/app.css';

  private array $effectsPseudos = [
    'hover:',
    'focus:',
    'active:',
    'group-hover:',
    'transition'
  ];

  private function tailwindRemoveEffectsClassNames(array $classNames): array
  {
    $effectlessClassNames = [];

    foreach($classNames as $key => $val) :
      $className = is_string($key) ? $key : $val;

      if (!Str::of($className)->contains($this->effectsPseudos)) :
        continue;
      endif;

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
}
