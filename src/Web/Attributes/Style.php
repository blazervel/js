<?php

namespace Blazervel\Web\Attributes;

// use Blazervel\Web\Attributes\Traits\WithTailwind;

use Blazervel\Exceptions\BlazervelComponentAttributeStyleException;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Blazervel\Web\Attributes\Style\Property;
use Blazervel\Web\Attributes\Style\Pseudo;

class Style
{
  public string $alignContent;
  public string $alignItems;
  public string $alignSelf;
  public string $all;
  public string $animation;
  public string $animationDelay;
  public string $animationDirection;
  public string $animationDuration;
  public string $animationFillMode;
  public string $animationIterationCount;
  public string $animationName;
  public string $animationPlayState;
  public string $animationTimingFunction;
  public string $backfaceVisibility;
  public string $background;
  public string $backgroundAttachment;
  public string $backgroundBlendMode;
  public string $backgroundClip;
  public string $backgroundColor;
  public string $backgroundImage;
  public string $backgroundOrigin;
  public string $backgroundPosition;
  public string $backgroundRepeat;
  public string $backgroundSize;
  public string $border;
  public string $borderBottom;
  public string $borderBottomColor;
  public string $borderBottomLeftRadius;
  public string $borderBottomRightRadius;
  public string $borderBottomStyle;
  public string $borderBottomWidth;
  public string $borderCollapse;
  public string $borderColor;
  public string $borderImage;
  public string $borderImageOutset;
  public string $borderImageRepeat;
  public string $borderImageSlice;
  public string $borderImageSource;
  public string $borderImageWidth;
  public string $borderLeft;
  public string $borderLeftColor;
  public string $borderLeftStyle;
  public string $borderLeftWidth;
  public string $borderRadius;
  public string $borderRight;
  public string $borderRightColor;
  public string $borderRightStyle;
  public string $borderRightWidth;
  public string $borderSpacing;
  public string $borderStyle;
  public string $borderTop;
  public string $borderTopColor;
  public string $borderTopLeftRadius;
  public string $borderTopRightRadius;
  public string $borderTopStyle;
  public string $borderTopWidth;
  public string $borderWidth;
  public string $bottom;
  public string $boxDecorationBreak;
  public string $boxShadow;
  public string $boxSizing;
  public string $breakAfter;
  public string $breakBefore;
  public string $breakInside;
  public string $captionSide;
  public string $caretColor;
  public string $charset;
  public string $clear;
  public string $clip;
  public string $clipPath;
  public string $color;
  public string $columnCount;
  public string $columnFill;
  public string $columnGap;
  public string $columnRule;
  public string $columnRuleColor;
  public string $columnRuleStyle;
  public string $columnRuleWidth;
  public string $columnSpan;
  public string $columnWidth;
  public string $columns;
  public string $content;
  public string $counterIncrement;
  public string $counterReset;
  public string $cursor;
  public string $direction;
  public string $display;
  public string $emptyCells;
  public string $filter;
  public string $flex;
  public string $flexBasis;
  public string $flexDirection;
  public string $flexFlow;
  public string $flexGrow;
  public string $flexShrink;
  public string $flexWrap;
  public string $float;
  public string $font;
  public string $fontFace;
  public string $fontFamily;
  public string $fontFeatureSettings;
  public string $fontKerning;
  public string $fontSize;
  public string $fontSizeAdjust;
  public string $fontStretch;
  public string $fontStyle;
  public string $fontVariant;
  public string $fontVariantCaps;
  public string $fontWeight;
  public string $gap;
  public string $grid;
  public string $gridArea;
  public string $gridAutoColumns;
  public string $gridAutoFlow;
  public string $gridAutoRows;
  public string $gridColumn;
  public string $gridColumnEnd;
  public string $gridColumnGap;
  public string $gridColumnStart;
  public string $gridGap;
  public string $gridRow;
  public string $gridRowEnd;
  public string $gridRowGap;
  public string $gridRowStart;
  public string $gridTemplate;
  public string $gridTemplateAreas;
  public string $gridTemplateColumns;
  public string $gridTemplateRows;
  public string $hangingPunctuation;
  public string $height;
  public string $hyphens;
  public string $imageRendering;
  public string $import;
  public string $isolation;
  public string $justifyContent;
  public string $keyframes;
  public string $left;
  public string $letterSpacing;
  public string $lineHeight;
  public string $listStyle;
  public string $listStyleImage;
  public string $listStylePosition;
  public string $listStyleType;
  public string $margin;
  public string $marginBottom;
  public string $marginLeft;
  public string $marginRight;
  public string $marginTop;
  public string $maskImage;
  public string $maskMode;
  public string $maskOrigin;
  public string $maskPosition;
  public string $maskRepeat;
  public string $maskSize;
  public string $maxHeight;
  public string $maxWidth;
  public string $media;
  public string $minHeight;
  public string $minWidth;
  public string $mixBlendMode;
  public string $objectFit;
  public string $objectPosition;
  public string $opacity;
  public string $order;
  public string $orphans;
  public string $outline;
  public string $outlineColor;
  public string $outlineOffset;
  public string $outlineStyle;
  public string $outlineWidth;
  public string $overflow;
  public string $overflowWrap;
  public string $overflowX;
  public string $overflowY;
  public string $padding;
  public string $paddingBottom;
  public string $paddingLeft;
  public string $paddingRight;
  public string $paddingTop;
  public string $pageBreakAfter;
  public string $pageBreakBefore;
  public string $pageBreakInside;
  public string $perspective;
  public string $perspectiveOrigin;
  public string $pointerEvents;
  public string $position;
  public string $quotes;
  public string $resize;
  public string $right;
  public string $rowGap;
  public string $scrollBehavior;
  public string $tabSize;
  public string $tableLayout;
  public string $textAlign;
  public string $textAlignLast;
  public string $textDecoration;
  public string $textDecorationColor;
  public string $textDecorationLine;
  public string $textDecorationStyle;
  public string $textDecorationThickness;
  public string $textIndent;
  public string $textJustify;
  public string $textOverflow;
  public string $textShadow;
  public string $textTransform;
  public string $top;
  public string $transform;
  public string $transformOrigin;
  public string $transformStyle;
  public string $transition;
  public string $transitionDelay;
  public string $transitionDuration;
  public string $transitionProperty;
  public string $transitionTimingFunction;
  public string $unicodeBidi;
  public string $userSelect;
  public string $verticalAlign;
  public string $visibility;
  public string $whiteSpace;
  public string $widows;
  public string $width;
  public string $wordBreak;
  public string $wordSpacing;
  public string $wordWrap;
  public string $writingMode;
  public string $zIndex;

