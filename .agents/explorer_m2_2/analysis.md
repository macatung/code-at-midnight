# Technical Blueprint: Interactive Tools & Grimoire Showcase (Milestone 2)

**Author**: Explorer 2 (`explorer_m2_2`)  
**Milestone**: `m2_frontend_components_responsive`  
**Target Components**:
1. `resources/js/Components/terminal/MidnightTerminal.vue` (Feature 10 & 11)
2. `resources/js/Components/talisman/TalismanGenerator.vue` (Feature 12, 13 & 14)
3. `resources/js/Components/projects/ProjectsSection.vue` & `ProjectModal.vue` (Feature 15 & 16)

---

## 1. Executive Summary & Architectural Overview

This blueprint provides the complete engineering specifications, component interfaces, state machines, command execution algorithms, modal accessibility lifecycles, and responsive anti-collision layout rules for the interactive tools and showcase components of **macatung.dev**.

All components are engineered using **Vue 3 Composition API (`<script setup lang="ts">`)**, **TailwindCSS v3**, and integrate with the zero-dependency Web Audio synthesizer (`soundEffects.ts`), `localStorage` persistence, and the static dataset layer.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          macatung.dev Architecture                          │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
        ┌─────────────────────────────┼─────────────────────────────┐
        ▼                             ▼                             ▼
┌───────────────────────┐   ┌───────────────────────┐   ┌───────────────────────┐
│   Midnight Terminal   │   │ Developer Talisman    │   │  Grimoire Projects    │
│         REPL          │   │         Forge         │   │   Showcase & Modal    │
│(MidnightTerminal.vue) │   │ (TalismanGenerator)   │   │ (ProjectsSection.vue) │
├───────────────────────┤   ├───────────────────────┤   ├───────────────────────┤
│ • 11 Command Suite    │   │ • 6 Preset Spells     │   │ • 5 Category Filters  │
│ • History Up/Down     │   │ • 4 Color Palettes    │   │ • 6 Project Cards     │
│ • Expand/Collapse     │   │ • Khai Quang Ritual   │   │ • Modal Dialog        │
│ • Copy Logs & Sound   │   │ • ASCII Exporter      │   │ • ESC & Scroll Lock   │
└───────────────────────┘   └───────────────────────┘   └───────────────────────┘
        │                             │                             │
        └─────────────────────────────┼─────────────────────────────┘
                                      ▼
                        ┌───────────────────────────┐
                        │ SoundEngine Synthesizer   │
                        │ (soundEffects.ts)         │
                        └───────────────────────────┘
```

---

## 2. Midnight Terminal REPL (`MidnightTerminal.vue`)

### 2.1 Component Specifications & Interfaces

- **File Path**: `resources/js/Components/terminal/MidnightTerminal.vue`
- **Features Supported**: F10 (Midnight Terminal CLI), F11 (Full Terminal Command Suite)

#### TypeScript Interface Definitions
```ts
export interface TerminalLog {
  id: string;
  type: 'input' | 'output' | 'system' | 'error';
  text: string;
  timestamp: string;
}

export interface TerminalProps {
  initialExpanded?: boolean;
}

