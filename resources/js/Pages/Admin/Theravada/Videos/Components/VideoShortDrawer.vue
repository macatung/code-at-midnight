<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import Icons from '@/Components/ui/Icons.vue';

interface Props {
  modelValue: boolean;
  form: any;
  articleTitle?: string;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: false,
  articleTitle: '',
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'save'): void;
}>();

// Active tab state
type TabKey = 'info' | 'media' | 'tiktok' | 'channels' | 'script';
const activeTab = ref<TabKey>('info');

// Preview mode in Media tab: 'poster' or 'video'
const previewMode = ref<'poster' | 'video'>('poster');

// TikTok Safe Zone visual overlay toggle
const showTikTokSafeZone = ref(false);

// Copy feedback helper
const copiedKey = ref<string | null>(null);
const copyText = (text: string, key: string) => {
  if (!text) return;
  navigator.clipboard.writeText(text);
  copiedKey.value = key;
  setTimeout(() => {
    if (copiedKey.value === key) {
      copiedKey.value = null;
    }
  }, 2000);
};

// Word and character counts
const countWords = (text?: string) => {
  if (!text) return 0;
  return text.trim().split(/\s+/).filter(Boolean).length;
};

const countChars = (text?: string) => {
  return text ? text.length : 0;
};

// Algorithmic TikTok Caption Builder (Hook + Core Insight + CTA + Viral Tags)
const tiktokCaption = computed(() => {
  const f = props.form;
  const hook = f.focus_hook ? `✦ ${f.focus_hook.trim()}` : `✦ ${f.title ? f.title.trim() : ''}`;
  const script = f.script ? f.script.trim() : '';
  let body = '';
  if (script) {
    const sentences = script.split(/[.!?]+/).map((s: string) => s.trim()).filter(Boolean);
    body = sentences.slice(0, 2).join('. ') + (sentences.length > 0 ? '.' : '');
  }
  const cta = '🙏 Thả tim & Follow kênh để đón nhận năng lượng bình an mỗi ngày.';
  const tags = '#xuhuong #fyp #phatphap #thien #tamlinh #tritue #loiphatday #taman #theravada #anlac';

  const parts = [hook];
  if (body) parts.push(body);
  parts.push(cta);
  parts.push(tags);
  return parts.join('\n\n');
});

// Download native 9:16 MP4 video for local TikTok upload
const downloadShortVideo = () => {
  const url = props.form.video_url;
  if (!url) return;
  const a = document.createElement('a');
  a.href = url;
  a.download = `tiktok_short_${props.form.id || (props.form.order_index !== undefined ? props.form.order_index + 1 : 'video')}_1080x1920.mp4`;
  a.target = '_blank';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
};

// Open TikTok Creator Studio Web
const openTikTokUpload = () => {
  window.open('https://www.tiktok.com/creator-center/upload', '_blank');
};

// Tab completion and error helpers
const hasErrors = (fields: string[]) => {
  if (!props.form?.errors) return false;
  return fields.some((f) => !!props.form.errors[f]);
};

const tabStatus = computed(() => {
  const f = props.form;
  return {
    info: {
      hasError: hasErrors(['title', 'focus_hook', 'duration', 'order_index']),
      isFilled: !!f.title?.trim(),
    },
    media: {
      hasError: hasErrors(['video_url', 'thumbnail_url']),
      isFilled: !!f.video_url?.trim(),
    },
    tiktok: {
      hasError: hasErrors(['tiktok_url']),
      isFilled: !!f.tiktok_url?.trim(),
    },
    channels: {
      hasError: hasErrors(['youtube_shorts_url', 'tiktok_url', 'reels_url']),
      isFilled: !!(f.youtube_shorts_url?.trim() || f.tiktok_url?.trim() || f.reels_url?.trim()),
    },
    script: {
      hasError: hasErrors(['script', 'description']),
      isFilled: !!(f.script?.trim() || f.description?.trim()),
    },
  };
});


// Close drawer handler
const closeDrawer = () => {
  emit('update:modelValue', false);
};

// Save handler
const triggerSave = () => {
  if (props.form.processing || !props.form.title || !props.form.video_url) {
    if (!props.form.title) activeTab.value = 'info';
    else if (!props.form.video_url) activeTab.value = 'media';
  }
  emit('save');
};

// Keyboard listener: Esc to close, Ctrl/Cmd + S to save
const handleKeyDown = (e: KeyboardEvent) => {
  if (!props.modelValue) return;

  if (e.key === 'Escape') {
    e.preventDefault();
    closeDrawer();
  } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
    e.preventDefault();
    triggerSave();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
  document.body.style.overflow = '';
});

// Manage body scroll lock
watch(
  () => props.modelValue,
  (val) => {
    if (val) {
      document.body.style.overflow = 'hidden';
      // Auto-focus first tab when opening
      activeTab.value = 'info';
    } else {
      document.body.style.overflow = '';
    }
  }
);
</script>

