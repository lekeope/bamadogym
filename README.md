# Bamado Gym

A Laravel 13 web application for managing an open-access gym. Handles member onboarding, check-in, billing, and staff admin.

## Stack

- **Framework:** Laravel 13 + PHP 8.3
- **Frontend:** Blade + Vite + Tailwind CSS 4
- **Auth:** Laravel Breeze (session-based)
- **Payments:** Laravel Cashier (Stripe)
- **Database:** SQLite (dev) / MySQL or PostgreSQL (prod)
- **Email:** Laravel Mail (log driver in dev, configure Mailgun/Resend in prod)

## Features

- **Marketing page** — hero, pricing plans, hours, location, and join CTA
- **Member signup** — custom join flow with plan selection
- **Digital waiver** — required before first check-in, timestamped per member
- **Member portal** — membership status, renewal date, payment history, QR check-in code
- **Self check-in** — button tap or QR code scan at entrance (`/checkin/{token}`)
- **Stripe checkout** — online membership renewal via Cashier
- **Manual payment recording** — staff records cash/bank transfer; auto-extends membership
- **Admin dashboard** — today's check-ins, membership stats, overdue list
- **Member management** — search, filter by status, view history, status overrides
- **Payment reminders** — scheduled daily command sends emails at 7-day, due-day, 3-day and 7-day overdue milestones
- **Role-based access** — `member`, `staff`, `admin` roles with middleware protection

## Setup

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run dev
php artisan serve
```

Default admin: `admin@bamadogym.com` / set a password via `php artisan tinker` → `User::first()->update(['password' => bcrypt('yourpassword')])`

## Environment variables

Copy `.env.example` to `.env` and fill in:

| Variable | Description |
|----------|-------------|
| `STRIPE_KEY` | Stripe publishable key |
| `STRIPE_SECRET` | Stripe secret key |
| `STRIPE_WEBHOOK_SECRET` | From `stripe listen --forward-to` or Dashboard |
| `MAIL_MAILER` | `log` (dev), `mailgun`/`resend` (prod) |

## Scheduled tasks

The payment reminder command runs daily at 08:00. Set up cron on your server:

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
