<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Icons from '@/Components/ui/Icons.vue';
import PillarCardTheravada from './PillarCardTheravada.vue';
import PillarCardDecode from './PillarCardDecode.vue';
import PillarCardCashback from './PillarCardCashback.vue';
import PillarCardDesktop from './PillarCardDesktop.vue';
import PillarCardTools from './PillarCardTools.vue';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

type FilterType = 'all' | 'platforms' | 'media' | 'apps';
const activeFilter = ref<FilterType>('all');

const filters = computed(() => [
  { id: 'all' as FilterType, label: isVi.value ? 'Tất Cả 5 Trụ Cột' : 'All 5 Pillars', count: 5 },
  { id: 'platforms' as FilterType, label: isVi.value ? 'Nền Tảng & Desktop' : 'Platforms & Desktop', count: 2 },
  { id: 'media' as FilterType, label: isVi.value ? 'Nghiên Cứu & Tri Thức' : 'Media & Research', count: 2 },
  { id: 'apps' as FilterType, label: isVi.value ? 'Công Cụ & Tiện Ích' : 'Tools & Utilities', count: 1 },
]);
</script>

<template>
  <section
    id="ecosystem-grid"
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 relative z-10"
    aria-label="5 Ecosystem Pillars Bento Grid"
  >
    <!-- Section Header -->
    <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
      <div class="inline-flex items-center gap-2 rounded-full border border-phantom-mint/30 bg-phantom-mint/10 px-4 py-1 text-xs font-mono text-phantom-mint mb-4">
        <span class="h-2 w-2 rounded-full bg-phantom-mint shadow-glow-mint"></span>
        {{ isVi ? 'KIẾN TRÚC 5 TRỤ CỘT CỐT LÕI' : '5 CORE ECOSYSTEM PILLARS' }}
      </div>
      <h2 class="text-3xl sm:text-5xl font-display font-black tracking-tight text-white leading-tight">
        {{ isVi ? 'Hệ Sinh Thái Sản Phẩm & Nền Tảng Số' : 'The Multi-Product Software Suite' }}
      </h2>
      <p class="mt-4 text-base sm:text-lg text-slate-300 leading-relaxed">
        {{ isVi
          ? 'Khám phá 5 phân hệ độc lập nhưng gắn kết chặt chẽ trong hệ sinh thái MacaTung: từ ứng dụng desktop Windows x64 điều phối AI agent, phân tích chuyên sâu các hệ thống ngầm toàn cầu, cổng hoàn tiền thông minh đến kinh điển Phật giáo nguyên thủy và phòng thí nghiệm tương tác.'
          : 'Explore 5 specialized pillars comprising the MacaTung software suite: AI agent desktop orchestration, systemic infrastructure research, transparent e-commerce cashback, Theravāda Buddhist scholarship, and interactive digital labs.'
        }}
      </p>

      <!-- Category Filter Pills -->
      <div class="mt-8 flex flex-wrap items-center justify-center gap-2">
        <button
          v-for="flt in filters"
          :key="flt.id"
          type="button"
          @click="activeFilter = flt.id"
          class="px-3.5 py-1.5 rounded-xl text-xs font-mono transition-all duration-200 flex items-center gap-2"
          :class="activeFilter === flt.id ? 'bg-phantom-mint text-midnight-950 font-bold shadow-glow-mint' : 'bg-white/5 text-slate-400 border border-white/10 hover:border-white/20 hover:text-white'"
        >
          <span>{{ flt.label }}</span>
          <span class="px-1.5 py-0.2 rounded text-[10px]" :class="activeFilter === flt.id ? 'bg-midnight-950/20 text-midnight-950' : 'bg-white/10 text-slate-300'">
            {{ flt.count }}
          </span>
        </button>
      </div>
    </div>

    <!-- Bento Grid Container -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
      <!-- Pillar 4: Task Companion Desktop (Dominant top left card) -->
      <div
        v-show="activeFilter === 'all' || activeFilter === 'platforms'"
        class="lg:col-span-7 flex flex-col"
      >
        <PillarCardDesktop class="h-full" />
      </div>

      <!-- Pillar 3: Shopee Cashback (Top right card) -->
      <div
        v-show="activeFilter === 'all' || activeFilter === 'platforms'"
        class="lg:col-span-5 flex flex-col"
      >
        <PillarCardCashback class="h-full" />
      </div>

      <!-- Pillar 2: Decode Series (Middle left card) -->
      <div
        v-show="activeFilter === 'all' || activeFilter === 'media'"
        class="lg:col-span-6 flex flex-col"
      >
        <PillarCardDecode class="h-full" />
      </div>

      <!-- Pillar 1: Theravada Buddhist Platform (Middle right card) -->
      <div
        v-show="activeFilter === 'all' || activeFilter === 'media'"
        class="lg:col-span-6 flex flex-col"
      >
        <PillarCardTheravada class="h-full" />
      </div>

      <!-- Pillar 5: Interactive Tools & Mini-Apps (Full-width bottom card) -->
      <div
        v-show="activeFilter === 'all' || activeFilter === 'apps'"
        class="lg:col-span-12 flex flex-col"
      >
        <PillarCardTools class="h-full" />
      </div>
    </div>
  </section>
</template>
