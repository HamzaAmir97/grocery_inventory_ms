<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $service['name'] }}</title>
    <style>
        :root {
            color-scheme: light;
            --surface: oklch(98% 0.006 95);
            --panel: oklch(94% 0.011 112);
            --ink: oklch(22% 0.025 118);
            --muted: oklch(45% 0.022 116);
            --line: oklch(84% 0.018 112);
            --accent: oklch(46% 0.11 151);
            --accent-ink: oklch(97% 0.012 145);
            --focus: oklch(58% 0.12 151);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--surface);
            color: var(--ink);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;
            line-height: 1.5;
        }

        main {
            min-height: 100vh;
            display: grid;
            align-content: center;
            padding: 48px 24px;
        }

        .shell {
            width: min(100%, 960px);
            margin: 0 auto;
        }

        .eyebrow {
            margin: 0 0 16px;
            color: var(--muted);
            font-size: 0.8125rem;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        h1 {
            max-width: 760px;
            margin: 0;
            font-size: 2.5rem;
            line-height: 1.12;
            letter-spacing: 0;
        }

        .purpose {
            max-width: 68ch;
            margin: 20px 0 0;
            color: var(--muted);
            font-size: 1.0625rem;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 32px;
        }

        a {
            color: inherit;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            color: var(--ink);
            font-weight: 700;
            text-decoration: none;
            transition: background-color 180ms ease, border-color 180ms ease, color 180ms ease;
        }

        .button:hover,
        .button:focus {
            border-color: var(--focus);
            outline: none;
        }

        .button.primary {
            border-color: var(--accent);
            background: var(--accent);
            color: var(--accent-ink);
        }

        .details {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 0.7fr);
            gap: 20px;
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid var(--line);
        }

        .panel {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            padding: 20px;
        }

        .panel h2 {
            margin: 0 0 12px;
            font-size: 1rem;
            letter-spacing: 0;
        }

        .panel p,
        .identity {
            margin: 0;
            color: var(--muted);
        }

        .identity {
            display: grid;
            gap: 8px;
            padding: 0;
            list-style: none;
            font-size: 0.9375rem;
        }

        code {
            color: var(--ink);
            font-family: ui-monospace, "SFMono-Regular", Consolas, monospace;
            font-size: 0.9375em;
        }

        @media (max-width: 720px) {
            main {
                align-content: start;
                padding-top: 32px;
            }

            h1 {
                font-size: 2rem;
            }

            .details {
                grid-template-columns: 1fr;
            }

            .button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="shell">
            <p class="eyebrow">Backend service</p>
            <h1>{{ $service['name'] }}</h1>
            <p class="purpose">Inventory, settings, lookup, authentication, and dashboard endpoints for the grocery administration workflow.</p>

            <nav class="actions" aria-label="Service links">
                <a class="button primary" href="{{ $documentation_url }}">Open API documentation</a>
                <a class="button" href="{{ $status_url }}">View service status</a>
            </nav>

            <section class="details" aria-label="Service details">
                <div class="panel">
                    <h2>Sign in</h2>
                    <p>Use <code>POST {{ $authentication['login_path'] }}</code> with the demo admin credentials shown in the documentation, then send the returned Bearer token.</p>
                </div>

                <div class="panel">
                    <h2>Identity</h2>
                    <ul class="identity">
                        <li>Name: {{ $service['name'] }}</li>
                        <li>Version: {{ $service['version'] }}</li>
                        <li>Environment: {{ $service['environment'] }}</li>
                    </ul>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
