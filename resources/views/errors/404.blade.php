@extends('layouts.app')

@section('title', '404 - Страница не найдена')

@section('content')
    <div class="red-background-block flex-column flex-center">
        <h1 class="error-title">404</h1>
        <p class="error-text">страница не найдена</p>
        <a class="error-button"
            href="{{ Auth::check() && Auth::user()->is_moderator ? route('admin.images') : route('home') }}">на главную</a>
    </div>
@endsection
