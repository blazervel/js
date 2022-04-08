<?php

namespace Blazervel\Blazervel\Blade;

use Illuminate\Support\Str;
use Illuminate\Routing\Route;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Js;

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

  // protected array $only = [
  //   'id', 
  //   'class', 
  //   'style', 
  //   'for', 
  //   'if', 
  //   'unless'
  // ];

  public function __construct(string $slot = null)
  {
    $this->id = (string) Str::orderedUuid();
    $this->slot = $slot ?: '';
  }

  public function render()
  {
    $calledClass   = get_called_class();
    $concept       = Concept::conceptNamespace($calledClass);
    $conceptName   = Str::snake(class_basename($concept), '-');
    $componentName = class_basename($calledClass);
    
    if (
      !View::exists("concepts.{$conceptName}::{$componentName}") &&
      View::exists("blazervel::{$componentName}")
    ) :
      return View::make(
        "blazervel::{$componentName}"
      );
    endif;

    return View::make(
      "concepts.{$conceptName}::{$componentName}"
    );
  }

  public function state()
  {
    return $this->data();
  }

  public function renderWithoutLayout()
  {
    $componentKey = Str::snake(Str::replace('\\', ' ', Str::remove('\\Components', Str::remove('App\\Concepts', $this::class))), '-');

    return View::make(CreateBladeView::fromString(
      "<div v-scope @vue:mounted=\"getState('{$componentKey}', {attribute: 'value'})\">
        {$this->render()}
      </div>"
    ), $this->data());

  }

  public function renderWithLayout(string $layout)
  {
    return View::make(CreateBladeView::fromString(
      "
      @extends('{$layout}')

      @section('content')

        {$this->renderWithoutLayout()}

      @endsection
      "
    ));
  }

  // public function __get(string $name): mixed
  // {
  //   return $this->arguments[$name] ?? $this->$name;
  // }
}
