<?php

namespace Blazervel\Web\Attributes\Style;

use ReflectionClass;

class Display
{
  const flex = 'display:flex';
  const inlineFlex = 'display:inline-flex';
  const block = 'display:block';
  const inline = 'display:inline';
  const inlineBlock = 'display:inline-block';

  public static function find(string $propertyName): string|null
  {
    $properties = new ReflectionClass(__CLASS__);
    $properties = $properties->getConstants();
    
    return $properties[$propertyName] ?? null;
  }

  public static function exists(string $propertyName): bool
  {
    return self::find($propertyName) !== null;
  }
}