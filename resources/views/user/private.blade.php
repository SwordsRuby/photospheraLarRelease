@extends('layouts.app')

@section('title', 'Приватные изображения')

@section('content')
    @include('components.profile_header')


    <div class="container flex-space-between mobile-column-reverse">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Приватные изображения</h2>
            <div class="main-line"></div>
            <p class="about-us_text mt-10">Эти изображения видны только вам</p>
        </div>
        <a href="{{ route('images.create') }}" class="red-button width-260 user-chosen-button">+ Добавить изображение</a>
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
                            <div class="status-badge pending"
                                style="position: absolute; top: 10px; left: 10px; background: rgba(255, 165, 0, 0.9); padding: 5px 10px; border-radius: 20px; font-size: 12px; color: white;">
                                ⏳ На модерации
                            </div>
                        @endif
                        <div class="status-badge private"
                            style="position: absolute; top: 10px; right: 10px; background: rgba(99, 102, 241, 0.9); padding: 5px 10px; border-radius: 20px; font-size: 12px; color: white;">
                            🔒 Приватное
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="pagination">
                {{ $images->links() }}
            </div>
        @else
            <div class="no-img-block flex-center gap-block-10px mobile-column">
                <h2>Приватные изображения не найдены</h2>
                <a href="{{ route('images.create') }}" class="red-button user-chosen-button mt-20">Добавить изображение</a>
            </div>
        @endif
    </div>
@endsection
