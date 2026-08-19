<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use Carbon\Carbon;

class TheravadaContentSeeder extends Seeder
{
    /**
     * Run the database seeds for Authentic Theravāda Canonical Teachings.
     */
    public function run(): void
    {
        $articles = [
            // 1. TỨ THÁNH ĐẾ
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
                'published_at' => Carbon::now()->subDays(6),
            ],

            // 2. KINH CHUYỂN PHÁP LUÂN
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Chuyển Pháp Luân (Dhammacakkappavattana Sutta) — Bài Pháp Đầu Tiên Của Đấng Toàn Giác',
                'pali_title' => 'Dhammacakkappavattana Sutta',
                'slug' => 'kinh-chuyen-phap-luan-song-ngu-pali-viet',
                'category' => 'kinh-tung',
                'excerpt' => 'Bài kinh lịch sử được Đức Phật thuyết tại Vườn Lộc Uyển (Isipatana) cho nhóm 5 anh em Kiều Trần Như, mở ra kỷ nguyên Phật Pháp trường tồn.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ (SN 56.11)',
                'content' => <<< 'EOF'
## 📜 Bối Cảnh Lịch Sử Của Bản Kinh

Sau khi đắc quả Vô Thượng Chánh Đẳng Chánh Giác dưới cội Bồ-đề, vào ngày rằm tháng Āsāḷha (tháng 6 âm lịch), Đức Phật đã đi bộ đến Vườn Nai (Isipatana, Sarnath gần Ba-la-nại) để chuyển bánh xe Pháp đầu tiên cho năm vị Tỳ-kheo đồng tu: Koṇḍañña (Kiều-trần-như), Bhaddiya, Vappa, Mahānāma, và Assaji.

---

## ☸️ Nguyên Văn Song Ngữ Pāḷi — Việt

> **Evaṃ me sutaṃ: Ekaṃ samayaṃ Bhagavā Bārāṇasiyaṃ viharati Isipatane Migadāye.**<br />
> *Tôi nghe như vầy: Một thời Thế Tôn ngự tại Ba-la-nại, ở Vườn Lộc Uyển.*

> **Tatra kho Bhagavā pañcavaggiye bhikkhū āmantesi:**<br />
> *"Dveme, bhikkhave, antā pabbajitena na sevitabbā. Katame dve? Yo cāyaṃ kāmesu kāmasukhallikānuyogo hīno gammo pothujjaniko anariyo anatthasañhito; yo cāyaṃ attakilamathānuyogo dukkho anariyo anatthasañhito."*<br />
> *Tại đấy, Thế Tôn bảo năm vị Tỳ-kheo: "Này các Tỳ-kheo, có hai cực đoan người xuất gia không nên thực hành. Thế nào là hai? Một là say đắm trong dục lạc — hạ liệt, đê tiện, phàm phu, không xứng bậc Thánh, không mang lại lợi ích; Hai là khổ hạnh ép xác — đau đớn, không xứng bậc Thánh, không mang lại lợi ích."*

> **"Ete te, bhikkhave, ubho ante anupagamma majjhimā paṭipadā Tathāgatena abhisambuddhā cakkhukaraṇī ñāṇakaraṇī upasamāya abhiññāya sambodhāya nibbānāya saṃvattati."**<br />
> *"Này các Tỳ-kheo, từ bỏ hai cực đoan ấy, con đường Trung Đạo (Majjhimā Paṭipadā) được Như Lai chứng ngộ, làm cho mắt sáng, trí sinh, đưa đến an tịnh, thắng trí, giác ngộ và Niết-bàn."*

---

## 🌟 Thành Quả Của Bài Pháp
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
                'published_at' => Carbon::now()->subDays(5),
            ],