export interface TerminalEmits {
  (e: 'command-executed', cmd: string, output: string): void;
  (e: 'hop-requested'): void;
  (e: 'summon-requested'): void;
}
```

#### Reactive State Model
| State Variable | Type | Default | Description |
|---|---|---|---|
| `logs` | `Ref<TerminalLog[]>` | `[{ type: 'system', text: '🌙 Midnight Terminal v1.0.0...' }]` | Array of terminal output rows |
| `history` | `Ref<string[]>` | `[]` | FIFO history buffer of executed command strings |
| `historyIndex` | `Ref<number>` | `-1` | Pointer index for arrow navigation (-1 = current new prompt) |
| `currentInput` | `Ref<string>` | `''` | Current string in the input field |
| `isExpanded` | `Ref<boolean>` | `false` | Window height toggle (collapsed ~320px, expanded ~540px) |
| `prompt` | `Ref<string>` | `'macatung:~$'` | Shell prompt label |
| `isCopied` | `Ref<boolean>` | `false` | Copy feedback icon/toast trigger |
| `terminalScrollContainer` | `Ref<HTMLElement | null>` | `null` | DOM ref for auto-scrolling log area |
| `inputElement` | `Ref<HTMLInputElement | null>` | `null` | DOM ref to focus input on terminal container click |

---

### 2.2 CLI Command Parsing Algorithm & 11 Spells Specification

The execution pipeline processes commands cleanly:
1. **Sanitize**: Trims whitespace and extracts the primary token in lower case (`command = tokens[0].toLowerCase()`).
2. **Arguments**: Retains rest tokens (`args = tokens.slice(1)`).
3. **Empty Input Handling**: If empty, appends prompt line without error.
4. **History Push**: Appends raw trimmed input to `history` and resets `historyIndex` to `-1`.
5. **Execution Dispatch**: Matches command via `switch(command)`.
6. **Sound Trigger**: Fires appropriate procedural audio synthesized note/chime.

```ts
public execute(rawCmd: string): string {
  const trimmed = rawCmd.trim();
  const now = new Date().toLocaleTimeString('vi-VN', { hour12: false });

  if (!trimmed) {
    this.logs.push({
      id: `log-${Date.now()}`,
      type: 'input',
      text: `${this.prompt} `,
      timestamp: now
    });
    return '';
  }

  // Push to history buffer
  this.history.push(trimmed);
  this.historyIndex = -1;

  // Log user input
  this.logs.push({
    id: `log-${Date.now()}-in`,
    type: 'input',
    text: `${this.prompt} ${trimmed}`,
    timestamp: now
  });

  const parts = trimmed.split(/\s+/);
  const command = parts[0].toLowerCase();
  const args = parts.slice(1);

  let output = '';
  let outType: 'output' | 'error' | 'system' = 'output';

  switch (command) {
    case 'help':
      output = [
        'Available spells:',
        '• whoami / bio   — Developer identity & nocturnal lore',
        '• projects / ls  — Grimoire project portfolio',
        '• skills         — Tech rune arsenal',
        '• hop            — Trigger mascot hop leap',
        '• coffee         — Brew Vietnamese Robusta caffeine boost',
        '• talisman       — Cast blessed developer talisman',
        '• slogan         — Midnight developer philosophy',
        '• summon         — Invoke Summoning Altar contact form',
        '• sudo rm -rf bugs — Exorcise all production bugs',
        '• clear          — Wipe terminal output buffer'
      ].join('\n');
      sound.playClick();
      break;

    case 'whoami':
    case 'bio':
      output = 'Ma Cà Tưng — Full-Stack Alchemist & Midnight Creative Engineer. Specializing in high-performance web systems and mystical UI.';
      sound.playClick();
      break;

    case 'projects':
    case 'ls':
      output = `Grimoire Projects (${projectsData.length}):\n` +
        projectsData.map((p) => `  [${p.category.toUpperCase()}] ${p.title} — ${p.tagline}`).join('\n');
      sound.playClick();
      break;

    case 'skills':
      const total = skillsData.reduce((acc, cat) => acc + cat.skills.length, 0);
      output = `Skills Arsenal (${total} runes):\n` +
        skillsData.map((c) => `  ⚡ ${c.title}: ${c.skills.map((s) => s.name).join(', ')}`).join('\n');
      sound.playClick();
      break;

    case 'hop':
      output = '🧛‍♂️ *HOP!* Ma Cà Tưng hops gracefully over production bugs!';
      sound.playHop(1.5);
      emit('hop-requested');
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
      sound.playClick();
      break;

    case 'summon':
      output = '🔮 Invoking Summoning Altar... Scroll down to offer coffee and initiate project contract!';
      sound.playClick();
      emit('summon-requested');
      break;

    case 'sudo':
      const sudoArg = args.join(' ');
      if (sudoArg === 'rm -rf bugs' || sudoArg === 'rm -rf /bugs') {
        output = '🔥 [EXORCISM IN PROGRESS] Purging 4,192 bugs from production... 0 bugs remaining. Realm is peaceful!';
        sound.playSuccess();
      } else {
        output = `sudo: ${sudoArg}: command not permitted by midnight council`;
        outType = 'error';
        sound.playClick();
      }
      break;

    case 'clear':
      this.logs = [];
      return '';

    default:
      output = `macatung-cli: command not found: ${command}. Type "help" for available commands.`;
      outType = 'error';
      sound.playClick();
      break;
  }

  this.logs.push({
    id: `log-${Date.now()}-out`,
    type: outType,
    text: output,
    timestamp: now
  });

  this.currentInput = '';
  nextTick(() => scrollToBottom());
  return output;
}
```

---

### 2.3 Command History Navigation (ArrowUp / ArrowDown)

```ts
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
```

---

### 2.4 Mobile Quick-Command Chips & Touch Optimization

To ensure seamless usage on mobile devices (360px–480px) where typing terminal commands via mobile virtual keyboards is cumbersome, the component includes:
- **Quick-Pills Bar**: Horizontally scrollable row of common spells (`help`, `whoami`, `projects`, `skills`, `hop`, `coffee`, `sudo rm -rf bugs`, `clear`).
- **One-Tap Execution**: Tapping any chip immediately feeds and runs the spell.
- **Touch-Friendly Submit Button**: Right-aligned enter arrow button (`min-w-[44px] min-h-[44px]`).

---

## 3. Developer Talisman Forge (`TalismanGenerator.vue`)

### 3.1 Component Specifications & Interfaces

- **File Path**: `resources/js/Components/talisman/TalismanGenerator.vue`
- **Features Supported**: F12 (Developer Talisman Forge), F13 (Khai Quang Blessing Seal Ritual), F14 (ASCII Talisman Exporter)

#### Reactive State Model
| State Variable | Type | Default | Description |
|---|---|---|---|
| `selectedPreset` | `Ref<TalismanPreset>` | `talismanPresets[0]` | Currently active talisman preset spell |
| `developerName` | `Ref<string>` | `''` | Custom owner name (fallback: `'Midnight Engineer'`) |
| `customWish` | `Ref<string>` | `''` | Custom developer wish (fallback: `selectedPreset.meaning`) |
| `colorPalette` | `Ref<'yellow' | 'crimson' | 'cyan' | 'purple'>` | `'yellow'` | Active talisman aura color scheme |
| `isBlessed` | `Ref<boolean>` | `false` | Blessing seal state |
| `isBlessingAnimation` | `Ref<boolean>` | `false` | Active animation debounce lock during Khai Quang |
| `copiedToast` | `Ref<boolean>` | `false` | ASCII clipboard export toast trigger |

---

### 3.2 Color Palette & Aesthetic Tokens

| Palette | Border Color | Glow Shadow | Accent Rune Tag | Text Gradient |
|---|---|---|---|---|
| **yellow** | `border-talisman-yellow/80` | `shadow-[0_0_35px_rgba(255,209,102,0.35)]` | `bg-amber-950/80 text-talisman-yellow` | `from-amber-200 to-yellow-400` |
| **crimson** | `border-phantom-crimson/80` | `shadow-[0_0_35px_rgba(255,0,84,0.35)]` | `bg-rose-950/80 text-rose-300` | `from-rose-200 to-red-400` |
| **cyan** | `border-phantom-mint/80` | `shadow-[0_0_35px_rgba(0,245,160,0.35)]` | `bg-emerald-950/80 text-phantom-mint` | `from-emerald-200 to-teal-400` |
| **purple** | `border-phantom-purple/80` | `shadow-[0_0_35px_rgba(157,78,221,0.35)]` | `bg-purple-950/80 text-purple-300` | `from-purple-200 to-indigo-400` |

---

### 3.3 Khai Quang Blessing Ritual Lifecycle

```
[User clicks "Khai Quang (Bless Talisman)"]
               │
               ▼
   [isBlessingAnimation === true ?] ──Yes──► [Ignore (Debounce Lock)]
               │ No
               ▼
   • Set isBlessingAnimation = true
   • Trigger sound.playTalisman() (4-note arpeggio chime D5, A5, D6, A6)
   • Trigger canvas-confetti ({ particleCount: 60, spread: 70 })
   • Start CSS pulse & glow aura animation
               │
               ▼
   [Wait 800ms / completion timeout]
               │
               ▼
   • Set isBlessed = true
   • Set isBlessingAnimation = false
   • Render animated rotating seal badge ("✓ ĐÃ KHAI QUANG")
