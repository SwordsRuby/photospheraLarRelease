@php
    $segments = request()->segments();
    $isHome = request()->path() === '/';
    $headerClass = $isHome ? 'transparent-block' : 'black-block';
@endphp

<div class="{{ $headerClass }} header-block">
    <header class="container flex-space-between">
        <a href="/">
            <img style="height: 40px;" src="{{ asset('img/logo/png/logo.png') }}" alt="logo" class="logo scale">
        </a>

        @if (!Auth::check() || !session('headerFlag'))
            <nav class="gap-block-20px nav-none">
                <a title="Добавить изображение" class="scale" href="{{ Auth::check() ? route('images.create') : '#' }}"
                    onclick="{{ !Auth::check() ? 'auth(); return false;' : '' }}">
                    <img src="{{ asset('img/header/add-image.svg') }}" alt="add-image">
                </a>
                <a title="Изображения" class="scale" href="{{ route('images.index') }}">
                    <img src="{{ asset('img/header/see-image.svg') }}" alt="see-image">
                </a>
                <a title="Главная" class="scale" href="/">
                    <img src="{{ asset('img/header/home.svg') }}" alt="home">
                </a>

                @if (Auth::check())
                    <a title="{{ Auth::user()->login }}" class="scale" href="{{ route('user.favorites') }}">
                        <img class="user-img-header" src="{{ asset(Auth::user()->img) }}" alt="user">
                    </a>
                @else
                    <a onclick="auth();" class="scale" title="Авторизация" href="#">
                        <img src="{{ asset('img/header/user.svg') }}" alt="user">
                    </a>
                @endif
            </nav>

            <div onclick="burgerMenu();" class="burger-menu">
                <div class="menu-line"></div>
                <div class="menu-line"></div>
                <div class="menu-line menu-line-short"></div>
            </div>
        @endif
    </header>
</div>

@if (!Auth::check() || !session('headerFlag'))
    <!-- Burger menu -->
    <div class="burger burger-none">
        <div class="container burger-block burger-none">
            <div class="flex-space-between">
                <a href="/">
                    <img src="{{ asset('img/logo/png/logo.png') }}" alt="" class="logo scale"
                        style="height: 40px;">
                </a>
                <div onclick="burgerMenu();" class="burger-menu">
                    <div class="menu-line"></div>
                    <div class="menu-line"></div>
                    <div class="menu-line menu-line-short"></div>
                </div>
            </div>

            <a class="gap-block-10px burger-link" href="{{ Auth::check() ? route('images.create') : '#' }}"
                onclick="{{ !Auth::check() ? 'auth(); burgerMenu(); return false;' : 'burgerMenu();' }}">
                <img src="{{ asset('img/header/add-image.svg') }}" alt="add-image">
                Добавить изображение
            </a>
            <a class="gap-block-10px burger-link" onclick="burgerMenu();" href="{{ route('images.index') }}">
                <img src="{{ asset('img/header/see-image.svg') }}" alt="see-image">
                Изображения
            </a>
            <a class="gap-block-10px burger-link" onclick="burgerMenu();" href="/">
                <img src="{{ asset('img/header/home.svg') }}" alt="home">
                Главная
            </a>

            @if (Auth::check())
                <a class="gap-block-10px burger-link" onclick="burgerMenu();" href="{{ route('user.favorites') }}">
                    <img src="{{ asset(Auth::user()->img) }}" class="user-img-header" alt="user">
                    {{ Auth::user()->login }}
                </a>
            @else
                <a class="gap-block-10px burger-link" onclick="burgerMenu(); auth();" href="#">
                    <img src="{{ asset('img/header/user.svg') }}" alt="user">
                    Авторизация
                </a>
            @endif
        </div>
    </div>
@endif

