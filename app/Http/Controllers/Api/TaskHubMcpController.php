<?php

namespace App\Http\Controllers\Api;

use App\Models\AgentRun;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskHubContextPackService;
use Illuminate\Http\Request;
use App\Services\GithubProjectIntegrationService;

class TaskHubMcpController extends ApiAgentRunController
{
    public function handle(Request $request, TaskHubContextPackService $contextService)
    {
        $payload = $request->json()->all();
        if (!$this->validProjectOrWorkspaceToken($request, $payload)) {
            return response()->json(['jsonrpc' => '2.0', 'error' => ['code' => -32001, 'message' => 'Invalid project or workspace MCP token']], 401);
        }

        $id = $payload['id'] ?? null;
        $method = $payload['method'] ?? '';
        $params = $payload['params'] ?? [];

        try {
            $result = match ($method) {
                'initialize' => ['protocolVersion' => '2024-11-05', 'serverInfo' => ['name' => 'task-hub', 'version' => '1.0.0'], 'capabilities' => ['tools' => (object) []]],
                'notifications/initialized' => null,
                'tools/list' => ['tools' => $this->tools()],
                'tools/call' => $this->callTool($params, $contextService),
                default => throw new \InvalidArgumentException('Method not found: ' . $method),
            };
            $response = ['jsonrpc' => '2.0', 'id' => $id, 'result' => $result];
            return response()->json($response);
        } catch (\Throwable $e) {
            $message = app()->environment('production') ? 'Tool execution failed.' : $e->getMessage();
            return response()->json(['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => -32000, 'message' => $message]], 200);
        }
    }

    private function tools(): array
    {
        return [
            ['name' => 'get_work_item', 'description' => 'Read a Task Hub work item.', 'inputSchema' => ['type' => 'object', 'properties' => ['task_id' => ['type' => 'integer']], 'required' => ['task_id']]],
            ['name' => 'get_context_pack', 'description' => 'Build the current context pack for a task.', 'inputSchema' => ['type' => 'object', 'properties' => ['task_id' => ['type' => 'integer']], 'required' => ['task_id']]],
            ['name' => 'start_agent_run', 'description' => 'Create an auditable agent run.', 'inputSchema' => ['type' => 'object', 'properties' => ['task_id' => ['type' => 'integer'], 'provider' => ['type' => 'string'], 'agent_session_id' => ['type' => 'string']], 'required' => ['provider']]],
            ['name' => 'update_agent_run', 'description' => 'Update agent lifecycle and repository references.', 'inputSchema' => ['type' => 'object', 'properties' => ['run_id' => ['type' => 'integer'], 'status' => ['type' => 'string'], 'summary' => ['type' => 'string']], 'required' => ['run_id']]],
            ['name' => 'attach_verification_evidence', 'description' => 'Attach test/build/security evidence to an agent run.', 'inputSchema' => ['type' => 'object', 'properties' => ['run_id' => ['type' => 'integer'], 'evidence_type' => ['type' => 'string'], 'status' => ['type' => 'string'], 'command' => ['type' => 'string'], 'summary' => ['type' => 'string']], 'required' => ['run_id', 'evidence_type', 'status']]],
            ['name' => 'request_human_approval', 'description' => 'Request human approval after evidence is attached.', 'inputSchema' => ['type' => 'object', 'properties' => ['task_id' => ['type' => 'integer']], 'required' => ['task_id']]],
            ['name' => 'get_next_action', 'description' => 'Return the next smallest actionable task.', 'inputSchema' => ['type' => 'object', 'properties' => []]],
        ];
    }

    private function callTool(array $params, TaskHubContextPackService $contextService): array
    {
        $name = $params['name'] ?? '';
        $args = $params['arguments'] ?? [];
        $data = match ($name) {
            'get_work_item' => Task::with(['project', 'sprint', 'agentRuns.evidence'])->findOrFail($args['task_id']),
            'get_context_pack' => $contextService->build(Task::findOrFail($args['task_id']), $args),
            'start_agent_run' => $this->store(Request::create('/', 'POST', ['task_id' => $args['task_id'] ?? null, 'provider' => $args['provider'], 'agent_session_id' => $args['agent_session_id'] ?? null]), $contextService)->getData(true),
            'update_agent_run' => $this->update(Request::create('/', 'PATCH', $args), AgentRun::findOrFail($args['run_id']))->getData(true),
            'attach_verification_evidence' => $this->evidence(Request::create('/', 'POST', $args), AgentRun::findOrFail($args['run_id']))->getData(true),
            'request_human_approval' => $this->approve(Task::findOrFail($args['task_id']))->getData(true),
            'get_next_action' => ['success' => true, 'data' => Task::with('project')->where('status', '!=', 'done')->orderByRaw("CASE WHEN status = 'in_progress' THEN 1 WHEN priority = 'urgent' THEN 2 WHEN priority = 'high' THEN 3 ELSE 4 END")->orderBy('due_date')->first()],
            default => throw new \InvalidArgumentException('Unknown tool: ' . $name),
        };
        return ['content' => [['type' => 'text', 'text' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]]];
    }

    private function validProjectOrWorkspaceToken(Request $request, array $payload): bool
    {
        $provided = (string) $request->bearerToken();
        if ($provided === '') return false;
        $workspaceToken = env('TASK_HUB_MCP_TOKEN');
        if ($workspaceToken && hash_equals($workspaceToken, $provided)) return true;

        $args = data_get($payload, 'params.arguments', []);
        $project = null;
        $headerProjectId = $request->header('X-Task-Hub-Project');
        if (!empty($args['project_id'])) $project = Project::find($args['project_id']);
        if (!$project && $headerProjectId) $project = Project::find($headerProjectId);
        if (!$project && !empty($args['task_id'])) $project = Task::with('project')->find($args['task_id'])?->project;
        if (!$project && !empty($args['run_id'])) $project = AgentRun::with('task.project')->find($args['run_id'])?->task?->project;
        if (!$project || !$project->task_hub_mcp_token) return false;
        return app(GithubProjectIntegrationService::class)->secret($project->task_hub_mcp_token) === $provided;
    }
}
