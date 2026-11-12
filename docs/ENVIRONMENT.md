# Environment Configuration — Aster

## Required application settings

| Variable | Default / notes |
|----------|-----------------|
| `APP_NAME` | Working brand (e.g. Aster) |
| `APP_ENV` | `local` / `production` |
| `APP_DEBUG` | `true` only locally |
| `APP_URL` | e.g. `http://chiffon.test` or `http://127.0.0.1:8000` |
| `APP_TIMEZONE` | `Africa/Addis_Ababa` |
| `APP_LOCALE` | `en` |
| `APP_FALLBACK_LOCALE` | `en` |

## Database

| Variable | Dev default |
|----------|-------------|
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | `127.0.0.1` |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `aster` |
| `DB_USERNAME` | `root` |
| `DB_PASSWORD` | empty on typical Laragon/XAMPP |

## Queues / cache / session

Local defaults use `database` drivers (`QUEUE_CONNECTION`, `CACHE_STORE`, `SESSION_DRIVER`). Prefer Redis in production.

## Mail

Local: `MAIL_MAILER=log`. Configure SMTP/API for real email.

## Future gateway placeholders

Do not enable live charges until credentials are configured and tested:

- `CHAPA_*` (future)
- `TELEBIRR_*` (future)

## File storage

`FILESYSTEM_DISK=local` for development; configure S3-compatible disk for production media if needed.
