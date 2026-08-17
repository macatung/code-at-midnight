<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import ProjectsSection from '@/Components/projects/ProjectsSection.vue';
import NextStepsHub from '@/Components/layout/NextStepsHub.vue';

defineProps<{
  projects?: any[];
  settings?: Record<string, string>;
}>();
</script>

<template>
  <Head title="Kho Grimoire Dự Án Thực Chiến — The Midnight Architect" />

  <div class="min-h-screen bg-midnight-950 text-slate-100 selection:bg-phantom-mint selection:text-midnight-950 flex flex-col justify-between relative overflow-x-hidden w-full bg-grid-pattern">
    <TalismanCanvas />
    <Navbar />

    <main class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-left">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs font-mono text-slate-400 mb-6" aria-label="Breadcrumb">
        <Link href="/" class="hover:text-phantom-mint transition-colors">Trang Chủ</Link>
        <span>/</span>
        <span class="text-phantom-mint font-bold">Kho Dự Án (Grimoire)</span>
      </nav>

      <!-- Projects Section Component (Full mode with filter & search) -->
      <ProjectsSection :projects="projects" :featured-only="false" />

      <!-- Next Steps Continuation Hub -->
      <NextStepsHub current-path="/projects" />
    </main>

    <Footer />
  </div>
</template>
