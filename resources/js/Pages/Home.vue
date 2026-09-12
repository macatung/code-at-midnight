<script setup lang="ts">
import { computed } from 'vue';
import SeoHead from '@/Components/common/SeoHead.vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import MinimalHero from '@/Components/ecosystem/MinimalHero.vue';
import CleanProductGrid from '@/Components/ecosystem/CleanProductGrid.vue';
import EcosystemPhilosophy from '@/Components/ecosystem/EcosystemPhilosophy.vue';
import { useI18n } from '@/composables/useI18n';

defineProps<{
  title?: string;
  settings?: Record<string, string>;
}>();

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

const seoTitle = computed(() => {
  return isVi.value
    ? 'MacaTung — Hệ Sinh Thái Phần Mềm & Nền Tảng Kỹ Thuật Số'
    : 'MacaTung — Digital Ecosystem & Software Suite';
});

const seoDescription = computed(() => {
  return isVi.value
    ? 'Cổng trung tâm hệ sinh thái công nghệ MacaTung: Ứng dụng desktop Task Companion cho Windows, series khảo cứu Decode CAD, cổng hoàn tiền Shopee 80%, nền tảng Phật giáo Theravāda và phòng thí nghiệm tương tác.'
    : 'The central hub of the MacaTung software ecosystem: Task Companion Windows desktop app, Decode CAD systems analysis, 80% Shopee cashback utility, Theravāda Buddhist digital platform, and interactive labs.';
});

const ecosystemJsonLd = {
  '@context': 'https://schema.org',
  '@graph': [
    {
      '@type': 'Organization',
      '@id': 'https://macatung.dev/#organization',
      name: 'MacaTung Ecosystem',
      url: 'https://macatung.dev',
      logo: 'https://macatung.dev/brand/macatung-logo-horizontal.png',
      description: 'Software suite and digital ecosystem: AI agent orchestration, systemic technical analysis, Shopee cashback portal, Theravāda Buddhist studies, and creative developer utilities.',
      sameAs: [
        'https://github.com/macatung',
        'https://theravada.macatung.dev',
        'https://decode.macatung.dev',
        'https://hoantien.macatung.dev',
      ],
    },
    {
      '@type': 'WebSite',
      '@id': 'https://macatung.dev/#website',
      url: 'https://macatung.dev',
      name: 'MacaTung — Digital Ecosystem & Product Hub',
      publisher: { '@id': 'https://macatung.dev/#organization' },
      inLanguage: ['vi', 'en'],
    },
    {
      '@type': 'SoftwareApplication',
      '@id': 'https://macatung.dev/#taskcompanion',
      name: 'Task Companion',
      applicationCategory: 'DeveloperApplication',
      operatingSystem: 'Windows 10, Windows 11',
      url: 'https://macatung.dev/desktop',
      downloadUrl: 'https://github.com/macatung/code-at-midnight/releases/latest/download/Task-Companion-Setup.exe',
      offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD' },
    },
  ],
};
</script>

<template>
  <SeoHead
    :title="seoTitle"
    :description="seoDescription"
    keywords="MacaTung, MacaTung Ecosystem, Task Companion, Decode, Hoàn Tiền Shopee, Theravada, Pali, AI Agent, Windows Desktop, Talisman Forge, Rune Typer"
    canonical="https://macatung.dev"
    :json-ld="ecosystemJsonLd"
  />

  <div class="min-h-screen bg-midnight-950 text-slate-100 selection:bg-phantom-mint selection:text-midnight-950 flex flex-col overflow-x-hidden relative">
    <!-- Global Navigation -->
    <Navbar />

    <!-- Main Content Area -->
    <main class="relative z-10 flex-1 flex flex-col">
      <!-- 1. Centered Minimalist Hero -->
      <MinimalHero />

      <!-- 2. Clean 5-Pillar Product Showcase Grid -->
      <CleanProductGrid />

      <!-- 3. Engineering Philosophy & Standards -->
      <EcosystemPhilosophy />
    </main>

    <!-- Global Footer -->
    <Footer />
  </div>
</template>
