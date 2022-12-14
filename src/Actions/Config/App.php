<?php

namespace Blazervel\Blazervel\Actions\Config;

use Blazervel\Blazervel\Action;

class App extends Action
{
    public function handle()
    {
        return \B::arr(
            translations: Translations::run(),
            routes: Routes::run(),
            models: Models::run()
        );
    }
}