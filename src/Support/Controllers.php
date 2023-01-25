<?php

namespace Blazervel\BlazervelJS\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Controllers
{
    public static function dir(): string
    {
        return 'app/Http/Blazervel';
    }

    public static function namespace(): string
    {
        return (new Collection(explode('/', static::dir())))
                    ->map(fn ($slug) => Str::ucfirst(Str::camel($slug)))
                    ->join('\\');
    }

    public static function configDefaults(string $controllerClass): array
    {
        $namespace = collect(explode('\\', Str::remove(static::namespace() . '\\', $controllerClass)));
        $name = $namespace->join('/');

        return [
            'name' => $name,
            'view' => "/{$name}", //"/{$name}.tsx",
            'route' => $namespace->map(fn ($s) => Str::snake($s, '-'))->join('/'),
        ];
    }
}