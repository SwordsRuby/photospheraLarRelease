@extends('layouts.app')

@section('title', "Альбом - $album->name")

@section('content')
    @include('components.profile_header')


    <div class="container">
        <div class="flex-space-between mobile-column-reverse mb-20">
            <div class="flex-column main-title-block">
                <h2 class="main-title wrap">{{ $album->name }}</h2>
                <div class="main-line"></div>
                @if ($album->description)
                    <p class="about-us_text mt-20">{{ $album->description }}</p>
                @endif
            </div>
            <div class="gap-block-10px mobile-column width-100-mobile">
                <a href="{{ route('user.albums.edit', $album->id) }}"
                    class="black-button user-chosen-button">Редактировать</a>
                <button onclick="showDeleteAlbumModal()" class="red-button user-chosen-button">Удалить альбом</button>

                <button onclick="toggleShareModal()" class="black-button user-chosen-button" style="background: #2196F3;">
                    🔗 Поделиться
                </button>
            </div>
        </div>
    </div>

    <div class="my-40 gallery container flex-column gap-block-20px">
        <h3 class="main-title">Изображения в альбоме</h3>
        @if ($album->images->count() > 0)
            <div class="gallery_block">
                @foreach ($album->images as $index => $image)
                    <a style="z-index:1;" href="{{ route('images.edit', $image->id) }}"
                        class="img-block block-img-gallery-{{ ($index % 12) + 1 }}">
                        <img class="gallery-img" src="{{ $image->img }}" alt="{{ $image->name }}">

                        <div class="gallery-img-block">
                            <div class="gallery-img-subblock" style="z-index:2;">
                                <form onsubmit="likeSubmit(this, event);" method="post"
                                    class="like-container like-form scale"
                                    data-url="{{ route('images.like', $image->id) }}">
                                    @csrf
                                    <input type="hidden" name="likeUserId" value="{{ Auth::id() ?? -1 }}">
                                    <input type="hidden" name="likeImgId" value="{{ $image->id }}">
                                    <button class="flex-center button-form-img">
                                        <h3
                                            class="like-numbers {{ $image->is_liked_by_user ? 'like-numbers-active' : '' }}">
                                            {{ $image->like_count }}
                                        </h3>
                                        <div
                                            class="gallery-like scale {{ $image->is_liked_by_user ? 'gallery-like-active' : '' }}">
                                        </div>
                                    </button>
                                </form>

                                <form class="like-container like-form scale" onsubmit="event.preventDefault()"
                                    action="" method="get">
                                    <!-- Remove from album button - opens modal -->
                                    <button style="z-index:2;"
                                        onclick="showRemoveFromAlbumModal({{ $album->id }}, {{ $image->id }}, '{{ addslashes($image->name) }}')"
                                        class="flex-center button-form-img" title="Удалить из альбома">
                                        <div class="gallery-trash scale">
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if ($image->is_private)
                            <div class="status-badge private"
                                style="position: absolute; top: 10px; right: 10px; background: rgba(99, 102, 241, 0.9); padding: 5px 10px; border-radius: 20px; font-size: 12px; color: white; z-index: 10;">
                                🔒 Приватное
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="no-img-block flex-center">
                <h2>В альбоме пока нет изображений</h2>
            </div>
        @endif
    </div>

    <!-- Available images to add -->
    @if ($userImages->count() > 0)
        <div class="my-40 gallery container flex-column gap-block-20px">
            <h3 class="main-title">Добавить изображения в альбом</h3>
            <div class="gallery_block">
                @foreach ($userImages as $index => $image)
                    <div class="img-block block-img-gallery-{{ ($index % 12) + 1 }}">
                        <img class="gallery-img" src="{{ $image->img }}" alt="{{ $image->name }}">
                        <div class="gallery-img-block">
                            <div class="gallery-img-subblock">
                                <form method="POST" action="{{ route('user.albums.addImage', $album->id) }}">
                                    @csrf
                                    <input type="hidden" name="image_id" value="{{ $image->id }}">
                                    <button type="submit" class="add-to-album-btn"
                                        style="background: #4CAF50; color: white; border: none; padding: 8px 20px; border-radius: 20px; cursor: pointer; font-size: 14px;">
                                        + Добавить
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pagination">
                {{ $userImages->links() }}
            </div>
        </div>
    @endif

    <!-- Delete album modal -->
    <div style="z-index: 10;" class="modal-block flex-center delete-album-modal none" id="deleteAlbumModal">
        <div onclick="closeDeleteAlbumModal();" class="block-for-close"></div>
        <div class="modal-subblock flex-column del-subblock gap-block-10px">
            <h2 class="modal-block-title" style="color: #ff3366; text-align: center;">Удаление альбома</h2>
            <p class="message-title" style="text-align: center;">Вы уверены, что хотите удалить альбом
                "{{ $album->name }}"?<br>Изображения останутся в вашей галерее.</p>
            <div class="flex-space-between gap-block-10px">
                <button onclick="closeDeleteAlbumModal();" class="black-button modal-button">Отмена</button>
                <form id="deleteAlbumForm" method="POST" action="{{ route('user.albums.destroy', $album->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="red-button modal-button" style="background: #ff3366;">Удалить</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove image from album modal -->
    <div style="z-index: 10;" class="modal-block flex-center remove-image-modal none" id="removeImageModal">
        <div onclick="closeRemoveImageModal();" class="block-for-close"></div>
        <div class="modal-subblock flex-column del-subblock gap-block-10px">
            <h2 class="modal-block-title" style="color: #ff3366; text-align: center;">Удаление изображения</h2>
            <p class="message-title" style="text-align: center;" id="removeImageMessage">Вы уверены, что хотите удалить
                изображение из альбома?</p>
            <div class="flex-space-between gap-block-10px">
                <button onclick="closeRemoveImageModal();" class="black-button modal-button">Отмена</button>
                <form id="removeImageForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="red-button modal-button" style="background: #ff3366;">Удалить</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div style="z-index: 10;" class="modal-block flex-center share-modal none" id="shareModal">
        <div onclick="closeShareModal();" class="block-for-close"></div>
        <div class="modal-subblock flex-column gap-block-10px" style="width: 450px;">
            <h2 class="modal-block-title" style="text-align: center;">Публичная ссылка на альбом</h2>

            <div id="shareLinkContainer" style="display: none;">
                <p class="text-center" style="margin-bottom: 10px;">
                    Любой, у кого есть эта ссылка, может просматривать альбом
                </p>
                <div class="share-link-box" style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <input type="text" id="shareLinkInput" class="input" readonly
                        style="flex: 1; background: #f5f5f5;">
                    <button onclick="copyShareLink()" class="red-button" style="padding: 12px 20px;">Копировать</button>
                </div>
                <div class="flex-space-between gap-block-10px">
                    <button onclick="regenerateShareLink()" class="black-button" style="flex: 1;">Обновить
                        ссылку</button>
                    <button onclick="disableShareLink()" class="red-button"
                        style="flex: 1; background: #ff3366;">Отключить</button>
                </div>
                <p class="info-text text-center" style="font-size: 11px; color: #888; margin-top: 15px;">
                    Ссылка действительна 30 дней. После отключения ссылка станет недоступной.
                </p>
            </div>

            <div id="noImagesWarning" style="display: none; text-align: center;">
                <p style="color: #ff3366; margin-bottom: 15px;">
                    ⚠️ Невозможно создать публичную ссылку для пустого альбома.
                </p>
                <p>Добавьте хотя бы одно изображение в альбом, чтобы поделиться им.</p>
                <button onclick="closeShareModal();" class="red-button modal-button mt-20">Закрыть</button>
            </div>

            <div id="loadingSpinner" style="display: none; text-align: center; padding: 20px;">
                <div class="spinner"></div>
                <p>Загрузка...</p>
            </div>
        </div>
    </div>

    <script>
        function showDeleteAlbumModal() {
            document.getElementById('deleteAlbumModal').classList.remove('none');
        }

        function closeDeleteAlbumModal() {
            document.getElementById('deleteAlbumModal').classList.add('none');
        }

        // Remove image from album
        let currentAlbumId = null;
        let currentImageId = null;

        function showRemoveFromAlbumModal(albumId, imageId, imageName) {
            currentAlbumId = albumId;
            currentImageId = imageId;

            const messageEl = document.getElementById('removeImageMessage');
            messageEl.textContent = `Вы уверены, что хотите удалить изображение "${imageName}" из альбома?`;

            const form = document.getElementById('removeImageForm');
            form.action = `/user/albums/${albumId}/remove-image/${imageId}`;

            document.getElementById('removeImageModal').classList.remove('none');
        }

        function closeRemoveImageModal() {
            document.getElementById('removeImageModal').classList.add('none');
            currentAlbumId = null;
            currentImageId = null;
        }

        // Share modal functions
        let currentShareToken = "{{ $album->share_token }}";
        let currentShareUrl = "{{ $album->share_url }}";

        function toggleShareModal() {
            const modal = document.getElementById('shareModal');
            const shareContainer = document.getElementById('shareLinkContainer');
            const warningContainer = document.getElementById('noImagesWarning');
            const spinner = document.getElementById('loadingSpinner');

            modal.classList.remove('none');

            // Check if album has images
            const imageCount = {{ $album->images()->count() }};

            if (imageCount === 0) {
                shareContainer.style.display = 'none';
                warningContainer.style.display = 'block';
                spinner.style.display = 'none';
                return;
            }

            if (currentShareToken && currentShareUrl) {
                // Already has share link
                shareContainer.style.display = 'block';
                warningContainer.style.display = 'none';
                spinner.style.display = 'none';
                document.getElementById('shareLinkInput').value = currentShareUrl;
            } else {
                // Generate new share link
                shareContainer.style.display = 'none';
                warningContainer.style.display = 'none';
                spinner.style.display = 'block';
                generateShareLink();
            }
        }

        function closeShareModal() {
            document.getElementById('shareModal').classList.add('none');
        }

        function generateShareLink() {
            fetch('{{ route('user.albums.share.generate', $album->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const spinner = document.getElementById('loadingSpinner');
                    const shareContainer = document.getElementById('shareLinkContainer');

                    spinner.style.display = 'none';

                    if (data.success) {
                        currentShareToken = data.share_token;
                        currentShareUrl = data.share_url;
                        document.getElementById('shareLinkInput').value = currentShareUrl;
                        shareContainer.style.display = 'block';
                    } else {
                        alert(data.message || 'Ошибка при создании ссылки');
                        closeShareModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('loadingSpinner').style.display = 'none';
                    alert('Ошибка при создании ссылки');
                    closeShareModal();
                });
        }

        function regenerateShareLink() {
            if (!confirm('Обновление ссылки сделает старую ссылку недействительной. Продолжить?')) {
                return;
            }

            fetch('{{ route('user.albums.share.regenerate', $album->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentShareToken = data.share_token;
                        currentShareUrl = data.share_url;
                        document.getElementById('shareLinkInput').value = currentShareUrl;
                        alert('Ссылка обновлена');
                    } else {
                        alert(data.message || 'Ошибка при обновлении ссылки');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при обновлении ссылки');
                });
        }

        function disableShareLink() {
            if (!confirm('Отключение ссылки сделает её недоступной для всех. Продолжить?')) {
                return;
            }

            fetch('{{ route('user.albums.share.disable', $album->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentShareToken = null;
                        currentShareUrl = null;
                        document.getElementById('shareLinkInput').value = '';
                        closeShareModal();
                        alert('Публичная ссылка отключена');
                    } else {
                        alert(data.message || 'Ошибка при отключении ссылки');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ошибка при отключении ссылки');
                });
        }

        function copyShareLink() {
            const input = document.getElementById('shareLinkInput');
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            alert('Ссылка скопирована в буфер обмена');
        }
    </script>

    <style>
        .add-to-album-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .add-to-album-btn:hover {
            background: #45a049;
            transform: scale(1.05);
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #6366f1;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .info-text {
            font-size: 11px;
            color: #888;
        }
    </style>
@endsection
