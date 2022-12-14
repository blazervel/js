<?php

namespace Blazervel\Blazervel\Console;

use Blazervel\Blazervel\Actions\Config\Translations;
use Blazervel\Blazervel\Actions\Config\Routes;
use Blazervel\Blazervel\Actions\Config\Models;
use Blazervel\Blazervel\Providers\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;

class MakeConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blazervel:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate json config files for Blazervel';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $directory = ServiceProvider::path('dist/config');

        File::ensureDirectoryExists($directory);

        collect([
            Translations::class,
            Routes::class,
            Models::class,
        ])->each(fn ($helper) => (
            file_put_contents(
                "{$directory}/{$helper::outputFileName()}",
                $helper::outputFileContents()
            )
        ));

        return 0;
    }
}
