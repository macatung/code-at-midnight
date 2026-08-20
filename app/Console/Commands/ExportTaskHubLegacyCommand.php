<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ExportTaskHubLegacyCommand extends Command
{
    protected $signature = 'taskhub:export-legacy {path : Destination JSON file outside source control}';
    protected $description = 'Export Task Hub history for the standalone Task Hub importer without credentials or secrets.';

    private const TABLES = ['users', 'projects', 'sprints', 'tasks', 'project_documents', 'task_documents', 'project_releases', 'agent_runs', 'verification_evidence', 'agent_run_events', 'github_events'];
    private const SECRET_COLUMNS = ['password', 'remember_token', 'github_access_token', 'github_token', 'github_webhook_secret', 'task_hub_mcp_token'];

    public function handle(): int
    {
        $tables = [];
        $checksums = [];
        foreach (self::TABLES as $table) {
            $rows = Schema::hasTable($table) ? DB::table($table)->orderBy('id')->get()->map(function ($row) {
                $data = (array) $row;
                foreach (self::SECRET_COLUMNS as $column) unset($data[$column]);
                return $data;
            })->all() : [];
            $tables[$table] = $rows;
            $checksums[$table] = hash('sha256', json_encode($rows, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        $payload = ['format' => 'task-hub-legacy-export/v1', 'exported_at' => now()->toIso8601String(), 'tables' => $tables, 'checksums' => $checksums];
        $path = (string) $this->argument('path');
        File::put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $this->info('Sanitized Task Hub export written to ' . $path);
        return self::SUCCESS;
    }
}
