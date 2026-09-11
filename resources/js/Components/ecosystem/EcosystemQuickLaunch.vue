<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Icons from '@/Components/ui/Icons.vue';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

const downloadUrl = 'https://github.com/macatung/code-at-midnight/releases/latest/download/Task-Companion-Setup.exe';

const platforms = computed(() => [
  {
    id: 'theravada',
    title: 'Theravāda Buddhist Platform',
    desc: isVi.value ? 'Kinh điển Pāḷi Tipiṭaka & 10 bài học Pāḷi' : 'Tipiṭaka scriptures & 10 Pāḷi lessons',
    href: '/theravada',
    badge: 'theravada.macatung.dev',
    color: 'text-amber-400 border-amber-500/30 hover:border-amber-400 bg-amber-500/10',
  },
  {
    id: 'decode',
    title: 'Decode Media Series',
    desc: isVi.value ? 'Khảo cứu kiến trúc hệ thống ngầm toàn cầu' : 'CAD blueprints of invisible global systems',
    href: '/decode',
    badge: 'decode.macatung.dev',
    color: 'text-cyan-400 border-cyan-500/30 hover:border-cyan-400 bg-cyan-500/10',
  },
  {
    id: 'cashback',
    title: 'Shopee Cashback Portal',
    desc: isVi.value ? 'Chuyển giao 80% hoa hồng Shopee trực tiếp' : '80% affiliate commission pass-through',
    href: '/hoantien',
    badge: 'hoantien.macatung.dev',
    color: 'text-emerald-400 border-emerald-500/30 hover:border-emerald-400 bg-emerald-500/10',
  },
  {
    id: 'desktop',
    title: 'Task Companion Desktop',
    desc: isVi.value ? 'Ứng dụng Windows 10/11 x64 điều phối AI Agent' : 'Windows 10/11 x64 AI agent orchestrator',
    href: '/desktop',
    badge: 'Windows 10/11 x64',
    color: 'text-phantom-mint border-phantom-mint/30 hover:border-phantom-mint bg-phantom-mint/10',
  },
  {
    id: 'tools',
    title: 'Interactive Digital Labs',
    desc: isVi.value ? 'Lò rèn bùa chú Dev, Rune Typer & tiện ích' : 'Talisman Forge, Rune Typer game & CLI',
    href: '/talisman',
    badge: '/talisman · /game · /tools',
    color: 'text-purple-400 border-purple-500/30 hover:border-purple-400 bg-purple-500/10',
  },
]);
</script>

<template>
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10" aria-label="Ecosystem Quick Launchpad">
    <div class="rounded-3xl border border-white/10 bg-midnight-900/60 p-8 sm:p-12 backdrop-blur-xl">
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 border-b border-white/10">
        <div>
          <span class="text-xs font-mono text-phantom-mint uppercase tracking-widest font-bold">
            {{ isVi ? 'TRUY CẬP TỨC THÌ' : 'INSTANT ACCESS' }}
          </span>
          <h2 class="text-2xl sm:text-4xl font-display font-black text-white mt-2">
            {{ isVi ? 'Sẵn Sàng Khám Phá Toàn Bộ Hệ Sinh Thái?' : 'Ready to Explore the Ecosystem?' }}
          </h2>
          <p class="text-slate-400 text-sm sm:text-base mt-2 max-w-xl">
            {{ isVi
              ? 'Truy cập trực tiếp vào các cổng dịch vụ, đọc bài nghiên cứu chuyên sâu hoặc tải bộ cài ứng dụng cho Windows.'
              : 'Directly launch portal services, read deep technical research, or install the Windows desktop application.'
            }}
          </p>
        </div>

        <div class="flex flex-wrap gap-3 shrink-0">
          <a
            :href="downloadUrl"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-phantom-mint hover:bg-emerald-400 text-midnight-950 font-bold text-xs tracking-wide shadow-glow-mint transition-all duration-200 hover:-translate-y-0.5"
          >
            <Icons name="Download" size="14" />
            <span>{{ isVi ? 'Tải Task Companion (.exe)' : 'Download Task Companion' }}</span>
          </a>
          <a
            href="/hoantien"
            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-white/15 bg-white/5 hover:bg-white/10 text-white font-bold text-xs tracking-wide transition-all duration-200"
          >
            <span>{{ isVi ? 'Cổng Hoàn Tiền' : 'Cashback Portal' }}</span>
          </a>
        </div>
      </div>

      <!-- Quick Platform Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-8">
        <a
          v-for="platform in platforms"
          :key="platform.id"
          :href="platform.href"
          class="p-4 rounded-2xl border border-white/10 bg-white/[0.02] hover:bg-white/[0.05] transition-all duration-200 flex flex-col justify-between group"
        >
          <div>
            <span class="inline-block text-[10px] font-mono px-2 py-0.5 rounded border mb-2.5" :class="platform.color">
              {{ platform.badge }}
            </span>
            <div class="text-sm font-bold text-white group-hover:text-phantom-mint transition-colors">
              {{ platform.title }}
            </div>
            <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
              {{ platform.desc }}
            </p>
          </div>
          <div class="mt-4 flex items-center gap-1 text-xs font-mono text-slate-400 group-hover:text-phantom-mint transition-colors">
            <span>{{ isVi ? 'Truy cập' : 'Open' }}</span>
            <Icons name="ChevronRight" size="13" class="group-hover:translate-x-1 transition-transform" />
          </div>
        </a>
      </div>
    </div>
  </section>
</template>
