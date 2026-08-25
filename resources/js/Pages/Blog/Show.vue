<script setup lang="ts">
import { computed, onMounted, watch, nextTick } from 'vue';
import { Link } from '@inertiajs/vue3';
import mermaid from 'mermaid';
import SeoHead from '@/Components/common/SeoHead.vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import { useI18n } from '@/composables/useI18n';

interface ArticleItem { id: number; title: string; slug: string; excerpt: string; content: string; tags: string[]; reading_time_min: number; published_at: string; }
const props = defineProps<{ article: ArticleItem; settings?: Record<string, string> }>();
const { t } = useI18n();

const renderMermaidDiagrams = async () => {
  if (typeof window === 'undefined') return;
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

    await nextTick();
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
  () => props.article.content,
  () => {
    renderMermaidDiagrams();
  }
);

const formattedContent = computed(() => {
  let content = (props.article.content || '').replace(/\r\n/g, '\n').replace(/\r/g, '\n');

  // Mermaid diagrams
  content = content.replace(/```\s*mermaid\s*\n([\s\S]*?)```/gim, (_match, code) => {
    return `\n\n<div class="blog-mermaid-container my-8 p-4 sm:p-6 rounded-2xl bg-slate-950 border border-emerald-500/30 shadow-xl overflow-x-auto flex flex-col items-center justify-center" data-code="${encodeURIComponent(code.trim())}">
      <div class="w-full flex items-center justify-between pb-2.5 mb-3 border-b border-emerald-500/20 text-xs font-mono text-emerald-400 font-semibold tracking-wider">
        <span>ARCHITECTURE & FLOW DIAGRAM</span>
        <span class="text-[10px] text-slate-400">Mermaid</span>
      </div>
      <div class="mermaid-render-target w-full flex justify-center py-2 text-slate-100">
      </div>
    </div>\n\n`;
  });

  return content
    .replace(/!\[([^\]]*)\]\(([^\s)]+)(?:\s+"[^"]*")?\)/g, '<img src="$2" alt="$1">')
    .replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>')
    .replace(/```([a-z]*)\n([\s\S]*?)```/g, (_match, language, code) => `<pre class="my-6 overflow-x-auto rounded-xl bg-slate-950 p-5 text-sm text-emerald-300"><code data-language="${language}">${code.trim()}</code></pre>`)
    .replace(/^### (.*)$/gm, '<h3 class="mt-8 text-xl font-bold text-slate-900">$1</h3>')
    .replace(/^## (.*)$/gm, '<h2 class="mt-10 border-b border-slate-200 pb-3 text-2xl font-bold text-slate-950">$1</h2>')
    .replace(/^# (.*)$/gm, '<h1 class="mt-10 text-3xl font-bold text-slate-950">$1</h1>')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .split(/\n\n+/)
    .map((block) => block.trim().startsWith('<') ? block : `<p class="mb-5 leading-8">${block.trim().replace(/\n/g, '<br>')}</p>`)
    .join('');
});

const articleJsonLd = computed(() => ({ '@context': 'https://schema.org', '@type': 'BlogPosting', headline: props.article.title, description: props.article.excerpt, url: `https://macatung.dev/blog/${props.article.slug}`, datePublished: props.article.published_at, author: { '@type': 'Person', name: 'Ma Cà Tưng' } }));
</script>

<template>
  <SeoHead :title="article.title" :description="article.excerpt" :keywords="article.tags?.join(', ')" :canonical="`https://macatung.dev/blog/${article.slug}`" og-type="article" :json-ld="articleJsonLd" />
  <div class="min-h-screen bg-midnight-950 text-slate-100 flex flex-col overflow-x-hidden bg-grid-pattern">
    <TalismanCanvas /><Navbar />
    <main class="relative z-10 flex-1 w-full max-w-4xl mx-auto px-4 py-10 sm:px-6 sm:py-14">
      <nav class="mb-8 flex items-center gap-2 text-xs font-mono text-slate-400" aria-label="Breadcrumb"><Link href="/" class="hover:text-phantom-mint">{{ t('nav.home') }}</Link><span>/</span><Link href="/blog" class="hover:text-phantom-mint">{{ t('blog.title') }}</Link></nav>
      <header class="mb-8 border-b border-white/10 pb-8"><h1 class="text-3xl font-display font-extrabold leading-tight tracking-tight text-white sm:text-5xl">{{ article.title }}</h1><p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-400">{{ article.excerpt }}</p><div class="mt-5 flex items-center gap-3 text-xs font-mono text-slate-400"><span>{{ article.published_at }}</span><span>·</span><span class="text-phantom-mint">{{ article.reading_time_min }} {{ t('common.minutes') }}</span></div></header>
      <article class="rounded-3xl bg-white p-6 text-slate-800 shadow-2xl sm:p-10 lg:p-12"><div class="article-body" v-html="formattedContent" /></article>
      <div class="mt-8"><Link href="/blog" class="text-sm font-semibold text-phantom-mint hover:text-white">← {{ t('blog.title') }}</Link></div>
    </main>
    <Footer />
  </div>
</template>

<style scoped>
.article-body :deep(ul), .article-body :deep(ol) { margin: 1.25rem 0; padding-left: 1.5rem; }
.article-body :deep(li) { margin: .5rem 0; }
.article-body :deep(img) { max-width: 100%; border-radius: 1rem; margin: 1.5rem 0; }
</style>
