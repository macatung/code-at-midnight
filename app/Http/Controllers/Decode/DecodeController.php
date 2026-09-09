<?php

namespace App\Http\Controllers\Decode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DecodeController extends Controller
{
    /**
     * Pilot Season Episodes Collection
     */
    protected array $pilotEpisodes = [
        [
            'id' => 1,
            'episode_number' => '01',
            'slug' => 'tap-01-quet-the-visa-100k-2-giay-du-hanh',
            'title' => 'Quẹt thẻ Visa 100k: 2 giây du hành qua nửa vòng Trái Đất',
            'short_title' => 'Quẹt thẻ Visa 100k',
            'duration' => '12:45',
            'total_latency' => '1.85s',
            'category' => 'Fintech & Networks',
            'summary' => 'Máy POS → Ngân hàng thanh toán (Acquirer) → Mạng lưới VisaNet → Hệ thống AI Fraud Detection → Ngân hàng phát hành (Issuer). Toàn bộ hành trình xuyên đại dương chỉ trong chớp mắt.',
            'hook' => 'Bạn vừa chạm thẻ vào máy POS tại quán cà phê. Chưa kịp nhấp một ngụm nước, tiếng "Bíp!" đã vang lên. Trong đúng 1.85 giây đó, thông tin tài khoản của bạn đã bay qua đáy đại dương tới bang Virginia (Mỹ), vượt qua hàng trăm chốt kiểm duyệt AI chống gian lận và quay trở lại in biên lai.',
            'key_nodes' => [
                [
                    'step' => 1,
                    'time' => 'T + 0.00s',
                    'node' => 'Máy POS & Thẻ Chip EMV',
                    'protocol' => 'ISO/IEC 7816 & EMV Contactless',
                    'action' => 'Đọc vi mạch chip thẻ, tạo chữ ký số động ARQC (Application Request Cryptogram) dùng một lần để chống sao chép thẻ.',
                    'latency' => '0.15s',
                    'status' => 'Encrypted'
                ],
                [
                    'step' => 2,
                    'time' => 'T + 0.25s',
                    'node' => 'Ngân hàng Thanh Toán (Acquirer Bank)',
                    'protocol' => 'ISO 8583 Message Standard',
                    'action' => 'Tiếp nhận gói tin qua đường truyền leased-line mã hóa, kiểm tra danh tính đơn vị chấp nhận thẻ (Merchant ID), đẩy gói tin vào mạng Visa.',
                    'latency' => '0.20s',
                    'status' => 'Verified'
                ],
                [
                    'step' => 3,
                    'time' => 'T + 0.55s',
                    'node' => 'Mạng Lưới Toàn Cầu VisaNet',
                    'protocol' => 'Dual Redundant Fiber Backbone',
                    'action' => 'Định tuyến gói tin qua cáp quang xuyên Thái Bình Dương đến Data Center Ashburn (Virginia) hoặc Highlands Ranch (Colorado). Xử lý cùng lúc 65.000 giao dịch/giây.',
                    'latency' => '0.35s',
                    'status' => 'Routed'
                ],
                [
                    'step' => 4,
                    'time' => 'T + 0.95s',
                    'node' => 'Hệ Thống AI Chống Gian Lận (Visa Advanced Authorization)',
                    'protocol' => 'Realtime Neural Network Engine',
                    'action' => 'Đánh giá 500 thuộc tính rủi ro trong 1.2 mili-giây: Tọa độ POS, tần suất chi tiêu, lịch sử hành vi, rủi ro thẻ bị đánh cắp. Cho điểm rủi ro từ 1 đến 99.',
                    'latency' => '0.05s',
                    'status' => 'Risk Score: 03 (Safe)'
                ],
                [
                    'step' => 5,
                    'time' => 'T + 1.15s',
                    'node' => 'Ngân Hàng Phát Hành Thẻ (Issuer Bank)',
                    'protocol' => 'Core Banking HSM Module',
                    'action' => 'Giải mã ARQC qua phần cứng bảo mật HSM, kiểm tra số dư khả dụng, phong tỏa 100.000 VNĐ và sinh mã ARPC (Application Response Cryptogram).',
                    'latency' => '0.35s',
                    'status' => 'Authorized'
                ],
                [
                    'step' => 6,
                    'time' => 'T + 1.85s',
                    'node' => 'Máy POS In Biên Lai (Thành Công)',
                    'protocol' => 'Thermal Printer Output',
                    'action' => 'Gói tin chấp thuận bay ngược về máy POS, xác thực mã ARPC với chip thẻ, phát tiếng BÍP và in hóa đơn thanh toán thành công.',
                    'latency' => '0.20s',
                    'status' => 'Approved (00)'
                ]
            ],
            'tags' => ['VisaNet', 'ISO 8583', 'EMV Chip', 'AI Fraud Detection', 'Core Banking'],
            'status' => 'Ready'
        ],
        [
            'id' => 2,
            'episode_number' => '02',
            'slug' => 'tap-02-google-tim-kiem-50-ty-trang-web-0-3-giay',
            'title' => 'Google tìm kiếm: Lục tung 50 tỷ trang web trong 0.3 giây bằng cách nào?',
            'short_title' => 'Google Search 0.3s',
            'duration' => '14:20',
            'total_latency' => '0.28s',
            'category' => 'Distributed Systems & Algorithms',
            'summary' => 'Web Crawler (Spider) → Inverted Index (chỉ mục ngược) → PageRank & Vector Embeddings → Cụm máy chủ phân tán toàn cầu (Distributed Sharding).',
            'hook' => 'Bạn gõ vào thanh tìm kiếm một cụm từ bất kỳ và ấn Enter. Trong 0.3 giây, Google không hề chạy đi quét từng trang web trên thế giới. Bí mật nằm ở một "cuốn từ điển ngược" khổng lồ đã được biên dịch sẵn.',
            'key_nodes' => [
                [
                    'step' => 1,
                    'time' => 'Tiền xử lý (Offline)',
                    'node' => 'Googlebot Spider',
                    'protocol' => 'Distributed Web Crawler',
                    'action' => 'Bò qua hàng tỷ liên kết mỗi ngày, tải nội dung HTML và bóc tách văn bản thô.',
                    'latency' => '24/7 Ongoing',
                    'status' => 'Indexed'
                ],
                [
                    'step' => 2,
                    'time' => 'Tiền xử lý (Offline)',
                    'node' => 'Inverted Index Engine',
                    'protocol' => 'Lexicon Sharding',
                    'action' => 'Lập chỉ mục ngược: Thay vì lưu URL chứa từ gì, Google lưu mỗi từ khóa xuất hiện ở những URL nào và vị trí số mấy.',
                    'latency' => 'PB-Scale DB',
                    'status' => 'Sharded'
                ],
                [
                    'step' => 3,
                    'time' => 'T + 0.05s',
                    'node' => 'DNS & Anycast Routing',
                    'protocol' => 'BGP Anycast',
                    'action' => 'Định tuyến truy vấn của người dùng đến Edge Pop máy chủ Google gần nhất.',
                    'latency' => '0.05s',
                    'status' => 'Connected'
                ],
                [
                    'step' => 4,
                    'time' => 'T + 0.12s',
                    'node' => 'Truy vấn Song Song Hàng Ngàn Shards',
                    'protocol' => 'MapReduce / Bigtable',
                    'action' => 'Băm câu hỏi của bạn và bắn song song tới hàng nghìn máy chủ chỉ mục, tìm giao điểm tập hợp kết quả trong 10ms.',
                    'latency' => '0.07s',
                    'status' => 'Merged'
                ],
                [
                    'step' => 5,
                    'time' => 'T + 0.28s',
                    'node' => 'RankBrain & Trả Kết Quả SERP',
                    'protocol' => 'Neural Semantic Ranking',
                    'action' => 'Chấm điểm bằng PageRank, độ tươi của tin, ngữ cảnh cá nhân hóa và trả về danh sách 10 kết quả hàng đầu.',
                    'latency' => '0.11s',
                    'status' => 'Rendered'
                ]
            ],
            'tags' => ['Inverted Index', 'PageRank', 'MapReduce', 'Anycast', 'RankBrain'],
            'status' => 'In Production'
        ],
        [
            'id' => 3,
            'episode_number' => '03',
            'slug' => 'tap-03-cuoc-goi-xuyen-luc-dia-cap-quang-day-bien',
            'title' => 'Cuộc gọi xuyên lục địa: Dữ liệu chui qua đáy biển như thế nào?',
            'short_title' => 'Cáp quang đáy biển',
            'duration' => '13:10',
            'total_latency' => '120ms',
            'category' => 'Global Infrastructure',
            'summary' => 'Hệ thống cáp quang ngầm đáy biển, tàu rải cáp chuyên dụng, vỏ bọc thép chống cá mập cắn và trạm trắc địa cập bờ (Landing Stations).',
            'hook' => '99% lưu lượng Internet quốc tế không phải đi qua vệ tinh như nhiều người lầm tưởng, mà đang bò dưới đáy đại dương ở độ sâu 8.000 mét, né tránh hàm răng của cá mập và mỏ neo tàu biển.',
            'key_nodes' => [
                [
                    'step' => 1,
                    'time' => 'Điểm khởi đầu',
                    'node' => 'Laser DWDM Bộ Phát Quang',
                    'protocol' => 'Dense Wavelength Division Multiplexing',
                    'action' => 'Bắn hàng trăm bước sóng ánh sáng khác nhau qua sợi thủy tinh mảnh như sợi tóc, đạt dung lượng hàng trăm Terabit/giây.',
                    'latency' => '0.01ms',
                    'status' => 'Light Pulse'
                ],
                [
                    'step' => 2,
                    'time' => 'Dưới biển sâu',
                    'node' => 'Trạm Lặp Quang Học (Optical Repeater)',
                    'protocol' => 'Erbium-Doped Fiber Amplifier (EDFA)',
                    'action' => 'Cứ mỗi 60km dưới đáy biển, một khối hợp kim titan chứa laser kích thích ion Erbium để phóng đại lại chùm sáng đã suy hao.',
                    'latency' => 'Continuous',
                    'status' => 'Amplified'
                ],
                [
                    'step' => 3,
                    'time' => 'Bảo vệ',
                    'node' => 'Lớp Giáp Thép & Polyethylene',
                    'protocol' => 'Double Armor Shielding',
                    'action' => 'Gần bờ cáp dày bằng bắp tay với 2 lớp giáp thép chống mỏ neo, xuống biển sâu cáp chỉ bằng ngón tay cái.',
                    'latency' => 'Passive',
                    'status' => 'Shielded'
                ],
                [
                    'step' => 4,
                    'time' => 'Cập bờ',
                    'node' => 'Cable Landing Station (Vũng Tàu / Quy Nhơn)',
                    'protocol' => 'Subsea-to-Terrestrial Handover',
                    'action' => 'Nơi sợi cáp chui từ biển lên đất liền, cấp nguồn điện 10.000 Volt dọc theo cáp và chuyển mạch sang mạng cáp quang quốc gia.',
                    'latency' => '0.05ms',
                    'status' => 'Connected'
                ]
            ],
            'tags' => ['Submarine Cable', 'DWDM', 'Repeater EDFA', 'Landing Station', 'Ocean Engineering'],
            'status' => 'Drafting'
        ],
        [
            'id' => 4,
            'episode_number' => '04',
            'slug' => 'tap-04-cay-atm-khong-bao-gio-nha-nham-tien',
            'title' => 'Cây ATM: Làm sao nó biết ví bạn còn bao nhiêu tiền và không bao giờ nhả nhầm?',
            'short_title' => 'Cây ATM rút tiền',
            'duration' => '11:50',
            'total_latency' => '3.50s',
            'category' => 'Hardware & Embedded Security',
            'summary' => 'Giao thức chuyển mạch liên ngân hàng (NDC/DDC), cảm biến hồng ngoại đo micromet độ dày từng tờ tiền, và két sắt Safe Vault chống nổ.',
            'hook' => 'Máy ATM có thể đếm 50 tờ tiền trong 3 giây và nhả ra cho bạn mà không bao giờ nhầm lẫn 2 tờ dính vào nhau. Làm sao cỗ máy đứng giữa ngã tư đường biết bạn còn bao nhiêu tiền trong thẻ?',
            'key_nodes' => [
                [
                    'step' => 1,
                    'time' => 'Xác thực',
                    'node' => 'Bàn phím EPP (Encrypting PIN Pad)',
                    'protocol' => 'Hardware Security Module (HSM)',
                    'action' => 'Mã PIN được mã hóa 3DES ngay bên trong bàn phím trước khi gửi lên CPU máy tính của cây ATM, chống nghe lén tuyệt đối.',
                    'latency' => '0.02s',
                    'status' => 'Encrypted'
                ],
                [
                    'step' => 2,
                    'time' => 'Chuyển mạch',
                    'node' => 'Switch Quốc Gia (Napas / Interbank Switch)',
                    'protocol' => 'ISO 8583 Banking Switch',
                    'action' => 'Hỏi số dư từ ngân hàng gốc của bạn và trả về lệnh đồng ý giải ngân tiền mặt.',
                    'latency' => '1.20s',
                    'status' => 'Authorized'
                ],
                [
                    'step' => 3,
                    'time' => 'Bóc tách tiền',
                    'node' => 'Bộ Nhả Tiền & Cảm Biến Quang Học',
                    'protocol' => 'IR Thickness Sensor',
                    'action' => 'Con lăn búng từng tờ tiền. Tia hồng ngoại đo độ dày chính xác đến micromet; nếu phát hiện 2 tờ dính nhau sẽ tự động đẩy vào khay Reject Cassette.',
                    'latency' => '1.50s',
                    'status' => '0 Error'
                ],
                [
                    'step' => 4,
                    'time' => 'An toàn kép',
                    'node' => 'Cửa Nhả Tiền & Cơ Chế Thu Hồi Tự Động',
                    'protocol' => 'Two-Phase Commit Transaction',
                    'action' => 'Sau 30 giây bạn không rút tiền, máy tự động nuốt lại vào hộc giữ tiền và hoàn tiền vào tài khoản.',
                    'latency' => '30s Timeout',
                    'status' => 'Protected'
                ]
            ],
            'tags' => ['ATM Hardware', 'EPP Keyboard', 'Optical Sensor', 'Napas Switch', 'Safe Vault'],
            'status' => 'Drafting'
        ],
        [
            'id' => 5,
            'episode_number' => '05',
            'slug' => 'tap-05-bam-dat-grab-ve-tinh-gps-thuyet-tuong-doi-einstein',
            'title' => 'Bấm đặt Grab: 24 vệ tinh ngoài vũ trụ và Thuyết tương đối của Einstein',
            'short_title' => 'Grab, GPS & Einstein',
            'duration' => '15:30',
            'total_latency' => '0.80s',
            'category' => 'Physics & Geolocation Algorithms',
            'summary' => 'Cách GPS định vị bằng độ trễ thời gian nanogiây, và tại sao nếu không sửa sai số thuyết tương đối, Grab sẽ chỉ bạn rơi xuống sông.',
            'hook' => 'Bạn đứng ở vỉa hè bấm đặt xe, ứng dụng Grab chấm đúng chấm xanh nơi bạn đứng. Để làm được điều đó, điện thoại của bạn phải "nói chuyện" với 4 vệ tinh bay cách Trái Đất 20.000km và áp dụng công thức của Albert Einstein.',
            'key_nodes' => [
                [
                    'step' => 1,
                    'time' => 'Ngoài vũ trụ',
                    'node' => '24 Vệ Tinh GPS MEO & Đồng Hồ Nguyên Tử',
                    'protocol' => 'Atomic Clock (Rubidium / Cesium)',
                    'action' => 'Mỗi vệ tinh liên tục phát sóng mang tín hiệu thời gian chính xác đến từng phần tỷ giây (nanosecond).',
                    'latency' => 'Speed of Light',
                    'status' => 'Broadcasting'
                ],
                [
                    'step' => 2,
                    'time' => 'Vật lý hiện đại',
                    'node' => 'Hiệu Ứng Thuyết Tương Đối Einstein',
                    'protocol' => 'Special & General Relativity',
                    'action' => 'Chuyển động nhanh làm đồng hồ chậm 7μs/ngày; trọng lực yếu ngoài không gian làm đồng hồ chạy nhanh 45μs/ngày. Bù trừ chênh lệch +38 microgiây/ngày (nếu không bù, vị trí sai 11km mỗi ngày).',
                    'latency' => '+38μs/day correction',
                    'status' => 'Corrected'
                ],
                [
                    'step' => 3,
                    'time' => 'Mặt đất',
                    'node' => 'Định Vị Giao Thoa 4 Vệ Tinh (Trilateration)',
                    'protocol' => 'Sphere Intersection Math',
                    'action' => 'Điện thoại giải phương trình giao thoa 4 mặt cầu không gian-thời gian để tính ra tọa độ kinh độ, vĩ độ và độ cao.',
                    'latency' => '0.05s',
                    'status' => 'Locked (±3m)'
                ],
                [
                    'step' => 4,
                    'time' => 'Server Grab',
                    'node' => 'Thuật Toán Ghép Xe & Bản Đồ H3 Spatial Index',
                    'protocol' => 'Uber H3 Hexagonal Grid Matching',
                    'action' => 'Băm tọa độ của bạn thành lục giác H3, quét bán kính 2km tìm tài xế có thời gian đón nhanh nhất và điều hướng tránh kẹt xe.',
                    'latency' => '0.65s',
                    'status' => 'Matched'
                ]
            ],
            'tags' => ['GPS Satellites', 'Einstein Relativity', 'Atomic Clock', 'Trilateration', 'H3 Spatial Index'],
            'status' => 'Drafting'
        ]
    ];