            // 3. KINH VÔ NGÃ TƯỚNG (ANATTALAKKHAṆA SUTTA)
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Vô Ngã Tướng (Anattalakkhaṇa Sutta) — Giáo Huấn Đưa 5 Vị Tỳ-Kheo Đắc Quả A-La-Hán',
                'pali_title' => 'Anattalakkhaṇa Sutta',
                'slug' => 'kinh-vo-nga-tuong-anattalakkhana-sutta-pali-viet',
                'category' => 'phap-hoc',
                'excerpt' => 'Bài kinh thứ hai Đức Phật thuyết giảng tại Vườn Nai, phân tích tường tận tính chất Vô Thường, Khổ não và Vô Ngã của Năm Uẩn.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ (Saṃyutta Nikāya 22.59)',
                'content' => <<< 'EOF'
## 📜 Bối Cảnh Bản Kinh Vô Ngã Tướng

Sau khi thuyết Kinh Chuyển Pháp Luân và năm vị Tỳ-kheo đã chứng đắc quả vị Dự Lưu (Sotāpanna), Đức Thế Tôn tiếp tục thuyết **Kinh Vô Ngã Tướng (Anattalakkhaṇa Sutta)** tại Vườn Lộc Uyển để dứt trừ toàn bộ vi tế ngã chấp, đưa cả năm vị đồng chứng quả vị A-la-hán giải thoát tối hậu.

---

## ☸️ Quán Chiếu Năm Uẩn Là Vô Ngã

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

## 🔍 Đối Thoại Khai Thị Chân Lý Vô Thường — Khổ — Vô Ngã

Đức Thế Tôn hỏi năm vị Tỳ-kheo:
- *"Này các Tỳ-kheo, Sắc là thường hay vô thường?"*
- — *"Bạch Thế Tôn, là vô thường (Aniccaṃ, bhante)."*
- *"Cái gì vô thường là khổ hay lạc?"*
- — *"Bạch Thế Tôn, là khổ (Dukkhaṃ, bhante)."*
- *"Cái gì vô thường, khổ, chịu sự biến hoại, có hợp lý chăng khi quán xét: 'Cái này là của tôi, cái này là tôi, cái này là tự ngã của tôi'?"*
- — *"Bạch Thế Tôn, chắc chắn là không (No hetaṃ, bhante)."*

---

## 🌟 Chân Ngôn Quán Chiếu Siêu Việt (Anatta Mantra)

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
                'published_at' => Carbon::now()->subDays(4),
            ],

            // 4. KINH TỨ NIỆM XỨ (SATIPAṬṬHĀNA SUTTA)
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Tứ Niệm Xứ (Satipaṭṭhāna Sutta) — Con Đường Độc Nhất Đưa Đến Thanh Tịnh',
                'pali_title' => 'Mahāsatipaṭṭhāna Sutta',
                'slug' => 'thien-tu-niem-xu-satipatthana-huong-dan-thuc-hanh-vipassana',
                'category' => 'phap-hanh',
                'excerpt' => 'Văn bản cốt lõi nhất của Thiền Minh Sát Vipassanā: Quán Thân, Quán Thọ, Quán Tâm, Quán Pháp — con đường thẳng tiến đến Niết-bàn giải thoát.',
                'author' => 'Đại Tạng Kinh Pāḷi — Trung Bộ Kinh (MN 10) & Trường Bộ (DN 22)',
                'content' => <<< 'EOF'
## 🧘 Lời Tuyên Bố Lịch Sử Của Đức Thế Tôn

Trong *Kinh Đại Niệm Xứ (Mahāsatipaṭṭhāna Sutta)*, Đức Phật đã khẳng định giá trị vô song của pháp hành này:

> **"Ekāyano ayaṃ, bhikkhave, maggo sattānaṃ visuddhiyā, sokaparidevānaṃ samatikkamāya, dukkhadomanassānaṃ atthaṅgamāya, ñāyassa adhigamāya, nibbānassa sacchikiriyāya, yadidaṃ cattāro satipaṭṭhānā."**<br />
> *"Này các Tỳ-kheo, đây là con đường độc nhất (Ekāyano Maggo) đưa đến sự thanh tịnh cho chúng sinh, vượt khỏi sầu não, chấm dứt khổ ưu, thành tựu chánh trí, chứng ngộ Niết-bàn. Đó là Bốn Niệm Xứ."*

---

## ☸️ Bốn Đối Tượng Quán Chiếu (Cattāro Satipaṭṭhānā)

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
                'published_at' => Carbon::now()->subDays(4),
            ],

            // 5. KINH TỪ BI (KARAṆĪYAMETTĀ SUTTA)
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Từ Bi (Karaṇīyamettā Sutta) — Năng Lượng Chữa Lành & Rải Tâm Từ Khắp Mười Phương',
                'pali_title' => 'Karaṇīyamettā Sutta',
                'slug' => 'kinh-tu-bi-metta-sutta-pali-viet',
                'category' => 'kinh-tung',
                'excerpt' => 'Bài kinh hộ trì (Paritta) tuyệt mỹ mở rộng lòng thương yêu vô lượng không phân biệt đến tất cả muôn loài chúng sinh.',
                'author' => 'Đại Tạng Kinh Pāḷi — Khuddakapāṭha (Kinh Tiểu Tụng) & Sutta Nipāta',
                'content' => <<< 'EOF'
## 🌸 Ý Nghĩa Thiêng Liêng Của Kinh Từ Bi

