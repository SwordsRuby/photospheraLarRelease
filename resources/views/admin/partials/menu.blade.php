<div class="flex-space-between container admin-menu mobile-column" style="margin: 20px auto !important;">
    <div class="admin-menu-links">
        <a class="admin-link {{ request()->routeIs('admin.images') ? 'admin-link-active' : '' }}"
            href="{{ route('admin.images') }}">изображения</a>
        <a class="admin-link {{ request()->routeIs('admin.users') ? 'admin-link-active' : '' }}"
            href="{{ route('admin.users') }}">пользователи</a>
        <a class="admin-link {{ request()->routeIs('admin.moderators') ? 'admin-link-active' : '' }}"
            href="{{ route('admin.moderators') }}">модераторы</a>
        <a class="admin-link {{ request()->routeIs('admin.categories') ? 'admin-link-active' : '' }}"
            href="{{ route('admin.categories') }}">категории</a>
        <a class="admin-link {{ request()->routeIs('admin.tags') ? 'admin-link-active' : '' }}"
            href="{{ route('admin.tags') }}">тэги</a>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <input class="black-button user-chosen-button" type="submit" value="Выйти">
    </form>
</div>
