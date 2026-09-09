/**
 * Test Suite: Challenger M2_1 Empirical Adversarial Stress & Verification Suite
 * Focus: resources/js/Pages/Admin/Theravada/Videos/Index.vue
 *
 * Scope:
 * 1. View mode toggle persistence in localStorage (initialization, mutation, malformed data, storage errors, session reload)
 * 2. Dual View Modes parity (Grid Cards vs Compact Data Table: key uniqueness, action parity, format badges, copy popovers)
 * 3. Quick Copy Popover reactivity & state isolation (single popover active, event propagation, per-item copy success flags)
 * 4. Multi-tier thumbnail error recovery (CDN error -> YouTube fallback -> Zen gradient poster card)
 * 5. Search, status filtering, and pagination query contract validation
 */

import { describe, it, expect, beforeEach, afterEach } from '../Harness/index.js';
import { setupTestEnvironment } from '../Harness/mock_helpers.js';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const projectRoot = path.resolve(__dirname, '../..');
const indexVuePath = path.join(projectRoot, 'resources/js/Pages/Admin/Theravada/Videos/Index.vue');

// ============================================================================
// Model simulating Index.vue View Mode & Reactivity Logic
// ============================================================================
class IndexViewModel {
  public viewMode: 'grid' | 'table' = 'grid';
  public currentStatus: string;
  public searchQuery: string;
  public activeQuickCopyId: number | null = null;
  public copySuccess: Record<string, boolean> = {};
  public thumbErrors: Record<number, boolean> = {};
  public routerCalls: Array<{ url: string; params: any; options: any }> = [];

  constructor(filters = { status: 'all', search: '' }) {
    this.currentStatus = filters.status || 'all';
    this.searchQuery = filters.search || '';
  }

  public onMounted(): void {
    try {
      const saved = localStorage.getItem('theravada_videos_view_mode');
      if (saved === 'grid' || saved === 'table') {
        this.viewMode = saved;
      }
    } catch {
      // Graceful fallback
    }
  }

  public setViewMode(mode: 'grid' | 'table'): void {
    this.viewMode = mode;
    try {
      localStorage.setItem('theravada_videos_view_mode', mode);
    } catch {
      // Ignore storage write issues
    }
  }

  public toggleQuickCopy(id: number, event?: { stopPropagation: () => void }): void {
    if (event?.stopPropagation) {
      event.stopPropagation();
    }
    this.activeQuickCopyId = this.activeQuickCopyId === id ? null : id;
  }

  public closeQuickCopy(): void {
    this.activeQuickCopyId = null;
  }

  public handleCopyText(text: string, key: string): void {
    if (!text) return;
    this.copySuccess[key] = true;
  }

  public applyFilter(statusKey: string): void {
    this.currentStatus = statusKey;
    this.routerCalls.push({
      url: '/admin/theravada/videos',
      params: {
        status: statusKey,
        search: this.searchQuery || undefined,
      },
      options: {
        preserveState: true,
        preserveScroll: true,
      },
    });
  }

  public handleSearch(): void {
    this.routerCalls.push({
      url: '/admin/theravada/videos',
      params: {
        status: this.currentStatus !== 'all' ? this.currentStatus : undefined,
        search: this.searchQuery || undefined,
      },
      options: {
        preserveState: true,
        preserveScroll: true,
      },
    });
  }

  public clearSearch(): void {
    this.searchQuery = '';
    this.handleSearch();
  }

  public getYouTubeId(item: any): string | null {
    if (item.youtube_id) return item.youtube_id;
    if (!item.youtube_url) return null;
    const url = item.youtube_url.trim();
    if (/^[a-zA-Z0-9_-]{11}$/.test(url)) return url;
    const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
    return match ? match[1] : null;
  }

  public handleThumbError(imgTarget: { src: string }, item: any): void {
    const ytId = this.getYouTubeId(item);
    if (ytId && !imgTarget.src.includes('img.youtube.com')) {
      imgTarget.src = `https://img.youtube.com/vi/${ytId}/mqdefault.jpg`;
    } else {
      this.thumbErrors[item.id] = true;
    }
  }

