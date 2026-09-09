/**
 * Test Suite: Ma Giải Mã (decode.macatung.dev) Subdomain Routing & Brand Identity Conformance
 * Tier 1: Asset Validation (SVG, PNG, ICO, Favicon, README)
 * Tier 2: Controller & Data Conformance (5 Pilot Season Episodes, Slugs, Key Nodes)
 * Tier 3: Subdomain & Fallback Routing Definitions
 */

import { describe, it, expect } from '../Harness/index.js';
import fs from 'fs';
import path from 'path';

describe('DecodeSubdomainRoutingAndBrandTest (Ma Giải Mã Brand Kit & Routing)', () => {
  const brandDecodeDir = path.resolve(process.cwd(), 'public/brand/decode');
  const webPhpPath = path.resolve(process.cwd(), 'routes/web.php');
  const controllerPath = path.resolve(process.cwd(), 'app/Http/Controllers/Decode/DecodeController.php');
  const layoutPath = path.resolve(process.cwd(), 'resources/js/Layouts/DecodeLayout.vue');
  const indexPath = path.resolve(process.cwd(), 'resources/js/Pages/Decode/Index.vue');
  const showPath = path.resolve(process.cwd(), 'resources/js/Pages/Decode/Show.vue');
  const brandPath = path.resolve(process.cwd(), 'resources/js/Pages/Decode/Brand.vue');

  // ==========================================================================
  // TIER 1: BRAND ASSET INTEGRITY
  // ==========================================================================
  it('[ASSET_01] Brand README guidelines exists and contains core brand values', () => {
    const readmePath = path.join(brandDecodeDir, 'README.md');
    expect(fs.existsSync(readmePath)).toBe(true);
    const content = fs.readFileSync(readmePath, 'utf8');
    expect(content.includes('Ma Giải Mã')).toBe(true);
    expect(content.includes('@MaGiaiMa')).toBe(true);
    expect(content.includes('decode.macatung.dev')).toBe(true);
    expect(content.includes('Mở nắp những hệ thống vô hình vận hành thế giới')).toBe(true);
    expect(content.includes('System Anatomist')).toBe(true);
  });

  it('[ASSET_02] Official vector SVGs exist and are non-empty', () => {
    const expectedSvgs = [
      'decode-badge-avatar.svg',
      'decode-logo-horizontal.svg',
      'decode-banner-youtube.svg',
      'decode-mascot-cad.svg',
      'og-decode-1200x630.svg',
      'favicon.svg',
    ];

    for (const svgName of expectedSvgs) {
      const p = path.join(brandDecodeDir, svgName);
      expect(fs.existsSync(p)).toBe(true);
      const content = fs.readFileSync(p, 'utf8');
      expect(content.length).toBeGreaterThan(100);
      expect(content.includes('<svg')).toBe(true);
      expect(content.includes('</svg>')).toBe(true);
    }
  });

  it('[ASSET_03] High-resolution PNGs and ICO files exist', () => {
    const expectedPngs = [
      'decode-badge-avatar.png',
      'decode-badge-avatar-512.png',
      'decode-logo-horizontal.png',
      'decode-mascot-cad.png',
      'mascot-ma-giai-ma-3d.png',
      'decode-avatar-3d-512.png',
      'og-decode-1200x630.png',
      'decode-banner-youtube.png',
      'favicon-decode-32x32.png',
      'favicon-decode.ico',
    ];

    for (const file of expectedPngs) {
      const p = path.join(brandDecodeDir, file);
      expect(fs.existsSync(p)).toBe(true);
      const stat = fs.statSync(p);
      expect(stat.size).toBeGreaterThan(500);
    }
  });

  it('[ASSET_04] Distinct 3D Mascot asset is present and distinct', () => {
    const mascot3d = path.join(brandDecodeDir, 'mascot-ma-giai-ma-3d.png');
    expect(fs.existsSync(mascot3d)).toBe(true);
    const stat = fs.statSync(mascot3d);
    expect(stat.size).toBeGreaterThan(10000);
  });

  // ==========================================================================
  // TIER 2: CONTROLLER & PILOT SEASON EPISODES CONFORMANCE
  // ==========================================================================
  it('[CONTROLLER_01] DecodeController.php exists and defines pilotEpisodes', () => {
    expect(fs.existsSync(controllerPath)).toBe(true);
    const code = fs.readFileSync(controllerPath, 'utf8');
    expect(code.includes('class DecodeController extends Controller')).toBe(true);
    expect(code.includes('$pilotEpisodes')).toBe(true);
    expect(code.includes('tap-01-quet-the-visa-100k-2-giay-du-hanh')).toBe(true);
    expect(code.includes('tap-02-google-tim-kiem-50-ty-trang-web-0-3-giay')).toBe(true);
    expect(code.includes('tap-03-cuoc-goi-xuyen-luc-dia-cap-quang-day-bien')).toBe(true);
    expect(code.includes('tap-04-cay-atm-khong-bao-gio-nha-nham-tien')).toBe(true);
    expect(code.includes('tap-05-bam-dat-grab-ve-tinh-gps-thuyet-tuong-doi-einstein')).toBe(true);
  });

  it('[CONTROLLER_02] Episode 01 Visa 100k contains full key nodes breakdown', () => {
    const code = fs.readFileSync(controllerPath, 'utf8');
    expect(code.includes('Máy POS & Thẻ Chip EMV')).toBe(true);
    expect(code.includes('Ngân hàng Thanh Toán (Acquirer Bank)')).toBe(true);
    expect(code.includes('Mạng Lưới Toàn Cầu VisaNet')).toBe(true);
    expect(code.includes('Visa Advanced Authorization')).toBe(true);
    expect(code.includes('Ngân Hàng Phát Hành Thẻ (Issuer Bank)')).toBe(true);
    expect(code.includes('1.85s')).toBe(true);
  });

  it('[CONTROLLER_03] Controller implements index, show, episode1, brand, sitemap, robots', () => {
    const code = fs.readFileSync(controllerPath, 'utf8');
    expect(code.includes('public function index()')).toBe(true);
    expect(code.includes('public function show(')).toBe(true);
    expect(code.includes('public function episode1()')).toBe(true);
    expect(code.includes('public function brand()')).toBe(true);
    expect(code.includes('public function sitemap()')).toBe(true);
    expect(code.includes('public function robots()')).toBe(true);
  });

  // ==========================================================================
  // TIER 3: SUBDOMAIN & ROUTING INTEGRITY
  // ==========================================================================
  it('[ROUTING_01] routes/web.php defines decode subdomain and path fallback', () => {
    expect(fs.existsSync(webPhpPath)).toBe(true);
    const code = fs.readFileSync(webPhpPath, 'utf8');
    expect(code.includes("Route::domain('decode.' . $baseDomain)")).toBe(true);
    expect(code.includes("Route::prefix('decode')->name('decode.')")).toBe(true);
    expect(code.includes("DecodeController::class, 'index'")).toBe(true);
    expect(code.includes("DecodeController::class, 'show'")).toBe(true);
    expect(code.includes("DecodeController::class, 'brand'")).toBe(true);
  });

  it('[ROUTING_02] Favicon endpoint serves decode-specific favicon for decode subdomain', () => {
    const code = fs.readFileSync(webPhpPath, 'utf8');
    expect(code.includes("str_starts_with($request->getHost(), 'decode.')")).toBe(true);
    expect(code.includes('brand/decode/favicon-decode.ico')).toBe(true);
  });

  // ==========================================================================
  // TIER 4: FRONTEND COMPONENTS CONFORMANCE
  // ==========================================================================
  it('[FRONTEND_01] DecodeLayout.vue exists with HUD, telemetry and channel links', () => {
    expect(fs.existsSync(layoutPath)).toBe(true);
    const code = fs.readFileSync(layoutPath, 'utf8');
    expect(code.includes('MA GIẢI MÃ')).toBe(true);
    expect(code.includes('@MaGiaiMa')).toBe(true);
    expect(code.includes('decode.macatung.dev')).toBe(true);
  });

  it('[FRONTEND_02] Decode/Index.vue, Show.vue, Brand.vue exist and are valid components', () => {
    expect(fs.existsSync(indexPath)).toBe(true);
    expect(fs.existsSync(showPath)).toBe(true);
    expect(fs.existsSync(brandPath)).toBe(true);

    const indexCode = fs.readFileSync(indexPath, 'utf8');
    expect(indexCode.includes('anatomy-visualizer')).toBe(true);
    expect(indexCode.includes('mascot-ma-giai-ma-3d.png')).toBe(true);

    const brandCode = fs.readFileSync(brandPath, 'utf8');
    expect(brandCode.includes('The Systems Anatomist')).toBe(true);
    expect(brandCode.includes('copyColor')).toBe(true);
  });
});
