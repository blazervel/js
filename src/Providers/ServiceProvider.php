<?php

namespace Blazervel\Blazervel\Providers;

use Blazervel\Blazervel\Actions\Pages;
use Blazervel\Blazervel\Console\MakeActionCommand;
use Blazervel\Blazervel\Console\MakeAnonymousActionCommand;
use Blazervel\Blazervel\Support\Actions;
use Blazervel\Blazervel\Support\ActionRoutes;

use Illuminate\Routing\Router;
use Illuminate\Foundation\AliasLoader;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

use Lorisleiva\Actions\Facades\Actions as LaravelActions;

class ServiceProvider extends BaseServiceProvider
{
    private string $path = __DIR__ . '/../..';

    public function register()
    {
        $this
            ->ensureDirectoryExists()
            ->registerAnonymousClassAliases()
            ->registerRouterMacro();
    }

    public function boot()
    {
        $this
            ->loadViews()
            ->loadRoutes()
            ->loadTranslations()
            ->loadConfig();

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeActionCommand::class,
                MakeAnonymousActionCommand::class
            ]);
        }
    }

    private function ensureDirectoryExists(): self
    {
        File::ensureDirectoryExists(
            Actions::dir()
        );

        return $this;
    }

    private function loadConfig(): self
    {
        $this->publishes([
            "{$this->path}/config/blazervel.php" => config_path('blazervel.php'),
        ], 'blazervel');

        return $this;
    }

    private function loadViews(): self
    {
        $this->loadViewsFrom(
            "{$this->path}/resources/views", 'blazervel'
        );

        return $this;
    }

    private function registerAnonymousClassAliases(): self
    {
        if (! Config::get('blazervel.actions.anonymous_classes', true)) {
            return $this;
        }

        $this->app->booting(function ($app) {
            $loader = AliasLoader::getInstance();

            collect([
                'Blazervel\\Action'           => 'Blazervel\\Blazervel\\Action',
                'Blazervel\\WithModelActions' => 'Blazervel\\Blazervel\\WithModelActions',
                'B'                           => 'Blazervel\\Blazervel\\Support\\Helpers',
            ])->map(fn ($class, $namespace) => (
                $loader->alias(
                    $namespace,
                    $class
                )
            ));

            Actions::anonymousClasses()->map(fn ($class, $namespace) => (
                $loader->alias(
                    $namespace,
                    $class
                )
            ));
        });

        return $this;
    }

    private function registerRouterMacro(): self
    {
        Router::macro('blazervel', fn ($uri, $component, $props = []) => (
            $this
                ->match(['POST', 'GET', 'HEAD'], $uri, Pages\Show::class)
                ->defaults('component', $component)
                ->defaults('props', $props)
        ));

        return $this;
    }

    private function loadRoutes(): self
    {
        // LaravelActions::registerRoutes(
        //     Actions::directories()
        // );

        ActionRoutes::register();

        return $this;
    }

    private function loadTranslations(): self
    {
        $this->loadTranslationsFrom(
            "{$this->path}/lang",
            'blazervel-actions'
        );

        return $this;
    }
}
