<script setup lang="ts">
import { ref } from 'vue';
import TheravadaLayout from '@/Layouts/TheravadaLayout.vue';
import DhammapadaCardGenerator from '@/Components/theravada/DhammapadaCardGenerator.vue';
import VipassanaTimer from '@/Components/theravada/VipassanaTimer.vue';

defineProps<{
  title?: string;
}>();

const activeTab = ref<'card' | 'timer'>('card');

const appsJsonLd = {
  '@context': 'https://schema.org',
  '@graph': [
    {
      '@type': 'WebApplication',
      'name': 'Trợ Niệm Pháp Cú — Xuất Thẻ Ảnh Chia Sẻ HD',
      'description': 'Ứng dụng rút quẻ kệ Kinh Pháp Cú (Dhammapada), tùy biến 4 phong cách thiền môn và xuất ảnh chất lượng cao 9:16 & 1:1 chia sẻ mạng xã hội.',
      'applicationCategory': 'LifestyleApplication',
      'operatingSystem': 'Any',
      'url': 'https://theravada.macatung.dev/ung-dung-tu-hoc'
    },
    {
      '@type': 'WebApplication',
      'name': 'Đồng Hồ Tọa Thiền Minh Sát Vipassanā',
      'description': 'Ứng dụng bấm giờ tọa thiền chánh niệm với chuông xoay Tây Tạng 432Hz và vòng thở Ānāpānasati quán sổ tức.',
      'applicationCategory': 'HealthAndFitnessApplication',
      'operatingSystem': 'Any',
      'url': 'https://theravada.macatung.dev/ung-dung-tu-hoc'
    }
  ]
};
</script>

<template>
  <TheravadaLayout
    :title="title || 'Ứng Dụng Pháp Bảo — Tạo Thẻ Pháp Cú & Tọa Thiền Vipassanā'"
    description="Công cụ hỗ trợ tu học Phật giáo Theravāda: Tạo thẻ ảnh Kinh Pháp Cú HD chia sẻ đa nền tảng và Đồng hồ tọa thiền Vipassanā tích hợp chuông xoay 432Hz."
    keywords="Tạo thẻ Pháp Cú, Dhammapada Card Generator, Đồng hồ tọa thiền, Vipassana Timer, Chuông xoay Tây Tạng, Phật giáo Theravada"
    canonical="https://theravada.macatung.dev/ung-dung-tu-hoc"
    :json-ld="appsJsonLd"
  >
    <div class="max-w-5xl mx-auto py-4 sm:py-10 space-y-6 sm:space-y-8 text-center font-serif px-2 sm:px-4">
      <!-- Hub Header -->
      <div>
        <div class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1 sm:py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[11px] sm:text-xs font-bold mb-2 sm:mb-3 shadow-sm max-w-[92vw]">
          <span>☸️</span>
          <span class="truncate">TRUNG TÂM PHÁP BẢO & THỰC HÀNH CHÁNH NIỆM</span>
        </div>
        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-bold text-amber-100 tracking-tight">
          Ứng Dụng Tu Học & Lan Tỏa Chánh Pháp
        </h1>
        <p class="text-xs sm:text-base text-stone-400 max-w-2xl mx-auto mt-2 sm:mt-3 leading-relaxed px-2">
          Công cụ hỗ trợ hành giả tĩnh tâm, thực hành thiền định Vipassanā mỗi ngày và tạo thẻ ảnh trích dẫn lời Phật dạy để gieo duyên lành đến muôn người.
        </p>
      </div>

      <!-- Quick Tab Switcher -->
      <div class="flex items-center justify-center gap-2 sm:gap-3 p-1 sm:p-1.5 bg-stone-900/90 rounded-2xl border border-stone-800 max-w-md mx-auto w-full">
        <button
          @click="activeTab = 'card'"
          :class="[
            'flex-1 py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl text-xs sm:text-sm font-serif font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 sm:gap-2 min-h-[42px]',
            activeTab === 'card'
              ? 'bg-amber-500 text-stone-950 shadow-md'
              : 'text-stone-400 hover:text-white hover:bg-stone-800/60'
          ]"
        >
          <span>📜</span>
          <span class="truncate">Tạo Thẻ Pháp Cú</span>
        </button>

        <button
          @click="activeTab = 'timer'"
          :class="[
            'flex-1 py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl text-xs sm:text-sm font-serif font-bold transition-all cursor-pointer flex items-center justify-center gap-1.5 sm:gap-2 min-h-[42px]',
            activeTab === 'timer'
              ? 'bg-amber-500 text-stone-950 shadow-md'
              : 'text-stone-400 hover:text-white hover:bg-stone-800/60'
          ]"
        >
          <span>🧘</span>
          <span class="truncate">Đồng Hồ Tọa Thiền</span>
        </button>
      </div>

      <!-- Active Application Stage -->
      <div class="transition-all duration-300">
        <DhammapadaCardGenerator v-if="activeTab === 'card'" />
        <VipassanaTimer v-else />
      </div>
    </div>
  </TheravadaLayout>
</template>
