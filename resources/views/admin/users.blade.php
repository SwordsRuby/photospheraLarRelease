@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
    <div class="admin-main">
        @include('admin.partials.menu')

        <div class="flex-column container filter-form" style="margin: 20px auto !important;">
            <form class="flex-column" method="get">
                <div class="search-block gap-block-20px">
                    <img class="search-img" src="{{ asset('img/main/search.svg') }}" alt="search">
                    <input class="search-input" autocomplete="off" type="search" name="search" placeholder="Поиск..."
                        value="{{ request('search') }}">
                </div>
            </form>
        </div>

        <div class="flex-column main-title-block container mb-20">
            <h2 class="main-title wrap">Управление пользователями</h2>
            <div class="main-line"></div>
        </div>

        @if ($users->count() > 0)
            <div class="gallery_block container mt-20">
                @foreach ($users as $user)
                    <div class="admin-card">
                        <img class="user-img" style="width: 100px; height: 100px; border-radius: 50%;"
                            src="{{ asset($user->img) }}" alt="{{ $user->login }}">
                        <div class="flex-column gap-block-5px">
                            <h3 class="admin-card-users-title">{{ $user->login }}</h3>
                            <p class="file-path-subblock-text">{{ $user->email }}</p>
                            <p class="file-path-subblock-text">Дата регистрации: {{ $user->created_at->format('d.m.Y') }}
                            </p>
                            <p class="file-path-subblock-text">Изображений: {{ $user->images()->count() }}</p>
                            @if ($user->is_banned)
                                <p class="file-path-subblock-text" style="color: #ff3366; font-size: 12px;">Причина бана:
                                    {{ $user->ban_reason }}</p>
                            @endif
                        </div>
                        @if (!$user->is_banned)
                            <div style="height: 100%; display:block;"></div>
                        @endif
                        <div class="flex-space-between gap-block-10px">
                            @if (!$user->is_banned)
                                <button onclick="showBanModal({{ $user->id }}, '{{ addslashes($user->login) }}')"
                                    class="red-button admin-img-button"
                                    style="background: #ff3366; width: 100%;">Забанить</button>
                            @else
                                <form method="POST" action="{{ route('admin.users.unban', $user->id) }}"
                                    style="width: 100%;">
                                    @csrf
                                    <button type="submit" class="red-button admin-img-button"
                                        style="background: #4CAF50; width: 100%;">Снять бан</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pagination">
                {{ $users->links() }}
            </div>
        @else
            <div class="no-img-block flex-center">
                <h2>Пользователи не найдены</h2>
            </div>
        @endif
    </div>

    <!-- Ban Modal -->
    <div style="z-index: 10;" class="modal-block flex-center ban-modal none" id="banModal">
        <div onclick="closeBanModal();" class="block-for-close"></div>
        <div class="modal-subblock flex-column gap-block-10px" style="width: 450px;">
            <h2 class="modal-block-title" style="color: #ff3366; text-align: center;">Блокировка пользователя</h2>
            <p class="message-title" id="banUserLogin" style="text-align: center;"></p>

            <form id="banForm" method="POST" class="width-100">
                @csrf
                <div class="width-100">
                    <label class="black-text" style="display: block; margin-bottom: 10px;">Причина блокировки</label>
                    <textarea name="ban_reason" id="ban_reason" class="input" rows="4" required
                        style="resize: vertical; width: 100%;">не соблюдение правил сообщества</textarea>
                    <p class="input-error-text" style="font-size: 11px; margin-top: 5px;">Максимум 100 символов</p>
                </div>

                <div class="flex-space-between gap-block-10px mt-20">
                    <button type="button" onclick="closeBanModal();" class="black-button modal-button">Отмена</button>
                    <button type="submit" class="red-button modal-button" style="background: #ff3366;">Забанить</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showBanModal(userId, userLogin) {
            const modal = document.getElementById('banModal');
            const form = document.getElementById('banForm');
            const userLoginSpan = document.getElementById('banUserLogin');

            userLoginSpan.innerHTML = `Вы уверены, что хотите заблокировать пользователя <strong>${userLogin}</strong>?`;
            form.action = `/admin/users/${userId}/ban`;

            modal.classList.remove('none');
        }

        function closeBanModal() {
            const modal = document.getElementById('banModal');
            modal.classList.add('none');
            document.getElementById('ban_reason').value = 'не соблюдение правил веб-сайта';
        }
    </script>

    <style>
        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .admin-card {
            text-align: center;
            width: 100%;
            max-width: 320px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            border-radius: var(--border-radius-lg);
            background: white;
            padding: 32px 24px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
        }

        .admin-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
    </style>
@endsection
