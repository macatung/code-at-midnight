/**
 * Test Suite: Ma Cà Tưng Yin-Yang Fortune Oracle (F23_ORACLE_DIVINATION)
 * Tier 1: Feature Coverage (Isolation)
 * Tier 2: Boundary & Corner Cases
 * Tier 3: Cross-Feature Interactions
 * Tier 4: Real-World E2E Scenarios
 */

import { describe, it, expect, beforeEach, afterEach } from '../Harness/index.js';
import { setupTestEnvironment } from '../Harness/mock_helpers.js';
import {
  oracleFortunes,
  ORACLE_CATEGORIES,
  getRandomFortune,
} from '../../resources/js/data/oracleData.ts';
import type { OracleCategory } from '../../resources/js/data/oracleData.ts';

describe('OracleFortuneTest (F23_ORACLE_DIVINATION)', () => {
  let env: any;

  beforeEach(() => {
    env = setupTestEnvironment();
  });

  afterEach(() => {
    env.teardown();
  });

  // ==========================================================================
  // TIER 1: Feature Coverage (Isolation)
  // ==========================================================================
  describe('[T1_F23] Oracle Library Integrity & Records', () => {
    it('[T1_F23_01] All oracle fortunes contain 4-line poem, meanings for all 4 categories, and mascot advice', () => {
      expect(oracleFortunes.length).toBeGreaterThanOrEqual(8);
      oracleFortunes.forEach(fortune => {
        expect(fortune.id).toBeGreaterThan(0);
        expect(fortune.title.length).toBeGreaterThan(0);
        expect(fortune.level.length).toBeGreaterThan(0);
        expect(fortune.element.length).toBeGreaterThan(0);
        expect(fortune.score).toBeGreaterThanOrEqual(50);
        expect(fortune.score).toBeLessThanOrEqual(100);

        // Poem lines
        expect(fortune.poem.line1.length).toBeGreaterThan(0);
        expect(fortune.poem.line2.length).toBeGreaterThan(0);
        expect(fortune.poem.line3.length).toBeGreaterThan(0);
        expect(fortune.poem.line4.length).toBeGreaterThan(0);

        // All 4 categories
        expect(fortune.meanings.career.length).toBeGreaterThan(0);
        expect(fortune.meanings.wealth.length).toBeGreaterThan(0);
        expect(fortune.meanings.love.length).toBeGreaterThan(0);
        expect(fortune.meanings.peace.length).toBeGreaterThan(0);

        // Advice
        expect(fortune.mascotAdvice.length).toBeGreaterThan(0);
        expect(fortune.luckyColor.startsWith('#')).toBe(true);
        expect(fortune.luckyNumber).toBeGreaterThan(0);
      });
    });

    it('[T1_F23_02] All 4 divination categories are defined with valid icons and descriptors', () => {
      expect(ORACLE_CATEGORIES.length).toBe(4);
      const catIds = ORACLE_CATEGORIES.map(c => c.id);
      expect(catIds).toContain('career');
      expect(catIds).toContain('wealth');
      expect(catIds).toContain('love');
      expect(catIds).toContain('peace');
    });
  });

  // ==========================================================================
  // TIER 2: Boundary & Random Selection
  // ==========================================================================
  describe('[T2_F23] Divination Selection & Fallback', () => {
    it('[T2_F23_01] getRandomFortune returns a valid fortune for each category', () => {
      const categories: OracleCategory[] = ['career', 'wealth', 'love', 'peace'];
      categories.forEach(cat => {
        const fortune = getRandomFortune(cat);
        expect(fortune).toBeDefined();
        expect(fortune.meanings[cat]).toBeDefined();
        expect(fortune.meanings[cat].length).toBeGreaterThan(10);
      });
    });

    it('[T2_F23_02] Fortune levels are valid auspicious designations', () => {
      const validLevels = ['Thượng Thượng Cát', 'Đại Cát', 'Trung Cát', 'Tiểu Cát', 'Bình An Cát'];
      oracleFortunes.forEach(f => {
        expect(validLevels).toContain(f.level);
      });
    });
  });

  // ==========================================================================
  // TIER 4: Real-World E2E Scenario
  // ==========================================================================
  describe('[T4_F23] E2E Divination Flow Simulation', () => {
    it('[T4_F23_01] User selects Wealth -> Shakes Bamboo -> Tosses Yin-Yang Coins -> Receives Meaning', () => {
      // 1. User picks Wealth
      const chosenCategory: OracleCategory = 'wealth';
      const categoryInfo = ORACLE_CATEGORIES.find(c => c.id === chosenCategory);
      expect(categoryInfo?.label).toBe('Tài Lộc & Tiền Bạc');

      // 2. Shaking completes -> Coin toss
      const coinResult = { coin1: 'yang', coin2: 'yin' };
      const isConcordant = coinResult.coin1 !== coinResult.coin2;
      expect(isConcordant).toBe(true);

      // 3. Reveal Fortune
      const fortune = getRandomFortune(chosenCategory);
      expect(fortune.title).toBeDefined();
      expect(fortune.meanings.wealth.length).toBeGreaterThan(0);
      expect(fortune.mascotAdvice.length).toBeGreaterThan(0);
    });
  });
});
