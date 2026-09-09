/**
 * Test Suite: Challenger M1_2 Empirical Adversarial Stress & Verification Suite
 * Focus: resources/js/Pages/Admin/Theravada/Videos/Show.vue
 *
 * Scope:
 * 1. Viewport layout stress test (2-column lg grid, mobile/tablet response, overflow containment)
 * 2. Copy kit generators with null/empty/malformed inputs
 * 3. Form submission reactivity, scroll preservation, and loading state analysis
 * 4. Regex extraction and duration formatting edge cases
 */

import { describe, it, expect } from '../Harness/index.js';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const projectRoot = path.resolve(__dirname, '../..');
const showVuePath = path.join(projectRoot, 'resources/js/Pages/Admin/Theravada/Videos/Show.vue');

// Helper to extract YouTube ID based on Show.vue line 93-99
function extractYtId(url?: string | null): string | null {
  const trimmed = url?.trim();
  if (!trimmed) return null;
  if (/^[a-zA-Z0-9_-]{11}$/.test(trimmed)) return trimmed;
  const match = trimmed.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|shorts\/|watch\?v=|watch\?.+&v=))([\w-]{11})/);
  return match ? match[1] : null;
}

// Helper for hashtags formatting based on Show.vue line 208-216
function formatHashtagsList(raw?: string | null): string[] {
  if (!raw) return ['#TamAnVanSuAn', '#LoiPhatDay', '#Theravada', '#ThienDinh', '#MaToaThien'];
  const parts = raw.split(',').map((t) => t.trim()).filter(Boolean);
  if (parts.length === 0) return ['#TamAnVanSuAn', '#LoiPhatDay', '#Theravada', '#ThienDinh', '#MaToaThien'];
  return parts.map((t) => (t.startsWith('#') ? t : `#${t}`));
}

function formatHashtagsString(raw?: string | null): string {
  return formatHashtagsList(raw).join(' ');
}

// Helper for Copy Kit generators based on Show.vue line 219-267
function generateYoutubeFullKit(form: any, article: any): string {
  const formattedTitle = form.seo_title || article.title || '';
  const formattedDescription = form.seo_description || article.excerpt || '';
  const hashtags = formatHashtagsString(form.hashtags);
  const slug = article.slug || '';

  return [
    `=== TIÊU ĐỀ YOUTUBE ===`,
    formattedTitle,
    ``,
    `=== MÔ TẢ YOUTUBE (SEO & TIMESTAMPS) ===`,
    formattedDescription,
    ``,
    `🎧 Nghe trọn bộ tuyển tập Pháp Thoại tại: https://theravada.macatung.dev`,
    `🔔 Đăng ký kênh Ma Tọa Thiền để đón nhận nguồn năng lượng bình an mỗi ngày.`,
    ``,
    `=== TỪ KHÓA / HASHTAGS ===`,
    hashtags,
    ``,
    `=== BÀI VIẾT NGUYÊN BẢN ===`,
    `https://theravada.macatung.dev/phap-thoai/${slug}`,
  ].join('\n');
}

function generateTiktokFullKit(form: any, article: any): string {
  const formattedCaption = form.social_caption || '';
  const hashtags = formatHashtagsString(form.hashtags);

  return [
    formattedCaption || article.title || '',
    ``,
    `🎧 Nghe trọn bài giảng tại link bio!`,
    hashtags + ' #shorts #reels #xuhuong',
  ].join('\n');
}

function generateReelsFullKit(form: any, article: any): string {
  const formattedCaption = form.social_caption || '';
  const hashtags = formatHashtagsString(form.hashtags);

  return [
    formattedCaption || article.title || '',
    ``,
    `🌿 Lời Phật dạy cho tâm an giữa vạn biến cuộc đời.`,
    `🎵 Âm thanh: Nhạc thiền 432Hz hòa trộn tiếng chuông chánh niệm`,
    hashtags,
  ].join('\n');
}

