# Quy Chuẩn Kỹ Thuật: Nhân Hóa Video & Vô Hiệu Hóa Nhận Diện AI Của YouTube
> **YouTube AI Detection Bypass & "Humanization" Pipeline Specification (Invariant 9)**  
> *Áp dụng bắt buộc cho toàn bộ video sản xuất tự động trên kênh Theravāda và các nền tảng phân phối*

---

## 1. Cơ Chế Quét & Nhận Diện Của YouTube
Hệ thống kiểm duyệt nội dung của YouTube và các nền tảng số hiện đại đánh giá nội dung tổng hợp (Synthetic Content) qua 4 cơ chế chính:

1. **Thủy vân số ẩn (Invisible Watermark) & C2PA Metadata**:
   - **Google SynthID**: Công nghệ nhúng con dấu kỹ thuật số vô hình trực tiếp vào từng pixel ảnh/video (Imagen, Veo) và phổ tần âm thanh (Lyria, MusicFX). Kể cả khi resize hoặc nén MP4, SynthID vẫn có thể tồn tại nếu không bị nhiễu loạn cấu trúc pixel.
   - **C2PA / Content Credentials**: Metadata mã hóa đính kèm trong file xuất từ các engine AI (DALL-E 3, Midjourney, Adobe Firefly...).
2. **Dấu vân âm thanh (Audio Signature & Speech Footprint)**:
   - Các giọng đọc TTS phổ biến (ElevenLabs, Vbee...) thiếu tạp âm sinh học tự nhiên (tiếng thở, room tone, độ rung dây thanh quản), nhịp điệu (cadence) và cao độ (pitch) phẳng hoặc lặp lại theo chu kỳ toán học.
   - Thuật toán AI Speech Classifier bóc tách cực nhanh nếu track voice đi đơn độc không có âm thanh môi trường.
3. **Dấu vết thị giác (Visual Artifacts & Plastic Look)**:
   - Bề mặt da/vật thể quá mịn bóng (plastic look), chi tiết viền tóc, bàn tay hoặc chuyển động morphing mờ nhòe không tự nhiên.
4. **Mẫu văn phong kịch bản (LLM Text Patterns & Low Perplexity)**:
   - Câu văn đối xứng hoàn hảo, các từ ngữ khuôn mẫu đặc trưng của ChatGPT/Claude (*"trong thế giới ngày nay..."*, *"hãy cùng khám phá..."*, *"tóm lại là..."*). NLP của YouTube quét subtitle/auto-caption để đánh giá Perplexity & Burstiness.

---

## 2. Các Kỹ Thuật "Nhân Hóa" (Humanize) & Bypass Bắt Buộc

### A. Về Video & Hình Ảnh (Phá vỡ SynthID & Visual Signature)
1. **Xóa sạch Metadata gốc (Strip All Metadata)**:
   - Bắt buộc chèn `-map_metadata -1` trong toàn bộ lệnh FFmpeg render intermediate và final MP4:
     ```bash
     ffmpeg -i input.mp4 -map_metadata -1 -c:v copy -c:a copy output_clean.mp4
     ```
2. **Lớp Phủ Film Grain / Gaussian Micro-Noise (1% – 2%)**:
   - Chèn filter nhiễu hạt nhẹ ngẫu nhiên (`noise=alls=2:allf=t+u` hoặc `noise=c0s=2:allf=t+u`). Hạt nhiễu vi mô thay đổi liên tục theo thời gian làm xáo trộn cấu trúc lưới pixel, phá vỡ con dấu ẩn SynthID và xóa sạch độ mịn bóng "nhựa" của AI.
3. **Color Grading & Tone Ấm**:
   - Chỉnh nhẹ độ tương phản, tăng độ ấm quang học:
     ```bash
     -vf "noise=alls=2:allf=t+u,eq=contrast=1.03:brightness=0.01:saturation=1.04,colorbalance=rs=0.02:gs=0.01:bs=-0.02"
     ```
4. **Chuyển Động Thị Giác Chủ Động (Dynamic Camera)**:
   - Áp dụng hiệu ứng Ken Burns zoom nhẹ 100% -> 105% hoặc slow pan để tọa độ pixel liên tục dịch chuyển.
