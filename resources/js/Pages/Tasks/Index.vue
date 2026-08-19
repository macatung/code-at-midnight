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
  key?: string;
  category?: string;
  type: 'work' | 'personal';
  color?: string;
  description?: string | null;
  tasks_count?: number;
}

export interface SprintItem {
  id: number;
  project_id: number | null;
  name: string;
  goal: string | null;
  start_date: string | null;
  end_date: string | null;
  status: 'future' | 'active' | 'completed';
  total_points?: number;
  done_points?: number;
  total_tasks?: number;
  done_tasks?: number;
  tasks?: TaskItem[];
  created_at?: string;
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
  issue_key?: string;
  issue_type: 'epic' | 'story' | 'task' | 'bug';
  title: string;
  description: string | null;
  status: 'todo' | 'in_progress' | 'review' | 'done';
  priority: 'urgent' | 'high' | 'medium' | 'low';
  category: string;
  story_points: number | null;
  sprint_id: number | null;
  sprint?: SprintItem | null;
  epic_id: number | null;
  epic?: TaskItem | null;
  start_date: string | null;
  due_date: string | null;
  completed_at: string | null;
  estimated_pomodoros: number;
  completed_pomodoros: number;
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
  total_story_points: number;
  completed_story_points: number;
  total_pomodoros_estimated: number;
  total_pomodoros_completed: number;
  completion_rate: number;
}

const props = defineProps<{
  tasks: TaskItem[];
  projects: ProjectItem[];
  sprints?: SprintItem[];
  epics?: TaskItem[];
  stats: Stats;
  selectedDate: string;
  selectedProjectId?: string | number;
}>();

// Main Reactive State
const taskList = ref<TaskItem[]>(
  props.tasks.map(t => ({
    ...t,
    issue_type: t.issue_type || 'task',
    issue_key: t.issue_key || ('MCT-' + t.id),
    subtasks: t.notes ? tryParseSubtasks(t.notes) : [],
  }))
);

const projectList = ref<ProjectItem[]>([...props.projects]);
const sprintList = ref<SprintItem[]>(props.sprints ? [...props.sprints] : []);
const epicList = computed(() => taskList.value.filter(t => t.issue_type === 'epic'));

function tryParseSubtasks(notes: string): SubtaskItem[] {
  try {
    const parsed = JSON.parse(notes);
    if (Array.isArray(parsed)) return parsed;
  } catch (e) {}
  return [];
}

// Light / Dark Theme State (Default: Light Mode)
const isDarkMode = ref(false);

const toggleTheme = () => {
  isDarkMode.value = !isDarkMode.value;
  localStorage.setItem('macatung_tasks_theme', isDarkMode.value ? 'dark' : 'light');
  sound.playClick();
};

// Sidebar State
const isSidebarOpen = ref(true);
const selectedProjectId = ref<string | number>(props.selectedProjectId || 'all');
const activeProjectMenuId = ref<number | null>(null);

// Top View Mode: Board (Kanban) | Backlog (Sprint Planning) | Roadmap (Gantt)
const currentView = ref<'board' | 'backlog' | 'roadmap'>('board');

// Board Swimlane Mode: 'none' | 'epic' | 'category'
const swimlaneMode = ref<'none' | 'epic' | 'category'>('none');

// Collapsed Sprints in Backlog View
const collapsedSprints = ref<Record<number, boolean>>({});
const toggleSprintCollapse = (sprintId: number) => {
  collapsedSprints.value[sprintId] = !collapsedSprints.value[sprintId];
  sound.playClick();
};

// Quick Filters
const searchQuery = ref('');
const filterIssueType = ref<'all' | 'story' | 'task' | 'bug' | 'epic'>('all');
const filterPriority = ref<'all' | 'urgent' | 'high' | 'medium' | 'low'>('all');
const filterEpicId = ref<string | number>('all');
const filterSprintId = ref<string | number>('active');

const quickInputText = ref('');
const quickInputRef = ref<HTMLInputElement | null>(null);
const searchInputRef = ref<HTMLInputElement | null>(null);

// PIN Security State (Master PIN: 301095)
const isPinUnlocked = ref(false);
const pinInput = ref('');
const pinError = ref('');
const isPinShaking = ref(false);

