<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import CashbackLayout from '@/Layouts/CashbackLayout.vue';
import Icons from '@/Components/ui/Icons.vue';
import AuthModal from '@/Components/Cashback/AuthModal.vue';
import type {
  CashbackWalletData,
  CashbackClickData,
  CashbackOrderData,
  CashbackWithdrawalData,
  CashbackStatsData,
  GenerateLinkResponse,
  AuthUserData,
} from '@/types/cashback';

const props = defineProps<{
  wallet: CashbackWalletData;
  auth_user?: AuthUserData | null;
  clicks: CashbackClickData[];
  orders: CashbackOrderData[];
  withdrawals: CashbackWithdrawalData[];
  stats: CashbackStatsData;
}>();

// Navigation Tabs
const activeTab = ref<'shopping' | 'wallet' | 'orders'>('shopping');

// Link Generator Form State
const inputUrl = ref('');
const isGenerating = ref(false);
const generatedResult = ref<GenerateLinkResponse | null>(null);
const errorMessage = ref('');
const copiedResultLink = ref(false);

// Order Filter & Search
const orderFilter = ref<'all' | 'pending' | 'confirmed' | 'cancelled'>('all');
const searchQuery = ref('');

// Auth Modal state
const showAuthModal = ref(false);
const authModalMode = ref<'login' | 'register'>('login');

// Withdrawal Form State
const withdrawForm = useForm({
  amount: props.wallet.available_balance >= 50000 ? 50000 : 0,
  bank_name: props.wallet.default_bank_name || 'Techcombank',
  bank_account_number: props.wallet.default_bank_account_number || '',
  bank_account_name: props.wallet.default_bank_account_name || '',
  save_default_bank: true,
  password: '',
});

const withdrawSuccessMessage = ref('');
const withdrawErrorMessage = ref('');

// Popular Vietnam Banks List
const bankList = [
  'Techcombank',
  'Vietcombank',
  'MB Bank',
  'ACB',
  'BIDV',
  'VPBank',
  'TPBank',
  'VietinBank',
  'Agribank',
  'Sacombank',
  'VIB',
  'HDBank',
  'OCB',
  'MSB',
  'MoMo',
  'ZaloPay',
];

// Presets for withdrawal
const presetAmounts = [50000, 100000, 200000, 500000];

const setAmount = (val: number) => {
  withdrawForm.amount = val;
};

const setMaxAmount = () => {
  withdrawForm.amount = Math.floor(props.wallet.available_balance);
};

// Formatters
const formatVND = (num: number) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num || 0);
};

