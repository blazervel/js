<?php

namespace Blazervel\BlazervelQL\Actions\Config;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class Actions extends Config
{
    public function generate(): array
    {
        $controllerActions = (new Controllers)->generate();

        $standaloneActions = (
            collect($this->classes())
                ->keys()
                ->filter(fn ($class) => (new ReflectionClass($class))->isAbstract() === false)
                ->map(fn ($class) => [$class => [
                    'key' => Str::remove('App.', Str::replace('\\', '.', $class)),
                    'methods' => $this->getActionInvoker($class)
                ]])
                ->collapse()
                ->all()
        );

        return ['actions' => array_merge(
            $controllerActions['controllers'],
            $standaloneActions
        )];
    }

    private function classes(): array
    {
        $actionsDir       = 'app/Actions';
        $actionsNamespace = 'App\\Actions';
        $classNames       = [];
        $files            = (new Filesystem)->allFiles(base_path($actionsDir));

        foreach ($files as $file) {
            $path      = $file->getPathName();
            $className = explode("{$actionsDir}/", $path)[1];
            $className = Str::remove('.php', $className);
            $className = Str::replace('/', '\\', $className);
            $className = "{$actionsNamespace}\\{$className}";

            $classNames[$className] = $path;
        }

        return $classNames;
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