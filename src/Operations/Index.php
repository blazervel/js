<?php

namespace Blazervel\Blazervel\Operations;

use Illuminate\Database\Eloquent\Collection;

use Blazervel\Blazervel\Operation as Blazervel;

class Index extends Blazervel
{
  public function steps(): array
  {
    return [
      'authorize',
      'collection'
    ];
  }
}