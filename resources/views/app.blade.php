<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title inertia>{{ config('app.name', 'MacaTung — Building AI Agents & Business Systems') }}</title>
    <meta name="description" content="MacaTung builds AI agents, automation systems and software products for real businesses — from architecture and workflows to production.">
    <meta name="author" content="Ma Cà Tưng (macatung.dev)">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="theme-color" content="#070b14">
    <link rel="canonical" href="https://macatung.dev/">

    <!-- Open Graph / Facebook / Zalo -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="MacaTung • macatung.dev">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:title" content="MacaTung — Building AI Agents & Business Systems">
    <meta property="og:description" content="AI agents, automation systems and software products built from idea to production.">
    <meta property="og:url" content="https://macatung.dev/">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@macatung">
    <meta name="twitter:creator" content="@macatung">

    <!-- Favicon -->
    @php
        $isTheravadaSite = str_starts_with(request()->getHost(), 'theravada.') || request()->is('theravada*');
    @endphp
    @if($isTheravadaSite)
        <link rel="icon" type="image/png" sizes="32x32" href="/brand/theravada/favicon-theravada-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/brand/theravada/favicon-theravada-16x16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/brand/theravada/apple-touch-icon.png">
        <link rel="shortcut icon" href="/brand/theravada/favicon-theravada.ico">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='24' fill='%23070b14'/><circle cx='50' cy='52' r='28' fill='%231a233d' stroke='%2300f5a0' stroke-width='2'/><path d='M35 24 C35 16 65 16 65 24 L72 32 L28 32 Z' fill='%2311182c' stroke='%23ffd166' stroke-width='2'/><rect x='42' y='28' width='16' height='32' rx='3' fill='%23ffd166'/><circle cx='50' cy='36' r='3' fill='%23e63946'/><line x1='46' y1='44' x2='54' y2='44' stroke='%23e63946' stroke-width='1.5'/><line x1='46' y1='50' x2='54' y2='50' stroke='%23e63946' stroke-width='1.5'/><circle cx='40' cy='52' r='4' fill='%2300f5d4'/><circle cx='60' cy='52' r='4' fill='%2300f5d4'/><circle cx='40' cy='52' r='1.5' fill='%23ffffff'/><circle cx='60' cy='52' r='1.5' fill='%23ffffff'/><ellipse cx='34' cy='60' rx='3' ry='2' fill='%23ff0054' opacity='0.6'/><ellipse cx='66' cy='60' rx='3' ry='2' fill='%23ff0054' opacity='0.6'/><path d='M47 62 Q50 65 53 62' stroke='%23ffffff' stroke-width='2' fill='none' stroke-linecap='round'/></svg>" />
    @endif

    <!-- Google Fonts with full Vietnamese & Pāḷi diacritics support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,600&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@graph": [
                {
                    "@type": "WebSite",
                    "@id": "https://macatung.dev/#website",
                    "url": "https://macatung.dev/",
                    "name": "MacaTung — Building AI Agents & Business Systems",
                    "publisher": { "@id": "https://macatung.dev/#person" }
                },
                {
                    "@type": "Person",
                    "@id": "https://macatung.dev/#person",
                    "name": "MacaTung",
                    "alternateName": "Ma Cà Tưng",
                    "url": "https://macatung.dev/",
                    "jobTitle": "Software Engineer & AI Builder",
                    "sameAs": ["https://github.com/macatung"]
                }
            ]
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body class="bg-midnight-950 text-slate-100 font-sans antialiased selection:bg-phantom-mint selection:text-midnight-950 overflow-x-hidden">
    @inertia
    <noscript>
        <main>
            <h1>MacaTung — Building AI Agents &amp; Business Systems</h1>
            <p>I build AI agents, automation systems and software products that help real businesses operate better.</p>
            <p>Explore software architecture, workflow automation, distributed systems and product engineering projects.</p>
            <nav aria-label="Primary navigation">
                <a href="/projects">Explore projects</a>
                <a href="/about">About MacaTung</a>
                <a href="/blog">Technical notes</a>
                <a href="/contact">Contact</a>
            </nav>
        </main>
    </noscript>
</body>
</html>
