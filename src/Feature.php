<?php

namespace Blazervel\Blazervel;

use Illuminate\Support\{ Str, Collection };
use Illuminate\Support\Facades\{ Route, View };
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;

class Feature
{
  public string $name;
  public string $namespace;
  public string $path;
  public array $operations = [];

  public function __construct(string $name, string $namespace, string $path)
  {
    $this->name = $name;
    $this->namespace = $namespace;
    $this->path = $path;

    $this->retrieveOperations();
  }

  public static function componentLookup(string $componentNameOrPath): string|null
  {
    $conceptComponent = null;

    if (Str::of($componentNameOrPath)->contains(['.', '/'])) :
      $componentPath      = Str::replace('.', '/', $componentNameOrPath);
      $componentPath      = explode('/', $componentPath);
      $componentName      = Str::ucfirst(Str::camel(end($componentPath)));
      $componentNamespace = (new Collection($componentPath))->map(function($value){ return Str::ucfirst(Str::camel($value)); })->join('\\');
      $conceptComponent   = "App\\Features\\{$componentNamespace}";
    else :
      $componentName      = Str::ucfirst(Str::camel($componentNameOrPath));
    endif;

    $sharedComponent = "App\\Features\\Shared\\Components\\{$componentName}";
    $blazervelComponent = "Blazervel\\Blazervel\\Components\\Components\\{$componentName}";

    if ($conceptComponent && class_exists($conceptComponent)) :

      return $conceptComponent;

    elseif (class_exists($sharedComponent)) :

      return $sharedComponent;

    elseif (class_exists($blazervelComponent)) :

      return $blazervelComponent;

    endif;

    return null;
  }

  public static function viewLookup(string $componentNameOrPath): string|null
  {
    $conceptView = null;

    if (Str::of($componentNameOrPath)->contains(['.', '/'])) :
      $componentPath = Str::replace('.', '/', $componentNameOrPath);
      $conceptName   = explode('/', $componentPath)[0];
      $componentName = end(explode('/', $componentPath));
      $componentName = Str::snake($componentName, '-');
      $conceptView   = "blazervel.{$conceptName}::{$componentName}";
    else :
      $componentName = Str::snake($componentNameOrPath, '-');
    endif;

    if ($conceptView && View::exists($conceptView)) :
      return $conceptView;
    endif;
    
    if (View::exists($view = "blazervel.shared::{$componentName}")) :
      return $view;
    endif;
    
    if (View::exists($view = "blazervel::{$componentName}")) :
      return $view;
    endif;

    return null;
  }
  
}