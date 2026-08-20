<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import type { TaskItem } from '../composables/useTaskSync';

declare global {
  interface Window {
    desktopApi?: any;
  }
}

const props = defineProps<{ tasks: TaskItem[]; initialTask?: TaskItem | null }>();
const emit = defineEmits<{ (e: 'close'): void }>();

type Phase = 'select_task' | 'pairing' | 'loading_context' | 'configuring' | 'ready' | 'running' | 'error';
const phase = ref<Phase>('select_task');
const provider = ref('codex');
const workspace = ref('');
const taskId = ref<number | null>(props.initialTask?.id || null);
const taskSearch = ref('');
const taskHubUrl = ref('https://tasks.macatung.dev');
const pairingUrl = ref('');
const pairingMessage = ref('');
const errorMessage = ref('');
const output = ref('');
const contextPack = ref<any>(null);
const credential = ref<{ token: string; projectId: string } | null>(null);
const runId = ref<number | null>(null);
const sessionId = ref<string | null>(null);
let pollTimer: ReturnType<typeof setInterval> | undefined;
let removeOutput: (() => void) | undefined;
let removeExit: (() => void) | undefined;

const selectedTask = computed(() => props.tasks.find(task => task.id === taskId.value) || null);
const filteredTasks = computed(() => {
  const query = taskSearch.value.trim().toLowerCase();
  return props.tasks.filter(task => !query || [task.title, task.issue_key, task.project?.title].filter(Boolean).join(' ').toLowerCase().includes(query));
});
const busy = computed(() => ['pairing', 'loading_context', 'configuring', 'running'].includes(phase.value));

const chooseWorkspace = async () => {
  workspace.value = await window.desktopApi?.agent?.pickWorkspace() || workspace.value;
};

const mcpCall = (method: string, params: Record<string, any> = {}) => {
  if (!credential.value) throw new Error('Task Hub chưa được xác thực.');
  return window.desktopApi.taskHub.mcpCall(taskHubUrl.value, credential.value.token, credential.value.projectId, method, params);
};

const readMcpText = (response: any) => {
  if (response?.error) throw new Error(response.error.message || 'MCP request failed.');
  const text = response?.result?.content?.find((item: any) => item.type === 'text')?.text;
  if (!text) throw new Error('MCP không trả về dữ liệu.');
  return JSON.parse(text);
};

const stopPolling = () => {
  if (pollTimer) clearInterval(pollTimer);
  pollTimer = undefined;
};

const beginPairing = async () => {
  errorMessage.value = '';
  try {
    if (!selectedTask.value) { errorMessage.value = 'Hãy chọn đúng một task.'; return; }
    if (!selectedTask.value.project_id) { errorMessage.value = 'Task phải thuộc một project trước khi chạy agent.'; return; }
    if (!workspace.value) await chooseWorkspace();
    if (!workspace.value) { errorMessage.value = 'Hãy chọn workspace repository.'; return; }
    phase.value = 'pairing';
    const pairing = await window.desktopApi.taskHub.startPairing(taskHubUrl.value, selectedTask.value.project_id);
    pairingUrl.value = pairing.approval_url;
    pairingMessage.value = `Mã xác thực: ${pairing.code}. Đang chờ bạn approve trong browser...`;
    await window.desktopApi.openExternal(pairing.approval_url);
    const startedAt = Date.now();
    pollTimer = setInterval(async () => {
      try {
      if (Date.now() - startedAt > 10 * 60 * 1000) { stopPolling(); throw new Error('Pairing đã hết hạn.'); }
      const status = await window.desktopApi.taskHub.pollPairing(taskHubUrl.value, pairing.pairing_id, pairing.device_secret);
      if (status.status === 'approved') {
        stopPolling();
        credential.value = { token: status.mcp_token, projectId: String(status.project_id) };
        await loadContextAndPrepare();
      } else if (['denied', 'expired', 'rejected'].includes(status.status)) {
        stopPolling(); throw new Error(`Pairing ${status.status}.`);
      }
      } catch (error: any) {
        stopPolling(); phase.value = 'error'; errorMessage.value = error?.message || 'Không thể xác thực Task Hub.';
      }
    }, 2000);
  } catch (error: any) {
    stopPolling(); phase.value = 'error'; errorMessage.value = error?.message || 'Không thể kết nối Task Hub.';
  }
};

