<?php

namespace Blazervel\Blazervel\Actions\Models;

class Show extends ModelAction
{
    public function handle()
    {
        if ($this->only->count()) {
            return $this->model->only(
                $this->only->all()
            );
        }

        return $this->model;
    }
}