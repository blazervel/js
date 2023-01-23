<?php

namespace Blazervel\BlazervelQL\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Check
{
    public function __invoke(Request $request)
    {
        if (!$user = Auth::user()) {
          return ['user' => null];
        }

        $keyName = $user->getKeyName();

        return ['user' => $user->$keyName];
    }
}