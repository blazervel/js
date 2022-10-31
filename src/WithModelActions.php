<?php

namespace Blazervel\Blazervel;

trait WithModelActions
{
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

    public function getActionsHidden(): array|null
    {
        return $this->actionsHidden ?? null;
    }

    public function getActionPolicy(): bool|callable
    {
        if (method_exists(get_called_class(), 'actionPolicy')) {

            return fn (string $action) => $this->actionPolicy($action);

        }
        
        if (($this->actionPolicy ?? null) === false) {

            return false;

        }

        return true;
    }

    public function getActionRules(): array
    {
        return (
            method_exists(get_called_class(), 'actionRules')
                ? $this->actionRules()
                : ($this->actionRules ?? [])
        );
    }
}
