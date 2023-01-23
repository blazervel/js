<?php

namespace Blazervel\BlazervelQL\Actions\Config;

use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Notifications\Notification;

class Notifications extends Config
{
    public function generate(): array
    {
        $path = app_path('Notifications');

        if (!File::exists($path)) {
            return [];
        }

        return [
            'notifications' => (
                collect(File::allFiles($path))
                    ->map(fn ($file) => (
                        Str::replace('/', '\\', Str::replace(app_path(), 'App', Str::remove('.php', $file->getPathname())))
                    ))
                    ->filter(fn ($class) => is_subclass_of($class, Notification::class))
                    ->filter(fn ($class) => (new ReflectionClass($class))->isAbstract() === false)
                    ->map(fn ($class) => [$class => [
                        'key' => Str::remove('App.', Str::replace('\\', '.', $class)),
                        'arguments' => $this->getNotificationArguments($class)
                    ]])
                    ->collapse()
            )
        ];
    }

    private function getNotificationArguments(string $class): array
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
}