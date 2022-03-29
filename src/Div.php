<?php

namespace Blazervel\Blazervel;

use Illuminate\Support\Facades\View;

abstract class Div
{
  public string  $tagName = 'div';
  public ?array  $className;
  public ?array  $style;
  public ?array  $content;
  public ?string $text;
  public ?string $type;
  public ?array  $for;
  public ?array  $if;
  public ?array  $unless;
  public ?array  $model;

  abstract protected function render(): self;

  public function __construct(
    string $tagName = null,
    array $className = null,
    array $style = null,
    array $content = null,
    array $text = null,
    string $type = null,
    array $for = null,
    array $if = null,
    array $unless = null,
    array $model = null
  )
  {
    $this->tagName   = $tagName ?: $this->tagName;
    $this->className = $className;
    $this->style     = $style;
    $this->content   = $content;
    $this->text      = $text;
    $this->type      = $type;
    $this->for       = $for;
    $this->if        = $if;
    $this->unless    = $for;
    $this->model     = $model;
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
      case 'className' :
        return new $calledClassName(className: $arguments);
        break;
      case 'style' :
        return new $calledClassName(style: $arguments);
        break;
      case 'content' :
        return new $calledClassName(content: $arguments);
        break;
      case 'text' :
        return new $calledClassName(text: $arguments[0] ?? null);
        break;
      case 'type' :
        return new $calledClassName(type: $arguments[0] ?? null);
        break;
      case 'for' :
        return new $calledClassName(for: $arguments[0] ?? null);
        break;
      case 'if' :
        return new $calledClassName(if: $arguments[0] ?? null);
        break;
      case 'unless' :
        return new $calledClassName(unless: $arguments[0] ?? null);
        break;
      case 'model' :
        return new $calledClassName(model: $arguments[0] ?? null);
        break;
    endswitch;
  }

  public function __call($name, $arguments)
  {
    switch($name) :
      case 'tagName' :
        $this->tagName = $arguments[0] ?? $this->tagName;
        break;
      case 'className' :
        $this->className = array_merge($this->className ?: [], $arguments);
        break;
      case 'style' :
        $this->style = array_merge($this->style ?: [], $arguments);
        break;
      case 'content' :
        $this->content = $arguments ?: $this->content;
        break;
      case 'text' :
        $this->text = $arguments[0] ?: $this->text;
        break;
      case 'type' :
        $this->type = $arguments[0] ?: $this->type;
        break;
      case 'for' :
        $this->for = $arguments[0] ?: $this->for;
        break;
      case 'if' :
        $this->if = $arguments[0] ?: $this->if;
        break;
      case 'unless' :
        $this->unless = $arguments[0] ?: $this->unless;
        break;
      case 'model' :
        $this->model = $arguments[0] ?: $this->model;
        break;
    endswitch;

    return $this;
  }

  protected function toHtml(): string
  {
    $content = '';
    
    foreach($this->content ?: [] as $component) :
      $content.= is_string($component) ? $component : $component->render()->toHtml();
    endforeach;

    $attributes = collect([
      'type' => $this->type,
      'class' => $this->className ? trim(join(' ', $this->className ?: [])) : null,
      'style' => $this->style     ? trim(join(';', $this->style ?: []))     : null,
    ])->whereNotNull()->map(function($value, $attribute){
      return "{$attribute}=\"{$value}\"";
    })->join(' ');

    if (in_array($this->tagName, ['input', 'img'])) :
      return trim("
        <{$this->tagName} {$attributes} />
      ");
    endif;

    return trim("
      <{$this->tagName} {$attributes} >{$content}</{$this->tagName}>
    ");
  }

}