<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import MiniMascotLogo from '@/Components/mascot/MiniMascotLogo.vue';
import { sound } from '@/audio/soundEffects';

export interface ProjectItem {
  id: number;
  title: string;
  slug: string;
  category?: string;
  type: 'work' | 'personal';
  color?: string;
  description?: string | null;
  tasks_count?: number;
}

export interface SubtaskItem {
  id: string;
  text: string;
  done: boolean;
}

export interface TaskItem {
  id: number;
  project_id: number | null;
  project?: ProjectItem | null;
  title: string;
  description: string | null;
  status: 'todo' | 'in_progress' | 'review' | 'done';
  priority: 'urgent' | 'high' | 'medium' | 'low';
  category: string;
  estimated_pomodoros: number;
  completed_pomodoros: number;
  due_date: string | null;
  completed_at: string | null;
  notes: string | null;
  subtasks?: SubtaskItem[];
  created_at?: string;
  updated_at?: string;
}

export interface Stats {
  total: number;
  todo: number;
  in_progress: number;
  review: number;
  done: number;
  total_pomodoros_estimated: number;
  total_pomodoros_completed: number;
  completion_rate: number;
}

const props = defineProps<{
  tasks: TaskItem[];
  projects: ProjectItem[];
  stats: Stats;
  selectedDate: string;
  selectedProjectId?: string | number;
}>();

const taskList = ref<TaskItem[]>(
  props.tasks.map(t => ({
    ...t,
    subtasks: t.notes ? tryParseSubtasks(t.notes) : [],
  }))
);

const projectList = ref<ProjectItem[]>([...props.projects]);

function tryParseSubtasks(notes: string): SubtaskItem[] {
  try {
    const parsed = JSON.parse(notes);
    if (Array.isArray(parsed)) return parsed;
  } catch (e) {}
  return [];
}

// Sidebar State
const isSidebarOpen = ref(true);
const selectedProjectId = ref<string | number>(props.selectedProjectId || 'all');
const activeProjectMenuId = ref<number | null>(null);

// Filter & View State
const viewMode = ref<'kanban' | 'list'>('kanban');
const searchQuery = ref('');
const activeFilter = ref<'all' | 'today' | 'urgent' | 'in_progress' | 'uncompleted'>('all');
const selectedCategory = ref<string>('all');
const quickInputText = ref('');
const quickInputRef = ref<HTMLInputElement | null>(null);
const searchInputRef = ref<HTMLInputElement | null>(null);

// Modals & Drawer State
const selectedTask = ref<TaskItem | null>(null);
const showCreateModal = ref(false);
const showDispatchModal = ref(false);
const showReviewModal = ref(false);
const isSubmitting = ref(false);
const newSubtaskText = ref('');

// PIN Security State (Master PIN: 301095)
const isPinUnlocked = ref(false);
const pinInput = ref('');
const pinError = ref('');
const isPinShaking = ref(false);
const pinInputRef = ref<HTMLInputElement | null>(null);

const checkPin = () => {
  if (pinInput.value === '301095') {
    sound.playSuccess();
    isPinUnlocked.value = true;
    pinError.value = '';
    sessionStorage.setItem('macatung_tasks_pin_auth', '301095');
  } else {
    sound.playError();
    pinError.value = 'Mã PIN bảo mật không chính xác. Vui lòng thử lại!';
    isPinShaking.value = true;
    setTimeout(() => {
      isPinShaking.value = false;
      pinInput.value = '';
    }, 600);
  }
};

const handleNumpadPress = (digit: string) => {
  if (pinInput.value.length < 6) {
    pinInput.value += digit;
    sound.playClick();
    pinError.value = '';
    if (pinInput.value.length === 6) {
      checkPin();
    }
  }
};

const handleNumpadBackspace = () => {
  if (pinInput.value.length > 0) {
    pinInput.value = pinInput.value.slice(0, -1);
    sound.playClick();
    pinError.value = '';
  }
};

const handleNumpadClear = () => {
  pinInput.value = '';
  pinError.value = '';
  sound.playClick();
};

const lockWorkspace = () => {
  sessionStorage.removeItem('macatung_tasks_pin_auth');
  isPinUnlocked.value = false;
  pinInput.value = '';
  pinError.value = '';
  sound.playClick();
};

// Project CRUD Modal State
const showProjectModal = ref(false);
const projectModalMode = ref<'create' | 'edit'>('create');
const editingProjectId = ref<number | null>(null);
const isProjectSubmitting = ref(false);

const projectForm = ref({
  title: '',
  type: 'work' as 'work' | 'personal',
  color: '#00f5a0',
  description: '',
});

const presetColors = [
  '#00f5a0', // Mint
  '#3b82f6', // Blue
  '#a855f7', // Purple
  '#f59e0b', // Gold
  '#ec4899', // Pink
  '#06b6d4', // Cyan
  '#ef4444', // Red
];

// Drag & Drop State
const draggedTaskId = ref<number | null>(null);
const dragOverColumn = ref<string | null>(null);

const newTaskForm = ref({
  project_id: null as number | null,
  title: '',
  description: '',
  status: 'todo' as TaskItem['status'],
  priority: 'high' as TaskItem['priority'],
  category: 'backend',
  estimated_pomodoros: 2,
  due_date: new Date().toISOString().split('T')[0],
});

// Projects Grouping
const workProjects = computed(() => projectList.value.filter(p => p.type === 'work'));
const personalProjects = computed(() => projectList.value.filter(p => p.type === 'personal'));

const activeProjectObject = computed(() => {
  if (selectedProjectId.value === 'all' || selectedProjectId.value === 'unassigned') {
    return null;
  }
  return projectList.value.find(p => p.id === Number(selectedProjectId.value)) || null;
});

// Task count helper
const getProjectTaskCount = (projectId: number | 'all' | 'unassigned') => {
  if (projectId === 'all') return taskList.value.length;
  if (projectId === 'unassigned') return taskList.value.filter(t => !t.project_id).length;
  return taskList.value.filter(t => t.project_id === projectId).length;
};

// Smart Filtered Tasks
const filteredTasks = computed(() => {
  return taskList.value.filter(task => {
    // 1. Project filter
    if (selectedProjectId.value === 'unassigned') {
      if (task.project_id !== null) return false;
    } else if (selectedProjectId.value !== 'all') {
      if (task.project_id !== Number(selectedProjectId.value)) return false;
    }

    // 2. Category filter
    if (selectedCategory.value !== 'all' && task.category !== selectedCategory.value) {
      return false;
    }

    // 3. Search query filter
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase();
      const matchTitle = task.title.toLowerCase().includes(q);
      const matchDesc = task.description ? task.description.toLowerCase().includes(q) : false;
      const matchProject = task.project ? task.project.title.toLowerCase().includes(q) : false;
      if (!matchTitle && !matchDesc && !matchProject) return false;
    }

    // 4. Quick Smart Filters
    if (activeFilter.value === 'today') {
      const today = new Date().toISOString().split('T')[0];
      return task.due_date === today || task.status === 'in_progress';
    } else if (activeFilter.value === 'urgent') {
      return task.priority === 'urgent' || task.priority === 'high';
    } else if (activeFilter.value === 'in_progress') {
      return task.status === 'in_progress';
    } else if (activeFilter.value === 'uncompleted') {
      return task.status !== 'done';
    }

    return true;
  });
});

const todoTasks = computed(() => filteredTasks.value.filter(t => t.status === 'todo'));
const inProgressTasks = computed(() => filteredTasks.value.filter(t => t.status === 'in_progress'));
const reviewTasks = computed(() => filteredTasks.value.filter(t => t.status === 'review'));
const doneTasks = computed(() => filteredTasks.value.filter(t => t.status === 'done'));

// Clean Badges
const getPriorityBadge = (priority: string) => {
  switch (priority) {
    case 'urgent': return { label: 'Khẩn cấp', class: 'bg-red-500/10 text-red-400 border-red-500/20' };
    case 'high': return { label: 'Ưu tiên', class: 'bg-amber-500/10 text-amber-300 border-amber-500/20' };
    case 'medium': return { label: 'Bình thường', class: 'bg-slate-800 text-slate-300 border-slate-700' };
    case 'low': return { label: 'Thấp', class: 'bg-slate-900 text-slate-500 border-slate-800' };
    default: return { label: priority, class: 'bg-slate-800 text-slate-400 border-slate-700' };
  }
};

