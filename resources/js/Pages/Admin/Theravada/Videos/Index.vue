<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
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

// View Mode State persisted in localStorage
const viewMode = ref<'grid' | 'table'>('grid');

const currentStatus = ref(props.filters.status || 'all');
const searchQuery = ref(props.filters.search || '');
const isTriggering = ref<number | null>(null);

// Quick Copy State
const activeQuickCopyId = ref<number | null>(null);
const copySuccess = ref<Record<string, boolean>>({});

// Multi-tier thumbnail fallback tracking
const thumbErrors = ref<Record<number, boolean>>({});

const filterTabs = [
  { key: 'all', label: 'Tất cả' },
  { key: 'completed', label: 'Đã hoàn thành' },
  { key: 'published', label: 'Đã xuất bản' },
  { key: 'processing', label: 'Đang xử lý' },
  { key: 'draft', label: 'Bản nháp' },
];

onMounted(() => {
  try {
    const saved = localStorage.getItem('theravada_videos_view_mode');
    if (saved === 'grid' || saved === 'table') {
      viewMode.value = saved;
    }
  } catch {
    // Graceful fallback for non-storage environments
  }
  window.addEventListener('click', closeQuickCopy);
});

onUnmounted(() => {
  window.removeEventListener('click', closeQuickCopy);
});

const setViewMode = (mode: 'grid' | 'table') => {
  viewMode.value = mode;
  try {
    localStorage.setItem('theravada_videos_view_mode', mode);
  } catch {
    // Ignore storage write issues
  }
};

const getYouTubeId = (item: ArticleItem): string | null => {
  if (item.youtube_id) return item.youtube_id;
  if (!item.youtube_url) return null;
  const url = item.youtube_url.trim();
  if (/^[a-zA-Z0-9_-]{11}$/.test(url)) return url;
  const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
  return match ? match[1] : null;
};

// Multi-tier thumbnail error handling (CDN -> YouTube -> Zen gradient poster card)
const handleThumbError = (event: Event, item: ArticleItem) => {
  const img = event.target as HTMLImageElement;
  const ytId = getYouTubeId(item);
  if (ytId && !img.src.includes('img.youtube.com')) {
    img.src = `https://img.youtube.com/vi/${ytId}/mqdefault.jpg`;
  } else {
    thumbErrors.value[item.id] = true;
  }
};

const getStatusLabel = (status: string) => {
  switch (status) {
    case 'published':
      return 'Đã xuất bản';
    case 'completed':
      return 'Đã hoàn thành';
    case 'processing':
      return 'Đang xử lý';
    case 'failed':
      return 'Thất bại';
    case 'draft':
    default:
      return 'Bản nháp';
  }
};

