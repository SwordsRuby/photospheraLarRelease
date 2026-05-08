@extends('layouts.app')

@section('title', 'Админ панель - Пользователи')

@section('content')
<div class="admin-main">
    @include('admin.partials.menu')

    <div class="flex-column container filter-form" style="margin: 20px auto !important;">
        <form class="flex-column" method="get">
            <div class="search-block gap-block-20px">
                <img class="search-img" src="{{ asset('img/main/search.svg') }}" alt="search">
                <input value="{{ request('search') }}" autocomplete="off" class="search-input" type="search" placeholder="Поиск" name="search">
            </div>
        </form>
    </div>

    <div class="admin-categories-tag-catalog gap-block-20px flex-column container">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Пользователи</h2>
            <div class="main-line"></div>
        </div>

        <div class="tag-admin-block gap-block-10px flex-wrap width-100">
            @foreach($users as $user)
                <div id="user-{{ $user->id }}" class="admin-card">
                    <img src="{{ $user->img }}" alt="{{ $user->login }}" class="admin-user-img" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                    <h2 class="admin-card-users-title">{{ $user->login }}</h2>
                    <p>Изображений: {{ $user->images()->count() }}</p>
                    <form action="{{ $user->is_banned ? route('admin.users.unban', $user->id) : route('admin.users.ban', $user->id) }}" class="width-100" method="POST">
                        @csrf
                        <input type="submit" value="{{ $user->is_banned ? 'Снять бан' : 'Забанить' }}" 
                               class="width-100 {{ $user->is_banned ? 'black-button' : 'red-button' }} flex-center width-100 admin-img-button">
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection