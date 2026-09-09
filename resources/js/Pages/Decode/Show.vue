<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import DecodeLayout from '@/Layouts/DecodeLayout.vue';
import Icons from '@/Components/ui/Icons.vue';

interface KeyNode {
  step: number;
  time: string;
  node: string;
  protocol: string;
  action: string;
  latency: string;
  status: string;
}

interface Episode {
  id: number;
  episode_number: string;
  slug: string;
  title: string;
  short_title: string;
  duration: string;
  total_latency: string;
  category: string;
  summary: string;
  hook: string;
  tags: string[];
  status: string;
  key_nodes: KeyNode[];
}

interface EpisodeSummary {
  id: number;
  slug: string;
  episode_number: string;
  short_title: string;
  title: string;
  total_latency: string;
}

defineProps<{
  episode: Episode;
  allEpisodes: EpisodeSummary[];
}>();
</script>

<template>
  <DecodeLayout
    :title="'Tập ' + episode.episode_number + ': ' + episode.short_title"
    :description="episode.summary"
  >
    <!-- Episode Banner Header -->
    <section class="relative pt-12 pb-16 bg-[#070d1a] border-b border-[#00b4d8]/20">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-left space-y-6">
        <!-- Breadcrumb / Back Link -->
        <div class="flex items-center gap-2 font-mono text-xs text-slate-400">
          <Link href="/decode" class="text-[#00f5d4] hover:underline flex items-center gap-1">
            <Icons name="ChevronRight" :size="14" class="rotate-180" />
            <span>Pilot Season</span>
          </Link>
          <span>/</span>
          <span class="text-white">Tập {{ episode.episode_number }}</span>
        </div>

        <!-- Meta Tags Row -->
        <div class="flex flex-wrap items-center gap-3">
          <span class="px-3 py-1 rounded-full bg-[#00f5d4]/15 border border-[#00f5d4]/40 text-[#00f5d4] font-mono text-xs font-bold">
            TẬP {{ episode.episode_number }}
          </span>
          <span class="px-3 py-1 rounded-full bg-[#0a1832] border border-[#00b4d8]/30 text-[#38bdf8] font-mono text-xs">
            {{ episode.category }}
          </span>
          <span class="px-3 py-1 rounded-full bg-[#0a1832] border border-amber-500/30 text-amber-400 font-mono text-xs">
            Độ trễ: {{ episode.total_latency }}
          </span>
          <span class="text-slate-400 font-mono text-xs flex items-center gap-1">
            <Icons name="Clock" :size="12" /> Thời lượng: {{ episode.duration }}
          </span>
        </div>

        <!-- Main Title -->
        <h1 class="font-display text-3xl sm:text-5xl font-black text-white leading-tight">
          {{ episode.title }}
        </h1>

        <!-- Hook / Lead In -->
        <p class="text-base sm:text-xl text-slate-300 font-sans leading-relaxed max-w-3xl">
          {{ episode.hook }}
        </p>

        <!-- Tags Row -->
        <div class="flex flex-wrap gap-2 pt-2">
          <span
            v-for="(tag, idx) in episode.tags"
            :key="idx"
            class="text-xs font-mono px-2.5 py-1 rounded bg-white/5 border border-white/10 text-slate-300"
          >
            #{{ tag }}
          </span>
        </div>
      </div>
    </section>

    <!-- Main Content Container -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16 text-left">
      <!-- Section: Key Nodes Timeline Breakdown -->
      <div class="space-y-6">
        <div class="flex items-center justify-between border-b border-[#00b4d8]/20 pb-4">
          <div class="flex items-center gap-2 text-sm font-mono text-[#00f5d4] uppercase font-bold tracking-wider">
            <Icons name="Activity" :size="16" />
            <span>Phân Rã Timeline Từng Giây</span>
          </div>
          <span class="text-xs font-mono text-slate-400">{{ episode.key_nodes.length }} Trạm Kiểm Soát</span>
        </div>

        <div class="space-y-4">
          <div
            v-for="(node, nIdx) in episode.key_nodes"
            :key="nIdx"
            class="p-5 sm:p-6 rounded-2xl bg-[#080f1e] border border-white/5 hover:border-[#00b4d8]/40 transition-all flex flex-col sm:flex-row sm:items-start gap-4"
          >
            <!-- Node Step Circle -->
            <div class="w-12 h-12 rounded-xl bg-[#0a1832] border border-[#00f5d4]/40 flex items-center justify-center font-mono font-bold text-[#00f5d4] text-lg shrink-0">
              0{{ node.step }}
            </div>

            <!-- Content Area -->
            <div class="flex-1 space-y-2">
              <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-display font-bold text-lg text-white">
                  {{ node.node }}
                </h3>
                <div class="flex items-center gap-2 font-mono text-xs">
                  <span class="px-2 py-0.5 rounded bg-[#060a14] border border-white/10 text-amber-400 font-semibold">
                    {{ node.time }}
                  </span>
                  <span class="px-2 py-0.5 rounded bg-[#00f5d4]/10 border border-[#00f5d4]/30 text-[#00f5d4]">
                    {{ node.status }}
                  </span>
                </div>
              </div>

              <!-- Protocol -->
              <div class="text-xs font-mono text-[#38bdf8]">
                Giao thức / Chuẩn kỹ thuật: {{ node.protocol }}
              </div>

              <!-- Action Detail -->
              <p class="text-sm text-slate-300 font-sans leading-relaxed pt-1">
                {{ node.action }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Episode Switcher Navigation -->
      <div class="pt-8 border-t border-[#00b4d8]/20 space-y-6">
        <h3 class="font-display font-bold text-xl text-white">Các Tập Khác Trong Pilot Season</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <Link
            v-for="item in allEpisodes"
            :key="item.id"
            :href="`/decode/tap/${item.slug}`"
            class="p-4 rounded-xl border transition-all text-left space-y-1 group"
            :class="item.slug === episode.slug ? 'bg-[#00f5d4]/15 border-[#00f5d4]' : 'bg-[#080e1c] border-white/5 hover:border-[#00b4d8]/40'"
          >
            <div class="flex items-center justify-between text-[11px] font-mono">
              <span class="text-[#00f5d4] font-bold">TẬP {{ item.episode_number }}</span>
              <span class="text-slate-400">{{ item.total_latency }}</span>
            </div>
            <div class="font-display font-bold text-sm text-white group-hover:text-[#00f5d4] transition-colors line-clamp-1">
              {{ item.short_title }}
            </div>
          </Link>
        </div>
      </div>
    </div>
  </DecodeLayout>
</template>
