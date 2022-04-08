<?php

namespace Blazervel\Blazervel;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Routing\Route;
use Illuminate\Contracts\Container\Container;

use Inertia\Inertia;

use Blazervel\Blazervel\Exceptions\BlazervelOperationException;
use Blazervel\Blazervel\Traits\WithModel;
use Blazervel\Blazervel\Traits\WithContract;
use Blazervel\Blazervel\Traits\WithPolicy;

abstract class Operation
{
  use WithModel, WithContract, WithPolicy;

  protected Request $request;
  protected array $arguments;
  public string $method;
  public null|string|bool $uri = null;

  abstract public function steps(): array;

  public static function run(...$arguments): mixed 
  {
    $calledClass = get_called_class();

    $operation = new $calledClass;

    $operation->action    = Str::camel(class_basename($calledClass));
    $operation->arguments = $arguments;
    $operation->request   = request();
    $operation->method    = $operation->method ?: $operation->runMethod();

    foreach ($operation->steps() as $stepMethod) :

      if ($stepMethod == 'model' && !method_exists($calledClass, 'model')) :
        $lastResponse = $operation->runModel();
        continue;
      endif;

      if ($stepMethod == 'authorize' && !method_exists($calledClass, 'authorize')) :
        $lastResponse = $operation->runAuthorize();
        continue;
      endif;
  
      if ($stepMethod == 'validate' && !method_exists($calledClass, 'validate')) :
        $lastResponse = $operation->runValidate();
        continue;
      endif;

      if ($stepMethod == 'collection' && !method_exists($calledClass, 'collection')) :
        $lastResponse = $operation->runCollection();
        continue;
      endif;

      $lastResponse = $operation->$stepMethod();

    endforeach;

    return $lastResponse;
  }

  public function __invoke(Container $container, Route $route)
  {
    $componentClass = Concept::componentFor($this::class);
    $concept = Concept::conceptFor($this::class);
    $actionName = class_basename($this::class);

    if (
      !class_exists($componentClass) &&
      File::exists("{$concept->path}/Components/{$actionName}.js")
    ) :

      if (
        !View::exists('concepts.shared::layouts.inertia') &&
        View::exists('blazervel::layouts.inertia')
      ) :
        Inertia::setRootView(
          'blazervel::layouts.inertia'
        );
      else :
        Inertia::setRootView(
          'concepts.shared::layouts.inertia'
        );
      endif;
      
      return Inertia::render("{$concept->name}/Components/{$actionName}", [
        // data
      ]);
    endif;

    $component = new $componentClass;
    $viewName = Str::snake($actionName, '-');
    $view = "blazervel::{$viewName}";
    $layout = 'blazervel::layouts.app';
    $conceptSlug = Str::snake($concept->name, '-');

    if (
      View::exists("concepts.{$conceptSlug}::layouts.app")
    ) :
      $layout = "concepts.{$conceptSlug}::layouts.app";
    elseif (
      View::exists('concepts.shared::layouts.app')
    ) :
      $layout = 'concepts.shared::layouts.app';
    endif;

    return $component->renderWithLayout($layout);
  }

  public function __get(string $name): mixed
  {
    if (in_array($name, ['model', 'modelName'])) :
      $this->runModel();
    endif;

    if (property_exists($this, $name)) :

      return $this->$name;

    elseif (method_exists($this, $name)) :

      return $this->$name();

    elseif (isset($this->arguments[$name])) :

      return $this->arguments[$name];

    endif;

    return $this->$name;
  }

}