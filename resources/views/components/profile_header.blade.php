<div class="container user-block flex-space-between mobile-column" style="margin: 20px auto !important;">
    <div class="user-subblock gap-block-10px">
        <img class="user-img" src="{{ asset(Auth::user()->img) }}" alt="user">
        <h3 class="user-name">{{ Auth::user()->login }}</h3>
    </div>
    <div class="user-button-block gap-block-10px mobile-column">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <input class="black-button user-chosen-button" type="submit" value="Выйти">
        </form>
        <button onclick="userModal();" class="red-button user-chosen-button">Редактировать профиль</button>
    </div>
</div>

<!-- Navigation menu -->
<div class="container">
    <div class="admin-menu-links gap-block-20px flex-wrap mb-20">
        <a href="{{ route('user.added') }}"
            class="admin-link {{ request()->routeIs('user.added') ? 'admin-link-active' : '' }}">
            Публичные изображения
        </a>
        <a href="{{ route('user.private') }}"
            class="admin-link {{ request()->routeIs('user.private') ? 'admin-link-active' : '' }}">
            Приватные изображения
        </a>
        <a href="{{ route('user.albums.index') }}"
            class="admin-link {{ request()->routeIs('user.albums.*') ? 'admin-link-active' : '' }}">
            Альбомы
        </a>
        <a href="{{ route('user.favorites') }}"
            class="admin-link {{ request()->routeIs('user.favorites') ? 'admin-link-active' : '' }}">
            Избранное
        </a>
        <a href="{{ route('user.storage') }}"
            class="admin-link {{ request()->routeIs('user.storage') ? 'admin-link-active' : '' }}">
            Хранилище
        </a>
        <a href="{{ route('subscription.index') }}"
            class="admin-link {{ request()->routeIs('subscription.*') ? 'admin-link-active' : '' }}">
            Тарифы
        </a>
    </div>
</div>

<!-- User Profile Modal -->
<div class="modal-block flex-center user-modal-block none">
    <div onclick="userModal();" class="block-for-close"></div>
    <div class="modal-subblock flex-column user-modal-subblock gap-block-10px">
        <form onchange="formRegVerify();" class="modal-form gap-block-10px flex-column" enctype="multipart/form-data"
            method="POST" action="{{ route('user.profile.update') }}">
            @csrf
            <img src="{{ Auth::user()->img }}" alt="user" class="user-img user-img-modal">
            <div class="photo-modal-user-chose-block">
                <input onchange="userImgSwap();" type="file" class="user-modal-file" name="image"
                    id="userImgUpdate">
                <h3 class="user-modal-photo-chose">Выбрать фото</h3>
                <p id="img-error" class="input-error-text none">Неверный формат изображения или некорректный путь</p>
                @error('image')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="width-100">
                <input class="input" type="text" name="login" id="regLogin" maxlength="50"
                    value="{{ old('login', Auth::user()->login) }}" placeholder="Логин">
                <p id="reg-login-error" class="input-error-text none">Поле логин пусто</p>
                @error('login')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="width-100">
                <input class="input" type="password" name="password" id="regPassword" maxlength="40"
                    placeholder="Новый пароль">
                <p id="reg-password-error" class="input-error-text none">Поле пароль пусто</p>
                <p id="reg-password-length-error" class="input-error-text none">Пароль должен иметь больше 8 символов
                </p>
                @error('password')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="width-100">
                <input class="input" type="password" maxlength="40" id="passwordRepeat" name="password_confirmation"
                    placeholder="Подтвердите пароль">
                <p id="password-reapeat-error" class="input-error-text none">Пароли не совпадают</p>
            </div>

            <div class="flex-space-between gap-block-10px">
                <input class="red-button unactive-button top-modal-button modal-button" id="reg-button-form-submit"
                    type="button" disabled onclick="formSubmit(this);" value="Сохранить">
                <button type="button" onclick="confirmAccountDelete()" class="black-button top-modal-button modal-button" style="background: #f44336;">Удалить аккаунт</button>
            </div>
        </form>
        <button onclick="userModal();" class="black-button modal-button">Отменить</button>
    </div>
</div>

<form id="delete-account-form" method="POST" action="{{ route('user.account.delete') }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>