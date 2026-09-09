<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import DecodeLayout from '@/Layouts/DecodeLayout.vue';
import Icons from '@/Components/ui/Icons.vue';

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
  summary: string;
  hook: string;
  takeaways?: string[];
  tags: string[];
  status: string;
  key_nodes: KeyNode[];
}

const props = defineProps<{
  episodes: Episode[];
  featuredEpisode: Episode;
  channel: {
    name: string;
    handle: string;
    subdomain: string;
    tagline: string;
    sub_tagline: string;
    video_format: string;
    pilot_season_episodes_count: number;
  };
}>();

// Active step in interactive visualizer (0-indexed)
const activeStepIndex = ref(0);

const selectStep = (index: number) => {
  activeStepIndex.value = index;
};

const nextStep = () => {
  activeStepIndex.value = (activeStepIndex.value + 1) % props.featuredEpisode.key_nodes.length;
};

const prevStep = () => {
  activeStepIndex.value = (activeStepIndex.value - 1 + props.featuredEpisode.key_nodes.length) % props.featuredEpisode.key_nodes.length;
};

// Article Filtering & Search
const searchQuery = ref('');
const selectedCategory = ref('ALL');

const categories = [
  { id: 'ALL', label: 'Tất Cả Bài Viết' },
  { id: 'Fintech & Payment Systems', label: 'Fintech & Thanh Toán' },
  { id: 'Search Infrastructure & Distributed Systems', label: 'Hạ Tầng Tìm Kiếm' },
  { id: 'Telecommunications & Submarine Cables', label: 'Cáp Quang Biển' },
  { id: 'Banking Hardware & Distributed Consensus', label: 'Phần Cứng ATM & Đồng Thuận' },
  { id: 'Relativistic Physics & Satellite Systems', label: 'Vệ Tinh GPS & Einstein' },
];

const filteredEpisodes = computed(() => {
  return props.episodes.filter((ep) => {
    const matchesCategory = selectedCategory.value === 'ALL' || ep.category === selectedCategory.value;
    const query = searchQuery.value.trim().toLowerCase();
    const matchesQuery = !query || 
      ep.title.toLowerCase().includes(query) ||
      ep.short_title.toLowerCase().includes(query) ||
      ep.hook.toLowerCase().includes(query) ||
      ep.summary.toLowerCase().includes(query) ||
      ep.tags.some((t) => t.toLowerCase().includes(query));
    return matchesCategory && matchesQuery;
  });
});
</script>

