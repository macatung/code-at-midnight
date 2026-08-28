/**
 * Dual Perspective Parser & Interactive Widget Initializer
 * Transforms `:::perspective ... :::` tags into interactive dual-tab & split-screen comparison widgets.
 * Perspective 1: 🌱 Đời Thường (Góc nhìn đời sống & tâm lý con người hiện đại)
 * Perspective 2: 🧘 Theravāda (Quán chiếu tâm thức & tu tập Vipassanā)
 */

export interface DualPerspectiveData {
  devContent: string;
  theravadaContent: string;
  devTitle?: string;
  theravadaTitle?: string;
}

/**
 * Basic markdown inline formatter for text inside perspective blocks
 */
function formatPerspectiveInlineMarkdown(md: string): string {
  if (!md) return '';
  return md
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`([^`]+)`/g, '<code class="px-1.5 py-0.5 rounded bg-black/20 text-xs font-mono">$1</code>')
    .replace(/^#### (.*$)/gim, '<h4 class="text-base sm:text-lg font-bold mb-2">$1</h4>')
    .replace(/^### (.*$)/gim, '<h3 class="text-lg sm:text-xl font-bold mb-2">$1</h3>')
    .replace(/^-\s(.*)$/gim, '<li class="ml-4 list-disc my-1 leading-relaxed">$1</li>')
    .replace(/^\d+\.\s(.*)$/gim, '<li class="ml-4 list-decimal my-1 leading-relaxed">$1</li>')
    .split(/\n\n+/)
    .map(block => {
      const trimmed = block.trim();
      if (trimmed.startsWith('<h') || trimmed.startsWith('<li') || trimmed.startsWith('<ul') || trimmed.startsWith('<ol')) {
        return trimmed;
      }
      return `<p class="mb-3 leading-relaxed">${trimmed.replace(/\n/g, '<br/>')}</p>`;
    })
    .join('\n');
}

/**
 * Replace :::perspective blocks in markdown with interactive HTML widgets
 * @param content Raw markdown content
 * @param currentDomain 'main' | 'theravada'
 * @param isPaperMode boolean for Theravada paper theme
 */
export function parsePerspectiveBlocks(
  content: string,
  currentDomain: 'main' | 'theravada' = 'main',
  isPaperMode: boolean = false
): string {
  if (!content) return '';

  const perspectiveRegex = /:::\s*perspective\s*\n([\s\S]*?):::/gim;

  return content.replace(perspectiveRegex, (_match, blockContent) => {
    // Extract [dev]...[/dev] or [life]...[/life]
    const devMatch = blockContent.match(/\[(?:dev|life)\]([\s\S]*?)\[\/(?:dev|life)\]/i);
    // Extract [theravada]...[/theravada]
    const theravadaMatch = blockContent.match(/\[theravada\]([\s\S]*?)\[\/theravada\]/i);

    const devRaw = devMatch ? devMatch[1].trim() : '';
    const theravadaRaw = theravadaMatch ? theravadaMatch[1].trim() : '';

    const devHtml = formatPerspectiveInlineMarkdown(devRaw);
    const theravadaHtml = formatPerspectiveInlineMarkdown(theravadaRaw);

    const defaultTab = currentDomain === 'theravada' ? 'theravada' : 'dev';
    const widgetId = `dp-${Math.random().toString(36).substring(2, 9)}`;

    const borderColor = isPaperMode
      ? 'border-amber-300 bg-amber-50/70 shadow-md text-stone-900'
      : currentDomain === 'theravada'
        ? 'border-amber-500/40 bg-stone-900/90 shadow-xl text-stone-100'
        : 'border-emerald-500/30 bg-slate-950/90 shadow-xl text-slate-100';

    const headerBg = isPaperMode
      ? 'bg-amber-100/80 border-b border-amber-300'
      : currentDomain === 'theravada'
        ? 'bg-stone-900/90 border-b border-amber-500/30'
        : 'bg-slate-900/90 border-b border-emerald-500/20';

    return `