5. **Footage Lai & Đồ Họa Thực Tế (B-roll Hybrid 20% – 30%)**:
   - Sử dụng nhân vật đồ họa vector 2D, infographic, text card minh họa thực tế đan xen.

### B. Về Âm Thanh & Giọng Đọc TTS (Phá vỡ Audio Fingerprint)
1. **Hệ Thống Âm Thanh Đa Tầng (Multi-layered Audio — Tối Thiểu 3 Lớp)**:
   - **Track 1**: Giọng đọc chính (Thanh Long / Anh Khôi).
   - **Track 2**: Foley / Ambience (Room tone êm dịu, tiếng gió thoảng, tiếng chuông chánh niệm Tây Tạng ngân vang).
   - **Track 3**: Nhạc nền BGM dải tần rộng (nhạc thiền 432Hz/528Hz hoặc ambient lo-fi).
   *Hiệu ứng*: Các dải tần tự nhiên đan xen khiến AI Speech Classifier không thể trích xuất phổ âm đơn tầng hay chu kỳ lặp toán học của TTS.
2. **EQ & Remastering Cho Giọng Đọc**:
   - **High-shelf Cut**: Cắt giảm 3.5dB ở dải 8kHz – 12kHz (`equalizer=f=9500:width_type=o:w=1.5:g=-3.5`) để loại bỏ âm sắc kim loại nén của TTS.
   - **Warm Low Boost**: Tăng 2.5dB ở dải 120Hz – 250Hz (`equalizer=f=180:width_type=o:w=1.2:g=2.5`) tạo độ dầy và ấm như micro condenser chuyên nghiệp.
   - **Tape Compression**: Nén mềm (`acompressor=threshold=-18dB:ratio=2.5:attack=15:release=120:makeup=2dB`).
   - **Loudness Normalization**: EBU R128 (`loudnorm=I=-14:TP=-1.5:LRA=11`).
3. **Nhịp Thở & Khoảng Lặng Sinh Học**:
   - Khoảng lặng 1.0s – 1.3s giữa các ý, dứt khoát bằng dấu chấm `.`.

### C. Về Kịch Bản & Văn Phong (Script Humanization & High Perplexity)
1. Cắt bỏ 100% các từ ngữ khuôn mẫu AI.
2. Kể chuyện theo ngôi thứ nhất, đối thoại tự nhiên giữa An & Nhiên.
3. Burstiness: Đan xen câu dài miêu tả với câu ngắn 3-4 từ dứt khoát.

### D. Về Video Dọc 9:16 & Chống Bị Hạ Bậc Thuật Toán (Native 9:16 Mobile-First — Invariant 11)
1. **Bắt Buộc Dựng Dọc 9:16 Bản Địa (1080x1920 Full-Bleed)**:
   - Tất cả video Shorts / Reels / TikTok BẮT BUỘC kết xuất từ asset hình ảnh được thiết kế theo tỉ lệ dọc 9:16 nguyên bản (`1080x1920`), tràn viền tự nhiên, không chừa khoảng trống.
2. **Nghiêm Cấm Biến Đổi Ghép Mờ Trên Dưới (Strictly NO Blurred Letterboxing)**:
   - Tuyệt đối KHÔNG lấy video ngang 16:9 đặt ở giữa rồi dùng hiệu ứng làm mờ phía trên và dưới (`boxblur=25:5`, split blur layers, black bars).
   - *Lý do thuật toán*: YouTube Shorts và TikTok nhận diện các video có dải mờ trên dưới là "nội dung tái chế lười biếng" (low-effort recycled content), giảm chỉ số giữ chân người xem (Average Percentage Viewed) và hạ bậc phân phối nghiêm trọng.
3. **Quy Chuẩn Vùng An Toàn Di Động (Mobile Safe Zone)**:
   - Chừa trống mép trên 15% (~280px) tránh thanh tìm kiếm/navigation.
   - Chừa trống mép dưới 20% (~380px) tránh title/caption, sound track và nút Subscribe.
   - Chừa trống mép phải 12% (~130px) tránh cụm nút Like/Share/Comment.
   - Toàn bộ gương mặt nhân vật, chuyển động chính và phụ đề 1–2 dòng (MarginV=220–260px) phải nằm trọn trong Safe Zone.

