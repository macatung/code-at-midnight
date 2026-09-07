<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, nextTick, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import TheravadaLayout from '@/Layouts/TheravadaLayout.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';
import { PALI_GLOSSARY, PaliGlossaryEntry } from '@/data/paliGlossary';
import { useZenAtmosphere } from '@/composables/useZenAtmosphere';
import { useI18n } from '@/composables/useI18n';
import mermaid from 'mermaid';
import DualPerspectiveHeaderBanner from '@/Components/common/DualPerspectiveHeaderBanner.vue';
import DualPerspectiveFooterCard from '@/Components/common/DualPerspectiveFooterCard.vue';
import DualPerspectiveFloatingPill from '@/Components/common/DualPerspectiveFloatingPill.vue';
import { parsePerspectiveBlocks, initPerspectiveWidgets } from '@/utils/dualPerspectiveParser';

const props = defineProps<{
  article: {
    id: number;
    title: string;
    pali_title?: string;
    slug: string;
    category: string;
    excerpt?: string;
    author?: string;
    content: string;
    tags?: string[];
    pali_terms?: { term: string; meaning: string }[];
    reading_time_min: number;
    published_at: string;
  };
  paired_article?: {
    id?: number;
    title: string;
    pali_title?: string;
    slug: string;
    excerpt?: string;
    site_domain: string;
    reading_time_min?: number;
    url: string;
    subdomain_url?: string;
    main_domain_url?: string;
  } | null;
  related?: any[];
  title?: string;
}>();

// Zen Reader Settings
const fontSize = ref<number>(18);
const readingProgress = ref(0);
const isRinging = ref(false);
const isCandlelightOn = ref(false);
const isFocusModeOn = ref(false);
const isPaperMode = ref(true); // Default to high-contrast paper background
const copiedLink = ref(false);
const isMobileScreen = ref(false);
const isZenSheetOpen = ref(false);
const isPaliHintDismissed = ref(false);
const { isLeavesEnabled, toggleLeaves } = useZenAtmosphere();
const { t, locale } = useI18n();

const STORAGE_FONT_SIZE_KEY = 'zen_reader_font_size';
const STORAGE_PAPER_MODE_KEY = 'zen_reader_paper_mode';

const checkMobileScreen = () => {
  if (typeof window !== 'undefined') {
    isMobileScreen.value = window.innerWidth < 640;
  }
};

interface TocHeading {
  id: string;
  title: string;
  level: number;
}

const tocHeadings = computed<TocHeading[]>(() => {
  if (!props.article.content) return [];
  const headings: TocHeading[] = [];
  let counter = 0;
  const headingRegex = /^(#{2,3})\s+(.*$)/gm;
  let match: RegExpExecArray | null;
  while ((match = headingRegex.exec(props.article.content)) !== null) {
    counter++;
    const level = match[1].length;
    const title = cleanHeadingText(match[2]);
    headings.push({ id: `toc-sec-${counter}`, title, level });
  }
  return headings;
});

const scrollToHeading = (id: string) => {
  isZenSheetOpen.value = false;
  if (typeof window === 'undefined') return;
  nextTick(() => {
    const el = document.getElementById(id);
    if (el) {
      const yOffset = -75;
      const y = el.getBoundingClientRect().top + window.pageYOffset + yOffset;
      window.scrollTo({ top: y, behavior: 'smooth' });
      mindfulBell.ringBell(528, 0.8);
    }
  });
};

const categoryLabel = (category: string) => {
  if (category === 'phap-thoai') return locale.value === 'en' ? 'Dharma Talks' : 'Pháp Thoại & Pháp Âm';
  const key = category === 'phap-hoc' ? 'theravada.study' : category === 'phap-hanh' ? 'theravada.practice' : category === 'kinh-tung' ? 'theravada.chanting' : 'theravada.history';
  return t(key);
};

const hasMediaAttachment = computed(() => {
  return props.article.content?.includes('phap-am-dinh-kem') || props.article.content?.includes('iframe') || props.article.category === 'phap-thoai';
});

// Social Share Methods
const copyArticleLink = async () => {
  const url = typeof window !== 'undefined' ? window.location.href : `https://theravada.macatung.dev/kinh/${props.article.slug}`;
  try {
    await navigator.clipboard.writeText(url);
    copiedLink.value = true;
    mindfulBell.ringBell(528, 1.5);
    setTimeout(() => {
      copiedLink.value = false;
    }, 3000);
  } catch (err) {
    console.error('Copy link error:', err);
  }
};

const handleNativeArticleShare = async () => {
  const url = typeof window !== 'undefined' ? window.location.href : `https://theravada.macatung.dev/kinh/${props.article.slug}`;
  const shareTitle = `${props.article.title} — Ma Tọa Thiền`;
  const shareText = `${locale.value === 'en' ? 'Read this Dhamma article' : 'Đọc bài kinh'}: ${props.article.title}\n${props.article.excerpt || ''}`;

  if (navigator.share) {
    try {
      await navigator.share({
        title: shareTitle,
        text: shareText,
        url: url,
      });
    } catch (err) {
      if ((err as any).name !== 'AbortError') {
        copyArticleLink();
      }
    }
  } else {
    copyArticleLink();
  }
};

const shareArticleToFacebook = () => {
  const url = encodeURIComponent(typeof window !== 'undefined' ? window.location.href : `https://theravada.macatung.dev/kinh/${props.article.slug}`);
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
};

const shareArticleToZalo = () => {
  const url = encodeURIComponent(typeof window !== 'undefined' ? window.location.href : `https://theravada.macatung.dev/kinh/${props.article.slug}`);
  window.open(`https://sp.zalo.me/plugins/share?url=${url}`, '_blank', 'width=600,height=400');
};

const shareArticleToTelegram = () => {
  const url = encodeURIComponent(typeof window !== 'undefined' ? window.location.href : `https://theravada.macatung.dev/kinh/${props.article.slug}`);
  const text = encodeURIComponent(`${props.article.title}\n\n${props.article.excerpt || ''}`);
  window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
};

const shareArticleToX = () => {
  const url = encodeURIComponent(typeof window !== 'undefined' ? window.location.href : `https://theravada.macatung.dev/kinh/${props.article.slug}`);
  const text = encodeURIComponent(`${props.article.title}\n\n#Theravada #ChanhPhap`);
  window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank', 'width=600,height=400');
};

// Interactive Pāḷi Tooltip State
interface ActiveTooltipState {
  targetEl: HTMLElement | null;
  term: string;
  pali: string;
  vietnamese: string;
  category: string;
  meaning: string;
  x: number;
  y: number;
  placement: 'top' | 'bottom';
}

const activeTooltip = ref<ActiveTooltipState | null>(null);
let tooltipHideTimeout: any = null;

const togglePaperMode = () => {
  isPaperMode.value = !isPaperMode.value;
  mindfulBell.ringBell(528, 1.0);
};

const toggleCandlelight = () => {
  isCandlelightOn.value = !isCandlelightOn.value;
  mindfulBell.ringBell(650, 1.2);
};

const toggleFocusMode = () => {
  isFocusModeOn.value = !isFocusModeOn.value;
  mindfulBell.ringBell(528, 1.2);
};

const handleScroll = () => {
  if (typeof window === 'undefined') return;
  const total = document.documentElement.scrollHeight - window.innerHeight;
  if (total > 0) {
    readingProgress.value = Math.min(100, Math.max(0, (window.scrollY / total) * 100));
  }
};

const showTooltip = (el: HTMLElement) => {
  if (tooltipHideTimeout) {
    clearTimeout(tooltipHideTimeout);
    tooltipHideTimeout = null;
  }

  const term = decodeURIComponent(el.getAttribute('data-term') || '');
  const pali = decodeURIComponent(el.getAttribute('data-pali') || term);
  const vietnamese = decodeURIComponent(el.getAttribute('data-vietnamese') || term);
  const category = decodeURIComponent(el.getAttribute('data-category') || 'Pháp Học Pāḷi');
  const meaning = decodeURIComponent(el.getAttribute('data-meaning') || '');

  if (!meaning) return;

  const rect = el.getBoundingClientRect();
  const popoverWidth = Math.min(380, window.innerWidth - 32);
  let left = rect.left + rect.width / 2 - popoverWidth / 2;

  // Clamp within viewport
  if (left < 16) left = 16;
  if (left + popoverWidth > window.innerWidth - 16) {
    left = window.innerWidth - popoverWidth - 16;
  }

  let top = rect.top - 12;
  let placement: 'top' | 'bottom' = 'top';

  if (rect.top < 220) {
    top = rect.bottom + 12;
    placement = 'bottom';
  }

  activeTooltip.value = {
    targetEl: el,
    term,
    pali,
    vietnamese,
    category,
    meaning,
    x: left,
    y: top,
    placement,
  };
};

const hideTooltipWithDelay = () => {
  tooltipHideTimeout = setTimeout(() => {
    activeTooltip.value = null;
  }, 280);
};

const keepTooltipOpen = () => {
  if (tooltipHideTimeout) {
    clearTimeout(tooltipHideTimeout);
    tooltipHideTimeout = null;
  }
};

const closeTooltip = () => {
  activeTooltip.value = null;
};

// Event delegation handler for article text (links and tooltips)
const handleArticleInteraction = (e: MouseEvent) => {
  const link = (e.target as HTMLElement)?.closest('a.zen-internal-link') as HTMLAnchorElement | null;
  if (e.type === 'click' && link) {
    const href = link.getAttribute('href');
    if (href && (href.startsWith('/') || href.startsWith('#'))) {
      if (href.startsWith('/')) {
        e.preventDefault();
        mindfulBell.ringBell(528, 0.8);
        router.visit(href);
        return;
      }
    }
  }

  const target = (e.target as HTMLElement)?.closest('.zen-pali-term') as HTMLElement | null;
  if (e.type === 'mouseover' || e.type === 'click') {
    if (target) {
      showTooltip(target);
    }
  } else if (e.type === 'mouseout') {
    if (target) {
      hideTooltipWithDelay();
    }
  }
};

const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') {
    closeTooltip();
  }
};

