/**
 * Test Suite: Theravāda Buddhist Canonical Content, Schema Conformance & Internal Linking Mesh
 * Tier 1: Feature Coverage (Isolation) — Schema fields, unique slugs, canonical completeness
 * Tier 2: Boundary & Corner Cases — Vietnamese word count (>1000w), Mermaid diagrams, internal links
 */

import { describe, it, expect } from '../Harness/index.js';
import { PALI_GLOSSARY, findPaliTermDefinition } from '../../resources/js/data/paliGlossary.ts';
import { DHAMMAPADA_VERSES } from '../../resources/js/data/dhammapadaCollection.ts';
import fs from 'fs';
import path from 'path';

export const EXPANSION_SLUGS = [
  'hai-muoi-bon-duyen-he-patthana-catu-visatipaccaya-vi-dieu-phap',
  'sac-phap-chan-de-rupa-paramattha-cau-truc-bon-sac-kalapa',
  'nam-muoi-hai-so-huu-tam-cetasika-quy-luat-phoi-hop-tam-thuc',
  'tien-trinh-can-tu-va-tai-sinh-cuti-patisandhi-vithi-31-coi',
  'duyen-khoi-lien-hoan-paticcasamuppada-12-chi-phan-va-3-luan-chuyen',
  'lich-su-phan-phai-phat-giao-so-khai-theravada-va-mahasanghika',
  'dai-truong-lao-xa-loi-phat-va-muc-kien-lien-hai-vi-thuong-thu-thinh-van',
  'ky-ket-tap-lan-thu-tu-aluvihara-khac-tam-tang-len-la-boi-tich-lan',
  'ky-ket-tap-lan-ba-va-chin-phai-doan-hoang-phap-thoi-vua-asoka',
  'truong-lao-mahinda-va-ni-truong-sanghamitta-khai-sang-phat-giao-tich-lan',
  'toan-thu-40-de-muc-thien-dinh-samatha-kammatthana-visuddhimagga',
  'lo-trinh-16-tang-tue-minh-sat-solasa-nana-va-that-thanh-tinh',
  'phuong-phap-quan-32-the-trong-cua-than-dvattimsakara-kayagatasati',
  'cam-nang-thuc-hanh-gioi-can-ban-va-bat-quan-trai-gioi-uposatha',
  'phuong-phap-quan-tu-dai-catudhatuvavatthana-12-dac-tinh-chan-de',
];

export function calculateVietnameseWordCount(rawContent: string): number {
  if (!rawContent) return 0;
  const noCode = rawContent.replace(/```[\s\S]*?```/g, '');
  const noHtml = noCode.replace(/<[^>]+>/g, ' ');
  const noMdLinks = noHtml.replace(/\[([^\]]+)\]\([^)]+\)/g, '$1');
  return noMdLinks.trim().split(/\s+/).filter(Boolean).length;
}

