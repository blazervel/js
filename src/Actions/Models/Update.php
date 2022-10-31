<?php

namespace Blazervel\Blazervel\Actions\Models;

class Update extends ModelAction
{
    public function handle()
    {
        $model = $this->model;

        $model->update(
            $this->request->all()
        );

        return $model;
    }
}