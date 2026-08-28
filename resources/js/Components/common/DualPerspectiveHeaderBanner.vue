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
  <div v-if="pairedArticle" class="my-6">
    <!-- Banner for Life Article (Linking to Theravada) -->
    <div
      v-if="currentType === 'dev'"
      class="group relative overflow-hidden rounded-2xl border border-amber-500/30 bg-gradient-to-r from-amber-950/40 via-stone-900/60 to-amber-950/20 p-4 sm:p-5 shadow-lg backdrop-blur-md transition-all duration-300 hover:border-amber-400/60 hover:shadow-amber-500/10"
    >
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500/20 text-xl border border-amber-400/30">
            🧘
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-amber-400 font-sans">
                SERIES ĐỐI CHIẾU KÉP • BẢN QUÁN CHIẾU PHẬT HỌC
              </span>
            </div>
            <h3 class="mt-1 text-sm sm:text-base font-serif font-bold text-amber-100 group-hover:text-amber-300 transition-colors">
              {{ pairedArticle.title }}
            </h3>
            <p v-if="pairedArticle.pali_title" class="text-xs text-amber-300/80 italic font-serif mt-0.5">
              Pāḷi: {{ pairedArticle.pali_title }}
            </p>
          </div>
        </div>

        <a
          :href="targetHref"
          class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-amber-400 px-4 py-2.5 text-xs sm:text-sm font-bold text-stone-950 shadow-md transition-all duration-200 hover:bg-amber-300 hover:scale-105 active:scale-95"
        >
          <span>Đọc Góc Nhìn Thiền Quán</span>
          <span>➔</span>
        </a>
      </div>
    </div>

    <!-- Banner for Theravada Article (Linking to Everyday Life) -->
    <div
      v-else
      class="group relative overflow-hidden rounded-2xl border border-emerald-500/30 bg-gradient-to-r from-slate-900/90 via-slate-950 to-slate-900/90 p-4 sm:p-5 shadow-lg backdrop-blur-md transition-all duration-300 hover:border-emerald-400/60 hover:shadow-emerald-500/10"
    >
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/20 text-xl border border-emerald-400/30">
            🌱
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-400 font-sans">
                SERIES ĐỐI CHIẾU KÉP • BẢN GÓC NHÌN ĐỜI SỐNG THƯỜNG NHẬT
              </span>
            </div>
            <h3 class="mt-1 text-sm sm:text-base font-sans font-bold text-slate-100 group-hover:text-emerald-300 transition-colors">
              {{ pairedArticle.title }}
            </h3>
            <p v-if="pairedArticle.excerpt" class="text-xs text-slate-400 line-clamp-1 mt-0.5 font-sans">
              {{ pairedArticle.excerpt }}
            </p>
          </div>
        </div>

        <a
          :href="targetHref"
          class="shrink-0 inline-flex items-center gap-2 rounded-xl bg-emerald-400 px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-950 shadow-md transition-all duration-200 hover:bg-emerald-300 hover:scale-105 active:scale-95"
        >
          <span>Đọc Góc Nhìn Đời Thường</span>
          <span>➔</span>
        </a>
      </div>
    </div>
  </div>
</template>
