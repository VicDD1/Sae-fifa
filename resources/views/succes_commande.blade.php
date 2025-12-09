@extends('layouts.app')

@section('title', 'Commande confirmée')

@section('content')
<link rel="stylesheet" href="{{ asset('css/success_commande.css') }}">

<div class="success-container">
    <div class="success-card">
        <h1 class="success-title">Votre commande a été passée avec succès</h1>

        <p class="success-message">
            Merci pour votre confiance. Votre commande a bien été enregistrée et est en cours de traitement.
        </p>

        <a href="{{ url('/') }}" class="btn-home">Retour à l’accueil</a>
    </div>
</div>
@endsection
