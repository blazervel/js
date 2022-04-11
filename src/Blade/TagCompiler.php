<?php

namespace Blazervel\Blazervel\Blade;

use Illuminate\View\Compilers\ComponentTagCompiler;

class TagCompiler extends ComponentTagCompiler
{
  public function compile($value)
  {
    return $this->compileBlazervelTags($value);
  }

  protected function compileBlazervelTags($value)
  {
    $pattern = "/
      <
        \s*
        blazervel\:([\w\-\:\.]*)
        \s*
        (?<attributes>
          (?:
            \s+
            [\w\-:.@]+
            (
              =
              (?:
                \\\"[^\\\"]*\\\"
                |
                \'[^\']*\'
                |
                [^\'\\\"=<>]+
              )
            )?
          )*
          \s*
        )
      \/?>
      (?<slot>(.*))
      <\/blazervel\:([\w\-\:\.]*)>
    /x";

    preg_match($pattern, $value, $matches);

    if (count($matches) > 0) :

      return preg_replace_callback($pattern, function (array $matches) {

        $component = str_replace(':', '', $matches[1]);

        return "<x-blazervel::{$component} {$matches['attributes']}>{$matches['slot']}</x-blazervel::{$component}>";

      }, $value);

    endif;

    $pattern = "/
      <
        \s*
        blazervel\:([\w\-\:\.]*)
        \s*
        (?<attributes>
          (?:
            \s+
            [\w\-:.@]+
            (
              =
              (?:
                \\\"[^\\\"]*\\\"
                |
                \'[^\']*\'
                |
                [^\'\\\"=<>]+
              )
            )?
          )*
          \s*
        )
      \/?>
    /x";

    return preg_replace_callback($pattern, function (array $matches) {

      $component = str_replace(':', '', $matches[1]);

      return "<x-blazervel::{$component} {$matches['attributes']} />";

    }, $value);
  }
}