<div class="dual-perspective-widget my-10 rounded-2xl border ${borderColor} overflow-hidden font-sans transition-all duration-300" id="${widgetId}" data-current-tab="${defaultTab}">
  <!-- Widget Header & Controls -->
  <div class="px-4 py-3 sm:px-6 flex flex-wrap items-center justify-between gap-3 ${headerBg}">
    <div class="flex items-center gap-2">
      <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold">⚖️</span>
      <span class="text-xs sm:text-sm font-bold uppercase tracking-wider ${isPaperMode ? 'text-amber-950' : 'text-slate-200'}">
        Đối Chiếu Kép • Hai Góc Nhìn
      </span>
    </div>
    
    <!-- Tab Selectors -->
    <div class="flex items-center p-1 rounded-xl bg-black/20 backdrop-blur-sm border border-white/10 text-xs font-semibold gap-1 select-none">
      <button type="button" class="dp-tab-btn px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 ${defaultTab === 'dev' ? 'bg-emerald-500 text-slate-950 font-bold shadow' : 'text-slate-400 hover:text-slate-200'}" data-tab="dev" data-widget-id="${widgetId}">
        <span>🌱</span> <span class="hidden sm:inline">Góc nhìn</span> Đời Thường
      </button>
      <button type="button" class="dp-tab-btn px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 ${defaultTab === 'theravada' ? 'bg-amber-400 text-stone-950 font-bold shadow' : 'text-slate-400 hover:text-slate-200'}" data-tab="theravada" data-widget-id="${widgetId}">
        <span>🧘</span> <span class="hidden sm:inline">Quán chiếu</span> Theravāda
      </button>
      <button type="button" class="dp-tab-btn hidden md:flex px-3 py-1.5 rounded-lg transition-all items-center gap-1.5 text-slate-400 hover:text-slate-200" data-tab="split" data-widget-id="${widgetId}">
        <span>⚖️</span> Song Song
      </button>
    </div>
  </div>

  <!-- Content Panels -->
  <div class="p-5 sm:p-7">
    <!-- Life Panel -->
    <div class="dp-panel dp-panel-dev ${defaultTab === 'dev' ? 'block' : 'hidden'} transition-opacity duration-300">
      <div class="flex items-center gap-2 mb-3 text-xs font-sans text-emerald-400 uppercase tracking-wider font-semibold">
        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>LĂNG KÍNH ĐỜI SỐNG & TÂM LÝ HIỆN ĐẠI</span>
      </div>
      <div class="text-sm sm:text-base leading-relaxed ${isPaperMode ? 'text-stone-800' : 'text-slate-200'}">
        ${devHtml}
      </div>
    </div>

    <!-- Theravada Panel -->
    <div class="dp-panel dp-panel-theravada ${defaultTab === 'theravada' ? 'block' : 'hidden'} transition-opacity duration-300">
      <div class="flex items-center gap-2 mb-3 text-xs font-serif text-amber-500 uppercase tracking-wider font-bold">
        <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
        <span>LĂNG KÍNH PHẬT HỌC • VÔ NGÃ & TÂM THỨC VIPASSANĀ</span>
      </div>
      <div class="text-sm sm:text-base leading-relaxed font-serif ${isPaperMode ? 'text-stone-900' : 'text-amber-100/90'}">
        ${theravadaHtml}
      </div>
    </div>

    <!-- Split Side-by-Side Panel -->
    <div class="dp-panel dp-panel-split hidden grid-cols-1 md:grid-cols-2 gap-6 transition-opacity duration-300">
      <div class="p-4 sm:p-5 rounded-xl bg-slate-950/60 border border-emerald-500/20 text-xs sm:text-sm">
        <div class="flex items-center gap-2 mb-2.5 text-xs font-sans text-emerald-400 font-bold uppercase">
          <span>🌱 Góc Nhìn Đời Thường</span>
        </div>
        <div class="text-slate-300 leading-relaxed">${devHtml}</div>
      </div>
      <div class="p-4 sm:p-5 rounded-xl bg-amber-950/20 border border-amber-500/30 text-xs sm:text-sm font-serif">
        <div class="flex items-center gap-2 mb-2.5 text-xs font-serif text-amber-400 font-bold uppercase">
          <span>🧘 Quán Chiếu Theravāda</span>
        </div>
        <div class="text-amber-100/90 leading-relaxed">${theravadaHtml}</div>
      </div>
    </div>
  </div>
</div>
`;
  });
}

/**
 * Initializes click handlers for perspective widgets on page mount
 */
export function initPerspectiveWidgets(): void {
  if (typeof window === 'undefined') return;

  const widgets = document.querySelectorAll('.dual-perspective-widget');
  widgets.forEach(widget => {
    const buttons = widget.querySelectorAll('.dp-tab-btn');
    const devPanel = widget.querySelector('.dp-panel-dev');
    const theravadaPanel = widget.querySelector('.dp-panel-theravada');
    const splitPanel = widget.querySelector('.dp-panel-split');

    buttons.forEach(btn => {
      btn.onclick = (e) => {
        e.preventDefault();
        const targetTab = btn.getAttribute('data-tab');
        if (!targetTab) return;

        widget.setAttribute('data-current-tab', targetTab);

        buttons.forEach(b => {
          b.className = 'dp-tab-btn px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 text-slate-400 hover:text-slate-200';
          if (b.getAttribute('data-tab') === 'split') {
            b.className = 'dp-tab-btn hidden md:flex px-3 py-1.5 rounded-lg transition-all items-center gap-1.5 text-slate-400 hover:text-slate-200';
          }
        });

        if (targetTab === 'dev') {
          btn.className = 'dp-tab-btn px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 bg-emerald-500 text-slate-950 font-bold shadow';
          if (devPanel) devPanel.classList.remove('hidden');
          if (theravadaPanel) theravadaPanel.classList.add('hidden');
          if (splitPanel) {
            splitPanel.classList.remove('grid');
            splitPanel.classList.add('hidden');
          }
        } else if (targetTab === 'theravada') {
          btn.className = 'dp-tab-btn px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 bg-amber-400 text-stone-950 font-bold shadow';
          if (devPanel) devPanel.classList.add('hidden');
          if (theravadaPanel) theravadaPanel.classList.remove('hidden');
          if (splitPanel) {
            splitPanel.classList.remove('grid');
            splitPanel.classList.add('hidden');
          }
        } else if (targetTab === 'split') {
          btn.className = 'dp-tab-btn hidden md:flex px-3 py-1.5 rounded-lg transition-all items-center gap-1.5 bg-cyan-400 text-slate-950 font-bold shadow';
          if (devPanel) devPanel.classList.add('hidden');
          if (theravadaPanel) theravadaPanel.classList.add('hidden');
          if (splitPanel) {
            splitPanel.classList.remove('hidden');
            splitPanel.classList.add('grid');
          }
        }
      };
    });
  });
}
