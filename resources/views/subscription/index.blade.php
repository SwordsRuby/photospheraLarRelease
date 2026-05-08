@extends('layouts.app')

@section('title', 'Моя подписка')

@section('content')
    @include('components.profile_header')

    <div class="container my-40">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Моя подписка</h2>
            <div class="main-line"></div>
        </div>

        <div class="admin-card my-40" style="max-width: 100%;">
            <div class="flex-space-between mobile-column">
                <div>
                    <h3 class="admin-card-title">{{ $subscription->plan_name }}</h3>
                    <p class="mt-10">Использовано: {{ round($subscription->storage_used / 1073741824, 2) }} ГБ /
                        {{ round($subscription->storage_limit / 1073741824, 1) }} ГБ</p>
                    <div class="progress-bar mt-10"
                        style="width: 300px; height: 10px; background: #e0e0e0; border-radius: 5px;">
                        <div class="progress-fill"
                            style="width: {{ $subscription->storage_used_percentage }}%; height: 100%; background: #6366f1; border-radius: 5px;">
                        </div>
                    </div>
                </div>
                <div>
                    @if ($subscription->plan !== 'basic')
                        @if ($subscription->expires_at)
                            <p>Действует до: <strong>{{ $subscription->expires_at->format('d.m.Y') }}</strong></p>
                        @endif
                        <!-- Cancel subscription button - opens modal -->
                        <button onclick="showCancelSubscriptionModal()" class="black-button mt-20"
                            style="background: #ff3366;">Отменить подписку</button>
                    @else
                        <a href="{{ route('subscription.plans') }}" class="red-button">Улучшить тариф</a>
                    @endif
                </div>
            </div>
        </div>

        @if ($payments->count() > 0)
            <div class="flex-column main-title-block mt-60">
                <h2 class="main-title wrap">История платежей</h2>
                <div class="main-line"></div>
            </div>

            <div class="tablet-2-col gap-block-10px mt-40" style="flex-wrap:wrap;">
                @foreach ($payments as $payment)
                    <div class="admin-card" style="padding: 15px; min-height: 170px;">
                        <div class="flex-space-between">
                            <span>{{ $payment->created_at->format('d.m.Y') }}</span>
                            <span class="admin-card-title">{{ number_format($payment->amount, 2) }} ₽</span>
                        </div>
                        <div class="flex-space-between mt-10">
                            <span>Тариф: {{ ucfirst($payment->plan) }}</span>
                            <span class="{{ $payment->status === 'succeeded' ? 'green-text' : 'red-text' }}">
                                {{ $payment->status === 'succeeded' ? 'Оплачено' : ($payment->status === 'pending' ? 'Ожидает оплаты' : 'Ошибка') }}
                            </span>
                        </div>
                        @if ($payment->status === 'pending')
                            <div class="mt-10">
                                <a href="{{ route('subscription.check.payment', $payment->id) }}" class="red-button"
                                    style="padding: 5px 10px; font-size: 12px;">
                                    Проверить статус
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="pagination mt-40">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Modal window for subscription cancellation -->
    <div class="modal-block flex-center cancel-subscription-modal none" id="cancelSubscriptionModal">
        <div onclick="closeCancelSubscriptionModal();" class="block-for-close"></div>
        <div class="modal-subblock flex-column del-subblock gap-block-10px">
            <h2 class="modal-block-title" style="color: #ff3366; text-align: center;">Отмена подписки</h2>
            <p class="message-title" style="text-align: center;">
                Вы уверены, что хотите отменить подписку "{{ $subscription->plan_name }}"?
                <br><br>
                После отмены вы перейдете на <strong>Базовый тариф</strong> (1 ГБ хранилища).
                <br>
                Все ваши изображения останутся в сохранности, но вы не сможете загружать новые, если превысите лимит.
            </p>
            <div class="flex-space-between gap-block-10px">
                <button onclick="closeCancelSubscriptionModal();" class="black-button modal-button">Отмена</button>
                <form id="cancelSubscriptionForm" method="POST" action="{{ route('subscription.cancel') }}">
                    @csrf
                    <button type="submit" class="red-button modal-button" style="background: #ff3366; min-width: 150px;">Отменить
                        подписку</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .progress-bar {
            background: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-fill {
            background: #6366f1;
            transition: width 0.3s;
        }
        .green-text {
            color: #4CAF50;
        }
        .red-text {
            color: #f44336;
        }
        .mt-10 { margin-top: 10px; }
        .mt-20 { margin-top: 20px; }
        .mt-40 { margin-top: 40px; }
        .mt-60 { margin-top: 60px; }
    </style>
@endsection