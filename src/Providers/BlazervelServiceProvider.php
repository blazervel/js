<?php

namespace Blazervel\Blazervel\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Blade;
use Illuminate\Console\Scheduling\Schedule;

use Blazervel\Blazervel\View\TagCompiler;
use Blazervel\Blazervel\Feature;

use Illuminate\Support\ServiceProvider;

class BlazervelServiceProvider extends ServiceProvider 
{
  private string $pathTo = __DIR__ . '/../..';

	public function register()
	{
    // foreach (Feature::operations() as $operation) :

    //     $operationClass = $operation::class;

    //     $this->app->bind($operationClass, function($app) use ($operationClass) {
    //       return new $operationClass(
    //         request: $app->request,
    //       );
    //     });

    // endforeach;
	}

  public function boot()
  {
    $this->loadViews();
    $this->loadComponents();
    $this->loadRoutes();
    $this->loadTranslations();
    // $this->scheduleTasks();
  }

  private function loadViews()
  {
    $this->loadViewsFrom(
      "{$this->pathTo}/resources/views", 'blazervel'
    );

    // foreach (Feature::list() as $name => $concept) :
    //   $conceptName = Str::snake($name, '-');

    //   $this->loadViewsFrom(
    //     "{$concept->path}/resources/views", "blazervel.{$conceptName}"
    //   );
    // endforeach;
  }

  private function loadComponents()
  {
    Blade::componentNamespace(
      'Blazervel\\Blazervel\\Components\\Components', 
      'blazervel'
    );

    // foreach (Feature::list() as $name => $concept) :
    //   $conceptName = Str::snake($name, '-');

    //   Blade::componentNamespace(
    //     "{$concept->namespace}\\Components", 
    //     "blazervel.{$conceptName}"
    //   );
    // endforeach;

    if (method_exists($this->app['blade.compiler'], 'precompiler')) {
      $this->app['blade.compiler']->precompiler(function ($string) {
        return app(TagCompiler::class)->compile($string);
      });
    }
  }

  private function scheduleTasks()
  {
    $this->app->booted(function () {
      foreach(Feature::scheduleables() as $schedule) :
        $className = $schedule::class;
        $arguments = $schedule->scheduleArguments;
        $frequency = $schedule->scheduleFrequency;

        app(Schedule::class)->job(
          new $className(...$arguments)
        )->$frequency();
      endforeach;
    });
  }
  
  public function loadRoutes() 
  {
    $this->loadRoutesFrom(
      "{$this->pathTo}/routes/web.php"
    );

    // Feature::registerRoutes();
  }

  public function loadTranslations() 
  {
    $this->loadTranslationsFrom(
      "{$this->pathTo}/lang", 
      'blazervel'
    );
  }
}