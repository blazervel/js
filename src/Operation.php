<?php

namespace Blazervel\Blazervel;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{ View, Log };
use Illuminate\Routing\Route;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;

use Blazervel\Blazervel\Exceptions\BlazervelOperationException;
use Blazervel\Blazervel\Operations\Shared;
use Blazervel\Blazervel\Operations\Traits\{ 
  WithModel,
  WithContract,
  WithPolicy,
  WithQueueable,
  WithScheduleable 
};

abstract class Operation implements ShouldQueue
{
  use WithModel, WithContract, WithPolicy, WithQueueable, WithScheduleable;

  protected Request $request;
  protected array $arguments = [];
  public ?string $method = null;
  public ?string $uriPrefix = null;
  public null|string|bool $uri = null;
  public string $name;
  public string $concept;
  public string $className;
  public string $namespace;
  public array $httpMiddleware = ['auth'];
  private ?Shared $shared;

  private $actionMethodMap = [
    'index'   => 'GET',
    'show'    => 'GET',
    'create'  => 'GET',
    'store'   => 'POST',
    'edit'    => 'GET',
    'update'  => 'PUT',
    'destroy' => 'DELETE',
  ];

  private $actionUriMap = [
    'index'   => '/',
    'show'    => '/{model}',
    'create'  => '/{action}',
    'store'   => '/',
    'edit'    => '/{model}/{action}',
    'update'  => '/{model}/{action}',
    'destroy' => '/{model}',
  ];

  abstract public function steps(): array;

  public function __construct(mixed ...$arguments)
  {
    $this->arguments = $arguments;
    $this->request = request();

    $this->props();
    // $this->shared();
    $this->method();
    $this->uri();
  }

  public function handle()
  {
    foreach ($this->steps() as $stepMethod) :

      if ($stepMethod == 'model' && !method_exists($calledClass, 'model')) :
        $lastResponse = $this->runModel();
        continue;
      endif;

      if ($stepMethod == 'authorize' && !method_exists($calledClass, 'authorize')) :
        $lastResponse = $this->runAuthorize();
        continue;
      endif;
  
      if ($stepMethod == 'validate' && !method_exists($calledClass, 'validate')) :
        $lastResponse = $this->runValidate();
        continue;
      endif;

      if ($stepMethod == 'collection' && !method_exists($calledClass, 'collection')) :
        $lastResponse = $this->runCollection();
        continue;
      endif;

      $lastResponse = $this->$stepMethod();

    endforeach;

    return $lastResponse;
  }

  public static function run(...$arguments): mixed 
  {
    $calledClass = get_called_class();

    return (new $calledClass(...$arguments))->handle();
  }

  private function props(): void
  {
    $calledClass     = get_called_class();
    $this->action    = Str::camel(class_basename($calledClass));
    $this->concept   = Concept::conceptName($calledClass);
    $this->name      = class_basename($calledClass);
    $this->namespace = Str::remove("\\{$this->name}", $calledClass);
  }

  private function shared()
  {
    $sharedClass = 'App\\Concepts\\Shared\\Operations\\Shared';

    $this->shared = class_exists($sharedClass) ? new $sharedClass(...$this->arguments) : null;
  }

  private function method(): void
  {
    $this->method = $this->actionMethodMap[Str::camel($this->name)] ?? $this->method ?: 'GET';
  }

  private function uri(): void
  {
    $prefix = $this->shared->uriPrefix ?? $this->uriPrefix ?: false;

    if ($this->uri === false) :

      return;

    elseif ($this->uri) :

      $this->uri = (
        $prefix
          ? "{$prefix}/{$this->uri}"
          : $this->uri
      );

    endif;

    $this->runModel();

    $conceptSlug = Str::plural(Str::snake($this->concept, '-'));
    $actionSlug  = Str::snake($this->name, '-');
    $uri         = $this->actionUriMap[Str::camel($this->name)] ?? $this->uri ?? (in_array('model', $this->steps()) ? "/{model}/{action}" : "/{action}");
    $uri         = "{$conceptSlug}{$uri}";
    $uri         = Str::replace('{model}', '{' . $this->modelName . '}', $uri);
    $uri         = Str::replace('{action}', $actionSlug, $uri);
    $uri         = Str::of($uri)->endsWith('/') ? Str::replaceLast('/', '', $uri) : $uri;
    
    $this->uri (
      $prefix
          ? "{$prefix}/{$uri}"
          : $uri
    );
  }

  private function component(): string
  {
    $componentClass = Concept::componentFor($this::class);
    $concept        = Concept::conceptFor($this::class);
    $actionName     = class_basename($this::class);
    $layout         = 'blazervel::layouts.app';
    $conceptSlug    = Str::snake($concept->name, '-');
    $component      = new $componentClass;

    if ($component->hasJsView() && class_exists('\\Inertia\\Inertia')) :

      if (
        !View::exists('blazervel.shared::layouts.inertia') &&
        View::exists('blazervel::layouts.inertia')
      ) :
        \Inertia\Inertia::setRootView(
          'blazervel::layouts.inertia'
        );
      else :
        \Inertia\Inertia::setRootView(
          'blazervel.shared::layouts.inertia'
        );
      endif;
      
      return \Inertia\Inertia::render(
        "{$concept->name}/resources/js/{$actionName}",
        $component->data()
      );

    endif;

    return $component->renderWithLayout();
  }

  public function __invoke(Container $container, Route $route)
  {
    return $this->component();
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