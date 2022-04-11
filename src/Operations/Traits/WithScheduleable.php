<?php

namespace Blazervel\Blazervel\Operations\Traits;

trait WithScheduleable
{
  public string $scheduleFrequency;
  public array $scheduleArguments = [];
}