const getCategoryBadge = (category: string) => {
  switch (category) {
    case 'ai_agent': return { label: 'AI Agent', class: 'text-purple-400 bg-purple-500/10 border-purple-500/20' };
    case 'backend': return { label: 'Backend', class: 'text-blue-400 bg-blue-500/10 border-blue-500/20' };
    case 'frontend': return { label: 'Frontend', class: 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20' };
    case 'infra': return { label: 'Infra', class: 'text-amber-400 bg-amber-500/10 border-amber-500/20' };
    case 'mindful': return { label: 'Chánh Niệm', class: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' };
    default: return { label: category, class: 'text-slate-400 bg-slate-800 border-slate-700' };
  }
};

// ============================================================================
// PROJECT CRUD METHODS
// ============================================================================
const openCreateProjectModal = (type: 'work' | 'personal' = 'work') => {
  projectModalMode.value = 'create';
  editingProjectId.value = null;
  projectForm.value = {
    title: '',
    type,
    color: type === 'work' ? '#00f5a0' : '#ffd166',
    description: '',
  };
  showProjectModal.value = true;
  activeProjectMenuId.value = null;
  sound.playClick();
};

const openEditProjectModal = (project: ProjectItem) => {
  projectModalMode.value = 'edit';
  editingProjectId.value = project.id;
  projectForm.value = {
    title: project.title,
    type: project.type || 'work',
    color: project.color || '#00f5a0',
    description: project.description || '',
  };
  showProjectModal.value = true;
  activeProjectMenuId.value = null;
  sound.playClick();
};

const handleSaveProject = async () => {
  if (!projectForm.value.title.trim()) return;
  isProjectSubmitting.value = true;

  try {
    if (projectModalMode.value === 'create') {
      const res = await axios.post('/api/projects', projectForm.value);
      if (res.data.success) {
        const created: ProjectItem = res.data.data;
        projectList.value.push(created);
        selectedProjectId.value = created.id;
        sound.playSuccess();
        showProjectModal.value = false;
      }
    } else if (projectModalMode.value === 'edit' && editingProjectId.value) {
      const res = await axios.patch(`/api/projects/${editingProjectId.value}`, projectForm.value);
      if (res.data.success) {
        const updated: ProjectItem = res.data.data;
        const idx = projectList.value.findIndex(p => p.id === updated.id);
        if (idx !== -1) projectList.value[idx] = updated;

        // Also update project objects attached in taskList
        taskList.value.forEach(t => {
          if (t.project_id === updated.id) {
            t.project = updated;
          }
        });

        sound.playSuccess();
        showProjectModal.value = false;
      }
    }
  } catch (err) {
    console.error('Save project error:', err);
    alert('Không thể lưu dự án. Vui lòng thử lại!');
  } finally {
    isProjectSubmitting.value = false;
  }
};

const handleDeleteProject = async (project: ProjectItem) => {
  activeProjectMenuId.value = null;
  if (!confirm(`Bạn có chắc muốn xóa dự án "${project.title}"?\n(Toàn bộ các nhiệm vụ thuộc dự án này sẽ được giữ lại an toàn và chuyển vào mục 'Chung')`)) {
    return;
  }

  try {
    await axios.delete(`/api/projects/${project.id}`);
    
    // Remove from local project list
    projectList.value = projectList.value.filter(p => p.id !== project.id);

    // Safely update tasks that belonged to this project to unassigned
    taskList.value.forEach(t => {
      if (t.project_id === project.id) {
        t.project_id = null;
        t.project = null;
      }
    });

    if (selectedProjectId.value === project.id) {
      selectedProjectId.value = 'all';
    }

    sound.playClick();
  } catch (err) {
    console.error('Delete project failed:', err);
    alert('Lỗi khi xóa dự án!');
  }
};

// ============================================================================
// TASK DRAWER & DETAILS
// ============================================================================
const openTaskDrawer = (task: TaskItem) => {
  selectedTask.value = { ...task };
  sound.playClick();
};

const closeTaskDrawer = () => {
  if (selectedTask.value) {
    saveTaskDrawerChanges();
  }
  selectedTask.value = null;
};

const saveTaskDrawerChanges = async () => {
  if (!selectedTask.value) return;
  const task = selectedTask.value;

  if (task.project_id) {
    task.project = projectList.value.find(p => p.id === Number(task.project_id)) || null;
  } else {
    task.project = null;
  }

  const idx = taskList.value.findIndex(t => t.id === task.id);
  if (idx !== -1) {
    taskList.value[idx] = { ...task };
  }

  try {
    await axios.patch(`/api/tasks/${task.id}`, {
      project_id: task.project_id ? Number(task.project_id) : null,
      title: task.title,
      description: task.description,
      status: task.status,
      priority: task.priority,
      category: task.category,
      estimated_pomodoros: task.estimated_pomodoros,
      completed_pomodoros: task.completed_pomodoros,
      due_date: task.due_date,
      notes: JSON.stringify(task.subtasks || []),
    });
  } catch (err) {
    console.error('Failed to save task drawer:', err);
  }
};

const addSubtask = () => {
  if (!newSubtaskText.value.trim() || !selectedTask.value) return;
  if (!selectedTask.value.subtasks) selectedTask.value.subtasks = [];
  selectedTask.value.subtasks.push({
    id: Date.now().toString(),
    text: newSubtaskText.value.trim(),
    done: false,
  });
  newSubtaskText.value = '';
  saveTaskDrawerChanges();
  sound.playClick();
};

const toggleSubtask = (index: number) => {
  if (!selectedTask.value?.subtasks) return;
  selectedTask.value.subtasks[index].done = !selectedTask.value.subtasks[index].done;
  saveTaskDrawerChanges();
  sound.playClick();
};

const removeSubtask = (index: number) => {
  if (!selectedTask.value?.subtasks) return;
  selectedTask.value.subtasks.splice(index, 1);
  saveTaskDrawerChanges();
};

// ============================================================================
// TASK CREATION & STATUS
// ============================================================================
const handleQuickCreate = async () => {
  if (!quickInputText.value.trim()) return;
  const title = quickInputText.value.trim();
  quickInputText.value = '';
  
  let pId: number | null = null;
  if (typeof selectedProjectId.value === 'number') {
    pId = selectedProjectId.value;
  }

  try {
    const res = await axios.post('/api/tasks', {
      project_id: pId,
      title,
      priority: 'high',
      category: selectedCategory.value === 'all' ? 'backend' : selectedCategory.value,
      estimated_pomodoros: 2,
    });
    if (res.data.success) {
      const created = {
        ...res.data.data,
        subtasks: [],
        project: projectList.value.find(p => p.id === pId) || null,
      };
      taskList.value.unshift(created);
      sound.playSuccess();
    }
  } catch (err) {
    console.error('Quick create failed:', err);
  }
};

const openCreateTaskModal = () => {
  newTaskForm.value.project_id = typeof selectedProjectId.value === 'number' ? selectedProjectId.value : null;
  showCreateModal.value = true;
};

const handleCreateTask = async () => {
  if (!newTaskForm.value.title.trim()) return;
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/tasks', newTaskForm.value);
    if (res.data.success) {
      const created = {
        ...res.data.data,
        subtasks: [],
        project: projectList.value.find(p => p.id === newTaskForm.value.project_id) || null,
      };
      taskList.value.unshift(created);
      sound.playSuccess();
      showCreateModal.value = false;
      newTaskForm.value.title = '';
      newTaskForm.value.description = '';
    }
  } catch (err) {
    console.error('Error creating task:', err);
  } finally {
    isSubmitting.value = false;
  }
};

const updateTaskStatus = async (task: TaskItem, newStatus: TaskItem['status']) => {
  const oldStatus = task.status;
  task.status = newStatus;
  if (newStatus === 'done') {
    sound.playSuccess();
    task.completed_at = new Date().toISOString();
  } else {
    sound.playClick();
  }

  try {
    await axios.patch(`/api/tasks/${task.id}`, { status: newStatus });
  } catch (err) {
    task.status = oldStatus;
    console.error('Update status failed:', err);
  }
};

const incrementPomodoro = async (task: TaskItem) => {
  task.completed_pomodoros++;
  sound.playTalisman();
  try {
    await axios.patch(`/api/tasks/${task.id}`, { completed_pomodoros: task.completed_pomodoros });
  } catch (err) {
    task.completed_pomodoros--;
  }
};

const deleteTask = async (task: TaskItem) => {
  if (!confirm(`Bạn có chắc muốn xóa task "${task.title}"?`)) return;
  if (selectedTask.value?.id === task.id) {
    selectedTask.value = null;
  }
  try {
    await axios.delete(`/api/tasks/${task.id}`);
    taskList.value = taskList.value.filter(t => t.id !== task.id);
    sound.playClick();
  } catch (err) {
    console.error('Delete failed:', err);
  }
};

// Drag & Drop Handlers
const onDragStart = (e: DragEvent, task: TaskItem) => {
  draggedTaskId.value = task.id;
  if (e.dataTransfer) {
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', task.id.toString());
  }
};

const onDragOver = (e: DragEvent, columnStatus: string) => {
  e.preventDefault();
  dragOverColumn.value = columnStatus;
  if (e.dataTransfer) {
    e.dataTransfer.dropEffect = 'move';
  }
};

const onDragLeave = (_e: DragEvent) => {
  dragOverColumn.value = null;
};

const onDrop = async (e: DragEvent, targetStatus: TaskItem['status']) => {
  e.preventDefault();
  dragOverColumn.value = null;
  if (!draggedTaskId.value) return;

  const task = taskList.value.find(t => t.id === draggedTaskId.value);
  if (task && task.status !== targetStatus) {
    await updateTaskStatus(task, targetStatus);
  }
  draggedTaskId.value = null;
};

// Global Keyboard Shortcuts
const handleGlobalKey = (e: KeyboardEvent) => {
  if (!isPinUnlocked.value) {
    if (e.key >= '0' && e.key <= '9') {
      e.preventDefault();
      handleNumpadPress(e.key);
    } else if (e.key === 'Backspace') {
      e.preventDefault();
      handleNumpadBackspace();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      checkPin();
    }
    return;
  }

  if (['INPUT', 'TEXTAREA', 'SELECT'].includes((e.target as HTMLElement)?.tagName)) {
    if (e.key === 'Escape') {
      (e.target as HTMLElement).blur();
      closeTaskDrawer();
      showProjectModal.value = false;
      activeProjectMenuId.value = null;
    }
    return;
  }

  if (e.key === 'n' || e.key === 'N') {
    e.preventDefault();
    quickInputRef.value?.focus();
  } else if (e.key === 'k' || e.key === 'K') {
    e.preventDefault();
    viewMode.value = viewMode.value === 'kanban' ? 'list' : 'kanban';
  } else if (e.key === '/') {
    e.preventDefault();
    searchInputRef.value?.focus();
  } else if (e.key === 'Escape') {
    closeTaskDrawer();
    showCreateModal.value = false;
    showDispatchModal.value = false;
    showReviewModal.value = false;
    showProjectModal.value = false;
    activeProjectMenuId.value = null;
  }
};

const closeAllMenus = () => {
  activeProjectMenuId.value = null;
};

onMounted(() => {
  const saved = sessionStorage.getItem('macatung_tasks_pin_auth');
  if (saved === '301095') {
    isPinUnlocked.value = true;
  }
  window.addEventListener('keydown', handleGlobalKey);
  window.addEventListener('click', closeAllMenus);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleGlobalKey);
  window.removeEventListener('click', closeAllMenus);
});
</script>

