<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { sound } from '@/audio/soundEffects';
import { useI18n } from '@/composables/useI18n';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

interface ProductItem {
  id: string;
  badge: { vi: string; en: string };
  badgeColor: string;
  title: { vi: string; en: string };
  description: { vi: string; en: string };
  tags: string[];
  href: string;
  isExternal?: boolean;
  cta: { vi: string; en: string };
  icon: string;
  glowColor: string;
  borderColor: string;
}

const products = computed<ProductItem[]>(() => [
  {
    id: 'taskcompanion',
    badge: { vi: 'PHẦN MỀM WINDOWS', en: 'WINDOWS APP' },
    badgeColor: 'bg-phantom-mint/15 text-phantom-mint border-phantom-mint/30',
    title: { vi: 'Task Companion cho Windows', en: 'Task Companion for Windows' },
    description: {
      vi: 'Ứng dụng desktop đồng hành quản lý tác vụ và điều phối coding agent (Codex, Antigravity, Claude Code). Giữ ngữ cảnh tập trung từ ý tưởng đến review.',
      en: 'Native Windows desktop companion connecting Task Hub with coding agents. Keep full workspace context from task selection to review.',
    },
    tags: ['Windows 10/11', 'AI Agent Orchestration', 'Context Packager'],
    href: '/desktop',
    cta: { vi: 'Xem Chi Tiết & Tải Về', en: 'View App & Download' },
    icon: '💻',
    glowColor: 'hover:shadow-[0_0_30px_rgba(0,245,160,0.15)]',
    borderColor: 'group-hover:border-phantom-mint/50',
  },
  {
    id: 'theravada',
    badge: { vi: 'PHẬT HỌC & THIỀN QUÁN', en: 'THERAVĀDA BUDDHISM' },
    badgeColor: 'bg-amber-500/15 text-amber-300 border-amber-500/30',
    title: { vi: 'Nền Tảng Phật Giáo Theravāda', en: 'Theravāda Buddhist Platform' },
    description: {
      vi: 'Không gian học Phật và thiền tập: 50+ bản kinh điển Pāḷi, lộ trình tự học tiếng Pāḷi sơ cấp, từ điển thuật ngữ chuyên sâu và chuông chánh niệm.',
      en: 'A sanctuary for Dhamma study: 50+ canonical Pāḷi suttas, foundational Pāḷi grammar lessons, glossary lexicon, and mindfulness audio.',
    },
    tags: ['Kinh Tạng Pāḷi', 'Học Tiếng Pāḷi', 'Chuông 432Hz'],
    href: '/theravada',
    cta: { vi: 'Bước Vào Cõi Thiền', en: 'Explore Dhamma' },
    icon: '🪷',
    glowColor: 'hover:shadow-[0_0_30px_rgba(255,209,102,0.15)]',
    borderColor: 'group-hover:border-amber-400/50',
  },
  {
    id: 'decode',
    badge: { vi: 'PHÂN TÍCH HỆ THỐNG', en: 'SYSTEMS ANALYSIS' },
    badgeColor: 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30',
    title: { vi: 'Ma Giải Mã (Decode CAD)', en: 'Decode CAD Media Series' },
    description: {
      vi: 'Chuỗi bài viết và podcast giải mã kiến trúc công nghệ, hệ thống phân tán, kinh nghiệm tối ưu hóa hiệu năng và chiến lược sản phẩm.',
      en: 'Technical analysis and podcast series decoding distributed software architectures, performance tuning, and software craft.',
    },
    tags: ['Systems Design', 'High Concurrency', 'Tech Podcast'],
    href: '/decode',
    cta: { vi: 'Đọc & Nghe Khảo Cứu', en: 'Read & Listen' },
    icon: '🎙️',
    glowColor: 'hover:shadow-[0_0_30px_rgba(6,182,212,0.15)]',
    borderColor: 'group-hover:border-cyan-400/50',
  },
  {
    id: 'cashback',
    badge: { vi: 'TIỆN ÍCH TIẾT KIỆM', en: 'SHOPPING UTILITY' },
    badgeColor: 'bg-orange-500/15 text-orange-300 border-orange-500/30',
    title: { vi: 'Cổng Hoàn Tiền Shopee', en: 'Shopee Cashback Utility' },
    description: {
      vi: 'Công cụ chuyển đổi liên kết mua sắm và hoàn tiền 80% hoa hồng tiếp thị liên kết trực tiếp vào tài khoản ngân hàng với đối soát minh bạch.',
      en: 'Smart affiliate shopping link generator providing up to 80% commission pass-through cashback with transparent automated payout.',
    },
    tags: ['80% Pass-Through', 'Rút Tiền VietQR', 'Minh Bạch 100%'],
    href: '/hoantien',
    cta: { vi: 'Tạo Link Hoàn Tiền', en: 'Get Cashback Link' },
    icon: '🛍️',
    glowColor: 'hover:shadow-[0_0_30px_rgba(255,87,34,0.15)]',
    borderColor: 'group-hover:border-orange-400/50',
  },
  {
    id: 'tools',
    badge: { vi: 'TIỆN ÍCH TƯƠNG TÁC', en: 'INTERACTIVE LAB' },
    badgeColor: 'bg-purple-500/15 text-purple-300 border-purple-500/30',
    title: { vi: 'Phòng Thí Nghiệm & Dev Game', en: 'Creative Lab & Games' },
    description: {
      vi: 'Bộ sưu tập công cụ sáng tạo: Lò thỉnh bùa lập trình 0-bug (Talisman Forge), phòng máy gõ code Rune Typer và Midnight Terminal giả lập.',
      en: 'Creative developer playground: Talisman Forge zero-bug charm generator, Rune Typer typing game, and interactive Midnight CLI.',
    },
    tags: ['Lò Thỉnh Bùa', 'Rune Typer Game', 'Midnight Terminal'],
    href: '/tools',
    cta: { vi: 'Trải Nghiệm Ngay', en: 'Launch Tools' },
    icon: '🎮',
    glowColor: 'hover:shadow-[0_0_30px_rgba(168,85,247,0.15)]',
    borderColor: 'group-hover:border-purple-400/50',
  },
]);
</script>