    /**
     * Decode Portal Home
     */
    public function index(): Response
    {
        return Inertia::render('Decode/Index', [
            'episodes' => $this->pilotEpisodes,
            'featuredEpisode' => $this->pilotEpisodes[0],
            'channel' => [
                'name' => 'Ma Giải Mã',
                'handle' => '@MaGiaiMa',
                'subdomain' => 'decode.macatung.dev',
                'tagline' => 'Mở nắp những hệ thống vô hình vận hành thế giới.',
                'sub_tagline' => 'Giải mã cách thế giới công nghệ thực sự chạy dưới nắp capô.',
                'video_format' => 'Isometric 3D & CAD Blueprint Architecture',
                'pilot_season_episodes_count' => count($this->pilotEpisodes),
            ],
        ]);
    }

    /**
     * Episode Detail & Breakdown
     */
    public function show(string $slug): Response
    {
        $episode = collect($this->pilotEpisodes)->firstWhere('slug', $slug);

        if (!$episode) {
            // Fallback to episode 1 if slug not found
            $episode = $this->pilotEpisodes[0];
        }

        return Inertia::render('Decode/Show', [
            'episode' => $episode,
            'allEpisodes' => collect($this->pilotEpisodes)->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'slug' => $item['slug'],
                    'episode_number' => $item['episode_number'],
                    'short_title' => $item['short_title'],
                    'title' => $item['title'],
                    'total_latency' => $item['total_latency'],
                ];
            })->all(),
        ]);
    }

    /**
     * Shortcut to Episode 1
     */
    public function episode1(): Response
    {
        return $this->show($this->pilotEpisodes[0]['slug']);
    }

    /**
     * Interactive Brand Identity Kit
     */
    public function brand(): Response
    {
        return Inertia::render('Decode/Brand', [
            'brand' => [
                'name' => 'Ma Giải Mã',
                'handle' => '@MaGiaiMa',
                'subdomain' => 'decode.macatung.dev',
                'tagline' => 'Mở nắp những hệ thống vô hình vận hành thế giới.',
                'sub_tagline' => 'Giải mã cách thế giới công nghệ thực sự chạy dưới nắp capô.',
                'colors' => [
                    ['name' => 'Cyber Obsidian', 'hex' => '#060913', 'desc' => 'Nền tối sâu thẳm chuẩn studio đồ họa'],
                    ['name' => 'Blueprint Slate', 'hex' => '#0a1122', 'desc' => 'Nền panel HUD, thẻ bài và container CAD'],
                    ['name' => 'Laser Cyan', 'hex' => '#00f5d4', 'desc' => 'Tia laser soi vi mạch và bùa mạch in'],
                    ['name' => 'Optical Blue', 'hex' => '#00b4d8', 'desc' => 'Màu xanh thấu kính quang học hiển vi'],
                    ['name' => 'Blueprint Navy', 'hex' => '#0077b6', 'desc' => 'Đường nét kết cấu bản vẽ kỹ thuật'],
                    ['name' => 'Laser Amber', 'hex' => '#ffb703', 'desc' => 'Điểm nóng dữ liệu & hạt nhân vi xử lý'],
                    ['name' => 'Glitch Red', 'hex' => '#ff0054', 'desc' => 'Cảnh báo lỗi & nút Subscribe YouTube'],
                ],
                'typography' => [
                    ['role' => 'Display & Headlines', 'font' => 'Space Grotesk / Syne', 'usage' => 'Tiêu đề video, tên kênh, số tập nổi bật'],
                    ['role' => 'Telemetry & HUD Data', 'font' => 'JetBrains Mono', 'usage' => 'Thời gian mili-giây, giao thức, địa chỉ hex, thông số'],
                    ['role' => 'Body & Prose', 'font' => 'Plus Jakarta Sans / Inter', 'usage' => 'Lời bình, kịch bản thuyết minh, chú giải'],
                ],
                'mascot_concept' => [
                    'title' => 'The Systems Anatomist (Kỹ Sư Mổ Xẻ Hệ Thống)',
                    'action' => 'Tư thế bóc tách mở nắp vi xử lý 3D trong suốt, giải phóng dòng ánh sáng dữ liệu',
                    'talisman' => 'Bùa mạch in mica trong suốt (PCB Ribbon) phát quang neon xanh, khắc cổng logic AND/OR & mã DECODE',
                    'gear' => 'Kính vi phân quang học HUD Monocle phóng đại 1000X có tâm ngắm laser và thông số telemetry trực tiếp',
                    'robe' => 'Áo gấm Đạo sĩ vi mạch thêu vệt mạch in bán dẫn và thước đo kỹ thuật CAD',
                ]
            ],
        ]);
    }

    /**
     * XML Sitemap
     */
    public function sitemap()
    {
        $baseUrl = 'https://decode.macatung.dev';
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Home
        $xml .= '<url><loc>' . $baseUrl . '/</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>';
        // Brand Kit
        $xml .= '<url><loc>' . $baseUrl . '/brand-kit</loc><changefreq>monthly</changefreq><priority>0.8</priority></url>';
        
        // Episodes
        foreach ($this->pilotEpisodes as $ep) {
            $xml .= '<url><loc>' . $baseUrl . '/tap/' . $ep['slug'] . '</loc><changefreq>weekly</changefreq><priority>0.9</priority></url>';
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Robots.txt
     */
    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nSitemap: https://decode.macatung.dev/sitemap.xml\n";
        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
