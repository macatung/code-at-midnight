<script setup lang="ts">
import { computed, onMounted, watch, nextTick, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import mermaid from 'mermaid';
import SeoHead from '@/Components/common/SeoHead.vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import DualPerspectiveHeaderBanner from '@/Components/common/DualPerspectiveHeaderBanner.vue';
import DualPerspectiveFooterCard from '@/Components/common/DualPerspectiveFooterCard.vue';
import DualPerspectiveFloatingPill from '@/Components/common/DualPerspectiveFloatingPill.vue';
import { parsePerspectiveBlocks, initPerspectiveWidgets } from '@/utils/dualPerspectiveParser';
import { useI18n } from '@/composables/useI18n';
import { sound } from '@/audio/soundEffects';

interface ArticleItem {
  id: number;
  title: string;
  title_en?: string | null;
  slug: string;
  excerpt: string;
  excerpt_en?: string | null;
  content: string;
  content_en?: string | null;
  cover_image?: string | null;
  tags: string[];
  reading_time_min: number;
  published_at: string;
}

interface PairedArticle {
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
}

const props = defineProps<{
  article: ArticleItem;
  paired_article?: PairedArticle | null;
  settings?: Record<string, string>;
}>();

const { locale, t, setLocale } = useI18n();
const copiedCodeIdx = ref<number | null>(null);

const currentTitle = computed(() => {
  if (locale.value === 'en' && props.article.title_en) {
    return props.article.title_en;
  }
  return props.article.title;
});

const currentExcerpt = computed(() => {
  if (locale.value === 'en' && props.article.excerpt_en) {
    return props.article.excerpt_en;
  }
  return props.article.excerpt;
});

const rawContent = computed(() => {
  if (locale.value === 'en' && props.article.content_en) {
    return props.article.content_en;
  }
  return props.article.content || '';
});

const switchLanguage = (lang: 'vi' | 'en') => {
  setLocale(lang);
  sound.playClick();
};

const renderMermaidDiagrams = async () => {
  if (typeof window === 'undefined') return;
  await nextTick();
  initPerspectiveWidgets();

  try {
    mermaid.initialize({
      startOnLoad: false,
      securityLevel: 'loose',
      theme: 'dark',
      themeVariables: {
        darkMode: true,
        background: '#020617',
        primaryColor: '#10b981',
        primaryTextColor: '#f8fafc',
        primaryBorderColor: '#059669',
        lineColor: '#34d399',
        secondaryColor: '#0f172a',
        tertiaryColor: '#020617',
      },
      fontFamily: 'JetBrains Mono, monospace, sans-serif',
    });

    const containers = document.querySelectorAll('.blog-mermaid-container');
    for (let i = 0; i < containers.length; i++) {
      const el = containers[i];
      const rawCode = decodeURIComponent(el.getAttribute('data-code') || '');
      if (rawCode) {
        try {
          const id = `mermaid-blog-${Date.now()}-${i}-${Math.random().toString(36).slice(2, 7)}`;
          const { svg } = await mermaid.render(id, rawCode);
          const target = el.querySelector('.mermaid-render-target');
          if (target) {
            target.innerHTML = svg;
          }
        } catch (itemErr) {
          console.error(`Error rendering blog mermaid diagram #${i}:`, itemErr);
          const target = el.querySelector('.mermaid-render-target');
          if (target) {
            target.innerHTML = `<pre class="text-xs text-emerald-400/80 bg-slate-950 p-3 rounded-lg overflow-x-auto text-left font-mono">${rawCode}</pre>`;
          }
        }
      }
    }
  } catch (err) {
    console.error('Mermaid render error:', err);
  }
};

onMounted(() => {
  renderMermaidDiagrams();
});

watch(
  [() => rawContent.value, () => locale.value],
  () => {
    nextTick(() => {
      renderMermaidDiagrams();
    });
  }
);

// Advanced Markdown Parser with Tables, Lists, Callouts, Code Blocks, Math & Typography
const formattedContent = computed(() => {
  let content = rawContent.value.replace(/\r\n/g, '\n').replace(/\r/g, '\n');

  // 1. Mermaid diagrams
  content = content.replace(/```\s*mermaid\s*\n([\s\S]*?)```/gim, (_match, code) => {
    return `\n\n<div class="blog-mermaid-container my-8 p-4 sm:p-6 rounded-2xl bg-slate-950 border border-emerald-500/30 shadow-2xl overflow-x-auto flex flex-col items-center justify-center" data-code="${encodeURIComponent(code.trim())}">
      <div class="w-full flex items-center justify-between pb-3 mb-3 border-b border-emerald-500/20 text-xs font-mono text-emerald-400 font-semibold tracking-wider">
        <span class="flex items-center gap-2"><span>❖</span><span>ARCHITECTURE & FLOW DIAGRAM</span></span>
        <span class="text-[10px] bg-emerald-950/80 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 rounded font-mono">Mermaid</span>
      </div>
      <div class="mermaid-render-target w-full flex justify-center py-2 text-slate-100 overflow-x-auto">
      </div>
    </div>\n\n`;
  });

  // 2. Code blocks with language badge
  content = content.replace(/```([a-zA-Z0-9_-]*)\n([\s\S]*?)```/g, (_match, language, code) => {
    const lang = language.trim() || 'text';
    const cleanCode = code
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');

    return `\n\n<div class="code-block-wrapper my-6 rounded-2xl border border-white/10 bg-slate-950 shadow-xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-2.5 bg-white/[0.03] border-b border-white/10 text-xs font-mono text-slate-400">
        <span class="font-bold text-phantom-mint uppercase tracking-wider">${lang}</span>
        <span class="text-[11px] text-slate-400">Code Snippet</span>
      </div>
      <pre class="p-5 text-xs sm:text-sm font-mono leading-relaxed text-emerald-300 overflow-x-auto"><code class="language-${lang}">${cleanCode.trim()}</code></pre>
    </div>\n\n`;
  });

  // 3. Dual Perspective Cards
  content = parsePerspectiveBlocks(content, 'main', false);

  // 4. Block Math ($$ ... $$) and Clean Flow Equations
  content = content.replace(/\$\$\s*([\s\S]*?)\s*\$\$/g, (_match, inner) => {
    const cleanFlow = inner
      .replace(/\\text\{([^}]+)\}/g, '$1')
      .replace(/\\(long)?rightarrow/g, '➔')
      .replace(/\\longrightarrow/g, '➔')
      .replace(/\\rightarrow/g, '➔')
      .replace(/\\approx/g, '≈')
      .replace(/\\times/g, '×')
      .replace(/\\le/g, '≤')
      .replace(/\\ge/g, '≥')
      .replace(/\\in/g, '∈')
      .replace(/\\sum_\{([^}]+)\}\^\{([^}]+)\}/g, '∑ ($1 to $2)')
      .replace(/\\prod_\{([^}]+)\}\^\{([^}]+)\}/g, '∏ ($1 to $2)')
      .replace(/\\frac\{([^}]+)\}\{([^}]+)\}/g, '($1) / ($2)')
      .trim();
    return `\n\n<div class="my-6 p-4 sm:p-5 rounded-2xl border border-emerald-500/30 bg-slate-950/90 text-emerald-400 font-mono text-sm sm:text-base flex items-center justify-center text-center font-semibold shadow-xl overflow-x-auto"><span>${cleanFlow}</span></div>\n\n`;
  });

  // 5. Inline Math ($ ... $)
  content = content.replace(/\$([^\$\n]+)\$/g, (_match, inner) => {
    const cleanMath = inner
      .replace(/\\text\{([^}]+)\}/g, '$1')
      .replace(/\\(long)?rightarrow/g, '➔')
      .replace(/\\approx/g, '≈')
      .replace(/\\le/g, '≤')
      .replace(/\\ge/g, '≥')
      .replace(/\\times/g, '×')
      .replace(/\\in/g, '∈')
      .trim();
    return `<span class="inline-block font-mono text-emerald-300 bg-emerald-950/60 px-1.5 py-0.5 rounded border border-emerald-500/25 text-xs sm:text-sm font-medium mx-0.5">${cleanMath}</span>`;
  });

  // 6. Markdown Tables
  content = content.replace(/(?:^|\n)((?:\|[^\n]+\|\r?\n)+)/g, (_match, tableBlock) => {
    const lines = tableBlock.trim().split('\n').map((l: string) => l.trim()).filter((l: string) => l.startsWith('|'));
    if (lines.length < 2) return tableBlock;

    const parseRow = (rowStr: string) => {
      return rowStr
        .replace(/^\|/, '')
        .replace(/\|$/, '')
        .split('|')
        .map((c: string) => c.trim());
    };

    const headerCells = parseRow(lines[0]);
    const isDivider = (line: string) => /^\|(\s*:?-+:?\s*\|)+$/.test(line);

    let bodyStartIndex = 1;
    if (lines.length > 1 && isDivider(lines[1])) {
      bodyStartIndex = 2;
    }

    const headerHtml = `<thead><tr class="border-b border-white/10 bg-white/[0.04] text-xs font-mono font-bold text-phantom-mint uppercase tracking-wider">${headerCells.map((c: string) => `<th class="p-3.5 sm:p-4 text-left">${c}</th>`).join('')}</tr></thead>`;

    const bodyRows = lines.slice(bodyStartIndex).map((r: string) => {
      const cells = parseRow(r);
      return `<tr class="border-b border-white/5 hover:bg-white/[0.02] transition-colors">${cells.map((c: string) => `<td class="p-3.5 sm:p-4 text-xs sm:text-sm text-slate-300 leading-relaxed">${c}</td>`).join('')}</tr>`;
    }).join('');

    return `\n\n<div class="overflow-x-auto my-8 rounded-2xl border border-white/10 bg-slate-950/90 shadow-2xl"><table class="w-full text-left border-collapse">${headerHtml}<tbody>${bodyRows}</tbody></table></div>\n\n`;
  });

  // 7. GitHub Style Callout Alerts & Blockquotes
  content = content
    .replace(/^>\s*\[!NOTE\]\s*([\s\S]*?)(?=\n\n|$)/gim, '<div class="my-6 p-4 sm:p-5 rounded-2xl border-l-4 border-emerald-400 bg-emerald-950/30 text-slate-200 text-sm leading-relaxed"><div class="flex items-center gap-2 font-mono text-emerald-400 font-bold mb-2"><span>ℹ️</span><span>NOTE</span></div><p>$1</p></div>')
    .replace(/^>\s*\[!TIP\]\s*([\s\S]*?)(?=\n\n|$)/gim, '<div class="my-6 p-4 sm:p-5 rounded-2xl border-l-4 border-teal-400 bg-teal-950/30 text-slate-200 text-sm leading-relaxed"><div class="flex items-center gap-2 font-mono text-teal-400 font-bold mb-2"><span>💡</span><span>TIP</span></div><p>$1</p></div>')
    .replace(/^>\s*\[!IMPORTANT\]\s*([\s\S]*?)(?=\n\n|$)/gim, '<div class="my-6 p-4 sm:p-5 rounded-2xl border-l-4 border-amber-400 bg-amber-950/30 text-slate-200 text-sm leading-relaxed"><div class="flex items-center gap-2 font-mono text-amber-400 font-bold mb-2"><span>⚠️</span><span>IMPORTANT</span></div><p>$1</p></div>')
    .replace(/^>\s*\[!WARNING\]\s*([\s\S]*?)(?=\n\n|$)/gim, '<div class="my-6 p-4 sm:p-5 rounded-2xl border-l-4 border-rose-400 bg-rose-950/30 text-slate-200 text-sm leading-relaxed"><div class="flex items-center gap-2 font-mono text-rose-400 font-bold mb-2"><span>🚨</span><span>WARNING</span></div><p>$1</p></div>')
    .replace(/^>\s*(.*)$/gm, '<blockquote class="my-6 border-l-4 border-phantom-mint bg-phantom-mint/10 px-5 py-4 rounded-r-2xl italic text-slate-300">$1</blockquote>');

  // 8. Markdown Headings
  content = content
    .replace(/^#### (.*)$/gm, '<h4 class="mt-8 mb-3 text-base sm:text-lg font-display font-bold text-slate-100">$1</h4>')
    .replace(/^### (.*)$/gm, '<h3 class="mt-10 mb-4 text-lg sm:text-xl font-display font-bold text-phantom-mint flex items-center gap-2"><span class="text-xs text-phantom-mint/60">✦</span><span>$1</span></h3>')
    .replace(/^## (.*)$/gm, '<h2 class="mt-12 mb-6 border-b border-white/10 pb-3 text-xl sm:text-2xl font-display font-extrabold text-white tracking-tight flex items-center gap-2.5"><span class="h-2 w-2 rounded-full bg-phantom-mint"></span><span>$1</span></h2>')
    .replace(/^# (.*)$/gm, '<h1 class="mt-14 mb-8 text-2xl sm:text-3xl font-display font-extrabold text-white tracking-tight border-b border-white/15 pb-4">$1</h1>');

  // 9. Unordered & Ordered Lists
  content = content
    .replace(/^-\s*\[\s*\]\s*(.*)$/gm, '<li class="flex items-start gap-2.5 my-1.5"><span class="text-slate-500 font-mono">☐</span><span class="text-slate-300">$1</span></li>')
    .replace(/^-\s*\[x\]\s*(.*)$/gm, '<li class="flex items-start gap-2.5 my-1.5"><span class="text-phantom-mint font-mono font-bold">☑</span><span class="text-slate-200 font-medium">$1</span></li>')
    .replace(/^[\*\-]\s+(.*)$/gm, '<li class="flex items-start gap-2.5 my-2"><span class="text-phantom-mint text-xs mt-1">✦</span><span class="text-slate-300 leading-relaxed">$1</span></li>')
    .replace(/^(\d+)\.\s+(.*)$/gm, '<li class="flex items-start gap-2.5 my-2.5"><span class="flex-shrink-0 font-mono text-[11px] font-bold text-phantom-mint bg-phantom-mint/10 border border-phantom-mint/30 px-1.5 py-0.5 rounded mt-0.5">$1</span><span class="text-slate-300 leading-relaxed">$2</span></li>');

  // 10. Inline Text Styling & Links
  content = content
    .replace(/!\[([^\]]*)\]\(([^\s)]+)(?:\s+"[^"]*")?\)/g, '<img src="$2" alt="$1" class="rounded-2xl border border-white/10 my-6 max-w-full">')
    .replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-phantom-mint hover:underline font-medium inline-flex items-center gap-1"><span>$1</span><span class="text-xs">↗</span></a>')
    .replace(/`([^`\n]+)`/g, '<code class="rounded-md bg-slate-950 px-1.5 py-0.5 font-mono text-xs text-phantom-mint border border-phantom-mint/30 font-semibold">$1</code>')
    .replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-white">$1</strong>')
    .replace(/\*(.*?)\*/g, '<em class="italic text-slate-200">$1</em>')
    .replace(/^---$/gm, '<hr class="my-10 border-white/10" />');

  // 11. Wrap loose lines in paragraphs
  return content
    .split(/\n\n+/)
    .map((block) => {
      const trimmed = block.trim();
      if (!trimmed) return '';
      if (
        trimmed.startsWith('<div') ||
        trimmed.startsWith('<h1') ||
        trimmed.startsWith('<h2') ||
        trimmed.startsWith('<h3') ||
        trimmed.startsWith('<h4') ||
        trimmed.startsWith('<hr') ||
        trimmed.startsWith('<blockquote') ||
        trimmed.startsWith('<table') ||
        trimmed.startsWith('<pre')
      ) {
        return trimmed;
      }
      if (trimmed.startsWith('<li')) {
        return `<ul class="my-5 space-y-1">${trimmed}</ul>`;
      }
      return `<p class="mb-5 leading-8 text-slate-300 font-sans text-sm sm:text-base">${trimmed.replace(/\n/g, '<br>')}</p>`;
    })
    .join('');
});

const articleJsonLd = computed(() => ({
  '@context': 'https://schema.org',
  '@type': 'BlogPosting',
  headline: currentTitle.value,
  description: currentExcerpt.value,
  url: `https://macatung.dev/blog/${props.article.slug}`,
  datePublished: props.article.published_at,
  author: { '@type': 'Person', name: 'Ma Cà Tưng' }
}));
</script>

