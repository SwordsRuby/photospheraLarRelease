@extends('layouts.app')

@section('title', $album->name . ' - Публичный альбом')

@section('content')
    <div class="container my-40">
        <!-- Header with album info -->
        <div class="flex-column main-title-block text-center">
            <h1 class="main-title wrap" style="font-size: 32px;">{{ $album->name }}</h1>
            <div class="main-line" style="margin: 0 auto; margin-top: 10px;"></div>
            @if ($album->description)
                <p class="about-us_text mt-20" style="text-align: center; max-width: 600px; margin: 20px auto 0;">
                    {{ $album->description }}
                </p>
            @endif
            <p class="shared-by" style="margin-top: 15px; color: #888;">
                📸 Альбом пользователя <strong>{{ $album->user->login }}</strong>
            </p>
        </div>

        <!-- Gallery -->
        <div class="my-40 gallery container flex-column gap-block-20px">
            @if ($images->count() > 0)
                <div class="gallery_block">
                    @foreach ($images as $index => $image)
                        <a href="{{ route('albums.shared.image', ['token' => $album->share_token, 'imageId' => $image->id]) }}"
                            class="img-block block-img-gallery-{{ ($index % 12) + 1 }}">
                            <div class="image-wrapper" style="position: relative; width: 100%; height: 100%;">
                                <img class="gallery-img" src="{{ $image->display_img ?? $image->img }}"
                                    alt="{{ $image->name }}">

                                <div class="image-overlay">
                                    <div class="image-info">
                                        <h3 class="image-name">{{ $image->name }}</h3>
                                        <div class="image-stats">
                                            <span class="likes-count">❤️ {{ $image->like_count }}</span>
                                            @if ($image->category)
                                                <span class="category">📁 {{ $image->category->name }}</span>
                                            @endif
                                        </div>
                                        @if ($image->tags->count() > 0)
                                            <div class="image-tags">
                                                @foreach ($image->tags->take(5) as $tag)
                                                    <span class="tag">#{{ $tag->title }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="no-img-block flex-center">
                    <h2>В альбоме пока нет изображений</h2>
                </div>
            @endif
        </div>

        <!-- Footer note -->
        <div class="text-center mt-40" style="color: #888; font-size: 12px;">
            <p>Публичный альбом • Фотосфера</p>
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

        .my-40 {
            margin-top: 40px;
            margin-bottom: 40px;
        }

        /* Image overlay styles */
        .image-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .img-block {
            cursor: pointer;
            text-decoration: none;
        }

        .img-block:hover .image-overlay {
            opacity: 1;
        }

        .image-info {
            color: white;
            text-align: center;
            padding: 20px;
            transform: translateY(20px);
            transition: transform 0.3s;
        }

        .img-block:hover .image-info {
            transform: translateY(0);
        }

        .image-name {
            font-size: 18px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .image-stats {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .image-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
        }

        .tag {
            font-size: 11px;
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 8px;
            border-radius: 15px;
        }

        .shared-by {
            font-size: 14px;
        }

        /* Watermark badge for gallery */
        .watermark-badge-gallery {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            z-index: 10;
            pointer-events: none;
        }

        :root {
            --main-small-img: 256px;
            --main-average-img: 436px;
            --main-big-img: 576px;
        }

        /* media desktop */
        @media screen and (max-width: 1400px) {
            .container {
                max-width: 1200px;
            }

            :root {
                --main-small-img: 227px;
                --main-average-img: 355px;
                --main-big-img: 483px;
            }
        }

        /* media tablet */
        @media screen and (max-width: 1200px) {
            .container {
                max-width: 960px;
            }

            :root {
                --main-small-img: 260px;
                --main-average-img: 424px;
                --main-big-img: 587px;
            }

            .gallery_block {
                display: grid;
                grid-template:
                    'img-1 img-1 img-1 img-2'
                    'img-3 img-3 img-4 img-4'
                    'img-6 img-5 img-5 img-5'
                    'img-8 img-8 img-10 img-10'
                    'img-9 img-9 img-9 img-7'
                    'img-12 img-11 img-11 img-11';
            }
        }

        @media screen and (max-width: 960px) {
            :root {
                --main-small-img: 240px;
                --main-average-img: 240px;
                --main-big-img: 240px;
            }

            .gallery_block {
                display: flex !important;
                gap: 20px;
                max-width: 500px;
                flex-direction: row !important;
                flex-wrap: wrap !important;
            }

            .img-block {
                max-width: var(--main-average-img) !important;
            }
        }

        @media (max-width: 768px) {
            :root {
                --main-small-img: 400px;
                --main-average-img: 400px;
                --main-big-img: 400px;
            }

            .gallery_block {
                display: flex !important;
                max-width: 500px;
                flex-direction: column !important;
                flex-wrap: nowrap;
            }

            .img-block {
                max-width: var(--main-average-img) !important;
            }
        }
    </style>
@endsection
