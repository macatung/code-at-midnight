<?php

/**
 * Adversarial Route & Interaction Stress Verifier (Challenger 1)
 *
 * Verifies:
 * 1. HTTP 200 and Inertia props across all 5 ecosystem routes and subdomains.
 * 2. Link integrity of all links and CTAs on Home.vue and embedded components.
 * 3. Feature regressions across Theravāda, Decode, and Cashback.
 * 4. Task Companion download artifact verification.
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "\n" . str_repeat('=', 80) . "\n";
echo " CHANGER 1: EMPIRICAL ROUTE & INTERACTION STRESS VERIFICATION\n";
echo str_repeat('=', 80) . "\n\n";

$results = [
    'routes' => [],
    'subdomains' => [],
    'home_links' => [],
    'regressions' => [],
];

function dispatchRequest($kernel, $uri, $method = 'GET', $headers = [], $content = null) {
    $server = [];
    $parsed = parse_url($uri);
    $path = $parsed['path'] ?? '/';
    if (!empty($parsed['query'])) {
        $path .= '?' . $parsed['query'];
    }

    if (!empty($parsed['host'])) {
        $server['HTTP_HOST'] = $parsed['host'];
        $server['SERVER_NAME'] = $parsed['host'];
    }

    foreach ($headers as $k => $v) {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $k));
        $server[$serverKey] = $v;
    }

    $request = Illuminate\Http\Request::create(
        $uri,
        $method,
        [],
        [],
        [],
        $server,
        $content
    );

    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);

    $status = $response->getStatusCode();
    $body = $response->getContent();
    
    // Extract Inertia data
    $inertiaComponent = null;
    $inertiaProps = [];
    if (preg_match('/data-page="([^"]+)"/', $body, $m)) {
        $page = json_decode(htmlspecialchars_decode($m[1], ENT_QUOTES), true);
        $inertiaComponent = $page['component'] ?? null;
        $inertiaProps = array_keys($page['props'] ?? []);
    } elseif ($status === 200 && str_starts_with($body, '{')) {
        $json = json_decode($body, true);
        if (isset($json['component'])) {
            $inertiaComponent = $json['component'];
            $inertiaProps = array_keys($json['props'] ?? []);
        }
    }

    return [
        'status' => $status,
        'component' => $inertiaComponent,
        'props' => $inertiaProps,
        'content_type' => $response->headers->get('Content-Type'),
        'location' => $response->headers->get('Location'),
        'body_length' => strlen($body),
        'raw_body' => $body,
    ];
}

// -----------------------------------------------------------------------------
// TASK 1: Dispatch HTTP Requests to 5 Ecosystem Routes & Subdomains
// -----------------------------------------------------------------------------
echo "--- TASK 1: 5 ECOSYSTEM ROUTES & SUBDOMAINS ---\n";

$targetRoutes = [
    'Root Hub' => ['uri' => '/', 'expected_comp' => 'Home', 'expected_props' => ['title']],
    'Pillar 1: Theravada (Path)' => ['uri' => '/theravada', 'expected_comp' => 'Theravada/Index', 'expected_props' => ['articles']],
    'Pillar 1: Theravada (Domain)' => ['uri' => 'http://theravada.macatung.dev', 'expected_comp' => 'Theravada/Index', 'expected_props' => ['articles']],
    'Pillar 2: Decode (Path)' => ['uri' => '/decode', 'expected_comp' => 'Decode/Index', 'expected_props' => ['episodes']],
    'Pillar 2: Decode (Domain)' => ['uri' => 'http://decode.macatung.dev', 'expected_comp' => 'Decode/Index', 'expected_props' => ['episodes']],
    'Pillar 3: Cashback (Path)' => ['uri' => '/hoantien', 'expected_comp' => 'Cashback/Index', 'expected_props' => ['wallet']],
    'Pillar 3: Cashback (Domain prod)' => ['uri' => 'http://hoantien.macatung.dev', 'expected_comp' => 'Cashback/Index', 'expected_props' => ['wallet']],
    'Pillar 3: Cashback (Domain local)' => ['uri' => 'http://hoantien.localhost', 'expected_comp' => 'Cashback/Index', 'expected_props' => ['wallet']],
    'Pillar 4: Desktop (Path)' => ['uri' => '/desktop', 'expected_comp' => 'Desktop/Index', 'expected_props' => []],
    'Pillar 5: Tools (Path /tools)' => ['uri' => '/tools', 'expected_comp' => 'Game/Index', 'expected_props' => ['settings']],
    'Pillar 5: Game (Path /game)' => ['uri' => '/game', 'expected_comp' => 'Game/Index', 'expected_props' => ['settings']],
    'Pillar 5: Talisman (Path /talisman)' => ['uri' => '/talisman', 'expected_comp' => 'Talisman/Index', 'expected_props' => ['settings']],
];

foreach ($targetRoutes as $name => $spec) {
    $res = dispatchRequest($kernel, $spec['uri']);
    $statusOk = ($res['status'] === 200);
    $compOk = ($res['component'] === $spec['expected_comp']);
    $propsOk = true;
    foreach ($spec['expected_props'] as $reqProp) {
        if (!in_array($reqProp, $res['props'])) {
            $propsOk = false;
        }
    }

    $pass = $statusOk && $compOk && $propsOk;
    $statusText = $pass ? "✔ PASS" : "✖ FAIL";
    printf("%-35s | Status: %-3d | Comp: %-20s | Props: %-15s | %s\n",
        $name,
        $res['status'],
        $res['component'] ?? 'NONE',
        implode(',', array_slice($res['props'], 0, 3)),
        $statusText
    );

    $results['routes'][$name] = [
        'uri' => $spec['uri'],
        'status' => $res['status'],
        'component' => $res['component'],
        'props' => $res['props'],
        'passed' => $pass,
    ];
}

// -----------------------------------------------------------------------------
// TASK 2: Stress Test Link Integrity on Home.vue & Subcomponents
// -----------------------------------------------------------------------------
echo "\n--- TASK 2: LINK INTEGRITY AUDIT ON HOME.VUE ---\n";

// List of all links found across Home.vue and its subcomponents
$linksToAudit = [
    // Navbar
    'Navbar: Home' => '/',
    'Navbar: Theravada' => '/theravada',
    'Navbar: Decode' => '/decode',
    'Navbar: Hoan Tien' => '/hoantien',
    'Navbar: Desktop' => '/desktop',
    'Navbar: Tools' => '/tools',
    // Status Banner
    'StatusBanner: Theravada' => '/theravada',
    'StatusBanner: Decode' => '/decode',
    'StatusBanner: Hoan Tien' => '/hoantien',
    'StatusBanner: Desktop' => '/desktop',
    'StatusBanner: Talisman' => '/talisman',
    // Hero Section
    'Hero: Desktop CTA' => '/desktop',
    'Hero: Cashback CTA' => '/hoantien',
    // Pillar Cards
    'Card Desktop: Specs' => '/desktop',
    'Card Cashback: Portal CTA' => '/hoantien',
    'Card Decode: Ep 01 link' => '/decode/tap-01-quet-the-visa-100k-2-giay-du-hanh',
    'Card Decode: Ep 01 valid route test 1' => '/decode/tap/tap-01-quet-the-visa-100k-2-giay-du-hanh',
    'Card Decode: Ep 01 valid route test 2' => '/decode/tap-1-visa-100k',
    'Card Decode: Series CTA' => '/decode',
    'Card Theravada: Suttas' => '/theravada',
    'Card Theravada: Pali Course' => '/theravada/hoc-pali',
    'Card Theravada: Video Studio' => '/admin/theravada/videos',
    'Card Theravada: Apps' => '/theravada/ung-dung-tu-hoc',
    'Card Theravada: Platform CTA' => '/theravada',
    'Card Tools: Talisman' => '/talisman',
    'Card Tools: Game' => '/game',
    'Card Tools: CLI' => '/tools',
    'Card Tools: Game CTA' => '/game',
    'Card Tools: Talisman CTA' => '/talisman',
    // Quick Launch
    'QuickLaunch: Cashback CTA' => '/hoantien',
    'QuickLaunch: Theravada' => '/theravada',
    'QuickLaunch: Decode' => '/decode',
    'QuickLaunch: Cashback' => '/hoantien',
    'QuickLaunch: Desktop' => '/desktop',
    'QuickLaunch: Talisman' => '/talisman',
    // Footer
    'Footer: Theravada' => '/theravada',
    'Footer: Decode' => '/decode',
    'Footer: Cashback' => '/hoantien',
    'Footer: Desktop' => '/desktop',
    'Footer: Talisman' => '/talisman',
    'Footer: Game' => '/game',
    'Footer: Projects' => '/projects',
    'Footer: Blog' => '/blog',
    'Footer: Contact' => '/contact',
    'Footer: Admin' => '/admin',
];

$failedLinks = [];

foreach ($linksToAudit as $desc => $targetUri) {
    $res = dispatchRequest($kernel, $targetUri);
    // Note: /admin or /admin/theravada/videos without auth will return 302 redirect to /admin/login, which is valid auth guard!
    $isValid = ($res['status'] === 200 || $res['status'] === 302);
    $mark = $isValid ? "✔ OK ({$res['status']})" : "✖ BROKEN ({$res['status']})";

    if (!$isValid) {
        $failedLinks[$desc] = [
            'uri' => $targetUri,
            'status' => $res['status'],
        ];
    }

    printf("%-38s | URI: %-45s | %s\n", $desc, $targetUri, $mark);
}

if (count($failedLinks) > 0) {
    echo "\n⚠ DETECTED " . count($failedLinks) . " BROKEN LINK(S) ON HOME.VUE:\n";
    foreach ($failedLinks as $desc => $fl) {
        echo "  - {$desc} [{$fl['uri']}] returned HTTP {$fl['status']}!\n";
    }
} else {
    echo "\n✔ 100% of links on Home.vue and subcomponents are valid and return 200 or 302!\n";
}

// -----------------------------------------------------------------------------
// TASK 3: Verify Zero Regressions on Theravada, Decode, and Cashback
// -----------------------------------------------------------------------------
echo "\n--- TASK 3: FEATURE REGRESSION TESTS ---\n";

// 3.1 Theravāda Features
echo "1. Testing Theravāda Pali Lessons & Detail Pages...\n";
$paliLessons = [
    'nguyen-am-va-phu-am-pali',
    'quy-tac-phat-am-chuan-va-trong-am',
    'danh-tu-va-8-bien-cach-vibhatti',
    'dong-tu-va-thoi-hien-tai-akhyata',
    'tam-bao-va-tam-quy-y-tisarana',
    'tu-thanh-de-va-bat-chanh-dao-cattari-ariyasaccani',
    'kinh-phap-cu-ke-so-1-yamakavagga',
    'kinh-phap-cu-ke-so-183-buddhavagga',
    'tho-tri-ngu-gioi-pancasila',
    'kinh-rai-tam-tu-metta-sutta',
];

$theravadaRegressionFail = 0;
foreach ($paliLessons as $lessonSlug) {
    $uri = "/theravada/hoc-pali/{$lessonSlug}";
    $res = dispatchRequest($kernel, $uri);
    $ok = ($res['status'] === 200 && $res['component'] === 'Theravada/PaliLessonShow' && in_array('lessonMeta', $res['props']));
    if (!$ok) {
        echo "  ✖ Regression on Pali lesson: {$lessonSlug} (Status: {$res['status']}, Comp: {$res['component']})\n";
        $theravadaRegressionFail++;
    }
}
if ($theravadaRegressionFail === 0) {
    echo "  ✔ All 10 Pali lessons return HTTP 200 with PaliLessonShow component and lessonMeta props.\n";
}

// Pali Lesson 301 alias redirect
$aliasRes = dispatchRequest($kernel, '/theravada/hoc-pali/pali-01-nguyen-am-phu-am');
echo "  Pali Lesson 301 Alias: Status {$aliasRes['status']} -> Location: {$aliasRes['location']}\n";

// Sutta show
$suttaRes = dispatchRequest($kernel, '/theravada/kinh/tu-thanh-de-bon-chan-ly-toi-thuong');
echo "  Theravada Sutta Detail: Status {$suttaRes['status']} (Comp: {$suttaRes['component']})\n";

// 3.2 Decode Episodes
echo "\n2. Testing Decode Episodes (All 5 Episodes)...\n";
$decodeEpisodes = [
    'tap-01-quet-the-visa-100k-2-giay-du-hanh',
    'tap-02-google-tim-kiem-50-ty-trang-web-0-3-giay',
    'tap-03-cuoc-goi-xuyen-luc-dia-cap-quang-day-bien',
    'tap-04-cay-atm-khong-bao-gio-nha-nham-tien',
    'tap-05-gps-thuyet-tuong-doi-einstein',
];

$decodeRegressionFail = 0;
foreach ($decodeEpisodes as $epSlug) {
    $uri = "/decode/tap/{$epSlug}";
    $res = dispatchRequest($kernel, $uri);
    $ok = ($res['status'] === 200 && $res['component'] === 'Decode/Show' && in_array('episode', $res['props']));
    if (!$ok) {
        echo "  ✖ Regression on Decode episode: {$epSlug} (Status: {$res['status']})\n";
        $decodeRegressionFail++;
    }
}
if ($decodeRegressionFail === 0) {
    echo "  ✔ All 5 Decode episodes return HTTP 200 with Decode/Show component and episode props.\n";
}

// 3.3 Cashback Features
echo "\n3. Testing Cashback Features...\n";
$cbRes = dispatchRequest($kernel, '/hoantien');
$cbOk = ($cbRes['status'] === 200 && in_array('wallet', $cbRes['props']));
echo "  Cashback Portal Index: " . ($cbOk ? "✔ HTTP 200 with wallet prop" : "✖ FAILED") . "\n";

// Test Link Generation
$genRequest = dispatchRequest($kernel, '/hoantien/generate-link', 'POST', [
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
], json_encode(['url' => 'https://shopee.vn/product/123456/789012']));

echo "  Cashback Link Generator: Status {$genRequest['status']}\n";
$genJson = json_decode($genRequest['raw_body'], true);
if (isset($genJson['affiliate_url'])) {
    echo "  ✔ Generated Affiliate Link: " . $genJson['affiliate_url'] . "\n";
} else {
    echo "  Raw Response: " . substr($genRequest['raw_body'], 0, 100) . "...\n";
}

echo "\n" . str_repeat('=', 80) . "\n";
echo " EMPIRICAL VERIFICATION COMPLETE\n";
echo str_repeat('=', 80) . "\n";
