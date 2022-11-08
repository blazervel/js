<?php

namespace Blazervel\Blazervel\Fortify;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Blazervel\Workspaces\Models\WorkspaceUserInviteModel;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input): User
    {
        if (!WorkspaceUserInviteModel::intendedUrlIsWorkspaceInviteAccept()) {
            return redirect()->route('login');
        }


        Validator::make($input, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            // 'terms'    => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // if (!$this->ensureAcceptingInvitation()) {
        //     $user->ownedTeams()->save(Team::forceCreate([
        //         'user_id' => $user->id,
        //         'name' => explode(' ', $user->name, 2)[0]."'s Company",
        //         'personal_team' => true,
        //     ]));
        // }
        

        return $user;
    }

    /**
     * Check to see if user it registering in order to access a team invitation
     *
     * @return bool
     */
    private function ensureAcceptingInvitation(): bool
    {
        return (
            Str::contains(
                Session::get('url.intended'),
                '\/team-invitations\/'
            )
        );
    }
}
