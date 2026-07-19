# Фотосфера

![Photosphera Logo](public/img/logo/png/logo.png)

Веб-приложение для публикации, хранения и управления фотографиями. Пользователи могут загружать изображения, создавать альбомы, ставить лайки и добавлять фотографии в избранное. Для расширения доступного хранилища предусмотрена система платных подписок с оплатой через YooKassa.

## Возможности

### Пользовательская часть

- регистрация и авторизация пользователей;
- подтверждение электронной почты шестизначным кодом;
- повторная отправка кода подтверждения;
- профиль пользователя;
- загрузка и редактирование фотографий;
- удаление собственных фотографий;
- категории и теги;
- лайки;
- избранные фотографии;
- приватные изображения;
- создание и управление альбомами;
- добавление и удаление изображений из альбомов;
- публичная ссылка на альбом;
- просмотр общедоступных изображений;
- скачивание изображений;
- автоматическое наложение водяного знака для неавторизованных пользователей;
- управление доступным дисковым пространством;
- платные подписки;
- оплата подписки через YooKassa.

### Административная часть

Администратор или модератор может:

- просматривать загруженные изображения;
- одобрять изображения;
- удалять изображения;
- просматривать пользователей;
- блокировать и разблокировать пользователей;
- указывать причину блокировки;
- создавать и удалять модераторов;
- подтверждать модераторов;
- управлять категориями;
- управлять тегами.

## Тарифы

| Тариф | Стоимость | Хранилище | Дополнительные возможности |
|---|---:|---:|---|
| Базовый | Бесплатно | 1 ГБ | Приватные альбомы |
| Pro | 299 ₽/мес. | 10 ГБ | Приватные альбомы, приоритетная поддержка |
| Premium | 599 ₽/мес. | 50 ГБ | Приватные альбомы, приоритетная поддержка |

> Стоимость и параметры тарифов задаются в `app/Models/Subscription.php`.

## Технологический стек

### Backend

- PHP 8.2+
- Laravel 12
- Laravel Blade
- Eloquent ORM
- SQLite по умолчанию
- MySQL/MariaDB могут быть использованы при соответствующей настройке окружения

### Frontend

- Blade Templates
- JavaScript

### Дополнительные библиотеки

- `intervention/image` — обработка изображений и водяные знаки;
- `yoomoney/yookassa-sdk-php` — интеграция с YooKassa;

## Требования

Перед установкой убедитесь, что установлены:

- PHP >= 8.2;
- Composer;
- Node.js и npm;
- SQLite или другая поддерживаемая база данных;
- PHP GD extension — требуется для обработки изображений через Intervention Image.

Проверить версии:

```bash
php -v
composer -V
node -v
npm -v
```

## Установка

### 1. Клонирование проекта

```bash
git clone <repository-url>
cd photospheraLarRelease-main
```

Если проект получен в виде архива, распакуйте его и перейдите в директорию проекта:

```bash
cd photospheraLarRelease-main
```

### 2. Установка PHP-зависимостей

```bash
composer install
```

### 3. Установка frontend-зависимостей

```bash
npm install
```

### 4. Настройка окружения

Создайте файл `.env`:

```bash
cp .env.example .env
```

Для Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Сгенерируйте ключ приложения:

```bash
php artisan key:generate
```

## Настройка базы данных

По умолчанию проект использует SQLite:

```env
DB_CONNECTION=sqlite
```

Создайте файл базы данных, если он отсутствует:

```bash
touch database/database.sqlite
```

Для Windows можно создать пустой файл `database/database.sqlite` вручную.

После этого выполните миграции:

```bash
php artisan migrate
```

Для заполнения базы тестовыми данными:

```bash
php artisan db:seed
```

Или:

```bash
php artisan migrate:fresh --seed
```

> Команда `migrate:fresh --seed` удаляет существующие таблицы и создаёт базу заново. Не используйте её на production-базе без необходимости.

## Настройка хранилища

Для работы с публичными файлами создайте символическую ссылку:

```bash
php artisan storage:link
```

Это связывает:

```text
storage/app/public
```

с:

```text
public/storage
```

Загруженные изображения и автоматически созданные версии с водяным знаком могут храниться в публичном storage.

## Настройка почты

Для регистрации и подтверждения электронной почты необходимо настроить SMTP.

Пример:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

Для Gmail рекомендуется использовать **App Password**, а не обычный пароль от аккаунта.

Для локального тестирования можно использовать:

```env
MAIL_MAILER=log
```

В этом случае письма не отправляются реально, а записываются в Laravel-логи.

## Настройка YooKassa

Для работы платных подписок необходимо указать параметры магазина:

```env
YOOKASSA_SHOP_ID=your_shop_id
YOOKASSA_SECRET_KEY=your_secret_key
YOOKASSA_TEST_MODE=true
```

Основная логика работы с YooKassa находится в:

```text
app/Services/YooKassaService.php
```

Процесс оплаты:

1. пользователь выбирает тариф;
2. создаётся платёж в YooKassa;
3. платёж сохраняется в базе данных;
4. пользователь перенаправляется на страницу оплаты;
5. после оплаты проверяется статус платежа;
6. при успешной оплате активируется подписка;
7. пользователю устанавливается лимит хранилища согласно выбранному тарифу.

## Запуск проекта

### Запуск Laravel

```bash
php artisan serve
```

После запуска приложение будет доступно по адресу:

```text
http://127.0.0.1:8000
```

