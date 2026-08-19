<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import TheravadaLayout from '@/Layouts/TheravadaLayout.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';

defineProps<{
  title?: string;
}>();

const searchQuery = ref('');
const selectedLetter = ref<string>('ALL');

const glossaryData = [
  { term: 'Anattā', pali: 'Anattā', vietnamese: 'Vô ngã', letter: 'A', category: 'Tam Tướng', definition: 'Một trong ba đặc tướng của vạn pháp. Không có một linh hồn hay cái Ta độc lập, bất biến làm chủ thể vĩnh viễn.' },
  { term: 'Anicca', pali: 'Anicca', vietnamese: 'Vô thường', letter: 'A', category: 'Tam Tướng', definition: 'Đặc tính biến đổi, sinh diệt không ngừng của mọi pháp hữu vi trong từng sát-na.' },
  { term: 'Arahant', pali: 'Arahant', vietnamese: 'A-la-hán / Ứng Cúng', letter: 'A', category: 'Thánh Quả', definition: 'Bậc Thánh đã đoạn tận hoàn toàn 10 kiết sử, dập tắt mọi lậu hoặc, không còn tái sinh trong luân hồi.' },
  { term: 'Avijjā', pali: 'Avijjā', vietnamese: 'Vô minh', letter: 'A', category: 'Phiền Não', definition: 'Sự không hiểu biết như thật về Bốn Chân Lý Thánh (Tứ Diệu Đế), cội rễ của bánh xe Duyên Khởi.' },
  { term: 'Bhāvanā', pali: 'Bhāvanā', vietnamese: 'Tu tập / Thiền dưỡng', letter: 'B', category: 'Đạo Lộ', definition: 'Sự rèn luyện, làm cho tâm trí phát triển thanh tịnh qua Thiền Định (Samatha) và Thiền Quán (Vipassanā).' },
  { term: 'Bodhi', pali: 'Bodhi', vietnamese: 'Bồ-đề / Giác ngộ', letter: 'B', category: 'Cốt Lõi', definition: 'Sự thấu suốt hoàn toàn thực tướng của vạn pháp và Tứ Thánh Đế.' },
  { term: 'Cetanā', pali: 'Cetanā', vietnamese: 'Tác ý', letter: 'C', category: 'Tâm Lý', definition: 'Ý muốn, động lực tâm lý chỉ đạo hành động. Đức Phật dạy: Tác ý chính là Nghiệp.' },
  { term: 'Citta', pali: 'Citta', vietnamese: 'Tâm / Tâm thức', letter: 'C', category: 'Tâm Lý', definition: 'Khả năng nhận biết đối tượng (cảnh) của giác quan và ý căn.' },
  { term: 'Dhamma', pali: 'Dhamma', vietnamese: 'Pháp / Chân lý', letter: 'D', category: 'Cốt Lõi', definition: 'Lời dạy của Đức Phật, quy luật tự nhiên của vũ trụ, hoặc bất kỳ hiện tượng thực tại nào.' },
  { term: 'Dukkha', pali: 'Dukkha', vietnamese: 'Khổ não / Bất toàn', letter: 'D', category: 'Tam Tướng', definition: 'Bản chất bất toàn, xung đột, không thể thỏa mãn trọn vẹn của đời sống ngũ uẩn.' },
  { term: 'Kamma', pali: 'Kamma', vietnamese: 'Nghiệp', letter: 'K', category: 'Đạo Lộ', definition: 'Hành động thiện hay ác bắt nguồn từ tác ý qua Thân, Khẩu, Ý, tạo ra quả báo tương ứng.' },
  { term: 'Khandha', pali: 'Khandha', vietnamese: 'Uẩn / Ngũ uẩn', letter: 'K', category: 'Tâm Lý', definition: 'Năm tập hợp cấu thành con người: Sắc (Rūpa), Thọ (Vedanā), Tưởng (Saññā), Hành (Saṅkhāra), Thức (Viññāṇa).' },
  { term: 'Mettā', pali: 'Mettā', vietnamese: 'Tâm Từ', letter: 'M', category: 'Tứ Vô Lượng', definition: 'Lòng thương yêu chân thật, mong ước muôn loài chúng sinh được an lạc hạnh phúc.' },
  { term: 'Nibbāna', pali: 'Nibbāna', vietnamese: 'Niết-bàn', letter: 'N', category: 'Cốt Lõi', definition: 'Cảnh giới tịch diệt tối thượng, sự chấm dứt hoàn toàn Tham, Sân, Si và khổ đau luân hồi.' },
  { term: 'Paññā', pali: 'Paññā', vietnamese: 'Trí tuệ', letter: 'P', category: 'Đạo Lộ', definition: 'Tuệ giác thấy rõ bản chất Vô thường, Khổ, Vô ngã của vạn pháp.' },
  { term: 'Sati', pali: 'Sati', vietnamese: 'Chánh niệm', letter: 'S', category: 'Thiền Định', definition: 'Sự ghi nhận tỉnh thức rõ ràng mọi hiện tượng đang diễn ra trong thân và tâm ở giây phút hiện tại.' },
  { term: 'Sīla', pali: 'Sīla', vietnamese: 'Giới hạnh', letter: 'S', category: 'Đạo Lộ', definition: 'Các quy tắc đạo đức thanh tịnh bảo vệ người tu khỏi các hành vi tổn hại mình và người.' },
  { term: 'Taṇhā', pali: 'Taṇhā', vietnamese: 'Ái dục', letter: 'T', category: 'Phiền Não', definition: 'Lòng khao khát, thèm muốn, bám víu mãnh liệt vào dục lạc, hữu vi và phi hữu.' },
  { term: 'Vipassanā', pali: 'Vipassanā', vietnamese: 'Minh sát tuệ', letter: 'V', category: 'Thiền Định', definition: 'Tuệ giác quán chiếu trực tiếp sự sinh diệt của thân tâm để đạt giải thoát tối hậu.' },
];

