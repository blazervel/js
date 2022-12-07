<?php

namespace Blazervel\Blazervel\Support;

use Blazervel\Blazervel\Actions as ActionActions;
use Blazervel\Blazervel\Actions\Models as ModelActions;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;

class ActionRoutes
{
    private string $endpointPrefix = '/api/blazervel';

    private array $middleware = ['api'];

    public static function register(): void
    {
        (new self)->registerRoutes();
    }

    public function registerRoutes(): void
    {
        Route::middleware($this->middleware)->group(function () {
            Route::prefix($this->endpointPrefix)->group(function() {
                // Model Action Routes
                Route::get(   'models/{model}',             ModelActions\Index::class);
                Route::post(  'models/{model}',             ModelActions\Store::class);
                Route::get(   'models/{model}/{id}',        ModelActions\Show::class);
                Route::put(   'models/{model}/{id}',        ModelActions\Update::class);
                Route::delete('models/{model}/{id}',        ModelActions\Destroy::class);
                Route::post(  'models/{model}/{id}/notify', ModelActions\Notify::class);

                // Action Routes
                Route::post(  'actions/{action}',           ActionActions\Handle::class);
                Route::get(   'actions/{action}',           ActionActions\Handle::class);
                Route::post(  'batch',                      ActionActions\Batch::class);
                Route::get(   'batch',                      ActionActions\Batch::class);
                Route::post(  'run-actions',                ActionActions\RunActions::class);
                Route::get(   'run-actions',                ActionActions\RunActions::class);
            });
        });
    }
}