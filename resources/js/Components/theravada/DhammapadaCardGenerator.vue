<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { DHAMMAPADA_VERSES, DhammapadaVerse } from '@/data/dhammapadaCollection';
import { mindfulBell } from '@/audio/mindfulBellAudio';

const currentVerse = ref<DhammapadaVerse>(DHAMMAPADA_VERSES[0]);
const aspectRatio = ref<'story' | 'square'>('story'); // 'story' (9:16) or 'square' (1:1)
const isDrawing = ref(false);
const copied = ref(false);

const drawRandomVerse = () => {
  mindfulBell.ringBell(432, 5.0);
  const randomIndex = Math.floor(Math.random() * DHAMMAPADA_VERSES.length);
  currentVerse.value = DHAMMAPADA_VERSES[randomIndex];
};

const copyVerseText = async () => {
  const text = `☸️ KINH PHÁP CÚ (DHAMMAPADA) — KỆ SỐ ${currentVerse.value.verse_number}\n${currentVerse.value.chapter_vi} (${currentVerse.value.chapter_pali})\n\n📜 NGUYÊN VĂN PĀḶI:\n${currentVerse.value.pali}\n\n🌸 BẢN DỊCH VIỆT:\n${currentVerse.value.vietnamese}\n\n💡 TUỆ GIÁC CHIÊM NGHIỆM:\n${currentVerse.value.insight}\n\n— Ma Cà Tưng Dhamma (theravada.macatung.dev)`;
  
  try {
    await navigator.clipboard.writeText(text);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 3000);
  } catch (err) {
    console.error('Clipboard copy error:', err);
  }
};

const downloadCardImage = () => {
  isDrawing.value = true;

  const width = 1080;
  const height = aspectRatio.value === 'story' ? 1440 : 1080;

  const canvas = document.createElement('canvas');
  canvas.width = width;
  canvas.height = height;
  const ctx = canvas.getContext('2d');

  if (!ctx) {
    isDrawing.value = false;
    return;
  }

  // 1. Deep Obsidian / Amber Radial Background
  const bgGrad = ctx.createRadialGradient(width / 2, height * 0.4, 100, width / 2, height / 2, width * 0.8);
  bgGrad.addColorStop(0, '#1c1917');
  bgGrad.addColorStop(0.5, '#0c0a09');
  bgGrad.addColorStop(1, '#050505');
  ctx.fillStyle = bgGrad;
  ctx.fillRect(0, 0, width, height);

  // 2. Golden Ambient Center Glow
  const glowGrad = ctx.createRadialGradient(width / 2, height * 0.35, 50, width / 2, height * 0.35, 450);
  glowGrad.addColorStop(0, 'rgba(245, 158, 11, 0.22)');
  glowGrad.addColorStop(0.6, 'rgba(217, 119, 6, 0.08)');
  glowGrad.addColorStop(1, 'rgba(0, 0, 0, 0)');
  ctx.fillStyle = glowGrad;
  ctx.fillRect(0, 0, width, height);

  // 3. Elegant Gold Double Border
  ctx.strokeStyle = '#f59e0b';
  ctx.lineWidth = 4;
  ctx.strokeRect(40, 40, width - 80, height - 80);

  ctx.strokeStyle = '#fbbf24';
  ctx.lineWidth = 1.5;
  ctx.setLineDash([8, 8]);
  ctx.strokeRect(55, 55, width - 110, height - 110);
  ctx.setLineDash([]); // Reset line dash

  // 4. Corner Golden Ornaments
  const drawCorner = (x: number, y: number, rot: number) => {
    ctx.save();
    ctx.translate(x, y);
    ctx.rotate(rot);
    ctx.strokeStyle = '#f59e0b';
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.moveTo(0, 25);
    ctx.lineTo(0, 0);
    ctx.lineTo(25, 0);
    ctx.stroke();
    ctx.fillStyle = '#fef08a';
    ctx.beginPath();
    ctx.arc(0, 0, 4, 0, Math.PI * 2);
    ctx.fill();
    ctx.restore();
  };
  drawCorner(40, 40, 0);
  drawCorner(width - 40, 40, Math.PI / 2);
  drawCorner(width - 40, height - 40, Math.PI);
  drawCorner(40, height - 40, -Math.PI / 2);

  // 5. Header Emblem & Title
  ctx.textAlign = 'center';
  ctx.fillStyle = '#f59e0b';
  ctx.font = 'bold 36px serif';
  ctx.fillText('☸️  MA TỌA THIỀN  ☸️', width / 2, 120);

  ctx.fillStyle = '#d6d3d1';
  ctx.font = 'italic 24px serif';
  ctx.fillText('Thánh Điển Kinh Pháp Cú (Dhammapada)', width / 2, 160);

  // Divider with Golden Lotus
  ctx.strokeStyle = 'rgba(245, 158, 11, 0.4)';
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(width / 2 - 180, 195);
  ctx.lineTo(width / 2 - 30, 195);
  ctx.moveTo(width / 2 + 30, 195);
  ctx.lineTo(width / 2 + 180, 195);
  ctx.stroke();
  ctx.fillText('🌸', width / 2, 202);

  // 6. Verse Badge
  ctx.fillStyle = 'rgba(245, 158, 11, 0.15)';
  ctx.fillRect(width / 2 - 200, 230, 400, 48);
  ctx.strokeStyle = '#f59e0b';
  ctx.lineWidth = 1.5;
  ctx.strokeRect(width / 2 - 200, 230, 400, 48);

  ctx.fillStyle = '#fde047';
  ctx.font = 'bold 22px serif';
  ctx.fillText(`KỆ SỐ ${currentVerse.value.verse_number} • ${currentVerse.value.chapter_vi.toUpperCase()}`, width / 2, 262);

  // 7. Pali Original Verse
  ctx.fillStyle = '#fde68a';
  ctx.font = 'italic 24px "Lora", serif';
  const paliLines = currentVerse.value.pali.split('\n');
  let currentY = 340;
  paliLines.forEach(line => {
    ctx.fillText(`"${line}"`, width / 2, currentY);
    currentY += 34;
  });

  // Middle Lotus divider
  currentY += 20;
  ctx.strokeStyle = 'rgba(245, 158, 11, 0.3)';
  ctx.beginPath();
  ctx.moveTo(width / 2 - 120, currentY);
  ctx.lineTo(width / 2 + 120, currentY);
  ctx.stroke();

  // 8. Vietnamese Translation Verse (Large & Clear)
  currentY += 50;
  ctx.fillStyle = '#ffffff';
  ctx.font = 'bold 30px "Lora", serif';
  const viLines = currentVerse.value.vietnamese.split('\n');
  viLines.forEach(line => {
    ctx.fillText(line, width / 2, currentY);
    currentY += 46;
  });

  // 9. Insight Note Box
  currentY += 40;
  ctx.fillStyle = 'rgba(41, 37, 36, 0.8)';
  ctx.fillRect(100, currentY, width - 200, 100);
  ctx.strokeStyle = 'rgba(245, 158, 11, 0.3)';
  ctx.strokeRect(100, currentY, width - 200, 100);

  ctx.fillStyle = '#fbbf24';
  ctx.font = 'bold 20px serif';
  ctx.fillText('💡 TUỆ GIÁC CHIÊM NGHIỆM', width / 2, currentY + 36);

  ctx.fillStyle = '#e7e5e4';
  ctx.font = 'italic 20px serif';
  ctx.fillText(currentVerse.value.insight, width / 2, currentY + 72);

  // 10. Footer Watermark
  ctx.fillStyle = '#a8a29e';
  ctx.font = '18px serif';
  ctx.fillText('Gieo Duyên Lành • Chia Sẻ Chánh Pháp • theravada.macatung.dev', width / 2, height - 75);

  // Download Trigger
  setTimeout(() => {
    const dataUrl = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.download = `Phap-Cu-Ke-${currentVerse.value.verse_number}-MaToaThien.png`;
    link.href = dataUrl;
    link.click();
    isDrawing.value = false;
  }, 150);
};
</script>

