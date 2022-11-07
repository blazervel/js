<?php

namespace Blazervel\Blazervel\Actions\Config;

use Blazervel\Blazervel\Action;
use Blazervel\Blazervel\Support\Actions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use ReflectionMethod;
use Tightenco\Ziggy\Ziggy;

class Routes extends Action
{
    public array $config;

    public function handle()
    {
        $this->config = (new Ziggy)->toArray();

        $this
            ->setRouteActions()
            ->setRouteParams();

        return $this->config;
    }

    private function setRouteActions(): self
    {
        $routes = $this->config['routes'];
        $actions = $this->getRouteActions();

        collect($routes)
            ->filter(fn ($route) => isset($actions[$route['uri']]))
            ->map(function ($route, $name) use ($actions) {
                
                $action = $actions[$route['uri']] ?? null;

                $action = $action ? Actions::actionKey($action) : $action;

                $routes[$name]['action'] = $action;

                return [$name => $route];

            })
            ->collapse()
            ->all();

        $this->config['routes'] = $routes;

        return $this;
    }

    private function getRouteActions(): Collection
    {
        $actions = (array) Route::getRoutes();

        $actions = collect($actions)
                        ->filter(fn ($list, $key) => Str::contains($key, 'actionList'))
                        ->first();

        return (
            collect($actions)
                ->filter(fn ($route, $action) => is_subclass_of($action, Action::class))
                ->map(fn ($route, $action) => [$route->uri => $action])
                ->collapse()
        );
    }

    private function setRouteParams(): self
    {
        $routes = $this->config['routes'];
        $actions = $this->getRouteActions();

        collect($routes)
            ->filter(fn ($route) => isset($actions[$route['uri']]))
            ->map(function ($route, $name) use ($actions) {
                $action = $actions[$route['uri']];

                if (Str::contains('@', $action)) {
                    list($class, $method) = explode('@', $action);
                }

                $class = $action;
                $method = '__invoke';

                if (is_subclass_of($class, Action::class)) {
                    $method = 'handle';
                }

                $routes[$name]['parameters'] = $this->getRouteParams($class, $method);

                return [$name => $route];
            })
            ->collapse()
            ->all();

        $this->config['routes'] = $routes;

        return $this;
    }

    private function getRouteParams(string $class, string $method)
    {
        $reflect = new ReflectionMethod($class, $method);

        return (
            collect($reflect->getParameters())
                ->filter(fn ($p) => $p->getType()->getName() !== Request::class)
                ->map(fn ($p) => [$p->getName() => $p->getType()->getName()])
                ->collapse()
        );
    }
}