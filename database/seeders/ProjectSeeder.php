<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'slug' => 'ecommerce-flashsale-checkout',
                'title' => 'FlashPay — Nền Tảng Checkout & Thanh Toán Tải Cao',
                'tagline' => 'Cổng thanh toán tự động VietQR / Stripe với Redis Queue giữ chỗ chống oversell',
                'description' => 'Hệ thống e-commerce checkout tối ưu chuyển đổi cao cho các sự kiện flash sale. Xử lý đồng thời hàng nghìn đơn hàng mỗi phút với Redis Lock phân tán và kiểm soát tồn kho theo thời gian thực.',
                'category' => 'fullstack',
                'cover_gradient' => 'from-emerald-950 via-teal-900 to-slate-950',
                'tags' => ['Laravel 11', 'Vue 3', 'Redis Queue', 'VietQR', 'TailwindCSS'],
                'tech_stack' => ['Laravel 11', 'Vue 3 (Inertia)', 'Redis', 'PostgreSQL', 'TailwindCSS', 'Docker'],
                'metrics' => [
                    ['label' => 'Tốc Độ Xử Lý', 'value' => '< 45ms / đơn'],
                    ['label' => 'Uptime Chiến Dịch', 'value' => '99.99%'],
                    ['label' => 'Tỉ Lệ Oversell', 'value' => '0.00%'],
                ],
                'architecture_highlights' => [
                    'Xử lý hàng đợi bất đồng bộ với Redis Queue và workers tự động cân bằng tải',
                    'Cơ chế Idempotency Key bảo đảm không bao giờ trừ tiền hay tạo đơn trùng lặp',
                    'Webhook xác thực thanh toán tức thì với độ trễ phản hồi sub-second'
                ],
                'midnight_fact' => 'Tối ưu lại truy vấn SQL lúc 2:00 AM giúp giảm 80% tải Database trong đợt sale Black Friday.',
                'live_url' => 'https://macatung.dev',
                'github_url' => 'https://github.com/macatung/flashpay-checkout',
                'featured' => true,
                'order' => 1,
            ],
            [
                'slug' => 'dev-grimoire-snippet-vault',
                'title' => 'DevGrimoire — Kho Lưu Trữ & Ghi Chú Kỹ Thuật',
                'tagline' => 'Ứng dụng ghi chú Markdown, quản lý Code Snippet & tài liệu kỹ thuật tìm kiếm tức thì',
                'description' => 'Nền tảng quản lý kiến thức dành cho lập trình viên. Hỗ trợ soạn thảo Markdown thời gian thực, tô màu cú pháp 40+ ngôn ngữ, phân loại theo tag và tìm kiếm full-text siêu nhanh.',
                'category' => 'tools',
                'cover_gradient' => 'from-purple-950 via-indigo-900 to-slate-950',
                'tags' => ['Vue 3', 'TypeScript', 'TailwindCSS', 'SQLite', 'Markdown'],
                'tech_stack' => ['Vue 3 Composition API', 'TypeScript', 'Pinia', 'SQLite', 'TailwindCSS', 'Vite'],
                'metrics' => [
                    ['label' => 'Tốc Độ Tìm Kiếm', 'value' => '< 5ms'],
                    ['label' => 'Hỗ Trợ Ngôn Ngữ', 'value' => '45+ Stacks'],
                    ['label' => 'Dung Lượng Bundle', 'value' => '< 60 KB'],
                ],
                'architecture_highlights' => [
                    'Bộ phân tích cú pháp AST Markdown nhẹ nhàng, render mượt mà 60 FPS',
                    'Hỗ trợ chế độ Offline-First lưu trữ đồng bộ cục bộ IndexedDB / SQLite',
                    'Tích hợp phím tắt Command Palette (Cmd+K / Ctrl+K) chuẩn Pro Developer'
                ],
                'midnight_fact' => 'Viết toàn bộ bộ máy tìm kiếm fuzzy search trong 1 đêm tĩnh lặng.',
                'live_url' => 'https://macatung.dev',
                'github_url' => 'https://github.com/macatung/dev-grimoire-vault',
                'featured' => true,
                'order' => 2,
            ],
            [
                'slug' => 'web-audio-sound-studio',
                'title' => 'Midnight Audio FX — Bộ Tổng Hợp Âm Thanh Web',
                'tagline' => 'Synthesizer âm thanh procedural trực tiếp trên trình duyệt bằng Web Audio API thuần',
                'description' => 'Bộ công cụ tạo hiệu ứng âm thanh click phím cơ, tiếng hop mascot, âm báo notification mà không cần tải bất kỳ file MP3/WAV nào, giúp tối ưu 100% tốc độ tải trang.',
                'category' => 'creative',
                'cover_gradient' => 'from-teal-950 via-slate-900 to-midnight-950',
                'tags' => ['Web Audio API', 'Canvas 2D', 'TypeScript', 'Procedural Audio'],
                'tech_stack' => ['Web Audio API', 'HTML5 Canvas', 'TypeScript', 'Vue 3'],
                'metrics' => [
                    ['label' => 'Dung Lượng Asset', 'value' => '0 KB Audio'],
                    ['label' => 'Tần Số Lấy Mẫu', 'value' => '48 kHz'],
                    ['label' => 'Độ Trễ Âm Thanh', 'value' => '< 2ms'],
                ],
                'architecture_highlights' => [
                    'Sử dụng các OscillatorNode hình sin, tam giác kết hợp GainNode Envelope ADSR',
                    'Bộ lọc BiquadFilterNode tạo hiệu ứng cộng hưởng âm thanh analog ấm áp',
                    'Visualizer sóng âm thời gian thực vẽ trên Canvas 2D mượt mà'
                ],
                'midnight_fact' => 'Bạn đang nghe trực tiếp bộ engine này trên website macatung.dev khi bấm nút hoặc tương tác với linh vật!',
                'live_url' => 'https://macatung.dev',
                'github_url' => 'https://github.com/macatung/web-audio-fx-studio',
                'featured' => true,
                'order' => 3,
            ],
            [
                'slug' => 'multitenant-saas-billing-portal',
                'title' => 'SaaS Pulse — Cổng Quản Lý Gói & Xuất Hóa Đơn',
                'tagline' => 'Hệ thống quản lý Subscription, phân quyền thành viên và tự động xuất hóa đơn PDF',
                'description' => 'Bộ khung quản trị SaaS tinh gọn cho các startup phần mềm. Tích hợp quản lý người dùng, phân quyền Role/Permission, quản lý chu kỳ thanh toán và tạo báo cáo doanh thu trực quan.',
                'category' => 'fullstack',
                'cover_gradient' => 'from-amber-950 via-yellow-950 to-slate-950',
                'tags' => ['Laravel 11', 'Inertia.js', 'Vue 3', 'MySQL', 'PDF Export'],
                'tech_stack' => ['Laravel 11', 'Inertia Vue 3', 'MySQL', 'TailwindCSS', 'DomPDF'],
                'metrics' => [
                    ['label' => 'Thời Gian Xuất PDF', 'value' => '< 200ms'],
                    ['label' => 'Tiết Kiệm Thời Gian', 'value' => '15h / tuần'],
                    ['label' => 'Chuẩn Bảo Mật', 'value' => 'SOC2 Ready'],
                ],
                'architecture_highlights' => [
                    'Kiến trúc Single Database Multi-Tenant với Global Scope lọc dữ liệu an toàn',
                    'Hệ thống cấp phát API Token quản lý giới hạn Rate Limiting theo gói',
                    'Xuất hóa đơn điện tử chuẩn format in ấn với CSS Paged Media'
                ],
                'midnight_fact' => 'Hoàn thành tính năng tự động gia hạn subscription ngay trước thềm năm mới.',
                'live_url' => 'https://macatung.dev',
                'github_url' => 'https://github.com/macatung/saas-billing-portal',
                'featured' => false,
                'order' => 4,
            ],
            [
                'slug' => 'talisman-generator-cli',
                'title' => 'Talisman Forge & CLI — Bùa Lập Trình Viên Open Source',
                'tagline' => 'Bộ công cụ tạo bùa chú 0-bug cho Developer, xuất mã ASCII và SVG tương tác',
                'description' => 'Dự án mã nguồn mở mang lại niềm vui cho cộng đồng lập trình viên. Cho phép tùy biến bùa chú theo ngôn ngữ lập trình, đóng dấu Khai Quang và chia sẻ mã ASCII vào file README.',
                'category' => 'creative',
                'cover_gradient' => 'from-rose-950 via-pink-950 to-slate-950',
                'tags' => ['Vue 3', 'Canvas Confetti', 'ASCII Art', 'Open Source'],
                'tech_stack' => ['Vue 3 Composition API', 'TypeScript', 'Canvas Confetti', 'TailwindCSS'],
                'metrics' => [
                    ['label' => 'Lượt Khai Quang', 'value' => '15,000+'],
                    ['label' => 'Số Bản Mẫu', 'value' => '6 Presets'],
                    ['label' => 'Độ Vui Vẻ', 'value' => '100%'],
                ],
                'architecture_highlights' => [
                    'Trình xuất ASCII chuẩn Unicode tương thích với mọi terminal và GitHub Markdown',
                    'Hiệu ứng pháo hoa nhẹ nhàng tối ưu không gây giật lag trình duyệt',
                    'Hệ thống quản lý trạng thái Reactive đơn giản, không phụ thuộc thư viện nặng'
                ],
                'midnight_fact' => 'Ý tưởng bắt nguồn từ 1 đêm fix bug crash lúc 3:00 AM và ước có lá bùa hộ mệnh.',
                'live_url' => 'https://macatung.dev/#talisman',
                'github_url' => 'https://github.com/macatung/talisman-forge',
                'featured' => false,
                'order' => 5,
            ],
            [
                'slug' => 'telegram-ai-support-bot',
                'title' => 'NightOwl Bot — Trợ Lý Chăm Sóc Khách Hàng 24/7',
                'tagline' => 'Telegram Webhook Bot tích hợp AI LLM, lưu trữ lịch sử và tự động chuyển tiếp admin',
                'description' => 'Bot chăm sóc khách hàng tự động giải đáp thắc mắc dịch vụ, phân loại câu hỏi và bắn thông báo tức thì về nhóm quản trị khi phát hiện khách hàng VIP hoặc yêu cầu khẩn cấp.',
                'category' => 'ai-web3',
                'cover_gradient' => 'from-cyan-950 via-blue-950 to-slate-950',
                'tags' => ['Laravel Queue', 'Telegram Bot API', 'OpenAI / Gemini', 'Webhooks'],
                'tech_stack' => ['Laravel 11', 'Telegram SDK', 'Gemini API', 'Redis Queue', 'SQLite'],
                'metrics' => [
                    ['label' => 'Thời Gian Phản Hồi', 'value' => '< 1.5s'],
                    ['label' => 'Tỉ Lệ Giải Quyết', 'value' => '88%'],
                    ['label' => 'Hoạt Động', 'value' => '24/7/365'],
                ],
                'architecture_highlights' => [
                    'Xử lý Telegram Webhook qua Laravel Queue tránh bị timeout từ Telegram server',
                    'Quản lý context hội thoại thông minh với giới hạn token tối ưu chi phí',
                    'Bộ lọc từ khóa cảnh báo khẩn cấp (Emergency Dispatcher) báo ngay cho Admin'
                ],
                'midnight_fact' => 'Bot đã cứu 1 sự cố của khách hàng lúc 4:00 AM trước khi team thức dậy.',
                'live_url' => 'https://macatung.dev',
                'github_url' => 'https://github.com/macatung/nightowl-telegram-bot',
                'featured' => false,
                'order' => 6,
            ],
        ];

        foreach ($projects as $data) {
            Project::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
