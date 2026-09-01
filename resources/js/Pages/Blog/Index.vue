<script setup lang="ts">
import SeoHead from '@/Components/common/SeoHead.vue';
import Navbar from '@/Components/layout/Navbar.vue';
import Footer from '@/Components/layout/Footer.vue';
import TalismanCanvas from '@/Components/mascot/TalismanCanvas.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/composables/useI18n';

interface ArticleItem {
  id: number;
  title: string;
  title_en?: string | null;
  slug: string;
  excerpt: string;
  excerpt_en?: string | null;
  reading_time_min: number;
  published_at: string;
}

defineProps<{
  articles: ArticleItem[];
  settings?: Record<string, string>;
}>();

const { locale, t, setLocale } = useI18n();

const blogIndexJsonLd = {
  '@context': 'https://schema.org',
  '@type': 'Blog',
  name: 'Macatung Technical Notes',
  url: 'https://macatung.dev/blog',
};

const getArticleTitle = (article: ArticleItem) => {
  if (locale.value === 'en' && article.title_en) {
    return article.title_en;
  }
  return article.title;
};

const getArticleExcerpt = (article: ArticleItem) => {
  if (locale.value === 'en' && article.excerpt_en) {
    return article.excerpt_en;
  }
  return article.excerpt;
};
</script>

<template>
  <SeoHead
    :title="t('blog.title')"
    :description="t('blog.description')"
    keywords="software architecture, AI systems, technical notes, Multi-Agent, MCP"
    canonical="https://macatung.dev/blog"
    :json-ld="blogIndexJsonLd"
  />

  <div class="min-h-screen bg-midnight-950 text-slate-100 flex flex-col overflow-x-hidden bg-grid-pattern">
    <TalismanCanvas />
    <Navbar />

    <main class="relative z-10 flex-1 w-full max-w-4xl mx-auto px-4 py-12 sm:px-6 sm:py-16">
      <div class="flex items-center justify-between gap-4 mb-8">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-400" aria-label="Breadcrumb">
          <Link href="/" class="hover:text-phantom-mint">{{ t('nav.home') }}</Link>
          <span>/</span>
          <span class="text-phantom-mint">{{ t('blog.title') }}</span>
        </nav>

        <!-- Quick Language Switcher Pill -->
        <div class="flex items-center rounded-xl border border-white/10 bg-midnight-900/90 p-1 text-xs font-mono" role="group" :aria-label="t('common.language')">
          <button
            type="button"
            class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg transition-all cursor-pointer"
            :class="locale === 'vi' ? 'bg-phantom-mint text-midnight-950 font-bold shadow-glow-mint' : 'text-slate-400 hover:text-white'"
            @click="setLocale('vi')"
          >
            <span>🇻🇳</span>
            <span>VI</span>
          </button>
          <button
            type="button"
            class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg transition-all cursor-pointer"
            :class="locale === 'en' ? 'bg-phantom-mint text-midnight-950 font-bold shadow-glow-mint' : 'text-slate-400 hover:text-white'"
            @click="setLocale('en')"
          >
            <span>🇬🇧</span>
            <span>EN</span>
          </button>
        </div>
      </div>

      <header class="mb-12 border-b border-white/10 pb-8">
        <div class="inline-flex items-center gap-2 rounded-full border border-phantom-mint/30 bg-phantom-mint/10 px-3 py-1 text-xs font-mono text-phantom-mint mb-4">
          <span>AI AGENTS & ARCHITECTURE</span>
        </div>
        <h1 class="text-3xl font-display font-extrabold tracking-tight text-white sm:text-5xl">
          {{ t('blog.title') }}
        </h1>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-400 sm:text-base">
          {{ t('blog.description') }}
        </p>
      </header>

      <div v-if="articles.length === 0" class="rounded-2xl border border-white/10 bg-midnight-900/70 p-12 text-center text-slate-400">
        <h2 class="font-display text-xl font-bold text-white">{{ t('blog.empty') }}</h2>
        <p class="mt-2 text-sm">{{ t('blog.emptyHint') }}</p>
      </div>

      <div v-else class="space-y-6">
        <article
          v-for="article in articles"
          :key="article.id"
          class="group relative rounded-2xl border border-white/10 bg-midnight-900/60 p-6 sm:p-8 backdrop-blur-sm transition-all duration-300 hover:border-phantom-mint/40 hover:bg-midnight-900/90 hover:shadow-xl hover:shadow-phantom-mint/5"
        >
          <div class="flex flex-wrap items-center gap-3 text-xs font-mono text-slate-400 mb-3">
            <span class="text-slate-300">{{ article.published_at }}</span>
            <span>·</span>
            <span class="text-phantom-mint font-semibold">{{ article.reading_time_min }} {{ t('common.minutes') }}</span>
          </div>

          <h2 class="text-xl sm:text-2xl font-display font-bold text-white transition-colors group-hover:text-phantom-mint leading-snug">
            <Link :href="`/blog/${article.slug}`" class="focus:outline-none">
              {{ getArticleTitle(article) }}
            </Link>
          </h2>

          <p class="mt-3 text-sm sm:text-base leading-relaxed text-slate-300">
            {{ getArticleExcerpt(article) }}
          </p>

          <div class="mt-5 flex items-center justify-between pt-4 border-t border-white/5">
            <Link
              :href="`/blog/${article.slug}`"
              class="inline-flex items-center gap-2 text-sm font-semibold text-phantom-mint group-hover:translate-x-1 transition-transform"
            >
              <span>{{ t('blog.read') }}</span>
              <span>→</span>
            </Link>
          </div>
        </article>
      </div>
    </main>

    <Footer />
  </div>
</template>
