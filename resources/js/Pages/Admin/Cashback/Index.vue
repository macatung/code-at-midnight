<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Icons from '@/Components/ui/Icons.vue';

interface UserInfo {
  id: number;
  name: string;
  email: string;
}

interface WalletInfo {
  id: number;
  sub_id: string;
  pending_balance: number;
  available_balance: number;
  withdrawn_balance: number;
}

interface WithdrawalItem {
  id: number;
  wallet_id: number;
  user_id: number | null;
  amount: number;
  bank_name: string;
  bank_account_number: string;
  bank_account_name: string;
  status: 'pending' | 'paid' | 'completed' | 'rejected';
  bank_ref_code?: string | null;
  admin_note?: string | null;
  note?: string | null;
  created_at: string;
  processed_at?: string | null;
  user?: UserInfo | null;
  wallet?: WalletInfo | null;
}

interface OrderItem {
  id: number;
  shopee_order_id: string;
  product_name: string;
  product_image?: string | null;
  gmv: number;
  commission_shopee: number;
  cashback_rate: number;
  cashback_amount: number;
  status: string;
  order_time?: string | null;
  created_at: string;
  user?: UserInfo | null;
}

interface StatsInfo {
  total_gmv: number;
  total_commission: number;
  total_cashback_paid: number;
  pending_withdrawals_count: number;
  pending_withdrawals_amount: number;
  total_orders_count: number;
  confirmed_orders_count: number;
  total_wallets_count: number;
  registered_users_count: number;
}

const props = defineProps<{
  withdrawals: {
    data: WithdrawalItem[];
    links: any[];
    total: number;
  };
  orders: OrderItem[];
  stats: StatsInfo;
  currentFilter: string;
}>();

// Active tab
const activeTab = ref<'withdrawals' | 'orders'>('withdrawals');
const isSyncing = ref(false);

// Formatters
const formatVND = (amount: number) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount || 0);
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

// VietQR Bank Code Mapping for Vietnam banks
const bankBinMap: Record<string, string> = {
  Techcombank: 'TCB',
  Vietcombank: 'VCB',
  'MB Bank': 'MB',
  MB: 'MB',
  ACB: 'ACB',
  BIDV: 'BIDV',
  VPBank: 'VPB',
  TPBank: 'TPB',
  Agribank: 'VBA',
  VietinBank: 'CTG',
  Sacombank: 'STB',
  HDBank: 'HDB',
  VIB: 'VIB',
  OCB: 'OCB',
  MSB: 'MSB',
  SHB: 'SHB',
  Cake: 'CAKE',
  Timo: 'TIMO',
};

// Modals
const selectedWithdrawal = ref<WithdrawalItem | null>(null);
const showApproveModal = ref(false);
const showRejectModal = ref(false);

const approveForm = useForm({
  bank_ref_code: '',
  admin_note: '',
});

const rejectForm = useForm({
  reason: '',
});

const openApproveModal = (w: WithdrawalItem) => {
  selectedWithdrawal.value = w;
  approveForm.reset();
  approveForm.clearErrors();
  showApproveModal.value = true;
};

const openRejectModal = (w: WithdrawalItem) => {
  selectedWithdrawal.value = w;
  rejectForm.reset();
  rejectForm.clearErrors();
  showRejectModal.value = true;
};

const vietQrUrl = computed(() => {
  if (!selectedWithdrawal.value) return '';
  const w = selectedWithdrawal.value;
  const bankCode = bankBinMap[w.bank_name] || 'TCB';
  const accountNo = encodeURIComponent(w.bank_account_number.trim());
  const amount = Math.round(w.amount);
  const addInfo = encodeURIComponent(`HoanTien_WD${w.id}`);
  const accountName = encodeURIComponent(w.bank_account_name.trim());
  return `https://api.vietqr.io/image/${bankCode}-${accountNo}-compact2.jpg?amount=${amount}&addInfo=${addInfo}&accountName=${accountName}`;
});

// Actions
const submitApprove = () => {
  if (!selectedWithdrawal.value) return;
  approveForm.post(`/admin/cashback/withdrawals/${selectedWithdrawal.value.id}/approve`, {
    onSuccess: () => {
      showApproveModal.value = false;
      selectedWithdrawal.value = null;
    },
  });
};

const submitReject = () => {
  if (!selectedWithdrawal.value) return;
  rejectForm.post(`/admin/cashback/withdrawals/${selectedWithdrawal.value.id}/reject`, {
    onSuccess: () => {
      showRejectModal.value = false;
      selectedWithdrawal.value = null;
    },
  });
};