const handleWindowClick = (e: MouseEvent) => {
  const target = e.target as HTMLElement;
  if (!target.closest('.zen-pali-term') && !target.closest('.zen-pali-popover')) {
    closeTooltip();
  }
};

const renderMermaidDiagrams = async () => {
  if (typeof window === 'undefined') return;
  try {
    mermaid.initialize({
      startOnLoad: false,
      securityLevel: 'loose',
      theme: isPaperMode.value ? 'neutral' : 'dark',
      themeVariables: isPaperMode.value
        ? {
            background: '#fefce8',
            primaryColor: '#b45309',
            primaryTextColor: '#78350f',
            primaryBorderColor: '#d97706',
            lineColor: '#92400e',
            secondaryColor: '#fef3c7',
            tertiaryColor: '#fffbeb',
          }
        : {
            darkMode: true,
            background: '#1c1917',
            primaryColor: '#d97706',
            primaryTextColor: '#fffbeb',
            primaryBorderColor: '#b45309',
            lineColor: '#f59e0b',
            secondaryColor: '#292524',
            tertiaryColor: '#1c1917',
          },
      fontFamily: 'Lora, Merriweather, serif',
    });

    await nextTick();
    const containers = document.querySelectorAll('.zen-mermaid-container');
    for (let i = 0; i < containers.length; i++) {
      const el = containers[i];
      const rawCode = decodeURIComponent(el.getAttribute('data-code') || '');
      if (rawCode) {
        try {
          const id = `mermaid-zen-${Date.now()}-${i}-${Math.random().toString(36).slice(2, 7)}`;
          const { svg } = await mermaid.render(id, rawCode);
          const target = el.querySelector('.mermaid-render-target');
          if (target) {
            target.innerHTML = svg;
          }
        } catch (itemErr) {
          console.error(`Error rendering mermaid diagram #${i}:`, itemErr);
          const target = el.querySelector('.mermaid-render-target');
          if (target) {
            target.innerHTML = `<pre class="text-xs text-amber-400/80 bg-stone-950/80 p-3 rounded-lg overflow-x-auto text-left font-mono">${rawCode}</pre>`;
          }
        }
      }
    }
  } catch (err) {
    console.error('Mermaid render error:', err);
  }
};

onMounted(() => {
  checkMobileScreen();
  try {
    const savedSize = localStorage.getItem(STORAGE_FONT_SIZE_KEY);
    if (savedSize) {
      const parsed = parseInt(savedSize, 10);
      if (parsed >= 15 && parsed <= 26) {
        fontSize.value = parsed;
      }
    }
    const savedPaper = localStorage.getItem(STORAGE_PAPER_MODE_KEY);
    if (savedPaper !== null) {
      isPaperMode.value = savedPaper === 'true';
    }
  } catch (e) {
    // Gracefully handle private browsing mode storage restriction
  }

  window.addEventListener('resize', checkMobileScreen, { passive: true });
  window.addEventListener('scroll', handleScroll, { passive: true });
  window.addEventListener('keydown', handleKeydown);
  window.addEventListener('click', handleWindowClick);
  renderMermaidDiagrams();
  nextTick(() => initPerspectiveWidgets());
});

watch(fontSize, (newVal) => {
  try {
    localStorage.setItem(STORAGE_FONT_SIZE_KEY, newVal.toString());
  } catch (e) {}
});

watch(isPaperMode, (newVal) => {
  try {
    localStorage.setItem(STORAGE_PAPER_MODE_KEY, newVal ? 'true' : 'false');
  } catch (e) {}
});

watch(
  () => [props.article.content, isPaperMode.value],
  () => {
    renderMermaidDiagrams();
    nextTick(() => initPerspectiveWidgets());
  }
);

onUnmounted(() => {
  window.removeEventListener('resize', checkMobileScreen);
  window.removeEventListener('scroll', handleScroll);
  window.removeEventListener('keydown', handleKeydown);
  window.removeEventListener('click', handleWindowClick);
});

const ringBell = () => {
  isRinging.value = true;
  mindfulBell.ringBell(432, 6.0);
  setTimeout(() => {
    isRinging.value = false;
  }, 3000);
};

const escapeRegExp = (str: string) => {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
};

const annotateTextSegment = (
  text: string,
  pattern: RegExp,
  termMap: Map<string, PaliGlossaryEntry>,
  termSeenCount: Map<string, number>
) => {
  return text.replace(pattern, (matched) => {
    const entry = termMap.get(matched.toLowerCase());
    if (!entry) return matched;

    const count = termSeenCount.get(entry.term) || 0;
    if (count >= 2) {
      return matched;
    }
    termSeenCount.set(entry.term, count + 1);

    const safeTerm = encodeURIComponent(entry.term);
    const safePali = encodeURIComponent(entry.pali || entry.term);
    const safeVietnamese = encodeURIComponent(entry.vietnamese || matched);
    const safeCategory = encodeURIComponent(entry.category || 'Pháp Học Pāḷi');
    const safeMeaning = encodeURIComponent(entry.definition);

    return `<span class="zen-pali-term" data-term="${safeTerm}" data-pali="${safePali}" data-vietnamese="${safeVietnamese}" data-category="${safeCategory}" data-meaning="${safeMeaning}" tabindex="0" role="button" aria-haspopup="dialog" title="Nhấp để xem giải nghĩa: ${entry.vietnamese || entry.pali}">${matched}</span>`;
  });
};

