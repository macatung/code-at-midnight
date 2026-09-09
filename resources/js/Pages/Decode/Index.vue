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
  summary: string;
  hook: string;
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
const isAutoPlaying = ref(false);
let autoPlayTimer: any = null;

const selectStep = (index: number) => {
  activeStepIndex.value = index;
};

const nextStep = () => {
  activeStepIndex.value = (activeStepIndex.value + 1) % props.featuredEpisode.key_nodes.length;
};

const prevStep = () => {
  activeStepIndex.value = (activeStepIndex.value - 1 + props.featuredEpisode.key_nodes.length) % props.featuredEpisode.key_nodes.length;
};

const toggleAutoPlay = () => {
  isAutoPlaying.value = !isAutoPlaying.value;
  if (isAutoPlaying.value) {
    autoPlayTimer = setInterval(nextStep, 2500);
  } else {
    clearInterval(autoPlayTimer);
  }
};

// Article Filtering & Search
const searchQuery = ref('');
const selectedCategory = ref('ALL');

const categories = [
  { id: 'ALL', label: 'Tất Cả Bài Viết' },
  { id: 'Fintech & Payment Systems', label: 'Fintech' },
  { id: 'Search Infrastructure & Distributed Systems', label: 'Hạ Tầng Tìm Kiếm' },
  { id: 'Telecommunications & Submarine Cables', label: 'Cáp Biển' },
  { id: 'Banking Hardware & Distributed Consensus', label: 'ATM' },
  { id: 'Relativistic Physics & Satellite Systems', label: 'GPS' },
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
    <!-- HERO SECTION -->
    <section class="relative pt-12 pb-20 sm:pt-20 sm:pb-28 overflow-hidden">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          <!-- Hero Text -->
          <div class="lg:col-span-7 space-y-6 text-left">
            <!-- Telemetry Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#00f5d4]/10 border border-[#00f5d4]/30 text-[#00f5d4] text-xs font-mono">
              <span class="w-2 h-2 rounded-full bg-[#00f5d4] animate-pulse" />
              <span>SUBDOMAIN CHÍNH THỨC:</span>
              <strong class="text-white">{{ channel.subdomain }}</strong>
            </div>

            <!-- Main Title -->
            <div class="space-y-2">
              <div class="text-xs sm:text-sm font-mono tracking-widest text-[#00b4d8] uppercase font-bold">
                Kênh YouTube &amp; Cổng Khảo Cứu Kiến Trúc Ngầm
              </div>
              <h1 class="font-display text-4xl sm:text-6xl font-black text-white tracking-tight leading-[1.1]">
                MA GIẢI MÃ
              </h1>
              <p class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-[#00f5d4] via-[#38bdf8] to-[#0077b6] bg-clip-text text-transparent">
                "{{ channel.tagline }}"
              </p>
            </div>

            <!-- Description -->
            <p class="text-base sm:text-lg text-slate-300 font-sans leading-relaxed max-w-2xl">
              {{ channel.sub_tagline }} Từ chiếc thẻ Visa quẹt trong 2 giây, cuộc gọi xuyên đáy đại dương, đến vệ tinh GPS tính toán theo thuyết tương đối của Einstein.
            </p>

            <!-- Key Metric Counters -->
            <div class="grid grid-cols-3 gap-4 pt-2 max-w-lg font-mono text-xs">
              <div class="p-3 rounded-xl bg-[#0a1426] border border-[#00b4d8]/20">
                <div class="text-[#00f5d4] font-bold text-lg">05 TẬP</div>
                <div class="text-slate-400 text-[10px]">Pilot Season 2026</div>
              </div>
              <div class="p-3 rounded-xl bg-[#0a1426] border border-[#00b4d8]/20">
                <div class="text-[#38bdf8] font-bold text-lg">ISOMETRIC</div>
                <div class="text-slate-400 text-[10px]">ByteByteGo Style</div>
              </div>
              <div class="p-3 rounded-xl bg-[#0a1426] border border-[#00b4d8]/20">
                <div class="text-amber-400 font-bold text-lg">4K 60FPS</div>
                <div class="text-slate-400 text-[10px]">CAD Schematics</div>
              </div>
            </div>

            <!-- Action CTA Group -->
            <div class="pt-4 flex flex-wrap items-center gap-4">
              <a
                href="#anatomy-visualizer"
                class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-gradient-to-r from-[#00f5d4] to-[#00b4d8] text-[#060913] font-bold text-sm shadow-xl shadow-[#00f5d4]/20 hover:scale-[1.02] active:scale-[0.98] transition-all"
              >
                <Icons name="Zap" :size="18" />
                <span>Mở Nắp Hệ Thống Visa 100k</span>
              </a>

              <a
                href="#danh-sach-bai-viet"
                class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl border border-[#00b4d8]/30 bg-[#0a1426] hover:bg-[#0f1e38] text-slate-200 text-sm font-semibold transition-all"
              >
                <Icons name="FileText" :size="18" class="text-[#00f5d4]" />
                <span>Danh Sách Bài Viết ({{ episodes.length }})</span>
              </a>

              <a
                href="https://youtube.com/@MaGiaiMa"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 px-4 py-3.5 rounded-xl bg-[#ff0054]/15 hover:bg-[#ff0054]/25 border border-[#ff0054]/40 text-[#ff0054] text-sm font-bold transition-all"
              >
                <Icons name="Play" :size="16" />
                <span>@MaGiaiMa</span>
              </a>
            </div>
          </div>

          <!-- Hero Isometric 2D Core Visual (The Systems Anatomist) -->
          <div class="lg:col-span-5 relative flex justify-center">
            <!-- Glowing CAD Projection Box -->
            <div class="relative w-full max-w-[420px] aspect-square rounded-3xl p-6 bg-gradient-to-b from-[#0a162c] to-[#050a14] border border-[#00f5d4]/30 shadow-2xl shadow-[#00b4d8]/20 group flex flex-col items-center justify-between">
              <!-- HUD Reticles -->
              <div class="w-full flex items-center justify-between text-[10px] font-mono text-[#00f5d4]/80">
                <span class="flex items-center gap-1.5">
                  <span class="w-1.5 h-1.5 bg-[#00f5d4] rounded-full animate-ping" />
                  ISOMETRIC // 2D_SYSTEMS_ANATOMY
                </span>
                <span class="text-amber-400 font-semibold">CAD.LAYER: 4K</span>
              </div>

              <!-- 2D Exploded Isometric Graphic -->
              <div class="w-full flex-1 rounded-2xl overflow-hidden relative flex items-center justify-center p-4 bg-[#060913]/60 my-3 border border-[#00b4d8]/20">
                <img
                  src="/brand/decode/decode-badge-avatar.svg"
                  alt="Ma Giải Mã - 2D Exploded Isometric Core"
                  class="w-4/5 h-4/5 object-contain group-hover:scale-105 transition-transform duration-500 drop-shadow-[0_0_25px_rgba(0,245,212,0.3)]"
                />

                <!-- Subtle Grid lines overlay -->
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#00f5d4]/5 to-transparent pointer-events-none opacity-30" />
              </div>

              <!-- Deconstruction Callout Badges -->
              <div class="w-full p-3 rounded-xl bg-[#060913]/90 backdrop-blur-md border border-[#00f5d4]/30 text-left">
                <div class="flex items-center justify-between text-[11px] font-mono mb-1">
                  <span class="text-[#00f5d4] font-bold">MÔ HÌNH THƯƠNG HIỆU 2D:</span>
                  <span class="text-amber-400 font-semibold">"Mở Nắp Hệ Thống"</span>
                </div>
                <p class="text-[11px] text-slate-300 font-sans leading-tight">
                  Khối Isometric M-Core phân rã 3 tầng • Bản vẽ kỹ thuật CAD sắc nét • Không mascot, tập trung 100% vào giải phẫu kiến trúc ngầm.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 2: INTERACTIVE SYSTEM ANATOMY VISUALIZER (BÓC TÁCH HỆ THỐNG) -->
    <section id="anatomy-visualizer" class="py-16 bg-[#080d1a] border-y border-[#00b4d8]/20 relative">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-left space-y-2 mb-10">
          <div class="inline-flex items-center gap-2 text-xs font-mono text-[#00f5d4] tracking-wider uppercase font-bold">
            <Icons name="Terminal" :size="14" />
            <span>Mô Phỏng Tương Tác • Episode 01 Breakdown</span>
          </div>
          <h2 class="font-display text-2xl sm:text-4xl font-extrabold text-white">
            Bóc Tách Từng Mili-giây: Quẹt Thẻ Visa 100k
          </h2>
          <p class="text-slate-400 text-sm sm:text-base max-w-3xl">
            Click vào từng trạm bên dưới để theo dõi hành trình gói tin dữ liệu ISO 8583 du hành qua đáy đại dương tới Virginia (Mỹ) trong 1.85 giây.
          </p>
        </div>

        <!-- Visualizer Dashboard Container -->
        <div class="rounded-2xl bg-[#060913] border border-[#00b4d8]/30 shadow-2xl p-6 sm:p-8 relative overflow-hidden">
          <!-- Top Telemetry Row -->
          <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-[#00b4d8]/15 font-mono text-xs">
            <div class="flex items-center gap-3">
              <span class="px-2.5 py-1 rounded bg-[#00b4d8]/20 text-[#38bdf8] font-bold">
                TẬP 01
              </span>
              <span class="text-slate-300">Tổng Độ Trễ: <strong class="text-[#00f5d4]">{{ featuredEpisode.total_latency }}</strong></span>
              <span>•</span>
              <span class="text-slate-400">Trạm {{ activeStepIndex + 1 }} / {{ featuredEpisode.key_nodes.length }}</span>
            </div>

            <div class="flex items-center gap-2">
              <button
                type="button"
                class="px-3 py-1.5 rounded-lg border border-[#00b4d8]/30 text-xs font-mono text-slate-300 hover:text-white hover:bg-white/5 transition-colors"
                @click="prevStep"
              >
                ◀ Trước
              </button>
              <button
                type="button"
                class="px-3 py-1.5 rounded-lg border border-[#00b4d8]/30 text-xs font-mono text-slate-300 hover:text-white hover:bg-white/5 transition-colors"
                @click="nextStep"
              >
                Tiếp ▶
              </button>
              <button
                type="button"
                class="px-3 py-1.5 rounded-lg border text-xs font-mono transition-colors"
                :class="isAutoPlaying ? 'bg-[#00f5d4]/20 border-[#00f5d4] text-[#00f5d4]' : 'border-white/10 text-slate-400 hover:text-white'"
                @click="toggleAutoPlay"
              >
                {{ isAutoPlaying ? '⏸ Đang Chạy...' : '▶ Tự Động Mô Phỏng' }}
              </button>
            </div>
          </div>

          <!-- Interactive Node Pipeline Bar -->
          <div class="py-8">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
              <button
                v-for="(node, idx) in featuredEpisode.key_nodes"
                :key="idx"
                type="button"
                class="p-3.5 rounded-xl text-left border transition-all relative overflow-hidden"
                :class="activeStepIndex === idx ? 'bg-[#00b4d8]/20 border-[#00f5d4] shadow-lg shadow-[#00f5d4]/10' : 'bg-[#0a1222] border-white/5 hover:border-[#00b4d8]/40 text-slate-400'"
                @click="selectStep(idx)"
              >
                <!-- Active Indicator Pin -->
                <div v-if="activeStepIndex === idx" class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#00f5d4] to-[#00b4d8]" />
                
                <div class="flex items-center justify-between text-[10px] font-mono mb-1.5">
                  <span :class="activeStepIndex === idx ? 'text-[#00f5d4] font-bold' : 'text-slate-400'">
                    0{{ node.step }}
                  </span>
                  <span class="text-amber-400 font-semibold">{{ node.time }}</span>
                </div>
                <div class="font-sans text-xs font-bold line-clamp-2" :class="activeStepIndex === idx ? 'text-white' : 'text-slate-300'">
                  {{ node.node }}
                </div>
              </button>
            </div>
          </div>

          <!-- Active Step Deep-Dive Card -->
          <div
            v-if="featuredEpisode.key_nodes[activeStepIndex]"
            class="p-6 rounded-xl bg-[#091122] border border-[#00f5d4]/30 text-left space-y-4 transition-all"
          >
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/5 pb-3">
              <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-lg bg-[#00f5d4]/15 border border-[#00f5d4]/40 flex items-center justify-center font-mono font-bold text-[#00f5d4] text-sm">
                  0{{ featuredEpisode.key_nodes[activeStepIndex].step }}
                </span>
                <div>
                  <h3 class="font-display font-bold text-lg text-white">
                    {{ featuredEpisode.key_nodes[activeStepIndex].node }}
                  </h3>
                  <p class="text-xs font-mono text-[#38bdf8]">
                    Giao Thức: {{ featuredEpisode.key_nodes[activeStepIndex].protocol }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-3 font-mono text-xs">
                <span class="px-2.5 py-1 rounded bg-[#060913] border border-white/10 text-slate-300">
                  Thời Gian: <strong class="text-amber-400">{{ featuredEpisode.key_nodes[activeStepIndex].time }}</strong>
                </span>
                <span class="px-2.5 py-1 rounded bg-[#00f5d4]/10 border border-[#00f5d4]/30 text-[#00f5d4] font-semibold">
                  Trạng Thái: {{ featuredEpisode.key_nodes[activeStepIndex].status }}
                </span>
              </div>
            </div>

            <!-- Action Explanation -->
            <p class="text-sm sm:text-base text-slate-200 font-sans leading-relaxed">
              {{ featuredEpisode.key_nodes[activeStepIndex].action }}
            </p>

            <!-- Technical Secrets Payload Box -->
            <div class="p-3.5 rounded-lg bg-[#04070e] border border-[#00b4d8]/20 font-mono text-xs text-slate-300 space-y-1">
              <div class="text-[#00f5d4] font-bold text-[10px] uppercase tracking-wider">Gói Tin Dữ Liệu Ngầm (Inspection Dump):</div>
              <div class="text-[11px] text-slate-400 truncate">
                PAYLOAD // MTID: 0100 (Authorization Request) • PAN: 4***-****-****-1002 • AMOUNT: 100,000 VND • POS_GEO: 21.0285° N, 105.8542° E
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 3: DANH SÁCH BÀI VIẾT (EPISODES & ARTICLES LIST) -->
    <section id="danh-sach-bai-viet" class="py-20 border-t border-[#00b4d8]/20 bg-[#060a16]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4 text-left">
          <div class="space-y-2">
            <div class="text-xs font-mono text-[#00f5d4] uppercase font-bold tracking-widest flex items-center gap-2">
              <Icons name="FileText" :size="16" />
              <span>Kho Tàng Khảo Cứu • Danh Sách Bài Viết (Pilot Season)</span>
            </div>
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-white">
              Danh Sách Bài Viết &amp; Phân Tích Hệ Thống
            </h2>
            <p class="text-slate-400 text-sm sm:text-base max-w-2xl font-sans">
              Mỗi bài viết là một chuyên án giải phẫu chi tiết: Từ tín hiệu điện trên chip, gói tin du hành đáy biển đến vệ tinh không gian.
            </p>
          </div>
          <div class="font-mono text-xs text-slate-400 flex items-center gap-2 self-start md:self-end">
            <span class="px-2.5 py-1 rounded bg-[#00f5d4]/10 text-[#00f5d4] border border-[#00f5d4]/20 font-bold">
              {{ filteredEpisodes.length }} / {{ episodes.length }} Bài Viết
            </span>
          </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="p-4 rounded-2xl bg-[#091224] border border-[#00b4d8]/20 mb-10 space-y-4">
          <div class="flex flex-col sm:flex-row gap-3">
            <!-- Search Bar -->
            <div class="relative flex-1">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <Icons name="Search" :size="16" />
              </div>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Tìm kiếm theo chủ đề, tiêu đề, giao thức (Visa, ISO 8583, Google, GPS...)..."
                class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-[#040711] border border-white/10 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-[#00f5d4] focus:ring-1 focus:ring-[#00f5d4] transition-all font-sans"
              />
              <button
                v-if="searchQuery"
                type="button"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white"
                @click="searchQuery = ''"
              >
                <Icons name="X" :size="16" />
              </button>
            </div>

            <!-- Reset Filters (if search or non-all filter active) -->
            <button
              v-if="searchQuery || selectedCategory !== 'ALL'"
              type="button"
              class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-xs font-mono text-slate-300 border border-white/10 transition-colors whitespace-nowrap"
              @click="searchQuery = ''; selectedCategory = 'ALL'"
            >
              Đặt lại bộ lọc
            </button>
          </div>

          <!-- Category Chips -->
          <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-mono">
            <button
              v-for="cat in categories"
              :key="cat.id"
              type="button"
              class="px-3 py-1.5 rounded-lg whitespace-nowrap transition-all border font-semibold"
              :class="selectedCategory === cat.id ? 'bg-[#00f5d4] text-[#060913] border-[#00f5d4] shadow-md shadow-[#00f5d4]/20' : 'bg-[#040711] text-slate-300 border-white/5 hover:border-[#00b4d8]/40 hover:text-white'"
              @click="selectedCategory = cat.id"
            >
              {{ cat.label }}
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="filteredEpisodes.length === 0"
          class="p-12 rounded-2xl bg-[#091122] border border-white/10 text-center space-y-4 max-w-lg mx-auto"
        >
          <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center mx-auto text-slate-400">
            <Icons name="Search" :size="24" />
          </div>
          <h3 class="font-display font-bold text-lg text-white">Không tìm thấy bài viết phù hợp</h3>
          <p class="text-xs text-slate-400 font-sans">
            Thử tìm kiếm với từ khóa khác như "Visa", "Google", "GPS", "Cáp", "ATM".
          </p>
          <button
            type="button"
            class="px-4 py-2 rounded-xl bg-[#00f5d4]/20 text-[#00f5d4] text-xs font-bold font-mono hover:bg-[#00f5d4]/30 transition-colors"
            @click="searchQuery = ''; selectedCategory = 'ALL'"
          >
            Hiện tất cả 5 bài viết
          </button>
        </div>

        <!-- Episodes & Articles Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
          <div
            v-for="ep in filteredEpisodes"
            :key="ep.id"
            class="rounded-2xl bg-[#091122] border border-[#00b4d8]/20 hover:border-[#00f5d4]/50 transition-all duration-300 p-6 flex flex-col justify-between group shadow-xl hover:shadow-2xl hover:shadow-[#00b4d8]/10"
          >
            <div class="space-y-4">
              <!-- Top Row: Episode Badge & Latency -->
              <div class="flex items-center justify-between text-xs font-mono">
                <span class="px-2 py-0.5 rounded bg-[#00f5d4]/15 text-[#00f5d4] border border-[#00f5d4]/30 font-bold">
                  TẬP {{ ep.episode_number }}
                </span>
                <span class="text-slate-400 flex items-center gap-1.5">
                  <Icons name="Clock" :size="12" /> {{ ep.duration }}
                </span>
              </div>

              <!-- Title -->
              <h3 class="font-display font-bold text-lg text-white group-hover:text-[#00f5d4] transition-colors line-clamp-2">
                <Link :href="`/decode/tap/${ep.slug}`">
                  {{ ep.title }}
                </Link>
              </h3>

              <!-- Hook / Story -->
              <p class="text-xs text-slate-300 font-sans line-clamp-3 leading-relaxed">
                {{ ep.hook }}
              </p>

              <!-- Category & Summary -->
              <div class="p-3 rounded-xl bg-[#060913] border border-white/5 space-y-1">
                <div class="text-[10px] font-mono text-[#38bdf8] uppercase font-bold">
                  {{ ep.category }}
                </div>
                <div class="text-xs text-slate-400 font-sans line-clamp-2">
                  {{ ep.summary }}
                </div>
              </div>

              <!-- Tags -->
              <div class="flex flex-wrap gap-1.5 pt-1">
                <span
                  v-for="(tag, tIdx) in ep.tags.slice(0, 3)"
                  :key="tIdx"
                  class="text-[10px] font-mono px-2 py-0.5 rounded bg-white/5 text-slate-300 border border-white/5"
                >
                  #{{ tag }}
                </span>
              </div>
            </div>

            <!-- Bottom Action Link -->
            <div class="pt-6 mt-4 border-t border-white/5 space-y-3">
              <div class="flex items-center justify-between text-[11px] font-mono">
                <span class="text-slate-400">Độ trễ khảo cứu:</span>
                <span class="text-amber-400 font-bold">{{ ep.total_latency }}</span>
              </div>
              <Link
                :href="`/decode/tap/${ep.slug}`"
                class="w-full py-2.5 rounded-xl bg-[#00f5d4]/10 hover:bg-[#00f5d4] hover:text-[#060913] border border-[#00f5d4]/30 text-[#00f5d4] text-xs font-bold font-mono flex items-center justify-center gap-1.5 transition-all shadow-md group-hover:border-[#00f5d4]"
              >
                <span>Đọc Bài Viết Chi Tiết</span>
                <Icons name="ChevronRight" :size="14" class="group-hover:translate-x-1 transition-transform" />
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 4: THE ECOSYSTEM TRIO (MA CÀ TƯNG • MA TỌA THIỀN • MA GIẢI MÃ) -->
    <section class="py-16 bg-[#04070f] border-t border-[#00b4d8]/20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center space-y-3 mb-12">
          <div class="text-xs font-mono text-[#00f5d4] uppercase font-bold tracking-widest">
            Hệ Sinh Thái Thương Hiệu • macatung.dev
          </div>
          <h2 class="font-display text-3xl sm:text-4xl font-black text-white">
            Bộ Ba Bản Sắc Hoàn Chỉnh
          </h2>
          <p class="text-slate-400 text-sm max-w-xl mx-auto">
            Từ dòng code lúc nửa đêm, thiền quán giải mã nội tâm, đến bóc tách hệ thống vận hành thế giới.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
          <!-- Card 1: Ma Cà Tưng -->
          <a
            href="https://macatung.dev"
            class="p-6 rounded-2xl bg-[#070c17] border border-white/10 hover:border-[#00f5a0]/50 transition-all group"
          >
            <div class="w-12 h-12 rounded-xl bg-[#00f5a0]/10 border border-[#00f5a0]/30 flex items-center justify-center text-2xl mb-4">
              🧛‍♂️
            </div>
            <div class="text-xs font-mono text-[#00f5a0] font-bold">macatung.dev</div>
            <h3 class="font-display font-bold text-xl text-white mt-1 mb-2">Ma Cà Tưng</h3>
            <p class="text-xs text-slate-400 font-sans leading-relaxed">
              "The Midnight Alchemist" — Portfolio cá nhân, kiến trúc sư phần mềm, AI agents và những dự án lập trình lúc nửa đêm.
            </p>
            <div class="pt-4 text-xs font-mono text-[#00f5a0] flex items-center gap-1 group-hover:translate-x-1 transition-transform">
              Khám phá portfolio ↗
            </div>
          </a>

          <!-- Card 2: Ma Tọa Thiền -->
          <a
            href="https://theravada.macatung.dev"
            class="p-6 rounded-2xl bg-[#070c17] border border-white/10 hover:border-amber-400/50 transition-all group"
          >
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-2xl mb-4">
              ☸️
            </div>
            <div class="text-xs font-mono text-amber-400 font-bold">theravada.macatung.dev</div>
            <h3 class="font-display font-bold text-xl text-white mt-1 mb-2">Ma Tọa Thiền</h3>
            <p class="text-xs text-slate-400 font-sans leading-relaxed">
              "Giải mã nội tâm, tìm về tĩnh lặng" — Tam Tạng Kinh Điển Pāḷi, thiền Vipassanā, Vi Diệu Pháp và pháp thoại căn bản.
            </p>
            <div class="pt-4 text-xs font-mono text-amber-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
              Khám phá kinh điển ↗
            </div>
          </a>

          <!-- Card 3: Ma Giải Mã (Current) -->
          <div class="p-6 rounded-2xl bg-[#0a162c] border border-[#00f5d4]/40 shadow-xl shadow-[#00f5d4]/10 relative">
            <div class="absolute top-4 right-4 px-2 py-0.5 rounded bg-[#00f5d4]/20 text-[#00f5d4] text-[10px] font-mono font-bold">
              ĐANG XEM
            </div>
            <div class="w-12 h-12 rounded-xl bg-[#00f5d4]/15 border border-[#00f5d4]/40 flex items-center justify-center text-2xl mb-4">
              🔬
            </div>
            <div class="text-xs font-mono text-[#00f5d4] font-bold">decode.macatung.dev</div>
            <h3 class="font-display font-bold text-xl text-white mt-1 mb-2">Ma Giải Mã</h3>
            <p class="text-xs text-slate-300 font-sans leading-relaxed">
              "Mở nắp những hệ thống vô hình" — Kênh YouTube mổ xẻ công nghệ ngầm, kiến trúc phân tán &amp; bản vẽ Isometric 3D.
            </p>
            <div class="pt-4 text-xs font-mono text-[#00f5d4] font-bold">
              Kênh chính thức: @MaGiaiMa
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 5: YOUTUBE SUBSCRIBE BANNER CTA -->
    <section class="py-16 bg-gradient-to-b from-[#060913] to-[#04060d]">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl p-8 sm:p-12 bg-gradient-to-r from-[#091428] via-[#0e1f3d] to-[#091428] border border-[#00f5d4]/30 shadow-2xl relative overflow-hidden text-center space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#ff0054]/20 border border-[#ff0054]/40 text-[#ff0054] text-xs font-mono font-bold">
            <Icons name="Play" :size="14" />
            <span>KÊNH YOUTUBE CHÍNH THỨC</span>
          </div>

          <h2 class="font-display text-3xl sm:text-5xl font-black text-white tracking-tight">
            Đăng Ký Kênh <span class="text-[#00f5d4]">@MaGiaiMa</span> Ngay Hôm Nay
          </h2>

          <p class="text-slate-300 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed font-sans">
            Đón xem 5 tập đầu tiên của Pilot Season: Thẻ Visa 100k, Google Search 50 tỷ trang, Cáp quang biển, Cây ATM nhả tiền và Đặt xe Grab cùng Einstein.
          </p>

          <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
            <a
              href="https://youtube.com/@MaGiaiMa"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-[#ff0054] hover:bg-[#e11d48] text-white text-base font-extrabold shadow-xl shadow-[#ff0054]/30 hover:scale-105 active:scale-95 transition-all"
            >
              <Icons name="Play" :size="20" />
              <span>Theo Dõi @MaGiaiMa Trên YouTube</span>
            </a>

            <a
              href="#danh-sach-bai-viet"
              class="inline-flex items-center gap-2 px-6 py-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/15 text-slate-200 text-sm font-semibold transition-all"
            >
              <Icons name="FileText" :size="18" class="text-[#00f5d4]" />
              <span>Khám Phá Danh Sách Bài Viết</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  </DecodeLayout>
</template>
