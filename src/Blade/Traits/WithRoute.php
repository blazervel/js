<?php

namespace Blazervel\Blazervel\Blade\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

trait WithRoute
{
  public function routeData(string|array $route): array
  {
    if (is_string($route)) :
      $route = [$route, null];
    endif;

    list($routeName, $routeArguments) = $route;

    $routeUrl = URL::route(
      $routeName,
      $routeArguments,
      false
    );

    $routes = new Collection((array) Route::getRoutes()->getIterator());
    $routeData = $routes->where('action.as', $routeName)->first();
    $routeMethod = (new Collection($routeData->methods))->whereNotIn(null, ['HEAD'])->first();

    return [
      'tag' => $routeMethod === 'GET' ? 'a' : 'form',
      'method' => $routeMethod,
      'url' => $routeUrl,
    ];
  }
}