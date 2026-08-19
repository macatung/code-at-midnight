# 🧘 Ma Cà Tưng & Ma Tọa Thiền — Desktop Companion App (Windows)

Ứng dụng **Desktop Pet / Companion Chánh Niệm & Nhắc Nhở Kinh Kệ Pháp Cú** chạy lơ lửng trên màn hình Windows.

---

## ✨ Tính Năng Nổi Bật

1. **Cửa Sổ Trong Suốt Lơ Lửng (Frameless Transparent Window)**:
   - Mascot bay bổng trên màn hình với nền trong suốt tuyệt đối 100%.
   - Kéo thả tự do (`Drag & Drop`) tới bất kỳ góc màn hình nào bạn thích.
   - Luôn ở trên cùng (`Always-on-Top`) để bạn không bỏ lỡ những khoảnh khắc chánh niệm.

2. **Chuyển Đổi 2 Hình Thái Linh Vật (Persona)**:
   - 🧘 **Ma Tọa Thiền**: Phong cách Phật học Zen, ngồi đài sen ngũ sắc, phát hào quang vàng kim và vòng quay Pháp Luân (Dhamma Wheel).
   - ☕ **Ma Cà Tưng Coder**: Phong cách Midnight Architect, lá bùa hộ mệnh Code 00:00 và tách cà phê bốc khói.

3. **Lời Nhắc Chánh Niệm & Kinh Kệ Pháp Cú (Dhammapada)**:
   - 📜 **Rút Quẻ Kệ Pháp Cú**: Hiển thị kệ số, nguyên văn Pāḷi, thơ dịch tiếng Việt và tuệ giác chiêm nghiệm.
   - 🔔 **Chuông Chánh Niệm 432Hz / 528Hz**: Bộ tổng hợp âm thanh chuông xoay Tây Tạng ngân vang thuần khiết bằng Web Audio API.
   - 🌸 **Điều Tức 3 Nhịp Thở**: Vòng tròn hướng dẫn hít vào tĩnh lặng, thở ra mỉm cười.
   - 💧 **Nhắc Nhở Sức Khỏe**: Uống nước, thư giãn mắt 20-20-20, thả lỏng vai gáy định kỳ.

4. **Khay Hệ Thống (System Tray)**:
   - Icon góc phải Taskbar với menu chuột phải đầy đủ: Ẩn/Hiện, Rút kệ mới, Thỉnh chuông, Đổi hình thái, Cài đặt và Thoát.

---

## 🚀 Hướng Dẫn Khởi Chạy

### 1. Chạy thử nghiệm chế độ Development:
```powershell
cd d:\Work\macatung\desktop
npm run dev
```

### 2. Đóng gói ứng dụng Windows (.exe):
```powershell
cd d:\Work\macatung\desktop
npm run build
```
File cài đặt `.exe` sẽ được tạo tự động trong thư mục `desktop/dist/`.

---

## 🛠️ Cấu Trúc Mã Nguồn

```
desktop/
├── electron/
│   ├── main.ts              # Quản lý BrowserWindow trong suốt & System Tray
│   └── preload.ts           # Bridge API giữa Electron và Vue
├── src/
│   ├── audio/
│   │   └── mindfulBellAudio.ts # Bộ tổng hợp chuông Tây Tạng 432Hz/528Hz
│   ├── components/
│   │   ├── ZenMascotStage.vue          # Mascot Ma Tọa Thiền (Đài sen & Hào quang)
│   │   ├── CoderMascotStage.vue        # Mascot Ma Cà Tưng Coder (Bùa chú & Cà phê)
│   │   ├── DhammapadaSpeechBubble.vue  # Bong bóng kệ Pháp Cú & Lời nhắc
│   │   ├── BreathingPacer.vue          # Vòng tròn điều tức 3 nhịp thở
│   │   └── SettingsModal.vue           # Bảng cài đặt chu kỳ nhắc nhở
│   ├── composables/
│   │   └── useMindfulScheduler.ts      # Bộ đếm giờ tự động định kỳ
│   ├── data/
│   │   └── dhammapadaVerses.ts         # Kho kệ Pháp Cú & Nhắc nhở sức khỏe
│   ├── App.vue                         # Component điều phối chính
│   └── main.ts
├── package.json
└── vite.config.ts
```
