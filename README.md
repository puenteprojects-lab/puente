# Puente

Веб- и мобильная платформа психологической (психотерапевтической) практики
в Испании.

Веб-часть — единое Laravel-приложение: backend и фронтенд на Vue 3 через
Inertia, без отдельного SPA-сервера и без REST-слоя между ними. Мобильный
клиент на Flutter — следующий этап; он подключится к тому же приложению по
REST API с авторизацией через Sanctum.

## Статус

Ранняя разработка. Развёрнут каркас: стартер-кит, база и окружение. Контент и
доменная логика ещё не наполнены.

## Стек

| Слой              | Технология                                     |
| ----------------- | ---------------------------------------------- |
| Backend           | Laravel 13, PHP 8.4+                           |
| Фронтенд          | Vue 3 + Inertia 3 (в составе Laravel)          |
| UI                | Tailwind CSS 4, shadcn-vue (reka-ui)           |
| Авторизация       | Laravel Fortify (+ passkeys)                   |
| База данных       | PostgreSQL 18                                  |
| Окружение         | Docker через Laravel Sail                      |
| Тесты и качество  | Pest 5, Pint, Larastan, ESLint, Prettier       |
| Мобильный клиент  | Flutter (Android / iOS) — планируется          |

## Структура репозитория

Один Laravel-проект в корне, без разделения на отдельные `app/` и `api/`:

```
puente/
├── app/            # Домен и HTTP-слой Laravel
├── bootstrap/
├── config/
├── database/       # Миграции, фабрики, сиды
├── docs/           # Брендбук и фирменные ассеты
├── lang/           # Переводы интерфейса: en, es, ru, uk, pl
├── mobile/         # Зарезервировано под Flutter-клиент (пока пусто)
├── public/
├── resources/
│   ├── js/         # Vue 3 + Inertia: страницы, компоненты, layouts
│   └── views/      # Корневой Blade-шаблон Inertia
├── routes/
├── tests/          # Pest
└── compose.yaml    # Sail: контейнер приложения + контейнер PostgreSQL
```

## Требования

- Docker Desktop (на Windows — с WSL 2)
- PHP 8.4+ и Composer — для первичной установки зависимостей
- Node.js 20+ и npm

Всё остальное, включая PostgreSQL, поднимается в контейнерах — локально СУБД
ставить не нужно.

## Запуск

Клонировать и установить зависимости:

```bash
git clone https://github.com/puenteprojects-lab/puente.git
cd puente
composer install
npm install
```

Подготовить окружение:

```bash
cp .env.example .env
php artisan key:generate
```

Поднять контейнеры — приложение и PostgreSQL стартуют отдельными сервисами:

```bash
./vendor/bin/sail up -d
```

Накатить миграции:

```bash
./vendor/bin/sail artisan migrate
```

Запустить сборку фронтенда в watch-режиме:

```bash
./vendor/bin/sail npm run dev
```

Приложение будет доступно на `http://localhost`.

Остановить окружение:

```bash
./vendor/bin/sail down
```

### Удобный алиас

Чтобы не писать путь целиком:

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

## Языки

Базовый язык — английский, он отдаётся с корня без префикса. Остальные живут
за своим префиксом, чтобы присланная ссылка всегда открывалась на нужном языке:

| Язык        | Адрес |
| ----------- | ----- |
| English     | `/`   |
| Español     | `/es` |
| Русский     | `/ru` |
| Українська  | `/uk` |
| Polski      | `/pl` |

Список локалей задан в `config/locales.php`, выбор языка — в middleware
`SetLocale` (приоритет: префикс в адресе, затем прошлый выбор в сессии, затем
`Accept-Language`). Тексты лежат в `lang/{locale}/landing.php` и уезжают во
frontend общим пропом Inertia; во Vue их читает `useTranslations()`.

Чтобы добавить язык, заведите папку в `lang/` и допишите строку в
`config/locales.php` — маршруты и переключатель подхватят её сами.

## Админка услуг

Карточки в блоке «С чем я работаю» редактируются через интерфейс, а не в коде.
Админка живёт по адресу `/admin/services` и закрыта флагом `is_admin` у
пользователя (middleware `admin`).

Выдать доступ:

```bash
./vendor/bin/sail artisan puente:make-admin you@example.com
./vendor/bin/sail artisan puente:make-admin you@example.com --revoke
```

Что умеет: создание, правка, удаление, публикация и порядок карточек. Тексты
хранятся по локалям в одной JSON-колонке — форма даёт вкладку на каждый язык.
Обязателен только базовый (английский), остальные можно дописать позже; в
списке такие карточки помечены кодом недостающего языка.

Если в базе нет ни одной опубликованной услуги, лендинг показывает встроенные
тексты из `lang/{locale}/landing.php` — секция не окажется пустой ни при каком
состоянии базы.

Первичное наполнение переносит те же шесть услуг из языковых файлов в базу:

```bash
./vendor/bin/sail artisan db:seed
```

## Разработка

Проверки перед пул-реквестом:

```bash
./vendor/bin/sail test                 # Pest
./vendor/bin/sail pint                 # стиль PHP
./vendor/bin/sail bin phpstan analyse  # статический анализ (Larastan)
npm run lint                           # ESLint с автофиксом
npm run format                         # Prettier
npm run types:check                    # vue-tsc
```

Подключиться к базе в контейнере:

```bash
./vendor/bin/sail psql
```

Продакшн-сборка фронтенда:

```bash
./vendor/bin/sail npm run build
```

## Мобильное приложение

Папка `mobile/` зарезервирована под Flutter-клиент. Он будет отдельным
приложением, работающим с тем же Laravel-бэкендом по REST API через Sanctum.
Разработка начнётся после того, как веб-часть выйдет на рабочий объём.

## Лицензия

> **TODO:** выбрать лицензию и добавить файл `LICENSE`.
