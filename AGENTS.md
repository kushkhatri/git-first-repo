# AGENTS.md

## Jamsora Laravel eCommerce (primary app)

The main application is in **`jamsora/`** — Laravel CMS + gemstone storefront ([shop.jamsora.com](https://shop.jamsora.com/) reference).

```bash
cd jamsora
composer install
php artisan migrate --force
php artisan db:seed --force
npm install && npm run build
php artisan serve --host=0.0.0.0 --port=8000
```

| URL | Purpose |
|-----|---------|
| http://127.0.0.1:8000 | Storefront |
| http://127.0.0.1:8000/admin | CMS (`admin@jamsora.com` / `password`) |

### Brand assets

| Path | Use |
|------|-----|
| `jamsora/public/brand/logo-dark.png` | Header (light background) |
| `jamsora/public/brand/logo-light.png` | Footer (dark background) |

SVG fallbacks exist if PNGs are missing.

### Product catalog

1. **Your spreadsheet (preferred):** save Google Sheets export as `jamsora/database/data/products.csv`, then:
   ```bash
   php artisan products:import database/data/products.csv --fresh
   ```
2. **Reference sync** (if CSV missing or API reachable):
   ```bash
   php artisan products:sync-reference --limit=50
   ```

The seeder imports `products.csv` when valid; otherwise runs `products:sync-reference` with fallback demos if the API times out.

## Cursor Cloud specific instructions

### Services

| Service | Command |
|---------|---------|
| Laravel | `cd /workspace/jamsora && php artisan serve --host=0.0.0.0 --port=8000` |

Use tmux session `jamsora-laravel`. SQLite is default (`database/database.sqlite`).

### VM update script

```bash
cd /workspace/jamsora
composer install --no-interaction
npm ci
npm run build
php artisan migrate --force
```

Do **not** run `db:seed` on every boot (slow / hits external API). Seed manually when resetting data.

### Desktop uploads

If the user uploads `products.csv` or logo PNGs via the Desktop pane, confirm files exist at:

- `jamsora/database/data/products.csv`
- `jamsora/public/brand/logo-dark.png`
- `jamsora/public/brand/logo-light.png`

Then run `products:import` or refresh the browser. Invalid CSV (HTML login pages) is rejected by the importer.

### Legacy static HTML

Root `*.html` files are an old Git demo — ignore for Jamsora work.

### E-commerce features (stone categories, IGI, import)

- Stone category pages: `/diamonds`, `/sapphire`, etc. (`php artisan stones:seed-categories`)
- Full CSV import: `php artisan products:import database/data/products.csv` (see `database/data/products.csv.example`)
- IGI add-on: `config/jamsora.php` — fee and extra delivery days; flows through cart, checkout, orders, admin
- Admin certifications: `/admin/certifications`
- Customer orders: `/account/orders` (login required)
