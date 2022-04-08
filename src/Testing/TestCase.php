<?php

namespace Blazervel\Blazervel\Testing;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase as AppTestCase;

abstract class TestCase extends AppTestCase
{
  use DatabaseMigrations;

  //
}
