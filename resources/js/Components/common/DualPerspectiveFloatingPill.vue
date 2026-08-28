<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';

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

const isVisible = ref(false);

const handleScroll = () => {
  if (typeof window === 'undefined') return;
  isVisible.value = window.scrollY > 300;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

const targetHref = computed(() => {
  if (!props.pairedArticle) return '#';
  return props.pairedArticle.url;
});
</script>

<template>
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0 translate-y-4 scale-95"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-4 scale-95"
  >
    <div
      v-if="pairedArticle && isVisible"
      class="fixed bottom-6 left-6 z-40 max-w-xs sm:max-w-sm select-none"
    >
      <a
        :href="targetHref"
        class="group flex items-center gap-3 px-4 py-2.5 rounded-full shadow-2xl backdrop-blur-xl transition-all duration-300 hover:scale-105 active:scale-95 border"
        :class="
          currentType === 'dev'
            ? 'bg-stone-950/90 border-amber-500/40 text-amber-200 hover:border-amber-400 hover:shadow-amber-500/20'
            : 'bg-slate-950/90 border-emerald-500/40 text-emerald-300 hover:border-emerald-400 hover:shadow-emerald-500/20'
        "
        :title="`Chuyển sang: ${pairedArticle.title}`"
      >
        <span class="flex h-7 w-7 items-center justify-center rounded-full text-sm" :class="currentType === 'dev' ? 'bg-amber-500/20' : 'bg-emerald-500/20'">
          {{ currentType === 'dev' ? '🧘' : '🌱' }}
        </span>
        <div class="flex flex-col text-left overflow-hidden pr-1">
          <span class="text-[10px] uppercase tracking-wider font-sans opacity-75">
            {{ currentType === 'dev' ? 'Góc nhìn Thiền Quán' : 'Góc nhìn Đời Thường' }}
          </span>
          <span class="text-xs font-semibold truncate max-w-[170px] sm:max-w-[210px] text-white group-hover:text-amber-300 transition-colors">
            {{ pairedArticle.title }}
          </span>
        </div>
        <span class="text-xs font-bold shrink-0 opacity-80 group-hover:translate-x-0.5 transition-transform">➔</span>
      </a>
    </div>
  </transition>
</template>
