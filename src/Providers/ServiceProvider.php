<?php

namespace Blazervel\Blazervel\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
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
