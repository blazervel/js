<?php

namespace Blazervel\Web\Attributes;

use Blazervel\Exceptions\BlazervelComponentAttributeForException;

class ForCollection
{
  public string $collection;
  public string $controllerClass;

  public function __construct(string $controllerClass, string $collection)
  {
    if (!class_exists($controllerClass)) :
      throw new BlazervelComponentAttributeForException(
        "Model class {$controllerClass} not found"
      );
    endif;

    $this->controllerClass = $controllerClass;
    $this->collection = $collection;
  }

  public function collection(): mixed
  {
    $controllerClass = $this->controllerClass;
    $controllerCollection = $this->collection;

    return (new $controllerClass)->$controllerCollection();
  }

  public function __invoke(): mixed
  {
    return $this->collection();
  }
}