<template>
  <Head title="Quản Lý Task & Dự Án | Ma Cà Tưng • Tasks Hub" />

  <div class="min-h-screen bg-[#080c14] text-slate-100 font-sans selection:bg-emerald-500/20 selection:text-emerald-300 flex flex-col">
    <!-- Navbar Header -->
    <header class="border-b border-slate-800/80 bg-slate-950/90 backdrop-blur-xl sticky top-0 z-40">
      <div class="w-full px-4 sm:px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <!-- Toggle Left Sidebar Button -->
          <button
            @click="isSidebarOpen = !isSidebarOpen"
            class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
            title="Đóng / Mở danh sách dự án"
          >
            <span class="text-xs">{{ isSidebarOpen ? '◀' : '▶' }}</span>
          </button>

          <a href="/" class="flex items-center gap-2.5 group">
            <MiniMascotLogo size="md" :enable-sound="true" />
            <div>
              <span class="font-display font-bold text-white text-base sm:text-lg group-hover:text-emerald-400 transition-colors">
                Ma Cà Tưng
              </span>
              <span class="text-[10px] font-mono text-slate-400 block -mt-1 font-semibold tracking-wider">
                TASKS & PROJECTS HUB
              </span>
            </div>
          </a>

          <div class="hidden md:flex items-center gap-1.5 ml-4 px-3 py-1 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-400">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Đồng bộ Desktop Mascot</span>
          </div>
        </div>

        <!-- Action Controls -->
        <div class="flex items-center gap-2">
          <!-- Morning Dispatch -->
          <button
            @click="showDispatchModal = true"
            class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-800 text-xs font-medium transition-all flex items-center gap-1.5 cursor-pointer"
            title="Giao việc cho Desktop Mascot"
          >
            <span>🌞</span>
            <span class="hidden sm:inline">Giao Việc</span>
          </button>

          <!-- Evening Review -->
          <button
            @click="showReviewModal = true"
            class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-800 text-xs font-medium transition-all flex items-center gap-1.5 cursor-pointer"
            title="Review cuối ngày"
          >
            <span>🌙</span>
            <span class="hidden sm:inline">Review</span>
          </button>

          <!-- Lock Workspace -->
          <button
            @click="lockWorkspace"
            class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-red-500/10 hover:border-red-500/30 text-slate-400 hover:text-red-400 border border-slate-800 text-xs font-medium transition-all flex items-center gap-1 cursor-pointer"
            title="Khóa bảo mật Workspace (Yêu cầu mã PIN 301095)"
          >
            <span>🔒</span>
            <span class="hidden md:inline text-[11px]">Khóa</span>
          </button>

          <!-- New Task CTA -->
          <button
            @click="openCreateTaskModal"
            class="px-3.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md transition-all flex items-center gap-1 cursor-pointer"
          >
            <span>+</span>
            <span>Tạo Task</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content Container with Left Sidebar & Kanban Area -->
    <div class="flex-1 flex overflow-hidden">
      <!-- ========================================================================= -->
      <!-- 1. COLLAPSIBLE LEFT SIDEBAR (PROJECTS MANAGEMENT: WORK & PERSONAL)         -->
      <!-- ========================================================================= -->
      <aside
        v-if="isSidebarOpen"
        class="w-64 sm:w-72 bg-slate-950 border-r border-slate-800/80 flex flex-col justify-between shrink-0 h-[calc(100vh-4rem)] select-none animate-slideInLeft"
      >
        <!-- Sidebar Navigation Area -->
        <div class="p-3.5 space-y-4 overflow-y-auto max-h-[calc(100vh-8.5rem)] pr-2">
          <!-- Top Global Selectors -->
          <div class="space-y-1">
            <button
              @click="selectedProjectId = 'all'"
              :class="[
                'w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-all cursor-pointer border text-left',
                selectedProjectId === 'all'
                  ? 'bg-slate-900 text-white border-slate-700 shadow-sm'
                  : 'text-slate-400 border-transparent hover:text-white hover:bg-slate-900/60'
              ]"
            >
              <span class="flex items-center gap-2">
                <span>📁</span>
                <span>Tất Cả Nhiệm Vụ</span>
              </span>
              <span class="font-mono text-[10px] text-slate-500 font-bold">{{ getProjectTaskCount('all') }}</span>
            </button>

            <button
              @click="selectedProjectId = 'unassigned'"
              :class="[
                'w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-all cursor-pointer border text-left',
                selectedProjectId === 'unassigned'
                  ? 'bg-slate-900 text-white border-slate-700 shadow-sm'
                  : 'text-slate-400 border-transparent hover:text-white hover:bg-slate-900/60'
              ]"
            >
              <span class="flex items-center gap-2">
                <span>📦</span>
                <span>Chung (Chưa phân dự án)</span>
              </span>
              <span class="font-mono text-[10px] text-slate-500 font-bold">{{ getProjectTaskCount('unassigned') }}</span>
            </button>
          </div>

          <!-- GROUP 1: WORK PROJECTS -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between px-2 text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
              <span class="flex items-center gap-1.5 text-blue-400">
                <span>💼</span>
                <span>CÔNG VIỆC (WORK)</span>
              </span>
              <button
                @click="openCreateProjectModal('work')"
                class="hover:text-emerald-400 p-0.5 rounded cursor-pointer text-xs"
                title="Tạo dự án công việc mới"
              >
                +
              </button>
            </div>

            <div class="space-y-0.5">
              <div
                v-for="proj in workProjects"
                :key="proj.id"
                :class="[
                  'relative group',
                  activeProjectMenuId === proj.id ? 'z-50' : 'z-10'
                ]"
              >
                <button
                  @click="selectedProjectId = proj.id"
                  :class="[
                    'w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all cursor-pointer border text-left',
                    selectedProjectId === proj.id
                      ? 'bg-slate-900 text-white border-blue-500/40 shadow-sm font-semibold'
                      : 'text-slate-400 border-transparent hover:text-slate-200 hover:bg-slate-900/50'
                  ]"
                >
                  <div class="flex items-center gap-2 min-w-0 pr-1">
                    <span class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: proj.color || '#3b82f6' }"></span>
                    <span class="truncate">{{ proj.title }}</span>
                  </div>

                  <span class="font-mono text-[10px] text-slate-500 font-bold shrink-0">
                    {{ getProjectTaskCount(proj.id) }}
                  </span>
                </button>

                <!-- 3-Dot Action Button -->
                <div
                  :class="[
                    'absolute right-1.5 top-1/2 -translate-y-1/2 transition-opacity z-50',
                    activeProjectMenuId === proj.id ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'
                  ]"
                >
                  <button
                    @click.stop="activeProjectMenuId = activeProjectMenuId === proj.id ? null : proj.id"
                    class="p-1 rounded-md bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs cursor-pointer border border-slate-700/60"
                    title="Tùy chọn dự án"
                  >
                    •••
                  </button>

                  <!-- Dropdown Menu -->
                  <div
                    v-if="activeProjectMenuId === proj.id"
                    class="absolute right-0 top-full mt-1.5 w-36 rounded-xl bg-[#0c111c] border border-slate-700 shadow-2xl p-1.5 z-50 text-xs font-medium backdrop-blur-2xl"
                    @click.stop
                  >
                    <button
                      @click.stop="openEditProjectModal(proj)"
                      class="w-full px-2.5 py-1.5 rounded-lg text-left text-slate-200 hover:bg-slate-800 hover:text-white flex items-center gap-2 cursor-pointer transition-colors"
                    >
                      <span>✏️</span>
                      <span>Chỉnh Sửa</span>
                    </button>
                    <button
                      @click.stop="handleDeleteProject(proj)"
                      class="w-full px-2.5 py-1.5 rounded-lg text-left text-red-400 hover:bg-red-500/10 hover:text-red-300 flex items-center gap-2 cursor-pointer transition-colors"
                    >
                      <span>🗑️</span>
                      <span>Xóa Dự Án</span>
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="workProjects.length === 0" class="px-3 py-2 text-[11px] text-slate-600 italic">
                Chưa có dự án công việc
              </div>
            </div>
          </div>

          <!-- GROUP 2: PERSONAL PROJECTS -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between px-2 text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
              <span class="flex items-center gap-1.5 text-amber-400">
                <span>👤</span>
                <span>CÁ NHÂN (PERSONAL)</span>
              </span>
              <button
                @click="openCreateProjectModal('personal')"
                class="hover:text-amber-400 p-0.5 rounded cursor-pointer text-xs"
                title="Tạo dự án cá nhân mới"
              >
                +
              </button>
            </div>

            <div class="space-y-0.5">
              <div
                v-for="proj in personalProjects"
                :key="proj.id"
                :class="[
                  'relative group',
                  activeProjectMenuId === proj.id ? 'z-50' : 'z-10'
                ]"
              >
                <button
                  @click="selectedProjectId = proj.id"
                  :class="[
                    'w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs transition-all cursor-pointer border text-left',
                    selectedProjectId === proj.id
                      ? 'bg-slate-900 text-white border-amber-500/40 shadow-sm font-semibold'
                      : 'text-slate-400 border-transparent hover:text-slate-200 hover:bg-slate-900/50'
                  ]"
                >
                  <div class="flex items-center gap-2 min-w-0 pr-1">
                    <span class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: proj.color || '#ffd166' }"></span>
                    <span class="truncate">{{ proj.title }}</span>
                  </div>

                  <span class="font-mono text-[10px] text-slate-500 font-bold shrink-0">
                    {{ getProjectTaskCount(proj.id) }}
                  </span>
                </button>

                <!-- 3-Dot Action Button -->
                <div
                  :class="[
                    'absolute right-1.5 top-1/2 -translate-y-1/2 transition-opacity z-50',
                    activeProjectMenuId === proj.id ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'
                  ]"
                >
                  <button
                    @click.stop="activeProjectMenuId = activeProjectMenuId === proj.id ? null : proj.id"
                    class="p-1 rounded-md bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs cursor-pointer border border-slate-700/60"
                    title="Tùy chọn dự án"
                  >
                    •••
                  </button>

                  <!-- Dropdown Menu -->
                  <div
                    v-if="activeProjectMenuId === proj.id"
                    class="absolute right-0 top-full mt-1.5 w-36 rounded-xl bg-[#0c111c] border border-slate-700 shadow-2xl p-1.5 z-50 text-xs font-medium backdrop-blur-2xl"
                    @click.stop
                  >
                    <button
                      @click.stop="openEditProjectModal(proj)"
                      class="w-full px-2.5 py-1.5 rounded-lg text-left text-slate-200 hover:bg-slate-800 hover:text-white flex items-center gap-2 cursor-pointer transition-colors"
                    >
                      <span>✏️</span>
                      <span>Chỉnh Sửa</span>
                    </button>
                    <button
                      @click.stop="handleDeleteProject(proj)"
                      class="w-full px-2.5 py-1.5 rounded-lg text-left text-red-400 hover:bg-red-500/10 hover:text-red-300 flex items-center gap-2 cursor-pointer transition-colors"
                    >
                      <span>🗑️</span>
                      <span>Xóa Dự Án</span>
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="personalProjects.length === 0" class="px-3 py-2 text-[11px] text-slate-600 italic">
                Chưa có dự án cá nhân
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar Bottom Footer CTA -->
        <div class="p-3 border-t border-slate-800/80 bg-slate-950">
          <button
            @click="openCreateProjectModal('work')"
            class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 text-slate-200 font-medium text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm"
          >
            <span>+</span>
            <span>Thêm Dự Án Mới</span>
          </button>
        </div>
      </aside>

      <!-- ========================================================================= -->
      <!-- 2. MAIN KANBAN & TASKS WORKSPACE (RIGHT AREA)                             -->
      <!-- ========================================================================= -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
        <!-- Active Project Header Banner -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-800/70">
          <div class="flex items-center gap-2.5">
            <div
              v-if="activeProjectObject"
              class="w-3 h-3 rounded-full"
              :style="{ backgroundColor: activeProjectObject.color || '#00f5a0' }"
            ></div>
            <div>
              <h2 class="text-base sm:text-lg font-display font-bold text-white flex items-center gap-2">
                <span>{{ activeProjectObject ? activeProjectObject.title : selectedProjectId === 'unassigned' ? 'Nhiệm Vụ Chưa Phân Dự Án' : 'Tất Cả Nhiệm Vụ' }}</span>
                <span
                  v-if="activeProjectObject"
                  :class="[
                    'text-[10px] font-mono px-2 py-0.5 rounded border',
                    activeProjectObject.type === 'work' ? 'text-blue-400 bg-blue-500/10 border-blue-500/20' : 'text-amber-300 bg-amber-500/10 border-amber-500/20'
                  ]"
                >
                  {{ activeProjectObject.type === 'work' ? '💼 Công Việc' : '👤 Cá Nhân' }}
                </span>
              </h2>
              <p v-if="activeProjectObject?.description" class="text-xs text-slate-400 mt-0.5">
                {{ activeProjectObject.description }}
              </p>
            </div>
          </div>

          <!-- Project Actions (If specific project selected) -->
          <div v-if="activeProjectObject" class="flex items-center gap-2">
            <button
              @click="openEditProjectModal(activeProjectObject)"
              class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 hover:border-slate-700 text-xs text-slate-300 hover:text-white cursor-pointer"
            >
              ✏️ Sửa Dự Án
            </button>
            <button
              @click="handleDeleteProject(activeProjectObject)"
              class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 hover:border-red-500/40 text-xs text-red-400 hover:text-red-300 cursor-pointer"
            >
              🗑️ Xóa
            </button>
          </div>
        </div>

        <!-- 2.1. INLINE QUICK ADD & SEARCH (MINIMALIST) -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <!-- 1-Click Quick Add Input -->
          <div class="md:col-span-2 relative flex items-center">
            <input
              ref="quickInputRef"
              v-model="quickInputText"
              @keyup.enter="handleQuickCreate"
              type="text"
              :placeholder="activeProjectObject ? `+ Thêm task cho '${activeProjectObject.title}'... (Enter hoặc 'N')` : '+ Thêm nhanh nhiệm vụ... (Nhấn Enter hoặc phím \'N\')'"
              class="w-full pl-4 pr-24 py-2.5 rounded-xl bg-slate-900/90 border border-slate-800 focus:border-slate-600 text-xs sm:text-sm text-white placeholder-slate-500 outline-none font-sans transition-all"
            />
            <button
              @click="handleQuickCreate"
              :disabled="!quickInputText.trim()"
              class="absolute right-2 px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium text-xs transition-all cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed"
            >
              Thêm ⏎
            </button>
          </div>

          <!-- Realtime Search Input -->
          <div class="relative flex items-center">
            <span class="absolute left-3 text-slate-500 text-xs">🔍</span>
            <input
              ref="searchInputRef"
              v-model="searchQuery"
              type="text"
              placeholder="Tìm kiếm task... (Phím '/')"
              class="w-full pl-8 pr-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-800 focus:border-slate-600 text-xs sm:text-sm text-white placeholder-slate-500 outline-none font-sans transition-all"
            />
            <button
              v-if="searchQuery"
              @click="searchQuery = ''"
              class="absolute right-3 text-slate-500 hover:text-white text-xs"
            >
              ✕
            </button>
          </div>
        </section>

        <!-- 2.2. MINIMALIST FILTER TABS & VIEW SWITCHER -->
        <section class="flex flex-wrap items-center justify-between gap-3">
          <!-- Status Filter Tabs -->
          <div class="flex flex-wrap items-center gap-1.5">
            <button
              @click="activeFilter = 'all'"
              :class="[
                'px-3 py-1 rounded-lg text-xs font-medium transition-all cursor-pointer',
                activeFilter === 'all' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200'
              ]"
            >
              Tất Cả
            </button>
            <button
              @click="activeFilter = 'today'"
              :class="[
                'px-3 py-1 rounded-lg text-xs font-medium transition-all cursor-pointer',
                activeFilter === 'today' ? 'bg-slate-800 text-amber-300' : 'text-slate-400 hover:text-slate-200'
              ]"
            >
              Hôm Nay
            </button>
            <button
              @click="activeFilter = 'in_progress'"
              :class="[
                'px-3 py-1 rounded-lg text-xs font-medium transition-all cursor-pointer',
                activeFilter === 'in_progress' ? 'bg-slate-800 text-amber-300' : 'text-slate-400 hover:text-slate-200'
              ]"
            >
              Đang Làm ({{ inProgressTasks.length }})
            </button>
            <button
              @click="activeFilter = 'urgent'"
              :class="[
                'px-3 py-1 rounded-lg text-xs font-medium transition-all cursor-pointer',
                activeFilter === 'urgent' ? 'bg-slate-800 text-red-400' : 'text-slate-400 hover:text-slate-200'
              ]"
            >
              Khẩn Cấp
            </button>
            <button
              @click="activeFilter = 'uncompleted'"
              :class="[
                'px-3 py-1 rounded-lg text-xs font-medium transition-all cursor-pointer',
                activeFilter === 'uncompleted' ? 'bg-slate-800 text-cyan-300' : 'text-slate-400 hover:text-slate-200'
              ]"
            >
              Chưa Xong
            </button>
          </div>

          <!-- View Switcher (Kanban / List) -->
          <div class="flex items-center p-0.5 rounded-lg bg-slate-900 border border-slate-800">
            <button
              @click="viewMode = 'kanban'"
              :class="[
                'px-2.5 py-1 rounded-md text-xs font-medium transition-all cursor-pointer',
                viewMode === 'kanban' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white'
              ]"
            >
              Kanban (K)
            </button>
            <button
              @click="viewMode = 'list'"
              :class="[
                'px-2.5 py-1 rounded-md text-xs font-medium transition-all cursor-pointer',
                viewMode === 'list' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white'
              ]"
            >
              Danh Sách
            </button>
          </div>
        </section>

        <!-- 2.3. MINIMALIST KANBAN BOARD (CLEAN SLATE & DRAG DROP) -->
        <section v-if="viewMode === 'kanban'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- COLUMN 1: TODO -->
          <div
            @dragover="onDragOver($event, 'todo')"
            @dragleave="onDragLeave($event)"
            @drop="onDrop($event, 'todo')"
            :class="[
              'p-3.5 rounded-2xl bg-slate-950 border transition-all flex flex-col min-h-[500px]',
              dragOverColumn === 'todo' ? 'border-slate-500 bg-slate-900/60' : 'border-slate-800/80'
            ]"
          >
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-800/80">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                <h3 class="font-bold text-xs text-slate-300 uppercase tracking-wider">Cần Làm</h3>
              </div>
              <span class="text-[11px] font-mono text-slate-500 font-bold">{{ todoTasks.length }}</span>
            </div>

            <div class="space-y-2.5 flex-1 overflow-y-auto max-h-[650px] pr-0.5">
              <div
                v-for="task in todoTasks"
                :key="task.id"
                draggable="true"
                @dragstart="onDragStart($event, task)"
                @click="openTaskDrawer(task)"
                class="p-3.5 rounded-xl bg-slate-900 border border-slate-800/80 hover:border-slate-600 transition-all shadow-sm group cursor-pointer hover:-translate-y-0.5"
              >
                <div class="flex items-center justify-between gap-2 mb-1.5">
                  <span class="text-[10px] font-mono text-slate-500">#{{ task.id }}</span>
                  <span :class="['text-[10px] font-medium px-2 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                    {{ getPriorityBadge(task.priority).label }}
                  </span>
                </div>

                <!-- Project Badge -->
                <div v-if="task.project" class="text-[10px] font-mono text-slate-400 mb-1 truncate flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: task.project.color || '#3b82f6' }"></span>
                  <span>{{ task.project.title }}</span>
                </div>

                <h4 class="font-semibold text-xs text-slate-100 leading-snug mb-1 group-hover:text-white">{{ task.title }}</h4>
                <p v-if="task.description" class="text-[11px] text-slate-400 mb-2 line-clamp-2">{{ task.description }}</p>

                <div class="flex items-center justify-between pt-2 border-t border-slate-800/60 text-[11px] text-slate-500">
                  <span :class="['font-mono text-[10px] px-1.5 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                    {{ getCategoryBadge(task.category).label }}
                  </span>
                  <span class="font-mono text-[10px]">🍅 {{ task.completed_pomodoros }}/{{ task.estimated_pomodoros }}</span>
                </div>
              </div>

              <div v-if="todoTasks.length === 0" class="h-24 border border-dashed border-slate-800 rounded-xl flex items-center justify-center text-[11px] text-slate-600">
                Kéo thả vào đây
              </div>
            </div>
          </div>

          <!-- COLUMN 2: IN PROGRESS -->
          <div
            @dragover="onDragOver($event, 'in_progress')"
            @dragleave="onDragLeave($event)"
            @drop="onDrop($event, 'in_progress')"
            :class="[
              'p-3.5 rounded-2xl bg-slate-950 border transition-all flex flex-col min-h-[500px]',
              dragOverColumn === 'in_progress' ? 'border-amber-500/60 bg-slate-900/60' : 'border-slate-800/80'
            ]"
          >
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-800/80">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                <h3 class="font-bold text-xs text-slate-300 uppercase tracking-wider">Đang Thực Thi</h3>
              </div>
              <span class="text-[11px] font-mono text-amber-400 font-bold">{{ inProgressTasks.length }}</span>
            </div>

            <div class="space-y-2.5 flex-1 overflow-y-auto max-h-[650px] pr-0.5">
              <div
                v-for="task in inProgressTasks"
                :key="task.id"
                draggable="true"
                @dragstart="onDragStart($event, task)"
                @click="openTaskDrawer(task)"
                class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-600 transition-all shadow-sm group cursor-pointer hover:-translate-y-0.5"
              >
                <div class="flex items-center justify-between gap-2 mb-1.5">
                  <span class="text-[10px] font-mono text-slate-500">#{{ task.id }}</span>
                  <span :class="['text-[10px] font-medium px-2 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                    {{ getPriorityBadge(task.priority).label }}
                  </span>
                </div>

                <!-- Project Badge -->
                <div v-if="task.project" class="text-[10px] font-mono text-slate-400 mb-1 truncate flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: task.project.color || '#3b82f6' }"></span>
                  <span>{{ task.project.title }}</span>
                </div>

                <h4 class="font-semibold text-xs text-slate-100 leading-snug mb-1 group-hover:text-white">{{ task.title }}</h4>
                <p v-if="task.description" class="text-[11px] text-slate-400 mb-2 line-clamp-2">{{ task.description }}</p>

                <div class="flex items-center justify-between pt-2 border-t border-slate-800/60 text-[11px] text-slate-500">
                  <span :class="['font-mono text-[10px] px-1.5 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                    {{ getCategoryBadge(task.category).label }}
                  </span>
                  <button
                    @click.stop="incrementPomodoro(task)"
                    class="font-mono text-[10px] text-amber-400 hover:text-amber-300"
                    title="+1 Pomodoro"
                  >
                    🍅 {{ task.completed_pomodoros }}/{{ task.estimated_pomodoros }} (+)
                  </button>
                </div>
              </div>

              <div v-if="inProgressTasks.length === 0" class="h-24 border border-dashed border-slate-800 rounded-xl flex items-center justify-center text-[11px] text-slate-600">
                Kéo thả vào đây
              </div>
            </div>
          </div>

          <!-- COLUMN 3: REVIEW -->
          <div
            @dragover="onDragOver($event, 'review')"
            @dragleave="onDragLeave($event)"
            @drop="onDrop($event, 'review')"
            :class="[
              'p-3.5 rounded-2xl bg-slate-950 border transition-all flex flex-col min-h-[500px]',
              dragOverColumn === 'review' ? 'border-purple-500/60 bg-slate-900/60' : 'border-slate-800/80'
            ]"
          >
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-800/80">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                <h3 class="font-bold text-xs text-slate-300 uppercase tracking-wider">Kiểm Thử (Review)</h3>
              </div>
              <span class="text-[11px] font-mono text-purple-400 font-bold">{{ reviewTasks.length }}</span>
            </div>

            <div class="space-y-2.5 flex-1 overflow-y-auto max-h-[650px] pr-0.5">
              <div
                v-for="task in reviewTasks"
                :key="task.id"
                draggable="true"
                @dragstart="onDragStart($event, task)"
                @click="openTaskDrawer(task)"
                class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-600 transition-all shadow-sm group cursor-pointer hover:-translate-y-0.5"
              >
                <div class="flex items-center justify-between gap-2 mb-1.5">
                  <span class="text-[10px] font-mono text-slate-500">#{{ task.id }}</span>
                  <span :class="['text-[10px] font-medium px-2 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                    {{ getPriorityBadge(task.priority).label }}
                  </span>
                </div>

                <!-- Project Badge -->
                <div v-if="task.project" class="text-[10px] font-mono text-slate-400 mb-1 truncate flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: task.project.color || '#3b82f6' }"></span>
                  <span>{{ task.project.title }}</span>
                </div>

                <h4 class="font-semibold text-xs text-slate-100 leading-snug mb-1 group-hover:text-white">{{ task.title }}</h4>
                <p v-if="task.description" class="text-[11px] text-slate-400 mb-2 line-clamp-2">{{ task.description }}</p>

                <div class="flex items-center justify-between pt-2 border-t border-slate-800/60 text-[11px] text-slate-500">
                  <span :class="['font-mono text-[10px] px-1.5 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                    {{ getCategoryBadge(task.category).label }}
                  </span>
                  <button
                    @click.stop="updateTaskStatus(task, 'done')"
                    class="text-[10px] font-medium px-2 py-0.5 rounded bg-emerald-500/15 hover:bg-emerald-500 text-emerald-300 hover:text-slate-950 transition-colors"
                  >
                    Xong ✓
                  </button>
                </div>
              </div>

              <div v-if="reviewTasks.length === 0" class="h-24 border border-dashed border-slate-800 rounded-xl flex items-center justify-center text-[11px] text-slate-600">
                Kéo thả vào đây
              </div>
            </div>
          </div>

          <!-- COLUMN 4: DONE -->
          <div
            @dragover="onDragOver($event, 'done')"
            @dragleave="onDragLeave($event)"
            @drop="onDrop($event, 'done')"
            :class="[
              'p-3.5 rounded-2xl bg-slate-950 border transition-all flex flex-col min-h-[500px]',
              dragOverColumn === 'done' ? 'border-emerald-500/60 bg-slate-900/60' : 'border-slate-800/80'
            ]"
          >
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-800/80">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <h3 class="font-bold text-xs text-slate-300 uppercase tracking-wider">Đã Hoàn Tất</h3>
              </div>
              <span class="text-[11px] font-mono text-emerald-400 font-bold">{{ doneTasks.length }}</span>
            </div>

            <div class="space-y-2.5 flex-1 overflow-y-auto max-h-[650px] pr-0.5">
              <div
                v-for="task in doneTasks"
                :key="task.id"
                draggable="true"
                @dragstart="onDragStart($event, task)"
                @click="openTaskDrawer(task)"
                class="p-3.5 rounded-xl bg-slate-900/60 border border-slate-800/80 hover:border-slate-600 opacity-70 hover:opacity-100 transition-all shadow-sm group cursor-pointer hover:-translate-y-0.5"
              >
                <div class="flex items-center justify-between gap-2 mb-1">
                  <span class="text-[10px] font-mono text-slate-500">#{{ task.id }}</span>
                  <span class="text-[9px] font-mono text-emerald-400">Hoàn thành ✓</span>
                </div>

                <!-- Project Badge -->
                <div v-if="task.project" class="text-[10px] font-mono text-slate-500 mb-1 truncate flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: task.project.color || '#3b82f6' }"></span>
                  <span>{{ task.project.title }}</span>
                </div>

                <h4 class="font-semibold text-xs text-slate-300 line-through leading-snug mb-1">{{ task.title }}</h4>

                <div class="flex items-center justify-between pt-1.5 border-t border-slate-800/60 text-[10px] text-slate-500">
                  <span>{{ task.completed_at ? new Date(task.completed_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : 'Hôm nay' }}</span>
                  <span class="font-mono">🍅 {{ task.completed_pomodoros }}</span>
                </div>
              </div>

              <div v-if="doneTasks.length === 0" class="h-24 border border-dashed border-slate-800 rounded-xl flex items-center justify-center text-[11px] text-slate-600">
                Kéo thả vào đây
              </div>
            </div>
          </div>
        </section>

        <!-- 2.4. LIST VIEW -->
        <section v-else class="rounded-2xl bg-slate-950 border border-slate-800 p-4 space-y-2">
          <div
            v-for="task in filteredTasks"
            :key="task.id"
            @click="openTaskDrawer(task)"
            class="p-3 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-600 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 cursor-pointer"
          >
            <div class="flex items-start gap-3 flex-1">
              <input
                type="checkbox"
                :checked="task.status === 'done'"
                @click.stop
                @change="updateTaskStatus(task, task.status === 'done' ? 'todo' : 'done')"
                class="accent-emerald-500 w-4 h-4 mt-0.5 cursor-pointer"
              />
              <div>
                <div class="flex items-center gap-2 mb-1">
                  <span class="text-[10px] font-mono text-slate-500">#{{ task.id }}</span>
                  <span v-if="task.project" class="text-[10px] font-mono text-slate-300 bg-slate-950 px-2 py-0.5 rounded border border-slate-800 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full" :style="{ backgroundColor: task.project.color || '#3b82f6' }"></span>
                    <span>{{ task.project.title }}</span>
                  </span>
                  <span :class="['text-[10px] font-medium px-2 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                    {{ getPriorityBadge(task.priority).label }}
                  </span>
                  <span :class="['text-[10px] font-mono px-2 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                    {{ getCategoryBadge(task.category).label }}
                  </span>
                </div>
                <h4 :class="['font-semibold text-xs sm:text-sm text-white', { 'line-through text-slate-500': task.status === 'done' }]">
                  {{ task.title }}
                </h4>
              </div>
            </div>

            <div class="flex items-center gap-3 shrink-0 self-end sm:self-center text-xs">
              <span class="font-mono text-slate-400">🍅 {{ task.completed_pomodoros }}/{{ task.estimated_pomodoros }}</span>
              <select
                :value="task.status"
                @click.stop
                @change="updateTaskStatus(task, ($event.target as HTMLSelectElement).value as any)"
                class="px-2 py-1 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-200 outline-none cursor-pointer"
              >
                <option value="todo">Cần Làm</option>
                <option value="in_progress">Đang Làm</option>
                <option value="review">Kiểm Thử</option>
                <option value="done">Hoàn Tất</option>
              </select>
            </div>
          </div>
        </section>
      </main>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. PROJECT CREATE / EDIT MODAL                                            -->
    <!-- ========================================================================= -->
    <div
      v-if="showProjectModal"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-fadeIn"
      @click.self="showProjectModal = false"
    >
      <div class="w-full max-w-md rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-4 font-sans">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <h3 class="font-display font-bold text-base text-white flex items-center gap-2">
            <span>{{ projectModalMode === 'create' ? '✨ Tạo Dự Án Mới' : '✏️ Chỉnh Sửa Dự Án' }}</span>
          </h3>
          <button @click="showProjectModal = false" class="text-slate-400 hover:text-white p-1">✕</button>
        </div>

        <form @submit.prevent="handleSaveProject" class="space-y-4 text-xs">
          <!-- Title -->
          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1">Tên Dự Án:</label>
            <input
              v-model="projectForm.title"
              type="text"
              required
              placeholder="VD: Macatung AI Agents, Học Tiếng Anh..."
              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 focus:border-slate-600 text-xs sm:text-sm text-white outline-none"
            />
          </div>

          <!-- Type (Work vs Personal) -->
          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">Phân Loại Dự Án:</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                type="button"
                @click="projectForm.type = 'work'"
                :class="[
                  'py-2 px-3 rounded-xl border text-xs font-semibold flex items-center justify-center gap-2 cursor-pointer transition-all',
                  projectForm.type === 'work'
                    ? 'bg-blue-500/20 text-blue-300 border-blue-500 shadow-sm'
                    : 'bg-slate-950 text-slate-400 border-slate-800 hover:bg-slate-900'
                ]"
              >
                <span>💼</span>
                <span>Công Việc (Work)</span>
              </button>

              <button
                type="button"
                @click="projectForm.type = 'personal'"
                :class="[
                  'py-2 px-3 rounded-xl border text-xs font-semibold flex items-center justify-center gap-2 cursor-pointer transition-all',
                  projectForm.type === 'personal'
                    ? 'bg-amber-500/20 text-amber-300 border-amber-500 shadow-sm'
                    : 'bg-slate-950 text-slate-400 border-slate-800 hover:bg-slate-900'
                ]"
              >
                <span>👤</span>
                <span>Cá Nhân (Personal)</span>
              </button>
            </div>
          </div>

          <!-- Color Preset Picker -->
          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1.5">Màu Sắc Nhận Diện:</label>
            <div class="flex items-center gap-2">
              <button
                v-for="c in presetColors"
                :key="c"
                type="button"
                @click="projectForm.color = c"
                class="w-7 h-7 rounded-full transition-transform cursor-pointer flex items-center justify-center border border-white/10 hover:scale-110"
                :style="{ backgroundColor: c }"
              >
                <span v-if="projectForm.color === c" class="text-slate-950 text-[10px] font-bold">✓</span>
              </button>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1">Mô Tả Ngắn (Tùy chọn):</label>
            <textarea
              v-model="projectForm.description"
              rows="2"
              placeholder="Mục tiêu hoặc tóm tắt dự án..."
              class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 focus:border-slate-600 text-xs text-white outline-none resize-none font-sans"
            ></textarea>
          </div>

          <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
            <button
              type="button"
              @click="showProjectModal = false"
              class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-medium hover:bg-slate-700 cursor-pointer"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="isProjectSubmitting"
              class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md transition-all cursor-pointer disabled:opacity-50"
            >
              {{ isProjectSubmitting ? 'Đang lưu...' : (projectModalMode === 'create' ? 'Tạo Dự Án' : 'Lưu Thay Đổi') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. JIRA-STYLE RIGHT SLIDE-OVER DRAWER WITH PROJECT SWITCHER               -->
    <!-- ========================================================================= -->
    <div
      v-if="selectedTask"
      class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 flex justify-end transition-opacity"
      @click.self="closeTaskDrawer"
    >
      <div
        class="w-full sm:w-[500px] md:w-[560px] h-full bg-slate-950 border-l border-slate-800 shadow-2xl p-6 overflow-y-auto flex flex-col justify-between font-sans animate-slideInRight"
      >
        <div class="space-y-5">
          <!-- Drawer Header / Breadcrumb -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
              <span class="text-xs font-mono text-slate-400 uppercase tracking-wider">TASKS / #{{ selectedTask.id }}</span>
              <span :class="['text-[10px] font-mono px-2 py-0.5 rounded border', getCategoryBadge(selectedTask.category).class]">
                {{ getCategoryBadge(selectedTask.category).label }}
              </span>
            </div>

            <button
              @click="closeTaskDrawer"
              class="text-slate-400 hover:text-white p-1 rounded-lg bg-slate-900 cursor-pointer text-xs"
              title="Đóng (Esc)"
            >
              ✕
            </button>
          </div>

          <!-- Editable Title Heading -->
          <div>
            <textarea
              v-model="selectedTask.title"
              rows="2"
              @blur="saveTaskDrawerChanges"
              placeholder="Tiêu đề nhiệm vụ..."
              class="w-full bg-transparent font-display font-bold text-lg text-white border-b border-transparent focus:border-slate-600 outline-none resize-none leading-snug"
            ></textarea>
          </div>

          <!-- Project Association Field -->
          <div class="p-3 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between gap-3 text-xs">
            <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
              <span>📁</span>
              <span>Dự Án Thuộc Về:</span>
            </span>
            <select
              v-model="selectedTask.project_id"
              @change="saveTaskDrawerChanges"
              class="flex-1 px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-200 font-medium outline-none cursor-pointer"
            >
              <option :value="null">Không phân dự án (General)</option>
              <optgroup label="💼 Công Việc (Work)">
                <option v-for="p in workProjects" :key="p.id" :value="p.id">
                  {{ p.title }}
                </option>
              </optgroup>
              <optgroup label="👤 Cá Nhân (Personal)">
                <option v-for="p in personalProjects" :key="p.id" :value="p.id">
                  {{ p.title }}
                </option>
              </optgroup>
            </select>
          </div>

          <!-- Status & Properties Grid -->
          <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-slate-900/70 border border-slate-800 text-xs">
            <!-- Status Switcher -->
            <div>
              <label class="block text-[10px] font-mono text-slate-400 uppercase mb-1">Trạng thái:</label>
              <select
                v-model="selectedTask.status"
                @change="saveTaskDrawerChanges"
                class="w-full px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-100 outline-none cursor-pointer"
              >
                <option value="todo">📋 Cần Làm (Todo)</option>
                <option value="in_progress">⚡ Đang Thực Thi</option>
                <option value="review">🔍 Kiểm Thử (Review)</option>
                <option value="done">✅ Đã Hoàn Tất (Done)</option>
              </select>
            </div>

            <!-- Priority Selector -->
            <div>
              <label class="block text-[10px] font-mono text-slate-400 uppercase mb-1">Độ ưu tiên:</label>
              <select
                v-model="selectedTask.priority"
                @change="saveTaskDrawerChanges"
                class="w-full px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-100 outline-none cursor-pointer"
              >
                <option value="urgent">🔥 Khẩn cấp</option>
                <option value="high">⚡ Ưu tiên cao</option>
                <option value="medium">🌿 Bình thường</option>
                <option value="low">🍃 Thấp</option>
              </select>
            </div>

            <!-- Estimated & Completed Pomodoros -->
            <div>
              <label class="block text-[10px] font-mono text-slate-400 uppercase mb-1">Pomodoro Focus:</label>
              <div class="flex items-center gap-2">
                <span class="font-mono text-amber-300 font-bold text-xs">
                  🍅 {{ selectedTask.completed_pomodoros }} / {{ selectedTask.estimated_pomodoros }}
                </span>
                <button
                  @click="incrementPomodoro(selectedTask)"
                  class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 text-[10px] font-bold cursor-pointer"
                  title="+1 Pomodoro"
                >
                  +1
                </button>
              </div>
            </div>

            <!-- Due Date -->
            <div>
              <label class="block text-[10px] font-mono text-slate-400 uppercase mb-1">Hạn hoàn thành:</label>
              <input
                v-model="selectedTask.due_date"
                type="date"
                @change="saveTaskDrawerChanges"
                class="w-full px-2.5 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-100 outline-none"
              />
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-xs font-bold text-slate-300 mb-1.5">Mô tả chi tiết:</label>
            <textarea
              v-model="selectedTask.description"
              rows="4"
              @blur="saveTaskDrawerChanges"
              placeholder="Thêm mô tả chi tiết, hướng giải quyết hoặc yêu cầu kỹ thuật..."
              class="w-full p-3 rounded-xl bg-slate-900 border border-slate-800 focus:border-slate-600 text-xs text-slate-200 placeholder-slate-500 outline-none resize-none font-sans leading-relaxed"
            ></textarea>
          </div>

          <!-- Subtasks / Checklist (Jira-style) -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="text-xs font-bold text-slate-300">Danh sách việc con (Checklist):</label>
              <span class="text-[10px] font-mono text-slate-500">
                {{ (selectedTask.subtasks || []).filter(s => s.done).length }}/{{ (selectedTask.subtasks || []).length }}
              </span>
            </div>

            <div class="space-y-1.5 mb-2.5">
              <div
                v-for="(sub, sIdx) in selectedTask.subtasks || []"
                :key="sub.id"
                class="flex items-center justify-between p-2 rounded-lg bg-slate-900/80 border border-slate-800/80 text-xs group"
              >
                <label class="flex items-center gap-2 cursor-pointer flex-1 min-w-0">
                  <input
                    type="checkbox"
                    :checked="sub.done"
                    @change="toggleSubtask(sIdx)"
                    class="accent-emerald-500 w-3.5 h-3.5 cursor-pointer shrink-0"
                  />
                  <span :class="['truncate', sub.done ? 'line-through text-slate-500' : 'text-slate-200']">
                    {{ sub.text }}
                  </span>
                </label>
                <button
                  @click="removeSubtask(sIdx)"
                  class="opacity-0 group-hover:opacity-100 text-slate-500 hover:text-red-400 p-1 text-[10px] cursor-pointer"
                >
                  ✕
                </button>
              </div>
            </div>

            <!-- Add Subtask Input -->
            <div class="flex gap-1.5">
              <input
                v-model="newSubtaskText"
                @keyup.enter="addSubtask"
                type="text"
                placeholder="+ Thêm mục việc con..."
                class="flex-1 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-xs text-white placeholder-slate-500 outline-none"
              />
              <button
                @click="addSubtask"
                class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium cursor-pointer"
              >
                Thêm
              </button>
            </div>
          </div>
        </div>

        <!-- Drawer Footer Actions -->
        <div class="pt-4 mt-6 border-t border-slate-800 flex items-center justify-between">
          <button
            @click="deleteTask(selectedTask)"
            class="text-xs text-red-400 hover:text-red-300 transition-colors cursor-pointer"
          >
            🗑️ Xóa nhiệm vụ này
          </button>

          <button
            @click="closeTaskDrawer"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-100 text-xs font-semibold cursor-pointer"
          >
            Đã Lưu (Đóng)
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Create Task -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 z-50">
      <div class="w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <h3 class="font-display font-bold text-base text-white">Tạo Nhiệm Vụ Mới</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form @submit.prevent="handleCreateTask" class="space-y-3">
          <!-- Project Selection -->
          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1">Dự Án Thuộc Về:</label>
            <select
              v-model="newTaskForm.project_id"
              class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-200 outline-none"
            >
              <option :value="null">Không phân dự án (General)</option>
              <optgroup label="💼 Công Việc (Work)">
                <option v-for="p in workProjects" :key="p.id" :value="p.id">
                  {{ p.title }}
                </option>
              </optgroup>
              <optgroup label="👤 Cá Nhân (Personal)">
                <option v-for="p in personalProjects" :key="p.id" :value="p.id">
                  {{ p.title }}
                </option>
              </optgroup>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1">Tiêu Đề:</label>
            <input
              v-model="newTaskForm.title"
              type="text"
              required
              placeholder="VD: Tối ưu query database..."
              class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 focus:border-slate-600 text-sm text-white outline-none"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-slate-300 mb-1">Mô Tả:</label>
            <textarea
              v-model="newTaskForm.description"
              rows="2.5"
              placeholder="Chi tiết công việc..."
              class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 focus:border-slate-600 text-xs text-white outline-none resize-none"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Độ Ưu Tiên:</label>
              <select v-model="newTaskForm.priority" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white outline-none">
                <option value="urgent">🔥 Khẩn cấp</option>
                <option value="high">⚡ Ưu tiên cao</option>
                <option value="medium">🌿 Bình thường</option>
                <option value="low">🍃 Thấp</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Chuyên Mục:</label>
              <select v-model="newTaskForm.category" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white outline-none">
                <option value="ai_agent">AI Agent</option>
                <option value="backend">Backend</option>
                <option value="frontend">Frontend</option>
                <option value="infra">Infra</option>
                <option value="mindful">Chánh Niệm</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Số Pomodoro:</label>
              <input
                v-model.number="newTaskForm.estimated_pomodoros"
                type="number"
                min="1"
                max="20"
                class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white outline-none"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-300 mb-1">Hạn Hoàn Thành:</label>
              <input
                v-model="newTaskForm.due_date"
                type="date"
                class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white outline-none"
              />
            </div>
          </div>

          <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
            <button
              type="button"
              @click="showCreateModal = false"
              class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-medium hover:bg-slate-700 cursor-pointer"
            >
              Hủy
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md transition-all cursor-pointer"
            >
              {{ isSubmitting ? 'Đang tạo...' : 'Tạo Nhiệm Vụ' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal: Morning Dispatch -->
    <div v-if="showDispatchModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 z-50">
      <div class="w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <div class="flex items-center gap-2">
            <span class="text-xl">🌞</span>
            <h3 class="font-display font-bold text-base text-slate-100">Giao Việc Buổi Sáng Cho Desktop Mascot</h3>
          </div>
          <button @click="showDispatchModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <p class="text-xs text-slate-400 leading-relaxed">
          Ma Cà Tưng sẽ mang các nhiệm vụ trọng tâm này xuất hiện trên màn hình desktop máy tính của bạn để nhắc nhở và đếm giờ Pomodoro!
        </p>

        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
          <div
            v-for="task in todoTasks.slice(0, 5)"
            :key="task.id"
            class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between text-xs"
          >
            <div>
              <div class="font-medium text-slate-200">{{ task.title }}</div>
              <div class="text-[10px] text-slate-400 font-mono">
                <span v-if="task.project" class="text-slate-300 mr-1">📁 {{ task.project.title }} •</span>
                <span>🍅 {{ task.estimated_pomodoros }} Pomodoro</span>
              </div>
            </div>
            <span class="text-emerald-400 font-medium">Sẵn sàng</span>
          </div>
        </div>

        <div class="pt-3 border-t border-slate-800 flex justify-end">
          <button
            @click="showDispatchModal = false; sound.playSuccess();"
            class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md transition-all cursor-pointer"
          >
            Đã Đồng Bộ Đến Desktop Mascot ✓
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Evening Review -->
    <div v-if="showReviewModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4 z-50">
      <div class="w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <div class="flex items-center gap-2">
            <span class="text-xl">🌙</span>
            <h3 class="font-display font-bold text-base text-slate-100">Tổng Kết Năng Suất Cuối Ngày</h3>
          </div>
          <button @click="showReviewModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="grid grid-cols-2 gap-3 text-center my-2">
          <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
            <div class="text-2xl font-bold font-mono text-emerald-400">{{ stats.done }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Nhiệm Vụ Đã Xong</div>
          </div>
          <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
            <div class="text-2xl font-bold font-mono text-purple-400">{{ stats.total_pomodoros_completed }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Pomodoros Hoàn Thành</div>
          </div>
        </div>

        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 italic leading-relaxed">
          "Chiến công tối thượng là tự thắng chính mình. Một ngày làm việc trọn vẹn trong chánh niệm và kiên trì!"
        </div>

        <div class="pt-2 border-t border-slate-800 flex justify-end">
          <button
            @click="showReviewModal = false; sound.playTalisman();"
            class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-medium text-xs shadow-md transition-all cursor-pointer"
          >
            Nghỉ Ngơi Xả Stress 🌸
          </button>
        </div>
      </div>
    </div>

    <!-- Backdrop for Project Actions Dropdown Menu -->
    <div
      v-if="activeProjectMenuId !== null"
      class="fixed inset-0 z-30 bg-transparent"
      @click="activeProjectMenuId = null"
    ></div>

    <!-- ==================================================================== -->
    <!-- 🔒 PIN SECURITY GATE MODAL (MASTER PIN: 301095)                      -->
    <!-- ==================================================================== -->
    <div
      v-if="!isPinUnlocked"
      class="fixed inset-0 z-50 bg-[#04070d]/95 backdrop-blur-2xl flex items-center justify-center p-4 selection:bg-emerald-500/30 select-none overflow-y-auto"
    >
      <!-- Background Ambient Glows -->
      <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none"></div>
      <div class="absolute -bottom-40 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-cyan-500/10 rounded-full blur-[140px] pointer-events-none"></div>

      <div
        :class="[
          'relative w-full max-w-md bg-[#0a0f1d] border border-slate-800/90 rounded-3xl p-6 sm:p-8 shadow-2xl text-center z-10 transition-all duration-300',
          isPinShaking ? 'animate-bounce !border-red-500/80 shadow-red-500/20' : 'border-slate-800'
        ]"
      >
        <!-- Security Shield & Mascot Header -->
        <div class="flex flex-col items-center mb-6">
          <div class="relative mb-3">
            <div class="w-16 h-16 rounded-2xl bg-slate-900 border border-emerald-500/30 flex items-center justify-center shadow-lg shadow-emerald-500/10">
              <span class="text-3xl animate-pulse">🔒</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 border-2 border-[#0a0f1d] flex items-center justify-center text-[10px] text-slate-950 font-bold">
              ✓
            </div>
          </div>

          <h2 class="text-lg sm:text-xl font-bold font-display text-white tracking-wide flex items-center gap-2">
            <span>BẢO MẬT DỰ ÁN & NHIỆM VỤ</span>
          </h2>
          <p class="text-xs text-slate-400 mt-1.5 max-w-xs leading-relaxed">
            Khu vực quản trị nội bộ. Vui lòng nhập mã PIN <strong class="text-emerald-400 font-mono">6 chữ số</strong> để mở khóa Workspace.
          </p>
        </div>

        <!-- 6 PIN Digit Display Slots -->
        <div class="flex items-center justify-center gap-2.5 sm:gap-3.5 mb-6">
          <div
            v-for="i in 6"
            :key="i"
            :class="[
              'w-11 h-14 sm:w-12 sm:h-14 rounded-2xl border-2 flex items-center justify-center font-mono font-bold text-xl transition-all duration-200 shadow-inner',
              pinInput.length >= i
                ? 'border-emerald-400 bg-emerald-500/10 text-emerald-300 shadow-emerald-500/20 scale-105'
                : pinInput.length === i - 1
                ? 'border-slate-600 bg-slate-900/80 text-slate-400 ring-2 ring-emerald-500/30'
                : 'border-slate-800 bg-slate-950/60 text-slate-600'
            ]"
          >
            <span v-if="pinInput.length >= i" class="text-xl text-emerald-400">●</span>
            <span v-else class="text-slate-700 text-xs">―</span>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="pinError" class="mb-4 text-xs text-red-400 font-medium bg-red-500/10 border border-red-500/20 py-2 px-3 rounded-xl flex items-center justify-center gap-1.5 animate-fadeIn">
          <span>⚠️</span>
          <span>{{ pinError }}</span>
        </div>

        <!-- Interactive Cyber Numpad -->
        <div class="grid grid-cols-3 gap-2.5 sm:gap-3 mb-6 max-w-xs mx-auto">
          <button
            v-for="num in ['1', '2', '3', '4', '5', '6', '7', '8', '9']"
            :key="num"
            @click="handleNumpadPress(num)"
            class="h-12 sm:h-13 rounded-2xl bg-slate-900/90 hover:bg-slate-800 hover:border-emerald-500/40 active:scale-95 border border-slate-800 text-slate-200 font-mono font-bold text-lg transition-all cursor-pointer shadow-sm"
          >
            {{ num }}
          </button>

          <!-- Clear -->
          <button
            @click="handleNumpadClear"
            class="h-12 sm:h-13 rounded-2xl bg-slate-950/80 hover:bg-slate-900 active:scale-95 border border-slate-800 text-slate-400 hover:text-slate-200 font-mono font-bold text-xs transition-all cursor-pointer"
          >
            XÓA
          </button>

          <!-- 0 -->
          <button
            @click="handleNumpadPress('0')"
            class="h-12 sm:h-13 rounded-2xl bg-slate-900/90 hover:bg-slate-800 hover:border-emerald-500/40 active:scale-95 border border-slate-800 text-slate-200 font-mono font-bold text-lg transition-all cursor-pointer shadow-sm"
          >
            0
          </button>

          <!-- Backspace -->
          <button
            @click="handleNumpadBackspace"
            class="h-12 sm:h-13 rounded-2xl bg-slate-950/80 hover:bg-slate-900 active:scale-95 border border-slate-800 text-slate-400 hover:text-red-400 font-mono font-bold text-lg transition-all cursor-pointer flex items-center justify-center"
          >
            ⌫
          </button>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-800/80 text-xs">
          <a
            href="/"
            class="text-slate-400 hover:text-white flex items-center gap-1.5 py-1.5 px-3 rounded-lg hover:bg-slate-900 transition-colors"
          >
            <span>←</span>
            <span>Về Trang Chủ</span>
          </a>

          <button
            @click="checkPin"
            :disabled="pinInput.length !== 6"
            :class="[
              'px-5 py-2 rounded-xl font-bold font-mono text-xs transition-all flex items-center gap-1.5 shadow-md',
              pinInput.length === 6
                ? 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/20 cursor-pointer'
                : 'bg-slate-800 text-slate-500 cursor-not-allowed opacity-50'
            ]"
          >
            <span>MỞ KHÓA</span>
            <span>🔓</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes slideInLeft {
  from {
    transform: translateX(-100%);
  }
  to {
    transform: translateX(0);
  }
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

.animate-slideInLeft {
  animation: slideInLeft 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-slideInRight {
  animation: slideInRight 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
