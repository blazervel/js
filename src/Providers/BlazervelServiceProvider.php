<?php

namespace Blazervel\Blazervel\Providers;

use Illuminate\Support\ServiceProvider;

class BlazervelServiceProvider extends ServiceProvider 
{
  
  public function boot()
  {
    $this->loadViews();
    $this->loadRoutes();
    $this->loadTranslations();
  }

  private function loadViews()
  {
    $this->loadViewsFrom(
      "{$this->pathTo}/resources/views", 'blazervel'
    );
  }
  
  private function loadRoutes() 
  {
    $this->loadRoutesFrom(
      "{$this->pathTo}/routes/routes.php"
    );
  }

  private function loadTranslations() 
  {
    $this->loadTranslationsFrom(
      "{$this->pathTo}/lang", 
      'blazervel'
    );
  }

}