const alphabet = ['ALL', 'A', 'B', 'C', 'D', 'K', 'M', 'N', 'P', 'S', 'T', 'V'];

const filteredList = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  return glossaryData.filter(item => {
    const matchesLetter = selectedLetter.value === 'ALL' || item.letter === selectedLetter.value;
    const matchesQuery = !q || item.term.toLowerCase().includes(q) || item.vietnamese.toLowerCase().includes(q) || item.definition.toLowerCase().includes(q);
    return matchesLetter && matchesQuery;
  });
});

const strikeBell = () => {
  mindfulBell.strikeWoodenFish();
};
</script>

<template>
  <TheravadaLayout :title="title">
    <div class="max-w-5xl mx-auto py-6 sm:py-10">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs font-serif text-stone-400 mb-6">
        <Link href="/theravada" class="hover:text-amber-300">Theravāda</Link>
        <span>/</span>
        <span class="text-amber-400 font-bold">Từ Điển Pāḷi</span>
      </nav>

      <!-- Header -->
      <header class="mb-10 text-left border-b border-stone-800 pb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs font-serif mb-3">
          <span>📖</span>
          <span>Bách Khoa Thuật Ngữ Pāḷi</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-amber-100">
          Từ Điển Thuật Ngữ Phật Học Pāḷi
        </h1>
        <p class="text-sm text-stone-400 font-serif mt-2 max-w-3xl leading-relaxed">
          Tra cứu ngữ nghĩa chuẩn xác của các thuật ngữ cốt lõi trong Tam Tạng Pāḷi Tipiṭaka và truyền thống thiền định Theravāda.
        </p>

        <!-- Search Bar -->
        <div class="mt-6 relative max-w-xl">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm kiếm thuật ngữ (ví dụ: Anicca, Sati, Nibbāna, Chánh niệm...)"
            class="w-full pl-10 pr-4 py-3 rounded-2xl bg-stone-900 border border-stone-700 text-sm text-stone-100 placeholder-stone-500 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/30"
          />
          <span class="absolute left-3.5 top-3.5 text-stone-500 text-sm">🔍</span>
        </div>

        <!-- A-Z Alphabet Strip -->
        <div class="flex flex-wrap gap-1.5 mt-4">
          <button
            v-for="letter in alphabet"
            :key="letter"
            @click="selectedLetter = letter"
            :class="[
              'px-3 py-1 rounded-xl text-xs font-mono font-bold transition-all',
              selectedLetter === letter
                ? 'bg-amber-500 text-stone-950 shadow-md scale-105'
                : 'bg-stone-900 text-stone-400 hover:text-white hover:bg-stone-800 border border-stone-800'
            ]"
          >
            {{ letter }}
          </button>
        </div>
      </header>

      <!-- Terms Grid -->
      <div class="space-y-4">
        <div
          v-for="item in filteredList"
          :key="item.term"
          class="p-6 rounded-3xl bg-stone-900/60 border border-stone-800 hover:border-amber-500/30 transition-all group cursor-pointer"
          @click="strikeBell"
        >
          <div class="flex flex-wrap items-baseline justify-between gap-2 mb-2">
            <div class="flex items-center gap-3">
              <h3 class="text-xl font-serif font-bold text-amber-300 group-hover:text-amber-200">
                {{ item.term }}
              </h3>
              <span class="text-base font-serif italic text-stone-300">
                ({{ item.vietnamese }})
              </span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-sans font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
              {{ item.category }}
            </span>
          </div>

          <p class="text-sm text-stone-300 font-sans leading-relaxed">
            {{ item.definition }}
          </p>
        </div>

        <div v-if="filteredList.length === 0" class="py-16 text-center text-stone-500 font-serif">
          Không tìm thấy thuật ngữ Pāḷi phù hợp với "{{ searchQuery }}".
        </div>
      </div>
    </div>
  </TheravadaLayout>
</template>
