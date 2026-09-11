<script setup lang="ts">
import { computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Icons from '@/Components/ui/Icons.vue';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

const principles = computed(() => [
  {
    icon: 'Zap',
    title: isVi.value ? 'Độ Trễ Cực Thấp (< 18ms)' : 'Ultra-Low Latency (< 18ms)',
    desc: isVi.value
      ? 'Bộ nhớ đệm đa tầng (multi-tier cache), index cơ sở dữ liệu chuyên sâu, pipeline bất đồng bộ non-blocking và tối ưu hóa hạ tầng CDN toàn cầu.'
      : 'Multi-tier distributed caching, deep database index optimizations, non-blocking asynchronous event loops, and edge CDN delivery.',
    accent: 'text-cyan-400 border-cyan-500/20 bg-cyan-500/10',
  },
  {
    icon: 'Shield',
    title: isVi.value ? 'Kiến Trúc Phòng Vệ Zero-Crash' : 'Defense-in-Depth & Zero-Crash',
    desc: isVi.value
      ? '100% Strict TypeScript, kiểm thử tự động toàn diện (E2E + Feature), tuân thủ nghiêm ngặt chuẩn an toàn OWASP và bảo vệ tài chính đa tầng.'
      : '100% strict TypeScript types, comprehensive automated test suites, OWASP-compliant API hardening, and transactional ledger safety.',
    accent: 'text-emerald-400 border-emerald-500/20 bg-emerald-500/10',
  },
  {
    icon: 'Cpu',
    title: isVi.value ? 'Điều Phối AI Agent Tự Trị & MCP' : 'Autonomous AI Agents & MCP',
    desc: isVi.value
      ? 'Tích hợp chuẩn AWS Labs CAO và giao thức Model Context Protocol (MCP) kết nối các AI agent (Codex, Claude, Antigravity) với quy trình phát triển thực tế.'
      : 'Built on AWS Labs CAO orchestration and the Model Context Protocol (MCP), bridging local agent runtimes with supervised workflows.',
    accent: 'text-phantom-mint border-phantom-mint/20 bg-phantom-mint/10',
  },
  {
    icon: 'Heart',
    title: isVi.value ? 'Công Nghệ Vị Nhân Sinh' : 'Humanistic Engineering',
    desc: isVi.value
      ? 'Phần mềm được kiến tạo để khuếch đại tiềm năng và sự tĩnh tại của con người — từ bảo tồn kinh điển Phật giáo đến sự minh bạch tài chính tuyệt đối.'
      : 'Software engineered to amplify human clarity and insight — from authentic Dhamma preservation to radical financial transparency.',
    accent: 'text-amber-400 border-amber-500/20 bg-amber-500/10',
  },
]);
</script>

<template>
  <section class="border-t border-white/5 bg-black/20 py-20 relative z-10" aria-label="Ecosystem Architecture Principles">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center max-w-3xl mx-auto mb-14">
        <p class="font-mono text-xs uppercase tracking-[.25em] text-phantom-mint mb-3">
          {{ isVi ? 'NGUYÊN TẮC KỸ NGHỆ' : 'ENGINEERING CRAFT' }}
        </p>
        <h2 class="text-3xl sm:text-4xl font-display font-black text-white">
          {{ isVi ? 'Tiêu Chuẩn Kiến Trúc & Chất Lượng Phần Mềm' : 'Architecture & Quality Standards' }}
        </h2>
        <p class="mt-3 text-sm sm:text-base text-slate-400 leading-relaxed">
          {{ isVi
            ? 'Những trụ cột kỹ thuật nền tảng định hình độ tin cậy và hiệu năng của toàn bộ hệ sinh thái MacaTung.'
            : 'Core architectural commitments governing performance, stability, and integrity across the MacaTung ecosystem.'
          }}
        </p>
      </div>

      <!-- Principles Grid -->
      <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <article
          v-for="item in principles"
          :key="item.title"
          class="rounded-2xl border border-white/10 bg-midnight-900/60 p-6 backdrop-blur-sm transition-all duration-300 hover:border-white/20 hover:-translate-y-1 hover:bg-midnight-900/90"
        >
          <div class="inline-flex p-3 rounded-xl border mb-4" :class="item.accent">
            <Icons :name="item.icon" size="20" />
          </div>
          <h3 class="font-display text-lg font-bold text-white mb-2">
            {{ item.title }}
          </h3>
          <p class="text-sm leading-relaxed text-slate-400">
            {{ item.desc }}
          </p>
        </article>
      </div>
    </div>
  </section>
</template>
