<?php

namespace Blazervel\Web\Attributes\Style;

use ReflectionClass;

class Pseudo
{
  const lang = 'lang';
  const nthChild = 'nth-child';
  const nthLastChild = 'nth-last-child';
  const nthLastOfType = 'nth-last-of-type';
  const nthOfType = 'nth-of-type';
  const not = 'not';
  const active = 'active';
  const checked = 'checked';
  const disabled = 'disabled';
  const empty = 'empty';
  const enabled = 'enabled';
  const firstChild = 'first-child';
  const firstOfType = 'first-of-type';
  const focus = 'focus';
  const hover = 'hover';
  const inRange = 'in-range';
  const invalid = 'invalid';
  const lastChild = 'last-child';
  const lastOfType = 'last-of-type';
  const link = 'link';
  const onlyOfType = 'only-of-type';
  const onlyChild = 'only-child';
  const optional = 'optional';
  const outOfRange = 'out-of-range';
  const readOnly = 'read-only';
  const readWrite = 'read-write';
  const required = 'required';
  const root = 'root';
  const target = 'target';
  const valid = 'valid';
  const visited = 'visited';
  const after = 'after';
  const before = 'before';
  const firstLetter = 'first-letter';
  const firstLine = 'first-line';
  const selection = 'selection';

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