const loadContextAndPrepare = async () => {
  if (!selectedTask.value || !credential.value) return;
  phase.value = 'loading_context';
  const response = await mcpCall('tools/call', { name: 'get_context_pack', arguments: { task_id: selectedTask.value.id } });
  contextPack.value = readMcpText(response);
  phase.value = 'configuring';
  await window.desktopApi.agent.configureMcp({
    cwd: workspace.value,
    provider: provider.value,
    taskHubUrl: taskHubUrl.value,
    projectId: credential.value.projectId,
    token: credential.value.token,
  });
  const session = `${provider.value}-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
  const runResponse = await mcpCall('tools/call', { name: 'start_agent_run', arguments: {
    task_id: selectedTask.value.id,
    provider: provider.value,
    agent_session_id: session,
    repository: contextPack.value?.repository,
    branch: contextPack.value?.branch,
    context: contextPack.value,
    instruction: { contract: 'Only implement the selected task. Update lifecycle and evidence through Task Hub MCP. Do not merge or deploy.' },
  } });
  const run = readMcpText(runResponse);
  runId.value = run?.data?.id || run?.id || null;
  phase.value = 'ready';
  output.value = `Context đã nạp cho ${selectedTask.value.issue_key || `Task #${selectedTask.value.id}`}\nMCP đã cấu hình tại workspace.\nAgent run: ${runId.value || 'created'}\n`;
};

const buildPrompt = () => `You are working on exactly this Task Hub task. Do not switch tasks, merge, or deploy without human approval. Use the configured task-hub MCP server to update lifecycle and attach verification evidence. Finish in needs_review.

TASK CONTEXT PACK:
${JSON.stringify(contextPack.value, null, 2)}

AGENT RUN ID: ${runId.value}
WORKSPACE: ${workspace.value}`;

const startAgent = async () => {
  if (!selectedTask.value || !contextPack.value || phase.value !== 'ready') return;
  phase.value = 'running';
  const result = await window.desktopApi.agent.start(provider.value, workspace.value, buildPrompt());
  if (result?.mode === 'desktop') {
    output.value += '\nAntigravity desktop đã mở. Prompt đã được copy vào clipboard.\n';
  } else {
    sessionId.value = result?.sessionId || null;
    output.value += '\nAgent process đã khởi chạy.\n';
  }
};

const updateRun = async (status: string, summary?: string) => {
  if (!runId.value || !credential.value) return;
  try { await mcpCall('tools/call', { name: 'update_agent_run', arguments: { run_id: runId.value, status, summary } }); } catch (error) { console.warn('Unable to update agent run:', error); }
};

const stopAgent = async () => {
  if (sessionId.value) await window.desktopApi.agent.stop(sessionId.value);
  await updateRun('cancelled', 'Agent stopped from desktop workspace.');
  sessionId.value = null;
  phase.value = 'ready';
};

const reopenPairing = () => { if (pairingUrl.value) window.desktopApi.openExternal(pairingUrl.value); };

onMounted(() => {
  removeOutput = window.desktopApi?.agent?.onOutput((event: any) => {
    if (event.sessionId === sessionId.value) output.value += event.text;
  });
  removeExit = window.desktopApi?.agent?.onExit(async (event: any) => {
    if (event.sessionId !== sessionId.value) return;
    output.value += `\n[agent exited: ${event.code ?? 'unknown'}]\n`;
    await updateRun(event.code === 0 ? 'needs_review' : 'failed', event.code === 0 ? 'Agent process completed; awaiting human review.' : `Agent exited with code ${event.code}.`);
    sessionId.value = null;
    phase.value = event.code === 0 ? 'ready' : 'error';
  });
});

onUnmounted(() => { stopPolling(); removeOutput?.(); removeExit?.(); if (sessionId.value) window.desktopApi?.agent?.stop(sessionId.value); });
</script>

<template>
  <div class="agent-modal w-[min(66vw,560px)] max-w-[calc(100vw-180px)] h-[min(82vh,560px)] max-h-[calc(100vh-32px)] rounded-2xl border border-slate-700 bg-slate-950 text-slate-100 shadow-2xl p-4 flex flex-col gap-3 overflow-hidden" @mousedown.stop>
    <div class="flex items-center justify-between"><div><h2 class="font-bold text-base">🤖 Agent Workspace</h2><p class="text-[10px] text-slate-400">Chọn task → nạp context → cấu hình MCP → chạy agent</p></div><button class="text-slate-400 hover:text-white px-2" @click="emit('close')">✕</button></div>

    <div class="flex gap-2">
      <select v-model="provider" class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-2 text-xs" :disabled="busy"><option value="codex">Codex</option><option value="claude_code">Claude Code</option><option value="antigravity">Antigravity / agy</option></select>
      <button class="flex-1 text-left truncate bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-xs" @click="chooseWorkspace">{{ workspace || 'Chọn thư mục project...' }}</button>
    </div>

    <input v-model="taskSearch" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-xs" placeholder="Tìm task theo key, tên hoặc project..." :disabled="busy" />
    <div class="max-h-32 overflow-y-auto space-y-1 pr-1">
      <button v-for="task in filteredTasks" :key="task.id" class="w-full text-left rounded-lg border px-3 py-2 text-xs" :class="task.id === taskId ? 'border-blue-400 bg-blue-950/50' : 'border-slate-800 bg-slate-900 hover:border-slate-600'" :disabled="busy" @click="taskId = task.id">
        <div class="flex justify-between gap-2"><span class="font-semibold truncate">{{ task.issue_key || `#${task.id}` }} · {{ task.title }}</span><span class="shrink-0 text-slate-400">{{ task.status }}</span></div>
        <div class="text-[10px] text-slate-500 mt-0.5">{{ task.project?.title || 'Không có project' }} · {{ task.priority }}</div>
      </button>
      <div v-if="filteredTasks.length === 0" class="text-xs text-slate-500 py-3 text-center">Không có task đủ điều kiện.</div>
    </div>

    <div v-if="selectedTask" class="rounded-lg border border-slate-800 bg-slate-900/70 p-3 text-[10px] text-slate-300 space-y-1"><div class="font-bold text-white">{{ selectedTask.issue_key || `Task #${selectedTask.id}` }} · {{ selectedTask.title }}</div><div>{{ selectedTask.description || 'Không có mô tả.' }}</div><div v-if="selectedTask.acceptance_criteria">Acceptance: {{ selectedTask.acceptance_criteria }}</div></div>
    <div v-if="pairingMessage" class="rounded-lg border border-amber-800 bg-amber-950/30 p-2 text-[10px] text-amber-200">{{ pairingMessage }} <button v-if="pairingUrl" class="underline font-bold" @click="reopenPairing">Mở lại trang approve</button></div>
    <div v-if="errorMessage" class="rounded-lg border border-red-800 bg-red-950/30 p-2 text-[10px] text-red-200">{{ errorMessage }}</div>

    <pre class="flex-1 min-h-0 overflow-auto whitespace-pre-wrap bg-black/40 border border-slate-800 rounded-lg p-3 text-[11px] leading-relaxed text-slate-300">{{ output || 'Chọn một task để bắt đầu.' }}</pre>
    <div class="flex gap-2"><button v-if="phase === 'select_task' || phase === 'error'" class="flex-1 bg-emerald-500 disabled:opacity-40 text-slate-950 rounded-lg px-3 py-2 text-xs font-bold" :disabled="!selectedTask || busy" @click="beginPairing">{{ phase === 'error' ? 'Thử lại kết nối' : 'Đăng nhập & kết nối Task Hub' }}</button><button v-if="phase === 'ready'" class="flex-1 bg-emerald-500 text-slate-950 rounded-lg px-3 py-2 text-xs font-bold" @click="startAgent">Mở phiên agent</button><button v-if="phase === 'running'" class="flex-1 bg-red-500 text-white rounded-lg px-3 py-2 text-xs font-bold" @click="stopAgent">Dừng agent</button><span v-if="busy && phase !== 'running'" class="flex-1 rounded-lg bg-slate-900 border border-slate-800 px-3 py-2 text-xs text-slate-400 text-center">{{ phase === 'pairing' ? 'Đang chờ approve...' : phase === 'loading_context' ? 'Đang nạp context...' : 'Đang cấu hình MCP...' }}</span></div>
  </div>
</template>
