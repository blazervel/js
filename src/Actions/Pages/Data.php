<?php

namespace Blazervel\Blazervel\Actions\Pages;

use Blazervel\Blazervel\Action;
use Blazervel\Blazervel\Actions\Handle;
use Blazervel\Blazervel\Support\Actions;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class Data extends Action
{
    public function handle(Request $request, string $url)
    {
        $action    = Actions::urlAction($url);
        $params    = Actions::urlParams($url);
        $component = Actions::actionComponent($action);
        $response  = Handle::run(new Request($params), $action);

        $data = [
            'action' => $action,
            'props' => $response,
            'componentName' => $component
        ];

        return $data;
    }
}