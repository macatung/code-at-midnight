<?php

namespace App\Http\Controllers\Decode;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DecodeController extends Controller
{
    /**
     * Pilot Season Episodes Collection (Long-form Technical Articles)
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
            'category' => 'Fintech & Payment Systems',
            'published_at' => '09/09/2026',
            'reading_time' => '15 phút đọc',
            'author' => 'Ma Cà Tưng (@macatung)',
            'series_title' => 'Series Pilot Season: Mở Nắp Những Hệ Thống Vô Hình Vận Hành Thế Giới',
            'summary' => 'Máy POS → Ngân hàng thanh toán (Acquirer) → Mạng lưới VisaNet → Hệ thống AI Fraud Detection → Ngân hàng phát hành (Issuer). Toàn bộ hành trình xuyên đại dương chỉ trong chớp mắt.',
            'hook' => 'Bạn vừa chạm thẻ vào máy POS tại quán cà phê. Chưa kịp nhấp một ngụm nước, tiếng "Bíp!" đã vang lên. Trong đúng 1.85 giây đó, thông tin tài khoản của bạn đã bay qua đáy đại dương tới bang Virginia (Mỹ), vượt qua hàng trăm chốt kiểm duyệt AI chống gian lận và quay trở lại in biên lai.',
            'takeaways' => [
                'Thẻ chip EMV không bao giờ gửi số thẻ tĩnh (PAN) nguyên bản; vi mạch chip sinh chữ ký mật mã động ARQC dùng một lần cho mỗi giao dịch.',
                'Giao thức ISO 8583 là xương sống liên ngân hàng toàn cầu suốt 40 năm qua, tối ưu từng bit dữ liệu qua cấu trúc Primary/Secondary Bitmap.',
                'VisaNet vận hành cụm trung tâm dữ liệu Active-Active đồng bộ tại Ashburn (Virginia) và Highlands Ranch (Colorado), chịu tải hơn 65.000 TPS.',
                'Hệ thống AI Visa Advanced Authorization (VAA) phán quyết rủi ro giao dịch dựa trên 500 biến số chỉ trong 1.2 mili-giây.',
                'Quá trình thanh toán tách biệt hoàn toàn giữa Cấp phép tức thời (Authorization - 2 giây) và Quyết toán tiền tệ (Clearing & Settlement - diễn ra vào phiên nửa đêm).'
            ],
            'sections' => [
                [
                    'id' => 'emv-chip-pos',
                    'title' => '1. Vi Mạch Thẻ EMV & Giao Thức Không Tiếp Xúc NFC',
                    'lead' => 'Khoảnh khắc bạn áp thẻ vào máy POS, một phiên đối thoại mật mã cấp độ quân sự diễn ra trong phạm vi chưa đầy 4 cm.',
                    'content' => "Khi thẻ tiếp xúc trường điện từ 13.56 MHz phát ra từ ăng-ten của máy POS, cuộn cảm ứng vi mô bên trong thẻ tiếp nhận năng lượng qua hiện tượng cảm ứng điện từ và đánh thức bộ vi xử lý Secure Element (SE) của thẻ. Không như dải băng từ cổ điển vốn chỉ lưu số thẻ (PAN) và ngày hết hạn dưới dạng chuỗi nhị phân lộ thiên rất dễ bị thiết bị skimmer sao chép, chip EMV (Europay, Mastercard, Visa theo tiêu chuẩn ISO/IEC 7816) là một máy tính mini hoàn chỉnh có hệ điều hành nhúng riêng (Java Card hoặc Multos).\n\nSau khi máy POS gửi bản tin SELECT AID (Application Identifier) để nhận diện thẻ Visa, máy POS cấp cho chip một số ngẫu nhiên gọi là Unpredictable Number (UN). Chip thẻ sử dụng khóa bí mật đối xứng 3DES hoặc AES (Session Key sinh từ Master Derivation Key lưu trong phân vùng phần cứng chống can thiệp) kết hợp với số dư giao dịch, mã tiền tệ (704 đối với VNĐ) và số UN để tính toán ra mã chứng thực ARQC (Application Request Cryptogram). Mỗi lần quẹt thẻ sinh ra một chuỗi ARQC hoàn toàn khác nhau; nếu kẻ gian nghe lén gói tin vô tuyến thì mã này cũng không thể tái sử dụng (Replay Attack bị vô hiệu hóa hoàn toàn).",
                    'callout' => [
                        'type' => 'info',
                        'title' => 'Tại sao dải băng từ bị khai tử?',
                        'body' => 'Băng từ hoạt động giống băng cát-sét: thông tin ghi chết trên các hạt từ tính. Skimmer chỉ cần đọc một lần là clone được 1.000 thẻ giả. Với chip EMV, không có khóa bí mật nào rời khỏi chip thẻ.'
                    ],
                    'code_snippet' => [
                        'language' => 'bash',
                        'filename' => 'emv_apdu_handshake.log',
                        'code' => ">> POS -> CHIP: 00 A4 04 00 07 A0000000031010 (SELECT AID: Visa Credit/Debit)\n<< CHIP -> POS: 6F 28 84 07 A0000000031010 A5 1D 50 0B 56 49 53 41 20 44 45 42 49 54\n>> POS -> CHIP: 80 AE 80 00 1D (GENERATE AC - Request ARQC)\n<< CHIP -> POS: 77 1E 9F 26 08 4F 8D 21 9C 0A 7B 83 E1 (ARQC Cryptogram) 9F 36 02 01 4A (ATC)"
                    ]
                ],
                [
                    'id' => 'iso-8583-message',
                    'title' => '2. Chuẩn Bản Tin ISO 8583 & Ngân Hàng Thanh Toán (Acquirer)',
                    'lead' => 'Chiếc máy POS đóng gói dữ liệu thẻ thành bản tin tài chính ISO 8583 và gửi qua đường truyền Leased-line tới Ngân hàng chấp nhận thanh toán.',
                    'content' => "Tại máy POS của quán cà phê, giao dịch được đóng gói thành bản tin ISO 8583 với mã định danh MTID (Message Type Identifier) là 0100 – tức Bản Tin Yêu Cầu Cấp Phép (Authorization Request). Điểm thiên tài của chuẩn ISO 8583 ra đời từ năm 1987 nằm ở cơ chế Bitmap nhị phân: để tiết kiệm từng byte băng thông trên đường truyền modem dial-up ngày xưa, bản tin không dùng JSON hay XML rườm rà. Thay vào đó, một chuỗi 64 bit (Primary Bitmap) được dùng làm cờ hiệu: bit thứ N bằng 1 nghĩa là trường dữ liệu thứ N có mặt trong bản tin.\n\nNgân hàng thanh toán (Acquirer Bank - ví dụ VietinBank hoặc Techcombank đặt máy POS tại quán) tiếp nhận gói tin, bóc tách Merchant ID để xác định cửa hàng có tài khoản hợp lệ, rồi chuyển tiếp bản tin tới cổng kết nối thanh toán quốc tế của VisaNet qua kênh truyền riêng biệt.",
                    'callout' => [
                        'type' => 'tip',
                        'title' => 'Giải mã các trường ISO 8583 trọng yếu',
                        'body' => 'Field 3: Mã loại giao dịch (000000 = Mua hàng). Field 4: Số tiền (000000100000 = 100.000 VNĐ). Field 11: Số STAN (System Trace Audit Number). Field 55: Khối dữ liệu EMV chứa mã ARQC.'
                    ],
                    'code_snippet' => [
                        'language' => 'json',
                        'filename' => 'iso8583_authorization_0100.json',
                        'code' => "{\n  \"MTID\": \"0100\",\n  \"PrimaryBitmap\": \"F23C400108A18000\",\n  \"Field_003_ProcessingCode\": \"000000\",\n  \"Field_004_Amount\": \"000000100000\",\n  \"Field_007_TransmissionDateTime\": \"0909091530\",\n  \"Field_011_STAN\": \"849201\",\n  \"Field_041_CardAcceptorTerminalID\": \"POS-VN-8821\",\n  \"Field_049_CurrencyCode\": \"704\",\n  \"Field_055_ICCData\": \"9F26084F8D219C0A7B83E19F3602014A...\"\n}"
                    ]
                ],
                [
                    'id' => 'visanet-datacenter',
                    'title' => '3. Siêu Mạng Lưới VisaNet & Cuộc Đua Ánh Sáng Đáy Biển',
                    'lead' => 'Gói tin rời Việt Nam, lướt qua hàng ngàn kilomet cáp quang xuyên Thái Bình Dương để đến đại bản doanh Visa tại Ashburn, Virginia.',
                    'content' => "Gói tin của bạn gia nhập VisaNet – một trong những mạng lưới thanh toán tư nhân an toàn và lớn nhất hành tinh. Visa vận hành các trung tâm dữ liệu siêu bảo mật ngầm kiên cố, tiêu biểu là Ashburn (Virginia) và Highlands Ranch (Colorado). Hai trung tâm này hoạt động theo kiến trúc Active-Active phân tán: bất kỳ cụm nào cũng có thể gánh toàn bộ tải toàn cầu nếu trung tâm kia gặp thảm họa tự nhiên hay bị tấn công.\n\nTốc độ ánh sáng trong lõi sợi thủy tinh là khoảng 200.000 km/giây (bằng 2/3 tốc độ ánh sáng trong chân không do chiết suất n ≈ 1.468). Với quãng đường vật lý xấp xỉ 25.000 km khứ hồi từ Việt Nam sang bờ đông nước Mỹ, độ trễ lan truyền ánh sáng vật lý thuần túy (Propagation Delay) đã chiếm khoảng 125 đến 150 mili-giây. Mọi tiến trình tính toán thuật toán định tuyến chỉ được phép diễn ra trong tích tắc mili-giây còn lại.",
                    'callout' => [
                        'type' => 'important',
                        'title' => 'Thông lượng khủng khiếp của VisaNet',
                        'body' => 'VisaNet có năng lực xử lý vượt mức 65.000 tin nhắn giao dịch mỗi giây (TPS). Vào các ngày hội mua sắm như Black Friday hay Tết Nguyên Đán, hệ thống đạt độ tin cậy 99.999% không gián đoạn.'
                    ]
                ],
                [
                    'id' => 'ai-fraud-scoring',
                    'title' => '4. Trí Tuệ Nhân Tạo Visa Advanced Authorization (VAA)',
                    'lead' => 'Tại trung tâm dữ liệu, cỗ máy AI phân tích hơn 500 tham số hành vi chỉ trong vỏn vẹn 1.2 mili-giây.',
                    'content' => "Trước khi chuyển gói tin đến ngân hàng phát hành thẻ của bạn, VisaNet kích hoạt mạng nơ-ron sâu của hệ thống Visa Advanced Authorization (VAA). Trong 1.2 mili-giây, mô hình máy học đối chiếu giao dịch 100.000 VNĐ này với mô hình hành vi lịch sử:\n\n- Tọa độ địa lý của máy POS có bất thường không (Ví dụ: Thẻ vừa rút tiền tại TP.HCM 30 phút trước, nay lại quẹt tại Hà Nội)?\n- Tần suất chi tiêu trong 1 giờ qua (Velocity Check)?\n- Ngành hàng của điểm bán (Merchant Category Code - MCC 5814: Quán ăn nhanh/Cà phê) có phù hợp thói quen thường nhật của chủ thẻ?\n- Mức độ uy tín của thiết bị POS và dải IP của Acquirer?\n\nAI tính toán và gán một điểm số rủi ro (Risk Score) từ 01 (cực kỳ an toàn) đến 99 (rủi ro gian lận cực cao) đính kèm vào bản tin trước khi đẩy tiếp sang Ngân hàng phát hành.",
                    'code_snippet' => [
                        'language' => 'text',
                        'filename' => 'vaa_risk_score_matrix.txt',
                        'code' => "INPUT ATTRIBUTES: 500+ dynamic signals evaluated in parallel\n- Location Delta: 0 km (Normal)\n- Spend Deviation: +12% from average ticket size (Normal)\n- Merchant Category: MCC 5814 (Fast Food / Coffee)\n- Device Velocity: 1 tx / 4 hours\n=> COMPUTED RISK SCORE: 03 (Confidence: 99.98% - Approve Recommended)"
                    ]
                ],
                [
                    'id' => 'core-banking-settlement',
                    'title' => '5. Ngân Hàng Phát Hành (Issuer) & Phép Màu Tách Rời Cấp Phép / Quyết Toán',
                    'lead' => 'Ngân hàng của bạn kiểm tra số dư qua phần cứng HSM và đưa ra phán quyết cuối cùng.',
                    'content' => "Ngân hàng phát hành thẻ (Issuer Bank - ví dụ Vietcombank) nhận bản tin MTID 0100. Hệ thống phần cứng bảo mật HSM (Hardware Security Module) của ngân hàng giải mã trường Field 55, dùng Master Key kiểm tra chữ ký ARQC của chip thẻ có trùng khớp không. Nếu chuẩn xác, Core Banking kiểm tra số dư tài khoản hoặc hạn mức tín dụng khả dụng.\n\nNếu số dư đủ 100.000 VNĐ, ngân hàng không lập tức chuyển tiền đi ngay. Ngân hàng chỉ thực hiện lệnh Phong Tỏa (Hold / Pre-authorization) 100.000 VNĐ trên tài khoản của bạn, sinh mã phản hồi ARPC (Application Response Cryptogram) và tạo mã chấp thuận Authorization Code (Approval 00). Bản tin phản hồi MTID 0110 bay ngược hành trình về máy POS, thẻ chip xác thực ARPC, phát tiếng 'Bíp!' và máy in nhiệt in hóa đơn thanh toán thành công.\n\nTiền thực sự chỉ được chuyển giao giữa ngân hàng Issuer và Acquirer vào lúc 23:00 đêm qua quá trình Bù trừ & Quyết toán (Clearing & Settlement), khi các ngân hàng đối soát danh sách giao dịch hàng loạt (Batch File) qua cổng Ngân hàng Trung ương.",
                    'callout' => [
                        'type' => 'warning',
                        'title' => 'Bài học kiến trúc phân tán',
                        'body' => 'Bằng cách phân tách bài toán làm hai nửa: Đồng bộ tức thì cho Authorization (nhanh, khóa số dư) và Bất đồng bộ hàng loạt cho Settlement (chậm, bù trừ tài chính), hệ thống ngân hàng toàn cầu đã giải quyết triệt để nghịch lý độ trễ trong hệ phân tán.'
                    ]
                ]
            ],
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
            'category' => 'Search Infrastructure & Distributed Systems',
            'published_at' => '10/09/2026',
            'reading_time' => '18 phút đọc',
            'author' => 'Ma Cà Tưng (@macatung)',
            'series_title' => 'Series Pilot Season: Mở Nắp Những Hệ Thống Vô Hình Vận Hành Thế Giới',
            'summary' => 'Web Crawler (Spider) → Inverted Index (chỉ mục ngược) → PageRank & Vector Embeddings → Cụm máy chủ phân tán toàn cầu (Distributed Sharding).',
            'hook' => 'Bạn gõ vào thanh tìm kiếm một cụm từ bất kỳ và ấn Enter. Trong 0.3 giây, Google không hề chạy đi quét từng trang web trên thế giới. Bí mật nằm ở một "cuốn từ điển ngược" khổng lồ đã được biên dịch sẵn.',
            'takeaways' => [
                'Google không bao giờ duyệt web thời gian thực khi bạn tìm kiếm; công cụ tìm kiếm chỉ truy vấn trên bản sao Inverted Index khổng lồ nằm sẵn trong bộ nhớ RAM.',
                'Inverted Index cấu trúc theo mô hình Lexicon (từ khóa) → Posting List (danh sách ID tài liệu chứa từ khóa) với kỹ thuật nén SIMD-BP128 siêu mật độ.',
                'Kiến trúc phân tán Scatter-Gather phân bổ một truy vấn tới hàng ngàn máy chủ Shard song song, ép toàn bộ việc tính toán phải xong trong Latency Budget 150ms.',
                'Kỹ thuật Hedged Requests và Tail Tolerance loại bỏ độ trễ của 1% máy chủ chậm chạp (99th percentile tail latency).',
                'Kết quả trả về là sự kết hợp giữa thuật toán liên kết đồ thị PageRank cổ điển, điểm số BM25 và mô hình Transformer nơ-ron sâu (RankEmbed/MUM).'
            ],
            'sections' => [
                [
                    'id' => 'googlebot-crawling',
                    'title' => '1. Googlebot & Cỗ Máy Đào Xới Web 24/7 (Crawling & Parsing)',
                    'lead' => 'Trước khi bạn gõ một chữ cái, hàng vạn máy chủ Googlebot đã liên tục tải và lập bản đồ thế giới mạng.',
                    'content' => "Internet là một đồ thị có hướng khổng lồ với các nút là trang web và các cạnh là siêu liên kết (hyperlinks). Googlebot vận hành kiến trúc Mercator Crawler phân tán cao độ. Bộ điều phối URL Frontier quản lý hàng tỷ liên kết cần duyệt, cân đối giữa hai mục tiêu: Khám phá trang mới (Discovery Crawl) và cập nhật trang cũ (Freshness Crawl).\n\nKhi tải một trang HTML về, Google kích hoạt một cụm trình duyệt Chrome không đầu (Headless Chromium) để thực thi toàn bộ mã JavaScript, hydrate DOM của các ứng dụng Single Page Application (SPA), trích xuất văn bản thô và áp dụng thuật toán băm nhận dạng SimHash để loại bỏ các bản sao nội dung trùng lặp (Near-duplicate Detection).",
                    'callout' => [
                        'type' => 'info',
                        'title' => 'Khái niệm Crawl Budget',
                        'body' => 'Googlebot không bao giờ cào dữ liệu mù quáng làm sập máy chủ webmaster. Nó tính toán Crawl Budget dựa trên tốc độ phản hồi của server máy chủ đích và độ uy tín nội dung.'
                    ]
                ],
                [
                    'id' => 'inverted-index-anatomy',
                    'title' => '2. Giải Phẫu Chỉ Mục Ngược (Inverted Index): Cuốn Từ Điển Của Nhân Loại',
                    'lead' => 'Nếu sách giáo khoa có mục lục tra cứu từ khóa ở trang cuối, thì Google chính là cuốn sách với toàn bộ nội dung được biến thành chỉ mục ngược.',
                    'content' => "Trong cơ sở dữ liệu quan hệ truyền thống, bạn lưu: Document ID → Nội dung văn bản. Để tìm từ 'Visa', bạn phải quét tuần tự toàn bộ bảng (Full Table Scan). Với 50 tỷ trang web, truy vấn này sẽ mất vài tiếng đồng hồ.\n\nChỉ mục ngược (Inverted Index) đảo ngược hoàn toàn cấu trúc: Từ Khóa (Term) → Posting List (Danh sách các Document ID chứa từ khóa đó cùng với vị trí và tần suất xuất hiện). Khi bạn tìm kiếm 'visa vietcombank', bài toán tìm kiếm trở thành phép tính giao nhau (Intersection) giữa hai danh sách số nguyên: PostingList('visa') ∩ PostingList('vietcombank').\n\nĐể nén hàng chục tỷ số nguyên này vào RAM máy chủ, Google sử dụng các thuật toán nén Delta Encoding kết hợp SIMD-BP128 (Bit-Packing 128-bit trên thanh ghi CPU), cho phép nén 1 số nguyên 32-bit xuống còn trung bình 4 đến 6 bit mà vẫn giải nén ở tốc độ hàng tỷ số mỗi giây.",
                    'code_snippet' => [
                        'language' => 'cpp',
                        'filename' => 'inverted_index_structure.h',
                        'code' => "struct Posting {\n    uint32_t doc_id;         // Delta compressed document ID\n    uint16_t term_frequency; // Number of occurrences in doc\n    std::vector<uint16_t> positions; // Byte offsets for snippet highlight\n};\n\n// Lexicon hashmap pointing to posting arrays in RAM\nunordered_map<TermString, CompressedPostingList> InvertedIndexRAM;"
                    ]
                ],
                [
                    'id' => 'scatter-gather-architecture',
                    'title' => '3. Kiến Trúc Scatter-Gather & Quản Lý Ngân Sách Độ Trễ (Latency Budget)',
                    'lead' => 'Một truy vấn được phóng tới 1.000 cụm máy chủ cùng một lúc và thu gom lại trong 150 mili-giây.',
                    'content' => "Không một máy tính đơn lẻ nào chứa nổi chỉ mục của 50 tỷ trang web. Google chia nhỏ Inverted Index thành hàng ngàn phân mảnh (Shards) theo Document Sharding. Khi bạn bấm Enter tại google.com:\n\n1. Yêu cầu tới Edge Datacenter gần nhất qua giao thức BGP Anycast.\n2. Root Query Broker bóc tách ngữ nghĩa, loại bỏ từ dừng, sinh vector ngữ nghĩa.\n3. Giai đoạn Scatter: Broker phát sóng truy vấn tới đồng loạt 1.000+ máy chủ Leaf Shard.\n4. Mỗi Shard quét danh sách posting của mình trong bộ nhớ RAM, chấm điểm sơ bộ BM25 và trả về Top 50 ứng viên tốt nhất của shard đó trong vòng chưa đầy 15 mili-giây.\n5. Giai đoạn Gather: Broker thu nhận kết quả từ tất cả các shard, xếp hạng lại (Reranking) và chọn ra Top 10 kết quả toàn cầu.\n\nNếu một vài shard bị chậm (Stragglers do rác bộ nhớ GC hoặc nghẽn mạng), Broker áp dụng kỹ thuật Hedged Requests (phát một bản sao truy vấn thứ hai sang node dự phòng sau 5ms) hoặc chấp nhận bỏ qua shard quá hạn để bảo vệ Latency Budget 300ms cam kết với người dùng.",
                    'callout' => [
                        'type' => 'tip',
                        'title' => 'Bí quyết The Tail at Scale của Jeff Dean',
                        'body' => 'Nếu một tác vụ chạm vào 1.000 máy chủ và mỗi máy chủ có xác suất 1% bị trễ 1 giây, thì xác suất người dùng phải chờ 1 giây là 99.99%! Cơ chế Hedged Requests sinh ra để hóa giải triệt để bài toán đuôi dài này.'
                    ]
                ],
                [
                    'id' => 'pagerank-transformer-ranking',
                    'title' => '4. Trọng Tài Phán Quyết: Từ Thuật Toán PageRank Đến Mô Hình Transformer',
                    'lead' => 'Trích xuất được 1.000 trang liên quan chỉ là bước dạo đầu. Xác định trang nào đứng vị trí số 1 mới là linh hồn của Google.',
                    'content' => "Mô hình xếp hạng của Google vận hành qua 2 tầng (Two-stage Retrieval):\n\n- Tầng 1 (First-Stage Retrieval): Sử dụng BM25 và Vector Search (ScaNN - Scalable Nearest Neighbors) lọc từ 50 tỷ trang xuống 1.000 ứng viên.\n- Tầng 2 (Deep Reranking): Đưa 1.000 ứng viên qua mô hình mạng nơ-ron học sâu (Deep Neural Network) đánh giá hàng trăm tín hiệu kết hợp:\n  + PageRank: Điểm uy tín liên kết đồ thị (trang web được bao nhiêu trang uy tín khác trỏ tới).\n  + RankBrain / BERT / MUM: Mô hình Transformer đối chiếu ngữ nghĩa câu truy vấn với ngữ cảnh thực tế của bài viết, nhận diện ý định tìm kiếm (Search Intent).\n  + Tín hiệu người dùng thực tế: Tỷ lệ click (CTR), thời gian dừng lại trên trang (Dwell Time), và độ tươi mới thông tin (Freshness Score đối với tin tức sự kiện nóng).\n\nToàn bộ quá trình từ bóc tách ngữ nghĩa đến in kết quả lên màn hình trình duyệt của bạn gói gọn trong đúng 0.28 giây.",
                    'code_snippet' => [
                        'language' => 'text',
                        'filename' => 'latency_budget_breakdown.txt',
                        'code' => "TOTAL LATENCY BUDGET: 280ms\n├── 0.05s: DNS Resolution + TLS 1.3 Handshake to Google Edge\n├── 0.03s: Query Understanding, Spellcheck & Embedding Generation\n├── 0.08s: Scatter-Gather to 1,000 Inverted Index Shards (BM25 + Lexicon)\n├── 0.07s: Multi-layer Transformer Neural Reranking (Top 100 docs)\n├── 0.03s: Snippet Generation & Result Page HTML Rendering\n└── 0.02s: Network Packet Return Delivery to Client"
                    ]
                ]
            ],
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
                    'action' => 'Định tuyến người dùng tới Google Edge Node gần nhất qua giao thức TLS 1.3 0-RTT.',
                    'latency' => '0.05s',
                    'status' => 'Connected'
                ],
                [
                    'step' => 4,
                    'time' => 'T + 0.12s',
                    'node' => 'Root Query Server (Broker)',
                    'protocol' => 'Scatter-Gather Architecture',
                    'action' => 'Phân tích câu hỏi, sửa lỗi chính tả, chia nhỏ truy vấn và bắn song song tới hàng ngàn máy chủ Index Shards.',
                    'latency' => '0.07s',
                    'status' => 'Scattered'
                ],
                [
                    'step' => 5,
                    'time' => 'T + 0.22s',
                    'node' => 'Leaf Shards & Scoring Engine',
                    'protocol' => 'PageRank + RankBrain AI',
                    'action' => 'Mỗi máy chủ tìm kiếm trong phân mảnh của mình, chấm điểm độ phù hợp (BM25, vector embeddings) và trả về Top 50.',
                    'latency' => '0.10s',
                    'status' => 'Scored'
                ],
                [
                    'step' => 6,
                    'time' => 'T + 0.28s',
                    'node' => 'Gather & Snippet Formatter',
                    'protocol' => 'Protobuf / HTTP2',
                    'action' => 'Tổng hợp kết quả tốt nhất, trích xuất đoạn văn bản xem trước (Snippet) và trả về màn hình người dùng.',
                    'latency' => '0.06s',
                    'status' => 'Rendered (Top 10)'
                ]
            ],
            'tags' => ['Googlebot', 'Inverted Index', 'PageRank', 'Distributed Sharding', 'Latency Budget'],
            'status' => 'In Production'
        ],
        [
            'id' => 3,
            'episode_number' => '03',
            'slug' => 'tap-03-cuoc-goi-xuyen-luc-dia-cap-quang-day-bien',
            'title' => 'Cuộc gọi xuyên lục địa: Dữ liệu lướt qua đáy biển trong ống thủy tinh mỏng bằng sợi tóc',
            'short_title' => 'Cáp quang đáy biển',
            'duration' => '16:10',
            'total_latency' => '0.13s',
            'category' => 'Telecommunications & Submarine Cables',
            'published_at' => '11/09/2026',
            'reading_time' => '16 phút đọc',
            'author' => 'Ma Cà Tưng (@macatung)',
            'series_title' => 'Series Pilot Season: Mở Nắp Những Hệ Thống Vô Hình Vận Hành Thế Giới',
            'summary' => 'Hơn 99% lưu lượng Internet toàn cầu không đi qua vệ tinh mà chạy trong những sợi thủy tinh mỏng bằng 1/10 sợi tóc nằm dưới đáy vực biển sâu 4.000m.',
            'hook' => 'Khi bạn gọi video cho người thân bên Mỹ, giọng nói của bạn được mã hóa thành các photon ánh sáng chui vào sợi thủy tinh mỏng như sợi tóc, phóng xuống đáy biển sâu 4.000 mét, né các dãy núi lửa ngầm để đến bờ bên kia trong 130 mili-giây.',
            'takeaways' => [
                '99% lưu lượng dữ liệu quốc tế được gánh bởi mạng lưới hơn 550 tuyến cáp quang đáy biển (Submarine Cables), không phải qua mạng vệ tinh.',
                'Lõi sợi quang đơn mốt (Single-Mode Fiber) chỉ dày 9 micron, giam giữ hạt photon ánh sáng di chuyển theo nguyên lý phản xạ toàn phần (Total Internal Reflection).',
                'Công nghệ ghép kênh bước sóng dày đặc (DWDM) cho phép bắn gần 100 chùm laser màu sắc khác nhau trên một sợi quang duy nhất, đạt băng thông trên 250 Tbps.',
                'Cứ mỗi 60-80 km dưới áp lực nước 400 atmosphere lại có một hộp phóng đại quang học EDFA bơm laser trực tiếp mà không cần chuyển đổi ánh sáng thành điện tử.',
                'Giao thức định tuyến BGP kết hợp cấu trúc mạng lưới (Mesh Topology) giúp tự động nắn dòng dữ liệu sang hướng khác chỉ trong 50ms khi cáp biển bị đứt.'
            ],
            'sections' => [
                [
                    'id' => 'fiber-physics',
                    'title' => '1. Ống Thủy Tinh 9 Micron & Hiện Tượng Phản Xạ Toàn Phần',
                    'lead' => 'Lõi sợi quang mỏng bằng 1/10 sợi tóc người nhưng có thể truyền tải toàn bộ thư viện nhân loại qua đại dương.',
                    'content' => "Trái tim của tuyến cáp quang biển là sợi quang đơn mốt (Single-Mode Fiber, chuẩn ITU-T G.654.D hoặc G.652). Lõi thủy tinh trung tâm (Core) làm bằng Silica siêu tinh khiết có đường kính vỏn vẹn 9 micromet, được bao bọc bởi lớp vỏ thủy tinh (Cladding) đường kính 125 micromet. Điểm mấu chốt nằm ở chỉ số khúc xạ: lớp lõi được pha tạp Germanium để chiết suất n₁ cao hơn chiết suất n₂ của lớp vỏ.\n\nTheo định luật Snell, khi ánh sáng đi từ môi trường có chiết suất cao sang môi trường có chiết suất thấp với góc tới lớn hơn góc tới hạn, hiện tượng Phản Xạ Toàn Phần (Total Internal Reflection) xảy ra. Ánh sáng bị 'giam cầm' hoàn toàn bên trong lõi sợi, uốn lượn theo hình zig-zag liên tục hàng ngàn kilomet mà không thoát ra ngoài. Thủy tinh làm cáp biển tinh khiết đến mức: nếu đại dương sâu 4.000 mét trong suốt như loại thủy tinh này, bạn có thể đứng trên boong tàu nhìn thấy từng hạt cát dưới đáy biển.",
                    'callout' => [
                        'type' => 'info',
                        'title' => 'Tại sao chọn bước sóng 1550 nm?',
                        'body' => 'Trong phổ ánh sáng, bước sóng hồng ngoại 1550 nm có mức suy hao quang học (Attenuation) thấp nhất trong sợi thủy tinh silica, chỉ khoảng 0.15 dB/km.'
                    ]
                ],
                [
                    'id' => 'dwdm-multiplexing',
                    'title' => '2. Phép Màu Ghép Kênh DWDM: 250 Terabit/giây Trên Một Cặp Sợi',
                    'lead' => 'Thay vì bắn một tia laser đơn độc, các kỹ sư bắn đồng thời gần 100 chùm ánh sáng mang bước sóng khác nhau vào cùng một ống thủy tinh.',
                    'content' => "Nếu mỗi sợi quang chỉ truyền một luồng tín hiệu bật/tắt (On-Off Keying) đơn thuần, băng thông tối đa sẽ bị chặn lại ở mức vài chục Gigabyte mỗi giây do giới hạn điện tử của chip phát. Đột phá mang tên DWDM (Dense Wavelength Division Multiplexing - Ghép Kênh Phân Chia Theo Bước Sóng Dày Đặc) đã thay đổi hoàn toàn cục diện.\n\nÁnh sáng được chia thành hàng chục dải màu quang phổ cách nhau chỉ 50 GHz hoặc 100 GHz trong dải bước sóng C-band và L-band. Mỗi bước sóng mang một dòng dữ liệu độc lập 400 Gbps hoặc 800 Gbps nhờ công nghệ điều chế quang học kết hợp Coherent Optics và QAM-64 (điều chế biên độ và pha cầu phương). Nhờ đó, một bó cáp biển chỉ chứa 12 đến 24 cặp sợi quang có thể mang tổng dung lượng khổng lồ trên 250 Terabit/giây – tương đương truyền tải đồng thời hàng chục triệu video 4K phát trực tiếp.",
                    'code_snippet' => [
                        'language' => 'text',
                        'filename' => 'dwdm_grid_allocation.txt',
                        'code' => "DWDM ITU-T Grid (193.10 THz - 196.10 THz, 50 GHz Channel Spacing)\n├─ Channel 01: 193.10 THz (1552.52 nm) -> Modulation: 64-QAM Coherent -> 800 Gbps\n├─ Channel 02: 193.15 THz (1552.12 nm) -> Modulation: 64-QAM Coherent -> 800 Gbps\n├─ Channel ...: [96 Parallel Laser Wavelengths]\n└─ Channel 96: 197.85 THz (1515.25 nm) -> Total Capacity per Fiber Pair: 76.8 Tbps"
                    ]
                ],
                [
                    'id' => 'edfa-repeaters',
                    'title' => '3. Những Cỗ Máy Tiếp Sức Dưới Đáy Vực: Bộ Lặp Quang Học EDFA',
                    'lead' => 'Dưới đáy biển sâu 4.000 mét, cứ mỗi 70 km lại có một ống kim loại titan chịu áp lực khổng lồ để tiếp sức cho ánh sáng.',
                    'content' => "Dù thủy tinh có tinh khiết đến đâu, sau 70 km di chuyển, 90% photon ánh sáng vẫn bị hấp thụ hoặc tán xạ bởi phân tử thủy tinh (Tán xạ Rayleigh). Nếu phải vớt tín hiệu lên, đổi thành điện rồi khuếch đại và đổi lại thành ánh sáng thì độ trễ và chi phí năng lượng sẽ phá hủy hệ thống.\n\nGiải pháp là bộ khuếch đại quang sợi pha tạp Erbium (EDFA - Erbium-Doped Fiber Amplifier). Bên trong hộp lặp (Repeater), một đoạn sợi quang được cấy các ion kim loại hiếm Erbium. Một diode laser bơm nguồn (Pump Laser 980 nm) kích hoạt các electron của ion Erbium lên mức năng lượng cao. Khi chùm photon tín hiệu 1550 nm yếu ớt đi qua, nó kích thích các ion Erbium giải phóng đồng loạt photon mới có cùng bước sóng và cùng pha (hiện tượng Bức Xạ Kích Thích - Stimulated Emission). Tín hiệu ánh sáng được phóng đại trực tiếp trong miền quang học thuần túy mà không tốn dù chỉ 1 nano-giây chuyển đổi điện tử!\n\nĐể nuôi hàng trăm bộ lặp này dọc chiều dài 10.000 km xuyên biển, trạm cập bờ (Cable Landing Station) truyền dòng điện một chiều cao thế lên tới 10.000 Volt (HVDC) chạy dọc theo lớp vỏ bọc bằng đồng của ống cáp.",
                    'callout' => [
                        'type' => 'important',
                        'title' => 'Áp lực 400 Atmosphere',
                        'body' => 'Dưới độ sâu 4.000m, áp lực nước tương đương một chiếc xe tải đè lên diện tích móng tay của bạn. Hộp bảo vệ Repeater làm bằng thép tôi và vỏ titan đúc nguyên khối chống nước tuyệt đối suốt 25 năm.'
                    ]
                ],
                [
                    'id' => 'bgp-self-healing',
                    'title' => '4. Khi Cáp Biển Bị Cào Đứt: Vũ Điệu Tự Phục Hồi Của Giao Thức BGP',
                    'lead' => 'Mỗi năm có hàng trăm vụ đứt cáp biển xảy ra do mỏ neo tàu cá, động đất ngầm hoặc hoạt động nạo vét.',
                    'content' => "Tại Việt Nam, các sự cố đứt cáp AAG, APG, IA, AAE-1 đã trở thành câu chuyện quen thuộc. Khi một con tàu kéo lê mỏ neo và cào đứt tuyến cáp quang dưới đáy biển, tín hiệu laser tắt ngấm. Làm thế nào mà kết nối Internet của bạn không bị mất hoàn toàn mà chỉ bị chập chờn?\n\nTại các trạm cập bờ và trung tâm trung chuyển Internet (IXP), các bộ định tuyến biên (Border Routers) chạy giao thức định tuyến liên vùng BGP (Border Gateway Protocol) kết hợp cơ chế BFD (Bidirectional Forwarding Detection). Khi mất nhịp tim BFD trong 50 mili-giây, router ngay lập tức phát bản tin BGP WITHDRAW để thu hồi tuyến đường chết, và kích hoạt cơ chế Fast Reroute (FRR) chuyển hướng toàn bộ luồng dữ liệu sang các tuyến cáp trên đất liền (qua biên giới phía Bắc) hoặc tuyến cáp biển dự phòng khác.\n\nTrong lúc đó, tàu chuyên dụng sửa cáp biển (Cable Repair Ship) được điều động ra khơi. Thủy thủ dùng thiết bị đo phản xạ miền thời gian quang học (OTDR) để xác định vị trí đứt chính xác tới từng mét bằng cách đo thời gian xung ánh sáng dội ngược, thả móc cẩu gắp hai đầu cáp lên boong tàu, và những kỹ sư lành nghề hàn nối từng sợi quang 9 micron dưới kính hiển vi quang học.",
                    'code_snippet' => [
                        'language' => 'bash',
                        'filename' => 'bgp_event_reroute.log',
                        'code' => "09:14:02.102 BFD-AGENT: Session to 203.119.0.1 (APG-Subsea-Trunk) state DOWN (Heartbeat Timeout)\n09:14:02.105 BGP-CORE: Peer 203.119.0.1 unreachable -> WITHDRAW prefix 1.0.0.0/24\n09:14:02.108 MPLS-FRR: Activating Pre-computed Backup Path via SMW-3 (Trans-Eurasia Overland)\n09:14:02.112 TRAFFIC-ENG: Switched 140 Gbps in 10ms. Packet Loss: 0.002%."
                    ]
                ]
            ],
            'key_nodes' => [
                [
                    'step' => 1,
                    'time' => 'T + 0.00s',
                    'node' => 'Microphone & Chip Mã Hóa Âm Thanh',
                    'protocol' => 'Opus Codec / WebRTC',
                    'action' => 'Số hóa giọng nói thành các gói tin số nén (20ms/gói), đóng gói vào giao thức UDP bảo đảm độ trễ thấp nhất.',
                    'latency' => '0.02s',
                    'status' => 'Encoded'
                ],
                [
                    'step' => 2,
                    'time' => 'T + 0.02s',
                    'node' => 'Trạm Cập Bờ (Cable Landing Station)',
                    'protocol' => 'Optical Transponder (DWDM)',
                    'action' => 'Đổi tín hiệu điện thành tia laser màu hồng ngoại 1550nm, điều chế QAM-64 bắn vào sợi quang đơn mốt (SMF).',
                    'latency' => '0.01s',
                    'status' => 'Modulated'
                ],
                [
                    'step' => 3,
                    'time' => 'T + 0.03s -> 0.08s',
                    'node' => 'Bộ Khuếch Đại EDFA Đáy Vực Biển',
                    'protocol' => 'Optical Stimulated Emission',
                    'action' => 'Cứ mỗi 70km đáy biển sâu 4.000m, laser 980nm kích thích ion Erbium khuếch đại chùm photon mà không cần chuyển sang tín hiệu điện.',
                    'latency' => '0.05s',
                    'status' => 'Amplified'
                ],
                [
                    'step' => 4,
                    'time' => 'T + 0.09s',
                    'node' => 'Hải Trình Vượt Thái Bình Dương (20.000km)',
                    'protocol' => 'Speed of Light in Glass (~200.000 km/s)',
                    'action' => 'Tia sáng uốn lượn liên tục theo nguyên lý phản xạ toàn phần bên trong ống silica 9 micron né tránh rãnh biển sâu.',
                    'latency' => '0.03s',
                    'status' => 'Propagated'
                ],
                [
                    'step' => 5,
                    'time' => 'T + 0.12s',
                    'node' => 'Trạm Bờ Bên Kia (Mỹ / Châu Âu)',
                    'protocol' => 'Photodiode Receiver & BGP Router',
                    'action' => 'Cảm biến quang học thu chùm sáng, tái tạo thành gói tin điện tử, router BGP giải mã và định tuyến tới điện thoại người nhận.',
                    'latency' => '0.01s',
                    'status' => 'Demodulated'
                ],
                [
                    'step' => 6,
                    'time' => 'T + 0.13s',
                    'node' => 'Loa Tai Nghe Người Nhận Phát Tiếng',
                    'protocol' => 'Analog Audio DAC',
                    'action' => 'Bộ giải mã Opus tái tạo sóng âm trong tai nghe. Bạn nghe thấy câu "Alo" của người bên kia chỉ sau 130 mili-giây.',
                    'latency' => '0.01s',
                    'status' => 'Delivered'
                ]
            ],
            'tags' => ['Subsea Fiber', 'DWDM', 'EDFA Repeater', 'BGP Routing', 'Total Internal Reflection'],
            'status' => 'Drafting'
        ],
        [
            'id' => 4,
            'episode_number' => '04',
            'slug' => 'tap-04-cay-atm-khong-bao-gio-nha-nham-tien',
            'title' => 'Cây ATM không bao giờ nhả nhầm tiền: Kiến trúc chịu lỗi và phép màu Two-Phase Commit',
            'short_title' => 'Cây ATM không nhả nhầm',
            'duration' => '13:50',
            'total_latency' => '1.20s',
            'category' => 'Banking Hardware & Distributed Consensus',
            'published_at' => '12/09/2026',
            'reading_time' => '14 phút đọc',
            'author' => 'Ma Cà Tưng (@macatung)',
            'series_title' => 'Series Pilot Season: Mở Nắp Những Hệ Thống Vô Hình Vận Hành Thế Giới',
            'summary' => 'Cơ cấu cơ học nhả tiền (Dispenser) với cảm biến quang học đo độ dày từng micromet kết hợp thuật toán phân tán Two-Phase Commit và nhật ký WAL.',
            'hook' => 'Bạn đã bao giờ tự hỏi: Giả sử cây ATM vừa đếm xong 2 triệu đồng và đang chuẩn bị đưa ra ngoài thì... toàn bộ khu phố mất điện đột ngột! Tài khoản của bạn có bị trừ oan không? Tại sao cỗ máy kim loại này chưa bao giờ nhả nhầm một tờ tiền?',
            'takeaways' => [
                'Máy ATM là một máy tính công nghiệp bảo mật chạy hệ điều hành chuyên dụng, giao tiếp với các module cơ học qua tiêu chuẩn chung CEN/XFS.',
                'Cơ cấu cơ học Dispenser sử dụng cảm biến độ dày kép (Double Detect Sensor) siêu nhạy đo độ dày từng tờ tiền với độ chính xác micromet để loại bỏ tiền dính.',
                'Giao thức Two-Phase Commit (2PC) kết hợp Write-Ahead Logging (WAL) đảm bảo tính toàn vẹn ACID: Tiền không bao giờ bị trừ nếu chưa vào tay khách.',
                'Nguyên lý Idempotency (Bất biến với số lần gọi) và bản tin Reversal (MTID 0420) giải quyết triệt để sự cố rớt mạng giữa chừng.',
                'Bàn phím mã hóa EPP (Encrypting PIN Pad) có cơ chế tự hủy (Zeroization) tức thời nếu phát hiện dấu hiệu cạy phá hoặc can thiệp vật lý.'
            ],
            'sections' => [
                [
                    'id' => 'atm-safe-xfs',
                    'title' => '1. Bên Trong Pháo Đài Kim Loại: Chuẩn Giao Tiếp CEN/XFS & EPP',
                    'lead' => 'Phía sau lớp kính mỏng manh của cây ATM là một két sắt nặng gần 1 tấn và phần cứng bảo mật cấp độ quân sự.',
                    'content' => "Một cỗ máy ATM (Automated Teller Machine) gồm hai phần tách biệt: Phần trên là máy tính công nghiệp điều khiển (thường chạy Windows IoT Enterprise được khóa cứng bảo mật), và phần dưới là Buồng két an toàn (Safe Box) đạt tiêu chuẩn UL 291 hoặc CEN L với vỏ thép hợp kim dày, chống khoan cắt và chịu được nhiệt độ đèn xì oxy-axetylen.\n\nĐể hệ điều hành giao tiếp an toàn với các thiết bị cơ khí phức tạp (Đầu đọc thẻ, Khay tiền, Máy in hóa đơn), ngành ngân hàng tạo ra chuẩn quốc tế CEN/XFS (Extensions for Financial Services). Mỗi thiết bị cơ học có một XFS Service Provider trừu tượng hóa lệnh phần cứng thành các API chuẩn hóa.\n\nĐặc biệt, bàn phím nhập mã PIN không phải bàn phím thông thường mà là khối EPP (Encrypting PIN Pad). Ngay khi ngón tay bạn nhấn số, vi xử lý bảo mật bên trong EPP lập tức mã hóa số PIN bằng thuật toán 3DES/AES kết hợp khóa DUKPT (Derived Unique Key Per Transaction) trước khi đẩy ra ngoài cổng cáp USB. Nếu kẻ gian cố tình khoan vào vỏ EPP, lưới cảm ứng vi mô ngầm sẽ đứt và kích hoạt mạch xả điện tụ, xóa sạch toàn bộ khóa mật mã trong chip nhớ (Zeroization) trong vài micro-giây.",
                    'callout' => [
                        'type' => 'tip',
                        'title' => 'Quy tắc vàng của EPP',
                        'body' => 'Mã PIN trần trụi (Clear PIN) không bao giờ rời khỏi khối EPP dưới bất kỳ dạng tín hiệu điện tử nào.'
                    ]
                ],
                [
                    'id' => 'mechanical-dispenser',
                    'title' => '2. Cơ Cấu Bốc Tiền Cơ Khí & Cảm Biến Đo Độ Dày Siêu Âm',
                    'lead' => 'Làm thế nào để cỗ máy tách được đúng 10 tờ tiền mới cóng dính chặt vào nhau mà không bao giờ nhả thừa?',
                    'content' => "Bên trong két sắt ATM là các hộp đựng tiền (Cash Cassettes) chứa hàng ngàn tờ tiền được xếp theo mệnh giá (500k, 200k, 100k). Khi nhận lệnh rút tiền, bánh lăn tách tiền (Pick Rollers) sử dụng lực ma sát để kéo từng tờ tiền đơn lẻ ra khỏi hộp băng chuyền với vận tốc 5 đến 7 tờ mỗi giây.\n\nMắt thần của cỗ máy là Cảm Biến Nhận Diện Tiền Kép (Double Detect Sensor). Cảm biến này có thể là cơ cấu con lăn điện cảm hoặc cảm biến siêu âm đo chiều dày tờ tiền với dung sai micromet. Khi tờ tiền bay qua:\n- Nếu độ dày bình thường (~0.1 mm với tiền Polymer), tờ tiền được băng chuyền đưa thẳng vào buồng gom (Stacker).\n- Nếu phát hiện hai tờ tiền dính nhau (độ dày gấp đôi ~0.2 mm), hoặc tờ tiền bị gấp mép, van chuyển hướng (Diverter Gate) cơ học lập tức lật sang hướng khác trong 20 mili-giây, đẩy tờ tiền lỗi vào Hộp Thu Hồi Phế Phẩm (Reject Bin). Cỗ máy tự động ra lệnh kéo một tờ tiền khác thay thế cho đến khi gom đủ số lượng yêu cầu.",
                    'code_snippet' => [
                        'language' => 'text',
                        'filename' => 'dispenser_flow_state.txt',
                        'code' => "CASSETTE [500K] -> Friction Pick Roller (1 bill)\n  ├── Sensor Check: Thickness = 0.108 mm (OK) -> Send to Stacker\nCASSETTE [500K] -> Friction Pick Roller (2 bills stuck together)\n  ├── Sensor Check: Thickness = 0.219 mm (DOUBLE DETECTED!)\n  └── Diverter Gate Flip -> Route to REJECT BIN (Count not decremented)\nRepeat until 4 valid bills gathered in Stacker -> Ready to Open Shutter"
                    ]
                ],
                [
                    'id' => 'two-phase-commit-wal',
                    'title' => '3. Phép Màu Thuật Toán Two-Phase Commit & Write-Ahead Logging (WAL)',
                    'lead' => 'Rút tiền là một giao dịch phân tán giữa máy móc vật lý ngoài đời thực và cơ sở dữ liệu số trong ngân hàng.',
                    'content' => "Trong khoa học máy tính, đây là bài toán kinh điển: Phân phối trạng thái giữa hai thực thể độc lập. Nếu ngân hàng trừ tiền trước, nhưng máy ATM kẹt cơ không mở cửa được, khách hàng mất tiền oan. Nếu ATM nhả tiền trước rồi ngân hàng mới trừ, lỡ khách rút tiền xong cáp mạng bị đứt, ngân hàng thất thoát tài sản.\n\nĐể giải quyết, kiến trúc ngân hàng áp dụng giao thức Cam Kết Hai Pha (Two-Phase Commit - 2PC) và Nhật Ký Ghi Trước (WAL):\n\n- Pha 1 (Prepare / Hold): Máy ATM gửi yêu cầu MTID 0100 lên máy chủ ngân hàng. Ngân hàng kiểm tra số dư, ghi log WAL với trạng thái PENDING_HOLD và phong tỏa 2 triệu đồng. Tiền chưa bị trừ khỏi tài khoản, nhưng không thể dùng để giao dịch khác. Ngân hàng trả mã APPROVE về cho ATM.\n- Pha 2 (Execute & Commit): Nhận được lệnh APPROVE, ATM tiến hành đếm tiền và gom tại cửa chớp Shutter. Cửa mở ra, cảm biến quang học tại cửa Shutter ghi nhận tờ tiền cuối cùng rời khỏi máy (khách hàng đã rút tay ra). Lúc này, ATM mới phát bản tin hoàn tất DISPENSE_OK (MTID 0200) lên máy chủ. Ngân hàng chuyển trạng thái từ PENDING_HOLD sang DEBITED chính thức.",
                    'callout' => [
                        'type' => 'important',
                        'title' => 'Tính chất ACID trong ngân hàng',
                        'body' => 'A (Atomicity) - Toàn vẹn: Hoàn toàn tiền ra và tài khoản bị trừ, hoặc là không có gì xảy ra. Không bao giờ tồn tại trạng thái lưng chừng.'
                    ]
                ],
                [
                    'id' => 'power-loss-reversal',
                    'title' => '4. Cúp Điện Đột Ngột & Vũ Khí Đối Soát Bản Tin Đảo Chiều (Reversal MTID 0420)',
                    'lead' => 'Chuyện gì xảy ra nếu cúp điện đúng lúc tiền đang nằm ở khe cửa nhưng bạn chưa kịp rút?',
                    'content' => "Kịch bản ác mộng nhất đối với kỹ sư hệ thống: Cửa Shutter vừa mở, tiền đang thò ra ngoài thì máy mất điện đột ngột hoặc bạn mải nghe điện thoại không rút tiền!\n\nMọi cây ATM đều trang bị bộ lưu điện khẩn cấp UPS tích hợp bên trong két sắt. Khi nguồn điện lưới sập, UPS lập tức duy trì điện áp cho máy tính và động cơ cơ khí trong ít nhất 3 đến 5 phút:\n\n1. Máy đếm ngược bộ hẹn giờ rút tiền (thường là 30 giây). Nếu cảm biến báo khách hàng không chạm vào tiền, cơ cấu cuốn ngược (Retract Mechanism) kích hoạt, kéo toàn bộ xấp tiền trở lại vào Hộp Thu Hồi Đặc Biệt (Retract Cassette).\n2. Máy ghi nhận sự kiện cơ học vào chip nhớ bất biến NVRAM và ổ đĩa thể rắn SSD.\n3. Khi có điện và mạng trở lại, hệ điều hành tự động quét log khởi động. Nhận thấy giao dịch trước chưa hoàn tất, ATM lập tức sinh bản tin Đảo Chiều Giao Dịch (Reversal Message MTID 0420) mang Transaction STAN nguyên bản gửi lên Core Banking.\n4. Nhờ nguyên lý Idempotency, Core Banking nhận bản tin 0420, xác định giao dịch bị hủy và tự động gỡ phong tỏa, hoàn trả trọn vẹn số tiền lại cho khách hàng.",
                    'code_snippet' => [
                        'language' => 'json',
                        'filename' => 'iso8583_reversal_0420.json',
                        'code' => "{\n  \"MTID\": \"0420\",\n  \"ReasonCode\": \"108\",\n  \"ReasonDescription\": \"Hardware Dispense Timeout / Retracted into Bin\",\n  \"OriginalDataElements\": {\n    \"OriginalMTID\": \"0100\",\n    \"OriginalSTAN\": \"849201\",\n    \"OriginalAmount\": \"2000000\"\n  },\n  \"Action\": \"RELEASE_HOLD_IMMEDIATELY\"\n}"
                    ]
                ]
            ],
            'key_nodes' => [
                [
                    'step' => 1,
                    'time' => 'T + 0.00s',
                    'node' => 'Khối Bàn Phím Mã Hóa EPP',
                    'protocol' => 'DUKPT & 3DES Encryption',
                    'action' => 'Khách nhập PIN, chip phần cứng EPP mã hóa trực tiếp trong phần cứng trước khi gửi ra ngoài cáp truyền.',
                    'latency' => '0.05s',
                    'status' => 'Encrypted PIN Block'
                ],
                [
                    'step' => 2,
                    'time' => 'T + 0.20s',
                    'node' => 'Xác Thực & Phong Tỏa (Hold)',
                    'protocol' => 'Two-Phase Commit (Phase 1)',
                    'action' => 'Core banking phong tỏa số dư tài khoản nhưng chưa trừ tiền thật, ghi nhật ký Write-Ahead Log (WAL).',
                    'latency' => '0.25s',
                    'status' => 'Pending Hold'
                ],
                [
                    'step' => 3,
                    'time' => 'T + 0.50s',
                    'node' => 'Cơ Cấu Bốc Tiền Cơ Khí (Dispenser)',
                    'protocol' => 'CEN/XFS Mechanical Hardware API',
                    'action' => 'Con lăn ma sát rút từng tờ tiền từ khay ra khay gom với tốc độ 6 tờ/giây.',
                    'latency' => '0.30s',
                    'status' => 'Picking'
                ],
                [
                    'step' => 4,
                    'time' => 'T + 0.85s',
                    'node' => 'Cảm Biến Nhận Diện Tiền Kép',
                    'protocol' => 'Double Detect Optical Thickness Sensor',
                    'action' => 'Đo độ dày từng tờ tiền với độ chính xác micromet. Nếu 2 tờ dính nhau, van cơ học lật chuyển hướng vào hộp Reject Bin.',
                    'latency' => '0.10s',
                    'status' => 'Thickness Verified'
                ],
                [
                    'step' => 5,
                    'time' => 'T + 1.00s',
                    'node' => 'Cửa Shutter Mở & Cảm Biến Rút Tiền',
                    'protocol' => 'Optical Gate Sensor Trigger',
                    'action' => 'Cửa sổ nhả tiền mở ra. Cảm biến quang học xác nhận khách đã rút toàn bộ tiền ra khỏi miệng máy.',
                    'latency' => '0.15s',
                    'status' => 'Dispensed'
                ],
                [
                    'step' => 6,
                    'time' => 'T + 1.20s',
                    'node' => 'Xác Nhận Cam Kết (Commit MTID 0200)',
                    'protocol' => 'Two-Phase Commit (Phase 2)',
                    'action' => 'ATM phát bản tin thành công, ngân hàng chính thức trừ tiền trong tài khoản và kết thúc phiên giao dịch.',
                    'latency' => '0.05s',
                    'status' => 'Committed (ACID)'
                ]
            ],
            'tags' => ['ATM Architecture', 'CEN/XFS', 'Two-Phase Commit', 'Double Detect Sensor', 'Idempotency'],
            'status' => 'Drafting'
        ],
        [
            'id' => 5,
            'episode_number' => '05',
            'slug' => 'tap-05-bam-dat-grab-ve-tinh-gps-thuyet-tuong-doi-einstein',
            'title' => 'Bấm đặt xe Grab: Vệ tinh GPS và thuyết tương đối của Einstein',
            'short_title' => 'Grab, GPS & Einstein',
            'duration' => '17:30',
            'total_latency' => '0.07s',
            'category' => 'Relativistic Physics & Satellite Systems',
            'published_at' => '13/09/2026',
            'reading_time' => '19 phút đọc',
            'author' => 'Ma Cà Tưng (@macatung)',
            'series_title' => 'Series Pilot Season: Mở Nắp Những Hệ Thống Vô Hình Vận Hành Thế Giới',
            'summary' => 'Đồng hồ nguyên tử trên vệ tinh GPS bị lệch 38 micro-giây mỗi ngày do thuyết tương đối của Einstein. Nếu không bù trừ, bạn đặt xe ở Quận 1 sẽ bị đón ở... Vũng Tàu.',
            'hook' => 'Mỗi lần bạn mở ứng dụng Grab và thấy chấm tròn màu xanh hiện đúng ngay trước cửa nhà, bạn đang vô tình kiểm chứng hai lý thuyết vật lý vĩ đại nhất của Albert Einstein: Thuyết Tương Đối Hẹp và Thuyết Tương Đối Rộng.',
            'takeaways' => [
                'Điện thoại của bạn là thiết bị thu thụ động (Receive-Only), không hề phát sóng lên vệ tinh; vị trí được tính bằng cách đo độ trễ thời gian bay của sóng vô tuyến.',
                'Thuyết tương đối hẹp (vận tốc nhanh làm thời gian chậm 7 µs/ngày) và thuyết tương đối rộng (trọng lực yếu làm thời gian nhanh 45 µs/ngày) khiến đồng hồ vệ tinh nhanh hơn Trái Đất đúng 38 micro-giây mỗi ngày.',
                'Nếu không có công thức hiệu chỉnh của Einstein, sai số tọa độ tích lũy lên tới 11.4 km mỗi ngày, khiến mọi bản đồ số trở nên vô dụng sau 2 phút mở ứng dụng.',
                'Cần tối thiểu 4 vệ tinh để giải hệ phương trình 4 ẩn số: kinh độ X, vĩ độ Y, cao độ Z và độ lệch đồng hồ thạch anh rẻ tiền của điện thoại.',
                'Ở tầng ứng dụng phần mềm, Grab sử dụng hệ thống chỉ mục không gian hình lục giác Uber H3 và thuật toán ẩn Hidden Markov Models (Map Matching) để gắn xe vào làn đường thực tế.'
            ],
            'sections' => [
                [
                    'id' => 'gps-constellation',
                    'title' => '1. Chòm Sao Vệ Tinh Navstar & Bản Chất Thu Thụ Động Của Điện Thoại',
                    'lead' => 'Điện thoại của bạn không hề gửi bất kỳ tín hiệu nào lên không gian; chip GPS là một máy nghe thụ động lắng nghe những chiếc đồng hồ chính xác nhất nhân loại.',
                    'content' => "Hệ thống định vị toàn cầu GPS (Mỹ) vận hành một chòm sao gồm ít nhất 31 vệ tinh đang bay ở quỹ đạo tầm trung MEO (Medium Earth Orbit) ở độ cao 20.200 km so với mặt biển. Mỗi vệ tinh hoàn thành một vòng quanh Trái Đất trong đúng 11 giờ 58 phút với vận tốc xấp xỉ 14.000 km/h.\n\nMỗi vệ tinh mang trên mình từ 3 đến 4 chiếc Đồng Hồ Nguyên Tử Rubidium và Cesium. Các đồng hồ này không dùng dây cót hay tinh thể thạch anh mà đo chu kỳ dao động của sóng điện từ kích thích electron chuyển tầng năng lượng trong nguyên tử Cesium-133: đúng 9.192.631.770 chu kỳ tương đương một giây chuẩn quốc tế. Độ sai lệch của đồng hồ nguyên tử chỉ là 1 giây sau mỗi 30 triệu năm!\n\nVệ tinh liên tục phát sóng vô tuyến dải tần L1 (1575.42 MHz) và L2 (1227.60 MHz) mang bản tin định vị (Navigation Message): bao gồm lịch thiên văn chính xác của vệ tinh (Ephemeris) và dấu thời gian thời điểm gói sóng xuất phát.",
                    'callout' => [
                        'type' => 'info',
                        'title' => 'Hiểu lầm phổ biến',
                        'body' => 'Nhiều người nghĩ điện thoại gửi tín hiệu GPS lên vệ tinh để hỏi vị trí. Sự thật là pin điện thoại chỉ vài Watt không thể truyền sóng 20.000 km vào không gian. Điện thoại chỉ đơn giản là máy thu sóng vô tuyến (Passive Receiver).'
                    ]
                ],
                [
                    'id' => 'einstein-time-drift',
                    'title' => '2. Thuyết Tương Đối Của Einstein: Khi Thời Gian Bị Kéo Dãn 38 Micro-giây',
                    'lead' => 'Nếu không có hai phương trình của Einstein, GPS chỉ là một đống sắt vụn vô dụng trên bầu trời.',
                    'content' => "Tại đây, hai định luật cơ bản của thế kỷ 20 xung đột trực tiếp trên đầu chúng ta:\n\n1. Thuyết Tương Đối Hẹp (1905): Vệ tinh bay quanh Trái Đất với tốc độ rất nhanh (v ≈ 3.874 m/s). Theo hiệu ứng giãn nở thời gian (Time Dilation), một vật chuyển động càng nhanh thì đồng hồ của nó chạy càng chậm so với người quan sát đứng yên trên mặt đất. Hiệu ứng này làm đồng hồ trên vệ tinh chạy chậm hơn trên mặt đất khoảng 7 micro-giây mỗi ngày (-7 µs/ngày).\n\n2. Thuyết Tương Đối Rộng (1915): Lực hấp dẫn làm cong không-thời gian. Ở độ cao 20.200 km, lực hút Trái Đất yếu hơn mặt đất rất nhiều (thế năng hấp dẫn cao hơn). Einstein chứng minh rằng lực hấp dẫn càng yếu thì thời gian trôi càng nhanh! Hiệu ứng này làm đồng hồ trên vệ tinh chạy nhanh hơn trên mặt đất tới 45 micro-giây mỗi ngày (+45 µs/ngày).\n\nTổng hợp hai hiệu ứng:\n+45 µs (Tương đối rộng) - 7 µs (Tương đối hẹp) = +38 micro-giây mỗi ngày!\n\n38 micro-giây tưởng như một cái chớp mắt, nhưng sóng vô tuyến di chuyển với vận tốc ánh sáng (c ≈ 300.000 km/s). Nếu bỏ qua sai số này:\nKhoảng cách sai số = 38 × 10⁻⁶ giây × 300.000.000 m/s = 11.400 mét = 11.4 km mỗi ngày!\n\nSau chỉ một giờ không bù trừ, sai số vị trí đã lên tới 475 mét – bạn đứng ở Nhà Thờ Đức Bà nhưng bản đồ sẽ chỉ bạn đang bơi ở giữa sông Sài Gòn!",
                    'callout' => [
                        'type' => 'important',
                        'title' => 'Giải pháp công nghệ của các kỹ sư không gian',
                        'body' => 'Trước khi phóng lên quỹ đạo, các kỹ sư cố tình lập trình bộ dao động đồng hồ nguyên tử trên vệ tinh chạy chậm lại: thay vì chạy ở tần số cơ sở 10.23 MHz, nó được điều chỉnh thành 10.22999999543 MHz. Khi lên tới độ cao 20.200 km, hiệu ứng tương đối tính làm nó tăng tốc vừa khít về đúng 10.23 MHz khi quan sát từ Trái Đất!'
                    ],
                    'code_snippet' => [
                        'language' => 'text',
                        'filename' => 'einstein_drift_formula.txt',
                        'code' => "Δt_special = -0.5 * (v^2 / c^2) * t0  ≈ -7.1 microseconds/day (Moving Clock Runs Slower)\nΔt_general = (ΔΦ / c^2) * t0          ≈ +45.7 microseconds/day (Weaker Gravity Runs Faster)\n--------------------------------------------------------------------------------------\nNET RELATIVISTIC DRIFT: +38.6 microseconds/day\nAccumulated Positioning Error per Day = 38.6 µs * 299,792.458 km/s = 11.57 km / day"
                    ]
                ],
                [
                    'id' => 'trilateration-math',
                    'title' => '3. Thuật Toán Giao Cầu (Trilateration) & Khử Độ Lệch Đồng Hồ Điện Thoại',
                    'lead' => 'Tại sao luôn cần tối thiểu 4 vệ tinh để xác định vị trí của bạn thay vì 3?',
                    'content' => "Mỗi vệ tinh phát sóng kèm mốc thời gian xuất phát T₀. Điện thoại nhận được sóng ở thời điểm T₁. Thời gian sóng bay là ΔT = T₁ - T₀. Nhân với vận tốc ánh sáng c, ta được khoảng cách từ điện thoại đến vệ tinh (gọi là Giả Cự Ly - Pseudorange). Biết khoảng cách đến 1 vệ tinh, vị trí của bạn nằm trên mặt của một hình cầu khổng lồ bán kính R₁.\n\n- Giao điểm của 2 mặt cầu tạo thành một đường tròn.\n- Giao điểm của đường tròn đó với mặt cầu thứ 3 thu hẹp lại còn đúng 2 điểm trong không gian (một điểm nằm trên mặt đất, một điểm bay ra ngoài vũ trụ bị loại trừ).\n\nNhưng tại sao lại cần vệ tinh thứ 4? Đồng hồ nguyên tử trên vệ tinh đắt hàng trăm ngàn USD, còn đồng hồ thạch anh trong điện thoại của bạn chỉ có giá vài cent và luôn bị lệch vài phần triệu giây. Nếu đồng hồ điện thoại lệch chỉ 1 mili-giây, sai số vị trí đã là 300 km!\n\nDo đó, vệ tinh thứ 4 được dùng để giải phương trình 4 ẩn số trong không gian phi tuyến: Tọa độ X, Tọa độ Y, Cao độ Z và Ẩn số thứ 4 chính là Độ lệch đồng hồ điện thoại (Clock Bias). Nhờ vậy, chip GPS vừa định vị bạn, vừa đồng bộ đồng hồ điện thoại về chuẩn nguyên tử!",
                    'code_snippet' => [
                        'language' => 'text',
                        'filename' => 'gps_pseudorange_equations.txt',
                        'code' => "rho_1 = sqrt((x - x1)^2 + (y - y1)^2 + (z - z1)^2) + c * delta_t_bias\nrho_2 = sqrt((x - x2)^2 + (y - y2)^2 + (z - z2)^2) + c * delta_t_bias\nrho_3 = sqrt((x - x3)^2 + (y - y3)^2 + (z - z3)^2) + c * delta_t_bias\nrho_4 = sqrt((x - x4)^2 + (y - y4)^2 + (z - z4)^2) + c * delta_t_bias\n\nUnknowns: (x, y, z) [User Coordinates] + delta_t_bias [Receiver Clock Offset]\nSolved via Newton-Raphson Iterative Matrix Solver in ~5 milliseconds."
                    ]
                ],
                [
                    'id' => 'grab-h3-map-matching',
                    'title' => '4. Tầng Ứng Dụng Grab: Lưới Lục Giác Uber H3 & Thuật Toán Gắn Làn Đường',
                    'lead' => 'Tọa độ GPS thô thường nhảy nhót hỗn loạn giữa các hẻm phố cao tầng. Grab làm thế nào để hiển thị tài xế chạy êm ái trên trục đường?',
                    'content' => "Tọa độ GPS từ vệ tinh trả về cho điện thoại là một cặp số (Kinh độ, Vidi độ) kèm theo sai số từ 5 đến 15 mét do hiện tượng hẻm vực đô thị (Urban Canyon) – sóng GPS bị phản xạ dội qua lại giữa các tòa nhà chọc trời trước khi tới ăng-ten điện thoại.\n\nĐể điều phối tài xế và tính giá cước theo thời gian thực, Grab sử dụng hệ thống chỉ mục không gian hình lục giác Uber H3 (Discrete Global Grid System). Khác với ô vuông truyền thống (có khoảng cách giữa các ô góc chéo không đồng đều), ô lục giác H3 có khoảng cách từ tâm đến cả 6 ô láng giềng hoàn toàn bằng nhau, giúp thuật toán tìm kiếm tài xế trong bán kính K vòng lục giác đạt tốc độ O(1) cực kỳ tối ưu.\n\nĐể chấm tròn không bị nhảy lung tung sang làn đường ngược chiều hoặc nhảy vào phòng khách nhà người khác, ứng dụng chạy thuật toán Gắn Bản Đồ (Map Matching) dựa trên Mô Hình Markov Ẩn (Hidden Markov Model - HMM). Thuật toán kết hợp tọa độ GPS với cảm biến la bàn điện tử và gia tốc kế (Inertial Measurement Unit) trên điện thoại để suy luận xác suất cao nhất xe đang nằm trên đoạn tim đường (Road Segment) nào, vẽ nên đường di chuyển mượt mà của tài xế trên màn hình điện thoại bạn.",
                    'callout' => [
                        'type' => 'tip',
                        'title' => 'Sự kỳ diệu của chuỗi công nghệ tích hợp',
                        'body' => 'Từ phương trình Einstein trên quỹ đạo vũ trụ 20.000 km, đến sóng vô tuyến nano-giây, hội tụ lại trong lưới lục giác Uber H3 trên màn hình điện thoại. Đó là tuyệt tác kiến trúc kỹ thuật mà nhân loại đang sử dụng hàng ngày.'
                    ]
                ]
            ],
            'key_nodes' => [
                [
                    'step' => 1,
                    'time' => 'T + 0.00s',
                    'node' => 'Đồng Hồ Nguyên Tử Trên Vệ Tinh Navstar',
                    'protocol' => 'Atomic Cesium-133 Resonator',
                    'action' => 'Đo 9.192.631.770 chu kỳ dao động/giây, lập trình chạy chậm ở 10.22999999543 MHz để bù đắp 38 micro-giây lệch thời gian theo thuyết tương đối của Einstein.',
                    'latency' => 'Realtime Tick',
                    'status' => 'Time-Calibrated'
                ],
                [
                    'step' => 2,
                    'time' => 'T + 0.02s',
                    'node' => 'Phát Sóng Vô Tuyến Không Gian (20.200km)',
                    'protocol' => 'L1 / L2 Radio Waves (1575.42 MHz)',
                    'action' => 'Vệ tinh liên tục phát quảng bá bản tin Ephemeris và mốc thời gian xuất phát sóng bay với vận tốc ánh sáng xuyên qua tầng điện ly.',
                    'latency' => '0.067s',
                    'status' => 'Broadcasting'
                ],
                [
                    'step' => 3,
                    'time' => 'T + 0.07s',
                    'node' => 'Ăng-ten Chip GPS Trên Điện Thoại',
                    'protocol' => 'Passive L-Band Radio Receiver',
                    'action' => 'Đón nhận tín hiệu sóng vô tuyến cực kỳ yếu từ ít nhất 4 vệ tinh cùng lúc (hoàn toàn không gửi tín hiệu ngược lên vũ trụ).',
                    'latency' => '0.001s',
                    'status' => 'Signal Captured'
                ],
                [
                    'step' => 4,
                    'time' => 'T + 0.071s',
                    'node' => 'Thuật Toán Giao Cầu (Trilateration Engine)',
                    'protocol' => 'Non-linear Pseudorange Solver',
                    'action' => 'Giải hệ 4 phương trình tìm tọa độ X, Y, Z và triệt tiêu độ sai lệch đồng hồ thạch anh rẻ tiền của điện thoại so với giờ nguyên tử.',
                    'latency' => '0.005s',
                    'status' => 'Fix Achieved (Lat/Long)'
                ],
                [
                    'step' => 5,
                    'time' => 'T + 0.076s',
                    'node' => 'Chỉ Mục Không Gian Lục Giác Uber H3',
                    'protocol' => 'Hexagonal Spatial Index (Resolution 8-10)',
                    'action' => 'Ánh xạ tọa độ GPS thành mã ô lục giác H3 duy nhất để truy vấn tài xế lân cận trong bán kính 1km ở độ phức tạp O(1).',
                    'latency' => '0.002s',
                    'status' => 'H3 Indexed'
                ],
                [
                    'step' => 6,
                    'time' => 'T + 0.080s',
                    'node' => 'Thuật Toán Gắn Làn Đường (Map Matching HMM)',
                    'protocol' => 'Hidden Markov Model Road Snapping',
                    'action' => 'Lọc nhiễu hẻm vực đô thị, gắn chính xác vị trí xe tài xế vào làn đường di chuyển và vẽ lộ trình tài xế đang chạy đến đón bạn.',
                    'latency' => '0.004s',
                    'status' => 'Road Snapped'
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
                'sub_tagline' => 'Khảo cứu kiến trúc hệ thống ngầm, bản vẽ kỹ thuật CAD & phân tích chuyên sâu.',
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
        $episodesCollection = collect($this->pilotEpisodes);
        $episode = $episodesCollection->firstWhere('slug', $slug);

        if (!$episode) {
            // Fallback to episode 1 if slug not found
            $episode = $this->pilotEpisodes[0];
        }

        $currentIndex = $episodesCollection->search(function ($item) use ($episode) {
            return $item['id'] === $episode['id'];
        });

        $prevEpisode = ($currentIndex > 0) ? $this->pilotEpisodes[$currentIndex - 1] : null;
        $nextEpisode = ($currentIndex < count($this->pilotEpisodes) - 1) ? $this->pilotEpisodes[$currentIndex + 1] : null;

        return Inertia::render('Decode/Show', [
            'episode' => $episode,
            'prevEpisode' => $prevEpisode ? [
                'id' => $prevEpisode['id'],
                'slug' => $prevEpisode['slug'],
                'episode_number' => $prevEpisode['episode_number'],
                'short_title' => $prevEpisode['short_title'],
                'title' => $prevEpisode['title'],
                'total_latency' => $prevEpisode['total_latency'],
            ] : null,
            'nextEpisode' => $nextEpisode ? [
                'id' => $nextEpisode['id'],
                'slug' => $nextEpisode['slug'],
                'episode_number' => $nextEpisode['episode_number'],
                'short_title' => $nextEpisode['short_title'],
                'title' => $nextEpisode['title'],
                'total_latency' => $nextEpisode['total_latency'],
            ] : null,
            'allEpisodes' => $episodesCollection->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'slug' => $item['slug'],
                    'episode_number' => $item['episode_number'],
                    'short_title' => $item['short_title'],
                    'title' => $item['title'],
                    'total_latency' => $item['total_latency'],
                    'category' => $item['category'],
                    'reading_time' => $item['reading_time'] ?? '15 phút đọc',
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
     * XML Sitemap
     */
    public function sitemap()
    {
        $baseUrl = 'https://decode.macatung.dev';
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Home
        $xml .= '<url><loc>' . $baseUrl . '/</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>';
        
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
