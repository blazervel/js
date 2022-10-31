<?php

namespace Blazervel\Blazervel\Casts;

use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Str;

class EnumArray implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): string
    {
        $map_property = Str::plural($key);
        $map = $model->$map_property;

        return $map[$value];
    }

    public function set($model, $key, $value, $attributes)
    {
        $model_name = get_class($model);
        $map_property = Str::plural($key);
        $map = $model->$map_property;

        if (is_int($value) && isset($map[$value])) {
            return $value;
        } elseif (is_string($value) && in_array($value, $map)) {
            return array_search($value, $map);
        }

        throw new Exception("Invalid enum value for {$key} field on {$model_name} model.");
    }
}
