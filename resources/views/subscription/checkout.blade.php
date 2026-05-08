@extends('layouts.app')

@section('title', 'Оформление подписки')

@section('content')
    <div class="container my-60">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Оформление подписки</h2>
            <div class="main-line"></div>
        </div>

        <div class="flex-space-between mobile-column gap-block-40px mt-40">
            <div class="admin-card" style="width: 100%; max-width: 400px;">
                <h3 class="admin-card-title">{{ $selectedPlan['name'] }}</h3>
                <p class="admin-card-users-title">{{ $selectedPlan['price'] }}₽/мес</p>
                <ul class="gap-block-10px flex-column" style="align-items: start; margin-top: 20px;">
                    <li>✓ {{ $selectedPlan['storage_gb'] }} ГБ хранилища</li>
                    <li>✓ Приватные альбомы</li>
                    @if ($selectedPlan['priority_support'] ?? false)
                        <li>✓ Приоритетная поддержка</li>
                    @endif
                    @if ($selectedPlan['unlimited_albums'] ?? false)
                        <li>✓ Неограниченное кол-во альбомов</li>
                    @endif
                </ul>
            </div>

            <form method="POST" action="{{ route('subscription.process') }}" class="flex-column gap-block-20px"
                style="width: 100%; max-width: 400px;">
                @csrf
                <input type="hidden" name="plan" value="{{ $plan }}">

                <div class="width-100">
                    <label class="black-text">Срок подписки</label>
                    <select name="duration_months" class="input" required>
                        <option value="1">1 месяц - {{ $selectedPlan['price'] }}₽</option>
                        <option value="3">3 месяца - {{ $selectedPlan['price'] * 3 }}₽</option>
                        <option value="6">6 месяцев - {{ $selectedPlan['price'] * 6 }}₽</option>
                        <option value="12">12 месяцев - {{ $selectedPlan['price'] * 12 }}₽</option>
                    </select>
                </div>

                <div class="add-img-info-buttons gap-block-10px mobile-column mt-20">
                    <a href="{{ route('subscription.plans') }}" class="black-button add-img-button flex-center">Назад</a>
                    <button type="submit" class="red-button add-img-button flex-center">
                        Оплатить через YooKassa
                    </button>
                </div>
            </form>
        </div>

        <div class="info-text text-center mt-40" style="font-size: 12px; color: #888;">
            <p>Оплата производится через платежную систему ЮKassa. Ваши данные защищены.</p>
            <p>После оплаты вы будете перенаправлены обратно на сайт.</p>
        </div>
    </div>

    <style>
        .mt-20 {
            margin-top: 20px;
        }

        .mt-40 {
            margin-top: 40px;
        }

        .mt-60 {
            margin-top: 60px;
        }

        .text-center {
            text-align: center;
        }
    </style>
@endsection
