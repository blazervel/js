<?php

namespace Blazervel\Blazervel\Blade\Components;

use Blazervel\Blazervel\Blade\Component;

class Dashboard extends Component
{
  public function render()
  {
    $component = $this;

    return <<<'blade'

      <!DOCTYPE html>

      <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <meta name="csrf-token" content="{{ csrf_token() }}">

          <title>{{ config('app.name', 'Laravel') }}</title>

          <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}"/>
          <x-blazervel::styles :href="mix('css/app.css')"/>
          <x-blazervel::script :src="mix('js/blazervel.js')"/>
        </head>
        
        <body id="petite" class="font-sans antialiased">

          {{ $slot }} 

        </body>

      </html>
      
    blade;
  }
}