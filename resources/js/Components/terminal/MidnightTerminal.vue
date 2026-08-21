<script setup lang="ts">
import { ref, nextTick } from 'vue';
import { projectsData } from '@/data/projectsData';
import { skillsData } from '@/data/skillsData';
import { sound } from '@/audio/soundEffects';
import { trackEvent } from '@/utils/analytics';
import { useTimeCycle, TimePhaseId } from '@/composables/useTimeCycle';
import { useI18n } from '@/composables/useI18n';

const { t } = useI18n();

const {
  formattedTime,
  activePhaseId,
  activePhase,
  isTimeTravelActive,
  TIME_PHASES,
  setPhaseOverride,
  resetToRealTime
} = useTimeCycle();

export interface TerminalLog {
  id: string;
  type: 'input' | 'output' | 'system' | 'error';
  text: string;
  timestamp: string;
}

const emit = defineEmits<{
  (e: 'hop-requested'): void;
  (e: 'summon-requested'): void;
  (e: 'command-executed', cmd: string, output: string): void;
}>();

const prompt = ref('macatung:~$');
const currentInput = ref('');
const history = ref<string[]>([]);
const historyIndex = ref(-1);
const isExpanded = ref(false);
const isCopied = ref(false);
const logs = ref<TerminalLog[]>([
  {
    id: 'init-1',
    type: 'system',
    text: '🌙 Midnight Terminal v1.2.0 (Dynamic Time-Cycle Ready) — Type "help" or click quick spell buttons.',
    timestamp: '00:00:00'
  }
]);

const quickSpells = [
  'help',
  'time',
  'cycle',
  'whoami',
  'cv',
  'projects',
  'skills',
  'manifesto',
  'hop',
  'coffee',
  'talisman',
  'game',
  'play',
  'socials',
  'summon',
  'sudo rm -rf bugs',
  'clear'
];

const scrollContainer = ref<HTMLElement | null>(null);
const inputField = ref<HTMLInputElement | null>(null);

const scrollToBottom = () => {
  if (scrollContainer.value) {
    scrollContainer.value.scrollTop = scrollContainer.value.scrollHeight;
  }
};

const toggleExpand = () => {
  isExpanded.value = !isExpanded.value;
  sound.playClick();
};

