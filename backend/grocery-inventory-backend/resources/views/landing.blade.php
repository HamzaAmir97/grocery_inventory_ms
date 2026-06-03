<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $service['name'] }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

    <style>
        :root {
            color-scheme: light;

            /* Surfaces — ported from the frontend design tokens */
            --bg:            #F6F7F9;
            --surface:       #FFFFFF;
            --surface-2:     #FAFAFB;
            --surface-3:     #F1F2F4;
            --border:        #ECEDEF;
            --border-strong: #DDDFE3;

            /* Text */
            --fg:            #1B1C1E;
            --fg-muted:      #6B7280;
            --fg-subtle:     #9CA3AF;

            /* Brand — warm orange */
            --accent:        #F97316;
            --accent-hover:  #EA6A0F;
            --accent-press:  #C2570B;
            --accent-soft:   #FFF1E6;
            --accent-ring:   rgba(249, 115, 22, 0.28);
            --brand-grad:    linear-gradient(135deg, #FF8904 0%, #F54900 100%);

            --success:       #16A34A;
            --success-soft:  #DCFCE7;

            /* Shadows */
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.06), 0 2px 4px rgba(15, 23, 42, 0.04);
            --shadow-lg: 0 12px 32px rgba(15, 23, 42, 0.10), 0 4px 8px rgba(15, 23, 42, 0.04);
            --shadow-accent: 0 10px 24px rgba(245, 73, 0, 0.28);

            /* Radii */
            --r-md: 8px;
            --r-lg: 12px;
            --r-xl: 16px;

            --ease: cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--fg);
            font-family: "Nunito", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            line-height: 1.5;
            font-optical-sizing: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Warm, on-brand backdrop glow — subtle, never loud. */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(620px 420px at 12% -8%, rgba(255, 137, 4, 0.10), transparent 60%),
                radial-gradient(560px 380px at 100% 0%, rgba(245, 73, 0, 0.06), transparent 55%);
        }

        main {
            position: relative;
            min-height: 100vh;
            display: grid;
            align-content: center;
            padding: 56px 24px;
        }

        .shell {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        /* Brand row */
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }
        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--brand-grad);
            color: #fff;
            box-shadow: var(--shadow-accent);
            flex: 0 0 auto;
        }
        .brand-mark svg { width: 22px; height: 22px; }
        .brand-word { font-weight: 800; font-size: 1.0625rem; letter-spacing: -0.01em; color: var(--fg); }
        .brand-tag {
            margin-left: 2px;
            padding: 3px 9px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent-press);
            font-size: 0.6875rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .eyebrow {
            margin: 0 0 14px;
            color: var(--accent-press);
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        h1 {
            max-width: 18ch;
            margin: 0;
            font-size: clamp(2.25rem, 5vw, 3.25rem);
            line-height: 1.08;
            font-weight: 900;
            letter-spacing: -0.025em;
        }
        h1 .accent {
            background: var(--brand-grad);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
        }

        .purpose {
            max-width: 60ch;
            margin: 20px 0 0;
            color: var(--fg-muted);
            font-size: 1.0625rem;
            font-weight: 500;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 34px;
        }

        a { color: inherit; text-decoration: none; }

        .button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 48px;
            padding: 0 20px;
            border: 1px solid var(--border-strong);
            border-radius: var(--r-md);
            background: var(--surface);
            color: var(--fg);
            font-weight: 800;
            font-size: 0.9375rem;
            cursor: pointer;
            transition: transform var(--ease) 180ms, background-color 180ms var(--ease),
                        border-color 180ms var(--ease), box-shadow 180ms var(--ease);
        }
        .button svg { width: 17px; height: 17px; }
        .button:hover { background: var(--surface-3); transform: translateY(-1px); }
        .button:focus-visible { outline: none; box-shadow: 0 0 0 3px var(--accent-ring); }

        .button.primary {
            border-color: transparent;
            background: var(--brand-grad);
            color: #fff;
            box-shadow: var(--shadow-accent);
        }
        .button.primary:hover { filter: brightness(1.04); transform: translateY(-2px); }
        .button.primary .arrow { transition: transform var(--ease) 180ms; }
        .button.primary:hover .arrow { transform: translateX(3px); }

        .details {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 0.8fr);
            gap: 16px;
            margin-top: 52px;
            padding-top: 40px;
            border-top: 1px solid var(--border);
        }

        .panel {
            border: 1px solid var(--border);
            border-radius: var(--r-xl);
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            padding: 22px;
        }
        .panel h2 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px;
            font-size: 0.9375rem;
            font-weight: 800;
        }
        .panel h2 .dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--accent);
        }
        .panel p {
            margin: 0;
            color: var(--fg-muted);
            font-size: 0.9375rem;
            font-weight: 500;
        }

        code {
            padding: 2px 7px;
            border-radius: 6px;
            background: var(--surface-3);
            color: var(--accent-press);
            font-family: "JetBrains Mono", ui-monospace, SFMono-Regular, Consolas, monospace;
            font-size: 0.8125em;
            font-weight: 700;
        }

        .identity {
            display: grid;
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .identity li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 0.9375rem;
        }
        .identity .k { color: var(--fg-subtle); font-weight: 700; }
        .identity .v { color: var(--fg); font-weight: 800; }
        .identity .v.env {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 10px;
            border-radius: 999px;
            background: var(--success-soft);
            color: var(--success);
            font-size: 0.8125rem;
        }
        .identity .v.env::before {
            content: "";
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--success);
        }

        @media (max-width: 720px) {
            main { align-content: start; padding-top: 36px; }
            .details { grid-template-columns: 1fr; }
            .button { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <main>
        <div class="shell">
            <div class="brand">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7l9-4 9 4-9 4-9-4z"></path>
                        <path d="M3 7v10l9 4 9-4V7"></path>
                        <path d="M12 11v10"></path>
                    </svg>
                </span>
                <span class="brand-word">Grocery Inventory</span>
                <span class="brand-tag">API</span>
            </div>

            <p class="eyebrow">Backend Service</p>
            <h1>Grocery Inventory <span class="accent">Management API</span></h1>
            <p class="purpose">Inventory, settings, lookup, authentication, and dashboard endpoints for the grocery administration workflow — JWT-secured and documented with OpenAPI.</p>

            <nav class="actions" aria-label="Service links">
                <a class="button primary" href="{{ $documentation_url }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    Open API documentation
                    <svg class="arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14"></path><path d="M13 6l6 6-6 6"></path>
                    </svg>
                </a>
                <a class="button" href="{{ $status_url }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                    </svg>
                    View service status
                </a>
            </nav>

            <section class="details" aria-label="Service details">
                <div class="panel">
                    <h2><span class="dot"></span> Sign in</h2>
                    <p>Use <code>POST {{ $authentication['login_path'] }}</code> with the demo admin credentials shown in the documentation, then send the returned Bearer token on protected requests.</p>
                </div>

                <div class="panel">
                    <h2><span class="dot"></span> Identity</h2>
                    <ul class="identity">
                        <li><span class="k">Name</span> <span class="v">{{ $service['name'] }}</span></li>
                        <li><span class="k">Version</span> <span class="v">{{ $service['version'] }}</span></li>
                        <li><span class="k">Environment</span> <span class="v env">{{ $service['environment'] }}</span></li>
                    </ul>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
