<?php

namespace Blazervel\Blazervel\Blade;

use Illuminate\Support\{ Str, Collection, Js };
use Illuminate\Database\Eloquent\{ Model, Builder, Collection as EloquentCollection };
use Illuminate\Support\Facades\View;

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

  public function renderWithoutLayout()
  {
    $conceptName = Str::snake(Concept::conceptName($this::class), '-');
    $operationName = Str::snake(class_basename($this::class));
    $stateData = Js::from($this->stateData());

    return View::make(CreateBladeView::fromString(
      "<div v-scope v-cloak @vue:mounted=\"init('{$conceptName}', '{$operationName}', {$stateData})\">
        <template v-if=\"mounted\">{$this->render()}</template>
      </div>"
    ));

  }

  public function renderWithLayout(string $layout)
  {
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
