# AGENTS.md


Guidance for AI agents working in this repository.

## Project overview

This is a **static HTML** Git tutorial/demo repository. There is no backend, no package manager, no build step, and no configured linters or test runners.

| Path | Role |
|------|------|
| `index.html`, `about.html` | Simple “Online Repo Update” placeholders |
| `home.html`, `template.html`, `template2.html` | Branch/PR demo pages |
| `contact.html` | Minimal contact page |
| `landing.html` | Large e-commerce-style mock (“The Concept Key”) |
| `images/` | PNG assets used by some pages |
| `demo.txt` | Plain-text Git demo file |
=======
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


No long-running application services are required. For browser testing and relative asset URLs, serve the repo over HTTP:

```bash
cd /workspace && python3 -m http.server 8080
```

Use a dedicated tmux session (for example `static-http-server`) so the server stays up across agent steps.

### Lint, test, and build

None are configured in this repo. Do not expect `npm test`, ESLint, or a compile step unless tooling is added later.

### Manual verification (hello world)

With the static server on port 8080:

1. Open `http://localhost:8080/landing.html` — confirm “The Concept Key” nav/branding.
2. Click the logo → `index.html` — confirm “Online Repo Update” headings.
3. Open `http://localhost:8080/contact.html` — confirm “Contact Us”.
4. Open `http://localhost:8080/home.html` — confirm “Developer 1 Branch” demo text.

Quick non-GUI check: `curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/index.html` should return `200` for each HTML file.

### Caveats

- `landing.html` references many image/CSS assets that are **not** in `images/`; broken images in the browser are expected.
- Pages work over `file://` for simple text demos, but HTTP is preferred for consistency.
=======
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

