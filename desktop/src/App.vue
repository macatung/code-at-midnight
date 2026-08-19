<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import ZenMascotStage from './components/ZenMascotStage.vue';
import CoderMascotStage from './components/CoderMascotStage.vue';
import DhammapadaSpeechBubble from './components/DhammapadaSpeechBubble.vue';
import BreathingPacer from './components/BreathingPacer.vue';
import SettingsModal from './components/SettingsModal.vue';
import PomodoroTimer from './components/PomodoroTimer.vue';
import RubberDuckModal from './components/RubberDuckModal.vue';
import QuickNotesModal from './components/QuickNotesModal.vue';
import DeskStretchingGuide from './components/DeskStretchingGuide.vue';
import WaterTrackerModal from './components/WaterTrackerModal.vue';
import TaskDispatchModal from './components/TaskDispatchModal.vue';
import EveningReviewModal from './components/EveningReviewModal.vue';
import CommandPaletteModal from './components/CommandPaletteModal.vue';
import DailyFocusBar from './components/DailyFocusBar.vue';
import { useMindfulScheduler } from './composables/useMindfulScheduler';
import { useTaskSync, TaskItem } from './composables/useTaskSync';
import { mindfulBell } from './audio/mindfulBellAudio';
import { sfx } from './audio/soundEffects';

const {
  settings,
  saveSettings,
  activeBubbleType,
  currentVerse,
  currentHealthReminder,
  drawRandomVerse,
  togglePersona,
  closeBubble,
} = useMindfulScheduler();

const {
  tasks,
  activeTask,
  isOnline,
  createTask,
  toggleTaskComplete,
  incrementPomodoro,
} = useTaskSync();

const isHovered = ref(false);
const activeCluster = ref<'flow' | 'zen' | 'body' | 'config' | null>(null);

// Active Modal State (Added 'palette')
type ActiveModal = 'palette' | 'dispatch' | 'review' | 'pomodoro' | 'duck' | 'notes' | 'stretch' | 'water' | 'breathing' | 'settings' | null;
const activeModal = ref<ActiveModal>(null);

const closeAllModals = () => {
  activeModal.value = null;
  activeCluster.value = null;
  closeBubble();
};

const openModal = (modal: ActiveModal) => {
  closeBubble();
  activeModal.value = activeModal.value === modal ? null : modal;
  activeCluster.value = null;
  sfx.playClick();
};

const toggleCluster = (cluster: 'flow' | 'zen' | 'body' | 'config') => {
  activeCluster.value = activeCluster.value === cluster ? null : cluster;
  sfx.playClick();
};

const handleMascotClick = () => {
  if (!activeBubbleType.value && !activeModal.value && !activeCluster.value) {
    drawRandomVerse();
    sfx.playDing();
  } else {
    closeAllModals();
  }
};

// Smooth Drag & Click Handler for Mascot
let isDragging = false;
let startScreenX = 0;
let startScreenY = 0;
let hasMoved = false;

const onMascotMouseDown = (e: MouseEvent) => {
  if (e.button !== 0) return; // Only left click
  isDragging = true;
  hasMoved = false;
  startScreenX = e.screenX;
  startScreenY = e.screenY;

  const onMouseMove = (moveEvent: MouseEvent) => {
    if (!isDragging) return;
    const dx = moveEvent.screenX - startScreenX;
    const dy = moveEvent.screenY - startScreenY;
    if (Math.abs(dx) >= 1 || Math.abs(dy) >= 1) {
      hasMoved = true;
      startScreenX = moveEvent.screenX;
      startScreenY = moveEvent.screenY;
      if ((window as any).desktopApi?.moveWindow) {
        (window as any).desktopApi.moveWindow(dx, dy);
      }
    }
  };

  const onMouseUp = () => {
    isDragging = false;
    window.removeEventListener('mousemove', onMouseMove);
    window.removeEventListener('mouseup', onMouseUp);

    // If mouse didn't drag, treat as a pure Click!
    if (!hasMoved) {
      handleMascotClick();
    }
  };

  window.addEventListener('mousemove', onMouseMove);
  window.addEventListener('mouseup', onMouseUp);
};

