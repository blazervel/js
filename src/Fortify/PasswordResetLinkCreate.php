<?php

namespace Blazervel\Blazervel\Fortify;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PasswordResetLinkCreate
{
    /**
     * Display the password reset link request view.
     *
     * @return \Inertia\Response
     */
    public function __invoke(): InertiaResponse
    {
        return Inertia::render('@blazervel-ui/Pages/Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }
}
