<?php

namespace Blazervel\BlazervelQL\Actions\Auth;

use Blazervel\BlazervelQL\Action;
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