<?php

namespace Blazervel\Blazervel\Actions\Models;

class Store extends ModelAction
{
    public function handle()
    {
        return $this->model->create(
            $this->request->all()
        );
    }
}