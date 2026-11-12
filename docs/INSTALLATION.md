# Installation Guide — Aster

## Prerequisites

- PHP 8.2+ (8.3/8.4 recommended)
- Composer 2
- Node.js 20+ and npm
- MySQL 8 or MariaDB 10.4+
- Laragon / XAMPP / equivalent local stack

## Steps

```bash
cd c:\laragon\www\Chiffon
composer install
copy .env.example .env   # if needed
php artisan key:generate

# Create database `aster` in MySQL, then configure .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=aster
# DB_USERNAME=root
# DB_PASSWORD=

php artisan migrate --seed
npm install
npm run build
# or for development:
composer run dev
```

## Demo owner (development only)

Seeded by `DatabaseSeeder` — see `DEVELOPMENT_PROGRESS.md` / seeder comments. Change password before any shared environment.

## Storage

```bash
php artisan storage:link
```
