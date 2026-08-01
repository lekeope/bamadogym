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
            max-width: 36rem;
            width: 100%;
            background: #18181b;
            border: 1px solid #3f3f46;
            border-radius: 1rem;
            padding: 2rem;
        }
        h1 { margin: 0 0 0.5rem; font-size: 1.5rem; }
        .lead { margin: 0 0 1.25rem; color: #a1a1aa; line-height: 1.5; }
        .cause {
            margin: 0 0 1.25rem;
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            background: #451a03;
            border: 1px solid #9a3412;
            color: #fdba74;
            font-weight: 600;
            line-height: 1.4;
        }
        .meta {
            display: grid;
            gap: 0.5rem;
            margin: 0 0 1.25rem;
            padding: 0.85rem 1rem;
            background: #09090b;
            border: 1px solid #27272a;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            color: #d4d4d8;
        }
        .meta div { display: flex; gap: 0.75rem; justify-content: space-between; }
        .meta span { color: #71717a; }
        .meta strong { font-weight: 600; color: #fafafa; text-align: right; overflow-wrap: anywhere; }
        details {
            margin: 0 0 1.25rem;
            border: 1px solid #27272a;
            border-radius: 0.75rem;
            background: #09090b;
            overflow: hidden;
        }
        summary {
            cursor: pointer;
            padding: 0.75rem 1rem;
            color: #a1a1aa;
            font-size: 0.85rem;
            user-select: none;
        }
        details[open] summary { border-bottom: 1px solid #27272a; }
        .tech {
            margin: 0;
            padding: 0.85rem 1rem;
            color: #fbbf24;
            font-size: 0.8rem;
            line-height: 1.45;
            overflow-wrap: anywhere;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        h2 {
            margin: 0 0 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #71717a;
        }
        ol { margin: 0; padding-left: 1.25rem; color: #d4d4d8; line-height: 1.7; font-size: 0.95rem; }
        code.inline {
            display: inline;
            padding: 0.1rem 0.35rem;
            border-radius: 0.25rem;
            background: #27272a;
            color: #fbbf24;
            font-size: 0.85em;
        }
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
        <p class="lead">The app is up, but the database connection failed.</p>

        <div class="cause">{{ $cause }}</div>

        @if(! empty(array_filter($connection ?? [])))
        <div class="meta">
            @foreach([
                'Host' => $connection['host'] ?? null,
                'Port' => $connection['port'] ?? null,
                'Database' => $connection['database'] ?? null,
                'Username' => $connection['username'] ?? null,
            ] as $label => $value)
                @if(filled($value))
                <div>
                    <span>{{ $label }}</span>
                    <strong>{{ $value }}</strong>
                </div>
                @endif
            @endforeach
        </div>
        @endif

        @if(! empty($technical))
        <details>
            <summary>Technical detail</summary>
            <p class="tech">{{ $technical }}</p>
        </details>
        @endif

        <h2>Fix in Coolify</h2>
        <ol>
            <li>Set <code class="inline">DB_CONNECTION=pgsql</code></li>
            <li>Set <code class="inline">DB_URL=postgres://USER:PASSWORD@HOST:5432/DATABASE</code> (internal Coolify URL)</li>
            <li>Remove conflicting <code class="inline">DB_HOST</code> / <code class="inline">DB_USERNAME</code> / <code class="inline">DB_PASSWORD</code></li>
            <li>Redeploy the app, then retry</li>
        </ol>
    </div>
</body>
</html>