Đức Thế Tôn thuyết giảng **Kinh Từ Bi (Karaṇīyamettā Sutta)** tại Kỳ Viên Tịnh Xá (Jetavana) trao tặng cho các Tỳ-kheo vào rừng sâu tu thiền bị chư thiên và dạ-xoa quấy nhiễu. Khi chư Tăng tụng đọc và rải tâm từ, chư thiên đã hoan hỷ trở thành những vị hộ pháp bảo bọc cho các hành giả thiền định.

---

## ☸️ Nguyên Văn Pāḷi — Việt

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
                'published_at' => Carbon::now()->subDays(3),
            ],

            // 6. KINH GIÁO GIỚI KĀLĀMA (KESAMUTTI SUTTA)
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Giáo Giới Kālāma (Kālāma Sutta) — Tuyên Ngôn Tự Do Tư Tưởng & Chánh Tín Của Đạo Phật',
                'pali_title' => 'Kesamutti Sutta',
                'slug' => 'kinh-giao-gioi-kalama-tuyen-ngon-tu-do-tu-tuong-chanh-tin',
                'category' => 'phap-hoc',
                'excerpt' => 'Bài kinh khai thị vĩ đại về thái độ tiếp nhận chân lý: 10 điều chớ vội tin và tiêu chuẩn tối hậu để kiểm chứng thiện ác giải thoát.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tăng Chi Bộ Kinh (Aṅguttara Nikāya 3.65)',
                'content' => <<< 'EOF'
## 📜 Bối Cảnh Lịch Sử Tại Thị Trấn Kesaputta

Khi Đức Phật cùng đại chúng du hóa đến thị trấn Kesaputta của bộ tộc Kālāma, các thiện nam tín nữ Kālāma đã đến bạch hỏi Đức Thế Tôn về nỗi hoang mang tột cùng của họ khi nhiều giáo sĩ và đạo sư thuộc các môn phái khác nhau đi qua, ai nấy đều hết lời ca ngợi giáo thuyết của mình và chê bai, bài xích giáo lý của kẻ khác.

---

## ☸️ Mười Tiêu Chuẩn "Chớ Vội Tin" (10 Không Tin Mù Quáng)

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