const handleRingBell = () => {
  mindfulBell.ringBell(settings.value.persona === 'zen' ? 432 : 528, 5.5);
};

const handleHideWindow = () => {
  if ((window as any).desktopApi) {
    (window as any).desktopApi.close();
  }
};

const handleStartTaskPomodoro = (task: TaskItem) => {
  activeTask.value = task;
  openModal('pomodoro');
};

const handlePomodoroCompleted = (task: TaskItem) => {
  incrementPomodoro(task);
  sfx.playSuccess();
};

const handleCreateTask = (title: string, priority = 'high') => {
  createTask(title, priority);
  sfx.playSuccess();
};

// Global Keyboard Shortcuts (Ctrl+K, Esc, Alt+Shift+P, etc.)
const handleKeyDown = (e: KeyboardEvent) => {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault();
    openModal('palette');
  } else if (e.altKey && e.code === 'Space') {
    e.preventDefault();
    openModal('palette');
  } else if (e.key === 'Escape') {
    closeAllModals();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);

  // System Tray Actions
  if ((window as any).desktopApi?.onTrayAction) {
    (window as any).desktopApi.onTrayAction((action: string) => {
      if (action === 'draw-verse') {
        closeAllModals();
        drawRandomVerse();
      } else if (action === 'ring-bell') {
        handleRingBell();
      } else if (action === 'toggle-persona') {
        togglePersona();
      } else if (action === 'open-dispatch') {
        openModal('dispatch');
      } else if (action === 'open-review') {
        openModal('review');
      } else if (action === 'open-pomodoro') {
        openModal('pomodoro');
      } else if (action === 'open-duck') {
        openModal('duck');
      } else if (action === 'open-notes') {
        openModal('notes');
      } else if (action === 'open-stretch') {
        openModal('stretch');
      } else if (action === 'open-water') {
        openModal('water');
      } else if (action === 'start-breathing') {
        openModal('breathing');
      } else if (action === 'open-settings') {
        openModal('settings');
      }
    });
  }
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
  <!-- Main Canvas: Placed at bottom-right corner of screen, panels open to the LEFT -->
  <div class="w-full h-full p-4 flex items-end justify-end relative select-none bg-transparent overflow-visible font-sans">
    
    <!-- ========================================================================= -->
    <!-- FLOATING EXTENSION PANELS (NẰM BÊN TRÁI CHÚ MASCOT ĐỂ KHÔNG BỊ TRÀN MÀN HÌNH)-->
    <!-- ========================================================================= -->
    <div class="mr-3 mb-2 z-30 shrink-0">
      <!-- 0. RAYCAST COMMAND PALETTE (Spotlight) -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <CommandPaletteModal
          v-if="activeModal === 'palette'"
          @close="activeModal = null"
          @create-task="handleCreateTask"
          @start-pomodoro="openModal('pomodoro')"
          @ring-bell="handleRingBell"
          @toggle-persona="togglePersona"
          @draw-verse="drawRandomVerse"
          @add-water="openModal('water')"
          @open-duck="openModal('duck')"
          @open-notes="openModal('notes')"
          @open-stretch="openModal('stretch')"
          @start-breathing="openModal('breathing')"
          @open-settings="openModal('settings')"
          @open-dispatch="openModal('dispatch')"
          @open-review="openModal('review')"
        />
      </Transition>

      <!-- 1. Dhammapada or Health Reminder Speech Bubble -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <DhammapadaSpeechBubble
          v-if="activeBubbleType"
          :type="activeBubbleType === 'verse' ? 'verse' : 'health'"
          :verse="currentVerse"
          :health-reminder="currentHealthReminder"
          :persona="settings.persona"
          @close="closeBubble"
          @draw-next="drawRandomVerse"
          @start-breathing="openModal('breathing')"
        />
      </Transition>

      <!-- 2. Task Dispatch Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <TaskDispatchModal
          v-if="activeModal === 'dispatch'"
          :tasks="tasks"
          :is-online="isOnline"
          @close="activeModal = null"
          @start-pomodoro="handleStartTaskPomodoro"
          @toggle-complete="toggleTaskComplete"
          @create-task="handleCreateTask"
        />
      </Transition>

      <!-- 3. Evening Review Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <EveningReviewModal
          v-if="activeModal === 'review'"
          :tasks="tasks"
          @close="activeModal = null"
        />
      </Transition>

      <!-- 4. Pomodoro Deep Work Timer (Supports Compact Mini Pill & Full Dial) -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <PomodoroTimer
          v-if="activeModal === 'pomodoro'"
          :active-task="activeTask"
          @pomodoro-completed="handlePomodoroCompleted"
          @close="activeModal = null"
        />
      </Transition>

      <!-- 5. Rubber Duck Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <RubberDuckModal
          v-if="activeModal === 'duck'"
          @close="activeModal = null"
        />
      </Transition>

      <!-- 6. Quick Notes Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <QuickNotesModal
          v-if="activeModal === 'notes'"
          @close="activeModal = null"
        />
      </Transition>

      <!-- 7. Desk Stretching Guide Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <DeskStretchingGuide
          v-if="activeModal === 'stretch'"
          @close="activeModal = null"
        />
      </Transition>

      <!-- 8. Water Tracker Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <WaterTrackerModal
          v-if="activeModal === 'water'"
          @close="activeModal = null"
        />
      </Transition>

      <!-- 9. Breathing Pacer Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <BreathingPacer
          v-if="activeModal === 'breathing'"
          @close="activeModal = null"
        />
      </Transition>

      <!-- 10. Settings Modal -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-x-4 scale-95"
        enter-to-class="opacity-100 translate-x-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0 scale-100"
        leave-to-class="opacity-0 translate-x-4 scale-95"
      >
        <SettingsModal
          v-if="activeModal === 'settings'"
          :settings="settings"
          @save="saveSettings"
          @close="activeModal = null"
        />
      </Transition>
    </div>

    <!-- ========================================================================= -->
    <!-- MASCOT STAGE (NẰM Ở GÓC PHẢI DƯỚI CÙNG, KÈM DAILY PROGRESS BAR)           -->
    <!-- ========================================================================= -->
    <div
      class="no-drag relative flex flex-col items-center cursor-pointer active:scale-98 transition-transform z-20 shrink-0 mr-2 mb-2 group/mascot"
      @mouseenter="isHovered = true"
      @mouseleave="isHovered = false"
      @mousedown="onMascotMouseDown"
      title="Click để rút bài Pháp Cú • Kéo giữ để di chuyển"
    >
      <!-- 4-HUB DOCK + ⌘K SPOTLIGHT TRIGGER (Anchored right-0 above mascot, flows leftwards) -->
      <div
        class="no-drag absolute -top-11 right-0 flex items-center gap-1 p-1 rounded-2xl bg-slate-950/98 border border-slate-800 shadow-2xl transition-all duration-300 backdrop-blur-xl z-30 ring-1 ring-white/10 whitespace-nowrap"
        :class="isHovered || activeCluster ? 'opacity-100 translate-y-0 scale-100 pointer-events-auto' : 'opacity-0 translate-y-2 scale-90 pointer-events-none'"
        @click.stop
        @mousedown.stop
      >
        <!-- 0. RAYCAST COMMAND PALETTE BUTTON -->
        <button
          @click="openModal('palette')"
          :class="[
            'px-2 py-1 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1',
            activeModal === 'palette'
              ? 'bg-purple-500 text-white shadow-md shadow-purple-500/20'
              : 'text-purple-400 hover:bg-slate-900'
          ]"
          title="⌘K — Quick Command Palette"
        >
          <span class="font-mono text-[11px] font-bold">⌘K</span>
        </button>

        <!-- 1. HUB: CÔNG VIỆC & TASK (FLOW) -->
        <button
          @click="toggleCluster('flow')"
          :class="[
            'px-2 py-1 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1',
            activeCluster === 'flow'
              ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20'
              : 'text-amber-400 hover:bg-slate-900'
          ]"
          title="🎯 Nhiệm vụ & Pomodoro Flow"
        >
          <span>🎯</span>
          <span class="text-[10px] font-mono">Tasks</span>
        </button>

        <!-- 2. HUB: CHÁNH NIỆM (ZEN) -->
        <button
          @click="toggleCluster('zen')"
          :class="[
            'px-2 py-1 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1',
            activeCluster === 'zen'
              ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20'
              : 'text-emerald-400 hover:bg-slate-900'
          ]"
          title="🧘 Chánh niệm, Kệ Pháp Cú & Chuông"
        >
          <span>🧘</span>
          <span class="text-[10px] font-mono">Zen</span>
        </button>

        <!-- 3. HUB: SỨC KHỎE (BODY) -->
        <button
          @click="toggleCluster('body')"
          :class="[
            'px-2 py-1 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1',
            activeCluster === 'body'
              ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/20'
              : 'text-cyan-400 hover:bg-slate-900'
          ]"
          title="💧 Uống nước & Giãn cơ 30s"
        >
          <span>💧</span>
          <span class="text-[10px] font-mono">Body</span>
        </button>

        <!-- 4. HUB: CÀI ĐẶT & CHẾ ĐỘ (CONFIG) -->
        <button
          @click="toggleCluster('config')"
          :class="[
            'px-2 py-1 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-1',
            activeCluster === 'config'
              ? 'bg-slate-700 text-white shadow-md'
              : 'text-slate-400 hover:bg-slate-900'
          ]"
          title="⚙️ Đổi chế độ & Cài đặt"
        >
          <span>⚙️</span>
        </button>
      </div>

      <!-- SUB-CLUSTER POPUP (Anchored right-0 above dock, flows leftwards) -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-2 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 -translate-y-2 scale-95"
      >
        <div
          v-if="activeCluster"
          class="no-drag absolute -top-22 right-0 flex items-center gap-1.5 p-1.5 rounded-2xl bg-slate-950/98 border border-slate-700/80 shadow-2xl backdrop-blur-xl z-40 ring-1 ring-white/15 whitespace-nowrap"
          @click.stop
          @mousedown.stop
        >
          <!-- SUB: FLOW (Tasks & Pomodoro) -->
          <template v-if="activeCluster === 'flow'">
            <button
              @click="openModal('dispatch')"
              class="px-2.5 py-1 rounded-xl bg-amber-500/15 hover:bg-amber-500 text-amber-300 hover:text-slate-950 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            >
              <span>📋</span>
              <span class="text-[11px]">Nhiệm Vụ</span>
            </button>
            <button
              @click="openModal('pomodoro')"
              class="px-2.5 py-1 rounded-xl bg-emerald-500/15 hover:bg-emerald-500 text-emerald-300 hover:text-slate-950 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            >
              <span>🍅</span>
              <span class="text-[11px]">Pomodoro</span>
            </button>
            <button
              @click="openModal('duck')"
              class="px-2 py-1 rounded-xl bg-slate-900 hover:bg-slate-800 text-yellow-300 text-xs font-bold transition-all cursor-pointer"
              title="Debug Tâm Thức & Yểm Bùa"
            >
              🦆
            </button>
            <button
              @click="openModal('notes')"
              class="px-2 py-1 rounded-xl bg-slate-900 hover:bg-slate-800 text-blue-300 text-xs font-bold transition-all cursor-pointer"
              title="Top Việc & Nháp Nhanh"
            >
              📝
            </button>
            <button
              @click="openModal('review')"
              class="px-2 py-1 rounded-xl bg-purple-500/20 hover:bg-purple-500 text-purple-300 hover:text-white text-xs font-bold transition-all cursor-pointer"
              title="Review Cuối Ngày"
            >
              🌙
            </button>
          </template>

          <!-- SUB: ZEN (Chánh Niệm) -->
          <template v-else-if="activeCluster === 'zen'">
            <button
              @click="drawRandomVerse"
              class="px-2.5 py-1 rounded-xl bg-amber-500/15 hover:bg-amber-500 text-amber-300 hover:text-slate-950 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            >
              <span>📜</span>
              <span class="text-[11px]">Kệ Pháp Cú</span>
            </button>
            <button
              @click="handleRingBell"
              class="px-2.5 py-1 rounded-xl bg-emerald-500/15 hover:bg-emerald-500 text-emerald-300 hover:text-slate-950 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            >
              <span>🔔</span>
              <span class="text-[11px]">Chuông 432Hz</span>
            </button>
            <button
              @click="openModal('breathing')"
              class="px-2.5 py-1 rounded-xl bg-pink-500/15 hover:bg-pink-500 text-pink-300 hover:text-slate-950 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            >
              <span>🌸</span>
              <span class="text-[11px]">Thở 3 Nhịp</span>
            </button>
          </template>

          <!-- SUB: BODY (Sức Khỏe) -->
          <template v-else-if="activeCluster === 'body'">
            <button
              @click="openModal('water')"
              class="px-2.5 py-1 rounded-xl bg-blue-500/15 hover:bg-blue-500 text-blue-300 hover:text-slate-950 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            >
              <span>💧</span>
              <span class="text-[11px]">8 Ly Nước</span>
            </button>
            <button
              @click="openModal('stretch')"
              class="px-2.5 py-1 rounded-xl bg-cyan-500/15 hover:bg-cyan-500 text-cyan-300 hover:text-slate-950 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            >
              <span>🧘‍♂️</span>
              <span class="text-[11px]">Giãn Cơ 30s</span>
            </button>
          </template>

          <!-- SUB: CONFIG (Tùy Chỉnh) -->
          <template v-else-if="activeCluster === 'config'">
            <button
              @click="togglePersona"
              class="px-2.5 py-1 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            >
              <span>🎭</span>
              <span class="text-[11px]">{{ settings.persona === 'zen' ? 'Đổi sang Dev Coder' : 'Đổi sang Tọa Thiền' }}</span>
            </button>
            <button
              @click="openModal('settings')"
              class="px-2 py-1 rounded-xl bg-slate-900 hover:bg-slate-800 text-stone-300 text-xs font-bold transition-all cursor-pointer"
              title="Cài đặt nhắc nhở"
            >
              ⚙️
            </button>
            <button
              @click="handleHideWindow"
              class="px-2 py-1 rounded-xl bg-slate-900 hover:bg-red-500 text-stone-400 hover:text-white text-xs font-bold transition-all cursor-pointer"
              title="Ẩn xuống khay hệ thống"
            >
              ✕
            </button>
          </template>
        </div>
      </Transition>

      <!-- Mascot Switcher Stage (Static & Peacefully Still) -->
      <CoderMascotStage v-if="settings.persona === 'coder'" :is-hovered="isHovered" />
      <ZenMascotStage v-else :is-hovered="isHovered" />

      <!-- Daily Focus Progress Bar (Under Persona Badge) -->
      <DailyFocusBar :tasks="tasks" />
    </div>
  </div>
</template>

<style scoped>
.drag-region {
  -webkit-app-region: drag;
}

.no-drag {
  -webkit-app-region: no-drag;
}
</style>