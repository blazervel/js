<?php

namespace Blazervel\Blazervel\Actions\Teams;

use Illuminate\Http\Request;
use Inertia\Inertia;

class Create
{
    public function __invoke(Request $request)
    {
        return Inertia::render('@blazervel-ui/react/jetstream/Pages/Teams/Create', [
            'auth' => fn () => [
                'user' => [
                    'teams' => $request->user()->teams()->get()
                ]
            ]
        ]);
    }
}