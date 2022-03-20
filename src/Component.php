<?php

namespace Blazervel\Blazervel;

use Blazervel\Blazervel\Exceptions\BlazervelComponentException;

abstract class Component
{

  // protected string $tagName = 'div';
  // protected ?string $className;
  // protected ?string $style;
  // protected array $children;

  // abstract protected function render(): Component;

  // public function __construct(string $tagName = null, string $className = null, string $style = null, array $children = [], ...$params)
  // {
  //   $this->tagName   = $tagName   ?: $this->tagName;
  //   $this->className = $className ?: $this->className;
  //   $this->style     = $style     ?: $this->style;
  //   $this->children  = array_merge($children, $params);
  // }

  // protected function mergeParams(Component $extenderComponent): Component
  // {
  //   if ($extenderComponent->tagName) :
  //     $this->tagName = $extenderComponent->tagName;
  //   endif;

  //   if ($extenderComponent->className) :
  //     $this->className.= " {$extenderComponent->className}";
  //   endif;

  //   if ($extenderComponent->style) :
  //     $this->style.= " {$extenderComponent->style}";
  //   endif;

  //   return $this;
  // }

  // public function __invoke()
  // {
  //   return $this->toHTML();
  // }

  // public function toHTML(): string
  // {
  //   // CardComponent Component ($this)
  //   $extenderComponent = $this;
    
  //   // Block Component (render-returned $component)
  //   $extendeeComponent = $extenderComponent->render();

  //   $paramMergedComponent = $extendeeComponent->mergeParams(
  //     $extenderComponent
  //   );

  //   $children = '';
  //   foreach($paramMergedComponent->children as $childComponent) :
  //     $children.= (new $childComponent)->print();
  //   endforeach;

  //   return trim("
  //     <{$paramMergedComponent->tagName} 
  //       class=\"{$paramMergedComponent->className}\" 
  //       style=\"{$paramMergedComponent->style}\"
  //     >{
  //       $children
  //     }</{$paramMergedComponent->tagName}>
  //   ");
  // }
}