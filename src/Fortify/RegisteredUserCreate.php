<?php

namespace Blazervel\Blazervel\Fortify;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class RegisteredUserCreate
{
    public function __invoke(): InertiaResponse
    {
        return Inertia::render('@blazervel-ui/inertia/react/Pages/Register');
    }
}
