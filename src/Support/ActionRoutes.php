<?php

namespace Blazervel\Blazervel\Support;

use Blazervel\Blazervel\Actions as ActionActions;
use Blazervel\Blazervel\Actions\Models as ModelActions;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

class ActionRoutes
{
    public static function register()
    {
        Route::middleware(['api', /*'auth:sanctum'*/])->group(function () {
            Route::prefix(static::endpointPrefix())->group(function() {
                // Model Action Routes
                Route::get(   'models/{model}',             ModelActions\Index::class);
                Route::post(  'models/{model}',             ModelActions\Store::class);
                Route::get(   'models/{model}/{id}',        ModelActions\Show::class);
                Route::put(   'models/{model}/{id}',        ModelActions\Update::class);
                Route::delete('models/{model}/{id}',        ModelActions\Destroy::class);
                Route::post(  'models/{model}/{id}/notify', ModelActions\Notify::class);

                // Action Routes
                Route::post(  'actions/{action}',    ActionActions\HandleAction::class);
                Route::get(   'actions/{action}',    ActionActions\HandleAction::class);
            });
        });
    }

    public static function endpointPrefix()
    {
        return Config::get('blazervel.actionsjs.endpoint_prefix', 'api/actionsjs');
    }
}