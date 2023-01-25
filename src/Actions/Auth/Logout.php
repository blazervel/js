<?php

namespace Blazervel\BlazervelJS\Actions\Auth;

use Blazervel\BlazervelJS\Action;
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