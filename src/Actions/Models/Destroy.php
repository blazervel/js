<?php

namespace Blazervel\BlazervelQL\Actions\Models;

class Destroy extends ModelAction
{
    public function handle()
    {
        return ['success' => $this->model->delete()];
    }
}