<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Icons from '@/Components/ui/Icons.vue';
import { sound } from '@/audio/soundEffects';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

const isBlessed = ref(false);
const blessText = ref('');

const invokeTalismanBlessing = () => {
  sound.playTalisman();
  isBlessed.value = true;
  blessText.value = isVi.value ? '✨ Đã Khai Quang Phúc Khí 0-Bug!' : '✨ 0-Bug Blessing Invoked!';
  setTimeout(() => {
    isBlessed.value = false;
    blessText.value = '';
  }, 3500);
};

const miniTools = computed(() => [
  {
    id: 'talisman',
    title: isVi.value ? 'Lò Rèn Bùa Hộ Mệnh Dev' : 'Developer Talisman Forge',
    badge: 'Talisman Canvas',
    desc: isVi.value ? 'Bùa Trừ Bug, Bùa Deploy Thứ 6, nghi thức Khai Quang âm thanh và xuất thẻ ASCII/HD.' : 'Bug-banishing charms, Friday deploy protection, Web Audio ritual & ASCII export.',
    href: '/talisman',
    icon: 'Sparkles',
    accentColor: 'text-amber-400 border-amber-500/20 bg-amber-500/10',
  },
  {
    id: 'game',
    title: isVi.value ? 'Sàn Đấu Trừ Tà Rune Typer' : 'Rune Typer Arcade Chamber',
    badge: 'Arcade Arcade',
    desc: isVi.value ? 'Game gõ phím cơ trảm bug, 3 cấp độ thử thách, combo x5 và bảng phong thần kỷ lục.' : 'Mechanical keyboard arcade game, 3 difficulty tiers, combo multipliers & high scores.',
    href: '/game',
    icon: 'Gamepad',
    accentColor: 'text-purple-400 border-purple-500/20 bg-purple-500/10',
  },
  {
    id: 'tools',
    title: isVi.value ? 'Tiện Ích Lập Trình & CLI' : 'Dev Utilities & Shell',
    badge: 'CLI & Tools',
    desc: isVi.value ? 'Bộ công cụ tính toán hash, định dạng JSON/YAML, tra cứu timestamp và terminal REPL.' : 'Hash calculators, JSON/YAML tools, Unix timestamp helpers & interactive terminal shell.',
    href: '/tools',
    icon: 'Terminal',
    accentColor: 'text-cyan-400 border-cyan-500/20 bg-cyan-500/10',
  },
]);
</script>

