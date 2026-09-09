<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import DecodeLayout from '@/Layouts/DecodeLayout.vue';
import Icons from '@/Components/ui/Icons.vue';

interface Callout {
  type: string;
  title: string;
  body: string;
}

interface CodeSnippet {
  language: string;
  filename: string;
  code: string;
}

interface ArticleSection {
  id: string;
  title: string;
  lead: string;
  content: string;
  callout?: Callout;
  code_snippet?: CodeSnippet;
}

interface KeyNode {
  step: number;
  time: string;
  node: string;
  protocol: string;
  action: string;
  latency: string;
  status: string;
}

interface Episode {
  id: number;
  episode_number: string;
  slug: string;
  title: string;
  short_title: string;
  duration: string;
  total_latency: string;
  category: string;
  published_at?: string;
  reading_time?: string;
  author?: string;
  series_title?: string;
  summary: string;
  hook: string;
  takeaways?: string[];
  sections?: ArticleSection[];
  tags: string[];
  status: string;
  key_nodes: KeyNode[];
}

interface NavEpisode {
  id: number;
  slug: string;
  episode_number: string;
  short_title: string;
  title: string;
  total_latency: string;
}

interface EpisodeSummary {
  id: number;
  slug: string;
  episode_number: string;
  short_title: string;
  title: string;
  total_latency: string;
  category?: string;
  reading_time?: string;
}

const props = defineProps<{
  episode: Episode;
  prevEpisode?: NavEpisode | null;
  nextEpisode?: NavEpisode | null;
  allEpisodes: EpisodeSummary[];
}>();

// Reading Progress Tracking
const readingProgress = ref(0);
const updateReadingProgress = () => {
  const scrollTop = window.scrollY || document.documentElement.scrollTop;
  const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
  if (docHeight > 0) {
    readingProgress.value = Math.min(100, Math.max(0, Math.round((scrollTop / docHeight) * 100)));
  }
};

