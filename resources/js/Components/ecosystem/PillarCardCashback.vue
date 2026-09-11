<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Icons from '@/Components/ui/Icons.vue';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

// Interactive Mini Link Converter Preview
const sampleUrl = 'https://shopee.vn/Tai-nghe-Bluetooth-Sony-WH-1000XM5-i.1234567.8901234';
const inputLink = ref('');
const isConverted = ref(false);
const generatedLink = ref('');
const copied = ref(false);

const pasteSample = () => {
  inputLink.value = sampleUrl;
  handleConvert();
};

const handleConvert = () => {
  if (!inputLink.value.trim()) return;
  const cleanUrl = inputLink.value.trim();
  generatedLink.value = `https://s.shopee.vn/aff?sub_id=MC_MEMBER_80&origin=${encodeURIComponent(cleanUrl.slice(0, 32))}...`;
  isConverted.value = true;
};

const copyResult = async () => {
  if (!generatedLink.value) return;
  try {
    await navigator.clipboard.writeText(generatedLink.value);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2500);
  } catch {
    // Fallback
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2500);
  }
};
</script>

<template>
  <article
    class="relative group rounded-3xl border border-emerald-500/20 bg-gradient-to-b from-[#0c1815] via-midnight-900 to-midnight-950 p-6 sm:p-8 overflow-hidden transition-all duration-300 hover:border-emerald-500/40 hover:shadow-2xl hover:shadow-emerald-500/10 flex flex-col justify-between"
    aria-label="Pillar 3: Shopee Cashback Affiliate Utility"
  >
    <!-- Ambient Flame/Mint Glow -->
    <div class="absolute -right-20 -top-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-emerald-500/15 transition-all duration-500"></div>

    <div>
      <!-- Header Badges -->
      <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
            PILLAR 03
          </span>
          <span class="px-2.5 py-1 rounded-full text-xs font-mono bg-slate-900 text-slate-300 border border-white/10 flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            hoantien.macatung.dev
          </span>
        </div>

        <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl bg-orange-950/40 border border-orange-500/30 text-xs font-mono font-bold text-orange-400">
          <span class="text-orange-400">🔥</span>
          <span>{{ isVi ? 'Hoàn 80% Hoa Hồng' : '80% Commission Pass-Through' }}</span>
        </div>
      </div>

      <!-- Title & Description -->
      <div class="mb-6">
        <div class="text-xs font-mono uppercase tracking-widest text-emerald-400/90 mb-1">
          {{ isVi ? 'CỔNG HOÀN TIỀN & SINH LINK TRACKING AFFILIATE SHOPEE' : 'SHOPEE AFFILIATE CASHBACK & TRACKING ENGINE' }}
        </div>
        <h3 class="text-2xl sm:text-3xl font-display font-black tracking-tight text-white group-hover:text-emerald-300 transition-colors">
          {{ isVi ? 'Hoàn Tiền Shopee — Cashback 80% Minh Bạch' : 'Shopee Cashback — 80% Direct Pass-Through' }}
        </h3>
        <p class="mt-3 text-sm sm:text-base text-slate-300 leading-relaxed">
          {{ isVi
            ? 'Tiện ích hoàn tiền thông minh chuyển giao trực tiếp 80% hoa hồng Shopee cho người mua hàng. Tự động sinh link tracking gắn sub_id, theo dõi đơn hàng thời gian thực qua Open Platform API và rút tiền về mọi tài khoản ngân hàng nội địa từ 50.000đ với 0đ phí.'
            : 'A transparent cashback portal returning 80% of Shopee affiliate commissions directly to the shopper. Instant affiliate link generation with unique sub_id, real-time conversion sync, and flexible domestic bank payouts starting from 50,000 VND with zero fees.'
          }}
        </p>
      </div>

      <!-- Interactive Mini Link Converter Preview -->
      <div class="rounded-2xl border border-emerald-500/30 bg-black/40 p-4 sm:p-5 mb-6 backdrop-blur-md">
        <div class="flex items-center justify-between mb-3 text-xs">
          <span class="font-mono text-emerald-400 font-bold flex items-center gap-1.5">
            <Icons name="Zap" size="14" class="text-emerald-400" />
            {{ isVi ? 'Thử Nghiệm Chuyển Đổi Link Shopee Ngay' : 'Try Live Link Converter' }}
          </span>
          <button
            type="button"
            @click="pasteSample"
            class="text-[11px] font-mono text-slate-400 hover:text-emerald-300 underline underline-offset-2 transition-colors"
          >
            {{ isVi ? 'Dán link mẫu' : 'Paste sample' }}
          </button>
        </div>

        <!-- Input & Convert Button -->
        <div class="flex flex-col sm:flex-row gap-2">
          <input
            v-model="inputLink"
            type="text"
            :placeholder="isVi ? 'Dán link sản phẩm (shopee.vn/..., s.shopee.vn/...)' : 'Paste Shopee product link (shopee.vn/...)'"
            class="flex-1 bg-midnight-950/90 border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-400 font-mono transition-colors"
            @keyup.enter="handleConvert"
          />
          <button
            type="button"
            @click="handleConvert"
            class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-midnight-950 font-bold text-xs tracking-wide transition-all duration-200 shrink-0 shadow-md shadow-emerald-500/20"
          >
            {{ isVi ? 'Sinh Link Hoàn Tiền' : 'Generate Link' }}
          </button>
        </div>

        <!-- Result Box -->
        <div v-if="isConverted" class="mt-3 p-3 rounded-xl bg-emerald-950/30 border border-emerald-500/30">
          <div class="flex items-center justify-between gap-2 text-xs">
            <span class="font-mono text-slate-300 truncate max-w-[280px] sm:max-w-md">
              {{ generatedLink }}
            </span>
            <button
              type="button"
              @click="copyResult"
              class="px-2.5 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-[11px] font-mono shrink-0 transition-colors flex items-center gap-1"
            >
              <Icons :name="copied ? 'Check' : 'Copy'" size="12" />
              <span>{{ copied ? (isVi ? 'Đã chép!' : 'Copied!') : (isVi ? 'Sao chép' : 'Copy') }}</span>
            </button>
          </div>

          <!-- Teaser estimated cashback calculation -->
          <div class="mt-2 pt-2 border-t border-emerald-500/20 flex items-center justify-between text-[11px] font-mono text-emerald-400/90">
            <span>{{ isVi ? 'Ước tính hoàn đơn 500.000đ:' : 'Est. cashback on 500,000đ order:' }}</span>
            <strong class="text-orange-400 font-bold text-xs">~ 32.000đ (80% Pass-Through)</strong>
          </div>
        </div>

        <!-- Wallet Feature Highlights -->
        <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t border-white/5 text-center text-[11px] font-mono">
          <div class="p-2 rounded-lg bg-white/[0.02] border border-white/5">
            <div class="text-slate-500 text-[10px]">{{ isVi ? 'Chờ Duyệt' : 'Pending' }}</div>
            <div class="text-amber-400 font-bold mt-0.5">Tự Động Sync</div>
          </div>
          <div class="p-2 rounded-lg bg-white/[0.02] border border-white/5">
            <div class="text-slate-500 text-[10px]">{{ isVi ? 'Khả Dụng' : 'Available' }}</div>
            <div class="text-emerald-400 font-bold mt-0.5">Rút Từ 50.000đ</div>
          </div>
          <div class="p-2 rounded-lg bg-white/[0.02] border border-white/5">
            <div class="text-slate-500 text-[10px]">{{ isVi ? 'Ngân Hàng' : 'Banks' }}</div>
            <div class="text-cyan-400 font-bold mt-0.5">100% VN Banks</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Actions -->
    <div class="pt-4 border-t border-emerald-500/20 flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap gap-2 text-xs font-mono text-slate-400">
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-emerald-300">Hoàn 80%</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-emerald-300">Min 50.000đ</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-emerald-300">0đ Phí Dịch Vụ</span>
      </div>

      <a
        href="/hoantien"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-midnight-950 font-bold text-xs tracking-wide transition-all duration-200 hover:-translate-y-0.5 shadow-lg shadow-emerald-500/20"
      >
        <span>{{ isVi ? 'Vào Cổng Hoàn Tiền' : 'Open Cashback Portal' }}</span>
        <Icons name="ChevronRight" size="14" />
      </a>
    </div>
  </article>
</template>
