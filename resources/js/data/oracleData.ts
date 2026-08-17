/**
 * Kho Dữ Liệu 24 Quẻ Xăm Âm Dương Ma Cà Tưng
 * Chủ đề: Vận Mệnh, Thơ Xăm, Lời Bình Cương Thi & Lời Khuyên Ngày Mới
 */

export type OracleCategory = 'career' | 'wealth' | 'love' | 'peace';

export type FortuneLevel = 'Thượng Thượng Cát' | 'Đại Cát' | 'Trung Cát' | 'Tiểu Cát' | 'Bình An Cát';

export interface FortunePoem {
  line1: string;
  line2: string;
  line3: string;
  line4: string;
}

export interface OracleRecord {
  id: number;
  title: string;
  level: FortuneLevel;
  element: 'Kim' | 'Mộc' | 'Thủy' | 'Hỏa' | 'Thổ';
  score: number; // 0 - 100
  poem: FortunePoem;
  meanings: {
    career: string;
    wealth: string;
    love: string;
    peace: string;
  };
  mascotAdvice: string;
  luckyColor: string;
  luckyNumber: number;
}

export const ORACLE_CATEGORIES = [
  { id: 'career' as OracleCategory, label: 'Công Danh & Sự Nghiệp', icon: '💼', desc: 'Hỏi về dự án, thăng tiến, chuyển việc, mở lối tương lai.' },
  { id: 'wealth' as OracleCategory, label: 'Tài Lộc & Tiền Bạc', icon: '💰', desc: 'Hỏi về dòng tiền, đầu tư, lộc lá bất ngờ, bội thu tài chính.' },
  { id: 'love' as OracleCategory, label: 'Tình Duyên & Gắn Kết', icon: '❤️', desc: 'Hỏi về người thương, nhân duyên, gia đạo hòa hợp, tri kỷ.' },
  { id: 'peace' as OracleCategory, label: 'Vận Hạn & Bình An', icon: '🌙', desc: 'Hỏi về sức khỏe, tâm an, giải trừ xui xẻo, may mắn hôm nay.' },
];

