<?php

namespace Blazervel\Blazervel\Actions\Models;

class Destroy extends ModelAction
{
    public function handle()
    {
        return ['success' => $this->model->delete()];
    }
}