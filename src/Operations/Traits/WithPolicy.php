<?php

namespace Blazervel\Blazervel\Operations\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Blazervel\Blazervel\Concept;
use Blazervel\Blazervel\Policy;

trait WithPolicy
{

  protected Policy $policy;

  protected function runPolicy(): void
  {
    $calledClassNamespace = Concept::conceptNamespace(get_called_class());
    $policyClass = "{$calledClassNamespace}\\Policy";

    $modelName = $this->modelName ?? null;

    if (!$modelName || !($model = $this->$modelName)) :

      $this->runModel();

      $modelName = $this->modelName;
      $model = $this->$modelName;

    endif;

    $this->policy = $policyClass::make(
      action: $this->action,
      model: $model
    );
  }

  public function runAuthorize(): void
  {
    $this->runPolicy();

    $this->policy->authorize();
  }
}