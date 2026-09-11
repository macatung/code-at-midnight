<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Icons from '@/Components/ui/Icons.vue';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

const downloadUrl = 'https://github.com/macatung/code-at-midnight/releases/latest/download/Task-Companion-Setup.exe';

// Interactive Floating Mini-Widget Preview State
const isTaskDone = ref(false);
const isTimerRunning = ref(false);
const timerSeconds = ref(1500); // 25:00
let intervalId: any = null;

const formattedTimer = computed(() => {
  const mins = Math.floor(timerSeconds.value / 60).toString().padStart(2, '0');
  const secs = (timerSeconds.value % 60).toString().padStart(2, '0');
  return `${mins}:${secs}`;
});

const toggleTimer = () => {
  isTimerRunning.value = !isTimerRunning.value;
  if (isTimerRunning.value) {
    intervalId = setInterval(() => {
      if (timerSeconds.value > 0) {
        timerSeconds.value--;
      } else {
        clearInterval(intervalId);
        isTimerRunning.value = false;
      }
    }, 1000);
  } else {
    clearInterval(intervalId);
  }
};
</script>

<template>
  <article
    class="relative group rounded-3xl border border-phantom-mint/20 bg-gradient-to-b from-[#07131a] via-midnight-900 to-midnight-950 p-6 sm:p-8 overflow-hidden transition-all duration-300 hover:border-phantom-mint/40 hover:shadow-2xl hover:shadow-phantom-mint/10 flex flex-col justify-between"
    aria-label="Pillar 4: Task Companion Desktop Application"
  >
    <!-- Ambient Mint Glow -->
    <div class="absolute -right-20 -top-20 w-72 h-72 bg-phantom-mint/10 rounded-full blur-3xl pointer-events-none group-hover:bg-phantom-mint/15 transition-all duration-500"></div>

    <div>
      <!-- Header Badges -->
      <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-phantom-mint/15 text-phantom-mint border border-phantom-mint/30">
            PILLAR 04
          </span>
          <span class="px-2.5 py-1 rounded-full text-xs font-mono bg-slate-900 text-slate-300 border border-white/10 flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-phantom-mint animate-pulse"></span>
            Windows 10/11 x64
          </span>
        </div>

        <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-phantom-mint/10 border border-phantom-mint/20 text-xs font-mono text-phantom-mint">
          <Icons name="Cpu" size="13" />
          <span>AWS Labs CAO Daemon</span>
        </div>
      </div>

      <!-- Title & Description -->
      <div class="mb-6">
        <div class="text-xs font-mono uppercase tracking-widest text-phantom-mint/90 mb-1">
          {{ isVi ? 'ỨNG DỤNG NỀN TẢNG DESKTOP ĐIỀU PHỐI AI AGENT CHO WINDOWS' : 'WINDOWS DESKTOP AI AGENT ORCHESTRATOR' }}
        </div>
        <h3 class="text-2xl sm:text-3xl font-display font-black tracking-tight text-white group-hover:text-phantom-mint transition-colors">
          {{ isVi ? 'Task Companion — Trợ Thủ AI Agent Trên Desktop' : 'Task Companion for Windows' }}
        </h3>
        <p class="mt-3 text-sm sm:text-base text-slate-300 leading-relaxed">
          {{ isVi
            ? 'Ứng dụng desktop bản địa dành cho Windows 10/11 kết nối trực tiếp với Task Hub, tự động điều phối Codex CLI, Claude Code và Antigravity qua chuẩn AWS Labs CAO. Tích hợp Monaco diff viewer, terminal streaming thời gian thực và thanh nổi floating mini-widget tiện dụng.'
            : 'A native desktop application for Windows 10/11 directly interfacing with Task Hub. Supervised multi-agent orchestration across Codex, Claude Code, and Antigravity with AWS Labs CAO, Monaco diff viewer, terminal stream, and floating mini-widget.'
          }}
        </p>
      </div>

      <!-- Interactive Floating Mini-Widget Preview -->
      <div class="rounded-2xl border border-phantom-mint/30 bg-black/40 p-4 sm:p-5 mb-6 backdrop-blur-md font-mono">
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-white/10 text-xs">
          <span class="text-phantom-mint font-bold flex items-center gap-1.5">
            <span class="h-2 w-2 rounded-full bg-phantom-mint animate-ping"></span>
            {{ isVi ? 'MINI FLOATING WIDGET PREVIEW' : 'FLOATING WIDGET PREVIEW' }}
          </span>
          <span class="text-[11px] text-slate-400">Always-on-top IDE overlay</span>
        </div>

        <!-- Simulated Draggable Mini Widget Pill -->
        <div class="p-3.5 rounded-2xl border border-phantom-mint/40 bg-midnight-950/90 shadow-xl shadow-black/60 flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="flex items-center gap-3 w-full sm:w-auto">
            <button
              type="button"
              @click="isTaskDone = !isTaskDone"
              class="h-5 w-5 rounded-md border flex items-center justify-center transition-colors"
              :class="isTaskDone ? 'bg-phantom-mint border-phantom-mint text-midnight-950' : 'border-white/30 hover:border-phantom-mint text-transparent'"
              :title="isVi ? 'Đánh dấu task' : 'Toggle task'"
            >
              <Icons name="Check" size="14" class="font-black" />
            </button>
            <div class="text-xs truncate max-w-[200px] sm:max-w-xs">
              <span :class="{ 'line-through text-slate-500': isTaskDone, 'text-white': !isTaskDone }">
                {{ isVi ? 'Task: MCP auto-auth & agent lifecycle' : 'Task: MCP auto-auth & agent lifecycle' }}
              </span>
            </div>
          </div>

          <!-- Pomodoro & Agents Pill -->
          <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <button
              type="button"
              @click="toggleTimer"
              class="px-2.5 py-1 rounded-lg border text-xs font-mono flex items-center gap-1.5 transition-colors"
              :class="isTimerRunning ? 'border-amber-400/40 bg-amber-500/10 text-amber-300' : 'border-white/10 bg-white/5 text-slate-300 hover:border-phantom-mint/30'"
            >
              <Icons :name="isTimerRunning ? 'Clock' : 'Play'" size="12" />
              <span>{{ formattedTimer }}</span>
            </button>
            <span class="px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 text-[11px]">
              Codex: Active
            </span>
          </div>
        </div>

        <!-- Agent Fleet Status Row -->
        <div class="grid grid-cols-3 gap-2 mt-3 text-[11px] text-center">
          <div class="p-2 rounded-lg bg-white/[0.02] border border-white/5">
            <span class="text-slate-500 text-[10px]">Orchestrator:</span>
            <div class="text-phantom-mint font-bold mt-0.5">AWS Labs CAO</div>
          </div>
          <div class="p-2 rounded-lg bg-white/[0.02] border border-white/5">
            <span class="text-slate-500 text-[10px]">Diff Engine:</span>
            <div class="text-cyan-400 font-bold mt-0.5">Monaco Editor</div>
          </div>
          <div class="p-2 rounded-lg bg-white/[0.02] border border-white/5">
            <span class="text-slate-500 text-[10px]">Guardrails:</span>
            <div class="text-amber-400 font-bold mt-0.5">Zero-Destructive</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Actions -->
    <div class="pt-4 border-t border-phantom-mint/20 flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap gap-2 text-xs font-mono text-slate-400">
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-phantom-mint">v1.1.2 NSIS</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-phantom-mint">Auto-Update</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-phantom-mint">Apache-2.0</span>
      </div>

      <div class="flex items-center gap-2">
        <a
          href="/desktop"
          class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 text-white text-xs font-medium transition-colors"
        >
          <span>{{ isVi ? 'Chi Tiết' : 'Specs' }}</span>
        </a>
        <a
          :href="downloadUrl"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-phantom-mint hover:bg-emerald-400 text-midnight-950 font-bold text-xs tracking-wide transition-all duration-200 hover:-translate-y-0.5 shadow-lg shadow-phantom-mint/20"
        >
          <Icons name="Download" size="14" />
          <span>{{ isVi ? 'Tải .exe Cho Windows' : 'Download for Windows' }}</span>
        </a>
      </div>
    </div>
  </article>
</template>
