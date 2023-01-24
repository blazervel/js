<?php

namespace Blazervel\BlazervelQL\Actions\Auth;

use Blazervel\BlazervelQL\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Logout
{
    public function __invoke(Request $request)
    {
        Auth::logout();

        return [];
    }
}