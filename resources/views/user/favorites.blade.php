@extends('layouts.app')

@section('title', 'Избранное')

@section('content')
    @include('components.profile_header')


    <div class="container flex-space-between mobile-column-reverse">
        <div class="flex-column main-title-block">
            <h2 class="main-title">Избранное</h2>
            <div class="main-line"></div>
            <p class="about-us_text mt-10">Изображения, которые вы добавили в избранное</p>
        </div>

        <div class="gap-block-10px mobile-column">
            <a href="{{ route('user.added') }}" class="black-button width-260 user-chosen-button">Мои изображения</a>
            <a href="{{ route('user.albums.index') }}" class="black-button width-260 user-chosen-button">Мои альбомы</a>
        </div>
    </div>

    <div class="gallery my-40 container flex-column gap-block-20px">
        @if ($images->count() > 0)
            <div class="gallery_block" id="galleryId">
                @foreach ($images as $index => $image)
                    @php
                        $targetRoute = $image->is_private
                            ? route('images.edit', $image->id)
                            : route('images.show', $image->id);
                    @endphp
                    <a href="{{ $targetRoute }}" class="img-block block-img-gallery-{{ ($index % 12) + 1 }}">
                        <img class="gallery-img" src="{{ $image->img }}" alt="{{ $image->name }}">
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
                                            class="like-numbers none-invert {{ $image->is_liked_by_user ? 'like-numbers-active' : '' }}">
                                            {{ $image->like_count }}
                                        </h3>
                                        <div
                                            class="gallery-like none-invert scale {{ $image->is_liked_by_user ? 'gallery-like-active' : '' }}">
                                        </div>
                                    </button>
                                </form>

                                <form onsubmit="bookmarkSubmit(this, event); deleteBookmarkInFavorites(this);"
                                    class="flex-center" method="post"
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
                        @if (!$image->is_approved)
                            <div class="status-badge pending"
                                style="position: absolute; top: 10px; left: 10px; background: rgba(255, 165, 0, 0.9); padding: 5px 10px; border-radius: 20px; font-size: 12px; color: white;">
                                ⏳ На модерации
                            </div>
                        @endif
                        @if ($image->is_private)
                            <div class="status-badge private"
                                style="position: absolute; top: 10px; right: 10px; background: rgba(99, 102, 241, 0.9); padding: 5px 10px; border-radius: 20px; font-size: 12px; color: white;">
                                🔒 Приватное
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
            <div class="pagination">
                {{ $images->links() }}
            </div>
        @else
            <div class="no-img-block flex-center">
                <h2>Изображения не найдены</h2>
            </div>
        @endif
    </div>
@endsection
