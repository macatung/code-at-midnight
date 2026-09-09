<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import DecodeLayout from '@/Layouts/DecodeLayout.vue';
import Icons from '@/Components/ui/Icons.vue';

interface ColorToken {
  name: string;
  hex: string;
  desc: string;
}

interface TypographySpec {
  role: string;
  font: string;
  usage: string;
}

defineProps<{
  brand: {
    name: string;
    handle: string;
    subdomain: string;
    tagline: string;
    sub_tagline: string;
    colors: ColorToken[];
    typography: TypographySpec[];
    mascot_concept: {
      title: string;
      action: string;
      talisman: string;
      gear: string;
      robe: string;
    };
  };
}>();

const copiedHex = ref<string | null>(null);

const copyColor = (hex: string) => {
  if (navigator?.clipboard) {
    navigator.clipboard.writeText(hex);
    copiedHex.value = hex;
    setTimeout(() => {
      copiedHex.value = null;
    }, 2000);
  }
};
</script>

<template>
  <DecodeLayout
    title="Bộ Nhận Diện Thương Hiệu • Brand Identity Kit"
    description="Hướng dẫn quy chuẩn thương hiệu, logo vector SVG, bảng màu kỹ thuật và triết lý thiết kế của Ma Giải Mã."
  >
    <!-- Brand Hero -->
    <section class="pt-12 pb-16 bg-[#070e1c] border-b border-[#00b4d8]/20 text-left">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <div class="inline-flex items-center gap-2 text-xs font-mono text-[#00f5d4] uppercase font-bold tracking-widest">
          <Icons name="Sparkles" :size="14" />
          <span>Hệ Thống Nhận Diện Kênh (Brand Identity Kit)</span>
        </div>
        <h1 class="font-display text-4xl sm:text-5xl font-black text-white">
          Bộ Nhận Diện Thương Hiệu Ma Giải Mã
        </h1>
        <p class="text-base sm:text-xl text-slate-300 font-sans max-w-3xl leading-relaxed">
          Được thiết kế theo ngôn ngữ <strong class="text-[#00f5d4]">Cyber Blue &amp; Blueprint Slate</strong>, kết hợp chất liệu bản vẽ kỹ thuật CAD và mô hình Isometric 3D sống động.
        </p>
      </div>
    </section>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-20 text-left">
      <!-- 1. MASCOT SPOTLIGHT & BRAND DIFFERENTIATION -->
      <section class="space-y-6">
        <div class="border-b border-[#00b4d8]/20 pb-4">
          <div class="text-xs font-mono text-[#00f5d4] uppercase tracking-wider font-bold">Mascot Design &amp; Character Dissection</div>
          <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mt-1">
            Linh Vật: The Systems Anatomist (Kỹ Sư Mổ Xẻ Hệ Thống)
          </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          <!-- Mascot Visual Card -->
          <div class="lg:col-span-5">
            <div class="rounded-3xl bg-[#060b14] border border-[#00f5d4]/40 p-4 shadow-2xl shadow-[#00b4d8]/15 relative overflow-hidden group">
              <div class="w-full aspect-square rounded-2xl overflow-hidden bg-black flex items-center justify-center relative">
                <img
                  src="/brand/decode/mascot-ma-giai-ma-3d.png"
                  alt="Mascot Ma Giải Mã 3D"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />
              </div>
              <div class="p-3 text-center">
                <span class="text-xs font-mono text-[#00f5d4] font-bold">Bản vẽ 3D Render • @MaGiaiMa</span>
              </div>
            </div>
          </div>

          <!-- Feature Dissection Breakdown -->
          <div class="lg:col-span-7 space-y-4">
            <h3 class="font-display font-bold text-xl text-white">
              Sự Khác Biệt Tuyệt Đối Với Các Phiên Bản Khác:
            </h3>

            <div class="space-y-3 font-sans text-sm">
              <div class="p-4 rounded-xl bg-[#081020] border border-white/5 space-y-1">
                <div class="flex items-center gap-2 font-mono text-xs text-[#00f5d4] font-bold">
                  <span>01</span>
                  <span>TƯ THẾ "MỞ NẮP CHIP" (SURGICAL DECONSTRUCTION)</span>
                </div>
                <p class="text-slate-300 text-xs sm:text-sm">
                  Hai tay bóc tách nâng nắp lưng vi xử lý trong suốt (Exploded-view chip), giải phóng chùm ánh sáng vàng và các tia dữ liệu sợi quang học đa tầng.
                </p>
              </div>

              <div class="p-4 rounded-xl bg-[#081020] border border-white/5 space-y-1">
                <div class="flex items-center gap-2 font-mono text-xs text-[#38bdf8] font-bold">
                  <span>02</span>
                  <span>BÙA MẠCH IN MICA TRONG SUỐT (HOLOGRAPHIC PCB TALISMAN)</span>
                </div>
                <p class="text-slate-300 text-xs sm:text-sm">
                  Không dùng bùa giấy vàng cổ điển! Trán linh vật đính lá bùa mica trong suốt phát quang xanh neon, khắc cổng logic điện tử <code>AND</code>, <code>OR</code> và mã nhị phân <code>DECODE</code>.
                </p>
              </div>

              <div class="p-4 rounded-xl bg-[#081020] border border-white/5 space-y-1">
                <div class="flex items-center gap-2 font-mono text-xs text-amber-400 font-bold">
                  <span>03</span>
                  <span>KÍNH THỰC TẾ TĂNG CƯỜNG (AR HUD MONOCLE)</span>
                </div>
                <p class="text-slate-300 text-xs sm:text-sm">
                  Bên mắt trái đeo kính quang học hiển vi phóng đại 1000X có tâm ngắm laser và chỉ số đo lường trực tiếp (<code>0x8583</code>, <code>CLK: 4GHz</code>).
                </p>
              </div>

              <div class="p-4 rounded-xl bg-[#081020] border border-white/5 space-y-1">
                <div class="flex items-center gap-2 font-mono text-xs text-emerald-400 font-bold">
                  <span>04</span>
                  <span>ÁO ĐẠO SĨ THÊU ĐƯỜNG MẠCH BÁN DẪN &amp; THƯỚC ĐO CAD</span>
                </div>
                <p class="text-slate-300 text-xs sm:text-sm">
                  Thân áo đen huyền bí chạy các vệt mạch in màu ngọc lam phát sáng, cổ áo có vạch chia kích thước chuẩn kỹ thuật.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 2. OFFICIAL LOGOS & DOWNLOADABLE ASSETS -->
      <section class="space-y-6">
        <div class="border-b border-[#00b4d8]/20 pb-4">
          <div class="text-xs font-mono text-[#00f5d4] uppercase tracking-wider font-bold">Vector SVG &amp; PNG High-Res</div>
          <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mt-1">
            Kho Tài Nguyên Đồ Họa Chính Thức
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Item 1: Horizontal Logo -->
          <div class="p-5 rounded-2xl bg-[#080f1e] border border-white/10 space-y-3">
            <div class="h-32 rounded-xl bg-[#04070e] border border-white/5 flex items-center justify-center p-4">
              <img src="/brand/decode/decode-logo-horizontal.svg" alt="Logo Ngang Ma Giải Mã" class="max-h-full max-w-full object-contain" />
            </div>
            <div class="flex items-center justify-between">
              <div>
                <h4 class="font-bold text-white text-sm">Logo Ngang Đầy Đủ (560 × 120 px)</h4>
                <p class="text-xs text-slate-400 font-mono">Dùng cho Header website, Slide, Watermark</p>
              </div>
              <a
                href="/brand/decode/decode-logo-horizontal.svg"
                download
                class="px-3 py-1.5 rounded-lg bg-[#00f5d4]/15 hover:bg-[#00f5d4]/25 text-[#00f5d4] border border-[#00f5d4]/30 text-xs font-mono font-bold"
              >
                SVG ⬇
              </a>
            </div>
          </div>

          <!-- Item 2: Avatar Badge 512 -->
          <div class="p-5 rounded-2xl bg-[#080f1e] border border-white/10 space-y-3">
            <div class="h-32 rounded-xl bg-[#04070e] border border-white/5 flex items-center justify-center p-4">
              <img src="/brand/decode/decode-badge-avatar.svg" alt="Avatar Badge Ma Giải Mã" class="max-h-full object-contain" />
            </div>
            <div class="flex items-center justify-between">
              <div>
                <h4 class="font-bold text-white text-sm">Avatar / Badge Vuông (512 × 512 px)</h4>
                <p class="text-xs text-slate-400 font-mono">Avatar YouTube, App Icon, Social Profile</p>
              </div>
              <a
                href="/brand/decode/decode-badge-avatar.svg"
                download
                class="px-3 py-1.5 rounded-lg bg-[#00f5d4]/15 hover:bg-[#00f5d4]/25 text-[#00f5d4] border border-[#00f5d4]/30 text-xs font-mono font-bold"
              >
                SVG ⬇
              </a>
            </div>
          </div>

          <!-- Item 3: YouTube Banner -->
          <div class="p-5 rounded-2xl bg-[#080f1e] border border-white/10 space-y-3 md:col-span-2">
            <div class="h-44 rounded-xl bg-[#04070e] border border-white/5 flex items-center justify-center p-4 overflow-hidden">
              <img src="/brand/decode/decode-banner-youtube.svg" alt="Banner YouTube Ma Giải Mã" class="w-full h-full object-contain" />
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div>
                <h4 class="font-bold text-white text-sm">Banner YouTube Channel (2560 × 1440 px)</h4>
                <p class="text-xs text-slate-400 font-mono">Chuẩn tỷ lệ Safe Area 1546 × 423 px ở tâm, hiển thị trọn vẹn trên Mobile &amp; TV</p>
              </div>
              <div class="flex items-center gap-2">
                <a
                  href="/brand/decode/decode-banner-youtube.svg"
                  download
                  class="px-3 py-1.5 rounded-lg bg-[#00f5d4]/15 hover:bg-[#00f5d4]/25 text-[#00f5d4] border border-[#00f5d4]/30 text-xs font-mono font-bold"
                >
                  Tải SVG ⬇
                </a>
                <a
                  href="/brand/decode/decode-banner-youtube.png"
                  download
                  class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-mono"
                >
                  Tải PNG (4K) ⬇
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 3. COLOR PALETTE WITH ONE-CLICK COPY -->
      <section class="space-y-6">
        <div class="border-b border-[#00b4d8]/20 pb-4">
          <div class="text-xs font-mono text-[#00f5d4] uppercase tracking-wider font-bold">Color Palette • Click Để Sao Chép</div>
          <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mt-1">
            Bảng Mã Màu Kỹ Thuật (Blueprint Palette)
          </h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          <button
            v-for="c in brand.colors"
            :key="c.hex"
            type="button"
            class="p-4 rounded-xl border border-white/10 bg-[#080f1e] hover:border-[#00f5d4]/50 transition-all text-left space-y-2 group"
            @click="copyColor(c.hex)"
          >
            <!-- Color Swatch -->
            <div
              class="w-full h-14 rounded-lg shadow-inner flex items-center justify-center font-mono text-xs font-bold"
              :style="{ backgroundColor: c.hex }"
            >
              <span v-if="copiedHex === c.hex" class="px-2 py-0.5 rounded bg-black/80 text-white">
                Đã chép!
              </span>
            </div>

            <div>
              <div class="font-bold text-white text-xs">{{ c.name }}</div>
              <div class="font-mono text-xs text-[#00f5d4]">{{ c.hex }}</div>
              <div class="text-[11px] text-slate-400 font-sans line-clamp-1 mt-0.5">{{ c.desc }}</div>
            </div>
          </button>
        </div>
      </section>

      <!-- 4. YOUTUBE THUMBNAIL DESIGN BLUEPRINT -->
      <section class="space-y-6">
        <div class="border-b border-[#00b4d8]/20 pb-4">
          <div class="text-xs font-mono text-[#00f5d4] uppercase tracking-wider font-bold">Thumbnail Visual Rules (16:9)</div>
          <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mt-1">
            Quy Chuẩn Thiết Kế Thumbnail Thu Hút Triệu View
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
          <div class="p-5 rounded-2xl bg-[#080f1e] border border-white/5 space-y-2">
            <div class="text-[#00f5d4] font-mono text-xs font-bold">VÙNG 1: BÊN TRÁI (35%)</div>
            <h4 class="font-bold text-white text-base">Chủ Thể Đời Thực &amp; Bối Cảnh</h4>
            <p class="text-xs text-slate-300 leading-relaxed font-sans">
              Hình ảnh thực tế tạo cảm giác thân quen ngay lập tức: Máy POS cà thẻ thật, smartphone đang bấm Grab, con cá mập bơi gần đáy biển.
            </p>
          </div>

          <div class="p-5 rounded-2xl bg-[#080f1e] border border-white/5 space-y-2">
            <div class="text-[#38bdf8] font-mono text-xs font-bold">VÙNG 2: Ở GIỮA (30%)</div>
            <h4 class="font-bold text-white text-base">Vệt Cắt "Mở Nắp" (X-Ray Cutaway)</h4>
            <p class="text-xs text-slate-300 leading-relaxed font-sans">
              Đường quét phân rã xuyên thấu chuyển dịch mượt mà từ vỏ ngoài đời thực sang bản vẽ mạch vi phân isometric phát sáng.
            </p>
          </div>

          <div class="p-5 rounded-2xl bg-[#080f1e] border border-white/5 space-y-2">
            <div class="text-amber-400 font-mono text-xs font-bold">VÙNG 3: BÊN PHẢI (35%)</div>
            <h4 class="font-bold text-white text-base">Hệ Thống Lõi &amp; Thông Số Gây Sốc</h4>
            <p class="text-xs text-slate-300 leading-relaxed font-sans">
              Typography cực lớn (3-5 từ khóa), viền phát sáng neon, kèm con số chấn động: <span class="text-[#00f5d4] font-bold font-mono">2 GIÂY</span>, <span class="text-[#00f5d4] font-bold font-mono">50 TỶ TRANG</span>, <span class="text-[#00f5d4] font-bold font-mono">24 VỆ TINH</span>.
            </p>
          </div>
        </div>
      </section>
    </div>
  </DecodeLayout>
</template>
