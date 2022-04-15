<?php

namespace Blazervel\Blazervel;

use Illuminate\Support\{ Str, Collection, Js };
use Illuminate\Database\Eloquent\{ Model, Builder, Collection as EloquentCollection };
use Illuminate\Support\Facades\{ View, File };

use Blazervel\Blazervel\Components\Components\Dashboard;
use Blazervel\Blazervel\Components\CreateBladeView;
use Blazervel\Blazervel\Concept;

use Illuminate\View\Component as LaravelComponent;

abstract class Component extends LaravelComponent
{
  protected string $layout = Dashboard::class;

  protected string $id;
  
  protected $class;
  protected $style;

  protected $if;
  protected $unless;
  protected $for;
  protected $onclick;
  protected $slot;

  public function data()
  {
    $this->except = array_merge([
      'hasJsView',
      'stateData',
      'renderWithLayout',
      'attributes',
      'componentName'
    ], $this->except);

    return parent::data();
  }

  public function hasJsView(): bool
  {
    $concept = Concept::conceptFor($this::class);
    $operationName = class_basename($this::class);

    return File::exists(
      "{$concept->path}/resources/js/{$operationName}.js"
    );
  }

  public function render()
  {
    $calledClass   = get_called_class();
    $conceptName   = Concept::conceptName($calledClass);
    $conceptSlug   = Str::snake($conceptName, '-');
    $componentName = class_basename($calledClass);
    $stateData     = Js::from($this->stateData([
      'concept' => $conceptSlug,
      'operation' => $componentName,
      'componentPath' => "{$conceptName}/resources/js/{$componentName}",
    ]));

    if ($this->hasJsView()) :
      return View::make(CreateBladeView::fromString(
        "<div id=\"react\" data-initial-state=\"{$stateData}\"></div>"
      ));
    endif;
    
    if (
      !View::exists("blazervel.{$conceptName}::{$componentName}") &&
      View::exists("blazervel::{$componentName}")
    ) :
      $slot = View::make(
        "blazervel::{$componentName}",
        $this->data()
      );
    endif;

    $slot = View::make(
      "blazervel.{$conceptName}::{$componentName}",
      $this->data()
    );

    return View::make(CreateBladeView::fromString(
      "<div v-scope v-cloak @vue:mounted=\"init('{$conceptName}', '{$componentName}', {$stateData})\">
        <template v-if=\"mounted\">{$slot}</template>
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

    return View::make(CreateBladeView::fromString(
     "@extends('{$layout}')
      @section('content')
        {$this->render()}
      @endsection"
    ));
  }

  public function stateData(array $mergeData = []): array
  {
    $data = (new Collection(

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

    return array_merge($data, $mergeData);
  }

  // public function __get(string $name): mixed
  // {
  //   return $this->arguments[$name] ?? $this->$name;
  // }
}
