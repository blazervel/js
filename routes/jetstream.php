<?php

use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Jetstream;

use Blazervel\Blazervel\Actions\Terms;
use Blazervel\Blazervel\Actions\Policy;
use Blazervel\Blazervel\Actions\ApiTokens;
use Blazervel\Blazervel\Actions\Teams;
use Blazervel\Blazervel\Actions\Users;
use Blazervel\Blazervel\Exceptions\OopsyDoopsies;

// Include Jetstream routes
try {

    require_once(
        base_path('vendor/laravel/jetstream/routes/inertia.php')
    );

} catch (Exception $e) {

    if (Str::contains($e->getMessage(), 'Failed to open stream: No such file or directory')) {

        throw new OopsyDoopsies(
            "Blazervel can't find Laravel Jetstream's routes...have you run `composer require laravel\jetstream` yet?"
        );

    } else {
        
        throw $e;

    }

}

$middleware = config('jetstream.middleware', [
    'web'
]);

$authMiddleware = array_values(array_filter([
    config('jetstream.guard')
        ? 'auth:'.config('jetstream.guard')
        : 'auth',

    config('jetstream.auth_session', false)
        ? config('jetstream.auth_session')
        : null
]));

// Override get routes with Blazervel views

Route::group(['middleware' => $middleware], function () {

    if (Jetstream::hasTermsAndPrivacyPolicyFeature()) {
        Route::get('terms-of-service', Terms\Show::class )->name('terms.show');
        Route::get('privacy-policy',   Policy\Show::class)->name('policy.show');
    }

});

Route::group(['middleware' => array_merge($middleware, $authMiddleware)], function () {
    
    Route::get('user/profile', Users\Show::class)->name('profile.show');

    Route::group(['middleware' => 'verified'], function () {

        if (Jetstream::hasApiFeatures()) {
            Route::get('user/api-tokens', ApiTokens\Index::class)->name('api-tokens.index');
        }

        // Teams...
        if (Jetstream::hasTeamFeatures()) {
            Route::get('teams/create', Teams\Create::class)->name('teams.create');
            Route::get('teams/{team}', Teams\Show::class  )->name('teams.show');

            Route::middleware(['signed'])->group(fn () => (
                Route::get('team-invitations/{invitation}', Teams\Invitations\Show::class)->name('team-invitations.accept')
            ));
        }
    });

});