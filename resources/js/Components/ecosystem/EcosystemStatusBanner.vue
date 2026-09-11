<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Icons from '@/Components/ui/Icons.vue';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

const pillarsStatus = computed(() => [
  {
    id: 'theravada',
    name: 'Theravāda Platform',
    label: isVi.value ? 'Phật Giáo Nguyên Thủy' : 'Theravāda Dhamma',
    badge: isVi.value ? '50+ Kinh Điển · 10 Bài Pāḷi' : '50+ Suttas · 10 Pali Lessons',
    href: '/theravada',
    color: 'text-amber-400',
    dotBg: 'bg-amber-400',
    borderColor: 'border-amber-500/20',
    bgBadge: 'bg-amber-500/10',
  },
  {
    id: 'decode',
    name: 'Decode Series',
    label: isVi.value ? 'Ma Giải Mã CAD' : 'Decode CAD Media',
    badge: isVi.value ? 'Tập 01 Visa 100k · Latency 1.85s' : 'Ep.01 Visa 100k · Latency 1.85s',
    href: '/decode',
    color: 'text-cyan-400',
    dotBg: 'bg-cyan-400',
    borderColor: 'border-cyan-500/20',
    bgBadge: 'bg-cyan-500/10',
  },
  {
    id: 'cashback',
    name: 'Shopee Cashback',
    label: isVi.value ? 'Cổng Hoàn Tiền' : 'Cashback Portal',
    badge: isVi.value ? 'Hoàn 80% · Rút Từ 50k' : '80% Pass-Through · 0đ Fee',
    href: '/hoantien',
    color: 'text-emerald-400',
    dotBg: 'bg-emerald-400',
    borderColor: 'border-emerald-500/20',
    bgBadge: 'bg-emerald-500/10',
  },
  {
    id: 'desktop',
    name: 'Task Companion',
    label: isVi.value ? 'Windows Desktop App' : 'Windows Desktop App',
    badge: isVi.value ? 'AWS Labs CAO · Monaco Diff' : 'AWS Labs CAO · Monaco Diff',
    href: '/desktop',
    color: 'text-phantom-mint',
    dotBg: 'bg-phantom-mint',
    borderColor: 'border-phantom-mint/20',
    bgBadge: 'bg-phantom-mint/10',
  },
  {
    id: 'tools',
    name: 'Interactive Labs',
    label: isVi.value ? 'Mini-Apps & Lò Bùa' : 'Mini-Apps & Arcade',
    badge: isVi.value ? 'Bùa Dev · Rune Typer Game' : 'Talisman Forge · Rune Typer',
    href: '/talisman',
    color: 'text-purple-400',
    dotBg: 'bg-purple-400',
    borderColor: 'border-purple-500/20',
    bgBadge: 'bg-purple-500/10',
  },
]);
</script>

<template>
  <aside
    class="border-y border-white/5 bg-midnight-950/80 backdrop-blur-md relative z-20 overflow-hidden"
    aria-label="Ecosystem Live Status Bar"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
      <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
        <!-- Live Indicator Left -->
        <div class="flex items-center gap-3 shrink-0">
          <div class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-phantom-mint opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-phantom-mint shadow-glow-mint"></span>
          </div>
          <span class="text-xs font-mono font-bold tracking-wider uppercase text-slate-300 flex items-center gap-2">
            <span>{{ isVi ? 'HỆ THỐNG TRỰC TUYẾN' : 'ECOSYSTEM ONLINE' }}</span>
            <span class="text-slate-600">|</span>
            <span class="text-phantom-mint">{{ isVi ? '5 TRỤ CỘT HOẠT ĐỘNG' : '5 ACTIVE PILLARS' }}</span>
          </span>
        </div>

        <!-- Pillars Scrolling / Flex Strip -->
        <div class="w-full lg:w-auto overflow-x-auto no-scrollbar py-1">
          <div class="flex items-center gap-2 sm:gap-3 shrink-0 min-w-max">
            <a
              v-for="item in pillarsStatus"
              :key="item.id"
              :href="item.href"
              class="group flex items-center gap-2 px-3 py-1.5 rounded-lg border bg-white/[0.02] hover:bg-white/[0.06] transition-all duration-200"
              :class="[item.borderColor]"
            >
              <span class="h-1.5 w-1.5 rounded-full" :class="[item.dotBg]"></span>
              <span class="text-xs font-medium text-slate-200 group-hover:text-white transition-colors">
                {{ item.label }}
              </span>
              <span class="text-[11px] font-mono px-1.5 py-0.5 rounded" :class="[item.bgBadge, item.color]">
                {{ item.badge }}
              </span>
              <Icons name="ChevronRight" size="12" class="text-slate-500 group-hover:translate-x-0.5 transition-transform" />
            </a>
          </div>
        </div>
      </div>
    </div>
  </aside>
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
