@extends('layouts.app')

@section('title', 'Админ панель - Модераторы')

@section('content')
    <div class="admin-main">
        @include('admin.partials.menu')

        <div class="flex-column container filter-form" style="margin: 20px auto !important;">
            <form class="flex-column" method="get">
                <div class="search-block gap-block-20px">
                    <img class="search-img" src="{{ asset('img/main/search.svg') }}" alt="search">
                    <input value="{{ request('search') }}" autocomplete="off" class="search-input" type="search"
                        placeholder="Поиск по логину" name="search">
                </div>
            </form>
        </div>

        <!-- Flash messages -->
        @if(session('success'))
            <div class="container my-20">
                <div class="success-message" style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 10px; padding: 15px; color: #155724;">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container my-20">
                <div class="error-message" style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 10px; padding: 15px; color: #721c24;">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="container my-20">
                <div class="info-message" style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 10px; padding: 15px; color: #0c5460;">
                    {{ session('info') }}
                </div>
            </div>
        @endif

        <!-- Delete confirmation modal -->
        <div class="modal-block flex-center del-block none">
            <div class="block-for-close"></div>
            <div class="modal-subblock flex-column del-subblock gap-block-10px">
                <form class="modal-form gap-block-10px flex-column" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <h2 class="modal-block-title">Удалить модератора?</h2>
                    <p class="text-center">Это действие нельзя отменить.</p>
                    <div class="flex-space-between gap-block-10px">
                        <input class="red-button modal-button" type="submit" value="Удалить">
                        <input type="reset" value="Отменить" onclick="del();" class="black-button modal-button">
                    </div>
                </form>
            </div>
        </div>

        <!-- Email verification modal for moderator -->
        <div class="modal-block flex-center verify-modal none" id="verifyModal">
            <div onclick="closeVerifyModal();" class="block-for-close"></div>
            <div class="modal-subblock flex-column gap-block-10px" style="width: 350px;">
                <h2 class="modal-block-title" style="text-align: center;">Подтверждение почты</h2>
                <p class="text-center" id="verifyModeratorInfo"></p>
                <form id="verifyForm" method="POST">
                    @csrf
                    <input type="hidden" name="moderator_id" id="verifyModeratorId">
                    <div class="width-100">
                        <input type="text" name="verification_code" class="input text-center" 
                               placeholder="Введите 6-значный код" maxlength="6" 
                               style="text-align: center; font-size: 24px; letter-spacing: 5px;" required>
                    </div>
                    <div class="flex-space-between gap-block-10px mt-20">
                        <button type="button" onclick="closeVerifyModal();" class="black-button modal-button">Отмена</button>
                        <button type="submit" class="red-button modal-button">Подтвердить</button>
                    </div>
                </form>
                <div class="text-center mt-20">
                    <form id="resendForm" method="POST">
                        @csrf
                        <button type="submit" class="resend-link">Отправить код повторно</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="container flex-column flex-space-between">
            <form method="POST"
                class="add-img-form-block flex-space-between mobile-column tablet-column flex-column tablet-gap desktop-gap"
                action="{{ route('admin.moderators.store') }}">
                @csrf
                <div class="flex-column main-title-block">
                    <h2 class="main-title wrap">Добавление модератора</h2>
                    <div class="main-line"></div>
                </div>

                <div class="tablet-2-col desktop-2-col width-100 gap-block-20px mobile-column flex-column">
                    <div class="width-100">
                        <input class="input" type="text" name="login" id="regLogin" maxlength="50"
                            placeholder="Логин" value="{{ old('login') }}"
                            oninput="formRegVerify();" onchange="formRegVerify();">
                        <p id="reg-login-error" class="input-error-text none">Поле логин пусто</p>
                        @error('login')
                            <p class="input-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="width-100">
                        <input class="input" type="email" name="email" id="regEmail" maxlength="255"
                            placeholder="Почта" value="{{ old('email') }}"
                            oninput="formRegVerify();" onchange="formRegVerify();">
                        <p id="reg-email-error" class="input-error-text none">Введите корректную почту</p>
                        @error('email')
                            <p class="input-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="width-100">
                        <input class="input" type="password" name="password" id="regPassword" maxlength="40"
                            placeholder="Пароль" oninput="formRegVerify();" onchange="formRegVerify();">
                        <p id="reg-password-error" class="input-error-text none">Поле пароль пусто</p>
                        <p id="reg-password-length-error" class="input-error-text none">Пароль должен иметь больше 8 символов</p>
                        @error('password')
                            <p class="input-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="width-100">
                        <input class="input" type="password" maxlength="40" id="passwordRepeat"
                            name="password_confirmation" placeholder="Подтвердите пароль"
                            oninput="formRegVerify();" onchange="formRegVerify();">
                        <p id="password-reapeat-error" class="input-error-text none">Пароли не совпадают</p>
                    </div>

                    <div class="add-img-info-buttons my-0 width-100">
                        <input class="red-button flex-center unactive-button button-width admin-img-button width-100"
                            disabled id="reg-button-form-submit" value="Добавить модератора" type="button"
                            onclick="submitModeratorForm();">
                    </div>
                </div>
            </form>

            <div class="admin-categories-tag-catalog gap-block-20px flex-column my-40 container">
                <div class="flex-column main-title-block">
                    <h2 class="main-title wrap">Список модераторов</h2>
                    <div class="main-line"></div>
                </div>

                <div class="categ-admin-block width-100 gap-block-10px flex-wrap">
                    @forelse ($moderators as $moderator)
                        <div id="moderator-{{ $moderator->id }}" class="admin-card" style="position: relative;">
                            <img src="{{ $moderator->img }}" alt="{{ $moderator->login }}" class="admin-user-img"
                                style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                            
                            <h2 class="admin-card-users-title">{{ $moderator->login }}</h2>
                            
                            <p class="moderator-email" style="font-size: 12px; color: #666;">Почта: {{ $moderator->email }}</p>
                            
                            <div class="email-status" style="margin: 10px 0;">
                                @if($moderator->hasVerifiedEmail())
                                    <span class="status-badge verified">✅ Почта подтверждена</span>
                                @else
                                    <span class="status-badge unverified">⚠️ Почта не подтверждена</span>
                                    <button type="button" 
                                            onclick="showVerifyModal({{ $moderator->id }}, '{{ $moderator->login }}', '{{ $moderator->email }}')" 
                                            class="verify-btn" style="margin-left: 10px; padding: 5px 10px; font-size: 12px;">
                                        Подтвердить
                                    </button>
                                @endif
                            </div>
                            
                            <div class="flex-space-between gap-block-10px width-100">
                                @if(Auth::id() !== $moderator->id)
                                    <button
                                        onclick="setDeleteUrl('{{ route('admin.moderators.destroy', $moderator->id) }}'); del();"
                                        class="red-button flex-center width-100 admin-img-button">Удалить</button>
                                @else
                                    <span class="current-user-badge">Текущий администратор</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="no-img-block flex-center">
                            <h2>Модераторы не найдены</h2>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Store current moderator ID for verification modal
    let currentModeratorId = null;

    function submitModeratorForm() {
        const form = document.querySelector('form[action="{{ route('admin.moderators.store') }}"]');
        if (form && formRegVerifyForSubmit()) {
            form.submit();
        }
    }

    function formRegVerifyForSubmit() {
        let flagReg = true;

        const login = document.querySelector('#regLogin');
        if (login && login.value.trim() === '') {
            document.querySelector('#reg-login-error')?.classList.remove('none');
            flagReg = false;
        } else {
            document.querySelector('#reg-login-error')?.classList.add('none');
        }

        const email = document.querySelector('#regEmail');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && email.value.trim() === '') {
            const error = document.querySelector('#reg-email-error');
            if (error) {
                error.textContent = 'Поле почта пусто';
                error.classList.remove('none');
            }
            flagReg = false;
        } else if (email && !emailPattern.test(email.value.trim())) {
            const error = document.querySelector('#reg-email-error');
            if (error) {
                error.textContent = 'Введите корректную почту';
                error.classList.remove('none');
            }
            flagReg = false;
        } else {
            document.querySelector('#reg-email-error')?.classList.add('none');
        }

        const password = document.querySelector('#regPassword');
        if (password && password.value.trim() === '') {
            document.querySelector('#reg-password-error')?.classList.remove('none');
            flagReg = false;
        } else {
            document.querySelector('#reg-password-error')?.classList.add('none');
            if (password && password.value.trim().length < 8) {
                document.querySelector('#reg-password-length-error')?.classList.remove('none');
                flagReg = false;
            } else {
                document.querySelector('#reg-password-length-error')?.classList.add('none');
            }
        }

        const passwordRepeat = document.querySelector('#passwordRepeat');
        if (password && passwordRepeat && password.value.trim() !== passwordRepeat.value.trim()) {
            document.querySelector('#password-reapeat-error')?.classList.remove('none');
            flagReg = false;
        } else {
            document.querySelector('#password-reapeat-error')?.classList.add('none');
        }

        return flagReg;
    }

    function formRegVerify() {
        let flagReg = true;

        const login = document.querySelector('#regLogin');
        if (login && login.value.trim() === '') {
            document.querySelector('#reg-login-error')?.classList.remove('none');
            flagReg = false;
        } else {
            document.querySelector('#reg-login-error')?.classList.add('none');
        }

        const email = document.querySelector('#regEmail');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && email.value.trim() === '') {
            const error = document.querySelector('#reg-email-error');
            if (error) {
                error.textContent = 'Поле почта пусто';
                error.classList.remove('none');
            }
            flagReg = false;
        } else if (email && !emailPattern.test(email.value.trim())) {
            const error = document.querySelector('#reg-email-error');
            if (error) {
                error.textContent = 'Введите корректную почту';
                error.classList.remove('none');
            }
            flagReg = false;
        } else {
            document.querySelector('#reg-email-error')?.classList.add('none');
        }

        const password = document.querySelector('#regPassword');
        if (password && password.value.trim() === '') {
            document.querySelector('#reg-password-error')?.classList.remove('none');
            flagReg = false;
        } else {
            document.querySelector('#reg-password-error')?.classList.add('none');
            if (password && password.value.trim().length < 8) {
                document.querySelector('#reg-password-length-error')?.classList.remove('none');
                flagReg = false;
            } else {
                document.querySelector('#reg-password-length-error')?.classList.add('none');
            }
        }

        const passwordRepeat = document.querySelector('#passwordRepeat');
        if (password && passwordRepeat && password.value.trim() !== passwordRepeat.value.trim()) {
            document.querySelector('#password-reapeat-error')?.classList.remove('none');
            flagReg = false;
        } else {
            document.querySelector('#password-reapeat-error')?.classList.add('none');
        }

        const submitBtn = document.querySelector('#reg-button-form-submit');
        if (submitBtn) {
            if (flagReg) {
                submitBtn.removeAttribute('disabled');
                submitBtn.classList.remove('unactive-button');
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.classList.add('unactive-button');
            }
        }
    }

    function showVerifyModal(userId, login, email) {
        currentModeratorId = userId;
        document.getElementById('verifyModeratorId').value = userId;
        document.getElementById('verifyModeratorInfo').innerHTML = `Пользователь: <strong>${login}</strong><br>Почта: <strong>${email}</strong>`;
        
        const verifyForm = document.getElementById('verifyForm');
        verifyForm.action = `/admin/moderators/${userId}/verify`;
        
        const resendForm = document.getElementById('resendForm');
        resendForm.action = `/admin/moderators/${userId}/resend-code`;
        
        document.getElementById('verifyModal').classList.remove('none');
    }

    function closeVerifyModal() {
        document.getElementById('verifyModal').classList.add('none');
        currentModeratorId = null;
    }

    /**
     * Initialize form validation on page load
     */
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof formRegVerify === 'function') {
            formRegVerify();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    .status-badge.verified {
        background: #d4edda;
        color: #155724;
    }
    .status-badge.unverified {
        background: #fff3cd;
        color: #856404;
    }
    
    .verify-btn {
        background: #6366f1;
        color: white;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .verify-btn:hover {
        background: #4f52e0;
        transform: scale(1.02);
    }
    
    .current-user-badge {
        background: #e0e0e0;
        color: #666;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 13px;
        width: 100%;
        text-align: center;
    }
    
    .moderator-email {
        word-break: break-all;
        text-align: center;
    }
    
    .resend-link {
        background: none;
        border: none;
        color: #6366f1;
        cursor: pointer;
        text-decoration: underline;
        font-size: 13px;
    }
    .resend-link:hover {
        color: #4f52e0;
    }
    
    .text-center {
        text-align: center;
    }
    .mt-20 {
        margin-top: 20px;
    }
    .my-20 {
        margin-top: 20px;
        margin-bottom: 20px;
    }
    
    <!-- Animation for flash messages -->
    .success-message, .error-message, .info-message {
        animation: fadeIn 0.5s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush