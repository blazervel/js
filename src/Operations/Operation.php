<?php

namespace Blazervel\Blazervel\Operations;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Blazervel\Blazervel\Exceptions\BlazervelOperationException;

class Operation
{
  public string $name;
  public string $concept;
  public string $className;
  public string $namespace;
  public string $method;
  public ?string $uri = null;

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

  public function __construct(string $className)
  {
    $this->className = $className;
    $this->concept   = explode('\\', Str::remove("App\\Concepts\\", $className))[0];
    $this->name      = class_basename($className);
    $this->namespace = Str::remove("\\{$this->name}", $className);
    $this->method    = $this->actionMethodMap[Str::camel($this->name)] ?? (new $className)->method ?? 'GET';
    $this->uri       = $this->uri();
  }

  public function uri(): string|null
  {
    $className = $this->className;
    $action = new $className;

    if ($action->uri === false) :
      return null;
    endif;

    $conceptSlug = Str::plural(Str::snake($this->concept, '-'));
    $actionSlug  = Str::snake($this->name, '-');
    $uri         = $this->actionUriMap[Str::camel($this->name)] ?? $action->uri ?? (in_array('model', $action->steps()) ? "/{model}/{action}" : "/{action}");
    $uri         = "{$conceptSlug}{$uri}";
    $uri         = Str::replace('{model}', '{' . $action->modelName . '}', $uri);
    $uri         = Str::replace('{action}', $actionSlug, $uri);
    $uri         = Str::of($uri)->endsWith('/') ? Str::replaceLast('/', '', $uri) : $uri;
    
    return $uri;
  }

  public function run(...$arguments)
  {
    $className = $this->className;

    return $className::run(...$arguments);
  }
}