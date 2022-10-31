<?php

namespace Blazervel\Blazervel\Actions\Models;

class DestroyAll extends ModelAction
{
    public function handle()
    {
        return [
            'deleted' => $this->model->newQuery()->actionsJS()->delete()
        ];
    }
}