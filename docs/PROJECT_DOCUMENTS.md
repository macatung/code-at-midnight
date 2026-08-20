# Project document registry

This manifest is the versioned catalogue of project documents. Task Hub imports it per linked project; a task may then mark individual documents as required for the agent context pack.

| type | title | path_or_url | owner | version | tags |
| --- | --- | --- | --- | --- | --- |
| brief | Task Hub & Desktop Companion brief | docs/PROJECT_DOCUMENTS.md | Product | 1.0 | scope,knowledge-management |
| architecture | Task Hub agent workflow | app/Services/TaskHubContextPackService.php | Engineering | 1.0 | mcp,agents,context |
| coding_standard | Repository agent contract | AGENTS.md | Engineering | 1.0 | quality,workflow |
| qa_plan | Automated test suite | tests/Feature/TaskHubAgentWorkflowTest.php | QA | 1.0 | tests,verification |
| release_runbook | Desktop release process | desktop/README.md | Release owner | 1.0 | desktop,release |
| changelog | Versioned changelog | docs/CHANGELOG.md | Product | 1.0 | release,overview |
