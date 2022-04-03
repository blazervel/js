<?php

namespace Blazervel\Blazervel\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

use Blazervel\Blazervel\Contract;

trait WithContract
{
  public Contract $contract;

  protected function runContract(): void
  {
    $utility = class_basename(__CLASS__);

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

  protected function runValidate(): void
  {
    $this->contract();

    $this->contract->validate();
  }
}