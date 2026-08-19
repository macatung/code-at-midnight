<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
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

// Sidebar State
const isSidebarOpen = ref(true);
const selectedProjectId = ref<string | number>(props.selectedProjectId || 'all');
const activeProjectMenuId = ref<number | null>(null);

// Top View Mode: Board (Active Sprint Kanban) | Backlog (Sprint Planning) | Roadmap (Gantt)
const currentView = ref<'board' | 'backlog' | 'roadmap'>('board');

// Board Swimlane Mode: 'none' | 'epic' | 'category'
const swimlaneMode = ref<'none' | 'epic' | 'category'>('none');

// Quick Filters
const searchQuery = ref('');
const filterIssueType = ref<'all' | 'story' | 'task' | 'bug' | 'epic'>('all');
const filterPriority = ref<'all' | 'urgent' | 'high' | 'medium' | 'low'>('all');
const filterEpicId = ref<string | number>('all');
const filterSprintId = ref<string | number>('active');
const filterOnlyMyTasks = ref(false);

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

// Modals & Drawer State
const selectedTask = ref<TaskItem | null>(null);
const isEditingDescription = ref(false);
const descriptionEditContent = ref('');
const showCreateModal = ref(false);
const showSprintModal = ref(false);
const showStartSprintModal = ref(false);
const showCompleteSprintModal = ref(false);
const targetSprintForAction = ref<SprintItem | null>(null);
const showDispatchModal = ref(false);
const showReviewModal = ref(false);
const isSubmitting = ref(false);
const newSubtaskText = ref('');
const newCommentText = ref('');

// Project CRUD Modal State
const showProjectModal = ref(false);
const projectModalMode = ref<'create' | 'edit'>('create');
const editingProjectId = ref<number | null>(null);
const isProjectSubmitting = ref(false);

