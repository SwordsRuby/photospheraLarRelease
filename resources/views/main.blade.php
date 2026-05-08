@extends('layouts.app')

@section('title', 'Фотосфера - Главная')

@section('content')
    <div class="banner-background">
        <div class="particles" id="particlesContainer"></div>
        <div class="banner flex-center flex-column">
            <h1 class="banner-title">
                Обменивайся впечатлениями -<br>храни воспоминания
            </h1>
            <a class="red-button banner-button" href="{{ route('images.index') }}">📸 К изображениям</a>
            <a class="arrow-banner" href="#main-categories-block">
                <img class="my-60 my-mobile-30" src="{{ asset('img/main/arrow-white.svg') }}" alt="arrow">
            </a>
        </div>
    </div>

    <div class="about-section container my-80">
        <div class="about-grid">
            <div class="about-content">
                <div class="about-badge">✨ О нас</div>
                <h2 class="about-title">Фотосфера —<br>ваш идеальный<br><span class="gradient-text">фотопортфолио</span>
                </h2>
                <div class="about-text">
                    <p>Мы создали платформу, где каждый может не просто хранить фотографии, а вдохновляться и вдохновлять
                        других. Фотосфера объединяет тысячи творческих людей со всего мира.</p>
                    <p>Наша миссия — сделать обмен визуальными идеями простым, безопасным и вдохновляющим. Загружайте,
                        организуйте в альбомы, делитесь с друзьями и получайте обратную связь от сообщества.</p>
                </div>
                <div class="about-features">
                    <div class="about-feature">
                        <div class="feature-icon">🚀</div>
                        <div>
                            <h4>Моментальная загрузка</h4>
                            <p>Поддержка всех популярных форматов</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="feature-icon">🔒</div>
                        <div>
                            <h4>Приватные альбомы</h4>
                            <p>Только вы решаете, кто увидит ваши фото</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="feature-icon">🎨</div>
                        <div>
                            <h4>Умные теги</h4>
                            <p>Легкий поиск по категориям и тегам</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="feature-icon">💾</div>
                        <div>
                            <h4>Облачное хранилище</h4>
                            <p>До 50 ГБ для ваших воспоминаний</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('images.create') }}" class="red-button about-button">
                    Начать загружать
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="about-image">
                <div class="about-image-wrapper">
                    <img src="{{ asset('img/main/photo-image.png') }}" alt="Фотосфера">
                    <div class="floating-card card-1">
                        <span>📸</span>
                        <p>фото</p>
                    </div>
                    <div class="floating-card card-2">
                        <span>❤️</span>
                        <p>лайки</p>
                    </div>
                    <div class="floating-card card-3">
                        <span>👥</span>
                        <p>онлайн</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="main-categories-block" class="container my-80 flex-column">
        <div class="flex-column main-title-block-right">
            <h2 class="main-title">Категории ИЗОБРАЖЕНИЙ</h2>
            <div class="main-line"></div>
            <p class="section-subtitle mt-10">Исследуйте мир через объектив нашего сообщества</p>
        </div>

        <div class="flex-space-between my-40 categories-block">
            <button onclick="prevButton();" class="black-button slider-button-prev flex-center">
                <img class="slider-button-img-prev" src="{{ asset('img/main/arrow-white.svg') }}" alt="prevButton">
            </button>

            <div class="slider-wrapper-block">
                <div class="slider-block gap-block-20px">
                    @foreach ($categories as $category)
                        <a href="{{ route('images.index', ['category' => $category->id]) }}" class="slider-card">
                            <img src="{{ $category->img }}" alt="{{ $category->name }}"
                                class="slider-img main-category-img">
                            <div class="slider-card-subblock">
                                <h2 class="slider-title">{{ $category->name }}</h2>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <button onclick="nextButton();" class="black-button slider-button-next flex-center">
                <img class="slider-button-img-next" src="{{ asset('img/main/arrow-white.svg') }}" alt="nextButton">
            </button>
        </div>
    </div>

    <div class="width-100 overflow-hidden">
        <div class="cta-banner my-80">
            <div class="container">
                <div class="cta-content">
                    <h2>Готовы поделиться своими работами?</h2>
                    <p>Присоединяйтесь к сообществу Фотосферы уже сегодня</p>
                    <a href="{{ route('images.create') }}" class="red-button cta-button">Начать загружать</a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-column container my-80">
        <div class="flex-column main-title-block">
            <h2 class="main-title">ЧАСТЫЕ ВОПРОСЫ</h2>
            <div class="main-line"></div>
            <p class="section-subtitle mt-10" style="text-align: left;">Ответы на самые популярные вопросы о сервисе</p>
        </div>

        <div class="my-40 faq-block-column gap-block-20px">
            <div tabindex="1" onfocus="faq(this);" onblur="faq(this);" class="faq-block">
                <div class="faq-card-block">
                    <div class="faq-card flex-space-between">
                        <h2>Можно ли скачивать изображения?</h2>
                        <img src="{{ asset('img/main/arrow-white.svg') }}" alt="">
                    </div>
                    <p class="faq-close">
                        Да, на нашем сайте вы можете скачивать изображения - нужно
                        авторизироваться, а затем перейти на страницу конкретного
                        изображения и нажать кнопку скачать или нажать на изображение.
                    </p>
                </div>
            </div>

            <div tabindex="2" onfocus="faq(this);" onblur="faq(this);" class="faq-block">
                <div class="faq-card-block">
                    <div class="faq-card flex-space-between">
                        <h2>Как искать изображения?</h2>
                        <img src="{{ asset('img/main/arrow-white.svg') }}" alt="">
                    </div>
                    <p>
                        Вы можете искать изображения, используя строку поиска в верхней
                        части страницы. Введите ключевые слова, связанные с тем, что вы
                        ищете, или воспользуйтесь фильтрами по категориям, чтобы сузить
                        результаты.
                    </p>
                </div>
            </div>

            <div tabindex="3" onfocus="faq(this);" onblur="faq(this);" class="faq-block">
                <div class="faq-card-block">
                    <div class="faq-card flex-space-between">
                        <h2>Безопасна ли регистрация на сайте?</h2>
                        <img src="{{ asset('img/main/arrow-white.svg') }}" alt="">
                    </div>
                    <p>
                        Да, регистрация на нашем сайте безопасна. Мы используем
                        современные технологии шифрования для защиты ваших данных. Ваши
                        личные данные не будут переданы третьим лицам.
                    </p>
                </div>
            </div>

            <div tabindex="4" onfocus="faq(this);" onblur="faq(this);" class="faq-block">
                <div class="faq-card-block">
                    <div class="faq-card flex-space-between">
                        <h2>Куда обратиться если возникла проблема?</h2>
                        <img src="{{ asset('img/main/arrow-white.svg') }}" alt="">
                    </div>
                    <p>
                        Если у вас возникли проблемы, вы можете обратиться в нашу службу
                        поддержки через социальные сети или другие контакты указанные в
                        подвале сайта. Мы постараемся ответить на ваш запрос как можно
                        быстрее.
                    </p>
                </div>
            </div>

            <div tabindex="5" onfocus="faq(this);" onblur="faq(this);" class="faq-block">
                <div class="faq-card-block">
                    <div class="faq-card flex-space-between">
                        <h2>Как загрузить свое изображение?</h2>
                        <img src="{{ asset('img/main/arrow-white.svg') }}" alt="">
                    </div>
                    <p>
                        Чтобы загрузить свое изображение, вам нужно авторизироваться и
                        перейти в раздел "Добавление изображения" через соответствующую
                        иконку в шапке сайта. Далее заполнить поля для добавления
                        изображения и нажать кнопку "Добавить".
                    </p>
                </div>
            </div>

            <div tabindex="6" onfocus="faq(this);" onblur="faq(this);" class="faq-block">
                <div class="faq-card-block">
                    <div class="faq-card flex-space-between">
                        <h2>Что если не нашел нужную категорию?</h2>
                        <img src="{{ asset('img/main/arrow-white.svg') }}" alt="">
                    </div>
                    <p>
                        Если вы не нашли нужную категорию, вы можете воспользоваться
                        функцией поиска или предложить новую категорию через форму
                        обратной связи. Мы всегда рады вашим предложениям и стремимся
                        улучшить наш сервис!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            text-align: center;
        }

        .stat-card {
            padding: 32px 24px;
            background: white;
            border-radius: 28px;
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .stat-number {
            font-size: 42px;
            font-weight: 700;
            color: var(--main-color-violet);
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            color: #888;
            letter-spacing: 1px;
        }

        /* About section - NEW DESIGN */
        .about-section {
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
            border-radius: 48px;
            padding: 60px;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .about-badge {
            display: inline-block;
            background: rgba(99, 102, 241, 0.1);
            color: var(--main-color-violet);
            padding: 6px 16px;
            border-radius: 40px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .about-title {
            font-size: 48px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 24px;
            color: var(--main-color-black);
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--main-color-violet), #a5b4fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .about-text p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .about-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 32px 0;
        }

        .about-feature {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .feature-icon {
            font-size: 28px;
        }

        .about-feature h4 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .about-feature p {
            font-size: 12px;
            color: #888;
            margin: 0;
        }

        .about-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .about-image-wrapper {
            position: relative;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .about-image-wrapper img {
            width: 100%;
            height: auto;
            display: block;
        }

        .floating-card {
            position: absolute;
            background: white;
            padding: 12px 20px;
            border-radius: 60px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-md);
            animation: float 3s ease-in-out infinite;
        }

        .floating-card span {
            font-size: 24px;
        }

        .floating-card p {
            font-size: 13px;
            font-weight: 500;
            margin: 0;
        }

        .card-1 {
            top: 20px;
            right: -20px;
            animation-delay: 0s;
        }

        .card-2 {
            bottom: 60px;
            left: -20px;
            animation-delay: 0.5s;
        }

        .card-3 {
            bottom: 120px;
            right: -15px;
            animation-delay: 1s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* CTA Banner */
        .cta-banner {
            position: relative;
            text-align: center;
            overflow: visible;
            z-index: 1;
            padding: 90px 0;
            background: transparent;
        }

        .cta-banner::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 0;
            width: 120%;
            height: calc(100% + 50px);
            background: linear-gradient(135deg, var(--main-color-black), #2a2a2a);
            transform: skewY(-3deg);
            transform-origin: top left;
            z-index: -1;
        }

        .overflow-hidden {
            overflow: hidden;
        }

        .cta-content h2 {
            font-size: 36px;
            font-weight: 700;
            color: white;
            margin-bottom: 16px;
        }

        .cta-content p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 32px;
        }

        .cta-button {
            padding: 16px 48px;
            font-size: 18px;
        }

        .section-subtitle {
            color: #888;
            font-size: 15px;
        }

        .text-center {
            text-align: center;
        }

        .my-80 {
            margin-top: 80px;
            margin-bottom: 80px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .about-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .about-title {
                font-size: 36px;
            }

            .about-section {
                padding: 40px 24px;
            }

            .cta-banner {
                padding: 40px 24px;
            }

            .cta-content h2 {
                font-size: 28px;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                gap: 16px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-number {
                font-size: 28px;
            }

            .about-features {
                grid-template-columns: 1fr;
            }

            .floating-card {
                display: none;
            }

            .about-title {
                font-size: 28px;
            }

            .my-80 {
                margin-top: 48px;
                margin-bottom: 48px;
            }
        }

        .stat-number {
            display: inline-block;
        }
    </style>

    <script>
        // Animated counter for stats
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');

            const animateNumber = (element) => {
                const target = parseInt(element.getAttribute('data-count'));
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        element.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current).toLocaleString();
                    }
                }, 30);
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateNumber(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            statNumbers.forEach(num => observer.observe(num));
        });

        function createParticles() {
            const container = document.getElementById('particlesContainer');
            if (!container) return;

            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                const size = Math.random() * 8 + 2;
                const duration = Math.random() * 15 + 8;
                const delay = Math.random() * 10;
                const left = Math.random() * 100;

                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${left}%`;
                particle.style.animationDuration = `${duration}s`;
                particle.style.animationDelay = `${delay}s`;

                container.appendChild(particle);
            }
        }

        document.addEventListener('DOMContentLoaded', createParticles);
    </script>
@endsection