<template>
  <div class="w-full max-w-4xl mx-auto p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-stone-900/95 via-stone-950/90 to-stone-900/95 border border-amber-500/30 shadow-2xl backdrop-blur-xl font-serif text-stone-100 relative overflow-hidden">
    <!-- Ambient Center Warm Aura -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-amber-500/10 rounded-full blur-[140px] pointer-events-none" />

    <div class="relative z-10 space-y-6">
      <!-- Section Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-amber-500/20 pb-5 text-left">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-bold mb-2">
            <span>🌸</span>
            <span>ỨNG DỤNG PHÁP BẢO • LAN TỎA CHÁNH PHÁP</span>
          </div>
          <h3 class="text-xl sm:text-2xl font-bold text-amber-100 tracking-tight">
            Trợ Niệm Pháp Cú & Xuất Thẻ Ảnh Chia Sẻ HD
          </h3>
          <p class="text-xs sm:text-sm text-stone-400 mt-1">
            Gieo duyên rút ngẫu nhiên lời dạy vàng ngọc của Đức Phật và tải về thẻ ảnh chất lượng cao để chia sẻ.
          </p>
        </div>

        <!-- Action: Random Draw Button -->
        <button
          @click="drawRandomVerse"
          class="shrink-0 flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-stone-950 font-bold text-xs sm:text-sm shadow-xl transition-all hover:scale-105 active:scale-95 cursor-pointer whitespace-nowrap"
        >
          <span>🎲</span>
          <span>Rút Quẻ Kệ Mới</span>
        </button>
      </div>

      <!-- Main Live Card Preview Stage -->
      <div
        class="relative mx-auto rounded-3xl border-2 border-amber-500/50 bg-stone-950 p-6 sm:p-8 shadow-2xl transition-all duration-300 flex flex-col justify-between text-center overflow-hidden"
        :class="aspectRatio === 'story' ? 'max-w-md aspect-[9/12]' : 'max-w-md aspect-square'"
      >
        <!-- Card Frame Gold Corner Dots -->
        <div class="absolute top-3 left-3 text-amber-500 text-xs">☸️</div>
        <div class="absolute top-3 right-3 text-amber-500 text-xs">☸️</div>
        <div class="absolute bottom-3 left-3 text-amber-500 text-xs">🌸</div>
        <div class="absolute bottom-3 right-3 text-amber-500 text-xs">🌸</div>

        <!-- Top Card Seal -->
        <div>
          <span class="text-[11px] font-serif uppercase tracking-widest text-amber-400 font-bold block mb-1">
            KINH PHÁP CÚ — KỆ SỐ {{ currentVerse.verse_number }}
          </span>
          <span class="text-xs text-stone-400 italic block">
            {{ currentVerse.chapter_vi }} ({{ currentVerse.chapter_pali }})
          </span>
          <div class="my-3 flex items-center justify-center gap-2 text-amber-500/40 text-xs">
            <span class="h-px w-12 bg-amber-500/30"></span>
            <span>🌸</span>
            <span class="h-px w-12 bg-amber-500/30"></span>
          </div>
        </div>

        <!-- Middle: Pali & Vietnamese Verses -->
        <div class="my-auto space-y-4">
          <!-- Pali Original -->
          <p class="text-xs sm:text-sm font-serif italic text-amber-200/90 leading-relaxed whitespace-pre-line">
            "{{ currentVerse.pali }}"
          </p>

          <div class="h-px w-20 bg-amber-500/20 mx-auto"></div>

          <!-- Vietnamese Translation -->
          <p class="text-sm sm:text-base md:text-lg font-serif font-bold text-stone-100 leading-relaxed whitespace-pre-line">
            {{ currentVerse.vietnamese }}
          </p>
        </div>

        <!-- Bottom: Insight Note & Watermark -->
        <div class="pt-3 border-t border-stone-900">
          <p class="text-[11px] font-serif text-amber-300/80 italic mb-2">
            💡 {{ currentVerse.insight }}
          </p>
          <span class="text-[10px] text-stone-400 font-sans tracking-wide block">
            Ma Tọa Thiền • theravada.macatung.dev
          </span>
        </div>
      </div>

      <!-- Controls & Download Bar -->
      <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-stone-800">
        <!-- Aspect Ratio Selection -->
        <div class="flex items-center gap-2 text-xs font-serif">
          <span class="text-stone-400">Định dạng ảnh:</span>
          <button
            @click="aspectRatio = 'story'"
            :class="[
              'px-3 py-1.5 rounded-xl border text-xs font-serif transition-all cursor-pointer',
              aspectRatio === 'story'
                ? 'bg-amber-500/20 border-amber-400 text-amber-300 font-bold'
                : 'bg-stone-900 border-stone-700 text-stone-400 hover:text-white'
            ]"
          >
            📱 Dọc 9:16 (Story/Zalo/Reels)
          </button>
          <button
            @click="aspectRatio = 'square'"
            :class="[
              'px-3 py-1.5 rounded-xl border text-xs font-serif transition-all cursor-pointer',
              aspectRatio === 'square'
                ? 'bg-amber-500/20 border-amber-400 text-amber-300 font-bold'
                : 'bg-stone-900 border-stone-700 text-stone-400 hover:text-white'
            ]"
          >
            🖼️ Vuông 1:1 (Feed/Avatar)
          </button>
        </div>

        <!-- Download & Copy Action Buttons -->
        <div class="flex items-center gap-2.5">
          <!-- Copy Verse Text -->
          <button
            @click="copyVerseText"
            class="flex items-center gap-1.5 px-4 py-2.5 rounded-2xl bg-stone-900 hover:bg-stone-800 border border-stone-700 text-stone-300 text-xs font-serif font-bold transition-all hover:text-amber-300 cursor-pointer shadow-md"
          >
            <span>{{ copied ? '✅' : '📋' }}</span>
            <span>{{ copied ? 'Đã Sao Chép Lời Kinh' : 'Sao Chép Chữ' }}</span>
          </button>

          <!-- Download HD PNG Card -->
          <button
            @click="downloadCardImage"
            :disabled="isDrawing"
            class="flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-stone-950 text-xs sm:text-sm font-serif font-bold shadow-lg transition-all hover:scale-105 active:scale-95 cursor-pointer disabled:opacity-50"
          >
            <span>📥</span>
            <span>{{ isDrawing ? 'Đang Xuất Ảnh...' : 'Tải Ảnh Thẻ HD (PNG)' }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