<template>
  <DecodeLayout
    :title="channel.name + ' — ' + channel.tagline"
    :description="channel.sub_tagline"
  >
    <!-- PUBLICATION MASTHEAD & HERO -->
    <section class="relative pt-12 pb-16 sm:pt-16 sm:pb-20 border-b border-slate-800/80">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-6 text-left">
          <!-- Series Kicker Badge -->
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 border border-slate-800 text-xs font-mono text-[#00f5d4]">
            <img src="/brand/decode/decode-badge-avatar.svg" alt="Ma Giải Mã Badge" class="w-3.5 h-3.5 object-contain" />
            <span class="font-semibold tracking-wider uppercase">SERIES: PILOT SEASON 2026</span>
            <span class="text-slate-600">•</span>
            <span class="text-slate-400">5 BÀI KHẢO CỨU KIẾN TRÚC NGẦM</span>
          </div>

          <!-- Main Publication Title -->
          <div class="space-y-3">
            <h1 class="font-display text-4xl sm:text-6xl font-black text-white tracking-tight leading-[1.08]">
              MA GIẢI MÃ
            </h1>
            <p class="text-xl sm:text-2xl font-medium text-slate-300">
              "{{ channel.tagline }}"
            </p>
          </div>

          <!-- Editorial Lead Paragraph -->
          <p class="text-base sm:text-lg text-slate-400 font-sans leading-relaxed">
            Chuyên trang khảo cứu kiến trúc phân tán, giao thức mạng ngầm và phần cứng tài chính bởi
            <strong class="text-white">Ma Cà Tưng (@macatung)</strong>.
            Từ chiếc thẻ Visa quẹt trong 2 giây, đường truyền cáp quang dưới đáy đại dương, cho đến vệ tinh GPS tính toán theo thuyết tương đối của Albert Einstein.
          </p>

          <!-- Editorial Meta Details Bar -->
          <div class="flex flex-wrap items-center gap-y-2 gap-x-6 text-xs font-mono text-slate-400 pt-2 border-t border-slate-800/60">
            <div class="flex items-center gap-1.5">
              <span class="w-1.5 h-1.5 rounded-full bg-[#00f5d4]" />
              <span>Xuất bản: <strong>05 Chuyên Đề Kỹ Thuật</strong></span>
            </div>
            <div>
              Độ dài trung bình: <strong class="text-slate-200">15-20 phút đọc/bài</strong>
            </div>
            <div>
              Mã nguồn &amp; Giao thức: <strong class="text-slate-200">Chuẩn hóa Production</strong>
            </div>
          </div>

          <!-- Quick Navigation Actions -->
          <div class="pt-2 flex flex-wrap items-center gap-3">
            <Link
              :href="`/decode/tap/${featuredEpisode.slug}`"
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#00f5d4] hover:bg-[#00e0c2] text-[#060913] font-bold text-xs tracking-wide transition-all shadow-md shadow-[#00f5d4]/10"
            >
              <span>Đọc Bài Khảo Cứu Nổi Bật (Tập 01)</span>
              <Icons name="ChevronRight" :size="14" />
            </Link>

            <a
              href="#danh-sach-bai-viet"
              class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 text-xs font-medium transition-all"
            >
              <Icons name="FileText" :size="14" class="text-[#00f5d4]" />
              <span>Danh Sách 5 Tập</span>
            </a>

            <a
              href="https://youtube.com/@MaGiaiMa"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-[#ff0054]/10 hover:bg-[#ff0054]/20 border border-[#ff0054]/30 text-[#ff0054] text-xs font-bold font-mono transition-all"
            >
              <Icons name="Play" :size="12" />
              <span>Kênh @MaGiaiMa</span>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION: FEATURED LEAD ARTICLE (TẬP 01 DEEP-DIVE) -->
    <section class="py-12 sm:py-16 border-b border-slate-800/80 bg-[#070b16]">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-left mb-6">
          <span class="text-xs font-mono font-bold tracking-widest uppercase text-[#00f5d4]">
            BÀI KHẢO CỨU NỔI BẬT TRONG SERIES
          </span>
        </div>

        <div class="p-6 sm:p-10 rounded-2xl bg-[#091020] border border-slate-800 hover:border-[#00f5d4]/40 transition-all text-left space-y-6">
          <!-- Article Top Meta -->
          <div class="flex flex-wrap items-center justify-between gap-3 text-xs font-mono">
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-1 rounded bg-[#00f5d4]/15 border border-[#00f5d4]/30 text-[#00f5d4] font-bold">
                TẬP {{ featuredEpisode.episode_number }}
              </span>
              <span class="text-slate-400">
                {{ featuredEpisode.category }}
              </span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
              <span v-if="featuredEpisode.reading_time" class="flex items-center gap-1">
                <Icons name="Clock" :size="13" /> {{ featuredEpisode.reading_time }}
              </span>
              <span>Độ trễ hệ thống: <strong class="text-amber-400">{{ featuredEpisode.total_latency }}</strong></span>
            </div>
          </div>

          <!-- Title -->
          <h2 class="font-display text-2xl sm:text-4xl font-extrabold text-white leading-tight">
            <Link :href="`/decode/tap/${featuredEpisode.slug}`" class="hover:text-[#00f5d4] transition-colors">
              {{ featuredEpisode.title }}
            </Link>
          </h2>

          <!-- Hook & Summary -->
          <p class="text-sm sm:text-base text-slate-300 font-sans leading-relaxed">
            {{ featuredEpisode.hook }}
          </p>

          <!-- Key Takeaways Bullet Box -->
          <div v-if="featuredEpisode.takeaways && featuredEpisode.takeaways.length > 0" class="p-4 rounded-xl bg-[#060a14] border border-slate-800 space-y-2">
            <div class="text-xs font-mono font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
              <Icons name="Terminal" :size="13" class="text-[#00f5d4]" />
              <span>Điểm Cốt Lõi Kiến Trúc (Key Takeaways):</span>
            </div>
            <ul class="space-y-1.5 text-xs text-slate-400 font-sans">
              <li v-for="(point, pIdx) in featuredEpisode.takeaways.slice(0, 3)" :key="pIdx" class="flex items-start gap-2">
                <span class="text-[#00f5d4] font-mono mt-0.5">•</span>
                <span>{{ point }}</span>
              </li>
            </ul>
          </div>

          <!-- Bottom Action Strip -->
          <div class="pt-2 flex flex-wrap items-center justify-between gap-4 border-t border-slate-800/80">
            <div class="flex flex-wrap gap-1.5">
              <span
                v-for="(tag, tIdx) in featuredEpisode.tags"
                :key="tIdx"
                class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-900 text-slate-400 border border-slate-800"
              >
                #{{ tag }}
              </span>
            </div>

            <Link
              :href="`/decode/tap/${featuredEpisode.slug}`"
              class="inline-flex items-center gap-2 text-xs font-mono font-bold text-[#00f5d4] hover:underline"
            >
              <span>Đọc Toàn Bộ Bài Khảo Cứu (15 Phút)</span>
              <Icons name="ChevronRight" :size="14" />
            </Link>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION: ARCHITECTURAL FLOW BLUEPRINT EXPLORER -->
    <section id="anatomy-visualizer" class="py-12 sm:py-16 border-b border-slate-800/80 bg-[#060913]">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-left space-y-2 mb-8">
          <div class="inline-flex items-center gap-2 text-xs font-mono text-[#00f5d4] tracking-wider uppercase font-bold">
            <Icons name="Activity" :size="14" />
            <span>Sơ Đồ Luồng Kỹ Thuật • Episode 01 Flow Anatomy</span>
          </div>
          <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white">
            Giải Phẫu Hành Trình Dữ Liệu: Quẹt Thẻ Visa 100k Trong 1.85s
          </h2>
          <p class="text-slate-400 text-sm font-sans max-w-2xl">
            Chọn từng chốt kiểm soát bên dưới để xem gói tin ISO 8583 được tạo, mã hóa và định tuyến xuyên lục địa như thế nào.
          </p>
        </div>

        <!-- Flow Container -->
        <div class="rounded-xl bg-[#091020] border border-slate-800 p-6 space-y-6 text-left">
          <!-- Step Selector Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 font-mono text-xs">
            <button
              v-for="(node, idx) in featuredEpisode.key_nodes"
              :key="idx"
              type="button"
              class="p-3 rounded-lg border text-left transition-all"
              :class="activeStepIndex === idx ? 'bg-[#00f5d4]/10 border-[#00f5d4] text-white' : 'bg-slate-900/60 border-slate-800 text-slate-400 hover:border-slate-700 hover:text-slate-300'"
              @click="selectStep(idx)"
            >
              <div class="flex items-center justify-between text-[10px] text-slate-500 mb-1">
                <span>0{{ node.step }}</span>
                <span class="text-amber-400">{{ node.time }}</span>
              </div>
              <div class="font-sans font-bold text-xs line-clamp-1">
                {{ node.node }}
              </div>
            </button>
          </div>

          <!-- Active Step Details Card -->
          <div
            v-if="featuredEpisode.key_nodes[activeStepIndex]"
            class="p-5 rounded-lg bg-[#060a14] border border-slate-800 space-y-3"
          >
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 pb-3">
              <div>
                <span class="text-xs font-mono text-[#00f5d4] font-bold mr-2">CHỐT 0{{ featuredEpisode.key_nodes[activeStepIndex].step }}</span>
                <h3 class="inline font-display font-bold text-base sm:text-lg text-white">
                  {{ featuredEpisode.key_nodes[activeStepIndex].node }}
                </h3>
              </div>
              <div class="flex items-center gap-3 font-mono text-xs text-slate-400">
                <span>Thời điểm: <strong class="text-amber-400">{{ featuredEpisode.key_nodes[activeStepIndex].time }}</strong></span>
                <span>•</span>
                <span class="text-[#00f5d4]">{{ featuredEpisode.key_nodes[activeStepIndex].status }}</span>
              </div>
            </div>

            <div class="text-xs font-mono text-slate-400">
              Giao thức: <span class="text-slate-200">{{ featuredEpisode.key_nodes[activeStepIndex].protocol }}</span>
            </div>

            <p class="text-sm text-slate-300 font-sans leading-relaxed">
              {{ featuredEpisode.key_nodes[activeStepIndex].action }}
            </p>

            <div class="pt-2 flex items-center justify-between font-mono text-xs">
              <span class="text-slate-500">Độ trễ phân đoạn: {{ featuredEpisode.key_nodes[activeStepIndex].latency }}</span>
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs"
                  @click="prevStep"
                >
                  ◀ Trước
                </button>
                <button
                  type="button"
                  class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs"
                  @click="nextStep"
                >
                  Sau ▶
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION: DANH SÁCH BÀI VIẾT (SERIES FEED) -->
    <section id="danh-sach-bai-viet" class="py-14 sm:py-20 bg-[#070b16]">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Feed Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4 text-left">
          <div class="space-y-2">
            <span class="text-xs font-mono font-bold tracking-widest uppercase text-[#00f5d4]">
              SERIES PILOT SEASON • TOÀN BỘ 5 BÀI KHẢO CỨU
            </span>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-white">
              Danh Sách Bài Viết Chi Tiết
            </h2>
            <p class="text-slate-400 text-sm font-sans max-w-2xl">
              Mỗi bài viết là một tài liệu khảo cứu sâu, phân tích chi tiết từ vi mạch, giao thức mạng, bản tin dữ liệu đến giải thuật phân tán.
            </p>
          </div>
          <div class="font-mono text-xs text-slate-400">
            Hiển thị <strong class="text-white">{{ filteredEpisodes.length }}</strong> / {{ episodes.length }} bài viết
          </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="p-4 rounded-xl bg-[#091020] border border-slate-800 mb-8 space-y-3">
          <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search Input -->
            <div class="relative flex-1">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                <Icons name="Search" :size="15" />
              </div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Tìm kiếm bài viết theo từ khóa (Visa, ISO 8583, Google, BGP, ATM, Einstein, GPS)..."
                class="w-full pl-9 pr-8 py-2 rounded-lg bg-[#060a14] border border-slate-800 text-white placeholder-slate-500 text-xs font-sans focus:outline-none focus:border-[#00f5d4] transition-all"
              />
              <button
                v-if="searchQuery"
                type="button"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-white"
                @click="searchQuery = ''"
              >
                <Icons name="X" :size="14" />
              </button>
            </div>

            <!-- Reset Filter Button -->
            <button
              v-if="searchQuery || selectedCategory !== 'ALL'"
              type="button"
              class="px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-mono transition-colors whitespace-nowrap"
              @click="searchQuery = ''; selectedCategory = 'ALL'"
            >
              Đặt lại bộ lọc
            </button>
          </div>

          <!-- Category Chips -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs font-mono">
            <button
              v-for="cat in categories"
              :key="cat.id"
              type="button"
              class="px-2.5 py-1 rounded-md whitespace-nowrap transition-all border font-medium"
              :class="selectedCategory === cat.id ? 'bg-[#00f5d4] text-[#060913] border-[#00f5d4]' : 'bg-[#060a14] text-slate-400 border-slate-800 hover:text-white hover:border-slate-700'"
              @click="selectedCategory = cat.id"
            >
              {{ cat.label }}
            </button>
          </div>
        </div>

        <!-- Empty Results Message -->
        <div
          v-if="filteredEpisodes.length === 0"
          class="p-10 rounded-xl bg-[#091020] border border-slate-800 text-center space-y-3 max-w-md mx-auto"
        >
          <div class="text-slate-500">
            <Icons name="Search" :size="24" class="mx-auto" />
          </div>
          <h3 class="font-display font-bold text-white text-base">Không tìm thấy bài viết phù hợp</h3>
          <p class="text-xs text-slate-400 font-sans">
            Thử tìm với từ khóa khác như "Visa", "Google", "Cáp quang", "ATM", hoặc "GPS".
          </p>
          <button
            type="button"
            class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-mono text-white"
            @click="searchQuery = ''; selectedCategory = 'ALL'"
          >
            Hiện lại 5 bài viết
          </button>
        </div>

        <!-- Articles Grid (5 Pilot Season Episodes) -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
          <article
            v-for="ep in filteredEpisodes"
            :key="ep.id"
            class="rounded-xl bg-[#091020] border border-slate-800 hover:border-slate-700 transition-all p-6 flex flex-col justify-between group shadow-lg"
          >
            <div class="space-y-4">
              <!-- Card Top Row -->
              <div class="flex items-center justify-between text-xs font-mono">
                <span class="px-2 py-0.5 rounded bg-slate-800 text-[#00f5d4] font-bold">
                  TẬP {{ ep.episode_number }}
                </span>
                <span class="text-slate-500">
                  {{ ep.reading_time || '15 phút đọc' }}
                </span>
              </div>

              <!-- Title -->
              <h3 class="font-display font-bold text-lg text-white group-hover:text-[#00f5d4] transition-colors line-clamp-2">
                <Link :href="`/decode/tap/${ep.slug}`">
                  {{ ep.title }}
                </Link>
              </h3>

              <!-- Hook / Story Preview -->
              <p class="text-xs text-slate-300 font-sans line-clamp-3 leading-relaxed">
                {{ ep.hook }}
              </p>

              <!-- Category & Summary Pill -->
              <div class="p-3 rounded-lg bg-[#060a14] border border-slate-800/80 space-y-1">
                <div class="text-[10px] font-mono font-bold text-[#00b4d8] uppercase">
                  {{ ep.category }}
                </div>
                <div class="text-xs text-slate-400 font-sans line-clamp-2">
                  {{ ep.summary }}
                </div>
              </div>

              <!-- Tags -->
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="(tag, tIdx) in ep.tags.slice(0, 3)"
                  :key="tIdx"
                  class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-900 text-slate-400 border border-slate-800"
                >
                  #{{ tag }}
                </span>
              </div>
            </div>

            <!-- Card Footer Link -->
            <div class="pt-5 mt-4 border-t border-slate-800/80 space-y-2">
              <div class="flex items-center justify-between text-[11px] font-mono text-slate-400">
                <span>Khảo cứu độ trễ:</span>
                <span class="text-amber-400 font-bold">{{ ep.total_latency }}</span>
              </div>
              <Link
                :href="`/decode/tap/${ep.slug}`"
                class="w-full py-2 rounded-lg bg-slate-900 group-hover:bg-[#00f5d4] group-hover:text-[#060913] border border-slate-800 group-hover:border-[#00f5d4] text-slate-200 text-xs font-mono font-bold flex items-center justify-center gap-1.5 transition-all"
              >
                <span>Đọc Bài Viết Chi Tiết</span>
                <Icons name="ChevronRight" :size="13" class="group-hover:translate-x-0.5 transition-transform" />
              </Link>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- SECTION: SERIES ROADMAP MATRIX TABLE -->
    <section class="py-14 sm:py-16 border-t border-slate-800/80 bg-[#060913]">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-left space-y-6">
        <div>
          <span class="text-xs font-mono font-bold tracking-widest uppercase text-[#00f5d4]">
            TỔNG QUAN HỆ THỐNG
          </span>
          <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white mt-1">
            Bản Đồ Kiến Trúc Pilot Season
          </h2>
          <p class="text-slate-400 text-sm font-sans">
            So sánh các thành phần công nghệ và giao thức cốt lõi được mổ xẻ qua từng chuyên đề.
          </p>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-800 bg-[#091020]">
          <table class="w-full text-left font-sans text-xs">
            <thead class="bg-slate-900/80 font-mono text-slate-400 border-b border-slate-800">
              <tr>
                <th class="py-3 px-4">Tập</th>
                <th class="py-3 px-4">Chuyên Đề</th>
                <th class="py-3 px-4">Giao Thức Trọng Tâm</th>
                <th class="py-3 px-4">Hạ Tầng Vật Lý</th>
                <th class="py-3 px-4">Độ Trễ</th>
                <th class="py-3 px-4 text-right">Hành Động</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
              <tr v-for="ep in episodes" :key="ep.id" class="hover:bg-slate-900/40 transition-colors">
                <td class="py-3 px-4 font-mono font-bold text-[#00f5d4]">
                  {{ ep.episode_number }}
                </td>
                <td class="py-3 px-4 font-semibold text-white">
                  {{ ep.short_title }}
                </td>
                <td class="py-3 px-4 font-mono text-slate-400">
                  {{ ep.tags.slice(0, 2).join(', ') }}
                </td>
                <td class="py-3 px-4 text-slate-400">
                  {{ ep.category }}
                </td>
                <td class="py-3 px-4 font-mono text-amber-400 font-bold">
                  {{ ep.total_latency }}
                </td>
                <td class="py-3 px-4 text-right">
                  <Link :href="`/decode/tap/${ep.slug}`" class="text-xs font-mono text-[#00f5d4] hover:underline">
                    Đọc →
                  </Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- SECTION: ECOSYSTEM TRIO -->
    <section class="py-14 sm:py-16 border-t border-slate-800/80 bg-[#070a14]">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-left space-y-6">
        <div class="space-y-1">
          <span class="text-xs font-mono font-bold tracking-widest uppercase text-slate-400">
            HỆ SINH THÁI THƯƠNG HIỆU
          </span>
          <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white">
            Bộ Ba Bản Sắc macatung.dev
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Card 1: Ma Cà Tưng -->
          <a
            href="https://macatung.dev"
            class="p-5 rounded-xl bg-[#091020] border border-slate-800 hover:border-slate-700 transition-all block group"
          >
            <div class="text-xs font-mono text-emerald-400 font-bold">macatung.dev</div>
            <h3 class="font-display font-bold text-lg text-white mt-1 mb-1">Ma Cà Tưng</h3>
            <p class="text-xs text-slate-400 font-sans leading-relaxed">
              Portfolio lập trình cá nhân, kiến trúc sư phần mềm, AI agents và những dự án code lúc nửa đêm.
            </p>
            <div class="pt-3 text-xs font-mono text-emerald-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
              Xem portfolio ↗
            </div>
          </a>

          <!-- Card 2: Ma Tọa Thiền -->
          <a
            href="https://theravada.macatung.dev"
            class="p-5 rounded-xl bg-[#091020] border border-slate-800 hover:border-slate-700 transition-all block group"
          >
            <div class="text-xs font-mono text-amber-400 font-bold">theravada.macatung.dev</div>
            <h3 class="font-display font-bold text-lg text-white mt-1 mb-1">Ma Tọa Thiền</h3>
            <p class="text-xs text-slate-400 font-sans leading-relaxed">
              Giải mã nội tâm, tìm về tĩnh lặng — Tam Tạng Kinh Điển Pāḷi, thiền quán Vipassanā và Vi Diệu Pháp.
            </p>
            <div class="pt-3 text-xs font-mono text-amber-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
              Xem kinh điển ↗
            </div>
          </a>

          <!-- Card 3: Ma Giải Mã -->
          <div class="p-5 rounded-xl bg-slate-900 border border-slate-700">
            <div class="text-xs font-mono text-[#00f5d4] font-bold">decode.macatung.dev</div>
            <h3 class="font-display font-bold text-lg text-white mt-1 mb-1">Ma Giải Mã</h3>
            <p class="text-xs text-slate-300 font-sans leading-relaxed">
              Mở nắp những hệ thống vô hình — Kênh YouTube &amp; chuyên san giải phẫu công nghệ ngầm, kiến trúc phân tán.
            </p>
            <div class="pt-3 text-xs font-mono text-[#00f5d4] font-bold">
              Đang xem chuyên mục này
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION: YOUTUBE SUBSCRIBE CTA -->
    <section class="py-12 sm:py-16 border-t border-slate-800/80 bg-[#060913]">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#ff0054]/10 border border-[#ff0054]/30 text-[#ff0054] text-xs font-mono font-bold">
          <Icons name="Play" :size="12" />
          <span>KÊNH YOUTUBE @MaGiaiMa</span>
        </div>

        <h2 class="font-display text-2xl sm:text-4xl font-extrabold text-white">
          Theo Dõi Video Giải Mã Trên YouTube
        </h2>

        <p class="text-slate-400 text-sm font-sans max-w-xl mx-auto">
          Mỗi bài viết đều có video hoạt họa Isometric 3D minh họa chi tiết. Đăng ký kênh để đón xem video mới sớm nhất.
        </p>

        <div class="pt-2 flex justify-center gap-3">
          <a
            href="https://youtube.com/@MaGiaiMa"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#ff0054] hover:bg-[#e11d48] text-white text-xs font-bold font-mono transition-all shadow-lg shadow-[#ff0054]/20"
          >
            <Icons name="Play" :size="16" />
            <span>Đăng Ký Kênh @MaGiaiMa</span>
          </a>
        </div>
      </div>
    </section>
  </DecodeLayout>
</template>
