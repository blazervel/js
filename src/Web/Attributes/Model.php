<?php

namespace Blazervel\Blazervel\Web\Attributes;

use Blazervel\Blazervel\Exceptions\BlazervelComponentAttributeModelException;
use Illuminate\Database\Schema\Builder;

class Model
{
  public string $field;
  public string $fieldType;
  public string $modelClass;
  public ?int $modelId;

  public function __construct(string $modelClass, string $field, int $modelId = null)
  {
    if (class_exists($modelClass)) :
      throw new BlazervelComponentAttributeModelException(
        "Model class {$modelClass} not found"
      );
    endif;

    $this->modelClass = $modelClass;
    $this->field = $field;
    // $this->fieldType = Builder::getColumnType(
    //   (new $modelClass)->getTable(),
    //   $field
    // );
    $this->modelId = $modelId;
  }

  public function field(int $modelId = null): mixed
  {
    $modelClass = $this->modelClass;
    $modelId = $modelId ?: $this->modelId;
    $modelField = $this->field;

    if ($modelId) :
      $model = $modelClass::find($modelId);
    endif;

    return $model->$modelField;
  }

  public function __invoke(int $modelId = null): mixed
  {
    return $this->field($modelId);
  }
}