<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Icons from '@/Components/ui/Icons.vue';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

const activeStep = ref(2); // Default to VisaNet Ashburn

const architectureSteps = computed(() => [
  {
    step: '01',
    node: 'EMV Chip POS',
    latency: '45ms',
    action: isVi.value ? 'Tạo Cryptogram ARQC bảo mật' : 'Generates ARQC cryptogram',
    color: 'border-cyan-400/30 text-cyan-300',
  },
  {
    step: '02',
    node: 'Acquiring Bank Gateway',
    latency: '120ms',
    action: isVi.value ? 'Đóng gói điện tín ISO 8583' : 'Formats ISO 8583 message',
    color: 'border-cyan-400/30 text-cyan-300',
  },
  {
    step: '03',
    node: 'VisaNet Ashburn DC',
    latency: '850ms',
    action: isVi.value ? 'Băng qua cáp quang Thái Bình Dương' : 'Crosses trans-Pacific optical fiber',
    color: 'border-cyan-400/50 text-cyan-200',
  },
  {
    step: '04',
    node: 'VAA Real-time AI',
    latency: '1.2ms',
    action: isVi.value ? 'AI chấm điểm rủi ro gian lận' : 'Sub-millisecond fraud scoring',
    color: 'border-emerald-400/50 text-emerald-300',
  },
  {
    step: '05',
    node: 'Issuing Bank Auth',
    latency: '350ms',
    action: isVi.value ? 'Two-Phase Commit khóa số dư' : '2-phase commit balance lock',
    color: 'border-cyan-400/30 text-cyan-300',
  },
]);
</script>

