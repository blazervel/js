<?php

namespace Blazervel\Blazervel\Actions\Users;

use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class Show
{
    public function __invoke(Request $request): Response
    {
        $sessions = [];

        $user = $request->user();
        
        $profilePhotoUrl = $user->profile_photo_url;

        if (config('session.driver') === 'database') {

            $connection = DB::connection(config('session.connection'));

            $sessions = (
                $connection
                    ->table(config('session.table', 'sessions'))
                    ->where('user_id', Auth::user()->getAuthIdentifier())
                    ->orderBy('last_activity', 'desc')
                    ->get()
                    ->map(fn ($session) => (object) [
                        'agent'             => $this->createAgent($session),
                        'ip_address'        => $session->ip_address,
                        'is_current_device' => $session->id === $request->session()->getId(),
                        'last_active'       => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    ])
            );
        }

        return Inertia::render('@blazervel-ui/react/jetstream/Pages/Profile/Show', compact('sessions', 'user', 'profilePhotoUrl'));
    }
    
    protected function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }
}