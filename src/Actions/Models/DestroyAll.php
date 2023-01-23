<?php

namespace Blazervel\BlazervelQL\Actions\Models;

class DestroyAll extends ModelAction
{
    public function handle()
    {
        return [
            'deleted' => $this->model->newQuery()->actionsJS()->delete()
        ];
    }
}