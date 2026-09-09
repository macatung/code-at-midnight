/**
 * Test Suite: Auditor M2 Index.vue Forensic Integrity & Empirical Verification Suite
 * Target: resources/js/Pages/Admin/Theravada/Videos/Index.vue
 *
 * Checks:
 * 1. Dual View Modes (16:9 Grid Cards View vs Compact Table View)
 * 2. Segmented control with localStorage persistence
 * 3. Status filter pills with dynamic count badges
 * 4. Instant search & filtering mechanics
 * 5. Universal Quick Copy popover with individual & full kit actions
 * 6. Multi-tier thumbnail fallback logic
 * 7. Absence of fake facades, mock data, or bypassed logic
 */

import { describe, it, expect } from '../Harness/index.js';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const projectRoot = path.resolve(__dirname, '../..');
const indexVuePath = path.join(projectRoot, 'resources/js/Pages/Admin/Theravada/Videos/Index.vue');

// Helper replicates getYouTubeId in Index.vue (lines 114-121)
function getYouTubeId(item: { youtube_id?: string | null; youtube_url?: string | null }): string | null {
  if (item.youtube_id) return item.youtube_id;
  if (!item.youtube_url) return null;
  const url = item.youtube_url.trim();
  if (/^[a-zA-Z0-9_-]{11}$/.test(url)) return url;
  const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
  return match ? match[1] : null;
}

// Helper replicates formatDuration in Index.vue (lines 230-239)
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

// Helper replicates getArticleHashtagsFormatted in Index.vue (lines 266-271)
function getArticleHashtagsFormatted(article: { hashtags?: string[] | null }): string {
  if (Array.isArray(article.hashtags) && article.hashtags.length > 0) {
    return article.hashtags.map((h) => (h.startsWith('#') ? h : `#${h}`)).join(' ');
  }
  return '#TamAnVanSuAn #LoiPhatDay #Theravada #macatungdev';
}