const getStatusDotClass = (status: string) => {
  switch (status) {
    case 'published':
      return 'bg-emerald-400';
    case 'completed':
      return 'bg-teal-400';
    case 'processing':
      return 'bg-amber-400 animate-pulse';
    case 'failed':
      return 'bg-rose-400';
    default:
      return 'bg-slate-500';
  }
};

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
  if (confirm(`Kích hoạt quy trình sản xuất video cho bài viết:\n"${article.title}"?`)) {
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
    `=== TIÊU ĐỀ YOUTUBE ===`,
    title,
    ``,
    `=== MÔ TẢ YOUTUBE (SEO & TIMESTAMPS) ===`,
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
</script>

<template>
  <AdminLayout title="Trạm Quản Lý Video & Phát Hành">
    <Head title="Quản Lý Video & Phát Hành — Admin" />

    <div class="space-y-6 pb-16 max-w-[1600px] mx-auto">
      <!-- Page Header: Minimalist SaaS -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-white/[0.06]">
        <div>
          <div class="flex items-center gap-2 text-xs font-mono text-slate-400">
            <span>Theravāda OS</span>
            <span>/</span>
            <span class="text-slate-300">Video Publishing Hub</span>
          </div>
          <h1 class="text-xl sm:text-2xl font-semibold text-slate-100 tracking-tight mt-1">
            Quản Lý Video & Trạm Phát Hành Đa Kênh
          </h1>
          <p class="text-xs sm:text-sm text-slate-400 font-sans mt-0.5">
            Quản trị, xem trước và đồng bộ video 16:9 YouTube, 9:16 Shorts/Reels, Thumbnails và bộ Metadata SEO.
          </p>
        </div>

        <div class="flex items-center gap-2.5">
          <a
            href="https://theravada.macatung.dev"
            target="_blank"
            class="px-3.5 py-2 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white text-xs sm:text-sm font-medium transition-all flex items-center gap-1.5 border border-white/[0.08]"
          >
            <Icons name="ExternalLink" :size="14" />
            <span>Mở Cổng Theravāda</span>
          </a>
        </div>
      </div>

      <!-- Linear Metric Cards Ribbon -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Metric 1: Total -->
        <div
          class="p-4 rounded-xl bg-slate-900/40 border border-white/[0.06] hover:border-white/[0.12] transition-colors cursor-pointer flex items-center justify-between shadow-sm"
          @click="applyFilter('all')"
        >
          <div>
            <div class="text-xs font-medium text-slate-400">Tổng Bài Pháp</div>
            <div class="text-2xl font-semibold text-slate-100 mt-1 font-mono">{{ statusCounts.all }}</div>
          </div>
          <div class="w-8 h-8 rounded-lg bg-white/[0.04] flex items-center justify-center text-slate-400 border border-white/[0.06]">
            <Icons name="FileText" :size="16" />
          </div>
        </div>

        <!-- Metric 2: Completed -->
        <div
          class="p-4 rounded-xl bg-slate-900/40 border border-white/[0.06] hover:border-white/[0.12] transition-colors cursor-pointer flex items-center justify-between shadow-sm"
          @click="applyFilter('completed')"
        >
          <div>
            <div class="text-xs font-medium text-slate-400">Đã Hoàn Thành</div>
            <div class="text-2xl font-semibold text-teal-400 mt-1 font-mono">{{ statusCounts.completed }}</div>
          </div>
          <div class="w-8 h-8 rounded-lg bg-white/[0.04] flex items-center justify-center text-teal-400 border border-white/[0.06]">
            <Icons name="Check" :size="16" />
          </div>
        </div>

        <!-- Metric 3: Published -->
        <div
          class="p-4 rounded-xl bg-slate-900/40 border border-white/[0.06] hover:border-white/[0.12] transition-colors cursor-pointer flex items-center justify-between shadow-sm"
          @click="applyFilter('published')"
        >
          <div>
            <div class="text-xs font-medium text-slate-400">Đã Xuất Bản Live</div>
            <div class="text-2xl font-semibold text-emerald-400 mt-1 font-mono">{{ statusCounts.published }}</div>
          </div>
          <div class="w-8 h-8 rounded-lg bg-white/[0.04] flex items-center justify-center text-emerald-400 border border-white/[0.06]">
            <Icons name="Play" :size="16" />
          </div>
        </div>

        <!-- Metric 4: Processing -->
        <div
          class="p-4 rounded-xl bg-slate-900/40 border border-white/[0.06] hover:border-white/[0.12] transition-colors cursor-pointer flex items-center justify-between shadow-sm"
          @click="applyFilter('processing')"
        >
          <div>
            <div class="text-xs font-medium text-slate-400">Đang Xử Lý</div>
            <div class="text-2xl font-semibold text-amber-400 mt-1 font-mono flex items-center gap-2">
              <span>{{ statusCounts.processing }}</span>
              <span v-if="statusCounts.processing > 0" class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            </div>
          </div>
          <div class="w-8 h-8 rounded-lg bg-white/[0.04] flex items-center justify-center text-amber-400 border border-white/[0.06]">
            <Icons name="Clock" :size="16" />
          </div>
        </div>
      </div>

      <!-- Controls Bar: Segmented Tabs + Search + View Switcher -->
      <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-1 p-1 rounded-xl bg-black/40 border border-white/[0.06] overflow-x-auto no-scrollbar text-xs">
          <button
            v-for="tab in filterTabs"
            :key="tab.key"
            type="button"
            class="px-3 py-1.5 rounded-lg transition-all whitespace-nowrap flex items-center gap-1.5 font-medium shrink-0"
            :class="
              currentStatus === tab.key
                ? 'bg-white/10 text-white font-semibold shadow-sm'
                : 'text-slate-400 hover:text-slate-200'
            "
            @click="applyFilter(tab.key)"
          >
            <span>{{ tab.label }}</span>
            <span class="text-[11px] font-mono text-slate-400">
              {{ (statusCounts as Record<string, number>)[tab.key] || 0 }}
            </span>
          </button>
        </div>

        <!-- Right Controls: Search + View Mode Switcher -->
        <div class="flex items-center gap-2.5">
          <!-- Search Input -->
          <div class="relative flex-1 sm:w-72">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm theo tiêu đề, Pāḷi hoặc slug..."
              class="w-full pl-8 pr-8 py-1.5 rounded-lg bg-black/40 border border-white/[0.08] text-xs text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-white/20 transition-all font-sans"
              @keyup.enter="handleSearch"
            />
            <span class="absolute left-2.5 top-2 text-slate-500">
              <Icons name="Search" :size="13" />
            </span>
            <button
              v-if="searchQuery"
              type="button"
              class="absolute right-2.5 top-2 text-slate-500 hover:text-slate-300"
              @click="clearSearch"
            >
              <Icons name="X" :size="13" />
            </button>
          </div>

          <!-- View Mode Toggle -->
          <div class="flex items-center gap-0.5 p-1 rounded-lg bg-black/40 border border-white/[0.06] text-xs">
            <button
              type="button"
              class="p-1.5 rounded transition-colors"
              :class="viewMode === 'grid' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-500 hover:text-slate-300'"
              title="Chế độ lưới (Cards View)"
              @click="setViewMode('grid')"
            >
              <Icons name="Layout" :size="14" />
            </button>
            <button
              type="button"
              class="p-1.5 rounded transition-colors"
              :class="viewMode === 'table' ? 'bg-white/10 text-white shadow-sm' : 'text-slate-500 hover:text-slate-300'"
              title="Chế độ bảng (Table View)"
              @click="setViewMode('table')"
            >
              <Icons name="Menu" :size="14" />
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="articles.data.length === 0"
        class="p-12 text-center rounded-xl bg-slate-900/30 border border-white/[0.06] space-y-3"
      >
        <Icons name="FileText" :size="36" class="mx-auto text-slate-600" />
        <div class="text-sm font-medium text-slate-300">Không tìm thấy bài viết hoặc video phù hợp</div>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">
          Thử thay đổi từ khóa tìm kiếm hoặc bấm nút bên dưới để xem toàn bộ danh sách.
        </p>
        <button
          type="button"
          class="px-4 py-1.5 rounded-lg bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/[0.08] text-xs font-medium transition-colors"
          @click="applyFilter('all')"
        >
          Đặt lại bộ lọc
        </button>
      </div>

      <!-- ======================================================== -->
      <!-- VIEW 1: REFINED GRID / CARDS VIEW                        -->
      <!-- ======================================================== -->
      <div
        v-else-if="viewMode === 'grid'"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5"
      >
        <div
          v-for="item in articles.data"
          :key="item.id"
          class="rounded-xl bg-slate-900/40 border border-white/[0.06] overflow-hidden flex flex-col justify-between hover:border-white/[0.15] transition-all shadow-sm group"
        >
          <!-- Thumbnail Frame -->
          <div class="relative aspect-video bg-black overflow-hidden group/thumb">
            <img
              v-if="!thumbErrors[item.id] && item.thumbnail_long_url"
              :src="item.thumbnail_long_url"
              :alt="item.title"
              class="w-full h-full object-cover group-hover/thumb:scale-105 transition-transform duration-300"
              @error="(e) => handleThumbError(e, item)"
            />
            <div
              v-else
              class="w-full h-full p-4 flex flex-col justify-center items-center bg-slate-950 text-center relative border border-white/[0.04]"
            >
              <span class="text-amber-400/90 font-serif italic text-xs line-clamp-1">
                {{ item.pali_title || 'Theravāda Dhamma' }}
              </span>
              <span class="text-white font-medium text-xs mt-1 line-clamp-2 px-2">
                {{ item.seo_title || item.title }}
              </span>
            </div>

            <!-- Duration Overlay -->
            <span
              v-if="item.video_long_duration"
              class="absolute bottom-2 right-2 px-1.5 py-0.5 rounded bg-black/80 text-white font-mono text-[11px] shadow border border-white/[0.08]"
            >
              {{ formatDuration(item.video_long_duration) }}
            </span>

            <!-- YouTube Live Dot -->
            <span
              v-if="item.youtube_url || item.youtube_id"
              class="absolute top-2 left-2 px-2 py-0.5 rounded bg-red-600/90 text-white font-mono text-[10px] font-semibold flex items-center gap-1 shadow"
            >
              <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
              <span>YT LIVE</span>
            </span>

            <!-- Status Dot -->
            <span class="absolute top-2 right-2 px-2 py-0.5 rounded-full text-[10px] font-medium bg-black/80 border border-white/[0.08] text-slate-300 flex items-center gap-1.5 shadow">
              <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(item.video_status)"></span>
              <span>{{ getStatusLabel(item.video_status) }}</span>
            </span>
          </div>

          <!-- Card Body -->
          <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
            <div>
              <div class="flex items-center justify-between text-[11px] font-mono text-slate-400">
                <div class="flex items-center gap-1.5">
                  <span class="text-slate-300 font-semibold">#{{ item.id }}</span>
                  <span>•</span>
                  <span>{{ item.category || 'phap-thoai' }}</span>
                </div>
                <div class="flex items-center gap-1">
                  <span
                    class="px-1.5 py-0.5 rounded text-[10px] font-mono"
                    :class="item.video_long_url ? 'bg-white/[0.08] text-slate-200' : 'text-slate-600'"
                  >
                    16:9
                  </span>
                  <span
                    class="px-1.5 py-0.5 rounded text-[10px] font-mono"
                    :class="item.video_short_url ? 'bg-white/[0.08] text-slate-200' : 'text-slate-600'"
                  >
                    9:16
                  </span>
                </div>
              </div>

              <!-- Title Link -->
              <Link
                :href="`/admin/theravada/videos/${item.id}`"
                class="font-medium text-slate-100 text-sm hover:text-white transition-colors line-clamp-2 mt-1.5 leading-snug block"
                :title="item.title"
              >
                {{ item.seo_title || item.title }}
              </Link>

              <!-- Pali Title -->
              <div v-if="item.pali_title" class="text-xs font-serif italic text-amber-400/80 line-clamp-1 mt-0.5">
                {{ item.pali_title }}
              </div>
            </div>

            <!-- Footer: Actions & Quick Copy Popover -->
            <div class="border-t border-white/[0.06] pt-3 flex items-center justify-between gap-2 relative">
              <!-- Quick Copy Button -->
              <div class="relative">
                <button
                  type="button"
                  class="px-2.5 py-1.5 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 text-xs font-medium border border-white/[0.08] flex items-center gap-1 transition-colors"
                  title="Sao chép nhanh thông tin"
                  @click="(e) => toggleQuickCopy(item.id, e)"
                >
                  <Icons name="Copy" :size="12" />
                  <span>Copy</span>
                </button>

                <!-- Clean Popover -->
                <div
                  v-if="activeQuickCopyId === item.id"
                  class="absolute bottom-full mb-1.5 left-0 w-64 rounded-xl bg-slate-900 border border-white/[0.1] p-1.5 shadow-2xl z-50 text-left space-y-0.5 backdrop-blur-md animate-in fade-in zoom-in-95 duration-100"
                  @click.stop
                >
                  <button
                    type="button"
                    class="w-full px-2.5 py-1.5 rounded-lg text-xs text-slate-200 hover:bg-white/[0.06] flex items-center justify-between text-left transition-colors"
                    @click="handleCopyText(item.seo_title || item.title, `title-${item.id}`)"
                  >
                    <span class="truncate pr-2">Tiêu đề SEO</span>
                    <span class="font-mono text-[10px] text-slate-400">
                      {{ copySuccess[`title-${item.id}`] ? '✓ Đã chép' : 'Chép' }}
                    </span>
                  </button>

                  <button
                    type="button"
                    class="w-full px-2.5 py-1.5 rounded-lg text-xs text-slate-200 hover:bg-white/[0.06] flex items-center justify-between text-left transition-colors"
                    @click="handleCopyText(item.seo_description || item.excerpt || '', `desc-${item.id}`)"
                  >
                    <span class="truncate pr-2">Mô tả YouTube</span>
                    <span class="font-mono text-[10px] text-slate-400">
                      {{ copySuccess[`desc-${item.id}`] ? '✓ Đã chép' : 'Chép' }}
                    </span>
                  </button>

                  <button
                    type="button"
                    class="w-full px-2.5 py-1.5 rounded-lg text-xs text-slate-200 hover:bg-white/[0.06] flex items-center justify-between text-left transition-colors"
                    @click="handleCopyText(item.social_caption || '', `caption-${item.id}`)"
                  >
                    <span class="truncate pr-2">Caption Reel</span>
                    <span class="font-mono text-[10px] text-slate-400">
                      {{ copySuccess[`caption-${item.id}`] ? '✓ Đã chép' : 'Chép' }}
                    </span>
                  </button>

                  <button
                    type="button"
                    class="w-full px-2.5 py-1.5 rounded-lg text-xs text-slate-200 hover:bg-white/[0.06] flex items-center justify-between text-left transition-colors"
                    @click="handleCopyText(getArticleHashtagsFormatted(item), `tags-${item.id}`)"
                  >
                    <span class="truncate pr-2">Bộ Hashtags</span>
                    <span class="font-mono text-[10px] text-slate-400">
                      {{ copySuccess[`tags-${item.id}`] ? '✓ Đã chép' : 'Chép' }}
                    </span>
                  </button>

                  <div class="pt-1 border-t border-white/[0.06]">
                    <button
                      type="button"
                      class="w-full px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-100 bg-white/[0.06] hover:bg-white/[0.1] flex items-center justify-center gap-1.5 transition-colors"
                      @click="copyFullKit(item)"
                    >
                      <Icons name="Copy" :size="12" />
                      <span>{{ copySuccess[`full-${item.id}`] ? '✓ Đã chép trọn bộ!' : 'Chép trọn gói phát hành' }}</span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Action Links -->
              <div class="flex items-center gap-1.5">
                <Link
                  :href="`/admin/theravada/videos/${item.id}`"
                  class="px-3 py-1.5 rounded-lg bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 hover:text-white text-xs font-medium transition-colors"
                >
                  Chi Tiết
                </Link>

                <button
                  v-if="item.video_status === 'draft' || item.video_status === 'failed'"
                  type="button"
                  class="px-2.5 py-1.5 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 text-xs font-medium transition-colors"
                  :disabled="isTriggering === item.id"
                  @click="triggerPipeline(item)"
                >
                  {{ isTriggering === item.id ? 'Đang gọi...' : 'Tạo Video' }}
                </button>

                <button
                  v-if="item.video_status === 'completed' || item.video_status === 'published'"
                  type="button"
                  class="px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors"
                  :class="
                    item.video_status === 'published'
                      ? 'text-slate-400 hover:text-slate-200'
                      : 'text-emerald-400 hover:text-emerald-300'
                  "
                  @click="togglePublish(item)"
                >
                  {{ item.video_status === 'published' ? 'Gỡ' : 'Xuất Bản' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- VIEW 2: REFINED DATA TABLE VIEW                          -->
      <!-- ======================================================== -->
      <div
        v-else
        class="rounded-xl bg-slate-900/40 border border-white/[0.06] overflow-hidden text-left shadow-sm"
      >
        <div class="overflow-x-auto no-scrollbar">
          <table class="w-full text-left font-sans text-xs">
            <thead class="bg-black/40 border-b border-white/[0.06] text-slate-400 font-medium uppercase tracking-wider">
              <tr>
                <th class="p-4 min-w-[340px]">Bài Pháp Thoại & Video</th>
                <th class="p-4 text-center min-w-[120px]">Định Dạng</th>
                <th class="p-4 text-center min-w-[130px]">Trạng Thái</th>
                <th class="p-4 text-center min-w-[120px]">Thời Lượng</th>
                <th class="p-4 text-center min-w-[130px]">Sao Chép Nhanh</th>
                <th class="p-4 text-right min-w-[160px]">Thao Tác</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.04] text-slate-300">
              <tr
                v-for="item in articles.data"
                :key="item.id"
                class="hover:bg-white/[0.02] transition-colors group"
              >
                <!-- Column 1: Video & Info -->
                <td class="p-4">
                  <div class="flex items-start gap-3">
                    <div class="relative w-24 aspect-video rounded-lg overflow-hidden bg-black border border-white/[0.08] shrink-0 shadow">
                      <img
                        v-if="!thumbErrors[item.id] && item.thumbnail_long_url"
                        :src="item.thumbnail_long_url"
                        :alt="item.title"
                        class="w-full h-full object-cover"
                        @error="(e) => handleThumbError(e, item)"
                      />
                      <div v-else class="w-full h-full flex items-center justify-center bg-slate-950 text-slate-500">
                        <Icons name="Play" :size="14" />
                      </div>
                      <span
                        v-if="item.video_long_duration"
                        class="absolute bottom-1 right-1 px-1 rounded bg-black/80 text-white font-mono text-[9px]"
                      >
                        {{ formatDuration(item.video_long_duration) }}
                      </span>
                    </div>

                    <div class="min-w-0">
                      <div class="flex items-center gap-1.5 text-[11px] font-mono text-slate-400">
                        <span class="text-slate-300 font-semibold">#{{ item.id }}</span>
                        <span>•</span>
                        <span>{{ item.category || 'phap-thoai' }}</span>
                      </div>

                      <Link
                        :href="`/admin/theravada/videos/${item.id}`"
                        class="font-medium text-slate-200 text-sm hover:text-white transition-colors line-clamp-1 mt-0.5 block"
                      >
                        {{ item.seo_title || item.title }}
                      </Link>

                      <div v-if="item.pali_title" class="text-xs font-serif italic text-amber-400/80 mt-0.5 truncate">
                        {{ item.pali_title }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Column 2: Ready Formats -->
                <td class="p-4 text-center">
                  <div class="flex items-center justify-center gap-1 font-mono text-[11px]">
                    <span
                      class="px-1.5 py-0.5 rounded"
                      :class="item.video_long_url ? 'bg-white/[0.08] text-slate-200' : 'text-slate-600'"
                    >
                      16:9
                    </span>
                    <span
                      class="px-1.5 py-0.5 rounded"
                      :class="item.video_short_url ? 'bg-white/[0.08] text-slate-200' : 'text-slate-600'"
                    >
                      9:16
                    </span>
                  </div>
                </td>

                <!-- Column 3: Status -->
                <td class="p-4 text-center">
                  <span class="inline-flex items-center gap-1.5 text-xs font-medium">
                    <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(item.video_status)"></span>
                    <span>{{ getStatusLabel(item.video_status) }}</span>
                  </span>
                </td>

                <!-- Column 4: Duration -->
                <td class="p-4 text-center font-mono text-xs text-slate-400">
                  {{ formatDuration(item.video_long_duration) }}
                </td>

                <!-- Column 5: Quick Copy -->
                <td class="p-4 text-center">
                  <button
                    type="button"
                    class="px-2.5 py-1 rounded bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 text-xs font-medium border border-white/[0.08] transition-colors"
                    @click="(e) => toggleQuickCopy(item.id, e)"
                  >
                    Copy
                  </button>
                </td>

                <!-- Column 6: Actions -->
                <td class="p-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <Link
                      :href="`/admin/theravada/videos/${item.id}`"
                      class="px-3 py-1 rounded bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 text-xs font-medium transition-colors"
                    >
                      Chi Tiết
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="articles.links && articles.links.length > 3"
        class="flex items-center justify-between text-xs text-slate-400 pt-2"
      >
        <div>
          Trang <span class="text-slate-200">{{ articles.current_page }}</span> / {{ articles.last_page }} (Tổng {{ articles.total }} bài)
        </div>
        <div class="flex items-center gap-1">
          <template v-for="(link, i) in articles.links" :key="i">
            <Link
              v-if="link.url"
              :href="link.url"
              class="px-2.5 py-1 rounded transition-colors"
              :class="link.active ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:text-slate-200'"
              v-html="link.label"
            />
          </template>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
