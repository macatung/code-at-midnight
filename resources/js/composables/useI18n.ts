import { computed, ref } from 'vue';

export type Locale = 'en' | 'vi';

const locale = ref<Locale>('en');
let initialized = false;

const messages: Record<Locale, Record<string, string>> = {
  en: {
    'nav.home': 'Home', 'nav.projects': 'Projects', 'nav.desktop': 'Desktop', 'nav.blog': 'Blog',
    'nav.game': 'Game 🎮', 'nav.talisman': 'Dev Talisman', 'nav.theravada': 'Meditation 🧘',
    'nav.summon': 'Summon', 'nav.summonDeveloper': 'Summon a Developer', 'nav.menu': 'Toggle mobile menu',
    'nav.new': 'NEW', 'nav.hot': 'HOT', 'nav.zen': 'ZEN', 'brand.tagline': 'Code at midnight',
    'footer.backToTop': 'Back to top', 'footer.lore': 'Grimoire & Lore', 'footer.night': 'Midnight Kingdom',
    'footer.philosophy': 'Humanistic Philosophy', 'footer.manifesto': 'Night Code & Manifesto',
    'footer.systems': 'Grimoire Systems', 'footer.labs': 'Labs & Artifacts', 'footer.projectVault': 'Project Grimoire',
    'footer.arcade': 'Rune Typer Arcade', 'footer.forge': 'Talisman Forge', 'footer.tools': 'Tools & Interaction',
    'footer.altar': 'Summoning Altar', 'footer.status': 'System Status', 'footer.admin': 'CMS Admin',
    'footer.crafted': 'Crafted with', 'footer.heartTitle': 'Click to activate the Midnight Love Easter Egg',
    'footer.copyright': '© 2026 macatung.dev — Crafted with Laravel 11, Inertia.js, Vue 3 & Midnight Magic.',
    'common.breadcrumb': 'Breadcrumb', 'common.readMore': 'Read article', 'common.enter': 'Enter this realm',
    'common.continue': 'Continue the journey', 'common.nextRealms': 'Explore the next realms',
    'common.consult': 'Or summon a solution consultation now', 'common.minutes': 'min read', 'common.views': 'views',
    'home.blogTitle': 'Architecture & Tech Notes', 'home.blogDescription': 'Deep dives into autonomous Multi-Agent AI, GIS routing algorithms and high-scale engineering.',
    'home.readAllBlog': 'Read all blog posts', 'home.viewDetails': 'View article details',
    'home.gameTitle': 'Rune Typer: Exorcist Keyboard', 'home.gameDescription': 'Destroy bugs with your keyboard, feel authentic mechanical thock, build x5 combos and hunt Boss Bugs.',
    'home.playGame': 'Play Rune Typer', 'home.talismanTitle': 'Developer Talisman Forge', 'home.talismanDescription': 'Create developer charms and export HD canvas artwork for your laptop or social posts.',
    'home.openForge': 'Open Talisman Forge', 'home.terminalDescription': 'A virtual zsh environment with 11 magic commands, arrow-key history and mechanical typing sounds.',
    'projects.title': 'Project Grimoire', 'projects.breadcrumb': 'Project Grimoire', 'about.breadcrumb': 'Philosophy & Manifesto',
    'contact.breadcrumb': 'Summoning Altar (Contact)', 'talisman.breadcrumb': 'Developer Talisman Forge', 'game.breadcrumb': 'Arcade Chamber',
    'blog.breadcrumb': 'Midnight Tech Chronicle', 'blog.title': 'Architecture & Night Tech Notes', 'blog.description': 'Deep dives into autonomous Multi-Agent AI, high-scale backends, GIS and real-world system design.',
    'blog.search': 'Search posts, technologies...', 'blog.all': 'All topics', 'blog.empty': 'No matching articles',
    'blog.emptyHint': 'Try another keyword or choose “All topics”.', 'blog.read': 'Read article',
    'desktop.badge': 'WINDOWS DESKTOP COMPANION', 'desktop.title': 'Solve tasks faster,', 'desktop.titleAccent': 'right on your desktop.',
    'desktop.description': 'Task Companion is a desktop mascot connected directly to Task Hub. Pick a task, open the right workspace and hand a context pack to Codex, Antigravity or Claude Code.',
    'desktop.download': 'Download for Windows', 'desktop.openHub': 'Open Task Hub', 'desktop.free': 'Windows 10/11 · Free · Auto-updated via GitHub Releases',
    'desktop.workflow': 'From task to review-ready result', 'desktop.changelog': 'View changelog and releases on GitHub →',
    'game.title': 'Arcade Chamber: ', 'game.openTerminal': 'Open Terminal', 'game.viewProjects': 'View Projects', 'game.spellbook': 'Spellbook Reference',
    'talisman.badge': 'Dev Artifact Creator', 'talisman.title': 'Talisman Forge', 'talisman.description': 'Customize bug-banishing charms, midnight deploy protection and high-quality artwork for your laptop or social posts.',
  },
  vi: {
    'nav.home': 'Trang Chủ', 'nav.projects': 'Dự Án', 'nav.desktop': 'Desktop', 'nav.blog': 'Blog',
    'nav.game': 'Game 🎮', 'nav.talisman': 'Bùa Dev', 'nav.theravada': 'Tọa Thiền 🧘', 'nav.summon': 'Triệu Hồi',
    'nav.summonDeveloper': 'Triệu Hồi Lập Trình Viên', 'nav.menu': 'Mở menu di động', 'nav.new': 'MỚI', 'nav.hot': 'HOT', 'nav.zen': 'ZEN',
    'brand.tagline': 'Code lúc nửa đêm', 'footer.backToTop': 'Về đầu trang', 'footer.lore': 'Grimoire & Lore', 'footer.night': 'Vương Quốc Đêm',
    'footer.philosophy': 'Triết Lý Vị Nhân Sinh', 'footer.manifesto': 'Bản Lĩnh Đêm & Manifesto', 'footer.systems': 'Hệ Thống Grimoire',
    'footer.labs': 'Thí Nghiệm & Pháp Bảo', 'footer.projectVault': 'Kho Dự Án Grimoire', 'footer.arcade': 'Phòng Máy Rune Typer', 'footer.forge': 'Lò Luyện Bùa Chú',
    'footer.tools': 'Công Cụ & Tương Tác', 'footer.altar': 'Bàn Thờ Triệu Hồi', 'footer.status': 'Trạng Thái Hệ Thống', 'footer.admin': 'Quản Trị CMS',
    'footer.crafted': 'Crafted with', 'footer.heartTitle': 'Bấm để kích hoạt Midnight Love Easter Egg', 'footer.copyright': '© 2026 macatung.dev — Crafted with Laravel 11, Inertia.js, Vue 3 & Midnight Magic.',
    'common.breadcrumb': 'Breadcrumb', 'common.readMore': 'Xem chi tiết bài viết', 'common.enter': 'Bước vào cõi này', 'common.continue': 'Tiếp tục hành trình', 'common.nextRealms': 'Khám phá các cõi tiếp theo', 'common.consult': 'Hoặc triệu hồi tư vấn giải pháp ngay', 'common.minutes': 'phút đọc', 'common.views': 'lượt đọc',
    'home.blogTitle': 'Ghi Chép Kiến Trúc & Blog', 'home.blogDescription': 'Các bài phân tích chuyên sâu về Multi-Agent AI tự trị, giải thuật GIS và kỹ thuật chịu tải cao.', 'home.readAllBlog': 'Đọc toàn bộ blog', 'home.viewDetails': 'Xem chi tiết bài viết', 'home.gameTitle': 'Rune Typer: Thần Phím Trừ Tà', 'home.gameDescription': 'Gõ phím diệt Bug, cảm nhận âm thanh thock, chuỗi combo x5 và săn Boss Bug.', 'home.playGame': 'Vào chơi Rune Typer', 'home.talismanTitle': 'Lò Rèn Bùa Hộ Mệnh Lập Trình Viên', 'home.talismanDescription': 'Tạo bùa hộ mệnh dev và tải file ảnh HD để dán laptop hoặc chia sẻ.', 'home.openForge': 'Mở lò rèn bùa Dev', 'home.terminalDescription': 'Môi trường zsh ảo với 11 câu lệnh ma thuật, lịch sử phím mũi tên và âm thanh gõ phím cơ học.',
    'projects.title': 'Kho Grimoire Dự Án', 'projects.breadcrumb': 'Kho Dự Án (Grimoire)', 'about.breadcrumb': 'Triết Lý & Tuyên Ngôn (Manifesto)', 'contact.breadcrumb': 'Điện Thờ Triệu Hồi (Contact)', 'talisman.breadcrumb': 'Lò Rèn Bùa Hộ Mệnh', 'game.breadcrumb': 'Phòng Máy Arcade', 'blog.breadcrumb': 'Midnight Tech Chronicle', 'blog.title': 'Ghi Chép Kiến Trúc & Kỹ Thuật Đêm', 'blog.description': 'Các bài viết chuyên sâu về Multi-Agent AI, backend chịu tải cao, GIS và thiết kế hệ thống thực chiến.', 'blog.search': 'Tìm kiếm bài viết, công nghệ...', 'blog.all': 'Tất cả chủ đề', 'blog.empty': 'Chưa có bài viết phù hợp', 'blog.emptyHint': 'Hãy thử từ khóa khác hoặc chọn “Tất cả chủ đề”.', 'blog.read': 'Đọc bài viết',
    'desktop.badge': 'WINDOWS DESKTOP COMPANION', 'desktop.title': 'Giải quyết task nhanh hơn,', 'desktop.titleAccent': 'ngay trên desktop.', 'desktop.description': 'Task Companion là mascot desktop kết nối trực tiếp với Task Hub. Chọn task, mở workspace và giao context pack cho Codex, Antigravity hoặc Claude Code.', 'desktop.download': 'Tải cho Windows', 'desktop.openHub': 'Mở Task Hub', 'desktop.free': 'Windows 10/11 · Miễn phí · Cập nhật tự động qua GitHub Releases', 'desktop.workflow': 'Từ task đến kết quả review', 'desktop.changelog': 'Xem changelog và các bản phát hành trên GitHub →', 'game.title': 'Phòng Máy Arcade: ', 'game.openTerminal': 'Mở Terminal', 'game.viewProjects': 'Xem Dự Án', 'game.spellbook': 'Bí Kíp Thần Chú (Spellbook Reference)', 'talisman.badge': 'Dev Artifact Creator', 'talisman.title': 'Lò Rèn Bùa Hộ Mệnh', 'talisman.description': 'Tùy biến bùa trừ Bug, bảo trợ deploy lúc nửa đêm và xuất ảnh chất lượng cao cho laptop hoặc mạng xã hội.'
  },
};

export function useI18n() {
  if (!initialized && typeof window !== 'undefined') {
    const saved = window.localStorage.getItem('macatung-locale');
    if (saved === 'vi' || saved === 'en') locale.value = saved;
    document.documentElement.lang = locale.value;
    initialized = true;
  }
  const t = (key: string, fallback?: string) => messages[locale.value][key] || fallback || key;
  const setLocale = (next: Locale) => {
    locale.value = next;
    if (typeof window !== 'undefined') {
      window.localStorage.setItem('macatung-locale', next);
      document.documentElement.lang = next;
    }
  };
  return { locale: computed(() => locale.value), t, setLocale };
}