```

---

### 3.4 Formatted ASCII Talisman Exporter

```ts
const generateAsciiTalisman = (): string => {
  const name = developerName.value.trim() || 'Midnight Engineer';
  const wish = customWish.value.trim() || selectedPreset.value.meaning;
  const title = selectedPreset.value.title;
  const seal = isBlessed.value ? '[✓ ĐÃ KHAI QUANG]' : '[CHƯA KHAI QUANG]';

  return `
+------------------------------------------+
|  ⚡ MACATUNG.DEV DEV TALISMAN FORGE ⚡  |
+------------------------------------------+
|  SPELL:  ${title.padEnd(30, ' ')} |
|  OWNER:  ${name.padEnd(30, ' ')} |
|  WISH:   ${wish.slice(0, 30).padEnd(30, ' ')} |
|  STATUS: ${seal.padEnd(30, ' ')} |
+------------------------------------------+
|  ${selectedPreset.value.codeSnippet.slice(0, 40).padEnd(40, ' ')} |
+------------------------------------------+
`.trim();
};

const copyAsciiToClipboard = async () => {
  const ascii = generateAsciiTalisman();
  sound.playClick();
  try {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      await navigator.clipboard.writeText(ascii);
    }
  } catch {
    // Fallback for non-secure / headless environments
  }
  copiedToast.value = true;
  setTimeout(() => {
    copiedToast.value = false;
  }, 2500);
};
```

---

## 4. Grimoire Project Showcase & Modal (`ProjectsSection.vue` & `ProjectModal.vue`)

### 4.1 Component Specifications & Interfaces

- **File Paths**: 
  - `resources/js/Components/projects/ProjectsSection.vue` (F15)
  - `resources/js/Components/projects/ProjectModal.vue` (F16)
- **Data Source**: `resources/js/data/projectsData.ts` (6 projects)

#### Category Filter Tabs
```ts
export type ProjectCategory = 'all' | 'fullstack' | 'creative' | 'ai-web3' | 'tools';

export interface CategoryTab {
  id: ProjectCategory;
  label: string;
  count: number;
}
```

---

### 4.2 Modal Accessibility & Interaction Matrix

| Requirement | Implementation Detail |
|---|---|
| **Escape Key Listener** | `window.addEventListener('keydown', handleEscape)` registered on mount, unregistered on unmount. If `e.key === 'Escape'` and modal is open, triggers `closeModal()`. |
| **Backdrop Click Dismiss** | Backdrop container uses `@click.self="emit('close')"` so clicking outside the dialog card dismisses modal. |
| **Body Scroll Lock** | On open: `document.body.classList.add('overflow-hidden')`. On close/unmount: `document.body.classList.remove('overflow-hidden')`. |
| **Focus Trap & Keyboard** | Close button and interactive links have visible focus rings (`focus:ring-2 focus:ring-phantom-mint focus:outline-none`). |
| **External Links Security** | All `<a>` tags for `liveUrl` and `githubUrl` specify `target="_blank"` and `rel="noopener noreferrer"`. |

---

### 4.3 Detailed Project Schema Structure

Each project card and modal displays rich data points:
- **`coverGradient`**: Thematic obsidian neon color stops (e.g. `from-emerald-950 via-teal-900 to-slate-950`).
- **`metrics`**: 3 KPI stat counters (e.g. "Active Night Owls: 42.5k+", "WASM Speedup: 3.8x").
- **`architectureHighlights`**: Technical bullet points showcasing systems engineering, zero-allocation workers, and concurrency models.
- **`midnightFact`**: Authentic nocturnal developer lore and anecdotes.
- **`techStack`**: Styled badge pills with hover luminescence.

---

## 5. Comprehensive Responsive & Anti-Collision Engineering

### 5.1 Viewport Breakpoint Specifications

| Viewport | Device Class | Grid Columns | Typography Sizing | Container Padding |
|---|---|---|---|---|
| **360px – 480px** | Mobile Phones (iPhone SE, Pixel, Galaxy) | `grid-cols-1` | `text-2xl` / `text-3xl`, `clamp(1.5rem, 5vw, 2.25rem)` | `px-4 py-8` |
| **768px – 1024px** | Tablets / iPads | `md:grid-cols-2` | `text-4xl` / `text-5xl` | `px-6 py-12` |
| **1440px+** | Desktop Displays & 4K | `lg:grid-cols-3` | `text-5xl` / `text-6xl` | `max-w-7xl mx-auto px-8 py-16` |

### 5.2 Anti-Collision & Layout Safety Rules
1. **`break-words` & `overflow-wrap`**: Applied to all user inputs, terminal output strings, and project descriptions to prevent long continuous words (e.g., long URLs, code tokens) from forcing horizontal viewport scrolling.
2. **`min-h-[44px]` & `min-w-[44px]` Touch Targets**: Enforced on all buttons, filter tabs, terminal chips, close triggers, and talisman palette swatches.
3. **`overflow-x-hidden` on Root Containers**: Enforced on page and section containers to eliminate unwanted mobile horizontal wobble.
4. **Fluid Category Tab Bar**: Horizontal scrollable container (`overflow-x-auto no-scrollbar`) with gradient edge masks for mobile swipe navigation.

---

## 6. Complete Implementation Blueprints

### 6.1 `MidnightTerminal.vue`
```vue
<script setup lang="ts">
import { ref, nextTick, onMounted } from 'vue';
import { projectsData } from '@/data/projectsData';
import { skillsData } from '@/data/skillsData';
import { sound } from '@/audio/soundEffects';

export interface TerminalLog {
  id: string;
  type: 'input' | 'output' | 'system' | 'error';
  text: string;
  timestamp: string;
}

const emit = defineEmits<{
  (e: 'hop-requested'): void;
  (e: 'summon-requested'): void;
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
    text: '🌙 Midnight Terminal v1.0.0 — Type "help" to explore available commands.',
    timestamp: '00:00:00'
  }
]);

