<?php

namespace Blazervel\Blazervel;

use ReflectionMethod;
use Blazervel\Blazervel\WithInertia;
use Blazervel\Blazervel\Actions\Handle;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Concerns\AsObject;
use Illuminate\Support\Facades\Route;
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

    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'blazervel-ui::app';

    public function render()
    {
        return view(
            $this->getRootView()
        );
    }

    // public function asController(...$parameters)
    // {
    //     if (in_array(WithInertia::class, class_uses($this))) {
    //         return $this->handle(...$parameters);
    //     }
    //     return $this->render($request);
    // }

    protected function getRootView()
    {
        return $this->rootView;
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
