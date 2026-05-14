@extends('layouts.app')

@section('title', "Изображение - $image->name - $album->name")

@section('content')
    <div class="shared-image-container container my-40">
        <!-- Navigation -->
        <div class="navigation-bar">
            <a href="{{ route('albums.shared', $album->share_token) }}" class="back-link">
                ← Вернуться к альбому
            </a>
            <div class="album-name">{{ $album->name }}</div>
        </div>

        <!-- Main image content -->
        <div class="shared-image-main">
            <div class="shared-image-wrapper">
                <img src="{{ $displayImage ?? $image->img }}" alt="{{ $image->name }}" class="shared-image">

                <div class="button-img-block-shared">
                    @auth
                        <a href="{{ route('images.download', $image->id) }}"
                            class="red-button button-for-img-shared flex-center" style="min-width: 140px;">
                            📥 Скачать
                        </a>
                    @else
                        <button type="button" onclick="auth();"
                            class="red-button button-for-img-shared flex-center download-button">
                            🔒 Войдите чтобы скачать
                        </button>
                    @endauth

                    <div class="shared-img-subblock">
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
                    <div class="status-badge private-image-shared">🔒 Приватное изображение</div>
                @endif
                @if (!$image->is_approved)
                    <div class="status-badge pending-image-shared">⏳ На модерации</div>
                @endif
            </div>

            <!-- Image info sidebar -->
            <div class="shared-image-info">
                <h1 class="image-title">{{ $image->name }}</h1>

                <div class="info-section">
                    <div class="section-title">Автор</div>
                    <div class="author-info">
                        <img src="{{ asset($album->user->img) }}" alt="{{ $album->user->login }}" class="author-avatar">
                        <div class="author-details">
                            <div class="author-name">{{ $album->user->login }}</div>
                            <div class="author-date">{{ $image->created_at->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                @if ($image->category)
                    <a href="{{ route('images.index', ['category' => $image->category_id]) }}">
                        <div class="info-section">
                            <div class="section-title">Категория</div>
                            <div class="category-badge">{{ $image->category->name }}</div>
                        </div>
                    </a>
                @endif

                @if ($image->tags->count() > 0)
                    <div class="info-section">
                        <div class="section-title">Теги</div>
                        <div class="tags-list">
                            @foreach ($image->tags as $tag)
                                <a href="{{ route('images.index', ['search' => $tag->title]) }}">
                                    <span class="tag">#{{ $tag->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="info-section">
                    <div class="section-title">Статистика</div>
                    <div class="stats">
                        <div class="stat">
                            <span class="stat-icon">❤️</span>
                            <span class="stat-value" id="stat-like-count">{{ $image->like_count }}</span>
                            <span class="stat-label">лайков</span>
                        </div>
                    </div>
                </div>

                @if ($album->description)
                    <div class="info-section">
                        <div class="section-title">Об альбоме</div>
                        <p class="album-description">{{ $album->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Navigation between images -->
        <div class="image-navigation">
            @if ($prevImage)
                <a href="{{ route('albums.shared.image', ['token' => $album->share_token, 'imageId' => $prevImage->id]) }}"
                    class="nav-link prev">
                    ← Предыдущее
                </a>
            @else
                <span class="nav-link disabled">← Предыдущее</span>
            @endif

            <span class="image-counter">{{ $currentPosition ?? 1 }} / {{ $totalImages ?? 0 }}</span>

            @if ($nextImage)
                <a href="{{ route('albums.shared.image', ['token' => $album->share_token, 'imageId' => $nextImage->id]) }}"
                    class="nav-link next">
                    Следующее →
                </a>
            @else
                <span class="nav-link disabled">Следующее →</span>
            @endif
        </div>
    </div>

    <style>
        /* Container */
        .shared-image-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Navigation bar */
        .navigation-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .download-button {
            min-width: 250px;
        }

        .back-link {
            color: #6366f1;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #4f52e0;
            text-decoration: underline;
        }

        .album-name {
            font-size: 18px;
            font-weight: 500;
            color: #333;
        }

        /* Main content layout */
        .shared-image-main {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .shared-image-wrapper {
            flex: 2;
            min-width: 300px;
            background: #f5f5f5;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .shared-image {
            max-width: 100%;
            max-height: 500px;
            object-fit: contain;
            border-radius: 10px;
        }

        .button-img-block-shared {
            height: 60px;
            position: absolute;
            bottom: 0;
            left: 0;
            border-radius: 0 0 20px 20px;
            width: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
        }

        .button-for-img-shared {
            height: 40px !important;
            width: 120px;
            font-size: 14px;
            border-radius: 30px;
            color: var(--main-color-white);
            background: linear-gradient(135deg, var(--main-color-violet), #8183f4);
            padding: 12px 24px;
            font-weight: 500;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .button-for-img-shared:hover {
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
            transform: translateY(-2px);
        }

        .shared-img-subblock {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .button-form-img {
            background: transparent;
            display: flex;
            gap: 7px;
            border: none;
            cursor: pointer;
        }

        .watermark-notice-shared {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 12px;
            color: #ffaa00;
            white-space: nowrap;
            pointer-events: none;
        }

        .watermark-notice-shared a {
            color: #ffaa00;
            pointer-events: auto;
            text-decoration: underline;
        }

        .status-badge.private-image-shared,
        .status-badge.pending-image-shared {
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

        .status-badge.private-image-shared {
            background: rgba(99, 102, 241, 0.95);
            color: white;
        }

        .status-badge.pending-image-shared {
            background: rgba(255, 165, 0, 0.95);
            color: white;
        }

        /* Info sidebar */
        .shared-image-info {
            flex: 1;
            min-width: 280px;
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .image-title {
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #6366f1;
            word-break: break-word;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .author-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .author-name {
            font-size: 16px;
            font-weight: 500;
        }

        .author-date {
            font-size: 12px;
            color: #888;
        }

        .category-badge {
            display: inline-block;
            background: #6366f1;
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
        }

        .tags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tag {
            background: #f0f0f0;
            color: #6366f1;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
        }

        .tag:hover {
            background: #6366f1;
            color: white;
        }

        .stats {
            display: flex;
            gap: 25px;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-icon {
            font-size: 18px;
        }

        .stat-value {
            font-size: 16px;
            font-weight: 600;
        }

        .stat-label {
            font-size: 12px;
            color: #888;
        }

        .album-description {
            color: #666;
            line-height: 1.5;
            font-size: 14px;
        }

        /* Navigation between images */
        .image-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .nav-link {
            background: #6366f1;
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }

        .nav-link:hover {
            background: #4f52e0;
        }

        .nav-link.prev:hover {
            transform: translateX(-3px);
        }

        .nav-link.next:hover {
            transform: translateX(3px);
        }

        .nav-link.disabled {
            background: #ccc;
            cursor: not-allowed;
            pointer-events: none;
        }

        .image-counter {
            font-size: 14px;
            color: #888;
            background: #f5f5f5;
            padding: 8px 16px;
            border-radius: 20px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .shared-image-main {
                flex-direction: column;
            }

            .shared-image-wrapper {
                min-width: auto;
            }
        }

        @media (max-width: 768px) {
            .shared-image-container {
                padding: 20px 16px;
            }

            .download-button {
                min-width: 200px;
            }

            .navigation-bar {
                flex-direction: column;
                text-align: center;
            }

            .image-title {
                font-size: 20px;
            }

            .image-navigation {
                justify-content: center;
            }

            .nav-link {
                padding: 8px 20px;
            }

            .button-for-img-shared {
                width: 100px;
                font-size: 12px;
                padding: 8px 12px;
            }
        }

        @media (max-width: 480px) {
            .watermark-notice-shared {
                white-space: normal;
                text-align: center;
                font-size: 10px;
                bottom: 10px;
            }

            .button-img-block-shared {
                padding: 0 12px;
            }

            .button-for-img-shared {
                width: 85px;
                font-size: 10px;
                padding: 6px 8px;
            }

            .shared-img-subblock {
                gap: 8px;
            }
        }
    </style>
@endsection
