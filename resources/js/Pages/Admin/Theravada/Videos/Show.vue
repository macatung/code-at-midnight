<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
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

const activeScriptTab = ref<'long' | 'reel' | 'original'>('long');
const isTriggering = ref(false);
const copyFeedback = ref<Record<string, boolean>>({});

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

// 1-Click Copy Utility
const copyToClipboard = async (text: string, key: string) => {
  if (!text) return;
  try {
    await navigator.clipboard.writeText(text);
    copyFeedback.value[key] = true;
    setTimeout(() => {
      copyFeedback.value[key] = false;
    }, 2000);
  } catch {
    // Fallback for environments without clipboard permissions
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    copyFeedback.value[key] = true;
    setTimeout(() => {
      copyFeedback.value[key] = false;
    }, 2000);
  }
};

// Formatted Strings for 1-Click Copy
const formattedTitle = computed(() => props.article.seo_title || props.article.title);
const formattedDescription = computed(() => props.article.seo_description || props.article.excerpt || '');
const formattedCaption = computed(() => props.article.social_caption || '');
const formattedHashtags = computed(() => {
  const tags = props.article.hashtags;
  if (Array.isArray(tags)) {
    return tags.map((t) => (t.startsWith('#') ? t : `#${t}`)).join(' ');
  }
  return '#TamAnVanSuAn #LoiPhatDay #Theravada #macatungdev';
});

