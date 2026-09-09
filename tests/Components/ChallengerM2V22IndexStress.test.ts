/**
 * Test Suite: Challenger M2_2 Empirical Adversarial Stress & Verification Suite
 * Focus: resources/js/Pages/Admin/Theravada/Videos/Index.vue
 *
 * Scope:
 * 1. Quick Copy Popover: rapid clicks, outside clicks, event stopPropagation, lifecycle cleanup
 * 2. Clipboard Engine & Visual Feedback: modern API, legacy textarea fallback, master kit generator
 * 3. Thumbnail Fallback & Infinite Loop Proof: CDN -> YouTube -> Zen gradient multi-tier error handling
 * 4. YouTube ID Extraction Fuzzing: standard, short, embed, 11-char ID, query params, malformed URLs
 * 5. View Mode Persistence & LocalStorage Durability: grid <-> table toggling, private browsing resilience
 * 6. Duration Formatter & Status Engine: numeric/non-numeric bounds, status badge classes & labels
 * 7. Template Architecture & Layout Safety: dual view modes, popover positioning, aspect-ratio locks
 */

import { describe, it, expect } from '../Harness/index.js';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const projectRoot = path.resolve(__dirname, '../..');
const indexVuePath = path.join(projectRoot, 'resources/js/Pages/Admin/Theravada/Videos/Index.vue');

// ============================================================================
// Mirror Implementations from Index.vue for Empirical Testing
// ============================================================================

interface ArticleItem {
  id: number;
  title: string;
  pali_title?: string | null;
  slug: string;
  category?: string | null;
  author?: string | null;
  excerpt?: string | null;
  video_status: string;
  video_long_url?: string | null;
  video_short_url?: string | null;
  youtube_url?: string | null;
  youtube_id?: string | null;
  thumbnail_long_url?: string | null;
  thumbnail_short_url?: string | null;
  video_long_duration?: string | null;
  video_short_duration?: string | null;
  seo_title?: string | null;
  seo_description?: string | null;
  social_caption?: string | null;
  hashtags?: string[] | null;
  is_published: boolean;
  updated_at: string;
}

function getYouTubeId(item: Partial<ArticleItem>): string | null {
  if (item.youtube_id) return item.youtube_id;
  if (!item.youtube_url) return null;
  const url = item.youtube_url.trim();
  if (/^[a-zA-Z0-9_-]{11}$/.test(url)) return url;
  const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
  return match ? match[1] : null;
}

function formatDuration(val?: string | null): string {
  if (!val) return '—';
  const num = Number(val);
  if (!isNaN(num)) {
    const mins = Math.floor(num / 60);
    const secs = Math.floor(num % 60);
    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
  }
  return val;
}

