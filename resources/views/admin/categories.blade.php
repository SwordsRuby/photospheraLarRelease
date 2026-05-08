@extends('layouts.app')

@section('title', 'Админ панель - Категории')

@section('content')
    <div class="admin-main">
        @include('admin.partials.menu')

        <div class="flex-column container filter-form" style="margin: 20px auto !important;">
            <form class="flex-column" method="get">
                <div class="search-block gap-block-20px">
                    <img class="search-img" src="{{ asset('img/main/search.svg') }}" alt="search">
                    <input value="{{ request('search') }}" autocomplete="off" class="search-input" type="search"
                        placeholder="Поиск" name="search">
                </div>
            </form>
        </div>

        <!-- Update modal -->
        <div class="modal-block flex-center update-block none">
            <div class="block-for-close" onclick="tagCategoriesUpdate()"></div>
            <div class="modal-subblock flex-column update-subblock gap-block-10px">
                <form class="modal-form gap-block-10px flex-column" method="POST" id="updateForm">
                    @csrf
                    @method('PUT')
                    <h2 class="modal-block-title">Редактирование</h2>
                    <div class="flex-column gap-block-10px">
                        <input type="hidden" name="updateId" id="updateId">
                        <div class="width-100">
                            <input class="input" placeholder="Добавьте название" type="text" maxlength="100"
                                name="name" id="update-name" oninput="formUpdateVerify()" onchange="formUpdateVerify()">
                            <p id="name-update-error" class="input-error-text none">Поле название пусто</p>
                        </div>
                        <div class="flex-space-between gap-block-10px">
                            <input class="red-button modal-button my-20" type="button" id="button-form-update-submit"
                                onclick="formSubmit(this);" value="Сохранить">
                            <button type="button" onclick="openDeleteCategoryModal();" class="black-button modal-button"
                                style="background: #ff3366; color: white;">Удалить</button>
                        </div>
                        <input type="button" value="Отменить" onclick="tagCategoriesUpdate();"
                            class="black-button modal-button">
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete category modal -->
        <div class="modal-block flex-center del-category-block none" id="deleteCategoryModal">
            <div class="block-for-close" onclick="closeDeleteCategoryModal()"></div>
            <div class="modal-subblock flex-column del-subblock gap-block-10px">
                <form class="modal-form gap-block-10px flex-column" method="POST" id="deleteCategoryForm">
                    @csrf
                    @method('DELETE')
                    <h2 class="modal-block-title">Удалить категорию?</h2>
                    <p class="text-center">Все изображения в этой категории потеряют связь с ней.<br>Это действие нельзя
                        отменить.</p>
                    <div class="flex-space-between gap-block-10px">
                        <input class="red-button modal-button" type="submit" value="Удалить" style="background: #ff3366;">
                        <input type="button" value="Отменить" onclick="closeDeleteCategoryModal();"
                            class="black-button modal-button">
                    </div>
                </form>
            </div>
        </div>

        <div class="flex-column container gap-block-20px flex-space-between">
            <form method="POST" enctype="multipart/form-data" class="width-100 flex-column desktop-gap tablet-gap"
                action="{{ route('admin.categories.store') }}">
                @csrf
                <div id="file-url-block-img-add" class="max-width-700 width-100 gap-block-10px flex-column">
                    <div class="flex-column main-title-block">
                        <h2 class="main-title wrap">Добавление категории</h2>
                        <div class="main-line"></div>
                    </div>

                    <div class="add-img-file-path-block flex-center my-20">
                        <input type="file" name="image" id="add-img-file-category"
                            accept="image/jpeg,image/png,image/jpg,image/svg,image/webp" onchange="addImgCategoryF();">
                        <div class="add-img-file-path-subblock flex-column gap-block-20px">
                            <h3 class="file-path-subblock-title">
                                <span class="red-file-choose">Выберите файл</span> или перетащите его сюда
                            </h3>
                            <img src="{{ asset('img/main/arrow-black.svg') }}" alt=""
                                class="file-path-subblock-img">
                            <p class="file-path-subblock-text">.JPG .PNG .JPEG .SVG .WEBP</p>
                        </div>
                    </div>

                    <h3 class="add-img-text">или</h3>
                    <div class="width-100">
                        <input autocomplete="off" class="input" placeholder="Введите URL" type="text" name="image_url"
                            id="add-img-URL-category" oninput="addImgCategoryU();" onchange="addImgCategoryU();">
                        <p id="img-error-category" class="input-error-text none">Неверный формат изображения или
                            некорректный путь</p>
                        @error('image')
                            <p class="input-error-text">{{ $message }}</p>
                        @enderror
                        @error('image_url')
                            <p class="input-error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="width-100 flex-column gap-block-10px" style="max-width: 800px; width: 100%; margin: 0 auto;">
                    <div class="width-100">
                        <input class="input" placeholder="Добавьте название" type="text" maxlength="100"
                            name="name" id="add-img-name-category" value="{{ old('name') }}"
                            oninput="formVerifyCategory();" onchange="formVerifyCategory();">
                        <p id="name-error-category" class="input-error-text none">Поле название пусто</p>
                        @error('name')
                            <p class="input-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <input class="red-button flex-center unactive-button admin-img-button width-100 button-margin" disabled
                        id="button-form-submit-category" value="Добавить" type="button" onclick="formSubmit(this);">
                </div>
            </form>

            <div class="admin-categories-tag-catalog pt-40-tablet-mobile">
                <div class="flex-column my-40 main-title-block">
                    <h2 class="main-title wrap">Категории</h2>
                    <div class="main-line"></div>
                </div>

                <div class="tag-admin-block pt-20 gap-block-10px width-100 flex-wrap">
                    @foreach ($categories as $category)
                        <div class="admin-card">
                            <img src="{{ $category->img }}" alt="{{ $category->name }}"
                                style="width: 170px; height: 170px; object-fit: cover; border-radius: 10px;">
                            <h2 class="admin-card-title">{{ $category->name }}</h2>
                            @if ($category->id != 1)
                                <button
                                    onclick="openUpdateModal('{{ $category->id }}', '{{ $category->name }}', '{{ route('admin.categories.update', $category->id) }}')"
                                    class="black-button flex-center width-100 admin-img-button">Редактировать</button>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Fix for file input */
        #add-img-file-category,
        #add-img-file {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .add-img-file-path-block {
            position: relative;
            cursor: pointer;
            overflow: hidden;
        }
    </style>
