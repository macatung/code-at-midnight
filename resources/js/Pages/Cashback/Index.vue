<script setup lang="ts">
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import CashbackLayout from '@/Layouts/CashbackLayout.vue';
import type {
  CashbackWalletData,
  CashbackClickData,
  CashbackOrderData,
  CashbackWithdrawalData,
  CashbackStatsData,
  GenerateLinkResponse,
} from '@/types/cashback';

const props = defineProps<{
  wallet: CashbackWalletData;
  clicks: CashbackClickData[];
  orders: CashbackOrderData[];
  withdrawals: CashbackWithdrawalData[];
  stats: CashbackStatsData;
}>();

// Link Generator Form State
const inputUrl = ref('');
const isGenerating = ref(false);
const generatedResult = ref<GenerateLinkResponse | null>(null);
const errorMessage = ref('');
const copiedResultLink = ref(false);
const copiedItemLink = ref<number | null>(null);

// Dashboard Navigation Tabs
const activeTab = ref<'orders' | 'clicks' | 'withdraw'>('orders');
const orderFilter = ref<'all' | 'pending' | 'confirmed' | 'cancelled'>('all');

// Withdrawal Modal & Form State
const showWithdrawModal = ref(false);
const withdrawForm = useForm({
  amount: props.wallet.available_balance >= 50000 ? 50000 : 0,
  bank_name: 'Techcombank',
  bank_account_number: '',
  bank_account_name: '',
});

const bankList = [
  'Techcombank',
  'Vietcombank',
  'MB Bank',
  'ACB',
  'BIDV',
  'VPBank',
  'TPBank',
  'Agribank',
  'MoMo (Ví điện tử)',
  'ZaloPay',
];

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

// Filtered Orders
const filteredOrders = computed(() => {
  if (orderFilter.value === 'all') return props.orders;
  if (orderFilter.value === 'pending') {
    return props.orders.filter(o => o.status === 'pending');
  }
  if (orderFilter.value === 'confirmed') {
    return props.orders.filter(o => o.status === 'confirmed');
  }
  if (orderFilter.value === 'cancelled') {
    return props.orders.filter(o => o.status === 'cancelled' || o.status === 'refunded');
  }
  return props.orders;
});

// Client-side quick check
const isShopeeUrl = (url: string) => {
  const pattern = /^(https?:\/\/)?([a-zA-Z0-9_-]+\.)?(shopee\.vn|s\.shopee\.vn|shope\.ee|vn\.shp\.ee)(\/.*)?$/i;
  return pattern.test(url.trim());
};

// Dynamic API endpoint resolution (subdomain / vs fallback path /hoantien)
const getApiEndpoint = (endpoint: string) => {
  const isSubdomain = typeof window !== 'undefined' && window.location.hostname.startsWith('hoantien.');
  return isSubdomain ? `/${endpoint}` : `/hoantien/${endpoint}`;
};

// Submit Link Generator
const handleGenerateLink = async () => {
  errorMessage.value = '';
  const url = inputUrl.value.trim();

  if (!url) {
    errorMessage.value = 'Vui lòng dán đường dẫn sản phẩm Shopee.';
    return;
  }

  if (!isShopeeUrl(url)) {
    errorMessage.value = 'Đường dẫn không hợp lệ. Vui lòng dán link từ Shopee (shopee.vn, s.shopee.vn, shope.ee hoặc vn.shp.ee).';
    return;
  }

  isGenerating.value = true;
  try {
    const res = await fetch(getApiEndpoint('generate-link'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
      },
      body: JSON.stringify({ url }),
    });

    const data: GenerateLinkResponse = await res.json();
    if (res.ok && data.success) {
      generatedResult.value = data;
      // Refresh page props in background to update clicks history
      router.reload({ only: ['clicks', 'wallet'] });
    } else {
      errorMessage.value = data.message || 'Không thể tạo link hoàn tiền. Vui lòng thử lại sau.';
    }
  } catch {
    errorMessage.value = 'Đã có lỗi xảy ra trong quá trình kết nối. Vui lòng thử lại.';
  } finally {
    isGenerating.value = false;
  }
};

// Paste from Clipboard
const handlePaste = async () => {
  try {
    const text = await navigator.clipboard.readText();
    if (text) {
      inputUrl.value = text.trim();
      errorMessage.value = '';
    }
  } catch {
    // Clipboard permission denied or unsupported
  }
};