// Automatic Pāḷi Term Highlighter & Explanations Injector (Only for body paragraphs, strictly skipping Headings, Code, and Buttons)
const annotatePaliTermsInHtml = (html: string) => {
  if (!html) return '';

  const customTerms: PaliGlossaryEntry[] = (props.article.pali_terms || []).map(item => ({
    term: item.term,
    pali: item.term,
    vietnamese: item.term,
    category: 'Thuật Ngữ Bài Kinh',
    definition: item.meaning,
    aliases: []
  }));

  const allTerms = [...customTerms, ...PALI_GLOSSARY];

  const termMap = new Map<string, PaliGlossaryEntry>();
  const searchPhrases: string[] = [];

  allTerms.forEach(entry => {
    const keys = [entry.term, entry.pali, entry.vietnamese, ...(entry.aliases || [])];
    keys.forEach(k => {
      // Must be at least 4 characters to avoid matching small generic words
      if (k && k.trim().length >= 4 && !k.includes('/')) {
        const lower = k.trim().toLowerCase();
        if (!termMap.has(lower)) {
          termMap.set(lower, entry);
          searchPhrases.push(k.trim());
        }
      }
    });
  });

  searchPhrases.sort((a, b) => b.length - a.length);
  if (searchPhrases.length === 0) return html;

  const pattern = new RegExp(`(?<![\\p{L}\\p{N}])(${searchPhrases.map(w => escapeRegExp(w)).join('|')})(?![\\p{L}\\p{N}])`, 'gui');
  const termSeenCount = new Map<string, number>();

  // Excluded tags: Headings, Code, SVGs, Pre, Buttons, Links, Strong
  const tagRegex = /(<\/?([a-z0-9]+)[^>]*>)/gi;
  let lastIndex = 0;
  let result = '';
  let insideExcludedTag = 0;
  const excludedTags = new Set(['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'code', 'pre', 'svg', 'button', 'a', 'strong', 'th', 'td', 'iframe']);

  let match: RegExpExecArray | null;
  while ((match = tagRegex.exec(html)) !== null) {
    const text = html.substring(lastIndex, match.index);
    if (text) {
      if (insideExcludedTag === 0) {
        result += annotateTextSegment(text, pattern, termMap, termSeenCount);
      } else {
        result += text;
      }
    }

    const fullTag = match[1];
    const tagName = match[2].toLowerCase();
    const isClosing = fullTag.startsWith('</');

    if (excludedTags.has(tagName)) {
      if (isClosing) {
        insideExcludedTag = Math.max(0, insideExcludedTag - 1);
      } else if (!fullTag.endsWith('/>')) {
        insideExcludedTag++;
      }
    }

    result += fullTag;
    lastIndex = tagRegex.lastIndex;
  }

  const remainingText = html.substring(lastIndex);
  if (remainingText) {
    if (insideExcludedTag === 0) {
      result += annotateTextSegment(remainingText, pattern, termMap, termSeenCount);
    } else {
      result += remainingText;
    }
  }

  return result;
};

// Clean Heading Text: Strip any emoji / corrupted symbol prefix
const cleanHeadingText = (text: string) => {
  return text.replace(/^[\p{Emoji}\p{Symbol}\p{Punctuation}\s—–]+/u, '').trim();
};

const escapeHtml = (text: string): string => {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
};

// Markdown List Parser (Handles *, -, +, and numbered lists 1., 2. with multi-line text)
const parseMarkdownLists = (content: string, isPaper: boolean): string => {
  const liClass = isPaper
    ? 'text-[#1c1917] leading-relaxed text-base sm:text-lg font-serif'
    : 'text-stone-100 leading-relaxed text-base sm:text-lg font-serif';
  const ulClass = isPaper
    ? 'my-5 space-y-2.5 list-disc pl-6 marker:text-amber-800'
    : 'my-5 space-y-2.5 list-disc pl-6 marker:text-amber-400';
  const olClass = isPaper
    ? 'my-5 space-y-2.5 list-decimal pl-6 marker:text-amber-800'
    : 'my-5 space-y-2.5 list-decimal pl-6 marker:text-amber-400';

  // Process unordered lists: lines starting with *, -, or +
  const unorderedBlockRegex = /(?:^[ \t]*[-*+]\s+[^\n]*(?:\n[ \t]*\n[ \t]*[-*+]\s+[^\n]*|\n[ \t]*[-*+]\s+[^\n]*|\n[ \t]{2,}[^\n]+)*)/gm;

  content = content.replace(unorderedBlockRegex, (block) => {
    const rawLines = block.split('\n');
    const items: string[] = [];
    let currentItem = '';

    for (const rawLine of rawLines) {
      const trimmed = rawLine.trim();
      if (!trimmed) continue;
      const match = rawLine.match(/^[ \t]*[-*+]\s+(.*)$/);
      if (match) {
        if (currentItem) items.push(currentItem);
        currentItem = match[1].trim();
      } else if (currentItem && /^[ \t]{2,}(.*)$/.test(rawLine)) {
        currentItem += ' ' + trimmed;
      }
    }
    if (currentItem) items.push(currentItem);
    if (items.length === 0) return block;

    const lisHtml = items.map(it => `  <li class="${liClass}">${it}</li>`).join('\n');
    return `\n\n<ul class="${ulClass}">\n${lisHtml}\n</ul>\n\n`;
  });

  // Process ordered lists: lines starting with digits + dot + space
  const orderedBlockRegex = /(?:^[ \t]*\d+\.\s+[^\n]*(?:\n[ \t]*\n[ \t]*\d+\.\s+[^\n]*|\n[ \t]*\d+\.\s+[^\n]*|\n[ \t]{2,}[^\n]+)*)/gm;

  content = content.replace(orderedBlockRegex, (block) => {
    const rawLines = block.split('\n');
    const items: string[] = [];
    let currentItem = '';

    for (const rawLine of rawLines) {
      const trimmed = rawLine.trim();
      if (!trimmed) continue;
      const match = rawLine.match(/^[ \t]*\d+\.\s+(.*)$/);
      if (match) {
        if (currentItem) items.push(currentItem);
        currentItem = match[1].trim();
      } else if (currentItem && /^[ \t]{2,}(.*)$/.test(rawLine)) {
        currentItem += ' ' + trimmed;
      }
    }
    if (currentItem) items.push(currentItem);
    if (items.length === 0) return block;

    const lisHtml = items.map(it => `  <li class="${liClass}">${it}</li>`).join('\n');
    return `\n\n<ol class="${olClass}">\n${lisHtml}\n</ol>\n\n`;
  });

  return content;
};

// Markdown Table Parser
const parseMarkdownTables = (content: string, isPaper: boolean): string => {
  const tableRegex = /((?:\|[^\n]+\|\r?\n)(?:\|[-:\|\s]+\|\r?\n)(?:\|[^\n]+\|\r?\n?)+)/g;
  return content.replace(tableRegex, (match) => {
    const lines = match.trim().split(/\r?\n/).filter(l => l.trim().startsWith('|'));
    if (lines.length < 2) return match;
    const headerCells = lines[0].split('|').slice(1, -1).map(c => c.trim());
    const bodyRows = lines.slice(2);

    const thClass = isPaper
      ? 'px-4 py-3 bg-amber-100/90 text-amber-950 font-serif font-bold text-sm sm:text-base text-left border-b border-amber-300'
      : 'px-4 py-3 bg-amber-950/50 text-amber-200 font-serif font-bold text-sm sm:text-base text-left border-b border-amber-500/30';

    const tableClass = isPaper
      ? 'w-full text-left font-serif text-sm sm:text-base border-collapse my-6 bg-white/70 rounded-xl overflow-hidden shadow-sm border border-amber-200'
      : 'w-full text-left font-serif text-sm sm:text-base border-collapse my-6 bg-stone-900/70 rounded-xl overflow-hidden shadow-sm border border-stone-800';

    const tdClass = isPaper
      ? 'px-4 py-3 border-b border-amber-100/80 text-stone-900'
      : 'px-4 py-3 border-b border-stone-800/80 text-stone-200';

    let html = `<div class="overflow-x-auto my-6"><table class="${tableClass}"><thead><tr>`;
    headerCells.forEach(h => {
      html += `<th class="${thClass}">${h}</th>`;
    });
    html += `</tr></thead><tbody>`;
    bodyRows.forEach((row, idx) => {
      const cells = row.split('|').slice(1, -1).map(c => c.trim());
      const rowBg = isPaper
        ? (idx % 2 === 1 ? 'bg-amber-50/50' : 'bg-transparent')
        : (idx % 2 === 1 ? 'bg-stone-900/40' : 'bg-transparent');
      html += `<tr class="${rowBg}">`;
      cells.forEach(c => {
        html += `<td class="${tdClass}">${c}</td>`;
      });
      html += `</tr>`;
    });
    html += `</tbody></table></div>`;
    return html;
  });
};

const parseBlockquotes = (content: string, isPaper: boolean) => {
  return content.replace(/(?:^>[^\n]*(?:\n|$))+/gm, (block) => {
    const lines = block
      .split('\n')
      .map(line => line.replace(/^>[ \t]?/, ''))
      .filter(l => l.trim().length > 0);
    const rawContent = lines.join('\n').trim();
    if (!rawContent) return '';

    let text = rawContent
      .replace(/\r/g, '')
      .replace(/(?:<br\s*\/?>\s*){2,}/gi, '___STANZA_BREAK___')
      .replace(/\n\s*\n+/g, '___STANZA_BREAK___')
      .replace(/<br\s*\/?>/gi, '\n');

    const stanzas = text.split('___STANZA_BREAK___');
    const borderDivider = isPaper ? 'border-amber-900/15' : 'border-amber-500/20';
    const formatted = stanzas.map(st => {
      const stanzaLines = st.split('\n').map(l => l.trim()).filter(l => l.length > 0);
      return stanzaLines.join('<br/>');
    }).filter(s => s.length > 0).join(`<div class="my-3 border-t ${borderDivider}"></div>`);

    const containerStyle = isPaper
      ? 'border-amber-700 bg-amber-50/90 shadow-sm text-[#1c1917]'
      : 'border-amber-500/80 bg-amber-950/30 shadow-lg text-amber-100/95';

    return `\n\n<blockquote class="my-6 pl-5 pr-4 py-4 border-l-4 ${containerStyle} rounded-r-2xl font-serif text-base sm:text-lg leading-relaxed">\n  <div class="not-italic font-serif leading-[1.85]">${formatted}</div>\n</blockquote>\n\n`;
  });
};

// Math and LaTeX Formula / Flow Sanitizer for Markdown
const sanitizeMathAndFlows = (content: string, isPaper: boolean): string => {
  if (!content) return '';

  // Convert $$ ... $$ formula / flow blocks
  content = content.replace(/\$\$\s*([\s\S]*?)\s*\$\$/g, (_match, inner) => {
    const cleanFlow = inner
      .replace(/\\text\{([^}]+)\}/g, '$1')
      .replace(/\\(long)?rightarrow/g, '➔')
      .replace(/\\longrightarrow/g, '➔')
      .replace(/\\rightarrow/g, '➔')
      .trim();

    const bgClass = isPaper
      ? 'bg-amber-100/80 border-amber-300 text-amber-950'
      : 'bg-stone-900/90 border-amber-500/30 text-amber-300';
    return `\n\n<div class="my-4 p-3.5 sm:p-4 rounded-xl border ${bgClass} font-mono text-sm sm:text-base flex items-center justify-center text-center font-medium shadow-sm overflow-x-auto"><span>${cleanFlow}</span></div>\n\n`;
  });

  // Convert inline LaTeX arrows $\rightarrow$ or $\longrightarrow$
  content = content.replace(/\$\\(long)?rightarrow\$/g, '➔');
  content = content.replace(/\\(long)?rightarrow/g, '➔');
  content = content.replace(/\\text\{([^}]+)\}/g, '$1');

  return content;
};

