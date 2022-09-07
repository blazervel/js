<?php

namespace Blazervel\Blazervel\Providers;

use Illuminate\Support\ServiceProvider;

class BlazervelServiceProvider extends ServiceProvider
{
    private string $pathTo = __DIR__.'/../..';

    public function boot()
    {
        $this->loadViews();
        $this->loadRoutes();
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
}
