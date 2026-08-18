<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Serv5 - Select Portal</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #0f172a;
            --panel: rgba(30, 41, 59, .68);
            --border: rgba(148, 163, 184, .24);
            --muted: #94a3b8;
            --orange: #f97316;
            --blue: #60a5fa;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            color: #fff;
            background:
                radial-gradient(circle at 15% 10%, rgba(37, 99, 235, .24), transparent 34%),
                radial-gradient(circle at 85% 90%, rgba(249, 115, 22, .2), transparent 34%),
                radial-gradient(circle at center, #1e293b 0%, var(--bg) 72%);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .portal-container {
            width: min(100%, 960px);
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .brand { margin-bottom: 46px; }

        .logo {
            width: 112px;
            height: 112px;
            margin: 0 auto 22px;
            object-fit: contain;
            filter: drop-shadow(0 18px 24px rgba(0, 0, 0, .35));
            transition: transform .2s ease;
        }

        .logo:hover { transform: scale(1.05); }

        h1 {
            margin: 0 0 12px;
            color: #f8fafc;
            font-size: clamp(2.1rem, 5vw, 3.25rem);
            line-height: 1.1;
            letter-spacing: -.04em;
            font-weight: 800;
        }

        .brand-name {
            color: var(--orange);
            background: linear-gradient(90deg, #fb923c, #ea580c);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            max-width: 560px;
            margin: 0 auto;
            color: var(--muted);
            font-size: 1.05rem;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
            text-align: start;
        }

        .portal-card {
            position: relative;
            display: block;
            overflow: hidden;
            padding: 30px;
            color: inherit;
            text-decoration: none;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 24px;
            backdrop-filter: blur(14px);
            box-shadow: 0 18px 50px rgba(0, 0, 0, .18);
            transition: transform .2s ease, background .2s ease, border-color .2s ease;
        }

        .portal-card:hover,
        .portal-card:focus-visible {
            transform: translateY(-4px);
            background: rgba(30, 41, 59, .9);
            border-color: rgba(249, 115, 22, .55);
            outline: none;
        }

        .portal-card.user:hover,
        .portal-card.user:focus-visible { border-color: rgba(96, 165, 250, .55); }

        .icon {
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            margin-bottom: 22px;
            border-radius: 16px;
        }

        .icon.admin {
            color: var(--orange);
            background: rgba(249, 115, 22, .16);
            border: 1px solid rgba(249, 115, 22, .3);
        }

        .icon.user {
            color: var(--blue);
            background: rgba(59, 130, 246, .16);
            border: 1px solid rgba(96, 165, 250, .3);
        }

        .icon svg { width: 28px; height: 28px; }

        .card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 10px;
            color: #f8fafc;
            font-size: 1.45rem;
            line-height: 1.25;
        }

        .card-title svg { width: 19px; height: 19px; color: #64748b; }

        .card-text {
            margin: 0;
            color: var(--muted);
            font-size: .95rem;
            line-height: 1.65;
        }

        .website-link {
            width: min(100%, 430px);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 42px auto 0;
            padding: 15px 22px;
            color: #fff;
            text-decoration: none;
            font-size: 1.05rem;
            font-weight: 700;
            background: linear-gradient(90deg, #f97316, #ea580c);
            border-radius: 16px;
            box-shadow: 0 0 24px rgba(249, 115, 22, .28);
            transition: transform .2s ease, filter .2s ease;
        }

        .website-link:hover,
        .website-link:focus-visible { transform: translateY(-3px); filter: brightness(1.08); outline: none; }
        .website-link svg { width: 24px; height: 24px; }

        @media (max-width: 700px) {
            .portal-grid { grid-template-columns: 1fr; }
            .brand { margin-bottom: 34px; }
            .portal-card { padding: 24px; }
        }
    </style>
</head>
<body>
    <main class="portal-container">
        <header class="brand">
            <img class="logo" src="{{ asset('logo.svg') }}" alt="Serv5 Logo">
            <h1>Welcome to <span class="brand-name">Serv5</span></h1>
            <p class="subtitle">Please select your destination portal to continue.</p>
        </header>

        <section class="portal-grid" aria-label="Available portals">
            <a class="portal-card" href="/admin">
                <div class="icon admin" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h2 class="card-title">
                    Admin Portal
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </h2>
                <p class="card-text">Access the Filament admin dashboard to manage website content, system settings, and operations.</p>
            </a>

            <a class="portal-card user" href="https://serv5.com.eg">
                <div class="icon user" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="card-title">
                    User Portal
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </h2>
                <p class="card-text">Open the Serv5 website and access the public user experience.</p>
            </a>
        </section>

        <a class="website-link" href="https://serv5.com.eg">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>GO TO OUR WEBSITE</span>
        </a>
    </main>
</body>
</html>
