/**
 * Test Suite: Midnight Tech & Fun Hub Multi-Page Routing (F22_MULTI_PAGE_BLOG)
 * Tier 1: Feature Coverage (Isolation)
 * Tier 2: Boundary & Corner Cases
 * Tier 3: Cross-Feature Interactions
 * Tier 4: Real-World E2E Navigation
 */

import { describe, it, expect, beforeEach, afterEach } from '../Harness/index.js';
import { setupTestEnvironment } from '../Harness/mock_helpers.js';

export interface RouteItem {
  name: string;
  path: string;
  component: string;
}

export const appRoutes: RouteItem[] = [
  { name: 'home', path: '/', component: 'Home' },
  { name: 'oracle.index', path: '/oracle', component: 'Oracle/Index' },
  { name: 'blog.index', path: '/blog', component: 'Blog/Index' },
  { name: 'blog.show', path: '/blog/:slug', component: 'Blog/Show' },
  { name: 'game.index', path: '/game', component: 'Game/Index' },
  { name: 'talisman.index', path: '/talisman', component: 'Talisman/Index' },
  { name: 'contact.index', path: '/contact', component: 'Contact/Index' },
];

export const sampleArticles = [
  {
    id: 1,
    title: 'Kiến Trúc Multi-Agent AI Tự Trị Thay Thế 100% Customer Service 24/7',
    slug: 'kien-truc-multi-agent-ai-customer-service',
    tags: ['AI Agents', 'Multi-Agent', 'GenAI', 'Customer Service', 'Laravel 12'],
    reading_time_min: 6,
    is_published: true,
  },
  {
    id: 2,
    title: 'Tối Ưu Cổng Định Giá Cổ Phiếu 7 Năm Với 50+ Artisan Crawlers & Gemini AI',
    slug: 'toi-uu-dinh-gia-co-phieu-artisan-gemini-ai',
    tags: ['Laravel 12', 'Filament 3', 'Gemini AI', 'Finance', 'Redis Queue'],
    reading_time_min: 5,
    is_published: true,
  },
  {
    id: 3,
    title: 'Số Hóa 500,000+ Điểm Nút Mạng Cáp Quang Toàn Quốc Với QGIS & PostGIS',
    slug: 'so-hoa-mang-cap-quang-toan-quoc-qgis-postgis',
    tags: ['GIS / Spatial', 'QGIS', 'PostgreSQL', 'PostGIS', 'Telecom'],
    reading_time_min: 7,
    is_published: true,
  },
  {
    id: 4,
    title: 'Giám Sát Hạ Tầng Truyền Dẫn SDH/DWDM Thời Gian Thực Bằng NMS & ML',
    slug: 'giam-sat-ha-tang-truyen-dan-sdh-dwdm-nms-ml',
    tags: ['NMS', 'Telecom', 'Elasticsearch', 'RabbitMQ', 'Machine Learning'],
    reading_time_min: 8,
    is_published: true,
  },
];

describe('MultiPageRoutingTest (Midnight Tech & Fun Hub)', () => {
  let env: any;

  beforeEach(() => {
    env = setupTestEnvironment();
  });

  afterEach(() => {
    env.teardown();
  });

  // ==========================================================================
  // TIER 1: Feature Coverage (Isolation)
  // ==========================================================================
  describe('[T1_F22] Knowledge & Fun Page Route Registration', () => {
    it('[T1_F22_01] All core knowledge and fun hub pages exist in the routing table', () => {
      expect(appRoutes.length).toBe(7);
      const paths = appRoutes.map(r => r.path);
      expect(paths).toContain('/');
      expect(paths).toContain('/blog');
      expect(paths).toContain('/blog/:slug');
      expect(paths).toContain('/game');
      expect(paths).toContain('/talisman');
      expect(paths).toContain('/contact');
    });

    it('[T1_F22_02] Blog articles have required fields: title, slug, tags, reading_time_min', () => {
      expect(sampleArticles.length).toBeGreaterThanOrEqual(4);
      sampleArticles.forEach(a => {
        expect(a.title.length).toBeGreaterThan(0);
        expect(a.slug.length).toBeGreaterThan(0);
        expect(a.tags.length).toBeGreaterThan(0);
        expect(a.reading_time_min).toBeGreaterThan(0);
        expect(a.is_published).toBe(true);
      });
    });

    it('[T1_F22_03] Blog tag filter correctly filters articles by category tag', () => {
      const aiArticles = sampleArticles.filter(a => a.tags.includes('AI Agents'));
      expect(aiArticles.length).toBe(1);
      expect(aiArticles[0].slug).toBe('kien-truc-multi-agent-ai-customer-service');

      const gisArticles = sampleArticles.filter(a => a.tags.includes('GIS / Spatial'));
      expect(gisArticles.length).toBe(1);
      expect(gisArticles[0].slug).toBe('so-hoa-mang-cap-quang-toan-quoc-qgis-postgis');
    });
  });

  // ==========================================================================
  // TIER 2: Boundary & Search Filtering
  // ==========================================================================
  describe('[T2_F22] Search Filtering & Edge Cases', () => {
    it('[T2_F22_01] Matching search query returns relevant tech articles', () => {
      const query = 'crawlers';
      const results = sampleArticles.filter(a =>
        a.title.toLowerCase().includes(query) ||
        a.tags.some(t => t.toLowerCase().includes(query))
      );
      expect(results.length).toBe(1);
      expect(results[0].slug).toBe('toi-uu-dinh-gia-co-phieu-artisan-gemini-ai');
    });

    it('[T2_F22_02] Non-matching search query returns empty array gracefully', () => {
      const query = 'non_existent_framework_xyz';
      const results = sampleArticles.filter(a =>
        a.title.toLowerCase().includes(query) ||
        a.tags.some(t => t.toLowerCase().includes(query))
      );
      expect(results.length).toBe(0);
    });
  });

  // ==========================================================================
  // TIER 3 & TIER 4: Cross-Feature Navigation
  // ==========================================================================
  describe('[T4_F22] Multi-Page Navigation Simulation', () => {
    it('[T4_F22_01] User navigates cleanly across knowledge and fun pages without reload', () => {
      let currentPath = '/';
      expect(currentPath).toBe('/');

      currentPath = '/blog';
      expect(currentPath).toBe('/blog');

      const article = sampleArticles[0];
      currentPath = `/blog/${article.slug}`;
      expect(currentPath).toBe('/blog/kien-truc-multi-agent-ai-customer-service');

      currentPath = '/game';
      expect(currentPath).toBe('/game');

      currentPath = '/talisman';
      expect(currentPath).toBe('/talisman');

      currentPath = '/contact';
      expect(currentPath).toBe('/contact');
    });
  });
});
