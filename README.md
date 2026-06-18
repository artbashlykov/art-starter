# ART Starter

Плагин WordPress для быстрой настройки нового сайта: главная страница-визитка со ссылками (в духе TypeLink) и отдельная настраиваемая страница 404.

**Версия:** 1.0.0  
**Требования:** WordPress 6.0+, PHP 7.4+

**Официальный репозиторий:** [https://github.com/artbashlykov/art-starter](https://github.com/artbashlykov/art-starter)

**Материалы автора:** [https://forge.artbashlykov.ru](https://forge.artbashlykov.ru)

## Возможности

- Мастер первичной настройки сайта
- Главная-визитка: профиль, CTA, ссылки, рекомендация, соцсети
- Семь цветовых шаблонов с превью в админке
- Кастомная страница 404 с изображением, текстом и кнопками
- Изоляция фронтенд-страниц от стилей активной темы

## Установка из репозитория

1. Склонируйте репозиторий в `wp-content/plugins/art-starter`.
2. Активируйте плагин в админке WordPress.
3. Откройте **ART Starter** в меню и настройте главную страницу или 404.

## Обновления (GitHub Releases)

Плагин использует [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker). Для **приватного** репозитория на сайте в `wp-config.php` можно задать:

```php
define( 'ART_STARTER_GITHUB_TOKEN', 'your-github-token' );
```

Для публичного репозитория токен не нужен.

## Лицензия

GPL v2 or later. См. [LICENSE](LICENSE).
