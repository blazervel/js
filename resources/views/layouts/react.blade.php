<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}"/>
    <x-blazervel::styles :href="mix('css/app.css')"/>
    <x-blazervel::script :src="mix('blazervel/js/react.js')"/>
    @include('blazervel::head')
  </head>
  <body class="font-sans antialiased">
    @yield('content')
  </body>
</html>