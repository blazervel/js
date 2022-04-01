<?php

namespace Blazervel\Blazervel\Web\Attributes\Style;

use ReflectionClass;

class Property
{
  const media = '@media';
  const import = '@import';
  const charset = '@charset';
  const fontFace = '@font-face';
  const keyframes = '@keyframes';

  const alignContent = 'align-content';
  const alignItems = 'align-items';
  const alignSelf = 'align-self';
  const all = 'all';
  const animation = 'animation';
  const animationDelay = 'animation-delay';
  const animationDirection = 'animation-direction';
  const animationDuration = 'animation-duration';
  const animationFillMode = 'animation-fill-mode';
  const animationIterationCount = 'animation-iteration-count';
  const animationName = 'animation-name';
  const animationPlayState = 'animation-play-state';
  const animationTimingFunction = 'animation-timing-function';
  const backfaceVisibility = 'backface-visibility';
  const background = 'background';
  const backgroundAttachment = 'background-attachment';
  const backgroundBlendMode = 'background-blend-mode';
  const backgroundClip = 'background-clip';
  const backgroundColor = 'background-color';
  const backgroundImage = 'background-image';
  const backgroundOrigin = 'background-origin';
  const backgroundPosition = 'background-position';
  const backgroundRepeat = 'background-repeat';
  const backgroundSize = 'background-size';
  const border = 'border';
  const borderBottom = 'border-bottom';
  const borderBottomColor = 'border-bottom-color';
  const borderBottomLeftRadius = 'border-bottom-left-radius';
  const borderBottomRightRadius = 'border-bottom-right-radius';
  const borderBottomStyle = 'border-bottom-style';
  const borderBottomWidth = 'border-bottom-width';
  const borderCollapse = 'border-collapse';
  const borderColor = 'border-color';
  const borderImage = 'border-image';
  const borderImageOutset = 'border-image-outset';
  const borderImageRepeat = 'border-image-repeat';
  const borderImageSlice = 'border-image-slice';
  const borderImageSource = 'border-image-source';
  const borderImageWidth = 'border-image-width';
  const borderLeft = 'border-left';
  const borderLeftColor = 'border-left-color';
  const borderLeftStyle = 'border-left-style';
  const borderLeftWidth = 'border-left-width';
  const borderRadius = 'border-radius';
  const borderRight = 'border-right';
  const borderRightColor = 'border-right-color';
  const borderRightStyle = 'border-right-style';
  const borderRightWidth = 'border-right-width';
  const borderSpacing = 'border-spacing';
  const borderStyle = 'border-style';
  const borderTop = 'border-top';
  const borderTopColor = 'border-top-color';
  const borderTopLeftRadius = 'border-top-left-radius';
  const borderTopRightRadius = 'border-top-right-radius';
  const borderTopStyle = 'border-top-style';
  const borderTopWidth = 'border-top-width';
  const borderWidth = 'border-width';
  const bottom = 'bottom';
  const boxDecorationBreak = 'box-decoration-break';
  const boxShadow = 'box-shadow';
  const boxSizing = 'box-sizing';
  const breakAfter = 'break-after';
  const breakBefore = 'break-before';
  const breakInside = 'break-inside';
  const captionSide = 'caption-side';
  const caretColor = 'caret-color';
  const clear = 'clear';
  const clip = 'clip';
  const clipPath = 'clip-path';
  const color = 'color';
  const columnCount = 'column-count';
  const columnFill = 'column-fill';
  const columnGap = 'column-gap';
  const columnRule = 'column-rule';
  const columnRuleColor = 'column-rule-color';
  const columnRuleStyle = 'column-rule-style';
  const columnRuleWidth = 'column-rule-width';
  const columnSpan = 'column-span';
  const columnWidth = 'column-width';
  const columns = 'columns';
  const content = 'content';
  const counterIncrement = 'counter-increment';
  const counterReset = 'counter-reset';
  const cursor = 'cursor';
  const direction = 'direction';
  const display = 'display';
  const emptyCells = 'empty-cells';
  const filter = 'filter';
  const flex = 'flex';
  const flexBasis = 'flex-basis';
  const flexDirection = 'flex-direction';
  const flexFlow = 'flex-flow';
  const flexGrow = 'flex-grow';
  const flexShrink = 'flex-shrink';
  const flexWrap = 'flex-wrap';
  const floatDirection = 'float';
  const font = 'font';
  const fontFamily = 'font-family';
  const fontFeatureSettings = 'font-feature-settings';
  const fontKerning = 'font-kerning';
  const fontSize = 'font-size';
  const fontSizeAdjust = 'font-size-adjust';
  const fontStretch = 'font-stretch';
  const fontStyle = 'font-style';
  const fontVariant = 'font-variant';
  const fontVariantCaps = 'font-variant-caps';
  const fontWeight = 'font-weight';
  const gap = 'gap';
  const grid = 'grid';
  const gridArea = 'grid-area';
  const gridAutoColumns = 'grid-auto-columns';
  const gridAutoFlow = 'grid-auto-flow';
  const gridAutoRows = 'grid-auto-rows';
  const gridColumn = 'grid-column';
  const gridColumnEnd = 'grid-column-end';
  const gridColumnGap = 'grid-column-gap';
  const gridColumnStart = 'grid-column-start';
  const gridGap = 'grid-gap';
  const gridRow = 'grid-row';
  const gridRowEnd = 'grid-row-end';
  const gridRowGap = 'grid-row-gap';
  const gridRowStart = 'grid-row-start';
  const gridTemplate = 'grid-template';
  const gridTemplateAreas = 'grid-template-areas';
  const gridTemplateColumns = 'grid-template-columns';
  const gridTemplateRows = 'grid-template-rows';
  const hangingPunctuation = 'hanging-punctuation';
  const height = 'height';
  const hyphens = 'hyphens';
  const imageRendering = 'image-rendering';
  const isolation = 'isolation';
  const justifyContent = 'justify-content';
  const left = 'left';
  const letterSpacing = 'letter-spacing';
  const lineHeight = 'line-height';
  const listStyle = 'list-style';
  const listStyleImage = 'list-style-image';
  const listStylePosition = 'list-style-position';
  const listStyleType = 'list-style-type';
  const margin = 'margin';
  const marginBottom = 'margin-bottom';
  const marginLeft = 'margin-left';
  const marginRight = 'margin-right';
  const marginTop = 'margin-top';
  const maskImage = 'mask-image';
  const maskMode = 'mask-mode';
  const maskOrigin = 'mask-origin';
  const maskPosition = 'mask-position';
  const maskRepeat = 'mask-repeat';
  const maskSize = 'mask-size';
  const maxHeight = 'max-height';
  const maxWidth = 'max-width';
  const minHeight = 'min-height';
  const minWidth = 'min-width';
  const mixBlendMode = 'mix-blend-mode';
  const objectFit = 'object-fit';
  const objectPosition = 'object-position';
  const opacity = 'opacity';
  const order = 'order';
  const orphans = 'orphans';
  const outline = 'outline';
  const outlineColor = 'outline-color';
  const outlineOffset = 'outline-offset';
  const outlineStyle = 'outline-style';
  const outlineWidth = 'outline-width';
  const overflow = 'overflow';
  const overflowWrap = 'overflow-wrap';
  const overflowX = 'overflow-x';
  const overflowY = 'overflow-y';
  const padding = 'padding';
  const paddingBottom = 'padding-bottom';
  const paddingLeft = 'padding-left';
  const paddingRight = 'padding-right';
  const paddingTop = 'padding-top';
  const pageBreakAfter = 'page-break-after';
  const pageBreakBefore = 'page-break-before';
  const pageBreakInside = 'page-break-inside';
  const perspective = 'perspective';
  const perspectiveOrigin = 'perspective-origin';
  const pointerEvents = 'pointer-events';
  const position = 'position';
  const quotes = 'quotes';
  const resize = 'resize';
  const right = 'right';
  const rowGap = 'row-gap';
  const scrollBehavior = 'scroll-behavior';
  const tabSize = 'tab-size';
  const tableLayout = 'table-layout';
  const textAlign = 'text-align';
  const textAlignLast = 'text-align-last';
  const textDecoration = 'text-decoration';
  const textDecorationColor = 'text-decoration-color';
  const textDecorationLine = 'text-decoration-line';
  const textDecorationStyle = 'text-decoration-style';
  const textDecorationThickness = 'text-decoration-thickness';
  const textIndent = 'text-indent';
  const textJustify = 'text-justify';
  const textOverflow = 'text-overflow';
  const textShadow = 'text-shadow';
  const textTransform = 'text-transform';
  const top = 'top';
  const transform = 'transform';
  const transformOrigin = 'transform-origin';
  const transformStyle = 'transform-style';
  const transition = 'transition';
  const transitionDelay = 'transition-delay';
  const transitionDuration = 'transition-duration';
  const transitionProperty = 'transition-property';
  const transitionTimingFunction = 'transition-timing-function';
  const unicodeBidi = 'unicode-bidi';
  const userSelect = 'user-select';
  const verticalAlign = 'vertical-align';
  const visibility = 'visibility';
  const whiteSpace = 'white-space';
  const widows = 'widows';
  const width = 'width';
  const wordBreak = 'word-break';
  const wordSpacing = 'word-spacing';
  const wordWrap = 'word-wrap';
  const writingMode = 'writing-mode';
  const zIndex = 'z-index';

  public static function all(): array
  {
    $properties = new ReflectionClass(__CLASS__);
    return $properties->getConstants();
  }

  public static function find(string $propertyName): string|null
  {
    $properties = self::all();
    
    return $properties[$propertyName] ?? null;
  }

  public static function exists(string $propertyName): bool
  {
    return self::find($propertyName) !== null;
  }
}