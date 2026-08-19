<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, nextTick, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import TheravadaLayout from '@/Layouts/TheravadaLayout.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';
import mermaid from 'mermaid';

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
  related?: any[];
  title?: string;
}>();

// Zen Reader Settings
const fontSize = ref<number>(18);
const readingProgress = ref(0);
const isRinging = ref(false);
const isCandlelightOn = ref(false);
const isFocusModeOn = ref(false);
const isPaperMode = ref(true); // Default to clean white/parchment paper background for optimal readability

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

const renderMermaidDiagrams = async () => {
  if (typeof window === 'undefined') return;
  try {
    mermaid.initialize({
      startOnLoad: false,
      theme: 'dark',
      themeVariables: {
        darkMode: true,
        background: '#1c1917',
        primaryColor: '#f59e0b',
        primaryTextColor: '#fffbeb',
        primaryBorderColor: '#d97706',
        lineColor: '#fbbf24',
        secondaryColor: '#292524',
        tertiaryColor: '#1c1917',
      },
      fontFamily: 'Playfair Display, serif',
    });

    await nextTick();
    const containers = document.querySelectorAll('.zen-mermaid-container');
    for (let i = 0; i < containers.length; i++) {
      const el = containers[i];
      const rawCode = decodeURIComponent(el.getAttribute('data-code') || '');
      if (rawCode) {
        const id = `mermaid-zen-${Date.now()}-${i}`;
        const { svg } = await mermaid.render(id, rawCode);
        const target = el.querySelector('.mermaid-render-target');
        if (target) {
          target.innerHTML = svg;
        }
      }
    }
  } catch (err) {
    console.error('Mermaid render error:', err);
  }
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
  renderMermaidDiagrams();
});

watch(
  () => [props.article.content, isPaperMode.value],
  () => {
    renderMermaidDiagrams();
  }
);

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

const ringBell = () => {
  isRinging.value = true;
  mindfulBell.ringBell(432, 6.0);
  setTimeout(() => {
    isRinging.value = false;
  }, 3000);
};

