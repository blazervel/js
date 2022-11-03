<?php

namespace Blazervel\Blazervel\Actions\Models;

use Blazervel\Blazervel\Support\Actions;

class Notify extends ModelAction
{
    public function handle()
    {
        $this->request->validate([
            'notification' => 'required|string',
            'payload' => 'required|json'
        ]);

        $notifyClass = Actions::keyClass($this->request->notification);
        $params = json_decode($this->request->payload, true); //Actions::jsonParams();
        $params = collect($params)->map(fn ($value, $key) => [
            $key => $value
        ])->all();

        $this->model->notify(new $notifyClass(...$params));
    }
}