@if (!Auth::check())
    <!-- Authorization Modal -->
    <div class="modal-block flex-center auth-block auth-reg-none">
        <div onclick="closeAuthModal();" class="block-for-close"></div>
        <div class="modal-subblock flex-column auth-subblock gap-block-10px">
            <div class="modal-header">
                <div class="modal-icon">🔐</div>
                <h2 class="modal-block-title">Добро пожаловать</h2>
                <p class="modal-subtitle">Войдите в свой аккаунт</p>
            </div>
            <form onchange="formAuthVerify();" class="modal-form gap-block-10px flex-column" method="POST"
                action="{{ route('login') }}" id="loginForm">
                @csrf
                <div class="width-100">
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input class="input" type="text" name="loginAuth" id="authLogin" maxlength="20"
                            placeholder="Логин" value="{{ old('loginAuth') }}">
                    </div>
                    <p id="login-error" class="input-error-text none">Поле логин пусто</p>
                    @error('loginAuth')
                        <p class="input-error-text" style="display: block;">{{ $message }}</p>
                    @enderror
                </div>
                <div class="width-100">
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input class="input" type="password" id="authPassword" maxlength="40" name="passwordAuth"
                            placeholder="Пароль">
                    </div>
                    <p id="password-error" class="input-error-text none">Поле пароль пусто</p>
                    @error('passwordAuth')
                        <p class="input-error-text" style="display: block;">{{ $message }}</p>
                    @enderror
                </div>
                <input class="red-button top-modal-button modal-button" type="button" id="button-form-submit"
                    disabled onclick="formSubmit(this);" value="Войти">
            </form>
            <div class="modal-divider">
                <span>Нет аккаунта?</span>
            </div>
            <button onclick="reg(); closeAuthModal();" class="black-button modal-button">Регистрация</button>
        </div>
    </div>

    <!-- Registration Modal -->
    <div class="modal-block flex-center reg-block auth-reg-none">
        <div onclick="closeRegModal();" class="block-for-close"></div>
        <div class="modal-subblock flex-column reg-subblock gap-block-10px">
            <div class="modal-header">
                <div class="modal-icon">✨</div>
                <h2 class="modal-block-title">Создать аккаунт</h2>
                <p class="modal-subtitle">Присоединяйтесь к сообществу</p>
            </div>
            <form onchange="formRegVerify()" class="modal-form gap-block-10px flex-column" method="POST"
                action="{{ route('register') }}" id="registerForm">
                @csrf
                <h2 class="modal-block-title">Регистрация</h2>
                @if (session('showAuthModal'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            auth();
                        });
                    </script>
                @endif

                @if (session('showRegModal'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            reg();
                        });
                    </script>
                @endif

                <div class="width-100">
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input class="input" type="text" name="loginRegister" id="regLogin" maxlength="20"
                            placeholder="Логин" value="{{ old('loginRegister') }}">
                    </div>
                    <p id="reg-login-error" class="input-error-text none">Поле логин пусто</p>
                    @error('loginRegister')
                        <p class="input-error-text" style="display: block;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="width-100">
                    <div class="input-wrapper">
                        <span class="input-icon">📧</span>
                        <input class="input" type="email" name="emailRegister" id="regEmail" maxlength="255"
                            placeholder="Почта" value="{{ old('emailRegister') }}">
                    </div>
                    <p id="reg-email-error" class="input-error-text none">Введите корректную почту</p>
                    @error('emailRegister')
                        <p class="input-error-text" style="display: block;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="width-100">
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input class="input" type="password" name="passwordRegister" id="regPassword"
                            maxlength="40" placeholder="Пароль">
                    </div>
                    <p id="reg-password-error" class="input-error-text none">Поле пароль пусто</p>
                    <p id="reg-password-length-error" class="input-error-text none">Пароль должен иметь больше 8
                        символов</p>
                    @error('passwordRegister')
                        <p class="input-error-text" style="display: block;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="width-100">
                    <div class="input-wrapper">
                        <span class="input-icon">✓</span>
                        <input class="input" type="password" maxlength="40" id="passwordRepeat"
                            name="passwordRegister_confirmation" placeholder="Подтвердите пароль">
                    </div>
                    <p id="password-reapeat-error" class="input-error-text none">Пароли не совпадают</p>
                </div>
                @if (
                    $errors->has('loginRegister') ||
                        $errors->has('emailRegister') ||
                        $errors->has('passwordRegister') ||
                        session('showRegModal'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            reg();
                        });
                    </script>
                @endif

                @if ($errors->has('loginAuth') || $errors->has('passwordAuth') || session('showAuthModal'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            auth();
                        });
                    </script>
                @endif

                <p class="reg-text">
                    Я принимаю условия
                    <a class="reg-link" href="{{ asset('docs/termsOfUse.pdf') }}" target="_blank">пользовательского
                        соглашения</a>
                    и
                    <a class="reg-link" href="{{ asset('docs/privacyPolicy.pdf') }}" target="_blank">политику
                        конфиденциальности</a>
                </p>
                <input class="red-button top-modal-button modal-button" id="reg-button-form-submit" type="button"
                    disabled onclick="submitRegisterForm();" value="Зарегистрироваться">
            </form>
        </div>
    </div>