// Rich Markdown to Zen HTML Parser with Sutta-first readability
const renderedMarkdown = computed(() => {
  if (!props.article.content) return '';
  let md = props.article.content;

  // 1. Mermaid Diagrams
  md = md.replace(/```mermaid\n([\s\S]*?)```/gim, (match, code) => {
    return `<div class="zen-mermaid-container my-8 p-4 sm:p-6 rounded-3xl bg-stone-900 border border-amber-500/40 shadow-2xl overflow-x-auto flex flex-col items-center justify-center" data-code="${encodeURIComponent(code.trim())}">
      <div class="w-full flex items-center justify-between pb-3 mb-4 border-b border-amber-500/20 text-xs font-serif text-amber-300 font-bold">
        <span class="flex items-center gap-2"><span>☸️</span><span>SƠ ĐỒ PHÁP HỌC & QUÁN CHIẾU</span></span>
        <span class="text-[11px] text-stone-400 font-mono">Mermaid Zen Flow</span>
      </div>
      <div class="mermaid-render-target w-full flex justify-center py-2 text-stone-100">
      </div>
    </div>`;
  });

  if (isPaperMode.value) {
    // 2. Blockquotes (Paper Mode)
    md = md.replace(/^>\s?(.*)$/gim, (match, p1) => {
      return `<blockquote class="my-6 pl-5 py-4 border-l-4 border-amber-600 bg-amber-50/90 rounded-r-2xl text-stone-800 font-serif italic text-base sm:text-lg leading-relaxed shadow-sm">
        <div class="flex items-start gap-2">
          <span class="text-amber-700 text-xl select-none font-bold">“</span>
          <div class="flex-1">${p1.trim()}</div>
        </div>
      </blockquote>`;
    });

    // 3. Headings
    md = md.replace(/^### (.*$)/gim, '<h3 class="text-lg sm:text-xl font-bold text-amber-900 mt-8 mb-3 font-serif flex items-center gap-2"><span class="text-amber-600 text-sm">✦</span>$1</h3>');
    md = md.replace(/^## (.*$)/gim, '<h2 class="text-xl sm:text-2xl font-bold text-amber-950 mt-10 mb-4 pb-2.5 border-b border-amber-200 font-serif flex items-center gap-2.5"><span>☸️</span>$1</h2>');

    // 4. Horizontal Rules
    md = md.replace(/^---$/gim, '<div class="my-10 flex items-center justify-center gap-3 text-amber-600/40 select-none"><span class="h-px w-24 bg-amber-300"></span><span>☸️ 🌸 ☸️</span><span class="h-px w-24 bg-amber-300"></span></div>');

    // 5. Bold & Italic
    md = md.replace(/\*\*(.*?)\*\*/gim, '<strong class="font-bold text-amber-900">$1</strong>');
    md = md.replace(/\*(.*?)\*/gim, '<em class="italic text-stone-700 font-serif">$1</em>');

    // 6. Ordered & Unordered Lists
    md = md.replace(/^\d+\.\s(.*)$/gim, '<li class="ml-4 pl-2 list-decimal text-stone-800 my-1.5 leading-relaxed text-base sm:text-lg">$1</li>');
    md = md.replace(/^-\s(.*)$/gim, '<li class="ml-4 pl-2 list-disc text-stone-800 my-1.5 leading-relaxed text-base sm:text-lg">$1</li>');

    // 7. Paragraphs
    const paragraphs = md.split(/\n\n+/);
    return paragraphs.map(p => {
      p = p.trim();
      if (p.startsWith('<div') || p.startsWith('<blockquote') || p.startsWith('<h') || p.startsWith('<li')) {
        return p;
      }
      return `<p class="my-4 text-stone-800 font-serif leading-[2] text-base sm:text-lg text-justify">${p.replace(/\n/g, '<br/>')}</p>`;
    }).join('\n');
  } else {
    // 2. Blockquotes (Night Mode)
    md = md.replace(/^>\s?(.*)$/gim, (match, p1) => {
      return `<blockquote class="my-6 pl-5 py-3.5 border-l-4 border-amber-500 bg-amber-950/20 rounded-r-2xl text-amber-200/95 font-serif italic text-base sm:text-lg leading-relaxed shadow-sm">
        <div class="flex items-start gap-2">
          <span class="text-amber-400 text-xl select-none">“</span>
          <div class="flex-1">${p1.trim()}</div>
        </div>
      </blockquote>`;
    });

    // 3. Headings
    md = md.replace(/^### (.*$)/gim, '<h3 class="text-lg sm:text-xl font-bold text-amber-200 mt-8 mb-3 font-serif flex items-center gap-2"><span class="text-amber-500 text-sm">✦</span>$1</h3>');
    md = md.replace(/^## (.*$)/gim, '<h2 class="text-xl sm:text-2xl font-bold text-amber-300 mt-10 mb-4 pb-2 border-b border-amber-500/20 font-serif flex items-center gap-2.5"><span>☸️</span>$1</h2>');

    // 4. Horizontal Rules
    md = md.replace(/^---$/gim, '<div class="my-10 flex items-center justify-center gap-3 text-amber-500/40 select-none"><span class="h-px w-24 bg-amber-500/30"></span><span>☸️ 🌸 ☸️</span><span class="h-px w-24 bg-amber-500/30"></span></div>');

    // 5. Bold & Italic
    md = md.replace(/\*\*(.*?)\*\*/gim, '<strong class="font-bold text-amber-200">$1</strong>');
    md = md.replace(/\*(.*?)\*/gim, '<em class="italic text-stone-300 font-serif">$1</em>');

    // 6. Ordered & Unordered Lists
    md = md.replace(/^\d+\.\s(.*)$/gim, '<li class="ml-4 pl-2 list-decimal text-stone-200 my-1 leading-relaxed">$1</li>');
    md = md.replace(/^-\s(.*)$/gim, '<li class="ml-4 pl-2 list-disc text-stone-200 my-1 leading-relaxed">$1</li>');

    // 7. Paragraphs
    const paragraphs = md.split(/\n\n+/);
    return paragraphs.map(p => {
      p = p.trim();
      if (p.startsWith('<div') || p.startsWith('<blockquote') || p.startsWith('<h') || p.startsWith('<li')) {
        return p;
      }
      return `<p class="my-4 text-stone-200 font-serif leading-relaxed text-justify">${p.replace(/\n/g, '<br/>')}</p>`;
    }).join('\n');
  }
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
    '@id': `https://theravada.macatung.dev/kinh/${props.article.slug}`
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
    <!-- Top Reading Progress Indicator with Sliding Golden Lotus -->
    <div class="fixed top-0 left-0 w-full h-1.5 bg-stone-900/60 z-50 overflow-visible pointer-events-none">
      <div
        class="h-full bg-gradient-to-r from-amber-600 via-amber-400 to-yellow-300 relative transition-all duration-150 ease-out shadow-[0_0_12px_rgba(251,191,36,0.8)]"
        :style="{ width: `${readingProgress}%` }"
      >
        <!-- Sliding Golden Lotus Icon -->
        <span
          v-if="readingProgress > 1"
          class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/2 text-sm select-none filter drop-shadow-[0_0_8px_rgba(251,191,36,0.9)] animate-pulse"
        >
          🪷
        </span>
      </div>
    </div>

    <!-- Ambient Candlelight / Oil Lamp Breathing Aura for Deep Night Reading -->
    <div
      v-if="isCandlelightOn"
      class="fixed inset-0 pointer-events-none z-0 flex items-center justify-center transition-opacity duration-1000"
    >
      <div
        class="w-[850px] h-[850px] rounded-full bg-gradient-to-r from-amber-600/20 via-yellow-500/15 to-orange-600/20 blur-[150px] animate-pulse"
        style="animation-duration: 4s;"
      />
    </div>

    <div class="max-w-4xl mx-auto py-6 sm:py-10 relative z-10">
      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center gap-2 text-xs font-serif text-stone-400 mb-6" aria-label="Breadcrumb">
        <Link href="/theravada" class="hover:text-amber-300">Theravāda</Link>
        <span>/</span>
        <Link :href="`/theravada/danh-muc/${article.category}`" class="hover:text-amber-300">
          {{ article.category === 'phap-hoc' ? 'Pháp Học' : article.category === 'phap-hanh' ? 'Pháp Hành' : 'Kinh Tụng' }}
        </Link>
        <span>/</span>
        <span class="text-amber-400 font-bold truncate max-w-[200px] sm:max-w-md">
          {{ article.title }}
        </span>
      </nav>

      <!-- Article Header -->
      <header class="mb-10 text-left border-b border-stone-800 pb-8">
        <div class="flex flex-wrap items-center gap-2.5 mb-4">
          <span class="px-3 py-1 rounded-full text-xs font-serif font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
            {{ article.category === 'phap-hoc' ? '📖 Pháp Học (Pariyatti)' : article.category === 'phap-hanh' ? '🧘 Pháp Hành (Vipassanā)' : '📜 Tam Tạng & Kinh Tụng' }}
          </span>
          <span class="text-xs text-stone-400 font-serif">
            ⏱️ {{ article.reading_time_min }} phút đọc chánh niệm
          </span>
        </div>

        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-amber-100 leading-tight mb-4">
          {{ article.title }}
        </h1>

        <p v-if="article.pali_title" class="text-base sm:text-lg font-serif italic text-amber-400/90 mb-4">
          Pāḷi: {{ article.pali_title }}
        </p>

        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-stone-900 text-xs font-serif text-stone-400">
          <span class="italic">Tác giả / Nguồn: <strong class="text-stone-200 not-italic">{{ article.author || 'Pāḷi Tipiṭaka' }}</strong></span>

          <!-- Reader Controls: Paper Mode, Candlelight, Focus Mode, Font size & Bell -->
          <div class="flex flex-wrap items-center gap-2">
            <!-- Paper / Night Mode Toggle -->
            <button
              @click="togglePaperMode"
              :class="[
                'flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all cursor-pointer font-bold text-xs shadow-sm',
                isPaperMode
                  ? 'bg-amber-100 text-stone-900 border-amber-400 shadow-[0_0_15px_rgba(251,191,36,0.5)]'
                  : 'bg-stone-900 text-stone-300 border-stone-800 hover:border-amber-500/40'
              ]"
              :title="isPaperMode ? 'Chuyển sang nền đêm tĩnh mịch' : 'Chuyển sang nền giấy trắng sáng'"
            >
              <span>{{ isPaperMode ? '📜 Nền Giấy' : '🌙 Nền Đêm' }}</span>
            </button>

            <!-- Candlelight Glow Toggle -->
            <button
              @click="toggleCandlelight"
              :class="[
                'flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all cursor-pointer font-bold text-xs',
                isCandlelightOn
                  ? 'bg-amber-500 text-stone-950 border-amber-400 shadow-[0_0_15px_rgba(251,191,36,0.6)]'
                  : 'bg-stone-900 text-stone-300 border-stone-800 hover:border-amber-500/40'
              ]"
              title="Chế độ đèn dầu thiền quán"
            >
              <span>🕯️</span>
              <span>Đèn Dầu</span>
            </button>

            <!-- Focus Paragraph Mode Toggle -->
            <button
              @click="toggleFocusMode"
              :class="[
                'flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all cursor-pointer font-bold text-xs',
                isFocusModeOn
                  ? 'bg-amber-500 text-stone-950 border-amber-400 shadow-[0_0_15px_rgba(251,191,36,0.6)]'
                  : 'bg-stone-900 text-stone-300 border-stone-800 hover:border-amber-500/40'
              ]"
              title="Chế độ đọc chánh niệm (làm sáng đoạn văn đang đọc)"
            >
              <span>🧘</span>
              <span>Đọc Chánh Niệm</span>
            </button>

            <!-- Bell Trigger -->
            <button
              @click="ringBell"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/15 text-amber-300 hover:bg-amber-500/25 border border-amber-500/30 transition-all cursor-pointer font-bold"
              :class="{ 'animate-pulse ring-2 ring-amber-400': isRinging }"
              title="Thỉnh chuông chánh niệm"
            >
              <span>🔔</span>
              <span>Chuông</span>
            </button>

            <!-- Adjust Font Size -->
            <div class="flex items-center bg-stone-900 rounded-xl border border-stone-800 p-0.5 text-xs">
              <button
                @click="fontSize = Math.max(15, fontSize - 1)"
                class="px-2 py-1 hover:bg-stone-800 rounded-lg text-stone-300 font-bold"
                title="Giảm cỡ chữ"
              >
                A-
              </button>
              <span class="px-2 font-mono text-amber-300">{{ fontSize }}px</span>
              <button
                @click="fontSize = Math.min(26, fontSize + 1)"
                class="px-2 py-1 hover:bg-stone-800 rounded-lg text-stone-300 font-bold"
                title="Tăng cỡ chữ"
              >
                A+
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Text Body (Rendered HTML Markdown inside High-Contrast Container) -->
      <article
        :class="[
          'zen-article-content font-serif leading-loose rounded-3xl p-6 sm:p-10 lg:p-12 mb-12 relative overflow-hidden transition-all duration-500 shadow-2xl',
          isPaperMode
            ? 'bg-stone-50/95 text-stone-900 border border-amber-600/30 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.5)]'
            : 'bg-stone-900/80 text-stone-200 border border-amber-500/30 backdrop-blur-md',
          { 'focus-mode-active': isFocusModeOn }
        ]"
        :style="{ fontSize: `${fontSize}px` }"
      >
        <!-- Top Golden Accent Bar -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-600 via-amber-400 to-yellow-400" />

        <div class="space-y-4 font-serif text-left" v-html="renderedMarkdown" />
      </article>

      <!-- Pāḷi Terms Annotation Box (if present) -->
      <div
        v-if="article.pali_terms && article.pali_terms.length > 0"
        class="my-12 p-6 sm:p-8 rounded-3xl bg-stone-900/90 border border-amber-500/30 shadow-xl text-left"
      >
        <div class="flex items-center gap-2 text-amber-300 font-serif font-bold text-base mb-4">
          <span>📖</span>
          <span>Chú Giải Thuật Ngữ Pāḷi Trong Bài</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-for="item in article.pali_terms"
            :key="item.term"
            class="p-3.5 rounded-2xl bg-stone-950/80 border border-stone-800"
          >
            <span class="text-sm font-serif font-bold text-amber-300">{{ item.term }}</span>
            <p class="text-xs font-serif text-stone-400 mt-1 leading-relaxed">{{ item.meaning }}</p>
          </div>
        </div>
      </div>

      <!-- Footer Actions & Tags -->
      <div class="mt-10 pt-6 border-t border-stone-800 flex flex-wrap items-center justify-between gap-4">
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
          <span>← Quay lại trang chủ Theravāda</span>
        </Link>
      </div>

      <!-- Related Teachings Section -->
      <section v-if="related && related.length > 0" class="mt-16 pt-10 border-t border-stone-800 text-left">
        <h3 class="text-xl font-serif font-bold text-amber-200 mb-6">
          Kinh Văn & Giáo Lý Cùng Chủ Đề
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
          <div
            v-for="rel in related"
            :key="rel.id"
            class="p-5 rounded-2xl bg-stone-900/60 border border-stone-800 hover:border-amber-500/30 transition-all flex flex-col justify-between"
          >
            <div>
              <span class="text-[11px] font-serif text-amber-400">
                {{ rel.category === 'phap-hoc' ? 'Pháp Học' : rel.category === 'phap-hanh' ? 'Pháp Hành' : 'Kinh Tụng' }}
              </span>
              <h4 class="text-sm font-serif font-bold text-stone-100 hover:text-amber-300 mt-1 line-clamp-2">
                <Link :href="`/theravada/kinh/${rel.slug}`">{{ rel.title }}</Link>
              </h4>
            </div>
            <Link
              :href="`/theravada/kinh/${rel.slug}`"
              class="text-xs font-serif text-amber-400 font-bold mt-4 block"
            >
              Đọc bài ➔
            </Link>
          </div>
        </div>
      </section>
    </div>
  </TheravadaLayout>
</template>

<style scoped>
/* Chế độ đọc Chánh Niệm (Focus Mode) */
.focus-mode-active :deep(p),
.focus-mode-active :deep(blockquote),
.focus-mode-active :deep(li),
.focus-mode-active :deep(.zen-mermaid-container) {
  transition: opacity 0.35s ease, filter 0.35s ease, transform 0.35s ease;
  opacity: 0.35;
  filter: blur(0.2px);
}

.focus-mode-active :deep(p:hover),
.focus-mode-active :deep(blockquote:hover),
.focus-mode-active :deep(li:hover),
.focus-mode-active :deep(.zen-mermaid-container:hover) {
  opacity: 1;
  filter: none;
  transform: translateX(4px);
  background: rgba(245, 158, 11, 0.04);
  border-radius: 8px;
}
</style>
