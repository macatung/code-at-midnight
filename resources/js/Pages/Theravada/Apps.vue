<script setup lang="ts">
import { ref } from 'vue';
import TheravadaLayout from '@/Layouts/TheravadaLayout.vue';
import DhammapadaCardGenerator from '@/Components/theravada/DhammapadaCardGenerator.vue';
import VipassanaTimer from '@/Components/theravada/VipassanaTimer.vue';

defineProps<{
  title?: string;
}>();

const activeTab = ref<'card' | 'timer'>('card');
</script>

<template>
  <TheravadaLayout :title="title || 'Ứng Dụng Pháp Bảo & Tọa Thiền — Ma Cà Tưng Dhamma'">
    <div class="max-w-5xl mx-auto py-6 sm:py-10 space-y-8 text-center font-serif">
      <!-- Hub Header -->
      <div>
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-bold mb-3 shadow-sm">
          <span>☸️</span>
          <span>TRUNG TÂM PHÁP BẢO & THỰC HÀNH CHÁNH NIỆM</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-amber-100 tracking-tight">
          Ứng Dụng Tu Học & Lan Tỏa Chánh Pháp
        </h1>
        <p class="text-sm sm:text-base text-stone-400 max-w-2xl mx-auto mt-3 leading-relaxed">
          Công cụ hỗ trợ hành giả tĩnh tâm, thực hành thiền định Vipassanā mỗi ngày và tạo thẻ ảnh trích dẫn lời Phật dạy để gieo duyên lành đến muôn người.
        </p>
      </div>

      <!-- Quick Tab Switcher -->
      <div class="flex items-center justify-center gap-3 p-1.5 bg-stone-900/90 rounded-2xl border border-stone-800 max-w-md mx-auto">
        <button
          @click="activeTab = 'card'"
          :class="[
            'flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-serif font-bold transition-all cursor-pointer flex items-center justify-center gap-2',
            activeTab === 'card'
              ? 'bg-amber-500 text-stone-950 shadow-md'
              : 'text-stone-400 hover:text-white hover:bg-stone-800/60'
          ]"
        >
          <span>📜</span>
          <span>Tạo Thẻ Pháp Cú</span>
        </button>

        <button
          @click="activeTab = 'timer'"
          :class="[
            'flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-serif font-bold transition-all cursor-pointer flex items-center justify-center gap-2',
            activeTab === 'timer'
              ? 'bg-amber-500 text-stone-950 shadow-md'
              : 'text-stone-400 hover:text-white hover:bg-stone-800/60'
          ]"
        >
          <span>🧘</span>
          <span>Đồng Hồ Tọa Thiền</span>
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
