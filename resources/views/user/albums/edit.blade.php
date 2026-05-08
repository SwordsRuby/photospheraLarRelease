@extends('layouts.app')

@section('title', 'Редактировать альбом')

@section('content')
    @include('components.profile_header')

    <div class="add-img-block container my-40">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Редактирование альбома</h2>
            <div class="main-line"></div>
        </div>

        <form method="post" enctype="multipart/form-data" class="add-img-form-block my-40 flex-column gap-block-20px"
            action="{{ route('user.albums.update', $album->id) }}">
            @csrf
            @method('PUT')

            <div class="width-100">
                <input class="input" placeholder="Название альбома" type="text" maxlength="100" name="name"
                    value="{{ old('name', $album->name) }}" required>
                @error('name')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="width-100">
                <textarea class="input" placeholder="Описание альбома (необязательно)" name="description" rows="4"
                    style="resize: vertical;">{{ old('description', $album->description) }}</textarea>
                @error('description')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="width-100">
                <label class="black-text" style="display: block; margin-bottom: 10px;">Обложка альбома</label>

                <div class="current-cover mb-20">
                    <p class="black-text" style="margin-bottom: 10px;">Текущая обложка:</p>
                    <img src="{{ asset($album->cover_image) }}" alt="current cover"
                        style="width: 200px; height: 200px; object-fit: cover; border-radius: 20px;">
                </div>

                <div class="add-img-file-path-block" id="coverPreviewBlock"
                    style="min-height: 200px; background: #D9D9D9; cursor: pointer; position: relative;">
                    <div class="add-img-file-path-subblock flex-center flex-column" style="height: 100%;">
                        <h3 class="file-path-subblock-title">Нажмите или перетащите новую обложку</h3>
                        <p class="file-path-subblock-text">.JPG .PNG .JPEG .WEBP (макс. 5MB)</p>
                    </div>
                    <input type="file" name="cover" id="coverInput" accept="image/jpeg,image/png,image/jpg,image/webp"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                </div>
                @error('cover')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="add-img-info-buttons gap-block-10px mobile-column">
                <a href="{{ route('user.albums.show', $album->id) }}"
                    class="black-button add-img-button flex-center">Отменить</a>
                <input class="red-button add-img-button flex-center" value="Сохранить" type="submit">
            </div>
        </form>
    </div>

    <script>
        // Preview cover image before upload
        document.getElementById('coverInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewBlock = document.getElementById('coverPreviewBlock');
                    if (previewBlock) {
                        previewBlock.style.background = `url(${event.target.result}) no-repeat center center`;
                        previewBlock.style.backgroundSize = 'cover';
                        // Hide the text overlay
                        const textBlock = previewBlock.querySelector('.add-img-file-path-subblock');
                        if (textBlock) textBlock.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    <style>
        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .current-cover img {
            border: 2px solid #e0e0e0;
            object-fit: cover;
        }

        .add-img-file-path-block {
            transition: all 0.3s;
        }

        .add-img-file-path-block:hover {
            opacity: 0.9;
        }
    </style>
@endsection