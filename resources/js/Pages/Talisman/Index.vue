<script setup lang="ts">
import SeoHead from '@/Components/common/SeoHead.vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import TalismanGenerator from '@/Components/talisman/TalismanGenerator.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps<{
  settings?: Record<string, string>;
}>();
const { t } = useI18n();

const talismanJsonLd = {
  '@context': 'https://schema.org',
  '@type': 'WebApplication',
  'name': 'Lò Rèn Bùa Hộ Mệnh Dev (Talisman Forge)',
  'description': 'Ứng dụng tạo và tùy biến bùa hộ mệnh phong cách Đạo gia kết hợp thuật toán công nghệ bảo trợ deploy không lỗi.',
  'applicationCategory': 'UtilitiesApplication',
  'operatingSystem': 'Any',
  'url': 'https://macatung.dev/talisman',
  'author': {
    '@type': 'Person',
    'name': 'Ma Cà Tưng',
    'url': 'https://macatung.dev'
  }
};
</script>

<template>
  <SeoHead
    title="Developer Talisman Forge — The Midnight Architect"
    description="Customize bug-banishing charms, protect midnight deployments and export high-quality artwork for your laptop or social posts."
    keywords="Bùa Lập Trình, Dev Talisman, Bùa Trừ Bug, No Bug Charm, Creative Tool, Canvas Exporter"
    canonical="https://macatung.dev/talisman"
    :json-ld="talismanJsonLd"
  />

  <div class="min-h-screen bg-midnight-950 text-slate-100 selection:bg-phantom-mint selection:text-midnight-950 flex flex-col justify-between relative overflow-x-hidden w-full bg-grid-pattern">
    <TalismanCanvas />
    <Navbar />

    <main class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-left">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs font-mono text-slate-400 mb-6" aria-label="Breadcrumb">
        <Link href="/" class="hover:text-phantom-mint transition-colors">{{ t('nav.home') }}</Link>
        <span>/</span>
        <span class="text-talisman-gold font-bold">{{ t('talisman.breadcrumb') }}</span>
      </nav>

      <div class="flex flex-col items-start mb-8">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-talisman-gold/10 border border-talisman-gold/30 text-talisman-gold text-xs font-mono mb-3 shadow-glow-talisman">
          📜 {{ t('talisman.badge') }}
        </span>
        <h1 class="text-3xl sm:text-5xl font-display font-extrabold text-white tracking-tight">
          {{ t('talisman.title') }}
        </h1>
        <p class="text-sm sm:text-base text-slate-400 mt-2 max-w-2xl font-sans">
          {{ t('talisman.description') }}
        </p>
      </div>

      <!-- Talisman Forge Tool -->
      <TalismanGenerator />

    </main>

    <Footer />
  </div>
</template>
