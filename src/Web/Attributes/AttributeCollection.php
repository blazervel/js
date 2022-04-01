<?php

namespace Blazervel\Blazervel\Web\Attributes;

use Illuminate\Support\Collection;

class AttributeCollection extends Collection
{
  public function string()
  {
    return $this->whereNotNull()->map(function($value, $attribute){
      
      return "{$attribute}=\"{$value}\"";

    })->join(' ');
  }
}