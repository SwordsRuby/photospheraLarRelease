@extends('layouts.app')

@section('title', 'Админ панель - Теги')

@section('content')
    <div class="admin-main">
        @include('admin.partials.menu')

        <div class="flex-column container filter-form" style="margin: 20px auto !important;">
            <form class="flex-column" method="get">
                <div class="search-block gap-block-20px">
                    <img class="search-img" src="{{ asset('img/main/search.svg') }}" alt="search">
                    <input value="{{ request('search') }}" autocomplete="off" class="search-input" type="search"
                        placeholder="Поиск..." name="search">
                </div>
            </form>
        </div>

        <!-- Update modal -->
        <div class="modal-block flex-center update-block none">
            <div class="block-for-close" onclick="closeUpdateModal()"></div>
            <div class="modal-subblock flex-column update-subblock gap-block-10px">
                <form class="modal-form gap-block-10px flex-column" method="POST" id="updateForm">
                    @csrf
                    @method('PUT')
                    <h2 class="modal-block-title">Редактирование</h2>
                    <div class="flex-column gap-block-10px">
                        <input type="hidden" name="updateId" id="updateId">
                        <div class="width-100">
                            <input class="input" placeholder="Добавьте название" type="text" maxlength="100"
                                name="title" id="update-name">
                            <p id="name-update-error" class="input-error-text none">Поле название пусто</p>
                        </div>
                        <div class="flex-space-between gap-block-10px">
                            <input class="red-button modal-button" type="button" id="button-form-update-submit"
                                onclick="submitUpdateForm();" value="Сохранить">
                            <button type="button" onclick="openDeleteTagModal();" class="black-button modal-button"
                                style="background: #ff3366; color: white;">Удалить</button>
                        </div>
                        <input type="button" value="Отменить" onclick="closeUpdateModal();"
                            class="black-button modal-button">
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete tag modal -->
        <div class="modal-block flex-center del-tag-block none" id="deleteTagModal">
            <div class="block-for-close" onclick="closeDeleteTagModal()"></div>
            <div class="modal-subblock flex-column del-subblock gap-block-10px">
                <form class="modal-form gap-block-10px flex-column" method="POST" id="deleteTagForm">
                    @csrf
                    @method('DELETE')
                    <h2 class="modal-block-title">Удалить тег?</h2>
                    <p class="text-center">Тег будет удален из всех изображений.<br>Это действие нельзя отменить.</p>
                    <div class="flex-space-between gap-block-10px">
                        <input class="red-button modal-button" type="submit" value="Удалить" style="background: #ff3366;">
                        <input type="button" value="Отменить" onclick="closeDeleteTagModal();"
                            class="black-button modal-button">
                    </div>
                </form>
            </div>
        </div>

        <div class="tablet-column container flex-column">
            <form onchange="formVerify();" method="POST" class="flex-space-between flex-column"
                action="{{ route('admin.tags.store') }}">
                @csrf
                <div class="flex-column main-title-block">
                    <h2 class="main-title wrap">Добавление тега</h2>
                    <div class="main-line"></div>
                </div>

                <div class="width-100 flex-column gap-block-10px my-40 my-20"
                    style="max-width: 800px; width: 100%; margin: 0 auto; margin-top: 20px !important;">
                    <div class="width-100">
                        <input class="input" placeholder="Добавьте название" type="text" maxlength="100" name="title"
                            id="add-img-name" value="{{ old('title') }}">
                        <p id="name-error" class="input-error-text none">Поле название пусто</p>
                        @error('title')
                            <p class="input-error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <input class="red-button flex-center unactive-button admin-img-button width-100 button-margin" disabled
                        id="button-form-submit" value="Добавить" type="button" onclick="formSubmit(this);">
                </div>
            </form>

            <div class="admin-categories-tag-catalog my-40">
                <div class="flex-column main-title-block">
                    <h2 class="main-title wrap">Теги</h2>
                    <div class="main-line"></div>
                </div>

                <div class="categ-admin-block pt-20 gap-block-10px width-100 flex-wrap">
                    @foreach ($tags as $tag)
                        <div class="admin-card">
                            <h2 class="admin-card-title">#{{ $tag->title }}</h2>
                            <button
                                onclick="openUpdateModal({{ $tag->id }}, '{{ $tag->title }}', '{{ route('admin.tags.update', $tag->id) }}')"
                                class="black-button flex-center width-100 admin-img-button">Редактировать</button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentUpdateUrl = '';

        function openUpdateModal(id, title, url) {
            currentUpdateUrl = url;
            document.getElementById('updateId').value = id;
            document.getElementById('update-name').value = title;

            const errorElement = document.getElementById('name-update-error');
            if (errorElement) {
                errorElement.classList.add('none');
            }

            const submitBtn = document.getElementById('button-form-update-submit');
            if (submitBtn) {
                submitBtn.removeAttribute('disabled');
                submitBtn.classList.remove('unactive-button');
            }

            const updateBlock = document.querySelector('.update-block');
            if (updateBlock) {
                updateBlock.classList.remove('none');
            }
        }

        function closeUpdateModal() {
            const updateBlock = document.querySelector('.update-block');
            if (updateBlock) {
                updateBlock.classList.add('none');
            }

            document.getElementById('update-name').value = '';
            document.getElementById('updateId').value = '';
            currentUpdateUrl = '';

            const errorElement = document.getElementById('name-update-error');
            if (errorElement) {
                errorElement.classList.add('none');
            }
        }

        function submitUpdateForm() {
            const titleInput = document.getElementById('update-name');
            const title = titleInput.value.trim();
            const errorElement = document.getElementById('name-update-error');

            if (title === '') {
                if (errorElement) {
                    errorElement.textContent = 'Поле название пусто';
                    errorElement.classList.remove('none');
                }
                return false;
            }

            if (errorElement) {
                errorElement.classList.add('none');
            }

            const form = document.getElementById('updateForm');
            if (form && currentUpdateUrl) {
                form.action = currentUpdateUrl;
                form.submit();
            }
        }

        let currentDeleteTagUrl = '';

        function openDeleteTagModal() {
            const updateId = document.getElementById('updateId').value;
            if (updateId) {
                let routeUrl = '{{ route('admin.tags.destroy', ':id') }}';
                currentDeleteTagUrl = routeUrl.replace(':id', updateId);
                document.getElementById('deleteTagForm').action = currentDeleteTagUrl;
                document.getElementById('deleteTagModal').classList.remove('none');
            }
        }

        function closeDeleteTagModal() {
            document.getElementById('deleteTagModal').classList.add('none');
            currentDeleteTagUrl = '';
        }

        function closeUpdateModal() {
            const updateBlock = document.querySelector('.update-block');
            if (updateBlock) {
                updateBlock.classList.add('none');
            }

            document.getElementById('update-name').value = '';
            document.getElementById('updateId').value = '';
            currentUpdateUrl = '';

            const errorElement = document.getElementById('name-update-error');
            if (errorElement) {
                errorElement.classList.add('none');
            }
        }
    </script>
@endpush