<template>
  <SeoHead
    :title="currentTitle"
    :description="currentExcerpt"
    :keywords="article.tags?.join(', ')"
    :canonical="`https://macatung.dev/blog/${article.slug}`"
    og-type="article"
    :json-ld="articleJsonLd"
  />

  <div class="min-h-screen bg-midnight-950 text-slate-100 flex flex-col overflow-x-hidden bg-grid-pattern">
    <TalismanCanvas />
    <Navbar />

    <main class="relative z-10 flex-1 w-full max-w-4xl mx-auto px-4 py-10 sm:px-6 sm:py-14">
      <!-- Top Navigation & Interactive Bilingual Switcher -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-400" aria-label="Breadcrumb">
          <Link href="/" class="hover:text-phantom-mint">{{ t('nav.home') }}</Link>
          <span>/</span>
          <Link href="/blog" class="hover:text-phantom-mint">{{ t('blog.title') }}</Link>
          <span>/</span>
          <span class="text-phantom-mint truncate max-w-[200px] sm:max-w-xs">{{ currentTitle }}</span>
        </nav>

        <!-- Article Language Switcher -->
        <div class="flex items-center gap-1 rounded-xl border border-white/15 bg-midnight-900/90 p-1 text-xs font-mono shadow-lg" role="group" :aria-label="t('common.language')">
          <button
            type="button"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all cursor-pointer font-medium"
            :class="locale === 'vi' ? 'bg-phantom-mint text-midnight-950 font-bold shadow-glow-mint' : 'text-slate-400 hover:text-white'"
            @click="switchLanguage('vi')"
          >
            <span>🇻🇳</span>
            <span>Tiếng Việt</span>
          </button>
          <button
            type="button"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all cursor-pointer font-medium"
            :class="locale === 'en' ? 'bg-phantom-mint text-midnight-950 font-bold shadow-glow-mint' : 'text-slate-400 hover:text-white'"
            @click="switchLanguage('en')"
          >
            <span>🇬🇧</span>
            <span>English</span>
          </button>
        </div>
      </div>

      <header class="mb-8 border-b border-white/10 pb-8">
        <!-- Tags Pill -->
        <div v-if="article.tags && article.tags.length > 0" class="flex flex-wrap gap-2 mb-4">
          <span
            v-for="tag in article.tags"
            :key="tag"
            class="rounded-full border border-phantom-mint/30 bg-phantom-mint/10 px-3 py-0.5 text-xs font-mono text-phantom-mint"
          >
            #{{ tag }}
          </span>
        </div>

        <h1 class="text-3xl font-display font-extrabold leading-tight tracking-tight text-white sm:text-5xl">
          {{ currentTitle }}
        </h1>

        <p class="mt-4 max-w-3xl text-base sm:text-lg leading-relaxed text-slate-300">
          {{ currentExcerpt }}
        </p>

        <div class="mt-6 flex flex-wrap items-center gap-4 text-xs font-mono text-slate-400">
          <span class="flex items-center gap-1.5 text-slate-300">
            <span>📅</span>
            <span>{{ article.published_at }}</span>
          </span>
          <span>·</span>
          <span class="flex items-center gap-1.5 text-phantom-mint font-semibold">
            <span>⏱️</span>
            <span>{{ article.reading_time_min }} {{ t('common.minutes') }}</span>
          </span>
        </div>

        <!-- Dual Perspective Header Banner if counterpart exists -->
        <DualPerspectiveHeaderBanner
          v-if="paired_article"
          :paired-article="paired_article"
          current-type="dev"
        />
      </header>

      <!-- Main Article Card: Immersive Dark Midnight Theme -->
      <article class="rounded-3xl border border-white/10 bg-midnight-900/80 p-6 sm:p-10 lg:p-12 text-slate-200 shadow-2xl backdrop-blur-xl">
        <div class="article-body font-sans text-slate-300 text-sm sm:text-base leading-relaxed" v-html="formattedContent" />

        <!-- Dual Perspective Footer Callout -->
        <DualPerspectiveFooterCard
          v-if="paired_article"
          :paired-article="paired_article"
          current-type="dev"
        />
      </article>

      <div class="mt-10 flex items-center justify-between pt-6 border-t border-white/10">
        <Link
          href="/blog"
          class="inline-flex items-center gap-2 text-sm font-semibold text-phantom-mint hover:text-white transition-colors"
        >
          <span>←</span>
          <span>{{ t('blog.title') }}</span>
        </Link>
      </div>

      <!-- Floating quick switch pill -->
      <DualPerspectiveFloatingPill
        v-if="paired_article"
        :paired-article="paired_article"
        current-type="dev"
      />
    </main>

    <Footer />
  </div>
</template>

<style scoped>
.article-body :deep(img) {
  max-width: 100%;
  border-radius: 1rem;
  margin: 1.5rem 0;
}
.article-body :deep(pre) {
  scrollbar-width: thin;
  scrollbar-color: rgba(52, 211, 153, 0.3) rgba(2, 6, 23, 0.5);
}
</style>
