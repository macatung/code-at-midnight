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

const handleScroll = () => {
  if (typeof window === 'undefined') return;
  const total = document.documentElement.scrollHeight - window.innerHeight;
  if (total > 0) {
    readingProgress.value = Math.min(100, Math.max(0, (window.scrollY / total) * 100));
  }
};

const renderMermaidDiagrams = async () => {
  if (typeof window === 'undefined') return;
  await nextTick();
  try {
    mermaid.initialize({
      startOnLoad: false,
      theme: 'dark',
      themeVariables: {
        darkMode: true,
        background: 'transparent',
        primaryColor: '#78350f',
        primaryTextColor: '#fef08a',
        primaryBorderColor: '#f59e0b',
        lineColor: '#fbbf24',
        secondaryColor: '#1c1917',
        tertiaryColor: '#292524',
        fontFamily: 'Lora, serif',
        fontSize: '15px'
      }
    });

    const elements = document.querySelectorAll('.zen-mermaid-container');
    for (let i = 0; i < elements.length; i++) {
      const el = elements[i];
      const rawCode = decodeURIComponent(el.getAttribute('data-code') || '');
      if (rawCode) {
        const uniqueId = `mermaid-svg-${Date.now()}-${i}`;
        const { svg } = await mermaid.render(uniqueId, rawCode);
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
  () => props.article.content,
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
    return `<div class="zen-mermaid-container my-8 p-4 sm:p-6 rounded-3xl bg-stone-900/95 border border-amber-500/40 shadow-2xl overflow-x-auto flex flex-col items-center justify-center backdrop-blur-md" data-code="${encodeURIComponent(code.trim())}">
      <div class="w-full flex items-center justify-between pb-3 mb-4 border-b border-amber-500/20 text-xs font-serif text-amber-300 font-bold">
        <span class="flex items-center gap-2"><span>☸️</span><span>SƠ ĐỒ PHÁP HỌC & QUÁN CHIẾU</span></span>
        <span class="text-[11px] text-stone-400 font-mono">Mermaid Zen Flow</span>
      </div>
      <div class="mermaid-render-target w-full flex justify-center py-2 text-stone-100">
        <div class="animate-pulse text-xs text-amber-400 font-serif">Đang kiến tạo sơ đồ Chánh Pháp...</div>
      </div>
    </div>`;
  });

  // 2. Code / Sutta Text Blocks
  md = md.replace(/```([a-zA-Z0-9_-]*)\n([\s\S]*?)```/gim, (match, lang, code) => {
    return `<div class="my-6 rounded-2xl bg-stone-900/90 border border-amber-500/30 overflow-hidden shadow-xl">
      <div class="px-4 py-2 bg-stone-950 border-b border-amber-500/20 flex items-center justify-between text-xs font-mono text-amber-400">
        <span class="font-bold uppercase flex items-center gap-1.5"><span>📜</span><span>${lang || 'SUTTA TEXT'}</span></span>
        <span class="text-[10px] text-stone-400">Pāḷi Canon</span>
      </div>
      <pre class="p-4 sm:p-5 overflow-x-auto text-xs sm:text-sm font-mono text-amber-200/90 leading-relaxed"><code>${code.trim()}</code></pre>
    </div>`;
  });

  // 3. Horizontal Rules
  md = md.replace(/^---$/gim, `<div class="my-10 flex items-center justify-center gap-3 text-amber-500/50 text-base"><span>🌸</span><span class="h-px w-24 sm:w-36 bg-amber-500/30"></span><span>☸️</span><span class="h-px w-24 sm:w-36 bg-amber-500/30"></span><span>🌸</span></div>`);

  // 4. Headings
  md = md.replace(/^### (.*$)/gim, '<h3 class="text-lg sm:text-xl font-serif font-bold text-amber-200 mt-8 mb-3 flex items-center gap-2"><span class="text-amber-400 text-sm">✦</span><span>$1</span></h3>');
  md = md.replace(/^## (.*$)/gim, '<h2 class="text-xl sm:text-2xl lg:text-3xl font-serif font-bold text-amber-100 mt-10 mb-4 pb-2.5 border-b border-amber-500/20 flex items-center gap-2.5"><span class="text-amber-400 text-lg">☸️</span><span>$1</span></h2>');
  md = md.replace(/^# (.*$)/gim, '<h1 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-bold text-amber-100 mt-12 mb-6">$1</h1>');

  // 5. Unified Multi-Line Blockquotes (Lời Kinh & Khai Thị — Ghép các dòng liên tiếp thành 1 khung duy nhất)
  md = md.replace(/(?:^>[ \t]?(.*)(?:\r?\n|$))+/gm, (block) => {
    const innerLines = block
      .split(/\r?\n/)
      .map(line => line.replace(/^>[ \t]?/, '').trim())
      .filter(line => line.length > 0)
      .join('<br />');
    
    return `<blockquote class="my-6 p-5 sm:p-7 rounded-3xl bg-gradient-to-r from-amber-950/25 via-stone-900/60 to-stone-950/80 border-l-4 border-amber-500 text-stone-100 font-serif text-base sm:text-lg leading-relaxed shadow-lg backdrop-blur-sm">${innerLines}</blockquote>`;
  });

  // 6. Bold & Italic (High contrast & warm amber)
  md = md.replace(/\*\*(.*?)\*\*/gim, '<strong class="font-bold text-amber-200">$1</strong>');
  md = md.replace(/\*(.*?)\*/gim, '<em class="italic text-amber-300/90 font-serif">$1</em>');

  // 7. Numbered Lists
  md = md.replace(/^(\d+)\. (.*$)/gim, '<div class="flex items-start gap-3 my-2.5 text-stone-200"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-500/20 text-amber-300 text-xs font-mono font-bold shrink-0 mt-0.5">$1</span><span>$2</span></div>');

  // 8. Bullet Lists
  md = md.replace(/^\- (.*$)/gim, '<div class="flex items-start gap-2.5 my-2.5 text-stone-200"><span class="text-amber-400 text-sm shrink-0 mt-0.5">🌸</span><span>$1</span></div>');

  // 9. Clean Paragraph Separation
  const paragraphs = md.split(/\n\n+/);
  return paragraphs.map(p => {
    p = p.trim();
    if (!p) return '';
    if (p.startsWith('<h') || p.startsWith('<blockquote') || p.startsWith('<div') || p.startsWith('<pre')) {
      return p;
    }
    return `<p class="mb-5 leading-loose text-stone-200 text-justify sm:text-left">${p}</p>`;
  }).join('\n');
});
</script>

<template>
  <TheravadaLayout :title="title">
    <!-- Top Reading Progress Indicator -->
    <div
      class="fixed top-0 left-0 h-1 bg-amber-500 z-50 transition-all duration-100 ease-out"
      :style="{ width: `${readingProgress}%` }"
    />

    <div class="max-w-4xl mx-auto py-6 sm:py-10">
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

          <!-- Reader Controls (Font size & Bell) -->
          <div class="flex items-center gap-2">
            <button
              @click="ringBell"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/15 text-amber-300 hover:bg-amber-500/25 border border-amber-500/30 transition-all cursor-pointer font-bold"
              :class="{ 'animate-pulse ring-2 ring-amber-400': isRinging }"
              title="Thỉnh chuông chánh niệm"
            >
              <span>🔔</span>
              <span>Thỉnh Chuông</span>
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

      <!-- Main Text Body (Rendered HTML Markdown with Unified Sutta Blocks) -->
      <article
        class="zen-article-content font-serif text-stone-200 leading-loose"
        :style="{ fontSize: `${fontSize}px` }"
      >
        <div class="space-y-4 font-serif" v-html="renderedMarkdown" />
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
