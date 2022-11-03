<?php

namespace Blazervel\Blazervel;

use ReflectionMethod;
use Blazervel\Blazervel\Actions\Handle;
use Illuminate\Support\Facades\Route;
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Concerns\AsObject;
use Lorisleiva\Actions\Concerns\AsJob;
use Lorisleiva\Actions\Concerns\AsCommand;
use Lorisleiva\Actions\Concerns\AsListener;
use Lorisleiva\Actions\Concerns\AsFake;

class Action
{
    use AsController;
    use AsObject;
    // use AsJob;
    // use AsCommand;
    // use AsListener;
    // use AsFake;

    public function render(array $data)
    {
        return view($this->getRootView(), $data);
    }

    // public function __invoke(...$params)
    // {
    //     $request = request();

    //     $params = $this->getParams(
    //         action: get_called_class(),
    //         request: $request,
    //         params: $params
    //     );

    //     if ($request->expectsJson()) {
    //         return $this->handle(...$params);
    //     }

    //     return view(
    //         $this->getRootView(),
    //         $this->handle(...$params)
    //     );
    // }

    // public static function run(...$params)
    // {
    //     $action = get_called_class();

    //     return (new $action)->handle(...$params);
    // }

    // public static function make(...$params)
    // {
    //     return static::run(...$params);
    // }

    // protected function getParams($action, $request, $params): array
    // {
    //     $reflect = new ReflectionMethod($action, 'handle');

    //     $paramTypes = (
    //         collect($reflect->getParameters())
    //             // ->filter(fn ($p) => !$p->isOptional())
    //             ->map(fn ($p) => [$p->getName() => $p->getType()->getName()])
    //             ->collapse()
    //     );

    //     $route = Route::getCurrentRoute();

    //     // Merge any available route params
    //     $params = array_merge(
    //         $params,
    //         $request->all(),
    //         $route->parameters ?? []
    //     );

    //     dd($params);

    //     $params = collect($params ?? [])
    //                 ->filter(fn ($value, $key) => in_array($key, $paramTypes->keys()->all()))
    //                 ->all();

    //     // Add request if action requires it
    //     if ($paramTypes->has('request')) {
    //         $params['request'] = $request;
    //     }

    //     return $params;
    // }

    protected function getRootView()
    {
        return 'blazervel::app';
    }

    protected function validate(array $rules = null): array
    {
        $request = request();

        if ($this->rules ?? $rules ?: false) {
            $request->validate(
                $this->rules
            );
        }

        return $request->all();
    }
}