@endsection

@push('scripts')
    <script>
        function openUpdateModal(id, name, url) {
            document.getElementById('updateId').value = id;
            document.getElementById('update-name').value = name;
            document.getElementById('updateForm').action = url;
            tagCategoriesUpdate();
            formUpdateVerify();
        }

        function tagCategoriesUpdate() {
            const updateBlock = document.querySelector('.update-block');
            if (updateBlock) {
                updateBlock.classList.toggle('none');
            }
        }

        let currentDeleteCategoryUrl = '';

        function openDeleteCategoryModal() {
            const updateId = document.getElementById('updateId').value;
            if (updateId) {
                let routeUrl = '{{ route('admin.categories.destroy', ':id') }}';
                currentDeleteCategoryUrl = routeUrl.replace(':id', updateId);
                document.getElementById('deleteCategoryForm').action = currentDeleteCategoryUrl;
                document.getElementById('deleteCategoryModal').classList.remove('none');
            }
        }

        function closeDeleteCategoryModal() {
            document.getElementById('deleteCategoryModal').classList.add('none');
            currentDeleteCategoryUrl = '';
        }

        function tagCategoriesUpdate() {
            const updateBlock = document.querySelector('.update-block');
            if (updateBlock) {
                updateBlock.classList.add('none');
            }
            closeDeleteCategoryModal();
        }
    </script>
@endpush
