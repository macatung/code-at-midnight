<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import TheravadaLayout from '@/Layouts/TheravadaLayout.vue';
import ZenMascot from '@/Components/mascot/ZenMascot.vue';
import ZenTimeSlider from '@/Components/theravada/ZenTimeSlider.vue';
import DhammapadaCardGenerator from '@/Components/theravada/DhammapadaCardGenerator.vue';
import VipassanaTimer from '@/Components/theravada/VipassanaTimer.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';
import { useZenTimeCycle } from '@/composables/useZenTimeCycle';

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
const isChanting = ref(false);
const activeAppTab = ref<'card' | 'timer'>('card');

const playChantBell = () => {
  isChanting.value = true;
  mindfulBell.ringBell(384, 7.0);
  setTimeout(() => {
    isChanting.value = false;
  }, 4000);
};
</script>

<template>
  <TheravadaLayout :title="title">
    <!-- 1. Hero Section: Saffron Zen Wisdom & Meditating Mascot -->
    <section class="pt-2 sm:pt-4 pb-10 sm:pb-16 max-w-5xl mx-auto flex flex-col items-center text-center">
      
      <!-- Top Tag Badge -->
      <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-serif mb-4 shadow-sm">
        <span>☸️</span>
        <span class="font-bold tracking-wider">EHIPASSIKO — HÃY ĐẾN ĐỂ THẤY & THỂ NGHIỆM</span>
      </div>

      <!-- Mascot Ma Cà Tưng Tọa Thiền Stage (~200px balanced height) -->
      <div class="mb-4">
        <ZenMascot />
      </div>

      <!-- Main Headline / Slogan Trau Chuốt -->
      <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-amber-100 tracking-tight leading-snug sm:leading-tight mb-4">
        Vượt Khỏi Đêm Đen Vô Minh <br />
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-yellow-200 to-amber-500">
          Hướng Về Ánh Sáng Chánh Tri Kiến
        </span>
      </h1>

      <!-- Triết Lý Đại Ý "Dù Là Ma Cũng Giác Ngộ Phật Giáo" -->
      <p class="text-xs sm:text-sm md:text-base text-stone-300 font-serif leading-relaxed max-w-2xl mx-auto mb-8 text-center px-4">
        "Dù là Ma Cà Tưng lang thang trong bóng đêm vô minh, khi có duyên lành hạnh ngộ Chánh Pháp cũng buông bỏ vọng niệm để tọa thiền, nương tựa Tam Bảo tìm về sự an tịnh và giải thoát tối hậu."
      </p>

      <!-- 3 CTA Action Buttons -->
      <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 mb-4">
        <Link
          href="/theravada/danh-muc/phap-hoc"
          class="px-5 sm:px-6 py-3 sm:py-3.5 rounded-2xl bg-amber-500 text-stone-950 font-serif font-bold text-xs sm:text-sm hover:bg-amber-400 transition-all shadow-lg hover:scale-105 active:scale-95"
        >
          Khảo Cứu Pháp Học (Pariyatti) ➔
        </Link>
        <Link
          href="/theravada/danh-muc/phap-hanh"
          class="px-5 sm:px-6 py-3 sm:py-3.5 rounded-2xl bg-stone-900 border border-amber-500/40 text-amber-300 font-serif font-bold text-xs sm:text-sm hover:bg-stone-800 transition-all hover:scale-105 active:scale-95"
        >
          Thực Hành Thiền Vipassanā 🧘
        </Link>
        <Link
          href="/theravada/ung-dung-tu-hoc"
          class="px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl bg-amber-500/20 border border-amber-400/50 text-amber-200 hover:text-white font-serif text-xs sm:text-sm transition-all hover:bg-amber-500/30"
        >
          ✨ Ứng Dụng Pháp Bảo
        </Link>
      </div>
    </section>

    <!-- 2. Interactive 24H Zen Monastic Time Traveler (Tua Giờ Thiền Môn) -->
    <section class="my-6">
      <ZenTimeSlider />
    </section>

    <!-- 3. Interactive Buddhist Applications Hub (Ứng Dụng Tu Học Trực Tiếp) -->
    <section class="my-14 space-y-6">
      <div class="text-center max-w-2xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-bold mb-2">
          <span>✨</span>
          <span>CÔNG CỤ TU HỌC & LAN TỎA CHÁNH PHÁP</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-serif font-bold text-amber-100 tracking-tight">
          Ứng Dụng Pháp Bảo Tương Tác
        </h2>
        <p class="text-xs sm:text-sm text-stone-400 font-serif mt-1">
          Trợ niệm với lời dạy Kinh Pháp Cú và thực hành thiền định Vipassanā theo nhịp thở chánh niệm
        </p>

        <!-- App Switcher Tabs -->
        <div class="mt-4 flex items-center justify-center gap-2 p-1 bg-stone-900/90 rounded-2xl border border-stone-800 max-w-sm mx-auto">
          <button
            @click="activeAppTab = 'card'"
            :class="[
              'flex-1 py-2 px-3 rounded-xl text-xs font-serif font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5',
              activeAppTab === 'card'
                ? 'bg-amber-500 text-stone-950 shadow-md'
                : 'text-stone-400 hover:text-white'
            ]"
          >
            <span>📜</span>
            <span>Tạo Thẻ Pháp Cú HD</span>
          </button>
          <button
            @click="activeAppTab = 'timer'"
            :class="[
              'flex-1 py-2 px-3 rounded-xl text-xs font-serif font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5',
              activeAppTab === 'timer'
                ? 'bg-amber-500 text-stone-950 shadow-md'
                : 'text-stone-400 hover:text-white'
            ]"
          >
            <span>🧘</span>
            <span>Đồng Hồ Tọa Thiền</span>
          </button>
        </div>
      </div>

      <!-- App Canvas / Widget -->
      <div>
        <DhammapadaCardGenerator v-if="activeAppTab === 'card'" />
        <VipassanaTimer v-else />
      </div>
    </section>

    <!-- 4. Daily Dhammapada Verse (Lời Phật Dạy Mỗi Ngày) -->
    <section class="my-10 p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-amber-950/40 via-stone-900/90 to-stone-950 border border-amber-500/30 shadow-2xl relative overflow-hidden backdrop-blur-md">
      <div class="absolute -right-6 -bottom-6 text-8xl text-amber-500/5 select-none pointer-events-none font-serif">
        ☸️
      </div>

      <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex-1 space-y-3 text-left">
          <div class="flex flex-wrap items-center gap-2">
            <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 text-xs font-serif font-bold border border-amber-500/30">
              Kinh Pháp Cú (Dhammapada) — Kệ số {{ dailyVerse.verse_number }}
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
          class="shrink-0 flex items-center gap-2 px-4 py-3 rounded-2xl bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/40 text-amber-300 font-serif text-xs font-bold transition-all hover:scale-105 active:scale-95 shadow-md cursor-pointer"
        >
          <span>🔔</span>
          <span>Lắng Lòng Chiêm Nghiệm</span>
        </button>
      </div>
    </section>

    <!-- 5. Category Portals (3 Trụ Cột Tu Tập) -->
    <section class="my-14">
      <div class="text-center mb-10">
        <h2 class="text-2xl sm:text-3xl font-serif font-bold text-amber-100 tracking-tight">
          Tam Đại Trụ Cột Tu Tập Theravāda
        </h2>
        <p class="text-sm text-stone-400 font-serif mt-2">
          Học hiểu tường tận giáo pháp và thể nghiệm chân lý trong từng khoảnh khắc hiện tại
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Link
          v-for="cat in categories"
          :key="cat.slug"
          :href="`/theravada/danh-muc/${cat.slug}`"
          class="group flex flex-col justify-between p-6 rounded-3xl bg-stone-900/80 border border-stone-800 hover:border-amber-500/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl text-left"
        >
          <div>
            <div class="flex items-center justify-between mb-4">
              <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 border border-amber-500/20 text-2xl text-amber-400 group-hover:scale-110 transition-transform">
                {{ cat.slug === 'phap-hoc' ? '📖' : cat.slug === 'phap-hanh' ? '🧘' : '📜' }}
              </span>
              <span class="text-xs font-serif text-amber-400/90 font-bold bg-stone-950 px-2.5 py-1 rounded-full border border-stone-800">
                {{ cat.count }} Bài viết
              </span>
            </div>

            <h3 class="text-lg font-serif font-bold text-amber-200 group-hover:text-amber-100 transition-colors">
              {{ cat.name }}
            </h3>
            <p class="text-xs font-serif text-amber-500/80 italic mb-3">
              {{ cat.pali }}
            </p>

            <p class="text-xs text-stone-400 font-serif leading-relaxed">
              {{ cat.description }}
            </p>
          </div>

          <div class="mt-6 pt-4 border-t border-stone-800/80 flex items-center justify-between text-xs font-serif font-bold text-amber-400">
            <span>Khám phá chuyên mục</span>
            <span class="group-hover:translate-x-1 transition-transform">➔</span>
          </div>
        </Link>
      </div>
    </section>

    <!-- 6. Latest Teachings & Suttas Grid -->
    <section class="my-14">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 text-left">
        <div>
          <span class="text-xs font-serif font-bold text-amber-400 uppercase tracking-wider">
            Thánh Điển & Giáo Lý Cốt Lõi
          </span>
          <h2 class="text-2xl sm:text-3xl font-serif font-bold text-amber-100 tracking-tight mt-1">
            Kinh Văn & Bài Học Gần Đây
          </h2>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article
          v-for="item in articles"
          :key="item.id"
          class="flex flex-col justify-between rounded-3xl bg-stone-900/60 border border-stone-800 hover:border-amber-500/40 p-6 transition-all duration-300 hover:-translate-y-1 hover:bg-stone-900 text-left"
        >
          <div>
            <div class="flex items-center justify-between gap-2 text-xs font-serif text-stone-400 mb-3">
              <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 font-serif font-medium">
                {{ item.category === 'phap-hoc' ? 'Pháp Học' : item.category === 'phap-hanh' ? 'Pháp Hành' : 'Kinh Tụng' }}
              </span>
              <span>⏱️ {{ item.reading_time_min }} phút đọc</span>
            </div>

            <h3 class="text-base sm:text-lg font-serif font-bold text-stone-100 hover:text-amber-300 transition-colors leading-snug line-clamp-2 mb-2">
              <Link :href="`/theravada/kinh/${item.slug}`">
                {{ item.title }}
              </Link>
            </h3>

            <p v-if="item.pali_title" class="text-xs font-serif text-amber-500/80 italic mb-3">
              Pāḷi: {{ item.pali_title }}
            </p>

            <p class="text-xs text-stone-400 font-serif leading-relaxed line-clamp-3 mb-4">
              {{ item.excerpt }}
            </p>
          </div>

          <div class="pt-4 border-t border-stone-800/80 flex items-center justify-between text-xs font-serif text-stone-400">
            <span class="truncate max-w-[160px] italic">
              {{ item.author || 'Pāḷi Canon' }}
            </span>
            <Link
              :href="`/theravada/kinh/${item.slug}`"
              class="font-bold text-amber-400 hover:text-amber-300 flex items-center gap-1"
            >
              <span>Đọc bài</span>
              <span>➔</span>
            </Link>
          </div>
        </article>
      </div>
    </section>
  </TheravadaLayout>
</template>