const quickSpells = [
  'help',
  'whoami',
  'projects',
  'skills',
  'hop',
  'coffee',
  'talisman',
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
  const now = '00:00:00';

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

  let output = '';
  let outType: 'output' | 'error' | 'system' = 'output';

  switch (command) {
    case 'help':
      output = 'Available spells:\n• whoami / bio\n• projects / ls\n• skills\n• hop\n• coffee\n• talisman\n• slogan\n• summon\n• sudo rm -rf bugs\n• clear';
      sound.playClick();
      break;
    case 'whoami':
    case 'bio':
      output = 'Ma Cà Tưng — Full-Stack Alchemist & Midnight Creative Engineer. Specializing in high-performance web systems and mystical UI.';
      sound.playClick();
      break;
    case 'projects':
    case 'ls':
      output = `Grimoire Projects (${projectsData.length}):\n` + projectsData.map((p) => `  [${p.category.toUpperCase()}] ${p.title} — ${p.tagline}`).join('\n');
      sound.playClick();
      break;
    case 'skills':
      const total = skillsData.reduce((acc, cat) => acc + cat.skills.length, 0);
      output = `Skills Arsenal (${total} runes):\n` + skillsData.map((c) => `  ⚡ ${c.title}: ${c.skills.map((s) => s.name).join(', ')}`).join('\n');
      sound.playClick();
      break;
    case 'hop':
      output = '🧛‍♂️ *HOP!* Ma Cà Tưng hops gracefully over production bugs!';
      sound.playHop(1.5);
      emit('hop-requested');
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
      sound.playClick();
      break;
    case 'summon':
      output = '🔮 Invoking Summoning Altar... Scroll down to offer coffee and initiate project contract!';
      sound.playClick();
      emit('summon-requested');
      break;
    case 'sudo':
      if (args.join(' ') === 'rm -rf bugs' || args.join(' ') === 'rm -rf /bugs') {
        output = '🔥 [EXORCISM IN PROGRESS] Purging 4,192 bugs from production... 0 bugs remaining. Realm is peaceful!';
        sound.playSuccess();
      } else {
        output = `sudo: ${args.join(' ')}: command not permitted by midnight council`;
        outType = 'error';
        sound.playClick();
      }
      break;
    case 'clear':
      logs.value = [];
      currentInput.value = '';
      return '';
    default:
      output = `macatung-cli: command not found: ${command}. Type "help" for available commands.`;
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
  const raw = logs.value.map((l) => l.text).join('\n');
  if (typeof navigator !== 'undefined' && navigator.clipboard) {
    await navigator.clipboard.writeText(raw);
  }
  isCopied.value = true;
  sound.playClick();
  setTimeout(() => {
    isCopied.value = false;
  }, 2000);
};

const focusTerminal = () => {
  inputField.value?.focus();
};
</script>

<template>
  <div
    class="w-full max-w-4xl mx-auto rounded-2xl overflow-hidden glass-panel border border-white/10 shadow-2xl transition-all duration-300 flex flex-col bg-midnight-950/90"
    :class="isExpanded ? 'h-[540px]' : 'h-[360px]'"
    @click="focusTerminal"
  >
    <!-- Terminal Header Bar -->
    <div class="px-4 py-3 bg-midnight-900/90 border-b border-white/10 flex items-center justify-between select-none">
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full bg-rose-500/80 cursor-pointer hover:opacity-100" @click.stop="execute('clear')" title="Clear" />
        <div class="w-3 h-3 rounded-full bg-amber-500/80 cursor-pointer hover:opacity-100" @click.stop="toggleExpand" title="Toggle Expand" />
        <div class="w-3 h-3 rounded-full bg-emerald-500/80 cursor-pointer hover:opacity-100" @click.stop="execute('help')" title="Help" />
        <span class="ml-2 font-mono text-xs text-slate-400 font-medium">macatung-cli — zsh — 80x24</span>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors text-xs font-mono flex items-center gap-1 min-h-[36px] min-w-[36px]"
          @click.stop="copyLogs"
          title="Copy Logs"
        >
          <span>{{ isCopied ? '✓ Copied' : '📋 Copy' }}</span>
        </button>
        <button
          type="button"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors text-xs font-mono min-h-[36px] min-w-[36px]"
          @click.stop="toggleExpand"
          title="Expand/Collapse"
        >
          <span>{{ isExpanded ? '🗗 Collapse' : '🗖 Expand' }}</span>
        </button>
      </div>
    </div>

    <!-- Quick Spells Strip for Mobile & Rapid Clicks -->
    <div class="px-3 py-1.5 bg-midnight-900/50 border-b border-white/5 flex items-center gap-1.5 overflow-x-auto no-scrollbar">
      <span class="text-[10px] font-mono text-slate-500 uppercase tracking-wider shrink-0 mr-1">Spells:</span>
      <button
        v-for="spell in quickSpells"
        :key="spell"
        type="button"
        class="px-2 py-0.5 rounded text-[11px] font-mono bg-midnight-800/80 text-slate-300 hover:text-phantom-mint hover:bg-midnight-700/80 border border-white/5 transition-all shrink-0 active:scale-95"
        @click.stop="execute(spell)"
      >
        {{ spell }}
      </button>
    </div>

    <!-- Terminal Log Stream -->
    <div
      ref="scrollContainer"
      class="flex-1 p-4 overflow-y-auto font-mono text-xs sm:text-sm space-y-2 text-left select-text scrollbar-thin scrollbar-thumb-white/10"
    >
      <div v-for="log in logs" :key="log.id" class="break-words leading-relaxed whitespace-pre-wrap">
        <span v-if="log.type === 'system'" class="text-talisman-yellow font-medium">{{ log.text }}</span>
        <span v-else-if="log.type === 'input'" class="text-slate-300 font-semibold">{{ log.text }}</span>
        <span v-else-if="log.type === 'error'" class="text-rose-400 font-mono">{{ log.text }}</span>
        <span v-else class="text-phantom-mint/90 font-mono">{{ log.text }}</span>
      </div>
    </div>

    <!-- Terminal Input Prompt -->
    <div class="p-3 bg-midnight-900/80 border-t border-white/10 flex items-center gap-2">
      <span class="text-phantom-mint font-mono font-bold text-xs sm:text-sm select-none">{{ prompt }}</span>
      <input
        ref="inputField"
        v-model="currentInput"
        type="text"
        placeholder="type 'help' or any spell..."
        class="flex-1 bg-transparent border-none outline-none font-mono text-xs sm:text-sm text-white placeholder-slate-600 focus:ring-0"
        @keydown="handleKeyDown"
      />
      <button
        type="button"
        class="px-3 py-1.5 rounded-lg bg-phantom-mint/10 text-phantom-mint hover:bg-phantom-mint hover:text-midnight-950 font-mono text-xs font-bold transition-all min-h-[36px] flex items-center justify-center"
        @click.stop="execute(currentInput)"
      >
        ⏎ Run
      </button>
    </div>
  </div>
</template>
```

---

### 6.2 `TalismanGenerator.vue`
```vue
<script setup lang="ts">
import { ref, computed } from 'vue';
import { talismanPresets } from '@/data/talismanData';
import type { TalismanPreset } from '@/types/portfolio';
import { sound } from '@/audio/soundEffects';

const selectedPreset = ref<TalismanPreset>(talismanPresets[0]);
const developerName = ref('');
const customWish = ref('');
const colorPalette = ref<'yellow' | 'crimson' | 'cyan' | 'purple'>(talismanPresets[0].colorScheme);
const isBlessed = ref(false);
const isBlessingAnimation = ref(false);
const copiedToast = ref(false);

const palettes = [
  { id: 'yellow', label: 'Talisman Gold', border: 'border-talisman-yellow', glow: 'shadow-glow-yellow', badge: 'bg-amber-950/80 text-talisman-yellow' },
  { id: 'crimson', label: 'Cinnabar Red', border: 'border-phantom-crimson', glow: 'shadow-glow-crimson', badge: 'bg-rose-950/80 text-rose-300' },
  { id: 'cyan', label: 'Neon Mint', border: 'border-phantom-mint', glow: 'shadow-glow-mint', badge: 'bg-emerald-950/80 text-phantom-mint' },
  { id: 'purple', label: 'Phantom Violet', border: 'border-phantom-purple', glow: 'shadow-glow-purple', badge: 'bg-purple-950/80 text-purple-300' }
] as const;

const activePaletteConfig = computed(() => {
  return palettes.find((p) => p.id === colorPalette.value) || palettes[0];
});

const displayName = computed(() => developerName.value.trim() || 'Midnight Engineer');
const displayWish = computed(() => customWish.value.trim() || selectedPreset.value.meaning);

const selectPreset = (preset: TalismanPreset) => {
  selectedPreset.value = preset;
  colorPalette.value = preset.colorScheme;
  isBlessed.value = false;
  sound.playClick();
};

const triggerKhaiQuang = () => {
  if (isBlessingAnimation.value) return;

  isBlessingAnimation.value = true;
  sound.playTalisman();

  try {
    if (typeof (globalThis as any).confetti === 'function') {
      (globalThis as any).confetti({
        particleCount: 45,
        spread: 65,
        origin: { y: 0.6 }
      });
    }
  } catch {
    // Graceful fallback
  }

  setTimeout(() => {
    isBlessed.value = true;
    isBlessingAnimation.value = false;
  }, 800);
};

const generateAsciiTalisman = (): string => {
  const name = displayName.value;
  const wish = displayWish.value;
  const title = selectedPreset.value.title;
  const seal = isBlessed.value ? '[✓ ĐÃ KHAI QUANG]' : '[CHƯA KHAI QUANG]';

  return `
+------------------------------------------+
|  ⚡ MACATUNG.DEV DEV TALISMAN FORGE ⚡  |
+------------------------------------------+
|  SPELL:  ${title.padEnd(30, ' ')} |
|  OWNER:  ${name.padEnd(30, ' ')} |
|  WISH:   ${wish.slice(0, 30).padEnd(30, ' ')} |
|  STATUS: ${seal.padEnd(30, ' ')} |
+------------------------------------------+
|  try { deploy(); } catch { /* PEACE */ } |
+------------------------------------------+
`.trim();
};

const copyAscii = async () => {
  const ascii = generateAsciiTalisman();
  sound.playClick();
  if (typeof navigator !== 'undefined' && navigator.clipboard) {
    await navigator.clipboard.writeText(ascii);
  }
  copiedToast.value = true;
  setTimeout(() => {
    copiedToast.value = false;
  }, 2500);
};
</script>

<template>
  <div class="w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    <!-- Left: Forge Controls Panel (7 Cols) -->
    <div class="lg:col-span-7 flex flex-col gap-6 text-left">
      <div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-talisman-gold/10 border border-talisman-gold/30 text-talisman-gold text-xs font-mono mb-3">
          ⚡ Developer Talisman Forge
        </span>
        <h2 class="text-3xl sm:text-4xl font-display font-extrabold text-white tracking-tight">
          Lò Luyện Bùa <span class="text-transparent bg-clip-text bg-gradient-to-r from-talisman-gold via-phantom-mint to-phantom-purple">Lập Trình Viên</span>
        </h2>
        <p class="text-sm sm:text-base text-slate-400 mt-2">
          Chọn thần chú hộ mệnh, điền tên và tâm nguyện, sau đó thực hiện nghi thức Khai Quang để nhận phúc khí 0-bug cho toàn bộ repository.
        </p>
      </div>

      <!-- Preset Spells Grid -->
      <div>
        <label class="block text-xs font-mono text-slate-400 uppercase tracking-wider mb-2">1. Chọn Thần Chú Bùa Chú</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
          <button
            v-for="preset in talismanPresets"
            :key="preset.id"
            type="button"
            class="p-3 rounded-xl border text-left transition-all relative overflow-hidden flex flex-col justify-between min-h-[72px]"
            :class="selectedPreset.id === preset.id
              ? 'bg-midnight-850 border-talisman-gold shadow-glow-yellow'
              : 'bg-midnight-900/60 border-white/5 hover:border-white/20 text-slate-300'"
            @click="selectPreset(preset)"
          >
            <span class="font-display font-bold text-xs sm:text-sm leading-tight text-white">{{ preset.title }}</span>
            <span class="font-mono text-[10px] text-slate-400 mt-1 truncate">{{ preset.runeTop }}</span>
          </button>
        </div>
      </div>

      <!-- Custom Inputs -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-mono text-slate-400 uppercase tracking-wider mb-1.5">2. Tên Kỹ Sư (Author)</label>
          <input
            v-model="developerName"
            type="text"
            placeholder="e.g. Alchemist Tưng"
            class="w-full px-3.5 py-2.5 rounded-xl bg-midnight-900 border border-white/10 text-white font-sans text-sm placeholder-slate-600 focus:border-phantom-mint focus:outline-none min-h-[44px]"
          />
        </div>
        <div>
          <label class="block text-xs font-mono text-slate-400 uppercase tracking-wider mb-1.5">3. Nguyện Ước / Lời Chúc</label>
          <input
            v-model="customWish"
            type="text"
            :placeholder="selectedPreset.meaning"
            class="w-full px-3.5 py-2.5 rounded-xl bg-midnight-900 border border-white/10 text-white font-sans text-sm placeholder-slate-600 focus:border-phantom-mint focus:outline-none min-h-[44px]"
          />
        </div>
      </div>

      <!-- Palette Selector -->
      <div>
        <label class="block text-xs font-mono text-slate-400 uppercase tracking-wider mb-2">4. Khí Sắc Bùa (Color Palette)</label>
        <div class="flex items-center gap-3">
          <button
            v-for="p in palettes"
            :key="p.id"
            type="button"
            class="px-3 py-2 rounded-xl border text-xs font-mono flex items-center gap-2 transition-all min-h-[44px]"
            :class="colorPalette === p.id ? `${p.border} bg-midnight-800 text-white` : 'border-white/10 text-slate-400 hover:text-white'"
            @click="colorPalette = p.id; sound.playClick()"
          >
            <span class="w-3 h-3 rounded-full" :class="p.id === 'yellow' ? 'bg-talisman-gold' : p.id === 'crimson' ? 'bg-phantom-crimson' : p.id === 'cyan' ? 'bg-phantom-mint' : 'bg-phantom-purple'" />
            <span>{{ p.label }}</span>
          </button>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-wrap items-center gap-4 pt-2">
        <button
          type="button"
          class="flex-1 px-6 py-3.5 rounded-xl font-display font-bold text-sm transition-all shadow-lg flex items-center justify-center gap-2 min-h-[48px]"
          :class="isBlessingAnimation ? 'bg-slate-700 text-slate-400 cursor-not-allowed animate-pulse' : 'bg-gradient-to-r from-talisman-gold to-amber-500 text-midnight-950 hover:brightness-110 active:scale-95 shadow-glow-yellow'"
          :disabled="isBlessingAnimation"
          @click="triggerKhaiQuang"
        >
          <span>{{ isBlessingAnimation ? '⏳ Đang Niệm Chú Khai Quang...' : isBlessed ? '✨ Khai Quang Lại' : '🔥 Khai Quang Trì Chú' }}</span>
        </button>

        <button
          type="button"
          class="px-5 py-3.5 rounded-xl border border-white/10 hover:border-phantom-mint bg-midnight-900 text-slate-200 hover:text-white font-mono text-xs font-bold transition-all flex items-center gap-2 min-h-[48px]"
          @click="copyAscii"
        >
          <span>{{ copiedToast ? '✓ Đã Copy ASCII!' : '📋 Copy ASCII Card' }}</span>
        </button>
      </div>
    </div>

    <!-- Right: Live Visual Talisman Card (5 Cols) -->
    <div class="lg:col-span-5 flex flex-col items-center">
      <div
        class="w-full max-w-sm rounded-2xl border-2 p-6 transition-all duration-500 relative flex flex-col items-center text-center select-none bg-gradient-to-b from-midnight-900 via-midnight-950 to-midnight-900"
        :class="[activePaletteConfig.border, activePaletteConfig.glow, isBlessingAnimation ? 'animate-pulse scale-105' : '']"
      >
        <!-- Top Hanging Ring -->
        <div class="w-8 h-8 rounded-full border-2 border-dashed border-white/30 -mt-10 mb-3 flex items-center justify-center bg-midnight-950">
          <div class="w-3 h-3 rounded-full bg-talisman-gold" />
        </div>

        <!-- Protocol Rune -->
        <div class="text-[11px] font-mono tracking-widest uppercase mb-3 px-3 py-1 rounded-full border border-white/10" :class="activePaletteConfig.badge">
          {{ selectedPreset.runeTop }}
        </div>

        <!-- Calligraphic Title -->
        <h3 class="text-2xl font-display font-extrabold text-white tracking-wide mb-4">
          {{ selectedPreset.title }}
        </h3>

        <!-- Mystic Seal Code Box -->
        <div class="w-full p-3 rounded-xl bg-midnight-950/80 border border-white/10 font-mono text-xs text-phantom-mint mb-4 text-left break-words">
          <code>{{ selectedPreset.codeSnippet }}</code>
        </div>

        <!-- Developer Inscriptions -->
        <div class="w-full border-t border-b border-white/10 py-3 mb-4 space-y-1.5 text-left text-xs font-mono">
          <div class="text-slate-400">
            Kỹ Sư: <span class="text-white font-semibold">{{ displayName }}</span>
          </div>
          <div class="text-slate-400 leading-snug">
            Nguyện: <span class="text-amber-200/90 font-sans">{{ displayWish }}</span>
          </div>
        </div>

        <!-- Rotating Khai Quang Seal Badge -->
        <div class="mt-2 flex items-center justify-center h-16">
          <div
            v-if="isBlessed"
            class="px-4 py-2 rounded-full border-2 border-emerald-400 text-emerald-400 font-mono font-extrabold text-xs tracking-wider shadow-glow-mint transform -rotate-6 transition-all duration-300 scale-105 flex items-center gap-1.5"
          >
            <span>✓ ĐÃ KHAI QUANG</span>
          </div>
          <div
            v-else
            class="px-4 py-1.5 rounded-full border border-dashed border-slate-600 text-slate-500 font-mono text-[11px]"
          >
            [CHƯA KHAI QUANG]
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
```

---

### 6.3 `ProjectsSection.vue` & `ProjectModal.vue`

```vue
<!-- ProjectsSection.vue -->
<script setup lang="ts">
import { ref, computed } from 'vue';
import { projectsData } from '@/data/projectsData';
import type { Project } from '@/types/portfolio';
import ProjectModal from './ProjectModal.vue';
import { sound } from '@/audio/soundEffects';

type CategoryFilter = 'all' | 'fullstack' | 'creative' | 'ai-web3' | 'tools';

const activeCategory = ref<CategoryFilter>('all');
const selectedProject = ref<Project | null>(null);
const isModalOpen = ref(false);

const categories = [
  { id: 'all', label: 'Tất Cả Spells' },
  { id: 'fullstack', label: 'Full-Stack' },
  { id: 'creative', label: 'Creative & Audio' },
  { id: 'ai-web3', label: 'AI & Web3' },
  { id: 'tools', label: 'Developer Tools' }
] as const;

const filteredProjects = computed(() => {
  if (activeCategory.value === 'all') return projectsData;
  return projectsData.filter((p) => p.category === activeCategory.value);
});

const setCategory = (cat: CategoryFilter) => {
  activeCategory.value = cat;
  sound.playClick();
};

const openProject = (project: Project) => {
  selectedProject.value = project;
  isModalOpen.value = true;
  sound.playClick();
  if (typeof document !== 'undefined') {
    document.body.classList.add('overflow-hidden');
  }
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedProject.value = null;
  sound.playClick();
  if (typeof document !== 'undefined') {
    document.body.classList.remove('overflow-hidden');
  }
};
</script>

<template>
  <section id="projects" class="w-full py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-left">
    <!-- Header -->
    <div class="flex flex-col items-start mb-10">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-phantom-mint/10 border border-phantom-mint/30 text-phantom-mint text-xs font-mono mb-3">
        📜 The Midnight Grimoire
      </span>
      <h2 class="text-3xl sm:text-5xl font-display font-extrabold text-white tracking-tight">
        Tác Phẩm & <span class="text-transparent bg-clip-text bg-gradient-to-r from-phantom-mint via-phantom-cyan to-talisman-gold">Hệ Thống Đêm</span>
      </h2>
      <p class="text-sm sm:text-base text-slate-400 mt-2 max-w-2xl">
        Các dự án mã nguồn mở, kiến trúc phân tán siêu tải, giao diện Web Audio kỳ ảo được kiến tạo trong những phiên lập trình tĩnh lặng lúc nửa đêm.
      </p>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex items-center gap-2 mb-10 overflow-x-auto no-scrollbar pb-2">
      <button
        v-for="cat in categories"
        :key="cat.id"
        type="button"
        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-mono font-medium transition-all min-h-[44px] shrink-0 border"
        :class="activeCategory === cat.id
          ? 'bg-phantom-mint text-midnight-950 border-phantom-mint font-bold shadow-glow-mint'
          : 'bg-midnight-900/80 text-slate-400 border-white/5 hover:border-white/20 hover:text-white'"
        @click="setCategory(cat.id as CategoryFilter)"
      >
        {{ cat.label }}
      </button>
    </div>

    <!-- Project Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="project in filteredProjects"
        :key="project.id"
        class="rounded-2xl border border-white/10 glass-panel overflow-hidden flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:border-phantom-mint/50 group"
      >
        <!-- Card Cover Banner -->
        <div class="h-36 w-full p-4 flex flex-col justify-between bg-gradient-to-br" :class="project.coverGradient">
          <div class="flex items-center justify-between">
            <span class="px-2.5 py-0.5 rounded-full bg-black/50 backdrop-blur-md text-[10px] font-mono uppercase tracking-wider text-slate-200 border border-white/10">
              {{ project.category }}
            </span>
            <span v-if="project.featured" class="text-xs font-mono text-talisman-gold bg-black/50 px-2 py-0.5 rounded-full border border-talisman-gold/40">
              ★ Featured
            </span>
          </div>
          <h3 class="font-display font-bold text-lg text-white group-hover:text-phantom-mint transition-colors">
            {{ project.title }}
          </h3>
        </div>

        <!-- Card Content -->
        <div class="p-5 flex-1 flex flex-col justify-between text-left">
          <div>
            <p class="text-xs font-mono text-phantom-mint/90 mb-2">{{ project.tagline }}</p>
            <p class="text-xs text-slate-400 line-clamp-3 mb-4 leading-relaxed">{{ project.description }}</p>

            <!-- Metrics Preview -->
            <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-midnight-950/70 border border-white/5 mb-4">
              <div v-for="m in project.metrics" :key="m.label" class="text-center">
                <div class="text-[10px] font-mono text-slate-500 truncate">{{ m.label }}</div>
                <div class="text-xs font-display font-bold text-slate-200 mt-0.5">{{ m.value }}</div>
              </div>
            </div>

            <!-- Tech Stack Badges -->
            <div class="flex flex-wrap gap-1.5 mb-5">
              <span
                v-for="tech in project.tags.slice(0, 4)"
                :key="tech"
                class="px-2 py-0.5 rounded text-[10px] font-mono bg-white/5 text-slate-300 border border-white/5"
              >
                {{ tech }}
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 pt-2 border-t border-white/5">
            <button
              type="button"
              class="flex-1 py-2.5 px-3 rounded-xl bg-phantom-mint/10 hover:bg-phantom-mint text-phantom-mint hover:text-midnight-950 font-display font-bold text-xs transition-all min-h-[44px] flex items-center justify-center gap-1"
              @click="openProject(project)"
            >
              <span>Khám Phá Chi Tiết</span>
              <span>→</span>
            </button>
            <a
              v-if="project.githubUrl"
              :href="project.githubUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="p-2.5 rounded-xl bg-midnight-900 border border-white/10 hover:border-white/30 text-slate-300 hover:text-white transition-all min-h-[44px] min-w-[44px] flex items-center justify-center text-xs font-mono"
              title="GitHub Repo"
            >
              ⌥
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Project Modal Dialog -->
    <ProjectModal
      :is-open="isModalOpen"
      :project="selectedProject"
      @close="closeModal"
    />
  </section>
</template>
```

```vue
<!-- ProjectModal.vue -->
<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import type { Project } from '@/types/portfolio';

const props = defineProps<{
  isOpen: boolean;
  project: Project | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.isOpen) {
    emit('close');
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
  if (typeof document !== 'undefined') {
    document.body.classList.remove('overflow-hidden');
  }
});
</script>

