<?php

namespace Blazervel\Blazervel\Providers;

use Blazervel\Actions\Console\MakeActionCommand;
use Blazervel\Actions\Console\MakeAnonymousActionCommand;
use Blazervel\Actions\Support\Actions;
use Blazervel\Blazervel\Support\ActionRoutes;
use Lorisleiva\Actions\Facades\Actions as LaravelActions;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    private string $path = __DIR__ . '/../..';

    public function register()
    {
        $this->ensureDirectoryExists();
        $this->registerAnonymousClassAliases();
    }

    public function boot()
    {
        $this->loadViews();
        $this->loadRoutes();
        $this->loadTranslations();
        $this->loadConfig();

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeActionCommand::class,
                MakeAnonymousActionCommand::class
            ]);
        }
    }

    private function ensureDirectoryExists()
    {
        File::ensureDirectoryExists(
            Actions::dir()
        );
    }

    private function loadConfig()
    {
        $this->publishes([
            "{$this->path}/config/blazervel.php" => config_path('blazervel.php'),
        ], 'blazervel');
    }

    private function loadViews()
    {
        $this->loadViewsFrom(
            "{$this->path}/resources/views", 'blazervel'
        );

        return $this;
    }

    private function registerAnonymousClassAliases(): void
    {
        if (! Config::get('blazervel.actions.anonymous_classes', true)) {
            return;
        }

        $this->app->booting(function ($app) {
            $loader = AliasLoader::getInstance();

            Actions::anonymousClasses()->map(fn ($class, $namespace) => (
                $loader->alias(
                    $namespace,
                    $class
                )
            ));
        });
    }

    private function loadRoutes()
    {
        // LaravelActions::registerRoutes(
        //     Actions::directories()
        // );

        ActionRoutes::register();
    }

    private function loadTranslations()
    {
        $this->loadTranslationsFrom(
            "{$this->path}/lang",
            'blazervel-actions'
        );
    }
}