// Rich Markdown to Zen HTML Parser with Sutta-first readability & Pāḷi Annotation
const renderedMarkdown = computed(() => {
  if (!props.article.content) return '';
  // Normalize line endings to LF
  let md = props.article.content.replace(/\r\n/g, '\n').replace(/\r/g, '\n');

  // Sanitize math and raw LaTeX symbols into elegant typography
  md = sanitizeMathAndFlows(md, isPaperMode.value);

  // 1. Mermaid Diagrams (Isolated with double newlines)
  md = md.replace(/```\s*mermaid\s*\n([\s\S]*?)```/gim, (_match, code) => {
    const containerTheme = isPaperMode.value
      ? 'bg-amber-50/80 border-amber-300 shadow-md'
      : 'bg-stone-900 border-amber-500/30 shadow-xl';
    const headerBorder = isPaperMode.value ? 'border-amber-300/80 text-amber-900' : 'border-amber-500/20 text-amber-300';
    const subText = isPaperMode.value ? 'text-amber-800/60' : 'text-stone-400';
    const targetColor = isPaperMode.value ? 'text-stone-900' : 'text-stone-100';

    return `\n\n<div class="zen-mermaid-container my-8 p-4 sm:p-6 rounded-2xl ${containerTheme} border overflow-x-auto flex flex-col items-center justify-center" data-code="${encodeURIComponent(code.trim())}">
      <div class="w-full flex items-center justify-between pb-2.5 mb-3 border-b ${headerBorder} text-xs font-serif font-semibold tracking-wider">
        <span>SƠ ĐỒ PHÁP HỌC & QUÁN CHIẾU</span>
        <span class="text-[10px] ${subText} font-mono">Mermaid Flow</span>
      </div>
      <div class="mermaid-render-target w-full flex justify-center py-2 ${targetColor}">
      </div>
    </div>\n\n`;
  });

  // 1.5 Generic Code Blocks & ASCII Diagrams (Isolated with double newlines)
  md = md.replace(/```([a-zA-Z0-9_-]*)[^\n]*\n([\s\S]*?)```/gim, (_match, lang, code) => {
    const rawLang = (lang || '').trim().toLowerCase();
    const cleanCode = escapeHtml(code.trim());
    const isAsciiOrText = !rawLang || rawLang === 'text' || rawLang === 'ascii';

    const containerTheme = isPaperMode.value
      ? 'bg-amber-50/90 border-amber-300 shadow-sm text-stone-900'
      : 'bg-stone-900/95 border-stone-800 shadow-lg text-amber-200/90';

    const headerBorder = isPaperMode.value
      ? 'border-amber-200/80 bg-amber-100/60 text-amber-900'
      : 'border-stone-800 bg-stone-950/60 text-amber-400';

    const langTitle = isAsciiOrText
      ? 'SƠ ĐỒ PHÁP HỌC & TIẾN TRÌNH QUÁN CHIẾU'
      : (rawLang.toUpperCase() + ' CODE');

    const subBadge = isAsciiOrText ? 'ASCII Diagram' : rawLang.toUpperCase();

    return `\n\n<div class="zen-ascii-diagram my-8 rounded-2xl border ${containerTheme} overflow-hidden font-mono shadow-sm">
      <div class="px-4 py-2.5 border-b ${headerBorder} flex items-center justify-between text-xs font-semibold tracking-wider">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80 inline-block"></span>
          <span class="font-serif">${langTitle}</span>
        </div>
        <span class="text-[10px] opacity-75 font-mono px-2 py-0.5 rounded-full bg-black/10 border border-current/20">${subBadge}</span>
      </div>
      <div class="p-4 sm:p-5 overflow-x-auto text-xs sm:text-sm leading-relaxed whitespace-pre font-mono"><code>${cleanCode}</code></div>
    </div>\n\n`;
  });

  // 2. Dual Perspective Cards
  md = parsePerspectiveBlocks(md, 'theravada', isPaperMode.value);

  // 3. Markdown Tables
  md = parseMarkdownTables(md, isPaperMode.value);

  // 4. Blockquotes (Grouping contiguous lines & stanzas by Pali and Vietnamese)
  md = parseBlockquotes(md, isPaperMode.value);

  // 4.5 Lists (Unordered *, -, + and Ordered 1., 2.)
  md = parseMarkdownLists(md, isPaperMode.value);

  let parsedHtml = '';
  let headingCounter = 0;
  const headingReplacer = (_m: string, hashes: string, p1: string) => {
    headingCounter++;
    const id = `toc-sec-${headingCounter}`;
    const title = cleanHeadingText(p1);
    const level = hashes.length;
    if (level === 2) {
      const h2Class = isPaperMode.value
        ? 'text-xl sm:text-2xl font-bold text-amber-950 mt-9 mb-3.5 pb-2 border-b border-amber-300/80 font-serif leading-snug scroll-mt-24'
        : 'text-xl sm:text-2xl font-bold text-amber-300 mt-9 mb-3.5 pb-2 border-b border-amber-500/30 font-serif leading-snug scroll-mt-24';
      return `<h2 id="${id}" class="${h2Class}">${title}</h2>`;
    } else {
      const h3Class = isPaperMode.value
        ? 'text-lg sm:text-xl font-bold text-amber-950 mt-7 mb-2.5 font-serif leading-snug scroll-mt-24'
        : 'text-lg sm:text-xl font-bold text-amber-200 mt-7 mb-2.5 font-serif leading-snug scroll-mt-24';
      return `<h3 id="${id}" class="${h3Class}">${title}</h3>`;
    }
  };

  if (isPaperMode.value) {
    // 5. Headings (Pure Elegant Typography with TOC anchor IDs)
    md = md.replace(/^(#{2,3})\s+(.*$)/gim, headingReplacer);

    // 6. Horizontal Rules (Minimalist hairline)
    md = md.replace(/^---$/gim, '<div class="my-8 flex items-center justify-center gap-3 text-amber-700/40 select-none"><span class="h-px w-20 bg-amber-300"></span><span class="text-xs">✦</span><span class="h-px w-20 bg-amber-300"></span></div>');

    // 7. Bold & Italic
    md = md.replace(/\*\*(.*?)\*\*/gim, '<strong class="font-bold text-amber-950">$1</strong>');
    md = md.replace(/\*(.*?)\*/gim, '<em class="italic text-stone-800 font-serif">$1</em>');

    // 8. Inline Code
    md = md.replace(/`([^`\n]+)`/g, (_m, code) => `<code class="bg-amber-100/90 text-amber-950 border border-amber-300/80 px-1.5 py-0.5 rounded text-xs sm:text-sm font-mono">${escapeHtml(code)}</code>`);

    // 8.5 Markdown Images (With responsive wrapper and caption)
    md = md.replace(/!\[(.*?)\]\((.*?)\)/gim, '<figure class="my-6 text-center max-w-2xl mx-auto"><img src="$2" alt="$1" class="rounded-2xl max-w-full h-auto mx-auto shadow-md border border-amber-300/40 object-cover" loading="lazy" /><figcaption class="text-xs font-serif text-stone-600 mt-2 italic">$1</figcaption></figure>');

    // 9. Markdown Links (Internal & External)
    md = md.replace(/\[(.*?)\]\((.*?)\)/gim, '<a href="$2" class="zen-internal-link text-amber-800 hover:text-amber-950 font-semibold underline decoration-amber-400 decoration-1 hover:decoration-2 transition-all inline-flex items-center gap-0.5">$1</a>');

    // 10. Paragraphs
    const paragraphs = md.split(/\n\n+/);
    parsedHtml = paragraphs.map(p => {
      p = p.trim();
      if (!p) return '';
      if (p.startsWith('<div') || p.startsWith('<blockquote') || p.startsWith('<h') || p.startsWith('<ul') || p.startsWith('<ol') || p.startsWith('<li') || p.startsWith('<table') || p.startsWith('<figure') || p.startsWith('<iframe')) {
        return p;
      }
      return `<p class="my-4 text-[#1c1917] font-serif leading-[1.95] text-base sm:text-lg text-justify font-normal">${p.replace(/\n/g, '<br/>')}</p>`;
    }).filter(Boolean).join('\n');
  } else {
    // 5. Headings (Night Mode Typography with TOC anchor IDs)
    md = md.replace(/^(#{2,3})\s+(.*$)/gim, headingReplacer);

    // 6. Horizontal Rules
    md = md.replace(/^---$/gim, '<div class="my-8 flex items-center justify-center gap-3 text-amber-500/40 select-none"><span class="h-px w-20 bg-amber-500/30"></span><span class="text-xs">✦</span><span class="h-px w-20 bg-amber-500/30"></span></div>');

    // 7. Bold & Italic
    md = md.replace(/\*\*(.*?)\*\*/gim, '<strong class="font-bold text-amber-300">$1</strong>');
    md = md.replace(/\*(.*?)\*/gim, '<em class="italic text-stone-200 font-serif">$1</em>');

    // 8. Inline Code
    md = md.replace(/`([^`\n]+)`/g, (_m, code) => `<code class="bg-stone-800 text-amber-300 border border-stone-700 px-1.5 py-0.5 rounded text-xs sm:text-sm font-mono">${escapeHtml(code)}</code>`);

    // 8.5 Markdown Images (With responsive wrapper and caption)
    md = md.replace(/!\[(.*?)\]\((.*?)\)/gim, '<figure class="my-6 text-center max-w-2xl mx-auto"><img src="$2" alt="$1" class="rounded-2xl max-w-full h-auto mx-auto shadow-md border border-amber-500/30 object-cover" loading="lazy" /><figcaption class="text-xs font-serif text-stone-400 mt-2 italic">$1</figcaption></figure>');

    // 9. Markdown Links (Internal & External)
    md = md.replace(/\[(.*?)\]\((.*?)\)/gim, '<a href="$2" class="zen-internal-link text-amber-400 hover:text-amber-200 font-semibold underline decoration-amber-500/60 decoration-1 hover:decoration-2 transition-all inline-flex items-center gap-0.5">$1</a>');

    // 10. Paragraphs
    const paragraphs = md.split(/\n\n+/);
    parsedHtml = paragraphs.map(p => {
      p = p.trim();
      if (!p) return '';
      if (p.startsWith('<div') || p.startsWith('<blockquote') || p.startsWith('<h') || p.startsWith('<ul') || p.startsWith('<ol') || p.startsWith('<li') || p.startsWith('<table') || p.startsWith('<figure') || p.startsWith('<iframe')) {
        return p;
      }
      return `<p class="my-4 text-stone-100 font-serif leading-[1.95] text-base sm:text-lg text-justify font-normal">${p.replace(/\n/g, '<br/>')}</p>`;
    }).filter(Boolean).join('\n');
  }

  return annotatePaliTermsInHtml(parsedHtml);
});

const suttaJsonLd = computed(() => ({
  '@context': 'https://schema.org',
  '@type': 'ScholarlyArticle',
  'headline': props.article.title,
  'alternativeHeadline': props.article.pali_title,
  'description': props.article.excerpt,
  'author': {
    '@type': 'Person',
    'name': props.article.author || 'Đại Tạng Kinh Pāḷi Tipiṭaka'
  },
  'datePublished': props.article.published_at,
  'articleSection': props.article.category,
  'inLanguage': ['vi', 'pi'],
  'keywords': props.article.tags ? props.article.tags.join(', ') : 'Theravada, Pāḷi, Sutta, Kinh điển nguyên thủy',
  'mainEntityOfPage': {
    '@type': 'WebPage',
    'id': `https://theravada.macatung.dev/kinh/${props.article.slug}`
  },
  'publisher': {
    '@type': 'Organization',
    'name': 'Ma Tọa Thiền',
    'url': 'https://theravada.macatung.dev'
  }
}));
</script>

<template>
  <TheravadaLayout
    :title="article.title"
    :description="article.excerpt"
    :keywords="`${article.title}, ${article.pali_title || ''}, Theravada, Pāḷi Sutta, Phật giáo nguyên thủy, ${article.tags ? article.tags.join(', ') : ''}`"
    :canonical="`https://theravada.macatung.dev/kinh/${article.slug}`"
    og-type="article"
    :article="{
      publishedTime: article.published_at,
      author: 'Ma Tọa Thiền',
      section: article.category,
      tags: article.tags
    }"
    :json-ld="suttaJsonLd"
  >
    <!-- Top Reading Progress Indicator -->
    <div class="fixed top-0 left-0 w-full h-1 bg-stone-900/60 z-50 overflow-visible pointer-events-none">
      <div
        class="h-full bg-gradient-to-r from-amber-600 to-amber-400 transition-all duration-150 ease-out shadow-[0_0_8px_rgba(251,191,36,0.6)]"
        :style="{ width: `${readingProgress}%` }"
      />
    </div>

    <!-- Ambient Candlelight Aura -->
    <div
      v-if="isCandlelightOn"
      class="fixed inset-0 pointer-events-none z-0 flex items-center justify-center transition-opacity duration-1000"
    >
      <div
        class="w-[850px] h-[850px] rounded-full bg-gradient-to-r from-amber-600/15 via-yellow-500/10 to-orange-600/15 blur-[150px] animate-pulse"
        style="animation-duration: 4s;"
      />
    </div>

    <!-- Floating Pāḷi explanation for highlighted terms (Desktop only) -->
    <div
      v-if="activeTooltip && !isMobileScreen"
      class="zen-pali-popover fixed w-[min(380px,calc(100vw-32px))] rounded-2xl border border-amber-500/70 p-4 text-left font-sans shadow-2xl"
      :style="{
        left: `${activeTooltip.x}px`,
        top: `${activeTooltip.y}px`,
        transform: activeTooltip.placement === 'top' ? 'translateY(-100%)' : 'none'
      }"
      role="dialog"
      aria-live="polite"
      @mouseenter="keepTooltipOpen"
      @mouseleave="hideTooltipWithDelay"
    >
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div class="pali-title text-base font-serif font-bold truncate">{{ activeTooltip.term }}</div>
          <div class="pali-vn text-xs mt-0.5">{{ activeTooltip.vietnamese }}</div>
        </div>
        <button
          type="button"
          class="shrink-0 rounded-lg px-2 py-1 text-stone-400 hover:bg-stone-800 hover:text-white"
          :aria-label="locale === 'en' ? 'Close explanation' : 'Đóng giải thích'"
          @click="closeTooltip"
        >
          ✕
        </button>
      </div>
      <div class="pali-badge inline-flex mt-3 rounded-full border px-2 py-0.5 text-[10px] font-semibold">
        {{ activeTooltip.category }}
      </div>
      <div class="pali-meaning mt-3 rounded-xl border p-3 text-xs leading-relaxed">
        {{ activeTooltip.meaning }}
      </div>
    </div>

    <!-- Mobile Bottom Sheet for Pāḷi Term Explanation -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-y-full opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-full opacity-0"
    >
      <div
        v-if="activeTooltip && isMobileScreen"
        class="zen-pali-mobile-sheet fixed inset-x-0 bottom-0 z-50 p-5 bg-[#0f0d0b] border-t-2 border-amber-500/70 shadow-[0_-20px_60px_rgba(0,0,0,0.95)] rounded-t-3xl max-h-[80vh] overflow-y-auto text-left"
        role="dialog"
        aria-live="polite"
      >
        <div class="w-12 h-1.5 bg-amber-500/40 rounded-full mx-auto mb-4" />

        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="text-lg font-serif font-bold text-amber-300">{{ activeTooltip.term }}</div>
            <div class="text-xs text-amber-200/90 font-serif mt-0.5">{{ activeTooltip.vietnamese }}</div>
          </div>
          <button
            type="button"
            class="p-2 rounded-xl text-stone-400 hover:text-white hover:bg-stone-800 transition-colors shrink-0"
            @click="closeTooltip"
            :aria-label="locale === 'en' ? 'Close' : 'Đóng'"
          >
            ✕
          </button>
        </div>

        <div class="inline-flex mt-3 rounded-full border border-amber-500/40 bg-amber-500/15 px-2.5 py-0.5 text-[11px] font-semibold text-amber-300 font-sans">
          {{ activeTooltip.category }}
        </div>

        <div class="mt-3.5 rounded-2xl bg-stone-900 border border-stone-800 p-4 text-stone-200 text-sm font-serif leading-relaxed">
          {{ activeTooltip.meaning }}
        </div>
      </div>
    </transition>

    <!-- Backdrop for mobile pali sheet -->
    <div
      v-if="activeTooltip && isMobileScreen"
      class="fixed inset-0 bg-black/70 backdrop-blur-xs z-40"
      @click="closeTooltip"
    />

    <div class="max-w-4xl mx-auto py-4 sm:py-10 px-0 sm:px-4 relative z-10 pb-20 sm:pb-0">
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center justify-between text-xs font-serif text-stone-400 mb-4 sm:mb-6 px-4 sm:px-0" aria-label="Breadcrumb">
        <!-- Mobile: clean back button to category -->
        <Link
          :href="`/theravada/danh-muc/${article.category}`"
          class="sm:hidden inline-flex items-center gap-1.5 text-amber-400 hover:text-amber-300 font-medium py-1"
        >
          <span>←</span>
          <span class="truncate max-w-[220px]">{{ categoryLabel(article.category) }}</span>
        </Link>

        <!-- Desktop: full 3-tier breadcrumb -->
        <div class="hidden sm:flex items-center gap-2">
          <Link href="/theravada" class="hover:text-amber-300">Theravāda</Link>
          <span>/</span>
          <Link :href="`/theravada/danh-muc/${article.category}`" class="hover:text-amber-300">
            {{ categoryLabel(article.category) }}
          </Link>
          <span>/</span>
          <span class="text-amber-400 font-bold truncate max-w-[200px] sm:max-w-md">
            {{ article.title }}
          </span>
        </div>

        <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs text-stone-400 font-serif shrink-0 px-2 py-0.5 rounded-full bg-stone-900/80 border border-stone-800/80">
          <span>⏱️</span>
          <span>{{ article.reading_time_min }} {{ t('theravada.minutes') }}</span>
        </span>
      </nav>

      <!-- Article Header -->
      <header class="mb-6 sm:mb-8 text-left border-b border-stone-800 pb-5 sm:pb-6 px-4 sm:px-0">
        <div v-if="hasMediaAttachment" class="flex flex-wrap items-center gap-2 mb-2.5 sm:mb-3">
          <span class="hidden sm:inline-flex px-3 py-1 rounded-full text-xs font-serif font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
            {{ categoryLabel(article.category) }}
          </span>
          <a
            href="#phap-am-dinh-kem"
            class="px-2.5 py-1 rounded-full text-xs font-serif font-medium bg-amber-500/10 text-amber-300 hover:text-amber-100 hover:bg-amber-500/20 border border-amber-500/30 transition-all inline-flex items-center gap-1.5"
          >
            <span>🎧</span>
            <span>{{ locale === 'en' ? 'Attached Media' : 'Có video & pháp âm đính kèm' }}</span>
            <span class="text-[10px] text-amber-400">↓</span>
          </a>
        </div>
        <div v-else class="hidden sm:flex items-center gap-2 mb-3">
          <span class="px-3 py-1 rounded-full text-xs font-serif font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
            {{ categoryLabel(article.category) }}
          </span>
        </div>

        <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-amber-100 leading-snug sm:leading-tight mb-2.5 sm:mb-3 tracking-tight">
          {{ article.title }}
        </h1>

        <div v-if="article.pali_title" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-amber-500/10 border border-amber-500/25 text-amber-300/90 mb-3.5 sm:mb-4 text-xs sm:text-sm md:text-base font-serif italic max-w-full">
          <span class="text-xs not-italic select-none opacity-80">🪷</span>
          <span class="truncate sm:whitespace-normal"><span class="font-semibold not-italic">Pāḷi:</span> {{ article.pali_title }}</span>
        </div>

        <!-- Dual Perspective Header Banner if counterpart exists -->
        <DualPerspectiveHeaderBanner
          v-if="paired_article"
          :paired-article="paired_article"
          current-type="theravada"
        />

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 sm:pt-4 border-t border-stone-900 text-xs font-serif text-stone-400">
          <span class="italic text-[11px] sm:text-xs">{{ locale === 'en' ? 'Author / Source' : 'Tác giả / Nguồn' }}: <strong class="text-stone-200 not-italic">{{ article.author || 'Pāḷi Tipiṭaka' }}</strong></span>

          <!-- Desktop Reader Controls: Minimalist Toolbar (hidden on mobile, bottom floating bar handles mobile) -->
          <div class="hidden sm:flex items-center gap-1.5 overflow-x-auto pb-1.5 pt-0.5 flex-wrap no-scrollbar">
            <!-- Paper / Night Mode Toggle -->
            <button
              @click="togglePaperMode"
              :class="[
                'px-2.5 sm:px-3 py-1.5 rounded-xl border transition-all cursor-pointer font-medium text-[11px] sm:text-xs shadow-sm whitespace-nowrap shrink-0',
                isPaperMode
                  ? 'bg-amber-100 text-stone-900 border-amber-400 font-bold'
                  : 'bg-stone-900 text-stone-300 border-stone-800 hover:border-amber-500/40'
              ]"
              :title="isPaperMode ? t('theravada.switchNight') : t('theravada.switchPaper')"
            >
              <span>{{ isPaperMode ? `📜 ${t('theravada.paperMode')}` : `🌙 ${t('theravada.nightMode')}` }}</span>
            </button>

            <!-- Falling Leaves Toggle -->
            <button
              @click="toggleLeaves"
              :class="[
                'px-2.5 sm:px-3 py-1.5 rounded-xl border transition-all cursor-pointer font-medium text-[11px] sm:text-xs shadow-sm whitespace-nowrap shrink-0',
                isLeavesEnabled
                  ? 'bg-amber-500/20 text-amber-300 border-amber-500/40'
                  : 'bg-stone-900 text-stone-400 border-stone-800 hover:border-stone-700'
              ]"
              :title="t('theravada.toggleLeaves')"
            >
              <span>🍃 {{ t('theravada.leaves') }}</span>
            </button>

            <!-- Candlelight Glow Toggle -->
            <button
              @click="toggleCandlelight"
              :class="[
                'px-2.5 sm:px-3 py-1.5 rounded-xl border transition-all cursor-pointer font-medium text-[11px] sm:text-xs whitespace-nowrap shrink-0',
                isCandlelightOn
                  ? 'bg-amber-500 text-stone-950 border-amber-400 font-bold'
                  : 'bg-stone-900 text-stone-300 border-stone-800 hover:border-amber-500/40'
              ]"
              :title="t('theravada.toggleCandle')"
            >
              <span>🕯️ {{ t('theravada.candle') }}</span>
            </button>

            <!-- Focus Paragraph Mode Toggle -->
            <button
              @click="toggleFocusMode"
              :class="[
                'px-2.5 sm:px-3 py-1.5 rounded-xl border transition-all cursor-pointer font-medium text-[11px] sm:text-xs whitespace-nowrap shrink-0',
                isFocusModeOn
                  ? 'bg-amber-500 text-stone-950 border-amber-400 font-bold'
                  : 'bg-stone-900 text-stone-300 border-stone-800 hover:border-amber-500/40'
              ]"
              :title="t('theravada.toggleFocus')"
            >
              <span>🎯 {{ t('theravada.focus') }}</span>
            </button>

            <!-- Bell Trigger -->
            <button
              @click="ringBell"
              class="px-2.5 sm:px-3 py-1.5 rounded-xl bg-amber-500/15 text-amber-300 hover:bg-amber-500/25 border border-amber-500/30 transition-all cursor-pointer font-medium text-[11px] sm:text-xs whitespace-nowrap shrink-0"
              :class="{ 'animate-pulse ring-2 ring-amber-400': isRinging }"
              :title="t('theravada.ringBell')"
            >
              <span>🔔 {{ t('theravada.bell') }}</span>
            </button>

            <!-- Share Article Trigger -->
            <button
              @click="handleNativeArticleShare"
              class="px-2.5 sm:px-3 py-1.5 rounded-xl bg-stone-900 text-stone-300 hover:text-amber-200 hover:bg-stone-800 border border-stone-800 hover:border-amber-500/40 transition-all cursor-pointer font-medium text-[11px] sm:text-xs whitespace-nowrap shrink-0"
              :title="t('theravada.shareArticle')"
            >
              <span>{{ copiedLink ? `✅ ${t('common.copied')}` : `🔗 ${t('common.share')}` }}</span>
            </button>

            <!-- Adjust Font Size -->
            <div class="flex items-center bg-stone-900 rounded-xl border border-stone-800 p-0.5 text-xs whitespace-nowrap shrink-0">
              <button
                @click="fontSize = Math.max(15, fontSize - 1)"
                class="px-2 py-1 hover:bg-stone-800 rounded-lg text-stone-300 font-bold"
                :title="t('theravada.decreaseFont')"
              >
                A-
              </button>
              <span class="px-1.5 font-mono text-amber-300 text-[11px] sm:text-xs">{{ fontSize }}px</span>
              <button
                @click="fontSize = Math.min(26, fontSize + 1)"
                class="px-2 py-1 hover:bg-stone-800 rounded-lg text-stone-300 font-bold"
                :title="t('theravada.increaseFont')"
              >
                A+
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Text Body (Full-bleed on mobile, elegant card on desktop) -->
      <article
        :class="[
          'zen-article-content font-serif leading-relaxed rounded-none sm:rounded-3xl px-4 py-6 sm:p-10 lg:p-12 mb-8 sm:mb-12 relative overflow-hidden transition-all duration-500 shadow-xl sm:shadow-2xl',
          isPaperMode
            ? 'is-paper-mode bg-[#faf7ee] text-[#1c1917] border-y sm:border border-amber-600/20 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.4)]'
            : 'is-night-mode bg-[#141210] text-stone-200 border-y sm:border border-amber-500/30 backdrop-blur-md',
          { 'focus-mode-active': isFocusModeOn }
        ]"
        :style="{ fontSize: `${fontSize}px` }"
        @mouseover="handleArticleInteraction"
        @mouseout="handleArticleInteraction"
        @click="handleArticleInteraction"
      >
        <!-- Top Golden Accent Bar -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-600 via-amber-400 to-yellow-400" />

        <!-- Notification Banner about Pāḷi Term Highlights (Mobile-friendly & dismissible) -->
        <div
          v-if="!isPaliHintDismissed"
          :class="[
            'mb-6 flex items-center justify-between gap-3 text-xs sm:text-sm font-serif px-3.5 py-2.5 sm:px-4 sm:py-3 rounded-xl shadow-sm border transition-all',
            isPaperMode
              ? 'bg-amber-100/70 text-amber-950 border-amber-300/80'
              : 'bg-stone-900/90 text-stone-200 border-amber-500/40'
          ]"
        >
          <div class="flex items-center gap-2 text-left min-w-0">
            <span class="text-amber-500 text-xs shrink-0">✦</span>
            <span class="truncate sm:whitespace-normal">
              <strong :class="isPaperMode ? 'text-amber-900' : 'text-amber-300'" class="font-semibold">{{ t('theravada.glossary') }}:</strong>
              <span class="hidden sm:inline"> {{ t('theravada.glossaryHint') }}</span>
              <span class="sm:hidden"> {{ t('theravada.glossaryHintMobile') }}</span>
            </span>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span class="text-[10px] font-mono border px-2 py-0.5 rounded-lg shrink-0 hidden sm:inline" :class="isPaperMode ? 'text-amber-900 border-amber-400/40 bg-amber-50/60' : 'text-amber-300 border-amber-500/30 bg-stone-950'">Pāḷi Canon</span>
            <button
              type="button"
              @click="isPaliHintDismissed = true"
              class="text-xs px-1.5 py-0.5 rounded hover:bg-black/10 dark:hover:bg-white/10 transition-colors cursor-pointer text-stone-400 hover:text-stone-200"
              title="Đóng thông báo"
              aria-label="Đóng thông báo"
            >
              ✕
            </button>
          </div>
        </div>

        <div class="space-y-4 font-serif text-left antialiased" v-html="renderedMarkdown" />
      </article>

      <!-- Dual Perspective Footer Callout -->
      <div class="px-4 sm:px-0">
        <DualPerspectiveFooterCard
          v-if="paired_article"
          :paired-article="paired_article"
          current-type="theravada"
        />
      </div>

      <!-- Pāḷi Terms Annotation Box (if present) -->
      <div
        v-if="article.pali_terms && article.pali_terms.length > 0"
        class="my-8 sm:my-10 p-5 sm:p-7 mx-4 sm:mx-0 rounded-2xl bg-stone-900/90 border border-amber-500/30 shadow-xl text-left"
      >
        <div class="text-amber-300 font-serif font-bold text-base mb-4 flex items-center gap-2">
          <span class="text-xs">✦</span>
          <span>{{ t('theravada.inArticleGlossary') }}</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
          <div
            v-for="item in article.pali_terms"
            :key="item.term"
            class="p-3.5 rounded-xl bg-stone-950/80 border border-stone-800"
          >
            <span class="text-sm font-serif font-bold text-amber-300">{{ item.term }}</span>
            <p class="text-xs font-serif text-stone-400 mt-1 leading-relaxed">{{ item.meaning }}</p>
          </div>
        </div>
      </div>

      <!-- Social Sharing Bar (Lan Tỏa Chánh Pháp) -->
      <div class="my-8 sm:my-10 p-5 sm:p-7 mx-4 sm:mx-0 rounded-2xl bg-stone-900/90 border border-amber-500/30 shadow-xl text-left">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="space-y-1">
            <div class="text-amber-300 font-serif font-bold text-sm sm:text-base flex items-center gap-2">
              <span class="text-xs">✦</span>
              <span>{{ t('theravada.shareTitle') }}</span>
            </div>
            <p class="text-xs font-serif text-stone-300">
              {{ t('theravada.shareDescription') }}
            </p>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <button
              @click="shareArticleToFacebook"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#1877F2]/15 hover:bg-[#1877F2] text-[#4ea1ff] hover:text-white border border-[#1877F2]/40 transition-all font-sans text-xs font-semibold shadow-sm cursor-pointer"
              :title="'Share on Facebook'"
            >
              <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              <span>Facebook</span>
            </button>

            <button
              @click="shareArticleToZalo"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#0068FF]/15 hover:bg-[#0068FF] text-[#5295ff] hover:text-white border border-[#0068FF]/40 transition-all font-sans text-xs font-semibold shadow-sm cursor-pointer"
              :title="'Share via Zalo'"
            >
              <span class="font-bold font-sans text-[11px] px-1 py-0.2 bg-blue-500/30 rounded text-white">Z</span>
              <span>Zalo</span>
            </button>

            <button
              @click="shareArticleToTelegram"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#24A1DE]/15 hover:bg-[#24A1DE] text-[#55c0f5] hover:text-white border border-[#24A1DE]/40 transition-all font-sans text-xs font-semibold shadow-sm cursor-pointer"
              :title="'Send via Telegram'"
            >
              <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
              </svg>
              <span>Telegram</span>
            </button>

            <button
              @click="shareArticleToX"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-stone-950 hover:bg-stone-900 text-stone-200 hover:text-white border border-stone-700 transition-all font-sans text-xs font-semibold shadow-sm cursor-pointer"
              :title="'Post on X'"
            >
              <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
              </svg>
              <span>X</span>
            </button>

            <button
              @click="copyArticleLink"
              class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-stone-950 hover:bg-stone-900 text-amber-300 hover:text-amber-200 border border-amber-500/40 transition-all font-sans text-xs font-semibold shadow-sm cursor-pointer"
              :title="t('common.copyLink')"
            >
              <span>{{ copiedLink ? t('common.copied') : t('common.copyLink') }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Footer Actions & Tags -->
      <div class="mt-8 sm:mt-10 pt-6 px-4 sm:px-0 border-t border-stone-800 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
          <span class="text-xs font-serif text-stone-400">Từ khóa:</span>
          <span
            v-for="tag in (article.tags || [])"
            :key="tag"
            class="px-2.5 py-1 rounded-full text-xs font-serif bg-stone-900 text-stone-300 border border-stone-800"
          >
            #{{ tag }}
          </span>
        </div>

        <Link
          href="/theravada"
          class="text-xs font-serif font-bold text-amber-400 hover:text-amber-300 flex items-center gap-1.5"
        >
          <span>← {{ t('theravada.backHome') }}</span>
        </Link>
      </div>
    </div>

    <!-- Mobile Zen Bottom Floating Bar (sm:hidden) -->
    <div
      class="sm:hidden fixed bottom-0 left-0 right-0 z-40 px-3 pb-[max(12px,env(safe-area-inset-bottom))] pt-2.5 bg-[#141210]/95 backdrop-blur-xl border-t border-amber-500/30 shadow-[0_-10px_35px_rgba(0,0,0,0.9)] flex items-center justify-between gap-1.5 select-none"
    >
      <!-- Paper / Night Mode Toggle -->
      <button
        type="button"
        @click="togglePaperMode"
        class="flex-1 py-2 px-2 rounded-xl border flex items-center justify-center gap-1 text-xs font-medium transition-all active:scale-95"
        :class="isPaperMode ? 'bg-amber-100 text-stone-950 border-amber-400 font-bold shadow-sm' : 'bg-stone-900 text-stone-300 border-stone-800'"
        :title="isPaperMode ? t('theravada.switchNight') : t('theravada.switchPaper')"
      >
        <span>{{ isPaperMode ? '📜 Giấy' : '🌙 Đêm' }}</span>
      </button>

      <!-- Font Size Quick Adjust -->
      <div class="flex items-center bg-stone-900 rounded-xl border border-stone-800 px-1 py-0.5">
        <button
          type="button"
          @click="fontSize = Math.max(15, fontSize - 1)"
          class="px-2 py-1 text-stone-300 font-bold text-xs active:scale-95"
          :title="t('theravada.decreaseFont')"
          aria-label="Giảm cỡ chữ"
        >
          A-
        </button>
        <span class="px-1 text-xs font-mono text-amber-300 font-bold min-w-[28px] text-center">{{ fontSize }}</span>
        <button
          type="button"
          @click="fontSize = Math.min(26, fontSize + 1)"
          class="px-2 py-1 text-stone-300 font-bold text-xs active:scale-95"
          :title="t('theravada.increaseFont')"
          aria-label="Tăng cỡ chữ"
        >
          A+
        </button>
      </div>

      <!-- Mindful Bell Ring Trigger -->
      <button
        type="button"
        @click="ringBell"
        class="p-2 rounded-xl border border-amber-500/30 bg-amber-500/15 text-amber-300 active:scale-95 transition-all text-xs flex items-center justify-center shrink-0 min-w-[40px]"
        :class="{ 'animate-pulse ring-2 ring-amber-400 bg-amber-500/30': isRinging }"
        :title="t('theravada.ringBell')"
        aria-label="Thỉnh chuông"
      >
        <span>🔔</span>
      </button>

      <!-- TOC & Zen Sheet Menu Button -->
      <button
        type="button"
        @click="isZenSheetOpen = true"
        class="py-2 px-2.5 rounded-xl border border-amber-500/30 bg-stone-900 text-amber-300 active:scale-95 transition-all text-xs font-medium flex items-center justify-center gap-1.5 shrink-0"
        aria-label="Mở menu đọc và mục lục"
      >
        <span>📑</span>
        <span class="font-serif text-[11px]">{{ t('theravada.toc') }}</span>
        <span v-if="tocHeadings.length > 0" class="text-[10px] bg-amber-500/25 px-1 py-0.2 rounded-full font-mono font-bold">{{ tocHeadings.length }}</span>
      </button>
    </div>

    <!-- Mobile Zen Bottom Sheet (TOC, Reading Tools & Share) -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-y-full opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-full opacity-0"
    >
      <div
        v-if="isZenSheetOpen && isMobileScreen"
        class="fixed inset-x-0 bottom-0 z-50 p-5 bg-[#12100e] border-t-2 border-amber-500/60 shadow-[0_-20px_60px_rgba(0,0,0,0.95)] rounded-t-3xl max-h-[85vh] overflow-y-auto text-left flex flex-col"
        role="dialog"
        aria-live="polite"
      >
        <!-- Pull Handle -->
        <div class="w-12 h-1.5 bg-amber-500/30 rounded-full mx-auto mb-3" />

        <div class="flex items-center justify-between pb-3 border-b border-stone-800">
          <div>
            <h3 class="text-base font-serif font-bold text-amber-300">{{ t('theravada.readingPreferences') }}</h3>
            <p class="text-xs text-stone-400 font-serif mt-0.5 truncate max-w-[250px]">{{ article.title }}</p>
          </div>
          <button
            type="button"
            class="p-2 rounded-xl text-stone-400 hover:text-white hover:bg-stone-800 transition-colors"
            @click="isZenSheetOpen = false"
            aria-label="Đóng bảng tuỳ chỉnh"
          >
            ✕
          </button>
        </div>

        <!-- Section 1: Table of Contents (TOC) -->
        <div class="my-4">
          <div class="flex items-center justify-between mb-2.5">
            <span class="text-xs font-serif font-bold text-amber-400 flex items-center gap-1.5">
              <span>📑</span>
              <span>{{ t('theravada.toc') }}</span>
            </span>
            <span class="text-[10px] text-stone-400 font-serif italic">{{ t('theravada.tocDescription') }}</span>
          </div>

          <div v-if="tocHeadings.length > 0" class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
            <button
              v-for="heading in tocHeadings"
              :key="heading.id"
              type="button"
              @click="scrollToHeading(heading.id)"
              class="w-full text-left px-3 py-2 rounded-xl text-xs font-serif transition-colors flex items-start gap-2 group hover:bg-amber-500/10 active:bg-amber-500/20 cursor-pointer"
              :class="heading.level === 2 ? 'bg-stone-900/80 text-amber-200 font-medium' : 'bg-stone-950/60 text-stone-300 pl-6 text-[11px]'"
            >
              <span class="text-amber-500/60 group-hover:text-amber-400 text-[10px] mt-0.5">✦</span>
              <span class="leading-relaxed line-clamp-2">{{ heading.title }}</span>
            </button>
          </div>
          <div v-else class="text-xs font-serif text-stone-400 italic p-3 bg-stone-900/50 rounded-xl">
            Đang đọc toàn văn bài kinh
          </div>
        </div>

        <!-- Section 2: Atmosphere Tools -->
        <div class="my-3 pt-3 border-t border-stone-800">
          <span class="text-xs font-serif font-bold text-amber-400 flex items-center gap-1.5 mb-2.5">
            <span>🧘</span>
            <span>{{ t('theravada.zenControls') }}</span>
          </span>

          <div class="grid grid-cols-3 gap-2">
            <!-- Falling Leaves Toggle -->
            <button
              type="button"
              @click="toggleLeaves"
              class="py-2.5 px-2 rounded-xl border text-center text-xs font-serif transition-all cursor-pointer active:scale-95"
              :class="isLeavesEnabled ? 'bg-amber-500/20 text-amber-300 border-amber-500/50 font-bold' : 'bg-stone-900 text-stone-400 border-stone-800'"
            >
              <div>🍃</div>
              <div class="text-[11px] mt-1">{{ t('theravada.leaves') }}</div>
            </button>

            <!-- Candlelight Glow Toggle -->
            <button
              type="button"
              @click="toggleCandlelight"
              class="py-2.5 px-2 rounded-xl border text-center text-xs font-serif transition-all cursor-pointer active:scale-95"
              :class="isCandlelightOn ? 'bg-amber-500 text-stone-950 border-amber-400 font-bold' : 'bg-stone-900 text-stone-300 border-stone-800'"
            >
              <div>🕯️</div>
              <div class="text-[11px] mt-1">{{ t('theravada.candle') }}</div>
            </button>

            <!-- Focus Mode Toggle -->
            <button
              type="button"
              @click="toggleFocusMode"
              class="py-2.5 px-2 rounded-xl border text-center text-xs font-serif transition-all cursor-pointer active:scale-95"
              :class="isFocusModeOn ? 'bg-amber-500 text-stone-950 border-amber-400 font-bold' : 'bg-stone-900 text-stone-300 border-stone-800'"
            >
              <div>🎯</div>
              <div class="text-[11px] mt-1">{{ t('theravada.focus') }}</div>
            </button>
          </div>
        </div>

        <!-- Section 3: Quick Share -->
        <div class="mt-3 pt-3 border-t border-stone-800">
          <span class="text-xs font-serif font-bold text-amber-400 flex items-center gap-1.5 mb-2.5">
            <span>🔗</span>
            <span>{{ t('theravada.shareTitle') }}</span>
          </span>

          <div class="grid grid-cols-4 gap-2">
            <button
              type="button"
              @click="shareArticleToFacebook"
              class="py-2 px-1 rounded-xl bg-[#1877F2]/20 border border-[#1877F2]/40 text-[#5295ff] text-[11px] font-sans font-medium flex flex-col items-center justify-center gap-1 active:scale-95 cursor-pointer"
            >
              <span>Facebook</span>
            </button>
            <button
              type="button"
              @click="shareArticleToZalo"
              class="py-2 px-1 rounded-xl bg-[#0068FF]/20 border border-[#0068FF]/40 text-[#5295ff] text-[11px] font-sans font-medium flex flex-col items-center justify-center gap-1 active:scale-95 cursor-pointer"
            >
              <span>Zalo</span>
            </button>
            <button
              type="button"
              @click="shareArticleToTelegram"
              class="py-2 px-1 rounded-xl bg-[#24A1DE]/20 border border-[#24A1DE]/40 text-[#55c0f5] text-[11px] font-sans font-medium flex flex-col items-center justify-center gap-1 active:scale-95 cursor-pointer"
            >
              <span>Telegram</span>
            </button>
            <button
              type="button"
              @click="copyArticleLink"
              class="py-2 px-1 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 text-[11px] font-sans font-medium flex flex-col items-center justify-center gap-1 active:scale-95 cursor-pointer"
            >
              <span>{{ copiedLink ? '✓ Đã chép' : 'Sao chép' }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Backdrop for mobile zen sheet -->
    <div
      v-if="isZenSheetOpen && isMobileScreen"
      class="fixed inset-0 bg-black/70 backdrop-blur-xs z-40"
      @click="isZenSheetOpen = false"
    />

    <!-- Floating quick switch pill -->
    <DualPerspectiveFloatingPill
      v-if="paired_article"
      :paired-article="paired_article"
      current-type="theravada"
    />
  </TheravadaLayout>
</template>

<style scoped>
/* Focus paragraph mode */
.focus-mode-active :deep(p),
.focus-mode-active :deep(blockquote),
.focus-mode-active :deep(li),
.focus-mode-active :deep(.zen-mermaid-container) {
  opacity: 0.35;
  transition: all 0.3s ease;
  filter: blur(0.3px);
}

.focus-mode-active :deep(p:hover),
.focus-mode-active :deep(blockquote:hover),
.focus-mode-active :deep(li:hover),
.focus-mode-active :deep(.zen-mermaid-container:hover) {
  opacity: 1;
  filter: none;
  background: rgba(245, 158, 11, 0.04);
  border-radius: 6px;
}

/* Pāḷi Term Highlight & Tooltip Trigger */
:deep(.zen-pali-term) {
  cursor: help;
  font-weight: 600;
  display: inline;
  border-bottom: 1.5px dotted #b45309;
  color: #78350f;
  padding: 0 1px;
  border-radius: 2px;
  transition: all 0.2s ease;
  text-decoration: none;
}

:deep(.zen-pali-term:hover),
:deep(.zen-pali-term:focus) {
  background-color: rgba(251, 191, 36, 0.22);
  color: #92400e;
  border-bottom-style: solid;
  border-bottom-color: #d97706;
  outline: none;
}

/* Night Mode Colors for terms */
:global(.dark) :deep(.zen-pali-term),
.zen-article-content.is-night-mode :deep(.zen-pali-term) {
  border-bottom: 1.5px dotted #fbbf24;
  color: #fde68a;
}

:global(.dark) :deep(.zen-pali-term:hover),
:global(.dark) :deep(.zen-pali-term:focus),
.zen-article-content.is-night-mode :deep(.zen-pali-term:hover) {
  background-color: rgba(120, 53, 15, 0.45);
  color: #fef3c7;
  border-bottom-style: solid;
  border-bottom-color: #f59e0b;
}

/* High Contrast Popover Enforcements */
:global(.zen-pali-popover),
:global(.zen-pali-mobile-sheet) {
  background-color: #0c0a09 !important;
  color: #f5f5f4 !important;
  box-shadow: 0 25px 80px -10px rgba(0, 0, 0, 0.98), 0 0 0 2px rgba(245, 158, 11, 0.8) !important;
  opacity: 1 !important;
}

:global(.zen-pali-popover) {
  z-index: 99999 !important;
}

:global(.zen-pali-popover .pali-title),
:global(.zen-pali-mobile-sheet .pali-title) {
  color: #fcd34d !important;
}

:global(.zen-pali-popover .pali-vn),
:global(.zen-pali-mobile-sheet .pali-vn) {
  color: #fde68a !important;
}

:global(.zen-pali-popover .pali-meaning),
:global(.zen-pali-mobile-sheet .pali-meaning) {
  background-color: #1c1917 !important;
  color: #ffffff !important;
  border-color: #292524 !important;
}

:global(.zen-pali-popover .pali-badge),
:global(.zen-pali-mobile-sheet .pali-badge) {
  background-color: rgba(245, 158, 11, 0.2) !important;
  color: #fcd34d !important;
  border-color: rgba(245, 158, 11, 0.5) !important;
}

/* Responsive 16:9 Video Wrapper Enforcements */
:deep(.zen-video-wrapper),
:deep(.aspect-video) {
  position: relative !important;
  width: 100% !important;
  padding-top: 56.25% !important; /* Strictly Lock to 16:9 Ratio */
  height: 0 !important;
  overflow: hidden !important;
  border-radius: 1rem !important;
  background-color: #000000 !important;
}

:deep(.zen-video-wrapper iframe),
:deep(.aspect-video iframe),
:deep(.zen-article-content iframe) {
  position: absolute !important;
  top: 0 !important;
  left: 0 !important;
  width: 100% !important;
  height: 100% !important;
  border: 0 !important;
}

/* Core Canvas Solid Background & Text Contrast Enforcements */
.zen-article-content.is-paper-mode {
  background-color: #faf7ee !important;
  color: #1c1917 !important;
  border-color: rgba(217, 119, 6, 0.25) !important;
}

.zen-article-content.is-night-mode {
  background-color: #141210 !important;
  color: #f5f5f4 !important;
  border-color: rgba(245, 158, 11, 0.3) !important;
}

.zen-article-content.is-paper-mode :deep(p),
.zen-article-content.is-paper-mode :deep(li) {
  color: #1c1917;
}

.zen-article-content.is-paper-mode :deep(h1),
.zen-article-content.is-paper-mode :deep(h2),
.zen-article-content.is-paper-mode :deep(h3),
.zen-article-content.is-paper-mode :deep(h4),
.zen-article-content.is-paper-mode :deep(strong) {
  color: #451a03;
}

.zen-article-content.is-night-mode :deep(p),
.zen-article-content.is-night-mode :deep(li) {
  color: #f5f5f4;
}

.zen-article-content.is-night-mode :deep(h1),
.zen-article-content.is-night-mode :deep(h2),
.zen-article-content.is-night-mode :deep(h3),
.zen-article-content.is-night-mode :deep(h4),
.zen-article-content.is-night-mode :deep(strong) {
  color: #fcd34d;
}

/* Zen Opening Quote Box Contrast (Paper Mode vs Night Mode) */
.zen-article-content.is-paper-mode :deep(.zen-opening-quote),
:deep(.is-paper-mode .zen-opening-quote) {
  background-color: #fef9ee !important;
  border: 1.5px solid rgba(217, 119, 6, 0.45) !important;
  box-shadow: 0 4px 15px rgba(180, 83, 9, 0.08) !important;
}
.zen-article-content.is-paper-mode :deep(.zen-opening-quote),
.zen-article-content.is-paper-mode :deep(.zen-opening-quote p),
:deep(.is-paper-mode .zen-opening-quote),
:deep(.is-paper-mode .zen-opening-quote p) {
  color: #451a03 !important; /* Deep dark warm brown for high contrast on light paper */
  font-weight: 500 !important;
}

.zen-article-content.is-night-mode :deep(.zen-opening-quote),
:deep(.is-night-mode .zen-opening-quote) {
  background-color: rgba(245, 158, 11, 0.12) !important;
  border: 1.5px solid rgba(245, 158, 11, 0.45) !important;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6) !important;
}
.zen-article-content.is-night-mode :deep(.zen-opening-quote),
.zen-article-content.is-night-mode :deep(.zen-opening-quote p),
:deep(.is-night-mode .zen-opening-quote),
:deep(.is-night-mode .zen-opening-quote p) {
  color: #fef3c7 !important; /* Bright warm gold for high contrast in dark mode */
  font-weight: 500 !important;
}

/* Zen Media Card Contrast (Paper Mode vs Night Mode) */
.zen-article-content.is-paper-mode :deep(.zen-media-card),
:deep(.is-paper-mode .zen-media-card) {
  background-color: #fdfbf7 !important;
  border: 1.5px solid rgba(217, 119, 6, 0.45) !important;
  box-shadow: 0 10px 30px -5px rgba(180, 83, 9, 0.12) !important;
}
.zen-article-content.is-paper-mode :deep(.zen-media-card-header),
:deep(.is-paper-mode .zen-media-card-header) {
  border-color: rgba(217, 119, 6, 0.25) !important;
}
.zen-article-content.is-paper-mode :deep(.zen-media-card-icon),
:deep(.is-paper-mode .zen-media-card-icon) {
  background-color: rgba(245, 158, 11, 0.2) !important;
  color: #92400e !important;
  border: 1px solid rgba(217, 119, 6, 0.3) !important;
}
.zen-article-content.is-paper-mode :deep(.zen-media-card-title),
:deep(.is-paper-mode .zen-media-card-title) {
  color: #451a03 !important; /* Deep dark amber-950 */
}
.zen-article-content.is-paper-mode :deep(.zen-media-card-subtitle),
.zen-article-content.is-paper-mode :deep(.zen-media-card-caption),
:deep(.is-paper-mode .zen-media-card-subtitle),
:deep(.is-paper-mode .zen-media-card-caption) {
  color: #78350f !important; /* Deep warm amber-900 */
}

.zen-article-content.is-night-mode :deep(.zen-media-card),
:deep(.is-night-mode .zen-media-card) {
  background-color: #0c0a09 !important;
  border: 1.5px solid rgba(245, 158, 11, 0.5) !important;
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.8) !important;
}
.zen-article-content.is-night-mode :deep(.zen-media-card-header),
:deep(.is-night-mode .zen-media-card-header) {
  border-color: rgba(245, 158, 11, 0.2) !important;
}
.zen-article-content.is-night-mode :deep(.zen-media-card-icon),
:deep(.is-night-mode .zen-media-card-icon) {
  background-color: rgba(245, 158, 11, 0.2) !important;
  color: #fcd34d !important;
  border: 1px solid rgba(245, 158, 11, 0.4) !important;
}
.zen-article-content.is-night-mode :deep(.zen-media-card-title),
:deep(.is-night-mode .zen-media-card-title) {
  color: #fde68a !important; /* Bright warm gold */
}
.zen-article-content.is-night-mode :deep(.zen-media-card-subtitle),
:deep(.is-night-mode .zen-media-card-subtitle) {
  color: #d6d3d1 !important; /* Stone-300 */
}
.zen-article-content.is-night-mode :deep(.zen-media-card-caption),
:deep(.is-night-mode .zen-media-card-caption) {
  color: #fde68a !important; /* Bright warm gold */
}

