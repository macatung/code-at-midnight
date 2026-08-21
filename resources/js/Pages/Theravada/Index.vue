<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import TheravadaLayout from '@/Layouts/TheravadaLayout.vue';
import ZenMascot from '@/Components/mascot/ZenMascot.vue';
import ZenTimeSlider from '@/Components/theravada/ZenTimeSlider.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';
import { useZenTimeCycle } from '@/composables/useZenTimeCycle';
import { useI18n } from '@/composables/useI18n';

const props = defineProps<{
  articles: any[];
  dailyVerse: {
    verse_number: number;
    pali: string;
    vietnamese: string;
    chapter: string;
  };
  categories: any[];
  title?: string;
}>();

const { activeZenPhase } = useZenTimeCycle();
const { locale, t } = useI18n();
const isChanting = ref(false);

const playChantBell = () => {
  isChanting.value = true;
  mindfulBell.ringBell(384, 7.0);
  setTimeout(() => {
    isChanting.value = false;
  }, 4000);
};

const theravadaHomeJsonLd = {
  '@context': 'https://schema.org',
  '@graph': [
    {
      '@type': 'WebSite',
      '@id': 'https://theravada.macatung.dev/#website',
      'url': 'https://theravada.macatung.dev',
      'name': 'Ma Tọa Thiền — Tam Tạng Kinh Điển Theravāda & Pháp Hành Vipassanā',
      'description': 'Bảo tồn và hoằng dương Chánh Pháp nguyên thủy: Tứ Thánh Đế, Bát Chánh Đạo, Kinh Tụng Pāḷi, Thiền Tứ Niệm Xứ Vipassanā.',
      'inLanguage': ['vi', 'pi']
    },
    {
      '@type': 'DefinedTermSet',
      '@id': 'https://theravada.macatung.dev/#tipitaka',
      'name': 'Pāḷi Tipiṭaka — Tam Tạng Thánh Điển Phật Giáo',
      'description': 'Kinh Tạng (Sutta Piṭaka), Luật Tạng (Vinaya Piṭaka), Vi Diệu Pháp Tạng (Abhidhamma Piṭaka)',
      'inLanguage': 'pi'
    }
  ]
};
</script>

