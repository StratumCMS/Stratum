<?php
@extends('layouts.app')

@section('title', 'Accès interdit')

@section('content')
    <div class="container text-center mt-5">
        <h1>403 - Accès interdit</h1>
        <p>Vous n'avez pas la permission d'accéder à cette page.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Retour à l'accueil</a>
    </div>
@endsection
