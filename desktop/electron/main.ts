import { app, BrowserWindow, ipcMain, Tray, Menu, nativeImage, screen, dialog, clipboard, shell } from 'electron';
import { autoUpdater } from 'electron-updater';
import { spawn, execFileSync, ChildProcessWithoutNullStreams } from 'node:child_process';
import path from 'node:path';
import fs from 'node:fs';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Disable GPU disk cache locks that cause "Access is denied" when launching multiple instances
app.commandLine.appendSwitch('disable-gpu-shader-disk-cache');
app.commandLine.appendSwitch('disable-gpu-program-cache');

// Single instance lock to prevent duplicate overlapping desktop pets
const gotTheLock = app.requestSingleInstanceLock();
if (!gotTheLock) {
  app.quit();
}

// The built directory structure
process.env.DIST = path.join(__dirname, '../dist');
process.env.VITE_PUBLIC = app.isPackaged ? process.env.DIST : path.join(process.env.DIST, '../public');

let win: BrowserWindow | null = null;
let tray: Tray | null = null;
const agentProcesses = new Map<string, ChildProcessWithoutNullStreams>();
type UpdateStatus = 'idle' | 'checking' | 'available' | 'downloading' | 'downloaded' | 'not-available' | 'error';
let updateState: { status: UpdateStatus; version?: string; percent?: number; message?: string } = { status: 'idle' };
let updateTimer: NodeJS.Timeout | undefined;

const AGENT_COMMANDS: Record<string, { command: string; args: string[] }> = {
  codex: { command: 'codex', args: [] },
  claude_code: { command: 'claude', args: [] },
};

function findAntigravityExecutable() {
  const candidates = [
    process.env.ANTIGRAVITY_PATH,
    path.join(process.env.LOCALAPPDATA || '', 'Programs', 'Antigravity', 'Antigravity.exe'),
    path.join(process.env.LOCALAPPDATA || '', 'Programs', 'Antigravity IDE', 'Antigravity IDE.exe'),
  ].filter(Boolean) as string[];
  return candidates.find((candidate) => fs.existsSync(candidate));
}

function resolveCli(command: string) {
  try {
    const result = execFileSync(process.platform === 'win32' ? 'where.exe' : 'which', [command], { encoding: 'utf8' });
    return result.split(/\r?\n/).map((value) => value.trim()).find(Boolean) || command;
  } catch {
    return null;
  }
}

async function taskHubRequest(taskHubUrl: string, pathName: string, options: RequestInit = {}) {
  const response = await fetch(`${taskHubUrl.replace(/\/$/, '')}${pathName}`, {
    ...options,
    headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
  });
  const body = await response.json().catch(() => ({}));
  if (!response.ok) throw new Error(body.message || body.error || `Task Hub request failed (${response.status}).`);
  return body;
}

async function taskHubMcpCall(taskHubUrl: string, token: string, projectId: string, method: string, params: Record<string, any> = {}) {
  return taskHubRequest(taskHubUrl, '/api/tasks/mcp', {
    method: 'POST',
    headers: { Authorization: `Bearer ${token}`, 'X-Task-Hub-Project': projectId },
    body: JSON.stringify({ jsonrpc: '2.0', id: `${Date.now()}-${Math.random()}`, method, params }),
  });
}

function broadcastUpdateState() {
  win?.webContents.send('updater-state', updateState);
}

function setUpdateState(next: { status: UpdateStatus; version?: string; percent?: number; message?: string }) {
  updateState = { ...updateState, ...next };
  broadcastUpdateState();
}

