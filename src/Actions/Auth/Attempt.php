<?php

namespace Blazervel\Blazervel\Actions\Auth;

use Blazervel\Blazervel\Action;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Attempt extends Action
{
    public function handle(Request $request)
    {
        $request->validate([
          'email' => 'required|string|email:dns,rfc,spoof,filter|max:255|exists:users,email',
          'password' => ['required', Rules\Password::defaults()],
        ]);

        if (Auth::check()) {
          Auth::logout();
        }

        Auth::attempt(
          $request->only([
            'email',
            'password'
          ])
        );

        $keyName = $user->getKeyName();

        return ['user' => $user->$keyName];
    }
}