const formattedFullKit = computed(() => {
  return [
    `=== TIÊU ĐỀ VIDEO YOUTUBE ===`,
    formattedTitle.value,
    ``,
    `=== MÔ TẢ VIDEO (YOUTUBE / FACEBOOK) ===`,
    formattedDescription.value,
    ``,
    `=== CAPTION MẠNG XÃ HỘI (TIKTOK / REELS / SHORTS) ===`,
    formattedCaption.value,
    ``,
    `=== HASHTAGS BÀI ĐĂNG ===`,
    formattedHashtags.value,
    ``,
    `=== LIÊN KẾT BÀI VIẾT NGUYÊN BẢN ===`,
    `https://theravada.macatung.dev/phap-thoai/${props.article.slug}`,
    ``,
    `=== TÀI NGUYÊN MEDIA CDN ===`,
    `Video dài (16:9): ${props.article.video_long_url || 'Chưa hoàn thành'}`,
    `Video ngắn (9:16): ${props.article.video_short_url || 'Chưa hoàn thành'}`,
    `Thumbnail 16:9: ${props.article.thumbnail_long_url || 'Chưa hoàn thành'}`,
    `Thumbnail 9:16: ${props.article.thumbnail_short_url || 'Chưa hoàn thành'}`,
  ].join('\n');
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
</script>

<template>
  <AdminLayout :title="`Video: ${article.title}`">
    <Head :title="`Chi Tiết Video: ${article.title} — Admin CMS`" />

    <div class="space-y-6">
      <!-- Breadcrumb & Top Action Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-white/10">
        <div>
          <Link
            href="/admin/theravada/videos"
            class="inline-flex items-center gap-1.5 text-xs font-mono text-slate-400 hover:text-phantom-mint transition-colors mb-2"
          >
            <Icons name="ChevronLeft" :size="14" />
            <span>Quay lại Danh Sách Video</span>
          </Link>

          <div class="flex items-center gap-2 flex-wrap">
            <span
              v-if="article.video_status === 'published'"
              class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-phantom-mint/15 text-phantom-mint border border-phantom-mint/30"
            >
              ✓ Đã Xuất Bản
            </span>
            <span
              v-else-if="article.video_status === 'completed'"
              class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30"
            >
              ✓ Đã Hoàn Thành
            </span>
            <span
              v-else-if="article.video_status === 'processing'"
              class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 animate-pulse"
            >
              ⚡ Đang Render Video
            </span>
            <span
              v-else-if="article.video_status === 'failed'"
              class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30"
            >
              ✕ Thất Bại
            </span>
            <span
              v-else
              class="px-2.5 py-0.5 rounded-full text-[10px] font-mono text-slate-400 bg-white/5 border border-white/10"
            >
              Bản Nháp
            </span>

            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-white/5 text-slate-400 border border-white/5">
              {{ article.category || 'phap-thoai' }}
            </span>

            <span class="text-xs font-mono text-slate-500">ID: #{{ article.id }}</span>
          </div>

          <h1 class="text-xl sm:text-2xl lg:text-3xl font-display font-extrabold text-white mt-1.5 line-clamp-2">
            {{ article.title }}
          </h1>
          <div v-if="article.pali_title" class="text-xs sm:text-sm font-serif italic text-amber-200/80 mt-0.5">
            {{ article.pali_title }}
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2.5 flex-wrap shrink-0">
          <a
            :href="`https://theravada.macatung.dev/phap-thoai/${article.slug}`"
            target="_blank"
            class="px-3.5 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-mono transition-all flex items-center gap-1.5 border border-white/10"
          >
            <Icons name="ExternalLink" :size="14" />
            <span>Xem Bài Viết Gốc</span>
          </a>

          <button
            type="button"
            class="px-4 py-2 rounded-xl font-mono text-xs font-bold transition-all shadow-sm flex items-center gap-1.5"
            :class="
              article.video_status === 'published'
                ? 'bg-white/10 hover:bg-slate-700 text-slate-200 border border-white/10'
                : 'bg-emerald-500 hover:bg-emerald-400 text-midnight-950 shadow-glow-mint'
            "
            @click="togglePublish"
          >
            <Icons name="Play" :size="14" />
            <span>{{ article.video_status === 'published' ? 'Gỡ Xuất Bản' : 'Xuất Bản Live' }}</span>
          </button>

          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-phantom-mint text-midnight-950 font-mono text-xs font-bold hover:brightness-110 transition-all shadow-glow-mint flex items-center gap-1.5"
            :disabled="isTriggering"
            @click="triggerPipeline"
          >
            <Icons :name="isTriggering ? 'Clock' : 'Zap'" :size="14" />
            <span>{{ isTriggering ? 'Đang kích hoạt...' : 'Tạo Video (n8n)' }}</span>
          </button>
        </div>
      </div>

      <!-- Pipeline Error Alert Banner -->
      <div
        v-if="article.pipeline_error"
        class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-mono flex items-start gap-3"
      >
        <Icons name="Bug" :size="18" class="text-rose-400 shrink-0 mt-0.5" />
        <div class="space-y-1">
          <div class="font-bold text-rose-200">Lỗi trong tiến trình render:</div>
          <div class="text-rose-300/90 whitespace-pre-wrap">{{ article.pipeline_error }}</div>
        </div>
      </div>

      <!-- Main Content Grid: Left Dual Players & Thumbnails (8 cols), Right 1-Click Copy Suite & Metadata (4 cols) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Column: Dual Video Players & Thumbnails (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
          <!-- Section 1: Dual Video Players -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
              <div class="flex items-center gap-2">
                <Icons name="Play" :size="18" class="text-phantom-mint" />
                <h2 class="font-display font-bold text-base text-white">
                  Trình Xem Trước Video Đa Định Dạng (HTML5 Video Players)
                </h2>
              </div>
              <div class="text-[11px] font-mono text-slate-400">
                16:9 YouTube & 9:16 Shorts/TikTok
              </div>
            </div>

            <!-- Dual Players Container -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
              <!-- Player 1: 16:9 Landscape Player (7 cols) -->
              <div class="md:col-span-7 space-y-2.5">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-mono text-indigo-300 flex items-center gap-1.5 font-bold">
                    <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                    <span>16:9 Landscape (Full HD 1080p)</span>
                  </span>
                  <span class="text-[11px] font-mono text-slate-400">
                    ⏱️ {{ formatDuration(article.video_long_duration) }}
                  </span>
                </div>

                <!-- Video Frame -->
                <div class="aspect-video rounded-xl overflow-hidden bg-black/90 border border-white/10 relative shadow-xl flex items-center justify-center">
                  <video
                    v-if="article.video_long_url"
                    controls
                    playsinline
                    class="w-full h-full object-contain"
                    :src="article.video_long_url"
                    :poster="article.thumbnail_long_url || undefined"
                  />
                  <div v-else class="p-6 text-center text-slate-500 font-mono text-xs space-y-2">
                    <Icons name="Play" :size="32" class="mx-auto text-slate-600 mb-1" />
                    <div>Video 16:9 chưa được render</div>
                    <button
                      type="button"
                      class="px-3 py-1.5 rounded-lg bg-phantom-mint/10 hover:bg-phantom-mint hover:text-midnight-950 text-phantom-mint text-[11px] transition-all"
                      @click="triggerPipeline"
                    >
                      Bấm "Tạo Video" để bắt đầu
                    </button>
                  </div>
                </div>

                <!-- Video 16:9 Actions -->
                <div v-if="article.video_long_url" class="flex items-center justify-between gap-2 pt-1">
                  <a
                    :href="article.video_long_url"
                    target="_blank"
                    class="text-[11px] font-mono text-slate-300 hover:text-phantom-mint flex items-center gap-1"
                  >
                    <Icons name="ExternalLink" :size="12" />
                    <span>Mở file MP4</span>
                  </a>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-slate-300 hover:text-phantom-mint flex items-center gap-1"
                    @click="copyToClipboard(article.video_long_url, 'v16x9')"
                  >
                    <Icons name="Copy" :size="12" />
                    <span>{{ copyFeedback['v16x9'] ? '✓ Đã chép CDN!' : 'Sao chép CDN link' }}</span>
                  </button>
                </div>
              </div>

              <!-- Player 2: 9:16 Vertical Smartphone Frame (5 cols) -->
              <div class="md:col-span-5 space-y-2.5">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-mono text-purple-300 flex items-center gap-1.5 font-bold">
                    <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                    <span>9:16 Reel / Shorts (1080x1920)</span>
                  </span>
                  <span class="text-[11px] font-mono text-slate-400">
                    ⏱️ {{ formatDuration(article.video_short_duration) }}
                  </span>
                </div>

                <!-- Smartphone Frame -->
                <div class="w-full max-w-[240px] aspect-[9/16] mx-auto rounded-3xl overflow-hidden bg-slate-900 border-4 border-slate-700 shadow-2xl relative flex flex-col justify-between">
                  <!-- Smartphone Pill Notch -->
                  <div class="absolute top-2 left-1/2 -translate-x-1/2 w-16 h-3.5 bg-black/80 rounded-full z-10 pointer-events-none flex items-center justify-center">
                    <div class="w-2 h-2 rounded-full bg-slate-800"></div>
                  </div>

                  <video
                    v-if="article.video_short_url"
                    controls
                    playsinline
                    class="w-full h-full object-cover"
                    :src="article.video_short_url"
                    :poster="article.thumbnail_short_url || undefined"
                  />
                  <div v-else class="h-full flex flex-col items-center justify-center p-4 text-center text-slate-500 font-mono text-xs space-y-2">
                    <Icons name="Play" :size="28" class="text-slate-600 mb-1" />
                    <div>Reel 9:16 chưa render</div>
                  </div>
                </div>

                <!-- Video 9:16 Actions -->
                <div v-if="article.video_short_url" class="flex items-center justify-between gap-2 pt-1 max-w-[240px] mx-auto">
                  <a
                    :href="article.video_short_url"
                    target="_blank"
                    class="text-[11px] font-mono text-slate-300 hover:text-purple-300 flex items-center gap-1"
                  >
                    <Icons name="ExternalLink" :size="12" />
                    <span>Mở Reel</span>
                  </a>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-slate-300 hover:text-purple-300 flex items-center gap-1"
                    @click="copyToClipboard(article.video_short_url, 'v9x16')"
                  >
                    <Icons name="Copy" :size="12" />
                    <span>{{ copyFeedback['v9x16'] ? '✓ Đã chép!' : 'Copy CDN link' }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 2: Dual Thumbnail Cards -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
              <div class="flex items-center gap-2">
                <Icons name="Sparkles" :size="18" class="text-amber-300" />
                <h2 class="font-display font-bold text-base text-white">
                  Hình Thu Nhỏ Độ Nét Cao (Dual High-Contrast Thumbnails)
                </h2>
              </div>
              <div class="text-[11px] font-mono text-slate-400">
                Chuẩn phông tiếng Việt Unicode
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
              <!-- 16:9 Thumbnail -->
              <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-2">
                <div class="flex items-center justify-between text-xs font-mono">
                  <span class="text-indigo-300 font-bold">Thumbnail 16:9 (YouTube)</span>
                  <span class="text-slate-500">1920x1080</span>
                </div>
                <div class="aspect-video rounded-lg overflow-hidden bg-black/60 border border-white/10 flex items-center justify-center">
                  <img
                    v-if="article.thumbnail_long_url"
                    :src="article.thumbnail_long_url"
                    alt="Thumbnail 16:9"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="text-slate-600 font-mono text-xs">Chưa có ảnh 16:9</div>
                </div>
                <div v-if="article.thumbnail_long_url" class="flex items-center justify-between pt-1">
                  <a
                    :href="article.thumbnail_long_url"
                    target="_blank"
                    class="text-[11px] font-mono text-slate-300 hover:text-white flex items-center gap-1"
                  >
                    <Icons name="ExternalLink" :size="12" />
                    <span>Mở ảnh gốc</span>
                  </a>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-phantom-mint hover:underline"
                    @click="copyToClipboard(article.thumbnail_long_url, 't16x9')"
                  >
                    {{ copyFeedback['t16x9'] ? '✓ Đã chép link!' : 'Copy Link Ảnh' }}
                  </button>
                </div>
              </div>

              <!-- 9:16 Thumbnail -->
              <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-2">
                <div class="flex items-center justify-between text-xs font-mono">
                  <span class="text-purple-300 font-bold">Thumbnail 9:16 (Shorts/TikTok)</span>
                  <span class="text-slate-500">1080x1920</span>
                </div>
                <div class="aspect-[9/16] max-h-52 rounded-lg overflow-hidden bg-black/60 border border-white/10 mx-auto flex items-center justify-center">
                  <img
                    v-if="article.thumbnail_short_url"
                    :src="article.thumbnail_short_url"
                    alt="Thumbnail 9:16"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="text-slate-600 font-mono text-xs">Chưa có ảnh 9:16</div>
                </div>
                <div v-if="article.thumbnail_short_url" class="flex items-center justify-between pt-1">
                  <a
                    :href="article.thumbnail_short_url"
                    target="_blank"
                    class="text-[11px] font-mono text-slate-300 hover:text-white flex items-center gap-1"
                  >
                    <Icons name="ExternalLink" :size="12" />
                    <span>Mở ảnh gốc</span>
                  </a>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-purple-300 hover:underline"
                    @click="copyToClipboard(article.thumbnail_short_url, 't9x16')"
                  >
                    {{ copyFeedback['t9x16'] ? '✓ Đã chép link!' : 'Copy Link Ảnh' }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 3: Tabbed Scripts & Original Buddhist Content -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
              <!-- Script Tabs -->
              <div class="flex items-center gap-1.5 p-1 rounded-xl bg-midnight-900 border border-white/10">
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-lg text-xs font-mono font-medium transition-all"
                  :class="activeScriptTab === 'long' ? 'bg-phantom-mint text-midnight-950 font-bold' : 'text-slate-400 hover:text-white'"
                  @click="activeScriptTab = 'long'"
                >
                  Kịch Bản Dài 16:9 ({{ wordCountLong }} từ)
                </button>
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-lg text-xs font-mono font-medium transition-all"
                  :class="activeScriptTab === 'reel' ? 'bg-phantom-mint text-midnight-950 font-bold' : 'text-slate-400 hover:text-white'"
                  @click="activeScriptTab = 'reel'"
                >
                  Kịch Bản Reel 9:16 ({{ wordCountReel }} từ)
                </button>
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-lg text-xs font-mono font-medium transition-all"
                  :class="activeScriptTab === 'original' ? 'bg-phantom-mint text-midnight-950 font-bold' : 'text-slate-400 hover:text-white'"
                  @click="activeScriptTab = 'original'"
                >
                  Bài Viết Gốc
                </button>
              </div>

              <!-- Copy active tab script button -->
              <button
                type="button"
                class="text-xs font-mono text-phantom-mint hover:underline flex items-center gap-1"
                @click="
                  copyToClipboard(
                    activeScriptTab === 'long'
                      ? article.script_long || ''
                      : activeScriptTab === 'reel'
                      ? article.script_short || ''
                      : article.content,
                    'activeScript'
                  )
                "
              >
                <Icons name="Copy" :size="12" />
                <span>{{ copyFeedback['activeScript'] ? '✓ Đã sao chép kịch bản!' : 'Sao chép văn bản' }}</span>
              </button>
            </div>

            <!-- Tab Content Display -->
            <div class="p-4 rounded-xl bg-midnight-900/80 border border-white/5 max-h-[480px] overflow-y-auto font-sans text-xs sm:text-sm text-slate-300 leading-relaxed space-y-3">
              <!-- Tab 1: Kịch bản dài -->
              <div v-if="activeScriptTab === 'long'" class="whitespace-pre-wrap font-sans">
                <div v-if="article.script_long">
                  {{ article.script_long }}
                </div>
                <div v-else class="text-slate-500 font-mono text-center py-8">
                  Chưa có kịch bản dài 16:9. Kịch bản sẽ tự động đồng bộ khi n8n workflow hoàn tất.
                </div>
              </div>

              <!-- Tab 2: Kịch bản Reel ngắn -->
              <div v-else-if="activeScriptTab === 'reel'" class="whitespace-pre-wrap font-sans">
                <div v-if="article.script_short">
                  {{ article.script_short }}
                </div>
                <div v-else class="text-slate-500 font-mono text-center py-8">
                  Chưa có kịch bản Reel 9:16. Kịch bản sẽ tự động đồng bộ khi n8n workflow hoàn tất.
                </div>
              </div>

              <!-- Tab 3: Bài viết gốc -->
              <div v-else class="space-y-4">
                <div v-if="article.excerpt" class="p-3 rounded-lg bg-white/5 border-l-2 border-phantom-mint italic text-slate-300">
                  {{ article.excerpt }}
                </div>
                <div class="whitespace-pre-wrap leading-relaxed font-sans text-slate-200">
                  {{ article.content }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: 1-Click Copy Tools Suite & Pipeline Metadata (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
          <!-- 1-Click Copy Tools Suite -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4">
            <div class="flex items-center gap-2 border-b border-white/5 pb-3">
              <Icons name="Copy" :size="18" class="text-phantom-mint" />
              <h2 class="font-display font-bold text-base text-white">
                Bộ Công Cụ Tiện Ích (1-Click Copy)
              </h2>
            </div>
            <p class="text-xs text-slate-400 font-sans">
              Sao chép tức thì siêu tốc các trường nội dung chuẩn SEO để đăng lên YouTube, TikTok, Facebook Reels.
            </p>

            <div class="space-y-3">
              <!-- Copy Button 1: Tiêu đề SEO -->
              <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1.5">
                <div class="flex items-center justify-between">
                  <span class="text-[11px] font-mono text-slate-400">Tiêu Đề SEO / YouTube</span>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-phantom-mint hover:underline font-bold"
                    @click="copyToClipboard(formattedTitle, 'title')"
                  >
                    {{ copyFeedback['title'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                  </button>
                </div>
                <div class="text-xs text-white font-medium line-clamp-2">
                  {{ formattedTitle }}
                </div>
              </div>

              <!-- Copy Button 2: Mô tả YouTube -->
              <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1.5">
                <div class="flex items-center justify-between">
                  <span class="text-[11px] font-mono text-slate-400">Mô Tả Video (SEO Description)</span>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-phantom-mint hover:underline font-bold"
                    @click="copyToClipboard(formattedDescription, 'desc')"
                  >
                    {{ copyFeedback['desc'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                  </button>
                </div>
                <div class="text-xs text-slate-300 line-clamp-3">
                  {{ formattedDescription || '—' }}
                </div>
              </div>

              <!-- Copy Button 3: Social Caption -->
              <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1.5">
                <div class="flex items-center justify-between">
                  <span class="text-[11px] font-mono text-slate-400">Social Caption (TikTok / Reels)</span>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-phantom-mint hover:underline font-bold"
                    @click="copyToClipboard(formattedCaption, 'caption')"
                  >
                    {{ copyFeedback['caption'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                  </button>
                </div>
                <div class="text-xs text-slate-300 line-clamp-3">
                  {{ formattedCaption || '—' }}
                </div>
              </div>

              <!-- Copy Button 4: Hashtags -->
              <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1.5">
                <div class="flex items-center justify-between">
                  <span class="text-[11px] font-mono text-slate-400">Hashtags</span>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-phantom-mint hover:underline font-bold"
                    @click="copyToClipboard(formattedHashtags, 'tags')"
                  >
                    {{ copyFeedback['tags'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                  </button>
                </div>
                <div class="text-xs font-mono text-phantom-mint/80 line-clamp-2">
                  {{ formattedHashtags }}
                </div>
              </div>

              <!-- Master Button: Copy Trọn Bộ Xuất Bản (Full Publishing Kit) -->
              <button
                type="button"
                class="w-full py-2.5 px-4 rounded-xl bg-phantom-mint/15 hover:bg-phantom-mint hover:text-midnight-950 text-phantom-mint text-xs font-mono font-bold transition-all border border-phantom-mint/30 flex items-center justify-center gap-2"
                @click="copyToClipboard(formattedFullKit, 'fullkit')"
              >
                <Icons name="Copy" :size="14" />
                <span>{{ copyFeedback['fullkit'] ? '✓ Đã sao chép Trọn Bộ Xuất Bản!' : '⚡ Sao Chép Trọn Bộ Xuất Bản' }}</span>
              </button>
            </div>
          </div>

          <!-- Pipeline Execution Metadata Card -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-3">
            <div class="flex items-center gap-2 border-b border-white/5 pb-2.5">
              <Icons name="Terminal" :size="16" class="text-slate-400" />
              <h3 class="font-mono font-bold text-xs text-slate-300 uppercase tracking-wider">
                Thông Số Tiến Trình Sản Xuất
              </h3>
            </div>

            <div class="space-y-2 text-xs font-mono">
              <div class="flex items-center justify-between">
                <span class="text-slate-500">Mã Tác Vụ (Job ID):</span>
                <span class="text-slate-200 truncate max-w-[160px]" :title="article.pipeline_task_id || undefined">
                  {{ article.pipeline_task_id || '—' }}
                </span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-slate-500">Trạng Thái Video:</span>
                <span class="text-phantom-mint uppercase font-bold">{{ article.video_status }}</span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-slate-500">Thời Gian Bắt Đầu:</span>
                <span class="text-slate-300">
                  {{ article.pipeline_started_at ? new Date(article.pipeline_started_at).toLocaleString('vi-VN') : '—' }}
                </span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-slate-500">Thời Gian Hoàn Tất:</span>
                <span class="text-slate-300">
                  {{ article.pipeline_completed_at ? new Date(article.pipeline_completed_at).toLocaleString('vi-VN') : '—' }}
                </span>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-slate-500">Cập Nhật Lần Cuối:</span>
                <span class="text-slate-400">
                  {{ new Date(article.updated_at).toLocaleString('vi-VN') }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