/* Zen ASCII Diagrams & Code Blocks Contrast */
.zen-article-content.is-paper-mode :deep(.zen-ascii-diagram),
:deep(.is-paper-mode .zen-ascii-diagram) {
  background-color: #fdfbf7 !important;
  border-color: rgba(217, 119, 6, 0.35) !important;
  color: #1c1917 !important;
}
.zen-article-content.is-paper-mode :deep(.zen-ascii-diagram pre),
.zen-article-content.is-paper-mode :deep(.zen-ascii-diagram code),
:deep(.is-paper-mode .zen-ascii-diagram pre),
:deep(.is-paper-mode .zen-ascii-diagram code) {
  color: #1c1917 !important;
  font-weight: 500;
}

.zen-article-content.is-night-mode :deep(.zen-ascii-diagram),
:deep(.is-night-mode .zen-ascii-diagram) {
  background-color: #0c0a09 !important;
  border-color: rgba(245, 158, 11, 0.3) !important;
  color: #fef3c7 !important;
}
.zen-article-content.is-night-mode :deep(.zen-ascii-diagram pre),
.zen-article-content.is-night-mode :deep(.zen-ascii-diagram code),
:deep(.is-night-mode .zen-ascii-diagram pre),
:deep(.is-night-mode .zen-ascii-diagram code) {
  color: #fde68a !important;
}
</style>
