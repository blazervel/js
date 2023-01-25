<?php

namespace Blazervel\BlazervelJS\Actions\Config;

use Illuminate\Support\Str;
use JsonSerializable;
use ReflectionMethod;
use ReflectionUnionType;

abstract class Config implements JsonSerializable
{
    abstract function generate(): array;

    static function run(): array
    {
        $object = get_called_class();

        return (new $object)->generate();
    }

    static function outputFileName(): string
    {
        return Str::snake(class_basename(get_called_class()), '-') . '.json';
    }

    static function outputFileContents(): string
    {
        return json_encode(
            static::run()
        );
    }

    public function jsonSerialize(): mixed
    {
        return static::run();
    }

    protected function getMethodReturnType(ReflectionMethod $method)
    {
        if (!$type = $method->getReturnType()) {
            return null;
        }

        if ($type::class === ReflectionUnionType::class) {
            return collect($type->getTypes())->map(fn ($t) => $t->getName())->join('|');
        }

        return $type->getName() . ($type->allowsNull() ? '|null' : '');
    }
}