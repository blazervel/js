<?php

namespace Blazervel\Blazervel\Operations;

use Blazervel\Blazervel\Operation as Blazervel;

class Show extends Blazervel
{
  public function steps(): array
  {
    return [
      'model',
      'authorize',
    ];
  }
}