onMounted(() => {
  window.addEventListener('scroll', updateReadingProgress, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', updateReadingProgress);
});

// Copy Code Snippet Helper
const copiedSnippetKey = ref<string | null>(null);
const copyCode = (text: string, key: string) => {
  if (navigator?.clipboard) {
    navigator.clipboard.writeText(text);
    copiedSnippetKey.value = key;
    setTimeout(() => {
      copiedSnippetKey.value = null;
    }, 2000);
  }
};
</script>

<template>
  <DecodeLayout
    :title="'Tập ' + episode.episode_number + ': ' + episode.short_title"
    :description="episode.summary"
  >
    <!-- Sticky Reading Progress Bar -->
    <div
      class="fixed top-16 left-0 right-0 h-1 bg-transparent z-40 pointer-events-none"
    >
      <div
        class="h-full bg-gradient-to-r from-[#00f5d4] to-[#00b4d8] transition-all duration-150"
        :style="{ width: readingProgress + '%' }"
      />
    </div>

    <!-- Article Header Masthead -->
    <header class="relative pt-12 pb-14 sm:pt-16 sm:pb-16 bg-[#070b16] border-b border-slate-800/80">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-left space-y-6">
        <!-- Breadcrumb & Back to Series -->
        <nav class="flex items-center gap-2 font-mono text-xs text-slate-400">
          <Link href="/decode" class="text-slate-400 hover:text-[#00f5d4] flex items-center gap-1 transition-colors">
            <Icons name="ChevronRight" :size="13" class="rotate-180" />
            <span>Trang Chủ Ma Giải Mã</span>
          </Link>
          <span class="text-slate-600">/</span>
          <a href="/decode#danh-sach-bai-viet" class="text-slate-400 hover:text-[#00f5d4] transition-colors">
            Danh Sách Bài Viết
          </a>
          <span class="text-slate-600">/</span>
          <span class="text-[#00f5d4] font-semibold">Tập {{ episode.episode_number }}</span>
        </nav>

        <!-- Series Tag & Meta Pills -->
        <div class="flex flex-wrap items-center gap-2.5">
          <span class="px-2.5 py-1 rounded bg-[#00f5d4]/15 border border-[#00f5d4]/30 text-[#00f5d4] font-mono text-xs font-bold">
            PILOT SEASON • TẬP {{ episode.episode_number }}
          </span>
          <span class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-slate-300 font-mono text-xs">
            {{ episode.category }}
          </span>
          <span class="px-2.5 py-1 rounded bg-slate-900 border border-amber-500/20 text-amber-400 font-mono text-xs font-medium">
            Độ trễ khảo cứu: {{ episode.total_latency }}
          </span>
        </div>

        <!-- Article Headline -->
        <h1 class="font-display text-3xl sm:text-5xl font-black text-white leading-tight">
          {{ episode.title }}
        </h1>

        <!-- Story Hook / Lead In -->
        <p class="text-base sm:text-xl text-slate-300 font-sans leading-relaxed max-w-4xl">
          {{ episode.hook }}
        </p>

        <!-- Author & Published Meta Strip -->
        <div class="pt-4 flex flex-wrap items-center justify-between gap-4 border-t border-slate-800 text-xs font-mono text-slate-400">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-sm">
              🧛‍♂️
            </div>
            <div>
              <div class="text-white font-semibold">Ma Cà Tưng (@macatung)</div>
              <div class="text-[11px] text-slate-500">Khảo cứu kiến trúc hệ thống</div>
            </div>
          </div>

          <div class="flex items-center gap-4 text-slate-400">
            <span v-if="episode.published_at">{{ episode.published_at }}</span>
            <span>•</span>
            <span class="flex items-center gap-1">
              <Icons name="Clock" :size="13" /> {{ episode.reading_time || '15 phút đọc' }}
            </span>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content Container: 2 Columns Layout -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-left">
      <div class="flex flex-col lg:flex-row gap-12 items-start">
        <!-- LEFT SIDEBAR: Table of Contents & Series Navigator (Sticky) -->
        <aside class="w-full lg:w-72 lg:shrink-0 lg:sticky lg:top-24 space-y-6 order-2 lg:order-1">
          <!-- Table of Contents Card -->
          <div class="p-5 rounded-xl bg-[#091020] border border-slate-800 space-y-4">
            <div class="text-xs font-mono font-bold text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
              <Icons name="FileText" :size="14" class="text-[#00f5d4]" />
              <span>Mục Lục Bài Viết</span>
            </div>
            <nav class="space-y-2 text-xs font-sans">
              <a
                v-if="episode.takeaways && episode.takeaways.length > 0"
                href="#takeaways"
                class="block text-slate-400 hover:text-[#00f5d4] transition-colors py-0.5"
              >
                • Điểm Cốt Lõi Kiến Trúc
              </a>
              <a
                v-for="sec in episode.sections"
                :key="sec.id"
                :href="`#${sec.id}`"
                class="block text-slate-400 hover:text-[#00f5d4] transition-colors py-0.5 line-clamp-1"
              >
                • {{ sec.title }}
              </a>
              <a
                href="#timeline-anatomy"
                class="block text-slate-400 hover:text-[#00f5d4] transition-colors py-0.5"
              >
                • Phân Rã Timeline Từng Giây
              </a>
              <a
                href="#series-navigation"
                class="block text-slate-400 hover:text-[#00f5d4] transition-colors py-0.5"
              >
                • Điều Hướng Series
              </a>
            </nav>
          </div>

          <!-- Series Pilot Tracker Card -->
          <div class="p-5 rounded-xl bg-[#091020] border border-slate-800 space-y-3">
            <div class="flex items-center justify-between text-xs font-mono">
              <span class="font-bold text-slate-200 uppercase tracking-wider">Series Pilot Season</span>
              <span class="text-[#00f5d4]">5 Tập</span>
            </div>
            <div class="space-y-1.5 font-sans text-xs">
              <Link
                v-for="item in allEpisodes"
                :key="item.id"
                :href="`/decode/tap/${item.slug}`"
                class="block p-2 rounded-lg transition-all"
                :class="item.slug === episode.slug ? 'bg-[#00f5d4]/10 border border-[#00f5d4]/30 text-[#00f5d4] font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900/60'"
              >
                <div class="flex items-center justify-between font-mono text-[10px] mb-0.5">
                  <span>TẬP {{ item.episode_number }}</span>
                  <span class="text-slate-500">{{ item.total_latency }}</span>
                </div>
                <div class="line-clamp-1">
                  {{ item.short_title }}
                </div>
              </Link>
            </div>
          </div>

          <!-- Author Backlink Card -->
          <div class="p-4 rounded-xl bg-[#060a14] border border-slate-800/80 text-xs text-slate-400 space-y-2">
            <div class="font-mono text-slate-300 font-semibold">Tác Giả: Ma Cà Tưng</div>
            <p class="font-sans leading-relaxed text-[11px]">
              Kỹ sư phần mềm &amp; kiến trúc sư hệ thống. Khám phá các dự án lập trình lúc nửa đêm tại <a href="https://macatung.dev" target="_blank" class="text-[#00f5d4] underline">macatung.dev ↗</a>.
            </p>
          </div>
        </aside>

        <!-- RIGHT MAIN COLUMN: Long-Form Technical Content -->
        <main class="flex-1 max-w-3xl space-y-12 order-1 lg:order-2">
          <!-- EXECUTIVE SUMMARY & KEY TAKEAWAYS -->
          <section id="takeaways" class="p-6 sm:p-8 rounded-2xl bg-[#091020] border border-slate-800 space-y-4">
            <div class="flex items-center gap-2 text-xs font-mono font-bold text-[#00f5d4] uppercase tracking-wider">
              <Icons name="Terminal" :size="14" />
              <span>Tóm Tắt Kiến Trúc Cốt Lõi (Key Architectural Takeaways)</span>
            </div>

            <ul class="space-y-3 text-sm text-slate-300 font-sans leading-relaxed">
              <li v-for="(item, tIdx) in episode.takeaways" :key="tIdx" class="flex items-start gap-2.5">
                <span class="w-5 h-5 rounded bg-[#00f5d4]/15 text-[#00f5d4] font-mono text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">
                  0{{ tIdx + 1 }}
                </span>
                <span>{{ item }}</span>
              </li>
            </ul>
          </section>

          <!-- IN-DEPTH ARTICLE SECTIONS -->
          <section
            v-for="sec in episode.sections"
            :key="sec.id"
            :id="sec.id"
            class="space-y-6 pt-4"
          >
            <!-- Section Title -->
            <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white tracking-tight border-b border-slate-800/80 pb-3">
              {{ sec.title }}
            </h2>

            <!-- Section Lead -->
            <p v-if="sec.lead" class="text-base sm:text-lg font-medium text-slate-200 font-sans leading-relaxed italic">
              "{{ sec.lead }}"
            </p>

            <!-- Section Body Paragraphs -->
            <div class="space-y-4 text-slate-300 font-sans text-sm sm:text-base leading-relaxed whitespace-pre-line">
              {{ sec.content }}
            </div>

            <!-- Optional Technical Callout Box -->
            <div
              v-if="sec.callout"
              class="p-5 rounded-xl border text-left space-y-2 font-sans"
              :class="{
                'bg-cyan-950/20 border-cyan-500/30 text-cyan-200': sec.callout.type === 'info',
                'bg-amber-950/20 border-amber-500/30 text-amber-200': sec.callout.type === 'warning' || sec.callout.type === 'important',
                'bg-emerald-950/20 border-emerald-500/30 text-emerald-200': sec.callout.type === 'tip'
              }"
            >
              <div class="flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider">
                <Icons name="Info" :size="14" />
                <span>{{ sec.callout.title }}</span>
              </div>
              <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                {{ sec.callout.body }}
              </p>
            </div>

            <!-- Optional Code Snippet / Protocol Dump Box -->
            <div
              v-if="sec.code_snippet"
              class="rounded-xl bg-[#04070e] border border-slate-800 overflow-hidden font-mono text-xs"
            >
              <div class="flex items-center justify-between px-4 py-2.5 bg-slate-900/80 border-b border-slate-800 text-slate-400 text-[11px]">
                <div class="flex items-center gap-2">
                  <span class="w-2.5 h-2.5 rounded-full bg-slate-700" />
                  <span class="font-semibold text-slate-200">{{ sec.code_snippet.filename }}</span>
                </div>
                <button
                  type="button"
                  class="flex items-center gap-1 text-slate-400 hover:text-white transition-colors"
                  @click="copyCode(sec.code_snippet.code, sec.id)"
                >
                  <Icons :name="copiedSnippetKey === sec.id ? 'Check' : 'Copy'" :size="12" />
                  <span>{{ copiedSnippetKey === sec.id ? 'Đã sao chép' : 'Sao chép' }}</span>
                </button>
              </div>
              <pre class="p-4 overflow-x-auto text-slate-300 leading-relaxed text-xs"><code>{{ sec.code_snippet.code }}</code></pre>
            </div>
          </section>

          <!-- SECTION: KEY NODES TIMELINE BREAKDOWN -->
          <section id="timeline-anatomy" class="pt-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
              <div class="flex items-center gap-2 text-xs font-mono text-[#00f5d4] uppercase font-bold tracking-wider">
                <Icons name="Activity" :size="15" />
                <span>Phân Rã Timeline Từng Giây ({{ episode.key_nodes.length }} Trạm Kiểm Soát)</span>
              </div>
              <span class="text-xs font-mono text-amber-400 font-bold">Tổng độ trễ: {{ episode.total_latency }}</span>
            </div>

            <div class="space-y-4">
              <div
                v-for="(node, nIdx) in episode.key_nodes"
                :key="nIdx"
                class="p-5 rounded-xl bg-[#080f1e] border border-slate-800 hover:border-slate-700 transition-all flex flex-col sm:flex-row sm:items-start gap-4"
              >
                <!-- Step Pin -->
                <div class="w-10 h-10 rounded-lg bg-slate-900 border border-[#00f5d4]/40 flex items-center justify-center font-mono font-bold text-[#00f5d4] text-sm shrink-0">
                  0{{ node.step }}
                </div>

                <!-- Step Info -->
                <div class="flex-1 space-y-1.5">
                  <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="font-display font-bold text-base text-white">
                      {{ node.node }}
                    </h3>
                    <div class="flex items-center gap-2 font-mono text-[11px]">
                      <span class="text-amber-400 font-semibold">{{ node.time }}</span>
                      <span>•</span>
                      <span class="text-[#00f5d4]">{{ node.status }}</span>
                    </div>
                  </div>

                  <div class="text-xs font-mono text-[#38bdf8]">
                    Giao thức: {{ node.protocol }}
                  </div>

                  <p class="text-xs sm:text-sm text-slate-300 font-sans leading-relaxed pt-1">
                    {{ node.action }}
                  </p>
                </div>
              </div>
            </div>
          </section>

          <!-- PREV / NEXT NAVIGATION CARDS -->
          <nav id="series-navigation" class="pt-8 border-t border-slate-800 space-y-4">
            <div class="text-xs font-mono text-slate-400 uppercase tracking-wider font-bold">
              Điều Hướng Trong Series
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Previous Episode -->
              <Link
                v-if="prevEpisode"
                :href="`/decode/tap/${prevEpisode.slug}`"
                class="p-4 rounded-xl bg-[#091020] border border-slate-800 hover:border-slate-700 transition-all text-left space-y-1 group block"
              >
                <div class="text-[10px] font-mono text-slate-500 uppercase flex items-center gap-1">
                  <Icons name="ChevronRight" :size="10" class="rotate-180" />
                  <span>TẬP TRƯỚC (TẬP {{ prevEpisode.episode_number }})</span>
                </div>
                <div class="font-display font-bold text-sm text-white group-hover:text-[#00f5d4] transition-colors line-clamp-1">
                  {{ prevEpisode.title }}
                </div>
              </Link>
              <div v-else class="p-4 rounded-xl bg-[#060a14] border border-slate-900 text-slate-600 text-xs font-mono">
                Đây là bài viết mở đầu series.
              </div>

              <!-- Next Episode -->
              <Link
                v-if="nextEpisode"
                :href="`/decode/tap/${nextEpisode.slug}`"
                class="p-4 rounded-xl bg-[#091020] border border-slate-800 hover:border-[#00f5d4]/40 transition-all text-right space-y-1 group block sm:col-start-2"
              >
                <div class="text-[10px] font-mono text-[#00f5d4] uppercase flex items-center justify-end gap-1 font-bold">
                  <span>TẬP TIẾP THEO (TẬP {{ nextEpisode.episode_number }})</span>
                  <Icons name="ChevronRight" :size="10" />
                </div>
                <div class="font-display font-bold text-sm text-white group-hover:text-[#00f5d4] transition-colors line-clamp-1">
                  {{ nextEpisode.title }}
                </div>
              </Link>
              <div v-else class="p-4 rounded-xl bg-[#060a14] border border-slate-900 text-slate-600 text-xs font-mono text-right sm:col-start-2">
                Bạn đã đọc đến tập cuối của Pilot Season.
              </div>
            </div>
          </nav>

          <!-- DISCUSSION & YOUTUBE CALLOUT -->
          <div class="p-6 rounded-2xl bg-gradient-to-r from-[#091428] to-[#070b16] border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="space-y-1 text-left">
              <h3 class="font-display font-bold text-white text-base">Xem Video Hoạt Họa Isometric 3D</h3>
              <p class="text-xs text-slate-400 font-sans">
                Tập này được minh họa chi tiết từng mili-giây trên kênh YouTube chính thức.
              </p>
            </div>
            <a
              href="https://youtube.com/@MaGiaiMa"
              target="_blank"
              rel="noopener noreferrer"
              class="px-5 py-2.5 rounded-lg bg-[#ff0054] hover:bg-[#e11d48] text-white text-xs font-bold font-mono transition-all whitespace-nowrap shadow-md shadow-[#ff0054]/20 flex items-center gap-1.5 shrink-0"
            >
              <Icons name="Play" :size="14" />
              <span>Xem Trên @MaGiaiMa</span>
            </a>
          </div>
        </main>
      </div>
    </div>
  </DecodeLayout>
</template>
