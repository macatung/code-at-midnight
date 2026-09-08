<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icons from '@/Components/ui/Icons.vue';

interface ArticleItem {
  id: number;
  title: string;
  pali_title?: string | null;
  slug: string;
  category?: string | null;
  author?: string | null;
  excerpt?: string | null;
  video_status: 'draft' | 'processing' | 'completed' | 'published' | 'failed' | string;
  video_long_url?: string | null;
  video_short_url?: string | null;
  youtube_url?: string | null;
  youtube_id?: string | null;
  thumbnail_long_url?: string | null;
  thumbnail_short_url?: string | null;
  video_long_duration?: string | null;
  video_short_duration?: string | null;
  seo_title?: string | null;
  seo_description?: string | null;
  social_caption?: string | null;
  hashtags?: string[] | null;
  has_video?: boolean;
  pipeline_task_id?: string | null;
  pipeline_started_at?: string | null;
  pipeline_completed_at?: string | null;
  pipeline_error?: string | null;
  is_published: boolean;
  updated_at: string;
}

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface PaginatedArticles {
  data: ArticleItem[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  links: PaginationLink[];
}

const props = defineProps<{
  articles: PaginatedArticles;
  filters: {
    status: string;
    search: string;
  };
  statusCounts: {
    all: number;
    draft: number;
    processing: number;
    completed: number;
    published: number;
  };
}>();

const currentStatus = ref(props.filters.status || 'all');
const searchQuery = ref(props.filters.search || '');
const isTriggering = ref<number | null>(null);

// Quick Copy State
const activeQuickCopyId = ref<number | null>(null);
const copySuccess = ref<Record<string, boolean>>({});

const filterTabs = [
  { key: 'all', label: 'Tất cả' },
  { key: 'completed', label: 'Đã Hoàn Thành' },
  { key: 'published', label: 'Đã Xuất Bản' },
  { key: 'processing', label: 'Đang Xử Lý' },
  { key: 'draft', label: 'Bản Nháp' },
];

const applyFilter = (statusKey: string) => {
  currentStatus.value = statusKey;
  router.get(
    '/admin/theravada/videos',
    {
      status: statusKey,
      search: searchQuery.value || undefined,
    },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
};

const handleSearch = () => {
  router.get(
    '/admin/theravada/videos',
    {
      status: currentStatus.value !== 'all' ? currentStatus.value : undefined,
      search: searchQuery.value || undefined,
    },
    {
      preserveState: true,
      preserveScroll: true,
    }
  );
};

const clearSearch = () => {
  searchQuery.value = '';
  handleSearch();
};

const triggerPipeline = (article: ArticleItem) => {
  if (isTriggering.value !== null) return;
  if (confirm(`Kích hoạt tiến trình tự động sản xuất video cho bài viết:\n"${article.title}"?`)) {
    isTriggering.value = article.id;
    router.post(
      `/admin/theravada/videos/${article.id}/trigger`,
      {},
      {
        preserveScroll: true,
        onFinish: () => {
          isTriggering.value = null;
        },
      }
    );
  }
};

const togglePublish = (article: ArticleItem) => {
  const actionText = article.video_status === 'published' ? 'gỡ xuất bản' : 'xuất bản công khai';
  if (confirm(`Bạn có chắc muốn ${actionText} video này không?`)) {
    router.patch(
      `/admin/theravada/videos/${article.id}/publish`,
      {},
      {
        preserveScroll: true,
      }
    );
  }
};

const formatDuration = (val?: string | null) => {
  if (!val) return '—';
  const num = Number(val);
  if (!isNaN(num)) {
    const mins = Math.floor(num / 60);
    const secs = Math.floor(num % 60);
    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
  }
  return val;
};

// Quick Copy Actions
const toggleQuickCopy = (id: number, event: MouseEvent) => {
  event.stopPropagation();
  activeQuickCopyId.value = activeQuickCopyId.value === id ? null : id;
};

const handleCopyText = async (text: string, key: string) => {
  if (!text) return;
  try {
    await navigator.clipboard.writeText(text);
  } catch {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
  }

  copySuccess.value[key] = true;
  setTimeout(() => {
    copySuccess.value[key] = false;
  }, 1800);
};

const getArticleHashtagsFormatted = (article: ArticleItem) => {
  if (Array.isArray(article.hashtags) && article.hashtags.length > 0) {
    return article.hashtags.map((h) => (h.startsWith('#') ? h : `#${h}`)).join(' ');
  }
  return '#TamAnVanSuAn #LoiPhatDay #Theravada #macatungdev';
};

const copyFullKit = (article: ArticleItem) => {
  const title = article.seo_title || article.title;
  const desc = article.seo_description || article.excerpt || '';
  const caption = article.social_caption || '';
  const tags = getArticleHashtagsFormatted(article);
  const link = `https://theravada.macatung.dev/phap-thoai/${article.slug}`;

  const fullText = [
    `=== TIÊU ĐỀ VIDEO YOUTUBE ===`,
    title,
    ``,
    `=== MÔ TẢ VIDEO (YOUTUBE / FACEBOOK) ===`,
    desc,
    ``,
    `=== CAPTION MẠNG XÃ HỘI (TIKTOK / REELS) ===`,
    caption,
    ``,
    `=== HASHTAGS BÀI ĐĂNG ===`,
    tags,
    ``,
    `=== LIÊN KẾT BÀI VIẾT GỐC ===`,
    link,
  ].join('\n');

  handleCopyText(fullText, `full-${article.id}`);
};

const closeQuickCopy = () => {
  activeQuickCopyId.value = null;
};

onMounted(() => {
  window.addEventListener('click', closeQuickCopy);
});

onUnmounted(() => {
  window.removeEventListener('click', closeQuickCopy);
});
</script>

<template>
  <AdminLayout title="Trạm Phát Hành Video Đa Nền Tảng">
    <Head title="Trạm Phát Hành Video Đa Nền Tảng (Social Publishing Hub) — Admin" />

    <!-- Page Header & Stats Summary -->
    <div class="space-y-4 pb-3 border-b border-white/10">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-phantom-mint text-xs font-mono uppercase tracking-widest">
            <span>Theravāda Buddhism OS</span>
            <span>•</span>
            <span class="px-2 py-0.5 rounded bg-phantom-mint/10 border border-phantom-mint/20">Social Publishing Hub</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-display font-extrabold text-white mt-1 flex items-center gap-2.5">
            <span>Trạm Phát Hành Đa Nền Tảng</span>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
              Video & Shorts CMS
            </span>
          </h1>
          <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5 max-w-3xl">
            Quản trị, xem trước và phát hành video tự động đa định dạng (16:9 YouTube + 9:16 Reels/TikTok + Dual Thumbnails + Trọn bộ Metadata SEO & 1-Click Copy).
          </p>
        </div>

        <div class="flex items-center gap-3">
          <a
            href="https://theravada.macatung.dev"
            target="_blank"
            class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-mono transition-all flex items-center gap-1.5 border border-white/10"
          >
            <Icons name="ExternalLink" :size="14" />
            <span>Mở Cổng Theravāda</span>
          </a>
        </div>
      </div>

      <!-- Quick Metrics Ribbon -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
        <div
          class="p-3.5 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between cursor-pointer hover:bg-white/10 transition-all"
          @click="applyFilter('all')"
        >
          <div>
            <div class="text-[11px] font-mono text-slate-400">Tổng Bài Pháp</div>
            <div class="text-xl font-bold text-white font-display">{{ statusCounts.all }}</div>
          </div>
          <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-slate-300">
            <Icons name="FileText" :size="16" />
          </div>
        </div>

        <div
          class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-between cursor-pointer hover:bg-emerald-500/15 transition-all"
          @click="applyFilter('completed')"
        >
          <div>
            <div class="text-[11px] font-mono text-emerald-300">Đã Hoàn Thành (Ready)</div>
            <div class="text-xl font-bold text-emerald-400 font-display">{{ statusCounts.completed }}</div>
          </div>
          <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-300">
            <Icons name="Check" :size="16" />
          </div>
        </div>

        <div
          class="p-3.5 rounded-xl bg-phantom-mint/10 border border-phantom-mint/20 flex items-center justify-between cursor-pointer hover:bg-phantom-mint/15 transition-all"
          @click="applyFilter('published')"
        >
          <div>
            <div class="text-[11px] font-mono text-phantom-mint">Đã Xuất Bản (Live)</div>
            <div class="text-xl font-bold text-phantom-mint font-display">{{ statusCounts.published }}</div>
          </div>
          <div class="w-8 h-8 rounded-lg bg-phantom-mint/20 flex items-center justify-center text-phantom-mint">
            <Icons name="Play" :size="16" />
          </div>
        </div>

        <div
          class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-between cursor-pointer hover:bg-amber-500/15 transition-all"
          @click="applyFilter('processing')"
        >
          <div>
            <div class="text-[11px] font-mono text-amber-300">Đang Xử Lý</div>
            <div class="text-xl font-bold text-amber-400 font-display flex items-center gap-1.5">
              <span>{{ statusCounts.processing }}</span>
              <span v-if="statusCounts.processing > 0" class="inline-block w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            </div>
          </div>
          <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-300">
            <Icons name="Clock" :size="16" />
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Tabs & Search Bar -->
    <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 pt-2">
      <!-- Tabs -->
      <div class="flex items-center gap-1 p-1 rounded-xl bg-midnight-900/80 border border-white/10 overflow-x-auto no-scrollbar">
        <button
          v-for="tab in filterTabs"
          :key="tab.key"
          type="button"
          class="px-3.5 py-1.5 rounded-lg text-xs font-mono font-medium transition-all whitespace-nowrap flex items-center gap-2"
          :class="
            currentStatus === tab.key
              ? 'bg-phantom-mint text-midnight-950 font-bold shadow-sm'
              : 'text-slate-400 hover:text-white hover:bg-white/5'
          "
          @click="applyFilter(tab.key)"
        >
          <span>{{ tab.label }}</span>
          <span
            class="px-1.5 py-0.2 rounded-full text-[10px]"
            :class="
              currentStatus === tab.key
                ? 'bg-midnight-950/20 text-midnight-950 font-bold'
                : 'bg-white/10 text-slate-400'
            "
          >
            {{ (statusCounts as Record<string, number>)[tab.key] || 0 }}
          </span>
        </button>
      </div>

      <!-- Search Input -->
      <div class="relative min-w-[260px] sm:min-w-[320px]">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Tìm theo tiêu đề, Pāḷi hoặc slug..."
          class="w-full pl-9 pr-8 py-2 rounded-xl bg-midnight-900/90 border border-white/10 text-xs text-white placeholder:text-slate-500 focus:outline-none focus:border-phantom-mint font-sans"
          @keyup.enter="handleSearch"
        />
        <span class="absolute left-3 top-2.5 text-slate-500">
          <Icons name="Terminal" :size="14" />
        </span>
        <button
          v-if="searchQuery"
          type="button"
          class="absolute right-2.5 top-2 text-slate-400 hover:text-white"
          @click="clearSearch"
        >
          <Icons name="X" :size="14" />
        </button>
      </div>
    </div>

    <!-- Video Articles Table with Direct Visual Thumbnails & Quick Copy -->
    <div class="rounded-2xl glass-panel border border-white/10 overflow-visible text-left mt-2">
      <div v-if="articles.data.length === 0" class="p-16 text-center text-slate-500 font-mono text-xs space-y-2">
        <Icons name="FileText" :size="32" class="mx-auto text-slate-600 mb-2" />
        <div>Không tìm thấy bài giảng nào phù hợp với bộ lọc hiện tại.</div>
        <button
          v-if="currentStatus !== 'all' || searchQuery"
          type="button"
          class="text-phantom-mint hover:underline text-xs"
          @click="applyFilter('all')"
        >
          Đặt lại bộ lọc
        </button>
      </div>

      <div v-else class="overflow-x-auto no-scrollbar">
        <table class="w-full text-left text-xs font-sans">
          <thead class="bg-midnight-900/90 border-b border-white/5 text-slate-400 font-mono text-[11px] uppercase tracking-wider">
            <tr>
              <th class="p-3.5 min-w-[320px]">Video & Bài Pháp Thoại</th>
              <th class="p-3.5 text-center min-w-[140px]">Định Dạng</th>
              <th class="p-3.5 text-center min-w-[130px]">Trạng Thái</th>
              <th class="p-3.5 text-center min-w-[130px]">Thời Lượng</th>
              <th class="p-3.5 text-center min-w-[130px]">⚡ Copy Nhanh</th>
              <th class="p-3.5 text-right min-w-[160px]">Thao Tác</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5 text-slate-300">
            <tr
              v-for="item in articles.data"
              :key="item.id"
              class="hover:bg-white/5 transition-colors group"
            >
              <!-- Column 1: Video Visual Thumbnail & Article Title -->
              <td class="p-3.5">
                <div class="flex items-start gap-3">
                  <!-- Visual Thumbnail Card (16:9) -->
                  <div class="relative w-24 sm:w-28 aspect-video rounded-lg overflow-hidden bg-black/80 border border-white/10 shrink-0 shadow-md group/thumb">
                    <img
                      v-if="item.thumbnail_long_url"
                      :src="item.thumbnail_long_url"
                      :alt="item.title"
                      class="w-full h-full object-cover group-hover/thumb:scale-105 transition-transform duration-300"
                    />
                    <div v-else class="w-full h-full flex flex-col items-center justify-center text-slate-600 p-1">
                      <Icons name="Play" :size="16" />
                      <span class="text-[9px] font-mono mt-0.5">Chưa có</span>
                    </div>

                    <!-- Duration Overlay -->
                    <span
                      v-if="item.video_long_duration"
                      class="absolute bottom-1 right-1 px-1 py-0.2 rounded bg-black/80 text-white font-mono text-[9px] font-bold"
                    >
                      {{ formatDuration(item.video_long_duration) }}
                    </span>

                    <!-- YouTube Live Dot Badge -->
                    <span
                      v-if="item.youtube_url || item.youtube_id"
                      class="absolute top-1 left-1 px-1 py-0.2 rounded bg-red-600/90 text-white font-mono text-[8px] font-bold flex items-center gap-0.5 shadow"
                      title="Đã kết nối YouTube"
                    >
                      YT
                    </span>
                  </div>

                  <!-- Article Meta -->
                  <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                      <span class="text-[10px] font-mono text-phantom-mint font-bold">#{{ item.id }}</span>
                      <span class="px-1.5 py-0.2 rounded text-[10px] font-mono bg-white/5 text-slate-400 border border-white/5">
                        {{ item.category || 'phap-thoai' }}
                      </span>
                      <span v-if="item.author" class="text-[10px] text-slate-500 font-sans">
                        • {{ item.author }}
                      </span>
                    </div>

                    <Link
                      :href="`/admin/theravada/videos/${item.id}`"
                      class="font-bold text-white text-sm hover:text-phantom-mint transition-colors line-clamp-1 mt-1 block"
                      :title="item.title"
                    >
                      {{ item.seo_title || item.title }}
                    </Link>

                    <div v-if="item.pali_title" class="text-[11px] font-serif italic text-amber-200/70 mt-0.5 line-clamp-1">
                      {{ item.pali_title }}
                    </div>

                    <div class="text-[10px] font-mono text-slate-500 mt-1 truncate max-w-[280px]">
                      /phap-thoai/{{ item.slug }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Column 2: Ready Formats Badges -->
              <td class="p-3.5 text-center">
                <div class="flex flex-col items-center justify-center gap-1">
                  <!-- YouTube 16:9 Badge -->
                  <span
                    class="px-2 py-0.5 rounded text-[10px] font-mono font-bold inline-flex items-center gap-1"
                    :class="
                      item.video_long_url
                        ? item.youtube_url
                          ? 'bg-red-500/20 text-red-300 border border-red-500/30'
                          : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30'
                        : 'bg-white/5 text-slate-600'
                    "
                    :title="item.youtube_url ? 'Đã có video & link YouTube' : item.video_long_url ? 'Đã có MP4 16:9 (Chưa up YouTube)' : 'Chưa có 16:9'"
                  >
                    <span>16:9 YT</span>
                    <span v-if="item.youtube_url" class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                  </span>

                  <!-- Reel 9:16 Badge -->
                  <span
                    class="px-2 py-0.5 rounded text-[10px] font-mono font-bold inline-flex items-center gap-1"
                    :class="item.video_short_url ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-white/5 text-slate-600'"
                    :title="item.video_short_url ? 'Đã có video Reel 9:16' : 'Chưa có reel 9:16'"
                  >
                    <span>9:16 Reel</span>
                  </span>
                </div>
              </td>

              <!-- Column 3: Video Status Badge -->
              <td class="p-3.5 text-center">
                <span
                  v-if="item.video_status === 'published'"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-phantom-mint/15 text-phantom-mint border border-phantom-mint/30"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-phantom-mint"></span>
                  <span>Đã Xuất Bản</span>
                </span>

                <span
                  v-else-if="item.video_status === 'completed'"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                  <span>Đã Hoàn Thành</span>
                </span>

                <span
                  v-else-if="item.video_status === 'processing'"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 animate-pulse"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                  <span>Đang Render</span>
                </span>

                <span
                  v-else-if="item.video_status === 'failed'"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                  <span>Thất Bại</span>
                </span>

                <span
                  v-else
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono text-slate-400 bg-white/5 border border-white/5"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                  <span>Bản Nháp</span>
                </span>
              </td>

              <!-- Column 4: Durations -->
              <td class="p-3.5 text-center font-mono text-[11px] text-slate-300">
                <div v-if="item.video_long_duration || item.video_short_duration" class="space-y-0.5">
                  <div v-if="item.video_long_duration" class="text-indigo-300 font-bold">
                    16:9: {{ formatDuration(item.video_long_duration) }}
                  </div>
                  <div v-if="item.video_short_duration" class="text-purple-300">
                    9:16: {{ formatDuration(item.video_short_duration) }}
                  </div>
                </div>
                <span v-else class="text-slate-600">—</span>
              </td>

              <!-- Column 5: ⚡ Copy Nhanh Menu Popover -->
              <td class="p-3.5 text-center relative">
                <div class="inline-block text-left">
                  <button
                    type="button"
                    class="px-2.5 py-1 rounded-lg bg-amber-500/10 hover:bg-amber-500 hover:text-midnight-950 text-amber-300 text-xs font-mono font-bold transition-all border border-amber-500/30 flex items-center gap-1 mx-auto shadow-sm"
                    @click="(e) => toggleQuickCopy(item.id, e)"
                    title="Mở menu sao chép siêu tốc các trường xuất bản"
                  >
                    <span>⚡ Copy Nhanh</span>
                    <Icons name="ChevronDown" :size="12" />
                  </button>

                  <!-- Popover Menu Dropdown -->
                  <div
                    v-if="activeQuickCopyId === item.id"
                    class="absolute right-0 sm:right-auto sm:left-1/2 sm:-translate-x-1/2 mt-1.5 w-64 rounded-xl bg-midnight-900 border border-white/15 p-2 shadow-2xl z-50 text-left space-y-1 backdrop-blur-xl animate-in fade-in zoom-in-95 duration-150"
                    @click.stop
                  >
                    <div class="px-2 py-1 text-[10px] font-mono text-slate-400 border-b border-white/10 uppercase tracking-wider font-bold">
                      Sao chép xuất bản (#{{ item.id }})
                    </div>

                    <!-- Copy Tiêu đề SEO -->
                    <button
                      type="button"
                      class="w-full px-2.5 py-1.5 rounded-lg text-xs font-sans text-slate-200 hover:bg-white/10 flex items-center justify-between text-left transition-colors"
                      @click="handleCopyText(item.seo_title || item.title, `title-${item.id}`)"
                    >
                      <span class="truncate pr-2">📋 Tiêu đề SEO</span>
                      <span class="font-mono text-[10px] shrink-0" :class="copySuccess[`title-${item.id}`] ? 'text-phantom-mint font-bold' : 'text-slate-400'">
                        {{ copySuccess[`title-${item.id}`] ? '✓ Đã chép' : 'Chép' }}
                      </span>
                    </button>

                    <!-- Copy Mô tả YouTube -->
                    <button
                      type="button"
                      class="w-full px-2.5 py-1.5 rounded-lg text-xs font-sans text-slate-200 hover:bg-white/10 flex items-center justify-between text-left transition-colors"
                      @click="handleCopyText(item.seo_description || item.excerpt || '', `desc-${item.id}`)"
                    >
                      <span class="truncate pr-2">📝 Mô tả YouTube</span>
                      <span class="font-mono text-[10px] shrink-0" :class="copySuccess[`desc-${item.id}`] ? 'text-phantom-mint font-bold' : 'text-slate-400'">
                        {{ copySuccess[`desc-${item.id}`] ? '✓ Đã chép' : 'Chép' }}
                      </span>
                    </button>

                    <!-- Copy Caption Reel/TikTok -->
                    <button
                      type="button"
                      class="w-full px-2.5 py-1.5 rounded-lg text-xs font-sans text-slate-200 hover:bg-white/10 flex items-center justify-between text-left transition-colors"
                      @click="handleCopyText(item.social_caption || '', `caption-${item.id}`)"
                    >
                      <span class="truncate pr-2">📱 Caption Reel/TikTok</span>
                      <span class="font-mono text-[10px] shrink-0" :class="copySuccess[`caption-${item.id}`] ? 'text-phantom-mint font-bold' : 'text-slate-400'">
                        {{ copySuccess[`caption-${item.id}`] ? '✓ Đã chép' : 'Chép' }}
                      </span>
                    </button>

                    <!-- Copy Hashtags -->
                    <button
                      type="button"
                      class="w-full px-2.5 py-1.5 rounded-lg text-xs font-sans text-slate-200 hover:bg-white/10 flex items-center justify-between text-left transition-colors"
                      @click="handleCopyText(getArticleHashtagsFormatted(item), `tags-${item.id}`)"
                    >
                      <span class="truncate pr-2">🏷️ Bộ Hashtags</span>
                      <span class="font-mono text-[10px] shrink-0" :class="copySuccess[`tags-${item.id}`] ? 'text-phantom-mint font-bold' : 'text-slate-400'">
                        {{ copySuccess[`tags-${item.id}`] ? '✓ Đã chép' : 'Chép' }}
                      </span>
                    </button>

                    <!-- Master Copy: Toàn bộ Kit -->
                    <div class="pt-1 border-t border-white/10">
                      <button
                        type="button"
                        class="w-full px-2.5 py-1.5 rounded-lg text-xs font-mono font-bold text-phantom-mint bg-phantom-mint/10 hover:bg-phantom-mint hover:text-midnight-950 flex items-center justify-center gap-1.5 transition-colors"
                        @click="copyFullKit(item)"
                      >
                        <Icons name="Copy" :size="12" />
                        <span>{{ copySuccess[`full-${item.id}`] ? '✓ Đã chép Trọn Gói!' : '⚡ Chép Trọn Gói Phát Hành' }}</span>
                      </button>
                    </div>
                  </div>
                </div>
              </td>

              <!-- Column 6: Actions -->
              <td class="p-3.5 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <Link
                    :href="`/admin/theravada/videos/${item.id}`"
                    class="px-2.5 py-1 rounded-lg bg-phantom-mint/10 hover:bg-phantom-mint hover:text-midnight-950 text-phantom-mint text-xs font-mono font-bold transition-all border border-phantom-mint/20"
                    title="Mở Trạm Phát Hành chi tiết"
                  >
                    Chi Tiết
                  </Link>

                  <button
                    v-if="item.video_status === 'draft' || item.video_status === 'failed'"
                    type="button"
                    class="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-amber-500 hover:text-midnight-950 text-amber-300 text-xs font-mono transition-all border border-white/10"
                    :disabled="isTriggering === item.id"
                    @click="triggerPipeline(item)"
                  >
                    {{ isTriggering === item.id ? 'Đang gọi...' : 'Tạo Video' }}
                  </button>

                  <button
                    v-if="item.video_status === 'completed' || item.video_status === 'published'"
                    type="button"
                    class="px-2.5 py-1 rounded-lg text-xs font-mono transition-all border"
                    :class="
                      item.video_status === 'published'
                        ? 'bg-white/5 hover:bg-slate-700 text-slate-300 border-white/10'
                        : 'bg-emerald-500/10 hover:bg-emerald-500 hover:text-white text-emerald-400 border-emerald-500/20'
                    "
                    @click="togglePublish(item)"
                  >
                    {{ item.video_status === 'published' ? 'Gỡ' : 'Xuất Bản' }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Links -->
      <div v-if="articles.last_page > 1" class="p-4 border-t border-white/5 flex items-center justify-between">
        <div class="text-[11px] font-mono text-slate-400">
          Hiển thị trang {{ articles.current_page }} / {{ articles.last_page }} (Tổng cộng {{ articles.total }} bài pháp)
        </div>
        <div class="flex items-center gap-1">
          <template v-for="(link, i) in articles.links" :key="i">
            <Link
              v-if="link.url"
              :href="link.url"
              class="px-2.5 py-1 rounded-lg text-xs font-mono transition-all"
              :class="
                link.active
                  ? 'bg-phantom-mint text-midnight-950 font-bold'
                  : 'bg-white/5 text-slate-400 hover:text-white hover:bg-white/10'
              "
              v-html="link.label"
            />
            <span v-else class="px-2.5 py-1 text-xs font-mono text-slate-600" v-html="link.label" />
          </template>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
