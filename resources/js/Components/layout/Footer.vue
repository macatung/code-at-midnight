<script setup lang="ts">
import { ref } from 'vue';
import confetti from 'canvas-confetti';
import { sound } from '@/audio/soundEffects';
import Icons from '@/Components/ui/Icons.vue';
import MiniMascotLogo from '@/Components/mascot/MiniMascotLogo.vue';
import { useI18n } from '@/composables/useI18n';

const heartClicks = ref(0);
const { t } = useI18n();

const scrollToTop = () => {
  sound.playHop(1.2);
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const triggerHeartEasterEgg = (event: MouseEvent) => {
  heartClicks.value++;
  sound.playSuccess();
  const rect = (event.currentTarget as HTMLElement).getBoundingClientRect();
  confetti({ particleCount: 45, spread: 60, origin: { x: (rect.left + rect.width / 2) / window.innerWidth, y: (rect.top + rect.height / 2) / window.innerHeight }, colors: ['#00f5a0', '#ffd166', '#ff0054', '#9d4edd'], disableForReducedMotion: true });
};
</script>

<template>
  <footer class="relative z-10 max-w-7xl mx-auto w-full border-t border-white/5 px-4 py-12 text-left sm:px-6 lg:px-8">
    <div class="flex flex-col items-center justify-between gap-4 border-b border-white/5 pb-8 sm:flex-row">
      <div class="flex items-center gap-3"><MiniMascotLogo size="sm" :animated="true" /><span class="font-display font-bold text-base text-white">macatung<span class="text-phantom-mint">.dev</span></span></div>
      <button type="button" class="inline-flex min-h-[44px] items-center gap-2 rounded-xl border border-white/10 bg-midnight-900 px-4 py-2.5 text-xs font-mono text-slate-300 transition hover:border-phantom-mint/40 hover:text-phantom-mint" @click="scrollToTop">{{ t('footer.backToTop') }} <Icons name="ChevronUp" :size="16" /></button>
    </div>

    <div class="grid grid-cols-2 gap-8 border-b border-white/5 py-10 text-xs font-sans md:grid-cols-4">
      <div>
        <h4 class="mb-3 font-mono text-[11px] uppercase tracking-wider text-slate-400">{{ t('footer.platforms') }}</h4>
        <ul class="space-y-2 text-slate-400">
          <li>
            <a href="/theravada" class="hover:text-amber-300 inline-flex items-center gap-1.5" title="Theravāda Buddhist Platform">
              {{ t('footer.theravadaPlatform') }}
              <span class="text-[9px] px-1 py-0.5 rounded bg-amber-500/20 text-amber-300 font-mono">ZEN</span>
            </a>
          </li>
          <li>
            <a href="/decode" class="hover:text-[#00f5d4] inline-flex items-center gap-1.5" title="Decode Media Series">
              {{ t('footer.decodeSeries') }}
              <span class="text-[9px] px-1 py-0.5 rounded bg-[#00f5d4]/20 text-[#00f5d4] font-mono">NEW</span>
            </a>
          </li>
          <li>
            <a href="/hoantien" class="hover:text-phantom-mint inline-flex items-center gap-1.5" title="Shopee Cashback Utility">
              {{ t('footer.cashbackPlatform') }}
              <span class="text-[9px] px-1 py-0.5 rounded bg-phantom-mint/20 text-phantom-mint font-mono">80%</span>
            </a>
          </li>
        </ul>
      </div>
      <div>
        <h4 class="mb-3 font-mono text-[11px] uppercase tracking-wider text-slate-400">{{ t('footer.softwareTools') }}</h4>
        <ul class="space-y-2 text-slate-400">
          <li>
            <a href="/desktop" class="hover:text-sky-300 inline-flex items-center gap-1.5" title="Task Companion Windows Desktop">
              {{ t('footer.taskCompanion') }}
              <span class="text-[9px] px-1 py-0.5 rounded bg-blue-500/20 text-sky-300 font-mono">Win</span>
            </a>
          </li>
          <li>
            <a href="/talisman" class="hover:text-phantom-mint">{{ t('footer.talismanForge') }}</a>
          </li>
          <li>
            <a href="/game" class="hover:text-phantom-mint">{{ t('footer.runeTyper') }}</a>
          </li>
        </ul>
      </div>
      <div>
        <h4 class="mb-3 font-mono text-[11px] uppercase tracking-wider text-slate-400">{{ t('footer.architecture') }}</h4>
        <ul class="space-y-2 text-slate-400">
          <li>
            <a href="/projects" class="hover:text-phantom-mint">{{ t('projects.title') }}</a>
          </li>
          <li>
            <a href="/blog" class="hover:text-phantom-mint">{{ t('footer.engineeringLog') }}</a>
          </li>
          <li>
            <a href="/contact" class="hover:text-phantom-mint">{{ t('footer.systemStatus') }}</a>
          </li>
        </ul>
      </div>
      <div>
        <h4 class="mb-3 font-mono text-[11px] uppercase tracking-wider text-slate-400">{{ t('footer.studioCommunity') }}</h4>
        <ul class="space-y-2 text-slate-400">
          <li>
            <a href="/contact" class="hover:text-phantom-mint">{{ t('footer.community') }}</a>
          </li>
          <li>
            <a href="https://github.com/macatung" target="_blank" rel="noopener noreferrer" class="hover:text-phantom-mint inline-flex items-center gap-1">
              GitHub
              <span class="text-[9px] font-mono opacity-60">↗</span>
            </a>
          </li>
          <li>
            <a href="/admin" class="hover:text-phantom-mint">{{ t('footer.admin') }}</a>
          </li>
        </ul>
      </div>
    </div>

    <div class="flex flex-col items-center justify-between gap-4 pt-8 text-xs font-mono text-slate-500 sm:flex-row">
      <p>{{ t('footer.copyright') }}</p>
      <div class="flex items-center gap-1.5">
        <span>{{ t('footer.crafted') }}</span>
        <button type="button" class="flex min-h-[32px] min-w-[32px] items-center justify-center p-1 text-rose-400" :title="t('footer.heartTitle')" @click="triggerHeartEasterEgg">❤️</button>
        <span v-if="heartClicks" class="font-bold text-phantom-mint">({{ heartClicks }})</span>
      </div>
    </div>
  </footer>
</template>
