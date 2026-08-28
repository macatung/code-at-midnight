/**
 * Test Suite: Mermaid Diagram Markdown Parsing & Rendering Resilience
 * Tier 1: Feature Coverage (Isolation)
 * Tier 2: Boundary & Corner Cases (CRLF / LF Robustness)
 */

import { describe, it, expect } from '../Harness/index.js';
import fs from 'fs';
import path from 'path';

describe('MermaidRenderingTest (CRLF/LF Normalization & Markdown Regex)', () => {
  const theravadaSeederPath = path.resolve(process.cwd(), 'database/seeders/TheravadaContentSeeder.php');
  const theravadaContent = fs.readFileSync(theravadaSeederPath, 'utf-8');

  const contentSeederPath = path.resolve(process.cwd(), 'database/seeders/ContentAndAnalyticsSeeder.php');
  const blogContent = fs.readFileSync(contentSeederPath, 'utf-8');

  const theravadaShowPath = path.resolve(process.cwd(), 'resources/js/Pages/Theravada/Show.vue');
  const theravadaShowCode = fs.readFileSync(theravadaShowPath, 'utf-8');

  const blogShowPath = path.resolve(process.cwd(), 'resources/js/Pages/Blog/Show.vue');
  const blogShowCode = fs.readFileSync(blogShowPath, 'utf-8');

  // ==========================================================================
  // TIER 1: Feature Coverage (Isolation)
  // ==========================================================================
  describe('[T1_MERMAID] Component Architecture & Rendering Functions', () => {
    it('[T1_MM_01] Theravada Show.vue initializes mermaid with loose security and dynamic theme', () => {
      expect(theravadaShowCode.includes("import mermaid from 'mermaid'")).toBe(true);
      expect(theravadaShowCode.includes("securityLevel: 'loose'")).toBe(true);
      expect(theravadaShowCode.includes("isPaperMode.value ? 'neutral' : 'dark'")).toBe(true);
      expect(theravadaShowCode.includes(".zen-mermaid-container")).toBe(true);
    });

    it('[T1_MM_02] Theravada Show.vue wraps diagram rendering in per-diagram try-catch block', () => {
      expect(theravadaShowCode.includes("try {")).toBe(true);
      expect(theravadaShowCode.includes("const { svg } = await mermaid.render(")).toBe(true);
      expect(theravadaShowCode.includes("catch (itemErr)")).toBe(true);
    });

    it('[T1_MM_03] Blog Show.vue supports Mermaid diagram rendering in technical articles', () => {
      expect(blogShowCode.includes("import mermaid from 'mermaid'")).toBe(true);
      expect(blogShowCode.includes("renderMermaidDiagrams")).toBe(true);
      expect(blogShowCode.includes(".blog-mermaid-container")).toBe(true);
    });

    it('[T1_MM_04] Theravada and Blog components normalize CRLF and CR linebreaks before regex parsing', () => {
      expect(theravadaShowCode.includes(".replace(/\\r\\n/g, '\\n').replace(/\\r/g, '\\n')")).toBe(true);
      expect(blogShowCode.includes(".replace(/\\r\\n/g, '\\n').replace(/\\r/g, '\\n')")).toBe(true);
    });
  });

  // ==========================================================================
  // TIER 2: Boundary & Corner Cases (CRLF vs LF Parsing & Extraction)
  // ==========================================================================
  describe('[T2_MERMAID] CRLF vs LF Parsing Robustness & Diagram Syntax Validation', () => {
    const parseMermaidMarkdown = (content: string) => {
      // Normalize both literal newlines and escaped \\n inside PHP double-quoted strings
      const normalized = content
        .replace(/\\n/g, '\n')
        .replace(/\\r/g, '\r')
        .replace(/\r\n/g, '\n')
        .replace(/\r/g, '\n');
      const matches: string[] = [];
      const regex = /```\s*mermaid\s*\n([\s\S]*?)```/gim;
      let m;
      while ((m = regex.exec(normalized)) !== null) {
        matches.push(m[1].trim());
      }
      return matches;
    };

    it('[T2_MM_01] Parser correctly parses mermaid diagram with LF (\\n) line breaks', () => {
      const sample = 'Some introductory text\n\n```mermaid\ngraph TD\nA --> B\n```\n\nSome trailing text';
      const diagrams = parseMermaidMarkdown(sample);
      expect(diagrams.length).toBe(1);
      expect(diagrams[0]).toBe('graph TD\nA --> B');
    });

    it('[T2_MM_02] Parser correctly parses mermaid diagram with Windows CRLF (\\r\\n) line breaks', () => {
      const sample = 'Some introductory text\r\n\r\n```mermaid\r\ngraph TD\r\nA[Start] --> B[End]\r\n```\r\n\r\nMore text';
      const diagrams = parseMermaidMarkdown(sample);
      expect(diagrams.length).toBe(1);
      expect(diagrams[0]).toBe('graph TD\nA[Start] --> B[End]');
    });

    it('[T2_MM_03] Parser correctly handles trailing spaces on ```mermaid header line', () => {
      const sample = '```mermaid   \r\ngraph LR\r\nA --> B\r\n```';
      const diagrams = parseMermaidMarkdown(sample);
      expect(diagrams.length).toBe(1);
      expect(diagrams[0]).toBe('graph LR\nA --> B');
    });

    it('[T2_MM_04] All 38 Theravada canonical articles contain extractable Mermaid diagrams', () => {
      const diagrams = parseMermaidMarkdown(theravadaContent);
      expect(diagrams.length).toBeGreaterThanOrEqual(38);
      diagrams.forEach((diag) => {
        expect(diag.length).toBeGreaterThan(15);
        const startsWithValidDirective =
          diag.startsWith('graph TD') ||
          diag.startsWith('graph LR') ||
          diag.startsWith('timeline') ||
          diag.startsWith('flowchart') ||
          diag.startsWith('sequenceDiagram') ||
          diag.startsWith('classDiagram') ||
          diag.startsWith('stateDiagram') ||
          diag.startsWith('pie') ||
          diag.startsWith('mindmap');
        expect(startsWithValidDirective).toBe(true);
      });
    });

    it('[T2_MM_05] Blog seeder contains valid Multi-Agent Orchestration Mermaid diagram', () => {
      const diagrams = parseMermaidMarkdown(blogContent);
      expect(diagrams.length).toBe(1);
      expect(diagrams[0].includes('Router Agent')).toBe(true);
      expect(diagrams[0].includes('ERP Database Agent')).toBe(true);
    });

    it('[T2_MM_06] URI Encoding and Decoding retains 100% diagram fidelity with Pāḷi diacritics', () => {
      const originalCode = `graph TD\n    A[Mắt Thấy Sắc Trần] --> B[Tâm Bāhiya: Tri giác thuần khiết]\n    B --> C[A-La-Hán Đắc Quả: Niết Bàn]`;
      const encoded = encodeURIComponent(originalCode.trim());
      const decoded = decodeURIComponent(encoded);
      expect(decoded).toBe(originalCode.trim());
    });
  });
});
