<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import MacatungMascot from '@/Components/mascot/MacatungMascot.vue';
import NextStepsHub from '@/Components/layout/NextStepsHub.vue';
import Icons from '@/Components/ui/Icons.vue';
import confetti from 'canvas-confetti';
import { sound } from '@/audio/soundEffects';
import { oracleAudio } from '@/audio/oracleAudio';
import {
  ORACLE_CATEGORIES,
  OracleCategory,
  OracleRecord,
  getRandomFortune,
} from '@/data/oracleData';

const selectedCategory = ref<OracleCategory>('career');
const isShaking = ref(false);
const shakeProgress = ref(0);
const isTossingCoin = ref(false);
const currentFortune = ref<OracleRecord | null>(null);
const divinationState = ref<'intent' | 'shaking' | 'coin_toss' | 'revealed'>('intent');
const coinResult = ref<{ coin1: 'yang' | 'yin'; coin2: 'yang' | 'yin' } | null>(null);
const canvasExportRef = ref<HTMLCanvasElement | null>(null);

let shakeIntervalId: any = null;

const activeCategoryInfo = computed(() => {
  return ORACLE_CATEGORIES.find(c => c.id === selectedCategory.value) || ORACLE_CATEGORIES[0];
});

// Start Shaking Bamboo Tube
const startShaking = () => {
  divinationState.value = 'shaking';
  isShaking.value = true;
  shakeProgress.value = 0;

  sound.playClick();
  oracleAudio.playBambooRattle();

  shakeIntervalId = setInterval(() => {
    shakeProgress.value += 5;
    oracleAudio.playBambooRattle();

    if (shakeProgress.value >= 100) {
      clearInterval(shakeIntervalId);
      isShaking.value = false;
      // Advance to coin toss
      triggerCoinToss();
    }
  }, 120);
};

// Yin-Yang Coin Toss Confirmation
const triggerCoinToss = () => {
  divinationState.value = 'coin_toss';
  isTossingCoin.value = true;
  oracleAudio.playCoinToss();

  setTimeout(() => {
    // 1 Yang, 1 Yin (Sacred Concordance)
    coinResult.value = { coin1: 'yang', coin2: 'yin' };
    isTossingCoin.value = false;

    // Pick fortune
    currentFortune.value = getRandomFortune(selectedCategory.value);
    divinationState.value = 'revealed';

    oracleAudio.playRevelationGong();
    sound.playTalisman();

    // Trigger magic confetti
    try {
      confetti({
        particleCount: 50,
        spread: 70,
        origin: { y: 0.6 },
        colors: ['#00f5a0', '#ffd166', '#ff4d6d', '#00d2ff'],
      });
    } catch {}
  }, 1200);
};

// Reset to Ask Again
const resetOracle = () => {
  divinationState.value = 'intent';
  currentFortune.value = null;
  coinResult.value = null;
  shakeProgress.value = 0;
  sound.playClick();
};

