import { contextBridge, ipcRenderer } from 'electron';

contextBridge.exposeInMainWorld('desktopApi', {
  close: () => ipcRenderer.send('window-close'),
  minimize: () => ipcRenderer.send('window-minimize'),
  setAlwaysOnTop: (alwaysOnTop: boolean) => ipcRenderer.send('window-set-always-on-top', alwaysOnTop),
  moveWindow: (dx: number, dy: number) => ipcRenderer.send('window-move-by', { dx, dy }),
  resizeWindow: (width: number, height: number) => ipcRenderer.send('window-resize', { width, height }),
  setIgnoreMouseEvents: (ignore: boolean, forward: boolean) => ipcRenderer.send('window-ignore-mouse-events', { ignore, forward }),
  onTrayAction: (callback: (action: string) => void) => {
    ipcRenderer.on('tray-action', (_event, action) => callback(action));
  },
});