const checkPin = () => {
  if (pinInput.value === '301095') {
    sound.playSuccess();
    isPinUnlocked.value = true;
    pinError.value = '';
    sessionStorage.setItem('macatung_tasks_pin_auth', '301095');
  } else {
    sound.playError();
    pinError.value = 'Mã PIN không chính xác. Vui lòng thử lại!';
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

// Modals & Drawer State
const selectedTask = ref<TaskItem | null>(null);
const isEditingDescription = ref(false);
const descriptionEditContent = ref('');
const showCreateModal = ref(false);
const showSprintModal = ref(false);
const showStartSprintModal = ref(false);
const showCompleteSprintModal = ref(false);
const targetSprintForAction = ref<SprintItem | null>(null);
const isSubmitting = ref(false);
const newSubtaskText = ref('');

// Project CRUD Modal State
const showProjectModal = ref(false);
const projectModalMode = ref<'create' | 'edit'>('create');
const editingProjectId = ref<number | null>(null);
const isProjectSubmitting = ref(false);

const projectForm = ref({
  title: '',
  key: '',
  type: 'work' as 'work' | 'personal',
  color: '#2563eb',
  description: '',
});

// Sprint Form
const sprintForm = ref({
  name: '',
  goal: '',
  duration_weeks: 2,
  start_date: new Date().toISOString().split('T')[0],
  end_date: '',
});

// Drag & Drop State
const draggedTaskId = ref<number | null>(null);
const dragOverColumn = ref<string | null>(null);
const dragOverSprintId = ref<string | number | null>(null);

// New Task Form
const newTaskForm = ref({
  project_id: null as number | null,
  issue_type: 'task' as TaskItem['issue_type'],
  title: '',
  description: '',
  status: 'todo' as TaskItem['status'],
  priority: 'high' as TaskItem['priority'],
  category: 'backend',
  story_points: 3,
  sprint_id: null as number | null,
  epic_id: null as number | null,
  estimated_pomodoros: 2,
  start_date: new Date().toISOString().split('T')[0],
  due_date: new Date().toISOString().split('T')[0],
});

// Computed Properties
const workProjects = computed(() => projectList.value.filter(p => p.type === 'work'));
const personalProjects = computed(() => projectList.value.filter(p => p.type === 'personal'));

const activeProjectObject = computed(() => {
  if (selectedProjectId.value === 'all' || selectedProjectId.value === 'unassigned') {
    return null;
  }
  return projectList.value.find(p => p.id === Number(selectedProjectId.value)) || null;
});

const activeSprint = computed(() => {
  return sprintList.value.find(s => s.status === 'active') || null;
});

// Filtered Tasks for Active Board
const filteredBoardTasks = computed(() => {
  return taskList.value.filter(task => {
    // Project filter
    if (selectedProjectId.value !== 'all') {
      if (selectedProjectId.value === 'unassigned') {
        if (task.project_id !== null) return false;
      } else {
        if (task.project_id !== Number(selectedProjectId.value)) return false;
      }
    }

    // Sprint filter
    if (filterSprintId.value === 'active') {
      if (activeSprint.value) {
        if (task.sprint_id !== activeSprint.value.id) return false;
      }
    } else if (filterSprintId.value !== 'all') {
      if (task.sprint_id !== Number(filterSprintId.value)) return false;
    }

    // Search query
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase();
      const matchTitle = task.title.toLowerCase().includes(q);
      const matchKey = (task.issue_key || '').toLowerCase().includes(q);
      const matchDesc = (task.description || '').toLowerCase().includes(q);
      if (!matchTitle && !matchKey && !matchDesc) return false;
    }

    // Issue Type Filter
    if (filterIssueType.value !== 'all' && task.issue_type !== filterIssueType.value) {
      return false;
    }

    // Priority Filter
    if (filterPriority.value !== 'all' && task.priority !== filterPriority.value) {
      return false;
    }

    // Epic Filter
    if (filterEpicId.value !== 'all') {
      if (filterEpicId.value === 'none') {
        if (task.epic_id !== null) return false;
      } else {
        if (task.epic_id !== Number(filterEpicId.value)) return false;
      }
    }

    return true;
  });
});

// Board Columns
const todoTasks = computed(() => filteredBoardTasks.value.filter(t => t.status === 'todo'));
const inProgressTasks = computed(() => filteredBoardTasks.value.filter(t => t.status === 'in_progress'));
const reviewTasks = computed(() => filteredBoardTasks.value.filter(t => t.status === 'review'));
const doneTasks = computed(() => filteredBoardTasks.value.filter(t => t.status === 'done'));

// Backlog Pool Tasks (No sprint assigned)
const backlogTasks = computed(() => {
  return taskList.value.filter(task => {
    if (selectedProjectId.value !== 'all') {
      if (selectedProjectId.value === 'unassigned') {
        if (task.project_id !== null) return false;
      } else {
        if (task.project_id !== Number(selectedProjectId.value)) return false;
      }
    }
    return task.sprint_id === null;
  });
});

const getSprintTasks = (sprintId: number) => {
  return taskList.value.filter(t => t.sprint_id === sprintId);
};

const getSprintStats = (sprintId: number) => {
  const tasks = getSprintTasks(sprintId);
  const totalPts = tasks.reduce((sum, t) => sum + (t.story_points || 0), 0);
  const donePts = tasks.filter(t => t.status === 'done').reduce((sum, t) => sum + (t.story_points || 0), 0);
  const inProgressPts = tasks.filter(t => t.status === 'in_progress' || t.status === 'review').reduce((sum, t) => sum + (t.story_points || 0), 0);
  const todoPts = tasks.filter(t => t.status === 'todo').reduce((sum, t) => sum + (t.story_points || 0), 0);

  const donePercent = totalPts > 0 ? Math.round((donePts / totalPts) * 100) : 0;
  const inProgressPercent = totalPts > 0 ? Math.round((inProgressPts / totalPts) * 100) : 0;
  const todoPercent = totalPts > 0 ? Math.max(0, 100 - donePercent - inProgressPercent) : 0;

  return {
    totalTasks: tasks.length,
    doneTasks: tasks.filter(t => t.status === 'done').length,
    inProgressTasks: tasks.filter(t => t.status === 'in_progress' || t.status === 'review').length,
    todoTasks: tasks.filter(t => t.status === 'todo').length,
    totalPts,
    donePts,
    inProgressPts,
    todoPts,
    donePercent,
    inProgressPercent,
    todoPercent,
  };
};

// Badges & Visual Styles
const getIssueTypeBadge = (type: string) => {
  switch (type) {
    case 'epic':
      return { label: 'EPIC', icon: '⚡', class: 'bg-purple-50 text-purple-700 border-purple-200 font-bold' };
    case 'story':
      return { label: 'STORY', icon: '📖', class: 'bg-emerald-50 text-emerald-700 border-emerald-200 font-semibold' };
    case 'bug':
      return { label: 'BUG', icon: '🐞', class: 'bg-rose-50 text-rose-700 border-rose-200 font-semibold' };
    case 'task':
    default:
      return { label: 'TASK', icon: '☑️', class: 'bg-blue-50 text-blue-700 border-blue-200 font-semibold' };
  }
};

const getPriorityBadge = (priority: string) => {
  switch (priority) {
    case 'urgent': return { label: 'Khẩn cấp', icon: '🔴', class: 'bg-red-50 text-red-700 border-red-200' };
    case 'high': return { label: 'Ưu tiên', icon: '🟠', class: 'bg-amber-50 text-amber-800 border-amber-200' };
    case 'medium': return { label: 'Bình thường', icon: '🟡', class: 'bg-slate-100 text-slate-700 border-slate-200' };
    case 'low': return { label: 'Thấp', icon: '⚪', class: 'bg-slate-50 text-slate-500 border-slate-200' };
    default: return { label: priority, icon: '⚪', class: 'bg-slate-100 text-slate-600 border-slate-200' };
  }
};

const getCategoryBadge = (category: string) => {
  switch (category) {
    case 'ai_agent': return { label: 'AI Agent', class: 'text-purple-700 bg-purple-50 border-purple-200' };
    case 'backend': return { label: 'Backend', class: 'text-blue-700 bg-blue-50 border-blue-200' };
    case 'frontend': return { label: 'Frontend', class: 'text-cyan-700 bg-cyan-50 border-cyan-200' };
    case 'infra': return { label: 'Infra', class: 'text-amber-800 bg-amber-50 border-amber-200' };
    case 'mindful': return { label: 'Chánh Niệm', class: 'text-emerald-700 bg-emerald-50 border-emerald-200' };
    default: return { label: category, class: 'text-slate-700 bg-slate-100 border-slate-200' };
  }
};

const getProjectTaskCount = (projectId: string | number) => {
  if (projectId === 'all') return taskList.value.length;
  if (projectId === 'unassigned') return taskList.value.filter(t => t.project_id === null).length;
  return taskList.value.filter(t => t.project_id === Number(projectId)).length;
};

// Lightweight Markdown Formatter
const formatMarkdown = (content: string | null): string => {
  if (!content) return '';

  let html = content
    // Escape standard HTML tags
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

  // Format Code Blocks ```lang ... ```
  html = html.replace(/```([a-zA-Z0-9_-]*)\n([\s\S]*?)```/g, (match, lang, code) => {
    return `<div class="my-3 rounded-xl overflow-hidden border border-slate-700 bg-slate-900 text-slate-100 shadow-md">
      <div class="px-3.5 py-1.5 bg-slate-800 text-[11px] font-mono text-slate-400 border-b border-slate-700 flex items-center justify-between">
        <span>${lang ? lang.toUpperCase() : 'CODE'}</span>
      </div>
      <pre class="p-3.5 text-xs font-mono text-emerald-400 overflow-x-auto leading-relaxed"><code>${code.trim()}</code></pre>
    </div>`;
  });

  // Format Headings
  html = html
    .replace(/^#### (.*$)/gim, '<h4 class="text-xs font-bold font-display uppercase tracking-wider text-slate-800 dark:text-slate-200 mt-3 mb-1.5">$1</h4>')
    .replace(/^### (.*$)/gim, '<h3 class="text-sm font-bold text-slate-900 dark:text-white mt-3.5 mb-1.5 border-b border-slate-200 dark:border-slate-800 pb-1">$1</h3>')
    .replace(/^## (.*$)/gim, '<h2 class="text-base font-bold text-slate-900 dark:text-white mt-4 mb-2">$1</h2>')
    .replace(/^# (.*$)/gim, '<h1 class="text-lg font-bold text-slate-900 dark:text-white mt-4 mb-2">$1</h1>');

  // Format Bold & Italic
  html = html
    .replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-slate-900 dark:text-white">$1</strong>')
    .replace(/\*(.*?)\*/g, '<em class="italic text-slate-600 dark:text-slate-300">$1</em>');

  // Format Bullet Points
  html = html.replace(/^\s*-\s+(.*$)/gim, '<li class="ml-4 list-disc text-slate-700 dark:text-slate-300 my-0.5">$1</li>');

  // Format Paragraphs
  html = html.replace(/\n\n/g, '<div class="h-2"></div>');

  return html;
};

// SPRINT ACTIONS
const openCreateSprintModal = () => {
  sprintForm.value = {
    name: `Sprint ${sprintList.value.length + 1} — `,
    goal: '',
    duration_weeks: 2,
    start_date: new Date().toISOString().split('T')[0],
    end_date: '',
  };
  showSprintModal.value = true;
  sound.playClick();
};

const handleSaveSprint = async () => {
  if (!sprintForm.value.name.trim()) return;
  isSubmitting.value = true;

  try {
    const payload = {
      project_id: selectedProjectId.value !== 'all' && selectedProjectId.value !== 'unassigned' ? Number(selectedProjectId.value) : null,
      name: sprintForm.value.name,
      goal: sprintForm.value.goal,
      start_date: sprintForm.value.start_date,
      end_date: sprintForm.value.end_date || null,
      status: 'future',
    };

    const res = await axios.post('/api/sprints', payload);
    if (res.data.success) {
      sprintList.value.unshift(res.data.data);
      sound.playSuccess();
      showSprintModal.value = false;
    }
  } catch (err) {
    console.error('Create sprint error:', err);
    alert('Không thể tạo Sprint. Vui lòng thử lại!');
  } finally {
    isSubmitting.value = false;
  }
};

const openStartSprintModal = (sprint: SprintItem) => {
  targetSprintForAction.value = sprint;
  showStartSprintModal.value = true;
  sound.playClick();
};

const confirmStartSprint = async () => {
  if (!targetSprintForAction.value) return;
  isSubmitting.value = true;

  try {
    const res = await axios.post(`/api/sprints/${targetSprintForAction.value.id}/start`, {
      duration_weeks: 2,
    });
    if (res.data.success) {
      sprintList.value.forEach(s => {
        if (s.id === targetSprintForAction.value?.id) {
          s.status = 'active';
        }
      });
      sound.playSuccess();
      showStartSprintModal.value = false;
    }
  } catch (err) {
    console.error('Start sprint error:', err);
    alert('Lỗi khi bắt đầu Sprint!');
  } finally {
    isSubmitting.value = false;
  }
};

const openCompleteSprintModal = (sprint: SprintItem) => {
  targetSprintForAction.value = sprint;
  showCompleteSprintModal.value = true;
  sound.playClick();
};

const confirmCompleteSprint = async () => {
  if (!targetSprintForAction.value) return;
  isSubmitting.value = true;

  try {
    const res = await axios.post(`/api/sprints/${targetSprintForAction.value.id}/complete`, {
      move_incomplete_to: 'backlog',
    });
    if (res.data.success) {
      const idx = sprintList.value.findIndex(s => s.id === targetSprintForAction.value?.id);
      if (idx !== -1) sprintList.value[idx].status = 'completed';

      taskList.value.forEach(t => {
        if (t.sprint_id === targetSprintForAction.value?.id && t.status !== 'done') {
          t.sprint_id = null;
        }
      });

      sound.playSuccess();
      showCompleteSprintModal.value = false;
    }
  } catch (err) {
    console.error('Complete sprint error:', err);
    alert('Lỗi khi hoàn thành Sprint!');
  } finally {
    isSubmitting.value = false;
  }
};

const handleDeleteSprint = async (sprint: SprintItem) => {
  if (!confirm(`Bạn có chắc muốn xóa Sprint "${sprint.name}"?\n(Toàn bộ công việc trong Sprint sẽ được chuyển về Backlog an toàn)`)) return;

  try {
    await axios.delete(`/api/sprints/${sprint.id}`);
    sprintList.value = sprintList.value.filter(s => s.id !== sprint.id);
    taskList.value.forEach(t => {
      if (t.sprint_id === sprint.id) t.sprint_id = null;
    });
    sound.playClick();
  } catch (err) {
    console.error('Delete sprint failed:', err);
    alert('Lỗi khi xóa Sprint!');
  }
};

// PROJECT CRUD
const openCreateProjectModal = (type: 'work' | 'personal' = 'work') => {
  projectModalMode.value = 'create';
  editingProjectId.value = null;
  projectForm.value = {
    title: '',
    key: '',
    type,
    color: type === 'work' ? '#2563eb' : '#f59e0b',
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
    key: project.key || '',
    type: project.type || 'work',
    color: project.color || '#2563eb',
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

        taskList.value.forEach(t => {
          if (t.project_id === updated.id) t.project = updated;
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
  if (!confirm(`Bạn có chắc muốn xóa dự án "${project.title}"?\n(Toàn bộ các nhiệm vụ thuộc dự án này sẽ được giữ lại an toàn)`)) {
    return;
  }

  try {
    await axios.delete(`/api/projects/${project.id}`);
    projectList.value = projectList.value.filter(p => p.id !== project.id);
    taskList.value.forEach(t => {
      if (t.project_id === project.id) {
        t.project_id = null;
        t.project = null;
      }
    });
    if (selectedProjectId.value === project.id) selectedProjectId.value = 'all';
    sound.playClick();
  } catch (err) {
    console.error('Delete project failed:', err);
    alert('Lỗi khi xóa dự án!');
  }
};

// TASK / ISSUE CRUD & DRAWER
const openCreateTaskModal = () => {
  newTaskForm.value = {
    project_id: selectedProjectId.value !== 'all' && selectedProjectId.value !== 'unassigned' ? Number(selectedProjectId.value) : null,
    issue_type: 'task',
    title: '',
    description: '',
    status: 'todo',
    priority: 'high',
    category: 'backend',
    story_points: 3,
    sprint_id: activeSprint.value ? activeSprint.value.id : null,
    epic_id: null,
    estimated_pomodoros: 2,
    start_date: new Date().toISOString().split('T')[0],
    due_date: new Date().toISOString().split('T')[0],
  };
  showCreateModal.value = true;
  sound.playClick();
};

const handleCreateTask = async () => {
  if (!newTaskForm.value.title.trim()) return;
  isSubmitting.value = true;

  try {
    const res = await axios.post('/api/tasks', newTaskForm.value);
    if (res.data.success) {
      const created: TaskItem = {
        ...res.data.data,
        subtasks: [],
      };
      taskList.value.unshift(created);
      showCreateModal.value = false;
      sound.playSuccess();
    }
  } catch (err) {
    console.error('Create task failed:', err);
    alert('Lỗi khi tạo nhiệm vụ mới!');
  } finally {
    isSubmitting.value = false;
  }
};

const handleQuickCreate = async (targetSprintId: number | null = null) => {
  if (!quickInputText.value.trim()) return;
  const title = quickInputText.value.trim();
  quickInputText.value = '';

  try {
    const payload = {
      title,
      project_id: selectedProjectId.value !== 'all' && selectedProjectId.value !== 'unassigned' ? Number(selectedProjectId.value) : null,
      issue_type: 'task',
      status: 'todo',
      priority: 'high',
      category: 'backend',
      story_points: 2,
      sprint_id: targetSprintId !== null ? targetSprintId : (activeSprint.value?.id || null),
    };

    const res = await axios.post('/api/tasks', payload);
    if (res.data.success) {
      const created: TaskItem = {
        ...res.data.data,
        subtasks: [],
      };
      taskList.value.unshift(created);
      sound.playClick();
    }
  } catch (err) {
    console.error('Quick task create error:', err);
  }
};

const openTaskDrawer = (task: TaskItem) => {
  selectedTask.value = { ...task };
  descriptionEditContent.value = task.description || '';
  isEditingDescription.value = false;
  sound.playClick();
};

const closeTaskDrawer = () => {
  if (selectedTask.value) {
    saveTaskDrawerChanges();
  }
  selectedTask.value = null;
  isEditingDescription.value = false;
};

const saveTaskDrawerChanges = async () => {
  if (!selectedTask.value) return;
  const task = selectedTask.value;

  if (isEditingDescription.value) {
    task.description = descriptionEditContent.value;
  }

  task.notes = JSON.stringify(task.subtasks || []);

  const idx = taskList.value.findIndex(t => t.id === task.id);
  if (idx !== -1) {
    taskList.value[idx] = { ...task };
  }

  try {
    await axios.patch(`/api/tasks/${task.id}`, {
      title: task.title,
      description: task.description,
      status: task.status,
      priority: task.priority,
      issue_type: task.issue_type,
      category: task.category,
      story_points: task.story_points,
      sprint_id: task.sprint_id,
      epic_id: task.epic_id,
      project_id: task.project_id,
      estimated_pomodoros: task.estimated_pomodoros,
      completed_pomodoros: task.completed_pomodoros,
      start_date: task.start_date,
      due_date: task.due_date,
      notes: task.notes,
    });
  } catch (err) {
    console.error('Failed to sync task drawer:', err);
  }
};

const updateTaskStatus = async (task: TaskItem, newStatus: TaskItem['status']) => {
  task.status = newStatus;
  if (newStatus === 'done') {
    task.completed_at = new Date().toISOString();
    sound.playSuccess();
  } else {
    task.completed_at = null;
    sound.playClick();
  }

  try {
    await axios.patch(`/api/tasks/${task.id}`, { status: newStatus });
  } catch (err) {
    console.error('Failed to update status:', err);
  }
};

const addSubtask = () => {
  if (!selectedTask.value || !newSubtaskText.value.trim()) return;
  if (!selectedTask.value.subtasks) selectedTask.value.subtasks = [];

  selectedTask.value.subtasks.push({
    id: 'st-' + Date.now(),
    text: newSubtaskText.value.trim(),
    done: false,
  });

  newSubtaskText.value = '';
  sound.playClick();
  saveTaskDrawerChanges();
};

const toggleSubtask = (st: SubtaskItem) => {
  st.done = !st.done;
  sound.playClick();
  saveTaskDrawerChanges();
};

const deleteSubtask = (stId: string) => {
  if (!selectedTask.value || !selectedTask.value.subtasks) return;
  selectedTask.value.subtasks = selectedTask.value.subtasks.filter(s => s.id !== stId);
  sound.playClick();
  saveTaskDrawerChanges();
};

const deleteTask = async (task: TaskItem) => {
  if (!confirm(`Bạn có chắc muốn xóa Issue "${task.issue_key || ''} — ${task.title}"?`)) return;

  try {
    await axios.delete(`/api/tasks/${task.id}`);
    taskList.value = taskList.value.filter(t => t.id !== task.id);
    if (selectedTask.value?.id === task.id) selectedTask.value = null;
    sound.playClick();
  } catch (err) {
    console.error('Delete task error:', err);
    alert('Lỗi khi xóa nhiệm vụ!');
  }
};

// DRAG & DROP HANDLERS
const onDragStart = (e: DragEvent, taskId: number) => {
  draggedTaskId.value = taskId;
  if (e.dataTransfer) {
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', String(taskId));
  }
};

const onDragOverColumn = (e: DragEvent, status: string) => {
  e.preventDefault();
  dragOverColumn.value = status;
};

const onDropColumn = async (targetStatus: TaskItem['status']) => {
  dragOverColumn.value = null;
  if (!draggedTaskId.value) return;

  const task = taskList.value.find(t => t.id === draggedTaskId.value);
  if (task && task.status !== targetStatus) {
    await updateTaskStatus(task, targetStatus);
  }
  draggedTaskId.value = null;
};

const onDragOverSprint = (e: DragEvent, sprintId: string | number) => {
  e.preventDefault();
  dragOverSprintId.value = sprintId;
};

const onDropSprint = async (targetSprintId: number | null) => {
  dragOverSprintId.value = null;
  if (!draggedTaskId.value) return;

  const task = taskList.value.find(t => t.id === draggedTaskId.value);
  if (task && task.sprint_id !== targetSprintId) {
    task.sprint_id = targetSprintId;
    sound.playClick();

    try {
      await axios.patch(`/api/tasks/${task.id}`, { sprint_id: targetSprintId });
    } catch (err) {
      console.error('Failed to move task to sprint:', err);
    }
  }
  draggedTaskId.value = null;
};

// Global Keyboard Handler
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
      showCreateModal.value = false;
      showSprintModal.value = false;
      activeProjectMenuId.value = null;
    }
    return;
  }

  if (e.key === 'n' || e.key === 'N') {
    e.preventDefault();
    quickInputRef.value?.focus();
  } else if (e.key === '/') {
    e.preventDefault();
    searchInputRef.value?.focus();
  } else if (e.key === '1') {
    currentView.value = 'board';
  } else if (e.key === '2') {
    currentView.value = 'backlog';
  } else if (e.key === '3') {
    currentView.value = 'roadmap';
  } else if (e.key === 'Escape') {
    closeTaskDrawer();
    showCreateModal.value = false;
    showSprintModal.value = false;
    showStartSprintModal.value = false;
    showCompleteSprintModal.value = false;
    showProjectModal.value = false;
    activeProjectMenuId.value = null;
  }
};

const closeAllMenus = () => {
  activeProjectMenuId.value = null;
};

onMounted(() => {
  const savedPin = sessionStorage.getItem('macatung_tasks_pin_auth');
  if (savedPin === '301095') {
    isPinUnlocked.value = true;
  }

  const savedTheme = localStorage.getItem('macatung_tasks_theme');
  if (savedTheme === 'dark') {
    isDarkMode.value = true;
  } else {
    isDarkMode.value = false;
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
  <Head title="Tasks Hub | Linear & Jira Modern Workspace" />

  <div
    :class="[
      'min-h-screen font-sans flex flex-col transition-colors duration-150 selection:bg-blue-100 selection:text-blue-900',
      isDarkMode ? 'bg-[#080d1a] text-slate-100' : 'bg-[#f8fafc] text-slate-800'
    ]"
  >
    <!-- ========================================================================= -->
    <!-- 1. TOP NAVBAR (CLEAN & MODERN)                                            -->
    <!-- ========================================================================= -->
    <header
      :class="[
        'sticky top-0 z-40 border-b backdrop-blur-md transition-colors',
        isDarkMode ? 'bg-[#0f172a]/95 border-slate-800' : 'bg-white/95 border-slate-200/90 shadow-xs'
      ]"
    >
      <div class="w-full px-4 sm:px-6 h-15 flex items-center justify-between">
        <div class="flex items-center gap-3.5">
          <!-- Toggle Sidebar -->
          <button
            @click="isSidebarOpen = !isSidebarOpen"
            :class="[
              'p-1.5 rounded-lg border transition-colors cursor-pointer text-xs',
              isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-400 hover:text-white' : 'bg-slate-50 border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100'
            ]"
            title="Đóng / Mở danh mục dự án"
          >
            {{ isSidebarOpen ? '◀' : '▶' }}
          </button>

          <!-- Logo & Brand -->
          <a href="/" class="flex items-center gap-2.5 group">
            <MiniMascotLogo size="sm" :enable-sound="true" />
            <div class="flex items-center gap-2">
              <span :class="['font-display font-bold text-base tracking-tight', isDarkMode ? 'text-white' : 'text-slate-900']">
                Tasks Hub
              </span>
              <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 font-mono text-[10px] font-bold">
                JIRA LITE
              </span>
            </div>
          </a>
        </div>

        <!-- Center Tabs: Board | Backlog | Roadmap -->
        <div
          :class="[
            'hidden md:flex items-center p-1 rounded-xl border font-medium text-xs gap-1',
            isDarkMode ? 'bg-slate-900/90 border-slate-800' : 'bg-slate-100 border-slate-200/80'
          ]"
        >
          <button
            @click="currentView = 'board'; sound.playClick();"
            :class="[
              'px-3.5 py-1.5 rounded-lg transition-all cursor-pointer flex items-center gap-1.5',
              currentView === 'board'
                ? (isDarkMode ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'bg-white text-blue-700 font-semibold shadow-xs border border-slate-200/60')
                : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')
            ]"
          >
            <span>📋</span>
            <span>Bảng Công Việc</span>
          </button>

          <button
            @click="currentView = 'backlog'; sound.playClick();"
            :class="[
              'px-3.5 py-1.5 rounded-lg transition-all cursor-pointer flex items-center gap-1.5',
              currentView === 'backlog'
                ? (isDarkMode ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'bg-white text-blue-700 font-semibold shadow-xs border border-slate-200/60')
                : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')
            ]"
          >
            <span>📦</span>
            <span>Kế Hoạch Sprint</span>
          </button>

          <button
            @click="currentView = 'roadmap'; sound.playClick();"
            :class="[
              'px-3.5 py-1.5 rounded-lg transition-all cursor-pointer flex items-center gap-1.5',
              currentView === 'roadmap'
                ? (isDarkMode ? 'bg-blue-600 text-white font-semibold shadow-xs' : 'bg-white text-blue-700 font-semibold shadow-xs border border-slate-200/60')
                : (isDarkMode ? 'text-slate-400 hover:text-white' : 'text-slate-600 hover:text-slate-900')
            ]"
          >
            <span>🗺️</span>
            <span>Tiến Độ (Roadmap)</span>
          </button>
        </div>

        <!-- Right Controls -->
        <div class="flex items-center gap-2">
          <!-- Light / Dark Toggle -->
          <button
            @click="toggleTheme"
            :class="[
              'p-2 rounded-xl border text-xs font-semibold transition-colors cursor-pointer',
              isDarkMode ? 'bg-slate-900 border-slate-800 text-amber-300 hover:bg-slate-800' : 'bg-slate-100 border-slate-200 text-slate-700 hover:bg-slate-200'
            ]"
            :title="isDarkMode ? 'Chuyển sang Giao diện Sáng' : 'Chuyển sang Giao diện Tối'"
          >
            <span>{{ isDarkMode ? '☀️ Sáng' : '🌙 Tối' }}</span>
          </button>

          <!-- Create Issue Button -->
          <button
            @click="openCreateTaskModal"
            class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
          >
            <span>+</span>
            <span>Tạo Task</span>
          </button>

          <!-- Lock Button -->
          <button
            @click="lockWorkspace"
            :class="[
              'p-2 rounded-xl border text-xs transition-colors cursor-pointer',
              isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-400 hover:text-red-400' : 'bg-slate-50 border-slate-200 text-slate-500 hover:text-red-600 hover:bg-red-50'
            ]"
            title="Khóa không gian làm việc (PIN: 301095)"
          >
            <span>🔒</span>
          </button>
        </div>
      </div>
    </header>

    <!-- ========================================================================= -->
    <!-- 2. MAIN LAYOUT (SIDEBAR + MAIN CANVAS)                                    -->
    <!-- ========================================================================= -->
    <div class="flex-1 flex overflow-hidden">
      <!-- SIDEBAR: DỰ ÁN 2-LINE LAYOUT -->
      <aside
        v-if="isSidebarOpen"
        :class="[
          'w-72 sm:w-80 border-r flex flex-col justify-between shrink-0 h-[calc(100vh-3.75rem)] select-none transition-colors',
          isDarkMode ? 'bg-[#090d16] border-slate-800/80' : 'bg-white border-slate-200/90'
        ]"
      >
        <div class="p-3.5 space-y-4 overflow-y-auto max-h-[calc(100vh-8.5rem)] pr-2">
          <!-- Overview Items -->
          <div class="space-y-1.5">
            <button
              @click="selectedProjectId = 'all'"
              :class="[
                'w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer border text-left',
                selectedProjectId === 'all'
                  ? (isDarkMode ? 'bg-slate-900 text-white border-blue-500/40 font-semibold' : 'bg-blue-50/80 text-blue-900 border-blue-200 font-semibold shadow-xs')
                  : (isDarkMode ? 'text-slate-400 border-transparent hover:text-white hover:bg-slate-900/60' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-slate-900')
              ]"
            >
              <div class="flex items-center gap-2.5 min-w-0">
                <span class="text-base">📁</span>
                <div>
                  <div class="font-bold text-xs text-slate-900 dark:text-white">Tất Cả Dự Án</div>
                  <div class="text-[11px] text-slate-500">Toàn bộ công việc & nhiệm vụ</div>
                </div>
              </div>
              <span :class="['font-mono text-xs font-bold px-2 py-0.5 rounded-full', isDarkMode ? 'bg-slate-800 text-slate-400' : 'bg-slate-100 text-slate-600 border border-slate-200']">
                {{ getProjectTaskCount('all') }}
              </span>
            </button>

            <button
              @click="selectedProjectId = 'unassigned'"
              :class="[
                'w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer border text-left',
                selectedProjectId === 'unassigned'
                  ? (isDarkMode ? 'bg-slate-900 text-white border-blue-500/40 font-semibold' : 'bg-blue-50/80 text-blue-900 border-blue-200 font-semibold shadow-xs')
                  : (isDarkMode ? 'text-slate-400 border-transparent hover:text-white hover:bg-slate-900/60' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-slate-900')
              ]"
            >
              <div class="flex items-center gap-2.5 min-w-0">
                <span class="text-base">📦</span>
                <div>
                  <div class="font-bold text-xs text-slate-900 dark:text-white">Chung (Chưa gán)</div>
                  <div class="text-[11px] text-slate-500">Task lẻ chưa gán vào dự án</div>
                </div>
              </div>
              <span :class="['font-mono text-xs font-bold px-2 py-0.5 rounded-full', isDarkMode ? 'bg-slate-800 text-slate-400' : 'bg-slate-100 text-slate-600 border border-slate-200']">
                {{ getProjectTaskCount('unassigned') }}
              </span>
            </button>
          </div>

          <!-- GROUP 1: WORK PROJECTS (2-LINE ITEM) -->
          <div class="space-y-2">
            <div class="flex items-center justify-between px-2 text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">
              <span class="flex items-center gap-1.5 text-blue-600">
                <span>💼</span>
                <span>DỰ ÁN CÔNG VIỆC</span>
              </span>
              <button
                @click="openCreateProjectModal('work')"
                class="hover:text-blue-600 p-0.5 rounded cursor-pointer text-xs"
                title="Tạo dự án mới"
              >
                +
              </button>
            </div>

            <div class="space-y-1">
              <div
                v-for="proj in workProjects"
                :key="proj.id"
                :class="[
                  'relative group rounded-xl',
                  activeProjectMenuId === proj.id ? 'z-50' : 'z-10'
                ]"
              >
                <button
                  @click="selectedProjectId = proj.id"
                  :class="[
                    'w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer border text-left',
                    selectedProjectId === proj.id
                      ? (isDarkMode ? 'bg-slate-900 text-white border-blue-500/40 font-semibold' : 'bg-blue-50/90 text-blue-950 border-blue-300 shadow-xs')
                      : (isDarkMode ? 'text-slate-400 border-transparent hover:text-slate-200 hover:bg-slate-900/50' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-slate-900')
                  ]"
                >
                  <!-- Left side: 2 Lines (Title + Description) -->
                  <div class="min-w-0 pr-2 flex-1">
                    <div class="flex items-center gap-2 mb-0.5">
                      <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: proj.color || '#2563eb' }"></span>
                      <span class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ proj.title }}</span>
                      <span v-if="proj.key" :class="['px-1.5 py-0.2 rounded text-[9px] font-mono font-bold shrink-0', isDarkMode ? 'bg-slate-800 text-slate-400' : 'bg-blue-50 text-blue-700 border border-blue-200']">
                        {{ proj.key }}
                      </span>
                    </div>

                    <div class="text-[11px] text-slate-500 truncate pl-4.5">
                      {{ proj.description || 'Dự án trọng điểm' }}
                    </div>
                  </div>

                  <!-- Right side: Count Badge -->
                  <span :class="['font-mono text-xs font-bold px-2 py-0.5 rounded-full shrink-0', isDarkMode ? 'bg-slate-800 text-slate-400' : 'bg-slate-100 text-slate-600 border border-slate-200']">
                    {{ getProjectTaskCount(proj.id) }}
                  </span>
                </button>

                <!-- 3-Dot Options Dropdown -->
                <div
                  :class="[
                    'absolute right-2 top-2.5 transition-opacity z-50',
                    activeProjectMenuId === proj.id ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'
                  ]"
                >
                  <button
                    @click.stop="activeProjectMenuId = activeProjectMenuId === proj.id ? null : proj.id"
                    :class="[
                      'p-1 rounded-md text-xs cursor-pointer border',
                      isDarkMode ? 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700' : 'bg-white text-slate-600 border-slate-200 shadow-xs hover:bg-slate-50'
                    ]"
                  >
                    •••
                  </button>

                  <div
                    v-if="activeProjectMenuId === proj.id"
                    :class="[
                      'absolute right-0 top-full mt-1.5 w-36 rounded-xl border shadow-xl p-1.5 z-50 text-xs font-medium',
                      isDarkMode ? 'bg-[#0f172a] border-slate-700 text-slate-200' : 'bg-white border-slate-200 text-slate-700'
                    ]"
                    @click.stop
                  >
                    <button
                      @click.stop="openEditProjectModal(proj)"
                      class="w-full px-2.5 py-1.5 rounded-lg text-left hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center gap-2 cursor-pointer"
                    >
                      <span>✏️</span>
                      <span>Chỉnh Sửa</span>
                    </button>
                    <button
                      @click.stop="handleDeleteProject(proj)"
                      class="w-full px-2.5 py-1.5 rounded-lg text-left text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center gap-2 cursor-pointer"
                    >
                      <span>🗑️</span>
                      <span>Xóa Dự Án</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- GROUP 2: PERSONAL PROJECTS (2-LINE ITEM) -->
          <div class="space-y-2">
            <div class="flex items-center justify-between px-2 text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">
              <span class="flex items-center gap-1.5 text-amber-600">
                <span>👤</span>
                <span>CÁ NHÂN</span>
              </span>
              <button
                @click="openCreateProjectModal('personal')"
                class="hover:text-amber-600 p-0.5 rounded cursor-pointer text-xs"
              >
                +
              </button>
            </div>

            <div class="space-y-1">
              <div
                v-for="proj in personalProjects"
                :key="proj.id"
                :class="[
                  'relative group rounded-xl',
                  activeProjectMenuId === proj.id ? 'z-50' : 'z-10'
                ]"
              >
                <button
                  @click="selectedProjectId = proj.id"
                  :class="[
                    'w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer border text-left',
                    selectedProjectId === proj.id
                      ? (isDarkMode ? 'bg-slate-900 text-white border-amber-500/40 font-semibold' : 'bg-amber-50/90 text-amber-950 border-amber-300 shadow-xs')
                      : (isDarkMode ? 'text-slate-400 border-transparent hover:text-slate-200 hover:bg-slate-900/50' : 'text-slate-700 border-transparent hover:bg-slate-50 hover:text-slate-900')
                  ]"
                >
                  <div class="min-w-0 pr-2 flex-1">
                    <div class="flex items-center gap-2 mb-0.5">
                      <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: proj.color || '#f59e0b' }"></span>
                      <span class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ proj.title }}</span>
                    </div>
                    <div class="text-[11px] text-slate-500 truncate pl-4.5">
                      {{ proj.description || 'Kế hoạch cá nhân' }}
                    </div>
                  </div>

                  <span :class="['font-mono text-xs font-bold px-2 py-0.5 rounded-full shrink-0', isDarkMode ? 'bg-slate-800 text-slate-400' : 'bg-slate-100 text-slate-600 border border-slate-200']">
                    {{ getProjectTaskCount(proj.id) }}
                  </span>
                </button>

                <div
                  :class="[
                    'absolute right-2 top-2.5 transition-opacity z-50',
                    activeProjectMenuId === proj.id ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'
                  ]"
                >
                  <button
                    @click.stop="activeProjectMenuId = activeProjectMenuId === proj.id ? null : proj.id"
                    :class="[
                      'p-1 rounded-md text-xs cursor-pointer border',
                      isDarkMode ? 'bg-slate-800 text-slate-300 border-slate-700 hover:bg-slate-700' : 'bg-white text-slate-600 border-slate-200 shadow-xs hover:bg-slate-50'
                    ]"
                  >
                    •••
                  </button>

                  <div
                    v-if="activeProjectMenuId === proj.id"
                    :class="[
                      'absolute right-0 top-full mt-1.5 w-36 rounded-xl border shadow-xl p-1.5 z-50 text-xs font-medium',
                      isDarkMode ? 'bg-[#0f172a] border-slate-700 text-slate-200' : 'bg-white border-slate-200 text-slate-700'
                    ]"
                    @click.stop
                  >
                    <button
                      @click.stop="openEditProjectModal(proj)"
                      class="w-full px-2.5 py-1.5 rounded-lg text-left hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center gap-2 cursor-pointer"
                    >
                      <span>✏️</span>
                      <span>Chỉnh Sửa</span>
                    </button>
                    <button
                      @click.stop="handleDeleteProject(proj)"
                      class="w-full px-2.5 py-1.5 rounded-lg text-left text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center gap-2 cursor-pointer"
                    >
                      <span>🗑️</span>
                      <span>Xóa Dự Án</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar Footer -->
        <div :class="['p-3.5 border-t', isDarkMode ? 'border-slate-800 bg-slate-950' : 'border-slate-200 bg-slate-50']">
          <button
            @click="openCreateProjectModal('work')"
            :class="[
              'w-full py-2.5 px-3 rounded-xl border font-semibold text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer',
              isDarkMode ? 'bg-slate-900 hover:bg-slate-800 border-slate-800 text-slate-200' : 'bg-white hover:bg-slate-100 border-slate-200 text-slate-800 shadow-xs'
            ]"
          >
            <span>+</span>
            <span>Thêm Dự Án Mới</span>
          </button>
        </div>
      </aside>

      <!-- MAIN WORKSPACE -->
      <main :class="['flex-1 flex flex-col overflow-hidden', isDarkMode ? 'bg-[#070b14]' : 'bg-[#f8fafc]']">
        <!-- Filter & Header Bar -->
        <div :class="['p-4 sm:p-5 border-b space-y-3.5 shrink-0 shadow-xs', isDarkMode ? 'bg-slate-950/60 border-slate-800' : 'bg-white border-slate-200']">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="flex items-center gap-2.5">
                <span class="text-xl">📁</span>
                <h1 :class="['text-lg sm:text-xl font-bold font-display', isDarkMode ? 'text-white' : 'text-slate-900']">
                  {{ activeProjectObject ? activeProjectObject.title : (selectedProjectId === 'unassigned' ? 'Nhiệm Vụ Chưa Phân Dự Án' : 'Tất Cả Nhiệm Vụ & Dự Án') }}
                </h1>
                <span v-if="activeProjectObject?.key" class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-mono text-xs font-bold border border-blue-200">
                  {{ activeProjectObject.key }}
                </span>
              </div>
              <p v-if="activeProjectObject?.description" class="text-xs text-slate-500 mt-1 line-clamp-1">
                {{ activeProjectObject.description }}
              </p>
            </div>

            <!-- Quick Stats Pills -->
            <div class="flex items-center gap-2.5 font-mono text-xs">
              <span :class="['px-3 py-1.5 rounded-xl border font-medium', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-700 shadow-xs']">
                <strong class="text-blue-600 font-bold text-sm">{{ filteredBoardTasks.length }}</strong> Tasks
              </span>
              <span :class="['px-3 py-1.5 rounded-xl border font-medium', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-400' : 'bg-slate-50 border-slate-200 text-slate-700 shadow-xs']">
                <strong class="text-purple-600 font-bold text-sm">{{ stats.total_story_points }}</strong> Story Pts
              </span>
            </div>
          </div>

          <!-- Quick Filters Line -->
          <div class="flex flex-wrap items-center justify-between gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 text-xs">
            <div class="flex flex-wrap items-center gap-2">
              <!-- Search -->
              <div class="relative min-w-[220px]">
                <input
                  ref="searchInputRef"
                  v-model="searchQuery"
                  type="text"
                  placeholder="Tìm kiếm task... (Phím '/')"
                  :class="[
                    'w-full border rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-blue-500 shadow-xs',
                    isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-800'
                  ]"
                />
                <span v-if="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">✕</span>
              </div>

              <!-- Issue Type Filter -->
              <select
                v-model="filterIssueType"
                :class="[
                  'border text-xs rounded-xl px-3 py-1.5 focus:outline-none cursor-pointer shadow-xs',
                  isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-300' : 'bg-white border-slate-200 text-slate-700'
                ]"
              >
                <option value="all">Tất cả loại issue</option>
                <option value="story">📖 Story</option>
                <option value="task">☑️ Task</option>
                <option value="bug">🐞 Bug</option>
                <option value="epic">⚡ Epic</option>
              </select>

              <!-- Priority Filter -->
              <select
                v-model="filterPriority"
                :class="[
                  'border text-xs rounded-xl px-3 py-1.5 focus:outline-none cursor-pointer shadow-xs',
                  isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-300' : 'bg-white border-slate-200 text-slate-700'
                ]"
              >
                <option value="all">Tất cả độ ưu tiên</option>
                <option value="urgent">🔴 Khẩn cấp</option>
                <option value="high">🟠 Ưu tiên</option>
                <option value="medium">🟡 Bình thường</option>
                <option value="low">⚪ Thấp</option>
              </select>
            </div>

            <!-- Quick Add in Bar -->
            <div class="flex items-center gap-2">
              <input
                ref="quickInputRef"
                v-model="quickInputText"
                type="text"
                placeholder="+ Thêm nhanh task mới... (Enter)"
                @keydown.enter="handleQuickCreate(null)"
                :class="[
                  'min-w-[240px] sm:min-w-[280px] border rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-blue-500 shadow-xs',
                  isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-800'
                ]"
              />
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- VIEW 1: CLEAN KANBAN BOARD (HIGH CONTRAST & CARD ELEVATION)          -->
        <!-- ===================================================================== -->
        <div v-if="currentView === 'board'" class="flex-1 p-4 sm:p-6 overflow-x-auto overflow-y-auto">
          <!-- Active Sprint Banner -->
          <div
            v-if="activeSprint"
            :class="[
              'mb-5 p-4 rounded-2xl border flex flex-wrap items-center justify-between gap-3 shadow-xs',
              isDarkMode ? 'bg-slate-900/60 border-blue-500/30' : 'bg-white border-blue-200 ring-2 ring-blue-50/50'
            ]"
          >
            <div class="flex items-center gap-3">
              <span class="w-3 h-3 rounded-full bg-blue-600 animate-pulse"></span>
              <div>
                <div class="flex items-center gap-2">
                  <span :class="['font-bold text-sm sm:text-base', isDarkMode ? 'text-white' : 'text-slate-900']">{{ activeSprint.name }}</span>
                  <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-100 text-emerald-800 font-bold border border-emerald-200">ACTIVE</span>
                </div>
                <p v-if="activeSprint.goal" class="text-xs text-slate-500 mt-0.5">{{ activeSprint.goal }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <span class="text-xs text-slate-500 font-mono">
                Hạn chót: <strong class="text-slate-800 dark:text-slate-200">{{ activeSprint.end_date || 'Chưa đặt' }}</strong>
              </span>
              <button
                @click="openCompleteSprintModal(activeSprint)"
                :class="[
                  'px-3.5 py-1.5 rounded-xl text-xs font-semibold border cursor-pointer transition-colors shadow-xs',
                  isDarkMode ? 'bg-slate-800 text-slate-200 border-slate-700 hover:bg-slate-700' : 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100'
                ]"
              >
                Hoàn Thành Sprint 🏁
              </button>
            </div>
          </div>

          <!-- 4 CLEAN KANBAN COLUMNS -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 h-full items-start">
            <!-- 1. TO DO -->
            <div
              :class="['flex flex-col border rounded-2xl p-3.5 min-h-[480px] transition-colors', isDarkMode ? 'bg-slate-950/70 border-slate-800' : 'bg-[#edf2f7] border-slate-200']"
              @dragover="onDragOverColumn($event, 'todo')"
              @drop="onDropColumn('todo')"
            >
              <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-800 px-1">
                <span class="flex items-center gap-2 font-mono text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wide">
                  <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                  <span>CẦN LÀM (TO DO)</span>
                </span>
                <span :class="['font-mono text-xs px-2.5 py-0.5 rounded-lg font-bold border', isDarkMode ? 'bg-slate-900 text-slate-400 border-slate-800' : 'bg-white text-slate-700 border-slate-200 shadow-xs']">
                  {{ todoTasks.length }}
                </span>
              </div>

              <div class="space-y-3 flex-1">
                <div
                  v-for="task in todoTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  :class="[
                    'p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 group shadow-xs',
                    isDarkMode ? 'bg-[#0f1422] border-slate-800 hover:border-blue-500/60' : 'bg-white border-slate-200/90 hover:border-blue-500 hover:shadow-md'
                  ]"
                >
                  <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                      <span>{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                      <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200">{{ task.issue_key }}</span>
                    </div>
                    <span v-if="task.story_points" class="px-2 py-0.5 rounded bg-indigo-50 border border-indigo-200 text-xs font-mono text-indigo-700 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>

                  <h4 :class="['text-sm font-semibold line-clamp-2 leading-relaxed', isDarkMode ? 'text-slate-100 group-hover:text-blue-300' : 'text-slate-900 group-hover:text-blue-700']">
                    {{ task.title }}
                  </h4>

                  <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 dark:border-slate-800/60 text-[11px]">
                    <span :class="['px-2 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                      {{ getCategoryBadge(task.category).label }}
                    </span>
                    <span :class="['px-2 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                      {{ getPriorityBadge(task.priority).label }}
                    </span>
                  </div>
                </div>

                <div v-if="todoTasks.length === 0" class="h-28 border-2 border-dashed border-slate-300 dark:border-slate-800 rounded-xl flex items-center justify-center text-xs text-slate-400">
                  Kéo thả task vào đây
                </div>
              </div>
            </div>

            <!-- 2. IN PROGRESS -->
            <div
              :class="['flex flex-col border rounded-2xl p-3.5 min-h-[480px] transition-colors', isDarkMode ? 'bg-slate-950/70 border-slate-800' : 'bg-[#edf2f7] border-slate-200']"
              @dragover="onDragOverColumn($event, 'in_progress')"
              @drop="onDropColumn('in_progress')"
            >
              <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-800 px-1">
                <span class="flex items-center gap-2 font-mono text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wide">
                  <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                  <span>ĐANG LÀM</span>
                </span>
                <span :class="['font-mono text-xs px-2.5 py-0.5 rounded-lg font-bold border', isDarkMode ? 'bg-slate-900 text-amber-400 border-slate-800' : 'bg-white text-amber-800 border-slate-200 shadow-xs']">
                  {{ inProgressTasks.length }}
                </span>
              </div>

              <div class="space-y-3 flex-1">
                <div
                  v-for="task in inProgressTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  :class="[
                    'p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 group shadow-xs',
                    isDarkMode ? 'bg-[#0f1422] border-amber-500/30 hover:border-amber-500/60' : 'bg-white border-amber-200 hover:border-amber-500 hover:shadow-md'
                  ]"
                >
                  <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                      <span>{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                      <span class="font-mono text-xs font-bold text-amber-800 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200">{{ task.issue_key }}</span>
                    </div>
                    <span v-if="task.story_points" class="px-2 py-0.5 rounded bg-indigo-50 border border-indigo-200 text-xs font-mono text-indigo-700 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>

                  <h4 :class="['text-sm font-semibold line-clamp-2 leading-relaxed', isDarkMode ? 'text-slate-100 group-hover:text-amber-300' : 'text-slate-900 group-hover:text-amber-800']">
                    {{ task.title }}
                  </h4>

                  <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 dark:border-slate-800/60 text-[11px]">
                    <span :class="['px-2 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                      {{ getCategoryBadge(task.category).label }}
                    </span>
                    <span :class="['px-2 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                      {{ getPriorityBadge(task.priority).label }}
                    </span>
                  </div>
                </div>

                <div v-if="inProgressTasks.length === 0" class="h-28 border-2 border-dashed border-slate-300 dark:border-slate-800 rounded-xl flex items-center justify-center text-xs text-slate-400">
                  Kéo thả task vào đây
                </div>
              </div>
            </div>

            <!-- 3. REVIEW -->
            <div
              :class="['flex flex-col border rounded-2xl p-3.5 min-h-[480px] transition-colors', isDarkMode ? 'bg-slate-950/70 border-slate-800' : 'bg-[#edf2f7] border-slate-200']"
              @dragover="onDragOverColumn($event, 'review')"
              @drop="onDropColumn('review')"
            >
              <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-800 px-1">
                <span class="flex items-center gap-2 font-mono text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wide">
                  <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                  <span>KIỂM THỬ (REVIEW)</span>
                </span>
                <span :class="['font-mono text-xs px-2.5 py-0.5 rounded-lg font-bold border', isDarkMode ? 'bg-slate-900 text-purple-400 border-slate-800' : 'bg-white text-purple-800 border-slate-200 shadow-xs']">
                  {{ reviewTasks.length }}
                </span>
              </div>

              <div class="space-y-3 flex-1">
                <div
                  v-for="task in reviewTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  :class="[
                    'p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 group shadow-xs',
                    isDarkMode ? 'bg-[#0f1422] border-purple-500/30 hover:border-purple-500/60' : 'bg-white border-purple-200 hover:border-purple-500 hover:shadow-md'
                  ]"
                >
                  <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                      <span>{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                      <span class="font-mono text-xs font-bold text-purple-800 bg-purple-50 px-1.5 py-0.2 rounded border border-purple-200">{{ task.issue_key }}</span>
                    </div>
                    <span v-if="task.story_points" class="px-2 py-0.5 rounded bg-indigo-50 border border-indigo-200 text-xs font-mono text-indigo-700 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>

                  <h4 :class="['text-sm font-semibold line-clamp-2 leading-relaxed', isDarkMode ? 'text-slate-100 group-hover:text-purple-300' : 'text-slate-900 group-hover:text-purple-800']">
                    {{ task.title }}
                  </h4>

                  <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 dark:border-slate-800/60 text-[11px]">
                    <span :class="['px-2 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                      {{ getCategoryBadge(task.category).label }}
                    </span>
                    <span :class="['px-2 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                      {{ getPriorityBadge(task.priority).label }}
                    </span>
                  </div>
                </div>

                <div v-if="reviewTasks.length === 0" class="h-28 border-2 border-dashed border-slate-300 dark:border-slate-800 rounded-xl flex items-center justify-center text-xs text-slate-400">
                  Kéo thả task vào đây
                </div>
              </div>
            </div>

            <!-- 4. DONE -->
            <div
              :class="['flex flex-col border rounded-2xl p-3.5 min-h-[480px] transition-colors', isDarkMode ? 'bg-slate-950/70 border-slate-800' : 'bg-[#edf2f7] border-slate-200']"
              @dragover="onDragOverColumn($event, 'done')"
              @drop="onDropColumn('done')"
            >
              <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-800 px-1">
                <span class="flex items-center gap-2 font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wide">
                  <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                  <span>ĐÃ HOÀN TẤT</span>
                </span>
                <span :class="['font-mono text-xs px-2.5 py-0.5 rounded-lg font-bold border', isDarkMode ? 'bg-slate-900 text-emerald-400 border-slate-800' : 'bg-white text-emerald-800 border-slate-200 shadow-xs']">
                  {{ doneTasks.length }}
                </span>
              </div>

              <div class="space-y-3 flex-1">
                <div
                  v-for="task in doneTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  :class="[
                    'p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 group shadow-xs opacity-90 hover:opacity-100',
                    isDarkMode ? 'bg-[#0f1422] border-emerald-500/20 hover:border-emerald-500/50' : 'bg-white border-emerald-200 hover:border-emerald-500 hover:shadow-md'
                  ]"
                >
                  <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                      <span>{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                      <span class="font-mono text-xs font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">{{ task.issue_key }}</span>
                    </div>
                    <span v-if="task.story_points" class="px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200 text-xs font-mono text-emerald-700 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>

                  <h4 :class="['text-sm font-medium line-clamp-2 line-through opacity-70', isDarkMode ? 'text-slate-200' : 'text-slate-600']">
                    {{ task.title }}
                  </h4>

                  <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 dark:border-slate-800/60 text-[11px]">
                    <span :class="['px-2 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                      {{ getCategoryBadge(task.category).label }}
                    </span>
                    <span class="text-emerald-700 font-mono font-bold">Hoàn tất ✓</span>
                  </div>
                </div>

                <div v-if="doneTasks.length === 0" class="h-28 border-2 border-dashed border-slate-300 dark:border-slate-800 rounded-xl flex items-center justify-center text-xs text-slate-400">
                  Kéo thả task vào đây
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- VIEW 2: BACKLOG SPRINT PLANNING (COLLAPSIBLE + MULTI-BAR PROGRESS)   -->
        <!-- ===================================================================== -->
        <div v-else-if="currentView === 'backlog'" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-6">
          <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
              <h2 :class="['text-base sm:text-lg font-bold font-display', isDarkMode ? 'text-white' : 'text-slate-900']">
                📦 Lập Kế Hoạch Sprint & Backlog
              </h2>
              <p class="text-xs text-slate-500 mt-0.5">
                Kéo thả các task vào từng Sprint để chuẩn bị giai đoạn phát triển.
              </p>
            </div>

            <button
              @click="openCreateSprintModal"
              class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
            >
              <span>+</span>
              <span>Tạo Sprint Mới</span>
            </button>
          </div>

          <div class="space-y-4">
            <!-- Sprint Containers -->
            <div
              v-for="sprint in sprintList"
              :key="sprint.id"
              :class="[
                'rounded-2xl border transition-all shadow-xs overflow-hidden',
                sprint.status === 'active'
                  ? (isDarkMode ? 'bg-[#0a0f1d] border-blue-500/40' : 'bg-white border-blue-300 ring-2 ring-blue-50')
                  : (isDarkMode ? 'bg-slate-950/80 border-slate-800' : 'bg-white border-slate-200')
              ]"
              @dragover="onDragOverSprint($event, sprint.id)"
              @drop="onDropSprint(sprint.id)"
            >
              <!-- Sprint Header Row -->
              <div :class="['p-4 flex flex-wrap items-center justify-between gap-3 border-b', isDarkMode ? 'bg-slate-900/60 border-slate-800' : 'bg-slate-50/80 border-slate-200']">
                <div class="flex items-center gap-3 min-w-0">
                  <!-- Collapse/Expand Toggle -->
                  <button
                    @click="toggleSprintCollapse(sprint.id)"
                    class="p-1 rounded text-slate-500 hover:text-slate-800 cursor-pointer text-xs"
                    title="Thu gọn / Mở rộng"
                  >
                    {{ collapsedSprints[sprint.id] ? '▶' : '▼' }}
                  </button>

                  <span
                    :class="[
                      'px-2.5 py-0.5 rounded-md font-mono text-[10px] font-bold border',
                      sprint.status === 'active' ? 'bg-blue-50 text-blue-700 border-blue-200' : (sprint.status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200')
                    ]"
                  >
                    {{ sprint.status.toUpperCase() }}
                  </span>

                  <h3 :class="['text-sm sm:text-base font-bold truncate', isDarkMode ? 'text-white' : 'text-slate-900']">{{ sprint.name }}</h3>

                  <span class="text-xs text-slate-500 font-mono shrink-0">
                    ({{ getSprintStats(sprint.id).totalTasks }} tasks • {{ getSprintStats(sprint.id).donePts }}/{{ getSprintStats(sprint.id).totalPts }} pts)
                  </span>
                </div>

                <div class="flex items-center gap-3">
                  <!-- Multi-Segment Progress Bar -->
                  <div class="hidden sm:flex items-center gap-2 min-w-[140px]">
                    <div class="h-2.5 flex-1 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden flex">
                      <!-- Done segment (green) -->
                      <div class="bg-emerald-500 h-full" :style="{ width: `${getSprintStats(sprint.id).donePercent}%` }"></div>
                      <!-- In progress segment (amber) -->
                      <div class="bg-amber-500 h-full" :style="{ width: `${getSprintStats(sprint.id).inProgressPercent}%` }"></div>
                      <!-- Todo segment (slate) -->
                      <div class="bg-slate-300 dark:bg-slate-700 h-full" :style="{ width: `${getSprintStats(sprint.id).todoPercent}%` }"></div>
                    </div>
                    <span class="font-mono text-[11px] font-bold text-slate-600 dark:text-slate-400">{{ getSprintStats(sprint.id).donePercent }}%</span>
                  </div>

                  <!-- Sprint Action Buttons -->
                  <div class="flex items-center gap-2">
                    <button
                      v-if="sprint.status === 'future'"
                      @click="openStartSprintModal(sprint)"
                      class="px-3 py-1 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition-colors cursor-pointer"
                    >
                      Bắt Đầu Sprint ▶
                    </button>

                    <button
                      v-if="sprint.status === 'active'"
                      @click="openCompleteSprintModal(sprint)"
                      class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-colors cursor-pointer"
                    >
                      Hoàn Thành Sprint ✓
                    </button>

                    <button
                      @click="handleDeleteSprint(sprint)"
                      class="p-1 text-slate-400 hover:text-red-600 cursor-pointer text-xs"
                      title="Xóa Sprint"
                    >
                      🗑️
                    </button>
                  </div>
                </div>
              </div>

              <!-- Sprint Tasks List (Collapsible) -->
              <div v-if="!collapsedSprints[sprint.id]" class="p-3.5 space-y-2">
                <div
                  v-for="task in getSprintTasks(sprint.id)"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  :class="[
                    'flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer shadow-xs',
                    isDarkMode ? 'bg-[#0f1422] border-slate-800 hover:border-blue-500/40' : 'bg-white border-slate-200/90 hover:border-blue-400 hover:shadow-sm'
                  ]"
                >
                  <div class="flex items-center gap-3 min-w-0">
                    <span class="text-sm">{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                    <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 shrink-0">{{ task.issue_key }}</span>
                    <span :class="['text-sm truncate font-medium', isDarkMode ? 'text-slate-200' : 'text-slate-900']">{{ task.title }}</span>
                  </div>

                  <div class="flex items-center gap-2.5 shrink-0">
                    <span :class="['px-2 py-0.5 rounded text-[11px] font-mono border', getPriorityBadge(task.priority).class]">
                      {{ task.priority }}
                    </span>
                    <span v-if="task.story_points" class="px-2 py-0.5 rounded bg-indigo-50 border border-indigo-200 text-xs font-mono text-indigo-700 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>
                </div>

                <div v-if="getSprintTasks(sprint.id).length === 0" class="py-6 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-center text-xs text-slate-400">
                  Sprint này chưa có task. Kéo thả từ Backlog vào đây.
                </div>
              </div>
            </div>

            <!-- Backlog Pool Box -->
            <div
              :class="['p-4 rounded-2xl border space-y-3 shadow-xs', isDarkMode ? 'bg-slate-950 border-slate-800' : 'bg-white border-slate-200']"
              @dragover="onDragOverSprint($event, 'backlog')"
              @drop="onDropSprint(null)"
            >
              <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                  <span class="text-lg">📦</span>
                  <h3 :class="['text-sm sm:text-base font-bold', isDarkMode ? 'text-white' : 'text-slate-900']">Backlog (Chưa Gán Sprint)</h3>
                  <span class="text-xs text-slate-500 font-mono">({{ backlogTasks.length }} tasks)</span>
                </div>
              </div>

              <div class="space-y-2">
                <div
                  v-for="task in backlogTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  :class="[
                    'flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer shadow-xs',
                    isDarkMode ? 'bg-[#0e1320] border-slate-800 hover:border-blue-500/40' : 'bg-white border-slate-200/90 hover:border-blue-400 hover:shadow-sm'
                  ]"
                >
                  <div class="flex items-center gap-3 min-w-0">
                    <span class="text-sm">{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                    <span class="font-mono text-xs font-bold text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200 shrink-0">{{ task.issue_key }}</span>
                    <span :class="['text-sm truncate font-medium', isDarkMode ? 'text-slate-200' : 'text-slate-900']">{{ task.title }}</span>
                  </div>

                  <div class="flex items-center gap-2.5 shrink-0">
                    <span :class="['px-2 py-0.5 rounded text-[11px] font-mono border', getPriorityBadge(task.priority).class]">
                      {{ task.priority }}
                    </span>
                    <span v-if="task.story_points" class="px-2 py-0.5 rounded bg-indigo-50 border border-indigo-200 text-xs font-mono text-indigo-700 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>
                </div>

                <div v-if="backlogTasks.length === 0" class="py-6 text-center text-xs text-slate-400 italic">
                  Backlog đang trống!
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- VIEW 3: ROADMAP & TIMELINE                                            -->
        <!-- ===================================================================== -->
        <div v-else-if="currentView === 'roadmap'" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-6">
          <div class="pb-3 border-b border-slate-200 dark:border-slate-800">
            <h2 :class="['text-base sm:text-lg font-bold font-display', isDarkMode ? 'text-white' : 'text-slate-900']">
              🗺️ Roadmap & Tiến Độ Các Mục Tiêu Lớn (Epics)
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
              Theo dõi tiến độ tổng thể của các Epic và Milestone theo dòng thời gian.
            </p>
          </div>

          <div class="space-y-4">
            <div
              v-for="epic in epicList"
              :key="epic.id"
              :class="['p-4 rounded-2xl border space-y-3 shadow-xs', isDarkMode ? 'bg-slate-950 border-slate-800' : 'bg-white border-slate-200']"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <span class="text-lg">⚡</span>
                  <span class="font-mono text-xs font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">{{ epic.issue_key }}</span>
                  <h3 :class="['text-sm sm:text-base font-bold', isDarkMode ? 'text-white' : 'text-slate-900']">{{ epic.title }}</h3>
                </div>

                <span class="font-mono text-xs text-slate-500">
                  {{ epic.start_date || 'Bắt đầu' }} ➔ {{ epic.due_date || 'Hạn chót' }}
                </span>
              </div>

              <!-- Progress Bar -->
              <div class="space-y-1.5">
                <div :class="['h-3 w-full rounded-full overflow-hidden p-0.5', isDarkMode ? 'bg-slate-900 border border-slate-800' : 'bg-slate-100 border border-slate-200']">
                  <div
                    class="h-full bg-gradient-to-r from-purple-600 to-blue-600 rounded-full transition-all duration-500"
                    :style="{ width: `${epic.status === 'done' ? 100 : (epic.status === 'in_progress' ? 50 : 20)}%` }"
                  ></div>
                </div>
                <div class="flex justify-between text-xs font-mono text-slate-500">
                  <span>Trạng thái: <strong class="uppercase text-slate-800 dark:text-slate-200">{{ epic.status }}</strong></span>
                  <span>{{ epic.story_points || 0 }} Story Points</span>
                </div>
              </div>
            </div>

            <div v-if="epicList.length === 0" class="py-8 text-center text-xs text-slate-400 italic">
              Chưa có Epic nào. Hãy tạo Issue loại Epic để hiển thị trên Roadmap.
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. TASK DETAIL DRAWER (RENDER MARKDOWN & CODE BLOCKS)                     -->
    <!-- ========================================================================= -->
    <div
      v-if="selectedTask"
      class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex justify-end"
      @click.self="closeTaskDrawer"
    >
      <div
        :class="[
          'w-full max-w-2xl border-l h-full flex flex-col shadow-2xl animate-slideInRight',
          isDarkMode ? 'bg-[#090d18] border-slate-800 text-slate-100' : 'bg-white border-slate-200 text-slate-900'
        ]"
      >
        <!-- Drawer Header -->
        <div :class="['px-6 py-4 border-b flex items-center justify-between', isDarkMode ? 'bg-slate-950 border-slate-800' : 'bg-slate-50 border-slate-200']">
          <div class="flex items-center gap-2.5">
            <span class="text-base">{{ getIssueTypeBadge(selectedTask.issue_type).icon }}</span>
            <span class="font-mono text-sm font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ selectedTask.issue_key }}</span>
            <span class="text-xs text-slate-400">/</span>
            <span class="text-xs text-slate-600 dark:text-slate-400 truncate max-w-[200px] font-medium">{{ selectedTask.project?.title || 'Chung' }}</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="deleteTask(selectedTask)"
              class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 text-xs cursor-pointer"
              title="Xóa Issue"
            >
              🗑️
            </button>
            <button
              @click="closeTaskDrawer"
              :class="['p-1.5 rounded-lg text-xs cursor-pointer', isDarkMode ? 'bg-slate-900 hover:bg-slate-800 text-slate-400' : 'bg-slate-200 hover:bg-slate-300 text-slate-700']"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Drawer Body -->
        <div class="flex-1 p-6 overflow-y-auto grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Left Column (Title, Formatted Markdown, Subtasks) -->
          <div class="md:col-span-2 space-y-6">
            <div>
              <input
                v-model="selectedTask.title"
                @blur="saveTaskDrawerChanges"
                :class="[
                  'w-full font-bold text-base sm:text-lg bg-transparent border-b border-transparent focus:border-blue-500 focus:outline-none py-1',
                  isDarkMode ? 'text-white' : 'text-slate-900'
                ]"
              />
            </div>

            <!-- Description Markdown & Code Render -->
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-bold text-slate-400 uppercase tracking-wider">Mô Tả Chi Tiết</span>
                <button
                  @click="isEditingDescription = !isEditingDescription"
                  class="text-xs text-blue-600 hover:underline cursor-pointer font-semibold"
                >
                  {{ isEditingDescription ? 'Xem Trước Format' : 'Chỉnh Sửa Markdown' }}
                </button>
              </div>

              <!-- Editing Mode -->
              <div v-if="isEditingDescription">
                <textarea
                  v-model="descriptionEditContent"
                  rows="8"
                  :class="[
                    'w-full p-3.5 rounded-xl border text-xs font-mono focus:outline-none focus:border-blue-500 shadow-xs',
                    isDarkMode ? 'bg-slate-900 border-slate-700 text-slate-200' : 'bg-slate-50 border-slate-300 text-slate-900'
                  ]"
                  placeholder="Nhập mô tả task bằng markdown..."
                ></textarea>
                <div class="flex justify-end mt-2">
                  <button
                    @click="isEditingDescription = false; saveTaskDrawerChanges();"
                    class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold cursor-pointer shadow-xs"
                  >
                    Lưu Mô Tả
                  </button>
                </div>
              </div>

              <!-- Rendered Markdown Mode -->
              <div
                v-else
                :class="[
                  'p-4 rounded-2xl border text-xs leading-relaxed min-h-[90px]',
                  isDarkMode ? 'bg-slate-950 border-slate-800 text-slate-300' : 'bg-slate-50/70 border-slate-200 text-slate-800'
                ]"
              >
                <div v-if="selectedTask.description" v-html="formatMarkdown(selectedTask.description)"></div>
                <div v-else class="text-slate-400 italic">
                  Chưa có mô tả chi tiết cho issue này. Bấm "Chỉnh Sửa Markdown" để thêm nội dung.
                </div>
              </div>
            </div>

            <!-- Subtasks Checklist -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-bold text-slate-400 uppercase tracking-wider">Nhiệm Vụ Con (Subtasks)</span>
                <span class="font-mono text-xs text-slate-500 font-bold">
                  {{ (selectedTask.subtasks || []).filter(s => s.done).length }}/{{ (selectedTask.subtasks || []).length }}
                </span>
              </div>

              <div class="flex gap-2">
                <input
                  v-model="newSubtaskText"
                  @keydown.enter="addSubtask"
                  placeholder="+ Thêm subtask mới... (Enter)"
                  :class="[
                    'flex-1 px-3.5 py-2 rounded-xl border text-xs focus:outline-none focus:border-blue-500 shadow-xs',
                    isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-800'
                  ]"
                />
                <button
                  @click="addSubtask"
                  class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold text-xs cursor-pointer shadow-xs"
                >
                  Thêm
                </button>
              </div>

              <div class="space-y-1.5">
                <div
                  v-for="st in selectedTask.subtasks || []"
                  :key="st.id"
                  :class="[
                    'flex items-center justify-between p-2.5 rounded-xl border text-xs shadow-xs',
                    isDarkMode ? 'bg-slate-950 border-slate-800' : 'bg-white border-slate-200'
                  ]"
                >
                  <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-0">
                    <input
                      type="checkbox"
                      :checked="st.done"
                      @change="toggleSubtask(st)"
                      class="rounded text-blue-600 focus:ring-0 cursor-pointer"
                    />
                    <span :class="['truncate font-medium', st.done ? 'line-through text-slate-400' : 'text-slate-800 dark:text-slate-200']">
                      {{ st.text }}
                    </span>
                  </label>

                  <button @click="deleteSubtask(st.id)" class="text-slate-400 hover:text-red-600 p-1 text-xs cursor-pointer">
                    ✕
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column Attributes -->
          <div :class="['space-y-4 text-xs border-t md:border-t-0 md:border-l md:pl-5', isDarkMode ? 'border-slate-800' : 'border-slate-200']">
            <!-- Status -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase">Trạng Thái</label>
              <select
                v-model="selectedTask.status"
                @change="saveTaskDrawerChanges"
                :class="[
                  'w-full border rounded-xl p-2.5 text-xs focus:outline-none focus:border-blue-500 shadow-xs font-semibold',
                  isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-800'
                ]"
              >
                <option value="todo">Cần Làm (To Do)</option>
                <option value="in_progress">Đang Thực Thi</option>
                <option value="review">Kiểm Thử (Review)</option>
                <option value="done">Đã Hoàn Tất (Done)</option>
              </select>
            </div>

            <!-- Issue Type -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase">Loại Issue</label>
              <select
                v-model="selectedTask.issue_type"
                @change="saveTaskDrawerChanges"
                :class="[
                  'w-full border rounded-xl p-2.5 text-xs focus:outline-none focus:border-blue-500 shadow-xs',
                  isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-800'
                ]"
              >
                <option value="task">☑️ Task (Công việc)</option>
                <option value="story">📖 Story (Tính năng)</option>
                <option value="bug">🐞 Bug (Lỗi)</option>
                <option value="epic">⚡ Epic (Mục tiêu lớn)</option>
              </select>
            </div>

            <!-- Story Points (7 Fibonacci Buttons Grid) -->
            <div class="space-y-1.5">
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase">Story Points (Fibonacci)</label>
              <div class="grid grid-cols-4 gap-1.5">
                <button
                  v-for="pts in [1, 2, 3, 5, 8, 13, 21]"
                  :key="pts"
                  @click="selectedTask.story_points = pts; saveTaskDrawerChanges();"
                  :class="[
                    'py-1.5 rounded-lg font-mono font-bold text-xs border transition-all cursor-pointer',
                    selectedTask.story_points === pts
                      ? 'bg-blue-600 text-white border-blue-600 shadow-xs'
                      : (isDarkMode ? 'bg-slate-900 text-slate-400 border-slate-800' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100')
                  ]"
                >
                  {{ pts }}
                </button>
              </div>
            </div>

            <!-- Sprint Link -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase">Sprint</label>
              <select
                v-model="selectedTask.sprint_id"
                @change="saveTaskDrawerChanges"
                :class="[
                  'w-full border rounded-xl p-2.5 text-xs focus:outline-none focus:border-blue-500 shadow-xs',
                  isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-800'
                ]"
              >
                <option :value="null">📦 Backlog (Chưa gán)</option>
                <option v-for="sprint in sprintList" :key="sprint.id" :value="sprint.id">
                  {{ sprint.name }}
                </option>
              </select>
            </div>

            <!-- Epic Link -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase">Gán Epic</label>
              <select
                v-model="selectedTask.epic_id"
                @change="saveTaskDrawerChanges"
                :class="[
                  'w-full border rounded-xl p-2.5 text-xs focus:outline-none focus:border-blue-500 shadow-xs',
                  isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-800'
                ]"
              >
                <option :value="null">Không thuộc Epic</option>
                <option v-for="epic in epicList" :key="epic.id" :value="epic.id">
                  ⚡ {{ epic.issue_key }} — {{ epic.title }}
                </option>
              </select>
            </div>

            <!-- Priority -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase">Độ Ưu Tiên</label>
              <select
                v-model="selectedTask.priority"
                @change="saveTaskDrawerChanges"
                :class="[
                  'w-full border rounded-xl p-2.5 text-xs focus:outline-none focus:border-blue-500 shadow-xs',
                  isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-800'
                ]"
              >
                <option value="urgent">🔴 Khẩn cấp</option>
                <option value="high">🟠 Ưu tiên</option>
                <option value="medium">🟡 Bình thường</option>
                <option value="low">⚪ Thấp</option>
              </select>
            </div>

            <!-- Due Date -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase">Hạn Chót (Due Date)</label>
              <input
                v-model="selectedTask.due_date"
                type="date"
                @change="saveTaskDrawerChanges"
                :class="[
                  'w-full border rounded-xl p-2.5 text-xs focus:outline-none focus:border-blue-500 shadow-xs',
                  isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-white border-slate-200 text-slate-800'
                ]"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. MODALS (CREATE SPRINT, START SPRINT, COMPLETE SPRINT, CREATE TASK)      -->
    <!-- ========================================================================= -->
    <!-- Modal: Create Sprint -->
    <div v-if="showSprintModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
      <div :class="['w-full max-w-md border rounded-3xl p-6 shadow-2xl space-y-4', isDarkMode ? 'bg-[#0a0f1d] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-900']">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
          <h3 class="font-bold text-sm">⚡ Tạo Sprint Scrum Mới</h3>
          <button @click="showSprintModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Tên Sprint</label>
            <input
              v-model="sprintForm.name"
              placeholder="VD: Sprint 1 — Triển Khai Tính Năng"
              :class="['w-full p-2.5 rounded-xl border focus:outline-none focus:border-blue-500', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800']"
            />
          </div>

          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Mục Tiêu (Goal)</label>
            <textarea
              v-model="sprintForm.goal"
              rows="3"
              placeholder="Mục tiêu sprint..."
              :class="['w-full p-2.5 rounded-xl border focus:outline-none focus:border-blue-500', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800']"
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
          <button @click="showSprintModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold cursor-pointer">Hủy</button>
          <button @click="handleSaveSprint" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold cursor-pointer">Tạo Sprint</button>
        </div>
      </div>
    </div>

    <!-- Modal: Start Sprint -->
    <div v-if="showStartSprintModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
      <div :class="['w-full max-w-md border rounded-3xl p-6 shadow-2xl space-y-4', isDarkMode ? 'bg-[#0a0f1d] border-blue-500/30 text-white' : 'bg-white border-blue-200 text-slate-900']">
        <h3 class="font-bold text-sm">🚀 Bắt Đầu Sprint: {{ targetSprintForAction?.name }}</h3>
        <p class="text-xs text-slate-500">Sprint sẽ được chuyển sang trạng thái <strong>ACTIVE</strong>.</p>
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
          <button @click="showStartSprintModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold cursor-pointer">Hủy</button>
          <button @click="confirmStartSprint" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold cursor-pointer">Bắt Đầu ▶</button>
        </div>
      </div>
    </div>

    <!-- Modal: Complete Sprint -->
    <div v-if="showCompleteSprintModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
      <div :class="['w-full max-w-md border rounded-3xl p-6 shadow-2xl space-y-4', isDarkMode ? 'bg-[#0a0f1d] border-emerald-500/30 text-white' : 'bg-white border-emerald-200 text-slate-900']">
        <h3 class="font-bold text-sm">🏁 Hoàn Thành Sprint: {{ targetSprintForAction?.name }}</h3>
        <p class="text-xs text-slate-500">Các task chưa xong sẽ được tự động chuyển về <strong>Backlog</strong> an toàn.</p>
        <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
          <button @click="showCompleteSprintModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold cursor-pointer">Hủy</button>
          <button @click="confirmCompleteSprint" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold cursor-pointer">Hoàn Thành ✓</button>
        </div>
      </div>
    </div>

    <!-- Modal: Create Task -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
      <div :class="['w-full max-w-lg border rounded-3xl p-6 shadow-2xl space-y-4', isDarkMode ? 'bg-[#0a0f1d] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-900']">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
          <h3 class="font-bold text-sm">✨ Tạo Task Mới</h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Tiêu Đề Task *</label>
            <input
              v-model="newTaskForm.title"
              placeholder="VD: Cập nhật giao diện Linear / Jira Modern"
              :class="['w-full p-2.5 rounded-xl border focus:outline-none focus:border-blue-500', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800']"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Loại Issue</label>
              <select
                v-model="newTaskForm.issue_type"
                :class="['w-full p-2.5 rounded-xl border focus:outline-none', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800']"
              >
                <option value="task">☑️ Task</option>
                <option value="story">📖 Story</option>
                <option value="bug">🐞 Bug</option>
                <option value="epic">⚡ Epic</option>
              </select>
            </div>

            <div>
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Story Points</label>
              <select
                v-model="newTaskForm.story_points"
                :class="['w-full p-2.5 rounded-xl border focus:outline-none', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800']"
              >
                <option :value="1">1 pt</option>
                <option :value="2">2 pts</option>
                <option :value="3">3 pts</option>
                <option :value="5">5 pts</option>
                <option :value="8">8 pts</option>
                <option :value="13">13 pts</option>
              </select>
            </div>
          </div>

          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Mô Tả</label>
            <textarea
              v-model="newTaskForm.description"
              rows="3"
              placeholder="Chi tiết công việc..."
              :class="['w-full p-2.5 rounded-xl border focus:outline-none', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800']"
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
          <button @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold cursor-pointer">Hủy</button>
          <button @click="handleCreateTask" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold cursor-pointer">Tạo Task</button>
        </div>
      </div>
    </div>

    <!-- Modal: Create / Edit Project -->
    <div v-if="showProjectModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
      <div :class="['w-full max-w-md border rounded-3xl p-6 shadow-2xl space-y-4', isDarkMode ? 'bg-[#0a0f1d] border-slate-800 text-white' : 'bg-white border-slate-200 text-slate-900']">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
          <h3 class="font-bold text-sm">
            {{ projectModalMode === 'create' ? 'Tạo Dự Án Mới' : 'Chỉnh Sửa Dự Án' }}
          </h3>
          <button @click="showProjectModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Tên Dự Án *</label>
            <input
              v-model="projectForm.title"
              placeholder="VD: Mobile App 2026"
              :class="['w-full p-2.5 rounded-xl border focus:outline-none focus:border-blue-500', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800']"
            />
          </div>

          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Mã Key Dự Án (2-5 ký tự)</label>
            <input
              v-model="projectForm.key"
              placeholder="VD: APP"
              :class="['w-full p-2.5 rounded-xl border font-mono uppercase focus:outline-none focus:border-blue-500', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800']"
            />
          </div>

          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Phân Loại</label>
            <select
              v-model="projectForm.type"
              :class="['w-full p-2.5 rounded-xl border focus:outline-none', isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-800']"
            >
              <option value="work">💼 Công Việc (Work)</option>
              <option value="personal">👤 Cá Nhân (Personal)</option>
            </select>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-200 dark:border-slate-800">
          <button @click="showProjectModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold cursor-pointer">Hủy</button>
          <button @click="handleSaveProject" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold cursor-pointer">Lưu Dự Án</button>
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
      :class="[
        'fixed inset-0 z-50 flex items-center justify-center p-4 select-none overflow-y-auto backdrop-blur-md',
        isDarkMode ? 'bg-[#04070d]/95' : 'bg-slate-900/60'
      ]"
    >
      <div
        :class="[
          'relative w-full max-w-md border rounded-3xl p-6 sm:p-8 shadow-2xl text-center z-10 transition-all duration-300',
          isPinShaking ? 'animate-bounce !border-red-500' : (isDarkMode ? 'bg-[#0a0f1d] border-slate-800' : 'bg-white border-slate-200 text-slate-800')
        ]"
      >
        <div class="flex flex-col items-center mb-6">
          <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-200 flex items-center justify-center shadow-xs mb-3">
            <span class="text-2xl">🔒</span>
          </div>

          <h2 :class="['text-lg sm:text-xl font-bold font-display', isDarkMode ? 'text-white' : 'text-slate-900']">
            BẢO MẬT TASKS WORKSPACE
          </h2>
          <p class="text-xs text-slate-500 mt-1">
            Nhập mã PIN <strong class="text-blue-600 font-mono">6 chữ số</strong> để mở khóa không gian làm việc.
          </p>
        </div>

        <!-- 6 PIN Digit Display Slots -->
        <div class="flex items-center justify-center gap-2.5 sm:gap-3 mb-6">
          <div
            v-for="i in 6"
            :key="i"
            :class="[
              'w-11 h-13 sm:w-12 sm:h-14 rounded-2xl border-2 flex items-center justify-center font-mono font-bold text-xl transition-all duration-150',
              pinInput.length >= i
                ? 'border-blue-600 bg-blue-50 text-blue-700 scale-105 shadow-xs'
                : pinInput.length === i - 1
                ? 'border-blue-400 bg-slate-50 text-slate-400 ring-2 ring-blue-100'
                : (isDarkMode ? 'border-slate-800 bg-slate-950 text-slate-600' : 'border-slate-200 bg-slate-50 text-slate-300')
            ]"
          >
            <span v-if="pinInput.length >= i" class="text-xl text-blue-600">●</span>
            <span v-else class="text-slate-300 dark:text-slate-700 text-xs">―</span>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="pinError" class="mb-4 text-xs text-red-600 bg-red-50 border border-red-200 py-2 px-3 rounded-xl">
          ⚠️ {{ pinError }}
        </div>

        <!-- Numpad -->
        <div class="grid grid-cols-3 gap-2.5 mb-6 max-w-xs mx-auto">
          <button
            v-for="num in ['1', '2', '3', '4', '5', '6', '7', '8', '9']"
            :key="num"
            @click="handleNumpadPress(num)"
            :class="[
              'h-12 rounded-2xl border font-mono font-bold text-lg transition-all active:scale-95 cursor-pointer shadow-xs',
              isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200 hover:bg-slate-800' : 'bg-slate-50 border-slate-200 text-slate-800 hover:bg-slate-100'
            ]"
          >
            {{ num }}
          </button>

          <button
            @click="handleNumpadClear"
            :class="[
              'h-12 rounded-2xl border font-mono font-bold text-xs transition-all active:scale-95 cursor-pointer',
              isDarkMode ? 'bg-slate-950 border-slate-800 text-slate-400' : 'bg-slate-100 border-slate-200 text-slate-600 hover:bg-slate-200'
            ]"
          >
            XÓA
          </button>

          <button
            @click="handleNumpadPress('0')"
            :class="[
              'h-12 rounded-2xl border font-mono font-bold text-lg transition-all active:scale-95 cursor-pointer shadow-xs',
              isDarkMode ? 'bg-slate-900 border-slate-800 text-slate-200 hover:bg-slate-800' : 'bg-slate-50 border-slate-200 text-slate-800 hover:bg-slate-100'
            ]"
          >
            0
          </button>

          <button
            @click="handleNumpadBackspace"
            :class="[
              'h-12 rounded-2xl border font-mono font-bold text-lg transition-all active:scale-95 cursor-pointer',
              isDarkMode ? 'bg-slate-950 border-slate-800 text-slate-400 hover:text-red-400' : 'bg-slate-100 border-slate-200 text-slate-600 hover:text-red-600 hover:bg-red-50'
            ]"
          >
            ⌫
          </button>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-800 text-xs">
          <a href="/" class="text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center gap-1">
            ← Về Trang Chủ
          </a>

          <button
            @click="checkPin"
            :disabled="pinInput.length !== 6"
            :class="[
              'px-5 py-2 rounded-xl font-bold font-mono text-xs transition-all flex items-center gap-1.5 shadow-xs',
              pinInput.length === 6
                ? 'bg-blue-600 hover:bg-blue-700 text-white cursor-pointer'
                : 'bg-slate-200 dark:bg-slate-800 text-slate-400 cursor-not-allowed opacity-60'
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
@keyframes slideInRight {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

.animate-slideInRight {
  animation: slideInRight 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
