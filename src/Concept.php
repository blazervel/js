<?php

namespace Blazervel\Blazervel;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Filesystem\Filesystem;

use Blazervel\Blazervel\Exceptions\BlazervelConceptException;

use Blazervel\Blazervel\Operations\Operation as OperationObject;

class Concept
{
  public string $name;
  public string $namespace;
  public string $path;
  public array $operations;

  public function __construct(string $name, string $namespace, string $path)
  {
    $this->name = $name;
    $this->namespace = $namespace;
    $this->path = $path;

    $this->retrieveOperations();
  }

  public function retrieveOperations(): array
  {
    $operations = [];
    $files = (new FileSystem)->files(
      "{$this->path}/Operations"
    );

    foreach ($files as $file) :
      $name = Str::remove(
        '.php', 
        basename($file->getFileName())
      );

      $operations[$name] = new OperationObject(
        className: "{$this->namespace}\\Operations\\{$name}"
      );
    endforeach;

    return $this->operations = $operations;
  }

  public static function list(): array
  {
    $concepts = [];

    $directories = (new FileSystem)->directories(
      base_path() . '/app/Concepts'
    );

    foreach($directories as $path) :
      $name = basename($path);
      $concepts[$name] = new self(
        name: $name,
        namespace: "App\\Concepts\\{$name}",
        path: $path,
      );
    endforeach;

    return $concepts;
  }

  public static function conceptFor(string $conceptItemClass): self
  {
    return (
      new self(
        name: self::conceptName($conceptItemClass),
        namespace: self::conceptNamespace($conceptItemClass),
        path: self::conceptPath($conceptItemClass),
      )
    );
  }

  public static function conceptName(string $conceptItemClass)
  {
    $conceptNamespace = self::conceptNamespace(
      $conceptItemClass
    );

    return (
      array_reverse(
        explode('\\', $conceptNamespace)
      )[0]
    );
  }

  public static function conceptPath(string $conceptItemClass)
  {
    $namespace = self::conceptNamespace($conceptItemClass);

    return base_path(
      Str::replace('App', 'app', 
        Str::replace('\\', '/', $namespace)
      )
    );
  }

  public static function conceptNamespace(string $conceptItemClass)
  {
    $utility = class_basename(
      get_parent_class($conceptItemClass) // contract|operation|policy|component
    );

    // Need smarter way of finding closest Concept namespace
    // May want to support plural and singular for Concept Items
    $utilityNamespaceSlug = in_array($utility, ['Operation', 'Component']) ? "{$utility}s" : $utility;

    $conceptItemClassNamespace = explode('\\', $conceptItemClass);

    $conceptItemClassNamespace = array_slice(
      $conceptItemClassNamespace,
      0,
      array_search($utilityNamespaceSlug, $conceptItemClassNamespace)
    );

    return join('\\', $conceptItemClassNamespace);
  }

  public static function componentFor(string $conceptItemClass)
  {
    $action = class_basename($conceptItemClass);
    $namespace = self::conceptNamespace(
      conceptItemClass: $conceptItemClass
    );

    return "{$namespace}\\Components\\{$action}";
  }

  public static function registerRoutes()
  {
    foreach(self::list() as $name => $concept) :
      foreach($concept->operations as $name => $operation) :
        $method = Str::lower($operation->method);
        Route::$method(
          $operation->uri, 
          $operation->className
        );
      endforeach;
    endforeach;
  }

  public static function __callStatic($name, $arguments)
  {
    //
  }

  public function __call($name, $arguments)
  {
    //
  }
  
}