export const oracleFortunes: OracleRecord[] = [
  {
    id: 1,
    title: 'Quẻ Số 01: Khai Thiên Lập Địa — Vạn Sự Hanh Thông',
    level: 'Thượng Thượng Cát',
    element: 'Kim',
    score: 99,
    poem: {
      line1: 'Đêm đen sương khói rạng đông sang,',
      line2: 'Ma Cà Tưng nhảy mở cõi vàng.',
      line3: 'Gươm báu mài xong chờ xuất trận,',
      line4: 'Muôn dặm trời mây bóng huy hoàng.',
    },
    meanings: {
      career: 'Thời cơ vàng đã chín muồi. Mọi kế hoạch, đề xuất hay dự án mới khởi sự đều có quý nhân nâng đỡ, kết quả vượt mong đợi.',
      wealth: 'Tài lộc dồi dào chảy về như thác. Dòng tiền thuận buồm xuôi gió, đầu tư có lãi lớn.',
      love: 'Nhân duyên hòa hợp như trăng rằm. Người độc thân sắp gặp tri kỷ đồng điệu, lứa đôi thêm gắn kết bền lâu.',
      peace: 'Thân tâm an lạc, vận xui tan biến. Năng lượng tích cực tràn đầy suốt ngày đêm.',
    },
    mascotAdvice: 'Cơ hội như chớp giật nửa đêm, đừng do dự! Hãy tự tin ra quyết định lớn ngay hôm nay.',
    luckyColor: '#ffd166',
    luckyNumber: 8,
  },
  {
    id: 2,
    title: 'Quẻ Số 02: Bát Trạch Nghênh Phong — Lộc Đến Nửa Đêm',
    level: 'Đại Cát',
    element: 'Thủy',
    score: 95,
    poem: {
      line1: 'Gió cuốn mây trôi trăng vằng vặc,',
      line2: 'Lộc biếc cành xuân nảy lộc vàng.',
      line3: 'Chăm chỉ gieo trồng nay hái quả,',
      line4: 'Tiếng cười vang rộn khắp thôn trang.',
    },
    meanings: {
      career: 'Những nỗ lực âm thầm trước đây nay bắt đầu cho quả ngọt. Cấp trên ghi nhận, đồng nghiệp nể phục.',
      wealth: 'Có khoản thu nhập bất ngờ hoặc tiền thưởng xứng đáng. Chi tiêu dư dả.',
      love: 'Đối phương thấu hiểu và chia sẻ. Một buổi hẹn hò ấm áp đang chờ đón.',
      peace: 'Sức khỏe dồi dào, giấc ngủ sâu và bình yên sau những ngày làm việc chăm chỉ.',
    },
    mascotAdvice: 'Uống một tách trà nóng, mỉm cười với mọi người và đón nhận niềm vui bất ngờ nhé!',
    luckyColor: '#00f5a0',
    luckyNumber: 6,
  },
  {
    id: 3,
    title: 'Quẻ Số 03: Long Du Thiển Thủy — Tích Lũy Chờ Thời',
    level: 'Trung Cát',
    element: 'Thổ',
    score: 82,
    poem: {
      line1: 'Rồng lượn sông sâu tạm ẩn mình,',
      line2: 'Trau dồi đạo đức dưỡng chân minh.',
      line3: 'Chờ khi mưa thuận cùng mây chuyển,',
      line4: 'Bay vút trời cao tỏ bóng hình.',
    },
    meanings: {
      career: 'Chưa nên vội vàng thay đổi lớn. Hãy củng cố kỹ năng nền tảng và chuẩn bị kỹ lưỡng trước khi bứt phá.',
      wealth: 'Tài chính ổn định, nên giữ gìn và quản lý chi tiêu chặt chẽ, tránh đầu tư mạo hiểm.',
      love: 'Cần thêm thời gian để lắng nghe và thấu cảm. Đừng nóng vội ép buộc cảm xúc.',
      peace: 'Tâm an thì vạn sự an. Dành thời gian nghỉ ngơi, thư giãn tinh thần.',
    },
    mascotAdvice: 'Lùi một bước để nhảy cao hơn mười bước! Chậm mà chắc là chìa khóa của sự vĩ đại.',
    luckyColor: '#00d2ff',
    luckyNumber: 3,
  },
  {
    id: 4,
    title: 'Quẻ Số 04: Đăng Sơn Vọng Nguyệt — Tầm Nhìn Rộng Mở',
    level: 'Đại Cát',
    element: 'Hỏa',
    score: 92,
    poem: {
      line1: 'Trèo lên đỉnh núi ngắm trăng thanh,',
      line2: 'Mọi sự mông lung hóa tỏ rành.',
      line3: 'Đường hướng tương lai nay đã rõ,',
      line4: 'Chí lớn muôn trùng tất sẽ thành.',
    },
    meanings: {
      career: 'Những bế tắc trước mắt được tháo gỡ. Bạn tìm ra giải pháp sáng tạo mang tính đột phá.',
      wealth: 'Nhìn thấy hướng đi mới giúp gia tăng thu nhập dài hạn bền vững.',
      love: 'Sự thẳng thắn và chân thành giúp hai bên xóa bỏ mọi hiểu lầm.',
      peace: 'Tinh thần minh mẫn, năng lượng sáng tạo bùng nổ.',
    },
    mascotAdvice: 'Khi đứng ở góc nhìn cao hơn, mọi khó khăn trước mắt chỉ là chuyện nhỏ xíu!',
    luckyColor: '#ff4d6d',
    luckyNumber: 9,
  },
  {
    id: 5,
    title: 'Quẻ Số 05: Đắc Đạo Trừ Tà — Vạn Chướng Tiêu Trừ',
    level: 'Thượng Thượng Cát',
    element: 'Mộc',
    score: 98,
    poem: {
      line1: 'Lá bùa dán trán xua tà khí,',
      line2: 'Cương thi nhảy múa đón bình minh.',
      line3: 'Tiểu nhân lùi bước nhường đường sáng,',
      line4: 'Chính nghĩa rạng ngời rạng chữ linh.',
    },
    meanings: {
      career: 'Những thị phi hoặc trở ngại từ ngoại cảnh hoàn toàn bị hóa giải. Bạn làm chủ hoàn toàn tình thế.',
      wealth: 'Tránh được tổn thất tài chính, các khoản nợ nần hay ách tắc tiền bạc được giải quyết êm đẹp.',
      love: 'Tình cảm trong sáng, không bị dao động bởi những lời đàm tiếu bên ngoài.',
      peace: 'Thân tâm thanh tịnh, như có tấm bùa hộ mệnh che chở bình an suốt chặng đường.',
    },
    mascotAdvice: 'Giữ tâm trong sáng, không sợ bóng ma nào cả. Năng lượng tích cực sẽ tự thu hút may mắn!',
    luckyColor: '#00f5a0',
    luckyNumber: 7,
  },
  {
    id: 6,
    title: 'Quẻ Số 06: Hoa Khai Phú Quý — Trăm Hoa Đua Nở',
    level: 'Đại Cát',
    element: 'Mộc',
    score: 94,
    poem: {
      line1: 'Xuân về hoa nở ngát hương bay,',
      line2: 'Vận tốt duyên lành hội tụ đây.',
      line3: 'Ý hợp tâm đầu muôn sự thỏa,',
      line4: 'Hạnh phúc tràn trề nắm trong tay.',
    },
    meanings: {
      career: 'Môi trường làm việc thân thiện, đồng đội hòa hợp, dự án đạt thành tích xuất sắc.',
      wealth: 'Thu nhập tăng trưởng đều đặn, mua sắm được những món đồ ưng ý mang lại niềm vui.',
      love: 'Đào hoa nở rộ, tình cảm mặn nồng thăng hoa, nhiều khoảnh khắc lãng mạn đáng nhớ.',
      peace: 'Gia đạo yên vui, bạn bè sum họp đầm ấm.',
    },
    mascotAdvice: 'Hãy mở rộng trái tim và đón nhận những tình cảm ngọt ngào quanh bạn nhé!',
    luckyColor: '#c084fc',
    luckyNumber: 2,
  },
  {
    id: 7,
    title: 'Quẻ Số 07: Thủy Tinh Ngưng Tụ — Tĩnh Tâm Đắc Trí',
    level: 'Trung Cát',
    element: 'Thủy',
    score: 85,
    poem: {
      line1: 'Nước phẳng lặng trong soi bóng trời,',
      line2: 'Chớ để bụi trần khuấy chơi vơi.',
      line3: 'Giữ dạ kiên trung lòng thanh thản,',
      line4: 'Trí sáng ngời soi vạn nẻo đời.',
    },
    meanings: {
      career: 'Tập trung sâu vào chuyên môn cốt lõi, tránh bị phân tâm bởi quá nhiều công việc râu ria.',
      wealth: 'Thích hợp cho việc tích lũy tiết kiệm hơn là vội vã đầu cơ lướt sóng.',
      love: 'Một khoảng lặng cần thiết để cả hai cùng suy ngẫm và trân trọng nhau hơn.',
      peace: 'Thực hành thiền định, đọc sách hoặc nghe nhạc nhẹ giúp hồi phục năng lượng sâu sắc.',
    },
    mascotAdvice: 'Tĩnh lặng là sức mạnh tối thượng. Càng giữ được bình tĩnh, bạn càng thông thái.',
    luckyColor: '#38bdf8',
    luckyNumber: 4,
  },
  {
    id: 8,
    title: 'Quẻ Số 08: Kim Bảng Đề Danh — Vinh Quy Bái Tổ',
    level: 'Thượng Thượng Cát',
    element: 'Kim',
    score: 100,
    poem: {
      line1: 'Mười năm đèn sách tỏ tài năng,',
      line2: 'Tên ghi bảng vàng sáng ánh trăng.',
      line3: 'Ngẩng cao đầu bước đường danh vọng,',
      line4: 'Công thành danh toại đẹp duyên lành.',
    },
    meanings: {
      career: 'Đỉnh cao của sự nghiệp! Đạt được giải thưởng, chứng chỉ cao cấp, thăng chức hoặc trúng tuyển vị trí mơ ước.',
      wealth: 'Tiền tài danh vọng song toàn, vị thế tài chính được nâng lên một tầm cao mới.',
      love: 'Được người thân và người thương vô cùng tự hào và ủng hộ hết lòng.',
      peace: 'Tâm trạng hoan hỉ, hào khí ngút trời, đi đến đâu cũng được người người quý mến.',
    },
    mascotAdvice: 'Xứng đáng 100 điểm! Đừng quên tự thưởng cho mình một bữa tiệc linh đình nhé!',
    luckyColor: '#ffd166',
    luckyNumber: 1,
  },
];

export const getRandomFortune = (category: OracleCategory): OracleRecord => {
  const index = Math.floor(Math.random() * oracleFortunes.length);
  return oracleFortunes[index];
};
