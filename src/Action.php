<?php

namespace Blazervel\Blazervel;

use Lorisleiva\Actions\Concerns\AsAction;

class Action
{
    use AsAction;

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
