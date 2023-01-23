<?php

namespace Blazervel\BlazervelQL\Actions\Models;

class UpdateAll extends ModelAction
{
    public function handle()
    {
        return [
            'updated' => $this->model->newQuery()->actionsJS()->update($this->request->all())
        ];
    }
}