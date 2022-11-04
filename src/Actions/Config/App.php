<?php

namespace Blazervel\Blazervel\Actions\Config;

use Blazervel\Blazervel\Action;

class App extends Action
{
    public function handle()
    {
        return \B::arr(
            localization: Localization::run(),
            routes: Routes::run(),
        );
    }
}