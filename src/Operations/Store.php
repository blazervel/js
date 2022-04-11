<?php

namespace Blazervel\Blazervel\Operations;

use Illuminate\Database\Eloquent\Model;

use Blazervel\Blazervel\Operation as Blazervel;

class Store extends Blazervel
{
  public function steps(): array
  {
    return [
      'model',
      'authorize',
      'validate',
      'store'
    ];
  }

  public function store(): Model
  {
    $modelName = $this->modelName;
    return $this->$modelName->save();
  }
}