<template>
  <article
    class="relative group rounded-3xl border border-cyan-500/20 bg-gradient-to-b from-[#06121f] via-midnight-900 to-midnight-950 p-6 sm:p-8 overflow-hidden transition-all duration-300 hover:border-cyan-500/40 hover:shadow-2xl hover:shadow-cyan-500/10 flex flex-col justify-between"
    aria-label="Pillar 2: Decode Media Series"
  >
    <!-- Ambient Cyber Glow -->
    <div class="absolute -right-20 -top-20 w-72 h-72 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-cyan-500/15 transition-all duration-500"></div>

    <div>
      <!-- Header Badges -->
      <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-cyan-500/15 text-cyan-400 border border-cyan-500/30">
            PILLAR 02
          </span>
          <span class="px-2.5 py-1 rounded-full text-xs font-mono bg-slate-900 text-slate-300 border border-white/10 flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
            decode.macatung.dev
          </span>
        </div>

        <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-cyan-950/40 border border-cyan-500/20 text-xs font-mono text-cyan-300">
          <Icons name="Clock" size="13" class="text-cyan-400" />
          <span>{{ isVi ? 'Tổng Độ Trễ: 1.85s' : 'Total Latency: 1.85s' }}</span>
        </div>
      </div>

      <!-- Title & Description -->
      <div class="mb-6">
        <div class="text-xs font-mono uppercase tracking-widest text-cyan-400/90 mb-1">
          {{ isVi ? 'SERIES KHẢO CỨU KIẾN TRÚC HỆ THỐNG NGẦM & CAD BLUEPRINT' : 'STRATEGIC SYSTEMS ANALYSIS & CAD BLUEPRINTS' }}
        </div>
        <h3 class="text-2xl sm:text-3xl font-display font-black tracking-tight text-white group-hover:text-cyan-300 transition-colors">
          {{ isVi ? 'Ma Giải Mã — Mở Nắp Các Hệ Thống Vô Hình' : 'Decode — The Architecture of Invisible Systems' }}
        </h3>
        <p class="mt-3 text-sm sm:text-base text-slate-300 leading-relaxed">
          {{ isVi
            ? 'Phân tích chuyên sâu cơ chế kỹ thuật đằng sau các hệ thống vận hành thế giới: mạng thanh toán toàn cầu Visa, công cụ tìm kiếm Google 0.3s, cáp quang xuyên lục địa 250+ Tbps và bài toán Two-Phase Commit ATM.'
            : 'Deep technical dissections of massive systems operating behind everyday reality: the VisaNet global payment mesh, Google 50-billion-page search in 0.3s, trans-oceanic optic fiber cables, and ATM distributed 2-phase commit.'
          }}
        </p>
      </div>

      <!-- Interactive Visa 100k Architecture Diagram Scrubber -->
      <div class="rounded-2xl border border-cyan-500/30 bg-black/40 p-4 sm:p-5 mb-6 backdrop-blur-md">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3 pb-3 border-b border-white/10 text-xs">
          <div class="flex items-center gap-2 text-cyan-300 font-mono font-bold">
            <span class="px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300">TẬP 01</span>
            <span>{{ isVi ? 'Quẹt Thẻ Visa 100k: 2 Giây Du Hành Nửa Vòng Trái Đất' : 'Visa 100k Swipe: 2-Second Global Journey' }}</span>
          </div>
          <span class="text-[11px] font-mono text-slate-400">
            {{ isVi ? 'Bấm vào từng chặng để xem độ trễ' : 'Click step to inspect latency' }}
          </span>
        </div>

        <!-- 5 Interactive Steps -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
          <button
            v-for="(item, idx) in architectureSteps"
            :key="item.step"
            type="button"
            @click="activeStep = idx"
            class="text-left p-2.5 rounded-xl border transition-all duration-200"
            :class="activeStep === idx ? 'border-cyan-400 bg-cyan-500/20 shadow-md shadow-cyan-500/20' : 'border-white/5 bg-white/[0.02] hover:border-cyan-500/30 hover:bg-white/[0.05]'"
          >
            <div class="flex items-center justify-between text-[10px] font-mono mb-1">
              <span :class="activeStep === idx ? 'text-cyan-300 font-bold' : 'text-slate-500'">#{{ item.step }}</span>
              <span class="px-1 py-0.2 rounded bg-black/40 text-cyan-400 font-mono text-[10px]">{{ item.latency }}</span>
            </div>
            <div class="text-xs font-bold truncate text-slate-200" :class="{ 'text-cyan-200': activeStep === idx }">
              {{ item.node }}
            </div>
          </button>
        </div>

        <!-- Active Step Details Card -->
        <div class="mt-3 p-3 rounded-xl bg-cyan-950/30 border border-cyan-500/20 flex items-center justify-between gap-3 text-xs">
          <div class="flex items-center gap-2.5">
            <span class="h-2 w-2 rounded-full bg-cyan-400 animate-ping"></span>
            <span class="text-slate-300">
              <strong class="text-cyan-300">{{ architectureSteps[activeStep].node }}:</strong> {{ architectureSteps[activeStep].action }}
            </span>
          </div>
          <span class="font-mono text-cyan-400 font-bold shrink-0">
            Δ {{ architectureSteps[activeStep].latency }}
          </span>
        </div>
      </div>
    </div>

    <!-- Bottom Actions -->
    <div class="pt-4 border-t border-cyan-500/20 flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap gap-2 text-xs font-mono text-slate-400">
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-cyan-300">Pilot Season (5 Tập)</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-cyan-300">CAD Blueprint</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-cyan-300">Latency Ticker</span>
      </div>

      <div class="flex items-center gap-2">
        <a
          href="/decode/tap/tap-01-quet-the-visa-100k-2-giay-du-hanh"
          class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-cyan-500/30 bg-cyan-950/30 hover:bg-cyan-500/20 text-cyan-300 text-xs font-mono transition-colors"
        >
          <span>Tập 01 (Visa)</span>
        </a>
        <a
          href="/decode"
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-400 hover:bg-cyan-300 text-midnight-950 font-bold text-xs tracking-wide transition-all duration-200 hover:-translate-y-0.5 shadow-lg shadow-cyan-400/20"
        >
          <span>{{ isVi ? 'Xem Toàn Bộ Series' : 'Explore Decode Media' }}</span>
          <Icons name="ChevronRight" size="14" />
        </a>
      </div>
    </div>
  </article>
</template>
