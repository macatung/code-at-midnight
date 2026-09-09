<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icons from '@/Components/ui/Icons.vue';

interface ArticleDetail {
  id: number;
  title: string;
  pali_title?: string | null;
  slug: string;
  category?: string | null;
  author?: string | null;
  excerpt?: string | null;
  content: string;
  tags?: string[];
  pali_terms?: string[] | Record<string, string>;
  video_status: 'draft' | 'processing' | 'completed' | 'published' | 'failed' | string;
  video_long_url?: string | null;
  video_short_url?: string | null;
  youtube_url?: string | null;
  youtube_id?: string | null;
  thumbnail_long_url?: string | null;
  thumbnail_short_url?: string | null;
  video_long_duration?: string | null;
  video_short_duration?: string | null;
  script_long?: string | null;
  script_short?: string | null;
  seo_title?: string | null;
  seo_description?: string | null;
  social_caption?: string | null;
  hashtags?: string[] | null;
  has_video?: boolean;
  pipeline_task_id?: string | null;
  pipeline_started_at?: string | null;
  pipeline_completed_at?: string | null;
  pipeline_error?: string | null;
  reading_time_min: number;
  is_published: boolean;
  published_at?: string | null;
  updated_at: string;
}

const props = defineProps<{
  article: ArticleDetail;
}>();

// Media Canvas Mode: 'player_16_9' | 'player_9_16' | 'thumb_16_9' | 'thumb_9_16'
const mediaView = ref<'player_16_9' | 'player_9_16' | 'thumb_16_9' | 'thumb_9_16'>('player_16_9');

// 16:9 Player Source: 'youtube' vs 'mp4'
const playerMode = ref<'youtube' | 'mp4'>(props.article.youtube_id ? 'youtube' : 'mp4');

// Publishing Hub Active Platform Tab
const activeTab = ref<'youtube' | 'tiktok' | 'reels' | 'website'>('youtube');

// Website Script Sub-Tabs: 'long' | 'reel' | 'original'
const activeScriptTab = ref<'long' | 'reel' | 'original'>('long');

// Description Expansion & Fullscreen Zen Modal States
const isDescExpanded = ref(false);
const isDescFullscreen = ref(false);
const isCaptionExpanded = ref(false);

// Form
const form = useForm({
  youtube_url: props.article.youtube_url || '',
  seo_title: props.article.seo_title || props.article.title || '',
  seo_description: props.article.seo_description || props.article.excerpt || '',
  social_caption: props.article.social_caption || '',
  hashtags: Array.isArray(props.article.hashtags)
    ? props.article.hashtags.join(', ')
    : (props.article.hashtags || ''),
});

// Sync form if article changes from external update
watch(
  () => props.article,
  (newArticle) => {
    if (newArticle && !form.isDirty) {
      form.youtube_url = newArticle.youtube_url || '';
      form.seo_title = newArticle.seo_title || newArticle.title || '';
      form.seo_description = newArticle.seo_description || newArticle.excerpt || '';
      form.social_caption = newArticle.social_caption || '';
      form.hashtags = Array.isArray(newArticle.hashtags)
        ? newArticle.hashtags.join(', ')
        : (newArticle.hashtags || '');
    }
  },
  { deep: true }
);

// Pipeline & Copy Feedback
const isTriggering = ref(false);
const copyFeedback = ref<Record<string, boolean>>({});

// Keyboard Shortcut: Cmd/Ctrl + S to save
const handleKeydown = (e: KeyboardEvent) => {
  if ((e.metaKey || e.ctrlKey) && e.key === 's') {
    e.preventDefault();
    handleSaveMetadata();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});

// Live YouTube ID Extraction
const extractedYtId = computed(() => {
  const url = form.youtube_url?.trim();
  if (!url) return null;
  if (/^[a-zA-Z0-9_-]{11}$/.test(url)) return url;
  const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|shorts\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
  return match ? match[1] : null;
});

// YouTube Connection Status
const ytConnectionStatus = computed(() => {
  const url = form.youtube_url?.trim();
  if (!url) {
    return { status: 'idle', label: 'Chưa liên kết', color: 'text-slate-500' };
  }
  if (extractedYtId.value) {
    return { status: 'connected', label: `Đã kết nối: ${extractedYtId.value}`, color: 'text-emerald-400' };
  }
  return { status: 'invalid', label: 'URL không hợp lệ', color: 'text-rose-400' };
});

// 16:9 Thumbnail Fallback
const thumbFailed = ref(false);
const thumbStep = ref<'cdn' | 'yt-max' | 'yt-hq' | 'poster'>('cdn');

const resolvedThumb = computed(() => {
  if (props.article.thumbnail_long_url) return props.article.thumbnail_long_url;
  const ytId = extractedYtId.value || props.article.youtube_id;
  if (ytId) return `https://img.youtube.com/vi/${ytId}/maxresdefault.jpg`;
  return '';
});

const handleThumbError = () => {
  const ytId = extractedYtId.value || props.article.youtube_id;
  if (thumbStep.value === 'cdn' && ytId) {
    thumbStep.value = 'yt-max';
  } else if ((thumbStep.value === 'yt-max' || thumbStep.value === 'cdn') && ytId) {
    thumbStep.value = 'yt-hq';
  } else {
    thumbStep.value = 'poster';
    thumbFailed.value = true;
  }
};

// 9:16 Thumbnail Fallback
const thumbShortFailed = ref(false);
const resolvedThumbShort = computed(() => props.article.thumbnail_short_url || '');

// Copy helper
const copyToClipboard = async (text: string, key: string) => {
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
  copyFeedback.value[key] = true;
  setTimeout(() => {
    copyFeedback.value[key] = false;
  }, 1800);
};

