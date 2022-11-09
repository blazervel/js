<?php

namespace Blazervel\Blazervel\Fortify;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ConfirmablePasswordPrompt
{
    /**
     * Show the confirm password view.
     *
     * @return \Inertia\Response
     */
    public function __invoke(): InertiaResponse
    {
        return Inertia::render('@blazervel-ui/react/jetstream/Pages/ConfirmPassword');
    }
}