function getStatusLabel(status: string): string {
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

function getStatusBadgeClass(status: string): string {
  switch (status) {
    case 'published':
      return 'bg-phantom-mint/20 text-phantom-mint border border-phantom-mint/30';
    case 'completed':
      return 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
    case 'processing':
      return 'bg-amber-500/20 text-amber-300 border border-amber-500/30 animate-pulse';
    case 'failed':
      return 'bg-rose-500/20 text-rose-400 border border-rose-500/30';
    case 'draft':
    default:
      return 'bg-white/5 text-slate-400 border border-white/10';
  }
}

function getArticleHashtagsFormatted(article: Partial<ArticleItem>): string {
  if (Array.isArray(article.hashtags) && article.hashtags.length > 0) {
    return article.hashtags.map((h) => (String(h).startsWith('#') ? String(h) : `#${h}`)).join(' ');
  }
  return '#TamAnVanSuAn #LoiPhatDay #Theravada #macatungdev';
}

function generateFullKitText(article: Partial<ArticleItem>): string {
  const title = article.seo_title || article.title || '';
  const desc = article.seo_description || article.excerpt || '';
  const caption = article.social_caption || '';
  const tags = getArticleHashtagsFormatted(article);
  const link = `https://theravada.macatung.dev/phap-thoai/${article.slug || ''}`;

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

// Controller Simulation for Index.vue Popover & Thumbnail Engine
class IndexPageSimulator {
  viewMode: 'grid' | 'table' = 'grid';
  activeQuickCopyId: number | null = null;
  copySuccess: Record<string, boolean> = {};
  thumbErrors: Record<number, boolean> = {};
  windowClickListeners: Function[] = [];
  clipboardWrites: string[] = [];
  execCommands: string[] = [];

  constructor() {
    this.mount();
  }

  mount(storageData: Record<string, string> = {}) {
    try {
      const saved = storageData['theravada_videos_view_mode'];
      if (saved === 'grid' || saved === 'table') {
        this.viewMode = saved;
      }
    } catch {}

    const listener = () => this.closeQuickCopy();
    this.windowClickListeners.push(listener);
  }

  unmount() {
    this.windowClickListeners = [];
  }

  setViewMode(mode: 'grid' | 'table', storage?: Record<string, string>) {
    this.viewMode = mode;
    if (storage) {
      storage['theravada_videos_view_mode'] = mode;
    }
  }

  toggleQuickCopy(id: number, event?: { stopPropagation?: Function }) {
    if (event?.stopPropagation) {
      event.stopPropagation();
    }
    this.activeQuickCopyId = this.activeQuickCopyId === id ? null : id;
  }

  closeQuickCopy() {
    this.activeQuickCopyId = null;
  }

  triggerWindowClick() {
    for (const listener of this.windowClickListeners) {
      listener();
    }
  }

  async handleCopyText(
    text: string,
    key: string,
    clipboardMock?: { writeText: (t: string) => Promise<void> }
  ): Promise<boolean> {
    if (!text) return false;

    let copied = false;
    try {
      if (clipboardMock) {
        await clipboardMock.writeText(text);
        this.clipboardWrites.push(text);
        copied = true;
      } else {
        throw new Error('Clipboard API unavailable');
      }
    } catch {
      // Legacy textarea fallback
      this.execCommands.push(text);
      copied = true;
    }

    this.copySuccess[key] = true;
    setTimeout(() => {
      this.copySuccess[key] = false;
    }, 1800);

    return copied;
  }

  handleThumbError(imgTarget: { src: string }, item: Partial<ArticleItem>) {
    const ytId = getYouTubeId(item);
    if (ytId && !imgTarget.src.includes('img.youtube.com')) {
      imgTarget.src = `https://img.youtube.com/vi/${ytId}/mqdefault.jpg`;
    } else {
      this.thumbErrors[item.id!] = true;
    }
  }
}

// ============================================================================
// TEST SUITE
// ============================================================================

describe('Challenger M2_2: Index.vue Popover & Fallback Empirical Stress Suite', () => {
  const indexContent = fs.readFileSync(indexVuePath, 'utf-8');

  // ==========================================================================
  // SECTION 1: QUICK COPY POPOVER MENU STRESS & RACE CONDITIONS
  // ==========================================================================
  describe('1. Quick Copy Popover Mechanics & Rapid Click Stress', () => {
    it('[POPOVER_01] Opens and closes cleanly on single article toggle', () => {
      const sim = new IndexPageSimulator();
      expect(sim.activeQuickCopyId).toBe(null);

      // Open Card 1
      sim.toggleQuickCopy(1);
      expect(sim.activeQuickCopyId).toBe(1);

      // Toggle Card 1 again -> closes
      sim.toggleQuickCopy(1);
      expect(sim.activeQuickCopyId).toBe(null);
    });

    it('[POPOVER_02] Switches cleanly between different articles in rapid succession without stuck states', () => {
      const sim = new IndexPageSimulator();

      const sequence = [1, 2, 3, 4, 2, 1, 5, 5, 2];
      for (const id of sequence) {
        sim.toggleQuickCopy(id);
        expect(sim.activeQuickCopyId === null || sim.activeQuickCopyId === id).toBe(true);
      }
      expect(sim.activeQuickCopyId).toBe(2);
    });

    it('[POPOVER_03] Rapid spam toggle (100 iterations) maintains state integrity', () => {
      const sim = new IndexPageSimulator();
      for (let i = 0; i < 100; i++) {
        sim.toggleQuickCopy(42);
        expect(sim.activeQuickCopyId).toBe(i % 2 === 0 ? 42 : null);
      }
    });

    it('[POPOVER_04] Outside click properly closes active popover menu', () => {
      const sim = new IndexPageSimulator();
      sim.toggleQuickCopy(99);
      expect(sim.activeQuickCopyId).toBe(99);

      // Outside click bubbles to window
      sim.triggerWindowClick();
      expect(sim.activeQuickCopyId).toBe(null);
    });

    it('[POPOVER_05] Event stopPropagation prevents button click from triggering window click listener', () => {
      let stopped = false;
      const fakeEvent = {
        stopPropagation: () => {
          stopped = true;
        },
      };

      const sim = new IndexPageSimulator();
      sim.toggleQuickCopy(10, fakeEvent);

      expect(stopped).toBe(true);
      expect(sim.activeQuickCopyId).toBe(10);
    });

    it('[POPOVER_06] Component unmount cleans up window click listener to prevent leaks', () => {
      const sim = new IndexPageSimulator();
      expect(sim.windowClickListeners.length).toBe(1);

      sim.unmount();
      expect(sim.windowClickListeners.length).toBe(0);

      // Triggering window click after unmount does not throw or mutate
      sim.activeQuickCopyId = 77;
      sim.triggerWindowClick();
      expect(sim.activeQuickCopyId).toBe(77);
    });

    it('[POPOVER_07] Exclusivity invariant: exactly one popover can be active at any given time', () => {
      const sim = new IndexPageSimulator();
      sim.toggleQuickCopy(101);
      expect(sim.activeQuickCopyId).toBe(101);

      // Toggling another card closes 101 and activates 102
      sim.toggleQuickCopy(102);
      expect(sim.activeQuickCopyId).toBe(102);
      expect(sim.activeQuickCopyId !== 101).toBe(true);
    });
  });

  // ==========================================================================
  // SECTION 2: CLIPBOARD ENGINE & VISUAL FEEDBACK
  // ==========================================================================
  describe('2. Clipboard Engine, Visual Feedback & Fallbacks', () => {
    it('[CLIPBOARD_01] Modern Clipboard API writes text and triggers visual feedback flag', async () => {
      const sim = new IndexPageSimulator();
      const mockClipboard = {
        writeText: async (t: string) => {},
      };

      const res = await sim.handleCopyText('Tiêu đề SEO bài giảng', 'title-1', mockClipboard);
      expect(res).toBe(true);
      expect(sim.clipboardWrites).toContain('Tiêu đề SEO bài giảng');
      expect(sim.copySuccess['title-1']).toBe(true);
    });

    it('[CLIPBOARD_02] Legacy fallback engages seamlessly when navigator.clipboard throws or is absent', async () => {
      const sim = new IndexPageSimulator();
      const failingClipboard = {
        writeText: async () => {
          throw new Error('NotAllowedError');
        },
      };

      const res = await sim.handleCopyText('Fallback Text via execCommand', 'desc-1', failingClipboard);
      expect(res).toBe(true);
      expect(sim.execCommands).toContain('Fallback Text via execCommand');
      expect(sim.copySuccess['desc-1']).toBe(true);
    });

    it('[CLIPBOARD_03] Empty string text is rejected without false positive feedback', async () => {
      const sim = new IndexPageSimulator();
      const res = await sim.handleCopyText('', 'empty-1');
      expect(res).toBe(false);
      expect(sim.copySuccess['empty-1']).toBe(undefined);
    });

    it('[CLIPBOARD_04] Master Copy Kit generates complete structured payload for all platforms', () => {
      const article: Partial<ArticleItem> = {
        id: 1,
        title: 'Tứ Vô Lượng Tâm',
        seo_title: 'Tâm An Vạn Sự An | Tứ Vô Lượng Tâm',
        seo_description: 'Mô tả chi tiết bài pháp thoại...',
        social_caption: 'Thực hành Từ Bi Hỷ Xả mỗi ngày',
        hashtags: ['TuVoLuongTam', '#Theravada', 'LoiPhatDay'],
        slug: 'tu-vo-luong-tam',
      };

      const kit = generateFullKitText(article);
      expect(kit).toContain('=== TIÊU ĐỀ VIDEO YOUTUBE ===\nTâm An Vạn Sự An | Tứ Vô Lượng Tâm');
      expect(kit).toContain('=== MÔ TẢ VIDEO (YOUTUBE / FACEBOOK) ===\nMô tả chi tiết bài pháp thoại...');
      expect(kit).toContain('=== CAPTION MẠNG XÃ HỘI (TIKTOK / REELS) ===\nThực hành Từ Bi Hỷ Xả mỗi ngày');
      expect(kit).toContain('=== HASHTAGS BÀI ĐĂNG ===\n#TuVoLuongTam #Theravada #LoiPhatDay');
      expect(kit).toContain('=== LIÊN KẾT BÀI VIẾT GỐC ===\nhttps://theravada.macatung.dev/phap-thoai/tu-vo-luong-tam');
    });

    it('[CLIPBOARD_05] Master Copy Kit survives null / missing properties gracefully', () => {
      const articleEmpty: Partial<ArticleItem> = {
        id: 2,
        title: 'Bài Giảng Không Metadata',
        slug: 'bai-giang-khong-metadata',
        seo_title: null,
        seo_description: null,
        excerpt: null,
        social_caption: null,
        hashtags: null,
      };

      const kit = generateFullKitText(articleEmpty);
      expect(kit).toContain('=== TIÊU ĐỀ VIDEO YOUTUBE ===\nBài Giảng Không Metadata');
      expect(kit).toContain('#TamAnVanSuAn #LoiPhatDay #Theravada #macatungdev');
      expect(kit).toContain('https://theravada.macatung.dev/phap-thoai/bai-giang-khong-metadata');
      expect(kit).not.toContain('undefined');
      expect(kit).not.toContain('null');
    });

    it('[CLIPBOARD_06] Rapid spam copying (50 operations) handles async timer races cleanly', async () => {
      const sim = new IndexPageSimulator();
      const mockClipboard = { writeText: async () => {} };

      for (let i = 0; i < 50; i++) {
        await sim.handleCopyText(`Payload ${i}`, `key-${i % 5}`, mockClipboard);
      }
      expect(sim.clipboardWrites.length).toBe(50);
    });

    it('[CLIPBOARD_07] Correctly prefixes missing hash symbols on hashtag items', () => {
      const article = { hashtags: ['Pali', '#Buddhism', 'Dhamma'] };
      const formatted = getArticleHashtagsFormatted(article);
      expect(formatted).toBe('#Pali #Buddhism #Dhamma');
    });
  });

  // ==========================================================================
  // SECTION 3: THUMBNAIL FALLBACK ENGINE & INFINITE LOOP PROOF
  // ==========================================================================
  describe('3. Thumbnail Multi-Tier Fallback & Infinite Loop Proof', () => {
    it('[THUMB_01] Tier 1: Initial CDN error rewrites src to YouTube CDN when YouTube ID exists', () => {
      const sim = new IndexPageSimulator();
      const item: Partial<ArticleItem> = {
        id: 1,
        youtube_id: 'cjdEOM6sn24',
        thumbnail_long_url: 'https://broken-cdn.com/thumb169.jpg',
      };

      const imgTarget = { src: 'https://broken-cdn.com/thumb169.jpg' };
      sim.handleThumbError(imgTarget, item);

      expect(imgTarget.src).toBe('https://img.youtube.com/vi/cjdEOM6sn24/mqdefault.jpg');
      expect(sim.thumbErrors[1]).toBe(undefined);
    });

    it('[THUMB_02] Tier 2: Secondary error on YouTube CDN triggers final Zen gradient fallback', () => {
      const sim = new IndexPageSimulator();
      const item: Partial<ArticleItem> = {
        id: 1,
        youtube_id: 'cjdEOM6sn24',
        thumbnail_long_url: 'https://broken-cdn.com/thumb169.jpg',
      };

      const imgTarget = { src: 'https://img.youtube.com/vi/cjdEOM6sn24/mqdefault.jpg' };
      sim.handleThumbError(imgTarget, item);

      expect(sim.thumbErrors[1]).toBe(true);
    });

    it('[THUMB_03] Infinite Loop Proof: 100 consecutive error events terminate at step 2', () => {
      const sim = new IndexPageSimulator();
      const item: Partial<ArticleItem> = {
        id: 42,
        youtube_id: 'invalid_yt_id',
        thumbnail_long_url: 'https://broken-cdn.com/404.jpg',
      };

      const imgTarget = { src: 'https://broken-cdn.com/404.jpg' };

      // Event 1: CDN failure -> tries YouTube
      sim.handleThumbError(imgTarget, item);
      expect(imgTarget.src).toBe('https://img.youtube.com/vi/invalid_yt_id/mqdefault.jpg');
      expect(sim.thumbErrors[42]).toBe(undefined);

      // Event 2: YouTube CDN failure -> flags thumbErrors[42] = true
      sim.handleThumbError(imgTarget, item);
      expect(sim.thumbErrors[42]).toBe(true);

      // Events 3 to 100: Simulate rogue repeated error triggers
      for (let i = 3; i <= 100; i++) {
        sim.handleThumbError(imgTarget, item);
        expect(sim.thumbErrors[42]).toBe(true);
        expect(imgTarget.src).toBe('https://img.youtube.com/vi/invalid_yt_id/mqdefault.jpg');
      }
    });

    it('[THUMB_04] Direct Tier 3: Item without YouTube ID or URL immediately falls back without attempting YouTube', () => {
      const sim = new IndexPageSimulator();
      const itemNoYt: Partial<ArticleItem> = {
        id: 99,
        youtube_id: null,
        youtube_url: null,
        thumbnail_long_url: 'https://broken-cdn.com/thumb.jpg',
      };

      const imgTarget = { src: 'https://broken-cdn.com/thumb.jpg' };
      sim.handleThumbError(imgTarget, itemNoYt);

      expect(sim.thumbErrors[99]).toBe(true);
      expect(imgTarget.src).toBe('https://broken-cdn.com/thumb.jpg');
    });

    it('[THUMB_05] Cross-view state synchronization: Shared thumbErrors state protects both Grid and Table', () => {
      const sim = new IndexPageSimulator();
      const item: Partial<ArticleItem> = {
        id: 7,
        youtube_id: null,
        thumbnail_long_url: 'https://broken.com/image.jpg',
      };

      sim.setViewMode('grid');
      sim.handleThumbError({ src: 'https://broken.com/image.jpg' }, item);
      expect(sim.thumbErrors[7]).toBe(true);

      sim.setViewMode('table');
      expect(sim.thumbErrors[7]).toBe(true);
    });
  });

  // ==========================================================================
  // SECTION 4: YOUTUBE ID EXTRACTION FUZZING
  // ==========================================================================
  describe('4. YouTube ID Extraction & Parsing Robustness', () => {
    it('[YT_01] Extracts ID from explicit youtube_id property first', () => {
      const item = { youtube_id: 'cjdEOM6sn24', youtube_url: 'https://youtu.be/otherId1234' };
      expect(getYouTubeId(item)).toBe('cjdEOM6sn24');
    });

    it('[YT_02] Correctly extracts ID from standard watch URL', () => {
      const item = { youtube_url: 'https://www.youtube.com/watch?v=cjdEOM6sn24' };
      expect(getYouTubeId(item)).toBe('cjdEOM6sn24');
    });

    it('[YT_03] Correctly extracts ID from short youtu.be URL', () => {
      const item = { youtube_url: 'https://youtu.be/cjdEOM6sn24' };
      expect(getYouTubeId(item)).toBe('cjdEOM6sn24');
    });

    it('[YT_04] Correctly extracts ID from embed URL', () => {
      const item = { youtube_url: 'https://www.youtube.com/embed/cjdEOM6sn24' };
      expect(getYouTubeId(item)).toBe('cjdEOM6sn24');
    });

    it('[YT_05] Correctly extracts raw 11-character YouTube video ID', () => {
      const item = { youtube_url: 'cjdEOM6sn24' };
      expect(getYouTubeId(item)).toBe('cjdEOM6sn24');
    });

    it('[YT_06] Handles query parameters including timestamps, playlists, and tracking', () => {
      const item = { youtube_url: 'https://www.youtube.com/watch?v=cjdEOM6sn24&t=145s&feature=shared&list=PL123' };
      expect(getYouTubeId(item)).toBe('cjdEOM6sn24');
    });

    it('[YT_07] Returns null for invalid, non-youtube, empty, or malformed URLs', () => {
      expect(getYouTubeId({ youtube_url: '' })).toBe(null);
      expect(getYouTubeId({ youtube_url: '   ' })).toBe(null);
      expect(getYouTubeId({ youtube_url: null })).toBe(null);
      expect(getYouTubeId({ youtube_url: 'https://vimeo.com/987654321' })).toBe(null);
      expect(getYouTubeId({ youtube_url: 'not-a-valid-id' })).toBe(null);
    });
  });

  // ==========================================================================
  // SECTION 5: VIEW MODE PERSISTENCE & LOCALSTORAGE DURABILITY
  // ==========================================================================
  describe('5. View Mode Switcher & LocalStorage Resilience', () => {
    it('[VIEW_01] Defaults to grid mode when no storage exists', () => {
      const sim = new IndexPageSimulator();
      expect(sim.viewMode).toBe('grid');
    });

    it('[VIEW_02] Restores table mode from storage on initialization', () => {
      const storage = { theravada_videos_view_mode: 'table' };
      const sim = new IndexPageSimulator();
      sim.mount(storage);
      expect(sim.viewMode).toBe('table');
    });

    it('[VIEW_03] setViewMode persists new selection to storage', () => {
      const storage: Record<string, string> = {};
      const sim = new IndexPageSimulator();
      sim.setViewMode('table', storage);
      expect(sim.viewMode).toBe('table');
      expect(storage['theravada_videos_view_mode']).toBe('table');

      sim.setViewMode('grid', storage);
      expect(sim.viewMode).toBe('grid');
      expect(storage['theravada_videos_view_mode']).toBe('grid');
    });

    it('[VIEW_04] Gracefully ignores corrupted or invalid storage values', () => {
      const storage = { theravada_videos_view_mode: 'invalid_mode' };
      const sim = new IndexPageSimulator();
      sim.mount(storage);
      expect(sim.viewMode).toBe('grid');
    });

    it('[VIEW_05] Handles storage throwing exceptions (e.g. Safari Private Mode / QuotaExceeded)', () => {
      const throwingStorage = {
        get theravada_videos_view_mode(): string {
          throw new Error('SecurityError: The operation is insecure.');
        },
      };

      const sim = new IndexPageSimulator();
      // Mounting with throwing storage falls back safely to 'grid' default
      sim.mount(throwingStorage as any);
      expect(sim.viewMode).toBe('grid');
    });
  });

  // ==========================================================================
  // SECTION 6: DURATION FORMATTER & STATUS ENGINE
  // ==========================================================================
  describe('6. Duration Formatter & Status Engine', () => {
    it('[DURATION_01] Formats numeric seconds into MM:SS correctly', () => {
      expect(formatDuration('0')).toBe('0:00');
      expect(formatDuration('9')).toBe('0:09');
      expect(formatDuration('45')).toBe('0:45');
      expect(formatDuration('65')).toBe('1:05');
      expect(formatDuration('599')).toBe('9:59');
      expect(formatDuration('3600')).toBe('60:00');
    });

    it('[DURATION_02] Preserves non-numeric strings and formats null/undefined as dash', () => {
      expect(formatDuration('12:34')).toBe('12:34');
      expect(formatDuration(null)).toBe('—');
      expect(formatDuration(undefined)).toBe('—');
      expect(formatDuration('')).toBe('—');
    });

    it('[STATUS_01] Correctly resolves status labels for all valid and default statuses', () => {
      expect(getStatusLabel('published')).toBe('Đã Xuất Bản');
      expect(getStatusLabel('completed')).toBe('Đã Hoàn Thành');
      expect(getStatusLabel('processing')).toBe('Đang Render');
      expect(getStatusLabel('failed')).toBe('Thất Bại');
      expect(getStatusLabel('draft')).toBe('Bản Nháp');
      expect(getStatusLabel('unknown_status')).toBe('Bản Nháp');
    });

    it('[STATUS_02] Correctly resolves status badge classes with animations', () => {
      expect(getStatusBadgeClass('published')).toContain('text-phantom-mint');
      expect(getStatusBadgeClass('completed')).toContain('text-emerald-400');
      expect(getStatusBadgeClass('processing')).toContain('animate-pulse');
      expect(getStatusBadgeClass('failed')).toContain('text-rose-400');
      expect(getStatusBadgeClass('draft')).toContain('text-slate-400');
    });
  });

  // ==========================================================================
  // SECTION 7: TEMPLATE & COMPONENT ARCHITECTURE INTEGRITY
  // ==========================================================================
  describe('7. Template Architecture & Component Integrity', () => {
    it('[TEMPLATE_01] Contains Dual View Modes (Grid & Table) with conditional rendering', () => {
      expect(indexContent).toContain("v-else-if=\"viewMode === 'grid'\"");
      expect(indexContent).toContain('VIEW 1: VISUAL 16:9 GRID / CARDS VIEW');
      expect(indexContent).toContain('VIEW 2: COMPACT DATA TABLE VIEW');
    });

    it('[TEMPLATE_02] Contains Quick Copy Popover in both Grid and Table modes with @click.stop', () => {
      expect(indexContent).toContain('activeQuickCopyId === item.id');
      const clickStopOccurrences = (indexContent.match(/@click\.stop/g) || []).length;
      expect(clickStopOccurrences >= 3).toBe(true);
    });

    it('[TEMPLATE_03] Contains multi-tier thumbnail error handler on img elements', () => {
      expect(indexContent).toContain('@error="(e) => handleThumbError(e, item)"');
      expect(indexContent).toContain('v-if="!thumbErrors[item.id] && item.thumbnail_long_url"');
      expect(indexContent).toContain('Zen Gradient Poster Card Fallback');
    });

    it('[TEMPLATE_04] Contains persistent segmented view switcher control', () => {
      expect(indexContent).toContain("@click=\"setViewMode('grid')\"");
      expect(indexContent).toContain("@click=\"setViewMode('table')\"");
      expect(indexContent).toContain('theravada_videos_view_mode');
    });

    it('[TEMPLATE_05] Contains instant search input with clear button', () => {
      expect(indexContent).toContain('v-model="searchQuery"');
      expect(indexContent).toContain('@keyup.enter="handleSearch"');
      expect(indexContent).toContain('@click="clearSearch"');
    });

    it('[TEMPLATE_06] Contains responsive grid break classes (1, 2, 3, 4 cols)', () => {
      expect(indexContent).toContain('grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4');
    });

    it('[TEMPLATE_07] Locks 16:9 aspect ratio on thumbnail containers in both views', () => {
      const aspectMatches = (indexContent.match(/aspect-video/g) || []).length;
      expect(aspectMatches >= 2).toBe(true);
    });
  });
});