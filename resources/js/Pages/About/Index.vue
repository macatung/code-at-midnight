<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import SkillsSection from '@/Components/skills/SkillsSection.vue';
import ExperienceSection from '@/Components/experience/ExperienceSection.vue';
import AboutSection from '@/Components/about/AboutSection.vue';
import MidnightTerminal from '@/Components/terminal/MidnightTerminal.vue';
import NextStepsHub from '@/Components/layout/NextStepsHub.vue';

defineProps<{
  skills?: any[];
  experiences?: any[];
  stats?: Record<string, any>;
  settings?: Record<string, string>;
}>();
</script>

<template>
  <Head title="Kỹ Năng & Hồ Sơ Chuyên Môn — The Midnight Architect" />

  <div class="min-h-screen bg-midnight-950 text-slate-100 selection:bg-phantom-mint selection:text-midnight-950 flex flex-col justify-between relative overflow-x-hidden w-full bg-grid-pattern">
    <TalismanCanvas />
    <Navbar />

    <main class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-left">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-xs font-mono text-slate-400 mb-6" aria-label="Breadcrumb">
        <Link href="/" class="hover:text-phantom-mint transition-colors">Trang Chủ</Link>
        <span>/</span>
        <span class="text-phantom-mint font-bold">Kỹ Năng & Hồ Sơ (Profile)</span>
      </nav>

      <!-- 1. Skills & Tech Rune Arsenal -->
      <SkillsSection :skills="skills" />

      <!-- 2. Experience & Chronicles -->
      <ExperienceSection :experiences="experiences" />

      <!-- 3. Developer Manifesto & Stats -->
      <AboutSection :stats="stats" />

      <!-- 4. Interactive Terminal CLI Section -->
      <section id="terminal" class="scroll-mt-24 w-full py-16 sm:py-20 text-left">
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

      <!-- Next Steps Continuation Hub -->
      <NextStepsHub current-path="/about" />
    </main>

    <Footer />
  </div>
</template>
