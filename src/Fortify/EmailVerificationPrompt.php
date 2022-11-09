<?php

namespace Blazervel\Blazervel\Fortify;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class EmailVerificationPrompt
{
    /**
     * Display the email verification prompt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function __invoke(Request $request): InertiaResponse
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(RouteServiceProvider::HOME)
                    : Inertia::render('@blazervel-ui/react/jetstream/Pages/VerifyEmail', ['status' => session('status')]);
    }
}
