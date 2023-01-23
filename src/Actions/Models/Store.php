<?php

namespace Blazervel\BlazervelQL\Actions\Models;

class Store extends ModelAction
{
    public function handle()
    {
        return $this->model->create(
            $this->request->all()
        );
    }
}