const formatDate = (dateStr?: string | null) => {
  if (!dateStr) return '—';
  const d = new Date(dateStr);
  return d.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

// Client-side URL extractor supporting full URLs, shortlinks, or app share text
const extractShopeeUrl = (text: string): string | null => {
  const trimmed = text.trim();
  const directPattern = /^(https?:\/\/)?([a-zA-Z0-9_-]+\.)?(shopee\.vn|s\.shopee\.vn|shope\.ee|vn\.shp\.ee)(\/.*)?$/i;
  if (directPattern.test(trimmed)) {
    return trimmed;
  }
  const match = trimmed.match(/(https?:\/\/[^\s]+|s\.shopee\.vn\/[^\s]+|shope\.ee\/[^\s]+|vn\.shp\.ee\/[^\s]+|shopee\.vn\/[^\s]+)/i);
  return match ? match[0] : null;
};

// Dynamic API endpoint resolution (subdomain / vs fallback path /hoantien)
const getApiEndpoint = (endpoint: string) => {
  const isSubdomain = typeof window !== 'undefined' && window.location.hostname.startsWith('hoantien.');
  return isSubdomain ? `/${endpoint}` : `/hoantien/${endpoint}`;
};

// Clipboard Paste Helper
const pasteFromClipboard = async () => {
  try {
    const text = await navigator.clipboard.readText();
    if (text) {
      inputUrl.value = text;
      handleGenerateLink();
    }
  } catch {
    errorMessage.value = 'Không thể truy cập bộ nhớ tạm. Vui lòng dán liên kết thủ công.';
  }
};

// CSRF token retrieval helper
const getCsrfToken = (): string => {
  if (typeof document === 'undefined') return '';
  const meta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
  if (meta?.content) return meta.content;
  const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
  return match ? decodeURIComponent(match[1]) : '';
};

// Submit Link Generator
const handleGenerateLink = async () => {
  errorMessage.value = '';
  const rawInput = inputUrl.value.trim();

  if (!rawInput) {
    errorMessage.value = 'Vui lòng nhập hoặc dán đường dẫn sản phẩm Shopee.';
    return;
  }

  const cleanUrl = extractShopeeUrl(rawInput);
  if (!cleanUrl) {
    errorMessage.value = 'Link không hợp lệ. Vui lòng dán link từ Shopee (shopee.vn, s.shopee.vn, hoặc vn.shp.ee).';
    return;
  }

  isGenerating.value = true;
  generatedResult.value = null;

  try {
    const res = await fetch(getApiEndpoint('generate-link'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      body: JSON.stringify({ url: cleanUrl }),
    });

    const data = await res.json();
    if (res.ok && data.success) {
      generatedResult.value = data;
    } else {
      errorMessage.value = data.message || 'Không thể tạo link hoàn tiền. Vui lòng thử lại sau.';
    }
  } catch (err: any) {
    errorMessage.value = 'Lỗi kết nối máy chủ. Vui lòng thử lại.';
  } finally {
    isGenerating.value = false;
  }
};

// Copy short link
const copyGeneratedLink = async () => {
  if (!generatedResult.value?.short_link) return;
  try {
    await navigator.clipboard.writeText(generatedResult.value.short_link);
    copiedResultLink.value = true;
    setTimeout(() => {
      copiedResultLink.value = false;
    }, 2000);
  } catch {
    // ignore
  }
};

// Submit Withdrawal
const handleWithdraw = () => {
  withdrawErrorMessage.value = '';
  withdrawSuccessMessage.value = '';

  if (props.wallet.available_balance < 50000) {
    withdrawErrorMessage.value = 'Số dư khả dụng chưa đạt mức tối thiểu 50.000 đ để rút tiền.';
    return;
  }

  withdrawForm.post(getApiEndpoint('withdraw'), {
    preserveScroll: true,
    onSuccess: () => {
      withdrawSuccessMessage.value = 'Yêu cầu rút tiền đã được gửi thành công! Admin sẽ xử lý chuyển khoản qua VietQR trong vòng 24h.';
      withdrawForm.reset('password');
      router.reload({ only: ['wallet', 'withdrawals'] });
    },
    onError: (errors) => {
      if (errors.password) {
        withdrawErrorMessage.value = errors.password;
      } else if (errors.amount) {
        withdrawErrorMessage.value = errors.amount;
      } else {
        withdrawErrorMessage.value = 'Gửi yêu cầu rút tiền thất bại. Vui lòng kiểm tra lại thông tin.';
      }
    },
  });
};

// Filtered Orders
const filteredOrders = computed(() => {
  let list = props.orders;
  if (orderFilter.value === 'pending') {
    list = list.filter(o => o.status === 'pending');
  } else if (orderFilter.value === 'confirmed') {
    list = list.filter(o => o.status === 'confirmed');
  } else if (orderFilter.value === 'cancelled') {
    list = list.filter(o => o.status === 'cancelled' || o.status === 'refunded');
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.trim().toLowerCase();
    list = list.filter(o => o.product_name.toLowerCase().includes(q) || o.shopee_order_id.toLowerCase().includes(q));
  }

  return list;
});

// Trigger manual sync
const isSyncingOrders = ref(false);
const syncFeedback = ref('');
const handleManualSync = async () => {
  isSyncingOrders.value = true;
  syncFeedback.value = '';
  try {
    const res = await fetch(getApiEndpoint('sync'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
    });
    const data = await res.json();
    syncFeedback.value = data.message || 'Đã kiểm tra đơn hàng thành công.';
    router.reload({ only: ['orders', 'wallet'] });
  } catch {
    syncFeedback.value = 'Lỗi kết nối khi đồng bộ.';
  } finally {
    isSyncingOrders.value = false;
  }
};
</script>

<template>
  <CashbackLayout
    title="Cổng Hoàn Tiền Shopee"
    :wallet="wallet"
    :auth-user="auth_user"
  >
    <!-- Top Hero Header Section -->
    <section class="relative overflow-hidden pt-8 pb-6 sm:pt-12 sm:pb-8 border-b border-white/10 bg-gradient-to-b from-orange-950/20 via-black to-[#04070d]">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center space-y-4 relative z-10">
        <!-- Floating Badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-semibold text-slate-300">
          <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
          <span>Shopee Affiliate Open API v2</span>
          <span class="text-phantom-mint font-bold">• Chia lại {{ stats.cashback_rate_percent }}% hoa hồng</span>
        </div>

        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight font-display text-white">
          Mua Shopee Giá Gốc, <br class="hidden sm:inline" />
          <span class="bg-gradient-to-r from-orange-400 via-amber-300 to-phantom-mint bg-clip-text text-transparent">
            Nhận Lại Tới {{ stats.cashback_rate_percent }}% Tiền Hoàn
          </span>
        </h1>

        <p class="text-xs sm:text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
          Chỉ cần dán link sản phẩm Shopee, nhận link mua hàng đã gắn tracking và tiền hoàn sẽ tự động chảy về ví cá nhân của bạn sau khi giao hàng thành công.
        </p>

        <!-- Main Navigation Tabs Bar -->
        <div class="pt-4 flex justify-center">
          <div class="p-1.5 bg-white/5 border border-white/10 rounded-2xl inline-flex items-center gap-1 sm:gap-2 shadow-2xl backdrop-blur-xl">
            <button
              type="button"
              class="px-4 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all"
              :class="activeTab === 'shopping' ? 'bg-phantom-mint text-midnight-950 shadow-lg shadow-phantom-mint/20' : 'text-slate-400 hover:text-white'"
              @click="activeTab = 'shopping'"
            >
              <Icons name="Zap" :size="16" />
              <span>Sinh Link Mua Sắm</span>
            </button>

            <button
              type="button"
              class="px-4 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all relative"
              :class="activeTab === 'wallet' ? 'bg-phantom-mint text-midnight-950 shadow-lg shadow-phantom-mint/20' : 'text-slate-400 hover:text-white'"
              @click="activeTab = 'wallet'"
            >
              <Icons name="Lock" :size="16" />
              <span>Ví & Rút Tiền</span>
              <span
                v-if="wallet.available_balance >= 50000"
                class="w-2 h-2 rounded-full bg-emerald-400 absolute top-2 right-2 animate-ping"
              ></span>
            </button>

            <button
              type="button"
              class="px-4 sm:px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all"
              :class="activeTab === 'orders' ? 'bg-phantom-mint text-midnight-950 shadow-lg shadow-phantom-mint/20' : 'text-slate-400 hover:text-white'"
              @click="activeTab = 'orders'"
            >
              <Icons name="RotateCcw" :size="16" />
              <span>Đơn Hàng ({{ orders.length }})</span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Container -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
      <!-- ================================================================= -->
      <!-- TAB 1: SHOPPING & LINK GENERATOR -->
      <!-- ================================================================= -->
      <div v-if="activeTab === 'shopping'" class="space-y-8 animate-in fade-in duration-200">
        <!-- Main Link Generator Card -->
        <div class="bg-gradient-to-b from-white/10 to-white/5 border border-white/15 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden">
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <label class="text-sm font-bold text-white flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-orange-500/20 text-orange-400">
                  <Icons name="Zap" :size="16" />
                </span>
                <span>Dán link sản phẩm Shopee cần mua:</span>
              </label>

              <!-- Clipboard Button -->
              <button
                type="button"
                class="text-xs text-phantom-mint hover:underline font-semibold flex items-center gap-1"
                @click="pasteFromClipboard"
              >
                <Icons name="Copy" :size="13" />
                <span>Dán từ bộ nhớ tạm</span>
              </button>
            </div>

            <!-- Input & Generate CTA -->
            <div class="flex flex-col sm:flex-row items-stretch gap-3">
              <div class="relative flex-grow">
                <input
                  v-model="inputUrl"
                  type="text"
                  placeholder="https://shopee.vn/product/... hoặc https://s.shopee.vn/..."
                  class="w-full h-12 sm:h-14 pl-4 pr-10 rounded-2xl bg-black/40 border border-white/20 text-white text-xs sm:text-sm placeholder:text-slate-500 focus:outline-none focus:border-phantom-mint transition-colors"
                  @keyup.enter="handleGenerateLink"
                />
                <button
                  v-if="inputUrl"
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white p-1"
                  @click="inputUrl = ''; generatedResult = null; errorMessage = ''"
                >
                  <Icons name="X" :size="16" />
                </button>
              </div>

              <button
                type="button"
                class="h-12 sm:h-14 px-8 rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-400 hover:to-amber-400 text-midnight-950 font-bold text-sm tracking-wide transition-all shadow-xl shadow-orange-500/20 disabled:opacity-50 flex items-center justify-center gap-2 shrink-0"
                :disabled="isGenerating"
                @click="handleGenerateLink"
              >
                <Icons v-if="isGenerating" name="RotateCcw" :size="18" class="animate-spin" />
                <span>{{ isGenerating ? 'Đang tạo link...' : 'Tạo Link Hoàn Tiền Ngay' }}</span>
              </button>
            </div>

            <!-- Error Banner -->
            <div
              v-if="errorMessage"
              class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs flex items-center gap-2"
            >
              <Icons name="X" :size="16" class="shrink-0" />
              <span>{{ errorMessage }}</span>
            </div>

            <!-- Generated Result Success Card -->
            <div
              v-if="generatedResult && generatedResult.short_link"
              class="mt-6 p-6 rounded-2xl bg-gradient-to-br from-phantom-mint/15 via-black/60 to-black/80 border border-phantom-mint/40 shadow-2xl space-y-4 animate-in zoom-in-95 duration-200"
            >
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-phantom-mint tracking-wider flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-phantom-mint animate-pulse"></span>
                  Link Hoàn Tiền Đã Sẵn Sàng!
                </span>
                <span class="text-[11px] font-mono text-slate-400">Tracking: {{ generatedResult.sub_id }}</span>
              </div>

              <!-- Link Box -->
              <div class="flex flex-col sm:flex-row items-center gap-3 bg-black/60 p-3.5 rounded-xl border border-white/10">
                <div class="font-mono text-xs sm:text-sm text-emerald-300 truncate w-full flex-grow select-all">
                  {{ generatedResult.short_link }}
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                  <button
                    type="button"
                    class="flex-1 sm:flex-none px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold flex items-center justify-center gap-1.5 transition-all"
                    @click="copyGeneratedLink"
                  >
                    <Icons :name="copiedResultLink ? 'Check' : 'Copy'" :size="14" />
                    <span>{{ copiedResultLink ? 'Đã sao chép!' : 'Sao chép' }}</span>
                  </button>

                  <a
                    :href="generatedResult.short_link"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex-1 sm:flex-none px-5 py-2 rounded-xl bg-phantom-mint hover:bg-phantom-mint/90 text-midnight-950 text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-md shadow-phantom-mint/20"
                  >
                    <span>Mở Shopee Mua Ngay</span>
                    <Icons name="ChevronRight" :size="14" />
                  </a>
                </div>
              </div>

              <p class="text-[11px] text-slate-400 leading-relaxed">
                👉 <strong class="text-white">Lưu ý quan trọng</strong>: Vui lòng bấm vào link trên để mở ứng dụng hoặc website Shopee và thanh toán trong vòng 24 giờ để hệ thống ghi nhận hoa hồng chính xác.
              </p>
            </div>
          </div>
        </div>

        <!-- 3-Step Visual Infographic Guide -->
        <div class="space-y-4">
          <div class="text-center space-y-1">
            <h2 class="text-lg sm:text-xl font-bold font-display text-white">Quy Trình Hoàn Tiền Trong 3 Bước</h2>
            <p class="text-xs text-slate-400">Hoàn toàn tự động, minh bạch và miễn phí 100%</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Step 1 -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-3 relative group hover:border-white/20 transition-all">
              <div class="flex items-center justify-between">
                <span class="w-8 h-8 rounded-xl bg-orange-500/20 text-orange-400 font-bold text-sm flex items-center justify-center border border-orange-500/30">
                  1
                </span>
                <span class="text-xs text-slate-500 font-mono">Dán Link</span>
              </div>
              <h3 class="font-bold text-sm text-white">Copy & Tạo Link Hoàn Tiền</h3>
              <p class="text-xs text-slate-400 leading-relaxed">
                Mở Shopee, sao chép link món đồ bạn muốn mua và dán vào ô tìm kiếm ở trên để tạo link có mã theo dõi riêng.
              </p>
            </div>

            <!-- Step 2 -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-3 relative group hover:border-white/20 transition-all">
              <div class="flex items-center justify-between">
                <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 font-bold text-sm flex items-center justify-center border border-amber-500/30">
                  2
                </span>
                <span class="text-xs text-slate-500 font-mono">Mua Hàng</span>
              </div>
              <h3 class="font-bold text-sm text-white">Bấm Mở Shopee & Thanh Toán</h3>
              <p class="text-xs text-slate-400 leading-relaxed">
                Bấm vào liên kết vừa tạo để vào Shopee, thêm sản phẩm vào giỏ và đặt hàng như bình thường.
              </p>
            </div>

            <!-- Step 3 -->
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-3 relative group hover:border-white/20 transition-all">
              <div class="flex items-center justify-between">
                <span class="w-8 h-8 rounded-xl bg-phantom-mint/20 text-phantom-mint font-bold text-sm flex items-center justify-center border border-phantom-mint/30">
                  3
                </span>
                <span class="text-xs text-slate-500 font-mono">Nhận Tiền</span>
              </div>
              <h3 class="font-bold text-sm text-white">Rút Tiền Về Ngân Hàng</h3>
              <p class="text-xs text-slate-400 leading-relaxed">
                Sau khi đơn hàng giao thành công, tiền hoa hồng được cộng vào ví. Bạn có thể rút ngay về tài khoản ngân hàng bất kỳ lúc nào.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- ================================================================= -->
      <!-- TAB 2: WALLET & WITHDRAWAL MANAGEMENT -->
      <!-- ================================================================= -->
      <div v-if="activeTab === 'wallet'" class="space-y-8 animate-in fade-in duration-200">
        <!-- 3 Balance Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <!-- Card 1: Available Balance -->
          <div class="bg-gradient-to-br from-phantom-mint/20 via-phantom-mint/5 to-transparent border border-phantom-mint/30 rounded-3xl p-6 shadow-xl relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-bold uppercase tracking-wider text-phantom-mint">Số Dư Khả Dụng</span>
              <span class="w-2.5 h-2.5 rounded-full bg-phantom-mint animate-pulse"></span>
            </div>
            <div class="text-3xl font-extrabold font-mono text-white">
              {{ formatVND(wallet.available_balance) }}
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
              Có thể rút ngay về tài khoản ngân hàng (tối thiểu 50.000 ₫).
            </p>
          </div>

          <!-- Card 2: Pending Balance -->
          <div class="bg-gradient-to-br from-amber-500/15 via-amber-500/5 to-transparent border border-amber-500/30 rounded-3xl p-6 shadow-xl relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-bold uppercase tracking-wider text-amber-300">Chờ Shopee Đối Soát</span>
              <span class="text-amber-400 text-xs">⏳ Đang xử lý</span>
            </div>
            <div class="text-3xl font-extrabold font-mono text-white">
              {{ formatVND(wallet.pending_balance) }}
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
              Tiền hoàn từ các đơn hàng mới mua, sẽ mở khóa khi đơn hoàn thành.
            </p>
          </div>

          <!-- Card 3: Total Withdrawn -->
          <div class="bg-gradient-to-br from-blue-500/15 via-blue-500/5 to-transparent border border-blue-500/30 rounded-3xl p-6 shadow-xl relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-bold uppercase tracking-wider text-blue-300">Đã Rút Thành Công</span>
              <span class="text-blue-400 text-xs">✓ Hoàn tất</span>
            </div>
            <div class="text-3xl font-extrabold font-mono text-white">
              {{ formatVND(wallet.withdrawn_balance) }}
            </div>
            <p class="text-[11px] text-slate-400 mt-2">
              Tổng số tiền đã được chuyển thành công vào tài khoản ngân hàng.
            </p>
          </div>
        </div>

        <!-- Withdrawal Form & Account Card -->
        <div class="bg-white/5 border border-white/10 rounded-3xl p-6 sm:p-8 backdrop-blur-xl space-y-6">
          <div class="border-b border-white/10 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <h2 class="text-lg font-bold font-display text-white flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-phantom-mint/20 text-phantom-mint">
                  <Icons name="Zap" :size="18" />
                </span>
                <span>Yêu Cầu Rút Tiền Về Ngân Hàng</span>
              </h2>
              <p class="text-xs text-slate-400 mt-0.5">
                Chuyển khoản liên ngân hàng 24/7 qua VietQR hoàn toàn miễn phí.
              </p>
            </div>

            <div v-if="!auth_user" class="text-xs text-amber-300 bg-amber-500/10 border border-amber-500/20 px-3 py-1.5 rounded-xl">
              💡 Bạn đang dùng ví tạm. <button type="button" class="underline font-bold" @click="showAuthModal = true">Đăng ký tài khoản</button> để bảo vệ số dư.
            </div>
          </div>

          <!-- Feedback Alerts -->
          <div
            v-if="withdrawSuccessMessage"
            class="p-4 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2"
          >
            <Icons name="Check" :size="18" class="shrink-0" />
            <span>{{ withdrawSuccessMessage }}</span>
          </div>

          <div
            v-if="withdrawErrorMessage"
            class="p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2"
          >
            <Icons name="X" :size="18" class="shrink-0" />
            <span>{{ withdrawErrorMessage }}</span>
          </div>

          <!-- Withdrawal Form -->
          <form @submit.prevent="handleWithdraw" class="space-y-5">
            <!-- Amount Input & Quick Preset Pills -->
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <label class="text-xs font-semibold text-slate-300">Số tiền muốn rút (VNĐ)</label>
                <span class="text-[11px] text-slate-400">
                  Tối thiểu: <strong class="text-white">50.000 ₫</strong>
                </span>
              </div>

              <div class="relative">
                <input
                  v-model.number="withdrawForm.amount"
                  type="number"
                  step="1000"
                  min="50000"
                  :max="wallet.available_balance"
                  required
                  placeholder="50000"
                  class="w-full h-12 pl-4 pr-16 rounded-2xl bg-black/40 border border-white/10 text-white font-mono font-bold text-base focus:outline-none focus:border-phantom-mint transition-colors"
                />
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">VNĐ</span>
              </div>

              <!-- Quick Presets -->
              <div class="flex items-center gap-2 overflow-x-auto pt-1">
                <button
                  v-for="amt in presetAmounts"
                  :key="amt"
                  type="button"
                  class="px-3 py-1 rounded-xl text-xs font-mono font-semibold transition-all border"
                  :class="withdrawForm.amount === amt ? 'bg-phantom-mint text-midnight-950 border-phantom-mint' : 'bg-white/5 text-slate-300 border-white/10 hover:border-white/20'"
                  @click="setAmount(amt)"
                >
                  {{ formatVND(amt) }}
                </button>

                <button
                  type="button"
                  class="px-3 py-1 rounded-xl text-xs font-mono font-semibold transition-all border border-phantom-mint/40 text-phantom-mint hover:bg-phantom-mint/10"
                  @click="setMaxAmount"
                >
                  Rút tất cả ({{ formatVND(wallet.available_balance) }})
                </button>
              </div>
            </div>

            <!-- Bank Selection & Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <!-- Bank Name -->
              <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-300">Ngân hàng thụ hưởng</label>
                <select
                  v-model="withdrawForm.bank_name"
                  required
                  class="w-full h-12 px-3.5 rounded-2xl bg-black/40 border border-white/10 text-white text-xs focus:outline-none focus:border-phantom-mint"
                >
                  <option v-for="b in bankList" :key="b" :value="b" class="bg-midnight-950 text-white">
                    {{ b }}
                  </option>
                </select>
              </div>

              <!-- Account Number -->
              <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-300">Số tài khoản ngân hàng</label>
                <input
                  v-model="withdrawForm.bank_account_number"
                  type="text"
                  required
                  placeholder="VD: 1903652881..."
                  class="w-full h-12 px-4 rounded-2xl bg-black/40 border border-white/10 text-white font-mono text-xs focus:outline-none focus:border-phantom-mint"
                />
              </div>

              <!-- Account Name -->
              <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-300">Tên chủ tài khoản (Viết hoa)</label>
                <input
                  v-model="withdrawForm.bank_account_name"
                  type="text"
                  required
                  placeholder="NGUYEN VAN A"
                  class="w-full h-12 px-4 rounded-2xl bg-black/40 border border-white/10 text-white uppercase text-xs focus:outline-none focus:border-phantom-mint"
                  @input="withdrawForm.bank_account_name = (withdrawForm.bank_account_name || '').toUpperCase()"
                />
              </div>
            </div>

            <!-- Security: Password Verification (Required if Logged In) -->
            <div v-if="auth_user" class="p-4 rounded-2xl bg-white/5 border border-white/10 space-y-2">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-white flex items-center gap-1.5">
                  <Icons name="Lock" :size="14" class="text-phantom-mint" />
                  <span>Xác nhận mật khẩu tài khoản của bạn:</span>
                </label>
                <span class="text-[10px] text-slate-400">Bảo mật giao dịch</span>
              </div>
              <input
                v-model="withdrawForm.password"
                type="password"
                required
                placeholder="Nhập mật khẩu tài khoản để duyệt lệnh rút..."
                class="w-full h-11 px-4 rounded-xl bg-black/50 border border-white/10 text-white text-xs placeholder:text-slate-600 focus:outline-none focus:border-phantom-mint"
              />
            </div>

            <!-- Save Default Bank Checkbox -->
            <div class="flex items-center gap-2 pt-1">
              <input
                id="save_bank"
                v-model="withdrawForm.save_default_bank"
                type="checkbox"
                class="rounded border-white/20 bg-white/5 text-phantom-mint focus:ring-phantom-mint"
              />
              <label for="save_bank" class="text-xs text-slate-400 cursor-pointer">
                Lưu thông tin ngân hàng này làm mặc định cho những lần rút tiền sau.
              </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
              <button
                type="submit"
                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-phantom-mint hover:bg-phantom-mint/90 text-midnight-950 font-bold text-xs sm:text-sm transition-all shadow-lg shadow-phantom-mint/20 disabled:opacity-50 flex items-center justify-center gap-2"
                :disabled="withdrawForm.processing || wallet.available_balance < 50000"
              >
                <Icons v-if="withdrawForm.processing" name="RotateCcw" :size="16" class="animate-spin" />
                <span>{{ withdrawForm.processing ? 'Đang gửi yêu cầu...' : 'Gửi Yêu Cầu Rút Tiền Về Ngân Hàng' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Withdrawal History Table -->
        <div class="space-y-3">
          <h3 class="text-base font-bold font-display text-white flex items-center gap-2">
            <Icons name="Clock" :size="16" class="text-slate-400" />
            <span>Lịch Sử Các Lệnh Rút Tiền</span>
          </h3>

          <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-xl">
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs">
                <thead class="bg-white/5 border-b border-white/10 text-slate-400 uppercase tracking-wider font-semibold">
                  <tr>
                    <th class="p-3.5">Mã Lệnh</th>
                    <th class="p-3.5">Thời Gian</th>
                    <th class="p-3.5">Số Tiền</th>
                    <th class="p-3.5">Ngân Hàng & STK</th>
                    <th class="p-3.5">Trạng Thái</th>
                    <th class="p-3.5">Ghi Chú</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                  <tr v-if="withdrawals.length === 0">
                    <td colspan="6" class="p-8 text-center text-slate-500">
                      Chưa có lệnh rút tiền nào. Số dư từ 50.000 ₫ là bạn có thể tạo lệnh rút đầu tiên.
                    </td>
                  </tr>
                  <tr v-for="w in withdrawals" :key="w.id" class="hover:bg-white/5 transition-colors">
                    <td class="p-3.5 font-mono font-semibold text-white">#WD-{{ w.id }}</td>
                    <td class="p-3.5 text-slate-400">{{ formatDate(w.created_at) }}</td>
                    <td class="p-3.5 font-mono font-bold text-emerald-400 text-sm">
                      {{ formatVND(w.amount) }}
                    </td>
                    <td class="p-3.5">
                      <div class="font-semibold text-white">{{ w.bank_name }}</div>
                      <div class="font-mono text-slate-300">{{ w.bank_account_number }} ({{ w.bank_account_name }})</div>
                    </td>
                    <td class="p-3.5">
                      <span
                        v-if="w.status === 'pending'"
                        class="px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[11px] font-semibold"
                      >
                        Chờ duyệt
                      </span>
                      <span
                        v-else-if="w.status === 'paid' || w.status === 'completed'"
                        class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-[11px] font-semibold"
                      >
                        Đã chuyển tiền
                      </span>
                      <span
                        v-else
                        class="px-2.5 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[11px] font-semibold"
                      >
                        Từ chối
                      </span>
                    </td>
                    <td class="p-3.5 text-slate-400 text-[11px]">
                      <span v-if="w.bank_ref_code" class="text-emerald-400 font-mono block">Mã GD: {{ w.bank_ref_code }}</span>
                      <span v-if="w.admin_note" class="text-slate-300 block">{{ w.admin_note }}</span>
                      <span v-if="w.note && !w.admin_note" class="text-slate-400 block">{{ w.note }}</span>
                      <span v-if="!w.bank_ref_code && !w.admin_note && !w.note">—</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- ================================================================= -->
      <!-- TAB 3: ORDER TRACKING & SYNC -->
      <!-- ================================================================= -->
      <div v-if="activeTab === 'orders'" class="space-y-6 animate-in fade-in duration-200">
        <!-- Controls & Filter Toolbar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <!-- Status Pills -->
          <div class="flex items-center gap-1.5 overflow-x-auto">
            <button
              v-for="st in [
                { label: 'Tất cả', val: 'all' },
                { label: 'Chờ duyệt', val: 'pending' },
                { label: 'Đã hoàn thành', val: 'confirmed' },
                { label: 'Đã hủy/hoàn', val: 'cancelled' },
              ]"
              :key="st.val"
              type="button"
              class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all"
              :class="orderFilter === st.val ? 'bg-phantom-mint text-midnight-950 shadow-md' : 'bg-white/5 text-slate-400 hover:text-white'"
              @click="orderFilter = st.val as any"
            >
              {{ st.label }}
            </button>
          </div>

          <!-- Search & Manual Check Button -->
          <div class="flex items-center gap-2 w-full sm:w-auto">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm mã đơn hoặc tên sản phẩm..."
              class="px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-white text-xs focus:outline-none focus:border-phantom-mint w-full sm:w-64"
            />

            <button
              type="button"
              class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold flex items-center gap-1.5 shrink-0 transition-all disabled:opacity-50"
              :disabled="isSyncingOrders"
              @click="handleManualSync"
            >
              <Icons name="RotateCcw" :size="14" :class="{ 'animate-spin': isSyncingOrders }" />
              <span>{{ isSyncingOrders ? 'Đang kiểm tra...' : 'Kiểm tra đơn mới' }}</span>
            </button>
          </div>
        </div>

        <div v-if="syncFeedback" class="text-xs text-phantom-mint bg-phantom-mint/10 p-3 rounded-xl border border-phantom-mint/20">
          {{ syncFeedback }}
        </div>

        <!-- Orders Table / List -->
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-xl">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-white/5 border-b border-white/10 text-slate-400 uppercase tracking-wider font-semibold">
                <tr>
                  <th class="p-3.5">Mã Đơn Shopee</th>
                  <th class="p-3.5">Sản Phẩm</th>
                  <th class="p-3.5">Giá Trị Đơn (GMV)</th>
                  <th class="p-3.5">Tiền Hoàn Về Ví</th>
                  <th class="p-3.5">Trạng Thái</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                <tr v-if="filteredOrders.length === 0">
                  <td colspan="5" class="p-8 text-center text-slate-500">
                    Không tìm thấy đơn hàng nào phù hợp. Hãy dán link Shopee ở tab "Sinh Link Mua Sắm" và đặt hàng để nhận hoàn tiền!
                  </td>
                </tr>
                <tr v-for="o in filteredOrders" :key="o.id" class="hover:bg-white/5 transition-colors">
                  <td class="p-3.5">
                    <span class="font-mono font-bold text-white">#{{ o.shopee_order_id }}</span>
                    <div class="text-[10px] text-slate-500 mt-0.5">{{ formatDate(o.order_time || o.created_at) }}</div>
                  </td>

                  <td class="p-3.5 max-w-sm">
                    <div class="truncate font-semibold text-slate-200" :title="o.product_name">
                      {{ o.product_name }}
                    </div>
                  </td>

                  <td class="p-3.5 font-mono text-slate-300">
                    {{ formatVND(o.gmv) }}
                  </td>

                  <td class="p-3.5 font-mono font-bold text-sm text-emerald-400">
                    +{{ formatVND(o.cashback_amount) }}
                    <span class="text-[10px] text-slate-500 font-normal">({{ Math.round(o.cashback_rate * 100) }}%)</span>
                  </td>

                  <td class="p-3.5">
                    <span
                      v-if="o.status === 'confirmed'"
                      class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-[11px] font-semibold"
                    >
                      Đã cộng tiền
                    </span>
                    <span
                      v-else-if="o.status === 'pending'"
                      class="px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[11px] font-semibold"
                    >
                      Chờ Shopee duyệt
                    </span>
                    <span
                      v-else
                      class="px-2.5 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[11px] font-semibold"
                    >
                      Đã hủy/hoàn trả
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Auth Modal Triggered From Page -->
    <AuthModal
      :show="showAuthModal"
      :initial-mode="authModalMode"
      @close="showAuthModal = false"
      @success="showAuthModal = false"
    />
  </CashbackLayout>
</template>