const execute = (rawCmd: string): string => {
  const trimmed = rawCmd.trim();
  const now = formattedTime.value || '00:00:00';

  if (!trimmed) {
    logs.value.push({
      id: `log-${Date.now()}`,
      type: 'input',
      text: `${prompt.value} `,
      timestamp: now
    });
    nextTick(scrollToBottom);
    return '';
  }

  history.value.push(trimmed);
  historyIndex.value = -1;

  logs.value.push({
    id: `log-${Date.now()}-in`,
    type: 'input',
    text: `${prompt.value} ${trimmed}`,
    timestamp: now
  });

  const parts = trimmed.split(/\s+/);
  const command = parts[0].toLowerCase();
  const args = parts.slice(1);

  // Track analytics event
  trackEvent('cli_executed', { command });

  let output = '';
  let outType: 'output' | 'error' | 'system' = 'output';

  switch (command) {
    case 'help':
      output = `Available spells:\n• time / clock   : Show live time and the current developer phase\n• cycle / phase  : Show the four-phase diurnal matrix\n• travel <phase> : Travel through time (midnight|dawn|noon|dusk)\n• reset-time     : Sync with the device clock\n• whoami / bio   : Show identity and architecture profile\n• manifesto      : Read the humanistic software manifesto\n• cv / resume    : Show capability summary and CV\n• projects / ls  : List production project grimoire\n• skills         : Show the technical arsenal\n• game / play    : Launch the Rune Typer keyboard game\n• socials        : Show communication channels\n• summon / hire  : Open the project contact altar\n• hop            : Make Ma Cà Tưng jump\n• coffee         : Pour 100% Robusta\n• talisman       : Receive a blessed 0-bug code charm\n• sudo rm -rf bugs : Exorcise production bugs\n• clear          : Clear the terminal`;
      sound.playClick();
      break;
    case 'time':
    case 'clock':
      output = `══════════════════════════════════════════════════════════
 ⏰ MACATUNG CHRONOS — DYNAMIC TIME-CYCLE ENGINE
══════════════════════════════════════════════════════════
 • Current Time       : ${formattedTime.value} (GMT+7)
 • Active Phase       : ${activePhase.value.name} (${activePhase.value.vietnameseName})
 • Time Window        : ${activePhase.value.timeRange}
 • Mode               : ${isTimeTravelActive.value ? '🔮 Time Travel Preview' : '🟢 Real-Time Synced'}
 • Caffeine Level     : ${activePhase.value.caffeineLevel}% Robusta Flow
 • Phase Message      : "${activePhase.value.tagline}"
 💡 Type "cycle" for all four phases or "travel <phase>" to jump!`;
      sound.playCelestialChime(activePhase.value.id);
      break;
    case 'cycle':
    case 'phase':
    case 'phases':
      output = `══════════════════════════════════════════════════════════════════
 🌌 DEVELOPER DIURNAL MATRIX — FOUR PHASES
══════════════════════════════════════════════════════════════════
 1. 🌙 Midnight Void  [00:00 - 05:59] : Deep focus, zero bugs ${activePhaseId.value === 'midnight' ? '👈 [ACTIVE]' : ''}
 2. 🌅 Golden Dawn    [06:00 - 11:59] : Amber dawn, morning coffee ${activePhaseId.value === 'dawn' ? '👈 [ACTIVE]' : ''}
 3. ☀️ High-Noon      [12:00 - 17:59] : Cyber noon, ship features ${activePhaseId.value === 'afternoon' ? '👈 [ACTIVE]' : ''}
 4. 🌆 Twilight Dusk  [18:00 - 23:59] : Purple dusk, start the night shift ${activePhaseId.value === 'twilight' ? '👈 [ACTIVE]' : ''}
──────────────────────────────────────────────────────────────────
 🔮 Use "travel midnight", "travel dawn", "travel noon" or "travel dusk"
    or "reset-time" to test the system response!`;
      sound.playClick();
      break;
    case 'travel':
    case 'timetravel': {
      const target = (args[0] || '').toLowerCase();
      let targetPhase: TimePhaseId | null = null;
      if (target === 'midnight' || target === 'night' || target === '0' || target === 'dem') targetPhase = 'midnight';
      else if (target === 'dawn' || target === 'morning' || target === 'sang') targetPhase = 'dawn';
      else if (target === 'noon' || target === 'afternoon' || target === 'chieu') targetPhase = 'afternoon';
      else if (target === 'dusk' || target === 'twilight' || target === 'evening' || target === 'toi') targetPhase = 'twilight';

      if (targetPhase) {
        setPhaseOverride(targetPhase);
        const phaseInfo = TIME_PHASES[targetPhase];
        output = `✨ [TIME TRAVEL SUCCESS] Warped to: ${phaseInfo.name} (${phaseInfo.vietnameseName})!\nThe lighting, charms, mascot and interface have shifted.`;
      } else {
        output = `travel: Invalid phase. Choose "midnight", "dawn", "noon" or "dusk".`;
        outType = 'error';
        sound.playClick();
      }
      break;
    }
    case 'reset-time':
    case 'realtime':
      resetToRealTime();
      output = `⚡ [TIME SYNC] Time Travel cancelled; synced 100% with the device clock.`;
      break;
    case 'whoami':
    case 'bio':
      output = `🧙‍♂️ Ma Cà Tưng — Lead Systems Architect & Creative Full-Stack Engineer.\nPositioning: "Code at midnight" — Turning Robusta coffee into high-scale distributed architecture.\nCurrent rhythm: [${activePhase.value.name}] | Caffeine: ${activePhase.value.caffeineLevel}% | Mascot: ${activePhase.value.mascotState}`;
      sound.playClick();
      break;
    case 'cv':
    case 'resume':
      output = `══════════════════════════════════════════════════════════
 📄 CAPABILITY PROFILE — MACATUNG.DEV (LEAD SYSTEMS ARCHITECT)
══════════════════════════════════════════════════════════
 • Role      : Lead Full-Stack Architect / Senior Engineer
 • Experience: > 8 years shipping high-scale systems
 • Strengths : Laravel, Vue 3, TypeScript, Microservices, Web Audio, High-Concurrency
 • Results   : +300% Throughput, 99.99% Uptime SLA, Zero Production Crash
 • Status    : 🟢 Ready for the next quest / collaboration
 🔗 Scroll to the Summoning Altar to download the full CV!`;
      sound.playSuccess();
      break;
    case 'projects':
    case 'ls':
      output = `Grimoire Projects (${projectsData.length} Spells):\n` + projectsData.map((p) => `  [${p.category.toUpperCase()}] ${p.title} — ${p.tagline} (Metrics: ${p.metrics.map(m => m.value).join(' | ')})`).join('\n');
      sound.playClick();
      break;
    case 'skills': {
      const total = skillsData.reduce((acc, cat) => acc + cat.skills.length, 0);
      output = `Skills Arsenal (${total} runes):\n` + skillsData.map((c) => `  ⚡ ${c.title}: ${c.skills.map((s) => `${s.name} (${s.level}%)`).join(', ')}`).join('\n');
      sound.playClick();
      break;
    }
    case 'socials':
    case 'contact':
      output = `📡 Spectral Communication Channels:\n  • Email   : dev@macatung.dev\n  • GitHub  : https://github.com/macatung\n  • Telegram: @macatung_dev\n  • Realm   : GMT+7 (Midnight Zone)`;
      sound.playClick();
      break;
    case 'manifesto':
    case 'philosophy':
    case 'nhansinh':
      output = `══════════════════════════════════════════════════════════════════════════
 🌿 HUMANISTIC SOFTWARE ENGINEERING MANIFESTO
══════════════════════════════════════════════════════════════════════════
 "Kind Intent in Code — Applications That Serve People"
 
 1. 🌿 EMPATHY & USER-FIRST DESIGN:
    • Remove ego and understand real user pain.
    • Say NO to dark patterns; protect privacy and freedom.

 2. ⚡ RESILIENT & GREEN COMPUTING:
    • Make modules work together while optimizing server resources.
    • Use decoupled architecture that adapts to technology shifts.

 3. 🛡️ ZERO-DEBT CRAFTSMANSHIP:
    • Technical debt creates anxiety; pursue strict type safety and zero debt.
    • Refine every detail in the deep-night midnight flow.

 4. ✨ AI FOR HUMAN EMPOWERMENT:
    • Autonomous multi-agent AI carries repetitive work 24/7.
    • Return time to people for creativity and a better life.
──────────────────────────────────────────────────────────────────────────`;
      sound.playCelestialChime(activePhase.value.id);
      break;
    case 'admin':
      output = '⚙️ [CMS PORTAL] Opening Admin Dashboard at /admin ... Manage Projects & Contacts: https://macatung.dev/admin';
      sound.playSuccess();
      if (typeof window !== 'undefined') {
        window.location.href = '/admin';
      }
      break;
    case 'hop':
      output = '🧛‍♂️ *HOP!* Ma Cà Tưng hops gracefully over production bugs! (+1 Hop)';
      sound.playHop(1.5);
      emit('hop-requested');
      break;
    case 'coffee':
      output = '☕ Poured 1 cup of Vietnamese Robusta! Caffeine level = 100%. Ready for 4 AM deploy.';
      sound.playSuccess();
      break;
    case 'talisman':
      output = '📜 [0-BUG CODE TALISMAN] try { deploy(); } catch { /* PEACE */ } — Blessing complete!';
      sound.playTalisman();
      break;
    case 'game':
    case 'play':
      output = '🎮 [RUNE TYPER ARCADE] Launching Rune Typer Dev Game... Scroll to #game to type away the bugs!';
      sound.playSuccess();
      if (typeof document !== 'undefined') {
        const gameEl = document.getElementById('game');
        if (gameEl) {
          gameEl.scrollIntoView({ behavior: 'smooth' });
        }
      }
      break;
    case 'slogan':
      output = '✨ "Kind intent in code — applications that serve people. Code at midnight, deploy with peace."';
      sound.playClick();
      break;
    case 'summon':
    case 'hire':
      output = '🔮 Invoking Summoning Altar... Scroll down to offer coffee and initiate project contract!';
      sound.playClick();
      emit('summon-requested');
      break;
    case 'sudo': {
      const sudoArg = args.join(' ');
      if (sudoArg === 'rm -rf bugs' || sudoArg === 'rm -rf /bugs') {
        output = '🔥 [EXORCISM IN PROGRESS] Purging 4,192 bugs from production... 0 bugs remaining. Realm is peaceful and verified 100%!';
        sound.playSuccess();
      } else {
        output = `sudo: ${sudoArg}: command not permitted by midnight council`;
        outType = 'error';
        sound.playClick();
      }
      break;
    }
    case 'clear':
      logs.value = [];
      currentInput.value = '';
      return '';
    default:
      output = `macatung-cli: command not found: "${command}". Type "help" to see available spells.`;
      outType = 'error';
      sound.playClick();
      break;
  }

  logs.value.push({
    id: `log-${Date.now()}-out`,
    type: outType,
    text: output,
    timestamp: now
  });

  emit('command-executed', trimmed, output);
  currentInput.value = '';
  nextTick(scrollToBottom);
  return output;
};

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'ArrowUp') {
    e.preventDefault();
    if (history.value.length === 0) return;
    if (historyIndex.value === -1) {
      historyIndex.value = history.value.length - 1;
    } else if (historyIndex.value > 0) {
      historyIndex.value--;
    }
    if (historyIndex.value >= 0 && historyIndex.value < history.value.length) {
      currentInput.value = history.value[historyIndex.value];
    }
  } else if (e.key === 'ArrowDown') {
    e.preventDefault();
    if (historyIndex.value < history.value.length - 1 && historyIndex.value !== -1) {
      historyIndex.value++;
      currentInput.value = history.value[historyIndex.value];
    } else {
      historyIndex.value = -1;
      currentInput.value = '';
    }
  } else if (e.key === 'Enter') {
    execute(currentInput.value);
  } else if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
    sound.playTerminalKey();
  }
};

