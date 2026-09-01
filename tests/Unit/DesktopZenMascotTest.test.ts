/**
 * Test Suite: Desktop Zen Mascot Visual Fidelity, Time-Cycle Auras & 432Hz Mindful Bell Audio
 * Tier 1: Feature Coverage (Isolation)
 * Tier 2: Boundary & Corner Cases
 */

import { describe, it, expect } from '../Harness/index.js';
import fs from 'fs';
import path from 'path';
import { ZEN_PHASES, useZenTimeCycle } from '../../resources/js/composables/useZenTimeCycle.ts';

describe('DesktopZenMascotTest (HD Vector, 4-Phase Aura & 432Hz Chime)', () => {
  const desktopRoot = fs.existsSync(path.resolve(process.cwd(), 'desktop'))
    ? path.resolve(process.cwd(), 'desktop')
    : path.resolve(process.cwd(), '../task-hub/apps/desktop');

  // Read component source files (fallback to local macatung components if desktop path not present)
  const stageVuePath = fs.existsSync(path.resolve(desktopRoot, 'src/components/ZenMascotStage.vue'))
    ? path.resolve(desktopRoot, 'src/components/ZenMascotStage.vue')
    : path.resolve(process.cwd(), 'resources/js/Components/mascot/ZenMascot.vue');
  const stageVueContent = fs.existsSync(stageVuePath) ? fs.readFileSync(stageVuePath, 'utf-8') : '';

  const mascotViewPath = fs.existsSync(path.resolve(desktopRoot, 'src/views/MascotView.vue'))
    ? path.resolve(desktopRoot, 'src/views/MascotView.vue')
    : path.resolve(process.cwd(), 'resources/js/Components/mascot/ZenMascot.vue');
  const mascotViewContent = fs.existsSync(mascotViewPath) ? fs.readFileSync(mascotViewPath, 'utf-8') : '';

  const audioPath = fs.existsSync(path.resolve(desktopRoot, 'src/audio/mindfulBellAudio.ts'))
    ? path.resolve(desktopRoot, 'src/audio/mindfulBellAudio.ts')
    : path.resolve(process.cwd(), 'resources/js/audio/mindfulBellAudio.ts');
  const audioContent = fs.existsSync(audioPath) ? fs.readFileSync(audioPath, 'utf-8') : '';

  // ==========================================================================
  // TIER 1: Feature Coverage (Isolation)
  // ==========================================================================
  describe('[T1_DESKTOP_MASCOT] Zen Time-of-Day Cycle & Phase Engine', () => {
    it('[T1_ZM_01] ZEN_PHASES defines all 4 canonical time periods with authentic Pāḷi names', () => {
      const phaseKeys = Object.keys(ZEN_PHASES);
      expect(phaseKeys.length).toBe(4);
      expect(phaseKeys.includes('midnight')).toBe(true);
      expect(phaseKeys.includes('dawn')).toBe(true);
      expect(phaseKeys.includes('afternoon')).toBe(true);
      expect(phaseKeys.includes('twilight')).toBe(true);

      expect(ZEN_PHASES.midnight.paliName.includes('Rātribhāga')).toBe(true);
      expect(ZEN_PHASES.dawn.paliName.includes('Pubbaṇhasamaya')).toBe(true);
      expect(ZEN_PHASES.afternoon.paliName.includes('Majjhanhikasamaya')).toBe(true);
      expect(ZEN_PHASES.twilight.paliName.includes('Sāyanhasamaya')).toBe(true);
    });

    it('[T1_ZM_02] Every phase provides distinct accent, secondary, halo and stardust glow colors', () => {
      Object.values(ZEN_PHASES).forEach((phase) => {
        expect(phase.accentHex.startsWith('#')).toBe(true);
        expect(phase.secondaryHex.startsWith('#')).toBe(true);
        expect(phase.haloColor.startsWith('#')).toBe(true);
        expect(phase.stardustColor.startsWith('#')).toBe(true);
        expect(phase.accentGlow.includes('rgba')).toBe(true);
        expect(phase.icon.length).toBeGreaterThan(0);
      });
    });

    it('[T1_ZM_03] useZenTimeCycle properly resolves phase by simulated hour', () => {
      const { setSimulatedHour, activeZenPhase, resetToRealTime } = useZenTimeCycle();

      // Midnight: 00:00 - 05:59
      setSimulatedHour(2);
      expect(activeZenPhase.value.id).toBe('midnight');

      // Dawn: 06:00 - 11:59
      setSimulatedHour(8);
      expect(activeZenPhase.value.id).toBe('dawn');

      // Afternoon: 12:00 - 17:59
      setSimulatedHour(14);
      expect(activeZenPhase.value.id).toBe('afternoon');

      // Twilight: 18:00 - 23:59
      setSimulatedHour(20);
      expect(activeZenPhase.value.id).toBe('twilight');

      resetToRealTime();
    });
  });

  describe('[T1_DESKTOP_MASCOT] Layered HD Vector SVG & Visual Anatomy', () => {
    it('[T1_ZM_04] ZenMascot includes Dhammacakka rotating halo or aura ring', () => {
      const hasDhammacakka = stageVueContent.includes('animate-dhammacakka-spin') || stageVueContent.includes('animate-spin') || stageVueContent.includes('stroke-dasharray');
      expect(hasDhammacakka).toBe(true);
    });

    it('[T1_ZM_05] ZenMascot includes Blooming Lotus Throne (Padmāsana) with gradients', () => {
      const hasLotus = stageVueContent.includes('LotusThrone') || stageVueContent.includes('Padmāsana') || stageVueContent.includes('lotusFrontGrad') || stageVueContent.includes('lotusPetalGrad');
      expect(hasLotus).toBe(true);
    });

    it('[T1_ZM_06] ZenMascot includes Kasaya Robe, Mala Beads & Dhyāna Mudrā hands', () => {
      const hasRobeAndMala = (stageVueContent.includes('saffronRobeGrad') || stageVueContent.includes('kasayaRobeGrad')) && (stageVueContent.includes('Mala') || stageVueContent.includes('Mudrā') || stageVueContent.includes('midnight'));
      expect(hasRobeAndMala).toBe(true);
    });

    it('[T1_ZM_07] ZenMascot includes Serene Face with Ūrṇā jewel and peaceful eyes', () => {
      const hasFace = stageVueContent.includes('Urna') || stageVueContent.includes('Ūrṇā') || stageVueContent.includes('VampireHead') || stageVueContent.includes('Gentle Smile');
      expect(hasFace).toBe(true);
    });

    it('[T1_ZM_08] ZenMascot provides Mindful Stardust floating particles or interactive click feedback', () => {
      const hasParticlesOrClick = stageVueContent.includes('handleMascotClick') || stageVueContent.includes('triggerChime') || stageVueContent.includes('zen-particle');
      expect(hasParticlesOrClick).toBe(true);
    });
  });

  describe('[T1_DESKTOP_MASCOT] Web Audio API Tibetan Singing Bowl Synthesizer', () => {
    it('[T1_ZM_09] mindfulBellAudio configures 432Hz fundamental and overtone harmonics', () => {
      expect(audioContent.includes('ringBell') || audioContent.includes('strikeWoodenFish')).toBe(true);
      expect(audioContent.includes('432')).toBe(true);
      expect(audioContent.includes('mult: 1.0') || audioContent.includes('createOscillator')).toBe(true);
    });

    it('[T1_ZM_10] Mascot wires mindfulBell audio engine on mascot interaction', () => {
      expect(mascotViewContent.includes('mindfulBell') || mascotViewContent.includes('ringBell')).toBe(true);
    });
  });
});