@endif

<!-- Message Modal -->
<div class="modal-block flex-center message-block none">
    <div onclick="showMessage();" class="block-for-close"></div>
    <div class="modal-subblock flex-column message-subblock gap-block-10px">
        <h2 class="modal-block-title message-title"></h2>
        <button onclick="showMessage();" class="red-button modal-button">OK</button>
    </div>
</div>

<style>
    .modal-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .modal-icon {
        font-size: 48px;
        margin-bottom: 10px;
    }

    .modal-subtitle {
        font-size: 13px;
        color: #888;
        margin-top: -5px;
    }

    .modal-divider {
        text-align: center;
        margin: 15px 0 10px;
        position: relative;
    }

    .modal-divider::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 100%;
        height: 1px;
        background: #e0e0e0;
    }

    .modal-divider span {
        background: white;
        padding: 0 15px;
        position: relative;
        font-size: 12px;
        color: #888;
    }

    .input-wrapper {
        position: relative;
        width: 100%;
    }

    .input-wrapper .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #aaa;
        z-index: 1;
    }

    .input-wrapper .input {
        padding-left: 45px;
    }

    .modal-block .input-error-text {
        margin-left: 10px;
        margin-top: 5px;
        font-size: 11px;
        color: var(--main-color-violet);
        width: 100%;
        display: block;
    }

    .modal-block .input-error-text.none {
        display: none;
    }

    .input-error-text:not(.none) {
        display: block !important;
    }

    .server-error-text {
        background: rgba(255, 51, 102, 0.1);
        border: 1px solid #ff3366;
        border-radius: 10px;
        padding: 10px;
        color: #ff3366;
        font-size: 13px;
        text-align: center;
    }

    /* Auth/Reg modals */
    .auth-subblock,
    .reg-subblock {
        max-width: 400px;
        width: 90%;
    }

    .reg-subblock {
        max-height: 90vh;
        overflow-y: auto;
    }

    /* Custom scrollbar for reg modal */
    .reg-subblock::-webkit-scrollbar {
        width: 4px;
    }

    .reg-subblock::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 4px;
    }

    .reg-subblock::-webkit-scrollbar-thumb {
        background: var(--main-color-violet);
        border-radius: 4px;
    }
</style>

<script>
    function checkAndOpenAuthModal() {
        @if (session('showAuthModal'))
            console.log('Opening auth modal from session flag');
            auth();
            return true;
        @endif

        @if ($errors->has('loginAuth') || $errors->has('passwordAuth'))
            console.log('Opening auth modal from validation errors');
            auth();
            return true;
        @endif

        return false;
    }

    // Auto open modals on errors
    function checkAndOpenRegModal() {
        @if (session('showRegModal'))
            console.log('Opening reg modal from session flag');
            reg();
            return true;
        @endif

        @if ($errors->has('loginRegister') || $errors->has('emailRegister') || $errors->has('passwordRegister'))
            console.log('Opening reg modal from validation errors');
            reg();
            return true;
        @endif

        setTimeout(function() {
            const regErrorElements = document.querySelectorAll('.reg-block .input-error-text');
            let hasVisibleError = false;
            regErrorElements.forEach(function(el) {
                if (el.textContent.trim() !== '' && !el.classList.contains('none')) {
                    hasVisibleError = true;
                }
            });
            if (hasVisibleError) {
                console.log('Opening reg modal from visible errors');
                reg();
            }
        }, 100);

        return false;
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkAndOpenRegModal();
        checkAndOpenAuthModal();
    });
</script>