const copyLogs = async () => {
  const plainText = logs.value.map((l) => `${l.type === 'input' ? '' : '  '}${l.text}`).join('\n');
  try {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      await navigator.clipboard.writeText(plainText);
    }
    isCopied.value = true;
    sound.playClick();
    setTimeout(() => {
      isCopied.value = false;
    }, 2000);
  } catch {
    // Fallback
  }
};

const runSpell = (cmd: string) => {
  execute(cmd);
};

const focusInput = () => {
  if (inputField.value) {
    inputField.value.focus();
  }
};
</script>

<template>
  <div
    class="w-full rounded-2xl border border-white/10 glass-panel overflow-hidden flex flex-col font-mono text-left shadow-2xl transition-all duration-300"
    :class="isExpanded ? 'h-[540px]' : 'h-[420px]'"
    @click="focusInput"
  >
    <!-- Terminal Header / Title Bar -->
    <div class="h-10 bg-midnight-900/90 border-b border-white/10 px-4 flex items-center justify-between select-none shrink-0">
      <!-- Window Controls & Status -->
      <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full bg-rose-500/80 inline-block" />
        <span class="w-3 h-3 rounded-full bg-amber-500/80 inline-block" />
        <span class="w-3 h-3 rounded-full bg-phantom-mint/80 inline-block" />
        <span class="text-xs text-slate-400 font-bold ml-2 hidden sm:inline truncate">
          macatung@midnight-node: ~ (zsh)
        </span>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center gap-2">
        <button
          type="button"
          class="p-1 rounded text-slate-400 hover:text-white text-xs transition-colors min-h-[32px] px-2 flex items-center gap-1"
          :title="isCopied ? t('common.copied') : t('terminal.copyLog')"
          @click.stop="copyLogs"
        >
          <span>{{ isCopied ? `✓ ${t('common.copied')}` : '📋 Copy' }}</span>
        </button>
        <button
          type="button"
          class="p-1 rounded text-slate-400 hover:text-white text-xs transition-colors min-h-[32px] px-2"
          :title="isExpanded ? t('terminal.collapse') : t('terminal.expand')"
          @click.stop="toggleExpand"
        >
          <span>{{ isExpanded ? '🗗' : '🗖' }}</span>
        </button>
      </div>
    </div>

    <!-- Quick Spells Pill Bar -->
    <div class="bg-midnight-950/80 border-b border-white/5 px-3 py-2 flex items-center gap-1.5 overflow-x-auto no-scrollbar select-none shrink-0">
      <span class="text-[10px] text-slate-500 uppercase tracking-wider shrink-0 mr-1 whitespace-nowrap">Spells:</span>
      <button
        v-for="spell in quickSpells"
        :key="spell"
        type="button"
        class="px-2.5 py-1 rounded-md text-[11px] font-mono bg-white/5 hover:bg-phantom-mint hover:text-midnight-950 text-slate-300 transition-all shrink-0 border border-white/5 whitespace-nowrap"
        @click.stop="runSpell(spell)"
      >
        {{ spell }}
      </button>
    </div>

    <!-- Log Output Container -->
    <div
      ref="scrollContainer"
      class="flex-1 p-4 overflow-y-auto space-y-2 text-xs sm:text-sm font-mono leading-relaxed"
    >
      <div
        v-for="log in logs"
        :key="log.id"
        class="break-words whitespace-pre-wrap"
        :class="{
          'text-phantom-mint font-semibold': log.type === 'input',
          'text-slate-300': log.type === 'output',
          'text-amber-300 font-semibold': log.type === 'system',
          'text-rose-400': log.type === 'error'
        }"
      >
        {{ log.text }}
      </div>

      <!-- Live Input Line -->
      <div class="flex items-center gap-2 pt-1 text-xs sm:text-sm">
        <span class="text-phantom-mint font-bold shrink-0">{{ prompt }}</span>
        <input
          ref="inputField"
          v-model="currentInput"
          type="text"
          class="flex-1 bg-transparent border-none outline-none text-slate-100 font-mono text-xs sm:text-sm p-0 focus:ring-0"
          :placeholder="t('terminal.placeholder')"
          autofocus
          @keydown="handleKeyDown"
        />
      </div>
    </div>
  </div>
</template>