<template>
  <TheravadaLayout
    :title="title || `${t('theravada.tagline')} • Tipiṭaka`"
    :description="locale === 'en' ? 'A Theravāda learning system preserving the Tipiṭaka, the Four Noble Truths, the Noble Eightfold Path, Vipassanā and the Pāḷi Glossary.' : 'Hệ thống tu học và bảo tồn kinh điển Phật giáo nguyên thủy Theravāda.'"
    keywords="Ma Tọa Thiền, Theravada, Phật giáo nguyên thủy, Tam Tạng Pāḷi, Kinh Pháp Cú, Dhammapada, Thiền Vipassana, Tứ Niệm Xứ, Bát Chánh Đạo, Chánh Niệm"
    canonical="https://theravada.macatung.dev"
    :json-ld="theravadaHomeJsonLd"
  >
    <!-- 1. Hero Section: Saffron Zen Wisdom & Meditating Mascot -->
    <section class="pt-2 sm:pt-4 pb-8 sm:pb-16 max-w-5xl mx-auto flex flex-col items-center text-center px-2 sm:px-4">
      
      <!-- Top Tag Badge -->
      <div class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1 sm:py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[11px] sm:text-xs font-serif mb-3 sm:mb-4 shadow-sm max-w-[92vw] text-center">
        <span>☸️</span>
        <span class="font-bold tracking-wide sm:tracking-wider truncate">{{ t('theravada.ehipassiko') }}</span>
      </div>

      <!-- Mascot Ma Cà Tưng Tọa Thiền Stage (~160-220px balanced height) -->
      <div class="mb-3 sm:mb-4 w-full flex justify-center">
        <ZenMascot />
      </div>

      <!-- Main Headline / Slogan Trau Chuốt -->
      <h1 class="text-xl sm:text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-amber-100 tracking-tight leading-snug sm:leading-tight mb-3 sm:mb-4 px-2">
        {{ t('theravada.heroTitle') }} <br class="hidden sm:inline" />
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-yellow-200 to-amber-500">
          {{ t('theravada.heroAccent') }}
        </span>
      </h1>

      <!-- Triết Lý Đại Ý "Dù Là Ma Cũng Giác Ngộ Phật Giáo" -->
      <p class="text-xs sm:text-sm md:text-base text-stone-300 font-serif leading-relaxed max-w-2xl mx-auto mb-6 sm:mb-8 text-center px-3 sm:px-4">
        "{{ t('theravada.heroQuote') }}"
      </p>

      <!-- CTA Action Buttons -->
      <div class="flex flex-wrap items-center justify-center gap-2.5 sm:gap-3.5 mb-4 w-full max-w-2xl px-2 sm:px-0">
        <Link
          href="/theravada/danh-muc/phap-hoc"
          class="px-5 sm:px-6 py-3 sm:py-3.5 rounded-2xl bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500 text-stone-950 font-serif font-bold text-xs sm:text-sm hover:brightness-110 transition-all shadow-lg hover:scale-105 active:scale-95 text-center flex items-center justify-center gap-2 min-h-[44px]"
        >
          <span>📖</span>
          <span>{{ t('theravada.study') }} ➔</span>
        </Link>
        <Link
          href="/theravada/danh-muc/phap-hanh"
          class="px-5 sm:px-6 py-3 sm:py-3.5 rounded-2xl bg-stone-900 border border-amber-500/40 text-amber-300 font-serif font-bold text-xs sm:text-sm hover:bg-stone-800 transition-all hover:scale-105 active:scale-95 text-center flex items-center justify-center min-h-[44px]"
        >
          {{ t('theravada.practice') }} 🧘
        </Link>
        <Link
          href="/theravada/danh-muc/kinh-tung"
          class="px-5 sm:px-6 py-3 sm:py-3.5 rounded-2xl bg-stone-900 border border-stone-700 text-stone-300 hover:text-white font-serif text-xs sm:text-sm transition-all hover:bg-stone-800 text-center flex items-center justify-center min-h-[44px]"
        >
          {{ t('theravada.chanting') }} 📜
        </Link>
        <Link
          href="/theravada/danh-muc/lich-su"
          class="px-5 sm:px-6 py-3 sm:py-3.5 rounded-2xl bg-stone-900 border border-amber-500/40 text-amber-300 font-serif font-bold text-xs sm:text-sm hover:bg-stone-800 transition-all hover:scale-105 active:scale-95 text-center flex items-center justify-center min-h-[44px]"
        >
          {{ t('theravada.history') }} 🏛️
        </Link>
        <Link
          href="/theravada/tu-dien-pali"
          class="px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl bg-stone-900/80 border border-stone-700 text-stone-300 hover:text-white font-serif text-xs sm:text-sm transition-all hover:bg-stone-800 text-center flex items-center justify-center min-h-[44px]"
        >
          {{ t('theravada.glossary') }} ☸️
        </Link>
      </div>
    </section>

    <!-- 2. Interactive 24H Zen Monastic Time Traveler (Tua Giờ Thiền Môn) -->
    <section class="my-4 sm:my-6">
      <ZenTimeSlider />
    </section>

    <!-- 3. Daily Dhammapada Verse (Lời Phật Dạy Mỗi Ngày) -->
    <section class="my-8 sm:my-10 p-5 sm:p-8 rounded-3xl bg-gradient-to-br from-amber-950/40 via-stone-900/90 to-stone-950 border border-amber-500/30 shadow-2xl relative overflow-hidden backdrop-blur-md">
      <div class="absolute -right-6 -bottom-6 text-8xl text-amber-500/5 select-none pointer-events-none font-serif">
        ☸️
      </div>

      <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 sm:gap-6">
        <div class="flex-1 space-y-3 text-left">
          <div class="flex flex-wrap items-center gap-2">
            <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 text-xs font-serif font-bold border border-amber-500/30">
              {{ t('theravada.verse') }} {{ dailyVerse.verse_number }}
            </span>
            <span class="text-xs font-serif text-stone-400 italic">
              {{ dailyVerse.chapter }}
            </span>
          </div>

          <!-- Pāḷi Original Verse -->
          <p class="font-serif italic text-amber-200/90 text-sm sm:text-base leading-relaxed border-l-2 border-amber-500/40 pl-3">
            "{{ dailyVerse.pali }}"
          </p>

          <!-- Vietnamese Verse Translation -->
          <p class="font-serif font-semibold text-stone-100 text-base sm:text-lg leading-relaxed">
            "{{ dailyVerse.vietnamese }}"
          </p>
        </div>

        <button
          @click="playChantBell"
          class="w-full md:w-auto shrink-0 flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/40 text-amber-300 font-serif text-xs font-bold transition-all hover:scale-105 active:scale-95 shadow-md cursor-pointer min-h-[44px]"
        >
          <span>🔔</span>
          <span>{{ t('theravada.reflect') }}</span>
        </button>
      </div>
    </section>

    <!-- 4. Category Portals (4 Trụ Cột Tu Tập & Lịch Sử) -->
    <section class="my-14">
      <div class="text-center mb-10">
        <h2 class="text-2xl sm:text-3xl font-serif font-bold text-amber-100 tracking-tight">
          {{ t('theravada.pillars') }}
        </h2>
        <p class="text-sm text-stone-400 font-serif mt-2">
          {{ t('theravada.pillarsDesc') }}
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <Link
          v-for="cat in categories"
          :key="cat.slug"
          :href="`/theravada/danh-muc/${cat.slug}`"
          class="group relative overflow-hidden flex flex-col justify-between p-6 rounded-3xl bg-stone-900/90 border border-stone-800 hover:border-amber-500/60 transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_-10px_rgba(245,158,11,0.3)] text-left backdrop-blur-md"
        >
          <!-- Shimmering Golden Rim Highlight -->
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-amber-400/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 pointer-events-none" />

          <div>
            <div class="flex items-center justify-between mb-4">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/15 border border-amber-500/30 text-2xl text-amber-300 group-hover:scale-110 transition-transform shadow-inner">
                {{ cat.slug === 'phap-hoc' ? '📖' : cat.slug === 'phap-hanh' ? '🧘' : cat.slug === 'kinh-tung' ? '📜' : '🏛️' }}
              </span>
              <span class="text-xs font-serif text-amber-300 font-bold bg-stone-950 px-3 py-1 rounded-full border border-stone-800 shadow-sm">
                {{ cat.count }} {{ t('theravada.articles') }}
              </span>
            </div>

            <h3 class="text-lg font-serif font-bold text-amber-100 group-hover:text-amber-300 transition-colors">
              {{ cat.name }}
            </h3>
            <p class="text-xs font-serif text-amber-400/90 italic font-medium mb-3">
              {{ cat.pali }}
            </p>

            <p class="text-xs text-stone-300 font-serif leading-relaxed line-clamp-3">
              {{ cat.description }}
            </p>
          </div>

          <div class="mt-6 pt-4 border-t border-stone-800 flex items-center justify-between text-xs font-serif font-bold text-amber-400 group-hover:text-amber-300">
            <span>{{ t('theravada.explore') }}</span>
            <span class="group-hover:translate-x-1.5 transition-transform">➔</span>
          </div>
        </Link>
      </div>
    </section>

    <!-- 5. Latest Teachings & Suttas Grid -->
    <section class="my-14">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 text-left">
        <div>
          <span class="text-xs font-serif font-bold text-amber-400 uppercase tracking-wider">
            {{ t('theravada.core') }}
          </span>
          <h2 class="text-2xl sm:text-3xl font-serif font-bold text-amber-100 tracking-tight mt-1">
            {{ t('theravada.recent') }}
          </h2>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="item in articles"
          :key="item.id"
          class="group relative overflow-hidden flex flex-col justify-between rounded-3xl bg-stone-900/90 border border-stone-800 hover:border-amber-500/60 p-6 sm:p-7 transition-all duration-500 hover:-translate-y-2 hover:shadow-[0_20px_40px_-12px_rgba(245,158,11,0.3)] text-left backdrop-blur-md"
        >
          <!-- Shimmering Golden Rim Highlight -->
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-amber-400/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 pointer-events-none" />

          <div>
            <div class="flex items-center justify-between gap-2 text-xs font-serif text-stone-300 mb-3">
              <span class="px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30 font-serif font-bold">
                {{ item.category === 'phap-hoc' ? (locale === 'en' ? 'Dhamma Study' : 'Pháp Học') : item.category === 'phap-hanh' ? (locale === 'en' ? 'Dhamma Practice' : 'Pháp Hành') : item.category === 'kinh-tung' ? t('theravada.chanting') : (locale === 'en' ? 'History' : 'Lịch Sử') }}
              </span>
              <span class="text-stone-300 font-medium">⏱️ {{ item.reading_time_min }} {{ t('theravada.minutes') }}</span>
            </div>

            <h3 class="text-lg font-serif font-bold text-stone-100 group-hover:text-amber-300 transition-colors leading-snug line-clamp-2 mb-2">
              <Link :href="`/theravada/kinh/${item.slug}`">
                {{ item.title }}
              </Link>
            </h3>

            <p v-if="item.pali_title" class="text-xs font-serif text-amber-400/90 italic font-medium mb-3">
              Pāḷi: {{ item.pali_title }}
            </p>

            <p class="text-xs sm:text-sm text-stone-300 font-serif leading-relaxed line-clamp-3 mb-4">
              {{ item.excerpt }}
            </p>
          </div>

          <div class="pt-4 border-t border-stone-800 flex items-center justify-between text-xs font-serif text-stone-400">
            <span class="truncate max-w-[160px] italic text-stone-300">
              {{ item.author || 'Pāḷi Canon' }}
            </span>
            <Link
              :href="`/theravada/kinh/${item.slug}`"
              class="font-bold text-amber-400 hover:text-amber-300 flex items-center gap-1 group-hover:translate-x-1 transition-transform"
            >
              <span>{{ t('theravada.read') }}</span>
              <span>➔</span>
            </Link>
          </div>
        </article>
      </div>
    </section>
  </TheravadaLayout>
</template>
