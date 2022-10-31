<?php

namespace Blazervel\Blazervel\Console;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;

class MakeAnonymousActionCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    protected $name = 'make:blazervel:anonymous';

    protected $description = 'Create a new Blazervel Action (anonymous class)';

    protected $type = 'Blazervel Action (anonymous class)';

    protected function getStub()
    {
        return $this->resolveStubPath(
            "/stubs/action.anonymous.stub"
        );
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Actions';
    }
}
