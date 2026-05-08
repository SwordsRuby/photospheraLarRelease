@extends('layouts.app')

@section('title', 'Создать альбом')

@section('content')
    @include('components.profile_header')

    <div class="add-img-block container my-40">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Создание альбома</h2>
            <div class="main-line"></div>
        </div>

        <form method="post" class="add-img-form-block my-40 flex-column gap-block-20px"
            action="{{ route('user.albums.store') }}">
            @csrf

            <div class="width-100">
                <input class="input" placeholder="Название альбома" type="text" maxlength="100" name="name"
                    id="album-name" value="{{ old('name') }}" required>
                @error('name')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="width-100">
                <textarea class="input" placeholder="Описание альбома (необязательно)" name="description" rows="4"
                    style="resize: vertical;">{{ old('description') }}</textarea>
                @error('description')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="add-img-info-buttons gap-block-10px mobile-column">
                <a href="{{ route('user.albums.index') }}" class="black-button add-img-button flex-center">Отменить</a>
                <input class="red-button add-img-button flex-center" value="Создать" type="submit">
            </div>
        </form>
    </div>
@endsection