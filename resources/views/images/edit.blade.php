@extends('layouts.app')

@section('title', 'Редактировать изображение')

@section('content')
    <div class="add-img-block container my-40">
        <div class="flex-column main-title-block">
            <h2 class="main-title wrap">Редактирование изображения</h2>
            <div class="main-line"></div>
            <p class="section-subtitle mt-10">Измените информацию об изображении</p>
        </div>

        <form method="post" enctype="multipart/form-data" class="add-img-form-block my-40 flex-space-between mobile-column"
            action="{{ route('images.update', $image->id) }}" id="editImageForm">
            @csrf
            @method('PUT')

            <div class="add-img-path-block gap-block-10px flex-column">
                <div class="add-img-file-path-block flex-center" id="imagePreviewBlock"
                    style="background: url('{{ $image->img }}') no-repeat center center; background-size: contain; min-height: 350px;">
                    <input type="file" name="image" id="add-img-file"
                        accept="image/jpeg,image/png,image/jpg,image/svg,image/webp" onchange="previewImage(this);">
                    <div class="add-img-file-path-subblock flex-column gap-block-20px" id="filePlaceholder"
                        style="display: none;">
                        <div class="upload-icon">🔄</div>
                        <h3 class="file-path-subblock-title">
                            <span class="red-file-choose">Выберите новый файл</span> или перетащите его сюда
                        </h3>
                        <img src="{{ asset('img/main/arrow-black.svg') }}" alt="" class="file-path-subblock-img">
                        <p class="file-path-subblock-text">.JPG .PNG .JPEG .SVG .WEBP (макс. 10MB)</p>
                    </div>
                </div>

                <div class="flex-space-between gap-block-10px mt-20">
                    <a href="{{ asset($image->img) }}" download class="black-button add-img-button flex-center"
                        style="background: #2196F3; color: white;">
                        ⬇ Скачать изображение
                    </a>
                </div>

                <div class="or-divider" id="orText" style="display: none;">
                    <span>или</span>
                </div>

                <div class="width-100" id="urlBlock" style="display: none;">
                    <div class="input-wrapper">
                        <span class="input-icon">🔗</span>
                        <input autocomplete="off" class="input" placeholder="Введите URL изображения" maxlength="1000"
                            type="text" name="image_url" id="add-img-URL" value="{{ old('image_url') }}"
                            oninput="validateUrl(this);">
                    </div>
                    <p id="img-error" class="input-error-text none">Неверный формат изображения или некорректный путь</p>
                </div>
                @error('image')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
                @error('image_url')
                    <p class="input-error-text">{{ $message }}</p>
                @enderror
            </div>

            <div class="add-img-info-block flex-column gap-block-10px">
                <div class="width-100">
                    <div class="input-wrapper">
                        <span class="input-icon">🏷️</span>
                        <input class="input" placeholder="Название" type="text" maxlength="50" name="name"
                            id="add-img-name" value="{{ old('name', $image->name) }}" oninput="validateForm();"
                            onchange="validateForm();">
                    </div>
                    <p id="name-error" class="input-error-text none">Поле название пусто</p>
                    @error('name')
                        <p class="input-error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags dropdown -->
                <div tabindex="1" onclick="toggleDropdown(this);" onblur="closeDropdown(this);"
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
                                    {{ in_array($tag->id, old('tags', $image->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                                    onchange="validateForm();">
                                <label for="tag_{{ $tag->id }}">#{{ $tag->title }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Category dropdown -->
                <div tabindex="2" onclick="toggleDropdown(this);" onblur="closeDropdown(this);"
                    class="input-select-main-block">
                    <div class="width-100">
                        <div class="input select-block flex-space-between">
                            <h3 id="categories-title" class="input-select-title">
                                {{ $image->category ? $image->category->name : 'Выберите категорию' }}
                            </h3>
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
                                    {{ old('category_id', $image->category_id) == $category->id ? 'checked' : '' }}
                                    onchange="updateCategoryTitle(this); validateForm();">
                                <label for="category_{{ $category->id }}">{{ $category->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Album selection -->
                <div class="width-100">
                    <select name="album_id" class="input">
                        <option value="">-- Добавить в альбом (необязательно) --</option>
                        @foreach ($albums as $album)
                            <option value="{{ $album->id }}"
                                {{ in_array($album->id, $imageAlbumIds ?? []) ? 'selected' : '' }}>
                                📁 {{ $album->name }} ({{ $album->images_count }} изображений)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Privacy checkbox -->
                <div class="privacy-option">
                    <label class="flex-space-between" style="cursor: pointer; gap: 10px;">
                        <span class="black-text">🔒 Сделать изображение приватным</span>
                        <input type="checkbox" name="is_private" value="1" id="isPrivateCheckbox"
                            {{ old('is_private', $image->is_private) ? 'checked' : '' }}
                            style="width: 20px; height: 20px; cursor: pointer;">
                    </label>
                    <p class="privacy-hint">Приватные изображения видны только вам</p>
                </div>

                <!-- Buttons -->
                <div class="add-img-info-buttons gap-block-10px mobile-column">
                    <div class="flex-space-between gap-block-10px button-mobile" style="width: 100%;">
                        <a href="{{ route('user.added') }}" class="black-button add-img-button flex-center">Отменить</a>
                        <button type="submit" class="red-button add-img-button flex-center" id="submitButton">💾
                            Сохранить</button>
                        <button type="button" onclick="showDeleteImageModal()"
                            class="red-button add-img-button flex-center" style="background: #ff3366;">
                            🗑 Удалить
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <!-- Delete image modal -->
    <div class="modal-block flex-center delete-image-modal none" id="deleteImageModal">
        <div onclick="closeDeleteImageModal();" class="block-for-close"></div>
        <div class="modal-subblock flex-column del-subblock gap-block-10px">
            <div class="modal-icon warning-icon">⚠️</div>
            <h2 class="modal-block-title" style="color: #ff3366; text-align: center;">Удаление изображения</h2>
            <p class="message-title" style="text-align: center;">Вы уверены, что хотите удалить изображение
                "{{ $image->name }}"?<br>Это действие невозможно отменить.</p>
            <div class="flex-space-between gap-block-10px">
                <button onclick="closeDeleteImageModal();" class="black-button modal-button">Отмена</button>
                <form id="deleteImageForm" method="POST" action="{{ route('images.destroy', $image->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="red-button modal-button" style="background: #ff3366;">Удалить</button>
                </form>
            </div>
        </div>
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
        }

        .warning-icon {
            font-size: 48px;
            text-align: center;
        }

        .unactive-button {
            opacity: 0.6;
            cursor: not-allowed !important;
        }

        @media (max-width: 768px) {
            .add-img-info-buttons {
                margin-top: 20px;
            }
        }

        @media (max-width: 500px) {
            .button-mobile {
                display: flex;
                flex-direction: column;
            }
        }
    </style>

    <script>
        function previewImage(input) {
            const previewBlock = document.getElementById('imagePreviewBlock');
            const placeholder = document.getElementById('filePlaceholder');
            const orText = document.getElementById('orText');
            const urlBlock = document.getElementById('urlBlock');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewBlock.style.background = `url('${e.target.result}') no-repeat center center`;
                    previewBlock.style.backgroundSize = 'cover';
                    placeholder.style.display = 'none';
                    orText.style.display = 'none';
                    urlBlock.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function validateUrl(input) {
            const errorEl = document.getElementById('img-error');
            const previewBlock = document.getElementById('imagePreviewBlock');
            const placeholder = document.getElementById('filePlaceholder');

            if (input.value.trim() !== '') {
                try {
                    new URL(input.value);
                    previewBlock.style.background = `url('${input.value}') no-repeat center center`;
                    previewBlock.style.backgroundSize = 'cover';
                    placeholder.style.display = 'none';
                    errorEl.classList.add('none');
                } catch (e) {
                    errorEl.classList.remove('none');
                }
            }
        }

        function toggleDropdown(el) {
            const dropdown = el.querySelector('.input-datalist');
            const arrow = el.querySelector('.input-select-arrow');
            if (dropdown) {
                dropdown.classList.toggle('input-datalist-active');
                if (arrow) arrow.classList.toggle('input-select-arrow-active');
            }
        }

        function closeDropdown(el) {
            setTimeout(() => {
                const dropdown = el.querySelector('.input-datalist');
                const arrow = el.querySelector('.input-select-arrow');
                if (dropdown) {
                    dropdown.classList.remove('input-datalist-active');
                    if (arrow) arrow.classList.remove('input-select-arrow-active');
                }
            }, 200);
        }

        function updateCategoryTitle(radio) {
            const label = document.querySelector(`label[for="${radio.id}"]`);
            const titleEl = document.getElementById('categories-title');
            if (label && titleEl) {
                titleEl.textContent = label.textContent;
                titleEl.classList.add('black-text');
            }
        }

        function validateForm() {
            const nameInput = document.getElementById('add-img-name');
            const nameError = document.getElementById('name-error');
            const submitBtn = document.getElementById('submitButton');
            const categorySelected = document.querySelector('input[name="category_id"]:checked');
            const categoriesError = document.getElementById('categories-error');

            let isValid = true;

            if (!nameInput.value.trim()) {
                nameError.classList.remove('none');
                isValid = false;
            } else {
                nameError.classList.add('none');
            }

            if (!categorySelected) {
                if (categoriesError) categoriesError.classList.remove('none');
                isValid = false;
            } else {
                if (categoriesError) categoriesError.classList.add('none');
            }

            if (submitBtn) {
                submitBtn.disabled = !isValid;
                if (isValid) {
                    submitBtn.classList.remove('unactive-button');
                } else {
                    submitBtn.classList.add('unactive-button');
                }
            }
        }

        function showDeleteImageModal() {
            document.getElementById('deleteImageModal').classList.remove('none');
        }

        function closeDeleteImageModal() {
            document.getElementById('deleteImageModal').classList.add('none');
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            validateForm();

            const selectedCategory = document.querySelector('input[name="category_id"]:checked');
            if (selectedCategory) {
                updateCategoryTitle(selectedCategory);
            }

            const nameInput = document.getElementById('add-img-name');
            if (nameInput) {
                nameInput.addEventListener('input', validateForm);
                nameInput.addEventListener('change', validateForm);
            }

            const categoryRadios = document.querySelectorAll('input[name="category_id"]');
            categoryRadios.forEach(radio => {
                radio.addEventListener('change', validateForm);
            });
        });
    </script>
@endsection