const triggerSync = () => {
  isSyncing.value = true;
  router.post('/admin/cashback/sync', {}, {
    onFinish: () => {
      isSyncing.value = false;
    },
  });
};

const filterStatus = (status: string) => {
  router.get('/admin/cashback', { status }, { preserveState: true, replace: true });
};
</script>

<template>
  <AdminLayout title="Quản Lý Hoàn Tiền Shopee">
    <Head title="Quản Lý Hoàn Tiền Shopee & Duyệt Rút Tiền — Admin CMS" />

    <div class="h-full flex flex-col p-4 sm:p-6 overflow-y-auto space-y-6">
      <!-- Header Banner & Action -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-xl">
        <div class="space-y-1">
          <div class="flex items-center gap-2.5">
            <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
              <Icons name="Zap" :size="20" />
            </span>
            <h1 class="text-xl sm:text-2xl font-bold font-display text-white">
              Quản Lý Hoàn Tiền Shopee & Duyệt Rút Tiền
            </h1>
          </div>
          <p class="text-xs sm:text-sm text-slate-400">
            Duyệt lệnh rút tiền chuyển khoản qua VietQR, theo dõi doanh thu đối soát Shopee Open Platform.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-white text-xs font-semibold flex items-center gap-2 transition-all disabled:opacity-50"
            :disabled="isSyncing"
            @click="triggerSync"
          >
            <Icons name="RotateCcw" :size="15" :class="{ 'animate-spin': isSyncing }" />
            <span>{{ isSyncing ? 'Đang đồng bộ...' : 'Đồng bộ đơn Shopee' }}</span>
          </button>
        </div>
      </div>

      <!-- KPI Stat Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Chờ duyệt rút tiền -->
        <div class="bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/20 rounded-2xl p-4">
          <div class="flex items-center justify-between text-xs text-amber-300 font-semibold mb-2">
            <span>Chờ Duyệt Rút</span>
            <span class="px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-200">
              {{ stats.pending_withdrawals_count }} lệnh
            </span>
          </div>
          <div class="text-2xl font-bold font-mono text-white">
            {{ formatVND(stats.pending_withdrawals_amount) }}
          </div>
          <p class="text-[11px] text-slate-400 mt-1">Cần Admin quét VietQR chi trả</p>
        </div>

        <!-- Card 2: Đã hoàn tiền cho user -->
        <div class="bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-transparent border border-emerald-500/20 rounded-2xl p-4">
          <div class="flex items-center justify-between text-xs text-emerald-300 font-semibold mb-2">
            <span>Đã Chi Trả Hoàn Tiền</span>
            <span class="p-1 rounded bg-emerald-500/20 text-emerald-400">
              <Icons name="Check" :size="14" />
            </span>
          </div>
          <div class="text-2xl font-bold font-mono text-white">
            {{ formatVND(stats.total_cashback_paid) }}
          </div>
          <p class="text-[11px] text-slate-400 mt-1">Tổng tiền thực tế đã giải ngân</p>
        </div>

        <!-- Card 3: GMV Shopee -->
        <div class="bg-gradient-to-br from-blue-500/10 via-blue-500/5 to-transparent border border-blue-500/20 rounded-2xl p-4">
          <div class="flex items-center justify-between text-xs text-blue-300 font-semibold mb-2">
            <span>Tổng GMV Mua Hàng</span>
            <span class="text-blue-400">{{ stats.confirmed_orders_count }}/{{ stats.total_orders_count }} đơn</span>
          </div>
          <div class="text-2xl font-bold font-mono text-white">
            {{ formatVND(stats.total_gmv) }}
          </div>
          <p class="text-[11px] text-slate-400 mt-1">Giá trị đơn hàng hoàn thành</p>
        </div>

        <!-- Card 4: Tổng hoa hồng Shopee -->
        <div class="bg-gradient-to-br from-purple-500/10 via-purple-500/5 to-transparent border border-purple-500/20 rounded-2xl p-4">
          <div class="flex items-center justify-between text-xs text-purple-300 font-semibold mb-2">
            <span>Hoa Hồng Shopee Trả</span>
            <span class="text-purple-400">{{ stats.registered_users_count }} users</span>
          </div>
          <div class="text-2xl font-bold font-mono text-white">
            {{ formatVND(stats.total_commission) }}
          </div>
          <p class="text-[11px] text-slate-400 mt-1">Gross commission nhận từ sàn</p>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-2 border-b border-white/10 pb-2">
        <button
          type="button"
          class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all"
          :class="activeTab === 'withdrawals' ? 'bg-white/15 text-white shadow' : 'text-slate-400 hover:text-white'"
          @click="activeTab = 'withdrawals'"
        >
          <Icons name="Zap" :size="15" />
          <span>Danh Sách Rút Tiền</span>
          <span
            v-if="stats.pending_withdrawals_count > 0"
            class="px-1.5 py-0.5 rounded-full bg-amber-500 text-black text-[10px] font-bold"
          >
            {{ stats.pending_withdrawals_count }}
          </span>
        </button>

        <button
          type="button"
          class="px-4 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all"
          :class="activeTab === 'orders' ? 'bg-white/15 text-white shadow' : 'text-slate-400 hover:text-white'"
          @click="activeTab = 'orders'"
        >
          <Icons name="ShoppingBag" :size="15" />
          <span>Đơn Hàng Đối Soát ({{ orders.length }})</span>
        </button>
      </div>

      <!-- TAB 1: WITHDRAWALS TABLE -->
      <div v-if="activeTab === 'withdrawals'" class="space-y-4">
        <!-- Status Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
          <button
            v-for="filter in [
              { label: 'Tất cả', val: 'all' },
              { label: 'Chờ duyệt', val: 'pending' },
              { label: 'Đã chi trả', val: 'paid' },
              { label: 'Đã từ chối', val: 'rejected' },
            ]"
            :key="filter.val"
            type="button"
            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
            :class="currentFilter === filter.val ? 'bg-white text-black font-semibold' : 'bg-white/5 text-slate-400 hover:text-white'"
            @click="filterStatus(filter.val)"
          >
            {{ filter.label }}
          </button>
        </div>

        <!-- Withdrawals Table -->
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-xl">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-white/5 border-b border-white/10 text-slate-400 uppercase tracking-wider font-semibold">
                <tr>
                  <th class="p-3.5">Mã & Ngày</th>
                  <th class="p-3.5">Người Yêu Cầu</th>
                  <th class="p-3.5">Số Tiền</th>
                  <th class="p-3.5">Tài Khoản Nhận</th>
                  <th class="p-3.5">Trạng Thái</th>
                  <th class="p-3.5 text-right">Thao Tác</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                <tr v-if="withdrawals.data.length === 0">
                  <td colspan="6" class="p-8 text-center text-slate-500">
                    Không có yêu cầu rút tiền nào trong danh sách.
                  </td>
                </tr>
                <tr
                  v-for="w in withdrawals.data"
                  :key="w.id"
                  class="hover:bg-white/5 transition-colors"
                >
                  <!-- ID & Time -->
                  <td class="p-3.5">
                    <div class="font-mono font-semibold text-white">#WD-{{ w.id }}</div>
                    <div class="text-[11px] text-slate-400 mt-0.5">{{ formatDate(w.created_at) }}</div>
                  </td>

                  <!-- User -->
                  <td class="p-3.5">
                    <div v-if="w.user" class="font-medium text-white">{{ w.user.name }}</div>
                    <div v-else class="text-slate-400 italic">Khách vãng lai</div>
                    <div class="text-[11px] text-slate-500 font-mono">{{ w.user?.email || w.wallet?.sub_id }}</div>
                  </td>

                  <!-- Amount -->
                  <td class="p-3.5">
                    <span class="font-mono font-bold text-sm text-emerald-400">
                      {{ formatVND(w.amount) }}
                    </span>
                  </td>

                  <!-- Bank Info -->
                  <td class="p-3.5">
                    <div class="font-semibold text-white flex items-center gap-1.5">
                      <span>{{ w.bank_name }}</span>
                    </div>
                    <div class="font-mono text-slate-300 font-medium tracking-wide">
                      {{ w.bank_account_number }}
                    </div>
                    <div class="text-[11px] text-slate-400 uppercase font-medium">
                      {{ w.bank_account_name }}
                    </div>
                  </td>

                  <!-- Status -->
                  <td class="p-3.5">
                    <span
                      v-if="w.status === 'pending'"
                      class="px-2.5 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[11px] font-semibold"
                    >
                      Chờ duyệt
                    </span>
                    <span
                      v-else-if="w.status === 'paid' || w.status === 'completed'"
                      class="px-2.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-[11px] font-semibold"
                    >
                      Đã chi trả
                    </span>
                    <span
                      v-else-if="w.status === 'rejected'"
                      class="px-2.5 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[11px] font-semibold"
                    >
                      Đã từ chối
                    </span>

                    <div v-if="w.bank_ref_code" class="text-[10px] text-slate-500 font-mono mt-1">
                      Ref: {{ w.bank_ref_code }}
                    </div>
                  </td>

                  <!-- Actions -->
                  <td class="p-3.5 text-right space-x-2">
                    <button
                      v-if="w.status === 'pending'"
                      type="button"
                      class="px-3 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/30 text-xs font-semibold inline-flex items-center gap-1.5 transition-all"
                      @click="openApproveModal(w)"
                    >
                      <Icons name="Zap" :size="13" />
                      <span>Quét VietQR & Duyệt</span>
                    </button>

                    <button
                      v-if="w.status === 'pending'"
                      type="button"
                      class="px-3 py-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/20 text-xs font-medium inline-flex items-center gap-1 transition-all"
                      @click="openRejectModal(w)"
                    >
                      <Icons name="X" :size="13" />
                      <span>Từ chối</span>
                    </button>

                    <span v-if="w.status !== 'pending'" class="text-slate-500 text-[11px] italic">
                      {{ formatDate(w.processed_at) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB 2: ORDERS TABLE -->
      <div v-if="activeTab === 'orders'" class="space-y-4">
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-xl">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-white/5 border-b border-white/10 text-slate-400 uppercase tracking-wider font-semibold">
                <tr>
                  <th class="p-3.5">Mã Đơn Shopee</th>
                  <th class="p-3.5">Sản Phẩm</th>
                  <th class="p-3.5">Giá Trị (GMV)</th>
                  <th class="p-3.5">Hoa Hồng Sàn</th>
                  <th class="p-3.5">Hoàn Cho User</th>
                  <th class="p-3.5">Trạng Thái</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/5">
                <tr v-if="orders.length === 0">
                  <td colspan="6" class="p-8 text-center text-slate-500">
                    Chưa ghi nhận đơn hàng Shopee nào. Bấm "Đồng bộ đơn Shopee" ở góc trên để đối soát.
                  </td>
                </tr>
                <tr
                  v-for="o in orders"
                  :key="o.id"
                  class="hover:bg-white/5 transition-colors"
                >
                  <td class="p-3.5">
                    <span class="font-mono font-semibold text-white">#{{ o.shopee_order_id }}</span>
                    <div class="text-[11px] text-slate-500 mt-0.5">{{ formatDate(o.order_time || o.created_at) }}</div>
                  </td>

                  <td class="p-3.5 max-w-xs">
                    <div class="truncate font-medium text-slate-200" :title="o.product_name">
                      {{ o.product_name }}
                    </div>
                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                      {{ o.user?.name || 'Khách vãng lai' }}
                    </div>
                  </td>

                  <td class="p-3.5 font-mono text-slate-300">
                    {{ formatVND(o.gmv) }}
                  </td>

                  <td class="p-3.5 font-mono text-purple-300 font-medium">
                    {{ formatVND(o.commission_shopee) }}
                  </td>

                  <td class="p-3.5 font-mono text-emerald-400 font-bold">
                    {{ formatVND(o.cashback_amount) }}
                    <span class="text-[10px] text-slate-500 font-normal">({{ Math.round(o.cashback_rate * 100) }}%)</span>
                  </td>

                  <td class="p-3.5">
                    <span
                      v-if="o.status === 'confirmed'"
                      class="px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-[11px]"
                    >
                      Thành công
                    </span>
                    <span
                      v-else-if="o.status === 'pending'"
                      class="px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[11px]"
                    >
                      Chờ duyệt
                    </span>
                    <span
                      v-else
                      class="px-2 py-0.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[11px]"
                    >
                      Đã hủy/hoàn
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL 1: VIETQR PAYOUT & APPROVAL MODAL -->
    <div
      v-if="showApproveModal && selectedWithdrawal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
      <div class="bg-midnight-900 border border-white/10 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
          <div class="flex items-center gap-2.5">
            <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
              <Icons name="Zap" :size="20" />
            </span>
            <div>
              <h3 class="text-lg font-bold text-white">Quét VietQR & Duyệt Chi Trả</h3>
              <p class="text-xs text-slate-400">Yêu cầu #WD-{{ selectedWithdrawal.id }}</p>
            </div>
          </div>
          <button
            type="button"
            class="p-2 rounded-xl text-slate-400 hover:text-white bg-white/5 hover:bg-white/10"
            @click="showApproveModal = false"
          >
            <Icons name="X" :size="18" />
          </button>
        </div>

        <!-- VietQR Dynamic Image -->
        <div class="flex flex-col items-center justify-center bg-white p-4 rounded-2xl shadow-inner">
          <img
            :src="vietQrUrl"
            alt="Mã VietQR Chuyển Khoản"
            class="w-56 h-56 object-contain rounded-lg"
          />
          <span class="text-[11px] text-slate-600 font-medium mt-2">
            Mở app ngân hàng bất kỳ quét mã để chuyển khoản chính xác 100%
          </span>
        </div>

        <!-- Transfer Information Summary -->
        <div class="bg-white/5 border border-white/10 rounded-xl p-3.5 space-y-1.5 text-xs">
          <div class="flex justify-between">
            <span class="text-slate-400">Số tiền chuyển:</span>
            <span class="font-mono font-bold text-emerald-400 text-sm">
              {{ formatVND(selectedWithdrawal.amount) }}
            </span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Ngân hàng thụ hưởng:</span>
            <span class="font-semibold text-white">{{ selectedWithdrawal.bank_name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Số tài khoản:</span>
            <span class="font-mono font-bold text-white">{{ selectedWithdrawal.bank_account_number }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Chủ tài khoản:</span>
            <span class="font-semibold text-white uppercase">{{ selectedWithdrawal.bank_account_name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-400">Nội dung chuyển:</span>
            <span class="font-mono text-amber-300 font-semibold">HoanTien_WD{{ selectedWithdrawal.id }}</span>
          </div>
        </div>

        <!-- Optional Ref Code & Note Form -->
        <form @submit.prevent="submitApprove" class="space-y-4">
          <div class="space-y-1">
            <label class="text-xs font-semibold text-slate-300">Mã giao dịch ngân hàng / Ref Code (tùy chọn)</label>
            <input
              v-model="approveForm.bank_ref_code"
              type="text"
              placeholder="VD: FT2625299812..."
              class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-xs font-mono focus:outline-none focus:border-emerald-500"
            />
          </div>

          <div class="space-y-1">
            <label class="text-xs font-semibold text-slate-300">Ghi chú duyệt chi trả</label>
            <input
              v-model="approveForm.admin_note"
              type="text"
              placeholder="Ghi chú thêm nếu có..."
              class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-xs focus:outline-none focus:border-emerald-500"
            />
          </div>

          <div class="flex items-center justify-end gap-3 pt-2">
            <button
              type="button"
              class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 text-xs font-medium"
              @click="showApproveModal = false"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black text-xs font-bold shadow-lg shadow-emerald-500/20 disabled:opacity-50"
              :disabled="approveForm.processing"
            >
              {{ approveForm.processing ? 'Đang lưu...' : 'Xác Nhận Đã Chuyển Tiền Thành Công' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL 2: REJECT WITHDRAWAL MODAL -->
    <div
      v-if="showRejectModal && selectedWithdrawal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
    >
      <div class="bg-midnight-900 border border-white/10 rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between border-b border-white/10 pb-3">
          <div class="flex items-center gap-2 text-rose-400 font-bold">
            <Icons name="X" :size="20" />
            <span>Từ Chối Yêu Cầu Rút Tiền</span>
          </div>
          <button
            type="button"
            class="p-1.5 rounded-lg text-slate-400 hover:text-white bg-white/5"
            @click="showRejectModal = false"
          >
            <Icons name="X" :size="16" />
          </button>
        </div>

        <p class="text-xs text-slate-300">
          Số tiền <span class="font-mono font-bold text-white">{{ formatVND(selectedWithdrawal.amount) }}</span> sẽ được tự động hoàn trả lại vào ví khả dụng của người dùng.
        </p>

        <form @submit.prevent="submitReject" class="space-y-3">
          <div class="space-y-1">
            <label class="text-xs font-semibold text-slate-300">Lý do từ chối <span class="text-rose-400">*</span></label>
            <textarea
              v-model="rejectForm.reason"
              rows="3"
              required
              placeholder="VD: Sai số tài khoản hoặc tên chủ thẻ không trùng khớp..."
              class="w-full px-3.5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white text-xs focus:outline-none focus:border-rose-500"
            ></textarea>
            <div v-if="rejectForm.errors.reason" class="text-rose-400 text-[11px]">
              {{ rejectForm.errors.reason }}
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-2">
            <button
              type="button"
              class="px-4 py-2 rounded-xl bg-white/5 text-slate-300 text-xs"
              @click="showRejectModal = false"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-white text-xs font-bold disabled:opacity-50"
              :disabled="rejectForm.processing"
            >
              {{ rejectForm.processing ? 'Đang xử lý...' : 'Xác Nhận Từ Chối' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