<template>
  <section id="products-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
    <!-- Section Header -->
    <div class="max-w-3xl mx-auto text-center mb-16">
      <h2 class="text-xs font-mono uppercase tracking-[0.25em] text-phantom-mint mb-3">
        {{ isVi ? 'Danh Mục Sản Phẩm & Nền Tảng' : 'Product & Platform Directory' }}
      </h2>
      <p class="text-2xl sm:text-4xl font-display font-bold text-white tracking-tight">
        {{ isVi ? '5 Trụ Cột Nền Tảng Đang Vận Hành' : '5 Core Operational Platforms' }}
      </p>
      <p class="mt-4 text-sm sm:text-base text-slate-400 leading-relaxed">
        {{ isVi 
          ? 'Mỗi nền tảng được xây dựng độc lập với công năng chuyên biệt, giải quyết trọn vẹn từng bài toán cụ thể.' 
          : 'Each platform is purposefully architected to solve specific real-world problems with dedicated tools.' 
        }}
      </p>
    </div>

    <!-- Clean Grid: 2 columns top (Desktop + Theravada), 3 columns bottom (Decode + Cashback + Tools) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
      <article
        v-for="(item, index) in products"
        :key="item.id"
        class="group relative rounded-3xl border border-white/10 bg-midnight-900/60 p-7 sm:p-8 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 backdrop-blur-sm"
        :class="[
          item.glowColor,
          item.borderColor,
          index === 0 ? 'md:col-span-2 lg:col-span-2 bg-gradient-to-br from-midnight-900/80 via-midnight-900/50 to-midnight-950/80 border-phantom-mint/20' : ''
        ]"
      >
        <div>
          <!-- Header with Icon & Category Badge -->
          <div class="flex items-center justify-between gap-4 mb-6">
            <span class="text-3xl sm:text-4xl select-none" role="img" :aria-label="item.title.vi">
              {{ item.icon }}
            </span>
            <span class="text-[10px] sm:text-xs font-mono font-bold px-3 py-1 rounded-full border" :class="item.badgeColor">
              {{ isVi ? item.badge.vi : item.badge.en }}
            </span>
          </div>

          <!-- Product Title -->
          <h3 class="text-xl sm:text-2xl font-display font-bold text-white group-hover:text-phantom-mint transition-colors duration-200">
            {{ isVi ? item.title.vi : item.title.en }}
          </h3>

          <!-- Description -->
          <p class="mt-3 text-sm text-slate-300 leading-relaxed">
            {{ isVi ? item.description.vi : item.description.en }}
          </p>

          <!-- Feature Tags -->
          <div class="mt-5 flex flex-wrap gap-2">
            <span
              v-for="tag in item.tags"
              :key="tag"
              class="text-[11px] font-mono px-2.5 py-0.5 rounded-md bg-white/5 text-slate-400 border border-white/5"
            >
              {{ tag }}
            </span>
          </div>
        </div>

        <!-- Action CTA Button -->
        <div class="mt-8 pt-6 border-t border-white/5 flex items-center justify-between">
          <Link
            :href="item.href"
            class="inline-flex items-center gap-2 text-sm font-bold text-white group-hover:text-phantom-mint transition-colors cursor-pointer"
            @click="sound.playClick()"
          >
            <span>{{ isVi ? item.cta.vi : item.cta.en }}</span>
            <span class="group-hover:translate-x-1 transition-transform">→</span>
          </Link>
          <span class="text-xs font-mono text-slate-600 group-hover:text-slate-400 transition-colors">
            macatung.dev{{ item.href }}
          </span>
        </div>
      </article>
    </div>
  </section>
</template>
