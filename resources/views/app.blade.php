<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    @php
        $baseDomain = config('app.base_domain', 'macatung.dev');
        $host = request()->getHost();
        $isTheravadaSubdomain = str_starts_with($host, 'theravada.');
        $isTheravadaSite = $isTheravadaSubdomain || request()->is('theravada*');
        $isCashbackSubdomain = str_starts_with($host, 'hoantien.');
        $isCashbackSite = $isCashbackSubdomain || request()->is('hoantien*');
        $isDecodeSubdomain = str_starts_with($host, 'decode.');
        $isDecodeSite = $isDecodeSubdomain || request()->is('decode*');

        if ($isTheravadaSite) {
            $siteUrl = 'https://theravada.' . $baseDomain;
            $canonicalUrl = $isTheravadaSubdomain 
                ? request()->url() 
                : $siteUrl . (request()->path() === 'theravada' ? '' : '/' . preg_replace('#^theravada/#', '', request()->path()));
            $siteName = 'Ma Tọa Thiền • Theravāda';
            $pageTitle = 'Ma Tọa Thiền — Tam Tạng Kinh Điển Theravāda & Thiền Vipassanā';
            $pageDescription = 'Hệ thống tu học và bảo tồn kinh điển Phật giáo nguyên thủy Theravāda: Tứ Thánh Đế, Bát Chánh Đạo, Thiền Minh Sát Vipassanā, Thẻ ảnh Pháp Cú và Từ điển Pāḷi.';
            $authorName = 'Ma Tọa Thiền • Theravāda';
            $ogImage = $siteUrl . '/brand/theravada/og-theravada-1200x630.jpg';
            $themeColor = '#0c0a09';
        } elseif ($isCashbackSite) {
            $siteUrl = 'https://hoantien.' . $baseDomain;
            $canonicalUrl = $isCashbackSubdomain
                ? request()->url()
                : $siteUrl . (request()->path() === 'hoantien' ? '' : '/' . preg_replace('#^hoantien/#', '', request()->path()));
            $siteName = 'Cổng Hoàn Tiền Shopee • MacaTung';
            $pageTitle = 'Cổng Hoàn Tiền Shopee — Tích Lũy Tới 80% Hoa Hồng | MacaTung';
            $pageDescription = 'Hệ thống hoàn tiền Shopee Affiliate tự động. Dán link sản phẩm Shopee, mua hàng và nhận hoàn tiền trực tiếp vào tài khoản ngân hàng.';
            $authorName = 'MacaTung';
            $ogImage = $siteUrl . '/brand/macatung-logo-horizontal.png';
            $themeColor = '#0f172a';
        } elseif ($isDecodeSite) {
            $siteUrl = 'https://decode.' . $baseDomain;
            $canonicalUrl = $isDecodeSubdomain
                ? request()->url()
                : $siteUrl . (request()->path() === 'decode' ? '' : '/' . preg_replace('#^decode/#', '', request()->path()));
            $siteName = 'Ma Giải Mã • Decode Series';
            $pageTitle = 'Ma Giải Mã — Khảo Cứu & Bóc Tách Hệ Thống Kỹ Thuật Số';
            $pageDescription = 'Series khảo cứu kiến trúc hạ tầng thanh toán toàn cầu, độ trễ mạng liên lục địa và phần mềm quy mô lớn.';
            $authorName = 'MacaTung';
            $ogImage = $siteUrl . '/brand/decode/mascot-ma-giai-ma-3d.png';
            $themeColor = '#04070d';
        } else {
            $siteUrl = 'https://' . $baseDomain;
            $canonicalUrl = request()->url();
            $siteName = 'MacaTung Ecosystem';
            $pageTitle = config('app.name', 'MacaTung — Digital Ecosystem & Software Product Hub');
            $pageDescription = 'MacaTung là hệ sinh thái kỹ thuật số đa nền tảng gồm 5 trụ cột sản phẩm: Nền tảng Phật giáo Theravāda, khảo cứu công nghệ Ma Giải Mã, Cổng Hoàn Tiền Shopee, Task Companion Desktop và các công cụ tương tác.';
            $authorName = 'MacaTung';
            $ogImage = $siteUrl . '/brand/macatung-logo-horizontal.png';
            $themeColor = '#070b14';
        }
    @endphp

    <title inertia>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="author" content="{{ $authorName }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="theme-color" content="{{ $themeColor }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Open Graph / Facebook / Zalo -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@macatung">
    <meta name="twitter:creator" content="@macatung">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- Favicon (Google Search Central compliant: multiples of 48px square) -->
    @if($isTheravadaSite)
        <link rel="icon" type="image/png" sizes="48x48" href="/brand/theravada/favicon-theravada-48x48.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/brand/theravada/favicon-theravada-96x96.png">
        <link rel="icon" type="image/png" sizes="192x192" href="/brand/theravada/favicon-theravada-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/brand/theravada/favicon-theravada-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/brand/theravada/favicon-theravada-16x16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/brand/theravada/apple-touch-icon.png">
        <link rel="shortcut icon" href="/brand/theravada/favicon-theravada.ico">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='24' fill='%23070b14'/><circle cx='50' cy='52' r='28' fill='%231a233d' stroke='%2300f5a0' stroke-width='2'/><path d='M35 24 C35 16 65 16 65 24 L72 32 L28 32 Z' fill='%2311182c' stroke='%23ffd166' stroke-width='2'/><rect x='42' y='28' width='16' height='32' rx='3' fill='%23ffd166'/><circle cx='50' cy='36' r='3' fill='%23e63946'/><line x1='46' y1='44' x2='54' y2='44' stroke='%23e63946' stroke-width='1.5'/><line x1='46' y1='50' x2='54' y2='50' stroke='%23e63946' stroke-width='1.5'/><circle cx='40' cy='52' r='4' fill='%2300f5d4'/><circle cx='60' cy='52' r='4' fill='%2300f5d4'/><circle cx='40' cy='52' r='1.5' fill='%23ffffff'/><circle cx='60' cy='52' r='1.5' fill='%23ffffff'/><ellipse cx='34' cy='60' rx='3' ry='2' fill='%23ff0054' opacity='0.6'/><ellipse cx='66' cy='60' rx='3' ry='2' fill='%23ff0054' opacity='0.6'/><path d='M47 62 Q50 65 53 62' stroke='%23ffffff' stroke-width='2' fill='none' stroke-linecap='round'/></svg>" />
        <link rel="shortcut icon" href="/favicon.ico">
    @endif

    <!-- Google Fonts with full Vietnamese & Pāḷi diacritics support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,600&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Merriweather:ital,wght@0,300;0,400;0,700;1,300;1,400&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    @if($isTheravadaSite)
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@graph": [
                {
                    "@type": "WebSite",
                    "@id": "{{ $siteUrl }}/#website",
                    "url": "{{ $siteUrl }}/",
                    "name": "Ma Tọa Thiền",
                    "alternateName": [
                        "Ma Tọa Thiền — Tam Tạng Theravāda",
                        "Phật Giáo Nguyên Thủy Theravāda",
                        "Theravāda MacaTung"
                    ],
                    "description": "Hệ thống tu học và bảo tồn kinh điển Phật giáo nguyên thủy Theravāda: Tứ Thánh Đế, Bát Chánh Đạo, Thiền Minh Sát Vipassanā, Thẻ ảnh Pháp Cú và Từ điển Pāḷi.",
                    "inLanguage": ["vi", "pi"],
                    "publisher": {
                        "@type": "Organization",
                        "name": "Ma Tọa Thiền",
                        "url": "{{ $siteUrl }}/",
                        "logo": "{{ $siteUrl }}/brand/theravada/logo-ma-toa-thien-512x512.png"
                    }
                }
            ]
        }
    </script>
    @elseif($isDecodeSite)
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@graph": [
                {
                    "@type": "WebSite",
                    "@id": "{{ $siteUrl }}/#website",
                    "url": "{{ $siteUrl }}/",
                    "name": "Ma Giải Mã — Decode Series",
                    "description": "Series khảo cứu kiến trúc hạ tầng tài chính và phần mềm quy mô toàn cầu.",
                    "inLanguage": ["vi"],
                    "publisher": {
                        "@type": "Organization",
                        "name": "MacaTung Ecosystem",
                        "url": "https://{{ $baseDomain }}/",
                        "logo": "https://{{ $baseDomain }}/brand/macatung-logo-horizontal.png"
                    }
                }
            ]
        }
    </script>
    @else
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@graph": [
                {
                    "@type": "WebSite",
                    "@id": "https://{{ $baseDomain }}/#website",
                    "url": "https://{{ $baseDomain }}/",
                    "name": "MacaTung — Digital Ecosystem & Software Product Hub",
                    "description": "{{ $pageDescription }}",
                    "publisher": { "@id": "https://{{ $baseDomain }}/#organization" }
                },
                {
                    "@type": "Organization",
                    "@id": "https://{{ $baseDomain }}/#organization",
                    "name": "MacaTung",
                    "alternateName": "MacaTung Ecosystem",
                    "url": "https://{{ $baseDomain }}/",
                    "logo": "{{ $siteUrl }}/brand/macatung-logo-horizontal.png",
                    "sameAs": ["https://github.com/macatung"]
                }
            ]
        }
    </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body class="{{ $isTheravadaSite ? 'bg-stone-950 text-stone-100 font-serif' : 'bg-midnight-950 text-slate-100 font-sans' }} antialiased selection:bg-phantom-mint selection:text-midnight-950 overflow-x-hidden">
    @inertia
    <noscript>
        @if($isTheravadaSite)
        <main>
            <h1>Ma Tọa Thiền — Tam Tạng Kinh Điển Theravāda &amp; Thiền Vipassanā</h1>
            <p>Hệ thống tu học và bảo tồn kinh điển Phật giáo nguyên thủy Theravāda: Tứ Thánh Đế, Bát Chánh Đạo, Thiền Minh Sát Vipassanā, Thẻ ảnh Pháp Cú và Từ điển Pāḷi thuần khiết.</p>
            <nav aria-label="Điều hướng chính Theravāda">
                <a href="/hoc-pali">Học Tiếng Pāḷi Căn Bản</a>
                <a href="/danh-muc/phap-hoc">Pháp Học (Pariyatti)</a>
                <a href="/danh-muc/phap-hanh">Pháp Hành (Paṭipatti)</a>
                <a href="/danh-muc/phap-thoai">Pháp Thoại &amp; Pháp Âm</a>
                <a href="/danh-muc/kinh-tung">Kinh Tụng &amp; Paritta</a>
                <a href="/danh-muc/lich-su">Lịch Sử Phật Giáo</a>
                <a href="/tu-dien-pali">Từ Điển Pāḷi Thực Dụng</a>
                <a href="/ung-dung-tu-hoc">Ứng Dụng Tu Học &amp; Thẻ Pháp Cú</a>
            </nav>
        </main>
        @elseif($isDecodeSite)
        <main>
            <h1>Ma Giải Mã — Khảo Cứu &amp; Bóc Tách Hệ Thống Kỹ Thuật Số</h1>
            <p>Series khảo cứu kiến trúc hạ tầng thanh toán toàn cầu, độ trễ mạng liên lục địa và phần mềm quy mô lớn.</p>
            <nav aria-label="Điều hướng Ma Giải Mã">
                <a href="/">Trang Chủ MacaTung</a>
                <a href="/tap-1-visa-100k">Tập 1: Visa 100K TPS Network</a>
            </nav>
        </main>
        @else
        <main>
            <h1>MacaTung — Digital Ecosystem &amp; Software Product Hub</h1>
            <p>Hệ sinh thái kỹ thuật số đa nền tảng gồm 5 trụ cột sản phẩm: Nền tảng Phật giáo Theravāda, khảo cứu công nghệ Ma Giải Mã, Cổng Hoàn Tiền Shopee, Task Companion Desktop và các công cụ tương tác.</p>
            <nav aria-label="Ecosystem navigation">
                <a href="/theravada">Nền Tảng Phật Giáo Theravāda</a>
                <a href="/decode">Ma Giải Mã (Decode Series)</a>
                <a href="/hoantien">Cổng Hoàn Tiền Shopee</a>
                <a href="/desktop">Task Companion Desktop</a>
                <a href="/projects">Kho Dự Án Grimoire</a>
                <a href="/game">Phòng Máy Arcade</a>
                <a href="/contact">Liên Hệ &amp; Hợp Tác</a>
            </nav>
        </main>
        @endif
    </noscript>
</body>
</html>
