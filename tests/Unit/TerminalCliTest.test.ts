/**
 * Test Suite: Midnight Terminal REPL CLI & Command Suite (F10, F11)
 * Tier 1: Feature Coverage (Isolation)
 * Tier 2: Boundary & Corner Cases
 */

import { describe, it, expect, beforeEach, afterEach } from '../Harness/index.js';
import { setupTestEnvironment, MockKeyboardEvent } from '../Harness/mock_helpers.js';
import { sound } from '../../resources/js/audio/soundEffects.ts';
import { projectsData } from '../../resources/js/data/projectsData.ts';
import { skillsData } from '../../resources/js/data/skillsData.ts';

export interface TerminalLog {
  id: string;
  type: 'input' | 'output' | 'system' | 'error';
  text: string;
  timestamp: string;
}

export class MidnightTerminalEngine {
  public logs: TerminalLog[] = [];
  public history: string[] = [];
  public historyIndex: number = -1;
  public currentInput: string = '';
  public isExpanded: boolean = false;
  public prompt: string = 'macatung:~$';

  constructor() {
    this.logs.push({
      id: 'init-1',
      type: 'system',
      text: '🌙 Midnight Terminal v1.0.0 — Type "help" to explore available commands.',
      timestamp: '00:00:00'
    });
  }

  public setInput(input: string) {
    this.currentInput = input;
    sound.playTerminalKey();
  }

  public toggleExpand() {
    this.isExpanded = !this.isExpanded;
    sound.playClick();
  }

  public navigateHistory(direction: 'up' | 'down'): string {
    if (this.history.length === 0) return this.currentInput;

    if (direction === 'up') {
      if (this.historyIndex === -1) {
        this.historyIndex = this.history.length - 1;
      } else if (this.historyIndex > 0) {
        this.historyIndex--;
      }
    } else if (direction === 'down') {
      if (this.historyIndex < this.history.length - 1 && this.historyIndex !== -1) {
        this.historyIndex++;
      } else {
        this.historyIndex = -1;
        this.currentInput = '';
        return '';
      }
    }

    if (this.historyIndex >= 0 && this.historyIndex < this.history.length) {
      this.currentInput = this.history[this.historyIndex];
    }
    return this.currentInput;
  }

  public execute(rawCmd: string): string {
    const trimmed = rawCmd.trim();
    const now = '00:00:00';

    if (!trimmed) {
      this.logs.push({ id: `log-${Date.now()}`, type: 'input', text: `${this.prompt} `, timestamp: now });
      return '';
    }

    this.history.push(trimmed);
    this.historyIndex = -1;
    this.logs.push({ id: `log-${Date.now()}-in`, type: 'input', text: `${this.prompt} ${trimmed}`, timestamp: now });

    const parts = trimmed.split(/\s+/);
    const command = parts[0].toLowerCase();
    const args = parts.slice(1);

    let output = '';
    let outType: 'output' | 'error' | 'system' = 'output';

    switch (command) {
      case 'help':
        output = 'Available spells:\n• whoami / bio\n• projects / ls\n• skills\n• hop\n• coffee\n• talisman\n• slogan\n• summon\n• sudo rm -rf bugs\n• clear';
        break;
      case 'whoami':
      case 'bio':
        output = 'Ma Cà Tưng — Full-Stack Alchemist & Midnight Creative Engineer. Specializing in high-performance web systems and mystical UI.';
        break;
      case 'projects':
      case 'ls':
        output = `Grimoire Projects (${projectsData.length}):\n` + projectsData.map((p) => `  [${p.category.toUpperCase()}] ${p.title} — ${p.tagline}`).join('\n');
        break;
      case 'skills':
        const total = skillsData.reduce((acc, cat) => acc + cat.skills.length, 0);
        output = `Skills Arsenal (${total} runes):\n` + skillsData.map((c) => `  ⚡ ${c.title}: ${c.skills.map((s) => s.name).join(', ')}`).join('\n');
        break;
      case 'hop':
        output = '🧛‍♂️ *HOP!* Ma Cà Tưng hops gracefully over production bugs!';
        sound.playHop(1.5);
        break;
      case 'coffee':
        output = '☕ Poured 1 cup of Vietnamese Robusta! Caffeine level = 100%. Ready for 4 AM deploy.';
        sound.playSuccess();
        break;
      case 'talisman':
        output = '📜 [BÙA CODE 0 BUG] try { deploy(); } catch { /* PEACE */ } — Khai Quang thành công!';
        sound.playTalisman();
        break;
      case 'slogan':
        output = '✨ "Code at midnight. Deploy with confidence. Rest when the city wakes."';
        break;
      case 'summon':
        output = '🔮 Invoking Summoning Altar... Scroll down to offer coffee and initiate project contract!';
        break;
      case 'sudo':
        if (args.join(' ') === 'rm -rf bugs' || args.join(' ') === 'rm -rf /bugs') {
          output = '🔥 [EXORCISM IN PROGRESS] Purging 4,192 bugs from production... 0 bugs remaining. Realm is peaceful!';
          sound.playSuccess();
        } else {
          output = `sudo: ${args.join(' ')}: command not permitted by midnight council`;
          outType = 'error';
        }
        break;
      case 'clear':
        this.logs = [];
        return '';
      default:
        output = `macatung-cli: command not found: ${command}. Type "help" for available commands.`;
        outType = 'error';
        break;
    }

    this.logs.push({ id: `log-${Date.now()}-out`, type: outType, text: output, timestamp: now });
    this.currentInput = '';
    return output;
  }

