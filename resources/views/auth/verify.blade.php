@extends('layouts.app')

@section('title', 'Подтверждение email')

@section('content')
    <div class="container my-60">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap text-center" style="text-align: center;">Подтверждение email</h2>
            <div class="main-line" style="margin: 0 auto; margin-top: 10px;"></div>
        </div>

        <div class="admin-card my-40" style="max-width: 500px; margin: 40px auto;">
            <div class="text-center">
                <p class="about-us_text" style="text-align: center;">
                    На указанный email был отправлен код подтверждения.
                    <br><br>
                    <strong>Email: {{ $user->email ?? '' }}</strong>
                    <br><br>
                    Введите код ниже, чтобы подтвердить email и войти в аккаунт.
                </p>
            </div>

            @if (session('warning'))
                <div class="warning-message"
                    style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 10px; padding: 12px; margin-top: 20px; text-align: center; color: #856404;">
                    {{ session('warning') }}
                </div>
            @endif

            @if (session('error'))
                <div class="error-message"
                    style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 10px; padding: 12px; margin-top: 20px; text-align: center; color: #721c24;">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="success-message"
                    style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 10px; padding: 12px; margin-top: 20px; text-align: center; color: #155724;">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.verify', $userId) }}"
                class="flex-column gap-block-20px mt-40">
                @csrf

                <div class="width-100">
                    <input type="text" name="verification_code" class="input text-center"
                        placeholder="Введите 6-значный код" maxlength="6"
                        style="text-align: center; font-size: 24px; letter-spacing: 5px;" required>
                    @error('verification_code')
                        <p class="input-error-text text-center">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="red-button modal-button" style="width: 100%;">Подтвердить и войти</button>
            </form>

            <div class="text-center mt-40">
                <p class="reg-text">Не пришло письмо?</p>
                <form method="POST" action="{{ route('verification.resend', $userId) }}">
                    @csrf
                    <button type="submit" class="black-button form-button">
                        Отправить код повторно
                    </button>
                </form>
                <p class="reg-text mt-20">
                    <a href="{{ route('home') }}" style="color: #6366f1;">Вернуться на главную</a>
                </p>
            </div>
        </div>
    </div>

    <style>
        .text-center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mt-40 {
            margin-top: 40px;
        }

        .mt-60 {
            margin-top: 60px;
        }

        .warning-message,
        .error-message,
        .success-message {
            animation: fadeIn 0.5s ease;
        }

        .form-button {
            background: transparent !important;
            color: #6366f1;
            padding: 0;
            border: none;
            cursor: pointer;
            text-decoration: underline;
        }

        .form-button:hover {
            box-shadow: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection
