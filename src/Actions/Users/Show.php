<?php

namespace Blazervel\Blazervel\Actions\Users;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class Show
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('@blazervel-ui/inertia/react/Pages/Users/Show', [
            'user' => $request->user()
        ]);
    }
}