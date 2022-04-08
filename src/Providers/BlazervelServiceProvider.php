<?php

namespace Blazervel\Blazervel\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Blade;

use Blazervel\Blazervel\Concept;

use Illuminate\Support\ServiceProvider;

class BlazervelServiceProvider extends ServiceProvider 
{
  public function boot()
  {
    $this->loadRoutesFrom(
      __DIR__ . '/../../routes/web.php'
    );

    $this->loadViewsFrom(
      __DIR__ . '/../../resources/views', 'blazervel'
    );

    Blade::componentNamespace(
      'Blazervel\\Blazervel\\Blade\\Components', 
      'blazervel'
    );

    foreach (Concept::list() as $name => $concept) :
      $conceptName = Str::snake($name, '-');

      Blade::componentNamespace(
        "{$concept->namespace}\\Components", 
        "concepts.{$conceptName}"
      );

      $this->loadViewsFrom(
        "{$concept->path}/resources/views", "concepts.{$conceptName}"
      );

    endforeach;
  }
}