<?php

namespace Blazervel;

use Blazervel\Blazervel\Exceptions\BlazervelContractException;
use Blazervel\Blazervel\Traits\WithModel;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorResponse;

abstract class Contract
{
  use WithModel;

  abstract protected function rules(array $data = []): array;

  public array $rules;
  public array $only;

  public function __construct(array $data, array $rules = null, string|array $only = null)
  {
    $this->runModel();

    $this->rules = $this->rules($data);

    if ($rules) :
      $this->rules = array_merge($this->rules, $rules);
    endif;

    if ($this->only = $only) :
      $this->rules = collect($this->rules)->only($only)->all();
      $data = collect($data)->only($only)->all();
    endif;

    $this->data = $data;
  }

  public static function make(array $data, array $rules = null, array $only = null): ValidatorResponse
  {
    $className = get_called_class();
    $contract = new $className($data, $rules, $only);

    return Validator::make(
      $contract->data, 
      $contract->rules, 
      $contract->only
    );
  }
  
}