<?php

namespace Blazervel\Blazervel\Actions\Teams;

use Inertia\Inertia;
use App\Models\Team;

class Show
{
    public function __invoke(Team $team)
    {
        return Inertia::render('@blazervel-ui/react/jetstream/Pages/Teams/Show', [
            'team' => $team
        ]);
    }
}