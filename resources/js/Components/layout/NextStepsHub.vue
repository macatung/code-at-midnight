<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { sound } from '@/audio/soundEffects';
import { useI18n } from '@/composables/useI18n';

interface Props {
  currentPath: string;
}

const props = defineProps<Props>();
const { t, locale } = useI18n();

const portalCopy = {
  en: {
    projects: ['Project Grimoire', '6+ production projects, AI agents & GIS telecom.', 'ENTERPRISE'], blog: ['Midnight Tech Chronicle', 'Deep notes on Multi-Agent architecture & systems optimization.', 'TECH NOTES'], game: ['Rune Typer Arcade', 'Exorcist typing arena with mechanical thock & Boss Bugs.', 'ARCADE'], talisman: ['Developer Talisman Forge', 'Customize developer charms and export crisp artwork.', 'DEV FORGE'], contact: ['Summoning Altar', 'Request technical consulting and connect with the architect.', 'CONSULTING'],
  },
  vi: {
    projects: ['Kho Grimoire Dự Án', '6+ dự án thực chiến, AI Agent & viễn thông GIS.', 'ENTERPRISE'], blog: ['Midnight Tech Chronicle', 'Ghi chép về kiến trúc Multi-Agent & tối ưu hệ thống.', 'TECH NOTES'], game: ['Phòng Máy Rune Typer', 'Sàn đấu gõ phím trừ tà, âm thanh thock & Boss Bug.', 'ARCADE'], talisman: ['Lò Rèn Bùa Hộ Mệnh', 'Tùy biến bùa hộ mệnh và xuất file ảnh sắc nét.', 'DEV FORGE'], contact: ['Điện Thờ Triệu Hồi', 'Gửi yêu cầu tư vấn kỹ thuật và kết nối kiến trúc sư.', 'CONSULTING'],
  },
};

const allPortals = computed(() => {
  const copy = portalCopy[locale.value];
  return [
  {
    path: '/projects',
    title: copy.projects[0], desc: copy.projects[1], badge: copy.projects[2],
    icon: '💼',
    color: 'border-phantom-mint/30 hover:border-phantom-mint text-phantom-mint',
  },
  {
    path: '/blog',
    title: copy.blog[0], desc: copy.blog[1], badge: copy.blog[2],
    icon: '📜',
    color: 'border-talisman-gold/30 hover:border-talisman-gold text-talisman-gold',
  },
  {
    path: '/game',
    title: copy.game[0], desc: copy.game[1], badge: copy.game[2],
    icon: '🎮',
    color: 'border-amber-400/30 hover:border-amber-400 text-amber-400',
  },
  {
    path: '/talisman',
    title: copy.talisman[0], desc: copy.talisman[1], badge: copy.talisman[2],
    icon: '✨',
    color: 'border-emerald-400/30 hover:border-emerald-400 text-emerald-400',
  },
  {
    path: '/contact',
    title: copy.contact[0], desc: copy.contact[1], badge: copy.contact[2],
    icon: '📜',
    color: 'border-purple-400/30 hover:border-purple-400 text-purple-400',
  },
  ];
});

// Filter out current page and take 3 items
const portals = computed(() => allPortals.value.filter(p => !props.currentPath.startsWith(p.path)).slice(0, 3));
</script>

<template>
  <section class="w-full pt-16 pb-6 border-t border-white/10 mt-16 text-left">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
      <div>
        <span class="text-xs font-mono text-slate-400 flex items-center gap-1.5 uppercase tracking-wider mb-1">
          <span>🔮</span>
          <span>{{ t('common.nextRealms') }}</span>
        </span>
        <h3 class="text-2xl sm:text-3xl font-display font-bold text-white">
          {{ t('common.continue') }}
        </h3>
      </div>
      <Link
        href="/contact"
        class="text-xs font-mono text-phantom-mint hover:underline flex items-center gap-1"
        @click="sound.playTalisman()"
      >
        <span>{{ t('common.consult') }}</span>
        <span>→</span>
      </Link>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <Link
        v-for="portal in portals"
        :key="portal.path"
        :href="portal.path"
        class="glass-panel p-6 rounded-3xl border transition-all duration-300 flex flex-col justify-between group hover:shadow-xl hover:-translate-y-1 bg-midnight-900/60"
        :class="portal.color"
        @click="sound.playClick()"
      >
        <div>
          <div class="flex items-center justify-between mb-4">
            <span class="text-3xl">{{ portal.icon }}</span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/5 border border-white/10 text-slate-300">
              {{ portal.badge }}
            </span>
          </div>

          <h4 class="font-display font-bold text-lg text-white group-hover:text-current transition-colors mb-2">
            {{ portal.title }}
          </h4>

          <p class="text-xs text-slate-300 leading-relaxed font-sans">
            {{ portal.desc }}
          </p>
        </div>

        <div class="text-xs font-mono mt-6 flex items-center gap-1.5 font-bold group-hover:translate-x-1 transition-transform">
          <span>{{ t('common.enter') }}</span>
          <span>→</span>
        </div>
      </Link>
    </div>
  </section>
</template>
