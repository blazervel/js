<?php

namespace Blazervel\Blazervel\Actions\Models;

class UpdateAll extends ModelAction
{
    public function handle()
    {
        return [
            'updated' => $this->model->newQuery()->actionsJS()->update($this->request->all())
        ];
    }
}