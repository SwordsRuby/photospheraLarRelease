@extends('layouts.app')

@section('title', "Изображение - $image->name" )

@section('content')
    <div class="container one-img-container">
        <div class="one-img-block">
            <div class="one-img">
                <img class="gallery-img" src="{{ $displayImage ?? $image->img }}" alt="{{ $image->name }}">

                <div class="button-img-block">
                    @auth
                        <a href="{{ route('images.download', $image->id) }}" class="red-button button-for-img flex-center"
                            style="min-width: 140px;">
                            📥 Скачать
                        </a>
                    @else
                        <button type="button" onclick="auth();" class="red-button button-for-img flex-center download-button">
                            🔒 Войдите чтобы скачать
                        </button>
                    @endauth

                    <div class="one-img-subblock">
                        <form onsubmit="likeSubmit(this, event);" method="post" class="like-container like-form scale"
                            data-url="{{ route('images.like', $image->id) }}">
                            @csrf
                            <input type="hidden" name="likeUserId" value="{{ Auth::id() ?? -1 }}">
                            <input type="hidden" name="likeImgId" value="{{ $image->id }}">
                            <button class="flex-center button-form-img">
                                <h3
                                    class="like-numbers none-invert {{ $image->is_liked_by_user ? 'like-numbers-active' : '' }}">
                                    {{ $image->like_count }}
                                </h3>
                                <div
                                    class="gallery-like none-invert scale {{ $image->is_liked_by_user ? 'gallery-like-active' : '' }}">
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
                                    class="gallery-bookmark none-invert scale {{ $image->is_favorited_by_user ? 'gallery-bookmark-active' : '' }}">
                                </div>
                            </button>
                        </form>
                    </div>
                </div>

                @if ($image->is_private)
                    <div class="status-badge private-image">🔒 Приватное изображение</div>
                @endif
                @if (!$image->is_approved)
                    <div class="status-badge pending-image">⏳ На модерации</div>
                @endif
            </div>

            <div class="one-img-info-block">
                <h2 class="one-img-title">{{ $image->name }}</h2>
                <div class="flex-space-between tablet-column one-img-info-subblock">
                    <div class="user-subblock gap-block-10px">
                        <img class="user-img user-img-one" src="{{ $image->author->img }}" alt="user">
                        <div>
                            <h3 class="user-name one-img-user-title">{{ $image->author->login }}</h3>
                            <p class="upload-date">{{ $image->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('images.index', ['category' => $image->category_id]) }}"
                        class="red-button user-chosen-button one-img-category-button">
                        📁 {{ $image->category->name }}
                    </a>
                </div>

                @if ($image->tags->count() > 0)
                    <div class="tag-block gap-block-10px flex-column">
                        <h2 class="tag-title">🏷️ Тэги:</h2>
                        <div class="tag-subblock gap-block-10px">
                            @foreach ($image->tags as $tag)
                                <a href="{{ route('images.index', ['search' => $tag->title]) }}"
                                    class="tag-text">#{{ $tag->title }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (Auth::check() && Auth::id() === $image->user_id)
                    <div class="image-actions">
                        <a href="{{ route('images.edit', $image->id) }}" class="black-button edit-btn">
                            ✏️ Редактировать
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .one-img-container {
            padding: 40px 24px;
        }

        .one-img-block {
            min-height: 500px;
            width: 100%;
            background: linear-gradient(135deg, #f5f5ff, #ffffff);
            border-radius: 32px;
            display: flex;
            gap: 32px;
            padding: 24px;
            box-shadow: var(--shadow-md);
        }

        .download-button {
            min-width: 250px;
        }

        .one-img {
            position: relative;
            flex: 1.5;
            border-radius: 24px;
            overflow: hidden;
        }

        .one-img .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            max-height: 550px;
        }

        .one-img-info-block {
            flex: 1;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .one-img-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--main-color-black);
        }

        .upload-date {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }

        .user-img-one {
            width: 60px;
            height: 60px;
            border: 3px solid var(--main-color-violet);
        }

        .tag-title {
            font-size: 14px;
            font-weight: 600;
            color: #555;
        }

        .tag-subblock {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tag-text {
            background: #f0f0f0;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            transition: var(--transition-smooth);
        }

        .tag-text:hover {
            background: var(--main-color-violet);
            color: white;
        }

        .image-actions {
            margin-top: auto;
            display: flex;
            gap: 12px;
        }

        .edit-btn {
            padding: 12px 24px;
            width: 100%;
            text-align: center;
        }

        .status-badge.private-image,
        .status-badge.pending-image {
            position: absolute;
            top: 16px;
            left: 16px;
            padding: 8px 16px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 500;
            z-index: 5;
            backdrop-filter: blur(8px);
        }

        .status-badge.private-image {
            background: rgba(99, 102, 241, 0.95);
            color: white;
        }

        .status-badge.pending-image {
            background: rgba(255, 165, 0, 0.95);
            color: white;
        }

        .button-img-block {
            height: 60px;
            position: absolute;
            bottom: 0;
            left: 0;
            border-radius: 0 0 24px 24px;
            width: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
        }

        .button-for-img {
            height: 40px !important;
            width: 120px;
            font-size: 14px;
            border-radius: 30px;
        }

        .one-img-subblock {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        @media (max-width: 992px) {
            .one-img-block {
                flex-direction: column;
            }

            .one-img {
                flex: auto;
                min-height: 400px;
            }

            .one-img-title {
                font-size: 24px;
            }
        }

        @media (max-width: 768px) {
            .one-img-container {
                padding: 20px 16px;
            }

            .download-button {
                min-width: 200px;
            }

            .one-img-block {
                padding: 16px;
            }

            .one-img-info-block {
                padding: 8px;
            }
        }
    </style>
@endsection
