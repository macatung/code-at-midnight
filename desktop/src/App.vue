<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import ZenMascotStage from './components/ZenMascotStage.vue';
import TaskDispatchModal from './components/TaskDispatchModal.vue';
import PomodoroTimer from './components/PomodoroTimer.vue';
import EveningReviewModal from './components/EveningReviewModal.vue';
import RubberDuckModal from './components/RubberDuckModal.vue';
import QuickNotesModal from './components/QuickNotesModal.vue';
import CommandPaletteModal from './components/CommandPaletteModal.vue';
import DailyFocusBar from './components/DailyFocusBar.vue';
import AgentConsoleModal from './components/AgentConsoleModal.vue';
import UpdateStatus from './components/UpdateStatus.vue';
import { useTaskSync, TaskItem } from './composables/useTaskSync';
import { sfx } from './audio/soundEffects';

const { tasks, agentTasks, activeTask, isOnline, createTask, toggleTaskComplete, incrementPomodoro } = useTaskSync();
const isHovered = ref(false);
const activeCluster = ref<'tasks' | 'config' | null>(null);
const TASK_HUB_URL = (import.meta as any).env?.VITE_TASK_HUB_URL || 'https://tasks.macatung.dev';
type ActiveModal = 'palette' | 'dispatch' | 'review' | 'pomodoro' | 'duck' | 'notes' | 'agent' | null;
const activeModal = ref<ActiveModal>(null);

const openWebAction = (path = '/tasks') => {
  const url = `${TASK_HUB_URL}${path}`;
  if ((window as any).desktopApi?.openExternal) (window as any).desktopApi.openExternal(url);
  else window.open(url, '_blank');
};

const closeAll = () => { activeModal.value = null; activeCluster.value = null; };
const openModal = (modal: ActiveModal) => {
  activeModal.value = activeModal.value === modal ? null : modal;
  activeCluster.value = null;
  sfx.playClick();
};
const toggleCluster = (cluster: 'tasks' | 'config') => {
  activeCluster.value = activeCluster.value === cluster ? null : cluster;
  sfx.playClick();
};
const handleMascotClick = () => (!activeModal.value && !activeCluster.value ? openModal('dispatch') : closeAll());

let isDragging = false;
let startScreenX = 0;
let startScreenY = 0;
let hasMoved = false;
const onMascotMouseDown = (event: MouseEvent) => {
  if (event.button !== 0) return;
  isDragging = true; hasMoved = false; startScreenX = event.screenX; startScreenY = event.screenY;
  const onMouseMove = (moveEvent: MouseEvent) => {
    if (!isDragging) return;
    const dx = moveEvent.screenX - startScreenX; const dy = moveEvent.screenY - startScreenY;
    if (Math.abs(dx) >= 1 || Math.abs(dy) >= 1) {
      hasMoved = true; startScreenX = moveEvent.screenX; startScreenY = moveEvent.screenY;
      (window as any).desktopApi?.moveWindow?.(dx, dy);
    }
  };
  const onMouseUp = () => {
    isDragging = false; window.removeEventListener('mousemove', onMouseMove); window.removeEventListener('mouseup', onMouseUp);
    if (!hasMoved) handleMascotClick();
  };
  window.addEventListener('mousemove', onMouseMove); window.addEventListener('mouseup', onMouseUp);
};

const handleCreateTask = (title: string, priority = 'high') => { createTask(title, priority); sfx.playSuccess(); };
const handleStartPomodoro = (task: TaskItem) => { activeTask.value = task; openModal('pomodoro'); };
const handlePomodoroCompleted = (task: TaskItem) => { incrementPomodoro(task); sfx.playSuccess(); };
const hideMascot = () => (window as any).desktopApi?.close?.();
const checkForUpdates = () => (window as any).desktopApi?.updater?.check?.();
const installUpdate = () => (window as any).desktopApi?.updater?.install?.();
const handleKeyDown = (event: KeyboardEvent) => {
  if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); openModal('palette'); }
  else if (event.key === 'Escape') closeAll();
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
  (window as any).desktopApi?.onTrayAction?.((action: string) => {
    const actions: Record<string, ActiveModal> = { 'open-dispatch': 'dispatch', 'open-agent': 'agent', 'open-review': 'review', 'open-pomodoro': 'pomodoro', 'open-duck': 'duck', 'open-notes': 'notes' };
    if (actions[action]) openModal(actions[action]);
    if (action === 'open-tasks') openWebAction('/tasks');
    if (action === 'check-updates') checkForUpdates();
    if (action === 'install-update') installUpdate();
  });
});
onUnmounted(() => window.removeEventListener('keydown', handleKeyDown));
</script>

