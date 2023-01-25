<?php

namespace Blazervel\BlazervelJS\Actions\Config;

use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Jobs extends Config
{
    public function generate(): array
    {
        $path = app_path('Jobs');

        if (!File::exists($path)) {
            return [];
        }

        return [
            'notifications' => (
                collect(File::allFiles($path))
                    ->map(fn ($file) => (
                        Str::replace('/', '\\', Str::replace(app_path(), 'App', Str::remove('.php', $file->getPathname())))
                    ))
                    ->filter(fn ($class) => (new ReflectionClass($class))->isAbstract() === false)
                    ->map(fn ($class) => [$class => [
                        'key' => Str::remove('App.', Str::replace('\\', '.', $class)),
                        'methods' => [
                            '__construct' => $this->getJobConstructor($class),
                            'handle' => $this->getJobHandle($class),
                        ]
                    ]])
                    ->collapse()
            )
        ];
    }

    private function getJobConstructor(string $class): array
    {
        $constructor = (new ReflectionClass($class))->getConstructor();

        return (
            collect($constructor->getParameters())
                ->map(fn ($p) => [
                    $p->getName() => [
                        'type' => $p->getType(),
                        'required' => !$p->isOptional()
                    ]
                ])
                ->collapse()
                ->all()
        );
    }

    private function getJobHandle(string $class): array
    {
        $methods = (new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC);
        $invoker = collect($methods)->filter(fn ($m) => in_array($m->getName(), ['handle']))->first();

        return [
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
        ];
    }
}