<template>
  <Teleport to="body">
    <!-- Backdrop Overlay -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm"
        @click="closeDrawer"
      />
    </Transition>

    <!-- Slide-over Drawer Panel -->
    <Transition
      enter-active-class="transition duration-300 ease-out transform"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition duration-200 ease-in transform"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <aside
        v-if="modelValue"
        class="fixed inset-y-0 right-0 z-50 w-full sm:w-[620px] lg:w-[680px] bg-slate-900 border-l border-slate-700/80 shadow-2xl flex flex-col overflow-hidden text-slate-100 font-sans"
        aria-labelledby="short-drawer-title"
      >
        <!-- ======================================================== -->
        <!-- HEADER (STICKY)                                          -->
        <!-- ======================================================== -->
        <div class="px-6 py-4.5 border-b border-slate-800 bg-slate-950/90 backdrop-blur shrink-0 flex items-center justify-between gap-4">
          <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 flex items-center justify-center shrink-0 shadow-inner">
              <Icons name="Play" :size="20" class="fill-amber-400/20" />
            </div>
            <div class="min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h2 id="short-drawer-title" class="text-base font-semibold text-white tracking-tight">
                  {{ form.id ? 'Chỉnh Sửa Video Short' : 'Thêm Video Short Mới' }}
                </h2>
                <span
                  v-if="form.order_index !== undefined"
                  class="px-2 py-0.5 rounded-full text-[11px] font-mono font-medium bg-slate-800 text-slate-300 border border-slate-700"
                >
                  #{{ form.order_index + 1 }}
                </span>
              </div>
              <p class="text-xs text-amber-300/80 font-mono flex items-center gap-1 mt-0.5 truncate">
                <Icons name="Sparkles" :size="12" class="text-amber-400 shrink-0" />
                <span>Chuẩn Invariant 12: &ge; 70% hình ảnh Đức Phật quang minh</span>
              </p>
            </div>
          </div>

          <div class="flex items-center gap-1.5 shrink-0">
            <button
              type="button"
              @click="closeDrawer"
              title="Đóng (Esc)"
              class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 border border-transparent hover:border-slate-700 transition-all"
            >
              <Icons name="X" :size="18" />
            </button>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- TAB NAVIGATION (STICKY SUB-HEADER)                       -->
        <!-- ======================================================== -->
        <nav class="px-4 bg-slate-950 border-b border-slate-800/80 shrink-0 flex items-center gap-1 overflow-x-auto no-scrollbar">
          <!-- Tab 1: Thông tin & Style -->
          <button
            type="button"
            @click="activeTab = 'info'"
            class="px-3.5 py-3 text-xs sm:text-sm font-medium flex items-center gap-2 border-b-2 transition-all whitespace-nowrap"
            :class="[
              activeTab === 'info'
                ? 'text-amber-300 border-amber-400 bg-amber-500/10'
                : 'text-slate-400 border-transparent hover:text-slate-200 hover:bg-slate-800/40'
            ]"
          >
            <Icons name="FileText" :size="15" />
            <span>Thông Tin & Style</span>
            <span
              v-if="tabStatus.info.hasError"
              class="w-2 h-2 rounded-full bg-rose-500"
              title="Có lỗi nhập liệu"
            />
            <span
              v-else-if="tabStatus.info.isFilled"
              class="w-1.5 h-1.5 rounded-full bg-emerald-400"
            />
          </button>

          <!-- Tab 2: Media CDN & Preview -->
          <button
            type="button"
            @click="activeTab = 'media'"
            class="px-3.5 py-3 text-xs sm:text-sm font-medium flex items-center gap-2 border-b-2 transition-all whitespace-nowrap"
            :class="[
              activeTab === 'media'
                ? 'text-amber-300 border-amber-400 bg-amber-500/10'
                : 'text-slate-400 border-transparent hover:text-slate-200 hover:bg-slate-800/40'
            ]"
          >
            <Icons name="Video" :size="15" />
            <span>Media & Preview</span>
            <span
              v-if="tabStatus.media.hasError"
              class="w-2 h-2 rounded-full bg-rose-500"
              title="Có lỗi nhập liệu"
            />
            <span
              v-else-if="tabStatus.media.isFilled"
              class="w-1.5 h-1.5 rounded-full bg-emerald-400"
            />
          </button>

          <!-- Tab 3: TikTok Studio -->
          <button
            type="button"
            @click="activeTab = 'tiktok'"
            class="px-3.5 py-3 text-xs sm:text-sm font-medium flex items-center gap-2 border-b-2 transition-all whitespace-nowrap"
            :class="[
              activeTab === 'tiktok'
                ? 'text-cyan-300 border-cyan-400 bg-cyan-500/10'
                : 'text-slate-400 border-transparent hover:text-slate-200 hover:bg-slate-800/40'
            ]"
          >
            <span class="w-2 h-2 rounded-full bg-cyan-400" />
            <span>TikTok Studio</span>
            <span
              v-if="form.tiktok_url"
              class="w-1.5 h-1.5 rounded-full bg-cyan-400"
              title="Đã có link TikTok"
            />
          </button>

          <!-- Tab 4: Kênh Xuất Bản -->
          <button
            type="button"
            @click="activeTab = 'channels'"
            class="px-3.5 py-3 text-xs sm:text-sm font-medium flex items-center gap-2 border-b-2 transition-all whitespace-nowrap"
            :class="[
              activeTab === 'channels'
                ? 'text-amber-300 border-amber-400 bg-amber-500/10'
                : 'text-slate-400 border-transparent hover:text-slate-200 hover:bg-slate-800/40'
            ]"
          >
            <Icons name="ExternalLink" :size="15" />
            <span>Kênh Xuất Bản</span>
            <span
              v-if="tabStatus.channels.isFilled"
              class="w-1.5 h-1.5 rounded-full bg-emerald-400"
            />
          </button>

          <!-- Tab 4: Kịch Bản & SEO -->
          <button
            type="button"
            @click="activeTab = 'script'"
            class="px-3.5 py-3 text-xs sm:text-sm font-medium flex items-center gap-2 border-b-2 transition-all whitespace-nowrap"
            :class="[
              activeTab === 'script'
                ? 'text-amber-300 border-amber-400 bg-amber-500/10'
                : 'text-slate-400 border-transparent hover:text-slate-200 hover:bg-slate-800/40'
            ]"
          >
            <Icons name="AlignLeft" :size="15" />
            <span>Kịch Bản & SEO</span>
            <span
              v-if="tabStatus.script.isFilled"
              class="w-1.5 h-1.5 rounded-full bg-emerald-400"
            />
          </button>
        </nav>

        <!-- ======================================================== -->
        <!-- BODY CONTENT (SCROLLABLE BLOCKS)                         -->
        <!-- ======================================================== -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-900/60">
          <!-- ==================================================== -->
          <!-- BLOCK 1: THÔNG TIN & STYLE                          -->
          <!-- ==================================================== -->
          <div v-show="activeTab === 'info'" class="space-y-5 animate-in fade-in-50 duration-200">
            <!-- Block Card 1: Tiêu đề & Focus Hook -->
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-2xl p-5 shadow-lg space-y-4">
              <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-400/90 font-mono flex items-center gap-1.5">
                  <Icons name="Sparkles" :size="13" />
                  Nội Dung Nhận Diện
                </span>
                <span class="text-[11px] text-slate-400 font-mono">Bắt buộc nhập tiêu đề</span>
              </div>

              <!-- Tiêu Đề Short -->
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-200">
                  Tiêu Đề Short <span class="text-rose-400 font-bold">*</span>
                </label>
                <input
                  v-model="form.title"
                  type="text"
                  placeholder="VD: Short 1: Lời Phật Khai Thị Về Ngã Mạn"
                  class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 transition-all font-sans"
                />
                <p v-if="form.errors.title" class="text-rose-400 text-xs mt-1 flex items-center gap-1">
                  <span>⚠</span> {{ form.errors.title }}
                </p>
              </div>

              <!-- Điểm Nhấn / Focus Hook -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                  <label class="block text-sm font-medium text-slate-200">
                    Điểm Nhấn / Focus Hook
                  </label>
                  <span class="text-slate-400 text-[11px]">Câu giật tít 3 giây đầu</span>
                </div>
                <input
                  v-model="form.focus_hook"
                  type="text"
                  placeholder="VD: Ta Đã Dừng Lại, Chỉ Có Ngươi Chưa Dừng"
                  class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 transition-all font-sans"
                />
              </div>
            </div>

            <!-- Block Card 2: Quy chuẩn & Tham số hiển thị -->
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-2xl p-5 shadow-lg space-y-4">
              <div class="pb-2 border-b border-slate-800">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-400/90 font-mono flex items-center gap-1.5">
                  <Icons name="Clock" :size="13" />
                  Quy Chuẩn & Thứ Tự Hiển Thị
                </span>
              </div>

              <!-- Visual Style (Invariant 12) -->
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-200">
                  Visual Style (Invariant 12)
                </label>
                <div class="relative">
                  <select
                    v-model="form.visual_style"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-amber-200 font-medium text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 transition-all cursor-pointer appearance-none pr-10"
                  >
                    <option value="Buddha Majestic Golden Glow">Buddha Majestic Golden Glow (Hào quang hoàng kim)</option>
                    <option value="Zen Minimalist Cinematic">Zen Minimalist Cinematic (Điện ảnh thiền định tối giản)</option>
                    <option value="Dhamma Wheel & Sangha">Dhamma Wheel & Sangha (Bánh xe Chánh Pháp & Tăng đoàn)</option>
                    <option value="Mindfulness Practice 30s">Mindfulness Practice 30s (Thực hành chánh niệm 30s)</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <Icons name="ChevronDown" :size="16" />
                  </div>
                </div>
              </div>

              <!-- Grid 2 cột: Thời Lượng & Thứ Tự -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                  <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-slate-200">
                      Thời Lượng
                    </label>
                    <span class="text-[11px] font-mono text-slate-400">mm:ss</span>
                  </div>
                  <input
                    v-model="form.duration"
                    type="text"
                    placeholder="00:34"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm font-mono focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 transition-all"
                  />
                </div>

                <div class="space-y-1.5">
                  <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-slate-200">
                      Thứ Tự Hiển Thị
                    </label>
                    <span class="text-[11px] font-mono text-slate-400">Index #</span>
                  </div>
                  <input
                    v-model.number="form.order_index"
                    type="number"
                    min="0"
                    placeholder="0"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm font-mono focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 transition-all"
                  />
                </div>
              </div>

              <!-- Gợi ý Invariant 12 -->
              <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-200 text-xs leading-relaxed flex items-start gap-2.5">
                <Icons name="Info" :size="16" class="text-amber-400 shrink-0 mt-0.5" />
                <span>
                  <strong>Quy ước Invariant 12:</strong> Video Short luôn hiển thị hình tượng Đức Phật tối thiểu 70% thời lượng với ánh hào quang trang nghiêm, giúp người xem lắng tâm tĩnh tại trong 30–60 giây.
                </span>
              </div>
            </div>
          </div>

          <!-- ==================================================== -->
          <!-- BLOCK 2: MEDIA CDN & PREVIEW                        -->
          <!-- ==================================================== -->
          <div v-show="activeTab === 'media'" class="space-y-5 animate-in fade-in-50 duration-200">
            <!-- URL Inputs Card -->
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-2xl p-5 shadow-lg space-y-4">
              <div class="pb-2 border-b border-slate-800 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-400/90 font-mono flex items-center gap-1.5">
                  <Icons name="Video" :size="13" />
                  Đường Dẫn CDN Lưu Trữ
                </span>
                <span class="text-[11px] text-slate-400 font-mono">Chuẩn MP4 dọc 9:16</span>
              </div>

              <!-- Video MP4 9:16 CDN URL -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                  <label class="block text-sm font-medium text-slate-200">
                    Video MP4 9:16 CDN URL <span class="text-rose-400 font-bold">*</span>
                  </label>
                  <a
                    v-if="form.video_url"
                    :href="form.video_url"
                    target="_blank"
                    rel="noopener"
                    class="text-amber-400 hover:text-amber-300 text-xs font-mono flex items-center gap-1 hover:underline"
                  >
                    <span>Mở link gốc</span>
                    <Icons name="ExternalLink" :size="12" />
                  </a>
                </div>
                <input
                  v-model="form.video_url"
                  type="text"
                  placeholder="https://storage.googleapis.com/.../short.mp4"
                  class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm font-mono focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 transition-all"
                />
                <p v-if="form.errors.video_url" class="text-rose-400 text-xs mt-1 flex items-center gap-1">
                  <span>⚠</span> {{ form.errors.video_url }}
                </p>
              </div>

              <!-- Thumbnail Poster 9:16 URL -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                  <label class="block text-sm font-medium text-slate-200">
                    Thumbnail Poster 9:16 URL
                  </label>
                  <a
                    v-if="form.thumbnail_url"
                    :href="form.thumbnail_url"
                    target="_blank"
                    rel="noopener"
                    class="text-amber-400 hover:text-amber-300 text-xs font-mono flex items-center gap-1 hover:underline"
                  >
                    <span>Mở link gốc</span>
                    <Icons name="ExternalLink" :size="12" />
                  </a>
                </div>
                <input
                  v-model="form.thumbnail_url"
                  type="text"
                  placeholder="https://storage.googleapis.com/.../short_poster.jpg"
                  class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm font-mono focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 transition-all"
                />
              </div>
            </div>

            <!-- Live 9:16 Preview Card -->
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-2xl p-5 shadow-lg space-y-4">
              <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-400/90 font-mono flex items-center gap-1.5">
                  <Icons name="Play" :size="13" />
                  Khung Xem Trước Trực Quan (Tỷ Lệ 9:16)
                </span>

                <!-- Switch Preview Mode & Safe Zone -->
                <div class="flex items-center gap-2 flex-wrap justify-end">
                  <!-- TikTok Safe Zone Toggle Button -->
                  <button
                    type="button"
                    @click="showTikTokSafeZone = !showTikTokSafeZone"
                    class="px-2.5 py-1 rounded-lg text-xs font-mono font-medium transition-all flex items-center gap-1.5 border"
                    :class="[
                      showTikTokSafeZone
                        ? 'bg-cyan-500/20 text-cyan-300 border-cyan-400/50 shadow-sm shadow-cyan-500/20'
                        : 'bg-slate-950 text-slate-400 border-slate-800 hover:text-slate-200'
                    ]"
                    title="Bật/Tắt hiển thị vùng an toàn và các nút bấm giao diện TikTok trên màn hình 9:16"
                  >
                    <Icons name="ShieldCheck" :size="13" :class="showTikTokSafeZone ? 'text-cyan-400' : 'text-slate-500'" />
                    <span>TikTok Safe Zone: {{ showTikTokSafeZone ? 'BẬT' : 'TẮT' }}</span>
                  </button>

                  <div class="flex items-center p-1 rounded-xl bg-slate-950 border border-slate-800">
                    <button
                      type="button"
                      @click="previewMode = 'poster'"
                      class="px-3 py-1 rounded-lg text-xs font-medium transition-all"
                      :class="[
                        previewMode === 'poster'
                          ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                          : 'text-slate-400 hover:text-white'
                      ]"
                    >
                      Poster
                    </button>
                    <button
                      type="button"
                      @click="previewMode = 'video'"
                      class="px-3 py-1 rounded-lg text-xs font-medium transition-all"
                      :class="[
                        previewMode === 'video'
                          ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                          : 'text-slate-400 hover:text-white'
                      ]"
                    >
                      Video MP4
                    </button>
                  </div>
                </div>
              </div>

              <!-- Preview Display Area -->
              <div class="flex flex-col sm:flex-row items-center justify-center gap-6 p-4 rounded-xl bg-slate-950/80 border border-slate-800/80 min-h-[360px]">
                <!-- Phone Frame Container (9:16 ratio) -->
                <div class="w-[210px] aspect-[9/16] rounded-2xl bg-black border-2 border-slate-700/80 shadow-2xl relative overflow-hidden flex flex-col justify-between shrink-0 select-none">
                  <!-- Mode 1: Video Player -->
                  <template v-if="previewMode === 'video'">
                    <video
                      v-if="form.video_url"
                      :src="form.video_url"
                      :poster="form.thumbnail_url || undefined"
                      controls
                      playsinline
                      class="w-full h-full object-cover"
                    >
                      Trình duyệt không hỗ trợ thẻ video.
                    </video>
                    <div
                      v-else
                      class="w-full h-full flex flex-col items-center justify-center p-4 text-center text-slate-500 space-y-2"
                    >
                      <Icons name="Video" :size="32" class="text-slate-600" />
                      <p class="text-xs">Chưa có đường dẫn video</p>
                      <p class="text-[10px] text-slate-600 font-mono">Dán link MP4 ở trên để phát thử</p>
                    </div>
                  </template>

                  <!-- Mode 2: Thumbnail Poster -->
                  <template v-else>
                    <img
                      v-if="form.thumbnail_url"
                      :src="form.thumbnail_url"
                      alt="Short Thumbnail Poster"
                      class="w-full h-full object-cover"
                    />
                    <div
                      v-else
                      class="w-full h-full flex flex-col items-center justify-center p-4 text-center text-slate-500 space-y-2 bg-slate-950"
                    >
                      <Icons name="Sparkles" :size="32" class="text-amber-500/40" />
                      <p class="text-xs">Chưa có Thumbnail</p>
                      <p class="text-[10px] text-slate-600 font-mono">Dán link poster để kiểm tra tỷ lệ 9:16</p>
                    </div>
                  </template>

                  <!-- ============================================== -->
                  <!-- TikTok UI Safe Zone Simulation Overlay         -->
                  <!-- ============================================== -->
                  <div
                    v-if="showTikTokSafeZone"
                    class="absolute inset-0 pointer-events-none z-20 flex flex-col justify-between text-white font-sans"
                  >
                    <!-- Top Bar (~14% Height) -->
                    <div class="pt-2 px-2.5 pb-1 bg-gradient-to-b from-black/80 to-transparent flex items-center justify-between text-[8px] font-semibold text-slate-300 border-b border-dashed border-red-500/40">
                      <div class="flex items-center gap-1 text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse" />
                        <span class="text-[7px]">LIVE</span>
                      </div>
                      <div class="flex items-center gap-1.5 text-[8px]">
                        <span class="opacity-50">Bạn bè</span>
                        <span class="opacity-50">Follow</span>
                        <span class="text-white font-bold underline decoration-white decoration-1 underline-offset-2">Dành cho bạn</span>
                      </div>
                      <Icons name="Search" :size="10" class="opacity-70" />
                    </div>

                    <!-- Center Safe Box -->
                    <div class="flex-1 mx-2 my-1 border border-dashed border-emerald-400/80 bg-emerald-400/[0.04] rounded-lg relative flex flex-col justify-end p-1.5">
                      <span class="absolute top-1 left-1 px-1 py-0.5 rounded bg-emerald-500/90 text-black text-[6.5px] font-bold font-mono uppercase tracking-wider">
                        Vùng An Toàn TikTok
                      </span>
                    </div>

                    <!-- Right Floating Actions -->
                    <div class="absolute right-1.5 bottom-12 flex flex-col items-center gap-2 z-30">
                      <!-- Avatar with (+) -->
                      <div class="relative w-6 h-6 rounded-full border border-white bg-slate-800 flex items-center justify-center text-[7px] text-amber-300 font-bold shadow">
                        <span>☸</span>
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2.5 h-2.5 rounded-full bg-rose-500 text-white flex items-center justify-center text-[7px] leading-none font-bold">+</span>
                      </div>
                      <!-- Like -->
                      <div class="flex flex-col items-center">
                        <Icons name="Heart" :size="12" class="text-white fill-white/80" />
                        <span class="text-[6.5px] font-bold mt-0.5">142K</span>
                      </div>
                      <!-- Comment -->
                      <div class="flex flex-col items-center">
                        <Icons name="MessageCircle" :size="12" class="text-white" />
                        <span class="text-[6.5px] font-bold mt-0.5">1.2K</span>
                      </div>
                      <!-- Bookmark -->
                      <div class="flex flex-col items-center">
                        <Icons name="Bookmark" :size="12" class="text-white" />
                        <span class="text-[6.5px] font-bold mt-0.5">35K</span>
                      </div>
                      <!-- Share -->
                      <div class="flex flex-col items-center">
                        <Icons name="Share2" :size="12" class="text-white" />
                        <span class="text-[6.5px] font-bold mt-0.5">8.9K</span>
                      </div>
                      <!-- Spinning Disc -->
                      <div class="w-5 h-5 rounded-full bg-black/80 border border-slate-700 flex items-center justify-center text-[6px] text-slate-300 animate-spin">
                        <span>♫</span>
                      </div>
                    </div>

                    <!-- Bottom Metadata Bar (~22% Height) -->
                    <div class="p-2 pb-1.5 bg-gradient-to-t from-black/90 via-black/60 to-transparent border-t border-dashed border-red-500/40 space-y-0.5 max-w-[78%]">
                      <p class="text-[8px] font-bold text-white tracking-tight flex items-center gap-1">
                        <span>@phapthoai.theravada</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400" />
                      </p>
                      <p class="text-[7.5px] text-slate-200 line-clamp-2 leading-tight">
                        {{ form.focus_hook || form.title || 'Ý thức đi về đâu khi hơi thở dừng lại?' }} #xuhuong #phatphap
                      </p>
                      <p class="text-[6.5px] text-slate-400 flex items-center gap-1 truncate pt-0.5 font-mono">
                        <Icons name="Music" :size="7" class="text-slate-300 shrink-0" />
                        <span>♫ Âm thanh gốc - Thiền 432Hz</span>
                      </p>
                    </div>
                  </div>

                  <!-- Regular Overlay when Safe Zone toggle is OFF -->
                  <div
                    v-else
                    class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/90 via-black/50 to-transparent pointer-events-none space-y-1"
                  >
                    <span class="inline-block px-1.5 py-0.5 rounded bg-amber-500/80 text-black text-[9px] font-bold font-mono uppercase tracking-wider">
                      {{ form.visual_style ? 'Invariant 12' : 'Short' }}
                    </span>
                    <p class="text-[11px] font-semibold text-white line-clamp-2 leading-tight">
                      {{ form.title || 'Tiêu đề video short...' }}
                    </p>
                    <p v-if="form.focus_hook" class="text-[9px] text-amber-300 font-mono line-clamp-1">
                      {{ form.focus_hook }}
                    </p>
                  </div>
                </div>

                <!-- Preview Info Notes -->
                <div class="flex-1 space-y-3 text-xs text-slate-300">
                  <h4 class="font-semibold text-sm text-white flex items-center gap-1.5">
                    <Icons name="Sparkles" :size="15" class="text-amber-400" />
                    Tiêu Chuẩn Định Dạng 9:16
                  </h4>
                  <ul class="space-y-2 text-slate-300 list-disc list-inside text-xs leading-relaxed">
                    <li>Độ phân giải khuyến nghị: <strong>1080 x 1920 px</strong>.</li>
                    <li>Định dạng video: <strong>MP4 (H.264 / AAC)</strong>, bitrate tối ưu &le; 12 Mbps.</li>
                    <li>Thời lượng lý tưởng: <strong>30s – 58s</strong> để tối đa tỷ lệ giữ chân (Retention).</li>
                    <li>Vùng an toàn (Safe Zone): Chừa lề trên 15% và lề dưới 20% để không bị che bởi thanh điều hướng TikTok/Shorts.</li>
                  </ul>
                  <div class="pt-2">
                    <button
                      type="button"
                      @click="previewMode = previewMode === 'poster' ? 'video' : 'poster'"
                      class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-mono transition-colors flex items-center gap-1.5 border border-slate-700"
                    >
                      <Icons :name="previewMode === 'poster' ? 'Video' : 'Play'" :size="13" />
                      <span>Chuyển sang xem {{ previewMode === 'poster' ? 'Video Player' : 'Ảnh Poster' }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ==================================================== -->
          <!-- BLOCK 3: TIKTOK STUDIO (INVARIANT 13)                -->
          <!-- ==================================================== -->
          <div v-show="activeTab === 'tiktok'" class="space-y-5 animate-in fade-in-50 duration-200">
            <!-- Hero Action Card: Tải Video & Mở TikTok Studio -->
            <div class="bg-gradient-to-r from-cyan-950/40 via-slate-900 to-slate-900 border border-cyan-500/30 rounded-2xl p-5 shadow-xl space-y-4">
              <div class="flex items-center justify-between pb-2 border-b border-cyan-500/20">
                <span class="text-xs font-semibold uppercase tracking-wider text-cyan-300 font-mono flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse" />
                  TikTok Fast Publishing Studio • Invariant 13
                </span>
                <span
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider border"
                  :class="[
                    form.tiktok_url
                      ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40'
                      : 'bg-amber-500/10 text-amber-300 border-amber-500/30'
                  ]"
                >
                  {{ form.tiktok_url ? 'ĐÃ ĐĂNG TIKTOK' : 'CHƯA ĐĂNG TIKTOK' }}
                </span>
              </div>

              <p class="text-xs text-slate-300 leading-relaxed">
                Tối ưu hóa toàn diện cho thuật toán phân phối TikTok: Tải nhanh video MP4 chuẩn tỷ lệ 9:16, tự động tạo Caption có Hook 3s kèm Call-To-Action và hệ thống Hashtags thịnh hành.
              </p>

              <!-- Quick Action Buttons -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <!-- Button 1: Tải Video MP4 -->
                <button
                  type="button"
                  @click="downloadShortVideo"
                  :disabled="!form.video_url"
                  class="px-4 py-2.5 rounded-xl bg-cyan-500/15 hover:bg-cyan-500/25 border border-cyan-400/40 text-cyan-200 hover:text-white text-xs font-semibold flex items-center justify-center gap-2 transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-sm shadow-cyan-500/10"
                >
                  <Icons name="Download" :size="15" class="text-cyan-400" />
                  <span>Tải Video MP4 Đăng TikTok</span>
                </button>

                <!-- Button 2: Mở TikTok Creator Studio Web -->
                <button
                  type="button"
                  @click="openTikTokUpload"
                  class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 hover:text-white text-xs font-semibold flex items-center justify-center gap-2 transition-all"
                >
                  <Icons name="ExternalLink" :size="15" class="text-cyan-400" />
                  <span>Mở TikTok Web Upload Studio</span>
                </button>
              </div>
            </div>

            <!-- Card 2: 1-Click Algorithmic TikTok Caption -->
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-2xl p-5 shadow-lg space-y-3">
              <div class="flex items-center justify-between pb-2 border-b border-slate-800 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                  <span class="text-xs font-semibold uppercase tracking-wider text-cyan-400 font-mono flex items-center gap-1.5">
                    <Icons name="Sparkles" :size="13" />
                    Caption TikTok Tạo Sẵn (Chuẩn Thuật Toán 2026)
                  </span>
                  <span class="text-[11px] font-mono text-cyan-300/80 bg-cyan-400/10 px-2 py-0.5 rounded border border-cyan-400/20">
                    {{ countChars(tiktokCaption) }}/2,200 ký tự (Chuẩn vàng: 150–300)
                  </span>
                </div>

                <button
                  type="button"
                  @click="copyText(tiktokCaption, 'tiktok-caption')"
                  class="px-3 py-1 rounded-lg bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-200 text-xs font-mono flex items-center gap-1.5 transition-all border border-cyan-400/30 font-semibold"
                >
                  <Icons :name="copiedKey === 'tiktok-caption' ? 'Check' : 'Copy'" :size="13" />
                  <span>{{ copiedKey === 'tiktok-caption' ? 'Đã sao chép!' : 'Sao chép 1-Click' }}</span>
                </button>
              </div>

              <!-- Display Preview Box -->
              <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 font-sans text-xs leading-relaxed whitespace-pre-line space-y-2">
                {{ tiktokCaption }}
              </div>

              <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono pt-1">
                <span class="flex items-center gap-1 text-emerald-400">
                  <Icons name="Check" :size="12" />
                  <span>Đã tích hợp Hook 3s + Lời dạy cốt lõi + CTA + Hashtags viral</span>
                </span>
                <button
                  type="button"
                  @click="copyText(tiktokCaption, 'tiktok-caption-sec')"
                  class="text-cyan-400 hover:text-cyan-300 hover:underline flex items-center gap-1"
                >
                  <Icons name="Copy" :size="11" />
                  <span>Chép vào Clipboard</span>
                </button>
              </div>
            </div>

            <!-- Card 3: TikTok URL & Status -->
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-2xl p-5 shadow-lg space-y-3">
              <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-200 font-mono flex items-center gap-1.5">
                  <Icons name="ExternalLink" :size="13" class="text-cyan-400" />
                  Liên Kết Video TikTok Đã Xuất Bản
                </span>
                <a
                  v-if="form.tiktok_url"
                  :href="form.tiktok_url"
                  target="_blank"
                  rel="noopener"
                  class="text-cyan-400 hover:text-cyan-300 text-xs font-mono flex items-center gap-1 hover:underline"
                >
                  <span>Mở xem trên TikTok</span>
                  <Icons name="ExternalLink" :size="12" />
                </a>
              </div>

              <div class="space-y-1.5">
                <label class="block text-xs font-medium text-slate-300">
                  TikTok Video URL:
                </label>
                <div class="flex items-center gap-2">
                  <input
                    v-model="form.tiktok_url"
                    type="text"
                    placeholder="https://www.tiktok.com/@kenh_phat_phap/video/..."
                    class="flex-1 px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm font-mono focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 transition-all"
                  />
                  <button
                    type="button"
                    @click="triggerSave"
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-semibold transition-all shrink-0"
                  >
                    Lưu Link
                  </button>
                </div>
              </div>
            </div>

            <!-- Card 4: TikTok Checklist Invariant 13 -->
            <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-4.5 space-y-2.5">
              <h5 class="text-xs font-semibold uppercase tracking-wider text-amber-300 font-mono flex items-center gap-1.5">
                <Icons name="Sparkles" :size="13" />
                Bộ Quy Chuẩn Video Ngắn TikTok (Invariant 13)
              </h5>
              <ul class="space-y-1.5 text-xs text-slate-300">
                <li class="flex items-center gap-2">
                  <span class="text-emerald-400 shrink-0">✓</span>
                  <span><strong>Tỷ lệ 9:16 dọc bản địa (1080x1920)</strong>: Không dùng dải đen hay ghép mờ trên dưới.</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="text-emerald-400 shrink-0">✓</span>
                  <span><strong>Hook 3 giây đầu</strong>: Câu hỏi hoặc trăn trở nhân sinh lôi cuốn giữ chân người xem lướt qua.</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="text-emerald-400 shrink-0">✓</span>
                  <span><strong>Đức Phật quang minh &ge; 70%</strong>: Hình ảnh Đức Phật uy nghiêm, y vàng rực rỡ làm chủ thể thị giác.</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="text-emerald-400 shrink-0">✓</span>
                  <span><strong>TikTok Safe Zone</strong>: Phụ đề và chủ thể không bị các nút Like, Comment, Share che khuất.</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="text-emerald-400 shrink-0">✓</span>
                  <span><strong>Âm thanh đa tầng 432Hz</strong>: Giọng trầm ấm phối nhạc thiền sâu lắng xoa dịu tâm trí.</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- ==================================================== -->
          <!-- BLOCK 4: KÊNH XUẤT BẢN                               -->
          <!-- ==================================================== -->
          <div v-show="activeTab === 'channels'" class="space-y-5 animate-in fade-in-50 duration-200">
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-2xl p-5 shadow-lg space-y-4">
              <div class="pb-2 border-b border-slate-800 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-400/90 font-mono flex items-center gap-1.5">
                  <Icons name="ExternalLink" :size="13" />
                  Liên Kết Đa Nền Tảng (Publishing URLs)
                </span>
                <span class="text-[11px] text-slate-400 font-mono">Tự động đồng bộ ra ngoài</span>
              </div>

              <p class="text-xs text-slate-300 leading-relaxed">
                Sau khi xuất bản video lên các nền tảng mạng xã hội, dán đường dẫn trực tiếp vào đây để người xem trên trang có thể bấm xem và tương tác nguyên bản.
              </p>

              <!-- YouTube Shorts -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-slate-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500" />
                    <span>YouTube Shorts URL</span>
                  </label>
                  <a
                    v-if="form.youtube_shorts_url"
                    :href="form.youtube_shorts_url"
                    target="_blank"
                    rel="noopener"
                    class="text-red-400 hover:text-red-300 text-xs font-mono flex items-center gap-1 hover:underline"
                  >
                    <span>Mở Shorts</span>
                    <Icons name="ExternalLink" :size="12" />
                  </a>
                </div>
                <input
                  v-model="form.youtube_shorts_url"
                  type="text"
                  placeholder="https://youtube.com/shorts/..."
                  class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm font-mono focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400/30 transition-all"
                />
              </div>

              <!-- TikTok -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-slate-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400" />
                    <span>TikTok Video URL</span>
                  </label>
                  <a
                    v-if="form.tiktok_url"
                    :href="form.tiktok_url"
                    target="_blank"
                    rel="noopener"
                    class="text-cyan-400 hover:text-cyan-300 text-xs font-mono flex items-center gap-1 hover:underline"
                  >
                    <span>Mở TikTok</span>
                    <Icons name="ExternalLink" :size="12" />
                  </a>
                </div>
                <input
                  v-model="form.tiktok_url"
                  type="text"
                  placeholder="https://www.tiktok.com/@.../video/..."
                  class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm font-mono focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 transition-all"
                />
              </div>

              <!-- Facebook / Instagram Reels -->
              <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-slate-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400" />
                    <span>Facebook / Instagram Reels URL</span>
                  </label>
                  <a
                    v-if="form.reels_url"
                    :href="form.reels_url"
                    target="_blank"
                    rel="noopener"
                    class="text-purple-400 hover:text-purple-300 text-xs font-mono flex items-center gap-1 hover:underline"
                  >
                    <span>Mở Reels</span>
                    <Icons name="ExternalLink" :size="12" />
                  </a>
                </div>
                <input
                  v-model="form.reels_url"
                  type="text"
                  placeholder="https://www.facebook.com/reel/..."
                  class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm font-mono focus:outline-none focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 transition-all"
                />
              </div>
            </div>
          </div>

          <!-- ==================================================== -->
          <!-- BLOCK 4: KỊCH BẢN & SEO                              -->
          <!-- ==================================================== -->
          <div v-show="activeTab === 'script'" class="space-y-5 animate-in fade-in-50 duration-200">
            <!-- Narration Script Card -->
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-2xl p-5 shadow-lg space-y-3">
              <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <div class="flex items-center gap-2">
                  <span class="text-xs font-semibold uppercase tracking-wider text-amber-400/90 font-mono flex items-center gap-1.5">
                    <Icons name="AlignLeft" :size="13" />
                    Kịch Bản Đọc / Lời Dẫn Short
                  </span>
                  <span class="text-[11px] font-mono text-slate-400">
                    ({{ countWords(form.script) }} từ • {{ countChars(form.script) }} ký tự)
                  </span>
                </div>

                <button
                  type="button"
                  @click="copyText(form.script, 'script')"
                  :disabled="!form.script"
                  class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-mono flex items-center gap-1 transition-colors disabled:opacity-40 border border-slate-700"
                >
                  <Icons :name="copiedKey === 'script' ? 'Check' : 'Copy'" :size="12" />
                  <span>{{ copiedKey === 'script' ? 'Đã sao chép' : 'Sao chép' }}</span>
                </button>
              </div>

              <textarea
                v-model="form.script"
                rows="6"
                placeholder="Nhập lời thoại dẫn thiền, câu chuyện khai thị hoặc trích dẫn Kinh tạng bằng giọng đọc trang nghiêm..."
                class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 transition-all font-sans leading-relaxed resize-y"
              ></textarea>
            </div>

            <!-- Publishing Description & SEO Card -->
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-2xl p-5 shadow-lg space-y-3">
              <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <div class="flex items-center gap-2">
                  <span class="text-xs font-semibold uppercase tracking-wider text-amber-400/90 font-mono flex items-center gap-1.5">
                    <Icons name="Sparkles" :size="13" />
                    Mô Tả & Hashtags Xuất Bản Tạo Sẵn
                  </span>
                  <span class="text-[11px] font-mono text-slate-400">
                    ({{ countWords(form.description) }} từ • {{ countChars(form.description) }} ký tự)
                  </span>
                </div>

                <button
                  type="button"
                  @click="copyText(form.description, 'description')"
                  :disabled="!form.description"
                  class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-mono flex items-center gap-1 transition-colors disabled:opacity-40 border border-slate-700"
                >
                  <Icons :name="copiedKey === 'description' ? 'Check' : 'Copy'" :size="12" />
                  <span>{{ copiedKey === 'description' ? 'Đã sao chép' : 'Sao chép' }}</span>
                </button>
              </div>

              <textarea
                v-model="form.description"
                rows="7"
                placeholder="Nhập mô tả chi tiết đăng kèm Shorts/Reels/TikTok, lời khuyên thực hành, link bài giảng và hashtag SEO (#phatphap #thien #theravada)..."
                class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder:text-slate-500 text-sm focus:outline-none focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 transition-all font-sans leading-relaxed resize-y"
              ></textarea>

              <p class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                <Icons name="Info" :size="12" class="text-amber-400" />
                <span>Nội dung này được tự động liên kết và cung cấp tại màn hình Publishing Hub của hệ thống.</span>
              </p>
            </div>
          </div>
        </div>

        <!-- ======================================================== -->
        <!-- FOOTER (STICKY)                                          -->
        <!-- ======================================================== -->
        <div class="px-6 py-4 border-t border-slate-800 bg-slate-950/95 backdrop-blur shrink-0 flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="text-xs text-slate-400 font-mono flex items-center gap-1.5 self-start sm:self-center">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse" />
            <span>Short #1 tự động làm video đại diện bài viết.</span>
          </div>

          <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <button
              type="button"
              @click="closeDrawer"
              class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition-all border border-slate-700"
            >
              Hủy (Esc)
            </button>
            <button
              type="button"
              @click="triggerSave"
              :disabled="form.processing || !form.title || !form.video_url"
              class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-bold text-xs transition-all shadow-lg shadow-amber-500/20 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <Icons v-if="form.processing" name="Activity" :size="14" class="animate-spin" />
              <span>{{ form.processing ? 'Đang lưu...' : 'Lưu Video Short (Ctrl+S)' }}</span>
            </button>
          </div>
        </div>
      </aside>
    </Transition>
  </Teleport>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
