<?php

namespace Blazervel\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

trait WithModel
{
  protected string $modelName;
  protected ?string $modelClass = null;
  
  protected function runModel(): void
  {
    if ($this->modelClass) :

      $modelClass = $this->modelClass;
      $modelClassName = class_basename($this->modelClass);

    else :

      $utility        = class_basename(__CLASS__);
      $exceptionClass = "Blazervel\\Blazervel\\Exceptions\\Blazervel{$utility}Exception";
      $className      = class_basename(get_called_class());
      $currentRoute   = Route::getCurrentRoute();

      if (!Str::of($className)->contains($utility)) :
        throw new $exceptionClass(
          "You've improperly named your {$utility}. The convention should be \"[ModelName]{$utility}}\"."
        );
      endif;
  
      $modelClassName = Str::remove($utility, $className);
  
      if (class_exists("\\App\\{$modelClassName}")) :
        $modelClass = "\\App\\{$modelClassName}"; 
      else :
        $modelClass = "\\App\\Models\\{$modelClassName}";
      endif;

      $this->modelClass = $modelClass;

    endif;

    $modelName = Str::lower($modelClassName);
    $modelProperty = Str::camel($modelClassName);

    if ($modelLookup = $currentRoute->parameters[$modelName] ?? false) :
      $model = $modelClass::find($modelLookup);
    else :
      $model = new $modelClass;
    endif;

    $this->modelName = $modelProperty;
    $this->$modelProperty = $model;
  }
}