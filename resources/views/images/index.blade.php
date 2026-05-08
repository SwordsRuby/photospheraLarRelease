@extends('layouts.app')

@section('title', 'Изображения')

@section('content')
    <div class="flex-column container filter-form" style="margin: 30px auto;">
        <form action="{{ route('images.index') }}" class="flex-column" method="get">
            <div class="search-block gap-block-20px">
                <img class="search-img" src="{{ asset('img/main/search.svg') }}" alt="search">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input autocomplete="off" class="search-input" type="search" value="{{ request('search') }}"
                    placeholder="Поиск по названию, тегам или автору" name="search">
            </div>

            <div onclick="categoriesShow();" class="categories-filter-block gap-block-20px flex-wrap">
                <a href="{{ route('images.index', ['search' => request('search')]) }}" class="slider-card category-card">
                    <img src="{{ asset('img/main/photo-image.png') }}" alt="Всё" class="slider-img main-category-img">
                    <div class="slider-card-subblock">
                        <h2 class="slider-title">Всё</h2>
                    </div>
                </a>

                @foreach ($categories as $category)
                    <a href="{{ route('images.index', ['category' => $category->id, 'search' => request('search')]) }}"
                        class="slider-card category-card">
                        <img src="{{ $category->img }}" alt="{{ $category->name }}" class="slider-img main-category-img">
                        <div class="slider-card-subblock">
                            <h2 class="slider-title">{{ $category->name }}</h2>
                        </div>
                    </a>
                @endforeach
            </div>
        </form>
        <button onclick="categoriesShow();" class="categories-button">
            <h3 class="categories-button-title">Выбор категории</h3>
            <img class="categories-button-img" src="{{ asset('img/main/arrow-black.svg') }}" alt="arrow">
        </button>
    </div>

    <div class="gallery container flex-column gap-block-20px">
        @if ($images->count() > 0)
            <div class="gallery_block">
                @foreach ($images as $index => $image)
                    <a href="{{ route('images.show', $image->id) }}"
                        class="img-block block-img-gallery-{{ ($index % 12) + 1 }}">
                        <img class="gallery-img" src="{{ $image->display_img ?? $image->img }}" alt="{{ $image->name }}">

                        <div class="gallery-img-block">
                            <div class="gallery-img-subblock">
                                <form onsubmit="likeSubmit(this, event);" method="post"
                                    class="like-container like-form scale"
                                    data-url="{{ route('images.like', $image->id) }}">
                                    @csrf
                                    <input type="hidden" name="likeUserId" value="{{ Auth::id() ?? -1 }}">
                                    <input type="hidden" name="likeImgId" value="{{ $image->id }}">
                                    <button class="flex-center button-form-img">
                                        <h3
                                            class="like-numbers {{ $image->is_liked_by_user ? 'like-numbers-active' : '' }}">
                                            {{ $image->like_count }}
                                        </h3>
                                        <div
                                            class="gallery-like scale {{ $image->is_liked_by_user ? 'gallery-like-active' : '' }}">
                                        </div>
                                    </button>
                                </form>

                                <form onsubmit="bookmarkSubmit(this, event);" class="flex-center" method="post"
                                    data-url="{{ route('images.favorite', $image->id) }}">
                                    @csrf
                                    <input type="hidden" name="bookmarkUserId" value="{{ Auth::id() ?? -1 }}">
                                    <input type="hidden" name="bookmarkImgId" value="{{ $image->id }}">
                                    <button class="flex-center button-form-img">
                                        <div
                                            class="gallery-bookmark scale {{ $image->is_favorited_by_user ? 'gallery-bookmark-active' : '' }}">
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if ($image->is_private)
                            <div class="status-badge private" style="position: absolute; top: 10px; right: 10px;">
                                🔒 Приватное
                            </div>
                        @endif
                        @if (!$image->is_approved)
                            <div class="status-badge pending" style="position: absolute; top: 10px; left: 10px;">
                                ⏳ На модерации
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="no-img-block flex-center">
                <div class="no-img-content">
                    <div class="no-img-icon">📸</div>
                    <h2>Изображения не найдены</h2>
                    <p class="no-img-text">Попробуйте изменить параметры поиска или категорию</p>
                    <a href="{{ route('images.index') }}" class="red-button no-img-button">Сбросить фильтры</a>
                </div>
            </div>
        @endif
    </div>

    <style>
        /* No image content styles */
        .no-img-content {
            text-align: center;
            padding: 60px 20px;
        }

        .no-img-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-img-text {
            color: #888;
            margin: 10px 0 20px;
        }

        .no-img-button {
            display: inline-block;
            margin-top: 10px;
        }

        .status-badge {
            position: absolute;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 500;
            z-index: 5;
            backdrop-filter: blur(4px);
        }

        .status-badge.private {
            background: rgba(99, 102, 241, 0.9);
            color: white;
        }

        .status-badge.pending {
            background: rgba(255, 165, 0, 0.9);
            color: white;
        }
    </style>
@endsection