### Запуск frontend-сборки

В режиме разработки:

```bash
npm run dev
```

Для production-сборки:

```bash
npm run build
```

## Запуск всех сервисов разработки

В `composer.json` предусмотрен скрипт:

```bash
composer run dev
```

Он запускает:

- Laravel development server;
- обработчик очередей;
- Laravel Pail для просмотра логов;
- Vite development server.

## Структура проекта

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── AlbumController.php
│   │   ├── AuthController.php
│   │   ├── DownloadController.php
│   │   ├── ImageController.php
│   │   ├── MainController.php
│   │   ├── SubscriptionController.php
│   │   └── UserController.php
│   ├── Middleware/
│   │   └── AdminMiddleware.php
│   └── Requests/
├── Mail/
│   └── VerificationCodeMail.php
├── Models/
│   ├── Album.php
│   ├── AlbumImage.php
│   ├── Category.php
│   ├── Favorite.php
│   ├── Image.php
│   ├── Like.php
│   ├── Payment.php
│   ├── Subscription.php
│   ├── Tag.php
│   └── User.php
└── Services/
    ├── WatermarkService.php
    └── YooKassaService.php

database/
├── factories/
├── migrations/
└── seeders/

public/
├── css/
├── docs/
├── fonts/
├── img/
├── js/
└── index.php

resources/
├── css/
├── js/
└── views/
    ├── admin/
    ├── auth/
    ├── components/
    ├── emails/
    ├── errors/
    ├── images/
    ├── layouts/
    ├── subscription/
    └── user/

routes/
└── web.php
```

## Основные маршруты

### Главная страница

```text
GET /
```

### Аутентификация

```text
POST /login
POST /register
POST /logout
GET  /verification/{userId}
POST /verification/{userId}/verify
POST /verification/{userId}/resend
```

### Изображения

```text
GET    /images
GET    /images/{id}/show
GET    /images/create
POST   /images/create
GET    /images/{id}/edit
PUT    /images/{id}
DELETE /images/{id}
POST   /images/{id}/like
POST   /images/{id}/favorite
GET    /images/download/{id}
```

### Пользователь

```text
GET    /user/favorites
GET    /user/added
GET    /user/private
GET    /user/storage
POST   /user/profile
DELETE /user/account
```

### Альбомы

```text
GET    /user/albums
GET    /user/albums/create
POST   /user/albums/create
GET    /user/albums/{id}
GET    /user/albums/{id}/edit
PUT    /user/albums/{id}
DELETE /user/albums/{id}
POST   /user/albums/{id}/add-image
DELETE /user/albums/{albumId}/remove-image/{imageId}
```

### Общий доступ к альбомам

```text
POST /user/albums/{id}/share/generate
POST /user/albums/{id}/share/disable
POST /user/albums/{id}/share/regenerate

GET /share/album/{token}
GET /share/album/{token}/image/{imageId}
```

### Подписки

```text
GET  /subscription/plans
GET  /subscription
GET  /subscription/checkout/{plan}
POST /subscription/process
GET  /subscription/success
GET  /subscription/check-payment/{paymentId}
POST /subscription/cancel
```

### Административная панель

Все административные маршруты защищены middleware:

```text
auth
admin
```

Основной префикс:

```text
/admin
```

Доступны операции управления:

- изображениями;
- пользователями;
- модераторами;
- категориями;
- тегами.

## Система водяных знаков

Для защиты оригинальных изображений используется `WatermarkService`.

Логика:

- авторизованный пользователь может скачать оригинал;
- неавторизованный пользователь получает версию изображения с водяным знаком;
- готовые версии кэшируются в:

```text
storage/app/public/watermarked
```

Водяной знак:

```text
© Фотосфера
```

Для работы с текстом водяного знака используется шрифт:

```text
public/fonts/arial.ttf
```

## Модель данных

Основные сущности приложения:

- `User` — пользователь;
- `Image` — изображение;
- `Category` — категория;
- `Tag` — тег;
- `Album` — альбом;
- `Favorite` — избранные изображения;
- `Like` — лайки;
- `Subscription` — подписка;
- `Payment` — платёж.

Основные связи:

```text
User
 ├── Images
 ├── Albums
 ├── Favorites
 ├── Likes
 ├── Subscriptions
 └── Payments

Image
 ├── Category
 ├── Author
 ├── Tags
 ├── Likes
 ├── Favorites
 └── Albums

Album
 └── Images
```

## Логи

Laravel-логи находятся в:

```text
storage/logs/laravel.log
```

Для просмотра логов в режиме разработки можно использовать:

```bash
php artisan pail
```

## Очистка кэша

При изменении конфигурации рекомендуется выполнить:

```bash
php artisan optimize:clear
```

При необходимости отдельные команды:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Production

Перед публикацией проекта в production:

1. установите зависимости без dev-пакетов:

```bash
composer install --no-dev --optimize-autoloader
```

2. соберите frontend:

```bash
npm run build
```

3. установите production-значения в `.env`:

```env
APP_ENV=production
APP_DEBUG=false
```

4. укажите корректные настройки базы данных;

5. настройте SMTP;

6. настройте YooKassa;

7. выполните миграции:

```bash
php artisan migrate --force
```

8. создайте storage-ссылку:

```bash
php artisan storage:link
```

9. очистите и закэшируйте конфигурацию:

```bash
php artisan optimize
```

> В production корень веб-сервера должен указывать на директорию `public`.