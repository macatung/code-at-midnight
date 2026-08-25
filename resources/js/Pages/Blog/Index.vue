<script setup lang="ts">
import SeoHead from '@/Components/common/SeoHead.vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

interface ArticleItem { id: number; title: string; slug: string; excerpt: string; reading_time_min: number; published_at: string; }
defineProps<{ articles: ArticleItem[]; settings?: Record<string, string> }>();
const { t } = useI18n();

const blogIndexJsonLd = { '@context': 'https://schema.org', '@type': 'Blog', name: 'Macatung Technical Notes', url: 'https://macatung.dev/blog' };
</script>

<template>
  <SeoHead :title="t('blog.title')" :description="t('blog.description')" keywords="software architecture, AI systems, technical notes" canonical="https://macatung.dev/blog" :json-ld="blogIndexJsonLd" />
  <div class="min-h-screen bg-midnight-950 text-slate-100 flex flex-col overflow-x-hidden bg-grid-pattern">
    <TalismanCanvas /><Navbar />
    <main class="relative z-10 flex-1 w-full max-w-4xl mx-auto px-4 py-12 sm:px-6 sm:py-16">
      <nav class="mb-8 flex items-center gap-2 text-xs font-mono text-slate-400" aria-label="Breadcrumb"><Link href="/" class="hover:text-phantom-mint">{{ t('nav.home') }}</Link><span>/</span><span class="text-phantom-mint">{{ t('blog.title') }}</span></nav>
      <header class="mb-12 border-b border-white/10 pb-8"><h1 class="text-3xl font-display font-extrabold tracking-tight text-white sm:text-5xl">{{ t('blog.title') }}</h1><p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-400 sm:text-base">{{ t('blog.description') }}</p></header>
      <div v-if="articles.length === 0" class="rounded-2xl border border-white/10 bg-midnight-900/70 p-12 text-center text-slate-400"><h2 class="font-display text-xl font-bold text-white">{{ t('blog.empty') }}</h2></div>
      <div v-else class="divide-y divide-white/10">
        <article v-for="article in articles" :key="article.id" class="py-7 first:pt-0">
          <div class="flex flex-wrap items-center gap-3 text-xs font-mono text-slate-400"><span>{{ article.published_at }}</span><span>·</span><span class="text-phantom-mint">{{ article.reading_time_min }} {{ t('common.minutes') }}</span></div>
          <h2 class="mt-3 text-xl font-display font-bold text-white transition-colors hover:text-phantom-mint sm:text-2xl"><Link :href="`/blog/${article.slug}`">{{ article.title }}</Link></h2>
          <p class="mt-3 text-sm leading-relaxed text-slate-400">{{ article.excerpt }}</p>
          <Link :href="`/blog/${article.slug}`" class="mt-4 inline-flex text-sm font-semibold text-phantom-mint hover:text-white">{{ t('blog.read') }} →</Link>
        </article>
      </div>
    </main>
    <Footer />
  </div>
</template>
