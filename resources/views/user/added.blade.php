@extends('layouts.app')

@section('title', 'Мои изображения')

@section('content')
    @include('components.profile_header')


    <div class="container flex-space-between mobile-column-reverse">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Публичные изображения</h2>
            <div class="main-line"></div>
            <p class="about-us_text mt-10">Эти изображения видны всем</p>
        </div>
        <div class="gap-block-10px mobile-column width-100-mobile">
            <a href="{{ route('user.albums.index') }}" class="black-button width-260 user-chosen-button">Мои альбомы</a>
            <a href="{{ route('images.create') }}" style="min-width: 210px;" class="red-button width-260 user-chosen-button">+
                Добавить
                изображение</a>
        </div>
    </div>

    <div class="my-40 gallery container flex-column gap-block-20px">
        @if ($images->count() > 0)
            <div class="gallery_block">
                @foreach ($images as $index => $image)
                    <a href="{{ route('images.edit', $image->id) }}"
                        class="img-block block-img-gallery-{{ ($index % 12) + 1 }}">
                        <img class="gallery-img" src="{{ $image->img }}" alt="{{ $image->name }}">

                        <div class="button-img-block">
                            <button class="red-button button-for-img">Редактировать</button>
                        </div>

                        @if (!$image->is_approved)
                            <div class="status-badge pending">
                                ⏳ На модерации
                            </div>
                        @endif

                        <div class="likes-count-badge">
                            ❤️ {{ $image->like_count }}
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="pagination">
                {{ $images->links() }}
            </div>
        @else
            <div class="no-img-block flex-center gap-block-10px mobile-column">
                <h2>Изображения не найдены</h2>
                <a href="{{ route('images.create') }}" class="red-button user-chosen-button mt-20">Добавить изображение</a>
            </div>
        @endif
    </div>
@endsection

<style>
    .status-badge {
        position: absolute;
        top: 10px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        z-index: 2;
        backdrop-filter: blur(4px);
        color: white;
    }

    .status-badge.pending {
        left: 10px;
        background: rgba(255, 165, 0, 0.9);
    }

    .status-badge.private {
        right: 10px;
        background: rgba(99, 102, 241, 0.9);
    }

    .likes-count-badge {
        position: absolute;
        bottom: 60px;
        right: 10px;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: bold;
        z-index: 2;
        backdrop-filter: blur(4px);
    }

    .admin-menu-links {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 20px;
    }

    .admin-link {
        color: var(--main-color-black);
        padding: 8px 16px;
        border-radius: 20px;
        transition: all 0.3s;
        font-size: 14px;
    }

    .admin-link:hover {
        color: var(--main-color-violet);
        background: rgba(99, 102, 241, 0.1);
    }

    .admin-link-active {
        color: var(--main-color-violet);
        background: rgba(99, 102, 241, 0.1);
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
    }

    .mb-20 {
        margin-bottom: 20px;
    }

    .mt-10 {
        margin-top: 10px;
    }

    .mt-20 {
        margin-top: 20px;
    }
</style>