function setupAutoUpdater() {
  if (!app.isPackaged) return;
  autoUpdater.autoDownload = true;
  autoUpdater.autoInstallOnAppQuit = false;
  autoUpdater.on('checking-for-update', () => setUpdateState({ status: 'checking', message: 'Đang kiểm tra cập nhật...' }));
  autoUpdater.on('update-available', (info) => setUpdateState({ status: 'available', version: info.version, percent: 0, message: `Đang tải bản ${info.version}...` }));
  autoUpdater.on('update-not-available', (info) => setUpdateState({ status: 'not-available', version: info.version, percent: 100, message: 'App đang ở phiên bản mới nhất.' }));
  autoUpdater.on('download-progress', (progress) => setUpdateState({ status: 'downloading', percent: Math.round(progress.percent), message: `Đang tải cập nhật ${Math.round(progress.percent)}%...` }));
  autoUpdater.on('update-downloaded', (info) => setUpdateState({ status: 'downloaded', version: info.version, percent: 100, message: `Bản ${info.version} đã sẵn sàng cài đặt.` }));
  autoUpdater.on('error', (error) => setUpdateState({ status: 'error', message: error.message.slice(0, 240) || 'Không thể kiểm tra cập nhật.' }));
  updateTimer = setInterval(() => {
    void autoUpdater.checkForUpdates().catch((error: Error) => setUpdateState({ status: 'error', message: error.message.slice(0, 240) || 'Không thể kiểm tra cập nhật.' }));
  }, 6 * 60 * 60 * 1000);
}

const DEFAULT_WIDTH = 640;
const DEFAULT_HEIGHT = 520;

