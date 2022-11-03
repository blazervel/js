<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-black">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Blazervel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/app.tsx')
  </head>
  <body class="font-sans antialiased"></body>
</html>