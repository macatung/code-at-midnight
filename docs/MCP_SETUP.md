# Task Hub MCP setup

Task Hub exposes one authenticated MCP endpoint:

```text
https://tasks.macatung.dev/api/tasks/mcp
```

The endpoint uses JSON-RPC over HTTP. Authentication is project-scoped: use the
MCP token from the project settings and the numeric project ID. Do not commit
either value or put them in `AGENTS.md`, `CLAUDE.md`, or source code.

## 1. Desktop Agent Workspace (recommended)

Do not enter a project ID or MCP token in the desktop app. Open `Tasks →
Agent`, choose exactly one open task and a local repository workspace, then
click **Đăng nhập & kết nối Task Hub**. The app starts a short-lived device
pairing request and opens the Task Hub approval page. Sign in with GitHub and
approve the selected project. If the project has no MCP token yet, Task Hub
creates and encrypts one during approval.

After approval the desktop app receives the credential once over HTTPS, calls
`get_context_pack` and `start_agent_run`, writes the provider-specific local
MCP config, and only then starts the selected agent. The credential is kept in
memory for that session; it is never shown in the UI or copied into the agent
prompt. A successful process ends the run in `needs_review`; a failed process
ends it in `failed`.

The generated files are:

```text
<workspace>/.agents/mcp_config.json  # Antigravity/agy
<workspace>/.mcp.json                # Codex/Claude Code
```

Existing MCP servers are preserved and a backup is created before merging.
The generated path is also added to the workspace's local
`.git/info/exclude`. Agents must use the Task Hub MCP lifecycle/evidence tools
and must not merge or deploy.

## 2. Manual setup (fallback)

1. Sign in to `https://tasks.macatung.dev` with GitHub.
2. Open a project and choose **Edit project**.
3. In **Kết nối riêng cho Project**, generate or paste a project MCP token.
4. Save the project and copy the token into a local environment variable.

PowerShell:

```powershell
$env:TASK_HUB_URL = "https://tasks.macatung.dev"
$env:TASK_HUB_PROJECT_ID = "123"
$env:TASK_HUB_PROJECT_MCP_TOKEN = "paste-token-locally"
```

## 3. Claude Code

The repository already contains `.mcp.json`. Run Claude Code from the
repository with the three environment variables above. If configuring a
global server, use this equivalent entry in Claude's MCP configuration:

```json
{
  "mcpServers": {
    "task-hub": {
      "type": "http",
      "url": "https://tasks.macatung.dev/api/tasks/mcp",
      "headers": {
        "Authorization": "Bearer ${TASK_HUB_PROJECT_MCP_TOKEN}",
        "X-Task-Hub-Project": "${TASK_HUB_PROJECT_ID}"
      }
    }
  }
}
```

## 4. Codex

Use the same HTTP MCP server configuration in Codex's MCP settings. If the
Codex installation does not support remote MCP, use the repository's `AGENTS.md`
contract and the CLI bridge when available; report the run and verification
evidence to Task Hub after the code changes are complete.

## 5. Antigravity

Add a remote MCP server in Antigravity with:

- URL: `https://tasks.macatung.dev/api/tasks/mcp`
- Header `Authorization`: `Bearer <project-token>`
- Header `X-Task-Hub-Project`: `<project-id>`

Start with read-only tools:

```text
get_work_item
get_context_pack
get_project_state
get_repository_context
get_repository_file
get_next_action
preview_project_breakdown
```

Before planning follow-up work, the agent should call
`get_project_state` and `get_repository_context`. The planning preview does
not write Project, Sprint, or Task records. Human approval is required before
any commit/creation action.

## Security and troubleshooting

- Never paste tokens into chat, Git, screenshots, or committed config.
- A `401` means the token or project ID is wrong.
- A `403` means the project is not accessible to the authenticated user.
- A `422` from GitHub integration usually means the GitHub account needs to be
  re-authenticated so Task Hub can refresh its encrypted access token.
- Rotate a token immediately if it has been exposed.
