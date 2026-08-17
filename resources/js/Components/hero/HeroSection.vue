<script setup lang="ts">
import { ref } from 'vue';
import MacatungMascot from '@/Components/mascot/MacatungMascot.vue';
import Icons from '@/Components/ui/Icons.vue';
import { sound } from '@/audio/soundEffects';
import { trackEvent } from '@/utils/analytics';

const emit = defineEmits<{
  (e: 'hop', count: number): void;
}>();

const heroHopCount = ref(0);

const handleMascotHop = (count: number) => {
  heroHopCount.value = count;
  emit('hop', count);
};

const handleCtaClick = (soundType: 'click' | 'talisman', eventName?: string) => {
  if (soundType === 'talisman') sound.playTalisman();
  else sound.playClick();

  if (eventName) {
    trackEvent(eventName);
  }
};
</script>

<template>
  <section id="hero" class="relative min-h-[calc(100vh-5rem)] flex items-center justify-center py-12 lg:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center w-full">
      <!-- Left Content Column (7 Columns) -->
      <div class="lg:col-span-7 flex flex-col items-center lg:items-start text-center lg:text-left">
        <!-- Live Status Pill -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-midnight-900/90 border border-phantom-mint/30 text-phantom-mint text-xs font-mono mb-6 shadow-glow-mint select-none whitespace-nowrap">
          <span class="w-2 h-2 rounded-full bg-phantom-mint animate-pulse" />
          <span>🟢 SẴN SÀNG NHẬN QUEST 00:00 AM</span>
        </div>

        <!-- Headline with Clean Minimalist Highlight -->
        <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-display font-extrabold tracking-tight text-white leading-[1.15] mb-6">
          Code at <span class="text-phantom-mint">midnight</span>.
        </h1>

        <!-- Subtitle (Concise & Impactful) -->
        <p class="text-base sm:text-lg text-slate-300 max-w-2xl font-sans leading-relaxed mb-8">
          Kỹ Sư Hệ Thống & Creative Full-Stack Engineer. Chuyển hóa Robusta nguyên chất thành kiến trúc phân tán siêu tốc độ và giao diện web mượt mà.
        </p>

        <!-- CTA Actions (whitespace-nowrap to guarantee single-line button text) -->
        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 sm:gap-4 mb-10 w-full">
          <a
            href="#projects"
            class="px-5 py-3.5 rounded-2xl bg-phantom-mint text-midnight-950 font-display font-bold text-sm sm:text-base hover:brightness-110 active:scale-95 transition-all shadow-glow-mint flex items-center gap-2 min-h-[48px] whitespace-nowrap"
            @click="handleCtaClick('click')"
          >
            <span>Khám Phá Dự Án</span>
            <span>✨</span>
          </a>
          <a
            href="#contact"
            class="px-5 py-3.5 rounded-2xl bg-midnight-900 border border-talisman-gold/40 text-talisman-gold hover:bg-talisman-gold/10 font-display font-bold text-sm sm:text-base transition-all shadow-glow-talisman flex items-center gap-2 min-h-[48px] whitespace-nowrap"
            @click="handleCtaClick('talisman')"
          >
            <span>Triệu Hồi Ngay</span>
            <span>📜</span>
          </a>
          <a
            href="#terminal"
            class="px-4 py-3.5 rounded-2xl bg-midnight-900/80 border border-white/10 hover:border-white/30 text-slate-300 hover:text-white font-mono text-xs sm:text-sm transition-all flex items-center gap-2 min-h-[48px] whitespace-nowrap"
            @click="handleCtaClick('click')"
          >
            <Icons name="Terminal" :size="16" />
            <span>Mở CLI</span>
          </a>
          <a
            href="#contact"
            class="px-4 py-3.5 rounded-2xl bg-white/5 border border-white/10 hover:border-phantom-mint/40 text-slate-300 hover:text-phantom-mint font-mono text-xs sm:text-sm transition-all flex items-center gap-2 min-h-[48px] whitespace-nowrap"
            title="Xem & Tải Hồ Sơ Năng Lực"
            @click="handleCtaClick('click', 'cv_download')"
          >
            <Icons name="FileText" :size="16" />
            <span>Tải CV</span>
          </a>
        </div>

        <!-- Trust Badges -->
        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 text-xs font-mono text-slate-400">
          <div class="flex items-center gap-1.5 whitespace-nowrap">
            <Icons name="Zap" :size="14" class="text-phantom-mint" />
            <span>&gt; 8 Năm Thực Chiến</span>
          </div>
          <div class="flex items-center gap-1.5 whitespace-nowrap">
            <Icons name="Activity" :size="14" class="text-phantom-mint" />
            <span>&lt; 18ms Latency</span>
          </div>
          <div class="flex items-center gap-1.5 whitespace-nowrap">
            <Icons name="Coffee" :size="14" class="text-amber-400" />
            <span>100% Robusta Flow</span>
          </div>
          <div class="flex items-center gap-1.5 whitespace-nowrap">
            <Icons name="Bug" :size="14" class="text-rose-400" />
            <span>0 Bug In Production</span>
          </div>
        </div>
      </div>

      <!-- Right Mascot Column (5 Columns) -->
      <div class="lg:col-span-5 flex justify-center items-center">
        <MacatungMascot
          size="hero"
          :show-controls="true"
          @hop-count-change="handleMascotHop"
        />
      </div>
    </div>
  </section>
</template>
