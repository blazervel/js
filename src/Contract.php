<?php

namespace Blazervel\Blazervel;

use Blazervel\Blazervel\Exceptions\BlazervelContractException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorResponse;

class Contract
{
  public array $rules;
  public array $data;
  public ?array $only = null;
  public ?array $messages = null;

  public function __construct(string $action, array $data)
  {
    $this->rules = $this->$action(
      $this->data = $data
    );
  }

  public static function make(string $action, array $data): ValidatorResponse
  {
    $calledClassNamespace = Concept::conceptNamespace(get_called_class());
    $contractClass = "{$calledClassNamespace}\\Contract";
    $contract = new $contractClass($action, $data);

    return Validator::make(
      $contract->data,
      $contract->rules,
    );
  }

  public function rulesLivewire(string $prefix = null): array
  {
    $prefix = $prefix ?: Str::snake(class_basename(get_class($this)));

    return (new Collection($this->rules()))->map(function ($rules, $field) use ($prefix) {

      return ["{$prefix}.{$field}" => $rules];

    })->flatMap(function ($values) {
        
      return $values;

    })->all();
  }

  private function fillableFields(string $modelClass): array
  {
    $model = new $modelClass;
    return (
      $model->fillable ?: array_keys(array_diff_key(
        $model->attributes, 
        array_flip(['id', 'guid'])
      ))
    );
  }

  private function rulesFromTableSchema(string $modelClass): array
  {
    $model = new $modelClass;
    $schema = DB::select("describe {$model->getTable()}");
    $fillable = $this->fillableFields($modelClass);

    return (new Collection($schema))->whereIn('Field', $fillable)->map(function ($field) {

      $field_rules = [];
      $required    = $field->Null == 'NO' && is_null($field->Default);
      $type_key    = (string) Str::of($field->Type)->match('/(.*)\(.*/');
      $size        = (string) Str::of($field->Type)->match('/.*\((.*)\)/');

      // Set required rule
      $field_rules[] = $required ? 'required' : 'nullable';

      // Set type rule
      foreach([
        'integer' => ['int', 'bigint'],
        'float'   => ['decimal'],
        'date'    => ['time', 'date', 'timestamp'],
        'string'  => ['varchar', 'text', 'longtext'],
      ] as $rule_name => $type_keys) :

        if (!in_array($type_key, $type_keys)) continue;

        if ($rule_name == 'string' && $size) :
          $field_rules[] = "max:{$size}";
        endif;

        $field_rules[] = $rule_name;

      endforeach;

      return [$field->Field => $field_rules];

    })->flatMap(function ($values) {
        
      return $values;

    })->all();
  }
  
}