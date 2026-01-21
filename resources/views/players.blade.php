<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>FIFA</title>
<link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="css/players.css">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">

    </head>
        
    @include('header')

           Joueur Ici
           
    <footer>
    <a href="{{ route('cookies.manage') }}">Gérer mes cookies</a>
        <span>|</span>
    <a href="/privacy_policy"> Conditions d'utilisation </a>
        <span>|</span>
     <a href="/privacy_policy"> Respect de la vie privée </a> 
</footer>
    </body>

    @include('botman')
</html>