// Copy Link Result
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

const copyItemLink = async (id: number, link: string) => {
  try {
    await navigator.clipboard.writeText(link);
    copiedItemLink.value = id;
    setTimeout(() => {
      copiedItemLink.value = null;
    }, 2000);
  } catch {
    // ignore
  }
};

// Set preset amount for withdrawal
const setWithdrawAmount = (amt: number) => {
  withdrawForm.amount = Math.min(amt, props.wallet.available_balance);
};

// Submit Withdrawal
const submitWithdrawal = () => {
  withdrawForm.post(getApiEndpoint('withdraw'), {
    preserveScroll: true,
    onSuccess: () => {
      showWithdrawModal.value = false;
      withdrawForm.reset('amount', 'bank_account_number', 'bank_account_name');
    },
  });
};
</script>

<template>
  <CashbackLayout :wallet="wallet">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">

      <!-- HERO / LINK GENERATION SECTION -->
      <section class="relative rounded-3xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border border-slate-800 p-6 sm:p-10 shadow-2xl overflow-hidden">
        <!-- Ambient Glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-3xl mx-auto text-center space-y-4">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs font-semibold">
            <span>⚡ Tự Động Kết Nối Shopee Open Platform API</span>
          </div>

          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
            Hoàn Tiền Mua Sắm Shopee <br />
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 via-orange-500 to-amber-300">
              Đến {{ stats.cashback_rate_percent }}% Hoa Hồng
            </span>
          </h1>

          <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl mx-auto">
            Dán link bất kỳ từ ứng dụng hoặc web Shopee. Hệ thống tự động gắn mã tracking cá nhân <code class="bg-slate-800 text-orange-400 px-1.5 py-0.5 rounded font-mono text-xs">{{ wallet.sub_id }}</code> và tích lũy tiền hoàn vào ví của bạn.
          </p>

          <!-- Search & Generate Box -->
          <div class="pt-4 max-w-2xl mx-auto">
            <form @submit.prevent="handleGenerateLink" class="relative flex flex-col sm:flex-row items-stretch gap-2.5">
              <div class="relative flex-grow">
                <input
                  v-model="inputUrl"
                  type="text"
                  placeholder="Dán link sản phẩm: https://shopee.vn/... hoặc https://s.shopee.vn/..."
                  class="w-full h-14 pl-4 pr-24 rounded-2xl bg-slate-800/90 border border-slate-700/80 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all shadow-inner"
                />
                <!-- Paste Button inside input -->
                <button
                  @click="handlePaste"
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 px-2.5 py-1 rounded-lg bg-slate-700/60 hover:bg-slate-700 text-xs text-slate-300 hover:text-white transition-colors border border-slate-600/50"
                  title="Dán từ bộ nhớ tạm"
                >
                  📋 Dán link
                </button>
              </div>

              <button
                type="submit"
                :disabled="isGenerating"
                class="h-14 px-8 rounded-2xl bg-gradient-to-r from-orange-600 to-orange-500 hover:from-orange-500 hover:to-orange-400 text-white font-bold text-sm shadow-lg shadow-orange-500/25 transition-all flex items-center justify-center gap-2 flex-shrink-0 disabled:opacity-50 disabled:cursor-not-allowed group"
              >
                <span v-if="isGenerating" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <span v-else class="group-hover:scale-105 transition-transform">⚡ Tạo Link Hoàn Tiền</span>
              </button>
            </form>

            <!-- Error Banner -->
            <div v-if="errorMessage" class="mt-3 p-3 rounded-xl bg-rose-950/50 border border-rose-500/30 text-rose-300 text-xs text-left flex items-center gap-2">
              <span class="text-rose-400 font-bold">⚠️</span>
              <span>{{ errorMessage }}</span>
            </div>

            <!-- RESULT CARD (when short link generated) -->
            <div v-if="generatedResult" class="mt-6 p-5 rounded-2xl bg-slate-800/80 border border-orange-500/40 text-left space-y-3 shadow-xl">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-emerald-400 flex items-center gap-1.5">
                  <span>✓</span> Đã tạo link tracking thành công
                </span>
                <span class="text-[11px] text-slate-400 font-mono">sub_id: {{ generatedResult.sub_id }}</span>
              </div>

              <div class="flex flex-col sm:flex-row items-center gap-2.5">
                <input
                  :value="generatedResult.short_link"
                  readonly
                  class="w-full sm:flex-grow h-11 px-3.5 rounded-xl bg-slate-900 border border-slate-700 text-orange-300 font-mono text-xs focus:outline-none"
                />
                <div class="flex items-center gap-2 w-full sm:w-auto">
                  <button
                    @click="copyGeneratedLink"
                    type="button"
                    class="h-11 px-4 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-semibold text-xs transition-colors flex items-center gap-1.5 flex-1 sm:flex-initial justify-center"
                  >
                    <span>{{ copiedResultLink ? '✓ Đã chép!' : '📋 Sao chép' }}</span>
                  </button>

                  <a
                    :href="generatedResult.short_link"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="h-11 px-5 rounded-xl bg-orange-500 hover:bg-orange-400 text-white font-bold text-xs transition-colors flex items-center gap-1.5 flex-1 sm:flex-initial justify-center shadow-md shadow-orange-500/20"
                  >
                    <span>Mở Shopee Mua Ngay →</span>
                  </a>
                </div>
              </div>

              <p class="text-[11px] text-slate-400 leading-normal">
                💡 <strong class="text-slate-300">Lưu ý:</strong> Vui lòng bấm link trên và đặt mua sản phẩm trên ứng dụng hoặc web Shopee. Đơn hàng sẽ xuất hiện trong bảng đối soát bên dưới sau 1-24h khi Shopee cập nhật báo cáo.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- 3 WALLET BALANCE CARDS (R1 & R3) -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- 1. Pending Balance -->
        <div class="rounded-2xl bg-slate-900/80 border border-amber-500/20 p-6 flex flex-col justify-between relative overflow-hidden group hover:border-amber-500/40 transition-colors">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-amber-400"></span> Chờ Duyệt (Pending)
            </span>
            <span class="p-2 rounded-lg bg-amber-500/10 text-amber-400 text-sm">⏳</span>
          </div>

          <div>
            <div class="text-2xl sm:text-3xl font-black text-amber-300 font-mono tracking-tight">
              {{ formatVND(wallet.pending_balance) }}
            </div>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
              Đơn hàng Shopee đã ghi nhận. Tiền hoàn sẽ chuyển sang khả dụng sau khi Shopee đối soát và hết hạn đổi trả.
            </p>
          </div>

          <div class="mt-4 pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
            <span>Đơn đang chờ đối soát:</span>
            <span class="font-bold text-amber-400">{{ stats.pending_orders }} đơn</span>
          </div>
        </div>

        <!-- 2. Available Balance -->
        <div class="rounded-2xl bg-gradient-to-b from-emerald-950/40 to-slate-900/90 border border-emerald-500/30 p-6 flex flex-col justify-between relative overflow-hidden group hover:border-emerald-500/50 transition-colors shadow-lg shadow-emerald-950/20">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Khả Dụng (Available)
            </span>
            <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400 text-sm">💰</span>
          </div>

          <div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-300 font-mono tracking-tight">
              {{ formatVND(wallet.available_balance) }}
            </div>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
              Số dư đã đối soát thành công, sẵn sàng rút trực tiếp về tài khoản ngân hàng hoặc ví điện tử.
            </p>
          </div>

          <div class="mt-4 pt-4 border-t border-slate-800/80 flex items-center justify-between">
            <span class="text-xs text-slate-400">Tối thiểu: {{ formatVND(stats.min_withdrawal) }}</span>
            <button
              @click="showWithdrawModal = true"
              :disabled="wallet.available_balance < stats.min_withdrawal"
              class="px-4 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 disabled:bg-slate-800 text-white disabled:text-slate-500 text-xs font-bold transition-all disabled:cursor-not-allowed shadow-md shadow-emerald-500/20"
            >
              Rút Tiền Ngay
            </button>
          </div>
        </div>

        <!-- 3. Withdrawn Balance -->
        <div class="rounded-2xl bg-slate-900/80 border border-blue-500/20 p-6 flex flex-col justify-between relative overflow-hidden group hover:border-blue-500/40 transition-colors">
          <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-semibold uppercase tracking-wider text-blue-400 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-blue-400"></span> Đã Rút (Withdrawn)
            </span>
            <span class="p-2 rounded-lg bg-blue-500/10 text-blue-400 text-sm">🏦</span>
          </div>

          <div>
            <div class="text-2xl sm:text-3xl font-black text-blue-300 font-mono tracking-tight">
              {{ formatVND(wallet.withdrawn_balance) }}
            </div>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
              Tổng số tiền hoàn đã được chi trả thành công về tài khoản ngân hàng của bạn.
            </p>
          </div>

          <div class="mt-4 pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
            <span>Tổng số giao dịch:</span>
            <span class="font-bold text-blue-400">{{ withdrawals.length }} lần rút</span>
          </div>
        </div>
      </section>

      <!-- DASHBOARD TABS (Orders / Clicks / Withdrawals) -->
      <section class="rounded-3xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <!-- Tabs Header -->
        <div class="border-b border-slate-800 px-6 py-4 flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center gap-2">
            <button
              @click="activeTab = 'orders'"
              :class="[
                'px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2',
                activeTab === 'orders'
                  ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20'
                  : 'text-slate-400 hover:text-white hover:bg-slate-800',
              ]"
            >
              <span>📦 Đơn Hàng Của Tôi</span>
              <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'orders' ? 'bg-white/20' : 'bg-slate-800'">
                {{ orders.length }}
              </span>
            </button>

            <button
              @click="activeTab = 'clicks'"
              :class="[
                'px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2',
                activeTab === 'clicks'
                  ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20'
                  : 'text-slate-400 hover:text-white hover:bg-slate-800',
              ]"
            >
              <span>🔗 Link Đã Tạo</span>
              <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'clicks' ? 'bg-white/20' : 'bg-slate-800'">
                {{ clicks.length }}
              </span>
            </button>

            <button
              @click="activeTab = 'withdraw'"
              :class="[
                'px-4 py-2 rounded-xl text-xs font-bold transition-colors flex items-center gap-2',
                activeTab === 'withdraw'
                  ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20'
                  : 'text-slate-400 hover:text-white hover:bg-slate-800',
              ]"
            >
              <span>💳 Lịch Sử Rút Tiền</span>
              <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'withdraw' ? 'bg-white/20' : 'bg-slate-800'">
                {{ withdrawals.length }}
              </span>
            </button>
          </div>

          <!-- Order Status Sub-Filter (when orders tab active) -->
          <div v-if="activeTab === 'orders' && orders.length > 0" class="flex items-center gap-1.5 bg-slate-950 p-1 rounded-xl border border-slate-800 text-[11px]">
            <button
              @click="orderFilter = 'all'"
              :class="['px-2.5 py-1 rounded-lg font-medium transition-colors', orderFilter === 'all' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200']"
            >
              Tất cả ({{ orders.length }})
            </button>
            <button
              @click="orderFilter = 'pending'"
              :class="['px-2.5 py-1 rounded-lg font-medium transition-colors', orderFilter === 'pending' ? 'bg-amber-500/20 text-amber-400' : 'text-slate-400 hover:text-slate-200']"
            >
              Chờ duyệt ({{ stats.pending_orders }})
            </button>
            <button
              @click="orderFilter = 'confirmed'"
              :class="['px-2.5 py-1 rounded-lg font-medium transition-colors', orderFilter === 'confirmed' ? 'bg-emerald-500/20 text-emerald-400' : 'text-slate-400 hover:text-slate-200']"
            >
              Đã duyệt ({{ stats.confirmed_orders }})
            </button>
            <button
              @click="orderFilter = 'cancelled'"
              :class="['px-2.5 py-1 rounded-lg font-medium transition-colors', orderFilter === 'cancelled' ? 'bg-rose-500/20 text-rose-400' : 'text-slate-400 hover:text-slate-200']"
            >
              Hủy/Trả ({{ stats.cancelled_orders }})
            </button>
          </div>
        </div>

        <!-- TAB 1: ORDERS LIST -->
        <div v-if="activeTab === 'orders'" class="p-6">
          <div v-if="filteredOrders.length === 0" class="py-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-3xl mx-auto text-slate-500">
              🛍️
            </div>
            <h3 class="text-base font-bold text-slate-300">Chưa có đơn hàng nào được ghi nhận</h3>
            <p class="text-xs text-slate-400 max-w-md mx-auto">
              Hãy dán link Shopee ở ô bên trên, bấm "Tạo Link Hoàn Tiền" và hoàn tất mua sắm để nhận tiền hoàn vào ví.
            </p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="text-slate-400 uppercase bg-slate-950/60 border-b border-slate-800">
                <tr>
                  <th class="py-3 px-4">Mã Đơn Shopee</th>
                  <th class="py-3 px-4">Sản Phẩm</th>
                  <th class="py-3 px-4 text-right">Giá Trị (GMV)</th>
                  <th class="py-3 px-4 text-right">Hoa Hồng Shopee</th>
                  <th class="py-3 px-4 text-right">Tiền Hoàn (User)</th>
                  <th class="py-3 px-4 text-center">Trạng Thái</th>
                  <th class="py-3 px-4 text-right">Thời Gian</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/60">
                <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-slate-800/40 transition-colors">
                  <td class="py-3 px-4 font-mono font-semibold text-slate-300">
                    #{{ order.shopee_order_id }}
                  </td>
                  <td class="py-3 px-4 max-w-xs truncate text-slate-200" :title="order.product_name">
                    {{ order.product_name }}
                  </td>
                  <td class="py-3 px-4 text-right font-mono text-slate-300">
                    {{ formatVND(order.gmv) }}
                  </td>
                  <td class="py-3 px-4 text-right font-mono text-slate-400">
                    {{ formatVND(order.commission_shopee) }}
                  </td>
                  <td class="py-3 px-4 text-right font-mono font-bold text-orange-400">
                    +{{ formatVND(order.cashback_amount) }}
                  </td>
                  <td class="py-3 px-4 text-center">
                    <span
                      v-if="order.status === 'confirmed'"
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"
                    >
                      Đã Duyệt
                    </span>
                    <span
                      v-else-if="order.status === 'pending'"
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30"
                    >
                      Chờ Duyệt
                    </span>
                    <span
                      v-else
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-500/20 text-rose-400 border border-rose-500/30"
                    >
                      Hủy / Hoàn Trả
                    </span>
                  </td>
                  <td class="py-3 px-4 text-right text-slate-400 font-mono text-[11px]">
                    {{ formatDate(order.order_time || order.created_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 2: CLICKS & LINKS LIST -->
        <div v-if="activeTab === 'clicks'" class="p-6">
          <div v-if="clicks.length === 0" class="py-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-3xl mx-auto text-slate-500">
              🔗
            </div>
            <h3 class="text-base font-bold text-slate-300">Chưa có liên kết tracking nào</h3>
            <p class="text-xs text-slate-400 max-w-md mx-auto">
              Dán link sản phẩm Shopee ở trên để sinh nhanh liên kết tracking chứa mã cá nhân của bạn.
            </p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="text-slate-400 uppercase bg-slate-950/60 border-b border-slate-800">
                <tr>
                  <th class="py-3 px-4">Thời Gian</th>
                  <th class="py-3 px-4">Link Tracking Rút Gọn</th>
                  <th class="py-3 px-4">Link Gốc Shopee</th>
                  <th class="py-3 px-4 text-center">Mã Định Danh (sub_id)</th>
                  <th class="py-3 px-4 text-right">Thao Tác</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/60">
                <tr v-for="c in clicks" :key="c.id" class="hover:bg-slate-800/40 transition-colors">
                  <td class="py-3 px-4 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                    {{ formatDate(c.created_at) }}
                  </td>
                  <td class="py-3 px-4 font-mono text-orange-400 max-w-xs truncate">
                    {{ c.short_link || c.affiliate_url }}
                  </td>
                  <td class="py-3 px-4 text-slate-400 max-w-xs truncate" :title="c.original_url">
                    {{ c.original_url }}
                  </td>
                  <td class="py-3 px-4 text-center font-mono text-slate-300">
                    {{ c.sub_id }}
                  </td>
                  <td class="py-3 px-4 text-right whitespace-nowrap">
                    <button
                      @click="copyItemLink(c.id, c.short_link || c.affiliate_url)"
                      type="button"
                      class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs transition-colors"
                    >
                      {{ copiedItemLink === c.id ? '✓ Đã chép' : '📋 Chép lại' }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- TAB 3: WITHDRAWALS HISTORY -->
        <div v-if="activeTab === 'withdraw'" class="p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-200">Lịch Sử Yêu Cầu Rút Tiền</h3>
            <button
              @click="showWithdrawModal = true"
              :disabled="wallet.available_balance < stats.min_withdrawal"
              class="px-3.5 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 disabled:bg-slate-800 text-white disabled:text-slate-500 text-xs font-bold transition-all disabled:cursor-not-allowed"
            >
              + Tạo Yêu Cầu Rút Tiền
            </button>
          </div>

          <div v-if="withdrawals.length === 0" class="py-12 text-center space-y-3">
            <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center text-3xl mx-auto text-slate-500">
              💳
            </div>
            <h3 class="text-base font-bold text-slate-300">Chưa có yêu cầu rút tiền nào</h3>
            <p class="text-xs text-slate-400 max-w-md mx-auto">
              Khi số dư khả dụng đạt tối thiểu {{ formatVND(stats.min_withdrawal) }}, bạn có thể tạo yêu cầu rút tiền về tài khoản ngân hàng.
            </p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="text-slate-400 uppercase bg-slate-950/60 border-b border-slate-800">
                <tr>
                  <th class="py-3 px-4">Mã Yêu Cầu</th>
                  <th class="py-3 px-4">Ngân Hàng</th>
                  <th class="py-3 px-4">Số Tài Khoản / Tên Chủ TK</th>
                  <th class="py-3 px-4 text-right">Số Tiền</th>
                  <th class="py-3 px-4 text-center">Trạng Thái</th>
                  <th class="py-3 px-4 text-right">Thời Gian Tạo</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-800/60">
                <tr v-for="w in withdrawals" :key="w.id" class="hover:bg-slate-800/40 transition-colors">
                  <td class="py-3 px-4 font-mono font-semibold text-slate-300">
                    #WT-{{ w.id }}
                  </td>
                  <td class="py-3 px-4 font-semibold text-slate-200">
                    {{ w.bank_name }}
                  </td>
                  <td class="py-3 px-4 text-slate-300 font-mono">
                    {{ w.bank_account_number }} ({{ w.bank_account_name }})
                  </td>
                  <td class="py-3 px-4 text-right font-mono font-bold text-emerald-400">
                    {{ formatVND(w.amount) }}
                  </td>
                  <td class="py-3 px-4 text-center">
                    <span
                      v-if="w.status === 'completed'"
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"
                    >
                      Đã Chuyển Tiền
                    </span>
                    <span
                      v-else-if="w.status === 'pending'"
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-500/20 text-amber-400 border border-amber-500/30"
                    >
                      Đang Xử Lý
                    </span>
                    <span
                      v-else
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-500/20 text-rose-400 border border-rose-500/30"
                    >
                      Từ Chối
                    </span>
                  </td>
                  <td class="py-3 px-4 text-right text-slate-400 font-mono text-[11px]">
                    {{ formatDate(w.created_at) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- HOW IT WORKS & FAQ -->
      <section class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
        <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-6 space-y-3">
          <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-400 font-black flex items-center justify-center text-lg">
            1
          </div>
          <h4 class="font-bold text-white text-sm">Dán Link Sản Phẩm</h4>
          <p class="text-xs text-slate-400 leading-relaxed">
            Copy link sản phẩm từ ứng dụng Shopee hoặc trang web shopee.vn rồi dán vào ô tạo link ở trên.
          </p>
        </div>

        <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-6 space-y-3">
          <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-400 font-black flex items-center justify-center text-lg">
            2
          </div>
          <h4 class="font-bold text-white text-sm">Mua Hàng Như Bình Thường</h4>
          <p class="text-xs text-slate-400 leading-relaxed">
            Hệ thống sinh nhanh link tracking rút gọn. Bấm vào link để mở Shopee và chốt đơn đặt hàng như thường lệ.
          </p>
        </div>

        <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-6 space-y-3">
          <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-400 font-black flex items-center justify-center text-lg">
            3
          </div>
          <h4 class="font-bold text-white text-sm">Nhận Tiền Vào Ví & Rút</h4>
          <p class="text-xs text-slate-400 leading-relaxed">
            Shopee tự động báo cáo đơn hàng qua API. Tiền hoàn tích lũy trong ví và rút về tài khoản ngân hàng của bạn.
          </p>
        </div>
      </section>

      <!-- WITHDRAWAL MODAL -->
      <div v-if="showWithdrawModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-6 shadow-2xl relative">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
              <span>💳</span> Rút Tiền Về Tài Khoản
            </h3>
            <button
              @click="showWithdrawModal = false"
              type="button"
              class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors"
            >
              ✕
            </button>
          </div>

          <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/30 flex items-center justify-between">
            <span class="text-xs text-slate-300">Số dư khả dụng hiện có:</span>
            <span class="text-base font-black text-emerald-400 font-mono">{{ formatVND(wallet.available_balance) }}</span>
          </div>

          <form @submit.prevent="submitWithdrawal" class="space-y-4 text-xs">
            <!-- Amount -->
            <div>
              <label class="block text-slate-300 font-semibold mb-1.5">Số tiền muốn rút (₫)</label>
              <input
                v-model="withdrawForm.amount"
                type="number"
                :min="stats.min_withdrawal"
                :max="wallet.available_balance"
                step="1000"
                class="w-full h-11 px-3.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-sm focus:outline-none focus:border-emerald-500"
              />
              <div v-if="withdrawForm.errors.amount" class="text-rose-400 text-[11px] mt-1">
                {{ withdrawForm.errors.amount }}
              </div>
              <!-- Quick presets -->
              <div class="flex items-center gap-2 mt-2">
                <button
                  type="button"
                  @click="setWithdrawAmount(50000)"
                  class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300"
                >
                  50k
                </button>
                <button
                  type="button"
                  @click="setWithdrawAmount(100000)"
                  class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300"
                >
                  100k
                </button>
                <button
                  type="button"
                  @click="setWithdrawAmount(wallet.available_balance)"
                  class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-emerald-400 font-semibold"
                >
                  Toàn bộ
                </button>
              </div>
            </div>

            <!-- Bank Name -->
            <div>
              <label class="block text-slate-300 font-semibold mb-1.5">Ngân hàng thụ hưởng</label>
              <select
                v-model="withdrawForm.bank_name"
                class="w-full h-11 px-3.5 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-emerald-500"
              >
                <option v-for="b in bankList" :key="b" :value="b">{{ b }}</option>
              </select>
              <div v-if="withdrawForm.errors.bank_name" class="text-rose-400 text-[11px] mt-1">
                {{ withdrawForm.errors.bank_name }}
              </div>
            </div>

            <!-- Bank Account Number -->
            <div>
              <label class="block text-slate-300 font-semibold mb-1.5">Số tài khoản ngân hàng / SĐT Ví</label>
              <input
                v-model="withdrawForm.bank_account_number"
                type="text"
                placeholder="Ví dụ: 19036789123456"
                class="w-full h-11 px-3.5 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-xs focus:outline-none focus:border-emerald-500"
              />
              <div v-if="withdrawForm.errors.bank_account_number" class="text-rose-400 text-[11px] mt-1">
                {{ withdrawForm.errors.bank_account_number }}
              </div>
            </div>

            <!-- Bank Account Name -->
            <div>
              <label class="block text-slate-300 font-semibold mb-1.5">Tên chủ tài khoản (Viết in hoa không dấu)</label>
              <input
                v-model="withdrawForm.bank_account_name"
                type="text"
                placeholder="Ví dụ: NGUYEN VAN A"
                class="w-full h-11 px-3.5 rounded-xl bg-slate-800 border border-slate-700 text-white uppercase text-xs focus:outline-none focus:border-emerald-500"
              />
              <div v-if="withdrawForm.errors.bank_account_name" class="text-rose-400 text-[11px] mt-1">
                {{ withdrawForm.errors.bank_account_name }}
              </div>
            </div>

            <div class="pt-2">
              <button
                type="submit"
                :disabled="withdrawForm.processing || withdrawForm.amount < stats.min_withdrawal || withdrawForm.amount > wallet.available_balance"
                class="w-full h-12 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 disabled:opacity-50 text-white font-bold text-sm shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2"
              >
                <span v-if="withdrawForm.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <span v-else>Xác Nhận Rút Tiền</span>
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </CashbackLayout>
</template>
