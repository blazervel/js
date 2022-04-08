<?php

namespace Blazervel\Blazervel\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Blazervel\Blazervel\Concept;

trait WithModel
{
  protected string $modelName;
  protected string $collectionName;
  protected ?string $modelClass = null;

  private function getModelProperties()
  {
    if ($this->modelClass) :

      $modelClass = $this->modelClass;
      $modelClassName = class_basename($this->modelClass);

    else :

      $calledClassNamespace = Concept::conceptNamespace(get_called_class());
      $modelClassName       = class_basename($calledClassNamespace);
      $currentRoute         = Route::getCurrentRoute();
      $modelClass           = "\\App\\Models\\{$modelClassName}";

      $this->modelClass = $modelClass;

    endif;

    $modelName = Str::camel($modelClassName);

    $this->modelName = $modelName;

    $collectionName = Str::plural($modelName);

    $this->collectionName = $collectionName;
  }
  
  protected function runModel(): void
  {
    $this->getModelProperties();

    $modelName = $this->modelName;
    $modelClass = $this->modelClass;

    if ($modelLookup = $currentRoute->parameters[$modelName] ?? false) :
      
      $model = $modelClass::find($modelLookup);
      
    elseif(isset($this->arguments[$modelName])) :

      $model = $this->arguments[$modelName];

    else :
      
      $model = new $modelClass;
      
    endif;

    $this->$modelName = $model;
  }

  public function runCollection()
  {
    $this->getModelProperties();

    $modelClass = $this->modelClass;
    $collectionName = $this->collectionName;
    
    $this->$collectionName = $modelClass::get();
  }
}