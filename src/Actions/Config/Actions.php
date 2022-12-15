<?php

namespace Blazervel\Blazervel\Actions\Config;

use Blazervel\Blazervel\Support\Actions as SupportActions;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class Actions extends Config
{
    public function generate(): array
    {
        return [
            'actions' => (
                collect(SupportActions::classes())
                    ->keys()
                    ->filter(fn ($class) => (new ReflectionClass($class))->isAbstract() === false)
                    ->map(fn ($class) => [$class => [
                        'key' => Str::remove('App.Actions.Blazervel.', Str::replace('\\', '.', $class)),
                        'methods' => $this->getActionInvoker($class)
                    ]])
                    ->collapse()
                    ->all()
            )
        ];
    }

    private function getActionInvoker(string $class): array
    {
        $methods = (new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC);
        $invoker = collect($methods)->filter(fn ($m) => in_array($m->getName(), ['__invoke', 'handle']))->first();

        return [$invoker->getName() => [
            'returnType' => $this->getMethodReturnType($invoker),
            'parameters' => (
                collect($invoker->getParameters())
                    ->map(fn ($p) => [
                        $p->getName() => [
                            'type' => $p->getType(),
                            'required' => !$p->isOptional()
                        ]
                    ])
                    ->collapse()
                    ->all()
            )
        ]];
    }
}