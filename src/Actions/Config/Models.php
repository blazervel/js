<?php

namespace Blazervel\BlazervelJS\Actions\Config;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Models extends Config
{
    public function generate(): array
    {
        $path = app_path('Models');

        if (!File::exists($path)) {
            return [];
        }

        return [
            'shared' => [
                'methods' => $this->getModelMethods(Model::class, true),
            ],
            'models' => (
                collect(File::allFiles($path))
                    ->map(fn ($file) => join('\\', ['App', 'Models', Str::remove('.php', $file->getFilename())]))
                    ->filter(fn ($class) => is_subclass_of($class, Model::class))
                    ->filter(fn ($class) => (new ReflectionClass($class))->isAbstract() === false)
                    ->map(fn ($class) => [$class => [
                        'key' => Str::remove('App.', Str::replace('\\', '.', $class)),
                        'attributes' => $this->getModelAttributes($class),
                        'methods' => ($methods = $this->getModelMethods($class)),
                        'scopes' => collect($methods)->filter(fn ($params, $method) => Str::endswith($method, 'Scope'))->all()
                    ]])
                    ->collapse()
                    ->all()
            )
        ];
    }

    private function getModelAttributes(string $class): array
    {
        $model = new $class;
        
        try {
            $schema = DB::select("DESCRIBE {$model->getTable()}");
        } catch (Exception $e) {
            $schema = [];
        }

        return (
            collect($schema)
                ->map(fn ($field) => [
                    $field->Field => join('', [
                        Str::camel($field->Type),
                        $field->Null === 'NO' && is_null($field->Default)
                            ? ''
                            : '?'
                    ])
                ])
                ->collapse()
                ->all()
        );
    }

    private function getModelMethods(string $class, bool $includeInherited = false): array
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