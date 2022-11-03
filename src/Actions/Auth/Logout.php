<?php

namespace Blazervel\Blazervel\Actions\Auth;

use Blazervel\Blazervel\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Logout
{
    public function handle(Request $request)
    {
        Auth::logout();

        return [];
    }
}