// Download HD Fortune Talisman Image
const downloadTalismanCard = () => {
  if (!currentFortune.value) return;
  const fortune = currentFortune.value;

  const canvas = document.createElement('canvas');
  canvas.width = 800;
  canvas.height = 1200;
  const ctx = canvas.getContext('2d');
  if (!ctx) return;

  // Background Gradient (Crimson Gold / Midnight Goth)
  const bgGrad = ctx.createLinearGradient(0, 0, 0, 1200);
  bgGrad.addColorStop(0, '#1a050b');
  bgGrad.addColorStop(0.5, '#2a0a14');
  bgGrad.addColorStop(1, '#0b0205');
  ctx.fillStyle = bgGrad;
  ctx.fillRect(0, 0, 800, 1200);

  // Border Gold Frame
  ctx.strokeStyle = '#ffd166';
  ctx.lineWidth = 12;
  ctx.strokeRect(30, 30, 740, 1140);

  ctx.strokeStyle = '#ff4d6d';
  ctx.lineWidth = 3;
  ctx.strokeRect(45, 45, 710, 1110);

  // Top Talisman Seal Header
  ctx.fillStyle = '#ff4d6d';
  ctx.fillRect(300, 30, 200, 60);
  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 24px "Space Grotesk", sans-serif';
  ctx.textAlign = 'center';
  ctx.fillText('敕令 · LINH PHÙ', 400, 70);

  // Title
  ctx.fillStyle = '#ffd166';
  ctx.font = 'bold 36px "Space Grotesk", sans-serif';
  ctx.fillText(fortune.title, 400, 160);

  // Level Badge
  ctx.fillStyle = '#00f5a0';
  ctx.font = 'bold 24px "JetBrains Mono", monospace';
  ctx.fillText(`【 ${fortune.level} · Ngũ Hành: ${fortune.element} 】`, 400, 210);

  // Divider
  ctx.strokeStyle = '#ffd16640';
  ctx.beginPath();
  ctx.moveTo(100, 250);
  ctx.lineTo(700, 250);
  ctx.stroke();

  // Poem Box
  ctx.fillStyle = '#fffdf0';
  ctx.font = 'italic 28px "Space Grotesk", serif';
  ctx.fillText(`"${fortune.poem.line1}"`, 400, 320);
  ctx.fillText(`"${fortune.poem.line2}"`, 400, 370);
  ctx.fillText(`"${fortune.poem.line3}"`, 400, 420);
  ctx.fillText(`"${fortune.poem.line4}"`, 400, 470);

  // Divider
  ctx.beginPath();
  ctx.moveTo(100, 520);
  ctx.lineTo(700, 520);
  ctx.stroke();

  // Selected Meaning
  ctx.fillStyle = '#ffd166';
  ctx.font = 'bold 22px "Space Grotesk", sans-serif';
  ctx.fillText(`[ LỜI BÌNH ${activeCategoryInfo.value.label.toUpperCase()} ]`, 400, 570);

  ctx.fillStyle = '#e2e8f0';
  ctx.font = '20px "Space Grotesk", sans-serif';
  const meaningText = fortune.meanings[selectedCategory.value];
  wrapText(ctx, meaningText, 400, 620, 620, 32);

  // Mascot Advice Box
  ctx.fillStyle = '#ffffff10';
  ctx.fillRect(80, 760, 640, 140);
  ctx.strokeStyle = '#00f5a060';
  ctx.strokeRect(80, 760, 640, 140);

  ctx.fillStyle = '#00f5a0';
  ctx.font = 'bold 20px "JetBrains Mono", monospace';
  ctx.fillText('🧙‍♂️ LỜI KHUYÊN MA CÀ TƯNG:', 400, 800);

  ctx.fillStyle = '#ffffff';
  ctx.font = '18px "Space Grotesk", sans-serif';
  wrapText(ctx, fortune.mascotAdvice, 400, 840, 600, 28);

  // Lucky Number & Color Pill
  ctx.fillStyle = '#ffd166';
  ctx.font = 'bold 20px "JetBrains Mono", monospace';
  ctx.fillText(`Số May Mắn: ${fortune.luckyNumber}  ·  Điểm Phúc Khí: ${fortune.score}/100 🌟`, 400, 960);

  // Footer Brand
  ctx.fillStyle = '#64748b';
  ctx.font = '16px "JetBrains Mono", monospace';
  ctx.fillText('macatung.dev · Điện Thờ Gieo Quẻ Âm Dương 00:00 AM', 400, 1100);

  // Download Trigger
  const link = document.createElement('a');
  link.download = `macatung_que_xam_${fortune.id}.png`;
  link.href = canvas.toDataURL('image/png');
  link.click();
  sound.playClick();
};

function wrapText(ctx: CanvasRenderingContext2D, text: string, x: number, y: number, maxWidth: number, lineHeight: number) {
  const words = text.split(' ');
  let line = '';
  let curY = y;

  for (let n = 0; n < words.length; n++) {
    const testLine = line + words[n] + ' ';
    const metrics = ctx.measureText(testLine);
    if (metrics.width > maxWidth && n > 0) {
      ctx.fillText(line, x, curY);
      line = words[n] + ' ';
      curY += lineHeight;
    } else {
      line = testLine;
    }
  }
  ctx.fillText(line, x, curY);
}
</script>

