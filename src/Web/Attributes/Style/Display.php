<?php

namespace Blazervel\Blazervel\Web\Attributes\Style;

enum Display: string
{
  case flex = 'display:flex';
  case inlineFlex = 'display:inline-flex';
  case block = 'display:block';
  case inline = 'display:inline';
  case inlineBlock = 'display:inline-block';
}