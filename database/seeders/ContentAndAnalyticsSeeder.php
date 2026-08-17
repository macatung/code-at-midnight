<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Article;
use App\Models\SiteSetting;
use App\Models\PageView;
use App\Models\AnalyticsEvent;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ContentAndAnalyticsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Skills
        $skills = [
            // Frontend
            ['name' => 'Vue 3 & Inertia.js', 'category' => 'frontend', 'level' => 98, 'rune' => '⚡', 'tag' => 'Core', 'order' => 1],
            ['name' => 'TypeScript Strict', 'category' => 'frontend', 'level' => 95, 'rune' => '🛡️', 'tag' => 'Type-Safe', 'order' => 2],
            ['name' => 'TailwindCSS 3/4', 'category' => 'frontend', 'level' => 96, 'rune' => '🎨', 'tag' => 'Styling', 'order' => 3],
            ['name' => 'Web Audio API', 'category' => 'frontend', 'level' => 92, 'rune' => '🎵', 'tag' => 'Audio', 'order' => 4],
            ['name' => 'Canvas 2D Physics', 'category' => 'frontend', 'level' => 88, 'rune' => '✨', 'tag' => 'Graphics', 'order' => 5],

            // Backend
            ['name' => 'Laravel 11 / PHP 8.3', 'category' => 'backend', 'level' => 96, 'rune' => '🏰', 'tag' => 'Architecture', 'order' => 6],
            ['name' => 'Redis Caching & Lock', 'category' => 'backend', 'level' => 94, 'rune' => '⚡', 'tag' => 'High-Load', 'order' => 7],
            ['name' => 'PostgreSQL / SQLite', 'category' => 'backend', 'level' => 90, 'rune' => '💾', 'tag' => 'Database', 'order' => 8],
            ['name' => 'REST & WebSocket APIs', 'category' => 'backend', 'level' => 93, 'rune' => '🔌', 'tag' => 'Realtime', 'order' => 9],

            // Cloud & DevOps
            ['name' => 'Docker & Microservices', 'category' => 'cloud', 'level' => 88, 'rune' => '🐳', 'tag' => 'Containers', 'order' => 10],
            ['name' => 'CI/CD GitHub Actions', 'category' => 'cloud', 'level' => 90, 'rune' => '🔄', 'tag' => 'Automation', 'order' => 11],
            ['name' => 'Linux Server & Nginx', 'category' => 'cloud', 'level' => 87, 'rune' => '🐧', 'tag' => 'Infra', 'order' => 12],
            ['name' => 'Cloudflare & CDN', 'category' => 'cloud', 'level' => 91, 'rune' => '🌐', 'tag' => 'Edge', 'order' => 13],

            // AI & Automation
            ['name' => 'Gemini & OpenAI API', 'category' => 'ai', 'level' => 89, 'rune' => '🧠', 'tag' => 'LLM', 'order' => 14],
            ['name' => 'Telegram Bot SDK', 'category' => 'ai', 'level' => 94, 'rune' => '🤖', 'tag' => 'Bot', 'order' => 15],
        ];

        foreach ($skills as $s) {
            Skill::updateOrCreate(['name' => $s['name']], $s);
        }

        // 2. Seed Experiences
        $experiences = [
            [
                'role' => 'Lead Systems Architect & Tech Lead',
                'company' => 'Midnight Engineering Lab',
                'period' => '2024 — Hiện Tại',
                'type' => 'Full-Time / Lead',
                'location' => 'Remote / HCMC',
                'summary' => 'Chủ trì kiến trúc các ứng dụng web phân tán, thiết kế hệ thống thanh toán tải cao và tối ưu hóa trải nghiệm tương tác Web Audio.',
                'achievements' => [
                    'Kiến trúc hệ thống Flash sale chịu tải 10,000+ RPS với độ trễ phản hồi < 45ms',
                    'Dẫn dắt chuyển dịch toàn bộ frontend sang Vue 3 Composition API & TypeScript strict mode',
                    'Đạt cam kết 99.99% uptime trong 2 năm liên tiếp không sự cố gián đoạn dịch vụ'
                ],
                'technologies' => ['Laravel 11', 'Vue 3', 'Redis', 'PostgreSQL', 'Docker', 'Web Audio API'],
                'order' => 1,
            ],
            [
                'role' => 'Senior Full-Stack Developer',
                'company' => 'Apex Digital Solutions',
                'period' => '2021 — 2024',
                'type' => 'Full-Time',
                'location' => 'Ho Chi Minh City',
                'summary' => 'Phát triển các cổng SaaS Multi-Tenant, xây dựng hệ thống thanh toán tự động và đồng bộ thời gian thực.',
                'achievements' => [
                    'Tự động hóa quy trình xuất hóa đơn điện tử phục vụ hơn 50,000 khách hàng doanh nghiệp',
                    'Tối ưu hóa chỉ số Google Lighthouse Core Web Vitals đạt điểm số tuyệt đối 100/100',
                    'Xây dựng webhook xử lý thông báo Telegram tức thì với hàng đợi bất đồng bộ'
                ],
                'technologies' => ['Laravel', 'Vue.js', 'Inertia.js', 'MySQL', 'TailwindCSS', 'REST APIs'],
                'order' => 2,
            ],
            [
                'role' => 'Indie Hacker & Creative Frontend Engineer',
                'company' => 'Open Source & Midnight Labs',
                'period' => '2018 — 2021',
                'type' => 'Open-Source & Freelance',
                'location' => 'Remote',
                'summary' => 'Khởi tạo các công cụ nguồn mở cho cộng đồng developer, xây dựng giao diện ma thuật và bot tự động hóa.',
                'achievements' => [
                    'Phát hành bộ công cụ Talisman Forge đạt hơn 15,000 lượt tạo bùa trên GitHub',
                    'Đóng góp các module tối ưu âm thanh Web Audio và Canvas physics cho cộng đồng lập trình',
                    'Hợp tác cùng hơn 20 startup trong và ngoài nước xây dựng các MVP tốc độ cao'
                ],
                'technologies' => ['JavaScript / TypeScript', 'Canvas 2D', 'Web Audio', 'PHP', 'TailwindCSS'],
                'order' => 3,
            ]
        ];

        foreach ($experiences as $exp) {
            Experience::updateOrCreate(['role' => $exp['role'], 'company' => $exp['company']], $exp);
        }

        // 3. Seed Articles / Midnight Tech Notes
        $articles = [
            [
                'title' => 'Tại sao tôi chọn Code at 00:00 AM? — Nghệ Thuật Của Vùng Tĩnh Lặng',
                'slug' => 'tai-sao-code-at-midnight',
                'excerpt' => 'Khám phá trạng thái Ultra-Flow khi lập trình ban đêm, cách loại bỏ 100% xao nhãng và tăng năng suất tư duy kiến trúc.',
                'content' => "## Khi thành phố ngủ, dòng code mới thực sự bừng tỉnh\n\nLập trình ban đêm không chỉ là một thói quen, đó là sự lựa chọn về **vùng tĩnh lặng nhận thức (Cognitive Quietness)**. Không có thông báo Slack, không có email ngắt quãng, não bộ được tự do bơi trong không gian thuật toán 3 chiều thuần khiết.\n\n### 3 Yếu tố giúp phiên Code đêm hiệu quả:\n1. **Robusta nguyên chất**: Kích thích sóng não Alpha.\n2. **Âm thanh tần số thấp**: Tiếng gõ phím cơ và nhạc Ambient 432Hz.\n3. **Kiến trúc trước khi gõ mã**: Vẽ sơ đồ luồng dữ liệu trước khi implement.",
                'tags' => ['Philosophy', 'Productivity', 'Midnight'],
                'reading_time_min' => 4,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'Tối Ưu Cổng Thanh Toán Flash Sale Với Redis Lock Trong Laravel 11',
                'slug' => 'toi-uu-flash-sale-redis-lock-laravel-11',
                'excerpt' => 'Cách sử dụng Atomic Lock và Redis Queue để ngăn chặn hoàn toàn hiện tượng oversell khi hàng ngàn người dùng bấm mua cùng một giây.',
                'content' => "## Bài toán Race Condition trong Flash Sale\n\nKhi 1,000 request cùng ập vào database để kiểm tra `stock > 0`, truy vấn SQL truyền thống rất dễ dẫn đến việc bán vượt quá số lượng tồn kho (Oversell).\n\n```php\n// Sử dụng Atomic Lock trong Redis\n\$lock = Cache::lock('product_checkout_' . \$productId, 5);\nif (\$lock->get()) {\n    try {\n        // Giảm tồn kho an toàn\n    } finally {\n        \$lock->release();\n    }\n}\n```\n\nGiải pháp này giúp hệ thống đạt độ trễ < 45ms và tỉ lệ lỗi 0.00%.",
                'tags' => ['Laravel', 'Redis', 'High-Load', 'Architecture'],
                'reading_time_min' => 6,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
            ]
        ];

        foreach ($articles as $art) {
            Article::updateOrCreate(['slug' => $art['slug']], $art);
        }

        // 4. Seed Site Settings
        $settings = [
            'site_name' => 'macatung.dev',
            'site_title' => 'macatung.dev — Code at midnight',
            'slogan' => 'Code at midnight',
            'hero_subtitle' => 'Kỹ Sư Hệ Thống & Creative Full-Stack Engineer. Chuyển hóa Robusta nguyên chất thành kiến trúc phân tán siêu tốc độ và giao diện web mượt mà.',
            'contact_email' => 'dev@macatung.dev',
            'telegram_username' => '@macatung_dev',
            'github_url' => 'https://github.com/macatung',
            'linkedin_url' => 'https://linkedin.com',
            'resume_download_url' => '/brand/macatung-logo-horizontal.png',
            'seo_description' => 'Portfolio chính thức của Ma Cà Tưng — Lead Systems Architect & Creative Full-Stack Engineer.',
            'admin_password' => 'macatung@midnight2026',
        ];

        foreach ($settings as $key => $val) {
            SiteSetting::set($key, $val, 'Cấu hình hệ thống');
        }
    }
}
