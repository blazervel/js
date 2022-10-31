<?php

namespace Blazervel\Blazervel\Support;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class Actions
{
    public static function dir(): string
    {
        return Config::get('blazervel.actions.actions_dir', 'app/Actions');
    }

    public static function namespace(): string
    {
        return (new Collection(explode('/', static::dir())))
                    ->map(fn ($slug) => Str::ucfirst(Str::camel($slug)))
                    ->join('\\');
    }

    public static function keyClass(string $key)
    {
        // Support blazervel package actions
        if (Str::startsWith($actionKey, 'blazervel-')) {
            $actionsNamespace = '';
        } else {
            $actionsNamespace = static::dir();
            $actionsNamespace = explode('/', $actionsNamespace);
            $actionsNamespace = collect($actionsNamespace)->map(fn ($an) => Str::ucfirst(Str::camel($an)))->join('\\');
            $actionsNamespace = "\\{$actionsNamespace}";
        }

        $actionClass = explode('-', $actionKey);
        $actionClass = collect($actionClass)->map(fn ($ac) => Str::ucfirst(Str::camel($ac)))->join('\\');
        $actionClass = "{$actionsNamespace}\\{$actionClass}";

        $actionClass = Str::replace('Blazervel\\Actionsjs', 'Blazervel\\ActionsJS', $actionClass);

        return $actionClass;
    }

    public static function keyAction(string $actionKey)
    {
        // Support blazervel package actions
        if (Str::startsWith($actionKey, 'blazervel-')) {
            $actionsNamespace = '';
        } else {
            $actionsNamespace = static::dir();
            $actionsNamespace = explode('/', $actionsNamespace);
            $actionsNamespace = collect($actionsNamespace)->map(fn ($an) => Str::ucfirst(Str::camel($an)))->join('\\');
            $actionsNamespace = "\\{$actionsNamespace}";
        }

        $actionClass = explode('-', $actionKey);
        $actionClass = collect($actionClass)->map(fn ($ac) => Str::ucfirst(Str::camel($ac)))->join('\\');
        $actionClass = "{$actionsNamespace}\\{$actionClass}";

        $actionClass = Str::replace('Blazervel\\Actionsjs', 'Blazervel\\ActionsJS', $actionClass);

        return $actionClass;
    }

    public static function directories(): array
    {
        $directory = static::dir();
        $directories = Finder::create()->in($directory)->directories()->sortByName();

        return (new Collection($directories))
                    ->map(fn ($dir) => Str::remove(base_path() . '/', $dir->getPathname()))
                    ->all();
    }

    public static function classes(): Collection
    {
        $actionsDir       = static::dir();
        $actionsNamespace = static::namespace();
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

        return collect($classNames);
    }

    public static function anonymousClasses(): Collection
    {
        $actions = [];

        foreach (static::classes() as $className => $path) {
            if (gettype(
                $class = require($path)
            ) !== 'object') {
                continue;
            }

            $class = get_class($class);

            if (! Str::contains($class, '@anonymous')) {
                continue;
            }

            $actions[$className] = $class;
        }

        return new Collection($actions);
    }
}
