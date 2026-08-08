# Bamado Gym

Marketing MVP for Bamado Gym — a single landing page to validate interest. **No database, no login, no admin, no Stripe.**

## Stack

- **Framework:** Laravel 13 + PHP 8.3
- **Frontend:** Blade + Vite + Tailwind CSS
- **Deploy:** Docker (nginx + PHP-FPM only)

## What’s live

- Public landing page (`/`) — brand, about, pricing, hours, contact
- CTAs open **WhatsApp** / **mailto** (no online registration)
- Copy + plans come from `config/gym.php` (overridable with `GYM_*` env vars)

## What’s paused (code may still exist unused)

Auth, admin, members, check-in, Stripe, reminders, Postgres, queue, scheduler.

## Setup (local)

```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
npm install && npm run dev
php artisan serve
```

Open **http://localhost:8000**. Edit `config/gym.php` or `GYM_*` in `.env` for real address / WhatsApp number.

## Deploy (Coolify / Docker)

1. Build Pack: **Dockerfile**
2. **Ports Exposes** = `PORT` (Coolify often uses `3000`)
3. Set `APP_KEY`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`
4. Set `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, `DB_CONNECTION=sqlite`
5. Set gym copy: `GYM_NAME`, `GYM_WHATSAPP`, `GYM_PHONE`, `GYM_EMAIL`, `GYM_ADDRESS`, hours, etc.
6. **Detach / delete the Postgres resource** — the app does not need it

Local Compose:

```bash
cp .env.docker.example .env
# set APP_KEY
docker compose up -d --build
```

App: **http://localhost:8080**

## Environment variables

| Variable | Description |
|----------|-------------|
| `APP_KEY` / `APP_URL` / `APP_ENV` / `APP_DEBUG` | App bootstrap |
| `GYM_NAME` / `GYM_TAGLINE` / `GYM_HERO_SUBTITLE` | Branding |
| `GYM_ADDRESS` / `GYM_PHONE` / `GYM_EMAIL` | Contact |
| `GYM_WHATSAPP` | Digits only with country code (e.g. `2348012345678`) |
| `GYM_HOURS_*` | Opening hours |
| `CACHE_CONFIG` | Cache Laravel config/routes/views on boot (default true in prod) |

Plan cards live in `config/gym.php` (`plans` array).

## Routes

| Path | Description |
|------|-------------|
| `/` | Marketing landing page |
| `/up` | Health check |
