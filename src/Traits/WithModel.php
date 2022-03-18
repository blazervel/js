<?php

namespace Blazervel\Blazervel\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

trait WithModel
{
  private function getModel(): void
  {
    $utility        = class_basename($this);
    $exceptionClass = "Blazervel\\Blazervel\\Exceptions\\Blazervel{$utility}Exception";
    $className      = class_basename(get_called_class());
    $currentRoute   = Route::getCurrentRoute();
  
    if (!Str::contains($utility, $className)) :
      throw new $exceptionClass(
        "You've improperly named your contract. The convention should be \"[ModelName]{$utility}}\"."
      );
    endif;
  
    $modelClassName = Str::remove($utility, $className);
    $modelName = Str::lower($modelClassName);
    $modelProperty = Str::camel($modelClassName);

    if (class_exists("\\App\\{$modelClassName}")) :
      $modelClass = "\\App\\{$modelClassName}"; 
    else :
      $modelClass = "\\App\\Models\\{$modelClassName}";
    endif;

    if ($modelLookup = $currentRoute->parameters[$modelName] ?? false) :
      $model = $modelClass::find($modelLookup);
    else :
      $model = new $modelClass;
    endif;

    $this->$modelProperty = $model;
  }
}