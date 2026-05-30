# AGENTS.md

## Jamsora Laravel eCommerce (primary app)

The main application lives in **`jamsora/`** — a Laravel CMS + gemstone storefront modeled on https://shop.jamsora.com/ per `docs/Jamsora_SRS.pdf`.

```bash
cd jamsora
composer install
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve --host=0.0.0.0 --port=8000
```

- **Storefront:** http://127.0.0.1:8000  
- **Admin CMS:** http://127.0.0.1:8000/admin — `admin@jamsora.com` / `password`

## Cursor Cloud specific instructions

### Services

| Service | Command |
|---------|---------|
| Laravel app | `cd /workspace/jamsora && php artisan serve --host=0.0.0.0 --port=8000` |

Use a dedicated tmux session (e.g. `jamsora-laravel`). SQLite is the default database; no MySQL required for dev.

### Update script scope

Run `composer install` inside `jamsora/` when dependencies change. Run `npm install && npm run build` when frontend assets change.

### Legacy static HTML

Root-level `*.html` files are an old Git demo; ignore them for Jamsora work unless explicitly requested.