describe('TheravadaContentTest (Canonical Teachings, Schema & Expansion Integrity)', () => {
  // Read TheravadaContentSeeder.php
  const seederPath = path.resolve(process.cwd(), 'database/seeders/TheravadaContentSeeder.php');
  const seederContent = fs.readFileSync(seederPath, 'utf-8');

  // Split into article blocks
  const articleBlocks = seederContent.split(/(?=\[\s*['"]site_domain['"]\s*=>\s*['"]theravada['"])/);
  const rawArticles = articleBlocks.slice(1);

  // Extract all slugs
  const slugRegex = /'slug'\s*=>\s*'([^']+)'/g;
  const slugs: string[] = [];
  let match: RegExpExecArray | null;
  while ((match = slugRegex.exec(seederContent)) !== null) {
    slugs.push(match[1]);
  }

  // Extract categories
  const categoryRegex = /'category'\s*=>\s*'([^']+)'/g;
  const categories: string[] = [];
  while ((match = categoryRegex.exec(seederContent)) !== null) {
    categories.push(match[1]);
  }

  // ==========================================================================
  // TIER 1: Feature Coverage (Isolation)
  // ==========================================================================
  describe('[T1_THERAVADA] Canonical Content, Schema & Article Completeness', () => {
    it('[T1_TH_01] TheravadaContentSeeder contains at least 50 canonical articles with strictly unique slugs', () => {
      expect(slugs.length).toBeGreaterThanOrEqual(50);
      const uniqueSlugs = new Set(slugs);
      expect(uniqueSlugs.size).toBe(slugs.length);
    });

    it('[T1_TH_02] Articles span across all 4 key canonical categories (phap-hoc, phap-hanh, kinh-tung, lich-su)', () => {
      expect(categories.length).toBe(slugs.length);
      const uniqueCategories = new Set(categories);
      expect(uniqueCategories.has('phap-hoc')).toBe(true);
      expect(uniqueCategories.has('phap-hanh')).toBe(true);
      expect(uniqueCategories.has('kinh-tung')).toBe(true);
      expect(uniqueCategories.has('lich-su')).toBe(true);

      const phapHocCount = categories.filter((c) => c === 'phap-hoc').length;
      const phapHanhCount = categories.filter((c) => c === 'phap-hanh').length;
      const kinhTungCount = categories.filter((c) => c === 'kinh-tung').length;
      const lichSuCount = categories.filter((c) => c === 'lich-su').length;

      expect(phapHocCount).toBeGreaterThanOrEqual(18);
      expect(phapHanhCount).toBeGreaterThanOrEqual(7);
      expect(kinhTungCount).toBeGreaterThanOrEqual(9);
      expect(lichSuCount).toBeGreaterThanOrEqual(6);
    });

    it('[T1_TH_03] Every article in seeder satisfies mandatory schema field constraints', () => {
      expect(rawArticles.length).toBe(slugs.length);

      rawArticles.forEach((block, idx) => {
        // site_domain
        expect(block.includes("'site_domain' => 'theravada'") || block.includes('"site_domain" => "theravada"')).toBe(true);

        // title
        const hasTitle = /['"]title['"]\s*=>\s*['"][^'"]+['"]/.test(block);
        expect(hasTitle).toBe(true);

        // pali_title
        const hasPaliTitle = /['"]pali_title['"]\s*=>\s*['"][^'"]+['"]/.test(block);
        expect(hasPaliTitle).toBe(true);

        // slug format
        const slugMatch = block.match(/['"]slug['"]\s*=>\s*['"]([^'"]+)['"]/);
        expect(slugMatch).toBeDefined();
        if (slugMatch) {
          expect(/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(slugMatch[1])).toBe(true);
        }

        // category
        const catMatch = block.match(/['"]category['"]\s*=>\s*['"]([^'"]+)['"]/);
        expect(catMatch).toBeDefined();
        if (catMatch) {
          expect(['phap-hoc', 'phap-hanh', 'kinh-tung', 'lich-su'].includes(catMatch[1])).toBe(true);
        }

        // excerpt
        const hasExcerpt = /['"]excerpt['"]\s*=>\s*['"][\s\S]+?['"]/.test(block);
        expect(hasExcerpt).toBe(true);

        // author
        const hasAuthor = /['"]author['"]\s*=>\s*['"][^'"]+['"]/.test(block);
        expect(hasAuthor).toBe(true);

        // content
        const hasContent = /['"]content['"]\s*=>\s*(?:<<<[ ']*([A-Za-z0-9_]+)|['"])/.test(block);
        expect(hasContent).toBe(true);
      });
    });

    it('[T1_TH_04] Pali Glossary contains comprehensive term definitions', () => {
      expect(PALI_GLOSSARY.length).toBeGreaterThan(45);

      const keyTerms = [
        'Dukkha',
        'Anicca',
        'Anattā',
        'Ariya Aṭṭhaṅgika Magga',
        'Brahmavihāra',
        'Mettā',
        'Karuṇā',
        'Muditā',
        'Upekkhā',
        'Lokadhamma',
        'Ānāpānasati',
        'Nīvaraṇa',
        'Sampajañña',
        'Citta-vīthi',
        'Visuddhi',
        'Vipassanā-ñāṇa',
        'Nimitta',
        'Mahāmaṅgala',
        'Bhaddekaratta',
        'Alagaddūpama',
      ];

      keyTerms.forEach((term) => {
        const found = findPaliTermDefinition(term);
        expect(found).toBeDefined();
        expect(found?.definition.length).toBeGreaterThan(10);
      });
    });

    it('[T1_TH_05] Dhammapada collection contains inspiring daily verses with insight', () => {
      expect(DHAMMAPADA_VERSES.length).toBeGreaterThanOrEqual(10);
      DHAMMAPADA_VERSES.forEach((verse) => {
        expect(typeof verse.verse_number).toBe('number');
        expect(verse.pali.length).toBeGreaterThan(10);
        expect(verse.vietnamese.length).toBeGreaterThan(10);
        expect(verse.insight.length).toBeGreaterThan(10);
      });
    });
  });

  // ==========================================================================
  // TIER 2: Boundary & Corner Cases (Examples, Word Count & Internal Linking Mesh)
  // ==========================================================================
  describe('[T2_THERAVADA] Rich Examples, Word Count & Internal Linking Mesh Integrity', () => {
    it('[T2_TH_01] All canonical articles contain detailed canonical or practical examples', () => {
      expect(rawArticles.length).toBeGreaterThanOrEqual(50);

      rawArticles.forEach((block) => {
        const hasExample =
          block.includes('Ví Dụ') ||
          block.includes('ví dụ') ||
          block.includes('Ẩn Dụ') ||
          block.includes('ẩn dụ') ||
          block.includes('Ứng Dụng') ||
          block.includes('ứng dụng') ||
          block.includes('Tình huống') ||
          block.includes('Kỳ Kết Tập') ||
          block.includes('Lịch Sử') ||
          block.includes('Kinh ');

        expect(hasExample).toBe(true);
      });
    });

    it('[T2_TH_02] All canonical articles contain internal markdown links referencing other articles', () => {
      const internalLinkPattern = /\[([^\]]+)\]\(\/theravada\/kinh\/([a-z0-9-]+)\)/g;

      let totalInternalLinks = 0;
      const foundLinkSlugs = new Set<string>();

      rawArticles.forEach((block) => {
        const linksInBlock: string[] = [];
        let linkMatch: RegExpExecArray | null;
        const re = new RegExp(internalLinkPattern.source, 'g');
        while ((linkMatch = re.exec(block)) !== null) {
          linksInBlock.push(linkMatch[2]);
          foundLinkSlugs.add(linkMatch[2]);
          totalInternalLinks++;
        }

        // Each article must have at least 2 internal links
        expect(linksInBlock.length).toBeGreaterThanOrEqual(2);
      });

      // Total internal links across all articles should be high (> 120)
      expect(totalInternalLinks).toBeGreaterThanOrEqual(120);

      // Verify that every linked slug actually exists in the articles list
      const validSlugSet = new Set(slugs);
      foundLinkSlugs.forEach((targetSlug) => {
        expect(validSlugSet.has(targetSlug)).toBe(true);
      });
    });

    it('[T2_TH_03] Articles have substantial content length and expansion articles meet >1000 words requirement', () => {
      rawArticles.forEach((block) => {
        const slugMatch = block.match(/['"]slug['"]\s*=>\s*['"]([^'"]+)['"]/);
        const slug = slugMatch ? slugMatch[1] : '';

        // Extract content
        const heredocMatch = block.match(/['"]content['"]\s*=>\s*<<<[ ']*([A-Za-z0-9_]+)[' ]*\r?\n([\s\S]*?)\r?\n\s*\1/);
        const rawContent = heredocMatch ? heredocMatch[2] : block;
        const wordCount = calculateVietnameseWordCount(rawContent);

        // Baseline: all articles have >200 Vietnamese words
        expect(wordCount).toBeGreaterThan(200);

        // Strict: deep-dive expansion articles have >1000 Vietnamese words
        if (EXPANSION_SLUGS.includes(slug)) {
          expect(wordCount).toBeGreaterThan(1000);
        }
      });
    });

    it('[T2_TH_04] All canonical articles contain valid Mermaid flowchart or timeline diagrams', () => {
      rawArticles.forEach((block) => {
        expect(block.includes('```mermaid')).toBe(true);
      });
    });
  });

  // ==========================================================================
  // TIER 3: Mobile UI/UX, Smart Header & Contrast Integrity
  // ==========================================================================
  describe('[T3_THERAVADA] Mobile Zen Reader, Bottom Controls & Contrast Specs', () => {
    const showVuePath = path.resolve(process.cwd(), 'resources/js/Pages/Theravada/Show.vue');
    const showVueContent = fs.readFileSync(showVuePath, 'utf-8');

    const layoutVuePath = path.resolve(process.cwd(), 'resources/js/Layouts/TheravadaLayout.vue');
    const layoutVueContent = fs.readFileSync(layoutVuePath, 'utf-8');

    const i18nPath = path.resolve(process.cwd(), 'resources/js/composables/useI18n.ts');
    const i18nContent = fs.readFileSync(i18nPath, 'utf-8');

    it('[T3_TH_01] Show.vue implements localStorage keys for reader settings persistence', () => {
      expect(showVueContent.includes("const STORAGE_FONT_SIZE_KEY = 'zen_reader_font_size'")).toBe(true);
      expect(showVueContent.includes("const STORAGE_PAPER_MODE_KEY = 'zen_reader_paper_mode'")).toBe(true);
      expect(showVueContent.includes('localStorage.getItem(STORAGE_FONT_SIZE_KEY)')).toBe(true);
      expect(showVueContent.includes('localStorage.getItem(STORAGE_PAPER_MODE_KEY)')).toBe(true);
    });

    it('[T3_TH_02] Show.vue parses Table of Contents headings and injects anchor IDs', () => {
      expect(showVueContent.includes('const tocHeadings = computed')).toBe(true);
      expect(showVueContent.includes('const scrollToHeading =')).toBe(true);
      expect(showVueContent.includes('toc-sec-')).toBe(true);
    });

    it('[T3_TH_03] useI18n.ts provides mobile-specific glossary hints and TOC translations', () => {
      expect(i18nContent.includes("'theravada.glossaryHintMobile'")).toBe(true);
      expect(i18nContent.includes("'theravada.toc'")).toBe(true);
      expect(i18nContent.includes("'theravada.readingPreferences'")).toBe(true);
      expect(i18nContent.includes("'theravada.zenControls'")).toBe(true);
    });

    it('[T3_TH_04] TheravadaLayout.vue implements smart header auto-hide on mobile scroll', () => {
      expect(layoutVueContent.includes('const isHeaderHidden = ref(false)')).toBe(true);
      expect(layoutVueContent.includes("'-translate-y-full': isHeaderHidden")).toBe(true);
      expect(layoutVueContent.includes('window.innerWidth < 768')).toBe(true);
    });

    it('[T3_TH_05] Show.vue enforces high-contrast styles using .is-paper-mode and .is-night-mode classes', () => {
      expect(showVueContent.includes("is-paper-mode bg-stone-50/98 text-[#1c1917]")).toBe(true);
      expect(showVueContent.includes("is-night-mode bg-stone-900/90 text-stone-200")).toBe(true);
      expect(showVueContent.includes('.zen-article-content.is-paper-mode :deep(.zen-opening-quote)')).toBe(true);
      expect(showVueContent.includes('.zen-article-content.is-night-mode :deep(.zen-opening-quote)')).toBe(true);
      expect(showVueContent.includes('#451a03 !important; /* Deep dark warm brown')).toBe(true);
    });

    it('[T3_TH_06] Show.vue renders Mobile Zen Bottom Floating Bar and Pāḷi Bottom Sheet', () => {
      expect(showVueContent.includes('sm:hidden fixed bottom-0 left-0 right-0 z-40')).toBe(true);
      expect(showVueContent.includes('isZenSheetOpen && isMobileScreen')).toBe(true);
      expect(showVueContent.includes('zen-pali-mobile-sheet')).toBe(true);
      expect(showVueContent.includes('sm:hidden inline-flex items-center gap-1.5 text-amber-400')).toBe(true);
    });
  });
});
