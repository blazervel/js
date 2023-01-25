<?php

namespace Blazervel\BlazervelJS\Actions;

use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionMethod;

class HandleBatchActions
{
    public function __invoke(Request $request)
    {
        // Example request:
        // http://blazervel.test/api/blazervel/batch-actions
        // ?actions=[{%22key%22:%2240661014d060c64829b0b721da6a8818%22,%22url%22:%22%E2%80%A6thod%22:%22get%22,%22params%22:{%22namespace%22:%22blazervel%22}}}]
        $request->validate([
            'actions' => 'required|json'
        ]);

        $responses = (
            $this
                ->getActions($request)
                ->map(fn (array $action) => $this->wrap($action))
                ->map(fn (object $action) => [$action->key => $action->run($request)])
                ->collapse()
                ->all()
        );

        return compact('responses');
    }

    protected function getActions(Request $request): Collection
    {
        $actions = json_decode($request->actions, true);

        return collect($actions);
    }

    protected function wrap(array $action): object
    {
        return new class (...$action) {
            
            public string $key;
            
            public string $name;

            public array $data;

            public function __construct(string $key, string $name, array $data) {
                $this->key = $key;
                $this->name = $name;
                $this->data = $data;
            }

            public function run(Request $request)
            {
                $name   = explode('.', $this->name);
                $params = $this->data['params'] ?? null;
                $method = array_pop($name);
                $class  = collect($name)->map(fn ($n) => Str::ucfirst(Str::camel($n)));
                $class  = $class->join('\\');

                try {
                
                    if ($params === null) {
                        $response = $class::$method();
                    } else {
                        $response = $class::$method(...$params);
                    }

                    if (is_object($response)) {
                        
                        $responseClass   = $response::class;
                        $responseName    = collect(explode('\\', $responseClass))->map(fn ($n) => Str::camel($n))->join('.');
                        $responseMethods = new ReflectionClass($responseClass);
                        $responseMethods = $responseMethods->getMethods(ReflectionMethod::IS_PUBLIC);

                        $responseMethods = (
                            collect($responseMethods)
                                ->filter(fn ($m) => !Str::startsWith($m->getName(), '_'))
                                ->map(fn ($m) => [
                                    $m->getName() => (
                                        collect($m->getParameters())
                                            ->map(fn ($p) => [
                                                $p->getName() => ['type' => $p->getType(), 'required' => !$p->isOptional()]
                                            ])
                                            ->collapse()
                                            ->all()
                                    )
                                ])
                                ->collapse()
                                ->all()
                        );
                        
                        $responseArray = method_exists($response, 'toArray') ? $response->toArray() : (array) $response;

                        $response = array_merge($responseArray, [
                            'actionName' => $responseName,
                            'actionMethods' => $responseMethods
                        ]);

                    } else {

                        $response = array_merge((array) $response, [
                            'actionName' => $this->name,
                            'actionMethods' => [$method]
                        ]);

                    }

                    return $response;

                } catch (Exception $e) {

                    return ['error' => $e->getMessage()];
                }
            }

        };
    }
}