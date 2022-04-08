<?php

namespace Blazervel\Blazervel\Operations;

use Blazervel\Blazervel\Operation as Blazervel;

class Destroy extends Blazervel
{
  public function steps(): array
  {
    return [
      'model',
      'authorize',
      'destroy'
    ];
  }

  public function destroy(): bool
  {
    $modelName = $this->modelName;
    return $this->$modelName->delete();
  }
}