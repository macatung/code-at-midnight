import { app, BrowserWindow, ipcMain, Tray, Menu, nativeImage, screen } from 'electron';
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
}

function createTray() {
  const appIcon = getIconImage();
  const trayIcon = appIcon.resize({ width: 20, height: 20 });
  
  tray = new Tray(trayIcon);
  tray.setToolTip('Ma Cà Tưng & Ma Tọa Thiền — Trợ Lý Chánh Niệm & Hiệu Suất');

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
    {
      label: '🎭 Đổi Chế Độ (Dev Coder ⇋ Tọa Thiền)',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'toggle-persona');
        }
      },
    },
    { type: 'separator' },
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
      label: '🦆 Debug Tâm Thức & Yểm Bùa 0 Bug',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'open-duck');
        }
      },
    },
    {
      label: '📋 Top 3 Việc Cần Làm & Nháp Nhanh',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'open-notes');
        }
      },
    },
    {
      label: '🧘‍♂️ Vận Động Cột Sống & Nghỉ Mắt (30s)',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'open-stretch');
        }
      },
    },
    {
      label: '💧 Ghi Nhận 1 Ly Nước Tươi Mát',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'open-water');
        }
      },
    },
    {
      label: '📜 Rút 1 Bài Kệ Pháp Cú Ngẫu Nhiên',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'draw-verse');
        }
      },
    },
    {
      label: '🔔 Thỉnh Chuông Chánh Niệm 432Hz',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'ring-bell');
        }
      },
    },
    {
      label: '🌸 Bắt Đầu Tập Thở 3 Nhịp',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'start-breathing');
        }
      },
    },
    { type: 'separator' },
    {
      label: '⚙️ Cài Đặt Chu Kỳ Nhắc Nhở...',
      click: () => {
        if (win) {
          win.show();
          win.webContents.send('tray-action', 'open-settings');
        }
      },
    },
    {
      label: '🌐 Mở Tasks Hub (tasks.macatung.dev)',
      click: () => {
        import('electron').then(({ shell }) => {
          shell.openExternal('http://localhost:8005/tasks');
        });
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