# git-first-repo

This repository contains:

| Path | Description |
|------|-------------|
| **`jamsora/`** | **Jamsora Laravel eCommerce + CMS** (primary application) — see [jamsora/README.md](jamsora/README.md) |
| `docs/` | Jamsora SRS technical architecture PDF |
| `*.html` | Legacy static HTML Git tutorial files |

## Run Jamsora locally

```bash
cd jamsora
composer install
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve
```

Admin: http://127.0.0.1:8000/admin — `admin@jamsora.com` / `password`
