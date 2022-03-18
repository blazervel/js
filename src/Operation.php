<?php

namespace Blazervel\Blazervel;

use Blazervel\Blazervel\Exceptions\BlazervelOperationException;
use Blazervel\Blazervel\Traits\WithModel;

abstract class Operation
{
  use WithModel;

  abstract protected function steps(): array;

  protected mixed $latestResponse;

  public function run(): mixed {

    $this->getModel();

    //$this->validate();

    foreach ($this->steps() as $stepMethod) :
      $latestResponse = $this->$stepMethod();
    endforeach;

    return $this->latestResponse = $latestResponse;
  }

}