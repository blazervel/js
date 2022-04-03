<?php

namespace Blazervel;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

use Blazervel\Web\ComponentCollection;
use Blazervel\Web\Attributes\AttributeCollection;
use Blazervel\Web\Attributes\ClassName;
use Blazervel\Web\Attributes\Style;
use Blazervel\Web\Attributes\ForCollection;
use Blazervel\Web\Attributes\Model;
use Blazervel\Web\Attributes\Action;

abstract class Component
{
  public string $tagName = 'div';
  public ?string $type = null;
  public ClassName $className;
  public Style $style;
  public ComponentCollection $children;
  public string $text;
  public ?ForCollection $for;
  public ?Model $model;
  public ?Action $click;
  public AttributeCollection $set;

  abstract protected function render(): self;

  public function __construct(
    string $tagName   = null,
    string $type      = null,
    array  $className = null,
    array  $style     = null,
    array  $children  = null,
    array  $for       = null,
    Model  $model     = null,
    Action $click     = null,
    array  $set       = null
  ) {

    $this->tagName = $tagName ?: $this->tagName;

    $this->type = $type;

    $this->className = new ClassName($className ?: []);

    $this->style = new Style($style);

    $this->children = new ComponentCollection($children ?: []);

    $this->for = $for;

    $this->model = $model;

    $this->click = $click;

    if ($set) :
      foreach($set as $attribute => $value) :
        unset($set[$attribute]);
        $set[Str::snake($attribute, '-')] = $value;
      endforeach;
    endif;

    $this->set = new AttributeCollection($set ?: []);
  }

  public function __invoke()
  {
    return View::make('layouts.app', ['slot' => $this->render()->toHtml()]);
  }

  public static function __callStatic($name, $arguments)
  {
    $calledClassName = get_called_class();

    switch($name) :
      case 'tagName' :
        return new $calledClassName(tagName: $arguments[0] ?? null);
        break;
      case 'type' :
        return new $calledClassName(type: $arguments[0] ?? null);
        break;
      case 'className' :
        return new $calledClassName(className: $arguments);
        break;
      case 'style' :
        return new $calledClassName(style: $arguments);
        break;
      case 'children' :
        return new $calledClassName(children: $arguments);
        break;
      case 'for' :
        return new $calledClassName(for: $arguments[0]);
        break;
      case 'model' :
        return new $calledClassName(model: $arguments[0]);
        break;
      case 'click' :
        return new $calledClassName(click: $arguments[0]);
        break;
      case 'set' :
        return new $calledClassName(set: $arguments);
        break;
    endswitch;
  }

  public function __call($name, $arguments)
  {
    switch($name) :
      case 'tagName' :
        $this->tagName = $arguments[0];
        break;
      case 'type' :
        $this->type = $arguments[0];
        break;
      case 'className' :
        $className = (new ClassName($arguments))->whereNotNull();
        if ($this->className) :
          $this->className = $this->className->merge($className);
        else :
          $this->className = $className;
        endif;
        break;
      case 'style' :
        $this->style = $this->style ?: [];
        break;
      case 'children' :
        $this->children = (new ComponentCollection($arguments))->whereNotNull();
        break;
      case 'unless' :
        $this->unless = $arguments[0];
        break;
      case 'model' :
        $this->model = $arguments[0];
        break;
      case 'click' :
        $this->click = $arguments[0];
        break;
      case 'set' :
        foreach($arguments as $attribute => $value) :
          unset($arguments[$attribute]);
          $arguments[Str::snake($attribute, '-')] = $value;
        endforeach;
        $set = (new AttributeCollection($arguments))->whereNotNull();
        if ($this->set) :
          $this->set = $this->set->merge($set);
        else :
          $this->set = $set;
        endif;
        break;
    endswitch;

    return $this;
  }

  protected function toHtml(): string
  {
    $children = '';
    
    if ($this->children->count() > 0) :

      $children = $this->children->map(function($component) {

        if (is_string($component)) :

          return $component;

        else :

          return $component->render()->toHtml();

        endif;

      })->join(' ');
      
    endif;

    $attributes = new AttributeCollection([
      'type' => $this->type,
      'class' => $this->className->string() ?: null,
      'style' => $this->style->string() ?: null,
    ]);

    if ($this->set->count() > 0) :
      $attributes = $attributes->merge($this->set);
    endif;
    
    $attributes = $attributes->string();

    if (in_array($this->tagName, ['input', 'img'])) :
      return trim("
        <{$this->tagName} {$attributes} />
      ");
    endif;

    return trim("
      <{$this->tagName} {$attributes}>{$children}</{$this->tagName}>
    ");
  }

}