<?php

namespace Blazervel\BlazervelJS\Http\Middleware;

use Blazervel\BlazervelJS\WithBlazervel;
use Closure;

class BlazervelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function __invoke($request, Closure $next)
    {
        $route = $request->route();
        $routeAction = $route->getAction();

        if (
            !$request->expectsJson() &&
            class_uses($routeAction['controller'], WithBlazervel::class)
        ) {
            $route->setAction(array_merge($routeAction, [
                'uses' => fn () => view((new $routeAction['controller'])->getRootView())
            ]));
        }

        return $next($request);
    }
}