  // Pseudos
  public array $lang; // [$lang => new Style()]
  public array $nthChild; // [$nth => new Style()]
  public array $nthLastChild;
  public array $nthLastOfType;
  public array $nthOfType;
  public self $not;
  public self $active;
  public self $checked;
  public self $disabled;
  public self $empty;
  public self $enabled;
  public self $firstChild;
  public self $firstOfType;
  public self $focus;
  public self $hover;
  public self $inRange;
  public self $invalid;
  public self $lastChild;
  public self $lastOfType;
  public self $link;
  public self $onlyOfType;
  public self $onlyChild;
  public self $optional;
  public self $outOfRange;
  public self $readOnly;
  public self $readWrite;
  public self $required;
  public self $root;
  public self $target;
  public self $valid;
  public self $visited;
  public self $after;
  public self $before;
  public self $firstLetter;
  public self $firstLine;
  public self $selection;

  public function __construct(
    string|array|self|null ...$arguments
  ) {

    if (!$arguments[0]) return;

    foreach ($arguments as $propertyOrPsuedo => $valueOrStyleArray) :

      if (Property::exists($propertyOrPsuedo)) :

        $this->$propertyOrPsuedo = $valueOrStyleArray;

      elseif (Pseudo::exists($propertyOrPsuedo)) :

        // $this->$propertyOrPsuedo($valueOrStyleArray['nth'], $valueOrStyleArray['style']);

      else :

        throw new BlazervelComponentAttributeStyleException(
          "'{$propertyOrPsuedo}' is not a valid css property or pseudo"
        );

      endif;
      
    endforeach;
  }

  public function string()
  {
    $string = '';
    $styleProperties = Property::all();
    $styles = get_object_vars($this);

    foreach ($styleProperties as $key => $name) :
      
      if ($option = $styles[$key] ?? null) :
        
        $string.= "{$name}:{$option->value};";
        
      endif;
      
    endforeach;

    return $string;
  }
}