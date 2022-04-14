<?php

namespace Blazervel\Blazervel\Blade;

use Illuminate\Support\{ Str, Collection, Js };
use Illuminate\Database\Eloquent\{ Model, Builder, Collection as EloquentCollection };
use Illuminate\Support\Facades\{ View, File };

use Blazervel\Blazervel\Blade\Components\Dashboard;
use Blazervel\Blazervel\Blade\State;
use Blazervel\Blazervel\Concept;

use Illuminate\View\Component as LaravelComponent;

abstract class Component extends LaravelComponent
{
  protected State $state;

  protected string $layout = Dashboard::class;

  protected string $id;
  
  protected $class;
  protected $style;

  protected $if;
  protected $unless;
  protected $for;
  protected $onclick;

  protected $slot;

  public function render()
  {
    $calledClass   = get_called_class();
    $concept       = Concept::conceptNamespace($calledClass);
    $conceptName   = Str::snake(class_basename($concept), '-');
    $componentName = class_basename($calledClass);
    
    if (
      !View::exists("blazervel.{$conceptName}::{$componentName}") &&
      View::exists("blazervel::{$componentName}")
    ) :
      return View::make(
        "blazervel::{$componentName}",
        $this->data()
      );
    endif;

    return View::make(
      "blazervel.{$conceptName}::{$componentName}",
      $this->data()
    );
  }

  public function componentKey()
  {
    return Str::snake(Str::replace('\\', ' ', 
      Str::remove('\\Components', 
        Str::remove('App\\Concepts', $this::class)
      )
    ), '-');
  }

  public function hasJsView()
  {
    $concept = Concept::conceptFor($this::class);
    $operationName = class_basename($this::class);

    return File::exists(
      "{$concept->path}/resources/js/{$operationName}.js"
    );
  }

  public function renderWithoutLayout()
  {
    $conceptName   = Concept::conceptName($this::class);
    $conceptSlug   = Str::snake($conceptName, '-');
    $operationName = class_basename($this::class);
    $operationSlug = Str::snake($operationName);
    $stateData     = Js::from($this->stateData());

    if ($this->hasJsView()) :
      return View::make(CreateBladeView::fromString(
        "<div 
          id=\"react\" 
          data-concept=\"{$conceptSlug}\" 
          data-operation=\"{$operationName}\" 
          data-initial-state=\"{$stateData}\"
          data-component-path=\"{$conceptName}/resources/js/{$operationName}\"
        ></div>"
      ));
    endif;

    return View::make(CreateBladeView::fromString(
      "<div v-scope v-cloak @vue:mounted=\"init('{$conceptName}', '{$operationName}', {$stateData})\">
        <template v-if=\"mounted\">{$this->render()}</template>
      </div>"
    ));

  }

  public function renderWithLayout()
  {
    $concept     = Concept::conceptFor($this::class);
    $layoutType  = $this->hasJsView() ? 'react' : 'app';
    $layout      = "blazervel::layouts.{$layoutType}";
    $conceptSlug = Str::snake($concept->name, '-');

    if (
      View::exists("blazervel.{$conceptSlug}::layouts.{$layoutType}")
    ) :
      $layout = "blazervel.{$conceptSlug}::layouts.{$layoutType}";
    elseif (
      View::exists("blazervel.shared::layouts.{$layoutType}")
    ) :
      $layout = "blazervel.shared::layouts.{$layoutType}";
    endif;

    return View::make(CreateBladeView::fromString("
      @extends('{$layout}')
      @section('content')
        {$this->renderWithoutLayout()}
      @endsection
    "));
  }

  public function stateData()
  {
    return (new Collection(

      $this->data()

    ))->map(function($value){

      if ($value instanceof Model) :

        return $value->toArray();

      elseif ($value instanceof Builder) :

        return $value->get()->map(function($value){

          if ($value instanceof Model) :

            return $value->toArray();

          endif;

          return $value;

        })->all();

      elseif (
        $value instanceof EloquentCollection ||
        $value instanceof Collection
      ) :

        return $value->map(function($value){

          if ($value instanceof Model) :

            return $value->toArray();

          endif;

          return $value;

        })->all();

      endif;

      return $value;

    })->all();
  }

  // public function __get(string $name): mixed
  // {
  //   return $this->arguments[$name] ?? $this->$name;
  // }
}