<template>
  <div class="w-full h-full p-4 flex items-end justify-end relative select-none bg-transparent overflow-visible font-sans">
    <div class="mr-3 mb-2 z-30 shrink-0">
      <UpdateStatus />
      <CommandPaletteModal v-if="activeModal === 'palette'" @close="activeModal = null" @create-task="handleCreateTask" @start-pomodoro="openModal('pomodoro')" @open-duck="openModal('duck')" @open-notes="openModal('notes')" @open-dispatch="openModal('dispatch')" @open-review="openModal('review')" @check-updates="checkForUpdates" @install-update="installUpdate" />
      <TaskDispatchModal v-if="activeModal === 'dispatch'" :tasks="tasks" :is-online="isOnline" @close="activeModal = null" @start-pomodoro="handleStartPomodoro" @toggle-complete="toggleTaskComplete" @create-task="handleCreateTask" />
      <AgentConsoleModal v-if="activeModal === 'agent'" :tasks="agentTasks" :initial-task="activeTask" @close="activeModal = null" />
      <PomodoroTimer v-if="activeModal === 'pomodoro'" :active-task="activeTask" @pomodoro-completed="handlePomodoroCompleted" @close="activeModal = null" />
      <EveningReviewModal v-if="activeModal === 'review'" :tasks="tasks" @close="activeModal = null" />
      <RubberDuckModal v-if="activeModal === 'duck'" @close="activeModal = null" />
      <QuickNotesModal v-if="activeModal === 'notes'" @close="activeModal = null" />
    </div>

    <div class="mascot-shell no-drag relative flex flex-col items-center cursor-pointer active:scale-98 transition-transform z-20 shrink-0 mr-2 mb-2" @mouseenter="isHovered = true" @mouseleave="isHovered = false" @mousedown="onMascotMouseDown" title="Mở Tasks và kéo để di chuyển">
      <div class="no-drag absolute -top-11 right-0 flex items-center gap-1 p-1 rounded-2xl bg-slate-950/98 border border-slate-800 shadow-2xl transition-all duration-300 backdrop-blur-xl z-30 ring-1 ring-white/10 whitespace-nowrap" :class="isHovered || activeCluster ? 'opacity-100 translate-y-0 scale-100 pointer-events-auto' : 'opacity-0 translate-y-2 scale-90 pointer-events-none'" @click.stop @mousedown.stop>
        <button @click="openModal('palette')" class="dock-button text-purple-400">⌘K</button>
        <button @click="toggleCluster('tasks')" class="dock-button text-amber-400">🎯 Tasks</button>
        <button @click="openWebAction('/tasks')" class="dock-button text-indigo-300">🌐 Hub</button>
        <button @click="toggleCluster('config')" class="dock-button text-slate-400">⚙️</button>
      </div>

      <div v-if="activeCluster" class="no-drag absolute -top-22 right-0 flex items-center gap-1.5 p-1.5 rounded-2xl bg-slate-950/98 border border-slate-700/80 shadow-2xl z-40 ring-1 ring-white/15 whitespace-nowrap" @click.stop @mousedown.stop>
        <template v-if="activeCluster === 'tasks'">
          <button @click="openModal('agent')" class="task-action text-blue-300">🤖 Agent</button>
          <button @click="openModal('dispatch')" class="task-action text-amber-300">📋 Nhiệm vụ</button>
          <button @click="openModal('pomodoro')" class="task-action text-emerald-300">🍅 Pomodoro</button>
          <button @click="openModal('review')" class="task-action text-purple-300">🌙 Review</button>
          <button @click="openModal('duck')" class="task-action text-yellow-300">🦆 Debug</button>
          <button @click="openModal('notes')" class="task-action text-cyan-300">📝 Notes</button>
          <button @click="openWebAction('/tasks?open=ai-plan')" class="task-action text-violet-300">✨ AI Plan</button>
          <button @click="openWebAction('/tasks?open=email-report')" class="task-action text-sky-300">✉️ Report</button>
        </template>
        <template v-else>
          <button @click="openWebAction('/tasks?open=ai-settings')" class="task-action text-violet-300">✨ AI Settings</button>
          <button @click="openWebAction('/tasks')" class="task-action text-indigo-300">🌐 Tasks Hub</button>
          <button @click="hideMascot" class="task-action text-slate-300">✕ Ẩn mascot</button>
        </template>
      </div>

      <ZenMascotStage :is-hovered="isHovered" />
      <DailyFocusBar :tasks="tasks" />
    </div>
  </div>
</template>

<style scoped>
.no-drag { -webkit-app-region: no-drag; }
.mascot-shell::before { content: ''; position: absolute; top: -3.5rem; left: -1.25rem; right: -1.25rem; height: 3.5rem; z-index: 0; }
.dock-button { padding: .25rem .5rem; border-radius: .75rem; font-size: .75rem; font-weight: 700; transition: background-color .15s; cursor: pointer; }
.dock-button:hover, .task-action:hover { background: rgb(15 23 42); }
.task-action { padding: .25rem .625rem; border-radius: .75rem; font-size: 11px; font-weight: 700; transition: background-color .15s; cursor: pointer; }
</style>
