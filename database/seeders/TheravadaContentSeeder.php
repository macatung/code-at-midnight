<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use Carbon\Carbon;

class TheravadaContentSeeder extends Seeder
{
    /**
     * Run the database seeds for Comprehensive Authentic Theravāda Canonical Teachings (Pariyatti, Paṭipatti, Sutta).
     */
    public function run(): void
    {
        $articles = [
            // =========================================================================
            // 1. TỨ THÁNH ĐẾ (CATTĀRI ARIYASACCĀNI)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Tứ Thánh Đế (Cattāri Ariyasaccāni) — Bốn Chân Lý Tối Thượng Của Bậc Giác Ngộ',
                'pali_title' => 'Cattāri Ariyasaccāni',
                'slug' => 'tu-thanh-de-bon-chan-ly-toi-thuong',
                'category' => 'phap-hoc',
                'excerpt' => 'Khám phá cốt lõi của toàn bộ Tam tạng Pāḷi: Khổ đế, Tập đế, Diệt đế và Đạo đế — bản đồ chỉ đường đưa hành giả vượt thoát sinh tử luân hồi.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ (Saṃyutta Nikāya 56.11)',
                'content' => <<< 'EOF'
## 1. Vị Trí Của Tứ Thánh Đế Trong Giáo Pháp Nguyên Thủy

Trong toàn bộ giáo lý của Đức Thế Tôn, **Tứ Thánh Đế (Cattāri Ariyasaccāni)** giữ vị trí tối thượng, tựa như dấu chân voi có thể dung chứa tất cả dấu chân của muôn thú trong rừng (*Dīgha Nikāya*). Đức Phật từng dạy trong *Tương Ưng Bộ Kinh (Saṃyutta Nikāya)*:

> *"Này các Tỳ-kheo, chính vì không hiểu biết, không thấu triệt Bốn Chân Lý Thánh mà Như Lai và các ngươi đã phải trôi lăn, luân chuyển trong biển sinh tử dài vô tận này."*

---

## 2. Chi Tiết Bốn Thánh Đế

```mermaid
graph TD
    A[Tứ Thánh Đế Cattāri Ariyasaccāni] --> B[1. Khổ Đế Dukkha Sacca]
    A --> C[2. Tập Đế Samudaya Sacca]
    A --> D[3. Diệt Đế Nirodha Sacca]
    A --> E[4. Đạo Đế Magga Sacca]
    
    B --> B1[Thực trạng Bất toàn: 8 nỗi khổ]
    C --> C1[Nguồn gốc: Ái dục Taṇhā]
    D --> D1[Đoạn tận Ái dục: Niết-bàn Nibbāna]
    E --> E1[Đạo lộ: Bát Chánh Đạo 8 Chi phần]
```

### I. Khổ Thánh Đế (Dukkha Sacca) — Chân lý về sự Bất toàn
Đức Phật chỉ rõ thực tướng của cuộc đời gồm 8 nỗi thống khổ:
1. **Sinh khổ (Jāti dukkhā)**: Nỗi đau đớn khi chào đời và sự tiếp diễn của một kiếp sống hữu hạn.
2. **Lão khổ (Jarā dukkhā)**: Sự tàn hoại của thân căn, răng long, tóc bạc, giác quan suy yếu.
3. **Bệnh khổ (Byādhi dukkhā)**: Sự giày vò của tứ đại bất hòa, đau ốm thể xác.
4. **Tử khổ (Maraṇaṃ dukkhaṃ)**: Nỗi kinh hoàng của sự chia lìa sinh mạng.
5. **Cầu bất đắc khổ (Yampicchaṃ na labhati tampi dukkhaṃ)**: Mong muốn mà không toại nguyện.
6. **Ái biệt ly khổ (Piyehi vippayogo dukkho)**: Chia lìa những người, những vật yêu thương.
7. **Oán tằng hội khổ (Appiyehi sampayogo dukkho)**: Phải sống chung, gặp gỡ điều mình oán ghét.
8. **Năm uẩn thủ chấp là khổ (Saṅkhittena pañcupādānakkhandhā dukkhā)**: Sự bám víu vào Sắc, Thọ, Tưởng, Hành, Thức.

### II. Tập Thánh Đế (Samudaya Sacca) — Nguồn gốc của Khổ đau
Nguồn gốc sinh khởi toàn bộ khối khổ đau này chính là **Ái dục (Taṇhā)**:
- **Dục ái (Kāma-taṇhā)**: Khát khao hưởng thụ ngũ dục (sắc, thanh, hương, vị, xúc).
- **Hữu ái (Bhava-taṇhā)**: Khát khao tồn tại vĩnh cửu, trường sinh bất tử.
- **Phi hữu ái (Vibhava-taṇhā)**: Khát khao hư vô đoạn diệt sau khi chết.

### III. Diệt Thánh Đế (Nirodha Sacca) — Sự chấm dứt Khổ đau
Sự đoạn tận hoàn toàn không còn dư tàn của chính Ái dục ấy, sự buông bỏ, xả ly, giải thoát, không còn chấp thủ — đó chính là cảnh giới **Niết-bàn (Nibbāna)** tối thượng, tịch tịnh, bất tử.

### IV. Đạo Thánh Đế (Magga Sacca) — Con đường dẫn đến Đoạn Diệt Khổ
Đó chính là **Bát Chánh Đạo (Ariya Aṭṭhaṅgika Magga)** gồm 8 chi phần:
- **Chánh kiến (Sammā-diṭṭhi)**: Thấy rõ như thật Bốn Thánh Đế.
- **Chánh tư duy (Sammā-saṅkappa)**: Suy nghĩ xuất ly, không sân hận, không hại người.
- **Chánh ngữ (Sammā-vācā)**: Lời nói chân thật, hòa ái, xây dựng.
- **Chánh nghiệp (Sammā-kammanta)**: Hành động không sát sinh, không trộm cắp, không tà dâm.
- **Chánh mạng (Sammā-ājīva)**: Nuôi sống bản thân bằng nghề nghiệp trong sạch.
- **Chánh tinh tấn (Sammā-vāyāma)**: Nỗ lực diệt ác, sinh thiện.
- **Chánh niệm (Sammā-sati)**: Quán sát thân, thọ, tâm, pháp trong hiện tại.
- **Chánh định (Sammā-samādhi)**: Tâm an trú vắng lặng vào các tầng thiền sắc giới.

---

## 3. Tam Chuyển Thập Nhị Hành Của Tứ Đế

Đối với Bốn Thánh Đế, Đức Thế Tôn dạy phải thực chứng qua 3 giai đoạn (Tam chuyển) với 12 khía cạnh (Thập nhị hành):
- **Thị chuyển (Sacca-ñāṇa)**: Nhận biết rõ đây là Khổ, đây là Tập, đây là Diệt, đây là Đạo.
- **Khuyến chuyển (Kicca-ñāṇa)**: Biết rõ việc cần làm: Khổ phải liễu tri; Tập phải đoạn trừ; Diệt phải chứng ngộ; Đạo phải tu tập.
- **Chứng chuyển (Kata-ñāṇa)**: Biết rõ việc đã làm xong: Khổ đã liễu tri; Tập đã đoạn trừ; Diệt đã chứng ngộ; Đạo đã tu tập.
EOF
,
                'tags' => ['Tứ Diệu Đế', 'Dukkha', 'Pariyatti', 'Giáo Lý Căn Bản', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Dukkha', 'meaning' => 'Khổ não, bất toàn, không bền vững, biến dịch'],
                    ['term' => 'Taṇhā', 'meaning' => 'Ái dục, lòng khao khát thèm muốn vị kỷ'],
                    ['term' => 'Nirodha', 'meaning' => 'Sự diệt tận, dập tắt phiền não'],
                    ['term' => 'Magga', 'meaning' => 'Con đường, Đạo lộ đưa đến giải thoát'],
                    ['term' => 'Nibbāna', 'meaning' => 'Niết-bàn, cảnh giới tịch diệt tối thượng'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 7,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(15),
            ],

            // =========================================================================
            // 2. BÁT CHÁNH ĐẠO (ARIYA AṬṬHAṄGIKA MAGGA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Bát Chánh Đạo (Ariya Aṭṭhaṅgika Magga) — Đạo Lộ Giới - Định - Tuệ Toàn Hảo',
                'pali_title' => 'Ariya Aṭṭhaṅgika Magga',
                'slug' => 'bat-chanh-dao-ariya-atthangika-magga-gioi-dinh-tue',
                'category' => 'phap-hoc',
                'excerpt' => 'Phân tích chi tiết 8 chi phần Bát Chánh Đạo theo định nghĩa chuẩn xác của Kinh Tạng Pāḷi: Chánh Kiến, Chánh Tư Duy, Chánh Ngữ, Chánh Nghiệp, Chánh Mạng, Chánh Tinh Tấn, Chánh Niệm, Chánh Định.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ (Saṃyutta Nikāya 45.8)',
                'content' => <<< 'EOF'
## 1. Bản Chất Của Bát Chánh Đạo

Trong *Tương Ưng Đạo (Magga Saṃyutta)*, Đức Thế Tôn dạy rằng Bát Chánh Đạo là chiếc bè độc nhất đưa chúng sinh từ bờ mê (sinh tử luân hồi) sang bến giác (Niết-bàn). Tám chi phần này vận hành gắn kết hỗ tương, gom trọn trong tiến trình tu tập **Tam Học: Giới — Định — Tuệ**.

```mermaid
graph TD
    A[Bát Chánh Đạo] --> B[Tuệ Học Paññā]
    A --> C[Giới Học Sīla]
    A --> D[Định Học Samādhi]
    
    B --> B1[1. Chánh Kiến Sammā-diṭṭhi]
    B --> B2[2. Chánh Tư Duy Sammā-saṅkappa]
    
    C --> C1[3. Chánh Ngữ Sammā-vācā]
    C --> C2[4. Chánh Nghiệp Sammā-kammanta]
    C --> C3[5. Chánh Mạng Sammā-ājīva]
    
    D --> D1[6. Chánh Tinh Tấn Sammā-vāyāma]
    D --> D2[7. Chánh Niệm Sammā-sati]
    D --> D3[8. Chánh Định Sammā-samādhi]
```

---

## 2. Chi Tiết Tám Chi Phần Theo Lời Phật Dạy

### I. Nhóm Tuệ Học (Paññā-kkhandha)
1. **Chánh Kiến (Sammā-diṭṭhi)**: Sự hiểu biết đúng đắn về Bốn Chân Lý Thánh:
   - Hiểu biết về Khổ.
   - Hiểu biết về Khổ tập (nguồn gốc của khổ).
   - Hiểu biết về Khổ diệt (sự chấm dứt khổ).
   - Hiểu biết về Khổ diệt đạo (con đường đưa đến chấm dứt khổ).
2. **Chánh Tư Duy (Sammā-saṅkappa)**: Ý nghĩ chân chánh gồm 3 loại:
   - **Xuất ly tư duy (Nekkhamma-saṅkappa)**: Suy nghĩ buông bỏ tham dục, không dính mắc.
   - **Vô sân tư duy (Abyāpāda-saṅkappa)**: Suy nghĩ tràn đầy lòng từ ái, không giận hờn oán ghét.
   - **Bất hại tư duy (Avihiṃsā-saṅkappa)**: Suy nghĩ tràn ngập lòng bi mẫn, không làm tổn hại chúng sinh.

### II. Nhóm Giới Học (Sīla-kkhandha)
3. **Chánh Ngữ (Sammā-vācā)**: Lời nói chân chánh, từ bỏ 4 khẩu nghiệp bất thiện:
   - Từ bỏ nói dối (Musāvādā veramaṇī).
   - Từ bỏ nói lời đâm thọc, chia rẽ (Pisuṇāya vācāya veramaṇī).
   - Từ bỏ nói lời thô bạo, ác khẩu (Pharusāya vācāya veramaṇī).
   - Từ bỏ nói lời nhảm nhí vô ích (Samphappalāpā veramaṇī).
4. **Chánh Nghiệp (Sammā-kammanta)**: Hành động chân chánh, từ bỏ 3 thân nghiệp bất thiện:
   - Từ bỏ sát sinh, tàn hại sinh mạng (Pāṇātipātā veramaṇī).
   - Từ bỏ trộm cắp, lấy của không cho (Adinnādānā veramaṇī).
   - Từ bỏ tà dâm, lang chạ bất chính (Kāmesu micchācārā veramaṇī).
5. **Chánh Mạng (Sammā-ājīva)**: Nuôi mạng chân chánh, từ bỏ 5 nghề buôn bán bất chính làm tổn hại nhân loại:
   - Không buôn bán vũ khí sát thương.
   - Không buôn bán người (nô lệ, mại dâm).
   - Không buôn bán thịt thú vật nuôi giết.
   - Không buôn bán rượu và các chất say gây nghiện.
   - Không buôn bán chất độc sát hại muôn loài.

### III. Nhóm Định Học (Samādhi-kkhandha)
6. **Chánh Tinh Tấn (Sammā-vāyāma)** — Tứ Chánh Cần:
   - Ngăn ngừa điều ác chưa sinh không cho phát sinh.
   - Đoạn trừ điều ác đã sinh.
   - Phát triển điều thiện chưa sinh cho phát sinh.
   - Duy trì và tăng trưởng điều thiện đã sinh viên mãn.
7. **Chánh Niệm (Sammā-sati)**: Sự an trú tâm tỉnh giác trọn vẹn vào **Bốn Niệm Xứ (Cattāro Satipaṭṭhānā)**:
   - Quán Thân nơi Thân.
   - Quán Thọ nơi Thọ.
   - Quán Tâm nơi Tâm.
   - Quán Pháp nơi Pháp.
8. **Chánh Định (Sammā-samādhi)**: Sự nhất tâm thanh tịnh, tuần tự chứng đắc và an trú trong Bốn Tầng Thiền Sắc Giới (Sơ thiền, Nhị thiền, Tam thiền, Tứ thiền) viễn ly các dục và bất thiện pháp.
EOF
,
                'tags' => ['Bát Chánh Đạo', 'Magga', 'Tam Học', 'Giới Định Tuệ', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Sammā-diṭṭhi', 'meaning' => 'Chánh Kiến — cái thấy sáng suốt như thật về Tứ Thánh Đế'],
                    ['term' => 'Sammā-saṅkappa', 'meaning' => 'Chánh Tư Duy — suy nghĩ xuất ly, vô sân, bất hại'],
                    ['term' => 'Sīla', 'meaning' => 'Giới hạnh thanh tịnh, nền tảng của mọi thiện pháp'],
                    ['term' => 'Samādhi', 'meaning' => 'Định lực, sự tập trung tâm ý vắng lặng'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 9,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(14),
            ],

            // =========================================================================
            // 3. TAM TƯỚNG (TILAKKHAṆA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Tam Tướng (Tilakkhaṇa) — Vô Thường, Khổ, Vô Ngã: Ba Dấu Ấn Phổ Quát Của Vạn Pháp',
                'pali_title' => 'Tilakkhaṇa',
                'slug' => 'tam-tuong-tilakkhana-vo-thuong-kho-vo-nga',
                'category' => 'phap-hoc',
                'excerpt' => 'Ba thực tại chi phối toàn bộ vũ trụ hữu vi: Sabbe saṅkhārā aniccā (Vô thường), Sabbe saṅkhārā dukkhā (Khổ não), Sabbe dhammā anattā (Vô ngã).',
                'author' => 'Đại Tạng Kinh Pāḷi — Kinh Pháp Cú (Dhammapada 277-279) & Thanh Tịnh Đạo (Visuddhimagga)',
                'content' => <<< 'EOF'
## 1. Tuyên Ngôn Bất Biến Của Tam Tướng

Dù Đức Phật có xuất hiện ở thế gian hay không xuất hiện, tính chất của **Tam Tướng (Tilakkhaṇa)** vẫn luôn là quy luật tự nhiên chi phối toàn bộ thế giới vạn vật:

```mermaid
graph LR
    A[Tam Tướng Tilakkhaṇa] --> B[1. Vô Thường Anicca]
    A --> C[2. Khổ Não Dukkha]
    A --> D[3. Vô Ngã Anattā]
    
    B --> E[Mọi Hành Saṅkhāra đều Biến Diệt]
    C --> F[Mọi Hành Saṅkhāra đều Bất Toàn]
    D --> G[Mọi Pháp Dhammā đều Phi Bản Ngã]
```

---

## 2. Ba Dấu Ấn Chân Lý (Kinh Pháp Cú)

### I. Vô Thường (Anicca)
> **"Sabbe saṅkhārā aniccā'ti, yadā paññāya passati;<br />
> Atha nibbindatī dukkhe, esa maggo visuddhiyā."** *(Dhp 277)*<br />
> *"Tất cả các hành là vô thường, khi thấu suốt bằng trí tuệ, người ấy sẽ nhàm chán khổ đau; Đây chính là con đường đưa đến thanh tịnh."*

- **Bản chất**: Tất cả các pháp do duyên sinh (saṅkhāra) đều tuân theo quy luật sinh - trụ - hoại - diệt trong từng sát-na cực ngắn. Không có bất kỳ vật chất hay tâm thức nào đứng yên.

### II. Khổ Não (Dukkha)
> **"Sabbe saṅkhārā dukkhā'ti, yadā paññāya passati;<br />
> Atha nibbindatī dukkhe, esa maggo visuddhiyā."** *(Dhp 278)*<br />
> *"Tất cả các hành là khổ não, khi thấu suốt bằng trí tuệ, người ấy sẽ nhàm chán khổ đau; Đây chính là con đường đưa đến thanh tịnh."*

- **Bản chất**: Vì vô thường biến hoại nên các pháp không thể mang lại sự an ổn tuyệt đối. Sự cưỡng cầu cái vô thường phải trở thành thường còn chính là nguồn cội của bức bách và khổ đau (*Vipariṇāma-dukkha*).

### III. Vô Ngã (Anattā)
> **"Sabbe dhammā anattā'ti, yadā paññāya passati;<br />
> Atha nibbindatī dukkhe, esa maggo visuddhiyā."** *(Dhp 279)*<br />
> *"Tất cả các pháp là vô ngã, khi thấu suốt bằng trí tuệ, người ấy sẽ nhàm chán khổ đau; Đây chính là con đường đưa đến thanh tịnh."*

- **Lưu ý uyên áo**: Đối với Vô thường và Khổ, Đức Phật dùng chữ **"Saṅkhārā"** (các pháp hữu vi do duyên tạo); nhưng đối với Vô ngã, Ngài dùng chữ **"Dhammā"** (bao hàm cả pháp hữu vi lẫn pháp vô vi là Niết-bàn). Nghĩa là ngay cả Niết-bàn cũng hoàn toàn là **Vô Ngã**, không có cái ngã hay đại ngã nào trú ngụ trong đó.

---

## 3. Ứng Dụng Tam Tướng Trong Thiền Vipassanā

Khi hành giả hành trì Minh Sát Tuệ (Vipassanā), việc trực nhận Tam Tướng trên danh sắc (thân tâm) sẽ mở ra **Ba Cửa Giải Thoát (Vimokkhamukha)**:
1. Quán Vô Thường đắc **Vô Tướng Giải Thoát (Animitta-vimokkha)**.
2. Quán Khổ Não đắc **Vô Nguyện Giải Thoát (Appaṇihita-vimokkha)**.
3. Quán Vô Ngã đắc **Không Tánh Giải Thoát (Suññatā-vimokkha)**.
EOF
,
                'tags' => ['Tilakkhana', 'Tam Tướng', 'Anicca', 'Dukkha', 'Anatta', 'Vipassana'],
                'pali_terms' => [
                    ['term' => 'Anicca', 'meaning' => 'Vô Thường — luôn biến dịch, không tồn tại vĩnh cửu'],
                    ['term' => 'Dukkha', 'meaning' => 'Khổ — bất toàn, bị bức bách bởi sự sinh diệt'],
                    ['term' => 'Anattā', 'meaning' => 'Vô Ngã — không có một chủ thể độc lập, bất biến'],
                    ['term' => 'Saṅkhāra', 'meaning' => 'Hành — các pháp hữu vi được cấu tạo bởi nhân duyên'],
                    ['term' => 'Vimokkha', 'meaning' => 'Cửa giải thoát — sự giải thoát rốt ráo khỏi kiết sử'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 8,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(13),
            ],

            // =========================================================================
            // 4. BA MƯƠI BẢY PHẨM TRỢ ĐẠO (BODHIPAKKHIYĀ DHAMMĀ)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Ba Mươi Bảy Phẩm Trợ Đạo (Bodhipakkhiyā Dhammā) — Toàn Bộ Đạo Lộ Giác Ngộ Của Đấng Toàn Giác',
                'pali_title' => 'Bodhipakkhiyā Dhammā',
                'slug' => 'ba-muoi-bay-pham-tro-dao-bodhipakkhiya-dhamma',
                'category' => 'phap-hoc',
                'excerpt' => 'Tổng hợp 37 pháp bồ-đề phần: Tứ Niệm Xứ, Tứ Chánh Cần, Tứ Như Ý Túc, Ngũ Căn, Ngũ Lực, Thất Giác Chi và Bát Chánh Đạo trong Tam Tạng Pāḷi.',
                'author' => 'Đại Tạng Kinh Pāḷi — Trường Bộ (Kinh Đại Bát Niết Bàn DN 16) & Tương Ưng Bộ (SN 45-51)',
                'content' => <<< 'EOF'
## 1. Lời Di Huấn Trước Khi Đức Thế Tôn Nhập Niết Bàn

Trong *Kinh Đại Bát Niết Bàn (Mahāparinibbāna Sutta - DN 16)*, tại thành Vesālī trước khi thị tịch, Đức Phật đã căn dặn chư Tỳ-kheo gìn giữ và thực hành trọn vẹn **37 Phẩm Trợ Đạo (Sattatiṃsa Bodhipakkhiyā Dhammā)** để Chánh Pháp được trường tồn lâu dài:

```mermaid
graph TD
    A[37 Phẩm Trợ Đạo Bodhipakkhiyā Dhammā] --> B[1. Tứ Niệm Xứ: 4 Pháp]
    A --> C[2. Tứ Chánh Cần: 4 Pháp]
    A --> D[3. Tứ Như Ý Túc: 4 Pháp]
    A --> E[4. Ngũ Căn: 5 Pháp]
    A --> F[5. Ngũ Lực: 5 Pháp]
    A --> G[6. Thất Giác Chi: 7 Pháp]
    A --> H[7. Bát Chánh Đạo: 8 Pháp]
```

---

## 2. Bảy Nhóm Pháp Bồ Đề Phần Chi Tiết

### I. Bốn Niệm Xứ (Cattāro Satipaṭṭhānā — 4 pháp)
1. Quán Thân bất tịnh (Kāyānupassanā).
2. Quán Thọ thị khổ (Vedanānupassanā).
3. Quán Tâm vô thường (Cittānupassanā).
4. Quán Pháp vô ngã (Dhammānupassanā).

### II. Bốn Chánh Cần (Cattāro Sammappadhānā — 4 pháp)
1. **Tinh tấn ngăn ngừa**: Không cho bất thiện pháp chưa sinh được sinh khởi.
2. **Tinh tấn đoạn trừ**: Đoạn diệt các bất thiện pháp đã lỡ sinh khởi.
3. **Tinh tấn phát triển**: Làm cho các thiện pháp chưa sinh được sinh khởi.
4. **Tinh tấn duy trì**: Giữ gìn và làm tăng trưởng các thiện pháp đã sinh khởi đến mức viên mãn.

### III. Bốn Như Ý Túc (Cattāro Iddhipādā — 4 pháp)
Nền tảng giúp thành tựu các công hạnh tâm linh và thiền định siêu việt:
1. **Dục như ý túc (Chanda-iddhipāda)**: Ý chí, niềm khao khát nhiệt thành đối với Chánh Pháp.
2. **Cần như ý túc (Viriya-iddhipāda)**: Sự kiên trì, nỗ lực dũng mãnh không lùi bước.
3. **Tâm như ý túc (Citta-iddhipāda)**: Tâm chuyên chú, dồn toàn bộ tâm lực vào mục tiêu giải thoát.
4. **Thẩm như ý túc (Vīmaṃsā-iddhipāda)**: Trí tuệ quán xét, tư duy thấu đáo về con đường tu tập.

### IV. Năm Căn (Pañcindriyāni — 5 pháp)
Năm năng lực gốc rễ dẫn dắt tâm linh:
1. **Tín căn (Saddhindriya)**: Đức tin bất động vào Tam Bảo và lý Nhân Quả Nghiệp Báo.
2. **Tấn căn (Viriyindriya)**: Sự nỗ lực siêng năng hành trì.
3. **Niệm căn (Satindriya)**: Sự ghi nhớ, tỉnh thức trên đề mục thiền quán.
4. **Định căn (Samādhindriya)**: Khả năng an trú tâm nhất cảnh.
5. **Tuệ căn (Paññindriya)**: Trí tuệ thấy rõ Tứ Thánh Đế.

### V. Năm Lực (Pañca Balāni — 5 pháp)
Khi Năm Căn được tôi luyện thuần thục, chúng trở thành Năm Sức Mạnh (Lực) đập tan mọi chướng ngại:
- **Tín lực** thắng Hoài nghi.
- **Tấn lực** thắng Biếng nhác.
- **Niệm lực** thắng Thất niệm.
- **Định lực** thắng Trạo cử và Phóng dật.
- **Tuệ lực** thắng Si mê và Vô minh.

### VI. Bảy Giác Chi (Satta Bojjhaṅgā — 7 pháp)
Bảy yếu tố dẫn thẳng đến sự bừng sáng của Tuệ Giác Ngộ:
1. **Niệm giác chi (Satisambojjhaṅgo)**.
2. **Trạch pháp giác chi (Dhammavicayasambojjhaṅgo)**: Khả năng phân tích thiện ác, đúng sai.
3. **Tinh tấn giác chi (Viriyasambojjhaṅgo)**.
4. **Hỷ giác chi (Pītisambojjhaṅgo)**: Niềm hoan hỷ thanh tịnh khi thực hành Pháp.
5. **Khinh an giác chi (Passaddhisambojjhaṅgo)**: Sự an tịnh, thư thái của thân và tâm.
6. **Định giác chi (Samādhisambojjhaṅgo)**.
7. **Xả giác chi (Upekkhāsambojjhaṅgo)**: Tâm xả ly, bình thản không dao động trước tám ngọn gió đời.

### VII. Bát Chánh Đạo (Ariya Aṭṭhaṅgika Magga — 8 pháp)
Chánh Kiến, Chánh Tư Duy, Chánh Ngữ, Chánh Nghiệp, Chánh Mạng, Chánh Tinh Tấn, Chánh Niệm, Chánh Định.
EOF
,
                'tags' => ['Bodhipakkhiya', 'Trợ Đạo', 'Tứ Niệm Xứ', 'Giác Chi', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Bodhipakkhiyā Dhammā', 'meaning' => '37 Pháp Bồ Đề Phần — các yếu tố trợ giúp giác ngộ'],
                    ['term' => 'Iddhipāda', 'meaning' => 'Như Ý Túc — bốn nền tảng dẫn đến thần thông và định lực'],
                    ['term' => 'Bojjhaṅga', 'meaning' => 'Thất Giác Chi — bảy chi phần của bậc giác ngộ'],
                    ['term' => 'Upekkhā', 'meaning' => 'Tâm Xả — sự điềm tĩnh không thiên lệch, buông xả chấp thủ'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 10,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(12),
            ],

            // =========================================================================
            // 5. NĂM UẨN VÀ NĂM THỦ UẨN (PAÑCAKKHANDHĀ & UPĀDĀNAKKHANDHĀ)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Năm Uẩn (Pañcakkhandhā) & Năm Thủ Uẩn — Giải Mã Cấu Trúc Thân Tâm Của Con Người',
                'pali_title' => 'Pañcakkhandhā & Upādānakkhandhā',
                'slug' => 'nam-uan-pancakkhandha-va-nam-thu-uan-giai-ma-than-tam',
                'category' => 'phap-hoc',
                'excerpt' => 'Phân tích bản chất sinh diệt của Sắc, Thọ, Tưởng, Hành, Thức và sự khác biệt trọng yếu giữa Năm Uẩn tự nhiên và Năm Thủ Uẩn chấp thủ tạo thành Khổ đế.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ Kinh (Saṃyutta Nikāya 22 Khandha Saṃyutta)',
                'content' => <<< 'EOF'
## 1. Khái Niệm Năm Uẩn Trong Phật Giáo Nguyên Thủy

Đức Phật dạy rằng cái gọi là "tôi", "con người", "chúng sinh" thực chất chỉ là sự kết hợp tạm thời của 5 nhóm thành tố biến dịch không ngừng nghỉ gọi là **Năm Uẩn (Pañcakkhandhā)**:

```mermaid
graph TD
    A[Năm Uẩn Pañcakkhandhā] --> B[1. Sắc Uẩn Rūpakkhandha - Vật chất]
    A --> C[2. Thọ Uẩn Vedanakkhandha - Cảm giác]
    A --> D[3. Tưởng Uẩn Saññakkhandha - Nhận thức]
    A --> E[4. Hành Uẩn Saṅkhārakkhandha - Tâm hành]
    A --> F[5. Thức Uẩn Viññāṇakkhandha - Tri giác]
```

---

## 2. Chi Tiết Bản Chất Từng Uẩn

### I. Sắc Uẩn (Rūpakkhandha)
Toàn bộ phần vật lý, thể xác gồm:
- **Tứ Đại Chủng (Cattāri Mahābhūtāni)**: Đất (Pathavī - tính cứng mềm), Nước (Āpo - tính kết dính), Lửa (Tejo - nhiệt độ nóng lạnh), Gió (Vāyo - sự di động, co dãn).
- **24 Sắc Y Đại Sinh (Upādārūpa)**: Mắt, tai, mũi, lưỡi, thân căn, sắc, thinh, hương, vị, xúc...

### II. Thọ Uẩn (Vedanākkhandha)
Khả năng cảm nhận của tâm khi tiếp xúc trần cảnh:
- **Thọ Lạc (Sukha)**: Cảm giác êm dịu, dễ chịu.
- **Thọ Khổ (Dukkha)**: Cảm giác đau đớn, khó chịu.
- **Thọ Xả (Upekkhā)**: Cảm giác trung tính, không lạc không khổ.

### III. Tưởng Uẩn (Saññākkhandha)
Khả năng ghi nhận, nhận diện và hồi tưởng các dấu hiệu của đối tượng (như màu sắc, âm thanh, hình dáng, khái niệm quen thuộc).

### IV. Hành Uẩn (Saṅkhārakkhandha)
Toàn bộ các trạng thái tâm lý tạo tác (gồm 50 tâm sở trong Vi Diệu Pháp), tiêu biểu như:
- Tác ý (Cetanā - Nghiệp).
- Tham (Lobha), Sân (Dosa), Si (Moha).
- Vô tham (Alobha), Vô sân (Adosa), Vô si (Amoha / Paññā).
- Tinh tấn, Niệm, Hỷ, Tàm, Quý...

### V. Thức Uẩn (Viññāṇakkhandha)
Sự nhận biết thuần túy đối tượng qua 6 giác quan: Nhãn thức, Nhĩ thức, Tỷ thức, Thiệt thức, Thân thức, Ý thức.

---

## 3. Sự Khác Biệt Giữa Năm Uẩn & Năm Thủ Uẩn (Upādānakkhandhā)

- **Năm Uẩn (Khandhā)**: Là tiến trình vật lý và tâm lý tự nhiên sinh diệt. Một vị Phật hay A-la-hán vẫn có đủ 5 uẩn sinh hoạt bình thường.
- **Năm Thủ Uẩn (Upādānakkhandhā)**: Là khi tâm có sự **chấp thủ (Upādāna)**, dính mắc coi năm uẩn là "của tôi", "là tôi", "là tự ngã của tôi". Chính Năm Thủ Uẩn này mới là đầu mối của toàn bộ **Khổ Thánh Đế (Dukkha Sacca)**.
EOF
,
                'tags' => ['Năm Uẩn', 'Khandha', 'Thủ Uẩn', 'Tương Ưng Bộ', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Rūpa', 'meaning' => 'Sắc pháp — vật chất, thân thể hữu hình'],
                    ['term' => 'Vedanā', 'meaning' => 'Thọ — cảm giác lạc, khổ hoặc vô ký'],
                    ['term' => 'Saññā', 'meaning' => 'Tưởng — tri giác, nhận biết và ghi nhớ hình bóng'],
                    ['term' => 'Saṅkhāra', 'meaning' => 'Hành — các yếu tố tâm lý tác tạo nên nghiệp'],
                    ['term' => 'Viññāṇa', 'meaning' => 'Thức — năng lực nhận biết của sáu giác quan'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 9,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(11),
            ],

            // =========================================================================
            // 6. THẬP NHỊ XỨ VÀ MƯỜI TÁM GIỚI (ĀYATANA & DHĀTU)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Mười Hai Xứ (Āyatana) & Mười Tám Giới (Dhātu) — Cơ Chế Nhận Thức Thế Giới',
                'pali_title' => 'Dvādasa Āyatanāni & Aṭṭhārasa Dhātuyo',
                'slug' => 'muoi-hai-xu-ayatana-va-muoi-tam-gioi-dhatu-co-che-nhan-thuc',
                'category' => 'phap-hoc',
                'excerpt' => 'Khám phá lục căn, lục trần và lục thức — toàn bộ thế giới kinh nghiệm của con người được soi sáng qua lăng kính Chánh Pháp nguyên thủy.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ (SN 35 Saḷāyatana Saṃyutta)',
                'content' => <<< 'EOF'
## 1. Định Nghĩa "Thế Giới" Của Đức Thế Tôn

Khi được hỏi: *"Bạch Thế Tôn, thế nào gọi là toàn bộ thế giới?"*, Đức Phật đã trả lời trong *Kinh Tất Cả (Sabba Sutta - SN 35.23)*:

> *"Này các Tỳ-kheo, tất cả chính là Mười Hai Xứ: Mắt và Sắc, Tai và Thanh, Mũi và Hương, Lưỡi và Vị, Thân và Xúc, Ý và Pháp. Nếu ai bảo rằng: 'Tôi sẽ chối bỏ Mười Hai Xứ này để chỉ ra một cái Tất Cả khác', người ấy sẽ chỉ nói lời trống rỗng và rơi vào hoang mang."*

```mermaid
graph LR
    A[Mười Hai Xứ] --> B[Sáu Nội Xứ Ajjhattikāyatana]
    A --> C[Sáu Ngoại Xứ Bāhirāyatana]
    
    B --> B1[Mắt Cakkhu, Tai Sota, Mũi Ghāna, Lưỡi Jivhā, Thân Kāya, Ý Mano]
    C --> C1[Sắc Rūpa, Thanh Sadda, Hương Gandha, Vị Rasa, Xúc Phoṭṭhabba, Pháp Dhamma]
```

---

## 2. Mười Tám Giới (Aṭṭhārasa Dhātuyo)

Khi sáu căn tiếp xúc với sáu trần, sáu thức tương ứng lập tức phát sinh, tạo thành **18 Giới (Dhātu)** cấu thành toàn bộ sự tương tác của sự sống:

| Căn (Nội Xứ) | Trần (Ngoại Xứ) | Thức (Tâm Nhận Biết) |
| :--- | :--- | :--- |
| **Nhãn giới** (Cakkhu-dhātu) | **Sắc giới** (Rūpa-dhātu) | **Nhãn thức giới** (Cakkhuviññāṇa-dhātu) |
| **Nhĩ giới** (Sota-dhātu) | **Thanh giới** (Sadda-dhātu) | **Nhĩ thức giới** (Sotaviññāṇa-dhātu) |
| **Tỷ giới** (Ghāna-dhātu) | **Hương giới** (Gandha-dhātu) | **Tỷ thức giới** (Ghānaviññāṇa-dhātu) |
| **Thiệt giới** (Jivhā-dhātu) | **Vị giới** (Rasa-dhātu) | **Thiệt thức giới** (Jivhāviññāṇa-dhātu) |
| **Thân giới** (Kāya-dhātu) | **Xúc giới** (Phoṭṭhabba-dhātu) | **Thân thức giới** (Kāyaviññāṇa-dhātu) |
| **Ý giới** (Mano-dhātu) | **Pháp giới** (Dhamma-dhātu) | **Ý thức giới** (Manoviññāṇa-dhātu) |

---

## 3. Quán Chiếu Lửa Dục Đốt Cháy Mười Hai Xứ (Ādittapariyāya Sutta)

Trong *Kinh Lửa Cháy (Kinh Thuyết Lửa)*, Đức Phật chỉ rõ:
- Mắt đang bốc cháy, Sắc đang bốc cháy, Nhãn thức đang bốc cháy...
- Bốc cháy bởi lửa gì? **Bốc cháy bởi lửa Tham (Rāga), lửa Sân (Dosa), lửa Si (Moha)**, bốc cháy bởi sinh, già, bệnh, chết, sầu bi khổ ưu não!

Thấu hiểu Mười Hai Xứ và Mười Tám Giới giúp hành giả phòng hộ các căn (*Indriyasaṃvara*), không để tham sân khởi sinh khi mắt thấy sắc hay tai nghe tiếng.
EOF
,
                'tags' => ['Āyatana', 'Dhātu', 'Mười Hai Xứ', 'Mười Tám Giới', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Āyatana', 'meaning' => 'Xứ — nơi nương tựa, cửa ngõ sinh khởi nhận thức'],
                    ['term' => 'Dhātu', 'meaning' => 'Giới — các yếu tố đặc tính tự nhiên, phân định ranh giới'],
                    ['term' => 'Indriyasaṃvara', 'meaning' => 'Phòng hộ các căn — giữ gìn chánh niệm khi 6 giác quan tiếp xúc trần cảnh'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 8,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
            ],

            // =========================================================================
            // 7. BỐN TẦNG THÁNH QUẢ VÀ 10 KIẾT SỬ (ARIYA PUGGALA & SAṂYOJANA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Bốn Tầng Thánh Quả (Ariya Puggala) & Mười Kiết Sử (Saṃyojana) — Nấc Thang Đoạn Tận Buộc Ràng',
                'pali_title' => 'Cattāro Ariyamaggaphala & Dasa Saṃyojanāni',
                'slug' => 'bon-tang-thanh-qua-va-muoi-kiet-su-giai-thoat',
                'category' => 'phap-hoc',
                'excerpt' => 'Lộ trình chứng đắc 4 tầng Thánh: Tu-đà-hoàn (Dự Lưu), Tư-đà-hàm (Nhất Lai), A-na-hàm (Bất Lai), A-la-hán (Vô Sanh) tương ứng với việc bẻ gãy 10 xiềng xích kiết sử luân hồi.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tăng Chi Bộ Kinh (AN 10.13) & Trung Bộ Kinh (MN 22)',
                'content' => <<< 'EOF'
## 1. Mười Xiềng Xích Buộc Ràng (Dasa Saṃyojanāni)

**Kiết Sử (Saṃyojana)** là 10 sợi dây trói buộc tâm thức chúng sinh chặt chẽ vào bánh xe luân hồi Tam Giới:

```mermaid
graph TD
    A[10 Kiết Sử Saṃyojana] --> B[Năm Hạ Phần Kiết Sử Orambhāgiya]
    A --> C[Năm Thượng Phần Kiết Sử Uddhambhāgiya]
    
    B --> B1[1. Thân kiến Sakkāyadiṭṭhi]
    B --> B2[2. Hoài nghi Vicikicchā]
    B --> B3[3. Giới cấm thủ Sīlabbataparāmāsa]
    B --> B4[4. Dục ái Kāmarāga]
    B --> B5[5. Sân hận Paṭigha]
    
    C --> C1[6. Sắc ái Rūparāga]
    C --> C2[7. Vô sắc ái Arūparāga]
    C --> C3[8. Ngã mạn Māna]
    C --> C4[9. Trạo cử Uddhacca]
    C --> C5[10. Vô minh Avijjā]
```

---

## 2. Bốn Tầng Thánh Quả (Ariya Puggala)

Tuần tự khi các Thánh Đạo Tuệ (Magga-ñāṇa) sinh khởi, các kiết sử bị bẻ gãy vĩnh viễn:

### 1. Bậc Dự Lưu / Tu-đà-hoàn (Sotāpanna)
- **Kiết sử đoạn trừ**: Đoạn tận hoàn toàn 3 kiết sử đầu:
  1. **Thân kiến (Sakkāya-diṭṭhi)**: Không còn chấp 5 uẩn là tự ngã.
  2. **Hoài nghi (Vicikicchā)**: Tin sâu tuyệt đối vào Phật, Pháp, Tăng và nghiệp quả.
  3. **Giới cấm thủ (Sīlabbata-parāmāsa)**: Không còn mê tín vào các nghi lễ cúng tế vu vơ.
- **Quả vị**: Đóng chặt hoàn toàn 4 đường ác (Địa ngục, Ngạ quỷ, Bàng sinh, A-tu-la), tái sinh tối đa 7 kiếp nữa ở cõi người/trời rồi chắc chắn đắc A-la-hán.

### 2. Bậc Nhất Lai / Tư-đà-hàm (Sakadāgāmī)
- **Kiết sử đoạn trừ**: Đoạn 3 kiết sử đầu và làm **suy giảm nhẹ bớt** 2 kiết sử thứ 4 & 5 (Dục ái và Sân hận).
- **Quả vị**: Chỉ còn trở lại cõi Dục giới này đúng 1 lần duy nhất nữa là chấm dứt khổ đau.

### 3. Bậc Bất Lai / A-na-hàm (Anāgāmī)
- **Kiết sử đoạn trừ**: Đoạn tận hoàn toàn **5 Hạ phần kiết sử** (diệt sạch gốc rễ Dục ái và Sân hận). Bậc A-na-hàm không còn bất kỳ tâm tham dục ái ân hay giận dữ phẫn nộ nào.
- **Quả vị**: Sau khi xả bỏ thân hoại mạng chung, hóa sinh thẳng lên cõi Tịnh Cư Thiên (Suddhāvāsa) thuộc Sắc giới và nhập Niết-bàn tại đó, không bao giờ trở lại cõi Dục.

### 4. Bậc A-La-Hán / Ứng Cúng (Arahant)
- **Kiết sử đoạn trừ**: Đoạn tận hoàn toàn **5 Thượng phần kiết sử**:
  6. Sắc ái (ham thích cõi Sắc giới thiền).
  7. Vô sắc ái (ham thích cõi Vô sắc giới).
  8. Ngã mạn (sự so sánh hơn thua bằng mình).
  9. Trạo cử vi tế.
  10. Vô minh (Avijjā).
- **Quả vị**: Tối thượng giải thoát, việc cần làm đã làm xong, gánh nặng đã đặt xuống, không còn tái sinh vào bất kỳ cõi nào nữa, chứng đắc Vô Dư Niết Bàn (*Parinibbāna*).
EOF
,
                'tags' => ['Thánh Quả', 'Ariya Puggala', 'Kiết Sử', 'Sotapanna', 'Arahant', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Sotāpanna', 'meaning' => 'Dự Lưu — bậc đã bước vào dòng Thánh, không còn thoái đọa'],
                    ['term' => 'Sakadāgāmī', 'meaning' => 'Nhất Lai — bậc chỉ còn trở lại cõi Dục một lần'],
                    ['term' => 'Anāgāmī', 'meaning' => 'Bất Lai — bậc không còn trở lại cõi Dục'],
                    ['term' => 'Arahant', 'meaning' => 'A-la-hán — bậc Ứng Cúng vô lậu, đoạn tận mọi kiết sử'],
                    ['term' => 'Saṃyojana', 'meaning' => 'Kiết sử — sợi dây xiềng xích trói buộc luân hồi'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 9,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(9),
            ],

            // =========================================================================
            // 8. BỐN PHÁP CHÂN ĐẾ TRONG VI DIỆU PHÁP (PARAMATTHA DHAMMĀ)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Bốn Pháp Chân Đế (Cattāri Paramattha Dhammā) — Bản Đồ Thực Tại Tối Hậu Vi Diệu Pháp',
                'pali_title' => 'Paramattha Dhammā',
                'slug' => 'bon-phap-chan-de-vi-dieu-phap-paramattha-dhamma',
                'category' => 'phap-hoc',
                'excerpt' => 'Tổng quan Thắng Pháp (Abhidhamma): Tâm (89/121 Citta), Tâm sở (52 Cetasika), Sắc pháp (28 Rūpa) và Niết-bàn (Nibbāna) — thực tại cùng tột vượt qua ngôn ngữ thế tục.',
                'author' => 'Thắng Pháp Tạng Pāḷi — Thắng Pháp Tập Yếu Luận (Abhidhammattha Saṅgaha)',
                'content' => <<< 'EOF'
## 1. Khái Niệm Chân Đế (Paramattha) & Tục Đế (Sammuti)

Trong Vi Diệu Pháp (Abhidhamma), Phật giáo phân định 2 tầng mức chân lý:
- **Tục Đế (Sammuti-sacca)**: Sự thật chế định, ước lệ của ngôn ngữ thế tục (như xe cộ, nhà cửa, đàn ông, đàn bà, tôi, bạn).
- **Chân Đế (Paramattha-sacca)**: Sự thật cùng tột, các pháp thực tại tự mang đặc tính riêng (*Sabhāva*), không bị biến đổi theo tên gọi quy ước.

```mermaid
graph TD
    A[Bốn Pháp Chân Đế Paramattha Dhammā] --> B[1. Tâm Citta: 89 hoặc 121 thứ]
    A --> C[2. Tâm Sở Cetasika: 52 thứ]
    A --> D[3. Sắc Pháp Rūpa: 28 thứ]
    A --> E[4. Niết Bàn Nibbāna: 1 Pháp Vô Vi]
    
    B --> F[Pháp Hữu Vi Saṅkhata]
    C --> F
    D --> F
    E --> G[Pháp Vô Vi Asaṅkhata]
```

---

## 2. Chi Tiết Bốn Pháp Siêu Lý Cùng Tột

### I. Tâm (Citta — 89/121 Tâm)
Tâm là thực tại có đặc tính nhận biết cảnh (đối tượng). Gồm 4 cõi tâm:
1. **Tâm Dục Giới (Kāmāvacara-citta)**: 54 tâm (gồm 12 tâm bất thiện tham-sân-si, 18 tâm vô nhân, 24 tâm tịnh hảo).
2. **Tâm Sắc Giới (Rūpāvacara-citta)**: 15 tâm (tương ứng 5 tầng thiền định Sắc giới).
3. **Tâm Vô Sắc Giới (Arūpāvacara-citta)**: 12 tâm (tương ứng 4 tầng thiền Vô sắc: Không vô biên xứ, Thức vô biên xứ, Vô sở hữu xứ, Phi tưởng phi phi tưởng xứ).
4. **Tâm Siêu Thế (Lokuttara-citta)**: 8 hoặc 40 tâm (tâm Đạo và tâm Quả của 4 tầng Thánh).

### II. Tâm Sở (Cetasika — 52 Tâm Sở)
Những trạng thái tâm lý đồng sinh, đồng diệt, đồng nương một căn và đồng bắt một cảnh với Tâm:
- **7 Biến hành (Sabbacittasādhāraṇa)**: Xúc, Thọ, Tưởng, Tác ý, Nhất tâm, Mạng quyền, Tác ý (có mặt trong mọi tâm).
- **6 Biệt cảnh (Pakiṇṇaka)**: Tầm, Tứ, Thắng giải, Cần, Hỷ, Dục.
- **14 Bất thiện (Akusala)**: Si, Vô tàm, Vô quý, Phóng dật, Tham, Tà kiến, Ngã mạn, Sân, Tật, Lận, Hối, Hôn trầm, Thụy miên, Hoài nghi.
- **25 Tịnh hảo (Sobhana)**: Tín, Niệm, Tàm, Quý, Vô tham, Vô sân, Hành xả, Tịnh thân, Tịnh tâm, Trí tuệ (Tuệ quyền)...

### III. Sắc Pháp (Rūpa — 28 Sắc Pháp)
Thực tại vật chất không biết cảnh, gồm:
- **4 Sắc Tứ Đại (Mahābhūta)**: Đất, Nước, Lửa, Gió.
- **24 Sắc Y Đại Sinh (Upādā-rūpa)**: 5 sắc thần kinh (mắt, tai, mũi, lưỡi, thân), 4 sắc cảnh giới (sắc, thinh, hương, vị), 2 sắc tính (nam/nữ), sắc trái tim (hadayavatthu), sắc mạng quyền, sắc vật thực, sắc giao giới, sắc biểu tri, sắc biến dịch, sắc tứ tướng (sinh, tiến, dị, diệt).

### IV. Niết-Bàn (Nibbāna — Pháp Vô Vi Tối Hậu)
Pháp duy nhất vô vi (*Asaṅkhata*), không do nhân duyên tạo tác, bất sinh, bất diệt, tịch tĩnh tuyệt đối, là sự dập tắt hoàn toàn mọi ngọn lửa tham, sân, si.
EOF
,
                'tags' => ['Abhidhamma', 'Vi Diệu Pháp', 'Paramattha', 'Citta', 'Nibbana'],
                'pali_terms' => [
                    ['term' => 'Citta', 'meaning' => 'Tâm — thực tại nhận biết cảnh'],
                    ['term' => 'Cetasika', 'meaning' => 'Tâm sở — các trạng thái tâm lý phối hợp cùng tâm'],
                    ['term' => 'Rūpa', 'meaning' => 'Sắc pháp — vật chất tứ đại và sắc y sinh'],
                    ['term' => 'Nibbāna', 'meaning' => 'Niết-bàn — cảnh giới vô vi tịch diệt, đoạn tận phiền não'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 11,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(8),
            ],

            // =========================================================================
            // 9. NGHIỆP & ĐỊNH LUẬT NGHIỆP BÁO (KAMMA NIYĀMA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Nghiệp (Kamma) & Định Luật Nhân Quả — Thập Thiện Nghiệp Đạo Đưa Đến An Lạc',
                'pali_title' => 'Kamma Niyāma & Dasa Kusala Kammapatha',
                'slug' => 'nghiep-kamma-va-dinh-luat-nhan-qua-thap-thien-nghiep-dao',
                'category' => 'phap-hoc',
                'excerpt' => 'Tìm hiểu định luật Nghiệp (Kamma) trong Đạo Phật: Tác ý là nghiệp (Cetanāhaṃ bhikkhave kammaṃ vadāmi), 10 nghiệp ác cần tránh và 10 nghiệp lành đưa đến phước báu tối thắng.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tăng Chi Bộ (AN 6.63) & Trung Bộ Kinh (Tiểu Nghiệp Phân Biệt MN 135)',
                'content' => <<< 'EOF'
## 1. Định Nghĩa Chân Xác Về Nghiệp Trong Phật Giáo

Đức Thế Tôn định nghĩa ngắn gọn và sâu sắc về Nghiệp trong *Tăng Chi Bộ Kinh (Aṅguttara Nikāya)*:

> **"Cetanāhaṃ, bhikkhave, kammaṃ vadāmi; cetayitvā kammaṃ karoti—kāyena vācāya manasā."**<br />
> *"Này các Tỳ-kheo, Như Lai tuyên bố Tác Ý (Cetanā) chính là Nghiệp. Do có tác ý, người ta mới hành động qua Thân, Khẩu hoặc Ý."*

Đạo Phật phủ nhận hoàn toàn thuyết định mệnh tiền định hoặc sự trừng phạt/ban ơn của thượng đế sáng tạo. Mỗi chúng sinh là chủ nhân của nghiệp, là kẻ thừa tự của nghiệp (*Kammadāyādo*).

---

## 2. Mười Nghiệp Bất Thiện (Dasa Akusala Kammapatha)

```mermaid
graph TD
    A[10 Nghiệp Bất Thiện Akusala] --> B[Thân Nghiệp: 3 Pháp]
    A --> C[Khẩu Nghiệp: 4 Pháp]
    A --> D[Ý Nghiệp: 3 Pháp]
    
    B --> B1[1. Sát sinh Pāṇātipāta]
    B --> B2[2. Trộm cắp Adinnādāna]
    B --> B3[3. Tà dâm Kāmesumicchācāra]
    
    C --> C1[4. Nói dối Musāvāda]
    C --> C2[5. Nói đâm thọc Pisuṇavācā]
    C --> C3[6. Nói ác khẩu Pharusavācā]
    C --> C4[7. Nói ỷ ngữ Samphappalāpa]
    
    D --> D1[8. Tham lam Abhijjhā]
    D --> D2[9. Sân hận Byāpāda]
    D --> D3[10. Tà kiến Micchādiṭṭhi]
```

---

## 3. Mười Nghiệp Thiện Lành (Dasa Kusala Kammapatha)

1. **Thân thiện nghiệp**:
   - Phóng sinh, từ bỏ sát hại, nuôi dưỡng lòng từ bi với muôn loài.
   - Bố thí, tôn trọng tài sản người khác.
   - Sống chung thủy, giữ gìn hạnh phúc gia đình.
2. **Khẩu thiện nghiệp**:
   - Nói lời chân thật, không lừa dối.
   - Nói lời hòa giải, hàn gắn bất hòa.
   - Nói lời lịch thiệp, nhã nhặn, dịu dàng.
   - Nói lời có ý nghĩa, đúng lúc, có ích cho đời.
3. **Ý thiện nghiệp**:
   - Không tham lam tài sản của người.
   - Tâm từ ái, mong cho muôn loài được an vui.
   - **Chánh kiến**: Tin sâu nhân quả nghiệp báo, tin có đời này đời sau, tin có các bậc Thánh giác ngộ.
EOF
,
                'tags' => ['Kamma', 'Nghiệp Báo', 'Nhân Quả', 'Thập Thiện', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Cetanā', 'meaning' => 'Tác ý — ý muốn, chủ tâm tạo tác nên hành vi'],
                    ['term' => 'Kamma', 'meaning' => 'Nghiệp — hành động có tác ý tạo ra quả báo'],
                    ['term' => 'Vipāka', 'meaning' => 'Nghiệp quả — quả báo chín muồi của hành vi'],
                    ['term' => 'Kusala', 'meaning' => 'Thiện nghiệp — hành động trong sạch mang lại an lạc'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 8,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(7),
            ],

            // =========================================================================
            // 10. MƯỜI PHÁP BA-LA-MẬT (DASA PĀRAMĪ)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Mười Pháp Ba-La-Mật (Dasa Pāramī) — Hạnh Nguyện Bồ Tát Toàn Hảo Của Bậc Giác Ngộ',
                'pali_title' => 'Dasa Pāramī',
                'slug' => 'muoi-phap-ba-la-mat-dasa-parami-hanh-nguyen-bo-tat',
                'category' => 'phap-hoc',
                'excerpt' => 'Khám phá 10 hạnh Ba-la-mật siêu việt mà Đức Bồ-tát Gotama đã tích lũy qua bốn A-tăng-kỳ và một trăm ngàn đại kiếp để thành tựu quả vị Chánh Đẳng Chánh Giác.',
                'author' => 'Đại Tạng Kinh Pāḷi — Phật Chủng Tính (Buddhavaṃsa) & Hạnh Tạng (Cariyāpiṭaka)',
                'content' => <<< 'EOF'
## 1. Khái Niệm Pāramī (Ba-La-Mật) Trong Phật Giáo Nguyên Thủy

**Pāramī (Ba-la-mật)** bắt nguồn từ chữ *Parama* (tối thượng, thù thắng), chỉ cho những phẩm hạnh đạo đức và tâm linh hoàn hảo được Bồ-tát (Bodhisatta) thực hành với động cơ vô ngã, hướng đến mục đích cứu độ chúng sinh và chứng đắc quả vị Phật.

```mermaid
graph TD
    A[Mười Pháp Ba-La-Mật Dasa Pāramī] --> B[1. Bố Thí Dāna]
    A --> C[2. Trì Giới Sīla]
    A --> D[3. Xuất Gia Nekkhamma]
    A --> E[4. Trí Tuệ Paññā]
    A --> F[5. Tinh Tấn Viriya]
    A --> G[6. Nhẫn Nhục Khanti]
    A --> H[7. Chân Thật Sacca]
    A --> I[8. Quyết Định Adhiṭṭhāna]
    A --> J[9. Tâm Từ Mettā]
    A --> K[10. Tâm Xả Upekkhā]
```

---

## 2. Chi Tiết Mười Ba-La-Mật

1. **Bố thí Ba-la-mật (Dāna Pāramī)**: Xả ly của cải, tài sản, thân mạng và truyền trao Chánh Pháp (Pháp thí là tối thượng) mà không mong cầu đền đáp.
2. **Trì giới Ba-la-mật (Sīla Pāramī)**: Giữ gìn giới hạnh thân khẩu trong sạch không tì vết dù gặp hiểm nguy đến tính mạng.
3. **Xuất gia Ba-la-mật (Nekkhamma Pāramī)**: Tâm xả ly, từ bỏ dục lạc thế tục để tìm cầu đời sống viễn ly thanh tịnh.
4. **Trí tuệ Ba-la-mật (Paññā Pāramī)**: Khả năng thấu triệt thực tướng vạn pháp, phân biệt thiện ác, diệt trừ si mê.
5. **Tinh tấn Ba-la-mật (Viriya Pāramī)**: Lòng dũng mãnh, kiên cường vượt qua mọi khổ nạn để hoàn thành thiện sự.
6. **Nhẫn nhục Ba-la-mật (Khanti Pāramī)**: Sức chịu đựng phi thường trước sự sỉ nhục, đớn đau thể xác và nghịch cảnh mà tâm không khởi sân hận.
7. **Chân thật Ba-la-mật (Sacca Pāramī)**: Sự thủy chung son sắt với chân lý, lời nói luôn đi đôi với việc làm.
8. **Quyết định Ba-la-mật (Adhiṭṭhāna Pāramī)**: Ý chí sắt đá không lay chuyển đối với đại nguyện giải thoát.
9. **Tâm từ Ba-la-mật (Mettā Pāramī)**: Tình thương yêu vô điều kiện bao trùm khắp tất cả muôn loài chúng sinh.
10. **Tâm xả Ba-la-mật (Upekkhā Pāramī)**: Sự điềm tĩnh an nhiên tuyệt đối trước 8 ngọn gió đời (được - mất, khen - chê, vinh - nhục, vui - khổ).

---

## 3. Ba Cấp Độ Tu Tập Ba-La-Mật
- **Hạ phẩm (Pāramī)**: Hy sinh của cải, tài sản bên ngoài.
- **Trung phẩm (Upapāramī)**: Hy sinh các bộ phận thân thể (mắt, tay, chân, máu).
- **Thượng phẩm (Paramattha Pāramī)**: Sẵn sàng hy sinh cả sinh mạng vì Chánh Pháp.
EOF
,
                'tags' => ['Pāramī', 'Ba La Mật', 'Bồ Tát', 'Phật Chủng Tính', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Pāramī', 'meaning' => 'Ba-la-mật — hạnh nguyện toàn hảo đưa đến bờ giác ngộ'],
                    ['term' => 'Dāna', 'meaning' => 'Bố thí — sự buông bỏ lòng bỏn xẻn, chia sẻ tài vật và Pháp'],
                    ['term' => 'Khanti', 'meaning' => 'Nhẫn nhục — sự kham nhẫn không sinh tâm sân hận'],
                    ['term' => 'Adhiṭṭhāna', 'meaning' => 'Quyết định — ý chí kiên định hướng đến mục tiêu cao thượng'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 9,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(6),
            ],

            // =========================================================================
            // 11. BA MƯƠI MỐT CÕI SỐNG TAM GIỚI (31 BHŪMI)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Ba Mươi Mốt Cõi Sống (31 Bhūmi) — Toàn Cảnh Vũ Trụ Quan Tam Giới Theravāda',
                'pali_title' => 'Tiloka & Ekatimsa Bhūmi',
                'slug' => 'ba-muoi-mot-coi-song-31-bhum-tam-gioi-theravada',
                'category' => 'phap-hoc',
                'excerpt' => 'Khảo sát chi tiết 31 cõi sinh tử luân hồi: 4 cõi ác đạo, cõi người, 6 cõi trời Dục giới, 16 cõi Sắc giới thiền và 4 cõi Vô sắc giới theo Thắng Pháp Tạng.',
                'author' => 'Thắng Pháp Tạng Pāḷi — Vi Diệu Pháp & Thanh Tịnh Đạo (Visuddhimagga)',
                'content' => <<< 'EOF'
## 1. Cấu Trúc Tam Giới Trong Phật Giáo Nguyên Thủy

Vũ trụ quan Phật giáo Theravāda mô tả **31 Cõi Sống (Ekatimsa Bhūmi)** nơi chúng sinh trôi lăn tái sinh tùy theo nghiệp lực thiện ác:

```mermaid
graph TD
    A[31 Cõi Sống Tam Giới] --> B[1. Dục Giới Kāmaloka: 11 Cõi]
    A --> C[2. Sắc Giới Rūpaloka: 16 Cõi]
    A --> D[3. Vô Sắc Giới Arūpaloka: 4 Cõi]
    
    B --> B1[4 Cõi Ác Đạo Apāya: Địa ngục, Ngạ quỷ, Bàng sinh, Asura]
    B --> B2[1 Cõi Người Manussa]
    B --> B3[6 Cõi Trời Dục Giới Devaloka]
    
    C --> C1[Sơ Thiền 3 cõi, Nhị Thiền 3 cõi, Tam Thiền 3 cõi, Tứ Thiền 7 cõi]
    D --> D1[Không vô biên, Thức vô biên, Vô sở hữu, Phi tưởng phi phi tưởng]
```

---

## 2. Chi Tiết Các Tầng Cõi

### I. Cõi Dục Giới (Kāma-bhūmi — 11 cõi)
1. **Bốn Cõi Khổ Ác Đạo (Duggati / Apāya)**: Do tạo ác nghiệp nặng (tham, sân, si):
   - **Địa ngục (Niraya)**: Nơi thọ khổ tột cùng để trả quả bất thiện.
   - **Ngạ quỷ (Peta)**: Cõi ma đói, luôn bị dày vò bởi đói khát do lòng bỏn xẻn tham lam.
   - **Súc sinh (Tiracchāna)**: Loài cầm thú sống trong sợ hãi, cấu xé lẫn nhau.
   - **A-tu-la (Asura)**: Loài quỷ thần hung hăng, nhiều sân hận.
2. **Cõi Người (Manussaloka)**: Nơi có sự cân bằng giữa khổ và vui, là mảnh đất lý tưởng nhất để gieo trồng phước báu và tu tập giải thoát.
3. **Sáu Cõi Trời Dục Giới (Chadevaloka)**: Do trì giới và bố thí thanh tịnh:
   - Tứ Thiên Vương (Cātumahārājikā).
   - Đạo Lợi (Tāvatiṃsa - cõi Đế Thích).
   - Dạ Ma (Yāmā).
   - Đâu Suất (Tusita - nơi chư Bồ Tát cư ngụ trước khi giáng trần).
   - Hóa Lạc Thiên (Nimmānaratī).
   - Tha Hóa Tự Tại (Paranimmita-vasavattī).

### II. Cõi Sắc Giới (Rūpa-bhūmi — 16 cõi)
Dành cho hành giả chứng đắc các tầng thiền định Sắc Giới (Jhāna):
- **Sơ thiền (3 cõi)**: Phạm chúng thiên, Phạm phụ thiên, Đại phạm thiên.
- **Nhị thiền (3 cõi)**: Thiểu quang thiên, Vô lượng quang thiên, Quang âm thiên.
- **Tam thiền (3 cõi)**: Thiểu tịnh thiên, Vô lượng tịnh thiên, Biến tịnh thiên.
- **Tứ thiền (7 cõi)**: Quảng quả thiên, Vô tưởng thiên và **5 Cõi Tịnh Cư Thiên (Suddhāvāsa)** chỉ dành riêng cho bậc Thánh Bất Lai (Anāgāmī): Vô phiền, Vô nhiệt, Thiện hiện, Thiện kiến, Sắc cứu cánh thiên.

### III. Cõi Vô Sắc Giới (Arūpa-bhūmi — 4 cõi)
Dành cho các vị đắc 4 tầng định Vô sắc, chỉ tồn tại thuần túy tâm thức (không có sắc thân vật chất):
- Không vô biên xứ thiên (Ākāsānañcāyatana).
- Thức vô biên xứ thiên (Viññāṇañcāyatana).
- Vô sở hữu xứ thiên (Ākiñcaññāyatana).
- Phi tưởng phi phi tưởng xứ thiên (Nevasaññānāsaññāyatana - tuổi thọ lên tới 84.000 đại kiếp).
EOF
,
                'tags' => ['31 Cõi Sống', 'Tam Giới', 'Dục Giới', 'Sắc Giới', 'Vô Sắc Giới', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Bhūmi', 'meaning' => 'Cõi sống, cảnh giới tái sinh của tâm thức'],
                    ['term' => 'Apāya', 'meaning' => 'Bốn cõi ác đạo — địa ngục, ngạ quỷ, bàng sinh, a-tu-la'],
                    ['term' => 'Suddhāvāsa', 'meaning' => 'Tịnh Cư Thiên — năm cõi trời dành riêng cho bậc Thánh A-na-hàm'],
                    ['term' => 'Jhāna', 'meaning' => 'Thiền chi — trạng thái tâm định tĩnh vắng lặng'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 10,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
            ],

            // =========================================================================
            // 12. THIỀN CHỈ & THIỀN QUÁN (SAMATHA & VIPASSANĀ)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Thiền Định (Samatha) & Thiền Tuệ (Vipassanā) — Hai Đôi Cánh Của Sự Giải Thoát',
                'pali_title' => 'Samatha & Vipassanā Bhāvanā',
                'slug' => 'thien-dinh-samatha-va-thien-tue-vipassana-hai-doi-canh-giai-thoat',
                'category' => 'phap-hanh',
                'excerpt' => 'Phân biệt rõ ràng phương pháp tu tập, đề mục và thành quả giữa Thiền Định (Samatha - gom tâm vào một điểm đắc Sơ thiền đến Tứ thiền) và Thiền Tuệ (Vipassanā - quán sát sinh diệt đắc Đạo Quả Niết-bàn).',
                'author' => 'Thanh Tịnh Đạo (Visuddhimagga) & Tăng Chi Bộ Kinh (AN 2.30)',
                'content' => <<< 'EOF'
## 1. Sự Hài Hòa Của Hai Pháp Hành (Samatha & Vipassanā)

Trong *Tăng Chi Bộ Kinh (AN 2.30)*, Đức Thế Tôn dạy:

> *"Này các Tỳ-kheo, có hai pháp này đưa đến giác ngộ. Thế nào là hai? Thiền Chỉ (Samatha) và Thiền Quán (Vipassanā). Khi Thiền Chỉ được tu tập, tâm được phát triển, tham ái được đoạn tận. Khi Thiền Quán được tu tập, tuệ được phát triển, vô minh được đoạn tận."*

```mermaid
graph TD
    A[Tu Tập Tâm Bhāvanā] --> B[1. Thiền Chỉ Samatha - Định]
    A --> C[2. Thiền Quán Vipassanā - Tuệ]
    
    B --> B1[Mục tiêu: Đè nén 5 Triền cái, đắc Định Jhāna]
    B --> B2[Đề mục: 40 Đề mục Kammaṭṭhāna Khái niệm Pannatti]
    
    C --> C1[Mục tiêu: Nhổ tận gốc Vô minh, đắc Niết-bàn Nibbāna]
    C --> C2[Đề mục: Danh Sắc Nāmarūpa Thực tại Paramattha]
```

---

## 2. So Sánh Chi Tiết Giữa Thiền Định & Thiền Tuệ

| Đặc Điểm | Thiền Chỉ (Samatha) | Thiền Quán (Vipassanā) |
| :--- | :--- | :--- |
| **Đối tượng quán sát** | Khái niệm chế định (*Paññatti*) như màu sắc Kasina, tử thi, hơi thở khái niệm | Thực tại chân đế (*Paramattha*): Danh sắc, Tứ đại, Cảm thọ, Tâm hành |
| **Trạng thái tâm** | An trú cố định vào một điểm duy nhất (*Ekaggatā*) | Tỉnh giác quan sát dòng chảy sinh diệt (*Udayabbaya*) |
| **Công năng diệt phiền não** | Đè nén phiền não tạm thời như đá đè cỏ (*Vikkhambhana-pahāna*) | Đoạn tuyệt gốc rễ phiền não bằng tuệ sát (*Samuccheda-pahāna*) |
| **Thành tựu tối cao** | Bốn tầng thiền Sắc giới và Tứ thiền Vô sắc, Thần thông | Trí tuệ thấy rõ Tam Tướng (Vô thường, Khổ, Vô ngã), đắc 4 tầng Thánh quả |

---

## 3. Bảy Giai Đoạn Thanh Tịnh (Thất Thanh Tịnh — Visuddhi)

Theo tác phẩm kinh điển *Thanh Tịnh Đạo (Visuddhimagga)* của Đại đức Buddhaghosa:
1. **Giới thanh tịnh (Sīla-visuddhi)**: Giữ gìn giới bổn trang nghiêm.
2. **Tâm thanh tịnh (Citta-visuddhi)**: Đạt được Cận định (*Upacāra*) hoặc An chỉ định (*Appanā*).
3. **Kiến thanh tịnh (Diṭṭhi-visuddhi)**: Phân biệt rõ Danh pháp (*Nāma*) và Sắc pháp (*Rūpa*).
4. **Đoạn nghi thanh tịnh (Kaṅkhāvitaraṇa-visuddhi)**: Thấy rõ lý Duyên Khởi (*Paticcasamuppada*), dứt sạch nghi ngờ về quá khứ, tương lai.
5. **Đạo phi đạo tri kiến thanh tịnh (Maggāmagga-ñāṇadassana-visuddhi)**: Nhận biết rõ cái gì là chánh đạo, không bị lạc vào các ảo tướng thiền (*Vipassanupakkilesa*).
6. **Đạo thái tri kiến thanh tịnh (Paṭipadā-ñāṇadassana-visuddhi)**: Tuần tự tiến qua 9 tuệ minh sát (Tuệ sinh diệt, Tuệ diệt, Tuệ sợ hãi, Tuệ hiểm họa, Tuệ nhàm chán, Tuệ muốn giải thoát, Tuệ quán chiếu lại, Tuệ hành xả, Tuệ thuận thứ).
7. **Tri kiến thanh tịnh (Ñāṇadassana-visuddhi)**: Chuyển tánh (*Gotrabhū*) chứng đắc Thánh Đạo và Thánh Quả (*Magga-Phala*), chứng ngộ Niết-bàn!
EOF
,
                'tags' => ['Samatha', 'Vipassana', 'Thiền Định', 'Minh Sát', 'Thanh Tịnh Đạo', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Samatha', 'meaning' => 'Thiền Chỉ — phương pháp làm lắng dịu tâm ý, phát triển định lực'],
                    ['term' => 'Vipassanā', 'meaning' => 'Thiền Quán — phương pháp minh sát thực tướng Tam Tướng để phát sinh trí tuệ'],
                    ['term' => 'Visuddhimagga', 'meaning' => 'Thanh Tịnh Đạo — bộ luận thư vĩ đại về Giới - Định - Tuệ'],
                    ['term' => 'Gotrabhū', 'meaning' => 'Chuyển tánh trí — sát-na tâm vượt từ hàng phàm phu bước sang hàng Thánh giả'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 11,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(4),
            ],

            // =========================================================================
            // 13. KINH CHUYỂN PHÁP LUÂN (DHAMMACAKKAPPAVATTANA SUTTA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Chuyển Pháp Luân (Dhammacakkappavattana Sutta) — Bài Pháp Đầu Tiên Của Đấng Toàn Giác',
                'pali_title' => 'Dhammacakkappavattana Sutta',
                'slug' => 'kinh-chuyen-phap-luan-song-ngu-pali-viet',
                'category' => 'kinh-tung',
                'excerpt' => 'Bài kinh lịch sử được Đức Phật thuyết tại Vườn Lộc Uyển (Isipatana) cho nhóm 5 anh em Kiều Trần Như, mở ra kỷ nguyên Phật Pháp trường tồn.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ (SN 56.11)',
                'content' => <<< 'EOF'
## Bối Cảnh Lịch Sử Của Bản Kinh

Sau khi đắc quả Vô Thượng Chánh Đẳng Chánh Giác dưới cội Bồ-đề, vào ngày rằm tháng Āsāḷha (tháng 6 âm lịch), Đức Phật đã đi bộ đến Vườn Nai (Isipatana, Sarnath gần Ba-la-nại) để chuyển bánh xe Pháp đầu tiên cho năm vị Tỳ-kheo đồng tu: Koṇḍañña (Kiều-trần-như), Bhaddiya, Vappa, Mahānāma, và Assaji.

---

## Nguyên Văn Song Ngữ Pāḷi — Việt

> **Evaṃ me sutaṃ: Ekaṃ samayaṃ Bhagavā Bārāṇasiyaṃ viharati Isipatane Migadāye.**<br />
> *Tôi nghe như vầy: Một thời Thế Tôn ngự tại Ba-la-nại, ở Vườn Lộc Uyển.*

> **Tatra kho Bhagavā pañcavaggiye bhikkhū āmantesi:**<br />
> *"Dveme, bhikkhave, antā pabbajitena na sevitabbā. Katame dve? Yo cāyaṃ kāmesu kāmasukhallikānuyogo hīno gammo pothujjaniko anariyo anatthasañhito; yo cāyaṃ attakilamathānuyogo dukkho anariyo anatthasañhito."*<br />
> *Tại đấy, Thế Tôn bảo năm vị Tỳ-kheo: "Này các Tỳ-kheo, có hai cực đoan người xuất gia không nên thực hành. Thế nào là hai? Một là say đắm trong dục lạc — hạ liệt, đê tiện, phàm phu, không xứng bậc Thánh, không mang lại lợi ích; Hai là khổ hạnh ép xác — đau đớn, không xứng bậc Thánh, không mang lại lợi ích."*

> **"Ete te, bhikkhave, ubho ante anupagamma majjhimā paṭipadā Tathāgatena abhisambuddhā cakkhukaraṇī ñāṇakaraṇī upasamāya abhiññāya sambodhāya nibbānāya saṃvattati."**<br />
> *"Này các Tỳ-kheo, từ bỏ hai cực đoan ấy, con đường Trung Đạo (Majjhimā Paṭipadā) được Như Lai chứng ngộ, làm cho mắt sáng, trí sinh, đưa đến an tịnh, thắng trí, giác ngộ và Niết-bàn."*

---

## Thành Quả Của Bài Pháp
Khi Đức Thế Tôn thuyết giảng xong, Tôn giả Koṇḍañña (Kiều-trần-như) đã đắc Pháp Nhãn thanh tịnh (*Dhammacakkhu*):

> *"Yaṅkiñci samudayadhammaṃ sabhantaṃ nirodhadhammanti."*<br />
> *(Phàm bất cứ pháp nào có tánh sinh khởi, pháp ấy đều có tánh đoạn diệt).*

Từ đó Tôn giả được gọi là **Aññā Koṇḍañña (Kiều-trần-như Đã Liễu Ngộ)**, và Tam Bảo (Phật - Pháp - Tăng) chính thức viên mãn xuất hiện trên thế gian.
EOF
,
                'tags' => ['Dhammacakkappavattana', 'Sutta', 'Kinh Tụng', 'Trung Đạo', 'Pali'],
                'pali_terms' => [
                    ['term' => 'Majjhimā Paṭipadā', 'meaning' => 'Trung Đạo — con đường trung dung siêu việt hai cực đoan'],
                    ['term' => 'Dhammacakkhu', 'meaning' => 'Pháp Nhãn — trí tuệ thấy rõ sự sinh diệt của các pháp'],
                    ['term' => 'Isipatana', 'meaning' => 'Vườn Nai (Chư Tiên Đọa Xứ), nơi sơ chuyển Pháp Luân'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 9,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(3),
            ],

            // =========================================================================
            // 14. KINH VÔ NGÃ TƯỚNG (ANATTALAKKHAṆA SUTTA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Vô Ngã Tướng (Anattalakkhaṇa Sutta) — Giáo Huấn Đưa 5 Vị Tỳ-Kheo Đắc Quả A-La-Hán',
                'pali_title' => 'Anattalakkhaṇa Sutta',
                'slug' => 'kinh-vo-nga-tuong-anattalakkhana-sutta-pali-viet',
                'category' => 'phap-hoc',
                'excerpt' => 'Bài kinh thứ hai Đức Phật thuyết giảng tại Vườn Nai, phân tích tường tận tính chất Vô Thường, Khổ não và Vô Ngã của Năm Uẩn.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ (Saṃyutta Nikāya 22.59)',
                'content' => <<< 'EOF'
## Bối Cảnh Bản Kinh Vô Ngã Tướng

Sau khi thuyết Kinh Chuyển Pháp Luân và năm vị Tỳ-kheo đã chứng đắc quả vị Dự Lưu (Sotāpanna), Đức Thế Tôn tiếp tục thuyết **Kinh Vô Ngã Tướng (Anattalakkhaṇa Sutta)** tại Vườn Lộc Uyển để dứt trừ toàn bộ vi tế ngã chấp, đưa cả năm vị đồng chứng quả vị A-la-hán giải thoát tối hậu.

---

## Quán Chiếu Năm Uẩn Là Vô Ngã

```mermaid
graph TD
    A[Năm Uẩn Pañcakkhandhā] --> B[Sắc Rūpa - Thể xác]
    A --> C[Thọ Vedanā - Cảm giác]
    A --> D[Tưởng Saññā - Nhận thức]
    A --> E[Hành Saṅkhāra - Tâm hành]
    A --> F[Thức Viññāṇa - Tri giác]
    
    B --> G[Đều là Vô Thường Anicca]
    C --> G
    D --> G
    E --> G
    F --> G
    
    G --> H[Là Khổ Dukkha]
    H --> I[Là Vô Ngã Anattā]
```

### 1. Sắc Thân Là Vô Ngã (Rūpaṃ Anattā)
> *"Này các Tỳ-kheo, sắc là vô ngã. Nếu sắc là tự ngã, thì sắc này không thể bị bệnh hoạn, và người ta có thể ra lệnh cho sắc: 'Mong rằng sắc của tôi như thế này, mong rằng sắc của tôi không như thế kia!' Nhưng vì sắc là vô ngã, nên sắc phải chịu bệnh tật và không ai có thể sai khiến sắc theo ý muốn."*

### 2. Thọ, Tưởng, Hành, Thức Là Vô Ngã
Đức Phật tiếp tục giảng giải tương tự cho bốn uẩn còn lại: Thọ (Vedanā), Tưởng (Saññā), Hành (Saṅkhāra), Thức (Viññāṇa).

---

## Đối Thoại Khai Thị Chân Lý Vô Thường — Khổ — Vô Ngã

Đức Thế Tôn hỏi năm vị Tỳ-kheo:
- *"Này các Tỳ-kheo, Sắc là thường hay vô thường?"*
- — *"Bạch Thế Tôn, là vô thường (Aniccaṃ, bhante)."*
- *"Cái gì vô thường là khổ hay lạc?"*
- — *"Bạch Thế Tôn, là khổ (Dukkhaṃ, bhante)."*
- *"Cái gì vô thường, khổ, chịu sự biến hoại, có hợp lý chăng khi quán xét: 'Cái này là của tôi, cái này là tôi, cái này là tự ngã của tôi'?"*
- — *"Bạch Thế Tôn, chắc chắn là không (No hetaṃ, bhante)."*

---

## Chân Ngôn Quán Chiếu Siêu Việt (Anatta Mantra)

> **"N'etaṃ mama, n'eso'hamasmi, na meso attā."**<br />
> *(Cái này không phải của tôi, cái này không phải là tôi, cái này không phải tự ngã của tôi).*

Khi nghe bài kinh này, tâm của năm vị Tỳ-kheo hoàn toàn xả ly, đoạn tận mọi lậu hoặc (*āsavā*), không còn chấp thủ, và thế gian lúc bấy giờ có sáu vị A-la-hán (gồm Đức Phật và 5 vị đệ tử).
EOF
,
                'tags' => ['Anatta', 'Vô Ngã', 'Năm Uẩn', 'Tương Ưng Bộ', 'A-la-hán'],
                'pali_terms' => [
                    ['term' => 'Anattā', 'meaning' => 'Vô Ngã — không có một bản ngã hay linh hồn độc lập, bất biến'],
                    ['term' => 'Anicca', 'meaning' => 'Vô Thường — luôn biến dịch, sinh diệt không ngừng nghỉ'],
                    ['term' => 'Pañcakkhandhā', 'meaning' => 'Năm Uẩn (Sắc, Thọ, Tưởng, Hành, Thức)'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 8,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(2),
            ],

            // =========================================================================
            // 15. KINH TỨ NIỆM XỨ (SATIPAṬṬHĀNA SUTTA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Tứ Niệm Xứ (Satipaṭṭhāna Sutta) — Con Đường Độc Nhất Đưa Đến Thanh Tịnh',
                'pali_title' => 'Mahāsatipaṭṭhāna Sutta',
                'slug' => 'thien-tu-niem-xu-satipatthana-huong-dan-thuc-hanh-vipassana',
                'category' => 'phap-hanh',
                'excerpt' => 'Văn bản cốt lõi nhất của Thiền Minh Sát Vipassanā: Quán Thân, Quán Thọ, Quán Tâm, Quán Pháp — con đường thẳng tiến đến Niết-bàn giải thoát.',
                'author' => 'Đại Tạng Kinh Pāḷi — Trung Bộ Kinh (MN 10) & Trường Bộ (DN 22)',
                'content' => <<< 'EOF'
## Lời Tuyên Bố Lịch Sử Của Đức Thế Tôn

Trong *Kinh Đại Niệm Xứ (Mahāsatipaṭṭhāna Sutta)*, Đức Phật đã khẳng định giá trị vô song của pháp hành này:

> **"Ekāyano ayaṃ, bhikkhave, maggo sattānaṃ visuddhiyā, sokaparidevānaṃ samatikkamāya, dukkhadomanassānaṃ atthaṅgamāya, ñāyassa adhigamāya, nibbānassa sacchikiriyāya, yadidaṃ cattāro satipaṭṭhānā."**<br />
> *"Này các Tỳ-kheo, đây là con đường độc nhất (Ekāyano Maggo) đưa đến sự thanh tịnh cho chúng sinh, vượt khỏi sầu não, chấm dứt khổ ưu, thành tựu chánh trí, chứng ngộ Niết-bàn. Đó là Bốn Niệm Xứ."*

---

## Bốn Đối Tượng Quán Chiếu (Cattāro Satipaṭṭhānā)

```mermaid
graph TD
    A[Tứ Niệm Xứ Satipaṭṭhāna] --> B[1. Quán Thân Trên Thân - Kāyānupassanā]
    A --> C[2. Quán Thọ Trên Thọ - Vedanānupassanā]
    A --> D[3. Quán Tâm Trên Tâm - Cittānupassanā]
    A --> E[4. Quán Pháp Trên Pháp - Dhammānupassanā]
```

### I. Quán Thân Nơi Thân (Kāyānupassanā)
- **Niệm hơi thở (Ānāpānasati)**: Nhận biết rõ ràng khi thở vào dài, thở ra dài; thở vào ngắn, thở ra ngắn.
- **Bốn oai nghi (Iriyāpatha)**: Khi đi biết đang đi, khi đứng biết đang đứng, khi ngồi biết đang ngồi, khi nằm biết đang nằm.
- **Tỉnh giác trong mọi cử động (Sampajañña)**: Bước tới, thối lui, co tay, duỗi chân đều có chánh niệm soi sáng.
- **Quán 32 thể trược (Paṭikūlamanasikāra)**: Thấy rõ thân này gồm tóc, lông, móng, răng, da, thịt, gân, xương...
- **Quán Tứ Đại (Dhātumanasikāra)**: Nhận rõ yếu tố Đất (cứng/mềm), Nước (kết dính), Lửa (nóng/lạnh), Gió (chuyển động).

### II. Quán Thọ Nơi Thọ (Vedanānupassanā)
Khi cảm thọ khởi lên, hành giả ghi nhận trực tiếp:
- **Cảm thọ Lạc (Sukha-vedanā)**: Thấy rõ cảm giác dễ chịu sinh khởi và biến diệt, không đắm nhiễm.
- **Cảm thọ Khổ (Dukkha-vedanā)**: Thấy rõ sự đau nhức, khó chịu sinh khởi và hoại diệt, không sân hận.
- **Cảm thọ Xả (Adukkhamasukha-vedanā)**: Nhận biết trạng thái trung tính, không lạc không khổ.

### III. Quán Tâm Nơi Tâm (Cittānupassanā)
Nhận biết tâm trạng ngay trong giây phút thực tại:
- Tâm có tham biết tâm có tham; tâm không tham biết tâm không tham.
- Tâm có sân biết tâm có sân; tâm không sân biết tâm không sân.
- Tâm có si biết tâm có si; tâm định tĩnh biết tâm định tĩnh.

### IV. Quán Pháp Nơi Pháp (Dhammānupassanā)
Quán chiếu các hiện tượng tâm linh theo giáo pháp:
- Quán Năm Triền Cái (Nīvaraṇa): Tham dục, Sân hận, Hôn trầm, Trạo cử, Hoài nghi.
- Quán Năm Uẩn (Khandha): Sự sinh diệt của Sắc, Thọ, Tưởng, Hành, Thức.
- Quán Sáu Nội Ngoại Xứ (Āyatana): Mắt và sắc, Tai và thanh, Mũi và hương...
- Quán Bảy Giác Chi (Bojjhaṅga): Niệm, Trạch pháp, Tinh tấn, Hỷ, Khinh an, Định, Xả.
- Quán Tứ Thánh Đế (Ariyasacca).
EOF
,
                'tags' => ['Satipatthana', 'Vipassana', 'Pháp Hành', 'Chánh Niệm', 'Thiền'],
                'pali_terms' => [
                    ['term' => 'Satipaṭṭhāna', 'meaning' => 'Tứ Niệm Xứ — sự thiết lập vững chắc của Chánh Niệm'],
                    ['term' => 'Sampajañña', 'meaning' => 'Tỉnh giác — sự hiểu biết sáng suốt, trọn vẹn trong hành động'],
                    ['term' => 'Vipassanā', 'meaning' => 'Minh Sát Tuệ — tuệ giác thấy rõ thực tướng Vô thường, Khổ, Vô ngã'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 12,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(2),
            ],

            // =========================================================================
            // 16. KINH TỪ BI (KARAṆĪYAMETTĀ SUTTA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Từ Bi (Karaṇīyamettā Sutta) — Năng Lượng Chữa Lành & Rải Tâm Từ Khắp Mười Phương',
                'pali_title' => 'Karaṇīyamettā Sutta',
                'slug' => 'kinh-tu-bi-metta-sutta-pali-viet',
                'category' => 'kinh-tung',
                'excerpt' => 'Bài kinh hộ trì (Paritta) tuyệt mỹ mở rộng lòng thương yêu vô lượng không phân biệt đến tất cả muôn loài chúng sinh.',
                'author' => 'Đại Tạng Kinh Pāḷi — Khuddakapāṭha (Kinh Tiểu Tụng) & Sutta Nipāta',
                'content' => <<< 'EOF'
## Ý Nghĩa Thiêng Liêng Của Kinh Từ Bi

Đức Thế Tôn thuyết giảng **Kinh Từ Bi (Karaṇīyamettā Sutta)** tại Kỳ Viên Tịnh Xá (Jetavana) trao tặng cho các Tỳ-kheo vào rừng sâu tu thiền bị chư thiên và dạ-xoa quấy nhiễu. Khi chư Tăng tụng đọc và rải tâm từ, chư thiên đã hoan hỷ trở thành những vị hộ pháp bảo bọc cho các hành giả thiền định.

---

## Nguyên Văn Pāḷi — Việt

> **"Karaṇīyamatthakusalena, yanta santaṃ padaṃ abhisamecca:<br />
> Sakko ujū ca sūjū ca, suvaco cassa mudu anatimānī."**<br />
> *(Người mong cầu an lạc, thấu triệt cảnh giới tịch tịnh cần phải thực hành: Có năng lực, ngay thẳng, hết sức ngay thẳng, dễ dạy, nhu hòa và không kiêu mạn).*

> **"Santussako ca subharo ca, appakicco ca sallahukavutti;<br />
> Santindriyo ca nipako ca, appagabbho kulesvananugiddho."**<br />
> *(Biết tri túc, dễ nuôi dưỡng, ít bận rộn, nếp sống thanh bần giản dị, lục căn thanh tịnh, có trí tuệ chín chắn, không thô lỗ và không quyến luyến các gia đình).*

> **"Mātā yathā niyaṃ puttaṃ, āyusā ekaputtamanurakkhe;<br />
> Evampi sabbabhūtesu, mānasaṃ bhāvaye aparimāṇaṃ."**<br />
> *(Như người mẹ chở che đứa con duy nhất của mình bằng chính sinh mạng; Cũng vậy, đối với tất cả chúng sinh, hãy mở rộng tâm từ bi vô lượng không ngần mé).*

> **"Mettañca sabbalokasmiṃ, mānasaṃ bhāvaye aparimāṇaṃ;<br />
> Uddhaṃ adho ca tiriyañca, asambādhaṃ averamasapattaṃ."**<br />
> *(Hãy rải tâm từ ái cùng khắp thế gian — phía trên, phía dưới, bốn phương tám hướng — không hạn lượng, không chướng ngại, không hận thù, không oan trái).*
EOF
,
                'tags' => ['Metta Sutta', 'Kinh Từ Bi', 'Paritta', 'Tâm Từ', 'Pali'],
                'pali_terms' => [
                    ['term' => 'Mettā', 'meaning' => 'Tâm Từ — tình thương yêu thanh tịnh, vô điều kiện đối với muôn loài'],
                    ['term' => 'Appamaññā', 'meaning' => 'Vô lượng tâm — không biên giới, không ngăn cách'],
                    ['term' => 'Averam', 'meaning' => 'Không hận thù — dập tắt mầm mống oán kết'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 6,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(1),
            ],

            // =========================================================================
            // 17. KINH GIÁO GIỚI KĀLĀMA (KESAMUTTI SUTTA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Giáo Giới Kālāma (Kālāma Sutta) — Tuyên Ngôn Tự Do Tư Tưởng & Chánh Tín Của Đạo Phật',
                'pali_title' => 'Kesamutti Sutta',
                'slug' => 'kinh-giao-gioi-kalama-tuyen-ngon-tu-do-tu-tuong-chanh-tin',
                'category' => 'phap-hoc',
                'excerpt' => 'Bài kinh khai thị vĩ đại về thái độ tiếp nhận chân lý: 10 điều chớ vội tin và tiêu chuẩn tối hậu để kiểm chứng thiện ác giải thoát.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tăng Chi Bộ Kinh (Aṅguttara Nikāya 3.65)',
                'content' => <<< 'EOF'
## Bối Cảnh Lịch Sử Tại Thị Trấn Kesaputta

Khi Đức Phật cùng đại chúng du hóa đến thị trấn Kesaputta của bộ tộc Kālāma, các thiện nam tín nữ Kālāma đã đến bạch hỏi Đức Thế Tôn về nỗi hoang mang tột cùng của họ khi nhiều giáo sĩ và đạo sư thuộc các môn phái khác nhau đi qua, ai nấy đều hết lời ca ngợi giáo thuyết của mình và chê bai, bài xích giáo lý của kẻ khác.

---

## Mười Tiêu Chuẩn "Chớ Vội Tin" (10 Không Tin Mù Quáng)

Đức Thế Tôn đã ban cho người dân Kālāma bài học lịch sử về tư duy phản biện và chánh kiến:

> **"Etha tumhe, Kālāmā, mā anussavena, mā paramparāya, mā itikirāya, mā piṭakasampadānena, mā takkahetu, mā nayahetu, mā ākārapariwitakkena, mā diṭṭhinijjhānakkhantiyā, mā bhabbarūpatāya, mā samaṇo no garūti."**

1. **Chớ vội tin vì nghe truyền thuyết (Anussavena)** — dù chuyện đó có xa xưa đến đâu.
2. **Chớ vội tin vì truyền thống lâu đời (Paramparāya)** — dù đã qua nhiều thế hệ.
3. **Chớ vội tin vì lời đồn đại dân gian (Itikirāya)**.
4. **Chớ vội tin chỉ vì điều đó được ghi trong kinh sách (Piṭakasampadānena)**.
5. **Chớ vội tin vì suy luận lý thuyết (Takkahetu)**.
6. **Chớ vội tin vì định đề diễn dịch (Nayahetu)**.
7. **Chớ vội tin sau khi suy tính dữ kiện bề ngoài (Ākārapariwitakkena)**.
8. **Chớ vội tin vì định kiến chủ quan của mình thấy vừa ý (Diṭṭhinijjhānakkhantiyā)**.
9. **Chớ vội tin chỉ vì uy thế hay vẻ ngoài khả tín của người nói (Bhabbarūpatāya)**.
10. **Chớ vội tin vì nghĩ rằng: "Vị Sa-môn này là bậc thầy tôn kính của chúng ta" (Samaṇo no garūti)**.

---

## Tiêu Chuẩn Thẩm Định Chân Lý Cốt Lõi

> *"Này người Kālāma, khi nào tự thân các ngươi biết rõ như thật:*<br />
> *- 'Các pháp này là bất thiện; Các pháp này là có tội; Các pháp này bị người trí chỉ trích; Các pháp này nếu chấp nhận và thực hành sẽ dẫn đến bất hạnh, khổ đau' — thì các ngươi hãy từ bỏ chúng.*<br />
> *- Khi nào tự thân các ngươi biết rõ như thật: 'Các pháp này là thiện; Các pháp này không có tội; Các pháp này được người trí tán thán; Các pháp này nếu thực hành sẽ đưa đến lợi ích, an lạc' — thì các ngươi hãy thành tựu và an trú trong đó."*
EOF
,
                'tags' => ['Kalama Sutta', 'Chánh Kiến', 'Tăng Chi Bộ', 'Tư Do Tư Tưởng', 'Pali'],
                'pali_terms' => [
                    ['term' => 'Ehipassiko', 'meaning' => 'Hãy đến để thấy — đặc tính của Chánh Pháp cần được tự mình kiểm chứng'],
                    ['term' => 'Kusala', 'meaning' => 'Thiện pháp — hành động trong sạch mang lại an lạc'],
                    ['term' => 'Akusala', 'meaning' => 'Bất thiện pháp — hành động xuất phát từ Tham, Sân, Si mang lại khổ đau'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 9,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(1),
            ],

            // =========================================================================
            // 18. KINH CHÂU BÁU (RATANA SUTTA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Châu Báu (Ratana Sutta) — Bài Kinh Hộ Trì Giải Trừ Tam Tai Thành Vesālī',
                'pali_title' => 'Ratana Sutta',
                'slug' => 'kinh-chau-bau-ratana-sutta-giai-tru-tam-tai-pali-viet',
                'category' => 'kinh-tung',
                'excerpt' => 'Bài kinh Paritta hộ trì thiêng liêng tán thán bảo vật Tam Bảo Phật - Pháp - Tăng, đẩy lùi dịch bệnh, nạn đói và sự quấy nhiễu của phi nhân.',
                'author' => 'Đại Tạng Kinh Pāḷi — Khuddakapāṭha 6 & Sutta Nipāta 2.1',
                'content' => <<< 'EOF'
## Bối Cảnh Lịch Sử Cứu Độ Thành Vesālī

Khi kinh đô Vesālī (Tỳ-xá-li) của vương quốc Licchavī lâm vào thảm cảnh tam tai: Hạn hán mất mùa đưa đến nạn đói khủng khiếp, dịch bệnh truyền nhiễm hoành hành khiến xác người nằm la liệt, và các loài phi nhân độc ác xâm nhập hãm hại cư dân. Các vương công Licchavī đã đến thỉnh Đức Phật và Tăng đoàn ngự giá quang lâm. 

Đức Phật đã truyền dạy Tôn giả Ānanda học thuộc **Kinh Châu Báu (Ratana Sutta)**, dùng nước trong bình bát của Phật rảy khắp kinh thành. Nhờ oai lực tán thán ân đức Tam Bảo, toàn bộ tà khí, dịch bệnh và phi nhân đã tan biến, mang lại mưa lành và sự an lạc tuyệt đối.

---

## Trích Đoạn Kinh Văn Pāḷi — Việt

> **"Yānīdha bhūtāni samāgatāni, bhummāni vā yāni va antalikkhe;<br />
> Sabbeva bhūtā sumanā bhavantu, athopi sakkacca suṇantu bhāsitaṃ."**<br />
> *(Tất cả chư địa thần hay thiên thần trong hư không đã vân tập về nơi đây; Cầu mong tất cả đều được hỷ lạc, và hãy lắng lòng cung kính lắng nghe lời kinh này).*

> **"Yaṅkiñci vittaṃ idha vā huraṃ vā, saggesu vā yaṃ ratanaṃ paṇītaṃ;<br />
> Na no samaṃ atthi Tathāgatena, idampi Buddhe ratanaṃ paṇītaṃ;<br />
> Etena saccena suvatthi hotu!"**<br />
> *(Bao nhiêu tài sản ở cõi này hay cõi khác, hoặc châu báu thù thắng trên các cõi trời, không có gì sánh bằng Đức Như Lai. Nơi Đức Phật chính là châu báu tối thượng. Do chân lý này, cầu mong muôn loài được an lành!)*

> **"Khayaṃ virāgaṃ amataṃ paṇītaṃ, yadajjhagā Sakyamunī samāhito;<br />
> Na tena dhammena samatthi kiñci, idampi Dhamme ratanaṃ paṇītaṃ;<br />
> Etena saccena suvatthi hotu!"**<br />
> *(Cảnh giới tịch diệt, ly tham, bất tử thù thắng mà Đức Thích-ca Mâu-ni trong thiền định đã chứng đắc; Không có pháp nào sánh bằng Pháp bảo tối thượng ấy. Nơi Giáo Pháp chính là châu báu tối thượng. Do chân lý này, cầu mong muôn loài được an lành!)*
EOF
,
                'tags' => ['Ratana Sutta', 'Kinh Châu Báu', 'Paritta', 'Hộ Trì', 'Pali'],
                'pali_terms' => [
                    ['term' => 'Ratana', 'meaning' => 'Châu báu quý báu vô song trên thế gian'],
                    ['term' => 'Suvatthi hotu', 'meaning' => 'Cầu mong sự an lành, thịnh vượng, may mắn'],
                    ['term' => 'Khayaṃ virāgaṃ', 'meaning' => 'Sự đoạn tận và ly tham đối với mọi ái dục'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 7,
                'is_published' => true,
                'published_at' => Carbon::now()->subHours(18),
            ],

            // =========================================================================
            // 19. THẬP NHỊ NHÂN DUYÊN (PAṬICCASAMUPPĀDA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Thập Nhị Nhân Duyên (Paṭiccasamuppāda) — Mắt Xích Sinh Diệt Của Bánh Xe Luân Hồi',
                'pali_title' => 'Paṭiccasamuppāda',
                'slug' => 'thap-nhi-nhan-duyen-paticcasamuppada-nguyen-ly-duyen-khoi',
                'category' => 'phap-hoc',
                'excerpt' => 'Giáo lý uyên áo bậc nhất chỉ rõ tiến trình sinh khởi và đoạn diệt của 12 nhân duyên: Vô minh sinh Hành, Hành sinh Thức... đưa đến toàn bộ khối khổ đau.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ Kinh (Saṃyutta Nikāya 12)',
                'content' => <<< 'EOF'
## Tuyên Ngôn Duyên Khởi Tối Thượng

Đức Phật từng dạy Tôn giả Ānanda trong *Trường Bộ Kinh (Mahānidāna Sutta)*:

> *"Này Ānanda, đừng nói như vậy! Pháp Duyên Khởi này thật là thâm sâu và có tướng trạng thâm sâu. Chính vì không hiểu biết, không thấu triệt pháp này mà chúng sinh bị rối ren như một cuộn chỉ, vướng mắc như tổ chim, không thể thoát khỏi vòng luân hồi khổ não."*

---

## Tiến Trình 12 Mắt Xích Duyên Sinh (Chiều Thuận — Samudaya)

```mermaid
graph TD
    A[1. Vô Minh Avijjā] --> B[2. Hành Saṅkhāra]
    B --> C[3. Thức Viññāṇa]
    C --> D[4. Danh Sắc Nāmarūpa]
    D --> E[5. Lục Nhập Saḷāyatana]
    E --> F[6. Xúc Phassa]
    F --> G[7. Thọ Vedanā]
    G --> H[8. Ái Taṇhā]
    H --> I[9. Thủ Upādāna]
    I --> J[10. Hữu Bhava]
    J --> K[11. Sinh Jāti]
    K --> L[12. Lão Tử, Sầu Bi Khổ Ưu Não Jarāmaraṇa]
```

            // =========================================================================
            // 20. TAM QUY & NGŨ GIỚI (TISARAṆA & PAÑCASĪLA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Tam Quy (Tisaraṇa) & Ngũ Giới (Pañcasīla) — Nền Tảng Giới Hạnh Của Người Cư Sĩ Phật Tử',
                'pali_title' => 'Tisaraṇa & Pañcasīla',
                'slug' => 'tam-quy-ngu-gioi-tisarana-pancasila-nen-tang-gioi-hanh',
                'category' => 'phap-hoc',
                'excerpt' => 'Nương tựa Ba Ngôi Báu (Phật - Pháp - Tăng) và giữ gìn 5 giới căn bản: không sát sinh, không trộm cắp, không tà dâm, không nói dối, không say sưa — chiếc áo giáp tâm linh hộ trì an lạc đời này và đời sau.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tiểu Bộ (Khuddakapāṭha) & Tương Ưng Bộ (Saṃyutta Nikāya 55)',
                'content' => <<< 'EOF'
## 1. Tam Quy (Tisaraṇa) — Ba Nơi Nương Tựa Tối Thượng

Trong Phật giáo Theravāda, bước đầu tiên bước vào cửa Đạo là phát nguyện quy y **Tam Bảo (Ratanattaya)**:

```mermaid
graph TD
    A[Tam Quy Tisaraṇa] --> B[1. Phật Bảo Buddharatana]
    A --> C[2. Pháp Bảo Dhammaratana]
    A --> D[3. Tăng Bảo Saṅgharatana]
    
    B --> B1[Buddhaṃ saraṇaṃ gacchāmi - Nương tựa Bậc Giác Ngộ Toàn Tri]
    C --> C1[Dhammaṃ saraṇaṃ gacchāmi - Nương tựa Chân Lý Thực Chứng]
    D --> D1[Saṅghaṃ saraṇaṃ gacchāmi - Nương tựa Đoàn Thể Thánh Chúng]
```

- **Buddhaṃ saraṇaṃ gacchāmi**: Con đem hết lòng thành kính xin quy y Phật — Đấng Toàn Giác đã tự mình khám phá ra chân lý và chỉ đường diệt khổ.
- **Dhammaṃ saraṇaṃ gacchāmi**: Con đem hết lòng thành kính xin quy y Pháp — Giáo pháp do Đức Phật khéo thuyết giảng, thiết thực hiện tại, đến để mà thấy, tự mình chứng nghiệm.
- **Saṅghaṃ saraṇaṃ gacchāmi**: Con đem hết lòng thành kính xin quy y Tăng — Bậc Thánh chúng đệ tử Thế Tôn thực hành chân chánh, là ruộng phước tối thượng của thế gian.

---

## 2. Ngũ Giới (Pañcasīla) — Năm Chuẩn Mực Đạo Đức Bất Biến

Đức Phật dạy rằng giữ gìn 5 giới là đem lại sự không sợ hãi (*Abhaya*), không hận thù (*Avera*), không tổn hại (*Abyāpajja*) đến cho vô lượng chúng sinh:

```mermaid
graph LR
    A[Ngũ Giới Pañcasīla] --> B[1. Tránh Sát Sinh Pāṇātipātā]
    A --> C[2. Tránh Trộm Cắp Adinnādānā]
    A --> D[3. Tránh Tà Dâm Kāmesumicchācārā]
    A --> E[4. Tránh Nói Dối Musāvādā]
    A --> F[5. Tránh Say Sưa Surāmeraya]
```

### Chi Tiết Năm Học Giới:
1. **Pāṇātipātā veramaṇī sikkhāpadaṃ samādiyāmi**: Con xin vâng giữ điều học là kiêng tránh sát hại sinh mạng muôn loài. Nuôi dưỡng lòng từ bi và tôn trọng sự sống.
2. **Adinnādānā veramaṇī sikkhāpadaṃ samādiyāmi**: Con xin vâng giữ điều học là kiêng tránh lấy của không cho. Nuôi dưỡng lòng thanh liêm, tôn trọng tài sản người khác.
3. **Kāmesumicchācārā veramaṇī sikkhāpadaṃ samādiyāmi**: Con xin vâng giữ điều học là kiêng tránh tà dâm, lang chạ bất chính. Bảo vệ hạnh phúc gia đình và sự chung thủy.
4. **Musāvādā veramaṇī sikkhāpadaṃ samādiyāmi**: Con xin vâng giữ điều học là kiêng tránh nói dối, nói lời đâm thọc, thô ác và phù phiếm. Tôn trọng sự thật và uy tín.
5. **Surāmerayamajjapamādaṭṭhānā veramaṇī sikkhāpadaṃ samādiyāmi**: Con xin vâng giữ điều học là kiêng tránh uống rượu và các chất say gây mê mờ, buông lung, mất tự chủ tâm trí.

---

## 3. Năm Lợi Ích Của Người Trì Giới Trong Sạch (Kinh Đại Bát Niết Bàn)

Đức Thế Tôn dạy trong *Trường Bộ Kinh (DN 16)*:
1. Có được khối tài sản lớn nhờ siêng năng và không phóng dật.
2. Tiếng thơm đồn xa khắp mười phương.
3. Tự tin, không sợ hãi khi bước vào bất kỳ hội chúng nào (vua chúa, trí thức, tu sĩ).
4. Lúc lâm chung tâm trí sáng suốt, bình thản, không mê mờ hốt hoảng.
5. Sau khi thân hoại mạng chung, sinh vào cõi lành, thiên giới an vui.
EOF
,
                'tags' => ['Tam Quy', 'Ngũ Giới', 'Tisaraṇa', 'Pañcasīla', 'Cư Sĩ', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Tisaraṇa', 'meaning' => 'Tam Quy — ba nơi nương tựa vững chắc: Phật, Pháp, Tăng'],
                    ['term' => 'Pañcasīla', 'meaning' => 'Ngũ Giới — năm điều học đạo đức của người tại gia'],
                    ['term' => 'Sīla', 'meaning' => 'Giới hạnh — sự ngăn giữ thân khẩu không làm điều ác'],
                    ['term' => 'Samādiyāmi', 'meaning' => 'Xin nguyện thọ trì và thực hành nghiêm mật'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 8,
                'is_published' => true,
                'published_at' => Carbon::now()->subHours(5),
            ],

            // =========================================================================
            // 21. TỨ VÔ LƯỢNG TÂM (BRAHMAVIHĀRA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Tứ Vô Lượng Tâm (Brahmavihāra) — Từ, Bi, Hỷ, Xả: Bốn Cung Bậc Tâm Vô Cùng Tận',
                'pali_title' => 'Cattāro Brahmavihārā',
                'slug' => 'tu-vo-luong-tam-brahmavihara-tu-bi-hy-xa',
                'category' => 'phap-hoc',
                'excerpt' => 'Bốn trạng thái tâm cao thượng của bậc Phạm Thiên: Từ (Mettā), Bi (Karuṇā), Hỷ (Muditā), Xả (Upekkhā) — con đường chuyển hóa sân hận, oán kết và mở rộng tình thương bao la đến muôn loài.',
                'author' => 'Đại Tạng Kinh Pāḷi — Trường Bộ Kinh (Kinh Tevijja DN 13) & Thanh Tịnh Đạo (Visuddhimagga IX)',
                'content' => <<< 'EOF'
## 1. Khái Niệm Tứ Vô Lượng Tâm (Brahmavihāra)

**Brahmavihāra** (Phạm Trú) là cảnh giới an trú tâm vô lượng, thanh cao tựa như tâm của chư Phạm Thiên. Bốn tâm này không có giới hạn ranh giới (vô lượng - *Appamaññā*), trải rộng không phân biệt thân sơ:

```mermaid
graph TD
    A[Tứ Vô Lượng Tâm Brahmavihāra] --> B[1. Tâm Từ Mettā]
    A --> C[2. Tâm Bi Karuṇā]
    A --> D[3. Tâm Hỷ Muditā]
    A --> E[4. Tâm Xả Upekkhā]
    
    B --> B1[Ước nguyện chúng sinh an vui - Đối trị Sân Hận]
    C --> C1[Xót thương chúng sinh đau khổ - Đối trị Tàn Bạo]
    D --> D1[Vui mừng khi thấy người khác thành công - Đối trị Ganh Tỵ]
    E --> E1[Bình thản trước thăng trầm nhân quả - Đối trị Dính Mắc]
```

---

## 2. Bản Chất & Kẻ Thù Trá Hình Của Từng Tâm

Trong luận thư *Thanh Tịnh Đạo (Visuddhimagga)*, mỗi tâm vô lượng đều có một kẻ thù xa (đối nghịch trực diện) và một kẻ thù gần (ngụy trang trá hình dễ gây hiểu lầm):

| Pháp | Ý Nghĩa Chân Thực | Kẻ Thù Xa (Trực diện) | Kẻ Thù Gần (Trá hình hiểm độc) |
| :--- | :--- | :--- | :--- |
| **Từ (Mettā)** | Lòng mong ước chân thành cho muôn loài được an vui hạnh phúc. | **Sân hận (Dosa / Vyāpāda)** | **Tham ái vị kỷ (Rāga / Taṇhā)**: Đội lốt tình thương nhưng đòi hỏi sở hữu. |
| **Bi (Karuṇā)** | Lòng trắc ẩn, đồng cảm sâu sắc muốn xoa dịu nỗi khổ của chúng sinh. | **Tâm tàn bạo, độc ác (Vihiṃsā)** | **Sầu bi u uất (Domanassa)**: Rơi vào đau buồn tuyệt vọng cùng người khác. |
| **Hỷ (Muditā)** | Niềm vui thanh tịnh khi thấy người khác gặt hái phước lành, thành công. | **Ganh ghét, đố kỵ (Issā)** | **Hỷ lạc thế tục (Pahāsa)**: Cười cợt, phấn khích bốc đồng theo dục lạc trần gian. |
| **Xả (Upekkhā)** | Tâm điềm tĩnh, thấy rõ định luật nghiệp báo chi phối, không thiên lệch. | **Tham ái & Sân hận** | **Thờ ơ, ngu muội (Aññāṇupekkha)**: Lãnh đạm, vô cảm, thiếu trí tuệ. |

---

## 3. Lời Dạy Về Rải Tâm Từ Khắp Mười Phương

Đức Phật dạy trong *Trung Bộ Kinh (Majjhima Nikāya)* phương pháp quán rải tâm từ:

> *"Hành giả an trú, biến mãn một phương với tâm câu hành với Từ, cũng vậy phương thứ hai, cũng vậy phương thứ ba, cũng vậy phương thứ tư. Như vậy, cùng khắp thế giới, trên, dưới, bề ngang, cùng khắp mọi nơi, vị ấy an trú biến mãn toàn thể vũ trụ với tâm câu hành với Từ — quảng đại, vô biên, vô lượng, không hận, không sân."*
EOF
,
                'tags' => ['Brahmavihara', 'Tứ Vô Lượng Tâm', 'Metta', 'Karuna', 'Mudita', 'Upekkha', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Brahmavihāra', 'meaning' => 'Phạm Trú — nơi an trú của tâm thanh tịnh cao thượng'],
                    ['term' => 'Mettā', 'meaning' => 'Tâm Từ — lòng yêu thương mong muốn chúng sinh an lạc'],
                    ['term' => 'Karuṇā', 'meaning' => 'Tâm Bi — lòng trắc ẩn muốn cứu vớt nỗi khổ đau'],
                    ['term' => 'Muditā', 'meaning' => 'Tâm Hỷ — niềm vui hoan hỷ trước hạnh phúc của người khác'],
                    ['term' => 'Upekkhā', 'meaning' => 'Tâm Xả — sự bình thản trước biến dịch cuộc đời'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 9,
                'is_published' => true,
                'published_at' => Carbon::now()->subHours(4),
            ],

            // =========================================================================
            // 22. NĂM TRIỀN CÁI (PAÑCA NĪVARAṆĀNI)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Năm Triền Cái (Pañca Nīvaraṇāni) — Nhận Diện & Đoạn Trừ Năm Chướng Ngại Tâm Trói Buộc',
                'pali_title' => 'Pañca Nīvaraṇāni',
                'slug' => 'nam-trien-cai-panca-nivarana-nhan-dien-va-doan-tru',
                'category' => 'phap-hoc',
                'excerpt' => 'Năm bức màn đen tối che lấp trí tuệ: Tham dục (Kāmacchanda), Sân hận (Vyāpāda), Hôn trầm thụy miên (Thīna-middha), Trạo hối (Uddhacca-kukkucca), Hoài nghi (Vicikicchā) và phương thuốc trị liệu của bậc Đạo Sư.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tăng Chi Bộ (AN 5.51) & Tương Ưng Bộ (SN 46.55 Sangaarava Sutta)',
                'content' => <<< 'EOF'
## 1. Bản Chất Của Triền Cái (Nīvaraṇa)

**Triền Cái (Nīvaraṇa)** là những chướng ngại tinh thần che lấp tâm trí, làm suy yếu tuệ giác và ngăn cản hành giả chứng đắc các tầng Thiền Định (*Jhāna*) cũng như Minh Sát Tuệ (*Vipassanā*).

```mermaid
graph TD
    A[Năm Triền Cái Pañca Nīvaraṇāni] --> B[1. Tham Dục Kāmacchanda]
    A --> C[2. Sân Hận Vyāpāda]
    A --> D[3. Hôn Trầm Thụy Miên Thīna-middha]
    A --> E[4. Trạo Cử Hối Quá Uddhacca-kukkucca]
    A --> F[5. Hoài Nghi Vicikicchā]
    
    B --> B1[Ví như nước pha màu sắc sặc sỡ]
    C --> C1[Ví như nước sôi sùng sục bốc hơi]
    D --> D1[Ví như nước đóng rêu bèo che phủ]
    E --> E1[Ví như nước bị gió thổi sóng cuộn trào]
    F --> F1[Ví như nước bị khuấy bùn đục ngầu bóng tối]
```

---

## 2. Ẩn Dụ Năm Bát Nước Trong Kinh Saṅgārava (SN 46.55)

Đức Phật ví tâm trí của người bị Triền Cái chi phối như nhìn mặt mình trong 5 bát nước:
1. **Tham dục**: Như nhìn bóng mình trong bát nước pha phẩm nhuộm xanh, đỏ, vàng — không thể thấy chân thực khuôn mặt.
2. **Sân hận**: Như nhìn bóng mình trong bát nước sôi bốc khói cuồn cuộn — hình ảnh bị bóp méo, bỏng rát.
3. **Hôn trầm thụy miên**: Như nhìn bóng mình trong bát nước đầy rêu bèo phủ kín — tối tăm, mù mịt, không thấy gì.
4. **Trạo cử hối quá**: Như nhìn bóng mình trong bát nước bị gió mạnh thổi sóng sánh chao đảo — hình ảnh vỡ vụn, dao động.
5. **Hoài nghi**: Như nhìn bóng mình trong bát nước bị khuấy đục ngầu bùn đất đặt trong bóng đêm — mờ mịt, ngờ vực.

---

## 3. Pháp Đoạn Trừ Triền Cái Theo Lời Phật Dạy

```mermaid
graph LR
    A[Phương Thuốc Đoạn Trừ] --> B[Quán Bất Tịnh Asubha diệt Tham Dục]
    A --> C[Quán Tâm Từ Mettā diệt Sân Hận]
    A --> D[Khởi Tác Ý Ánh Sáng & Tinh Tấn diệt Hôn Trầm]
    A --> E[Trú Tâm Chỉ Quán & Hơi Thở diệt Trạo Cử]
    A --> F[Học Hỏi Giáo Lý & Phân Tích Chân Đế diệt Hoài Nghi]
```

Khi Năm Triền Cái được lắng dịu hoàn toàn, tâm hành giả trở nên thanh tịnh, nhu nhuyễn, dễ sử dụng, sẵn sàng chứng nhập **Sơ Thiền (Paṭhamajjhāna)** tràn đầy Hỷ (*Pīti*) và Lạc (*Sukha*).
EOF
,
                'tags' => ['Nīvaraṇa', 'Triền Cái', 'Thiền Định', 'Tăng Chi Bộ', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Nīvaraṇa', 'meaning' => 'Triền cái — màn che ngăn lấp tâm trí, cản trở tuệ giác'],
                    ['term' => 'Kāmacchanda', 'meaning' => 'Dục ái triền cái — sự ham muốn thèm khát ngũ trần'],
                    ['term' => 'Vyāpāda', 'meaning' => 'Sân độc triền cái — tâm oán ghét, bất mãn, bực bội'],
                    ['term' => 'Thīna-middha', 'meaning' => 'Hôn trầm thụy miên — sự dã dượi của tâm và lừ đừ của thân'],
                    ['term' => 'Uddhacca-kukkucca', 'meaning' => 'Trạo cử hối quá — tâm lăng xăng tán loạn và ray rứt việc đã qua'],
                    ['term' => 'Vicikicchā', 'meaning' => 'Hoài nghi — sự phân vân, ngờ vực không tin Chánh Pháp'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 9,
                'is_published' => true,
                'published_at' => Carbon::now()->subHours(3),
            ],

            // =========================================================================
            // 23. BÁT PHONG (AṬṬHA LOKADHAMMĀ)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Bát Phong (Aṭṭha Lokadhammā) — Tám Ngọn Gió Thế Gian & Nghệ Thuật Giữ Tâm Bất Động Tự Tại',
                'pali_title' => 'Aṭṭha Lokadhammā',
                'slug' => 'bat-phong-attha-lokadhamma-tam-ngon-gio-doi-va-tam-bat-dong',
                'category' => 'phap-hoc',
                'excerpt' => 'Thấu hiểu tám ngọn gió đời luôn xoay vần: Được - Mất, Danh - Nhục, Chê - Khen, Lạc - Khổ để tâm an nhiên tự tại như vách đá kiên cố trước phong ba bão táp.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tăng Chi Bộ (Aṅguttara Nikāya 8.5 & 8.6 Lokadhamma Sutta)',
                'content' => <<< 'EOF'
## 1. Tám Ngọn Gió Đời (Aṭṭha Lokadhammā)

Trong *Kinh Các Pháp Thế Gian (Lokadhamma Sutta - AN 8.5)*, Đức Thế Tôn dạy rằng có 8 ngọn gió luôn thổi qua cuộc đời của mọi chúng sinh:

```mermaid
graph TD
    A[Bát Phong Aṭṭha Lokadhammā] --> B[Cặp 1: Được & Mất Lābha & Alābha]
    A --> C[Cặp 2: Danh thơm & Tiếng xấu Yasa & Ayasa]
    A --> D[Cặp 3: Chê bai & Khen ngợi Nindā & Pasaṃsā]
    A --> E[Cặp 4: An lạc & Khổ đau Sukha & Dukkha]
    
    B --> F[4 Pháp Thuận Cảnh - Dễ sinh Tâm Tham Dính Mắc]
    C --> F
    D --> F
    E --> F
    
    B --> G[4 Pháp Nghịch Cảnh - Dễ sinh Tâm Sân Phẫn Uất]
    C --> G
    D --> G
    E --> G
```

---

## 2. Sự Khác Biệt Giữa Kẻ Phàm Phu & Bậc Thánh Đệ Tử

### Người Phàm Phu Không Tu Tập:
- Khi gặp pháp thuận (**Được tài sản, Có danh vọng, Được ca tụng, Hưởng an lạc**): Tâm khởi lên tham ái, kiêu ngạo, say đắm và dính mắc.
- Khi gặp pháp nghịch (**Mất tài sản, Mất danh vọng, Bị chỉ trích, Chịu đau khổ**): Tâm chìm trong sân hận, đau đớn, than khóc và tuyệt vọng.
- Người ấy bị Bát Phong trói buộc, xô đẩy trôi dạt trong biển khổ luân hồi.

### Bậc Thánh Đệ Tử Có Trí Tuệ:
- Ngài quán sát rõ ràng: *"Được hay Mất này là vô thường (*Anicca*), là khổ (*Dukkha*), là biến dịch (*Vipariṇāma-dhamma*)."*
- Ngài không hân hoan khi được, không sầu não khi mất. Tâm giải thoát ly trần, an nhiên tĩnh lặng.

---

## 3. Lời Dạy Vàng Trong Kinh Pháp Cú (Dhammapada 81)

> **"Selo yathā ekaghano, vātena na samīrati;<br />
> Evaṃ nindāpasaṃsāsu, na samiñjanti paṇḍitā."**<br />
> *"Như tảng đá kiên cố nguyên khối, không bị gió bão làm rung chuyển;<br />
> Cũng vậy, trước mọi lời khen chê của thế gian, bậc hiền trí luôn giữ tâm an tịnh, không hề lay động."*
EOF
,
                'tags' => ['Bát Phong', 'Lokadhamma', 'Pháp Cú', 'Tăng Chi Bộ', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Lokadhamma', 'meaning' => 'Thế Gian Pháp — tám thực tại xoay vần của cuộc sống'],
                    ['term' => 'Lābha - Alābha', 'meaning' => 'Được tài lợi — Mất mát tài lợi'],
                    ['term' => 'Yasa - Ayasa', 'meaning' => 'Có danh vọng, chức quyền — Mất danh vọng, thất thế'],
                    ['term' => 'Nindā - Pasaṃsā', 'meaning' => 'Bị chê bai, vu khống — Được khen ngợi, tôn vinh'],
                    ['term' => 'Sukha - Dukkha', 'meaning' => 'Hưởng an lạc thân tâm — Chịu đựng khổ não bức bách'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 8,
                'is_published' => true,
                'published_at' => Carbon::now()->subHours(2),
            ],

            // =========================================================================
            // 24. KINH ĐIỀM LÀNH TỐI THƯỢNG (MAHĀ-MAṄGALA SUTTA)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Điềm Lành Tối Thượng (Mahā-maṅgala Sutta) — 38 Hạnh Phúc Chân Thật Của Cuộc Đời',
                'pali_title' => 'Mahā-maṅgala Sutta',
                'slug' => 'kinh-diem-lanh-toi-thuong-maha-mangala-sutta-38-hanh-phuc',
                'category' => 'phap-hoc',
                'excerpt' => 'Bản kinh châu báu chỉ dạy 38 điều điềm lành tối thượng: từ việc gần gũi bạn lành, hiếu dưỡng song thân, nghề nghiệp trong sạch đến tâm bất động trước nghịch cảnh và chứng ngộ Niết-bàn.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tiểu Bộ Kinh (Sutta Nipāta 2.4 & Khuddakapāṭha 5)',
                'content' => <<< 'EOF'
## 1. Duyên Khởi Kinh Điềm Lành (Mahā-maṅgala Sutta)

Tại thành Sāvatthī (Xá-vệ), chư Thiên và nhân loại đã tranh luận suốt 12 năm về câu hỏi: *"Thế nào là Điềm Lành (Hạnh Phúc) Tối Thượng đích thực trong đời?"*. Giữa đêm khuya, một vị Thiên tử hào quang rực rỡ đã đến đảnh lễ và thỉnh cầu Đức Thế Tôn giải đáp.

```mermaid
graph TD
    A[38 Điềm Lành Mahā-maṅgala Sutta] --> B[Tầng 1: Đạo Đức Xã Hội & Môi Trường]
    A --> C[Tầng 2: Bổn Phận Gia Đình & Xã Hội]
    A --> D[Tầng 3: Tu Dưỡng Tâm Tính & Giới Đức]
    A --> E[Tầng 4: Thực Chứng Chân Lý & Giải Thoát]
    
    B --> B1[Tránh kẻ ác, thân cận bậc hiền, ở trú xứ thích hợp]
    C --> C1[Hiếu dưỡng cha mẹ, nuôi nấng vợ con, làm nghề không tội lỗi]
    D --> D1[Khiêm cung, biết đủ, tri ân, nhẫn nại, nghe Pháp đúng thời]
    E --> E1[Thấy Bốn Thánh Đế, tâm không dao động trước Bát Phong, đắc Niết-bàn]
```

---

## 2. Các Bài Kệ Pháp Cốt Lõi Của 38 Điềm Lành

### Kệ 1 — Lựa Chọn Bạn Bè & Môi Trường
> **"Asevanā ca bālānaṃ, paṇḍitānañca sevanā;<br />
> Pūjā ca pūjanīyānaṃ, etaṃ maṅgalamuttamaṃ."**<br />
> *"Không thân cận kẻ ác, gần gũi bậc hiền trí, tôn kính bậc đáng kính — Đó là Điềm Lành Tối Thượng."*

### Kệ 2 — Học Vấn & Giới Hạnh
> *"Học nhiều, nghề khéo léo, giới luật rèn luyện nghiêm, lời nói khéo trau chuốt — Đó là Điềm Lành Tối Thượng."*

### Kệ 3 — Hiếu Nghĩa Gia Đình
> *"Hiếu dưỡng cha và mẹ, chăm sóc vợ và con, hành nghề không tội lỗi — Đó là Điềm Lành Tối Thượng."*

### Kệ 4 — Phẩm Chất Cao Quý
> *"Bố thí, sống đúng pháp, giúp đỡ hàng thân quyến, hành vi không tỳ vết — Đó là Điềm Lành Tối Thượng."*

### Kệ 5 — Rèn Luyện Tâm Trí
> *"Tránh xa việc ác độc, không uống rượu say sưa, siêng năng trong thiện pháp — Đó là Điềm Lành Tối Thượng."*

### Kệ 6 — Đức Tính Tốt Lành
> *"Tôn kính và khiêm tốn, biết đủ và tri ân, đúng thời lắng nghe Pháp — Đó là Điềm Lành Tối Thượng."*

### Kệ 7 — Tinh Thần Cầu Tiến
> *"Nhẫn nại, lời hòa ái, yết kiến các Sa-môn, đúng thời đàm luận Pháp — Đó là Điềm Lành Tối Thượng."*

### Kệ 8 — Đạo Lộ Tu Tập
> *"Tự chế, sống phạm hạnh, thấy rõ Tứ Thánh Đế, thực chứng quả Niết-bàn — Đó là Điềm Lành Tối Thượng."*

### Kệ 9 & 10 — Tâm Bất Động Tối Hậu
> **"Phuṭṭhassa lokadhammehi, cittaṃ yassa na kampati;<br />
> Asokaṃ virajaṃ khemaṃ, etaṃ maṅgalamuttamaṃ."**<br />
> *"Khi tiếp xúc việc đời, tâm không hề rung động, không sầu, sạch bụi trần, an ổn không sợ hãi — Đó là Điềm Lành Tối Thượng."*
EOF
,
                'tags' => ['Mangala Sutta', 'Điềm Lành', 'Hạnh Phúc', 'Tiểu Bộ Kinh', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Maṅgala', 'meaning' => 'Điềm lành — những phước hạnh chân thật đưa đến cát tường'],
                    ['term' => 'Paṇḍita', 'meaning' => 'Bậc trí tuệ — người có chánh kiến và sống theo giới hạnh'],
                    ['term' => 'Asokaṃ', 'meaning' => 'Không sầu não — tâm vượt thoát đau buồn phiền muộn'],
                    ['term' => 'Khemaṃ', 'meaning' => 'An ổn tuyệt đối — cảnh giới tịch tịnh Niết-bàn'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 10,
                'is_published' => true,
                'published_at' => Carbon::now()->subHours(1),
            ],

            // =========================================================================
            // 25. BỐN PHÁP THÀNH TỰU CỦA NGƯỜI CƯ SĨ (KINH DĪGHAJĀṆU)
            // =========================================================================
            [
                'site_domain' => 'theravada',
                'title' => 'Bốn Pháp Thành Tựu Của Người Cư Sĩ (Kinh Dīghajāṇu) — Nghệ Thuật Làm Giàu Chân Chính & Hạnh Phúc Gia Đình',
                'pali_title' => 'Diṭṭhadhammikattha & Samparāyikattha (Dīghajāṇu Sutta)',
                'slug' => 'bon-phap-thanh-tuu-kinh-dighajanu-tai-chanh-hanh-phuc',
                'category' => 'phap-hoc',
                'excerpt' => 'Lời Phật dạy về 4 nguyên tắc thành công tài chánh hiện đời: Siêng năng nghề nghiệp, Gìn giữ tài sản, Thân cận bạn lành, Chi tiêu điều hòa; cùng 4 pháp phước đức tâm linh cho đời sau.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tăng Chi Bộ (Aṅguttara Nikāya 8.54 Dīghajāṇu Sutta)',
                'content' => <<< 'EOF'
## 1. Duyên Khởi Kinh Dīghajāṇu (Vyagghapajja Sutta)

Một thanh niên cư sĩ tên Dīghajāṇu đến đảnh lễ Đức Phật và bạch:

> *"Bạch Đức Thế Tôn, chúng con là những người cư sĩ tại gia, sống thê nhi ràng buộc, dùng hương hoa phấn sáp, thọ hưởng tiền tài châu báu. Kính xin Thế Tôn thuyết giảng giáo pháp giúp chúng con được an lạc, hạnh phúc ngay trong hiện tại (*Diṭṭhadhamma*) và an lạc, hạnh phúc trong tương lai (*Samparāya*)."*

```mermaid
graph TD
    A[Kinh Dīghajāṇu AN 8.54] --> B[I. 4 Pháp Thành Tựu Hiện Tại Diṭṭhadhammikattha]
    A --> C[II. 4 Pháp Thành Tựu Vị Lai Samparāyikattha]
    
    B --> B1[1. Đầy đủ siêng năng Uṭṭhāna-sampadā]
    B --> B2[2. Đầy đủ gìn giữ Ārakkhasampadā]
    B --> B3[3. Bạn bè hiền thiện Kalyāṇamittatā]
    B --> B4[4. Chi tiêu thăng bằng Samajīvitā]
    
    C --> C1[1. Đầy đủ Đức tin Saddhā-sampadā]
    C --> C2[2. Đầy đủ Giới đức Sīla-sampadā]
    C --> C3[3. Đầy đủ Bố thí Cāga-sampadā]
    C --> C4[4. Đầy đủ Trí tuệ Paññā-sampadā]
```

---

## 2. Bốn Pháp Đưa Đến Thịnh Vượng Tài Chánh Hiện Tại

### 1. Đầy Đủ Siêng Năng (Uṭṭhāna-sampadā)
Dù làm bất kỳ nghề nghiệp chân chính nào (kinh doanh, nông nghiệp, công nghệ, quản lý), người cư sĩ phải thuần thục kỹ năng, không biếng nhác, siêng năng học hỏi và chủ động trong công việc.

### 2. Đầy Đủ Gìn Giữ (Ārakkhasampadā)
Tài sản kiếm được bằng mồ hôi nước mắt chân chính phải biết bảo vệ an toàn: không để bị trộm cắp, không bị lửa cháy lũ cuốn, không bị kẻ xấu lừa đảo, không bị người thừa kế bất lương phá tán.

### 3. Làm Bạn Với Người Lành (Kalyāṇamittatā)
Chọn bạn mà chơi: Thân cận những bậc có giới hạnh, có đức tin, có trí tuệ và rộng lượng; tránh xa kẻ cờ bạc, rượu chè, lười biếng, lừa gạt.

### 4. Chi Tiêu Thăng Bằng Điều Hòa (Samajīvitā)
Biết rõ cán cân thu chi của mình, không tiêu xài hoang phí vượt quá thu nhập (*như người ăn quả sung không biết chừng mực*), cũng không bủn xỉn bóp chắt (*như kẻ chết đói bên đống vàng*). Sống vừa vặn, có tích lũy dự phòng.

---

## 3. Bốn Cửa Ngõ Làm Tiêu Tán Tài Sản Cần Tuyệt Đối Tránh

Đức Phật ví 4 cửa ngõ làm rò rỉ tài sản như một hồ nước lớn có 4 cống xả mở toang:
1. **Đam mê nữ sắc, sắc dục buông thả**.
2. **Nghiện ngập rượu chè và các chất say**.
3. **Mê muội cờ bạc, đỏ đen, cá độ**.
4. **Kết giao với bạn bè bất lương xúi giục điều ác**.

Tránh được 4 hố sâu này và thực hành 4 pháp thành tựu, người cư sĩ sẽ xây dựng được đời sống kinh tế vững mạnh, gia đạo êm ấm và tâm linh thăng hoa.
EOF
,
                'tags' => ['Dighajanu', 'Kinh Cư Sĩ', 'Tài Chánh Phật Giáo', 'Tăng Chi Bộ', 'Theravada'],
                'pali_terms' => [
                    ['term' => 'Diṭṭhadhammikattha', 'meaning' => 'Lợi ích hiện tiền — hạnh phúc, an lạc ngay trong đời sống này'],
                    ['term' => 'Samparāyikattha', 'meaning' => 'Lợi ích tương lai — phước báu cho các kiếp sống mai sau'],
                    ['term' => 'Kalyāṇamitta', 'meaning' => 'Thiện tri thức — bạn bè hiền thiện trợ duyên điều lành'],
                    ['term' => 'Samajīvitā', 'meaning' => 'Sống thăng bằng — quản lý tài chánh thu chi hài hòa'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 9,
                'is_published' => true,
                'published_at' => Carbon::now()->subMinutes(30),
            ],
        ];

        // Truncate existing articles for theravada to ensure clean seed without duplicates
        Article::where('site_domain', 'theravada')->delete();

        foreach ($articles as $art) {
            Article::create($art);
        }
    }
}
