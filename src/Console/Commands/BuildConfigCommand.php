<?php

namespace Blazervel\BlazervelQL\Console\Commands;

use Blazervel\BlazervelQL\Actions\Config\Actions;
use Blazervel\BlazervelQL\Actions\Config\Controllers;
use Blazervel\BlazervelQL\Actions\Config\Notifications;
use Blazervel\BlazervelQL\Actions\Config\Jobs;
use Blazervel\BlazervelQL\Actions\Config\BlazervelControllers;
use Blazervel\BlazervelQL\Actions\Config\Translations;
use Blazervel\BlazervelQL\Actions\Config\Routes;
use Blazervel\BlazervelQL\Actions\Config\Models;
use Blazervel\BlazervelQL\Providers\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;

class BuildConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blazervelql:build-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate json config/schema files for Blazervel';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function __invoke()
    {
        $directory = ServiceProvider::path('dist/config');

        File::ensureDirectoryExists($directory);

        collect([
            Translations::class,
            Routes::class,
            Models::class,
            BlazervelControllers::class,
            // Notifications::class,
            // Actions::class,
            // Jobs::class,
        ])->each(fn ($helper) => (
            file_put_contents(
                "{$directory}/{$helper::outputFileName()}",
                $helper::outputFileContents()
            )
        ));

        $this->comment('Blazervel json config files built for translations, routes, models, notifications, actions, and jobs');

        return 0;
    }
}
