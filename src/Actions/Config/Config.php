<?php

namespace Blazervel\Blazervel\Actions\Config;

use Illuminate\Support\Str;
use JsonSerializable;

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
}