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

// Live Commission Estimator
const estimatedOrderValue = ref<number>(1000000);
const estimatedShopeeRate = ref<number>(0.08); // 8% avg Shopee affiliate commission
const calculatorPresets = [200000, 500000, 1000000, 2000000, 5000000];
const estimatedShopeeCommission = computed(() => Math.round(estimatedOrderValue.value * estimatedShopeeRate.value));
const estimatedUserCashback = computed(() => Math.round(estimatedShopeeCommission.value * (props.stats.cashback_rate_percent / 100)));

// Popular Categories
const popularCategories = [
  { name: 'Thời Trang & Làm Đẹp', rate: 'Tới 15%', icon: 'Sparkles', desc: 'Quần áo, mỹ phẩm, phụ kiện' },
  { name: 'Điện Thoại & Phụ Kiện', rate: 'Tới 8%', icon: 'Zap', desc: 'Smartphone, sạc cáp, tai nghe' },
  { name: 'Nhà Cửa & Đời Sống', rate: 'Tới 12%', icon: 'Layout', desc: 'Đồ gia dụng, decor, nhà bếp' },
  { name: 'Mẹ Bé & Tiêu Dùng', rate: 'Tới 10%', icon: 'Check', desc: 'Bỉm sữa, tạp hóa, bách hóa' },
];