## 🌟 Tiêu Chuẩn Thẩm Định Chân Lý Cốt Lõi

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
                'published_at' => Carbon::now()->subDays(2),
            ],

            // 7. KINH CHÂU BÁU (RATANA SUTTA)
            [
                'site_domain' => 'theravada',
                'title' => 'Kinh Châu Báu (Ratana Sutta) — Bài Kinh Hộ Trì Giải Trừ Tam Tai Thành Vesālī',
                'pali_title' => 'Ratana Sutta',
                'slug' => 'kinh-chau-bau-ratana-sutta-giai-tru-tam-tai-pali-viet',
                'category' => 'kinh-tung',
                'excerpt' => 'Bài kinh Paritta hộ trì thiêng liêng tán thán bảo vật Tam Bảo Phật - Pháp - Tăng, đẩy lùi dịch bệnh, nạn đói và sự quấy nhiễu của phi nhân.',
                'author' => 'Đại Tạng Kinh Pāḷi — Khuddakapāṭha 6 & Sutta Nipāta 2.1',
                'content' => <<< 'EOF'
## 📜 Bối Cảnh Lịch Sử Cứu Độ Thành Vesālī

Khi kinh đô Vesālī (Tỳ-xá-li) của vương quốc Licchavī lâm vào thảm cảnh tam tai: Hạn hán mất mùa đưa đến nạn đói khủng khiếp, dịch bệnh truyền nhiễm hoành hành khiến xác người nằm la liệt, và các loài phi nhân độc ác xâm nhập hãm hại cư dân. Các vương công Licchavī đã đến thỉnh Đức Phật và Tăng đoàn ngự giá quang lâm. 

Đức Phật đã truyền dạy Tôn giả Ānanda học thuộc **Kinh Châu Báu (Ratana Sutta)**, dùng nước trong bình bát của Phật rảy khắp kinh thành. Nhờ oai lực tán thán ân đức Tam Bảo, toàn bộ tà khí, dịch bệnh và phi nhân đã tan biến, mang lại mưa lành và sự an lạc tuyệt đối.

---

## ☸️ Trích Đoạn Kinh Văn Pāḷi — Việt

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
                'published_at' => Carbon::now()->subDays(1),
            ],

            // 8. THẬP NHỊ NHÂN DUYÊN (PAṬICCASAMUPPĀDA)
            [
                'site_domain' => 'theravada',
                'title' => 'Thập Nhị Nhân Duyên (Paṭiccasamuppāda) — Mắt Xích Sinh Diệt Của Bánh Xe Luân Hồi',
                'pali_title' => 'Paṭiccasamuppāda',
                'slug' => 'thap-nhi-nhan-duyen-paticcasamuppada-nguyen-ly-duyen-khoi',
                'category' => 'phap-hoc',
                'excerpt' => 'Giáo lý uyên áo bậc nhất chỉ rõ tiến trình sinh khởi và đoạn diệt của 12 nhân duyên: Vô minh sinh Hành, Hành sinh Thức... đưa đến toàn bộ khối khổ đau.',
                'author' => 'Đại Tạng Kinh Pāḷi — Tương Ưng Bộ Kinh (Saṃyutta Nikāya 12)',
                'content' => <<< 'EOF'
## ☸️ Tuyên Ngôn Duyên Khởi Tối Thượng

Đức Phật từng dạy Tôn giả Ānanda trong *Trường Bộ Kinh (Mahānidāna Sutta)*:

> *"Này Ānanda, đừng nói như vậy! Pháp Duyên Khởi này thật là thâm sâu và có tướng trạng thâm sâu. Chính vì không hiểu biết, không thấu triệt pháp này mà chúng sinh bị rối ren như một cuộn chỉ, vướng mắc như tổ chim, không thể thoát khỏi vòng luân hồi khổ não."*

---

## 🔄 Tiến Trình 12 Mắt Xích Duyên Sinh (Chiều Thuận — Samudaya)

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

### 12 Chi Phần Duyên Khởi Chi Tiết:
1. **Do Duyên Vô Minh (Avijjā) sinh Khởi Hành (Saṅkhāra)**: Sự si mê không thấy Tứ Đế tạo tác nên các nghiệp thiện, ác và bất động.
2. **Do Duyên Hành sinh Khởi Thức (Viññāṇa)**: Thức tái sinh nhập vào thai mẹ.
3. **Do Duyên Thức sinh Khởi Danh Sắc (Nāmarūpa)**: Thân thể vật chất và các yếu tố tâm lý hình thành.
4. **Do Duyên Danh Sắc sinh Khởi Lục Nhập (Saḷāyatana)**: 6 giác quan (mắt, tai, mũi, lưỡi, thân, ý).
5. **Do Duyên Lục Nhập sinh Khởi Xúc (Phassa)**: Sự tiếp xúc giữa căn, trần và thức.
6. **Do Duyên Xúc sinh Khởi Thọ (Vedanā)**: Cảm giác lạc, khổ hoặc không lạc không khổ.
7. **Do Duyên Thọ sinh Khởi Ái (Taṇhā)**: Lòng ham muốn khao khát vị kỷ.
8. **Do Duyên Ái sinh Khởi Thủ (Upādāna)**: Sự chấp chặt, bám víu không buông.
9. **Do Duyên Thủ sinh Khởi Hữu (Bhava)**: Tiến trình tạo nghiệp dẫn đến kiếp sống tương lai.
10. **Do Duyên Hữu sinh Khởi Sinh (Jāti)**: Sự chào đời trong kiếp sống mới.
11. **Do Duyên Sinh sinh Khởi Lão Tử (Jarāmaraṇa)**: Già, chết, sầu, bi, khổ, ưu, não.

---

## 🌟 Chiều Nghịch Đoạn Diệt (Nirodha — Con Đường Giải Thoát)

> **"Avijjāya tveva asesavirāganirodhā saṅkhāranirodho..."**<br />
> *"Do Vô minh đoạn diệt không còn dư tàn nên Hành diệt; do Hành diệt nên Thức diệt; do Thức diệt nên Danh sắc diệt... do Sinh diệt nên Lão, Tử, Sầu, Bi, Khổ, Ưu, Não đoạn diệt. Như vậy là sự đoạn diệt hoàn toàn của toàn bộ khối khổ đau này."*
EOF
,
                'tags' => ['Paticcasamuppada', 'Duyên Khởi', 'Tương Ưng Bộ', 'Vô Minh', 'Pali'],
                'pali_terms' => [
                    ['term' => 'Paṭiccasamuppāda', 'meaning' => 'Duyên khởi — cái này có thì cái kia có, cái này diệt thì cái kia diệt'],
                    ['term' => 'Avijjā', 'meaning' => 'Vô minh — không thấy như thật Bốn Chân Lý Thánh'],
                    ['term' => 'Upādāna', 'meaning' => 'Thủ chấp — sự ôm giữ bám chặt vào ngũ dục, tà kiến'],
                ],
                'audio_chanting_url' => null,
                'reading_time_min' => 11,
                'is_published' => true,
                'published_at' => Carbon::now()->subHours(12),
            ],
        ];

        // Truncate existing articles for theravada to ensure clean seed without duplicates
        Article::where('site_domain', 'theravada')->delete();

        foreach ($articles as $art) {
            Article::create($art);
        }
    }
}