<template>
  <Head title="Điện Thờ Gieo Quẻ Âm Dương — Ma Cà Tưng Oracle" />

  <div class="min-h-screen bg-midnight-950 text-slate-100 selection:bg-phantom-mint selection:text-midnight-950 flex flex-col justify-between relative overflow-x-hidden w-full bg-grid-pattern">
    <TalismanCanvas />
    <Navbar />

    <main class="relative z-10 flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-left">
      <!-- Breadcrumbs -->
      <nav class="flex items-center gap-2 text-xs font-mono text-slate-400 mb-8" aria-label="Breadcrumb">
        <Link href="/" class="hover:text-phantom-mint transition-colors">Trang Chủ</Link>
        <span>/</span>
        <span class="text-talisman-gold font-bold">Điện Thờ Gieo Quẻ Âm Dương</span>
      </nav>

      <!-- Section Header -->
      <div class="flex flex-col items-start mb-10">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-talisman-gold/10 border border-talisman-gold/30 text-talisman-gold text-xs font-mono mb-3 shadow-glow-talisman">
          🔮 Ma Cà Tưng Divination Oracle
        </span>
        <h1 class="text-3xl sm:text-5xl font-display font-extrabold text-white tracking-tight leading-tight">
          Điện Thờ <span class="text-transparent bg-clip-text bg-gradient-to-r from-talisman-gold via-phantom-mint to-phantom-cyan">Gieo Quẻ Âm Dương</span>
        </h1>
        <p class="text-sm sm:text-base text-slate-300 mt-2 max-w-2xl font-sans leading-relaxed">
          Thành tâm chọn một tâm nguyện, lắc ống thẻ xăm tre và gieo quẻ đồng xu để chú Ma Cà Tưng giải mã vận hạn, cơ hội và lời khuyên cát tường hôm nay.
        </p>
      </div>

      <!-- Main Interactive Shrine Cabinet -->
      <div class="glass-panel rounded-3xl border border-white/15 p-6 sm:p-10 bg-midnight-950/80 shadow-2xl relative overflow-hidden">
        
        <!-- STEP 1: Intent Selection -->
        <div v-if="divinationState === 'intent'" class="flex flex-col items-center text-center py-6">
          <h3 class="text-xl sm:text-2xl font-display font-bold text-white mb-2">
            1. Chọn Tâm Nguyện Bạn Muốn Xin Quẻ
          </h3>
          <p class="text-xs sm:text-sm text-slate-400 mb-8 font-sans">
            Mỗi quẻ xăm linh ứng nhất khi bạn giữ tâm thế an nhiên và tập trung vào một câu hỏi duy nhất.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 w-full max-w-2xl mb-10 text-left">
            <button
              v-for="cat in ORACLE_CATEGORIES"
              :key="cat.id"
              type="button"
              class="p-5 rounded-2xl border transition-all flex items-start gap-4 cursor-pointer select-none"
              :class="selectedCategory === cat.id
                ? 'bg-midnight-900 border-talisman-gold shadow-glow-talisman ring-2 ring-talisman-gold/30 scale-102'
                : 'bg-midnight-900/60 border-white/10 hover:border-white/20 hover:bg-midnight-900/90'"
              @click="selectedCategory = cat.id; sound.playClick()"
            >
              <span class="text-3xl p-2 rounded-xl bg-white/5 border border-white/10">{{ cat.icon }}</span>
              <div>
                <h4 class="font-display font-bold text-base text-white" :class="{ 'text-talisman-gold': selectedCategory === cat.id }">
                  {{ cat.label }}
                </h4>
                <p class="text-xs text-slate-400 mt-1 font-sans">{{ cat.desc }}</p>
              </div>
            </button>
          </div>

          <!-- Proceed Button -->
          <button
            type="button"
            class="px-8 py-4 rounded-2xl bg-gradient-to-r from-talisman-gold via-amber-400 to-talisman-gold text-midnight-950 font-display font-bold text-base hover:brightness-110 shadow-glow-talisman transition-all flex items-center gap-3 cursor-pointer active:scale-95 hover:scale-105"
            @click="startShaking"
          >
            <span>Lắc Ống Thẻ Xăm Tre</span>
            <span>🎋</span>
          </button>
        </div>

        <!-- STEP 2: Bamboo Tube Shaking Animation -->
        <div v-if="divinationState === 'shaking'" class="flex flex-col items-center text-center py-10">
          <div class="relative mb-6">
            <!-- Animated Shaking Bamboo Cylinder -->
            <div
              class="w-28 h-44 rounded-3xl bg-gradient-to-b from-amber-800 via-amber-900 to-amber-950 border-4 border-talisman-gold flex flex-col items-center justify-between p-3 shadow-2xl transition-transform duration-100"
              :class="{ 'animate-wiggle': isShaking }"
            >
              <!-- Emerging Fortune Stick -->
              <div
                class="w-6 bg-amber-200 border border-amber-400 rounded-t-lg transition-all duration-300 shadow-md"
                :style="{ height: `${Math.min(shakeProgress * 0.8, 60)}px` }"
              />
              <div class="font-mono text-[10px] text-amber-300 font-bold tracking-widest uppercase">
                Âm Dương Ống
              </div>
            </div>
            <!-- Aura Glow -->
            <div class="w-36 h-6 bg-talisman-gold/20 rounded-full blur-lg -mt-3 mx-auto" />
          </div>

          <h3 class="text-2xl font-display font-bold text-white mb-2">
            Đang Lắc Thẻ Xăm Tre...
          </h3>
          <p class="text-xs font-mono text-phantom-mint mb-6">
            Lắng nghe tiếng xào xạc của quẻ mệnh ({{ shakeProgress }}%)
          </p>

          <!-- Progress Bar -->
          <div class="w-64 h-2 bg-midnight-900 rounded-full border border-white/10 overflow-hidden">
            <div
              class="h-full bg-gradient-to-r from-talisman-gold to-phantom-mint transition-all duration-100"
              :style="{ width: `${shakeProgress}%` }"
            />
          </div>
        </div>

        <!-- STEP 3: Yin-Yang Coin Toss Confirmation -->
        <div v-if="divinationState === 'coin_toss'" class="flex flex-col items-center text-center py-12">
          <div class="flex items-center gap-6 mb-6">
            <!-- Coin 1 -->
            <div class="w-16 h-16 rounded-full bg-amber-500 border-4 border-amber-300 animate-spin flex items-center justify-center text-2xl shadow-glow-talisman">
              ☯️
            </div>
            <!-- Coin 2 -->
            <div class="w-16 h-16 rounded-full bg-amber-600 border-4 border-amber-400 animate-spin flex items-center justify-center text-2xl shadow-glow-talisman">
              ☯️
            </div>
          </div>
          <h3 class="text-2xl font-display font-bold text-white mb-2">
            Đang Gieo Quẻ Đồng Xu Âm Dương...
          </h3>
          <p class="text-xs font-mono text-slate-400">
            Xin quẻ thánh: Nhất Âm Nhất Dương là vạn sự tương thông
          </p>
        </div>

        <!-- STEP 4: Revealed Prophecy Card -->
        <div v-if="divinationState === 'revealed' && currentFortune" class="flex flex-col items-center py-4">
          <!-- Concordance Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-phantom-mint/10 border border-phantom-mint/40 text-phantom-mint text-xs font-mono font-bold mb-4 shadow-glow-mint">
            <span>☯️ Nhất Âm Nhất Dương · Thần Linh Ứng Thuận</span>
          </div>

          <!-- Fortune Title & Level -->
          <div class="text-center mb-8">
            <span class="text-xs font-mono font-bold px-3 py-1 rounded-lg bg-talisman-gold/20 text-talisman-gold border border-talisman-gold/40">
              {{ currentFortune.level }} · Ngũ Hành: {{ currentFortune.element }} · Phúc Khí: {{ currentFortune.score }}/100
            </span>
            <h2 class="text-2xl sm:text-4xl font-display font-extrabold text-white mt-3 tracking-tight">
              {{ currentFortune.title }}
            </h2>
          </div>

          <!-- Main Scroll Card Container -->
          <div class="w-full max-w-2xl bg-midnight-900/90 border border-talisman-gold/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-8 relative">
            
            <!-- Sacred Poem Callout -->
            <div class="p-6 rounded-2xl bg-midnight-950/80 border border-white/10 text-center mb-8 shadow-inner">
              <div class="text-[11px] font-mono text-talisman-gold uppercase tracking-widest mb-3">
                📜 Thơ Xăm Thánh Truyền
              </div>
              <div class="space-y-1.5 font-display text-base sm:text-lg text-amber-100 italic">
                <p>"{{ currentFortune.poem.line1 }}"</p>
                <p>"{{ currentFortune.poem.line2 }}"</p>
                <p>"{{ currentFortune.poem.line3 }}"</p>
                <p>"{{ currentFortune.poem.line4 }}"</p>
              </div>
            </div>

            <!-- Meaning Interpretation for Selected Category -->
            <div class="mb-6 text-left">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-xl">{{ activeCategoryInfo.icon }}</span>
                <h4 class="font-display font-bold text-lg text-white">
                  Lời Bình {{ activeCategoryInfo.label }}:
                </h4>
              </div>
              <p class="text-sm sm:text-base text-slate-200 leading-relaxed font-sans pl-7">
                {{ currentFortune.meanings[selectedCategory] }}
              </p>
            </div>

            <!-- Mascot Advice Box with Companion Avatar -->
            <div class="p-4 sm:p-5 rounded-2xl bg-phantom-mint/5 border border-phantom-mint/20 flex items-start gap-4 text-left">
              <div class="w-12 h-12 rounded-xl bg-midnight-900 border border-phantom-mint/40 flex items-center justify-center shrink-0 text-xl shadow-glow-mint">
                🧙‍♂️
              </div>
              <div>
                <div class="text-xs font-mono font-bold text-phantom-mint">Lời Khuyên Từ Chú Ma Cà Tưng:</div>
                <div class="text-xs sm:text-sm text-slate-300 mt-1 font-sans leading-relaxed">
                  {{ currentFortune.mascotAdvice }}
                </div>
              </div>
            </div>

            <!-- Lucky Badges Row -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-6 mt-6 border-t border-white/10 text-xs font-mono text-slate-400">
              <span>Con Số May Mắn: <strong class="text-talisman-gold text-sm">{{ currentFortune.luckyNumber }}</strong></span>
              <span>Màu Sắc Cát Tường: <span class="inline-block w-3 h-3 rounded-full align-middle ml-1" :style="{ backgroundColor: currentFortune.luckyColor }" /></span>
              <span>Ngày Xin Quẻ: <strong class="text-slate-200">{{ new Date().toLocaleDateString('vi-VN') }}</strong></span>
            </div>
          </div>

          <!-- Action Buttons (Download Talisman & Ask Again) -->
          <div class="flex flex-wrap items-center justify-center gap-4">
            <button
              type="button"
              class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-phantom-mint to-phantom-cyan text-midnight-950 font-display font-bold text-sm sm:text-base hover:brightness-110 shadow-glow-mint transition-all flex items-center gap-2 cursor-pointer active:scale-95"
              @click="downloadTalismanCard"
            >
              <span>Tải Thẻ Bùa May Mắn (Ảnh HD)</span>
              <span>📜</span>
            </button>

            <button
              type="button"
              class="px-6 py-3.5 rounded-2xl bg-midnight-900 border border-white/15 hover:border-white/30 text-white font-mono text-xs sm:text-sm transition-all flex items-center gap-2 cursor-pointer"
              @click="resetOracle"
            >
              <span>Xin Quẻ Khác</span>
              <span>🔄</span>
            </button>
          </div>
        </div>

      </div>

      <!-- Next Steps Continuation Hub -->
      <NextStepsHub current-path="/oracle" />
    </main>

    <Footer />
  </div>
</template>

<style scoped>
@keyframes wiggle {
  0%, 100% { transform: rotate(0deg) scale(1); }
  25% { transform: rotate(-8deg) scale(1.05); }
  75% { transform: rotate(8deg) scale(1.05); }
}
.animate-wiggle {
  animation: wiggle 0.15s ease-in-out infinite;
}
</style>
