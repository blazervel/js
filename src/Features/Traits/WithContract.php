<?php

namespace Blazervel\Blazervel\Operations\Traits;

use Illuminate\Validation\Validator;
use Blazervel\Blazervel\Feature;

trait WithContract
{
  public Validator $contract;

  protected function runContract(): void
  {
    $calledClassNamespace = Feature::conceptNamespace(get_called_class());
    $contractClass = "{$calledClassNamespace}\\Contract";

    $modelName = $this->modelName ?? null;

    if (!$modelName || !($model = $this->$modelName)) :

      $this->runModel();

      $modelName = $this->modelName;
      $model = $this->$modelName;

    endif;

    $this->contract = $contractClass::make(
      action: $this->action,
      data: $model->toArray()
    );
  }

  protected function runValidate(): void
  {
    $this->runContract();

    $this->contract->validate();
  }
}