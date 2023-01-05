<?php

namespace Blazervel\Blazervel\Providers;

use Illuminate\Contracts\Http\Kernel;

use Blazervel\Blazervel\Actions\Pages;
use Blazervel\Blazervel\Console\MakeActionCommand;
use Blazervel\Blazervel\Console\MakeAnonymousActionCommand;
use Blazervel\Blazervel\Console\Commands\BuildCommand;
use Blazervel\Blazervel\Support\Actions;
use Blazervel\Blazervel\Support\ActionRoutes;
use Blazervel\Blazervel\Http\Middleware\BlazervelMiddleware;

use Lorisleiva\Actions\Facades\Actions as LaravelActions;

use App\Providers\FortifyServiceProvider;
use Laravel\Fortify\Fortify;

use App\Providers\JetstreamServiceProvider;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\Features as JetstreamFeatures;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

use Illuminate\Routing\Router;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    private ?array $providers = null;

    public function register()
    {
        if (class_exists(Jetstream::class)) {
            Jetstream::$registersRoutes = false;
        }

        $this
            ->ensureDirectoryExists()
            ->registerAnonymousClassAliases()
            ->registerRouterMacro();
    }

    public function boot()
    {
        $this
            ->loadViews()
            ->loadRoutes()
            // ->loadMiddleware()
            ->loadTranslations()
            ->loadConfig()
            ->loadCommands();

        if (
            !$this->hasProvider(FortifyServiceProvider::class) &&
            class_exists(Fortify::class)
        ) {
            $this->loadFortify();
        }

        if (class_exists(Jetstream::class)) {

            $this->loadJetstreamRoutes();

            if (! $this->hasProvider(JetstreamServiceProvider::class)) {
                $this
                    ->loadJetstream()
                    ->loadJetstreamPermissions();
            }
        }
    }

    private function ensureDirectoryExists(): self
    {
        File::ensureDirectoryExists(
            Actions::dir()
        );

        return $this;
    }

    private function loadConfig(): self
    {
        $this->publishes([
            static::path('config/blazervel.php') => config_path('blazervel.php'),
        ], 'blazervel');

        return $this;
    }

    private function loadCommands(): self
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAnonymousActionCommand::class,
                MakeActionCommand::class,
                BuildCommand::class
            ]);
        }

        return $this;
    }

    private function loadViews(): self
    {
        $this->loadViewsFrom(
            static::path('resources/views'),
            'blazervel'
        );

        return $this;
    }

    private function registerAnonymousClassAliases(): self
    {
        $this->app->booting(function ($app) {
            $loader = AliasLoader::getInstance();

            collect([
                'Blazervel\\Action'           => \Blazervel\Blazervel\Action::class,
                'Blazervel\\WithModelActions' => \Blazervel\Blazervel\WithModelActions::class,
                'B'                           => \Blazervel\Blazervel\Support\Helpers::class,
            ])->map(fn ($class, $namespace) => (
                $loader->alias(
                    $namespace,
                    $class
                )
            ));

            if (Config::get('blazervel.actions.anonymous_classes', true)) {
                Actions::anonymousClasses()->map(fn ($class, $namespace) => (
                    $loader->alias(
                        $namespace,
                        $class
                    )
                ));
            }
        });

        return $this;
    }

    private function registerRouterMacro(): self
    {
        Router::macro('blazervel', fn ($uri, $component, $props = []) => (
            $this
                ->match(['POST', 'GET', 'HEAD'], $uri, Pages\Show::class)
                ->defaults('component', $component)
                ->defaults('props', $props)
        ));

        return $this;
    }

    private function loadRoutes(): self
    {
        // LaravelActions::registerRoutes(
        //     Actions::directories()
        // );

        ActionRoutes::register();

        return $this;
    }

    private function loadMiddleware(): self
    {
        $kernel = $this->app->make(Kernel::class);

        $kernel->appendMiddlewareToGroup('web', BlazervelMiddleware::class);
        $kernel->appendToMiddlewarePriority(BlazervelMiddleware::class);

        return $this;
    }

    private function loadTranslations(): self
    {
        $this->loadTranslationsFrom(
            static::path('lang'),
            'blazervel'
        );

        return $this;
    }

    private function loadFortify()
    {
        Config::set(['fortify.views' => false]);

        // Register inertia views and routes for fortify
        $this->loadRoutesFrom(
            static::path('routes/fortify.php')
        );

        Fortify::createUsersUsing(
            $this->fallback(
                \App\Actions\Fortify\CreateNewUser::class,
                \Blazervel\Blazervel\Fortify\CreateNewUser::class
            )
        );

        Fortify::updateUserProfileInformationUsing(
            $this->fallback(
                \App\Actions\Fortify\UpdateUserProfileInformation::class,
                \Blazervel\Blazervel\Fortify\UpdateUserProfileInformation::class
            )
        );

        Fortify::updateUserPasswordsUsing(
            $this->fallback(
                \App\Actions\Fortify\UpdateUserPassword::class,
                \Blazervel\Blazervel\Fortify\UpdateUserPassword::class
            )
        );

        Fortify::resetUserPasswordsUsing(
            $this->fallback(
                \App\Actions\Fortify\ResetUserPassword::class,
                \Blazervel\Blazervel\Fortify\ResetUserPassword::class
            )
        );
  
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
  
            return Limit::perMinute(5)->by($email.$request->ip());
        });
  
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        return $this;
    }

    private function loadJetstream(): self
    {
        // Set jetstream config defaults
        if (Config::get('jetstream.features', null) === null) {
            foreach([
                'middleware'         => ['web'],
                'auth_session'       => AuthenticateSession::class,
                'guard'              => 'sanctum',
                'profile_photo_disk' => 'public',
                'stack'              => 'inertia',
                'features'           => [
                    JetstreamFeatures::termsAndPrivacyPolicy(),
                    JetstreamFeatures::api(),
                    JetstreamFeatures::profilePhotos(),
                    JetstreamFeatures::teams(['invitations' => true]),
                    JetstreamFeatures::accountDeletion(),
                ],
            ] as $key => $value) {
                Config::set("jetstream.{$key}", $value);
            }
        }
        
        Jetstream::createTeamsUsing(
            $this->fallback(
                \App\Actions\Jetstream\CreateTeam::class,
                \Blazervel\Blazervel\Jetstream\CreateTeam::class
            )
        );

        Jetstream::updateTeamNamesUsing(
            $this->fallback(
                \App\Actions\Jetstream\UpdateTeamName::class,
                \Blazervel\Blazervel\Jetstream\UpdateTeamName::class
            )
        );

        Jetstream::addTeamMembersUsing(
            $this->fallback(
                \App\Actions\Jetstream\AddTeamMember::class,
                \Blazervel\Blazervel\Jetstream\AddTeamMember::class
            )
        );

        Jetstream::inviteTeamMembersUsing(
            $this->fallback(
                \App\Actions\Jetstream\InviteTeamMember::class,
                \Blazervel\Blazervel\Jetstream\InviteTeamMember::class
            )
        );

        Jetstream::removeTeamMembersUsing(
            $this->fallback(
                \App\Actions\Jetstream\RemoveTeamMember::class,
                \Blazervel\Blazervel\Jetstream\RemoveTeamMember::class
            )
        );

        Jetstream::deleteTeamsUsing(
            $this->fallback(
                \App\Actions\Jetstream\DeleteTeam::class,
                \Blazervel\Blazervel\Jetstream\DeleteTeam::class
            )
        );

        Jetstream::deleteUsersUsing(
            $this->fallback(
                \App\Actions\Jetstream\DeleteUser::class,
                \Blazervel\Blazervel\Jetstream\DeleteUser::class
            )
        );

        return $this;
    }

    protected function loadJetstreamPermissions(): self
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrator', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('editor', 'Editor', [
            'read',
            'create',
            'update',
        ])->description('Editor users have the ability to read, create, and update.');

        // Register user-defined roles
        // Roles::get()->map(fn ($role) => (
        //     Jetstream::role(
        //         $role->id,
        //         $role->name,
        //         $role->permissions
        //     )->description(
        //         $role->description
        //     )
        // ));

        return $this;
    }

    protected function loadJetstreamRoutes(): self
    {
        // Register inertia views and routes for jetstream
        $this->loadRoutesFrom(
            static::path('routes/jetstream.php')
        );

        return $this;
    }

    private function fallback(string $class, ...$classes): string
    {
        if (class_exists($class)) {
            return $class;
        }

        foreach ($classes as $class) {
            if (class_exists($class)) {
                return $class;
            }
        }
    }

    private function hasProvider(string $provider): bool
    {
        if (!$this->providers) {
            $this->providers = config('app.providers');
        }

        return in_array($provider, $this->providers);
    }

    static function path(string ...$path): string
    {
        return join('/', [
            Str::remove('src/Providers', __DIR__),
            ...$path
        ]);
    }
}