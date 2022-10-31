<?php

namespace Blazervel\Blazervel\Actions\Models;

class Index extends ModelAction
{
    public function handle()
    {
        if ($this->only->count()) {
            return $this->model->actionsJS()->select($this->only->all())->get();
        }

        return $this->model->actionsJS()->get();
    }
      
}