const projectForm = ref({
  title: '',
  key: '',
  type: 'work' as 'work' | 'personal',
  color: '#00f5a0',
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

// Groups & Computed
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

const futureSprints = computed(() => {
  return sprintList.value.filter(s => s.status === 'future');
});

const completedSprints = computed(() => {
  return sprintList.value.filter(s => s.status === 'completed');
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

    // Sprint filter (default: active sprint or all if no active sprint)
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

// Backlog Pool Tasks (Tasks with no sprint assigned)
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

// Sprints with their tasks
const getSprintTasks = (sprintId: number) => {
  return taskList.value.filter(t => t.sprint_id === sprintId);
};

const getSprintStoryPoints = (sprintId: number) => {
  const tasks = getSprintTasks(sprintId);
  const total = tasks.reduce((sum, t) => sum + (t.story_points || 0), 0);
  const done = tasks.filter(t => t.status === 'done').reduce((sum, t) => sum + (t.story_points || 0), 0);
  return { total, done };
};

// Badges & Visuals
const getIssueTypeBadge = (type: string) => {
  switch (type) {
    case 'epic':
      return { label: 'EPIC', icon: '⚡', class: 'bg-purple-500/15 text-purple-400 border-purple-500/30 font-bold' };
    case 'story':
      return { label: 'STORY', icon: '📖', class: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' };
    case 'bug':
      return { label: 'BUG', icon: '🐞', class: 'bg-rose-500/15 text-rose-400 border-rose-500/30' };
    case 'task':
    default:
      return { label: 'TASK', icon: '☑️', class: 'bg-blue-500/15 text-blue-400 border-blue-500/30' };
  }
};

const getPriorityBadge = (priority: string) => {
  switch (priority) {
    case 'urgent': return { label: 'Khẩn cấp', icon: '🔴', class: 'bg-red-500/10 text-red-400 border-red-500/20' };
    case 'high': return { label: 'Ưu tiên', icon: '🟠', class: 'bg-amber-500/10 text-amber-300 border-amber-500/20' };
    case 'medium': return { label: 'Bình thường', icon: '🟡', class: 'bg-slate-800 text-slate-300 border-slate-700' };
    case 'low': return { label: 'Thấp', icon: '⚪', class: 'bg-slate-900 text-slate-500 border-slate-800' };
    default: return { label: priority, icon: '⚪', class: 'bg-slate-800 text-slate-400 border-slate-700' };
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

const getProjectTaskCount = (projectId: string | number) => {
  if (projectId === 'all') return taskList.value.length;
  if (projectId === 'unassigned') return taskList.value.filter(t => t.project_id === null).length;
  return taskList.value.filter(t => t.project_id === Number(projectId)).length;
};

// ============================================================================
// SPRINT CRUD & ACTIONS
// ============================================================================
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
      // Update local state: de-activate old ones and activate this one
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

      // Move incomplete tasks to backlog
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

// ============================================================================
// PROJECT CRUD METHODS
// ============================================================================
const openCreateProjectModal = (type: 'work' | 'personal' = 'work') => {
  projectModalMode.value = 'create';
  editingProjectId.value = null;
  projectForm.value = {
    title: '',
    key: '',
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
    key: project.key || '',
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
  if (!confirm(`Bạn có chắc muốn xóa dự án "${project.title}"?\n(Toàn bộ các nhiệm vụ thuộc dự án này sẽ được giữ lại an toàn và chuyển vào mục 'Chung')`)) {
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

// ============================================================================
// TASK / ISSUE CRUD & DRAWER
// ============================================================================
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

  // Update notes JSON for subtasks
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
  if (!confirm(`Bạn có chắc muốn xóa vĩnh viễn Issue "${task.issue_key || ''} — ${task.title}"?`)) return;

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

// ============================================================================
// DRAG & DROP HANDLERS (KANBAN & BACKLOG)
// ============================================================================
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

// Keyboard Handler
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
  <Head title="Mini Jira Workspace | Ma Cà Tưng Tasks Hub" />

  <div class="min-h-screen bg-[#060a12] text-slate-100 font-sans selection:bg-emerald-500/20 selection:text-emerald-300 flex flex-col">
    <!-- Navbar Header -->
    <header class="border-b border-slate-800/80 bg-slate-950/90 backdrop-blur-xl sticky top-0 z-40">
      <div class="w-full px-4 sm:px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <!-- Toggle Left Sidebar Button -->
          <button
            @click="isSidebarOpen = !isSidebarOpen"
            class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer"
            title="Đóng / Mở Sidebar Dự Án"
          >
            <span class="text-xs">{{ isSidebarOpen ? '◀' : '▶' }}</span>
          </button>

          <a href="/" class="flex items-center gap-2.5 group">
            <MiniMascotLogo size="md" :enable-sound="true" />
            <div>
              <div class="flex items-center gap-2">
                <span class="font-display font-bold text-white text-base sm:text-lg group-hover:text-emerald-400 transition-colors">
                  Ma Cà Tưng
                </span>
                <span class="px-2 py-0.5 rounded-md bg-blue-500/10 border border-blue-500/20 text-[10px] font-mono text-blue-400 font-bold tracking-wider">
                  MINI JIRA
                </span>
              </div>
              <span class="text-[10px] font-mono text-slate-400 block -mt-0.5 font-medium tracking-wider">
                SCRUM & KANBAN WORKSPACE
              </span>
            </div>
          </a>
        </div>

        <!-- Center View Switcher (Tabs: Board | Backlog | Roadmap) -->
        <div class="hidden md:flex items-center gap-1 bg-slate-900/90 border border-slate-800 p-1 rounded-2xl shadow-inner">
          <button
            @click="currentView = 'board'; sound.playClick();"
            :class="[
              'px-4 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all cursor-pointer',
              currentView === 'board'
                ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
                : 'text-slate-400 hover:text-white hover:bg-slate-800/60'
            ]"
          >
            <span>📋</span>
            <span>Active Sprint Board</span>
            <span class="text-[10px] opacity-60 font-mono">(1)</span>
          </button>

          <button
            @click="currentView = 'backlog'; sound.playClick();"
            :class="[
              'px-4 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all cursor-pointer',
              currentView === 'backlog'
                ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
                : 'text-slate-400 hover:text-white hover:bg-slate-800/60'
            ]"
          >
            <span>📦</span>
            <span>Backlog Planning</span>
            <span class="text-[10px] opacity-60 font-mono">(2)</span>
          </button>

          <button
            @click="currentView = 'roadmap'; sound.playClick();"
            :class="[
              'px-4 py-1.5 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all cursor-pointer',
              currentView === 'roadmap'
                ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
                : 'text-slate-400 hover:text-white hover:bg-slate-800/60'
            ]"
          >
            <span>🗺️</span>
            <span>Roadmap / Timeline</span>
            <span class="text-[10px] opacity-60 font-mono">(3)</span>
          </button>
        </div>

        <!-- Action Controls -->
        <div class="flex items-center gap-2">
          <!-- Create Issue Button -->
          <button
            @click="openCreateTaskModal"
            class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-1.5 cursor-pointer shadow-blue-600/20"
          >
            <span>+</span>
            <span>Tạo Issue</span>
          </button>

          <!-- Lock Workspace Button -->
          <button
            @click="lockWorkspace"
            class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-red-500/10 hover:border-red-500/30 text-slate-400 hover:text-red-400 border border-slate-800 text-xs font-medium transition-all flex items-center gap-1 cursor-pointer"
            title="Khóa bảo mật Workspace (Yêu cầu mã PIN 301095)"
          >
            <span>🔒</span>
            <span class="hidden md:inline text-[11px]">Khóa</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Workspace Container (Sidebar + Content View) -->
    <div class="flex-1 flex overflow-hidden">
      <!-- ========================================================================= -->
      <!-- 1. LEFT SIDEBAR (PROJECTS DIRECTORY)                                      -->
      <!-- ========================================================================= -->
      <aside
        v-if="isSidebarOpen"
        class="w-64 sm:w-72 bg-slate-950 border-r border-slate-800/80 flex flex-col justify-between shrink-0 h-[calc(100vh-4rem)] select-none"
      >
        <div class="p-3.5 space-y-4 overflow-y-auto max-h-[calc(100vh-8.5rem)] pr-2">
          <!-- All Tasks & General -->
          <div class="space-y-1">
            <button
              @click="selectedProjectId = 'all'"
              :class="[
                'w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-all cursor-pointer border text-left',
                selectedProjectId === 'all'
                  ? 'bg-slate-900 text-white border-blue-500/40 shadow-sm font-semibold'
                  : 'text-slate-400 border-transparent hover:text-white hover:bg-slate-900/60'
              ]"
            >
              <span class="flex items-center gap-2">
                <span>📁</span>
                <span>Tất Cả Dự Án</span>
              </span>
              <span class="font-mono text-[10px] text-slate-500 font-bold">{{ getProjectTaskCount('all') }}</span>
            </button>

            <button
              @click="selectedProjectId = 'unassigned'"
              :class="[
                'w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-all cursor-pointer border text-left',
                selectedProjectId === 'unassigned'
                  ? 'bg-slate-900 text-white border-slate-700 shadow-sm font-semibold'
                  : 'text-slate-400 border-transparent hover:text-white hover:bg-slate-900/60'
              ]"
            >
              <span class="flex items-center gap-2">
                <span>📦</span>
                <span>Chung (Chưa gán)</span>
              </span>
              <span class="font-mono text-[10px] text-slate-500 font-bold">{{ getProjectTaskCount('unassigned') }}</span>
            </button>
          </div>

          <!-- GROUP 1: WORK PROJECTS -->
          <div class="space-y-1.5">
            <div class="flex items-center justify-between px-2 text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider">
              <span class="flex items-center gap-1.5 text-blue-400">
                <span>💼</span>
                <span>DỰ ÁN (WORK)</span>
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

                  <div class="flex items-center gap-1.5 shrink-0">
                    <span v-if="proj.key" class="px-1.5 py-0.2 rounded bg-slate-800 text-[9px] font-mono text-slate-400 font-bold">
                      {{ proj.key }}
                    </span>
                    <span class="font-mono text-[10px] text-slate-500 font-bold">
                      {{ getProjectTaskCount(proj.id) }}
                    </span>
                  </div>
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

                <div
                  :class="[
                    'absolute right-1.5 top-1/2 -translate-y-1/2 transition-opacity z-50',
                    activeProjectMenuId === proj.id ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'
                  ]"
                >
                  <button
                    @click.stop="activeProjectMenuId = activeProjectMenuId === proj.id ? null : proj.id"
                    class="p-1 rounded-md bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs cursor-pointer border border-slate-700/60"
                  >
                    •••
                  </button>

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
            </div>
          </div>
        </div>

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
      <!-- 2. MAIN WORKSPACE AREA (HEADER CONTROLS & VIEWS)                          -->
      <!-- ========================================================================= -->
      <main class="flex-1 flex flex-col overflow-hidden bg-[#080c16]">
        <!-- Top Workspace Bar & Quick Filters -->
        <div class="p-4 sm:p-5 border-b border-slate-800/80 bg-slate-950/60 space-y-3 shrink-0">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="flex items-center gap-2">
                <span class="text-lg">⚡</span>
                <h1 class="text-base sm:text-lg font-bold font-display text-white">
                  {{ activeProjectObject ? activeProjectObject.title : (selectedProjectId === 'unassigned' ? 'Nhiệm Vụ Chưa Phân Dự Án' : 'Tất Cả Nhiệm Vụ & Dự Án') }}
                </h1>
                <span v-if="activeProjectObject?.key" class="px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400 font-mono text-xs font-bold border border-blue-500/20">
                  {{ activeProjectObject.key }}
                </span>
              </div>
              <p v-if="activeProjectObject?.description" class="text-xs text-slate-400 mt-0.5 line-clamp-1">
                {{ activeProjectObject.description }}
              </p>
            </div>

            <!-- View Switcher (Mobile) -->
            <div class="flex md:hidden items-center gap-1 bg-slate-900 border border-slate-800 p-1 rounded-xl">
              <button
                @click="currentView = 'board'"
                :class="['px-3 py-1 rounded-lg text-xs font-semibold', currentView === 'board' ? 'bg-blue-600 text-white' : 'text-slate-400']"
              >
                Board
              </button>
              <button
                @click="currentView = 'backlog'"
                :class="['px-3 py-1 rounded-lg text-xs font-semibold', currentView === 'backlog' ? 'bg-blue-600 text-white' : 'text-slate-400']"
              >
                Backlog
              </button>
              <button
                @click="currentView = 'roadmap'"
                :class="['px-3 py-1 rounded-lg text-xs font-semibold', currentView === 'roadmap' ? 'bg-blue-600 text-white' : 'text-slate-400']"
              >
                Roadmap
              </button>
            </div>
          </div>

          <!-- Quick Filters Bar -->
          <div class="flex flex-wrap items-center justify-between gap-2.5 pt-2 border-t border-slate-800/60 text-xs">
            <!-- Search & Filter Badges -->
            <div class="flex flex-wrap items-center gap-2">
              <div class="relative min-w-[200px] sm:min-w-[240px]">
                <input
                  ref="searchInputRef"
                  v-model="searchQuery"
                  type="text"
                  placeholder="Tìm kiếm issue... (Phím '/')"
                  class="w-full bg-slate-900/90 border border-slate-800 focus:border-blue-500 rounded-xl px-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none"
                />
                <span v-if="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white cursor-pointer">✕</span>
              </div>

              <!-- Issue Type Filter -->
              <select
                v-model="filterIssueType"
                class="bg-slate-900 border border-slate-800 text-slate-300 text-xs rounded-xl px-2.5 py-1.5 focus:outline-none cursor-pointer"
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
                class="bg-slate-900 border border-slate-800 text-slate-300 text-xs rounded-xl px-2.5 py-1.5 focus:outline-none cursor-pointer"
              >
                <option value="all">Tất cả độ ưu tiên</option>
                <option value="urgent">🔴 Khẩn cấp</option>
                <option value="high">🟠 Ưu tiên</option>
                <option value="medium">🟡 Bình thường</option>
                <option value="low">⚪ Thấp</option>
              </select>

              <!-- Swimlane Picker (Only for Board view) -->
              <div v-if="currentView === 'board'" class="flex items-center gap-1.5 pl-2 border-l border-slate-800">
                <span class="text-[11px] text-slate-400 font-mono">Swimlane:</span>
                <button
                  @click="swimlaneMode = 'none'"
                  :class="['px-2 py-1 rounded-lg text-[11px] font-medium transition-colors', swimlaneMode === 'none' ? 'bg-slate-800 text-white' : 'text-slate-500 hover:text-slate-300']"
                >
                  Phẳng
                </button>
                <button
                  @click="swimlaneMode = 'epic'"
                  :class="['px-2 py-1 rounded-lg text-[11px] font-medium transition-colors', swimlaneMode === 'epic' ? 'bg-purple-950/80 text-purple-300 border border-purple-800/40' : 'text-slate-500 hover:text-slate-300']"
                >
                  Theo Epic
                </button>
                <button
                  @click="swimlaneMode = 'category'"
                  :class="['px-2 py-1 rounded-lg text-[11px] font-medium transition-colors', swimlaneMode === 'category' ? 'bg-blue-950/80 text-blue-300 border border-blue-800/40' : 'text-slate-500 hover:text-slate-300']"
                >
                  Theo Phân Loại
                </button>
              </div>
            </div>

            <!-- Quick Stats Pills -->
            <div class="flex items-center gap-2 font-mono text-[11px]">
              <span class="px-2 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-400">
                <strong class="text-blue-400">{{ filteredBoardTasks.length }}</strong> Issues
              </span>
              <span class="px-2 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-400">
                <strong class="text-purple-400">{{ stats.total_story_points }}</strong> Story Points
              </span>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- VIEW 1: ACTIVE SPRINT KANBAN BOARD                                    -->
        <!-- ===================================================================== -->
        <div v-if="currentView === 'board'" class="flex-1 p-4 sm:p-5 overflow-x-auto overflow-y-auto">
          <!-- Active Sprint Banner -->
          <div v-if="activeSprint" class="mb-4 p-3 rounded-2xl bg-gradient-to-r from-blue-950/40 via-slate-900/60 to-slate-900/40 border border-blue-500/20 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <span class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></span>
              <div>
                <div class="flex items-center gap-2">
                  <span class="font-bold text-sm text-white">{{ activeSprint.name }}</span>
                  <span class="px-2 py-0.2 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-mono text-[10px] font-bold">
                    ACTIVE
                  </span>
                </div>
                <p v-if="activeSprint.goal" class="text-xs text-slate-400 line-clamp-1">{{ activeSprint.goal }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <span class="font-mono text-xs text-slate-400">
                Hạn chót: <strong class="text-slate-200">{{ activeSprint.end_date || 'Chưa đặt' }}</strong>
              </span>
              <button
                @click="openCompleteSprintModal(activeSprint)"
                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 font-semibold text-xs cursor-pointer transition-all"
              >
                Hoàn Thành Sprint 🏁
              </button>
            </div>
          </div>

          <!-- STANDARD FLAT KANBAN COLUMNS -->
          <div v-if="swimlaneMode === 'none'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 h-full items-start">
            <!-- 1. TO DO -->
            <div
              class="flex flex-col bg-slate-950/70 border border-slate-800/80 rounded-2xl p-3 min-h-[450px]"
              @dragover="onDragOverColumn($event, 'todo')"
              @drop="onDropColumn('todo')"
            >
              <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 mb-3 px-1">
                <span class="flex items-center gap-2 font-mono text-xs font-bold text-slate-300 uppercase">
                  <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                  <span>CẦN LÀM (TO DO)</span>
                </span>
                <span class="font-mono text-xs px-2 py-0.5 rounded-lg bg-slate-900 text-slate-400 font-bold border border-slate-800">
                  {{ todoTasks.length }}
                </span>
              </div>

              <div class="space-y-2.5 flex-1">
                <div
                  v-for="task in todoTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  class="p-3 rounded-xl bg-[#0f1422] border border-slate-800/90 hover:border-blue-500/40 hover:shadow-lg transition-all cursor-pointer space-y-2 group"
                >
                  <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                      <span>{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                      <span class="font-mono text-[11px] font-bold text-blue-400">{{ task.issue_key }}</span>
                    </div>
                    <span v-if="task.story_points" class="px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800 text-[10px] font-mono text-purple-300 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>

                  <h4 class="text-xs font-medium text-slate-100 group-hover:text-blue-300 line-clamp-2 leading-relaxed">
                    {{ task.title }}
                  </h4>

                  <div class="flex items-center justify-between pt-1 border-t border-slate-800/60 text-[10px]">
                    <span :class="['px-1.5 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                      {{ getCategoryBadge(task.category).label }}
                    </span>
                    <span :class="['px-1.5 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                      {{ getPriorityBadge(task.priority).label }}
                    </span>
                  </div>
                </div>

                <div v-if="todoTasks.length === 0" class="h-28 border border-dashed border-slate-800/80 rounded-xl flex items-center justify-center text-xs text-slate-600">
                  Kéo thả issue vào đây
                </div>
              </div>
            </div>

            <!-- 2. IN PROGRESS -->
            <div
              class="flex flex-col bg-slate-950/70 border border-slate-800/80 rounded-2xl p-3 min-h-[450px]"
              @dragover="onDragOverColumn($event, 'in_progress')"
              @drop="onDropColumn('in_progress')"
            >
              <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 mb-3 px-1">
                <span class="flex items-center gap-2 font-mono text-xs font-bold text-amber-400 uppercase">
                  <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                  <span>ĐANG THỰC THI</span>
                </span>
                <span class="font-mono text-xs px-2 py-0.5 rounded-lg bg-slate-900 text-amber-400 font-bold border border-slate-800">
                  {{ inProgressTasks.length }}
                </span>
              </div>

              <div class="space-y-2.5 flex-1">
                <div
                  v-for="task in inProgressTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  class="p-3 rounded-xl bg-[#0f1422] border border-amber-500/30 hover:border-amber-500/60 shadow-sm transition-all cursor-pointer space-y-2 group"
                >
                  <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                      <span>{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                      <span class="font-mono text-[11px] font-bold text-amber-400">{{ task.issue_key }}</span>
                    </div>
                    <span v-if="task.story_points" class="px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800 text-[10px] font-mono text-purple-300 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>

                  <h4 class="text-xs font-medium text-slate-100 group-hover:text-amber-300 line-clamp-2 leading-relaxed">
                    {{ task.title }}
                  </h4>

                  <div class="flex items-center justify-between pt-1 border-t border-slate-800/60 text-[10px]">
                    <span :class="['px-1.5 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                      {{ getCategoryBadge(task.category).label }}
                    </span>
                    <span :class="['px-1.5 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                      {{ getPriorityBadge(task.priority).label }}
                    </span>
                  </div>
                </div>

                <div v-if="inProgressTasks.length === 0" class="h-28 border border-dashed border-slate-800/80 rounded-xl flex items-center justify-center text-xs text-slate-600">
                  Kéo thả issue vào đây
                </div>
              </div>
            </div>

            <!-- 3. REVIEW -->
            <div
              class="flex flex-col bg-slate-950/70 border border-slate-800/80 rounded-2xl p-3 min-h-[450px]"
              @dragover="onDragOverColumn($event, 'review')"
              @drop="onDropColumn('review')"
            >
              <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 mb-3 px-1">
                <span class="flex items-center gap-2 font-mono text-xs font-bold text-purple-400 uppercase">
                  <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                  <span>KIỂM THỬ (REVIEW)</span>
                </span>
                <span class="font-mono text-xs px-2 py-0.5 rounded-lg bg-slate-900 text-purple-400 font-bold border border-slate-800">
                  {{ reviewTasks.length }}
                </span>
              </div>

              <div class="space-y-2.5 flex-1">
                <div
                  v-for="task in reviewTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  class="p-3 rounded-xl bg-[#0f1422] border border-purple-500/30 hover:border-purple-500/60 shadow-sm transition-all cursor-pointer space-y-2 group"
                >
                  <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                      <span>{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                      <span class="font-mono text-[11px] font-bold text-purple-400">{{ task.issue_key }}</span>
                    </div>
                    <span v-if="task.story_points" class="px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800 text-[10px] font-mono text-purple-300 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>

                  <h4 class="text-xs font-medium text-slate-100 group-hover:text-purple-300 line-clamp-2 leading-relaxed">
                    {{ task.title }}
                  </h4>

                  <div class="flex items-center justify-between pt-1 border-t border-slate-800/60 text-[10px]">
                    <span :class="['px-1.5 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                      {{ getCategoryBadge(task.category).label }}
                    </span>
                    <span :class="['px-1.5 py-0.5 rounded border', getPriorityBadge(task.priority).class]">
                      {{ getPriorityBadge(task.priority).label }}
                    </span>
                  </div>
                </div>

                <div v-if="reviewTasks.length === 0" class="h-28 border border-dashed border-slate-800/80 rounded-xl flex items-center justify-center text-xs text-slate-600">
                  Kéo thả issue vào đây
                </div>
              </div>
            </div>

            <!-- 4. DONE -->
            <div
              class="flex flex-col bg-slate-950/70 border border-slate-800/80 rounded-2xl p-3 min-h-[450px]"
              @dragover="onDragOverColumn($event, 'done')"
              @drop="onDropColumn('done')"
            >
              <div class="flex items-center justify-between pb-3 border-b border-slate-800/80 mb-3 px-1">
                <span class="flex items-center gap-2 font-mono text-xs font-bold text-emerald-400 uppercase">
                  <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                  <span>ĐÃ HOÀN TẤT</span>
                </span>
                <span class="font-mono text-xs px-2 py-0.5 rounded-lg bg-slate-900 text-emerald-400 font-bold border border-slate-800">
                  {{ doneTasks.length }}
                </span>
              </div>

              <div class="space-y-2.5 flex-1">
                <div
                  v-for="task in doneTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  class="p-3 rounded-xl bg-[#0f1422] border border-emerald-500/20 hover:border-emerald-500/50 transition-all cursor-pointer space-y-2 group opacity-85 hover:opacity-100"
                >
                  <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5">
                      <span>{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                      <span class="font-mono text-[11px] font-bold text-emerald-400">{{ task.issue_key }}</span>
                    </div>
                    <span v-if="task.story_points" class="px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800 text-[10px] font-mono text-emerald-400 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>

                  <h4 class="text-xs font-medium text-slate-200 line-clamp-2 line-through opacity-70">
                    {{ task.title }}
                  </h4>

                  <div class="flex items-center justify-between pt-1 border-t border-slate-800/60 text-[10px]">
                    <span :class="['px-1.5 py-0.5 rounded border', getCategoryBadge(task.category).class]">
                      {{ getCategoryBadge(task.category).label }}
                    </span>
                    <span class="text-emerald-400 font-mono font-bold">Hoàn tất ✓</span>
                  </div>
                </div>

                <div v-if="doneTasks.length === 0" class="h-28 border border-dashed border-slate-800/80 rounded-xl flex items-center justify-center text-xs text-slate-600">
                  Kéo thả issue vào đây
                </div>
              </div>
            </div>
          </div>

          <!-- SWIMLANES BY EPIC -->
          <div v-else-if="swimlaneMode === 'epic'" class="space-y-6">
            <div
              v-for="epic in epicList"
              :key="epic.id"
              class="p-4 rounded-2xl bg-slate-950/80 border border-purple-500/20 space-y-3"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="text-base">⚡</span>
                  <span class="font-mono font-bold text-purple-400 text-xs">{{ epic.issue_key }}</span>
                  <h3 class="font-bold text-sm text-white">{{ epic.title }}</h3>
                </div>
                <span class="font-mono text-xs text-slate-400">{{ epic.story_points || 0 }} pts</span>
              </div>

              <!-- 4 Mini Columns for this Epic -->
              <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div
                  v-for="status in ['todo', 'in_progress', 'review', 'done'] as TaskItem['status'][]"
                  :key="status"
                  class="p-2.5 rounded-xl bg-[#0a0e1a] border border-slate-800/80 min-h-[120px]"
                  @dragover="onDragOverColumn($event, status)"
                  @drop="onDropColumn(status)"
                >
                  <div class="text-[10px] font-mono font-bold text-slate-400 uppercase mb-2">
                    {{ status }} ({{ filteredBoardTasks.filter(t => t.epic_id === epic.id && t.status === status).length }})
                  </div>

                  <div class="space-y-2">
                    <div
                      v-for="task in filteredBoardTasks.filter(t => t.epic_id === epic.id && t.status === status)"
                      :key="task.id"
                      draggable="true"
                      @dragstart="onDragStart($event, task.id)"
                      @click="openTaskDrawer(task)"
                      class="p-2.5 rounded-lg bg-slate-900 border border-slate-800 hover:border-blue-500 text-xs cursor-pointer space-y-1"
                    >
                      <div class="flex items-center justify-between text-[10px]">
                        <span class="font-mono text-blue-400 font-bold">{{ task.issue_key }}</span>
                        <span v-if="task.story_points" class="text-purple-300 font-mono">{{ task.story_points }} pts</span>
                      </div>
                      <p class="line-clamp-2 text-[11px] text-slate-200">{{ task.title }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- VIEW 2: BACKLOG & SPRINT PLANNING                                     -->
        <!-- ===================================================================== -->
        <div v-else-if="currentView === 'backlog'" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-6">
          <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-800">
            <div>
              <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                <span>📦 Lập Kế Hoạch Sprint & Backlog</span>
              </h2>
              <p class="text-xs text-slate-400 mt-1">
                Kéo thả các Issue giữa Backlog và Sprints để phân chia khối lượng công việc.
              </p>
            </div>

            <button
              @click="openCreateSprintModal"
              class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md transition-all flex items-center gap-2 cursor-pointer shadow-blue-600/20"
            >
              <span>+</span>
              <span>Tạo Sprint Mới</span>
            </button>
          </div>

          <!-- SPRINT CARDS CONTAINER -->
          <div class="space-y-4">
            <!-- Sprints List -->
            <div
              v-for="sprint in sprintList"
              :key="sprint.id"
              :class="[
                'p-4 rounded-2xl border transition-all',
                sprint.status === 'active'
                  ? 'bg-[#0a0f1d] border-blue-500/40 shadow-lg shadow-blue-950/20'
                  : sprint.status === 'completed'
                  ? 'bg-slate-950/50 border-slate-800/60 opacity-80'
                  : 'bg-slate-950/80 border-slate-800'
              ]"
              @dragover="onDragOverSprint($event, sprint.id)"
              @drop="onDropSprint(sprint.id)"
            >
              <!-- Sprint Header -->
              <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
                <div class="flex items-center gap-3">
                  <span
                    :class="[
                      'px-2 py-0.5 rounded-md font-mono text-[10px] font-bold border',
                      sprint.status === 'active' ? 'bg-blue-500/15 text-blue-400 border-blue-500/30' : (sprint.status === 'completed' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-800 text-slate-400 border-slate-700')
                    ]"
                  >
                    {{ sprint.status.toUpperCase() }}
                  </span>

                  <h3 class="text-sm font-bold text-white">{{ sprint.name }}</h3>

                  <span class="text-xs text-slate-400 font-mono">
                    ({{ getSprintTasks(sprint.id).length }} issues • {{ getSprintStoryPoints(sprint.id).done }}/{{ getSprintStoryPoints(sprint.id).total }} pts)
                  </span>
                </div>

                <!-- Sprint Actions -->
                <div class="flex items-center gap-2">
                  <button
                    v-if="sprint.status === 'future'"
                    @click="openStartSprintModal(sprint)"
                    class="px-3 py-1 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition-colors cursor-pointer"
                  >
                    Bắt Đầu Sprint ▶
                  </button>

                  <button
                    v-if="sprint.status === 'active'"
                    @click="openCompleteSprintModal(sprint)"
                    class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-colors cursor-pointer"
                  >
                    Hoàn Thành Sprint ✓
                  </button>

                  <button
                    @click="handleDeleteSprint(sprint)"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-slate-900 transition-colors cursor-pointer text-xs"
                    title="Xóa Sprint"
                  >
                    🗑️
                  </button>
                </div>
              </div>

              <!-- Sprint Tasks List -->
              <div class="pt-3 space-y-2">
                <div
                  v-for="task in getSprintTasks(sprint.id)"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  class="flex items-center justify-between p-2.5 rounded-xl bg-[#0f1422] border border-slate-800/80 hover:border-blue-500/40 transition-all cursor-pointer"
                >
                  <div class="flex items-center gap-3 min-w-0">
                    <span class="text-xs">{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                    <span class="font-mono text-xs font-bold text-blue-400 shrink-0">{{ task.issue_key }}</span>
                    <span class="text-xs text-slate-200 truncate font-medium">{{ task.title }}</span>
                  </div>

                  <div class="flex items-center gap-2 shrink-0">
                    <span :class="['px-2 py-0.5 rounded text-[10px] font-mono uppercase border', getPriorityBadge(task.priority).class]">
                      {{ task.priority }}
                    </span>
                    <span v-if="task.story_points" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[10px] font-mono text-purple-300 font-bold">
                      {{ task.story_points }} pts
                    </span>
                    <span class="px-2 py-0.5 rounded bg-slate-900 text-[10px] font-mono text-slate-400">
                      {{ task.status }}
                    </span>
                  </div>
                </div>

                <div v-if="getSprintTasks(sprint.id).length === 0" class="py-6 border border-dashed border-slate-800 rounded-xl text-center text-xs text-slate-600">
                  Sprint này chưa có Issue. Kéo thả từ Backlog vào đây.
                </div>
              </div>
            </div>

            <!-- BACKLOG POOL BOX -->
            <div
              class="p-4 rounded-2xl bg-slate-950 border border-slate-800/90 space-y-3"
              @dragover="onDragOverSprint($event, 'backlog')"
              @drop="onDropSprint(null)"
            >
              <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                  <span class="text-base">📦</span>
                  <h3 class="text-sm font-bold text-white">Backlog Chưa Gán Sprint</h3>
                  <span class="text-xs text-slate-400 font-mono">({{ backlogTasks.length }} issues)</span>
                </div>

                <!-- Quick add to backlog -->
                <div class="flex items-center gap-2 min-w-[280px]">
                  <input
                    ref="quickInputRef"
                    v-model="quickInputText"
                    type="text"
                    placeholder="+ Thêm nhanh vào Backlog... (Enter)"
                    @keydown.enter="handleQuickCreate(null)"
                    class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-1 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-blue-500"
                  />
                </div>
              </div>

              <div class="space-y-2">
                <div
                  v-for="task in backlogTasks"
                  :key="task.id"
                  draggable="true"
                  @dragstart="onDragStart($event, task.id)"
                  @click="openTaskDrawer(task)"
                  class="flex items-center justify-between p-2.5 rounded-xl bg-[#0e1320] border border-slate-800/80 hover:border-blue-500/40 transition-all cursor-pointer"
                >
                  <div class="flex items-center gap-3 min-w-0">
                    <span class="text-xs">{{ getIssueTypeBadge(task.issue_type).icon }}</span>
                    <span class="font-mono text-xs font-bold text-slate-400 shrink-0">{{ task.issue_key }}</span>
                    <span class="text-xs text-slate-200 truncate font-medium">{{ task.title }}</span>
                  </div>

                  <div class="flex items-center gap-2 shrink-0">
                    <span :class="['px-2 py-0.5 rounded text-[10px] font-mono uppercase border', getPriorityBadge(task.priority).class]">
                      {{ task.priority }}
                    </span>
                    <span v-if="task.story_points" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-[10px] font-mono text-purple-300 font-bold">
                      {{ task.story_points }} pts
                    </span>
                  </div>
                </div>

                <div v-if="backlogTasks.length === 0" class="py-6 text-center text-xs text-slate-600 italic">
                  Backlog đang trống!
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ===================================================================== -->
        <!-- VIEW 3: ROADMAP & TIMELINE GANTT                                      -->
        <!-- ===================================================================== -->
        <div v-else-if="currentView === 'roadmap'" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-6">
          <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
              <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                <span>🗺️ Roadmap & Timeline Gantt Chart</span>
              </h2>
              <p class="text-xs text-slate-400 mt-1">
                Theo dõi tiến độ tổng thể của các Epic và tính năng trọng tâm theo dòng thời gian.
              </p>
            </div>
          </div>

          <!-- Gantt Timeline Bars -->
          <div class="space-y-4">
            <div
              v-for="epic in epicList"
              :key="epic.id"
              class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                  <span class="text-lg">⚡</span>
                  <span class="font-mono text-xs font-bold text-purple-400">{{ epic.issue_key }}</span>
                  <h3 class="text-sm font-bold text-white">{{ epic.title }}</h3>
                </div>

                <span class="font-mono text-xs text-slate-400">
                  {{ epic.start_date || 'Bắt đầu' }} ➔ {{ epic.due_date || 'Hạn chót' }}
                </span>
              </div>

              <!-- Progress Bar -->
              <div class="space-y-1">
                <div class="h-3 w-full bg-slate-900 rounded-full overflow-hidden border border-slate-800 p-0.5">
                  <div
                    class="h-full bg-gradient-to-r from-purple-500 to-blue-500 rounded-full transition-all duration-500"
                    :style="{ width: `${epic.status === 'done' ? 100 : (epic.status === 'in_progress' ? 50 : 15)}%` }"
                  ></div>
                </div>
                <div class="flex justify-between text-[10px] font-mono text-slate-500">
                  <span>Trạng thái: <strong class="text-slate-300 uppercase">{{ epic.status }}</strong></span>
                  <span>{{ epic.story_points || 0 }} Story Points</span>
                </div>
              </div>
            </div>

            <div v-if="epicList.length === 0" class="py-12 text-center text-xs text-slate-500 italic">
              Chưa có Epic nào được tạo. Hãy tạo Issue loại Epic để hiển thị trên Roadmap.
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. JIRA FULL DETAIL DRAWER                                                -->
    <!-- ========================================================================= -->
    <div
      v-if="selectedTask"
      class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex justify-end"
      @click.self="closeTaskDrawer"
    >
      <div class="w-full max-w-2xl bg-[#090d18] border-l border-slate-800 h-full flex flex-col shadow-2xl animate-slideInRight">
        <!-- Drawer Header -->
        <div class="px-6 py-4 border-b border-slate-800/90 flex items-center justify-between bg-slate-950">
          <div class="flex items-center gap-3">
            <span class="text-base">{{ getIssueTypeBadge(selectedTask.issue_type).icon }}</span>
            <span class="font-mono text-sm font-bold text-blue-400">{{ selectedTask.issue_key }}</span>
            <span class="text-xs text-slate-500">/</span>
            <span class="text-xs text-slate-400 truncate max-w-[200px]">{{ selectedTask.project?.title || 'Chung' }}</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="deleteTask(selectedTask)"
              class="p-2 rounded-xl bg-slate-900 hover:bg-red-500/20 text-slate-400 hover:text-red-400 text-xs transition-colors cursor-pointer"
              title="Xóa Issue"
            >
              🗑️
            </button>
            <button
              @click="closeTaskDrawer"
              class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white text-xs transition-colors cursor-pointer"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Drawer Body (2-Column Jira Layout) -->
        <div class="flex-1 p-6 overflow-y-auto grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Left Main Column (Title, Description, Subtasks) -->
          <div class="md:col-span-2 space-y-6">
            <!-- Title -->
            <div>
              <input
                v-model="selectedTask.title"
                @blur="saveTaskDrawerChanges"
                class="w-full font-bold text-base sm:text-lg text-white bg-transparent border-b border-transparent hover:border-slate-700 focus:border-blue-500 focus:outline-none py-1"
              />
            </div>

            <!-- Description Markdown -->
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-bold text-slate-400 uppercase">Mô Tả Chi Tiết (Markdown)</span>
                <button
                  @click="isEditingDescription = !isEditingDescription"
                  class="text-[11px] text-blue-400 hover:underline cursor-pointer"
                >
                  {{ isEditingDescription ? 'Xem trước' : 'Chỉnh sửa' }}
                </button>
              </div>

              <div v-if="isEditingDescription">
                <textarea
                  v-model="descriptionEditContent"
                  rows="6"
                  class="w-full p-3 rounded-xl bg-slate-900 border border-slate-700 text-xs text-slate-200 font-mono focus:outline-none focus:border-blue-500"
                  placeholder="Nhập mô tả task bằng markdown..."
                ></textarea>
                <div class="flex justify-end mt-1.5">
                  <button
                    @click="isEditingDescription = false; saveTaskDrawerChanges();"
                    class="px-3 py-1 rounded-lg bg-blue-600 text-white text-xs font-semibold cursor-pointer"
                  >
                    Lưu Mô Tả
                  </button>
                </div>
              </div>

              <div
                v-else
                class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 prose prose-invert max-w-none leading-relaxed min-h-[80px]"
              >
                <div v-if="selectedTask.description" class="whitespace-pre-wrap font-sans">
                  {{ selectedTask.description }}
                </div>
                <div v-else class="text-slate-600 italic">
                  Chưa có mô tả chi tiết cho issue này. Bấm "Chỉnh sửa" để thêm nội dung.
                </div>
              </div>
            </div>

            <!-- Subtasks Checklist -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-xs font-mono font-bold text-slate-400 uppercase">Danh Sách Nhiệm Vụ Con (Subtasks)</span>
                <span class="font-mono text-xs text-slate-500">
                  {{ (selectedTask.subtasks || []).filter(s => s.done).length }}/{{ (selectedTask.subtasks || []).length }}
                </span>
              </div>

              <!-- Subtasks Input -->
              <div class="flex gap-2">
                <input
                  v-model="newSubtaskText"
                  @keydown.enter="addSubtask"
                  placeholder="+ Thêm subtask mới... (Enter)"
                  class="flex-1 px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:outline-none focus:border-blue-500"
                />
                <button
                  @click="addSubtask"
                  class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold cursor-pointer"
                >
                  Thêm
                </button>
              </div>

              <div class="space-y-1.5">
                <div
                  v-for="st in selectedTask.subtasks || []"
                  :key="st.id"
                  class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950 border border-slate-800/80 text-xs"
                >
                  <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-0">
                    <input
                      type="checkbox"
                      :checked="st.done"
                      @change="toggleSubtask(st)"
                      class="rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-0 cursor-pointer"
                    />
                    <span :class="['truncate', st.done ? 'line-through text-slate-500' : 'text-slate-200']">
                      {{ st.text }}
                    </span>
                  </label>

                  <button
                    @click="deleteSubtask(st.id)"
                    class="text-slate-600 hover:text-red-400 p-1 text-xs cursor-pointer"
                  >
                    ✕
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Sidebar Metadata Column -->
          <div class="space-y-4 text-xs border-t md:border-t-0 md:border-l border-slate-800/80 md:pl-6">
            <!-- Status -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-500 font-bold uppercase">Trạng Thái</label>
              <select
                v-model="selectedTask.status"
                @change="saveTaskDrawerChanges"
                class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2 text-slate-200 focus:outline-none focus:border-blue-500"
              >
                <option value="todo">Cần Làm (To Do)</option>
                <option value="in_progress">Đang Thực Thi</option>
                <option value="review">Kiểm Thử (Review)</option>
                <option value="done">Đã Hoàn Tất (Done)</option>
              </select>
            </div>

            <!-- Issue Type -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-500 font-bold uppercase">Loại Issue</label>
              <select
                v-model="selectedTask.issue_type"
                @change="saveTaskDrawerChanges"
                class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2 text-slate-200 focus:outline-none focus:border-blue-500"
              >
                <option value="task">☑️ Task (Công việc)</option>
                <option value="story">📖 Story (Tính năng)</option>
                <option value="bug">🐞 Bug (Lỗi)</option>
                <option value="epic">⚡ Epic (Mục tiêu lớn)</option>
              </select>
            </div>

            <!-- Story Points -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-500 font-bold uppercase">Story Points (Fibonacci)</label>
              <div class="grid grid-cols-4 gap-1">
                <button
                  v-for="pts in [1, 2, 3, 5, 8, 13, 21]"
                  :key="pts"
                  @click="selectedTask.story_points = pts; saveTaskDrawerChanges();"
                  :class="[
                    'py-1 rounded-lg font-mono font-bold text-xs border transition-all cursor-pointer',
                    selectedTask.story_points === pts
                      ? 'bg-purple-600 text-white border-purple-500 shadow-sm'
                      : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white'
                  ]"
                >
                  {{ pts }}
                </button>
              </div>
            </div>

            <!-- Sprint Link -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-500 font-bold uppercase">Gán Sprint</label>
              <select
                v-model="selectedTask.sprint_id"
                @change="saveTaskDrawerChanges"
                class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2 text-slate-200 focus:outline-none focus:border-blue-500"
              >
                <option :value="null">📦 Backlog (Chưa gán Sprint)</option>
                <option v-for="sprint in sprintList" :key="sprint.id" :value="sprint.id">
                  {{ sprint.name }} ({{ sprint.status }})
                </option>
              </select>
            </div>

            <!-- Epic Link -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-500 font-bold uppercase">Gán Epic</label>
              <select
                v-model="selectedTask.epic_id"
                @change="saveTaskDrawerChanges"
                class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2 text-slate-200 focus:outline-none focus:border-blue-500"
              >
                <option :value="null">Không thuộc Epic nào</option>
                <option v-for="epic in epicList" :key="epic.id" :value="epic.id">
                  ⚡ {{ epic.issue_key }} — {{ epic.title }}
                </option>
              </select>
            </div>

            <!-- Priority -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-500 font-bold uppercase">Mức Độ Ưu Tiên</label>
              <select
                v-model="selectedTask.priority"
                @change="saveTaskDrawerChanges"
                class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2 text-slate-200 focus:outline-none focus:border-blue-500"
              >
                <option value="urgent">🔴 Khẩn cấp (Urgent)</option>
                <option value="high">🟠 Ưu tiên cao (High)</option>
                <option value="medium">🟡 Bình thường (Medium)</option>
                <option value="low">⚪ Thấp (Low)</option>
              </select>
            </div>

            <!-- Due Date -->
            <div class="space-y-1">
              <label class="font-mono text-[10px] text-slate-500 font-bold uppercase">Hạn Chót (Due Date)</label>
              <input
                v-model="selectedTask.due_date"
                type="date"
                @change="saveTaskDrawerChanges"
                class="w-full bg-slate-900 border border-slate-800 rounded-xl p-2 text-slate-200 focus:outline-none focus:border-blue-500"
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
    <div v-if="showSprintModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="w-full max-w-md bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <h3 class="font-bold text-sm text-white flex items-center gap-2">
            <span>⚡ Tạo Sprint Scrum Mới</span>
          </h3>
          <button @click="showSprintModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Tên Sprint</label>
            <input
              v-model="sprintForm.name"
              placeholder="VD: Sprint 1 — Core Routing & AI"
              class="w-full p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 focus:outline-none focus:border-blue-500"
            />
          </div>

          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Mục Tiêu Sprint (Goal)</label>
            <textarea
              v-model="sprintForm.goal"
              rows="3"
              placeholder="Mục tiêu trọng tâm của sprint này..."
              class="w-full p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 focus:outline-none focus:border-blue-500"
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
          <button
            @click="showSprintModal = false"
            class="px-4 py-2 rounded-xl bg-slate-900 text-slate-400 hover:text-white text-xs font-semibold cursor-pointer"
          >
            Hủy
          </button>
          <button
            @click="handleSaveSprint"
            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold cursor-pointer"
          >
            Tạo Sprint
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Start Sprint -->
    <div v-if="showStartSprintModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="w-full max-w-md bg-[#0a0f1d] border border-blue-500/30 rounded-3xl p-6 shadow-2xl space-y-4">
        <h3 class="font-bold text-sm text-white flex items-center gap-2">
          <span>🚀 Bắt Đầu Sprint: {{ targetSprintForAction?.name }}</span>
        </h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Sprint này sẽ chuyển sang trạng thái <strong>ACTIVE</strong> với thời lượng chuẩn 2 tuần.
        </p>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
          <button
            @click="showStartSprintModal = false"
            class="px-4 py-2 rounded-xl bg-slate-900 text-slate-400 text-xs font-semibold cursor-pointer"
          >
            Hủy
          </button>
          <button
            @click="confirmStartSprint"
            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold cursor-pointer"
          >
            Bắt Đầu Ngay ▶
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Complete Sprint -->
    <div v-if="showCompleteSprintModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="w-full max-w-md bg-[#0a0f1d] border border-emerald-500/30 rounded-3xl p-6 shadow-2xl space-y-4">
        <h3 class="font-bold text-sm text-white flex items-center gap-2">
          <span>🏁 Hoàn Thành Sprint: {{ targetSprintForAction?.name }}</span>
        </h3>
        <p class="text-xs text-slate-400 leading-relaxed">
          Sprint sẽ được đánh dấu <strong>COMPLETED</strong>. Tất cả các issue chưa hoàn tất sẽ được tự động chuyển về <strong>Backlog</strong> an toàn.
        </p>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
          <button
            @click="showCompleteSprintModal = false"
            class="px-4 py-2 rounded-xl bg-slate-900 text-slate-400 text-xs font-semibold cursor-pointer"
          >
            Hủy
          </button>
          <button
            @click="confirmCompleteSprint"
            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold cursor-pointer"
          >
            Xác Nhận Hoàn Thành ✓
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Create Task / Issue -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="w-full max-w-lg bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <h3 class="font-bold text-sm text-white flex items-center gap-2">
            <span>✨ Tạo Issue / Nhiệm Vụ Mới</span>
          </h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Tiêu Đề Issue *</label>
            <input
              v-model="newTaskForm.title"
              placeholder="VD: Tối ưu hóa truy vấn Redis Cache"
              class="w-full p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 focus:outline-none focus:border-blue-500"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Loại Issue</label>
              <select
                v-model="newTaskForm.issue_type"
                class="w-full p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 focus:outline-none"
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
                class="w-full p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 focus:outline-none"
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
              class="w-full p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 focus:outline-none"
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
          <button
            @click="showCreateModal = false"
            class="px-4 py-2 rounded-xl bg-slate-900 text-slate-400 text-xs font-semibold cursor-pointer"
          >
            Hủy
          </button>
          <button
            @click="handleCreateTask"
            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold cursor-pointer"
          >
            Tạo Issue
          </button>
        </div>
      </div>
    </div>

    <!-- Modal: Create / Edit Project -->
    <div v-if="showProjectModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="w-full max-w-md bg-[#0a0f1d] border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
          <h3 class="font-bold text-sm text-white">
            {{ projectModalMode === 'create' ? 'Tạo Dự Án Mới' : 'Chỉnh Sửa Dự Án' }}
          </h3>
          <button @click="showProjectModal = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Tên Dự Án *</label>
            <input
              v-model="projectForm.title"
              placeholder="VD: Cloud Platform 2026"
              class="w-full p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 focus:outline-none focus:border-blue-500"
            />
          </div>

          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Project Key (2-5 Ký tự viết hoa)</label>
            <input
              v-model="projectForm.key"
              placeholder="VD: CLOUD"
              class="w-full p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 font-mono focus:outline-none focus:border-blue-500 uppercase"
            />
          </div>

          <div>
            <label class="font-mono text-[10px] text-slate-400 font-bold uppercase block mb-1">Phân Loại</label>
            <select
              v-model="projectForm.type"
              class="w-full p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 focus:outline-none"
            >
              <option value="work">💼 Công Việc (Work)</option>
              <option value="personal">👤 Cá Nhân (Personal)</option>
            </select>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-slate-800">
          <button
            @click="showProjectModal = false"
            class="px-4 py-2 rounded-xl bg-slate-900 text-slate-400 text-xs font-semibold cursor-pointer"
          >
            Hủy
          </button>
          <button
            @click="handleSaveProject"
            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold cursor-pointer"
          >
            Lưu Dự Án
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
      <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none"></div>
      <div class="absolute -bottom-40 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-blue-500/10 rounded-full blur-[140px] pointer-events-none"></div>

      <div
        :class="[
          'relative w-full max-w-md bg-[#0a0f1d] border border-slate-800/90 rounded-3xl p-6 sm:p-8 shadow-2xl text-center z-10 transition-all duration-300',
          isPinShaking ? 'animate-bounce !border-red-500/80 shadow-red-500/20' : 'border-slate-800'
        ]"
      >
        <div class="flex flex-col items-center mb-6">
          <div class="relative mb-3">
            <div class="w-16 h-16 rounded-2xl bg-slate-900 border border-blue-500/30 flex items-center justify-center shadow-lg shadow-blue-500/10">
              <span class="text-3xl animate-pulse">🔒</span>
            </div>
            <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-blue-500 border-2 border-[#0a0f1d] flex items-center justify-center text-[10px] text-white font-bold">
              ✓
            </div>
          </div>

          <h2 class="text-lg sm:text-xl font-bold font-display text-white tracking-wide flex items-center gap-2">
            <span>BẢO MẬT JIRA WORKSPACE</span>
          </h2>
          <p class="text-xs text-slate-400 mt-1.5 max-w-xs leading-relaxed">
            Khu vực quản trị tác vụ. Vui lòng nhập mã PIN <strong class="text-blue-400 font-mono">6 chữ số</strong> để mở khóa.
          </p>
        </div>

        <div class="flex items-center justify-center gap-2.5 sm:gap-3.5 mb-6">
          <div
            v-for="i in 6"
            :key="i"
            :class="[
              'w-11 h-14 sm:w-12 sm:h-14 rounded-2xl border-2 flex items-center justify-center font-mono font-bold text-xl transition-all duration-200 shadow-inner',
              pinInput.length >= i
                ? 'border-blue-400 bg-blue-500/10 text-blue-300 shadow-blue-500/20 scale-105'
                : pinInput.length === i - 1
                ? 'border-slate-600 bg-slate-900/80 text-slate-400 ring-2 ring-blue-500/30'
                : 'border-slate-800 bg-slate-950/60 text-slate-600'
            ]"
          >
            <span v-if="pinInput.length >= i" class="text-xl text-blue-400">●</span>
            <span v-else class="text-slate-700 text-xs">―</span>
          </div>
        </div>

        <div v-if="pinError" class="mb-4 text-xs text-red-400 font-medium bg-red-500/10 border border-red-500/20 py-2 px-3 rounded-xl flex items-center justify-center gap-1.5">
          <span>⚠️</span>
          <span>{{ pinError }}</span>
        </div>

        <div class="grid grid-cols-3 gap-2.5 sm:gap-3 mb-6 max-w-xs mx-auto">
          <button
            v-for="num in ['1', '2', '3', '4', '5', '6', '7', '8', '9']"
            :key="num"
            @click="handleNumpadPress(num)"
            class="h-12 sm:h-13 rounded-2xl bg-slate-900/90 hover:bg-slate-800 hover:border-blue-500/40 active:scale-95 border border-slate-800 text-slate-200 font-mono font-bold text-lg transition-all cursor-pointer shadow-sm"
          >
            {{ num }}
          </button>

          <button
            @click="handleNumpadClear"
            class="h-12 sm:h-13 rounded-2xl bg-slate-950/80 hover:bg-slate-900 active:scale-95 border border-slate-800 text-slate-400 hover:text-slate-200 font-mono font-bold text-xs transition-all cursor-pointer"
          >
            XÓA
          </button>

          <button
            @click="handleNumpadPress('0')"
            class="h-12 sm:h-13 rounded-2xl bg-slate-900/90 hover:bg-slate-800 hover:border-blue-500/40 active:scale-95 border border-slate-800 text-slate-200 font-mono font-bold text-lg transition-all cursor-pointer shadow-sm"
          >
            0
          </button>

          <button
            @click="handleNumpadBackspace"
            class="h-12 sm:h-13 rounded-2xl bg-slate-950/80 hover:bg-slate-900 active:scale-95 border border-slate-800 text-slate-400 hover:text-red-400 font-mono font-bold text-lg transition-all cursor-pointer flex items-center justify-center"
          >
            ⌫
          </button>
        </div>

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
                ? 'bg-blue-600 hover:bg-blue-500 text-white shadow-blue-500/20 cursor-pointer'
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
@keyframes slideInRight {
  from {
    transform: translateX(100%);
  }
  to {
    transform: translateX(0);
  }
}

.animate-slideInRight {
  animation: slideInRight 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
