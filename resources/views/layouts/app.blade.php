<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Фотосфера')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo/png/favicon.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Preconnect for fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <script defer src="{{ asset('js/main.js') }}"></script>
</head>

<body>

    @include('components.header')

    <main>
        <!-- Success modal -->
        @if (session('success'))
            <div class="modal-block flex-center" id="successModal" style="display: flex;">
                <div class="block-for-close" onclick="document.getElementById('successModal').style.display='none'"></div>
                <div class="modal-subblock flex-column del-subblock gap-block-10px">
                    <div class="success-icon">✓</div>
                    <h2 class="modal-block-title" style="color: #4CAF50; text-align: center;">Успешно!</h2>
                    <p class="message-title" style="text-align: center;">{{ session('success') }}</p>
                    <div class="flex-space-between gap-block-10px">
                        <input type="button" value="OK"
                            onclick="document.getElementById('successModal').style.display='none'"
                            class="red-button modal-button" style="background: #4CAF50;">
                    </div>
                </div>
            </div>
        @endif

        <!-- Error modal -->
        @if (session('error'))
            <div class="modal-block flex-center" id="errorModal" style="display: flex;">
                <div class="block-for-close" onclick="document.getElementById('errorModal').style.display='none'"></div>
                <div class="modal-subblock flex-column del-subblock gap-block-10px">
                    <div class="error-icon">✗</div>
                    <h2 class="modal-block-title" style="color: #ff3366; text-align: center;">Ошибка!</h2>
                    <p class="message-title" style="text-align: center;">{{ session('error') }}</p>
                    <div class="flex-space-between gap-block-10px">
                        <input type="button" value="OK"
                            onclick="document.getElementById('errorModal').style.display='none'"
                            class="red-button modal-button">
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('components.footer')

    @stack('scripts')
</body>

</html>

<style>
/* Modal icon styles */
.success-icon,
.error-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin: 0 auto 16px;
}

.success-icon {
    background: rgba(76, 175, 80, 0.1);
    color: #4CAF50;
}

.error-icon {
    background: rgba(255, 51, 102, 0.1);
    color: #ff3366;
}

main {
    animation: pageFadeIn 0.5s;
}

@keyframes pageFadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Loading skeleton */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>