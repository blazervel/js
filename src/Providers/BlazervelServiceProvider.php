<?php

namespace Blazervel\Blazervel\Providers;

use Blazervel\Blazervel\View\TagCompiler;

use Tightenco\Ziggy\BladeRouteGenerator;
use Illuminate\Support\Facades\{ File, Blade, App, Lang };
use Illuminate\Support\{ Str, ServiceProvider };

class BlazervelServiceProvider extends ServiceProvider 
{
  private string $pathTo = __DIR__ . '/../..';

	public function register()
	{
    //
	}

  public function boot()
  {
    $this->loadViews();
    $this->loadComponents();
    $this->loadRoutes();
    $this->loadTranslations();
    $this->loadDirectives();
  }

  private function loadDirectives(): void
  {
    Blade::directive('blazervel', fn ($group) => trim("
      <script type=\"text/javascript\"> 
        const Blazervel = <?php echo Js::from(['translations' => " . self::class . "::translations()]) ?>
      </script>

      <?php echo app('" . BladeRouteGenerator::class . "')->generate({$group}); ?>
    "));
  }

  static function translations(): array
  {
    $translationFiles = File::files(
      lang_path(
        App::currentLocale()
      )
    );

    $langKey = fn ($file) => (
      Str::remove(".{$file->getExtension()}", $file->getFileName())
    );

    return (
      collect($translationFiles)
        ->map(fn ($file) => [$langKey($file) => Lang::get($langKey($file))])
        ->collapse()
        ->all()
    );
  }

  private function loadViews()
  {
    $this->loadViewsFrom(
      "{$this->pathTo}/resources/views", 'blazervel'
    );
  }

  private function loadComponents()
  {
    Blade::componentNamespace(
      'Blazervel\\Blazervel\\Components\\Components', 
      'blazervel'
    );

    if (method_exists($this->app['blade.compiler'], 'precompiler')) {
      $this->app['blade.compiler']->precompiler(function ($string) {
        return app(TagCompiler::class)->compile($string);
      });
    }
  }
  
  private function loadRoutes() 
  {
    $this->loadRoutesFrom(
      "{$this->pathTo}/routes/routes.php"
    );
  }

  private function loadTranslations() 
  {
    $this->loadTranslationsFrom(
      "{$this->pathTo}/lang", 
      'blazervel'
    );
  }

}