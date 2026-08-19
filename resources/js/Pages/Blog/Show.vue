<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import SeoHead from '@/Components/common/SeoHead.vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import NextStepsHub from '@/Components/layout/NextStepsHub.vue';
import { sound } from '@/audio/soundEffects';

interface ArticleItem {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  tags: string[];
  reading_time_min: number;
  view_count: number;
  published_at: string;
}

interface Props {
  article: ArticleItem;
  relatedArticles: ArticleItem[];
}

const props = defineProps<Props>();

const scrollProgress = ref(0);
const readingProgressPercent = ref(0);

const handleScroll = () => {
  if (typeof window !== 'undefined') {
    const y = window.scrollY;
    const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
    if (totalHeight > 0) {
      readingProgressPercent.value = Math.min(100, Math.max(0, Math.round((y / totalHeight) * 100)));
    }
  }
};

// 2-Way Cross-Link: Matching project case study lookup
const getRelatedProjectInfo = (slug: string) => {
  if (slug.includes('multi-agent') || slug.includes('customer-service')) {
    return {
      title: 'Autonomous AI Customer Service Multi-Agent Hub',
      desc: 'Hệ sinh thái Multi-Agent tự trị xử lý 100% khiếu nại khách hàng với độ trễ < 800ms.',
      icon: '🤖',
      badge: 'AI AGENTS',
    };
  }
  if (slug.includes('dinh-gia') || slug.includes('crawlers')) {
    return {
      title: 'Financial Valuation Engine & 50+ Real-Time Crawlers',
      desc: 'Kiến trúc định giá cổ phiếu 7 năm tự động cào 2.4M records/ngày với Redis Queue.',
      icon: '📈',
      badge: 'HIGH-LOAD',
    };
  }
  if (slug.includes('cap-quang') || slug.includes('qgis')) {
    return {
      title: 'Telecom Fiber Network CAD to GIS Spatial Pipeline',
      desc: 'Hệ thống chuẩn hóa 240+ file CAD sang QGIS PostgreSQL tự động hóa hoàn toàn.',
      icon: '🗺️',
      badge: 'GEO-SPATIAL',
    };
  }
  if (slug.includes('sdh') || slug.includes('nms')) {
    return {
      title: 'NMS Matrix: Giám Sát Hạ Tầng Truyền Dẫn SDH/DWDM',
      desc: 'Thu thập 100,000 telemetry/s và mô hình ML dự báo sự cố sụt áp trước 30 phút.',
      icon: '⚡',
      badge: 'MISSION CRITICAL',
    };
  }
  return null;
};

const isPaperMode = ref(true); // Default to white paper background for crystal clear reading
const fontSize = ref(17);

const toggleTheme = () => {
  isPaperMode.value = !isPaperMode.value;
  sound.playClick();
};