function generateEmbedSnippet(form: any, article: any): string {
  const ytId = extractYtId(form.youtube_url) || article.youtube_id;
  if (ytId) {
    return `<div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px;">\n  <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" src="https://www.youtube-nocookie.com/embed/${ytId}" title="${article.title || ''}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>\n</div>`;
  }
  if (article.video_long_url) {
    return `<div style="border-radius: 12px; overflow: hidden; max-width: 100%;">\n  <video controls playsinline style="width: 100%; border-radius: 12px;" src="${article.video_long_url}" poster="${article.thumbnail_long_url || ''}"></video>\n</div>`;
  }
  return `<!-- Video chưa sẵn sàng -->`;
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

describe('Challenger M1_2: Show.vue Adversarial Stress & Verification', () => {
  const showContent = fs.readFileSync(showVuePath, 'utf-8');

  // ==========================================================================
  // SECTION 1: VIEWPORT LAYOUT & GRID ARCHITECTURE
  // ==========================================================================
  describe('1. Viewport Layout & Grid Architecture', () => {
    it('[LAYOUT_01] Confirms 2-column lg grid with 12-column system (7 cols left + 5 cols right)', () => {
      expect(showContent).toContain('grid grid-cols-1 lg:grid-cols-12 gap-5 items-start');
      expect(showContent).toContain('lg:col-span-7');
      expect(showContent).toContain('lg:col-span-5');
    });

    it('[LAYOUT_02] Confirms mobile & tablet responsive stacking via default grid-cols-1', () => {
      // Default before lg breakpoint is grid-cols-1, which stacks vertically
      expect(showContent).toContain('grid-cols-1 lg:grid-cols-12');
    });

    it('[LAYOUT_03] Confirms aspect-video ratio lock on media canvas', () => {
      // 16:9 ratio container prevents layout shifts
      expect(showContent).toContain('aspect-video rounded-xl overflow-hidden bg-black');
    });

    it('[LAYOUT_04] Confirms horizontal overflow safety on tabs and pre blocks', () => {
      // Release tabs navigation must have overflow-x-auto and no-scrollbar
      expect(showContent).toContain('overflow-x-auto no-scrollbar');
      // Pre tag for embed code must have overflow-x-auto
      expect(showContent).toContain('overflow-x-auto leading-relaxed border border-white/5');
      // Script view has vertical scroll lock
      expect(showContent).toContain('max-h-[180px] overflow-y-auto');
    });

    it('[LAYOUT_05] Confirms header and footer responsive wrapping', () => {
      expect(showContent).toContain('flex flex-col md:flex-row md:items-center justify-between');
      expect(showContent).toContain('flex items-center gap-2 flex-wrap');
    });
  });

  // ==========================================================================
  // SECTION 2: COPY KIT GENERATORS & FUZZING
  // ==========================================================================
  describe('2. Copy Kit Generators & Fuzzing (Null / Empty / Edge Cases)', () => {
    const defaultArticle = {
      id: 1,
      title: 'Tâm An Vạn Sự An',
      slug: 'tam-an-van-su-an',
      excerpt: 'Bài giảng về an tịnh tâm ý.',
      content: 'Nội dung chi tiết...',
      youtube_id: 'dQw4w9WgXcQ',
      video_long_url: 'https://storage.googleapis.com/test/video.mp4',
    };

    it('[KIT_01] YouTube Kit generates complete structured template with populated fields', () => {
      const form = {
        seo_title: 'Tâm An Vạn Sự An - Bản Gốc',
        seo_description: 'Mô tả chi tiết bài giảng',
        hashtags: '#TamAn, #Theravada',
      };
      const kit = generateYoutubeFullKit(form, defaultArticle);

      expect(kit).toContain('=== TIÊU ĐỀ YOUTUBE ===\nTâm An Vạn Sự An - Bản Gốc');
      expect(kit).toContain('=== MÔ TẢ YOUTUBE (SEO & TIMESTAMPS) ===\nMô tả chi tiết bài giảng');
      expect(kit).toContain('=== TỪ KHÓA / HASHTAGS ===\n#TamAn #Theravada');
      expect(kit).toContain('https://theravada.macatung.dev/phap-thoai/tam-an-van-su-an');
    });

    it('[KIT_02] YouTube Kit handles null/empty form fields by falling back to article defaults', () => {
      const formEmpty = {
        seo_title: '',
        seo_description: '',
        hashtags: '',
      };
      const kit = generateYoutubeFullKit(formEmpty, defaultArticle);

      expect(kit).toContain('=== TIÊU ĐỀ YOUTUBE ===\nTâm An Vạn Sự An');
      expect(kit).toContain('=== MÔ TẢ YOUTUBE (SEO & TIMESTAMPS) ===\nBài giảng về an tịnh tâm ý.');
      // When hashtags empty, falls back to default 5 tags
      expect(kit).toContain('#TamAnVanSuAn #LoiPhatDay #Theravada #ThienDinh #MaToaThien');
      expect(kit).not.toContain('undefined');
      expect(kit).not.toContain('null');
    });

    it('[KIT_03] YouTube Kit survives completely null article and form without throwing', () => {
      const formNull = {
        seo_title: null,
        seo_description: null,
        hashtags: null,
      };
      const articleEmpty = {
        id: 99,
        title: '',
        slug: '',
        excerpt: null,
      };
      const kit = generateYoutubeFullKit(formNull, articleEmpty);

      expect(typeof kit).toBe('string');
      expect(kit).not.toContain('undefined');
      expect(kit).not.toContain('null');
    });

    it('[KIT_04] TikTok Kit falls back to article title when caption is empty', () => {
      const form = { social_caption: '', hashtags: 'xuhuong, thuvien' };
      const kit = generateTiktokFullKit(form, defaultArticle);

      expect(kit).toContain('Tâm An Vạn Sự An');
      expect(kit).toContain('#xuhuong #thuvien #shorts #reels #xuhuong');
      expect(kit).not.toContain('undefined');
    });

    it('[KIT_05] FB & IG Reels Kit formats caption and meditation audio credits cleanly', () => {
      const form = { social_caption: 'Thực hành chánh niệm mỗi ngày', hashtags: '#ChanhNiem' };
      const kit = generateReelsFullKit(form, defaultArticle);

      expect(kit).toContain('Thực hành chánh niệm mỗi ngày');
      expect(kit).toContain('Nhạc thiền 432Hz hòa trộn tiếng chuông chánh niệm');
      expect(kit).toContain('#ChanhNiem');
      expect(kit).not.toContain('undefined');
    });

    it('[KIT_06] Embed Snippet prioritizes YouTube nocookie iframe when YouTube ID present', () => {
      const form = { youtube_url: 'https://youtu.be/dQw4w9WgXcQ' };
      const snippet = generateEmbedSnippet(form, defaultArticle);

      expect(snippet).toContain('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ');
      expect(snippet).toContain('allowfullscreen');
    });

    it('[KIT_07] Embed Snippet falls back to native HTML5 video tag when no YouTube ID', () => {
      const formNoYt = { youtube_url: '' };
      const articleNoYt = {
        ...defaultArticle,
        youtube_id: null,
        video_long_url: 'https://storage.googleapis.com/assets/talk.mp4',
        thumbnail_long_url: 'https://storage.googleapis.com/assets/thumb.jpg',
      };
      const snippet = generateEmbedSnippet(formNoYt, articleNoYt);

      expect(snippet).toContain('<video controls playsinline');
      expect(snippet).toContain('src="https://storage.googleapis.com/assets/talk.mp4"');
      expect(snippet).toContain('poster="https://storage.googleapis.com/assets/thumb.jpg"');
    });

    it('[KIT_08] Embed Snippet shows unready comment when no video sources exist', () => {
      const snippet = generateEmbedSnippet({ youtube_url: '' }, { youtube_id: null, video_long_url: null });
      expect(snippet).toBe('<!-- Video chưa sẵn sàng -->');
    });

    it('[KIT_09] Hashtag formatter prepends # to bare words and ignores commas/whitespace', () => {
      const tags = formatHashtagsList('  pali , #buddha,  , dhamma , ');
      expect(tags).toEqual(['#pali', '#buddha', '#dhamma']);
    });
  });

  // ==========================================================================
  // SECTION 3: YOUTUBE REGEX EXTRACTION & FUZZING
  // ==========================================================================
  describe('3. YouTube URL / ID Extraction Fuzzing', () => {
    it('[YT_01] Extracts 11-char ID from standard watch URL', () => {
      expect(extractYtId('https://www.youtube.com/watch?v=dQw4w9WgXcQ')).toBe('dQw4w9WgXcQ');
    });

    it('[YT_02] Extracts 11-char ID from short youtu.be link', () => {
      expect(extractYtId('https://youtu.be/dQw4w9WgXcQ')).toBe('dQw4w9WgXcQ');
    });

    it('[YT_03] Extracts 11-char ID from YouTube Shorts URL', () => {
      expect(extractYtId('https://www.youtube.com/shorts/dQw4w9WgXcQ?feature=share')).toBe('dQw4w9WgXcQ');
    });

    it('[YT_04] Extracts 11-char ID from embed URL', () => {
      expect(extractYtId('https://www.youtube.com/embed/dQw4w9WgXcQ')).toBe('dQw4w9WgXcQ');
    });

    it('[YT_05] Accepts raw 11-character video ID', () => {
      expect(extractYtId('dQw4w9WgXcQ')).toBe('dQw4w9WgXcQ');
      expect(extractYtId('a1b2c3d4e5f')).toBe('a1b2c3d4e5f');
    });

    it('[YT_06] Gracefully rejects empty, non-youtube, or malformed URLs', () => {
      expect(extractYtId('')).toBeNull();
      expect(extractYtId(null)).toBeNull();
      expect(extractYtId(undefined)).toBeNull();
      expect(extractYtId('   ')).toBeNull();
      expect(extractYtId('https://vimeo.com/12345678901')).toBeNull();
      expect(extractYtId('https://dailymotion.com/video/x7tgad0')).toBeNull();
      expect(extractYtId('<script>alert("xss")</script>')).toBeNull();
      expect(extractYtId('short')).toBeNull();
    });

    it('[YT_07_CHALLENGE] Domain Boundary Vulnerability: Substring match on phishing or spoofed domains', () => {
      // EMPIRICAL CHALLENGE: If a user pastes a spoofed domain containing 'youtube.com' (e.g. 'not-youtube.com' or 'fakeyoutube.com/watch?v=')
      // Show.vue line 97 regex lacks domain boundary (^ or https?://(www\.)?(m\.)?youtube\.com)
      const spoofedDomainUrl = 'https://fake-youtube.com/watch?v=dQw4w9WgXcQ';
      const extracted = extractYtId(spoofedDomainUrl);
      // Documenting exact behavior: the regex matches because 'youtube.com/watch?v=' is a substring
      expect(extracted).toBe('dQw4w9WgXcQ');
    });
  });

  // ==========================================================================
  // SECTION 4: FORM SUBMISSION REACTIVITY & SCROLL PRESERVATION
  // ==========================================================================
  describe('4. Form Submission Reactivity & Scroll Preservation', () => {
    it('[FORM_01] Confirms handleSaveMetadata submits via PUT with preserveScroll: true', () => {
      // Must contain preserveScroll: true inside form.put handler
      expect(showContent).toContain('preserveScroll: true');
      expect(showContent).toContain('form.put(`/admin/theravada/videos/${props.article.id}`');
    });

    it('[FORM_02] Confirms submit buttons have disabled state when form.processing is true', () => {
      // Buttons must have :disabled="form.processing" and disabled:opacity-50
      expect(showContent).toContain(':disabled="form.processing"');
      expect(showContent).toContain('disabled:opacity-50');
    });

    it('[FORM_03] Confirms loading text indicators during form.processing', () => {
      // Form buttons display 'Đang lưu...' or 'Lưu...' while submitting
      expect(showContent).toContain("form.processing ? 'Lưu...' : 'Lưu Link'");
      expect(showContent).toContain("form.processing ? 'Đang lưu...' : (form.recentlySuccessful ? '✓ Đã Lưu!' : '💾 Lưu Thay Đổi')");
    });

    it('[FORM_04] Evaluates loading spinner icon on save button', () => {
      // CRITICAL CHECK: Does save button display an animated spinner?
      // Observations indicate icons use static <Icons name="Save" :size="14" /> or <Icons name="Check" :size="12" />
      const hasSpinningIcon = showContent.includes('animate-spin') || showContent.includes('Loader');
      // Documenting exact empirical behavior: No animated spinner class is attached to save icons
      expect(hasSpinningIcon).toBe(false);
    });

    it('[FORM_05] Confirms watch(props.article) guards against overwriting unsaved dirty input', () => {
      // In Show.vue: if (newArticle && !form.isDirty)
      expect(showContent).toContain('if (newArticle && !form.isDirty)');
    });
  });

  // ==========================================================================
  // SECTION 5: UTILITY FUNCTIONS (DURATION & STATUS)
  // ==========================================================================
  describe('5. Utility Functions Stress Testing', () => {
    it('[UTIL_01] formatDuration formats seconds into mm:ss or falls back gracefully', () => {
      expect(formatDuration('65')).toBe('1:05');
      expect(formatDuration('3600')).toBe('60:00');
      expect(formatDuration('0')).toBe('0:00');
      expect(formatDuration(null)).toBe('—');
      expect(formatDuration(undefined)).toBe('—');
      expect(formatDuration('')).toBe('—');
      expect(formatDuration('12:34')).toBe('12:34');
    });
  });
});
