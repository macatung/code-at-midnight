export interface CashbackWalletData {
  id: number;
  sub_id: string;
  pending_balance: number;
  available_balance: number;
  withdrawn_balance: number;
  status: string;
}

export interface CashbackClickData {
  id: number;
  wallet_id: number;
  sub_id: string;
  original_url: string;
  affiliate_url: string;
  short_link: string | null;
  ip_address: string | null;
  created_at: string;
}

export interface CashbackOrderData {
  id: number;
  shopee_order_id: string;
  sub_id: string;
  product_name: string;
  product_image: string | null;
  gmv: number;
  commission_shopee: number;
  cashback_rate: number;
  cashback_amount: number;
  status: 'pending' | 'confirmed' | 'cancelled' | 'refunded' | 'paid';
  created_at: string;
  order_time: string | null;
}

export interface CashbackWithdrawalData {
  id: number;
  amount: number;
  bank_name: string;
  bank_account_number: string;
  bank_account_name: string;
  status: 'pending' | 'completed' | 'rejected';
  note: string | null;
  processed_at: string | null;
  created_at: string;
}

export interface CashbackStatsData {
  total_orders: number;
  pending_orders: number;
  confirmed_orders: number;
  cancelled_orders: number;
  cashback_rate_percent: number;
  min_withdrawal: number;
}

export interface GenerateLinkResponse {
  success: boolean;
  short_link?: string;
  sub_id?: string;
  original_url?: string;
  click_id?: number;
  message?: string;
}
