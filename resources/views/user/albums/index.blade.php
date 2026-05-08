@extends('layouts.app')

@section('title', 'Мои альбомы')

@section('content')
    @include('components.profile_header')


    <div class="container flex-space-between mobile-column-reverse">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Мои альбомы</h2>
            <div class="main-line"></div>
        </div>
        <a href="{{ route('user.albums.create') }}" class="red-button width-260 user-chosen-button">+ Создать альбом</a>
    </div>

    <div class="my-40 gallery container flex-column gap-block-20px">
        @if ($albums->count() > 0)
            <div class="gallery_block">
                @foreach ($albums as $album)
                    <a href="{{ route('user.albums.show', $album->id) }}"
                        class="img-block block-img-gallery-{{ ($loop->index % 12) + 1 }}">
                        <img class="gallery-img"
                            src="{{ $album->cover_image ? asset($album->cover_image) : asset('img/main/photo-image.png') }}"
                            alt="{{ $album->name }}">
                        <div class="button-img-block flex-center flex-column">
                            <h3 class="slider-title" style="color: white;">{{ $album->name }}</h3>
                            <p class="file-path-subblock-text" style="color: white; margin: 0;">изображений -
                                {{ $album->images_count }}
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="pagination">
                {{ $albums->links() }}
            </div>
        @else
            <div class="no-img-block flex-center gap-block-10px mobile-column">
                <h2>У вас пока нет альбомов</h2>
                <a href="{{ route('user.albums.create') }}" class="red-button user-chosen-button mt-20">Создать альбом</a>
            </div>
        @endif
    </div>
@endsection
