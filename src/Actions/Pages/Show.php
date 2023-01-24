<?php

namespace Blazervel\BlazervelQL\Actions\Pages;

use Blazervel\BlazervelQL\Action;
use Blazervel\BlazervelQL\Actions\ResolveAction;
use Blazervel\BlazervelQL\Support\Actions;
use Blazervel\BlazervelQL\Actions\Config;
use Blazervel\BlazervelQL\WithBlazervel;
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