<?php

namespace Blazervel\Blazervel;

use Lorisleiva\Actions\Concerns\AsController;
use Lorisleiva\Actions\Concerns\AsObject;
use ReflectionMethod;
use Blazervel\Blazervel\Actions\Handle;
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

    public function render()
    {
        return view($this->getRootView());
    }

    public function asController()
    {
        return $this->render();
    }

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
