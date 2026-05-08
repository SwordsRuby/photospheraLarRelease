@extends('layouts.app')

@section('title', '403 - Доступ запрещен')

@section('content')
    <div class="red-background-block flex-column flex-center">
        <h1 class="error-title">403</h1>
        <p class="error-text">доступ запрещен</p>
        <a class="error-button"
            href="{{ Auth::check() && Auth::user()->is_moderator ? route('admin.images') : route('home') }}">на главную</a>
    </div>
@endsection
