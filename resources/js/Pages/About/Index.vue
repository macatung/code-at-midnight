<script setup lang="ts">
import SeoHead from '@/Components/common/SeoHead.vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import SkillsSection from '@/Components/skills/SkillsSection.vue';
import ExperienceSection from '@/Components/experience/ExperienceSection.vue';
import AboutSection from '@/Components/about/AboutSection.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

defineProps<{
  skills?: any[];
  experiences?: any[];
  stats?: Record<string, any>;
  settings?: Record<string, string>;
}>();
const { t } = useI18n();

const aboutJsonLd = {
  '@context': 'https://schema.org',
  '@type': 'AboutPage',
  'name': 'MacaTung Studio — Triết Lý Kiến Trúc & Sứ Mệnh Hệ Sinh Thái',
  'description': 'Tìm hiểu về triết lý kỹ nghệ, sứ mệnh kiến tạo 5 trụ cột sản phẩm kỹ thuật số và năng lực hệ thống của MacaTung.',
  'url': 'https://macatung.dev/about',
  'mainEntity': {
    '@type': 'Organization',
    'name': 'MacaTung',
    'url': 'https://macatung.dev',
    'logo': 'https://macatung.dev/brand/macatung-logo-horizontal.png'
  }
};
</script>

<template>
  <SeoHead
    title="Triết Lý &amp; Sứ Mệnh Hệ Sinh Thái | MacaTung Studio"
    description="Tìm hiểu về triết lý kỹ nghệ, sứ mệnh kiến tạo 5 trụ cột sản phẩm kỹ thuật số và năng lực hệ thống của MacaTung."
    keywords="MacaTung, Triết lý kỹ nghệ, Hệ sinh thái phần mềm, Theravada, Decode, Task Companion, Kiến trúc hệ thống"
    canonical="https://macatung.dev/about"
    :json-ld="aboutJsonLd"
  />

  <div class="min-h-screen bg-midnight-950 text-slate-100 selection:bg-phantom-mint selection:text-midnight-950 flex flex-col justify-between relative overflow-x-hidden w-full bg-grid-pattern">
    <TalismanCanvas />
    <Navbar />

    <main class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-left">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs font-mono text-slate-400 mb-6" aria-label="Breadcrumb">
        <Link href="/" class="hover:text-phantom-mint transition-colors">{{ t('nav.home') }}</Link>
        <span>/</span>
        <span class="text-phantom-mint font-bold">{{ t('about.breadcrumb') }}</span>
      </nav>

      <!-- Studio Architecture & Ecosystem Overview -->
      <AboutSection :stats="stats" />

      <ExperienceSection :experiences="experiences" />
      <SkillsSection :skills="skills" />
    </main>

    <Footer />
  </div>
</template>
