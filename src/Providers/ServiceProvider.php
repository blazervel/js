<?php

namespace Blazervel\Blazervel\Providers;

use Blazervel\Blazervel\Actions\Pages;
use Blazervel\Blazervel\Console\MakeActionCommand;
use Blazervel\Blazervel\Console\MakeAnonymousActionCommand;
use Blazervel\Blazervel\Console\Commands\BuildCommand;
use Blazervel\Blazervel\Support\Actions;
use Blazervel\Blazervel\Support\ActionRoutes;
use Illuminate\Routing\Router;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
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
            ->loadConfig()
            ->loadCommands();
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
            static::path('config/blazervel.php') => config_path('blazervel.php'),
        ], 'blazervel');

        return $this;
    }

    private function loadCommands(): self
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAnonymousActionCommand::class,
                MakeActionCommand::class,
                BuildCommand::class
            ]);
        }

        return $this;
    }

    private function loadViews(): self
    {
        $this->loadViewsFrom(
            static::path('resources/views'),
            'blazervel'
        );

        return $this;
    }

    private function registerAnonymousClassAliases(): self
    {
        $this->app->booting(function ($app) {
            $loader = AliasLoader::getInstance();

            collect([
                'Blazervel\\Action'           => \Blazervel\Blazervel\Action::class,
                'Blazervel\\WithModelActions' => \Blazervel\Blazervel\WithModelActions::class,
                'B'                           => \Blazervel\Blazervel\Support\Helpers::class,
            ])->map(fn ($class, $namespace) => (
                $loader->alias(
                    $namespace,
                    $class
                )
            ));

            if (Config::get('blazervel.actions.anonymous_classes', true)) {
                Actions::anonymousClasses()->map(fn ($class, $namespace) => (
                    $loader->alias(
                        $namespace,
                        $class
                    )
                ));
            }
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
        ActionRoutes::register();

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