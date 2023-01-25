<?php

namespace Blazervel\BlazervelJS\Actions\Pages;

use Blazervel\BlazervelJS\Action;
use Blazervel\BlazervelJS\Actions\ResolveAction;
use Blazervel\BlazervelJS\Support\Actions;
use Blazervel\BlazervelJS\Actions\Config;
use Blazervel\BlazervelJS\WithBlazervel;
use Illuminate\Http\Request;

class Show extends Action
{
    use WithBlazervel;

    public function __invoke(Request $request, string $url)
    {
        $action    = Actions::urlAction($url);
        $params    = Actions::urlParams($url);
        $component = Actions::actionComponent($action);
        $response  = ResolveAction::run(new Request($params), $action);

        $data = [
            'action' => $action,
            'props' => $response,
            'config' => Config\App::run(),
            'componentName' => $component
        ];

        return $data;
    }
}