<?php

namespace Blazervel;

use Illuminate\Http\Request;

use Blazervel\Exceptions\BlazervelOperationException;
use Blazervel\Traits\WithModel;
use Blazervel\Traits\WithContract;

abstract class Operation
{
  use WithModel, WithContract;

  abstract protected function steps(): array;

  protected Request $request;
  protected array $arguments;

  public static function run(...$arguments): mixed {

    $calledClass = get_called_class();

    $operation = new $calledClass(...$arguments);

    $operation->arguments = $arguments;

    $operation->request = request();

    foreach ($operation->steps() as $stepMethod) :

      if ($stepMethod == 'model' && !method_exists($calledClass, 'model')) :
        $lastResponse = $operation->runModel();
        continue;
      endif;
  
      if ($stepMethod == 'contract' && !method_exists($calledClass, 'contract')) :
        $lastResponse = $operation->runContract();
        continue;
      endif;
  
      if ($stepMethod == 'validate' && !method_exists($calledClass, 'validate')) :
        $lastResponse = $operation->runValidate();
        continue;
      endif;

      $lastResponse = $operation->$stepMethod();

    endforeach;

    return $lastResponse;
  }

}