// Save Metadata
const handleSaveMetadata = () => {
  form.put(`/admin/theravada/videos/${props.article.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      if (extractedYtId.value) {
        playerMode.value = 'youtube';
      }
    },
  });
};

// Pipeline Trigger
const triggerPipeline = () => {
  if (isTriggering.value) return;
  if (confirm(`Kích hoạt quy trình sản xuất video (n8n Webhook + GCE Worker) cho bài viết:\n"${props.article.title}"?`)) {
    isTriggering.value = true;
    router.post(
      `/admin/theravada/videos/${props.article.id}/trigger`,
      {},
      {
        preserveScroll: true,
        onFinish: () => {
          isTriggering.value = false;
        },
      }
    );
  }
};

// Toggle Publish
const togglePublish = () => {
  const isPub = props.article.video_status === 'published';
  const actionText = isPub ? 'gỡ xuất bản' : 'xuất bản công khai';
  if (confirm(`Bạn có chắc muốn ${actionText} video này không?`)) {
    router.patch(
      `/admin/theravada/videos/${props.article.id}/publish`,
      {},
      {
        preserveScroll: true,
      }
    );
  }
};

// Smart Computed Fields
const formattedTitle = computed(() => form.seo_title || props.article.title);
const formattedDescription = computed(() => form.seo_description || props.article.excerpt || '');
const formattedCaption = computed(() => form.social_caption || '');

const titleCharCount = computed(() => (form.seo_title || '').length);
const descCharCount = computed(() => (form.seo_description || '').length);
const descWordCount = computed(() => {
  const text = (form.seo_description || '').trim();
  return text ? text.split(/\s+/).length : 0;
});
const captionCharCount = computed(() => (form.social_caption || '').length);
const captionWordCount = computed(() => {
  const text = (form.social_caption || '').trim();
  return text ? text.split(/\s+/).length : 0;
});

// Hashtags parsing
const formattedHashtagsList = computed(() => {
  const raw = form.hashtags;
  if (!raw) return ['#TamAnVanSuAn', '#LoiPhatDay', '#Theravada', '#ThienDinh', '#MaToaThien'];
  const parts = raw.split(',').map((t) => t.trim()).filter(Boolean);
  if (parts.length === 0) return ['#TamAnVanSuAn', '#LoiPhatDay', '#Theravada', '#ThienDinh', '#MaToaThien'];
  return parts.map((t) => (t.startsWith('#') ? t : `#${t}`));
});

const formattedHashtagsString = computed(() => formattedHashtagsList.value.join(' '));

