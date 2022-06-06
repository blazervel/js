<?php

namespace Blazervel\Blazervel\Operations\Traits;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

trait WithQueueable
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public static function dispatch(...$arguments)
  {
    dispatch(
      new self(...$arguments)
    );
  }
}