<template>
  <article
    class="relative group rounded-3xl border border-purple-500/20 bg-gradient-to-b from-[#140b1e] via-midnight-900 to-midnight-950 p-6 sm:p-8 overflow-hidden transition-all duration-300 hover:border-purple-500/40 hover:shadow-2xl hover:shadow-purple-500/10 flex flex-col justify-between"
    aria-label="Pillar 5: Interactive Digital Tools and Mini-Apps"
  >
    <!-- Ambient Purple Glow -->
    <div class="absolute -right-20 -top-20 w-72 h-72 bg-purple-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-purple-500/15 transition-all duration-500"></div>

    <div>
      <!-- Header Badges -->
      <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-purple-500/15 text-purple-400 border border-purple-500/30">
            PILLAR 05
          </span>
          <span class="px-2.5 py-1 rounded-full text-xs font-mono bg-slate-900 text-slate-300 border border-white/10 flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-purple-400 animate-pulse"></span>
            Interactive Labs
          </span>
        </div>

        <!-- Interactive Talisman Blessing Trigger -->
        <button
          type="button"
          @click="invokeTalismanBlessing"
          class="relative inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border text-xs font-medium transition-all duration-200"
          :class="isBlessed ? 'border-amber-400 bg-amber-500/20 text-amber-200' : 'border-purple-500/30 bg-purple-950/40 text-purple-300 hover:bg-purple-500/20 hover:border-purple-400'"
          :title="isVi ? 'Bấm để nghe âm thanh Khai Quang Bùa Chú' : 'Click to invoke talisman chime sound'"
        >
          <span v-if="isBlessed" class="absolute inset-0 rounded-xl bg-amber-400/20 animate-ping"></span>
          <Icons name="Sparkles" size="14" :class="{ 'animate-spin text-amber-300': isBlessed }" />
          <span>{{ isBlessed ? blessText : (isVi ? '✨ Khai Quang Bùa' : '✨ Invoke Blessing') }}</span>
        </button>
      </div>

      <!-- Title & Description -->
      <div class="mb-6">
        <div class="text-xs font-mono uppercase tracking-widest text-purple-400/90 mb-1">
          {{ isVi ? 'PHÒNG THÍ NGHIỆM KỸ THUẬT SỐ & TIỆN ÍCH TƯƠNG TÁC' : 'DIGITAL LABS & INTERACTIVE UTILITIES' }}
        </div>
        <h3 class="text-2xl sm:text-3xl font-display font-black tracking-tight text-white group-hover:text-purple-300 transition-colors">
          {{ isVi ? 'Phòng Thí Nghiệm Số & Mini-Apps' : 'Interactive Digital Tools & Mini-Apps' }}
        </h3>
        <p class="mt-3 text-sm sm:text-base text-slate-300 leading-relaxed">
          {{ isVi
            ? 'Không gian sáng tạo công nghệ kết hợp tính giải trí và tiện ích thực chiến: Lò rèn bùa trừ bug xuất ảnh Canvas HD, sàn đấu gõ code Rune Typer với âm thanh phím cơ chân thực và bộ tiện ích dòng lệnh CLI.'
            : 'Creative digital tools blending utility with playful craftsmanship: customize and export cybernetic developer talismans, test typing reflexes in the Rune Typer arcade chamber, and access practical developer tools.'
          }}
        </p>
      </div>

      <!-- 3 Sub-app Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
        <a
          v-for="tool in miniTools"
          :key="tool.id"
          :href="tool.href"
          class="p-3.5 rounded-2xl border border-white/10 bg-black/30 hover:border-purple-500/40 hover:bg-purple-950/20 transition-all duration-200 flex flex-col justify-between group/tool"
        >
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="p-1.5 rounded-lg" :class="tool.accentColor">
                <Icons :name="tool.icon" size="16" />
              </span>
              <span class="text-[10px] font-mono text-slate-400 px-1.5 py-0.5 rounded bg-white/5">
                {{ tool.badge }}
              </span>
            </div>
            <div class="text-xs font-bold text-white group-hover/tool:text-purple-300 transition-colors">
              {{ tool.title }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1.5 leading-relaxed">
              {{ tool.desc }}
            </p>
          </div>
          <div class="mt-3 flex items-center gap-1 text-[11px] font-mono text-purple-400 group-hover/tool:text-purple-300">
            <span>{{ isVi ? 'Khởi chạy' : 'Launch' }}</span>
            <Icons name="ChevronRight" size="12" class="group-hover/tool:translate-x-1 transition-transform" />
          </div>
        </a>
      </div>
    </div>

    <!-- Bottom Actions -->
    <div class="pt-4 border-t border-purple-500/20 flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap gap-2 text-xs font-mono text-slate-400">
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-purple-300">Web Audio FX</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-purple-300">Canvas HD</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-purple-300">Zero Install</span>
      </div>

      <div class="flex items-center gap-2">
        <a
          href="/game"
          class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-purple-500/30 bg-purple-950/30 hover:bg-purple-500/20 text-purple-300 text-xs font-mono transition-colors"
        >
          <span>🎮 Rune Typer</span>
        </a>
        <a
          href="/talisman"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-midnight-950 font-bold text-xs tracking-wide transition-all duration-200 hover:-translate-y-0.5 shadow-lg shadow-purple-500/20"
        >
          <span>{{ isVi ? 'Vào Lò Rèn Bùa' : 'Open Talisman Forge' }}</span>
          <Icons name="ChevronRight" size="14" />
        </a>
      </div>
    </div>
  </article>
</template>
