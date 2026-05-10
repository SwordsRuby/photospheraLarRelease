@extends('layouts.app')

@section('title', 'Тарифы')

@section('content')
    @include('components.profile_header')


    <div class="container my-60">
        <div class="flex-column main-title-block text-center">
            <h2 class="main-title wrap text-center" style="text-align: center;">Выберите тариф</h2>
            <div class="main-line" style="margin: 0 auto; margin-top: 10px;"></div>
            <p class="about-us_text text-center mt-20" style="margin: 10px auto 0;">Загружайте больше изображений с
                увеличенным хранилищем</p>
        </div>

        <div class="flex-wrap flex-center gap-block-20px mt-60 my-40">
            <!-- Basic plan -->
            <div class="admin-card">
                <h3 class="admin-card-title">Базовый</h3>
                <p class="admin-card-users-title">Бесплатно</p>
                <ul class="gap-block-10px flex-column" style="align-items: start; margin-top: 20px;">
                    <li>✓ 1 ГБ хранилища</li>
                    <li>✓ Приватные альбомы</li>
                    <li>✓ Базовая поддержка</li>
                </ul>
                @if ($currentSubscription && $currentSubscription->plan === 'basic')
                    <button class="black-button width-100 mt-20" disabled>Текущий тариф</button>
                @else
                    <a href="{{ route('subscription.checkout', 'basic') }}" class="black-button width-100 mt-20 text-center"
                        style="display: block;">Выбрать</a>
                @endif
            </div>

            <!-- Pro plan -->
            <div class="admin-card">
                <h3 class="admin-card-title">Pro</h3>
                <p class="admin-card-users-title">299₽/мес</p>
                <ul class="gap-block-10px flex-column" style="align-items: start; margin-top: 20px;">
                    <li>✓ 10 ГБ хранилища</li>
                    <li>✓ Приватные альбомы</li>
                    <li>✓ Приоритетная поддержка</li>
                </ul>
                @if ($currentSubscription && $currentSubscription->plan === 'pro')
                    <button class="black-button width-100 mt-20" disabled>Текущий тариф</button>
                @else
                    <a href="{{ route('subscription.checkout', 'pro') }}" class="red-button width-100 mt-20 text-center"
                        style="display: block;">Выбрать</a>
                @endif
            </div>

            <!-- Premium plan -->
            <div class="admin-card" style="border-color: #6366f1;">
                <h3 class="admin-card-title" style="color: #6366f1;">Premium</h3>
                <p class="admin-card-users-title">599₽/мес</p>
                <ul class="gap-block-10px flex-column" style="align-items: start; margin-top: 20px;">
                    <li>✓ 50 ГБ хранилища</li>
                    <li>✓ Приватные альбомы</li>
                    <li>✓ Приоритетная поддержка</li>
                </ul>
                @if ($currentSubscription && $currentSubscription->plan === 'premium')
                    <button class="black-button width-100 mt-20" disabled>Текущий тариф</button>
                @else
                    <a href="{{ route('subscription.checkout', 'premium') }}" class="red-button width-100 mt-20 text-center"
                        style="display: block;">Выбрать</a>
                @endif
            </div>
        </div>
    </div>
@endsection
