<?php

namespace Blazervel\BlazervelQL\Providers;

use Blazervel\BlazervelQL\Console\Commands\BuildConfigCommand;
use Blazervel\BlazervelQL\Support\ApiRoutes;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this
            ->loadViews()
            ->loadCommands()
            ->loadRoutes();
    }

    private function loadCommands(): self
    {
        if (! $this->app->runningInConsole()) {
            return $this;
        }

        $this->commands([
            BuildConfigCommand::class
        ]);

        return $this;
    }

    private function loadViews(): self
    {
        $this->loadViewsFrom(
            static::path('resources/views'),
            'blazervelql'
        );

        return $this;
    }

    private function loadRoutes(): self
    {
        ApiRoutes::register();

        return $this;
    }

    static function path(string ...$path): string
    {
        return join('/', [
            Str::remove('src/Providers', __DIR__),
            ...$path
        ]);
    }
}