const removeHashtag = (tagToRemove: string) => {
  const raw = form.hashtags || '';
  const parts = raw.split(',').map((t) => t.trim()).filter(Boolean);
  const cleanTag = tagToRemove.replace(/^#+/, '').toLowerCase();
  const filtered = parts.filter((t) => t.replace(/^#+/, '').toLowerCase() !== cleanTag);
  form.hashtags = filtered.join(', ');
};

// Copy Kits
const youtubeFullKit = computed(() => {
  return [
    `=== TIÊU ĐỀ YOUTUBE ===`,
    formattedTitle.value,
    ``,
    `=== MÔ TẢ YOUTUBE (SEO & TIMESTAMPS) ===`,
    formattedDescription.value,
    ``,
    `🎧 Nghe trọn bộ tuyển tập Pháp Thoại tại: https://theravada.macatung.dev`,
    `🔔 Đăng ký kênh Ma Tọa Thiền để đón nhận nguồn năng lượng bình an mỗi ngày.`,
    ``,
    `=== TỪ KHÓA / HASHTAGS ===`,
    formattedHashtagsString.value,
    ``,
    `=== BÀI VIẾT NGUYÊN BẢN ===`,
    `https://theravada.macatung.dev/phap-thoai/${props.article.slug}`,
  ].join('\n');
});

const tiktokFullKit = computed(() => {
  return [
    formattedCaption.value || props.article.title,
    ``,
    `🎧 Nghe trọn bài giảng tại link bio!`,
    formattedHashtagsString.value + ' #shorts #reels #xuhuong',
  ].join('\n');
});

const reelsFullKit = computed(() => {
  return [
    formattedCaption.value || props.article.title,
    ``,
    `🌿 Lời Phật dạy cho tâm an giữa vạn biến cuộc đời.`,
    `🎵 Âm thanh: Nhạc thiền 432Hz hòa trộn tiếng chuông chánh niệm`,
    formattedHashtagsString.value,
  ].join('\n');
});

// Embed Code Snippet
const embedCodeSnippet = computed(() => {
  const ytId = extractedYtId.value || props.article.youtube_id;
  if (ytId) {
    return `<div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">\n  <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" src="https://www.youtube-nocookie.com/embed/${ytId}" title="${props.article.title}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>\n</div>`;
  }
  if (props.article.video_long_url) {
    return `<div style="border-radius: 12px; overflow: hidden; max-width: 100%;">\n  <video controls playsinline style="width: 100%; border-radius: 12px;" src="${props.article.video_long_url}" poster="${props.article.thumbnail_long_url || ''}"></video>\n</div>`;
  }
  return `<!-- Video chưa sẵn sàng -->`;
});

const wordCountLong = computed(() => {
  if (!props.article.script_long) return 0;
  return props.article.script_long.trim().split(/\s+/).length;
});

const wordCountReel = computed(() => {
  if (!props.article.script_short) return 0;
  return props.article.script_short.trim().split(/\s+/).length;
});

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

// Status Helpers
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

const getStatusColor = (status: string) => {
  switch (status) {
    case 'published':
      return 'text-emerald-400';
    case 'completed':
      return 'text-teal-400';
    case 'processing':
      return 'text-amber-400';
    case 'failed':
      return 'text-rose-400';
    default:
      return 'text-slate-400';
  }
};

const getStatusDotClass = (status: string) => {
  switch (status) {
    case 'published':
      return 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.5)]';
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
</script>

<template>
  <AdminLayout :title="article.title">
    <Head :title="`${article.title} — Video Studio CMS`" />

    <div class="space-y-6 pb-16 max-w-[1600px] mx-auto">
      <!-- ======================================================== -->
      <!-- REFINED HEADER BAR (MINIMALIST LINEAR SAAS)              -->
      <!-- ======================================================== -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-white/[0.06]">
        <div class="space-y-1.5">
          <!-- Subtle Breadcrumb Path -->
          <div class="flex items-center gap-2 text-xs font-mono text-slate-400 flex-wrap">
            <Link
              href="/admin/theravada/videos"
              class="hover:text-slate-200 flex items-center gap-1 transition-colors"
            >
              <Icons name="ChevronRight" :size="14" class="rotate-180" />
              <span>Trạm Video</span>
            </Link>
            <span>/</span>
            <span class="text-slate-300">#{{ article.id }}</span>
            <span>•</span>
            <span class="flex items-center gap-1.5 font-sans font-medium" :class="getStatusColor(article.video_status)">
              <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(article.video_status)"></span>
              <span>{{ getStatusLabel(article.video_status) }}</span>
            </span>
            <template v-if="article.youtube_id">
              <span>•</span>
              <span class="text-slate-400 font-mono">YT: {{ article.youtube_id }}</span>
            </template>
          </div>

          <!-- Crisp, Elegant H1 -->
          <h1 class="text-xl sm:text-2xl font-semibold text-slate-100 tracking-tight leading-snug line-clamp-1">
            {{ article.seo_title || article.title }}
          </h1>

          <div v-if="article.pali_title" class="text-xs sm:text-sm font-serif italic text-amber-400/80">
            {{ article.pali_title }}
          </div>
        </div>

        <!-- Quiet, Professional Action Buttons -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
          <a
            :href="`https://theravada.macatung.dev/phap-thoai/${article.slug}`"
            target="_blank"
            class="px-3.5 py-2 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white text-xs sm:text-sm font-medium transition-all flex items-center gap-1.5 border border-white/[0.08]"
            title="Mở bài viết công khai"
          >
            <Icons name="ExternalLink" :size="14" />
            <span>Xem Bài Viết</span>
          </a>

          <button
            type="button"
            @click="triggerPipeline"
            :disabled="isTriggering"
            class="px-3.5 py-2 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white text-xs sm:text-sm font-medium transition-all flex items-center gap-1.5 border border-white/[0.08] disabled:opacity-50"
            title="Kích hoạt lại quy trình sản xuất qua n8n"
          >
            <Icons :name="isTriggering ? 'Clock' : 'Zap'" :size="14" />
            <span>{{ isTriggering ? 'Đang gọi...' : 'Tạo Lại (n8n)' }}</span>
          </button>

          <button
            type="button"
            @click="togglePublish"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all flex items-center gap-1.5 shadow-sm"
            :class="
              article.video_status === 'published'
                ? 'bg-white/[0.06] hover:bg-white/[0.1] text-slate-300 border border-white/[0.1]'
                : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950'
            "
          >
            <Icons :name="article.video_status === 'published' ? 'Eye' : 'Play'" :size="14" />
            <span>{{ article.video_status === 'published' ? 'Gỡ Xuất Bản' : 'Xuất Bản Live' }}</span>
          </button>
        </div>
      </div>

      <!-- Pipeline Error Alert Banner -->
      <div
        v-if="article.pipeline_error"
        class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs sm:text-sm font-mono flex items-start gap-3"
      >
        <Icons name="Bug" :size="16" class="text-rose-400 shrink-0 mt-0.5" />
        <div class="space-y-1">
          <div class="font-bold text-rose-200">Lỗi trong tiến trình render:</div>
          <div class="text-rose-300/90 whitespace-pre-wrap leading-relaxed">{{ article.pipeline_error }}</div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- 2-COLUMN STUDIO WORKSPACE (GAP-6 FOR PROPER BREATHING)    -->
      <!-- ======================================================== -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- ======================================================== -->
        <!-- LEFT COLUMN: UNIFIED MEDIA CANVAS (7 COLS)               -->
        <!-- ======================================================== -->
        <div class="lg:col-span-7 space-y-4">
          <div class="p-5 sm:p-6 rounded-2xl bg-slate-900/40 border border-white/[0.08] space-y-4 shadow-sm">
            
            <!-- Media Toolbar: Neutral Segmented Pill Control -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/[0.06] pb-3.5">
              <div class="flex items-center gap-1 p-1 rounded-xl bg-black/40 border border-white/[0.06] text-xs overflow-x-auto no-scrollbar">
                <!-- Tab 1: Video 16:9 -->
                <button
                  type="button"
                  @click="mediaView = 'player_16_9'"
                  class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 font-medium whitespace-nowrap"
                  :class="mediaView === 'player_16_9' ? 'bg-white/10 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-200'"
                >
                  <Icons name="Play" :size="13" />
                  <span>Video 16:9</span>
                </button>

                <!-- Tab 2: Reel 9:16 -->
                <button
                  type="button"
                  @click="mediaView = 'player_9_16'"
                  class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 font-medium whitespace-nowrap"
                  :class="mediaView === 'player_9_16' ? 'bg-white/10 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-200'"
                >
                  <Icons name="Film" :size="13" />
                  <span>Reel 9:16</span>
                </button>

                <!-- Tab 3: Bìa 16:9 -->
                <button
                  type="button"
                  @click="mediaView = 'thumb_16_9'"
                  class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 font-medium whitespace-nowrap"
                  :class="mediaView === 'thumb_16_9' ? 'bg-white/10 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-200'"
                >
                  <Icons name="Sparkles" :size="13" />
                  <span>Bìa 16:9</span>
                </button>

                <!-- Tab 4: Bìa 9:16 -->
                <button
                  type="button"
                  @click="mediaView = 'thumb_9_16'"
                  class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 font-medium whitespace-nowrap"
                  :class="mediaView === 'thumb_9_16' ? 'bg-white/10 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-200'"
                >
                  <Icons name="Sparkles" :size="13" />
                  <span>Bìa 9:16</span>
                </button>
              </div>

              <!-- Sleek Sub-Actions Cluster -->
              <div class="flex items-center gap-2 text-xs text-slate-400 shrink-0">
                <!-- If 16:9 Player: Switch between YouTube and MP4 -->
                <template v-if="mediaView === 'player_16_9'">
                  <div class="flex items-center gap-1 bg-black/40 p-1 rounded-lg border border-white/[0.06]">
                    <button
                      type="button"
                      @click="playerMode = 'youtube'"
                      :disabled="!extractedYtId && !article.youtube_id"
                      class="px-2.5 py-1 rounded text-xs transition-colors flex items-center gap-1"
                      :class="playerMode === 'youtube' ? 'bg-white/10 text-white font-medium' : 'text-slate-400 hover:text-slate-200'"
                      :title="!extractedYtId && !article.youtube_id ? 'Chưa có liên kết YouTube' : ''"
                    >
                      <span>YouTube</span>
                    </button>
                    <button
                      type="button"
                      @click="playerMode = 'mp4'"
                      class="px-2.5 py-1 rounded text-xs transition-colors flex items-center gap-1"
                      :class="playerMode === 'mp4' ? 'bg-white/10 text-white font-medium' : 'text-slate-400 hover:text-slate-200'"
                    >
                      <span>MP4 Gốc</span>
                    </button>
                  </div>
                </template>

                <!-- If 9:16 Reel Player -->
                <template v-else-if="mediaView === 'player_9_16'">
                  <span class="font-mono text-xs text-slate-400">
                    {{ formatDuration(article.video_short_duration) }}
                  </span>
                  <a
                    v-if="article.video_short_url"
                    :href="article.video_short_url"
                    target="_blank"
                    class="px-2.5 py-1 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white transition-colors border border-white/[0.08] flex items-center gap-1 text-xs"
                    title="Tải tệp video Reel 9:16"
                  >
                    <Icons name="ExternalLink" :size="12" />
                    <span>Tải MP4</span>
                  </a>
                </template>

                <!-- If 16:9 Thumbnail -->
                <template v-else-if="mediaView === 'thumb_16_9'">
                  <a
                    v-if="resolvedThumb"
                    :href="resolvedThumb"
                    target="_blank"
                    class="px-2.5 py-1 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white transition-colors border border-white/[0.08] flex items-center gap-1 text-xs"
                  >
                    <Icons name="ExternalLink" :size="12" />
                    <span>Mở ảnh</span>
                  </a>
                  <button
                    v-if="resolvedThumb"
                    type="button"
                    @click="copyToClipboard(resolvedThumb, 'cdn-thumb')"
                    class="hover:text-white font-mono text-xs transition-colors"
                  >
                    {{ copyFeedback['cdn-thumb'] ? '✓ Đã chép' : 'Copy CDN' }}
                  </button>
                </template>

                <!-- If 9:16 Thumbnail -->
                <template v-else-if="mediaView === 'thumb_9_16'">
                  <a
                    v-if="resolvedThumbShort"
                    :href="resolvedThumbShort"
                    target="_blank"
                    class="px-2.5 py-1 rounded-lg bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 hover:text-white transition-colors border border-white/[0.08] flex items-center gap-1 text-xs"
                  >
                    <Icons name="ExternalLink" :size="12" />
                    <span>Mở ảnh</span>
                  </a>
                  <button
                    v-if="resolvedThumbShort"
                    type="button"
                    @click="copyToClipboard(resolvedThumbShort, 'cdn-thumb-short')"
                    class="hover:text-white font-mono text-xs transition-colors"
                  >
                    {{ copyFeedback['cdn-thumb-short'] ? '✓ Đã chép' : 'Copy CDN' }}
                  </button>
                </template>
              </div>
            </div>

            <!-- Dynamic Media Viewport Container -->
            <div>
              <!-- 1. MODE: VIDEO 16:9 PLAYER -->
              <div
                v-if="mediaView === 'player_16_9'"
                class="aspect-video rounded-xl overflow-hidden bg-black border border-white/[0.08] relative shadow-md flex items-center justify-center"
              >
                <iframe
                  v-if="playerMode === 'youtube' && (extractedYtId || article.youtube_id)"
                  :src="`https://www.youtube-nocookie.com/embed/${extractedYtId || article.youtube_id}?autoplay=0&rel=0`"
                  class="w-full h-full border-0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                  allowfullscreen
                />
                <video
                  v-else-if="article.video_long_url"
                  controls
                  playsinline
                  class="w-full h-full object-contain"
                  :src="article.video_long_url"
                  :poster="resolvedThumb || undefined"
                />
                <div v-else class="text-center p-6 text-slate-400 font-sans text-xs sm:text-sm space-y-2">
                  <Icons name="Play" :size="32" class="mx-auto text-slate-600" />
                  <div class="text-slate-300 font-medium">Video dài 16:9 chưa sẵn sàng</div>
                  <button
                    type="button"
                    @click="triggerPipeline"
                    class="px-3.5 py-1.5 rounded-lg bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/[0.08] text-xs font-medium transition-colors"
                  >
                    Tạo video qua n8n
                  </button>
                </div>
              </div>

              <!-- 2. MODE: REEL 9:16 SMARTPHONE PLAYER -->
              <div
                v-else-if="mediaView === 'player_9_16'"
                class="rounded-xl bg-black/40 border border-white/[0.08] p-4 sm:p-6 flex flex-col items-center justify-center min-h-[460px] sm:min-h-[500px] relative"
              >
                <!-- Smartphone Frame Mockup -->
                <div class="w-full max-w-[260px] sm:max-w-[280px] aspect-[9/16] rounded-[2rem] overflow-hidden border-2 border-slate-700/80 bg-black relative shadow-2xl flex items-center justify-center">
                  <!-- Notch / Dynamic Island -->
                  <div class="absolute top-2 left-1/2 -translate-x-1/2 w-16 h-3 bg-slate-900 rounded-full z-20 border border-white/[0.08] flex items-center justify-center pointer-events-none">
                    <div class="w-1.5 h-1.5 rounded-full bg-slate-800"></div>
                  </div>

                  <video
                    v-if="article.video_short_url"
                    controls
                    playsinline
                    class="w-full h-full object-cover"
                    :src="article.video_short_url"
                    :poster="article.thumbnail_short_url || undefined"
                  />

                  <div v-else class="text-center p-5 text-slate-400 font-sans text-xs space-y-2.5">
                    <div class="w-10 h-10 rounded-full bg-white/[0.04] text-slate-400 flex items-center justify-center mx-auto border border-white/[0.08]">
                      <Icons name="Film" :size="20" />
                    </div>
                    <div class="font-medium text-slate-200">Reel 9:16 chưa tạo</div>
                    <button
                      type="button"
                      @click="triggerPipeline"
                      class="px-3 py-1 rounded-lg bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 text-xs font-medium border border-white/[0.08] transition-colors"
                    >
                      Tạo Reel Ngay
                    </button>
                  </div>
                </div>

                <div class="text-[11px] font-mono text-slate-500 mt-3">
                  Định dạng chuẩn dọc 9:16 (1080x1920)
                </div>
              </div>

              <!-- 3. MODE: THUMBNAIL 16:9 -->
              <div
                v-else-if="mediaView === 'thumb_16_9'"
                class="aspect-video rounded-xl overflow-hidden bg-black border border-white/[0.08] relative shadow-md flex items-center justify-center"
              >
                <img
                  v-if="!thumbFailed && resolvedThumb"
                  :src="resolvedThumb"
                  @error="handleThumbError"
                  alt="Thumbnail 16:9"
                  class="w-full h-full object-cover"
                />
                <!-- Zen Poster Fallback -->
                <div
                  v-else
                  class="w-full h-full p-6 flex flex-col justify-center items-center bg-slate-950 text-center relative select-none"
                >
                  <span class="text-amber-400/90 font-serif italic text-sm">{{ article.pali_title || 'Theravāda Dhamma' }}</span>
                  <h3 class="text-white font-semibold text-base mt-1 max-w-md line-clamp-2">{{ article.title }}</h3>
                  <span class="text-xs font-mono text-slate-500 mt-2">1920x1080 Full HD</span>
                </div>
              </div>

              <!-- 4. MODE: THUMBNAIL 9:16 -->
              <div
                v-else-if="mediaView === 'thumb_9_16'"
                class="rounded-xl bg-black/40 border border-white/[0.08] p-4 sm:p-6 flex flex-col items-center justify-center min-h-[460px] sm:min-h-[500px] relative"
              >
                <div class="w-full max-w-[260px] sm:max-w-[280px] aspect-[9/16] rounded-2xl overflow-hidden border border-white/[0.1] shadow-xl bg-black relative flex items-center justify-center">
                  <img
                    v-if="!thumbShortFailed && resolvedThumbShort"
                    :src="resolvedThumbShort"
                    @error="thumbShortFailed = true"
                    alt="Thumbnail Reel 9:16"
                    class="w-full h-full object-cover"
                  />
                  <div
                    v-else
                    class="w-full h-full p-4 flex flex-col justify-center items-center bg-slate-950 text-center relative select-none"
                  >
                    <span class="text-amber-400/90 font-serif italic text-xs">{{ article.pali_title || 'Theravāda' }}</span>
                    <div class="text-white font-semibold text-xs mt-1 px-2 line-clamp-3">{{ article.title }}</div>
                    <span class="text-[10px] font-mono text-slate-500 mt-2">Bìa Dọc 1080x1920</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- YouTube Studio URL Toolbar (Quiet, Sleek, Unified) -->
            <div class="p-3.5 rounded-xl bg-slate-900/40 border border-white/[0.06] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div class="flex items-center gap-2 text-xs font-medium text-slate-300 shrink-0">
                <Icons name="Play" :size="14" class="text-rose-400" />
                <span>YouTube Studio:</span>
              </div>
              <div class="relative flex-1">
                <input
                  v-model="form.youtube_url"
                  type="text"
                  placeholder="Dán link YouTube (youtu.be/... hoặc watch?v=...)"
                  class="w-full pl-3 pr-3 py-1.5 rounded-lg bg-black/40 border border-white/[0.08] text-xs font-mono text-slate-200 placeholder:text-slate-500 focus:outline-none focus:border-white/20 transition-all"
                />
              </div>
              <div class="flex items-center gap-2 shrink-0">
                <button
                  type="button"
                  @click="handleSaveMetadata"
                  :disabled="form.processing"
                  class="px-3 py-1.5 rounded-lg bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 text-xs font-medium transition-colors border border-white/[0.08] flex items-center gap-1.5"
                >
                  <Icons name="Check" :size="13" />
                  <span>{{ form.processing ? 'Lưu...' : 'Lưu' }}</span>
                </button>
                <span class="text-[11px] font-mono" :class="ytConnectionStatus.color">
                  {{ ytConnectionStatus.label }}
                </span>
              </div>
            </div>

            <!-- Specs Strip -->
            <div class="flex items-center justify-between text-xs font-mono text-slate-400 pt-1 flex-wrap gap-2">
              <div class="flex items-center gap-3">
                <span>16:9: <strong class="text-slate-200">{{ formatDuration(article.video_long_duration) }}</strong></span>
                <span>•</span>
                <span>9:16: <strong class="text-slate-200">{{ formatDuration(article.video_short_duration) }}</strong></span>
              </div>
              <div class="flex items-center gap-3 font-sans">
                <a
                  v-if="article.video_long_url"
                  :href="article.video_long_url"
                  target="_blank"
                  class="hover:text-slate-200 flex items-center gap-1 text-xs"
                >
                  <Icons name="ExternalLink" :size="12" />
                  <span>Tải 16:9</span>
                </a>
                <span v-if="article.video_long_url && article.video_short_url" class="text-slate-600">•</span>
                <a
                  v-if="article.video_short_url"
                  :href="article.video_short_url"
                  target="_blank"
                  class="hover:text-slate-200 flex items-center gap-1 text-xs"
                >
                  <Icons name="ExternalLink" :size="12" />
                  <span>Tải 9:16</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- RIGHT COLUMN: PLATFORM PUBLISHING FORMS (5 COLS)         -->
        <!-- ======================================================== -->
        <div class="lg:col-span-5 space-y-4">
          <div class="p-5 sm:p-6 rounded-2xl bg-slate-900/40 border border-white/[0.08] space-y-4 shadow-sm">
            
            <!-- Platform Tabs: Clean Slate Pill Control -->
            <div class="flex items-center gap-1 p-1 rounded-xl bg-black/40 border border-white/[0.06] overflow-x-auto no-scrollbar text-xs">
              <button
                type="button"
                @click="activeTab = 'youtube'"
                class="px-3 py-1.5 rounded-lg transition-all shrink-0 font-medium"
                :class="activeTab === 'youtube' ? 'bg-white/10 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-200'"
              >
                <span>YouTube Studio</span>
              </button>
              <button
                type="button"
                @click="activeTab = 'tiktok'"
                class="px-3 py-1.5 rounded-lg transition-all shrink-0 font-medium"
                :class="activeTab === 'tiktok' ? 'bg-white/10 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-200'"
              >
                <span>TikTok Hub</span>
              </button>
              <button
                type="button"
                @click="activeTab = 'reels'"
                class="px-3 py-1.5 rounded-lg transition-all shrink-0 font-medium"
                :class="activeTab === 'reels' ? 'bg-white/10 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-200'"
              >
                <span>FB & IG Reels</span>
              </button>
              <button
                type="button"
                @click="activeTab = 'website'"
                class="px-3 py-1.5 rounded-lg transition-all shrink-0 font-medium flex items-center gap-1"
                :class="activeTab === 'website' ? 'bg-white/10 text-white shadow-sm font-semibold' : 'text-slate-400 hover:text-slate-200'"
              >
                <Icons name="Code" :size="12" />
                <span>Website & Kịch Bản</span>
              </button>
            </div>

            <!-- TAB 1: YOUTUBE STUDIO FORM -->
            <div v-if="activeTab === 'youtube'" class="space-y-4">
              <!-- Field Card 1: Tiêu đề YouTube -->
              <div class="p-4 rounded-xl bg-slate-900/30 border border-white/[0.06] space-y-2 focus-within:border-white/[0.15] transition-colors">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-medium text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <Icons name="FileText" :size="13" class="text-slate-400" />
                    <span>Tiêu đề YouTube</span>
                  </span>
                  <div class="flex items-center gap-2">
                    <span
                      class="font-mono text-[11px]"
                      :class="titleCharCount <= 100 ? 'text-slate-400' : 'text-amber-400 font-semibold'"
                    >
                      {{ titleCharCount }}/100 ký tự
                    </span>
                    <button
                      type="button"
                      @click="copyToClipboard(form.seo_title, 'yt-title')"
                      class="text-slate-400 hover:text-slate-200 flex items-center gap-1 transition-colors"
                    >
                      <Icons name="Copy" :size="12" />
                      <span>{{ copyFeedback['yt-title'] ? '✓ Đã chép' : 'Sao chép' }}</span>
                    </button>
                  </div>
                </div>
                <input
                  v-model="form.seo_title"
                  type="text"
                  maxlength="130"
                  class="w-full px-3.5 py-2.5 rounded-lg bg-black/40 border border-white/[0.08] text-slate-100 font-sans text-sm focus:outline-none focus:border-white/20 transition-all placeholder:text-slate-600"
                  placeholder="Tiêu đề chuẩn SEO cho YouTube..."
                />
              </div>

              <!-- Field Card 2: Mô tả YouTube (Expandable & Fullscreen) -->
              <div class="p-4 rounded-xl bg-slate-900/30 border border-white/[0.06] space-y-2 focus-within:border-white/[0.15] transition-colors">
                <div class="flex items-center justify-between text-xs flex-wrap gap-2">
                  <span class="font-medium text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <Icons name="AlignLeft" :size="13" class="text-slate-400" />
                    <span>Mô tả YouTube (SEO & Timestamps)</span>
                  </span>
                  <div class="flex items-center gap-2">
                    <button
                      type="button"
                      @click="isDescExpanded = !isDescExpanded"
                      class="px-2 py-0.5 rounded bg-white/[0.04] hover:bg-white/[0.08] text-slate-400 hover:text-slate-200 transition-colors text-xs flex items-center gap-1"
                    >
                      <Icons :name="isDescExpanded ? 'Minimize2' : 'Maximize2'" :size="11" />
                      <span>{{ isDescExpanded ? 'Thu gọn' : 'Mở rộng' }}</span>
                    </button>
                    <button
                      type="button"
                      @click="isDescFullscreen = true"
                      class="px-2 py-0.5 rounded bg-white/[0.04] hover:bg-white/[0.08] text-slate-400 hover:text-slate-200 transition-colors text-xs flex items-center gap-1"
                    >
                      <Icons name="ExternalLink" :size="11" />
                      <span>Toàn màn hình</span>
                    </button>
                    <button
                      type="button"
                      @click="copyToClipboard(form.seo_description, 'yt-desc')"
                      class="text-slate-400 hover:text-slate-200 flex items-center gap-1 transition-colors"
                    >
                      <Icons name="Copy" :size="12" />
                      <span>{{ copyFeedback['yt-desc'] ? '✓ Đã chép' : 'Sao chép' }}</span>
                    </button>
                  </div>
                </div>

                <textarea
                  v-model="form.seo_description"
                  :rows="isDescExpanded ? 14 : 5"
                  class="w-full px-3.5 py-2.5 rounded-lg bg-black/40 border border-white/[0.08] text-slate-100 font-sans text-sm leading-relaxed focus:outline-none focus:border-white/20 transition-all placeholder:text-slate-600 resize-y"
                  placeholder="Nhập mô tả chi tiết, mốc thời gian, liên kết..."
                ></textarea>

                <div class="flex items-center justify-between text-[11px] font-mono text-slate-500">
                  <span>{{ descWordCount }} từ • {{ descCharCount }} ký tự</span>
                  <span>Kéo góc dưới để mở rộng</span>
                </div>
              </div>

              <!-- Field Card 3: Hashtags -->
              <div class="p-4 rounded-xl bg-slate-900/30 border border-white/[0.06] space-y-2 focus-within:border-white/[0.15] transition-colors">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-medium text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <Icons name="Hash" :size="13" class="text-slate-400" />
                    <span>Hashtags</span>
                  </span>
                  <button
                    type="button"
                    @click="copyToClipboard(formattedHashtagsString, 'yt-tags')"
                    class="text-slate-400 hover:text-slate-200 flex items-center gap-1 transition-colors"
                  >
                    <Icons name="Copy" :size="12" />
                    <span>{{ copyFeedback['yt-tags'] ? '✓ Đã chép' : 'Sao chép' }}</span>
                  </button>
                </div>

                <!-- Tag Chips Preview -->
                <div class="flex flex-wrap gap-1.5 pb-0.5">
                  <span
                    v-for="tag in formattedHashtagsList"
                    :key="tag"
                    class="px-2 py-0.5 rounded-md bg-white/[0.04] text-slate-300 border border-white/[0.06] text-xs font-mono flex items-center gap-1"
                  >
                    <span>{{ tag }}</span>
                    <button
                      type="button"
                      @click="removeHashtag(tag)"
                      class="text-slate-500 hover:text-slate-200 transition-colors"
                    >
                      <Icons name="X" :size="10" />
                    </button>
                  </span>
                </div>

                <input
                  v-model="form.hashtags"
                  type="text"
                  placeholder="Cách nhau bằng dấu phẩy (#TamAn, #Theravada...)"
                  class="w-full px-3.5 py-2 rounded-lg bg-black/40 border border-white/[0.08] text-slate-200 font-mono text-xs focus:outline-none focus:border-white/20 transition-all placeholder:text-slate-600"
                />
              </div>

              <!-- Refined Action Bar -->
              <div class="pt-2 flex items-center gap-3">
                <button
                  type="button"
                  @click="handleSaveMetadata"
                  :disabled="form.processing"
                  class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-white text-slate-950 font-semibold text-xs sm:text-sm flex items-center justify-center gap-2 shadow transition-all disabled:opacity-50"
                >
                  <Icons name="Check" :size="14" />
                  <span>{{ form.processing ? 'Đang lưu...' : (form.recentlySuccessful ? '✓ Đã Lưu!' : 'Lưu Thay Đổi (Cmd+S)') }}</span>
                </button>
                <button
                  type="button"
                  @click="copyToClipboard(youtubeFullKit, 'yt-full')"
                  class="flex-1 py-2.5 px-4 rounded-xl bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/[0.1] font-medium text-xs sm:text-sm flex items-center justify-center gap-2 transition-all"
                >
                  <Icons name="Copy" :size="14" />
                  <span>{{ copyFeedback['yt-full'] ? '✓ Đã chép Kit!' : 'Copy YouTube Kit' }}</span>
                </button>
              </div>
            </div>

            <!-- TAB 2: TIKTOK HUB FORM -->
            <div v-else-if="activeTab === 'tiktok'" class="space-y-4">
              <div class="p-4 rounded-xl bg-slate-900/30 border border-white/[0.06] space-y-2 focus-within:border-white/[0.15] transition-colors">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-medium text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <Icons name="FileText" :size="13" class="text-slate-400" />
                    <span>Caption TikTok</span>
                  </span>
                  <div class="flex items-center gap-2">
                    <button
                      type="button"
                      @click="isCaptionExpanded = !isCaptionExpanded"
                      class="px-2 py-0.5 rounded bg-white/[0.04] hover:bg-white/[0.08] text-slate-400 hover:text-slate-200 transition-colors text-xs"
                    >
                      {{ isCaptionExpanded ? 'Thu gọn' : 'Mở rộng' }}
                    </button>
                    <button
                      type="button"
                      @click="copyToClipboard(form.social_caption, 'tt-caption')"
                      class="text-slate-400 hover:text-slate-200 flex items-center gap-1 transition-colors"
                    >
                      <Icons name="Copy" :size="12" />
                      <span>{{ copyFeedback['tt-caption'] ? '✓ Đã chép' : 'Sao chép' }}</span>
                    </button>
                  </div>
                </div>
                <textarea
                  v-model="form.social_caption"
                  :rows="isCaptionExpanded ? 8 : 3"
                  class="w-full px-3.5 py-2.5 rounded-lg bg-black/40 border border-white/[0.08] text-slate-100 font-sans text-sm leading-relaxed focus:outline-none focus:border-white/20 transition-all placeholder:text-slate-600 resize-y"
                  placeholder="Lời tựa ngắn lôi cuốn cho video TikTok..."
                ></textarea>
                <div class="text-[11px] font-mono text-slate-500">
                  {{ captionWordCount }} từ • {{ captionCharCount }} ký tự
                </div>
              </div>

              <!-- Hashtags Preview -->
              <div class="p-4 rounded-xl bg-slate-900/30 border border-white/[0.06] space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-400">
                  <span class="font-medium uppercase tracking-wider">Hashtags Xu Hướng</span>
                  <button
                    type="button"
                    @click="copyToClipboard(formattedHashtagsString + ' #shorts #reels #xuhuong', 'tt-tags')"
                    class="hover:text-slate-200"
                  >
                    {{ copyFeedback['tt-tags'] ? '✓ Đã chép' : 'Sao chép' }}
                  </button>
                </div>
                <div class="text-xs font-mono text-slate-300 p-2.5 rounded-lg bg-black/40 border border-white/[0.04] leading-relaxed">
                  {{ formattedHashtagsString }} #shorts #reels #xuhuong
                </div>
              </div>

              <!-- Quick Switcher to 9:16 Canvas -->
              <div class="p-3.5 rounded-xl bg-slate-900/30 border border-white/[0.06] flex items-center justify-between">
                <div class="text-xs text-slate-300">
                  <span>Reel 9:16: <strong>{{ formatDuration(article.video_short_duration) }}</strong></span>
                </div>
                <button
                  type="button"
                  @click="mediaView = 'player_9_16'"
                  class="px-3 py-1 rounded-lg bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/[0.08] text-xs font-medium transition-colors flex items-center gap-1.5"
                >
                  <Icons name="Play" :size="12" />
                  <span>Xem trên Canvas</span>
                </button>
              </div>

              <!-- Action Bar -->
              <div class="pt-2 flex items-center gap-3">
                <button
                  type="button"
                  @click="handleSaveMetadata"
                  :disabled="form.processing"
                  class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-white text-slate-950 font-semibold text-xs sm:text-sm flex items-center justify-center gap-2 shadow transition-all disabled:opacity-50"
                >
                  <Icons name="Check" :size="14" />
                  <span>{{ form.processing ? 'Đang lưu...' : 'Lưu Thay Đổi' }}</span>
                </button>
                <button
                  type="button"
                  @click="copyToClipboard(tiktokFullKit, 'tt-full')"
                  class="flex-1 py-2.5 px-4 rounded-xl bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/[0.1] font-medium text-xs sm:text-sm flex items-center justify-center gap-2 transition-all"
                >
                  <Icons name="Copy" :size="14" />
                  <span>{{ copyFeedback['tt-full'] ? '✓ Đã chép Kit!' : 'Copy TikTok Kit' }}</span>
                </button>
              </div>
            </div>

            <!-- TAB 3: FB & IG REELS FORM -->
            <div v-else-if="activeTab === 'reels'" class="space-y-4">
              <div class="p-4 rounded-xl bg-slate-900/30 border border-white/[0.06] space-y-2 focus-within:border-white/[0.15] transition-colors">
                <div class="flex items-center justify-between text-xs">
                  <span class="font-medium text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                    <Icons name="FileText" :size="13" class="text-slate-400" />
                    <span>Caption Reels</span>
                  </span>
                  <button
                    type="button"
                    @click="copyToClipboard(form.social_caption, 'reels-caption')"
                    class="text-slate-400 hover:text-slate-200 flex items-center gap-1 transition-colors"
                  >
                    <Icons name="Copy" :size="12" />
                    <span>{{ copyFeedback['reels-caption'] ? '✓ Đã chép' : 'Sao chép' }}</span>
                  </button>
                </div>
                <textarea
                  v-model="form.social_caption"
                  rows="4"
                  class="w-full px-3.5 py-2.5 rounded-lg bg-black/40 border border-white/[0.08] text-slate-100 font-sans text-sm leading-relaxed focus:outline-none focus:border-white/20 transition-all placeholder:text-slate-600 resize-y"
                  placeholder="Caption Facebook & Instagram Reels..."
                ></textarea>
              </div>

              <div class="p-3.5 rounded-xl bg-slate-900/30 border border-white/[0.06] space-y-1 text-xs text-slate-400">
                <span class="uppercase tracking-wider font-medium text-slate-500">Âm thanh thiền:</span>
                <div class="text-slate-300">Nhạc thiền 432Hz (-22dB) + Tiếng chuông chánh niệm Ma Tọa Thiền.</div>
              </div>

              <div class="p-3.5 rounded-xl bg-slate-900/30 border border-white/[0.06] flex items-center justify-between">
                <div class="text-xs text-slate-300">
                  <span>Reel 9:16: <strong>{{ formatDuration(article.video_short_duration) }}</strong></span>
                </div>
                <button
                  type="button"
                  @click="mediaView = 'player_9_16'"
                  class="px-3 py-1 rounded-lg bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/[0.08] text-xs font-medium transition-colors flex items-center gap-1.5"
                >
                  <Icons name="Play" :size="12" />
                  <span>Xem trên Canvas</span>
                </button>
              </div>

              <!-- Action Bar -->
              <div class="pt-2 flex items-center gap-3">
                <button
                  type="button"
                  @click="handleSaveMetadata"
                  :disabled="form.processing"
                  class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-white text-slate-950 font-semibold text-xs sm:text-sm flex items-center justify-center gap-2 shadow transition-all disabled:opacity-50"
                >
                  <Icons name="Check" :size="14" />
                  <span>{{ form.processing ? 'Đang lưu...' : 'Lưu Thay Đổi' }}</span>
                </button>
                <button
                  type="button"
                  @click="copyToClipboard(reelsFullKit, 'reels-full')"
                  class="flex-1 py-2.5 px-4 rounded-xl bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/[0.1] font-medium text-xs sm:text-sm flex items-center justify-center gap-2 transition-all"
                >
                  <Icons name="Copy" :size="14" />
                  <span>{{ copyFeedback['reels-full'] ? '✓ Đã chép Kit!' : 'Copy Reels Kit' }}</span>
                </button>
              </div>
            </div>

            <!-- TAB 4: WEBSITE & KỊCH BẢN -->
            <div v-else-if="activeTab === 'website'" class="space-y-4">
              <!-- Embed Snippet -->
              <div class="p-4 rounded-xl bg-slate-900/30 border border-white/[0.06] space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-400">
                  <span class="font-medium uppercase tracking-wider">Mã Nhúng Iframe Website</span>
                  <button
                    type="button"
                    @click="copyToClipboard(embedCodeSnippet, 'embed-code')"
                    class="hover:text-slate-200 flex items-center gap-1"
                  >
                    <Icons name="Copy" :size="12" />
                    <span>{{ copyFeedback['embed-code'] ? '✓ Đã chép' : 'Sao chép' }}</span>
                  </button>
                </div>
                <pre class="p-3 rounded-lg bg-black/40 text-xs font-mono text-emerald-400/90 overflow-x-auto border border-white/[0.04] leading-relaxed">{{ embedCodeSnippet }}</pre>
              </div>

              <!-- Script Sub-tabs -->
              <div class="flex items-center justify-between border-b border-white/[0.06] pb-2">
                <div class="flex items-center gap-1 text-xs">
                  <button
                    type="button"
                    @click="activeScriptTab = 'long'"
                    class="px-3 py-1 rounded-lg transition-colors font-medium"
                    :class="activeScriptTab === 'long' ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:text-slate-200'"
                  >
                    16:9 ({{ wordCountLong }} từ)
                  </button>
                  <button
                    type="button"
                    @click="activeScriptTab = 'reel'"
                    class="px-3 py-1 rounded-lg transition-colors font-medium"
                    :class="activeScriptTab === 'reel' ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:text-slate-200'"
                  >
                    9:16 ({{ wordCountReel }} từ)
                  </button>
                  <button
                    type="button"
                    @click="activeScriptTab = 'original'"
                    class="px-3 py-1 rounded-lg transition-colors font-medium"
                    :class="activeScriptTab === 'original' ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:text-slate-200'"
                  >
                    Bài Gốc
                  </button>
                </div>

                <button
                  type="button"
                  class="text-xs text-slate-400 hover:text-slate-200 flex items-center gap-1 font-medium transition-colors"
                  @click="
                    copyToClipboard(
                      activeScriptTab === 'long'
                        ? article.script_long || ''
                        : activeScriptTab === 'reel'
                        ? article.script_short || ''
                        : article.content,
                      'script-copy'
                    )
                  "
                >
                  <Icons name="Copy" :size="12" />
                  <span>{{ copyFeedback['script-copy'] ? '✓ Đã chép' : 'Chép nội dung' }}</span>
                </button>
              </div>

              <!-- Script Viewer -->
              <div class="p-4 rounded-xl bg-black/40 border border-white/[0.06] max-h-[340px] overflow-y-auto font-sans text-sm text-slate-200 whitespace-pre-wrap leading-relaxed">
                <div v-if="activeScriptTab === 'long'">
                  {{ article.script_long || 'Chưa có kịch bản dài 16:9.' }}
                </div>
                <div v-else-if="activeScriptTab === 'reel'">
                  {{ article.script_short || 'Chưa có kịch bản ngắn 9:16.' }}
                </div>
                <div v-else class="space-y-3">
                  <div v-if="article.excerpt" class="italic text-slate-400 border-l-2 border-slate-700 pl-3">
                    {{ article.excerpt }}
                  </div>
                  <div>{{ article.content }}</div>
                </div>
              </div>
            </div>

            <!-- Audit Meta Footer -->
            <div class="border-t border-white/[0.06] pt-3 flex items-center justify-between text-[11px] font-mono text-slate-500">
              <div>Task: <span class="text-slate-400">{{ article.pipeline_task_id || '—' }}</span></div>
              <div>{{ new Date(article.updated_at).toLocaleDateString('vi-VN') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- ZEN FULLSCREEN MODAL FOR YOUTUBE DESCRIPTION             -->
    <!-- ======================================================== -->
    <div
      v-if="isDescFullscreen"
      class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6"
      @click.self="isDescFullscreen = false"
    >
      <div class="w-full max-w-4xl max-h-[90vh] rounded-2xl bg-slate-900 border border-white/[0.1] shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-white/[0.06] flex items-center justify-between bg-black/40">
          <div class="flex items-center gap-2.5">
            <Icons name="AlignLeft" :size="16" class="text-slate-400" />
            <h3 class="text-sm font-semibold text-white">Soạn Thảo Mô Tả YouTube (Toàn Màn Hình)</h3>
            <span class="text-xs font-mono text-slate-500">
              • {{ descWordCount }} từ • {{ descCharCount }} ký tự
            </span>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="copyToClipboard(form.seo_description, 'yt-desc-modal')"
              class="px-3 py-1.5 rounded-lg bg-white/[0.06] hover:bg-white/[0.1] text-slate-300 text-xs font-medium flex items-center gap-1 transition-colors"
            >
              <Icons name="Copy" :size="13" />
              <span>{{ copyFeedback['yt-desc-modal'] ? '✓ Đã chép' : 'Sao chép' }}</span>
            </button>
            <button
              type="button"
              @click="isDescFullscreen = false"
              class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/[0.06] transition-colors"
            >
              <Icons name="X" :size="16" />
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="p-6 flex-1 overflow-y-auto bg-black/20">
          <textarea
            v-model="form.seo_description"
            rows="18"
            class="w-full h-full min-h-[420px] p-4 rounded-xl bg-black/40 border border-white/[0.08] text-slate-100 font-sans text-sm sm:text-base leading-relaxed focus:outline-none focus:border-white/25 shadow-inner resize-none"
            placeholder="Nhập toàn bộ nội dung mô tả video..."
          ></textarea>
        </div>

        <!-- Footer -->
        <div class="px-6 py-3.5 border-t border-white/[0.06] bg-black/40 flex items-center justify-between">
          <div class="text-xs text-slate-500">
            Nhấn <kbd class="px-1 py-0.5 rounded bg-white/[0.06] text-slate-400 font-mono text-[10px]">Cmd+S</kbd> hoặc nút Lưu để lưu thay đổi.
          </div>
          <div class="flex items-center gap-2.5">
            <button
              type="button"
              @click="isDescFullscreen = false"
              class="px-4 py-2 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-slate-300 text-xs font-medium transition-colors"
            >
              Đóng
            </button>
            <button
              type="button"
              @click="handleSaveMetadata(); isDescFullscreen = false;"
              :disabled="form.processing"
              class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-white text-slate-950 font-semibold text-xs transition-all shadow"
            >
              Lưu Thay Đổi
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