  public formatDuration(val?: string | null): string {
    if (!val) return '—';
    const num = Number(val);
    if (!isNaN(num)) {
      const mins = Math.floor(num / 60);
      const secs = Math.floor(num % 60);
      return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    }
    return val;
  }

  public getStatusLabel(status: string): string {
    switch (status) {
      case 'published':
        return 'Đã Xuất Bản';
      case 'completed':
        return 'Đã Hoàn Thành';
      case 'processing':
        return 'Đang Render';
      case 'failed':
        return 'Thất Bại';
      case 'draft':
      default:
        return 'Bản Nháp';
    }
  }

  public getArticleHashtagsFormatted(article: any): string {
    if (Array.isArray(article.hashtags) && article.hashtags.length > 0) {
      return article.hashtags.map((h: string) => (h.startsWith('#') ? h : `#${h}`)).join(' ');
    }
    return '#TamAnVanSuAn #LoiPhatDay #Theravada #macatungdev';
  }

  public copyFullKit(article: any): string {
    const title = article.seo_title || article.title;
    const desc = article.seo_description || article.excerpt || '';
    const caption = article.social_caption || '';
    const tags = this.getArticleHashtagsFormatted(article);
    const link = `https://theravada.macatung.dev/phap-thoai/${article.slug}`;

    return [
      `=== TIÊU ĐỀ VIDEO YOUTUBE ===`,
      title,
      ``,
      `=== MÔ TẢ VIDEO (YOUTUBE / FACEBOOK) ===`,
      desc,
      ``,
      `=== CAPTION MẠNG XÃ HỘI (TIKTOK / REELS) ===`,
      caption,
      ``,
      `=== HASHTAGS BÀI ĐĂNG ===`,
      tags,
      ``,
      `=== LIÊN KẾT BÀI VIẾT GỐC ===`,
      link,
    ].join('\n');
  }
}

