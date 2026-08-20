<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class GithubProjectIntegrationService
{
    public function connect(Project $project, array $input): Project
    {
        $repository = trim($input['github_repository']);
        $token = $input['github_token'] ?? null;

        $project->github_repository = $repository;
        $project->github_default_branch = $input['github_default_branch'] ?? 'main';
        if ($token !== null && $token !== '') $project->github_token = Crypt::encryptString($token);
        if (($input['clear_github_token'] ?? false) === true) $project->github_token = null;
        if (!empty($input['github_webhook_secret'])) $project->github_webhook_secret = Crypt::encryptString($input['github_webhook_secret']);
        if (($input['clear_github_webhook_secret'] ?? false) === true) $project->github_webhook_secret = null;
        if (!empty($input['task_hub_mcp_token'])) $project->task_hub_mcp_token = Crypt::encryptString($input['task_hub_mcp_token']);
        if (($input['clear_task_hub_mcp_token'] ?? false) === true) $project->task_hub_mcp_token = null;
        $project->github_connected_at = now();
        if (Auth::check()) $project->user_id = Auth::id();
        $project->github_sync_status = 'connected';
        $project->github_sync_error = null;
        $project->save();

        return $project->fresh();
    }

    public function sync(Project $project): Project
    {
        if (!$project->github_repository) throw new \RuntimeException('Project chưa được cấu hình GitHub repository.');

        $project->update(['github_sync_status' => 'syncing', 'github_sync_error' => null]);
        try {
            $request = Http::acceptJson()->withHeaders(['User-Agent' => 'TaskHub/1.0'])->timeout(10);
            $token = $project->user ? $this->secret($project->user->github_access_token) : null;
            $token = $token ?: $this->secret($project->github_token);
            if (!$token && Auth::check()) $token = $this->secret(Auth::user()->github_access_token);
            if ($token) $request = $request->withToken($token);

            $repo = $request->get('https://api.github.com/repos/' . $project->github_repository)->throw()->json();
            $issues = $request->get('https://api.github.com/repos/' . $project->github_repository . '/issues', ['state' => 'open', 'per_page' => 30])->throw()->json();
            $pulls = $request->get('https://api.github.com/repos/' . $project->github_repository . '/pulls', ['state' => 'open', 'per_page' => 30])->throw()->json();
            $snapshot = [
                'repository' => [
                    'full_name' => $repo['full_name'] ?? $project->github_repository,
                    'description' => $repo['description'] ?? null,
                    'default_branch' => $repo['default_branch'] ?? $project->github_default_branch,
                    'private' => $repo['private'] ?? null,
                    'html_url' => $repo['html_url'] ?? null,
                    'updated_at' => $repo['updated_at'] ?? null,
                ],
                'issues' => array_map(fn ($issue) => $this->issueSummary($issue), is_array($issues) ? $issues : []),
                'pull_requests' => array_map(fn ($pull) => $this->pullSummary($pull), is_array($pulls) ? $pulls : []),
            ];
            $project->update([
                'github_sync_status' => 'synced',
                'github_last_sync_at' => now(),
                'github_sync_error' => null,
                'github_snapshot' => $snapshot,
                'github_default_branch' => $repo['default_branch'] ?? $project->github_default_branch,
            ]);
        } catch (\Throwable $e) {
            $project->update(['github_sync_status' => 'error', 'github_sync_error' => $e->getMessage()]);
            throw $e;
        }
        return $project->fresh();
    }

    public function status(Project $project): array
    {
        $project->loadMissing('user');
        return [
            'connected' => !empty($project->github_repository),
            'repository' => $project->github_repository,
            'default_branch' => $project->github_default_branch,
            'has_github_token' => !empty($project->github_token),
            'has_github_access' => !empty($project->user?->github_access_token),
            'has_webhook_secret' => !empty($project->github_webhook_secret),
            'has_mcp_token' => !empty($project->task_hub_mcp_token),
            'connected_at' => $project->github_connected_at?->toIso8601String(),
            'last_sync_at' => $project->github_last_sync_at?->toIso8601String(),
            'sync_status' => $project->github_sync_status,
            'sync_error' => $project->github_sync_error,
            'snapshot' => $project->github_snapshot,
        ];
    }

    public function secret(?string $encrypted): ?string
    {
        if (!$encrypted) return null;
        try { return Crypt::decryptString($encrypted); } catch (\Throwable) { return null; }
    }

    private function issueSummary(array $issue): array
    {
        return ['number' => $issue['number'] ?? null, 'title' => $issue['title'] ?? null, 'state' => $issue['state'] ?? null, 'url' => $issue['html_url'] ?? null, 'labels' => array_map(fn ($label) => $label['name'] ?? null, $issue['labels'] ?? [])];
    }

    private function pullSummary(array $pull): array
    {
        return ['number' => $pull['number'] ?? null, 'title' => $pull['title'] ?? null, 'state' => $pull['state'] ?? null, 'draft' => $pull['draft'] ?? false, 'url' => $pull['html_url'] ?? null, 'branch' => data_get($pull, 'head.ref')];
    }
}
