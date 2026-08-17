<script setup lang="ts">
import { ref, computed } from 'vue';
import { developerStats } from '@/data/experienceData';
import Icons from '@/Components/ui/Icons.vue';
import { sound } from '@/audio/soundEffects';

interface Props {
  stats?: {
    total_pageviews?: number;
    unique_visitors?: number;
    total_inquiries?: number;
    total_hops?: number;
    total_projects?: number;
  };
}

const props = withDefaults(defineProps<Props>(), {
  stats: () => ({}),
});

type ManifestoTabId = 'philosophy' | 'day-night' | 'craftsmanship';

const activeTab = ref<ManifestoTabId>('philosophy');

const tabs = [
  {
    id: 'philosophy',
    title: 'Triết Lý 00:00 AM',
    subtitle: 'Khi cả thành phố chìm vào giấc ngủ, thế giới của dòng code mới thực sự bừng tỉnh.',
    content: 'Không có tiếng thông báo Slack, không có họp hành ngắt quãng. Chỉ có âm thanh gõ phím, ly Robusta đậm đà và dòng chảy tư duy thuần khiết.',
    icon: 'Moon',
    badge: 'Ultra-Flow'
  },
  {
    id: 'day-night',
    title: 'Day vs Night Developer',
    subtitle: 'Ban ngày bảo trì hệ thống — Ban đêm kiến tạo tương lai.',
    content: 'Ban ngày là giao tiếp, đồng bộ và lập kế hoạch. Ban đêm là thời khắc của kiến trúc, thuật toán tối ưu và những sáng tạo vượt bậc.',
    icon: 'Sun',
    badge: 'Dual Mindset'
  },
  {
    id: 'craftsmanship',
    title: 'Khắc Bùa Chất Lượng',
    subtitle: 'Mỗi dòng mã là một đạo bùa chú trừ khử bug tận gốc.',
    content: 'Không chấp nhận sự cẩu thả. Mọi API đều phải chuẩn chỉ, mọi UI đều phải mượt mà 60 FPS, và mọi test suite đều phải pass 100% trước khi deploy.',
    icon: 'Shield',
    badge: 'Craftsmanship'
  }
];

const setTab = (id: ManifestoTabId) => {
  activeTab.value = id;
  sound.playClick();
};

const activeTabContent = computed(() => {
  return tabs.find((t) => t.id === activeTab.value) || tabs[0];
});

const handleTabKeydown = (e: KeyboardEvent) => {
  const currentIndex = tabs.findIndex((t) => t.id === activeTab.value);
  if (e.key === 'ArrowRight') {
    e.preventDefault();
    const nextIndex = (currentIndex + 1) % tabs.length;
    setTab(tabs[nextIndex].id as ManifestoTabId);
  } else if (e.key === 'ArrowLeft') {
    e.preventDefault();
    const prevIndex = (currentIndex - 1 + tabs.length) % tabs.length;
    setTab(tabs[prevIndex].id as ManifestoTabId);
  } else if (e.key === 'Home') {
    e.preventDefault();
    setTab(tabs[0].id as ManifestoTabId);
  } else if (e.key === 'End') {
    e.preventDefault();
    setTab(tabs[tabs.length - 1].id as ManifestoTabId);
  }
};

const displayStats = computed(() => {
  if (props.stats && props.stats.total_pageviews !== undefined) {
    return [
      {
        value: `${(props.stats.total_pageviews || 0).toLocaleString('vi-VN')}+`,
        label: 'Tổng Lượt Truy Cập Live',
        iconName: 'Eye',
        unit: 'Views',
        description: 'Pageviews ghi nhận bởi Analytics',
      },
      {
        value: `${props.stats.total_projects || 6}`,
        label: 'Dự Án Đang Chạy',
        iconName: 'Layers',
        unit: 'Active',
        description: 'Sản phẩm kỹ thuật trong CMS',
      },
      {
        value: `${props.stats.total_hops || 0}`,
        label: 'Lượt Nhảy Linh Vật',
        iconName: 'Zap',
        unit: 'Hops',
        description: 'Tương tác vật lý Ma Cà Tưng',
      },
      {
        value: '99.9%',
        label: 'Tỷ Lệ Sẵn Sàng 00:00 AM',
        iconName: 'Shield',
        unit: 'SLA',
        description: 'Zero Downtime Architecture',
      },
    ];
  }
  return developerStats;
});

