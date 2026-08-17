<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import HeroSection from '@/Components/hero/HeroSection.vue';
import AboutSection from '@/Components/about/AboutSection.vue';
import ProjectsSection from '@/Components/projects/ProjectsSection.vue';
import SkillsSection from '@/Components/skills/SkillsSection.vue';
import ExperienceSection from '@/Components/experience/ExperienceSection.vue';
import TalismanGenerator from '@/Components/talisman/TalismanGenerator.vue';
import MidnightTerminal from '@/Components/terminal/MidnightTerminal.vue';
import ContactSection from '@/Components/contact/ContactSection.vue';

defineProps<{
  title?: string;
  projects?: any[];
  skills?: any[];
  experiences?: any[];
  settings?: Record<string, string>;
  stats?: Record<string, any>;
}>();

const globalHopCount = ref(0);

const handleHop = (count: number) => {
  globalHopCount.value = count;
};
</script>

<template>
  <Head :title="title || 'Code at midnight — Ma Cà Tưng Portfolio'" />

  <div class="min-h-screen bg-midnight-950 text-slate-100 selection:bg-phantom-mint selection:text-midnight-950 flex flex-col justify-between relative overflow-x-hidden w-full bg-grid-pattern">
    <!-- Ambient 2D Canvas Particles Engine -->
    <TalismanCanvas />

    <!-- Ambient Background Glow Blurs -->
    <div class="fixed inset-0 pointer-events-none z-0">
      <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-phantom-mint/5 rounded-full blur-[140px]" />
      <div class="absolute top-2/3 right-10 w-[450px] h-[450px] bg-phantom-purple/10 rounded-full blur-[120px]" />
      <div class="absolute bottom-10 left-10 w-[400px] h-[400px] bg-talisman-gold/5 rounded-full blur-[100px]" />
    </div>

    <!-- Sticky Navigation Bar -->
    <Navbar />

    <!-- Master Content Sections -->
    <main class="relative z-10 flex-1 flex flex-col items-center w-full">
      <!-- 1. Hero Section (#hero) -->
      <HeroSection @hop="handleHop" />

      <!-- 2. Grimoire Projects Showcase (#projects) -->
      <ProjectsSection :projects="projects" />

      <!-- 3. Skills & Tech Rune Arsenal (#skills) -->
      <SkillsSection :skills="skills" />

      <!-- 4. Experience & Chronicles (#experience) -->
      <ExperienceSection :experiences="experiences" />

      <!-- 5. Developer Manifesto & Stats (#about) -->
      <AboutSection :stats="stats" />

      <!-- 6. Developer Talisman Forge (#talisman) -->
      <section id="talisman" class="scroll-mt-24 w-full py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <TalismanGenerator />
      </section>

      <!-- 7. Midnight Terminal REPL CLI (#terminal) -->
      <section id="terminal" class="scroll-mt-24 w-full py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-left">
        <div class="flex flex-col items-start mb-8">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-phantom-mint/10 border border-phantom-mint/30 text-phantom-mint text-xs font-mono mb-3 shadow-glow-mint">
            ⚡ Interactive REPL Shell
          </span>
          <h2 class="text-3xl sm:text-5xl font-display font-extrabold text-white tracking-tight">
            Midnight <span class="text-transparent bg-clip-text bg-gradient-to-r from-phantom-mint via-phantom-cyan to-talisman-gold">Terminal CLI</span>
          </h2>
          <p class="text-sm sm:text-base text-slate-400 mt-2 max-w-2xl font-sans">
            Môi trường dòng lệnh ảo mô phỏng zsh shell với 11 câu lệnh ma thuật, lịch sử điều hướng phím mũi tên và âm thanh gõ phím cơ học.
          </p>
        </div>
        <MidnightTerminal />
      </section>

      <!-- 8. Summoning Altar Contact Form (#contact) -->
      <ContactSection />
    </main>

    <!-- Global Footer with Hop-to-Top and Easter Egg -->
    <Footer />
  </div>
</template>
