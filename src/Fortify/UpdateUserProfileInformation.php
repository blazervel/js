<?php

namespace Blazervel\Blazervel\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'job_title'          => ['required', 'string', 'max:255'],
            'phone'              => ['required', 'string', 'max:255'],
            'timezone'           => ['required', 'string', Rule::in(config('app.timezones'))],
            'photo'              => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'profile_photo_path' => ['nullable', 'string'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {

            // If photo input is present/valid then upload it and
            // assign it to the user
            $user->updateProfilePhoto($input['photo']);

        } elseif (
            $user->profile_photo_path &&
            ($input['profile_photo_path'] ?? null) === null
        ) {

            // If profile_photo_path input is missing/null and 
            // user has existing profile photo then delete it
            $user->deleteProfilePhoto();

        }

        $data = collect($input)->only([
            'name',
            'email',
            'job_title',
            'phone',
            'timezone',
        ])->all();

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        if (isset($data['email_verified_at'])) {
            $user->sendEmailVerificationNotification();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
