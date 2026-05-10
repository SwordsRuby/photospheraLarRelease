@php
    $footerClass = isset($footer_flag) && !$footer_flag ? '' : 'my-120';
@endphp

<div class="black-block {{ $footerClass }} footer-block">
    <footer class="flex-column container gap-block-10px">
        <div class="footer-grid">
            <div class="footer-column">
                <div class="footer-logo">
                    <img src="{{ asset('img/logo/png/logo.png') }}" alt="Фотосфера" style="height: 40px;">
                </div>
                <p class="footer-description">
                    Фотосфера — платформа для обмена изображениями, где каждый может найти вдохновение и поделиться своими работами.
                </p>
                <div class="footer-social">
                    <a class="link-social-media flex-center max-link" href="https://web.max.ru/" target="_blank" rel="noopener noreferrer">
                        <img src="{{ asset('img/footer/max.svg') }}" alt="Max">
                    </a>
                    <a class="link-social-media flex-center" href="https://web.telegram.org/a/" target="_blank" rel="noopener noreferrer">
                        <img src="{{ asset('img/footer/telegram.svg') }}" alt="Telegram">
                    </a>
                    <a class="link-social-media flex-center" href="https://vk.com/" target="_blank" rel="noopener noreferrer">
                        <img src="{{ asset('img/footer/wk.svg') }}" alt="VK">
                    </a>
                </div>
            </div>

            <div class="footer-column">
                <h3 class="footer-title">Навигация</h3>
                <ul class="footer-links">
                    <li><a href="/" class="link-white red">Главная</a></li>
                    <li><a href="{{ route('images.index') }}" class="link-white red">Изображения</a></li>
                    @if(Auth::check())
                        <li><a href="{{ route('images.create') }}" class="link-white red">Добавить изображение</a></li>
                        <li><a href="{{ route('user.favorites') }}" class="link-white red">Избранное</a></li>
                    @endif
                </ul>
            </div>

            <div class="footer-column">
                <h3 class="footer-title">Документы</h3>
                <ul class="footer-links">
                    <li><a href="{{ asset('docs/privacyPolicy.pdf') }}" class="link-white red" target="_blank">Политика конфиденциальности</a></li>
                    <li><a href="{{ asset('docs/termsOfUse.pdf') }}" class="link-white red" target="_blank">Условия использования</a></li>
                    <li><a href="{{ asset('docs/legalInformation.pdf') }}" class="link-white red" target="_blank">Правовая информация</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3 class="footer-title">Контакты</h3>
                <ul class="footer-links footer-contacts">
                    <li>
                        <span class="contact-icon">📧</span>
                        <a href="mailto:photoSpheraRu@gmail.com" class="link-white-underline red">photoSpheraRu@gmail.com</a>
                    </li>
                    <li>
                        <span class="contact-icon">📞</span>
                        <a href="tel:+79384567890" class="link-white-underline red">8 (938) 456 78-90</a>
                    </li>
                </ul>
                @if(Auth::check() && Auth::user()->is_moderator)
                    <a href="{{ route('admin.images') }}" class="admin-footer-link red-button" style="margin-top: 20px; display: inline-block; padding: 8px 20px; font-size: 13px;">
                        🛡️ Админ панель
                    </a>
                @endif
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-copyright">
            <p>© Фотосфера {{ date('Y') }}</p>
            <p class="footer-rights">Все права защищены</p>
        </div>
    </footer>
</div>

<style>
.footer-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1.5fr;
    gap: 40px;
    margin-bottom: 40px;
    padding-bottom: 40px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.footer-column {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.footer-logo {
    margin-bottom: 8px;
}

.footer-description {
    color: rgba(255,255,255,0.7);
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 16px;
}

.footer-social {
    display: flex;
    gap: 12px;
}

.footer-social .link-social-media {
    background: rgba(255,255,255,0.1);
    transition: var(--transition-smooth);
}

.footer-social .link-social-media:hover {
    background: var(--main-color-violet);
    transform: translateY(-3px);
}

.footer-title {
    color: white;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.footer-links {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.footer-links li {
    list-style: none;
}

.footer-links a {
    font-size: 13px;
    transition: var(--transition-smooth);
    opacity: 0.7;
}

.footer-links a:hover {
    opacity: 1;
    color: var(--main-color-violet);
}

.footer-contacts li {
    display: flex;
    align-items: center;
    gap: 8px;
}

.contact-icon {
    font-size: 16px;
    opacity: 0.7;
}

.admin-footer-link {
    text-align: center;
    width: 100%;
}

.footer-copyright {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    padding-top: 20px;
}

.footer-copyright p {
    color: rgba(255,255,255,0.5);
    font-size: 12px;
}

.footer-rights {
    font-size: 11px;
}

@media (max-width: 992px) {
    .footer-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }
}

@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
        gap: 30px;
        text-align: center;
    }
    
    .footer-column {
        align-items: center;
    }
    
    .footer-social {
        justify-content: center;
    }
    
    .footer-contacts li {
        justify-content: center;
    }
    
    .footer-copyright {
        flex-direction: column;
        text-align: center;
    }
    
    .footer-block {
        margin-top: 40px;
    }
}

.error-page .footer-block {
    position: absolute;
    bottom: 0;
    width: 100%;
}
</style>