<?php

use Illuminate\Support\Facades\Route;

use Blazervel\Blazervel\Fortify;
use Laravel\Fortify\Features as FortifyFeatures;

use Illuminate\Support\Facades\Auth;

Route::get('logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->middleware(['web', 'auth'])->name('auth.logout');

Route::group(['middleware' => config('fortify.middleware', ['web'])], function () {

    Route::middleware(['guest:' . config('fortify.guard')])->group(function () {

        Route::get('login', Fortify\AuthenticatedSessionCreate::class)->name('login');

        if (FortifyFeatures::enabled(FortifyFeatures::resetPasswords())) {
            Route::get('forgot-password', Fortify\PasswordResetLinkCreate::class)->name('password.request');

            Route::get('reset-password/{token}', Fortify\NewPasswordCreate::class)->name('password.reset');
        }

        if (FortifyFeatures::enabled(FortifyFeatures::registration())) {
            Route::get('register', Fortify\RegisteredUserCreate::class)->name('register');
        }

        if (FortifyFeatures::enabled(FortifyFeatures::twoFactorAuthentication())) {
            Route::get('two-factor-challenge', Fortify\TwoFactorAuthenticatedSessionCreate::class)->name('two-factor.login');
        }

    });

    Route::middleware([config('fortify.auth_middleware', 'auth') . ':' . config('fortify.guard')])->group(function () {
        if (FortifyFeatures::enabled(FortifyFeatures::emailVerification())) {
            Route::get('email/verify', Fortify\EmailVerificationPrompt::class)->name('verification.notice');
        }

        Route::get('user/confirm-password', Fortify\ConfirmablePasswordPrompt::class)->name('password.confirm');
    });
});
