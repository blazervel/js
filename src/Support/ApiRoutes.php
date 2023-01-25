<?php

namespace Blazervel\BlazervelJS\Support;

use Blazervel\BlazervelJS\Actions;
use Illuminate\Support\Facades\Route;

class ApiRoutes
{
    private string $endpointPrefix = '/api/blazervel';

    public static function register(): void
    {
        $endpoint = (new self)->endpointPrefix;
        
        Route::middleware('api')->group(fn () => (
            Route::prefix($endpoint)->group(function() {
                // Model Action Routes
                //Route::get(   'models/{model}',             ModelActions\Index::class);
                //Route::post(  'models/{model}',             ModelActions\Store::class);
                //Route::get(   'models/{model}/{id}',        ModelActions\Show::class);
                //Route::put(   'models/{model}/{id}',        ModelActions\Update::class);
                //Route::delete('models/{model}/{id}',        ModelActions\Destroy::class);
                //Route::post(  'models/{model}/{id}/notify', ModelActions\Notify::class);

                // Action Routes
                Route::any('models/{model}/{id?}', Actions\HandleModel::class);
                Route::any('actions/{action}',     Actions\HandleAction::class);
                Route::any('batch-requests',       Actions\HandleBatchRequests::class);
                Route::any('batch-actions',        Actions\HandleBatchActions::class);
            })
        ));
    }
}