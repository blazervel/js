<?php

namespace Blazervel\Blazervel\Actions\Config;

use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Models extends Config
{
    public function generate(): array
    {
        return [
            'shared' => [
                'methods' => $this->getClassMethods(Model::class, true),
            ],
            'models' => (
                collect(get_declared_classes())
                    ->filter(fn ($class) => is_subclass_of($class, Model::class))
                    ->map(fn ($class) => [$class => [
                        'methods' => $this->getClassMethods($class)
                    ]])
                    ->collapse()
            )
        ];
    }

    private function getClassMethods(string $class, bool $includeInherited = false): array
    {
        $methods = (new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods = collect($methods);

        if (!$includeInherited) {
            $methods = $methods->where('class', $class);
        }

        return (
            $methods
                ->filter(fn ($m) => !Str::startsWith($m->getName(), '_'))
                ->map(fn ($m) => [
                    $m->getName() => (
                        collect($m->getParameters())
                            ->map(fn ($p) => [
                                $p->getName() => [
                                    'type' => $p->getType(),
                                    'required' => !$p->isOptional()
                                ]
                            ])
                            ->collapse()
                            ->all()
                    )
                ])
                ->collapse()
                ->all()
        );
    }
}