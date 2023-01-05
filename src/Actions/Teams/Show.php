<?php

namespace Blazervel\Blazervel\Actions\Teams;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Team;
use Inertia\Inertia;

class Show
{
    public function __invoke(Request $request, Team $team)
    {
        return Inertia::render('@blazervel-ui/react/jetstream/Pages/Teams/Show', [
            'team' => fn () => static::setupTeam($team),
            'availableRoles' => fn () => [
                ['key' => 'admin',  'name' => 'Admin',  'description' => 'Admins users can perform any action.'],
                ['key' => 'member', 'name' => 'Member', 'description' => 'Members have the ability to read, create, and update.']
            ],
        ]);
    }

    public static function setupTeam(Team $team)
    {
        $teamOwner = $team->owner()->first();

        $teamOwner->profile_photo_url = $teamOwner->profile_photo_url;

        $team->owner = $teamOwner;

        $team->team_invitations = DB::table('team_invitations')->where('team_id', $team->id)->get();
        
        $team->users = $team->users()->get()->map(function ($user) {
            $user->profile_photo_url = $user->profile_photo_url;
            return $user;
        });

        return $team;
    }
}