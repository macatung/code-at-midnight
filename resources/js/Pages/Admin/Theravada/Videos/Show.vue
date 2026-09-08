<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
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

// Active Platform Tab
const activePlatform = ref<'youtube' | 'tiktok' | 'reels' | 'website'>('youtube');

// Dual Player Mode for YouTube tab: 'youtube' or 'gcs_mp4'
const playerMode = ref<'youtube' | 'gcs_mp4'>(props.article.youtube_id ? 'youtube' : 'gcs_mp4');

// Sub-tabs for scripts in Website tab
const activeScriptTab = ref<'long' | 'reel' | 'original'>('long');

// Form for updating YouTube URL and metadata
const ytForm = useForm({
  youtube_url: props.article.youtube_url || '',
});

const isTriggering = ref(false);
const copyFeedback = ref<Record<string, boolean>>({});

// 1-Click Copy Utility
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
  }, 2000);
};

// Save YouTube URL
const saveYoutubeUrl = () => {
  ytForm.put(`/admin/theravada/videos/${props.article.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      if (props.article.youtube_id || ytForm.youtube_url) {
        playerMode.value = 'youtube';
      }
    },
  });
};

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

// Formatted Computed Properties
const formattedTitle = computed(() => props.article.seo_title || props.article.title);
const formattedDescription = computed(() => props.article.seo_description || props.article.excerpt || '');
const formattedCaption = computed(() => props.article.social_caption || '');

const formattedHashtagsList = computed(() => {
  const tags = props.article.hashtags;
  if (Array.isArray(tags) && tags.length > 0) {
    return tags.map((t) => (t.startsWith('#') ? t : `#${t}`));
  }
  return ['#TamAnVanSuAn', '#LoiPhatDay', '#Theravada', '#ThienDinh', '#MaToaThien'];
});

const formattedHashtagsString = computed(() => formattedHashtagsList.value.join(' '));

// Platform-Specific Kits
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
    formattedCaption.value,
    ``,
    `🎧 Nghe trọn bài giảng tại link bio!`,
    formattedHashtagsString.value + ' #shorts #reels #xuhuong',
  ].join('\n');
});

const reelsFullKit = computed(() => {
  return [
    formattedCaption.value,
    ``,
    `🌿 Lời Phật dạy cho tâm an giữa vạn biến cuộc đời.`,
    `🎵 Âm thanh: Nhạc thiền 432Hz hòa trộn tiếng chuông chánh niệm`,
    formattedHashtagsString.value,
  ].join('\n');
});

