<?php

namespace Blazervel\Blazervel\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

use Blazervel\Blazervel\Contract;

trait WithContract
{
  public Contract $contract;

  private function contract(): void
  {
    $utility = class_basename(get_parent_class());

    if ($modelProperty = $this->modelName) :
      $modelClassName = ucfirst($modelProperty);
      $contractClass = "\\App\\Contracts\\{$modelClassName}Contract";
    else :
      $contractClass = Str::replace($utility, 'Contract', get_called_class());
    endif;

    $this->contract = $contractClass::make(
      data: $this->request->all()
    );
  }

  private function validate(): void
  {
    $this->contract();

    $this->contract->validate();
  }
}