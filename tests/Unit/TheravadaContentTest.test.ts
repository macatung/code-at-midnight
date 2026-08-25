/**
 * Test Suite: Theravada Buddhist Canonical Content, Examples & Internal Linking Mesh
 * Tier 1: Feature Coverage (Isolation)
 * Tier 2: Boundary & Corner Cases
 */

import { describe, it, expect } from '../Harness/index.js';
import { PALI_GLOSSARY, findPaliTermDefinition } from '../../resources/js/data/paliGlossary.ts';
import { DHAMMAPADA_VERSES } from '../../resources/js/data/dhammapadaCollection.ts';
import fs from 'fs';
import path from 'path';

describe('TheravadaContentTest (Canonical Teachings & Internal Links)', () => {
  // Read TheravadaContentSeeder.php
  const seederPath = path.resolve(process.cwd(), 'database/seeders/TheravadaContentSeeder.php');
  const seederContent = fs.readFileSync(seederPath, 'utf-8');

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
  describe('[T1_THERAVADA] Canonical Content & Article Completeness', () => {
    it('[T1_TH_01] TheravadaContentSeeder contains 38 comprehensive canonical articles', () => {
      expect(slugs.length).toBe(38);
      const uniqueSlugs = new Set(slugs);
      expect(uniqueSlugs.size).toBe(38);
    });

    it('[T1_TH_02] Articles span across all 4 key canonical categories (phap-hoc, phap-hanh, kinh-tung, lich-su)', () => {
      expect(categories.length).toBe(38);
      const uniqueCategories = new Set(categories);
      expect(uniqueCategories.has('phap-hoc')).toBe(true);
      expect(uniqueCategories.has('phap-hanh')).toBe(true);
      expect(uniqueCategories.has('kinh-tung')).toBe(true);
      expect(uniqueCategories.has('lich-su')).toBe(true);

      const phapHocCount = categories.filter(c => c === 'phap-hoc').length;
      const phapHanhCount = categories.filter(c => c === 'phap-hanh').length;
      const kinhTungCount = categories.filter(c => c === 'kinh-tung').length;
      const lichSuCount = categories.filter(c => c === 'lich-su').length;

      expect(phapHocCount).toBeGreaterThanOrEqual(15);
      expect(phapHanhCount).toBeGreaterThanOrEqual(5);
      expect(kinhTungCount).toBeGreaterThanOrEqual(8);
      expect(lichSuCount).toBeGreaterThanOrEqual(6);
    });

    it('[T1_TH_03] Pali Glossary contains comprehensive term definitions', () => {
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
        'Alagaddūpama'
      ];

      keyTerms.forEach(term => {
        const found = findPaliTermDefinition(term);
        expect(found).toBeDefined();
        expect(found?.definition.length).toBeGreaterThan(10);
      });
    });

    it('[T1_TH_04] Dhammapada collection contains inspiring daily verses with insight', () => {
      expect(DHAMMAPADA_VERSES.length).toBeGreaterThanOrEqual(10);
      DHAMMAPADA_VERSES.forEach(verse => {
        expect(typeof verse.verse_number).toBe('number');
        expect(verse.pali.length).toBeGreaterThan(10);
        expect(verse.vietnamese.length).toBeGreaterThan(10);
        expect(verse.insight.length).toBeGreaterThan(10);
      });
    });
  });

  // ==========================================================================
  // TIER 2: Boundary & Corner Cases (Examples & Internal Linking Mesh)
  // ==========================================================================
  describe('[T2_THERAVADA] Rich Examples & Internal Linking Mesh Integrity', () => {
    it('[T2_TH_01] All 38 articles contain detailed canonical or practical examples', () => {
      // Split content by article block in seeder
      const articleBlocks = seederContent.split(/\[\s*'site_domain'\s*=>\s*'theravada'/);
      expect(articleBlocks.length).toBe(39); // 1 header + 38 articles

      for (let i = 1; i < articleBlocks.length; i++) {
        const block = articleBlocks[i];
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
      }
    });

    it('[T2_TH_02] All 38 articles contain internal markdown links referencing other articles', () => {
      const articleBlocks = seederContent.split(/\[\s*'site_domain'\s*=>\s*'theravada'/);
      const internalLinkPattern = /\[([^\]]+)\]\(\/theravada\/kinh\/([a-z0-9-]+)\)/g;

      let totalInternalLinks = 0;
      const foundLinkSlugs = new Set<string>();

      for (let i = 1; i < articleBlocks.length; i++) {
        const block = articleBlocks[i];
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
      }

      // Total internal links across all 38 articles should be high (> 90)
      expect(totalInternalLinks).toBeGreaterThanOrEqual(90);

      // Verify that every linked slug actually exists in the 38 articles list!
      const validSlugSet = new Set(slugs);
      foundLinkSlugs.forEach(targetSlug => {
        expect(validSlugSet.has(targetSlug)).toBe(true);
      });
    });

    it('[T2_TH_03] Articles have substantial content length ensuring in-depth coverage', () => {
      const articleBlocks = seederContent.split(/\[\s*'site_domain'\s*=>\s*'theravada'/);
      for (let i = 1; i < articleBlocks.length; i++) {
        const block = articleBlocks[i];
        // Ensure each article content is rich (> 1000 characters)
        expect(block.length).toBeGreaterThan(1200);
      }
    });

    it('[T2_TH_04] All 38 articles contain valid Mermaid flowchart or timeline diagrams', () => {
      const articleBlocks = seederContent.split(/\[\s*'site_domain'\s*=>\s*'theravada'/);
      for (let i = 1; i < articleBlocks.length; i++) {
        const block = articleBlocks[i];
        expect(block.includes('```mermaid')).toBe(true);
      }
    });
  });
});