const pillars = [
  {
    icon: 'Moon',
    title: 'Vùng Tĩnh Lặng (Ultra-Flow)',
    desc: 'Tập trung tuyệt đối vào giải quyết core logic, không bị gián đoạn bởi xao nhãng.',
  },
  {
    icon: 'Shield',
    title: 'Kiến Trúc & Type-Safety',
    desc: 'TypeScript nghiêm ngặt 100%, cấu trúc phân tán chuẩn SOLID, loại bỏ runtime crash.',
  },
  {
    icon: 'Zap',
    title: 'Tối Ưu Tốc Độ Thực Chiến',
    desc: 'Độ trễ phản hồi sub-millisecond, giao diện 60 FPS mượt mà và khả năng chịu tải cao.',
  },
];
</script>

<template>
  <section id="about" class="scroll-mt-24 w-full py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-left">
    <!-- Header -->
    <div class="flex flex-col items-start mb-10">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-phantom-mint text-xs font-mono mb-3 whitespace-nowrap select-none shadow-glow-mint">
        🌙 Developer Origin & Principles
      </span>
      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white tracking-tight">
        Triết Lý & <span class="text-transparent bg-clip-text bg-gradient-to-r from-phantom-mint via-phantom-cyan to-talisman-gold">Bản Lĩnh Đêm</span>
      </h2>
      <p class="text-sm sm:text-base text-slate-400 mt-2 max-w-2xl font-sans">
        Những con số thực chiến và nguyên tắc kỹ thuật được tôi luyện qua hàng ngàn đêm đối mặt với màn hình đen.
      </p>
    </div>

    <!-- 4 Developer Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-10">
      <div
        v-for="stat in displayStats"
        :key="stat.label"
        class="p-5 sm:p-6 rounded-2xl glass-panel border border-white/[0.08] hover:border-phantom-mint/30 transition-all duration-300 select-none hover:shadow-lg hover:shadow-black/40 group"
      >
        <div class="flex items-center justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-midnight-900 border border-white/10 flex items-center justify-center text-phantom-mint group-hover:scale-110 transition-transform">
            <Icons :name="stat.iconName || 'Zap'" :size="18" />
          </div>
          <span v-if="stat.unit" class="text-[10px] font-mono uppercase tracking-wider text-slate-400 px-2 py-0.5 rounded bg-white/5 whitespace-nowrap border border-white/5 font-semibold">
            {{ stat.unit }}
          </span>
        </div>
        <div class="text-3xl font-display font-extrabold text-white mb-1 tracking-tight whitespace-nowrap group-hover:text-phantom-mint transition-colors">
          {{ stat.value }}
        </div>
        <div class="text-sm font-semibold text-slate-200 mb-1 tracking-tight">{{ stat.label }}</div>
        <p class="text-xs font-mono text-slate-400 font-sans leading-relaxed break-words tracking-tight">{{ stat.description }}</p>
      </div>
    </div>

    <!-- Interactive 3-Tab Developer Manifesto -->
    <div class="mb-10 rounded-3xl glass-panel border border-white/10 p-6 sm:p-8 relative overflow-hidden">
      <!-- Ambient Glow Blur -->
      <div class="absolute -top-20 -right-20 w-64 h-64 bg-phantom-mint/5 rounded-full blur-3xl pointer-events-none" />

      <!-- Tabs Header Pills -->
      <div
        class="flex items-center gap-2 mb-6 border-b border-white/10 pb-4 overflow-x-auto no-scrollbar"
        role="tablist"
        @keydown="handleTabKeydown"
      >
        <button
          v-for="tab in tabs"
          :key="tab.id"
          type="button"
          role="tab"
          :aria-selected="activeTab === tab.id"
          tabindex="0"
          class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-display font-bold transition-all min-h-[42px] shrink-0 flex items-center gap-2 border whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-phantom-mint"
          :class="activeTab === tab.id
            ? 'bg-phantom-mint text-midnight-950 border-phantom-mint shadow-glow-mint'
            : 'bg-midnight-900/80 text-slate-400 border-white/5 hover:border-white/20 hover:text-white'"
          @click="setTab(tab.id as ManifestoTabId)"
        >
          <Icons :name="tab.icon" :size="16" />
          <span>{{ tab.title }}</span>
          <span
            class="text-[10px] font-mono px-1.5 py-0.2 rounded"
            :class="activeTab === tab.id ? 'bg-midnight-950/20 text-midnight-950 font-extrabold' : 'bg-white/5 text-slate-400'"
          >
            {{ tab.badge }}
          </span>
        </button>
      </div>

      <!-- Active Tab Content Panel -->
      <div class="space-y-3">
        <h3 class="text-xl sm:text-2xl font-display font-extrabold text-white">
          {{ activeTabContent.title }}
        </h3>
        <p class="text-sm font-mono text-phantom-mint font-semibold">
          {{ activeTabContent.subtitle }}
        </p>
        <p class="text-sm sm:text-base text-slate-300 font-sans leading-relaxed pt-2">
          {{ activeTabContent.content }}
        </p>
      </div>
    </div>

    <!-- Bio Origin Story Card & 3 Architectural Pillars Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
      <!-- Bio Origin Card (5 Columns) -->
      <div class="about-bio-card glass-panel lg:col-span-5 p-6 sm:p-7 rounded-2xl border border-white/10 flex flex-col justify-between relative overflow-hidden bg-gradient-to-b from-midnight-900/90 to-midnight-950">
        <div class="relative z-10">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-talisman-gold/10 text-talisman-gold border border-talisman-gold/30 text-xs font-mono mb-4 font-bold">
            ⚡ The Midnight Architect
          </div>
          <h3 class="font-display font-bold text-white text-xl mb-1">
            Senior Fullstack & AI Agent Architect
          </h3>
          <p class="text-xs font-mono text-phantom-mint mb-3">
            🏆 Giải Quốc Gia Tin Học · 8+ Năm Thực Chiến Hệ Thống Tải Cao
          </p>
          <p class="text-slate-300 text-sm leading-relaxed font-sans mb-4">
            Chuyên sâu kiến trúc hệ thống backend tải cao, nền tảng viễn thông GIS/NMS và thiết kế các hệ sinh thái <strong>Multi-Agent AI tự trị</strong> thay thế quy trình Customer Service truyền thống 24/7 với độ chính xác > 92%.
          </p>
        </div>
        <div class="pt-4 border-t border-white/5 flex items-center justify-between text-xs font-mono text-slate-400 relative z-10">
          <span>📍 Ho Chi Minh City / Remote</span>
          <span class="text-phantom-mint font-bold">⚡ Zero Downtime Pledge</span>
        </div>
      </div>

      <!-- 3 Architectural Pillar Cards (7 Columns) -->
      <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div
          v-for="(pillar, i) in pillars"
          :key="pillar.title"
          class="p-5 rounded-2xl glass-panel border border-white/[0.08] hover:border-phantom-mint/30 transition-colors flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between mb-3">
              <div class="w-9 h-9 rounded-xl bg-midnight-900 border border-white/10 flex items-center justify-center text-phantom-mint">
                <Icons :name="pillar.icon" :size="18" />
              </div>
              <span class="text-xs font-mono font-bold text-slate-500">0{{ i + 1 }}</span>
            </div>
            <h4 class="font-display font-bold text-white text-sm sm:text-base mb-1.5">{{ pillar.title }}</h4>
            <p class="text-xs text-slate-400 font-sans leading-relaxed">{{ pillar.desc }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
