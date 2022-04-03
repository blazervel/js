<?php

namespace Blazervel\Blazervel\Web\Attributes;

use Illuminate\Support\Collection;

class ClassName extends Collection
{
  public function string()
  {
    return trim(
      $this->join(' ')
    );
  }
}