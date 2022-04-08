<?php

namespace Blazervel\Blazervel\PHPNative;

use Blazervel\Blazervel\Exceptions\BlazervelComponentException;

abstract class Component
{
  protected string $tagName   = 'div';
  protected string $id        = '';
  protected string $className = '';
  protected string $style     = '';
  protected array $children   = [];
  protected array $content   = [];

  protected array $supportedAttributes = [
    'tagName',
    'id',
    'className',
    'style',
    'children'
  ];

  abstract protected function render(): Component;

  public function __construct(
    string $tagName = null,
    string $id = null,
    string $className = null,
    string $style = null,
    array $children = null
  )
  {
    if ($id) :
      $this->id = $id;
    endif;

    if ($tagName) :
      $this->tagName = $tagName;
    endif;

    if ($className) :
      $this->className = trim(join(' ', array_merge(explode(' ', $this->className), explode(' ', $className))));
    endif;

    if ($style) :
      $this->style.= " {$style}";
    endif;

    if ($children) :
      $this->children = $children;
    endif;
  }

  public function __invoke()
  {
    return $this->extrapolate()->toHtml();
  }

  public function inheritAttributes(Component $extendee): void
  {
    foreach($this->supportedAttributes as $attr) :
      $this->$attr = $this->$attr ?: $extendee->$attr;
    endforeach;
  }

  public function extrapolate()
  {
    $extendees = [
      $extender = $this
    ];

    do {

      $extendees[] = $extendee = $extender->render();

      $extender = $extendee;

    } while (get_parent_class($extender) === self::class && get_class($extender) !== self::class);

    foreach ($extendees as $extendee) :
      $this->inheritAttributes($extendee);
    endforeach;

    return $this;
  }

  public function toHtml(): string
  {
    $childrenHtml = '';

    foreach($this->children as $childComponent) :
      $childrenHtml.= $childComponent; //(new $childComponent)->print();
    endforeach;

    return trim("
      <{$this->tagName} 
        class=\"{$this->className}\" 
        style=\"{$this->style}\"
      >{$childrenHtml}</{$this->tagName}>
    ");
  }
}