// Render basic markdown formatting safely
const formattedContent = computed(() => {
  if (!props.article.content) return '';
  let md = props.article.content;

  if (isPaperMode.value) {
    // High-contrast Light / White Paper typography
    md = md.replace(/^### (.*$)/gim, '<h3 class="text-xl sm:text-2xl font-display font-bold text-slate-900 mt-8 mb-3 flex items-center gap-2"><span class="text-emerald-600 text-base">✦</span>$1</h3>');
    md = md.replace(/^## (.*$)/gim, '<h2 class="text-2xl sm:text-3xl font-display font-bold text-slate-950 mt-10 mb-4 pb-2.5 border-b border-slate-200">$1</h2>');
    md = md.replace(/^# (.*$)/gim, '<h1 class="text-3xl sm:text-4xl font-display font-extrabold text-slate-950 mt-12 mb-6">$1</h1>');

    md = md.replace(/\*\*(.*?)\*\*/gim, '<strong class="font-bold text-emerald-800">$1</strong>');
    md = md.replace(/\*(.*?)\*/gim, '<em class="italic text-slate-700 font-serif">$1</em>');

    md = md.replace(/^\- (.*$)/gim, '<li class="flex items-start gap-2.5 my-2.5 text-slate-800 text-base sm:text-lg leading-relaxed"><span class="text-emerald-600 font-bold">⚡</span><span>$1</span></li>');

    md = md.replace(/```([a-z]*)\n([\s\S]*?)```/gim, (match, lang, code) => {
      return `<div class="my-6 rounded-2xl bg-slate-950 border border-slate-800 overflow-hidden shadow-2xl">
        <div class="px-4 py-2.5 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-xs font-mono text-slate-400">
          <span class="text-emerald-400 font-bold uppercase">${lang || 'CODE'}</span>
          <span class="text-[10px]">utf-8</span>
        </div>
        <pre class="p-4 sm:p-5 overflow-x-auto text-xs sm:text-sm font-mono text-emerald-300 leading-relaxed"><code>${code.trim()}</code></pre>
      </div>`;
    });

    const paragraphs = md.split(/\n\n+/);
    return paragraphs.map(p => {
      p = p.trim();
      if (p.startsWith('<div') || p.startsWith('<h') || p.startsWith('<li')) {
        return p;
      }
      return `<p class="text-slate-800 leading-[1.85] font-sans mb-6 text-justify sm:text-left">${p.replace(/\n/g, '<br/>')}</p>`;
    }).join('\n');
  } else {
    // Classic Dark Midnight typography
    md = md.replace(/^### (.*$)/gim, '<h3 class="text-xl font-display font-bold text-white mt-8 mb-3 flex items-center gap-2"><span class="text-phantom-mint text-base">✦</span>$1</h3>');
    md = md.replace(/^## (.*$)/gim, '<h2 class="text-2xl sm:text-3xl font-display font-bold text-white mt-10 mb-4 pb-2 border-b border-white/10">$1</h2>');
    md = md.replace(/^# (.*$)/gim, '<h1 class="text-3xl sm:text-4xl font-display font-extrabold text-white mt-12 mb-6">$1</h1>');

    md = md.replace(/\*\*(.*?)\*\*/gim, '<strong class="font-bold text-phantom-mint">$1</strong>');
    md = md.replace(/\*(.*?)\*/gim, '<em class="italic text-slate-200">$1</em>');

    md = md.replace(/^\- (.*$)/gim, '<li class="flex items-start gap-2.5 my-2 text-slate-300"><span class="text-phantom-mint font-bold">⚡</span><span>$1</span></li>');

    md = md.replace(/```([a-z]*)\n([\s\S]*?)```/gim, (match, lang, code) => {
      return `<div class="my-6 rounded-2xl bg-midnight-900 border border-white/10 overflow-hidden shadow-xl">
        <div class="px-4 py-2 bg-midnight-950 border-b border-white/5 flex items-center justify-between text-xs font-mono text-slate-400">
          <span class="text-phantom-mint font-bold uppercase">${lang || 'CODE'}</span>
          <span class="text-[10px]">utf-8</span>
        </div>
        <pre class="p-4 overflow-x-auto text-xs sm:text-sm font-mono text-emerald-300 leading-relaxed"><code>${code.trim()}</code></pre>
      </div>`;
    });

    const paragraphs = md.split(/\n\n+/);
    return paragraphs.map(p => {
      p = p.trim();
      if (p.startsWith('<div') || p.startsWith('<h') || p.startsWith('<li')) {
        return p;
      }
      return `<p class="text-slate-200 leading-relaxed font-sans mb-5">${p.replace(/\n/g, '<br/>')}</p>`;
    }).join('\n');
  }
});

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
  }
});

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('scroll', handleScroll);
  }
});

const articleJsonLd = computed(() => ({
  '@context': 'https://schema.org',
  '@type': 'BlogPosting',
  'headline': props.article.title,
  'description': props.article.excerpt,
  'url': `https://macatung.dev/blog/${props.article.slug}`,
  'datePublished': props.article.published_at,
  'dateModified': props.article.published_at,
  'author': {
    '@type': 'Person',
    'name': 'Ma Cà Tưng',
    'url': 'https://macatung.dev'
  },
  'publisher': {
    '@type': 'Person',
    'name': 'Ma Cà Tưng',
    'url': 'https://macatung.dev'
  },
  'keywords': props.article.tags ? props.article.tags.join(', ') : '',
  'articleSection': 'Technology & Architecture',
  'inLanguage': 'vi'
}));
</script>