// Website Responsive Embed Code
const embedCodeSnippet = computed(() => {
  if (props.article.youtube_id) {
    return `<div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">\n  <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" src="https://www.youtube-nocookie.com/embed/${props.article.youtube_id}" title="${props.article.title}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>\n</div>`;
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
</script>

<template>
  <AdminLayout :title="`Trạm Phát Hành: ${article.title}`">
    <Head :title="`Trạm Phát Hành: ${article.title} — Admin CMS`" />

    <div class="space-y-6">
      <!-- Breadcrumb & Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-white/10">
        <div>
          <Link
            href="/admin/theravada/videos"
            class="inline-flex items-center gap-1.5 text-xs font-mono text-slate-400 hover:text-phantom-mint transition-colors mb-2"
          >
            <Icons name="ChevronLeft" :size="14" />
            <span>Quay lại Trạm Phát Hành Video</span>
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

            <span v-if="article.youtube_id" class="px-2 py-0.5 rounded text-[10px] font-mono bg-red-500/15 text-red-300 border border-red-500/30 font-bold">
              YouTube ID: {{ article.youtube_id }}
            </span>
          </div>

          <h1 class="text-xl sm:text-2xl lg:text-3xl font-display font-extrabold text-white mt-1.5 line-clamp-2">
            {{ article.seo_title || article.title }}
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
            <span>{{ isTriggering ? 'Đang kích hoạt...' : 'Tạo Lại (n8n)' }}</span>
          </button>
        </div>
      </div>

      <!-- Pipeline Error Alert Banner if any -->
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

      <!-- Platform Hub Tabs Navigation -->
      <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-midnight-900/90 border border-white/10 overflow-x-auto no-scrollbar">
        <!-- Tab 1: YouTube Studio -->
        <button
          type="button"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-mono text-xs font-bold transition-all shrink-0"
          :class="
            activePlatform === 'youtube'
              ? 'bg-red-600 text-white shadow-lg shadow-red-900/30'
              : 'text-slate-400 hover:text-white hover:bg-white/5'
          "
          @click="activePlatform = 'youtube'"
        >
          <span class="w-2 h-2 rounded-full" :class="article.video_long_url ? 'bg-red-300' : 'bg-slate-600'"></span>
          <span>YouTube Studio (16:9)</span>
          <span v-if="article.youtube_id" class="px-1.5 py-0.2 rounded bg-white/20 text-[10px]">LIVE</span>
        </button>

        <!-- Tab 2: TikTok -->
        <button
          type="button"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-mono text-xs font-bold transition-all shrink-0"
          :class="
            activePlatform === 'tiktok'
              ? 'bg-cyan-500 text-midnight-950 shadow-lg shadow-cyan-900/30'
              : 'text-slate-400 hover:text-white hover:bg-white/5'
          "
          @click="activePlatform = 'tiktok'"
        >
          <span class="w-2 h-2 rounded-full" :class="article.video_short_url ? 'bg-cyan-200' : 'bg-slate-600'"></span>
          <span>TikTok Hub (9:16)</span>
        </button>

        <!-- Tab 3: Facebook & Instagram Reels -->
        <button
          type="button"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-mono text-xs font-bold transition-all shrink-0"
          :class="
            activePlatform === 'reels'
              ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg shadow-purple-900/30'
              : 'text-slate-400 hover:text-white hover:bg-white/5'
          "
          @click="activePlatform = 'reels'"
        >
          <span class="w-2 h-2 rounded-full" :class="article.video_short_url ? 'bg-pink-200' : 'bg-slate-600'"></span>
          <span>Facebook & IG Reels</span>
        </button>

        <!-- Tab 4: Website & Kịch Bản -->
        <button
          type="button"
          class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-mono text-xs font-bold transition-all shrink-0"
          :class="
            activePlatform === 'website'
              ? 'bg-phantom-mint text-midnight-950 shadow-lg shadow-emerald-900/30 font-extrabold'
              : 'text-slate-400 hover:text-white hover:bg-white/5'
          "
          @click="activePlatform = 'website'"
        >
          <Icons name="Code" :size="14" />
          <span>Website & Kịch Bản</span>
        </button>
      </div>

      <!-- MAIN TAB CONTENT PANELS -->

      <!-- ======================================================== -->
      <!-- TAB 1: YOUTUBE STUDIO (16:9 Landscape Player & Kit)      -->
      <!-- ======================================================== -->
      <div v-if="activePlatform === 'youtube'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left 8 Cols: Dual Video Player & Thumbnail 16:9 -->
        <div class="lg:col-span-8 space-y-6">
          <!-- Video Player Container -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/5 pb-3">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                <h2 class="font-display font-bold text-base text-white">
                  Trình Xem Trước Video YouTube (16:9 Full HD)
                </h2>
              </div>

              <!-- Dual Player Mode Switcher -->
              <div class="flex items-center gap-1 p-1 rounded-xl bg-midnight-900 border border-white/10 text-xs font-mono">
                <button
                  type="button"
                  class="px-2.5 py-1 rounded-lg transition-all"
                  :class="
                    playerMode === 'youtube'
                      ? 'bg-red-600 text-white font-bold shadow'
                      : 'text-slate-400 hover:text-white'
                  "
                  :disabled="!article.youtube_id"
                  :title="!article.youtube_id ? 'Vui lòng nhập link YouTube bên dưới trước' : ''"
                  @click="playerMode = 'youtube'"
                >
                  Trình phát YouTube
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1 rounded-lg transition-all"
                  :class="
                    playerMode === 'gcs_mp4'
                      ? 'bg-indigo-600 text-white font-bold shadow'
                      : 'text-slate-400 hover:text-white'
                  "
                  @click="playerMode = 'gcs_mp4'"
                >
                  Video Gốc MP4
                </button>
              </div>
            </div>

            <!-- Frame Display -->
            <div class="aspect-video rounded-xl overflow-hidden bg-black border border-white/10 relative shadow-2xl flex items-center justify-center">
              <!-- YouTube Embed Player -->
              <iframe
                v-if="playerMode === 'youtube' && article.youtube_id"
                :src="`https://www.youtube-nocookie.com/embed/${article.youtube_id}?autoplay=0&rel=0`"
                class="w-full h-full border-0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
              />

              <!-- Native HTML5 MP4 Player -->
              <video
                v-else-if="article.video_long_url"
                controls
                playsinline
                class="w-full h-full object-contain"
                :src="article.video_long_url"
                :poster="article.thumbnail_long_url || undefined"
              />

              <!-- Placeholder when neither is available -->
              <div v-else class="p-8 text-center text-slate-500 font-mono text-xs space-y-2">
                <Icons name="Play" :size="36" class="mx-auto text-slate-600 mb-1" />
                <div>Video 16:9 chưa được tạo</div>
                <button
                  type="button"
                  class="px-3.5 py-1.5 rounded-lg bg-phantom-mint/10 hover:bg-phantom-mint hover:text-midnight-950 text-phantom-mint text-xs transition-all"
                  @click="triggerPipeline"
                >
                  Bấm "Tạo Lại (n8n)" để sản xuất
                </button>
              </div>
            </div>

            <!-- Video Player Controls & Direct Links -->
            <div class="flex items-center justify-between gap-3 text-xs font-mono text-slate-400 pt-1 flex-wrap">
              <div class="flex items-center gap-2">
                <span>Thời lượng:</span>
                <span class="text-white font-bold">{{ formatDuration(article.video_long_duration) }}</span>
                <span>•</span>
                <span>Độ phân giải: 1920x1080 (30fps)</span>
              </div>

              <div class="flex items-center gap-2">
                <a
                  v-if="article.video_long_url"
                  :href="article.video_long_url"
                  target="_blank"
                  class="text-indigo-300 hover:underline flex items-center gap-1"
                >
                  <Icons name="ExternalLink" :size="12" />
                  <span>Tải MP4 Gốc</span>
                </a>
                <span v-if="article.video_long_url">•</span>
                <button
                  v-if="article.video_long_url"
                  type="button"
                  class="text-phantom-mint hover:underline"
                  @click="copyToClipboard(article.video_long_url, 'cdn16x9')"
                >
                  {{ copyFeedback['cdn16x9'] ? '✓ Đã chép CDN!' : 'Copy CDN link' }}
                </button>
              </div>
            </div>
          </div>

          <!-- YouTube Link & Video Attachment Card -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-3">
            <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
              <div class="flex items-center gap-2">
                <Icons name="Play" :size="16" class="text-red-400" />
                <h3 class="font-mono font-bold text-xs text-white uppercase tracking-wider">
                  Liên Kết YouTube Đã Tải Lên (YouTube Video URL / ID)
                </h3>
              </div>
              <span v-if="article.youtube_id" class="text-[11px] font-mono text-phantom-mint">
                ✓ Đã trích xuất ID: {{ article.youtube_id }}
              </span>
            </div>

            <p class="text-xs text-slate-400 font-sans">
              Sau khi tải video lên YouTube Studio, hãy dán liên kết (ví dụ: <code class="text-red-300">https://youtu.be/cjdEOM6sn24</code> hoặc <code class="text-red-300">cjdEOM6sn24</code>) vào đây để hệ thống tự động nhúng trình phát YouTube.
            </p>

            <form @submit.prevent="saveYoutubeUrl" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 pt-1">
              <div class="relative flex-1">
                <input
                  v-model="ytForm.youtube_url"
                  type="text"
                  placeholder="https://youtu.be/cjdEOM6sn24 hoặc ID 11 ký tự..."
                  class="w-full pl-9 pr-4 py-2 rounded-xl bg-midnight-900 border border-white/15 text-xs text-white placeholder:text-slate-500 focus:outline-none focus:border-red-500 font-mono"
                />
                <span class="absolute left-3 top-2.5 text-red-400">
                  <Icons name="Play" :size="14" />
                </span>
              </div>

              <button
                type="submit"
                class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white font-mono text-xs font-bold transition-all shadow-md shrink-0 flex items-center justify-center gap-1.5"
                :disabled="ytForm.processing"
              >
                <Icons name="Check" :size="14" />
                <span>{{ ytForm.processing ? 'Đang lưu...' : 'Lưu Link YouTube' }}</span>
              </button>

              <a
                v-if="article.youtube_url"
                :href="article.youtube_url"
                target="_blank"
                class="px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 font-mono text-xs transition-all border border-white/10 flex items-center justify-center gap-1 shrink-0"
              >
                <Icons name="ExternalLink" :size="12" />
                <span>Mở YouTube</span>
              </a>
            </form>
          </div>

          <!-- 16:9 Thumbnail Preview Card -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-3">
            <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
              <div class="flex items-center gap-2">
                <Icons name="Sparkles" :size="16" class="text-amber-300" />
                <h3 class="font-mono font-bold text-xs text-white uppercase tracking-wider">
                  Thumbnail 16:9 Chuẩn YouTube 1080p (Unicode Tiếng Việt)
                </h3>
              </div>
              <span class="text-slate-400 font-mono text-xs">1920x1080</span>
            </div>

            <div class="aspect-video rounded-xl overflow-hidden bg-black/60 border border-white/10 flex items-center justify-center group/thumb relative shadow-md">
              <img
                v-if="article.thumbnail_long_url"
                :src="article.thumbnail_long_url"
                alt="Thumbnail 16:9"
                class="w-full h-full object-cover group-hover/thumb:scale-[1.02] transition-transform duration-300"
              />
              <div v-else class="text-slate-500 font-mono text-xs">Chưa có ảnh thu nhỏ 16:9</div>
            </div>

            <div v-if="article.thumbnail_long_url" class="flex items-center justify-between pt-1 text-xs font-mono">
              <a
                :href="article.thumbnail_long_url"
                target="_blank"
                class="text-slate-300 hover:text-white flex items-center gap-1"
              >
                <Icons name="ExternalLink" :size="12" />
                <span>Mở ảnh gốc Full HD</span>
              </a>

              <button
                type="button"
                class="text-phantom-mint hover:underline font-bold"
                @click="copyToClipboard(article.thumbnail_long_url, 't16x9')"
              >
                {{ copyFeedback['t16x9'] ? '✓ Đã chép link ảnh!' : 'Sao chép link CDN Thumbnail' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Right 4 Cols: YouTube Publishing Suite & 1-Click Copy -->
        <div class="lg:col-span-4 space-y-6">
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
              <div class="flex items-center gap-2">
                <Icons name="Copy" :size="18" class="text-red-400" />
                <h3 class="font-display font-bold text-sm text-white">
                  Bộ Xuất Bản YouTube Studio
                </h3>
              </div>
              <span class="text-[10px] font-mono text-slate-400">1-Click Copy</span>
            </div>

            <div class="space-y-3">
              <!-- Field 1: Tiêu đề YouTube -->
              <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1.5">
                <div class="flex items-center justify-between">
                  <span class="text-[11px] font-mono text-slate-400">Tiêu đề Video</span>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-red-400 hover:underline font-bold"
                    @click="copyToClipboard(formattedTitle, 'yt-title')"
                  >
                    {{ copyFeedback['yt-title'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                  </button>
                </div>
                <div class="text-xs text-white font-medium line-clamp-3">
                  {{ formattedTitle }}
                </div>
              </div>

              <!-- Field 2: Mô tả YouTube -->
              <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1.5">
                <div class="flex items-center justify-between">
                  <span class="text-[11px] font-mono text-slate-400">Mô tả (SEO & Timestamps)</span>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-red-400 hover:underline font-bold"
                    @click="copyToClipboard(formattedDescription, 'yt-desc')"
                  >
                    {{ copyFeedback['yt-desc'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                  </button>
                </div>
                <div class="text-xs text-slate-300 line-clamp-6 leading-relaxed whitespace-pre-line">
                  {{ formattedDescription || '—' }}
                </div>
              </div>

              <!-- Field 3: Thẻ Tags / Từ khóa -->
              <div class="p-3 rounded-xl bg-white/5 border border-white/10 space-y-1.5">
                <div class="flex items-center justify-between">
                  <span class="text-[11px] font-mono text-slate-400">Từ khóa & Hashtags</span>
                  <button
                    type="button"
                    class="text-[11px] font-mono text-red-400 hover:underline font-bold"
                    @click="copyToClipboard(formattedHashtagsString, 'yt-tags')"
                  >
                    {{ copyFeedback['yt-tags'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                  </button>
                </div>
                <div class="flex flex-wrap gap-1 pt-0.5">
                  <span
                    v-for="(tag, idx) in formattedHashtagsList"
                    :key="idx"
                    class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-red-500/15 text-red-300 border border-red-500/20"
                  >
                    {{ tag }}
                  </span>
                </div>
              </div>

              <!-- Master Button: Copy toàn bộ YouTube Kit -->
              <button
                type="button"
                class="w-full py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-500 text-white font-mono text-xs font-bold transition-all shadow-lg shadow-red-950/40 flex items-center justify-center gap-2"
                @click="copyToClipboard(youtubeFullKit, 'yt-full')"
              >
                <Icons name="Copy" :size="14" />
                <span>{{ copyFeedback['yt-full'] ? '✓ Đã sao chép Toàn Bộ Kit!' : '⚡ Sao Chép Toàn Bộ YouTube Kit' }}</span>
              </button>
            </div>
          </div>

          <!-- Pipeline Execution Specs -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-3">
            <div class="flex items-center gap-2 border-b border-white/5 pb-2">
              <Icons name="Terminal" :size="14" class="text-slate-400" />
              <h4 class="font-mono font-bold text-xs text-slate-300 uppercase tracking-wider">Thông Số Kỹ Thuật</h4>
            </div>
            <div class="space-y-1.5 text-xs font-mono text-slate-400">
              <div class="flex justify-between">
                <span>Task ID:</span>
                <span class="text-white truncate max-w-[150px]">{{ article.pipeline_task_id || '—' }}</span>
              </div>
              <div class="flex justify-between">
                <span>Trạng thái:</span>
                <span class="text-phantom-mint font-bold uppercase">{{ article.video_status }}</span>
              </div>
              <div class="flex justify-between">
                <span>Cập nhật:</span>
                <span class="text-slate-300">{{ new Date(article.updated_at).toLocaleDateString('vi-VN') }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- TAB 2: TIKTOK HUB (9:16 Vertical Smartphone Viewport)    -->
      <!-- ======================================================== -->
      <div v-else-if="activePlatform === 'tiktok'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left 6 Cols: Smartphone Frame with Reel Video Player -->
        <div class="lg:col-span-6 space-y-4">
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4 text-center">
            <div class="flex items-center justify-between border-b border-white/5 pb-3 text-left">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                <h2 class="font-display font-bold text-base text-white">
                  Khung Mô Phỏng Màn Hình TikTok (9:16)
                </h2>
              </div>
              <span class="text-xs font-mono text-cyan-300 font-bold">
                ⏱️ {{ formatDuration(article.video_short_duration) }}
              </span>
            </div>

            <!-- Smartphone Frame -->
            <div class="w-full max-w-[280px] aspect-[9/16] mx-auto rounded-[36px] overflow-hidden bg-black border-4 border-slate-700 shadow-2xl relative flex flex-col justify-between">
              <!-- Smartphone Island Notch -->
              <div class="absolute top-2.5 left-1/2 -translate-x-1/2 w-20 h-4 bg-black/90 rounded-full z-20 pointer-events-none flex items-center justify-center border border-white/5">
                <div class="w-2.5 h-2.5 rounded-full bg-slate-900 border border-slate-800"></div>
              </div>

              <!-- Native HTML5 Video Player -->
              <video
                v-if="article.video_short_url"
                controls
                playsinline
                class="w-full h-full object-cover"
                :src="article.video_short_url"
                :poster="article.thumbnail_short_url || undefined"
              />

              <div v-else class="h-full flex flex-col items-center justify-center p-6 text-slate-500 font-mono text-xs space-y-2">
                <Icons name="Play" :size="36" class="text-slate-600 mb-1" />
                <div>Reel 9:16 chưa được tạo</div>
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-lg bg-cyan-500/15 hover:bg-cyan-500 hover:text-midnight-950 text-cyan-300 text-xs transition-all"
                  @click="triggerPipeline"
                >
                  Tạo Reel tự động
                </button>
              </div>
            </div>

            <!-- Download and CDN Links -->
            <div v-if="article.video_short_url" class="flex items-center justify-center gap-4 text-xs font-mono pt-1">
              <a
                :href="article.video_short_url"
                target="_blank"
                class="text-cyan-300 hover:underline flex items-center gap-1 font-bold"
              >
                <Icons name="ExternalLink" :size="12" />
                <span>Tải Video Reel MP4</span>
              </a>
              <span>•</span>
              <button
                type="button"
                class="text-phantom-mint hover:underline font-bold"
                @click="copyToClipboard(article.video_short_url, 'cdn9x16')"
              >
                {{ copyFeedback['cdn9x16'] ? '✓ Đã chép CDN!' : 'Copy Link CDN MP4' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Right 6 Cols: TikTok Copy Suite & Thumbnail 9:16 -->
        <div class="lg:col-span-6 space-y-6">
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
              <div class="flex items-center gap-2">
                <Icons name="Copy" :size="18" class="text-cyan-400" />
                <h3 class="font-display font-bold text-sm text-white">
                  Bộ Xuất Bản TikTok
                </h3>
              </div>
              <span class="text-[10px] font-mono text-slate-400">Tối ưu tương tác</span>
            </div>

            <!-- TikTok Caption -->
            <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs font-mono text-slate-400">Social Caption (Lời tựa lôi cuốn)</span>
                <button
                  type="button"
                  class="text-xs font-mono text-cyan-400 hover:underline font-bold"
                  @click="copyToClipboard(formattedCaption, 'tt-caption')"
                >
                  {{ copyFeedback['tt-caption'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                </button>
              </div>
              <div class="text-xs sm:text-sm text-slate-200 leading-relaxed whitespace-pre-line">
                {{ formattedCaption || '—' }}
              </div>
            </div>

            <!-- TikTok Hashtags -->
            <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs font-mono text-slate-400">Bộ Hashtags TikTok</span>
                <button
                  type="button"
                  class="text-xs font-mono text-cyan-400 hover:underline font-bold"
                  @click="copyToClipboard(formattedHashtagsString + ' #shorts #tiktok #xuhuong', 'tt-tags')"
                >
                  {{ copyFeedback['tt-tags'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                </button>
              </div>
              <div class="text-xs font-mono text-cyan-300/90 leading-relaxed">
                {{ formattedHashtagsString }} #shorts #tiktok #xuhuong
              </div>
            </div>

            <!-- Master Button: Copy toàn bộ TikTok Kit -->
            <button
              type="button"
              class="w-full py-3 px-4 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-midnight-950 font-mono text-xs font-bold transition-all shadow-lg shadow-cyan-950/40 flex items-center justify-center gap-2"
              @click="copyToClipboard(tiktokFullKit, 'tt-full')"
            >
              <Icons name="Copy" :size="14" />
              <span>{{ copyFeedback['tt-full'] ? '✓ Đã sao chép Toàn Bộ TikTok Kit!' : '⚡ Sao Chép Toàn Bộ TikTok Kit' }}</span>
            </button>
          </div>

          <!-- Thumbnail 9:16 Card -->
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-3">
            <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
              <span class="text-xs font-mono font-bold text-white uppercase">Thumbnail Dọc 9:16 (1080x1920)</span>
              <button
                v-if="article.thumbnail_short_url"
                type="button"
                class="text-xs font-mono text-cyan-300 hover:underline"
                @click="copyToClipboard(article.thumbnail_short_url, 't9x16')"
              >
                {{ copyFeedback['t9x16'] ? '✓ Đã chép link!' : 'Copy Link Ảnh' }}
              </button>
            </div>

            <div class="w-36 aspect-[9/16] rounded-xl overflow-hidden bg-black/60 border border-white/10 mx-auto flex items-center justify-center shadow-md">
              <img
                v-if="article.thumbnail_short_url"
                :src="article.thumbnail_short_url"
                alt="Thumbnail 9:16"
                class="w-full h-full object-cover"
              />
              <span v-else class="text-slate-500 font-mono text-[10px]">Chưa có</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- TAB 3: FACEBOOK & INSTAGRAM REELS                        -->
      <!-- ======================================================== -->
      <div v-else-if="activePlatform === 'reels'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left 6 Cols: Reel Smartphone Player -->
        <div class="lg:col-span-6 space-y-4">
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4 text-center">
            <div class="flex items-center justify-between border-b border-white/5 pb-3 text-left">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                <h2 class="font-display font-bold text-base text-white">
                  Facebook & Instagram Reels
                </h2>
              </div>
              <span class="text-xs font-mono text-pink-300 font-bold">
                ⏱️ {{ formatDuration(article.video_short_duration) }}
              </span>
            </div>

            <!-- Smartphone Frame -->
            <div class="w-full max-w-[280px] aspect-[9/16] mx-auto rounded-[36px] overflow-hidden bg-black border-4 border-slate-700 shadow-2xl relative flex flex-col justify-between">
              <div class="absolute top-2.5 left-1/2 -translate-x-1/2 w-20 h-4 bg-black/90 rounded-full z-20 pointer-events-none flex items-center justify-center border border-white/5">
                <div class="w-2.5 h-2.5 rounded-full bg-slate-900 border border-slate-800"></div>
              </div>

              <video
                v-if="article.video_short_url"
                controls
                playsinline
                class="w-full h-full object-cover"
                :src="article.video_short_url"
                :poster="article.thumbnail_short_url || undefined"
              />
              <div v-else class="h-full flex flex-col items-center justify-center p-6 text-slate-500 font-mono text-xs space-y-2">
                <Icons name="Play" :size="36" class="text-slate-600 mb-1" />
                <div>Reel 9:16 chưa được tạo</div>
              </div>
            </div>

            <div v-if="article.video_short_url" class="flex items-center justify-center gap-3 text-xs font-mono pt-1">
              <a
                :href="article.video_short_url"
                target="_blank"
                class="text-pink-300 hover:underline flex items-center gap-1 font-bold"
              >
                <Icons name="ExternalLink" :size="12" />
                <span>Tải Tệp Video Reel</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Right 6 Cols: Facebook & Instagram Copy Suite -->
        <div class="lg:col-span-6 space-y-6">
          <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
              <div class="flex items-center gap-2">
                <Icons name="Copy" :size="18" class="text-pink-400" />
                <h3 class="font-display font-bold text-sm text-white">
                  Bộ Xuất Bản FB & IG Reels
                </h3>
              </div>
              <span class="text-[10px] font-mono text-slate-400">Chuẩn định dạng</span>
            </div>

            <!-- Reels Caption -->
            <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs font-mono text-slate-400">Caption Bài Đăng</span>
                <button
                  type="button"
                  class="text-xs font-mono text-pink-400 hover:underline font-bold"
                  @click="copyToClipboard(formattedCaption, 'reels-caption')"
                >
                  {{ copyFeedback['reels-caption'] ? '✓ Đã sao chép!' : 'Sao chép' }}
                </button>
              </div>
              <div class="text-xs sm:text-sm text-slate-200 leading-relaxed whitespace-pre-line">
                {{ formattedCaption || '—' }}
              </div>
            </div>

            <!-- Audio & Credit Notice -->
            <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-1.5">
              <div class="text-xs font-mono text-slate-400">Nhạc nền & Bản quyền âm thanh</div>
              <div class="text-xs text-slate-300">
                Nhạc thiền 432Hz (-22dB) + Tiếng chuông xoay chánh niệm (Bản quyền Ma Tọa Thiền).
              </div>
            </div>

            <!-- Master Button: Copy toàn bộ Reels Kit -->
            <button
              type="button"
              class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:brightness-110 text-white font-mono text-xs font-bold transition-all shadow-lg shadow-purple-950/40 flex items-center justify-center gap-2"
              @click="copyToClipboard(reelsFullKit, 'reels-full')"
            >
              <Icons name="Copy" :size="14" />
              <span>{{ copyFeedback['reels-full'] ? '✓ Đã sao chép Toàn Bộ Reels Kit!' : '⚡ Sao Chép Toàn Bộ FB/IG Reels Kit' }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- TAB 4: WEBSITE EMBED & SCRIPTS (HTML Iframe & Kịch Bản)  -->
      <!-- ======================================================== -->
      <div v-else-if="activePlatform === 'website'" class="space-y-6">
        <!-- Section 1: Responsive Iframe Embed Code -->
        <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-3">
          <div class="flex items-center justify-between border-b border-white/5 pb-2.5">
            <div class="flex items-center gap-2">
              <Icons name="Code" :size="18" class="text-phantom-mint" />
              <h3 class="font-display font-bold text-sm text-white">
                Mã Nhúng Trình Phát Video Vào Website (HTML Embed Snippet)
              </h3>
            </div>

            <button
              type="button"
              class="px-3 py-1 rounded-lg bg-phantom-mint/15 hover:bg-phantom-mint hover:text-midnight-950 text-phantom-mint font-mono text-xs font-bold transition-all border border-phantom-mint/30 flex items-center gap-1.5"
              @click="copyToClipboard(embedCodeSnippet, 'embed-code')"
            >
              <Icons name="Copy" :size="12" />
              <span>{{ copyFeedback['embed-code'] ? '✓ Đã sao chép mã!' : 'Sao chép mã nhúng' }}</span>
            </button>
          </div>

          <p class="text-xs text-slate-400 font-sans">
            Dán đoạn mã HTML này vào bất kỳ bài viết hoặc trang web nào để hiển thị trình phát video responsive tỉ lệ 16:9 tự động co giãn.
          </p>

          <pre class="p-4 rounded-xl bg-midnight-950 border border-white/10 overflow-x-auto text-xs font-mono text-emerald-400/90 leading-relaxed">{{ embedCodeSnippet }}</pre>
        </div>

        <!-- Section 2: Full Scripts & Original Content Viewer -->
        <div class="p-5 rounded-2xl glass-panel border border-white/10 space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/5 pb-3">
            <!-- Sub-tabs for scripts -->
            <div class="flex items-center gap-1.5 p-1 rounded-xl bg-midnight-900 border border-white/10 overflow-x-auto no-scrollbar">
              <button
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-mono font-medium transition-all shrink-0"
                :class="activeScriptTab === 'long' ? 'bg-phantom-mint text-midnight-950 font-bold' : 'text-slate-400 hover:text-white'"
                @click="activeScriptTab = 'long'"
              >
                Kịch Bản Dài 16:9 ({{ wordCountLong }} từ)
              </button>
              <button
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-mono font-medium transition-all shrink-0"
                :class="activeScriptTab === 'reel' ? 'bg-phantom-mint text-midnight-950 font-bold' : 'text-slate-400 hover:text-white'"
                @click="activeScriptTab = 'reel'"
              >
                Kịch Bản Reel 9:16 ({{ wordCountReel }} từ)
              </button>
              <button
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-mono font-medium transition-all shrink-0"
                :class="activeScriptTab === 'original' ? 'bg-phantom-mint text-midnight-950 font-bold' : 'text-slate-400 hover:text-white'"
                @click="activeScriptTab = 'original'"
              >
                Bài Viết Gốc Trên Theravāda
              </button>
            </div>

            <!-- Copy button for current active script -->
            <button
              type="button"
              class="text-xs font-mono text-phantom-mint hover:underline flex items-center gap-1 shrink-0"
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
              <span>{{ copyFeedback['activeScript'] ? '✓ Đã sao chép kịch bản!' : 'Sao chép văn bản tab này' }}</span>
            </button>
          </div>

          <!-- Script Content Container -->
          <div class="p-4 rounded-xl bg-midnight-900/80 border border-white/5 max-h-[500px] overflow-y-auto font-sans text-xs sm:text-sm text-slate-300 leading-relaxed">
            <!-- Long Script Tab -->
            <div v-if="activeScriptTab === 'long'" class="whitespace-pre-wrap font-sans">
              <div v-if="article.script_long">{{ article.script_long }}</div>
              <div v-else class="text-slate-500 font-mono text-center py-8">
                Chưa có nội dung kịch bản dài.
              </div>
            </div>

            <!-- Reel Script Tab -->
            <div v-else-if="activeScriptTab === 'reel'" class="whitespace-pre-wrap font-sans">
              <div v-if="article.script_short">{{ article.script_short }}</div>
              <div v-else class="text-slate-500 font-mono text-center py-8">
                Chưa có nội dung kịch bản Reel.
              </div>
            </div>

            <!-- Original Article Tab -->
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
    </div>
  </AdminLayout>
</template>