<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="isOpen && project"
      class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-3xl rounded-3xl bg-midnight-900 border border-white/15 shadow-2xl overflow-hidden text-left my-8"
      >
        <!-- Modal Banner Header -->
        <div class="h-32 sm:h-40 w-full p-6 flex flex-col justify-between bg-gradient-to-br relative" :class="project.coverGradient">
          <div class="flex items-center justify-between">
            <span class="px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-xs font-mono uppercase tracking-wider text-slate-200 border border-white/10">
              {{ project.category }}
            </span>
            <button
              type="button"
              class="w-9 h-9 rounded-full bg-black/60 hover:bg-rose-500/80 text-white flex items-center justify-center transition-colors border border-white/20 min-h-[36px] min-w-[36px]"
              @click="emit('close')"
            >
              ✕
            </button>
          </div>
          <div>
            <h2 class="text-xl sm:text-3xl font-display font-extrabold text-white">{{ project.title }}</h2>
            <p class="text-xs sm:text-sm font-mono text-phantom-mint mt-1">{{ project.tagline }}</p>
          </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6 sm:p-8 space-y-6 max-h-[65vh] overflow-y-auto scrollbar-thin">
          <!-- Description -->
          <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-sans">{{ project.description }}</p>

          <!-- Key Metrics Grid -->
          <div>
            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-2.5">Chỉ Số Hiệu Năng (Key Metrics)</h4>
            <div class="grid grid-cols-3 gap-3">
              <div v-for="m in project.metrics" :key="m.label" class="p-3 rounded-xl bg-midnight-950 border border-white/5 text-center">
                <div class="text-xs font-mono text-slate-400">{{ m.label }}</div>
                <div class="text-base sm:text-lg font-display font-bold text-phantom-mint mt-1">{{ m.value }}</div>
              </div>
            </div>
          </div>

          <!-- Architecture Highlights -->
          <div>
            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-2.5">Kiến Trúc & Giải Pháp Kỹ Thuật</h4>
            <ul class="space-y-2 text-xs sm:text-sm text-slate-300">
              <li v-for="(highlight, i) in project.architectureHighlights" :key="i" class="flex items-start gap-2.5">
                <span class="text-phantom-mint mt-0.5">⚡</span>
                <span class="leading-relaxed">{{ highlight }}</span>
              </li>
            </ul>
          </div>

          <!-- Midnight Fact / Lore -->
          <div class="p-4 rounded-xl bg-amber-950/20 border border-talisman-gold/30 text-xs sm:text-sm text-amber-200/90 flex items-start gap-3">
            <span class="text-xl">🌙</span>
            <div>
              <div class="font-mono font-bold text-talisman-gold text-xs uppercase mb-0.5">Midnight Lore</div>
              <p class="leading-relaxed">{{ project.midnightFact }}</p>
            </div>
          </div>

          <!-- Tech Stack -->
          <div>
            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-2.5">Tech Rune Stack</h4>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="tech in project.techStack"
                :key="tech"
                class="px-3 py-1 rounded-lg text-xs font-mono bg-midnight-950 text-slate-200 border border-white/10 shadow-sm"
              >
                {{ tech }}
              </span>
            </div>
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="p-4 sm:p-6 bg-midnight-950/80 border-t border-white/10 flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <a
              v-if="project.liveUrl"
              :href="project.liveUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="px-4 py-2.5 rounded-xl bg-phantom-mint text-midnight-950 font-display font-bold text-xs sm:text-sm hover:brightness-110 transition-all flex items-center gap-2 min-h-[44px]"
            >
              <span>Xem Trực Tiếp Demo</span>
              <span>↗</span>
            </a>
            <a
              v-if="project.githubUrl"
              :href="project.githubUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="px-4 py-2.5 rounded-xl bg-midnight-800 border border-white/10 hover:border-white/30 text-white font-mono text-xs sm:text-sm transition-all flex items-center gap-2 min-h-[44px]"
            >
              <span>Mã Nguồn GitHub</span>
              <span>⌥</span>
            </a>
          </div>

          <button
            type="button"
            class="px-4 py-2 rounded-xl text-slate-400 hover:text-white font-mono text-xs min-h-[44px]"
            @click="emit('close')"
          >
            Đóng [ESC]
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>
```

---

## 7. Verification & Testing Traceability Matrix

| Feature ID | Feature Name | Test Suite Target | Verified Test Cases |
|---|---|---|---|
| **F10** | Midnight Terminal CLI Infrastructure | `tests/Unit/TerminalCliTest.test.ts` | `T1_F10_01` to `T1_F10_05`, `T2_F10_01` to `T2_F10_05` |
| **F11** | Full 11 Terminal Commands Suite | `tests/Unit/TerminalCliTest.test.ts` | `T1_F11_01` to `T1_F11_11`, `T2_F11_01` to `T2_F11_05` |
| **F12** | Developer Talisman Forge Presets & Inputs | `tests/Unit/TalismanForgeTest.test.ts` | `T1_F12_01` to `T1_F12_05`, `T2_F12_01` to `T2_F12_05` |
| **F13** | Khai Quang Blessing Seal Ritual | `tests/Unit/TalismanForgeTest.test.ts` | `T1_F13_01` to `T1_F13_05`, `T2_F13_01` to `T2_F13_05` |
| **F14** | ASCII Talisman Exporter & Clipboard | `tests/Unit/TalismanForgeTest.test.ts` | `T1_F14_01` to `T1_F14_05`, `T2_F14_01` to `T2_F14_05` |
| **F15** | Grimoire Project Showcase Grid | `tests/Components/GrimoireProjectsTest.test.ts` | `T1_F15_01` to `T1_F15_05`, `T2_F15_01` to `T2_F15_05` |
| **F16** | Project Modal Dialog & Accessibility | `tests/Components/GrimoireProjectsTest.test.ts` | `T1_F16_01` to `T1_F16_05`, `T2_F16_01` to `T2_F16_05` |
| **F22** | Responsive Layout & Anti-Collision | `tests/Components/ResponsiveLayoutTest.test.ts` | `T1_F22_01` to `T1_F22_05`, `T2_F22_01` to `T2_F22_05` |
| **E2E** | Real-World User Scenarios | `tests/E2E/Scenarios_01_to_06.test.ts` | `T4_04_TERMINAL_POWER_USER`, `T4_05_TALISMAN_FORGING_RITUAL`, `T4_06_PROJECT_GRIMOIRE_EXPLORATION` |

---