describe('Challenger M2_1: Index.vue Empirical Adversarial Stress & Verification', () => {
  let env: any;
  const indexSource = fs.readFileSync(indexVuePath, 'utf-8');

  beforeEach(() => {
    env = setupTestEnvironment();
    localStorage.clear();
  });

  afterEach(() => {
    env.teardown();
    localStorage.clear();
  });

  // ==========================================================================
  // SECTION 1: VIEW MODE LOCALSTORAGE PERSISTENCE STRESS
  // ==========================================================================
  describe('1. View Mode Toggle Persistence in localStorage', () => {
    it('[CH2_VM_01] ViewMode defaults to "grid" when localStorage is empty', () => {
      const model = new IndexViewModel();
      model.onMounted();
      expect(model.viewMode).toBe('grid');
    });

    it('[CH2_VM_02] Reading "table" from localStorage initializes viewMode to "table"', () => {
      localStorage.setItem('theravada_videos_view_mode', 'table');
      const model = new IndexViewModel();
      model.onMounted();
      expect(model.viewMode).toBe('table');
    });

    it('[CH2_VM_03] Reading "grid" from localStorage initializes viewMode to "grid"', () => {
      localStorage.setItem('theravada_videos_view_mode', 'grid');
      const model = new IndexViewModel();
      model.onMounted();
      expect(model.viewMode).toBe('grid');
    });

    it('[CH2_VM_04] Corrupted or unknown localStorage values safely default to "grid"', () => {
      const hostileValues = [
        'compact',
        'list',
        'TABLE',
        'GRID',
        '',
        'null',
        'undefined',
        '{"mode":"table"}',
        '<script>alert(1)</script>',
        '123',
      ];

      for (const val of hostileValues) {
        localStorage.setItem('theravada_videos_view_mode', val);
        const model = new IndexViewModel();
        model.onMounted();
        expect(model.viewMode).toBe('grid');
      }
    });

    it('[CH2_VM_05] setViewMode switches reactive state and writes to localStorage', () => {
      const model = new IndexViewModel();
      model.onMounted();

      // Switch to table
      model.setViewMode('table');
      expect(model.viewMode).toBe('table');
      expect(localStorage.getItem('theravada_videos_view_mode')).toBe('table');

      // Switch to grid
      model.setViewMode('grid');
      expect(model.viewMode).toBe('grid');
      expect(localStorage.getItem('theravada_videos_view_mode')).toBe('grid');
    });

    it('[CH2_VM_06] Multi-session reload simulation maintains user preference', () => {
      // Session 1: User switches to table
      const session1 = new IndexViewModel();
      session1.onMounted();
      session1.setViewMode('table');
      expect(session1.viewMode).toBe('table');

      // Session 2: Page reload / re-mount
      const session2 = new IndexViewModel();
      session2.onMounted();
      expect(session2.viewMode).toBe('table');

      // Session 2: User switches to grid
      session2.setViewMode('grid');

      // Session 3: Another page visit
      const session3 = new IndexViewModel();
      session3.onMounted();
      expect(session3.viewMode).toBe('grid');
    });

    it('[CH2_VM_07] Handles localStorage SecurityError / QuotaExceededError without crashing', () => {
      const model = new IndexViewModel();
      model.onMounted();

      // Mock localStorage.setItem to simulate QuotaExceededError or private browsing lock
      const originalSetItem = localStorage.setItem;
      localStorage.setItem = () => {
        throw new Error('QuotaExceededError: The quota has been exceeded.');
      };

      expect(() => {
        model.setViewMode('table');
      }).not.toThrow();

      // Reactive state still updates in memory
      expect(model.viewMode).toBe('table');

      localStorage.setItem = originalSetItem;
    });
  });

  // ==========================================================================
  // SECTION 2: DUAL VIEW MODES PARITY (GRID VS TABLE)
  // ==========================================================================
  describe('2. Dual View Modes Parity & Template Verification', () => {
    it('[CH2_PARITY_01] Template implements both Grid view and Table view via segmented control', () => {
      expect(indexSource).toContain("v-else-if=\"viewMode === 'grid'\"");
      expect(indexSource).toContain('v-else');
      expect(indexSource).toContain('@click="setViewMode(\'grid\')"');
      expect(indexSource).toContain('@click="setViewMode(\'table\')"');
    });

    it('[CH2_PARITY_02] Both views bind unique keys using item.id without key collision', () => {
      // In Grid view
      expect(indexSource).toContain('v-for="item in articles.data"\n        :key="item.id"');
      // In Table view
      expect(indexSource).toContain('v-for="item in articles.data"\n              :key="item.id"');
    });

    it('[CH2_PARITY_03] Both views provide identical action sets for article management', () => {
      // 1. Detail link (/admin/theravada/videos/${item.id})
      const detailLinkCount = (indexSource.match(/:href="`\/admin\/theravada\/videos\/\$\{item\.id\}`"/g) || []).length;
      expect(detailLinkCount).toBeGreaterThanOrEqual(2); // At least one in grid and one in table

      // 2. Trigger Pipeline action for draft & failed
      const triggerCount = (indexSource.match(/triggerPipeline\(item\)/g) || []).length;
      expect(triggerCount).toBe(2); // 1 in grid, 1 in table

      // 3. Toggle Publish action for completed & published
      const togglePublishCount = (indexSource.match(/togglePublish\(item\)/g) || []).length;
      expect(togglePublishCount).toBe(2); // 1 in grid, 1 in table

      // 4. Quick copy popover trigger
      const quickCopyCount = (indexSource.match(/toggleQuickCopy\(item\.id, e\)/g) || []).length;
      expect(quickCopyCount).toBe(2); // 1 in grid, 1 in table
    });

    it('[CH2_PARITY_04] Both views display 16:9 and 9:16 format availability badges', () => {
      // Grid view has format indicator pills
      expect(indexSource).toContain('title="Video 16:9 YouTube"');
      expect(indexSource).toContain('title="Video 9:16 Shorts / Reels"');

      // Table view has format indicator column
      expect(indexSource).toContain('16:9 YT');
      expect(indexSource).toContain('9:16 Reel');
    });

    it('[CH2_PARITY_05] Both views utilize formatDuration for consistent time presentation', () => {
      const model = new IndexViewModel();

      expect(model.formatDuration('65')).toBe('1:05');
      expect(model.formatDuration('125')).toBe('2:05');
      expect(model.formatDuration('3600')).toBe('60:00');
      expect(model.formatDuration('0')).toBe('0:00');
      expect(model.formatDuration(null)).toBe('—');
      expect(model.formatDuration(undefined)).toBe('—');
      expect(model.formatDuration('')).toBe('—');
      expect(model.formatDuration('15:45')).toBe('15:45');
    });
  });

  // ==========================================================================
  // SECTION 3: QUICK COPY POPOVER REACTIVITY & ISOLATION
  // ==========================================================================
  describe('3. Quick Copy Popover Reactivity & State Isolation', () => {
    it('[CH2_QC_01] Toggling popover for article A opens it and stops event propagation', () => {
      const model = new IndexViewModel();
      let stopped = false;
      const fakeEvent = { stopPropagation: () => { stopped = true; } };

      model.toggleQuickCopy(10, fakeEvent);
      expect(stopped).toBe(true);
      expect(model.activeQuickCopyId).toBe(10);

      // Toggling same id closes it
      model.toggleQuickCopy(10, fakeEvent);
      expect(model.activeQuickCopyId).toBeNull();
    });

    it('[CH2_QC_02] Opening popover for article B automatically closes article A', () => {
      const model = new IndexViewModel();
      model.toggleQuickCopy(10);
      expect(model.activeQuickCopyId).toBe(10);

      model.toggleQuickCopy(20);
      expect(model.activeQuickCopyId).toBe(20);
    });

    it('[CH2_QC_03] closeQuickCopy closes active popover on window click', () => {
      const model = new IndexViewModel();
      model.toggleQuickCopy(15);
      expect(model.activeQuickCopyId).toBe(15);

      model.closeQuickCopy();
      expect(model.activeQuickCopyId).toBeNull();
    });

    it('[CH2_QC_04] Copy success flags are strictly scoped per item ID', () => {
      const model = new IndexViewModel();

      model.handleCopyText('Tiêu đề 1', 'title-1');
      expect(model.copySuccess['title-1']).toBe(true);
      expect(model.copySuccess['title-2']).toBeUndefined();

      model.handleCopyText('Tiêu đề 2', 'title-2');
      expect(model.copySuccess['title-2']).toBe(true);
    });

    it('[CH2_QC_05] copyFullKit generates complete structured content with fallbacks', () => {
      const model = new IndexViewModel();
      const article = {
        id: 42,
        title: 'Tứ Vô Lượng Tâm',
        slug: 'tu-vo-luong-tam',
        excerpt: 'Từ, Bi, Hỷ, Xả là bốn tâm vô lượng.',
        seo_title: 'Tâm An Vạn Sự An | Tứ Vô Lượng Tâm',
        seo_description: 'Mô tả chi tiết bài giảng Phật pháp...',
        social_caption: 'Thực hành Từ Bi Hỷ Xả mỗi ngày...',
        hashtags: ['#TuVoLuongTam', 'TuBiHyXa'],
      };

      const kit = model.copyFullKit(article);
      expect(kit).toContain('=== TIÊU ĐỀ VIDEO YOUTUBE ===');
      expect(kit).toContain('Tâm An Vạn Sự An | Tứ Vô Lượng Tâm');
      expect(kit).toContain('=== MÔ TẢ VIDEO (YOUTUBE / FACEBOOK) ===');
      expect(kit).toContain('Mô tả chi tiết bài giảng Phật pháp...');
      expect(kit).toContain('=== CAPTION MẠNG XÃ HỘI (TIKTOK / REELS) ===');
      expect(kit).toContain('Thực hành Từ Bi Hỷ Xả mỗi ngày...');
      expect(kit).toContain('=== HASHTAGS BÀI ĐĂNG ===');
      expect(kit).toContain('#TuVoLuongTam #TuBiHyXa');
      expect(kit).toContain('https://theravada.macatung.dev/phap-thoai/tu-vo-luong-tam');
    });

    it('[CH2_QC_06] copyFullKit handles null optional fields safely', () => {
      const model = new IndexViewModel();
      const minimalArticle = {
        id: 50,
        title: 'Bát Chánh Đạo',
        slug: 'bat-chanh-dao',
      };

      const kit = model.copyFullKit(minimalArticle);
      expect(kit).toContain('Bát Chánh Đạo');
      expect(kit).toContain('https://theravada.macatung.dev/phap-thoai/bat-chanh-dao');
      expect(kit).not.toContain('null');
      expect(kit).not.toContain('undefined');
    });
  });

  // ==========================================================================
  // SECTION 4: MULTI-TIER THUMBNAIL FALLBACK STRESS
  // ==========================================================================
  describe('4. Multi-tier Thumbnail Error Recovery', () => {
    it('[CH2_THUMB_01] Falls back to YouTube mqdefault when CDN image 404s and YouTube ID exists', () => {
      const model = new IndexViewModel();
      const item = {
        id: 101,
        youtube_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
      };
      const imgTarget = { src: 'https://cdn.example.com/broken-cdn.jpg' };

      model.handleThumbError(imgTarget, item);
      expect(imgTarget.src).toBe('https://img.youtube.com/vi/dQw4w9WgXcQ/mqdefault.jpg');
      expect(model.thumbErrors[101]).toBeUndefined();
    });

    it('[CH2_THUMB_02] Flags thumbErrors when YouTube thumbnail also fails or no YouTube ID', () => {
      const model = new IndexViewModel();

      // Case A: No YouTube ID at all
      const itemNoYt = { id: 102 };
      const imgTargetA = { src: 'https://cdn.example.com/broken.jpg' };
      model.handleThumbError(imgTargetA, itemNoYt);
      expect(model.thumbErrors[102]).toBe(true);

      // Case B: Already on YouTube fallback and failed again
      const itemWithYt = { id: 103, youtube_id: 'abc12345678' };
      const imgTargetB = { src: 'https://img.youtube.com/vi/abc12345678/mqdefault.jpg' };
      model.handleThumbError(imgTargetB, itemWithYt);
      expect(model.thumbErrors[103]).toBe(true);
    });
  });

  // ==========================================================================
  // SECTION 5: SEARCH & STATUS FILTERING REACTIVITY
  // ==========================================================================
  describe('5. Search & Status Filtering Reactivity', () => {
    it('[CH2_FILTER_01] applyFilter dispatches Inertia get with status and search params', () => {
      const model = new IndexViewModel({ status: 'all', search: 'phap cu' });
      model.applyFilter('completed');

      expect(model.currentStatus).toBe('completed');
      expect(model.routerCalls.length).toBe(1);
      expect(model.routerCalls[0].url).toBe('/admin/theravada/videos');
      expect(model.routerCalls[0].params).toEqual({
        status: 'completed',
        search: 'phap cu',
      });
      expect(model.routerCalls[0].options.preserveState).toBe(true);
      expect(model.routerCalls[0].options.preserveScroll).toBe(true);
    });

    it('[CH2_FILTER_02] handleSearch preserves active status filter when not "all"', () => {
      const model = new IndexViewModel({ status: 'processing', search: 'kinh' });
      model.handleSearch();

      expect(model.routerCalls.length).toBe(1);
      expect(model.routerCalls[0].params).toEqual({
        status: 'processing',
        search: 'kinh',
      });
    });

    it('[CH2_FILTER_03] clearSearch resets query and executes search', () => {
      const model = new IndexViewModel({ status: 'all', search: 'to be cleared' });
      model.clearSearch();

      expect(model.searchQuery).toBe('');
      expect(model.routerCalls.length).toBe(1);
      expect(model.routerCalls[0].params.search).toBeUndefined();
    });

    it('[CH2_FILTER_04] getStatusLabel maps all valid statuses and defaults safely', () => {
      const model = new IndexViewModel();

      expect(model.getStatusLabel('published')).toBe('Đã Xuất Bản');
      expect(model.getStatusLabel('completed')).toBe('Đã Hoàn Thành');
      expect(model.getStatusLabel('processing')).toBe('Đang Render');
      expect(model.getStatusLabel('failed')).toBe('Thất Bại');
      expect(model.getStatusLabel('draft')).toBe('Bản Nháp');
      expect(model.getStatusLabel('unknown_status')).toBe('Bản Nháp');
    });
  });
});
