<?php

namespace Blazervel\Blazervel;

use Illuminate\Http\Request;

use Blazervel\Blazervel\Exceptions\BlazervelOperationException;
use Blazervel\Blazervel\Traits\WithModel;
use Blazervel\Blazervel\Traits\WithContract;

abstract class Operation
{
  use WithModel, WithContract;

  abstract protected function steps(): array;

  protected Request $request;

  public static function run(): mixed {

    $calledClass = get_called_class();

    $operation = new $calledClass;

    $operation->request = request();

    foreach ($operation->steps() as $stepMethod) :

      if ($stepMethod == 'model' && method_exists($calledClass, 'model')) :
        $lastResponse = $operation->model();
        continue;
      endif;
  
      if ($stepMethod == 'contract' && method_exists($calledClass, 'contract')) :
        $lastResponse = $operation->contract();
        continue;
      endif;
  
      if ($stepMethod == 'validate' && method_exists($calledClass, 'validate')) :
        $lastResponse = $operation->validate();
        continue;
      endif;

      $lastResponse = $operation->$stepMethod();

    endforeach;

    return $lastResponse;
  }

}