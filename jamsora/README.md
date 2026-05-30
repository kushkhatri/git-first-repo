# Jamsora eCommerce Platform (Laravel CMS)

Custom Laravel rebuild of [shop.jamsora.com](https://shop.jamsora.com/) per the Jamsora SRS & Technical Architecture document.

## Stack

- **Laravel 13** (PHP 8.3+)
- **MySQL / SQLite** (SQLite used by default for local dev)
- **Blade + Tailwind CSS 3 + Alpine.js** (via Laravel Breeze)
- **Vite** for assets

## Features (MVP)

| Area | Capabilities |
|------|----------------|
| **Storefront** | Home, shop with category/search/sort, product detail, cart, checkout (COD + payment placeholders) |
| **CMS / Admin** | Dashboard, products, categories, orders, pages, store settings |
| **Auth** | Breeze login/register; multi-role (`admin`, `customer`, etc.) |
| **Data** | Seeder imports sample products/categories from the live WooCommerce Store API |

## Brand assets

Place your logo in `public/brand/` as `logo-dark.png` (header) and `logo-light.png` (footer).

## Product list (Google Sheets)

1. Download CSV from your sheet (File → Download → CSV)
2. Save as `database/data/products.csv`
3. Run: `php artisan products:import database/data/products.csv --fresh`

## Quick start

```bash
cd jamsora
composer install
cp .env.example .env   # already configured for SQLite
php artisan key:generate
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve
```

Open http://127.0.0.1:8000

### Default accounts

| Email | Password | Role |
|-------|----------|------|
| admin@jamsora.com | password | Admin → `/admin` |
| customer@jamsora.com | password | Customer |

## Architecture

```
Controller → Service → Eloquent Model
```

- `app/Services/CartService.php` — session/guest cart
- `app/Services/OrderService.php` — checkout & order creation
- `app/Services/SettingService.php` — CMS key/value settings

Admin routes are prefixed with `/admin` and protected by the `admin` middleware.

## Reference

- Live store: https://shop.jamsora.com/
- SRS PDF: `../docs/Jamsora_SRS.pdf`

## Roadmap (from SRS)

- Redis queues / Horizon, Meilisearch (Scout), Stripe & Razorpay integration
- Media library uploads, coupons, reviews, blog, newsletters
- REST API v1 for mobile apps
