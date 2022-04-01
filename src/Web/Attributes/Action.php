<?php

namespace Blazervel\Blazervel\Web\Attributes;

use Blazervel\Blazervel\Exceptions\BlazervelComponentAttributeClickException;

class Action
{
  public string $action;
  public string $controllerClass;

  public function __construct(string $controllerClass, string $action)
  {
    if (!class_exists($controllerClass)) :
      throw new BlazervelComponentAttributeClickException(
        "Controller class {$controllerClass} not found"
      );
    endif;

    $this->controllerClass = $controllerClass;
    $this->action = $action;
  }

  public function action(): mixed
  {
    $controllerClass = $this->controllerClass;
    $controllerAction = $this->action;

    // is static

    // requires args

    return (new $controllerClass)->$controllerAction();
  }

  public function __invoke(): mixed
  {
    return $this->action();
  }
}