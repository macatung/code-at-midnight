<script setup lang="ts">
import SeoHead from '@/Components/common/SeoHead.vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import MacatungMascot from '@/Components/mascot/MacatungMascot.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';
import { useTimeCycle } from '@/composables/useTimeCycle';

defineProps<{ title?: string; settings?: Record<string, string> }>();
const { t } = useI18n();
const { activePhase, formattedTime } = useTimeCycle();

const homeJsonLd = {
  '@context': 'https://schema.org',
  '@type': 'SoftwareApplication',
  name: 'Task Companion',
  applicationCategory: 'ProductivityApplication',
  operatingSystem: 'Windows 10, Windows 11',
  url: 'https://macatung.dev',
};
</script>

<template>
  <SeoHead
    :title="title || t('home.productTitle')"
    :description="t('home.productDescription')"
    keywords="Task Companion, Task Hub, Codex, Claude Code, AI agent workflow, Windows"
    canonical="https://macatung.dev"
    :json-ld="homeJsonLd"
  />

  <div class="min-h-screen bg-midnight-950 text-slate-100 selection:bg-phantom-mint selection:text-midnight-950 flex flex-col overflow-x-hidden bg-grid-pattern">
    <TalismanCanvas />
    <Navbar />

    <main class="relative z-10 flex-1">
      <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-16 sm:pt-24 sm:pb-24">
        <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_.9fr] lg:gap-16">
          <div class="max-w-3xl">
          <span class="inline-flex items-center gap-2 rounded-full border border-phantom-mint/30 bg-phantom-mint/10 px-3 py-1 text-xs font-mono text-phantom-mint">{{ t('home.productBadge') }}</span>
          <h1 class="mt-6 text-4xl sm:text-6xl font-display font-black tracking-tight text-white leading-tight">{{ t('home.productHeadline') }}</h1>
          <p class="mt-6 max-w-2xl text-base sm:text-lg leading-8 text-slate-300">{{ t('home.productDescription') }}</p>
          <div class="mt-9 flex flex-wrap gap-3">
            <Link href="/desktop" class="inline-flex items-center gap-2 rounded-xl bg-phantom-mint px-5 py-3 font-bold text-midnight-950 shadow-glow-mint transition hover:-translate-y-0.5">{{ t('home.viewFeatures') }} →</Link>
            <a href="https://github.com/macatung/code-at-midnight/releases/latest/download/Task-Companion-Setup.exe" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-5 py-3 font-bold text-white transition hover:border-phantom-mint/50 hover:bg-white/10">{{ t('home.downloadApp') }}</a>
          </div>
          </div>

          <aside class="relative mx-auto flex w-full max-w-md flex-col items-center" :style="{ '--mascot-accent': activePhase.accentHex }" :aria-label="t('home.mascotLabel')">
            <div class="mb-4 flex w-full items-center justify-between text-xs font-mono text-slate-400">
              <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full animate-pulse" :style="{ backgroundColor: activePhase.accentHex }" /> {{ activePhase.name }}</span>
              <span>{{ formattedTime }}</span>
            </div>
            <MacatungMascot size="hero" />
          </aside>
        </div>
      </section>

      <section class="border-y border-white/5 bg-black/20">
        <div class="max-w-7xl mx-auto grid gap-6 px-4 py-14 sm:grid-cols-3 sm:px-6 lg:px-8">
          <article v-for="item in ['home.benefitOne', 'home.benefitTwo', 'home.benefitThree']" :key="item" class="rounded-2xl border border-white/10 bg-midnight-900/70 p-6">
            <h2 class="font-display text-lg font-bold text-white">{{ t(`${item}Title`) }}</h2>
            <p class="mt-3 text-sm leading-6 text-slate-400">{{ t(`${item}Description`) }}</p>
          </article>
        </div>
      </section>

      <section class="max-w-5xl mx-auto px-4 py-20 sm:px-6 lg:px-8">
        <p class="font-mono text-xs uppercase tracking-[.25em] text-phantom-mint">{{ t('home.workflowLabel') }}</p>
        <h2 class="mt-3 text-3xl font-display font-bold text-white">{{ t('home.workflowTitle') }}</h2>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
          <div v-for="(step, index) in [t('home.workflowOne'), t('home.workflowTwo'), t('home.workflowThree')]" :key="step" class="rounded-2xl border border-white/10 p-5">
            <span class="font-mono text-phantom-mint">0{{ index + 1 }}</span>
            <p class="mt-4 text-sm leading-6 text-slate-300">{{ step }}</p>
          </div>
        </div>
        <div class="mt-12 rounded-3xl border border-phantom-mint/20 bg-phantom-mint/5 p-8 sm:flex sm:items-center sm:justify-between">
          <div>
            <h2 class="font-display text-2xl font-bold text-white">{{ t('home.readyTitle') }}</h2>
            <p class="mt-2 text-sm text-slate-400">{{ t('home.readyDescription') }}</p>
          </div>
          <a href="https://github.com/macatung/code-at-midnight/releases/latest/download/Task-Companion-Setup.exe" class="mt-5 inline-flex rounded-xl bg-phantom-mint px-5 py-3 font-bold text-midnight-950 sm:mt-0">{{ t('home.downloadApp') }}</a>
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>
