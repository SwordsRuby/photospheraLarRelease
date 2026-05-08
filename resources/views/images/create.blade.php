@extends('layouts.app')

@section('title', 'Добавить изображение')

@section('content')
    <div class="add-img-block container my-40">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Добавление изображения</h2>
            <div class="main-line"></div>
            <p class="section-subtitle mt-10">Загрузите новое изображение и поделитесь им с сообществом</p>
        </div>

        <form method="post" enctype="multipart/form-data" class="add-img-form-block my-40 flex-space-between mobile-column"
            action="{{ route('images.store') }}">
            @csrf

            <div class="add-img-path-block gap-block-10px flex-column">
                <div class="add-img-file-path-block flex-center">
                    <input type="file" name="image" id="add-img-file"
                        accept="image/jpeg,image/png,image/jpg,image/svg,image/webp" onchange="addImgF();">
                    <div class="add-img-file-path-subblock flex-column gap-block-20px">
                        <div class="upload-icon">📸</div>
                        <h3 class="file-path-subblock-title">
                            <span class="red-file-choose">Выберите файл</span> или перетащите его сюда
                        </h3>
                        <img src="{{ asset('img/main/arrow-black.svg') }}" alt="" class="file-path-subblock-img">
                        <p class="file-path-subblock-text">.JPG .PNG .JPEG .SVG .WEBP (макс. 10MB)</p>
                    </div>
                </div>

                <div class="or-divider">
                    <span>или</span>
                </div>

                <div class="width-100">
                    <div class="input-wrapper">
                        <span class="input-icon">🔗</span>
                        <input autocomplete="off" class="input" placeholder="Введите URL изображения" maxlength="1000"
                            type="text" name="image_url" id="add-img-URL" value="{{ old('image_url') }}"
                            oninput="addImgU();" onchange="addImgU();">
                    </div>
                    <p id="img-error" class="input-error-text none">Неверный формат изображения или некорректный путь</p>
                    @error('image')
                        <p class="input-error-text">{{ $message }}</p>
                    @enderror
                    @error('image_url')
                        <p class="input-error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="add-img-info-block flex-column gap-block-10px">
                <div class="width-100">
                    <div class="input-wrapper">
                        <span class="input-icon">🏷️</span>
                        <input class="input" placeholder="Добавьте название" type="text" maxlength="50" name="name"
                            id="add-img-name" value="{{ old('name') }}" oninput="formVerify();" onchange="formVerify();">
                    </div>
                    <p id="name-error" class="input-error-text none">Поле название пусто</p>
                    @error('name')
                        <p class="input-error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags dropdown -->
                <div tabindex="1" onclick="viewCategoriesTag(this);" onblur="viewCategoriesTagRemove(this);"
                    class="input-select-main-block">
                    <div class="input select-block flex-space-between">
                        <h3 class="input-select-title">Добавьте до 8 тэгов</h3>
                        <img src="{{ asset('img/main/arrow-black.svg') }}" alt="arrow" class="input-select-arrow">
                    </div>

                    <div class="input-datalist tag-datalist flex-wrap">
                        @foreach ($tags as $tag)
                            <div class="input-checkbox-block">
                                <input class="input-checkbox none" type="checkbox" name="tags[]"
                                    value="{{ $tag->id }}" id="tag_{{ $tag->id }}"
                                    {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }} onchange="formVerify();">
                                <label for="tag_{{ $tag->id }}">#{{ $tag->title }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Category dropdown -->
                <div tabindex="2" onclick="viewCategoriesTag(this);" onblur="viewCategoriesTagRemove(this);"
                    class="input-select-main-block">
                    <div class="width-100">
                        <div id="add-img-categories" class="input select-block flex-space-between">
                            <h3 id="categories-title" class="input-select-title">Выберите категорию</h3>
                            <img src="{{ asset('img/main/arrow-black.svg') }}" alt="arrow" class="input-select-arrow">
                        </div>
                        <p id="categories-error" class="input-error-text none">Поле категория пусто</p>
                        @error('category_id')
                            <p class="input-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="input-datalist categories-datalist flex-wrap categories-select-block gap-block-20px">
                        @foreach ($categories as $category)
                            <div class="input-radio-block">
                                <input value="{{ $category->id }}" class="input-checkbox none" type="radio"
                                    name="category_id" id="category_{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'checked' : '' }} onchange="formVerify();">
                                <label for="category_{{ $category->id }}">{{ $category->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Album selection -->
                <div class="width-100">
                    <select name="album_id" class="input">
                        <option value="">-- Добавить в альбом (необязательно) --</option>
                        @foreach (Auth::user()->albums as $album)
                            <option value="{{ $album->id }}" {{ old('album_id') == $album->id ? 'selected' : '' }}>
                                📁 {{ $album->name }} ({{ $album->images_count }} изображений)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Privacy checkbox -->
                <div class="privacy-option">
                    <label class="flex-space-between" style="cursor: pointer; gap: 10px;">
                        <span class="black-text">🔒 Сделать изображение приватным</span>
                        <input type="checkbox" name="is_private" value="1" {{ old('is_private') ? 'checked' : '' }}
                            style="width: 20px; height: 20px; cursor: pointer;">
                    </label>
                    <p class="privacy-hint">Приватные изображения видны только вам</p>
                </div>

                <!-- Buttons -->
                <div class="add-img-info-buttons gap-block-10px mobile-colum flex-center">
                    <a href="{{ route('images.index') }}" class="black-button add-img-button flex-center">Отменить</a>
                    <input class="red-button add-img-button flex-center unactive-button" disabled id="button-form-submit"
                        value="📤 Добавить изображение" type="submit" onclick="formSubmit(this);">
                </div>
            </div>
        </form>
    </div>

    <style>
        .section-subtitle {
            color: #888;
            font-size: 14px;
        }

        .upload-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .or-divider {
            text-align: center;
            margin: 16px 0;
            position: relative;
        }

        .or-divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
        }

        .or-divider span {
            background: white;
            padding: 0 15px;
            position: relative;
            font-size: 12px;
            color: #888;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            z-index: 1;
        }

        .input-wrapper .input {
            padding-left: 45px;
        }

        .privacy-option {
            background: #f8f9ff;
            padding: 16px;
            border-radius: 20px;
            margin-top: 8px;
        }

        .privacy-hint {
            font-size: 11px;
            color: #888;
            margin-top: 8px;
            margin-left: 0;
        }

        .add-img-file-path-block {
            min-height: 350px;
            background: linear-gradient(135deg, #f8f9ff, #f0f0ff);
            border: 2px dashed var(--main-color-violet);
            border-radius: 28px;
            transition: var(--transition-smooth);
            cursor: pointer;
        }

        .add-img-file-path-block:hover {
            border-color: var(--main-color-violet);
            background: linear-gradient(135deg, #f0f1ff, #e8e8ff);
        }

        .file-path-subblock-title {
            font-size: 16px;
            max-width: 260px;
            text-align: center;
        }

        @media (max-width: 1200px) {
            .add-img-info-buttons {
                margin-top: 20px;
            }

            .red-button {
                min-width: 250px !important;
            }
        }

        @media (max-width: 768px) {
            .add-img-info-buttons {
                margin-top: 20px;
            }

            .add-img-button {
                width: 100%;
            }
        }
    </style>
@endsection
