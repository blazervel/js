<?php

namespace Blazervel\Blazervel\Operations;

use Illuminate\Database\Eloquent\Model;

use Blazervel\Blazervel\Operation as Blazervel;

class Update extends Blazervel
{
  public function steps(): array
  {
    return [
      'model',
      'authorize',
      'validate',
      'update'
    ];
  }

  public function update(): Model
  {
    $modelName = $this->modelName;
    return $this->$modelName->save();
  }
}