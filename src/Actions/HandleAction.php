<?php

namespace Blazervel\Blazervel\Actions;

use Blazervel\Blazervel\Action;
use Blazervel\Blazervel\Support\Actions;
use Illuminate\Http\Request;
use ReflectionMethod;

class HandleAction
{
    public function __invoke(Request $request, string $action)
    {
        if ($request->namespace) {
            $action = "{$request->namespace}-actions-{$action}";
        }

        $actionClass = Actions::keyAction($action);
        $invokeMethod = '__invoke';

        if (is_subclass_of($actionClass, Action::class)) {
            $invokeMethod = 'handle';

            if (method_exists($actionClass, 'asController')) {
                $invokeMethod = 'asController';
            }
        }

        $reflect = new ReflectionMethod($actionClass, $invokeMethod);
        $paramTypes = collect($reflect->getParameters())
                    ->filter(fn ($p) => !$p->isOptional())
                    ->map(fn ($p) => [$p->getName() => $p->getType()->getName()])
                    ->collapse();

        $params = $request->except(['action', 'namespace']);

        if ($paramTypes->has('request')) {
            $params['request'] = $request;
        }

        return (new $actionClass)->$invokeMethod(...$params);

        return abort(404);
    }
}