function getIconImage() {
  const possiblePaths = [
    path.join(__dirname, '../public/icon.png'),
    path.join(__dirname, '../../public/brand/macatung-mascot-icon.png'),
    path.join(process.cwd(), 'public/brand/macatung-mascot-icon.png'),
    path.join(process.cwd(), 'desktop/public/icon.png'),
  ];

  for (const p of possiblePaths) {
    if (fs.existsSync(p)) {
      const img = nativeImage.createFromPath(p);
      if (!img.isEmpty()) return img;
    }
  }

  // Fallback programmatic green/gold circle
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><circle cx="16" cy="16" r="14" fill="#0c0a09" stroke="#00f5a0" stroke-width="2"/><circle cx="16" cy="16" r="6" fill="#ffd166"/></svg>`;
  return nativeImage.createFromBuffer(Buffer.from(svg));
}

function getPreloadPath() {
  const possiblePaths = [
    path.join(__dirname, 'preload.js'),
    path.join(__dirname, 'preload.mjs'),
    path.join(__dirname, '../dist-electron/preload.js'),
    path.join(__dirname, '../dist-electron/preload.mjs'),
  ];

  for (const p of possiblePaths) {
    if (fs.existsSync(p)) {
      return p;
    }
  }

  return path.join(__dirname, 'preload.js');
}

function createWindow() {
  const primaryDisplay = screen.getPrimaryDisplay();
  const { width: screenWidth, height: screenHeight } = primaryDisplay.workAreaSize;
  const appIcon = getIconImage();
  const preloadFile = getPreloadPath();

  win = new BrowserWindow({
    width: DEFAULT_WIDTH,
    height: DEFAULT_HEIGHT,
    x: screenWidth - DEFAULT_WIDTH - 20,
    y: screenHeight - DEFAULT_HEIGHT - 20,
    transparent: true,
    frame: false,
    alwaysOnTop: true,
    hasShadow: false,
    resizable: true,
    skipTaskbar: false,
    icon: appIcon,
    webPreferences: {
      preload: preloadFile,
      nodeIntegration: false,
      contextIsolation: true,
    },
  });

  win.setAspectRatio(0);

  if (process.env.VITE_DEV_SERVER_URL) {
    win.loadURL(process.env.VITE_DEV_SERVER_URL);
  } else {
    win.loadFile(path.join(process.env.DIST || path.join(__dirname, '../dist'), 'index.html'));
  }

  ipcMain.on('window-close', () => {
    if (win) win.hide();
  });

  ipcMain.on('window-minimize', () => {
    if (win) win.minimize();
  });

  ipcMain.on('window-set-always-on-top', (_event, alwaysOnTop: boolean) => {
    if (win) win.setAlwaysOnTop(alwaysOnTop);
  });

  ipcMain.on('window-move-by', (_event, { dx, dy }: { dx: number; dy: number }) => {
    if (win) {
      const [currentX, currentY] = win.getPosition();
      win.setPosition(Math.round(currentX + dx), Math.round(currentY + dy));
    }
  });

  ipcMain.on('window-resize', (_event, { width, height }: { width: number; height: number }) => {
    if (win) {
      const [currentX, currentY] = win.getPosition();
      const [currentW, currentH] = win.getSize();
      const newX = currentX - (width - currentW);
      const newY = currentY - (height - currentH);
      win.setBounds({
        x: Math.max(0, newX),
        y: Math.max(0, newY),
        width,
        height,
      });
    }
  });

  ipcMain.on('window-ignore-mouse-events', (_event, { ignore, forward }: { ignore: boolean; forward: boolean }) => {
    if (win) {
      win.setIgnoreMouseEvents(ignore, { forward });
    }
  });

  ipcMain.handle('agent-pick-workspace', async () => {
    const result = await dialog.showOpenDialog(win!, { properties: ['openDirectory'] });
    return result.canceled ? null : result.filePaths[0];
  });

  ipcMain.handle('open-external', async (_event, url: string) => {
    if (!/^https?:\/\//i.test(url)) throw new Error('Chỉ cho phép mở URL HTTP/HTTPS.');
    await shell.openExternal(url);
    return true;
  });

  ipcMain.handle('updater-get-state', () => updateState);
  ipcMain.handle('updater-check', async () => {
    if (!app.isPackaged) {
      setUpdateState({ status: 'not-available', message: 'Auto-update chỉ hoạt động ở bản đã đóng gói.' });
      return updateState;
    }
    try {
      await autoUpdater.checkForUpdates();
    } catch (error: any) {
      setUpdateState({ status: 'error', message: error?.message?.slice(0, 240) || 'Không thể kiểm tra cập nhật.' });
    }
    return updateState;
  });
  ipcMain.handle('updater-install', () => {
    if (app.isPackaged && updateState.status === 'downloaded') autoUpdater.quitAndInstall(false, true);
    return updateState;
  });
  ipcMain.handle('updater-dismiss', () => {
    setUpdateState({ status: 'idle', message: undefined });
    return updateState;
  });

  ipcMain.handle('taskhub-pairing-start', async (_event, { taskHubUrl, projectId }: { taskHubUrl: string; projectId: number }) => {
    return taskHubRequest(taskHubUrl, '/api/desktop/pairing/start', { method: 'POST', body: JSON.stringify({ project_id: projectId }) });
  });

  ipcMain.handle('taskhub-pairing-status', async (_event, { taskHubUrl, pairingId, deviceSecret }: { taskHubUrl: string; pairingId: string; deviceSecret: string }) => {
    return taskHubRequest(taskHubUrl, `/api/desktop/pairing/${encodeURIComponent(pairingId)}/status`, { headers: { 'X-Desktop-Pairing-Secret': deviceSecret } });
  });

  ipcMain.handle('taskhub-mcp-call', async (_event, { taskHubUrl, token, projectId, method, params }: { taskHubUrl: string; token: string; projectId: string; method: string; params?: Record<string, any> }) => {
    return taskHubMcpCall(taskHubUrl, token, projectId, method, params || {});
  });

  ipcMain.handle('agent-configure-mcp', (_event, { cwd, provider, taskHubUrl, projectId, token }: { cwd: string; provider: string; taskHubUrl: string; projectId: string; token: string }) => {
    if (!cwd || !fs.existsSync(cwd) || !fs.statSync(cwd).isDirectory()) throw new Error('Workspace không hợp lệ.');
    if (!/^https?:\/\//i.test(taskHubUrl)) throw new Error('Task Hub URL phải bắt đầu bằng http:// hoặc https://.');
    if (!/^\d+$/.test(String(projectId))) throw new Error('Project ID phải là số.');
    if (!token || token.length < 12) throw new Error('Project MCP token không hợp lệ.');

    const useAntigravityFormat = provider === 'antigravity' || provider === 'agy';
    const configDirectory = useAntigravityFormat ? path.join(cwd, '.agents') : cwd;
    const configPath = useAntigravityFormat ? path.join(configDirectory, 'mcp_config.json') : path.join(cwd, '.mcp.json');
    let config: Record<string, any> = { mcpServers: {} };
    if (fs.existsSync(configPath)) {
      try { config = JSON.parse(fs.readFileSync(configPath, 'utf8')); } catch { throw new Error('Không đọc được MCP config hiện tại.'); }
      fs.copyFileSync(configPath, `${configPath}.bak.${Date.now()}`);
    }
    config.mcpServers = config.mcpServers || {};
    config.mcpServers['task-hub'] = {
      ...(useAntigravityFormat ? { serverUrl: `${taskHubUrl.replace(/\/$/, '')}/api/tasks/mcp` } : { type: 'http', url: `${taskHubUrl.replace(/\/$/, '')}/api/tasks/mcp` }),
      headers: {
        Authorization: `Bearer ${token}`,
        'X-Task-Hub-Project': String(projectId),
      },
    };
    fs.mkdirSync(configDirectory, { recursive: true });
    fs.writeFileSync(configPath, `${JSON.stringify(config, null, 2)}\n`, 'utf8');
    const gitExclude = path.join(cwd, '.git', 'info', 'exclude');
    if (fs.existsSync(path.join(cwd, '.git'))) {
      fs.mkdirSync(path.dirname(gitExclude), { recursive: true });
      const existing = fs.existsSync(gitExclude) ? fs.readFileSync(gitExclude, 'utf8') : '';
      const relative = path.relative(cwd, configPath).replace(/\\/g, '/');
      if (!existing.split(/\r?\n/).includes(relative)) fs.appendFileSync(gitExclude, `${existing && !existing.endsWith('\n') ? '\n' : ''}${relative}\n`, 'utf8');
    }
    return { path: configPath, server: 'task-hub' };
  });

  ipcMain.handle('agent-start', (_event, { provider, cwd, prompt }: { provider: string; cwd: string; prompt?: string }) => {
    if (provider === 'antigravity') {
      const agy = resolveCli('agy');
      if (agy) {
        const sessionId = `antigravity-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
        const child = spawn(agy, [], { cwd, env: { ...process.env, FORCE_COLOR: '0' }, shell: true, windowsHide: true });
        agentProcesses.set(sessionId, child);
        child.stdout.on('data', (data) => win?.webContents.send('agent-output', { sessionId, stream: 'stdout', text: data.toString() }));
        child.stderr.on('data', (data) => win?.webContents.send('agent-output', { sessionId, stream: 'stderr', text: data.toString() }));
        child.on('error', (error) => win?.webContents.send('agent-output', { sessionId, stream: 'system', text: `Không thể khởi động agy: ${error.message}\n` }));
        child.on('close', (code, signal) => {
          agentProcesses.delete(sessionId);
          win?.webContents.send('agent-exit', { sessionId, code, signal });
        });
        if (prompt?.trim()) child.stdin.write(`${prompt.trim()}\n`);
        return { mode: 'cli', sessionId, provider, cwd };
      }
      const executable = findAntigravityExecutable();
      if (!executable) throw new Error('Không tìm thấy Antigravity.exe. Hãy cài Antigravity hoặc đặt biến môi trường ANTIGRAVITY_PATH.');
      const child = spawn(executable, [cwd], { cwd, detached: true, stdio: 'ignore', windowsHide: false });
      child.unref();
      if (prompt?.trim()) {
        clipboard.writeText(prompt.trim());
      }
      return { mode: 'desktop', provider, cwd, executable, promptCopied: Boolean(prompt?.trim()) };
    }
    const definition = AGENT_COMMANDS[provider];
    if (!definition) throw new Error('Agent không được hỗ trợ.');
    if (!cwd || !fs.existsSync(cwd) || !fs.statSync(cwd).isDirectory()) throw new Error('Workspace không hợp lệ.');

    const sessionId = `${provider}-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    const command = resolveCli(definition.command) || definition.command;
    const child = spawn(command, definition.args, {
      cwd,
      env: { ...process.env, FORCE_COLOR: '0' },
      shell: true,
      windowsHide: true,
    });
    agentProcesses.set(sessionId, child);
    child.stdout.on('data', (data) => win?.webContents.send('agent-output', { sessionId, stream: 'stdout', text: data.toString() }));
    child.stderr.on('data', (data) => win?.webContents.send('agent-output', { sessionId, stream: 'stderr', text: data.toString() }));
    child.on('error', (error) => win?.webContents.send('agent-output', { sessionId, stream: 'system', text: `Không thể khởi động ${provider}: ${error.message}\n` }));
    child.on('close', (code, signal) => {
      agentProcesses.delete(sessionId);
      win?.webContents.send('agent-exit', { sessionId, code, signal });
    });
    if (prompt?.trim()) child.stdin.write(`${prompt.trim()}\n`);
    return { sessionId, provider, cwd };
  });

  ipcMain.on('agent-input', (_event, { sessionId, input }: { sessionId: string; input: string }) => {
    const child = agentProcesses.get(sessionId);
    if (child && !child.killed) child.stdin.write(input.endsWith('\n') ? input : `${input}\n`);
  });

  ipcMain.handle('agent-stop', (_event, sessionId: string) => {
    const child = agentProcesses.get(sessionId);
    if (!child) return false;
    child.kill();
    agentProcesses.delete(sessionId);
    return true;
  });
}

function createTray() {
  const appIcon = getIconImage();
  const trayIcon = appIcon.resize({ width: 20, height: 20 });
  
  tray = new Tray(trayIcon);
  tray.setToolTip('Ma Tọa Thiền — Task Companion');

  const contextMenu = Menu.buildFromTemplate([
    {
      label: '🧙‍♂️ Hiển thị / Ẩn Mascot',
      click: () => {
        if (win) {
          if (win.isVisible()) win.hide();
          else win.show();
        }
      },
    },
    { type: 'separator' },
    {
      label: '📋 Mở Task Dispatch',
      click: () => {
        if (win) { win.show(); win.webContents.send('tray-action', 'open-dispatch'); }
      },
    },
    {
      label: '🤖 Mở Agent Workspace',
      click: () => {
        if (win) { win.show(); win.webContents.send('tray-action', 'open-agent'); }
      },
    },
    {
      label: '🍅 Bật Đồng Hồ Pomodoro Deep Work',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'open-pomodoro');
        }
      },
    },
    {
      label: '🦆 Debug cùng Rubber Duck',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'open-duck');
        }
      },
    },
    {
      label: '📋 Task Notes & Scratchpad',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'open-notes');
        }
      },
    },
    {
      label: '🌐 Mở Tasks Hub (tasks.macatung.dev)',
      click: () => {
        import('electron').then(({ shell }) => {
          shell.openExternal(process.env.TASK_HUB_URL || 'https://tasks.macatung.dev/tasks');
        });
      },
    },
    { type: 'separator' },
    {
      label: '🔄 Kiểm tra cập nhật',
      click: () => {
        if (win) { win.show(); win.webContents.send('tray-action', 'check-updates'); }
        if (app.isPackaged) void autoUpdater.checkForUpdates().catch(() => undefined);
      },
    },
    {
      label: '⬆️ Khởi động lại để cập nhật',
      click: () => {
        if (app.isPackaged && updateState.status === 'downloaded') autoUpdater.quitAndInstall(false, true);
      },
    },
    { type: 'separator' },
    {
      label: '❌ Thoát Hoàn Toàn Ứng Dụng',
      click: () => {
        app.quit();
      },
    },
  ]);

  tray.setContextMenu(contextMenu);

  tray.on('double-click', () => {
    if (win) {
      if (win.isVisible()) win.hide();
      else win.show();
    }
  });
}

app.whenReady().then(() => {
  createWindow();
  createTray();
  setupAutoUpdater();
  setTimeout(() => {
    if (app.isPackaged) void autoUpdater.checkForUpdates().catch((error: Error) => setUpdateState({ status: 'error', message: error.message.slice(0, 240) }));
  }, 10_000);

  app.on('second-instance', () => {
    if (win) {
      if (win.isMinimized()) win.restore();
      win.show();
      win.focus();
    }
  });

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow();
    }
  });
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

app.on('before-quit', () => {
  if (updateTimer) clearInterval(updateTimer);
  for (const child of agentProcesses.values()) child.kill();
  agentProcesses.clear();
});
