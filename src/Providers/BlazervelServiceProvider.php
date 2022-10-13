<?php

namespace Blazervel\Blazervel\Providers;

use Illuminate\Support\ServiceProvider;

class BlazervelServiceProvider extends ServiceProvider
{
    private string $path = __DIR__ . '/../..';

    public function boot()
    {
        $this->loadViews();
        $this->loadRoutes();
    }

    private function loadViews()
    {
        $this->loadViewsFrom(
            "{$this->path}/resources/views", 'blazervel'
        );
    }

    private function loadRoutes()
    {
        $this->loadRoutesFrom(
            "{$this->path}/routes/routes.php"
        );
    }
}