<template>
  <SeoHead
    :title="article.title"
    :description="article.excerpt"
    :keywords="article.tags?.join(', ')"
    :canonical="`https://macatung.dev/blog/${article.slug}`"
    og-type="article"
    :article="{
      publishedTime: article.published_at,
      author: 'Ma Cà Tưng',
      section: 'Technology',
      tags: article.tags
    }"
    :json-ld="articleJsonLd"
  />

  <div class="min-h-screen bg-midnight-950 text-slate-100 selection:bg-phantom-mint selection:text-midnight-950 flex flex-col justify-between relative overflow-x-hidden w-full bg-grid-pattern">
    <!-- Reading Progress Bar Fixed at Top -->
    <div
      class="fixed top-0 left-0 h-1 bg-gradient-to-r from-phantom-mint via-phantom-cyan to-talisman-gold z-50 transition-all duration-150"
      :style="{ width: `${readingProgressPercent}%` }"
    />

    <TalismanCanvas />
    <Navbar />

    <main class="relative z-10 flex-1 w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-left">
      <!-- Breadcrumbs -->
      <nav class="flex items-center gap-2 text-xs font-mono text-slate-400 mb-8" aria-label="Breadcrumb">
        <Link href="/" class="hover:text-phantom-mint transition-colors">Trang Chủ</Link>
        <span>/</span>
        <Link href="/blog" class="hover:text-phantom-mint transition-colors">Góc Kiến Thức</Link>
        <span>/</span>
        <span class="text-slate-200 truncate max-w-xs sm:max-w-md">{{ article.title }}</span>
      </nav>

      <!-- Article Header -->
      <header class="mb-8 pb-6 border-b border-white/10">
        <!-- Tags Array -->
        <div class="flex flex-wrap gap-2 mb-4">
          <span
            v-for="tag in article.tags"
            :key="tag"
            class="px-3 py-1 rounded-lg text-xs font-mono font-bold bg-midnight-900 border border-white/10 text-phantom-mint shadow-glow-mint"
          >
            #{{ tag }}
          </span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white tracking-tight leading-tight mb-6">
          {{ article.title }}
        </h1>

        <!-- Reading Meta Info & Reader Control Toolbar -->
        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-white/5 text-xs font-mono text-slate-400">
          <div class="flex flex-wrap items-center gap-3 sm:gap-4">
            <span class="flex items-center gap-1.5 text-phantom-mint font-bold">
              ⏱ {{ article.reading_time_min }} Phút Đọc
            </span>
            <span>·</span>
            <span>👁 {{ (article.view_count || 120).toLocaleString() }} Lượt Xem</span>
            <span>·</span>
            <span>📅 {{ new Date(article.published_at).toLocaleDateString('vi-VN') }}</span>
          </div>

          <!-- Reader Controls: Theme Paper/Night Mode & Font Size -->
          <div class="flex items-center gap-2.5">
            <!-- Theme Toggle: Nền Trắng Sáng / Nền Tối -->
            <button
              @click="toggleTheme"
              :class="[
                'flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all cursor-pointer font-sans font-bold text-xs shadow-sm',
                isPaperMode
                  ? 'bg-white text-slate-900 border-emerald-400 shadow-glow-mint'
                  : 'bg-midnight-900 text-slate-300 border-white/10 hover:border-phantom-mint/40'
              ]"
              :title="isPaperMode ? 'Chuyển sang nền tối' : 'Chuyển sang nền trắng giấy sáng'"
            >
              <span>{{ isPaperMode ? '☀️ Nền Sáng' : '🌙 Nền Tối' }}</span>
            </button>

            <!-- Adjust Font Size -->
            <div class="flex items-center bg-midnight-900 rounded-xl border border-white/10 p-0.5 text-xs">
              <button
                @click="fontSize = Math.max(15, fontSize - 1); sound.playClick()"
                class="px-2 py-1 hover:bg-white/10 rounded-lg text-slate-300 font-bold"
                title="Giảm cỡ chữ"
              >
                A-
              </button>
              <span class="px-2 font-mono text-phantom-mint">{{ fontSize }}px</span>
              <button
                @click="fontSize = Math.min(26, fontSize + 1); sound.playClick()"
                class="px-2 py-1 hover:bg-white/10 rounded-lg text-slate-300 font-bold"
                title="Tăng cỡ chữ"
              >
                A+
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Reading Container Card (High-Contrast White Paper vs Dark Mode) -->
      <article
        :class="[
          'rounded-3xl p-6 sm:p-10 lg:p-12 mb-16 relative overflow-hidden transition-all duration-300',
          isPaperMode
            ? 'bg-white text-slate-900 border border-slate-200/90 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.45)]'
            : 'bg-midnight-900/90 text-slate-100 border border-white/10 shadow-2xl backdrop-blur-md'
        ]"
      >
        <!-- Top Accent Shimmer Bar -->
        <div
          class="absolute top-0 left-0 right-0 h-1.5"
          :class="isPaperMode ? 'bg-gradient-to-r from-emerald-500 via-teal-400 to-amber-500' : 'bg-gradient-to-r from-phantom-mint via-phantom-cyan to-talisman-gold'"
        />

        <!-- Article Excerpt Highlight Box -->
        <div
          :class="[
            'p-5 sm:p-6 rounded-2xl mb-8 font-sans italic text-base sm:text-lg leading-relaxed shadow-sm transition-all',
            isPaperMode
              ? 'bg-emerald-50/80 border-l-4 border-emerald-600 text-slate-800'
              : 'bg-midnight-950/80 border-l-4 border-phantom-mint text-slate-200'
          ]"
        >
          "{{ article.excerpt }}"
        </div>

        <!-- Article Rendered Content -->
        <div
          class="article-body max-w-none text-left"
          :style="{ fontSize: `${fontSize}px` }"
          :class="isPaperMode ? 'prose prose-slate' : 'prose prose-invert'"
          v-html="formattedContent"
        />
      </article>

      <!-- Case Study Cross-Link Box (2-Way Bridge back to Projects) -->
      <div
        v-if="getRelatedProjectInfo(article.slug)"
        class="p-6 sm:p-8 rounded-3xl glass-panel border border-talisman-gold/30 bg-gradient-to-r from-talisman-gold/5 via-midnight-900/80 to-phantom-mint/5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-16 shadow-glow-talisman"
      >
        <div class="flex items-center gap-4">
          <span class="text-3xl sm:text-4xl">{{ getRelatedProjectInfo(article.slug)!.icon }}</span>
          <div>
            <div class="flex items-center gap-2">
              <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-talisman-gold/20 text-talisman-gold border border-talisman-gold/30">
                {{ getRelatedProjectInfo(article.slug)!.badge }}
              </span>
              <span class="text-xs font-mono text-slate-400">Case Study Thực Chiến Liên Quan</span>
            </div>
            <h4 class="font-display font-bold text-white text-base sm:text-lg mt-1">
              {{ getRelatedProjectInfo(article.slug)!.title }}
            </h4>
            <p class="text-xs text-slate-300 mt-1 font-sans">
              {{ getRelatedProjectInfo(article.slug)!.desc }}
            </p>
          </div>
        </div>

        <Link
          href="/projects"
          class="px-5 py-3 rounded-2xl bg-talisman-gold text-midnight-950 font-display font-bold text-xs sm:text-sm hover:brightness-110 shadow-glow-talisman transition-all whitespace-nowrap shrink-0 flex items-center gap-1.5"
          @click="sound.playClick()"
        >
          <span>Xem Chi Tiết Dự Án</span>
          <span>→</span>
        </Link>
      </div>

      <!-- Author Anonymous Persona Box -->
      <div class="p-6 sm:p-8 rounded-3xl glass-panel border border-white/10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 mb-16">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-2xl bg-midnight-900 border border-phantom-mint/40 flex items-center justify-center text-2xl shadow-glow-mint">
            🧙‍♂️
          </div>
          <div>
            <div class="font-display font-bold text-white text-base sm:text-lg">The Midnight Architect</div>
            <div class="text-xs font-mono text-phantom-mint">Senior Backend / Fullstack & AI Agent Architect</div>
            <div class="text-xs text-slate-400 mt-1">Ghi chép chuyên môn được thực thi trong những phiên code 00:00 — 05:00 AM.</div>
          </div>
        </div>
        <Link
          href="/contact"
          class="px-4 py-2.5 rounded-xl bg-phantom-mint text-midnight-950 font-display font-bold text-xs hover:brightness-110 shadow-glow-mint transition-all whitespace-nowrap shrink-0"
          @click="sound.playTalisman()"
        >
          Trao Đổi Kiến Trúc 📜
        </Link>
      </div>

      <!-- Related Articles -->
      <div v-if="relatedArticles.length > 0" class="pt-8 border-t border-white/10">
        <h3 class="font-display font-bold text-xl sm:text-2xl text-white mb-6">Ghi Chép Cùng Chuyên Mục</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Link
            v-for="rel in relatedArticles"
            :key="rel.id"
            :href="`/blog/${rel.slug}`"
            class="p-5 rounded-2xl bg-midnight-900/80 border border-white/5 hover:border-phantom-mint/30 transition-all flex flex-col justify-between group"
          >
            <div>
              <div class="text-[10px] font-mono text-phantom-mint mb-2">⏱ {{ rel.reading_time_min }} phút</div>
              <h4 class="font-display font-bold text-sm text-white group-hover:text-phantom-mint transition-colors line-clamp-2 mb-2">
                {{ rel.title }}
              </h4>
              <p class="text-xs text-slate-400 line-clamp-2">{{ rel.excerpt }}</p>
            </div>
            <div class="text-[11px] font-mono text-slate-400 group-hover:text-phantom-mint mt-4 flex items-center gap-1">
              <span>Xem bài viết</span>
              <span>→</span>
            </div>
          </Link>
        </div>
      </div>

      <!-- Next Steps Continuation Hub -->
      <NextStepsHub :current-path="`/blog/${article.slug}`" />
    </main>

    <Footer />
  </div>
</template>

<style scoped>
.article-body :deep(h2) {
  font-family: 'Space Grotesk', system-ui, sans-serif;
  margin-top: 2.5rem;
  margin-bottom: 1rem;
}
.article-body :deep(h3) {
  font-family: 'Space Grotesk', system-ui, sans-serif;
  margin-top: 2rem;
  margin-bottom: 0.75rem;
}
.article-body :deep(p) {
  line-height: 1.8;
  margin-bottom: 1.25rem;
}
</style>
