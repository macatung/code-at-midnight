<script setup lang="ts">
import { computed } from 'vue';

interface PairedArticle {
  id?: number;
  title: string;
  pali_title?: string;
  slug: string;
  excerpt?: string;
  site_domain: string;
  reading_time_min?: number;
  url: string;
  subdomain_url?: string;
  main_domain_url?: string;
}

const props = defineProps<{
  pairedArticle?: PairedArticle | null;
  currentType: 'dev' | 'theravada';
}>();

const targetHref = computed(() => {
  if (!props.pairedArticle) return '#';
  return props.pairedArticle.url;
});
</script>

<template>
  <div v-if="pairedArticle" class="my-10 sm:my-14">
    <!-- Life Card connecting to Theravada -->
    <div
      v-if="currentType === 'dev'"
      class="relative overflow-hidden rounded-3xl border border-amber-500/40 bg-gradient-to-br from-stone-950 via-amber-950/30 to-stone-950 p-6 sm:p-10 shadow-2xl"
    >
      <div class="absolute -top-16 -right-16 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="relative z-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-sans font-bold tracking-wider mb-4 border border-amber-500/30">
          <span>🧘 QUÁN CHIẾU ĐỐI ỨNG • DHAMMA REFLECTION</span>
        </div>

        <h3 class="text-xl sm:text-3xl font-serif font-extrabold text-amber-100 mb-3">
          {{ pairedArticle.title }}
        </h3>

        <p v-if="pairedArticle.pali_title" class="text-sm font-serif italic text-amber-300/90 mb-4">
          Pāḷi: {{ pairedArticle.pali_title }}
        </p>

        <p v-if="pairedArticle.excerpt" class="text-sm sm:text-base text-stone-300 leading-relaxed font-serif mb-6 max-w-3xl">
          {{ pairedArticle.excerpt }}
        </p>

        <div class="flex flex-wrap items-center gap-4">
          <a
            :href="targetHref"
            class="inline-flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 px-6 py-3 text-sm sm:text-base font-bold text-stone-950 shadow-lg transition-all duration-300 hover:from-amber-300 hover:to-amber-400 hover:shadow-amber-500/25 hover:scale-105 active:scale-95"
          >
            <span>Khám phá toàn văn bài quán chiếu</span>
            <span class="text-lg">➔</span>
          </a>
          <span class="text-xs text-stone-400 font-sans">
            ⏱️ {{ pairedArticle.reading_time_min || 7 }} phút đọc sâu
          </span>
        </div>
      </div>
    </div>

    <!-- Theravada Card connecting to Life -->
    <div
      v-else
      class="relative overflow-hidden rounded-3xl border border-emerald-500/40 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 p-6 sm:p-10 shadow-2xl"
    >
      <div class="absolute -top-16 -right-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="relative z-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-sans font-bold tracking-wider mb-4 border border-emerald-500/30">
          <span>🌱 GÓC NHÌN ĐỐI ỨNG • ĐỜI SỐNG & TÂM LÝ THƯỜNG NHẬT</span>
        </div>

        <h3 class="text-xl sm:text-3xl font-sans font-extrabold text-slate-100 mb-3">
          {{ pairedArticle.title }}
        </h3>

        <p v-if="pairedArticle.excerpt" class="text-sm sm:text-base text-slate-300 leading-relaxed font-sans mb-6 max-w-3xl">
          {{ pairedArticle.excerpt }}
        </p>

        <div class="flex flex-wrap items-center gap-4">
          <a
            :href="targetHref"
            class="inline-flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-emerald-400 to-emerald-500 px-6 py-3 text-sm sm:text-base font-bold text-slate-950 shadow-lg transition-all duration-300 hover:from-emerald-300 hover:to-emerald-400 hover:shadow-emerald-500/25 hover:scale-105 active:scale-95"
          >
            <span>Đọc toàn văn bài chia sẻ Đời thường</span>
            <span class="text-lg">➔</span>
          </a>
          <span class="text-xs text-slate-400 font-sans">
            ⏱️ {{ pairedArticle.reading_time_min || 6 }} phút đọc
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
