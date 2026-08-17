<script setup lang="ts">
import { computed } from 'vue';
import { developerStats } from '@/data/experienceData';
import Icons from '@/Components/ui/Icons.vue';

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

const displayStats = computed(() => {
  if (props.stats && props.stats.total_pageviews !== undefined) {
    return [
      {
        value: `${(props.stats.total_pageviews || 0).toLocaleString('vi-VN')}+`,
        label: 'Tổng Lượt Truy Cập Live',
        rune: '👁️',
        desc: 'Pageviews ghi nhận bởi Analytics',
      },
      {
        value: `${(props.stats.total_projects || 6)}`,
        label: 'Dự Án Đang Chạy',
        rune: '🏰',
        desc: 'Sản phẩm kỹ thuật trong CMS',
      },
      {
        value: `${(props.stats.total_hops || 0)}`,
        label: 'Lượt Nhảy Linh Vật',
        rune: '🧛‍♂️',
        desc: 'Tương tác vật lý Ma Cà Tưng',
      },
      {
        value: '99.9%',
        label: 'Tỷ Lệ Sẵn Sàng 00:00 AM',
        rune: '⚡',
        desc: 'Zero Downtime Architecture',
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
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-phantom-mint text-xs font-mono mb-3 whitespace-nowrap select-none">
        🌙 Developer Origin & Principles
      </span>
      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white tracking-tight">
        Triết Lý & <span class="text-phantom-mint">Bản Lĩnh Đêm</span>
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
        class="p-5 sm:p-6 rounded-2xl glass-panel border border-white/[0.07] hover:border-white/20 transition-all select-none"
      >
        <div class="flex items-center justify-between mb-3">
          <div class="w-9 h-9 rounded-xl bg-midnight-900 border border-white/10 flex items-center justify-center text-phantom-mint">
            <Icons :name="stat.iconName" :size="18" />
          </div>
          <span v-if="stat.unit" class="text-[10px] font-mono uppercase tracking-wider text-slate-400 px-2 py-0.5 rounded bg-white/5 whitespace-nowrap">{{ stat.unit }}</span>
        </div>
        <div class="text-3xl font-display font-extrabold text-white mb-1 tracking-tight whitespace-nowrap">
          {{ stat.value }}
        </div>
        <div class="text-sm font-semibold text-slate-200 mb-1 tracking-tight">{{ stat.label }}</div>
        <p class="text-xs text-slate-400 font-sans leading-relaxed break-words">{{ stat.description }}</p>
      </div>
    </div>

    <!-- 3 Clean Architectural Pillar Cards (Replaces long manifesto essays) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
      <div
        v-for="(pillar, i) in pillars"
        :key="pillar.title"
        class="p-6 rounded-2xl glass-panel border border-white/[0.07] hover:border-phantom-mint/30 transition-colors"
      >
        <div class="flex items-center justify-between mb-4">
          <div class="w-10 h-10 rounded-xl bg-midnight-900 border border-white/10 flex items-center justify-center text-phantom-mint">
            <Icons :name="pillar.icon" :size="20" />
          </div>
          <span class="text-xs font-mono font-bold text-slate-500">0{{ i + 1 }}</span>
        </div>
        <h3 class="font-display font-bold text-white text-base sm:text-lg mb-2">{{ pillar.title }}</h3>
        <p class="text-xs sm:text-sm text-slate-400 font-sans leading-relaxed">{{ pillar.desc }}</p>
      </div>
    </div>
  </section>
</template>