// FAQs
const openFaqIndex = ref<number | null>(null);
const toggleFaq = (idx: number) => {
  openFaqIndex.value = openFaqIndex.value === idx ? null : idx;
};
const faqs = [
  {
    q: 'Tiền hoàn Shopee được tính và đối soát như thế nào?',
    a: 'Khi bạn mua hàng qua link đã tạo, Shopee ghi nhận đơn hàng và gửi báo cáo qua Shopee Affiliate API. Hệ thống sẽ trích 80% số tiền hoa hồng mà Shopee chi trả để hoàn trực tiếp vào ví của bạn.'
  },
  {
    q: 'Sau bao lâu thì tiền hoàn về ví và có thể rút được?',
    a: 'Đơn hàng vừa đặt sẽ hiển thị ở mục "Chờ Shopee đối soát" trong vòng 1-24 giờ. Khi đơn hàng được giao thành công và hết hạn đổi trả của Shopee (thường 7-14 ngày), số dư sẽ chuyển thành "Khả dụng" để bạn rút ngay lập tức.'
  },
  {
    q: 'Rút tiền về tài khoản ngân hàng có mất phí không?',
    a: 'Hoàn toàn miễn phí 100%. Hệ thống chuyển khoản liên ngân hàng 24/7 qua cổng VietQR Napas247 với hạn mức tối thiểu chỉ từ 50.000 ₫.'
  },
  {
    q: 'Tôi chưa có tài khoản thì có nhận được tiền hoàn không?',
    a: 'Được chứ! Bạn chỉ cần dán link và mua sắm bình thường. Hệ thống sẽ tự động cấp một mã ví ẩn danh trên thiết bị của bạn. Khi bạn bấm Đăng ký hoặc Đăng nhập, toàn bộ số dư và lịch sử đơn sẽ tự động được gộp vào tài khoản chính thức.'
  },
];

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
    <section class="relative overflow-hidden pt-6 pb-4 sm:pt-8 sm:pb-5 border-b border-white/10 bg-midnight-950/60">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 text-center space-y-3.5 relative z-10">
        <!-- Floating Badge -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-midnight-900/90 border border-phantom-mint/30 text-xs font-mono text-slate-200 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-phantom-mint animate-pulse"></span>
          <span class="text-white font-medium">Shopee Affiliate Open API v2</span>
          <span class="text-phantom-mint font-bold">• Chia sẻ {{ stats.cashback_rate_percent }}% hoa hồng trực tiếp</span>
        </div>

        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight font-display text-white max-w-3xl mx-auto leading-tight">
          Cổng Hoàn Tiền Shopee
          <span class="block mt-1 sm:mt-1.5 bg-gradient-to-r from-phantom-mint via-emerald-300 to-amber-300 bg-clip-text text-transparent">
            Tích Lũy Lại {{ stats.cashback_rate_percent }}% Tiền Hoàn Mua Sắm
          </span>
        </h1>

        <p class="text-xs sm:text-sm text-slate-400 max-w-2xl mx-auto leading-relaxed">
          Dán link sản phẩm cần mua ➜ Nhận link tracking Shopee ➜ Tiền hoàn tự động cộng vào ví và rút về mọi ngân hàng qua Napas247 VietQR 24/7.
        </p>

        <!-- 4 Quick Trust Badges -->
        <div class="pt-1 flex flex-wrap items-center justify-center gap-2 sm:gap-3 text-[11px] font-mono text-slate-300">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10">
            <Icons name="Zap" :size="12" class="text-phantom-mint" />
            <span>Tạo link tức thì</span>
          </div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10">
            <Icons name="RotateCcw" :size="12" class="text-amber-400" />
            <span>Tự đối soát đơn</span>
          </div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10">
            <Icons name="Lock" :size="12" class="text-emerald-400" />
            <span>Rút VietQR từ 50k</span>
          </div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 border border-white/10">
            <Icons name="Check" :size="12" class="text-sky-400" />
            <span>100% Miễn phí</span>
          </div>
        </div>

        <!-- Main Navigation Tabs Bar -->
        <div class="pt-2 flex justify-center">
          <div class="p-1 bg-midnight-900/80 border border-white/10 rounded-2xl inline-flex items-center gap-1 sm:gap-1.5 shadow-xl backdrop-blur-xl">
            <button
              type="button"
              class="px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all"
              :class="activeTab === 'shopping' ? 'bg-phantom-mint text-midnight-950 shadow-glow-mint' : 'text-slate-400 hover:text-white'"
              @click="activeTab = 'shopping'"
            >
              <Icons name="Zap" :size="15" />
              <span>Sinh Link Mua Sắm</span>
            </button>

            <button
              type="button"
              class="px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all relative"
              :class="activeTab === 'wallet' ? 'bg-phantom-mint text-midnight-950 shadow-glow-mint' : 'text-slate-400 hover:text-white'"
              @click="activeTab = 'wallet'"
            >
              <Icons name="Lock" :size="15" />
              <span>Ví & Rút Tiền</span>
              <span
                v-if="wallet.available_balance >= 50000"
                class="w-2 h-2 rounded-full bg-emerald-400 absolute top-1.5 right-1.5 animate-ping"
              ></span>
            </button>

            <button
              type="button"
              class="px-4 sm:px-5 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all"
              :class="activeTab === 'orders' ? 'bg-phantom-mint text-midnight-950 shadow-glow-mint' : 'text-slate-400 hover:text-white'"
              @click="activeTab = 'orders'"
            >
              <Icons name="RotateCcw" :size="15" />
              <span>Đơn Hàng ({{ orders.length }})</span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Container -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-5 sm:py-6">
      <!-- ================================================================= -->
      <!-- TAB 1: SHOPPING & LINK GENERATOR -->
      <!-- ================================================================= -->
      <div v-if="activeTab === 'shopping'" class="space-y-5 animate-in fade-in duration-200">
        <!-- Main Link Generator Card -->
        <div class="bg-midnight-900/70 border border-white/10 rounded-2xl p-4 sm:p-5 shadow-xl backdrop-blur-md relative overflow-hidden">
          <div class="space-y-3.5">
            <div class="flex items-center justify-between">
              <label class="text-xs sm:text-sm font-bold text-white flex items-center gap-2">
                <span class="p-1 rounded-lg bg-orange-500/15 text-orange-400">
                  <Icons name="Zap" :size="15" />
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
            <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
              <div class="relative flex-grow">
                <input
                  v-model="inputUrl"
                  type="text"
                  placeholder="https://shopee.vn/product/... hoặc https://s.shopee.vn/..."
                  class="w-full h-11 sm:h-12 pl-3.5 pr-10 rounded-xl bg-black/50 border border-white/15 text-white text-xs sm:text-sm placeholder:text-slate-500 focus:outline-none focus:border-phantom-mint transition-colors"
                  @keyup.enter="handleGenerateLink"
                />
                <button
                  v-if="inputUrl"
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white p-1"
                  @click="inputUrl = ''; generatedResult = null; errorMessage = ''"
                >
                  <Icons name="X" :size="15" />
                </button>
              </div>

              <button
                type="button"
                class="h-11 sm:h-12 px-6 rounded-xl bg-phantom-mint hover:bg-phantom-mint/90 text-midnight-950 font-bold text-xs sm:text-sm tracking-wide transition-all shadow-glow-mint disabled:opacity-50 flex items-center justify-center gap-2 shrink-0"
                :disabled="isGenerating"
                @click="handleGenerateLink"
              >
                <Icons v-if="isGenerating" name="RotateCcw" :size="16" class="animate-spin" />
                <span>{{ isGenerating ? 'Đang tạo link...' : 'Tạo Link Hoàn Tiền Ngay' }}</span>
              </button>
            </div>

            <!-- Error Banner -->
            <div
              v-if="errorMessage"
              class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs flex items-center gap-2"
            >
              <Icons name="X" :size="15" class="shrink-0" />
              <span>{{ errorMessage }}</span>
            </div>

            <!-- Generated Result Success Card -->
            <div
              v-if="generatedResult && generatedResult.short_link"
              class="mt-4 p-4 rounded-xl bg-midnight-950/80 border border-phantom-mint/30 shadow-lg space-y-3 animate-in zoom-in-95 duration-200"
            >
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-phantom-mint tracking-wider flex items-center gap-1.5">
                  <span class="w-1.5 h-1.5 rounded-full bg-phantom-mint animate-pulse"></span>
                  Link Hoàn Tiền Đã Sẵn Sàng!
                </span>
                <span class="text-[11px] font-mono text-slate-400">Tracking: {{ generatedResult.sub_id }}</span>
              </div>

              <!-- Link Box -->
              <div class="flex flex-col sm:flex-row items-center gap-2.5 bg-black/60 p-2.5 sm:p-3 rounded-lg border border-white/10">
                <div class="font-mono text-xs sm:text-sm text-phantom-mint truncate w-full flex-grow select-all">
                  {{ generatedResult.short_link }}
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                  <button
                    type="button"
                    class="flex-1 sm:flex-none px-3.5 py-1.5 rounded-lg bg-white/10 hover:bg-white/15 text-white text-xs font-semibold flex items-center justify-center gap-1.5 transition-all"
                    @click="copyGeneratedLink"
                  >
                    <Icons :name="copiedResultLink ? 'Check' : 'Copy'" :size="13" />
                    <span>{{ copiedResultLink ? 'Đã sao chép!' : 'Sao chép' }}</span>
                  </button>

                  <a
                    :href="generatedResult.short_link"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex-1 sm:flex-none px-4 py-1.5 rounded-lg bg-phantom-mint hover:bg-phantom-mint/90 text-midnight-950 text-xs font-bold flex items-center justify-center gap-1.5 transition-all shadow-glow-mint"
                  >
                    <span>Mở Shopee Mua Ngay</span>
                    <Icons name="ChevronRight" :size="13" />
                  </a>
                </div>
              </div>

              <p class="text-[11px] text-slate-400 leading-relaxed">
                👉 <strong class="text-white">Lưu ý quan trọng</strong>: Vui lòng bấm vào link trên để mở ứng dụng hoặc website Shopee và thanh toán trong vòng 24 giờ để hệ thống ghi nhận hoa hồng chính xác.
              </p>
            </div>
          </div>
        </div>

        <!-- Live Commission Estimator Widget -->
        <div class="bg-midnight-900/70 border border-white/10 rounded-2xl p-4 sm:p-5 shadow-xl backdrop-blur-md relative overflow-hidden">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-white/10">
            <div>
              <h3 class="text-sm sm:text-base font-bold text-white flex items-center gap-2">
                <span class="p-1 rounded-lg bg-phantom-mint/15 text-phantom-mint">
                  <Icons name="Zap" :size="15" />
                </span>
                <span>Công Cụ Tính Tiền Hoàn Ước Tính</span>
              </h3>
              <p class="text-xs text-slate-400 mt-0.5">Chọn giá trị đơn hàng dự kiến để xem số tiền bạn sẽ nhận lại vào ví:</p>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap">
              <button
                v-for="p in calculatorPresets"
                :key="p"
                type="button"
                class="px-2.5 py-1 rounded-lg text-xs font-mono font-semibold transition-all border"
                :class="estimatedOrderValue === p ? 'bg-phantom-mint text-midnight-950 border-phantom-mint font-bold shadow-glow-mint' : 'bg-white/5 text-slate-300 border-white/10 hover:border-white/20'"
                @click="estimatedOrderValue = p"
              >
                {{ formatVND(p) }}
              </button>
            </div>
          </div>

          <!-- Slider Control -->
          <div class="space-y-2 mb-4">
            <div class="flex justify-between items-center text-xs font-mono">
              <span class="text-slate-400">Kéo chọn giá trị đơn:</span>
              <span class="text-white font-bold">{{ formatVND(estimatedOrderValue) }}</span>
            </div>
            <input
              v-model.number="estimatedOrderValue"
              type="range"
              min="100000"
              max="10000000"
              step="50000"
              class="w-full h-2 bg-white/10 rounded-lg appearance-none cursor-pointer accent-phantom-mint"
            />
          </div>

          <!-- 3-Column Calculation Result Box -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 sm:p-4 rounded-xl bg-black/50 border border-white/10">
            <div class="space-y-0.5">
              <span class="text-[11px] text-slate-400 font-medium">Đơn hàng Shopee</span>
              <div class="text-base sm:text-lg font-mono font-bold text-white">
                {{ formatVND(estimatedOrderValue) }}
              </div>
            </div>

            <div class="space-y-0.5">
              <span class="text-[11px] text-slate-400 font-medium">Shopee trả hoa hồng (~8%)</span>
              <div class="text-base sm:text-lg font-mono font-bold text-amber-300">
                ~{{ formatVND(estimatedShopeeCommission) }}
              </div>
            </div>

            <div class="space-y-0.5 sm:border-l sm:border-white/10 sm:pl-4">
              <span class="text-[11px] text-phantom-mint font-bold uppercase tracking-wider">Tiền Hoàn Về Ví (80%)</span>
              <div class="text-xl sm:text-2xl font-mono font-extrabold text-phantom-mint">
                +{{ formatVND(estimatedUserCashback) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Popular Cashback Categories Grid -->
        <div class="space-y-2.5">
          <div class="flex items-center justify-between">
            <h3 class="text-xs sm:text-sm font-bold text-white flex items-center gap-1.5">
              <Icons name="Sparkles" :size="14" class="text-amber-300" />
              <span>Tỷ Lệ Hoàn Tiền Theo Ngành Hàng Shopee</span>
            </h3>
            <span class="text-[11px] text-slate-400">Cập nhật theo biểu phí Shopee</span>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <div
              v-for="cat in popularCategories"
              :key="cat.name"
              class="p-3 rounded-xl bg-midnight-900/60 border border-white/10 hover:border-phantom-mint/30 transition-all group"
            >
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold text-white group-hover:text-phantom-mint transition-colors">{{ cat.name }}</span>
              </div>
              <div class="text-xs font-mono font-bold text-phantom-mint mb-0.5">{{ cat.rate }}</div>
              <p class="text-[10px] text-slate-400 truncate">{{ cat.desc }}</p>
            </div>
          </div>
        </div>

        <!-- Compact 3-Step Horizontal Stepper Guide -->
        <div class="rounded-2xl border border-white/10 bg-midnight-900/50 p-3 sm:p-4 backdrop-blur-md">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 divide-y md:divide-y-0 md:divide-x divide-white/5">
            <!-- Step 1 -->
            <div class="flex items-center gap-3 px-2 py-1">
              <span class="w-8 h-8 rounded-xl bg-phantom-mint/10 border border-phantom-mint/30 text-phantom-mint font-mono font-bold text-xs flex items-center justify-center shrink-0">01</span>
              <div class="min-w-0">
                <h4 class="text-xs font-bold text-white tracking-wide">Dán Link Shopee</h4>
                <p class="text-[11px] text-slate-400 truncate">Sao chép & dán link món đồ cần mua</p>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="flex items-center gap-3 px-2 md:pl-4 py-1">
              <span class="w-8 h-8 rounded-xl bg-amber-400/10 border border-amber-400/30 text-amber-300 font-mono font-bold text-xs flex items-center justify-center shrink-0">02</span>
              <div class="min-w-0">
                <h4 class="text-xs font-bold text-white tracking-wide">Mở Shopee Mua Hàng</h4>
                <p class="text-[11px] text-slate-400 truncate">Vào Shopee thanh toán như bình thường</p>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="flex items-center gap-3 px-2 md:pl-4 py-1">
              <span class="w-8 h-8 rounded-xl bg-emerald-400/10 border border-emerald-400/30 text-emerald-400 font-mono font-bold text-xs flex items-center justify-center shrink-0">03</span>
              <div class="min-w-0">
                <h4 class="text-xs font-bold text-white tracking-wide">Nhận Tiền Về Ví</h4>
                <p class="text-[11px] text-slate-400 truncate">Tiền hoàn tự động cộng vào ví rút ngay</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Frequently Asked Questions (Accordion) -->
        <div class="rounded-2xl border border-white/10 bg-midnight-900/50 p-4 sm:p-5 backdrop-blur-md space-y-3">
          <h3 class="text-xs sm:text-sm font-bold text-white flex items-center gap-2">
            <Icons name="Sun" :size="15" class="text-sky-400" />
            <span>Câu Hỏi Thường Gặp Khi Mua Sắm & Rút Tiền</span>
          </h3>

          <div class="space-y-2">
            <div
              v-for="(faq, idx) in faqs"
              :key="idx"
              class="rounded-xl border border-white/5 bg-black/30 overflow-hidden transition-all"
            >
              <button
                type="button"
                class="w-full px-3.5 py-2.5 text-left text-xs font-semibold text-slate-200 hover:text-white flex items-center justify-between gap-2"
                @click="toggleFaq(idx)"
              >
                <span>{{ faq.q }}</span>
                <Icons
                  :name="openFaqIndex === idx ? 'ChevronUp' : 'ChevronDown'"
                  :size="14"
                  class="text-slate-400 shrink-0 transition-transform"
                />
              </button>
              <div
                v-if="openFaqIndex === idx"
                class="px-3.5 pb-3 text-xs text-slate-400 leading-relaxed border-t border-white/5 pt-2"
              >
                {{ faq.a }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ================================================================= -->
      <!-- TAB 2: WALLET & WITHDRAWAL MANAGEMENT -->
      <!-- ================================================================= -->
      <div v-if="activeTab === 'wallet'" class="space-y-5 animate-in fade-in duration-200">
        <!-- 3 Balance Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
          <!-- Card 1: Available Balance -->
          <div class="bg-midnight-900/70 border border-phantom-mint/30 rounded-2xl p-4 sm:p-5 shadow-lg relative overflow-hidden backdrop-blur-md">
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-xs font-bold uppercase tracking-wider text-phantom-mint">Số Dư Khả Dụng</span>
              <span class="w-2 h-2 rounded-full bg-phantom-mint animate-pulse"></span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold font-mono text-phantom-mint">
              {{ formatVND(wallet.available_balance) }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1.5">
              Có thể rút ngay về tài khoản ngân hàng (tối thiểu 50.000 ₫).
            </p>
          </div>

          <!-- Card 2: Pending Balance -->
          <div class="bg-midnight-900/70 border border-amber-400/30 rounded-2xl p-4 sm:p-5 shadow-lg relative overflow-hidden backdrop-blur-md">
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-xs font-bold uppercase tracking-wider text-amber-300">Chờ Shopee Đối Soát</span>
              <span class="text-amber-400 text-xs">⏳ Đang xử lý</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold font-mono text-amber-300">
              {{ formatVND(wallet.pending_balance) }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1.5">
              Tiền hoàn từ các đơn hàng mới mua, sẽ mở khóa khi đơn hoàn thành.
            </p>
          </div>

          <!-- Card 3: Total Withdrawn -->
          <div class="bg-midnight-900/70 border border-white/10 rounded-2xl p-4 sm:p-5 shadow-lg relative overflow-hidden backdrop-blur-md">
            <div class="flex items-center justify-between mb-1.5">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Đã Rút Thành Công</span>
              <span class="text-emerald-400 text-xs">✓ Hoàn tất</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold font-mono text-white">
              {{ formatVND(wallet.withdrawn_balance) }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1.5">
              Tổng số tiền đã được chuyển thành công vào tài khoản ngân hàng.
            </p>
          </div>
        </div>

        <!-- Withdrawal Form & Account Card -->
        <div class="bg-midnight-900/70 border border-white/10 rounded-2xl p-4 sm:p-5 backdrop-blur-xl space-y-4 shadow-xl">
          <div class="border-b border-white/10 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <h2 class="text-base sm:text-lg font-bold font-display text-white flex items-center gap-2">
                <span class="p-1 rounded-lg bg-phantom-mint/15 text-phantom-mint">
                  <Icons name="Zap" :size="16" />
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
            class="p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2"
          >
            <Icons name="Check" :size="16" class="shrink-0" />
            <span>{{ withdrawSuccessMessage }}</span>
          </div>

          <div
            v-if="withdrawErrorMessage"
            class="p-3 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2"
          >
            <Icons name="X" :size="16" class="shrink-0" />
            <span>{{ withdrawErrorMessage }}</span>
          </div>

          <!-- Withdrawal Form -->
          <form @submit.prevent="handleWithdraw" class="space-y-4">
            <!-- Amount Input & Quick Preset Pills -->
            <div class="space-y-1.5">
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
                  class="w-full h-11 pl-3.5 pr-16 rounded-xl bg-black/50 border border-white/15 text-white font-mono font-bold text-sm sm:text-base focus:outline-none focus:border-phantom-mint transition-colors"
                />
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">VNĐ</span>
              </div>

              <!-- Quick Presets -->
              <div class="flex items-center gap-2 overflow-x-auto pt-0.5">
                <button
                  v-for="amt in presetAmounts"
                  :key="amt"
                  type="button"
                  class="px-2.5 py-1 rounded-lg text-xs font-mono font-semibold transition-all border"
                  :class="withdrawForm.amount === amt ? 'bg-phantom-mint text-midnight-950 border-phantom-mint' : 'bg-white/5 text-slate-300 border-white/10 hover:border-white/20'"
                  @click="setAmount(amt)"
                >
                  {{ formatVND(amt) }}
                </button>

                <button
                  type="button"
                  class="px-2.5 py-1 rounded-lg text-xs font-mono font-semibold transition-all border border-phantom-mint/40 text-phantom-mint hover:bg-phantom-mint/10"
                  @click="setMaxAmount"
                >
                  Rút tất cả ({{ formatVND(wallet.available_balance) }})
                </button>
              </div>
            </div>

            <!-- Bank Selection & Details -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <!-- Bank Name -->
              <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-300">Ngân hàng thụ hưởng</label>
                <select
                  v-model="withdrawForm.bank_name"
                  required
                  class="w-full h-11 px-3 rounded-xl bg-black/50 border border-white/15 text-white text-xs focus:outline-none focus:border-phantom-mint"
                >
                  <option v-for="b in bankList" :key="b" :value="b" class="bg-midnight-950 text-white">
                    {{ b }}
                  </option>
                </select>
              </div>

              <!-- Account Number -->
              <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-300">Số tài khoản ngân hàng</label>
                <input
                  v-model="withdrawForm.bank_account_number"
                  type="text"
                  required
                  placeholder="VD: 1903652881..."
                  class="w-full h-11 px-3.5 rounded-xl bg-black/50 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-phantom-mint"
                />
              </div>

              <!-- Account Name -->
              <div class="space-y-1">
                <label class="text-xs font-semibold text-slate-300">Tên chủ tài khoản (Viết hoa)</label>
                <input
                  v-model="withdrawForm.bank_account_name"
                  type="text"
                  required
                  placeholder="NGUYEN VAN A"
                  class="w-full h-11 px-3.5 rounded-xl bg-black/50 border border-white/15 text-white uppercase text-xs focus:outline-none focus:border-phantom-mint"
                  @input="withdrawForm.bank_account_name = (withdrawForm.bank_account_name || '').toUpperCase()"
                />
              </div>
            </div>

            <!-- Security: Password Verification (Required if Logged In) -->
            <div v-if="auth_user" class="p-3.5 rounded-xl bg-black/40 border border-white/10 space-y-1.5">
              <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-white flex items-center gap-1.5">
                  <Icons name="Lock" :size="13" class="text-phantom-mint" />
                  <span>Xác nhận mật khẩu tài khoản của bạn:</span>
                </label>
                <span class="text-[10px] text-slate-400">Bảo mật giao dịch</span>
              </div>
              <input
                v-model="withdrawForm.password"
                type="password"
                required
                placeholder="Nhập mật khẩu tài khoản để duyệt lệnh rút..."
                class="w-full h-10 px-3.5 rounded-lg bg-black/60 border border-white/15 text-white text-xs placeholder:text-slate-600 focus:outline-none focus:border-phantom-mint"
              />
            </div>

            <!-- Save Default Bank Checkbox -->
            <div class="flex items-center gap-2 pt-0.5">
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
            <div class="pt-1">
              <button
                type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-phantom-mint hover:bg-phantom-mint/90 text-midnight-950 font-bold text-xs sm:text-sm transition-all shadow-glow-mint disabled:opacity-50 flex items-center justify-center gap-2"
                :disabled="withdrawForm.processing || wallet.available_balance < 50000"
              >
                <Icons v-if="withdrawForm.processing" name="RotateCcw" :size="15" class="animate-spin" />
                <span>{{ withdrawForm.processing ? 'Đang gửi yêu cầu...' : 'Gửi Yêu Cầu Rút Tiền Về Ngân Hàng' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Withdrawal History Table -->
        <div class="space-y-2.5">
          <h3 class="text-sm font-bold font-display text-white flex items-center gap-2">
            <Icons name="Clock" :size="15" class="text-slate-400" />
            <span>Lịch Sử Các Lệnh Rút Tiền</span>
          </h3>

          <div class="bg-midnight-900/60 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-xl">
            <div class="overflow-x-auto">
              <table class="w-full text-left text-xs">
                <thead class="bg-white/5 border-b border-white/10 text-slate-400 uppercase tracking-wider font-semibold">
                  <tr>
                    <th class="p-3">Mã Lệnh</th>
                    <th class="p-3">Thời Gian</th>
                    <th class="p-3">Số Tiền</th>
                    <th class="p-3">Ngân Hàng & STK</th>
                    <th class="p-3">Trạng Thái</th>
                    <th class="p-3">Ghi Chú</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                  <tr v-if="withdrawals.length === 0">
                    <td colspan="6" class="p-6 text-center text-slate-500">
                      Chưa có lệnh rút tiền nào. Số dư từ 50.000 ₫ là bạn có thể tạo lệnh rút đầu tiên.
                    </td>
                  </tr>
                  <tr v-for="w in withdrawals" :key="w.id" class="hover:bg-white/5 transition-colors">
                    <td class="p-3 font-mono font-semibold text-white">#WD-{{ w.id }}</td>
                    <td class="p-3 text-slate-400">{{ formatDate(w.created_at) }}</td>
                    <td class="p-3 font-mono font-bold text-phantom-mint text-xs sm:text-sm">
                      {{ formatVND(w.amount) }}
                    </td>
                    <td class="p-3">
                      <div class="font-semibold text-white">{{ w.bank_name }}</div>
                      <div class="font-mono text-slate-400 text-[11px]">{{ w.bank_account_number }} ({{ w.bank_account_name }})</div>
                    </td>
                    <td class="p-3">
                      <span
                        v-if="w.status === 'completed' || w.status === 'paid'"
                        class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-[10px] font-semibold"
                      >
                        Đã chuyển tiền
                      </span>
                      <span
                        v-else-if="w.status === 'pending'"
                        class="px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[10px] font-semibold"
                      >
                        Đang chờ duyệt
                      </span>
                      <span
                        v-else
                        class="px-2 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[10px] font-semibold"
                      >
                        Bị từ chối
                      </span>
                    </td>
                    <td class="p-3 text-[11px]">
                      <span v-if="w.bank_ref_code" class="text-phantom-mint font-mono block">Mã GD: {{ w.bank_ref_code }}</span>
                      <span v-if="w.admin_note" class="text-slate-300 block">{{ w.admin_note }}</span>
                      <span v-if="w.note && !w.admin_note" class="text-slate-400 block">{{ w.note }}</span>
                      <span v-if="!w.bank_ref_code && !w.admin_note && !w.note" class="text-slate-600">—</span>
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
      <div v-if="activeTab === 'orders'" class="space-y-4 animate-in fade-in duration-200">
        <!-- Controls & Filter Toolbar -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
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
              class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all"
              :class="orderFilter === st.val ? 'bg-phantom-mint text-midnight-950 shadow-glow-mint' : 'bg-white/5 text-slate-400 hover:text-white'"
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
              class="px-3.5 py-1.5 rounded-xl bg-black/40 border border-white/15 text-white text-xs focus:outline-none focus:border-phantom-mint w-full sm:w-60 h-9"
            />

            <button
              type="button"
              class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-semibold flex items-center gap-1.5 shrink-0 transition-all disabled:opacity-50 h-9"
              :disabled="isSyncingOrders"
              @click="handleManualSync"
            >
              <Icons name="RotateCcw" :size="13" :class="{ 'animate-spin': isSyncingOrders }" />
              <span>{{ isSyncingOrders ? 'Đang kiểm tra...' : 'Kiểm tra đơn mới' }}</span>
            </button>
          </div>
        </div>

        <div v-if="syncFeedback" class="text-xs text-phantom-mint bg-phantom-mint/10 p-2.5 rounded-xl border border-phantom-mint/20">
          {{ syncFeedback }}
        </div>

        <!-- Orders Table / List -->
        <div class="bg-midnight-900/60 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-xl">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-white/5 border-b border-white/10 text-slate-400 uppercase tracking-wider font-semibold">
                <tr>
                  <th class="p-3">Mã Đơn Shopee</th>
                  <th class="p-3">Sản Phẩm</th>
                  <th class="p-3">Giá Trị Đơn (GMV)</th>
                  <th class="p-3">Tiền Hoàn Về Ví</th>
                  <th class="p-3">Trạng Thái</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                <tr v-if="filteredOrders.length === 0">
                  <td colspan="5" class="p-6 text-center text-slate-500">
                    Không tìm thấy đơn hàng nào phù hợp. Hãy dán link Shopee ở tab "Sinh Link Mua Sắm" và đặt hàng để nhận hoàn tiền!
                  </td>
                </tr>
                <tr v-for="o in filteredOrders" :key="o.id" class="hover:bg-white/5 transition-colors">
                  <td class="p-3">
                    <span class="font-mono font-bold text-white">#{{ o.shopee_order_id }}</span>
                    <div class="text-[10px] text-slate-500 mt-0.5">{{ formatDate(o.order_time || o.created_at) }}</div>
                  </td>

                  <td class="p-3 max-w-sm">
                    <div class="truncate font-semibold text-slate-200" :title="o.product_name">
                      {{ o.product_name }}
                    </div>
                  </td>

                  <td class="p-3 font-mono text-slate-300">
                    {{ formatVND(o.gmv) }}
                  </td>

                  <td class="p-3 font-mono font-bold text-xs sm:text-sm text-phantom-mint">
                    +{{ formatVND(o.cashback_amount) }}
                    <span class="text-[10px] text-slate-500 font-normal">({{ Math.round(o.cashback_rate * 100) }}%)</span>
                  </td>

                  <td class="p-3">
                    <span
                      v-if="o.status === 'confirmed'"
                      class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-[10px] font-semibold"
                    >
                      Đã cộng tiền
                    </span>
                    <span
                      v-else-if="o.status === 'pending'"
                      class="px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[10px] font-semibold"
                    >
                      Chờ Shopee duyệt
                    </span>
                    <span
                      v-else
                      class="px-2 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[10px] font-semibold"
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
