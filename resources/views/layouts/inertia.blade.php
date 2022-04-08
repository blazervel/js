<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}"/>
    <x-blazervel::styles :href="mix('css/app.css')"/>
    <x-blazervel::script :src="mix('blazervel/js/inertia.js')"/>
    @inertiaHead
  </head>
  <body class="font-sans antialiased">
    @inertia
  </body>
</html>