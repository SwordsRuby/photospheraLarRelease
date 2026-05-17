@extends('layouts.app')

@section('title', 'Админ панель - Изображения')

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

        <!-- Delete confirmation modal -->
        <div class="modal-block flex-center del-block none">
            <div class="block-for-close"></div>
            <div class="modal-subblock flex-column del-subblock gap-block-10px">
                <form class="modal-form gap-block-10px flex-column" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <h2 class="modal-block-title">Удалить?</h2>
                    <div class="flex-space-between gap-block-10px">
                        <input class="red-button modal-button" type="submit" value="Удалить">
                        <input type="reset" value="Отменить" onclick="del();" class="black-button modal-button">
                    </div>
                </form>
            </div>
        </div>

        @if ($images->count() > 0)
            <div class="admin-img-catalog gap-block-20px container flex-wrap">
                @foreach ($images as $image)
                    <div class="admin-img-card flex-column gap-block-10px">
                        <img src="{{ $image->img }}" class="admin-img" alt="{{ $image->name }}">
                        <p class="admin-img-title">{{ $image->name }}</p>
                        <p class="admin-img-author">Автор: {{ $image->author->login }}</p>
                        <div class="admin-button-block flex-space-between gap-block-10px width-100">
                            <button class="black-button flex-center admin-img-button width-100 modal-button"
                                onclick="setDeleteUrl('{{ route('admin.images.destroy', $image->id) }}'); del();">
                                Удалить
                            </button>
                            @if (!$image->is_approved)
                                <form class="width-100" method="POST"
                                    action="{{ route('admin.images.approve', $image->id) }}">
                                    @csrf
                                    <input class="red-button width-100 admin-img-button modal-button" type="submit"
                                        value="Одобрить">
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-img-block flex-center">
                <h2>Изображения не найдены</h2>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function setDeleteUrl(url) {
            document.getElementById('deleteForm').action = url;
        }
    </script>
@endpush