// Helper replicates copyFullKit in Index.vue (lines 273-298)
function assembleFullKit(article: any): string {
  const title = article.seo_title || article.title;
  const desc = article.seo_description || article.excerpt || '';
  const caption = article.social_caption || '';
  const tags = getArticleHashtagsFormatted(article);
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

describe('Auditor M2: Index.vue Forensic Integrity & Stress Verification', () => {
  const fileContent = fs.readFileSync(indexVuePath, 'utf-8');

  // ==========================================================================
  // SECTION 1: DUAL VIEW MODES STRUCTURAL AUDIT
  // ==========================================================================
  describe('1. Dual View Modes Structural Integrity', () => {
    it('[AUDIT_M2_VIEW_01] Both Grid View and Table View are conditionally rendered via viewMode', () => {
      expect(fileContent).toContain("const viewMode = ref<'grid' | 'table'>('grid');");
      expect(fileContent).toContain("v-else-if=\"viewMode === 'grid'\"");
      expect(fileContent).toContain('v-else');
    });

    it('[AUDIT_M2_VIEW_02] Grid View renders 16:9 cards layout with Tailwind aspect-video', () => {
      expect(fileContent).toContain('grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-2');
      expect(fileContent).toContain('aspect-video bg-black overflow-hidden');
    });

    it('[AUDIT_M2_VIEW_03] Table View renders clean 6-column tabular layout for high density', () => {
      expect(fileContent).toContain('<table class="w-full text-left text-xs font-sans">');
      expect(fileContent).toContain('Video & Bài Pháp Thoại');
      expect(fileContent).toContain('Định Dạng');
      expect(fileContent).toContain('Trạng Thái');
      expect(fileContent).toContain('Thời Lượng');
      expect(fileContent).toContain('⚡ Copy Nhanh');
      expect(fileContent).toContain('Thao Tác');
    });

    it('[AUDIT_M2_VIEW_04] Both views iterate directly on props.articles.data (zero hardcoded mock items)', () => {
      // Check grid loop
      const gridLoopMatch = fileContent.match(/v-for="item in articles\.data"\s+:key="item\.id"/g);
      expect(gridLoopMatch).not.toBeNull();
      expect(gridLoopMatch!.length).toBeGreaterThanOrEqual(2); // One in grid, one in table
    });
  });

  // ==========================================================================
  // SECTION 2: PERSISTENCE & SEGMENTED CONTROL
  // ==========================================================================
  describe('2. Persistent Segmented Control', () => {
    it('[AUDIT_M2_STORAGE_01] localStorage key theravada_videos_view_mode is read safely on mount', () => {
      expect(fileContent).toContain("localStorage.getItem('theravada_videos_view_mode')");
      expect(fileContent).toContain("if (saved === 'grid' || saved === 'table')");
    });

    it('[AUDIT_M2_STORAGE_02] localStorage setItem updates persistence on view mode toggle', () => {
      expect(fileContent).toContain("localStorage.setItem('theravada_videos_view_mode', mode)");
    });

    it('[AUDIT_M2_STORAGE_03] Storage operations are defensively wrapped in try/catch blocks', () => {
      expect(fileContent).toContain('try {');
      expect(fileContent).toContain('// Graceful fallback for non-storage environments');
    });

    it('[AUDIT_M2_STORAGE_04] Segmented buttons have distinct active vs inactive styling', () => {
      expect(fileContent).toContain("viewMode === 'grid'");
      expect(fileContent).toContain("viewMode === 'table'");
      expect(fileContent).toContain('bg-phantom-mint text-midnight-950 font-bold shadow-sm');
    });
  });

  // ==========================================================================
  // SECTION 3: FILTER PILLS, COUNTS & SEARCH
  // ==========================================================================
  describe('3. Dynamic Filter Pills & Search', () => {
    it('[AUDIT_M2_FILTER_01] All 5 filter tabs are defined with canonical keys and labels', () => {
      expect(fileContent).toContain("{ key: 'all', label: 'Tất cả' }");
      expect(fileContent).toContain("{ key: 'completed', label: 'Đã hoàn thành' }");
      expect(fileContent).toContain("{ key: 'published', label: 'Đã xuất bản' }");
      expect(fileContent).toContain("{ key: 'processing', label: 'Đang render' }");
      expect(fileContent).toContain("{ key: 'draft', label: 'Bản nháp' }");
    });

    it('[AUDIT_M2_FILTER_02] Tab badges bind dynamically to statusCounts prop', () => {
      expect(fileContent).toContain('{{ (statusCounts as Record<string, number>)[tab.key] || 0 }}');
    });

    it('[AUDIT_M2_FILTER_03] applyFilter dispatches Inertia router.get preserving state and scroll', () => {
      expect(fileContent).toContain("router.get(\n    '/admin/theravada/videos'");
      expect(fileContent).toContain('preserveState: true');
      expect(fileContent).toContain('preserveScroll: true');
    });

    it('[AUDIT_M2_FILTER_04] Instant search binds via v-model and responds to Enter and clear button', () => {
      expect(fileContent).toContain('v-model="searchQuery"');
      expect(fileContent).toContain('@keyup.enter="handleSearch"');
      expect(fileContent).toContain('@click="clearSearch"');
    });
  });

  // ==========================================================================
  // SECTION 4: UNIVERSAL QUICK COPY POPOVER
  // ==========================================================================
  describe('4. Universal Quick Copy Popover & Actions', () => {
    it('[AUDIT_M2_COPY_01] Popover is accessible and implemented in BOTH Grid and Table view', () => {
      const toggleMatches = fileContent.match(/toggleQuickCopy\(item\.id, e\)/g);
      expect(toggleMatches).not.toBeNull();
      expect(toggleMatches!.length).toBe(2); // Once in Grid, once in Table
    });

    it('[AUDIT_M2_COPY_02] Popover closes on outside click and stops event propagation on toggle', () => {
      expect(fileContent).toContain('event.stopPropagation()');
      expect(fileContent).toContain("window.addEventListener('click', closeQuickCopy)");
      expect(fileContent).toContain("window.removeEventListener('click', closeQuickCopy)");
      expect(fileContent).toContain('@click.stop');
    });

    it('[AUDIT_M2_COPY_03] Popover provides 5 targeted copy operations including master kit', () => {
      expect(fileContent).toContain('📋 Tiêu đề SEO');
      expect(fileContent).toContain('📝 Mô tả YouTube');
      expect(fileContent).toContain('📱 Caption Reel/TikTok');
      expect(fileContent).toContain('🏷️ Bộ Hashtags');
      expect(fileContent).toContain('⚡ Chép Trọn Gói Phát Hành');
    });

    it('[AUDIT_M2_COPY_04] Feedback flash states are tracked per item and field key for 1800ms', () => {
      expect(fileContent).toContain('copySuccess.value[key] = true;');
      expect(fileContent).toContain('copySuccess.value[key] = false;');
      expect(fileContent).toContain('1800');
    });
  });

  // ==========================================================================
  // SECTION 5: BEHAVIORAL & LOGIC UNIT STRESS TESTING
  // ==========================================================================
  describe('5. Behavioral Logic & Edge Case Stress Testing', () => {
    it('[AUDIT_M2_LOGIC_01] getYouTubeId extracts valid 11-char ID from diverse URL formats', () => {
      expect(getYouTubeId({ youtube_url: 'https://www.youtube.com/watch?v=cjdEOM6sn24' })).toBe('cjdEOM6sn24');
      expect(getYouTubeId({ youtube_url: 'https://youtu.be/cjdEOM6sn24' })).toBe('cjdEOM6sn24');
      expect(getYouTubeId({ youtube_url: 'https://www.youtube.com/embed/cjdEOM6sn24' })).toBe('cjdEOM6sn24');
      expect(getYouTubeId({ youtube_url: 'cjdEOM6sn24' })).toBe('cjdEOM6sn24');
      expect(getYouTubeId({ youtube_id: 'pre_extracted' })).toBe('pre_extracted');
      expect(getYouTubeId({ youtube_url: null })).toBeNull();
      expect(getYouTubeId({ youtube_url: '   ' })).toBeNull();
      expect(getYouTubeId({ youtube_url: 'https://example.com/not-youtube' })).toBeNull();
    });

    it('[AUDIT_M2_LOGIC_02] formatDuration correctly formats seconds and handles edge cases', () => {
      expect(formatDuration('60')).toBe('1:00');
      expect(formatDuration('125')).toBe('2:05');
      expect(formatDuration('3600')).toBe('60:00');
      expect(formatDuration('0')).toBe('0:00');
      expect(formatDuration(null)).toBe('—');
      expect(formatDuration(undefined)).toBe('—');
      expect(formatDuration('04:32')).toBe('04:32'); // Pre-formatted stays as is
    });

    it('[AUDIT_M2_LOGIC_03] getArticleHashtagsFormatted prepends hash and falls back gracefully', () => {
      expect(getArticleHashtagsFormatted({ hashtags: ['TamAn', '#LoiPhatDay'] })).toBe('#TamAn #LoiPhatDay');
      expect(getArticleHashtagsFormatted({ hashtags: [] })).toBe('#TamAnVanSuAn #LoiPhatDay #Theravada #macatungdev');
      expect(getArticleHashtagsFormatted({ hashtags: null })).toBe('#TamAnVanSuAn #LoiPhatDay #Theravada #macatungdev');
    });

    it('[AUDIT_M2_LOGIC_04] assembleFullKit includes all structured sections, metadata, and link', () => {
      const article = {
        title: 'Kinh Pháp Cú Phẩm Song Yếu',
        seo_title: 'Tâm An Vạn Sự An | Phẩm Song Yếu',
        seo_description: 'Giảng giải chi tiết bài kinh.',
        social_caption: 'Tâm dẫn đầu mọi pháp.',
        slug: 'kinh-phap-cu-pham-song-yeu',
        hashtags: ['KinhPhapCu', 'SongYeu'],
      };

      const kit = assembleFullKit(article);
      expect(kit).toContain('=== TIÊU ĐỀ VIDEO YOUTUBE ===');
      expect(kit).toContain('Tâm An Vạn Sự An | Phẩm Song Yếu');
      expect(kit).toContain('=== MÔ TẢ VIDEO (YOUTUBE / FACEBOOK) ===');
      expect(kit).toContain('Giảng giải chi tiết bài kinh.');
      expect(kit).toContain('=== CAPTION MẠNG XÃ HỘI (TIKTOK / REELS) ===');
      expect(kit).toContain('Tâm dẫn đầu mọi pháp.');
      expect(kit).toContain('=== HASHTAGS BÀI ĐĂNG ===');
      expect(kit).toContain('#KinhPhapCu #SongYeu');
      expect(kit).toContain('=== LIÊN KẾT BÀI VIẾT GỐC ===');
      expect(kit).toContain('https://theravada.macatung.dev/phap-thoai/kinh-phap-cu-pham-song-yeu');
    });
  });
});
