<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database unavailable — Bamado Gym</title>
    <style>
        :root { color-scheme: dark; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, sans-serif;
            background: #09090b;
            color: #fafafa;
            padding: 24px;
        }
        .card {
            max-width: 34rem;
            width: 100%;
            background: #18181b;
            border: 1px solid #3f3f46;
            border-radius: 1rem;
            padding: 2rem;
        }
        h1 { margin: 0 0 0.75rem; font-size: 1.5rem; }
        p { margin: 0 0 1rem; color: #a1a1aa; line-height: 1.5; }
        code {
            display: block;
            background: #09090b;
            border: 1px solid #27272a;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            color: #fbbf24;
            font-size: 0.85rem;
            overflow-wrap: anywhere;
            margin-bottom: 1rem;
        }
        ul { margin: 0; padding-left: 1.25rem; color: #d4d4d8; line-height: 1.6; }
        .badge {
            display: inline-block;
            background: #7c2d12;
            color: #fdba74;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 0.25rem 0.5rem;
            border-radius: 999px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">Database offline</div>
        <h1>Can’t reach PostgreSQL</h1>
        <p>
            The app is running, but it could not connect to the database.
            Fix the database URL in Coolify and restart — do not use
            <strong>127.0.0.1</strong> as the host inside a container.
        </p>
        @if(! empty($message))
            <code>{{ $message }}</code>
        @endif
        <ul>
            <li><strong>DB_CONNECTION</strong> = pgsql</li>
            <li><strong>DB_URL</strong> = <code style="display:inline;padding:0.1rem 0.35rem;margin:0;">postgres://USER:PASSWORD@HOST:5432/DATABASE</code> (Coolify internal URL)</li>
            <li>Remove separate <strong>DB_HOST</strong> / <strong>DB_USERNAME</strong> / <strong>DB_PASSWORD</strong> if they disagree with the URL</li>
            <li>Prefer <strong>SESSION_DRIVER=file</strong> until the DB is healthy</li>
        </ul>
    </div>
</body>
</html>
