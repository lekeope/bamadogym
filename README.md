# Bamado Gym

A Laravel 13 web application for managing an open-access gym. Handles member onboarding, check-in, billing, and staff admin.

## Stack

- **Framework:** Laravel 13 + PHP 8.3
- **Frontend:** Blade + Vite + Tailwind CSS 4
- **Auth:** Laravel Breeze (session-based)
- **Payments:** Laravel Cashier (Stripe)
- **Database:** SQLite (local) / PostgreSQL (Docker & production)
- **Email:** Laravel Mail (log driver in local, configure Mailgun/Resend/SMTP in prod)
- **Deploy:** Docker (single image: nginx + PHP-FPM + queue + scheduler) + PostgreSQL

## Features

- **Marketing page** — hero, pricing plans, hours, location, and join CTA
- **Member signup** — custom join flow with plan selection
- **Digital waiver** — required before first check-in, timestamped per member
- **Member portal** — membership status, renewal date, payment history, QR check-in code
- **Self check-in** — button tap or QR code scan at entrance (`/checkin/{token}`)
- **Stripe checkout** — online membership renewal via Cashier
- **Manual payment recording** — staff records cash/bank transfer; auto-extends membership
- **Admin dashboard** — today's check-ins, membership stats, overdue list
- **Gym settings** — branding, contact, hours, mail-from, currency, reminder offsets (admin-only; not in `.env`)
- **Member management** — search, filter by status, view history, status overrides
- **Payment reminders** — scheduled daily command; day offsets configurable in Gym Settings
- **Role-based access** — `member`, `staff`, `admin` roles with middleware protection

## Setup (local, without Docker)

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run dev
php artisan serve
```

Default admin: `admin@bamadogym.com` — set a password after seed:
`php artisan tinker` → `User::first()->update(['password' => bcrypt('yourpassword')])`

## Deploy with Docker (PostgreSQL)

Single container image (nginx + PHP-FPM + queue + scheduler). Works with **Coolify Dockerfile** deploys and local Compose.

| Piece | Role |
|-------|------|
| App container | HTTP on port **80** (maps to `8080` locally) |
| `postgres` | PostgreSQL 16 (Compose) / Coolify Postgres resource |
| Queue + scheduler | Supervisord processes inside the app container |

### Coolify (Dockerfile)

1. Build Pack: **Dockerfile**
2. **Ports Exposes** must match `PORT` (Coolify often sets `PORT=3000` — then expose **3000**, not 80)
3. Attach a PostgreSQL database and map `DB_HOST` to the **Coolify Postgres hostname** (never `127.0.0.1` inside the app container), plus `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
4. Set `APP_KEY`, `APP_URL` (your public HTTPS URL), `APP_ENV=production`, `APP_DEBUG=false`
5. Prefer `SESSION_DRIVER=file` and `CACHE_STORE=file` so a bad DB config does not blank every request
6. Optional: `RUN_SEEDERS=true` on first deploy only
7. After first login as admin, open **Admin → Settings** for branding, contact, hours, mail-from, currency (leave Stripe secrets in Coolify env)

The container **does not wait** for Postgres on boot. If the DB is unreachable, the site still starts and shows a **Database offline** page instead of hanging or crashing.

### Local Compose

```bash
cp .env.docker.example .env

# Generate an app key (one-time):
docker run --rm php:8.3-cli php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
# Paste into APP_KEY= in .env

docker compose up -d --build
```

App URL: **http://localhost:8080**

```bash
docker compose logs -f app
docker compose exec app php artisan migrate --force
docker compose down
```

First boot with `RUN_SEEDERS=true` creates plans + admin. Set `RUN_SEEDERS=false` after the first deploy.

## Environment variables

Only foundational / secret values belong in `.env` (or Coolify). Everything else is edited at **Admin → Gym Settings**.

| Variable | Description |
|----------|-------------|
| `APP_KEY` / `APP_URL` / `APP_ENV` / `APP_DEBUG` | App bootstrap |
| `DB_*` | Database connection |
| `SESSION_DRIVER` / `CACHE_STORE` / `QUEUE_CONNECTION` | Drivers |
| `MAIL_MAILER` / `MAIL_HOST` / `MAIL_USERNAME` / `MAIL_PASSWORD` | Mail transport (from address/name are in Settings) |
| `STRIPE_KEY` / `STRIPE_SECRET` / `STRIPE_WEBHOOK_SECRET` | Stripe secrets (currency is in Settings) |
| `RUN_MIGRATIONS` / `RUN_SEEDERS` | Docker boot behaviour |

## Scheduled tasks

In Docker, the app container runs the Laravel scheduler via Supervisord (no host cron needed).

Without Docker, add:

```
* * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
```

## Routes

| Path | Role | Description |
|------|------|-------------|
| `/` | Public | Marketing / landing page |
| `/join` | Public | Member signup |
| `/login` | Public | Login |
| `/waiver` | Auth | Waiver acceptance |
| `/member` | Member | Member portal |
| `/checkin` | Member | Self check-in |
| `/checkin/{token}` | Public | QR token check-in |
| `/admin` | Staff | Admin dashboard |
| `/admin/members` | Staff | Member list |
| `/admin/members/{id}` | Staff | Member detail + payments |

## Phase 2 (planned)

- Freeze / hold requests
- Day passes for non-members
- Attendance and revenue reports
- SMS reminders via Twilio
