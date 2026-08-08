# Bamado Gym

Static marketing site (`site/`) to validate gym interest. **No PHP, no database, no app container you maintain.**

Laravel code remains in the repo for later, but Coolify should serve **`site/` only**.

## Local preview

Open `site/index.html` in a browser, or:

```bash
cd site && python3 -m http.server 8080
```

Edit phone / WhatsApp / address / prices directly in `site/index.html`.

## Coolify (kill the Dockerfile app)

1. Open the existing app (or create a new one from the same GitHub repo).
2. **Build Pack → Static** (not Dockerfile / Nixpacks).
3. **Base Directory → `/site`**
4. Web server: **Nginx** (Coolify default).
5. Set your domain.
6. **Deploy**.
7. Optional cleanup:
   - Delete / detach the old **Postgres** resource
   - Remove old env vars (`APP_KEY`, `DB_*`, Stripe, etc.) — unused for static
   - Stop/delete the old Dockerfile-based service if you created a new Static app

Coolify wraps `site/` in a tiny Nginx image. You are not running Laravel.

Docs: [Coolify Static build pack](https://coolify.io/docs/applications/build-packs/static)

## Files that matter

| Path | Role |
|------|------|
| `site/index.html` | Landing page copy + WhatsApp links |
| `site/styles.css` | Styles |

## Parked for later

Everything outside `site/` (Laravel, Docker PHP image, auth, admin, etc.) is unused while Coolify points at Static + `/site`.
