@extends('layouts.app')

@section('title', 'Мое хранилище')

@section('content')
    @include('components.profile_header')


    <div class="container my-40">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Мое хранилище</h2>
            <div class="main-line"></div>
        </div>

        <div class="admin-card my-40" style="max-width: 100%;">
            <div class="width-100">
                <div class="flex-space-between mb-10">
                    <span class="admin-card-users-title">Использовано места</span>
                    <span class="admin-card-users-title">{{ $storageData['total_formatted'] }} /
                        {{ $storageData['limit_formatted'] }}</span>
                </div>
                <div class="progress-bar"
                    style="width: 100%; height: 20px; background: #e0e0e0; border-radius: 10px; overflow: hidden;">
                    <div class="progress-fill"
                        style="width: {{ $storageData['used_percent'] }}%; height: 100%; background: #6366f1; transition: width 0.3s;">
                    </div>
                </div>
                <div class="flex-space-between mt-10">
                    <span>Осталось: {{ $storageData['remaining_formatted'] }}</span>
                    <span>{{ round($storageData['used_percent'], 1) }}%</span>
                </div>
            </div>
        </div>

        @if ($subscription->plan !== 'premium')
            <div class="flex-column main-title-block mt-60 my-40">
                <h2 class="main-title wrap">Нужно больше места?</h2>
                <div class="main-line"></div>
            </div>

            <div class="tablet-2-col gap-block-20px mt-40 my-20">
                @if ($subscription->plan !== 'pro')
                    <div class="admin-card">
                        <h3 class="admin-card-title">Pro</h3>
                        <p class="admin-card-users-title">299₽/мес</p>
                        <ul class="gap-block-10px flex-column" style="align-items: start;">
                            <li>✓ 10 ГБ хранилища</li>
                            <li>✓ Приватные альбомы</li>
                            <li>✓ Приоритетная поддержка</li>
                        </ul>
                        <a href="{{ route('subscription.checkout', 'pro') }}" class="red-button width-100 mt-20">Выбрать</a>
                    </div>
                @endif

                @if ($subscription->plan !== 'premium')
                    <div class="admin-card" style="border-color: #6366f1;">
                        <h3 class="admin-card-title" style="color: #6366f1;">Premium</h3>
                        <p class="admin-card-users-title">599₽/мес</p>
                        <ul class="gap-block-10px flex-column" style="align-items: start;">
                            <li>✓ 50 ГБ хранилища</li>
                            <li>✓ Приватные альбомы</li>
                            <li>✓ Приоритетная поддержка</li>
                        </ul>
                        <a href="{{ route('subscription.checkout', 'premium') }}"
                            class="red-button width-100 mt-20">Выбрать</a>
                    </div>
                @endif
            </div>
        @endif

        @if ($subscription->plan !== 'basic')
            <div class="flex-space-between mobile-column mt-60">
                <p>Текущий тариф: <strong>{{ $subscription->plan_name }}</strong></p>
                @if ($subscription->expires_at)
                    <p>Действует до: <strong>{{ $subscription->expires_at->format('d.m.Y') }}</strong></p>
                @endif
                <form method="POST" action="{{ route('subscription.cancel') }}"
                    onsubmit="return confirm('Отменить подписку? Вы перейдете на базовый тариф (1 ГБ).')">
                    @csrf
                    <button type="submit" class="black-button">Отменить подписку</button>
                </form>
            </div>
        @endif
    </div>

    <style>
        .progress-bar {
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            background: #6366f1;
            transition: width 0.3s ease;
            border-radius: 10px;
        }
    </style>
@endsection
