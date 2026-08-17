/**
 * Test Suite: Midnight Clock & Live Status (F17)
 * Tier 1: Feature Coverage (Isolation)
 * Tier 2: Boundary & Corner Cases
 */

import { describe, it, expect, beforeEach, afterEach } from '../Harness/index.js';
import { setupTestEnvironment } from '../Harness/mock_helpers.js';

export class MidnightClockModel {
  public currentTime: Date;
  public pingMs: number = 18;

  constructor(initialDate?: Date) {
    this.currentTime = initialDate || new Date();
  }

  public setTime(date: Date) {
    this.currentTime = date;
  }

  public getFormattedTime(): string {
    const pad = (n: number) => String(n).padStart(2, '0');
    const hours = pad(this.currentTime.getHours());
    const minutes = pad(this.currentTime.getMinutes());
    const seconds = pad(this.currentTime.getSeconds());
    return `${hours}:${minutes}:${seconds}`;
  }

  public isMidnightMode(): boolean {
    const hours = this.currentTime.getHours();
    return hours >= 0 && hours < 5;
  }

  public getStatusBadge(): { mode: 'midnight' | 'daylight'; text: string } {
    if (this.isMidnightMode()) {
      return { mode: 'midnight', text: '🌙 Midnight Mode — Maximum Flow' };
    }
    return { mode: 'daylight', text: '☀️ Daylight Prep — Recharging' };
  }

  public getCaffeineLevel(): number {
    const hour = this.currentTime.getHours();
    // Peak caffeine between 1:00 AM and 4:00 AM (90 - 100%)
    if (hour >= 1 && hour <= 4) return 100;
    if (hour === 0 || hour === 5) return 85;
    if (hour >= 6 && hour <= 9) return 40;
    if (hour >= 10 && hour <= 17) return 25;
    if (hour >= 18 && hour <= 21) return 50;
    return 75; // 22:00 - 23:59
  }

  public getSimulatedPing(): number {
    // Return realistic sub-50ms ping
    return Math.max(8, Math.min(48, Math.floor(14 + Math.random() * 10)));
  }

  public tick(): string {
    this.currentTime = new Date(this.currentTime.getTime() + 1000);
    return this.getFormattedTime();
  }
}

describe('MidnightClockTest (F17)', () => {
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
  describe('[T1_F17] Midnight Clock & Real-time Live Status', () => {
    /**
     * @tier: 1
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T1_F17_01] Clock formats time string strictly as HH:mm:ss with 2-digit zero padding', () => {
      const clock = new MidnightClockModel(new Date('2026-08-17T03:07:09'));
      expect(clock.getFormattedTime()).toBe('03:07:09');
    });

    /**
     * @tier: 1
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T1_F17_02] 03:00 AM activates Midnight Mode badge', () => {
      const clock = new MidnightClockModel(new Date('2026-08-17T03:00:00'));
      expect(clock.isMidnightMode()).toBe(true);
      expect(clock.getStatusBadge().mode).toBe('midnight');
      expect(clock.getStatusBadge().text).toContain('Midnight Mode');
    });

    /**
     * @tier: 1
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T1_F17_03] 14:00 PM activates Daylight Prep badge', () => {
      const clock = new MidnightClockModel(new Date('2026-08-17T14:00:00'));
      expect(clock.isMidnightMode()).toBe(false);
      expect(clock.getStatusBadge().mode).toBe('daylight');
      expect(clock.getStatusBadge().text).toContain('Daylight Prep');
    });

    /**
     * @tier: 1
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T1_F17_04] Caffeine level calculator computes 100% peak during 01:00-04:00 AM', () => {
      const clock = new MidnightClockModel(new Date('2026-08-17T02:30:00'));
      expect(clock.getCaffeineLevel()).toBe(100);
    });

    /**
     * @tier: 1
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T1_F17_05] Clock tick increments time by 1 second', () => {
      const clock = new MidnightClockModel(new Date('2026-08-17T23:59:58'));
      expect(clock.getFormattedTime()).toBe('23:59:58');
      expect(clock.tick()).toBe('23:59:59');
      expect(clock.tick()).toBe('00:00:00');
    });
  });

  // ==========================================================================
  // TIER 2: Boundary & Corner Cases
  // ==========================================================================
  describe('[T2_F17] Boundary & Time Transition Handling', () => {
    /**
     * @tier: 2
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T2_F17_01] Exact midnight boundary 00:00:00 triggers Midnight Mode transition', () => {
      const clock = new MidnightClockModel(new Date('2026-08-17T00:00:00'));
      expect(clock.isMidnightMode()).toBe(true);
      expect(clock.getFormattedTime()).toBe('00:00:00');
      expect(clock.getStatusBadge().mode).toBe('midnight');
    });

    /**
     * @tier: 2
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T2_F17_02] Exact 05:00:00 morning boundary transitions cleanly to Daylight Prep', () => {
      const clock = new MidnightClockModel(new Date('2026-08-17T05:00:00'));
      expect(clock.isMidnightMode()).toBe(false);
      expect(clock.getFormattedTime()).toBe('05:00:00');
      expect(clock.getStatusBadge().mode).toBe('daylight');
    });

    /**
     * @tier: 2
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T2_F17_03] 23:59:59 late night boundary is in Daylight Prep state before rollover', () => {
      const clock = new MidnightClockModel(new Date('2026-08-17T23:59:59'));
      expect(clock.isMidnightMode()).toBe(false);
      clock.tick();
      expect(clock.isMidnightMode()).toBe(true); // rolls over to 00:00:00
    });

    /**
     * @tier: 2
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T2_F17_04] Caffeine level is bounded strictly between 0% and 100% across all 24 hours', () => {
      for (let h = 0; h < 24; h++) {
        const date = new Date(`2026-08-17T${String(h).padStart(2, '0')}:00:00`);
        const clock = new MidnightClockModel(date);
        const level = clock.getCaffeineLevel();
        expect(level).toBeGreaterThanOrEqual(0);
        expect(level).toBeLessThanOrEqual(100);
      }
    });

    /**
     * @tier: 2
     * @feature: F17_MIDNIGHT_CLOCK
     */
    it('[T2_F17_05] Simulated ping returns positive integer ms strictly under 100ms', () => {
      const clock = new MidnightClockModel();
      for (let i = 0; i < 20; i++) {
        const ping = clock.getSimulatedPing();
        expect(ping).toBeGreaterThan(0);
        expect(ping).toBeLessThan(100);
      }
    });
  });
});