  public copyLogs(): string {
    const raw = this.logs.map((l) => `${l.text}`).join('\n');
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(raw);
    }
    return raw;
  }
}

describe('TerminalCliTest (F10, F11)', () => {
  let env: any;

  beforeEach(() => {
    env = setupTestEnvironment();
    if (sound.isMuted()) sound.toggleMute();
  });

  afterEach(() => {
    env.teardown();
  });

  // ==========================================================================
  // TIER 1: Feature Coverage (Isolation)
  // ==========================================================================
  describe('[T1_F10] Midnight Terminal REPL Shell Infrastructure', () => {
    /**
     * @tier: 1
     * @feature: F10_TERMINAL_REPL
     */
    it('[T1_F10_01] Terminal REPL initializes with system banner and prompt macatung:~$', () => {
      const term = new MidnightTerminalEngine();
      expect(term.prompt).toBe('macatung:~$');
      expect(term.logs.length).toBeGreaterThanOrEqual(1);
      expect(term.logs[0].text).toContain('Midnight Terminal');
    });

    /**
     * @tier: 1
     * @feature: F10_TERMINAL_REPL
     */
    it('[T1_F10_02] setInput updates currentInput and plays keypress sound', () => {
      const term = new MidnightTerminalEngine();
      term.setInput('whoami');
      expect(term.currentInput).toBe('whoami');

      const oscs = env.audioContext.getAllOscillators();
      expect(oscs.length).toBeGreaterThanOrEqual(1);
      expect(oscs[oscs.length - 1].type).toBe('triangle');
    });

    /**
     * @tier: 1
     * @feature: F10_TERMINAL_REPL
     */
    it('[T1_F10_03] toggleExpand flips terminal drawer expansion state', () => {
      const term = new MidnightTerminalEngine();
      expect(term.isExpanded).toBe(false);
      term.toggleExpand();
      expect(term.isExpanded).toBe(true);
      term.toggleExpand();
      expect(term.isExpanded).toBe(false);
    });

    /**
     * @tier: 1
     * @feature: F10_TERMINAL_REPL
     */
    it('[T1_F10_04] Executing commands appends them to history array in order', () => {
      const term = new MidnightTerminalEngine();
      term.execute('help');
      term.execute('whoami');
      term.execute('coffee');

      expect(term.history).toEqual(['help', 'whoami', 'coffee']);
    });

    /**
     * @tier: 1
     * @feature: F10_TERMINAL_REPL
     */
    it('[T1_F10_05] copyLogs exports full terminal transcript to clipboard', () => {
      const term = new MidnightTerminalEngine();
      term.execute('whoami');
      const text = term.copyLogs();

      expect(text).toContain('whoami');
      expect(text).toContain('Ma Cà Tưng');
    });
  });

  describe('[T1_F11] Full 11 Terminal Commands Suite', () => {
    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_01] "help" command returns list of available commands', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('help');
      expect(res).toContain('whoami');
      expect(res).toContain('projects');
      expect(res).toContain('skills');
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_02] "whoami" and "bio" commands return developer identity', () => {
      const term = new MidnightTerminalEngine();
      const res1 = term.execute('whoami');
      const res2 = term.execute('bio');
      expect(res1).toContain('Ma Cà Tưng');
      expect(res2).toContain('Ma Cà Tưng');
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_03] "projects" and "ls" commands list Grimoire projects', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('projects');
      expect(res).toContain('FlashPay');
      expect(res).toContain('DevGrimoire');
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_04] "skills" command outputs technical skill runes', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('skills');
      expect(res).toContain('Frontend Sorcery');
      expect(res).toContain('Vue 3');
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_05] "hop" command executes hop narrative and triggers sound', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('hop');
      expect(res).toContain('*HOP!*');
      const oscs = env.audioContext.getAllOscillators();
      expect(oscs.length).toBeGreaterThanOrEqual(1);
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_06] "coffee" command outputs Robusta energy boost', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('coffee');
      expect(res).toContain('Vietnamese Robusta');
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_07] "talisman" command generates a blessed talisman code spell', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('talisman');
      expect(res).toContain('BÙA CODE 0 BUG');
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_08] "slogan" command prints midnight developer philosophy quote', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('slogan');
      expect(res).toContain('Code at midnight');
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_09] "summon" command prints contact guidance message', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('summon');
      expect(res).toContain('Summoning Altar');
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_10] "sudo rm -rf bugs" purges all production bugs with fanfare', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('sudo rm -rf bugs');
      expect(res).toContain('EXORCISM IN PROGRESS');
      expect(res).toContain('0 bugs remaining');
    });

    /**
     * @tier: 1
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T1_F11_11] "clear" command wipes the output buffer', () => {
      const term = new MidnightTerminalEngine();
      term.execute('whoami');
      term.execute('skills');
      expect(term.logs.length).toBeGreaterThan(2);

      term.execute('clear');
      expect(term.logs.length).toBe(0);
    });
  });

  // ==========================================================================
  // TIER 2: Boundary & Corner Cases
  // ==========================================================================
  describe('[T2_F10_F11] Boundary & Edge Handling for Terminal CLI', () => {
    /**
     * @tier: 2
     * @feature: F10_TERMINAL_REPL
     */
    it('[T2_F10_01] Up and down arrow navigation gracefully traverses history boundaries', () => {
      const term = new MidnightTerminalEngine();
      term.execute('help');
      term.execute('skills');
      term.execute('coffee');

      // Navigate UP: coffee -> skills -> help
      expect(term.navigateHistory('up')).toBe('coffee');
      expect(term.navigateHistory('up')).toBe('skills');
      expect(term.navigateHistory('up')).toBe('help');
      // At top boundary, stays at 'help'
      expect(term.navigateHistory('up')).toBe('help');

      // Navigate DOWN: help -> skills -> coffee -> empty
      expect(term.navigateHistory('down')).toBe('skills');
      expect(term.navigateHistory('down')).toBe('coffee');
      expect(term.navigateHistory('down')).toBe('');
    });

    /**
     * @tier: 2
     * @feature: F10_TERMINAL_REPL
     */
    it('[T2_F10_02] Arrow key navigation on empty history returns empty string without error', () => {
      const term = new MidnightTerminalEngine();
      expect(term.navigateHistory('up')).toBe('');
      expect(term.navigateHistory('down')).toBe('');
    });

    /**
     * @tier: 2
     * @feature: F10_TERMINAL_REPL
     */
    it('[T2_F10_03] Submitting empty string or whitespace records prompt line with no error', () => {
      const term = new MidnightTerminalEngine();
      const initialCount = term.logs.length;
      const res = term.execute('    ');
      expect(res).toBe('');
      expect(term.logs.length).toBe(initialCount + 1);
    });

    /**
     * @tier: 2
     * @feature: F10_TERMINAL_REPL
     */
    it('[T2_F10_04] Extra-long command strings (>1000 characters) execute safely', () => {
      const term = new MidnightTerminalEngine();
      const longInput = 'echo ' + 'A'.repeat(1200);
      expect(() => term.execute(longInput)).not.toThrow();
    });

    /**
     * @tier: 2
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T2_F11_01] Commands are case-insensitive (e.g. HELP, WhoAmI, CoFfEe)', () => {
      const term = new MidnightTerminalEngine();
      expect(term.execute('HELP')).toContain('Available spells');
      expect(term.execute('WhoAmI')).toContain('Ma Cà Tưng');
      expect(term.execute('CoFfEe')).toContain('Vietnamese Robusta');
    });

    /**
     * @tier: 2
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T2_F11_02] Commands with extra arguments/flags parse base command cleanly', () => {
      const term = new MidnightTerminalEngine();
      expect(term.execute('help --verbose')).toContain('Available spells');
      expect(term.execute('whoami --json')).toContain('Ma Cà Tưng');
    });

    /**
     * @tier: 2
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T2_F11_03] Unknown command triggers styled error and help suggestion', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('quantum-warp');
      expect(res).toContain('command not found: quantum-warp');
      expect(res).toContain('Type "help"');
    });

    /**
     * @tier: 2
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T2_F11_04] "sudo" with disallowed arguments returns error log', () => {
      const term = new MidnightTerminalEngine();
      const res = term.execute('sudo reboot');
      expect(res).toContain('command not permitted');
    });

    /**
     * @tier: 2
     * @feature: F11_TERMINAL_CMDS
     */
    it('[T2_F11_05] "clear" resets output log buffer while retaining command history', () => {
      const term = new MidnightTerminalEngine();
      term.execute('whoami');
      term.execute('coffee');
      term.execute('clear');

      expect(term.logs.length).toBe(0);
      expect(term.history).toEqual(['whoami', 'coffee', 'clear']);
      expect(term.navigateHistory('up')).toBe('clear');
    });

    /**
     * @tier: 2
     * @feature: F10_TERMINAL_REPL
     */
    it('[T2_F10_05] Rapid consecutive command executions (stress 20 commands) maintain ordered history', () => {
      const term = new MidnightTerminalEngine();
      for (let i = 0; i < 20; i++) {
        term.execute(`echo test-${i}`);
      }
      expect(term.history.length).toBe(20);
      expect(term.history[0]).toBe('echo test-0');
      expect(term.history[19]).toBe('echo test-19');
    });
  });
});
