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
  '@type': 'ProfilePage',
  'name': 'Hồ Sơ Năng Lực & Kỹ Năng Kỹ Thuật (Ma Cà Tưng)',
  'description': 'Hồ sơ chuyên môn, kinh nghiệm thực chiến và bộ kỹ năng công nghệ Full-Stack & Creative Engineering của Ma Cà Tưng.',
  'url': 'https://macatung.dev/about',
  'mainEntity': {
    '@type': 'Person',
    'name': 'Ma Cà Tưng',
    'jobTitle': 'Senior Full-Stack Engineer',
    'url': 'https://macatung.dev'
  }
};
</script>

<template>
  <SeoHead
    :title="t('page.aboutTitle')"
    :description="t('page.aboutDescription')"
    keywords="Kỹ năng Lập trình, Hồ sơ Lập trình viên, Full-Stack Developer Profile, Laravel, Vue.js, System Architecture"
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

      <!-- Professional profile -->
      <AboutSection :stats="stats" />

      <ExperienceSection :experiences="experiences" />
      <SkillsSection :skills="skills" />
    </main>

    <Footer />
  </div>
</template>
