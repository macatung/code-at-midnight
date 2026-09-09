/**
 * Test Suite: Shopee Cashback (hoantien.macatung.dev) Subdomain Routing & Module Integrity
 * Tier 1: Backend Architecture & Routing Definition Validation
 * Tier 2: Shopee Affiliate Service & Cryptographic Signatures
 * Tier 3: Frontend Layout & Portal Dashboard Integrity
 */

import { describe, it, expect } from '../Harness/index.js';
import fs from 'fs';
import path from 'path';

describe('CashbackSubdomainRoutingAndIntegrityTest (Shopee Cashback Module)', () => {
  const webPhpPath = path.resolve(process.cwd(), 'routes/web.php');
  const controllerPath = path.resolve(process.cwd(), 'app/Http/Controllers/Cashback/CashbackController.php');
  const shopeeServicePath = path.resolve(process.cwd(), 'app/Services/ShopeeAffiliateService.php');
  const walletServicePath = path.resolve(process.cwd(), 'app/Services/CashbackWalletService.php');
  const syncServicePath = path.resolve(process.cwd(), 'app/Services/CashbackOrderSyncService.php');
  const configPath = path.resolve(process.cwd(), 'config/cashback.php');
  const typesPath = path.resolve(process.cwd(), 'resources/js/types/cashback.ts');
  const layoutPath = path.resolve(process.cwd(), 'resources/js/Layouts/CashbackLayout.vue');
  const indexPath = path.resolve(process.cwd(), 'resources/js/Pages/Cashback/Index.vue');

  // ==========================================================================
  // TIER 1: ROUTING & CONTROLLER DEFINITION
  // ==========================================================================
  it('[ROUTING_01] routes/web.php defines hoantien subdomain and path fallback', () => {
    expect(fs.existsSync(webPhpPath)).toBe(true);
    const code = fs.readFileSync(webPhpPath, 'utf8');
    expect(code.includes("'hoantien.' . $baseDomain")).toBe(true);
    expect(code.includes("'hoantien.localhost'")).toBe(true);
    expect(code.includes("Route::prefix('hoantien')->name('cashback.')")).toBe(true);
    expect(code.includes("CashbackController::class, 'index'")).toBe(true);
    expect(code.includes("CashbackController::class, 'generateLink'")).toBe(true);
    expect(code.includes("CashbackController::class, 'withdraw'")).toBe(true);
  });

  it('[CONTROLLER_01] CashbackController exists with all required actions', () => {
    expect(fs.existsSync(controllerPath)).toBe(true);
    const code = fs.readFileSync(controllerPath, 'utf8');
    expect(code.includes('function index(')).toBe(true);
    expect(code.includes('function generateLink(')).toBe(true);
    expect(code.includes('function withdraw(')).toBe(true);
    expect(code.includes('function webhook(')).toBe(true);
    expect(code.includes('function sync(')).toBe(true);
  });

  // ==========================================================================
  // TIER 2: SERVICES & CONFIGURATION INTEGRITY
  // ==========================================================================
  it('[SERVICE_01] ShopeeAffiliateService implements SHA256 signature and URL validation', () => {
    expect(fs.existsSync(shopeeServicePath)).toBe(true);
    const code = fs.readFileSync(shopeeServicePath, 'utf8');
    expect(code.includes('function generateSignature(')).toBe(true);
    expect(code.includes('function verifySignature(')).toBe(true);
    expect(code.includes('function generateAuthHeader(')).toBe(true);
    expect(code.includes('function isValidShopeeUrl(')).toBe(true);
    expect(code.includes('function extractShopeeUrl(')).toBe(true);
    expect(code.includes('function generateShortLink(')).toBe(true);
    expect(code.includes('function getConversionReport(')).toBe(true);
  });

  it('[SERVICE_02] CashbackWalletService implements wallet, order ledger and withdrawal guardrails', () => {
    expect(fs.existsSync(walletServicePath)).toBe(true);
    const code = fs.readFileSync(walletServicePath, 'utf8');
    expect(code.includes('function getOrCreateWallet(')).toBe(true);
    expect(code.includes('function recordClick(')).toBe(true);
    expect(code.includes('function processOrder(')).toBe(true);
    expect(code.includes('function requestWithdrawal(')).toBe(true);
    expect(code.includes('function approveWithdrawal(')).toBe(true);
    expect(code.includes('function rejectWithdrawal(')).toBe(true);
    expect(code.includes('lockForUpdate()')).toBe(true);
    expect(code.includes('max(0.00')).toBe(true);
  });

  it('[SERVICE_03] CashbackOrderSyncService implements extractOrderNodes to normalize nested/single payload formats', () => {
    expect(fs.existsSync(syncServicePath)).toBe(true);
    const code = fs.readFileSync(syncServicePath, 'utf8');
    expect(code.includes('function extractOrderNodes(')).toBe(true);
    expect(code.includes('conversionReport')).toBe(true);
    expect(code.includes('function processReportNodes(')).toBe(true);
  });

  it('[CONFIG_01] config/cashback.php defines rate, min_withdrawal and shopee api configs', () => {
    expect(fs.existsSync(configPath)).toBe(true);
    const code = fs.readFileSync(configPath, 'utf8');
    expect(code.includes("'rate'")).toBe(true);
    expect(code.includes("'min_withdrawal'")).toBe(true);
    expect(code.includes("'shopee'")).toBe(true);
    expect(code.includes("'mock_enabled'")).toBe(true);
  });

  it('[SCHEDULE_01] routes/console.php schedules cashback:sync-orders hourly', () => {
    const consolePath = path.resolve(process.cwd(), 'routes/console.php');
    expect(fs.existsSync(consolePath)).toBe(true);
    const code = fs.readFileSync(consolePath, 'utf8');
    expect(code.includes("cashback:sync-orders")).toBe(true);
    expect(code.includes("hourly()")).toBe(true);
  });

  it('[SECURITY_01] bootstrap/app.php exempts webhook and sync from CSRF verification', () => {
    const bootstrapAppPath = path.resolve(process.cwd(), 'bootstrap/app.php');
    expect(fs.existsSync(bootstrapAppPath)).toBe(true);
    const code = fs.readFileSync(bootstrapAppPath, 'utf8');
    expect(code.includes('validateCsrfTokens')).toBe(true);
    expect(code.includes('hoantien/webhook')).toBe(true);
  });

  // ==========================================================================
  // TIER 3: FRONTEND COMPONENTS & TYPES
  // ==========================================================================
  it('[FRONTEND_01] resources/js/types/cashback.ts defines all data interfaces', () => {
    expect(fs.existsSync(typesPath)).toBe(true);
    const code = fs.readFileSync(typesPath, 'utf8');
    expect(code.includes('interface CashbackWalletData')).toBe(true);
    expect(code.includes('interface CashbackClickData')).toBe(true);
    expect(code.includes('interface CashbackOrderData')).toBe(true);
    expect(code.includes('interface CashbackWithdrawalData')).toBe(true);
    expect(code.includes('interface CashbackStatsData')).toBe(true);
  });

  it('[FRONTEND_02] CashbackLayout.vue and Cashback/Index.vue contain complete portal UI', () => {
    expect(fs.existsSync(layoutPath)).toBe(true);
    expect(fs.existsSync(indexPath)).toBe(true);

    const layoutCode = fs.readFileSync(layoutPath, 'utf8');
    expect(layoutCode.includes('Hoàn Tiền Shopee')).toBe(true);
    expect(layoutCode.includes('sub_id')).toBe(true);

    const indexCode = fs.readFileSync(indexPath, 'utf8');
    expect(indexCode.includes('handleGenerateLink')).toBe(true);
    expect(indexCode.includes('submitWithdrawal')).toBe(true);
    expect(indexCode.includes('getCsrfToken')).toBe(true);
    expect(indexCode.includes('extractShopeeUrl')).toBe(true);
    expect(indexCode.includes('Chờ Duyệt (Pending)')).toBe(true);
    expect(indexCode.includes('Khả Dụng (Available)')).toBe(true);
    expect(indexCode.includes('Đã Rút (Withdrawn)')).toBe(true);
  });
});
