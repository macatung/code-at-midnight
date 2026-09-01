/**
 * Pāḷi Canonical Learning Module Data (Học Tiếng Pāḷi Tipiṭaka)
 * Preserving authentic Theravāda language, grammar paradigms, vocabulary & verse analysis.
 */

export interface PaliVocabularyItem {
  term: string;
  ipa?: string;
  partOfSpeech: 'Danh từ (Nāma)' | 'Động từ (Ākhyāta)' | 'Tính từ (Guṇanāma)' | 'Bất biến từ (Avyaya)' | 'Đại từ (Sabbanāma)' | 'Tiền tố (Upasagga)' | 'Phó từ (Kriyāvisesana)';
  gender?: 'Nam tánh (Pulliṅga)' | 'Nữ tánh (Itthiliṅga)' | 'Trung tánh (Napuṃsakaliṅga)' | 'Bất biến';
  vietnamese: string;
  root?: string;
  note?: string;
  example?: string;
}

export interface PaliGrammarTable {
  headers: string[];
  rows: string[][];
}

export interface PaliGrammarSection {
  title: string;
  explanation: string;
  table?: PaliGrammarTable;
  tip?: string;
}

export interface PaliWordBreakdown {
  word: string;
  grammar: string;
  rootOrStem?: string;
  meaning: string;
}

export interface PaliVerseAnalysis {
  originalPali: string;
  vietnamese: string;
  english?: string;
  context?: string;
  breakdown: PaliWordBreakdown[];
}

export interface PaliQuizQuestion {
  id: string;
  question: string;
  options: string[];
  correctIndex: number;
  explanation: string;
}

export interface PaliLesson {
  id: string;
  slug: string;
  categoryId: string;
  order: number;
  title: string;
  paliTitle: string;
  description: string;
  level: 'Căn bản' | 'Trung cấp' | 'Nâng cao';
  estimatedMinutes: number;
  tags: string[];
  summaryPoints: string[];
  vocabulary: PaliVocabularyItem[];
  grammarSections: PaliGrammarSection[];
  verseAnalysis?: PaliVerseAnalysis;
  quiz: PaliQuizQuestion[];
}

export interface PaliLessonCategory {
  id: string;
  slug: string;
  name: string;
  paliName: string;
  description: string;
  icon: string;
  color: string;
  bgGradient: string;
  level: 'Căn bản' | 'Trung cấp' | 'Nâng cao';
}

export const PALI_LESSON_CATEGORIES: PaliLessonCategory[] = [
  {
    id: 'bang-chu-cai-phat-am',
    slug: 'bang-chu-cai-phat-am',
    name: 'Bảng Chữ Cái & Phát Âm',
    paliName: 'Akkharamālā & Uccāraṇa',
    description: 'Làm quen hệ thống 8 nguyên âm (Sara), 33 phụ âm (Vyañjana) và quy tắc phát âm chuẩn xác trong truyền thống tụng đọc Theravāda.',
    icon: 'BookOpen',
    color: '#f59e0b',
    bgGradient: 'from-amber-500/20 via-stone-900 to-stone-950',
    level: 'Căn bản'
  },
  {
    id: 'ngu-phap-can-ban',
    slug: 'ngu-phap-can-ban',
    name: 'Ngữ Pháp Pāḷi Căn Bản',
    paliName: 'Pāḷi Saddanīti Mūla',
    description: 'Học 8 biến cách danh từ (Aṭṭha Vibhatti), 3 giống (Liṅga), 2 số (Vacana) và thì hiện tại của động từ (Vattamānā Ākhyāta).',
    icon: 'Sparkles',
    color: '#eab308',
    bgGradient: 'from-yellow-500/20 via-stone-900 to-stone-950',
    level: 'Căn bản'
  },
  {
    id: 'tu-vung-cot-loi',
    slug: 'tu-vung-cot-loi',
    name: 'Từ Vựng Giáo Lý Cốt Lõi',
    paliName: 'Dhamma Vohāra & Mūla Padāni',
    description: 'Nắm vững các thuật ngữ nền tảng: Tam Bảo (Tiratana), Tứ Diệu Đế (Cattāri Ariyasaccāni), Bát Chánh Đạo, Ngũ Uẩn và Thập Nhị Nhân Duyên.',
    icon: 'Scroll',
    color: '#f97316',
    bgGradient: 'from-orange-500/20 via-stone-900 to-stone-950',
    level: 'Căn bản'
  },
  {
    id: 'phan-tich-ke-ngon',
    slug: 'phan-tich-ke-ngon',
    name: 'Khảo Sát Kệ Ngôn & Kinh Điển',
    paliName: 'Gāthā & Sutta Vicaya',
    description: 'Phân tích ngữ pháp từng từ (Word-by-word) trong Kinh Pháp Cú (Dhammapada), Kinh Chuyển Pháp Luân và các bài kinh hộ trì Paritta thiêng liêng.',
    icon: 'Compass',
    color: '#d97706',
    bgGradient: 'from-amber-600/20 via-stone-900 to-stone-950',
    level: 'Trung cấp'
  },
  {
    id: 'kinh-tung-thien-mon',
    slug: 'kinh-tung-thien-mon',
    name: 'Kinh Tụng & Tác Bạch Thiền Môn',
    paliName: 'Vandana & Sīla Samādāna',
    description: 'Thấu hiểu ý nghĩa lời kinh tụng hàng ngày: Tam Quy, Ngũ Giới, Kinh Rải Tâm Từ (Mettā Sutta) và nghi thức thiền môn Theravāda.',
    icon: 'Activity',
    color: '#10b981',
    bgGradient: 'from-emerald-500/20 via-stone-900 to-stone-950',
    level: 'Căn bản'
  }
];

export const PALI_LESSONS: PaliLesson[] = [
  // CATEGORY 1: Bảng Chữ Cái & Phát Âm
  {
    id: 'pali-01-nguyen-am-phu-am',
    slug: 'nguyen-am-va-phu-am-pali',
    categoryId: 'bang-chu-cai-phat-am',
    order: 1,
    title: 'Bài 1: Hệ Thống 41 Mẫu Tự Pāḷi (Sara & Vyañjana)',
    paliTitle: 'Paṭhamo Pāṭho: Akkharamālā (Sara ca Vyañjana)',
    description: 'Khảo cứu cấu trúc mẫu tự Pāḷi: 8 nguyên âm, 33 phụ âm phân bổ theo 5 vị trí phát âm cơ quan phát âm (thanh quản, vòm họng, uốn lưỡi, răng, môi) và âm mũi Niggahīta.',
    level: 'Căn bản',
    estimatedMinutes: 10,
    tags: ['Mẫu tự', 'Nguyên âm', 'Phụ âm', 'Sara', 'Vyañjana', 'Niggahīta'],
    summaryPoints: [
      'Pāḷi có tổng cộng 41 mẫu tự: 8 nguyên âm (Sara) và 33 phụ âm (Vyañjana).',
      '8 nguyên âm gồm: a, ā, i, ī, u, ū, e, o. Trong đó a, i, u là nguyên âm ngắn (Rassa), còn ā, ī, ū, e, o là nguyên âm dài (Dīgha).',
      '33 phụ âm gồm 25 phụ âm có nhóm (Vagga) theo 5 vị trí phát âm và 8 phụ âm không nhóm (Avagga) cộng với âm mũi ṃ (Niggahīta).',
      'Pāḷi là ngôn ngữ ngữ âm (phonetic): viết thế nào thì phát âm chuẩn xác như thế ấy.'
    ],
    vocabulary: [
      {
        term: 'Akkharo',
        ipa: '/ɐk.kʰɐ.ɾoː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Chữ cái, mẫu tự, không thể tiêu diệt (bất hoại)',
        root: 'a- + √khar (tiêu hoại)',
        note: 'Trong văn phạm Pāḷi, Akkhara chỉ mẫu tự biểu đạt âm thanh Chánh Pháp.',
        example: 'Akkharā pādavantā (Các mẫu tự cấu thành câu kệ).'
      },
      {
        term: 'Saro',
        ipa: '/sɐ.ɾoː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Nguyên âm, âm thanh phát ra tự nhiên',
        root: '√sar (phát ra âm thanh)',
        note: 'Gồm 8 nguyên âm cơ bản: a, ā, i, ī, u, ū, e, o.',
        example: 'Aṭṭha sarā (Tám nguyên âm).'
      },
      {
        term: 'Vyañjanaṃ',
        ipa: '/ʋjɐɲ.ɟɐ.nɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Phụ âm, ký hiệu làm rõ nét nghĩa',
        root: 'vi- + √añj (làm sáng tỏ)',
        note: 'Gồm 33 phụ âm cần kết hợp nguyên âm để phát âm trọn vẹn.',
        example: 'Tettimsā vyañjanā (Ba mươi ba phụ âm).'
      },
      {
        term: 'Niggahītaṃ',
        ipa: '/niɡ.ɡɐ.ɦiː.tɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Âm mũi Niggahīta (ký hiệu ṃ hoặc ŋ)',
        root: 'ni- + √gah (kềm chế, giữ lại)',
        note: 'Phát âm như âm "ng" trong tiếng Việt nhưng giữ hơi lại ở mũi (Buddhaṃ, Dhammaṃ).',
        example: 'Buddhaṃ saraṇaṃ gacchāmi.'
      }
    ],
    grammarSections: [
      {
        title: '1. Hệ Thống 8 Nguyên Âm (Sara)',
        explanation: 'Nguyên âm Pāḷi được phân chia thành Nguyên Âm Ngắn (Rassa Sara - phát âm trong 1 sát-na lãng tai) và Nguyên Âm Dài (Dīgha Sara - phát âm ngân dài gấp đôi sát-na).',
        table: {
          headers: ['Loại Nguyên Âm', 'Ký Tự Pāḷi', 'Thời Lượng Phát Âm', 'Ví Dụ Trong Kinh Văn'],
          rows: [
            ['Nguyên âm ngắn (Rassa)', 'a, i, u', '1 nhịp (ngắn, dứt khoát)', 'Mano, Citta, Dukkha'],
            ['Nguyên âm dài (Dīgha)', 'ā, ī, ū', '2 nhịp (ngân dài, mở khẩu hình)', 'Dhammā, Nibbāna, Rūpa'],
            ['Nguyên âm biến thể (Dīgha/Rassa)', 'e, o', 'Dài trước đơn âm; Ngắn trước phụ âm đôi', 'Eko, Lokadhamma, Metta']
          ]
        },
        tip: 'Khi "e" hoặc "o" đứng trước một phụ âm đôi (như trong "Khetta", "Oṭṭha"), chúng được đọc ngắn lại như âm e/ô gãy gọn.'
      },
      {
        title: '2. Bảng 5 Nhóm Phụ Âm (Vagga Vyañjana) & Cơ Quan Phát Âm',
        explanation: '25 phụ âm có nhóm được chia đều thành 5 nhóm (mỗi nhóm 5 âm: Bất thanh vô khí, Bất thanh hữu khí, Hữu thanh vô khí, Hữu thanh hữu khí, và Âm mũi).',
        table: {
          headers: ['Nhóm (Vagga)', 'Vị Trí Phát Âm (Vị Trí Miệng)', 'Âm 1 (Vô khí)', 'Âm 2 (Hữu khí)', 'Âm 3 (Hữu thanh)', 'Âm 4 (Hữu thanh + khí)', 'Âm 5 (Âm mũi)'],
          rows: [
            ['Ka-vagga (Cổ họng)', 'Họng (Kaṇṭhaja)', 'k (ca)', 'kh (khơ)', 'g (gờ)', 'gh (g-h)', 'ṅ (ngờ)'],
            ['Ca-vagga (Vòm miệng)', 'Vòm họng (Tāluja)', 'c (chờ)', 'ch (ch-h)', 'j (dờ/gi)', 'jh (j-h)', 'ñ (nhờ)'],
            ['Ṭa-vagga (Uốn lưỡi)', 'Vòm cứng (Muddhaja)', 'ṭ (t uốn lưỡi)', 'ṭh (th uốn)', 'ḍ (đ uốn)', 'ḍh (đ-h)', 'ṇ (n uốn)'],
            ['Ta-vagga (Răng)', 'Chân răng (Dantaja)', 't (tờ phẳng)', 'th (thờ)', 'd (đờ)', 'dh (đ-h)', 'n (nờ)'],
            ['Pa-vagga (Môi)', 'Hai môi (Oṭṭhaja)', 'p (pờ)', 'ph (phờ)', 'b (bờ)', 'bh (b-h)', 'm (mờ)']
          ]
        },
        tip: 'Phụ âm có chữ "h" đi kèm (như kh, gh, ch, jh, ṭh, th, dh, ph, bh) là âm hữu khí (Aspirated), cần bật luồng hơi mạnh từ cổ họng.'
      },
      {
        title: '3. Nhóm 8 Phụ Âm Không Nhóm (Avagga) & Âm Mũi ṃ',
        explanation: 'Gồm các phụ âm trôi chảy: y, r, l, v, s, h, ḷ (l uốn lưỡi), và ṃ (Niggahīta).',
        tip: 'Chữ "ḷ" là phụ âm quặt lưỡi đặc thù của Pāḷi (như trong từ Pāḷi, Tipiṭaka), phát âm bằng cách cong đầu lưỡi chạm vòm họng trên rồi bật nhẹ.'
      }
    ],
    quiz: [
      {
        id: 'q1-1',
        question: 'Trong ngôn ngữ Pāḷi có tất cả bao nhiêu mẫu tự (Akkhara)?',
        options: ['26 mẫu tự', '41 mẫu tự (8 nguyên âm, 33 phụ âm)', '54 mẫu tự', '32 mẫu tự'],
        correctIndex: 1,
        explanation: 'Pāḷi gồm đúng 41 mẫu tự: 8 nguyên âm (Sara) và 33 phụ âm (Vyañjana).'
      },
      {
        id: 'q1-2',
        question: 'Ba nguyên âm ngắn (Rassa Sara) trong Pāḷi là những chữ nào?',
        options: ['ā, ī, ū', 'a, i, u', 'e, o, a', 'k, c, ṭ'],
        correctIndex: 1,
        explanation: 'Ba nguyên âm ngắn có thời lượng 1 nhịp phát âm là: a, i, u.'
      },
      {
        id: 'q1-3',
        question: 'Ký tự Niggahīta (ṃ) trong từ "Buddhaṃ" phát âm theo cơ quan nào?',
        options: ['Âm bật môi', 'Âm mũi (giữ hơi ngân qua khoang mũi)', 'Âm nuốt cuống họng', 'Âm gió'],
        correctIndex: 1,
        explanation: 'Niggahīta (ṃ) là âm mũi, đóng luồng hơi miệng và thoát nhẹ qua mũi tương tự "ng" ngắn.'
      }
    ]
  },

  {
    id: 'pali-02-quy-tac-phat-am',
    slug: 'quy-tac-phat-am-chuan-va-trong-am',
    categoryId: 'bang-chu-cai-phat-am',
    order: 2,
    title: 'Bài 2: Quy Tắc Trọng Âm & Đọc Tụng Chuẩn Pāḷi',
    paliTitle: 'Dutiyo Pāṭho: Uccāraṇavidhi & Garulahu',
    description: 'Quy tắc xác định âm nặng (Garu), âm nhẹ (Lahu), cách xử lý phụ âm đôi (Saññoga) và nhịp điệu khi tụng tụng kinh điển Theravāda truyền thống.',
    level: 'Căn bản',
    estimatedMinutes: 12,
    tags: ['Phát âm', 'Trọng âm', 'Âm nặng', 'Garu', 'Lahu', 'Phụ âm đôi'],
    summaryPoints: [
      'Garu (Âm Nặng/Âm Trọng): là âm tiết có nguyên âm dài (ā, ī, ū, e, o) hoặc nguyên âm ngắn đứng trước phụ âm ghép hay Niggahīta.',
      'Lahu (Âm Nhẹ): là âm tiết chỉ chứa nguyên âm ngắn đứng riêng lẻ đơn độc.',
      'Khi gặp phụ âm đôi (ví dụ: dd, kk, mm, ññ), phụ âm đầu ghép vào âm tiết trước làm âm khép, phụ âm sau bắt đầu âm tiết kế tiếp.',
      'Trọng âm từ trong Pāḷi thường rơi vào âm Garu gần cuối từ (penultimate).'
    ],
    vocabulary: [
      {
        term: 'Garu',
        ipa: '/ɡɐ.ɾu/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        vietnamese: 'Nặng, trọng yếu, đáng kính, âm tiết dài/nặng',
        note: 'Âm tiết mang trọng âm trong câu tụng.',
        example: 'Garulo (Bậc tôn sư đáng kính).'
      },
      {
        term: 'Lahu',
        ipa: '/lɐ.ɦu/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        vietnamese: 'Nhẹ, thanh thoát, âm tiết ngắn',
        note: 'Thời lượng đọc ngắn, thanh thoát.',
        example: 'Lahutā (Trạng thái nhẹ nhàng thanh tịnh của tâm sở).'
      },
      {
        term: 'Saññogo',
        ipa: '/sɐɲ.ɲoː.ɡoː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Phụ âm ghép đôi, sự liên kết phụ âm',
        root: 'saṃ- + √yuj (kết hợp lại)',
        example: 'Phụ âm đôi trong "Dham-ma", "Nang-kha-la".'
      }
    ],
    grammarSections: [
      {
        title: '1. Quy Tắc Tách Âm Tiết Khi Gặp Phụ Âm Kép',
        explanation: 'Trong tiếng Pāḷi, phụ âm đôi xuất hiện rất thường xuyên nhằm tạo nhịp đập hùng hồn cho lời tụng.',
        table: {
          headers: ['Từ Pāḷi', 'Cách Tách Âm Tiết', 'Phiên Âm Đọc Chuẩn', 'Ý Nghĩa Giáo Lý'],
          rows: [
            ['Buddho', 'Bud + dho', 'Bút-đ-hô (nhấn Bud)', 'Đấng Tự Giác Ngộ'],
            ['Dhammo', 'Dham + mo', 'Đhăm-mô (nhấn Dham)', 'Chánh Pháp vi diệu'],
            ['Saṅgho', 'Saṅ + gho', 'Xăng-g-hô (nhấn Saṅ)', 'Tăng già thanh tịnh'],
            ['Nibbānaṃ', 'Nib + bā + naṃ', 'Níp-ba-năng (ngân bā)', 'Niết bàn tịch tịnh'],
            ['Anattā', 'A + nat + tā', 'A-nát-ta (ngân tā)', 'Vô ngã, không có tự tính']
          ]
        }
      },
      {
        title: '2. Trọng Âm Từ & Nhịp Điệu Tụng Pāḷi',
        explanation: 'Nguyên tắc vàng: Pāḷi không có thanh điệu (không dấu sắc/huyền/hỏi/ngã như tiếng Việt), nhưng có nhịp phách bằng sự tương phản giữa Garu (Trọng) và Lahu (Khinh).',
        tip: 'Khi tụng "Namo Tassa Bhagavato": Tách âm "Na-mô Tas-sa Bha-ga-va-tô". Âm "Tas" và "tô" là âm nặng cần phát âm dứt khoát và trang nghiêm.'
      }
    ],
    quiz: [
      {
        id: 'q2-1',
        question: 'Từ "Dhammo" được tách âm tiết chuẩn xác như thế nào?',
        options: ['Dha + mmo', 'Dham + mo', 'Dh + am + mo', 'Dha + m + mo'],
        correctIndex: 1,
        explanation: 'Phụ âm đôi "mm" được tách đôi: chữ m thứ nhất khép âm "Dham", chữ m thứ hai mở đầu âm "mo".'
      },
      {
        id: 'q2-2',
        question: 'Âm tiết nào sau đây là âm Garu (âm nặng)?',
        options: ['Âm có nguyên âm ngắn đứng riêng như "ca"', 'Âm có nguyên âm dài như "bā" hoặc đứng trước phụ âm kép như "dham"', 'Tất cả các nguyên âm ngắn', 'Âm đứng đầu từ'],
        correctIndex: 1,
        explanation: 'Âm Garu là âm chứa nguyên âm dài (ā, ī, ū, e, o) hoặc âm có phụ âm khép theo sau.'
      }
    ]
  },

  // CATEGORY 2: Ngữ Pháp Pāḷi Căn Bản
  {
    id: 'pali-03-danh-tu-8-bien-cach',
    slug: 'danh-tu-va-8-bien-cach-vibhatti',
    categoryId: 'ngu-phap-can-ban',
    order: 3,
    title: 'Bài 3: Danh Từ & 8 Biến Cách Pāḷi (Aṭṭha Vibhatti)',
    paliTitle: 'Tatiyo Pāṭho: Nāmapada & Aṭṭhavibhatti',
    description: 'Nền tảng quan trọng nhất của ngữ pháp Pāḷi: Hiểu tường tận 8 biến cách (Vibhatti) của danh từ Nam tánh tận cùng bằng mẫu tự -a (như Buddha, Dhamma, Purisa).',
    level: 'Căn bản',
    estimatedMinutes: 15,
    tags: ['Danh từ', 'Biến cách', 'Vibhatti', 'Nam tánh', 'Aṭṭhavibhatti'],
    summaryPoints: [
      'Pāḷi là ngôn ngữ biến cách (Inflected language): vai trò ngữ pháp của danh từ được quyết định bởi đuôi biến cách (Vibhatti) thay vì vị trí đứng trong câu.',
      '8 Biến Cách gồm: 1. Chủ cách (Paṭhamā), 2. Đối cách (Dutiyā), 3. Sở dụng cách (Tatiyā), 4. Chỉ định cách (Catutthī), 5. Xuất xứ cách (Pañcamī), 6. Sở thuộc cách (Chaṭṭhī), 7. Định vị cách (Sattamī), 8. Hô cách (Ālapana).',
      'Mỗi biến cách có hai dạng: Số ít (Ekavacana) và Số nhiều (Bahuvacana).'
    ],
    vocabulary: [
      {
        term: 'Buddho',
        ipa: '/bud.dʰoː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Đức Phật (Chủ cách số ít)',
        note: 'Nguyên mẫu là "Buddha", biến cách Paṭhamā số ít thêm -o thành "Buddho".',
        example: 'Buddho dhammaṃ deseti (Đức Phật thuyết Chánh Pháp).'
      },
      {
        term: 'Dhammaṃ',
        ipa: '/dʰɐm.mɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Chánh Pháp (Đối cách số ít - tân ngữ trực tiếp)',
        note: 'Thêm đuôi -ṃ chỉ đối tượng chịu tác động của hành động.',
        example: 'Puriso dhammaṃ suṇāti (Người đàn ông lắng nghe Pháp).'
      },
      {
        term: 'Dhammena',
        ipa: '/dʰɐm.meː.nɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Bằng Chánh Pháp, nhờ Chánh Pháp (Sở dụng cách)',
        note: 'Đuôi -ena chỉ công cụ, phương tiện thực hiện.',
        example: 'Dhammena jīvati (Sống đúng theo Chánh Pháp).'
      },
      {
        term: 'Buddhassa',
        ipa: '/bud.dʰɐs.sɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Của Đức Phật / Đến Đức Phật (Sở thuộc & Chỉ định cách)',
        note: 'Đuôi -assa chỉ quyền sở hữu (của ai) hoặc nơi tiếp nhận (cho ai).',
        example: 'Buddhassa sāvako (Đệ tử của Đức Phật).'
      }
    ],
    grammarSections: [
      {
        title: 'Bảng Biến Cách Toàn Diện Của Danh Từ Nam Tánh Tận Cùng Bằng "-a" (Buddha)',
        explanation: 'Đây là bảng quy mẫu (paradigm) kinh điển nhất. Bất kỳ danh từ nam tánh tận cùng bằng -a nào (như Dhamma, Sangha, Loka, Deva, Nara) đều biến cách y như sau:',
        table: {
          headers: ['Biến Cách (Vibhatti)', 'Chức Năng Cú Pháp', 'Số Ít (Ekavacana)', 'Số Nhiều (Bahuvacana)', 'Ý Nghĩa Dịch Việt'],
          rows: [
            ['1. Paṭhamā (Chủ cách)', 'Chủ từ thực hiện hành động', 'Buddho', 'Buddhā', 'Đức Phật / Chư Phật'],
            ['2. Dutiyā (Đối cách)', 'Tân ngữ trực tiếp (bị tác động)', 'Buddhaṃ', 'Buddhe', 'Đến Đức Phật / Chư Phật'],
            ['3. Tatiyā (Sở dụng cách)', 'Phương tiện, công cụ (bằng, bởi)', 'Buddhena', 'Buddhehi / Buddhebhi', 'Bởi Đức Phật / Nhờ chư Phật'],
            ['4. Catutthī (Chỉ định cách)', 'Tân ngữ gián tiếp (cho, đến)', 'Buddhassa / Buddhāya', 'Buddhānaṃ', 'Cho Đức Phật / Cúng dường chư Phật'],
            ['5. Pañcamī (Xuất xứ cách)', 'Nơi xuất phát, rời xa (từ, do)', 'Buddhasmā / Buddhamhā / Buddhā', 'Buddhehi / Buddhebhi', 'Từ Đức Phật'],
            ['6. Chaṭṭhī (Sở thuộc cách)', 'Chỉ sở hữu (của)', 'Buddhassa', 'Buddhānaṃ', 'Của Đức Phật / Của chư Phật'],
            ['7. Sattamī (Định vị cách)', 'Vị trí, thời gian (nơi, ở, trên)', 'Buddhasmiṃ / Buddhamhi / Buddhe', 'Buddhesu', 'Nơi Đức Phật / Trong chư Phật'],
            ['8. Ālapana (Hô cách)', 'Kêu gọi, xưng hô tôn xưng', 'Buddha / Buddhā', 'Buddhā', 'Kính lạy Đức Phật!']
          ]
        },
        tip: 'Hãy để ý: Đuôi số nhiều của Đối cách là "-e" (Buddhe), Sở thuộc cách số nhiều là "-ānaṃ" (Buddhānaṃ), và Định vị cách số nhiều là "-esu" (Buddhesu).'
      }
    ],
    quiz: [
      {
        id: 'q3-1',
        question: 'Trong câu "Puriso dhammaṃ suṇāti", từ "dhammaṃ" đang ở biến cách nào?',
        options: ['Chủ cách (Paṭhamā)', 'Đối cách (Dutiyā - tân ngữ)', 'Sở dụng cách (Tatiyā)', 'Sở thuộc cách (Chaṭṭhī)'],
        correctIndex: 1,
        explanation: 'Đuôi "-ṃ" trong "dhammaṃ" là biến cách Dutiyā (Đối cách), đóng vai trò tân ngữ trực tiếp (lắng nghe Pháp).'
      },
      {
        id: 'q3-2',
        question: 'Dạng số nhiều Sở thuộc cách (chỉ sở hữu "của các vị...") của danh từ "Dhamma" là gì?',
        options: ['Dhammānaṃ', 'Dhammesu', 'Dhamme', 'Dhammena'],
        correctIndex: 0,
        explanation: 'Đuôi Sở thuộc cách số nhiều của danh từ tận cùng -a là "-ānaṃ", do đó "Dhammānaṃ" có nghĩa là "của các Pháp".'
      },
      {
        id: 'q3-3',
        question: 'Biến cách nào chỉ phương tiện, công cụ ("bằng cách...", "nhờ...") trong tiếng Pāḷi?',
        options: ['Paṭhamā (Chủ cách)', 'Tatiyā (Sở dụng cách - đuôi -ena)', 'Sattamī (Định vị cách)', 'Dutiyā (Đối cách)'],
        correctIndex: 1,
        explanation: 'Tatiyā vibhatti (Sở dụng cách) mang đuôi "-ena" ở số ít, biểu đạt ý nghĩa bằng phương tiện gì.'
      }
    ]
  },

  {
    id: 'pali-04-dong-tu-thoi-hien-tai',
    slug: 'dong-tu-va-thoi-hien-tai-akhyata',
    categoryId: 'ngu-phap-can-ban',
    order: 4,
    title: 'Bài 4: Động Từ Thì Hiện Tại (Vattamānā Ākhyāta)',
    paliTitle: 'Catuttho Pāṭho: Ākhyātapada & Vattamānā Kāla',
    description: 'Cách chia động từ thì hiện tại trong Pāḷi theo 3 ngôi (Purisa: Ngôi thứ 3, Ngôi thứ 2, Ngôi thứ 1) và tiếp vĩ ngữ chia động từ kinh điển (ti, anti, si, tha, mi, ma).',
    level: 'Căn bản',
    estimatedMinutes: 14,
    tags: ['Động từ', 'Ākhyāta', 'Hiện tại', 'Vattamānā', 'Chia động từ'],
    summaryPoints: [
      'Động từ trong Pāḷi cấu tạo từ: Căn động từ (Dhātu) + Yếu tố biến tố (Vikarana) + Tiếp vĩ ngữ ngôi (Paccaya).',
      'Thứ tự ngôi trong văn phạm Pāḷi ngược lại với tiếng Anh: Ngôi thứ 3 (Paṭhama purisa - Người ấy), Ngôi thứ 2 (Majjhima purisa - Bạn), Ngôi thứ 1 (Uttama purisa - Tôi/Chúng tôi).',
      'Đuôi chia hiện tại chủ động (Parassapada): ti - anti, si - tha, mi - ma.'
    ],
    vocabulary: [
      {
        term: 'Gacchati',
        ipa: '/ɡɐt.t͡ɕʰɐ.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Đi, bước đi (Ngôi thứ 3 số ít)',
        root: '√gam (đi)',
        example: 'Bhikkhu gāmaṃ gacchati (Vị tỳ-kheo đi vào làng).'
      },
      {
        term: 'Deseti',
        ipa: '/deː.seː.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Thuyết giảng, chỉ dạy (Ngôi 3 số ít)',
        root: '√dis (chỉ ra, giảng giải)',
        example: 'Satthā dhammaṃ deseti (Bậc Đạo Sư thuyết giảng Chánh Pháp).'
      },
      {
        term: 'Passati',
        ipa: '/pɐs.sɐ.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Thấy, quán chiếu bằng tuệ giác (Ngôi 3 số ít)',
        root: '√dis / pas (nhìn thấy)',
        example: 'Paññāya passati (Quán thấy bằng trí tuệ).'
      },
      {
        term: 'Gacchāmi',
        ipa: '/ɡɐt.t͡ɕʰaː.mi/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Con xin đi đến, con nương tựa (Ngôi 1 số ít)',
        root: '√gam + mi',
        note: 'Khi gắn đuôi "-mi", nguyên âm trước nó thường được kéo dài: gaccha -> gacchāmi.',
        example: 'Buddhaṃ saraṇaṃ gacchāmi (Con đem hết lòng thành nương tựa Đức Phật).'
      }
    ],
    grammarSections: [
      {
        title: 'Bảng Tiếp Vĩ Ngữ Chia Động Từ Thì Hiện Tại (Vattamānā Parassapada)',
        explanation: 'Quy tắc chia động từ căn bản mẫu: căn √gam (Đi -> Gaccha-):',
        table: {
          headers: ['Ngôi (Purisa)', 'Đại Từ Tương Ứng', 'Số Ít (Ekavacana)', 'Số Nhiều (Bahuvacana)', 'Ví Dụ Chia Động Từ'],
          rows: [
            ['Ngôi thứ 3 (Paṭhama - Người ấy/Họ)', 'So / Te', '-ti (Gacchati)', '-anti (Gacchanti)', 'So gacchati (Anh ấy đi) / Te gacchanti (Họ đi)'],
            ['Ngôi thứ 2 (Majjhima - Bạn/Các bạn)', 'Tvaṃ / Tumhe', '-si (Gacchasi)', '-tha (Gacchatha)', 'Tvaṃ gacchasi (Bạn đi) / Tumhe gacchatha (Các bạn đi)'],
            ['Ngôi thứ 1 (Uttama - Tôi/Chúng tôi)', 'Ahaṃ / Mayaṃ', '-mi (Gacchāmi)', '-ma (Gacchāma)', 'Ahaṃ gacchāmi (Tôi đi) / Mayaṃ gacchāma (Chúng tôi đi)']
          ]
        },
        tip: 'Quy tắc ngâm dài: Trước đuôi "-mi" và "-ma", nguyên âm "a" ngắn luôn biến thành nguyên âm "ā" dài: Vadam -> Vadāmi, Tittha -> Tiṭṭhāma.'
      }
    ],
    quiz: [
      {
        id: 'q4-1',
        question: 'Đuôi chia động từ thì hiện tại ngôi thứ nhất số ít ("Tôi...") là gì?',
        options: ['-ti', '-si', '-mi (ví dụ: gacchāmi)', '-anti'],
        correctIndex: 2,
        explanation: 'Đuôi ngôi thứ nhất số ít là "-mi", như trong câu quy y "Buddhaṃ saraṇaṃ gacchāmi" (Con quy y Phật).'
      },
      {
        id: 'q4-2',
        question: 'Dịch câu sau sang Pāḷi: "Chư tỳ-kheo lắng nghe Chánh Pháp" (Biết: Bhikkhū = Chư Tăng, Dhammaṃ = Pháp, Suṇāti = nghe)?',
        options: ['Bhikkhū dhammaṃ suṇāti', 'Bhikkhū dhammaṃ suṇanti', 'Bhikkhū dhammaṃ suṇasi', 'Bhikkhū dhammaṃ suṇāma'],
        correctIndex: 1,
        explanation: 'Vì chủ ngữ "Bhikkhū" là số nhiều ngôi thứ 3, động từ phải chia ở dạng số nhiều là "-anti" -> "suṇanti".'
      }
    ]
  },

  // CATEGORY 3: Từ Vựng & Thuật Ngữ Cốt Lõi
  {
    id: 'pali-05-tam-bao-tam-quy-y',
    slug: 'tam-bao-va-tam-quy-y-tisarana',
    categoryId: 'tu-vung-cot-loi',
    order: 5,
    title: 'Bài 5: Tam Bảo & Lời Tuyên Ngôn Tam Quy Y (Ti-saraṇa)',
    paliTitle: 'Pañcamo Pāṭho: Ratanattaya ca Tisaraṇagamana',
    description: 'Phân tích ngữ nghĩa uyên áo và cấu trúc ngữ pháp từng từ trong câu tụng Tam Quy Y: Buddha, Dhamma, Saṅgha và Saraṇaṃ gacchāmi.',
    level: 'Căn bản',
    estimatedMinutes: 12,
    tags: ['Tam Bảo', 'Tiratana', 'Tam Quy', 'Tisarana', 'Buddha', 'Dhamma', 'Sangha'],
    summaryPoints: [
      'Ratanattaya (Tam Bảo): Ba viên ngọc báu quý giá vô thượng trên thế gian gồm Phật Bảo, Pháp Bảo và Tăng Bảo.',
      'Saraṇa: Nơi nương tựa an ổn, nơi che chở dập tắt mọi nỗi sợ hãi luân hồi sinh tử.',
      'Ti-saraṇa-gamana: Hành động tự nguyện đặt trọn niềm tin sáng suốt (Saddhā) bước theo con đường giác ngộ.'
    ],
    vocabulary: [
      {
        term: 'Buddha',
        ipa: '/bud.dʰɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Bậc Giác Ngộ hoàn toàn, Tỉnh Thức, Tự mình chứng ngộ chân lý không thầy chỉ dạy',
        root: '√budh (tỉnh thức, giác ngộ)'
      },
      {
        term: 'Dhamma',
        ipa: '/dʰɐm.mɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Chân lý thực tại, Giáo pháp nâng đỡ người thực hành không rơi vào bốn khổ cảnh',
        root: '√dhar (nâng đỡ, trì giữ)'
      },
      {
        term: 'Saṅgha',
        ipa: '/sɐŋ.ɡʰɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Đoàn thể Tăng già hòa hợp thanh tịnh gồm 4 đôi 8 vị Thánh đệ tử',
        root: 'saṃ- + √han (hội tụ, hòa hợp)'
      },
      {
        term: 'Saraṇaṃ',
        ipa: '/sɐ.ɾɐ.nɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Nơi nương tựa, chốn nương náu an toàn, nơi diệt trừ phiền não',
        root: '√sri (nương tựa)'
      }
    ],
    grammarSections: [
      {
        title: 'Cấu Trúc Ngữ Pháp Câu Tụng Tam Quy',
        explanation: 'Mỗi câu quy y gồm 2 tân ngữ (Buddhaṃ và Saraṇaṃ) và 1 động từ chia ngôi 1 số ít (gacchāmi):',
        table: {
          headers: ['Câu Tụng Pāḷi', 'Tân Ngữ 1', 'Tân Ngữ 2', 'Động Từ', 'Ý Nghĩa Dịch Trọn Vẹn'],
          rows: [
            ['Buddhaṃ saraṇaṃ gacchāmi', 'Buddhaṃ (Đức Phật)', 'Saraṇaṃ (nơi nương tựa)', 'Gacchāmi (Con xin đi đến)', 'Con đem hết lòng thành kính nương tựa Đức Phật.'],
            ['Dhammaṃ saraṇaṃ gacchāmi', 'Dhammaṃ (Chánh Pháp)', 'Saraṇaṃ (nơi nương tựa)', 'Gacchāmi (Con xin đi đến)', 'Con đem hết lòng thành kính nương tựa Chánh Pháp.'],
            ['Saṅghaṃ saraṇaṃ gacchāmi', 'Saṅghaṃ (Tăng Chúng)', 'Saraṇaṃ (nơi nương tựa)', 'Gacchāmi (Con xin đi đến)', 'Con đem hết lòng thành kính nương tựa Chư Tăng.']
          ]
        },
        tip: 'Khi tụng lần thứ hai thêm "Dutiyampi" (Lần thứ nhì), lần thứ ba thêm "Tatiyampi" (Lần thứ ba).'
      }
    ],
    quiz: [
      {
        id: 'q5-1',
        question: 'Thuật ngữ "Saraṇa" trong Tam Quy Y bắt nguồn từ căn ngữ mang ý nghĩa gì?',
        options: ['Chiến đấu', 'Nơi nương tựa, bảo hộ, che chở', 'Thế giới vật chất', 'Sự giàu có'],
        correctIndex: 1,
        explanation: 'Saraṇa có nghĩa là nơi nương náu an ổn, chở che tâm thức khỏi khổ đau vô minh.'
      },
      {
        id: 'q5-2',
        question: 'Từ "Dutiyampi" trong câu "Dutiyampi Buddhaṃ saraṇaṃ gacchāmi" có nghĩa là gì?',
        options: ['Lần thứ nhất', 'Lần thứ nhì', 'Lần thứ ba', 'Mãi mãi'],
        correctIndex: 1,
        explanation: '"Dutiya" là số thứ tự thứ hai, thêm tiếp vĩ ngữ "-pi" (cũng) -> Lần thứ nhì con cũng quy y Phật.'
      }
    ]
  },

  {
    id: 'pali-06-tu-thanh-de-bat-chanh-dao',
    slug: 'tu-thanh-de-va-bat-chanh-dao-cattari-ariyasaccani',
    categoryId: 'tu-vung-cot-loi',
    order: 6,
    title: 'Bài 6: Tứ Thánh Đế & Bát Chánh Đạo (Cattāri Ariyasaccāni)',
    paliTitle: 'Chaṭṭho Pāṭho: Cattāri Ariyasaccāni & Ariyo Aṭṭhaṅgiko Maggo',
    description: 'Nghiên cứu hệ thống thuật ngữ Pāḷi nền tảng trong bài kinh đầu tiên Chuyển Pháp Luân (Dhammacakkappavattana Sutta): 4 Chân Lý Cao Thượng và 8 Chi Phần Con Đường Giải Thoát.',
    level: 'Căn bản',
    estimatedMinutes: 16,
    tags: ['Tứ Diệu Đế', 'Bát Chánh Đạo', 'Ariyasacca', 'Magga', 'Dukkha', 'Nibbana'],
    summaryPoints: [
      'Cattāri Ariyasaccāni: 1. Dukkha Sacca (Khổ đế), 2. Samudaya Sacca (Tập đế), 3. Nirodha Sacca (Diệt đế), 4. Magga Sacca (Đạo đế).',
      'Bát Chánh Đạo gồm 3 học giới: Tuệ học (Paññā), Giới học (Sīla), và Định học (Samādhi).',
      'Tiếp đầu ngữ "Sammā-" có nghĩa là Chân Chánh, Hoàn Hảo, Đúng Đắn theo chiều hướng đưa đến giải thoát.'
    ],
    vocabulary: [
      {
        term: 'Ariyasaccaṃ',
        ipa: '/ɐ.ɾi.jɐ.sɐt.t͡ɕɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Chân lý cao thượng của bậc Thánh (Thánh Đế)',
        note: 'ariya (thánh thiện, cao thượng) + sacca (sự thật, chân lý)'
      },
      {
        term: 'Taṇhā',
        ipa: '/tɐɲ.ɦaː/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Khát ái, lòng tham đắm, cội nguồn sinh khởi đau khổ (Tập đế)',
        note: 'Gồm 3 loại: Kāmataṇhā (dục ái), Bhavataṇhā (hữu ái), Vibhavataṇhā (phi hữu ái).'
      },
      {
        term: 'Nirodho',
        ipa: '/ni.ɾoː.dʰoː/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Sự dập tắt hoàn toàn, tịch diệt, Niết-bàn (Diệt đế)',
        root: 'ni- + √rudh (ngăn chặn, dập tắt)'
      },
      {
        term: 'Sammādiṭṭhi',
        ipa: '/sɐm.maː.dit.tʰi/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Chánh Kiến — Sự hiểu biết đúng đắn như thực về Tứ Thánh Đế',
        note: 'Chi phần dẫn đầu của Bát Chánh Đạo.'
      }
    ],
    grammarSections: [
      {
        title: 'Bảng Thuật Ngữ 8 Chi Phần Bát Chánh Đạo (Ariyo Aṭṭhaṅgiko Maggo)',
        explanation: 'Phân loại 8 chi phần theo Tam Học (Tisikkhā):',
        table: {
          headers: ['Nhóm Tam Học', 'Chi Phần Pāḷi', 'Phiên Âm Hán Việt', 'Ý Nghĩa Giáo Lý'],
          rows: [
            ['Tuệ Học (Paññā Sikkhā)', '1. Sammādiṭṭhi', 'Chánh Kiến', 'Thấy rõ Tứ Đế, Nghiệp báo và Vô ngã'],
            ['Tuệ Học (Paññā Sikkhā)', '2. Sammāsaṅkappo', 'Chánh Tư Duy', 'Tư duy ly dục, vô sân và bất hại'],
            ['Giới Học (Sīla Sikkhā)', '3. Sammāvācā', 'Chánh Ngữ', 'Tránh nói dối, đâm thọc, thô ác, phù phiếm'],
            ['Giới Học (Sīla Sikkhā)', '4. Sammākammanto', 'Chánh Nghiệp', 'Tránh sát sinh, trộm cắp, tà dâm'],
            ['Giới Học (Sīla Sikkhā)', '5. Sammā-ājīvo', 'Chánh Mạng', 'Nuôi mạng bằng nghề nghiệp trong sạch'],
            ['Định Học (Samādhi Sikkhā)', '6. Sammāvāyāmo', 'Chánh Tinh Tấn', 'Tứ Chánh Cần (ngăn ác, diệt ác, sinh thiện, tăng thiện)'],
            ['Định Học (Samādhi Sikkhā)', '7. Sammāsati', 'Chánh Niệm', 'Tứ Niệm Xứ (Thân, Thọ, Tâm, Pháp)'],
            ['Định Học (Samādhi Sikkhā)', '8. Sammāsamādhi', 'Chánh Định', 'Bốn tầng thiền Sắc giới (Catu-jhāna)']
          ]
        }
      }
    ],
    quiz: [
      {
        id: 'q6-1',
        question: 'Cội nguồn sinh khởi đau khổ (Samudaya Sacca) theo lời Phật dạy trong Tứ Đế là gì?',
        options: ['Avijjā (Vô minh)', 'Taṇhā (Khát ái)', 'Dosa (Sân hận)', 'Māna (Ngã mạn)'],
        correctIndex: 1,
        explanation: 'Đức Phật tuyên thuyết trong Kinh Chuyển Pháp Luân: Taṇhā (Khát ái) chính là nhân sinh Khổ (Tập đế).'
      },
      {
        id: 'q6-2',
        question: 'Hai chi phần thuộc nhóm Tuệ học (Paññā) trong Bát Chánh Đạo là gì?',
        options: ['Sammāvācā & Sammākammanta', 'Sammādiṭṭhi & Sammāsaṅkappa', 'Sammāsati & Sammāsamādhi', 'Sammāvāyāma & Sammā-ājīva'],
        correctIndex: 1,
        explanation: 'Sammādiṭṭhi (Chánh Kiến) và Sammāsaṅkappa (Chánh Tư Duy) cấu thành nhóm Tuệ học.'
      }
    ]
  },

  // CATEGORY 4: Khảo Sát Kệ Ngôn
  {
    id: 'pali-07-kinh-phap-cu-ke-so-1',
    slug: 'kinh-phap-cu-ke-so-1-yamakavagga',
    categoryId: 'phan-tich-ke-ngon',
    order: 7,
    title: 'Bài 7: Khảo Sát Kệ Pháp Cú Số 1 (Dhammapada Yamakavagga)',
    paliTitle: 'Sattamo Pāṭho: Dhammapada Gāthā 1 Vicaya',
    description: 'Phân tích ngữ pháp chi tiết từng từ trong bài kệ mở đầu kinh Pháp Cú: "Manopubbaṅgamā dhammā, manoseṭṭhā manomayā..." và đối chiếu ý nghĩa giáo lý Tâm Dẫn Đầu.',
    level: 'Trung cấp',
    estimatedMinutes: 18,
    tags: ['Dhammapada', 'Pháp Cú', 'Kệ số 1', 'Phân tích ngữ pháp', 'Yamakavagga'],
    summaryPoints: [
      'Kệ số 1 thuộc Phẩm Song Yếu (Yamakavagga), khẳng định tâm ý là chủ thể dẫn đầu mọi hành vi và nghiệp báo.',
      'Từ ghép (Samāsa) Pāḷi xuất hiện dày đặc: Manopubbaṅgamā, Manoseṭṭhā, Manomayā.',
      'Hình ảnh ẩn dụ: Cỗ xe theo sau vết chân con bò kéo xe (cakkaṃva vahato padaṃ).'
    ],
    vocabulary: [
      {
        term: 'Mano',
        ipa: '/mɐ.noː/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Ý, tâm trí, năng lực nhận biết của tâm',
        note: 'Biến cách của danh từ căn Mano (gốc Manas).'
      },
      {
        term: 'Pubbaṅgamo',
        ipa: '/pub.bɐŋ.ɡɐ.moː/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        vietnamese: 'Đi trước, dẫn đầu, tiền đạo',
        root: 'pubba (trước) + √gam (đi)'
      },
      {
        term: 'Paduṭṭha',
        ipa: '/pɐ.dut.tʰɐ/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        vietnamese: 'Bị ô nhiễm, độc hại, hư hỏng bởi tham sân si',
        root: 'pa- + √dus (làm hư hỏng)'
      },
      {
        term: 'Anveti',
        ipa: '/ɐn.ʋeː.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Đi theo sau, đuổi theo sát nút',
        root: 'anu- + √i (đi theo sau)'
      }
    ],
    grammarSections: [],
    verseAnalysis: {
      originalPali: 'Manopubbaṅgamā dhammā, manoseṭṭhā manomayā;\nManasā ce paduṭṭhena, bhāsati vā karoti vā;\nTato naṃ dukkhamanveti, cakkaṃva vahato padaṃ.',
      vietnamese: 'Ý dẫn đầu các pháp, Ý làm chủ, ý tạo;\nNếu với ý ô nhiễm, Nói lên hay hành động,\nKhổ não bước theo sau, Như xe chân vật kéo.',
      english: 'Mind precedes all mental states. Mind is their chief; they are all mind-wrought. If with an impure mind a person speaks or acts, suffering follows him like the wheel that follows the foot of the ox.',
      context: 'Đức Phật thuyết kệ này tại Kỳ Viên Tịnh Xá (Jetavana) nhân sự tích đại trưởng lão Cakkhupāla (Đại đức Mù) chứng quả A-la-hán.',
      breakdown: [
        { word: 'Mano-pubbaṅgamā', grammar: 'Tính từ ghép (Tappurisa samāsa), Paṭhamā số nhiều', rootOrStem: 'mano + pubba + √gam', meaning: 'Có ý dẫn đầu, do ý đi trước' },
        { word: 'dhammā', grammar: 'Danh từ nam tánh, Paṭhamā số nhiều', rootOrStem: 'dhamma', meaning: 'Các pháp, các trạng thái tâm thức' },
        { word: 'mano-seṭṭhā', grammar: 'Tính từ ghép, Paṭhamā số nhiều', rootOrStem: 'mano + seṭṭha (tối thượng)', meaning: 'Có ý là tối thượng, ý làm chủ' },
        { word: 'mano-mayā', grammar: 'Tính từ ghép, Paṭhamā số nhiều', rootOrStem: 'mano + maya (tạo thành)', meaning: 'Do ý tạo tác nên' },
        { word: 'manasā', grammar: 'Danh từ trung tánh, Tatiyā số ít', rootOrStem: 'manas', meaning: 'Với tâm ý, bằng tâm ý' },
        { word: 'ce', grammar: 'Bất biến từ liên từ (Nipāta)', rootOrStem: 'ce', meaning: 'Nếu, giả sử như' },
        { word: 'paduṭṭhena', grammar: 'Quá khứ phân từ làm tính từ, Tatiyā số ít', rootOrStem: 'pa- + √dus', meaning: 'Bị ô nhiễm, bất thiện' },
        { word: 'bhāsati', grammar: 'Động từ thì hiện tại, ngôi 3 số ít', rootOrStem: '√bhās (nói)', meaning: 'Nói năng, phát ngôn' },
        { word: 'vā', grammar: 'Bất biến từ liên từ', rootOrStem: 'vā', meaning: 'Hay là, hoặc là' },
        { word: 'karoti', grammar: 'Động từ thì hiện tại, ngôi 3 số ít', rootOrStem: '√kar (làm)', meaning: 'Hành động, tạo tác' },
        { word: 'tato', grammar: 'Phó từ chỉ nguyên nhân', rootOrStem: 'ta- + to', meaning: 'Do nhân ấy, từ đó' },
        { word: 'naṃ', grammar: 'Đại từ chỉ người, Dutiyā số ít', rootOrStem: 'ta', meaning: 'Người ấy, kẻ ấy' },
        { word: 'dukkhaṃ', grammar: 'Danh từ trung tánh, Paṭhamā số ít', rootOrStem: 'dukkha', meaning: 'Sự khổ não, nỗi thống khổ' },
        { word: 'anveti', grammar: 'Động từ thì hiện tại, ngôi 3 số ít', rootOrStem: 'anu + √i', meaning: 'Bám theo sau, đuổi theo' },
        { word: 'cakkaṃ-va', grammar: 'Danh từ cakkaṃ + Bất biến từ iva (như là)', rootOrStem: 'cakka + iva', meaning: 'Như bánh xe' },
        { word: 'vahato', grammar: 'Hiện tại phân từ, Chaṭṭhī số ít', rootOrStem: '√vah (kéo, chở)', meaning: 'Của con vật đang kéo tải' },
        { word: 'padaṃ', grammar: 'Danh từ trung tánh, Dutiyā số ít', rootOrStem: 'pada', meaning: 'Dấu chân, bước chân' }
      ]
    },
    quiz: [
      {
        id: 'q7-1',
        question: 'Trong câu "Manopubbaṅgamā dhammā", từ "dhammā" đóng vai trò ngữ pháp gì?',
        options: ['Chủ từ số nhiều (Paṭhamā bahuvacana)', 'Tân ngữ số ít', 'Sở thuộc cách', 'Động từ'],
        correctIndex: 0,
        explanation: '"dhammā" là danh từ số nhiều làm chủ từ: "Các pháp..."'
      },
      {
        id: 'q7-2',
        question: 'Cụm từ "cakkaṃva vahato padaṃ" sử dụng hình ảnh ẩn dụ gì?',
        options: ['Như bóng theo hình', 'Như bánh xe lăn theo dấu chân con vật kéo', 'Như nước chảy về biển', 'Như ngọn đèn soi bóng đêm'],
        correctIndex: 1,
        explanation: 'Ẩn dụ bánh xe bò lăn theo dấu chân con bò kéo xe biểu trưng cho quả báo khổ đau đeo bám kẻ làm ác.'
      }
    ]
  },

  {
    id: 'pali-08-kinh-phap-cu-ke-so-183',
    slug: 'kinh-phap-cu-ke-so-183-buddhavagga',
    categoryId: 'phan-tich-ke-ngon',
    order: 8,
    title: 'Bài 8: Khảo Sát Kệ Pháp Cú Số 183 — Tôn Chỉ Chư Phật',
    paliTitle: 'Aṭṭhamo Pāṭho: Dhammapada Gāthā 183 (Sabbapāpassa Akaraṇaṃ)',
    description: 'Phân tích bài kệ đúc kết toàn bộ tôn chỉ giáo pháp của muôn đời chư Phật: Tránh mọi điều ác, thành tựu hạnh lành, thanh lọc tâm ý.',
    level: 'Trung cấp',
    estimatedMinutes: 15,
    tags: ['Pháp Cú 183', 'Buddhasāsana', 'Lời Phật Dạy', 'Kusala', 'Akusala'],
    summaryPoints: [
      'Gāthā 183 thuộc Phẩm Phật Đà (Buddhavagga), là bài kệ tụng Ovāda Pātimokkha nổi tiếng.',
      'Sử dụng danh động từ tận cùng "-anaṃ" (Akaraṇaṃ, Sacittapariyodapanaṃ).',
      'Đúc kết tiến trình tu tập hoàn chỉnh: Giới (không làm ác) -> Định & Hạnh lành (làm điều thiện) -> Tuệ (thanh lọc tâm ý).'
    ],
    vocabulary: [
      {
        term: 'Sabbapāpaṃ',
        ipa: '/sɐb.bɐ.paː.pɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Mọi điều ác, tất cả hành vi bất thiện tổn hại',
        note: 'sabba (tất cả) + pāpa (điều ác)'
      },
      {
        term: 'Kusalaṃ',
        ipa: '/ku.sɐ.lɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Điều thiện, phước báu, thiện xảo diệt trừ phiền não',
        root: '√kus (cắt đứt phiền não)'
      },
      {
        term: 'Sacittaṃ',
        ipa: '/sɐ.t͡ɕit.tɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Tâm của chính mình (Tự tâm)',
        note: 'sa (của mình) + citta (tâm)'
      },
      {
        term: 'Sāsanaṃ',
        ipa: '/saː.sɐ.nɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Lời giáo huấn, Chánh Pháp truyền thừa, Tôn giáo/Giáo hội',
        root: '√sās (dạy bảo, huấn thị)'
      }
    ],
    grammarSections: [],
    verseAnalysis: {
      originalPali: 'Sabbapāpassa akaraṇaṃ,\nKusalassa upasampadā;\nSacittapariyodapanaṃ,\nEtaṃ buddhāna sāsanaṃ.',
      vietnamese: 'Không làm mọi điều ác,\nThành tựu các hạnh lành,\nGiữ tâm ý trong sạch,\nChính lời chư Phật dạy.',
      english: 'To avoid all evil, to cultivate good, and to cleanse one\'s mind — this is the teaching of the Buddhas.',
      context: 'Bài kệ này là tóm tắt bản tuyên ngôn Ovāda Pātimokkha mà Đức Phật tuyên đọc trước 1.250 vị Thánh Tăng vào ngày rằm tháng Magha.',
      breakdown: [
        { word: 'Sabba-pāpassa', grammar: 'Danh từ ghép, Chaṭṭhī số ít', rootOrStem: 'sabba + pāpa', meaning: 'Của tất cả điều ác, bất thiện' },
        { word: 'a-karaṇaṃ', grammar: 'Danh động từ (Gerundial noun), Paṭhamā số ít', rootOrStem: 'a- + √kar + ana', meaning: 'Sự không làm, tránh xa' },
        { word: 'kusalassa', grammar: 'Danh từ trung tánh, Chaṭṭhī số ít', rootOrStem: 'kusala', meaning: 'Của điều thiện, sự trong lành' },
        { word: 'upasampadā', grammar: 'Danh từ nữ tánh, Paṭhamā số ít', rootOrStem: 'upa- + saṃ- + √pad', meaning: 'Sự thành tựu, vun bồi trọn vẹn' },
        { word: 'sa-citta-pariyodapanaṃ', grammar: 'Danh từ ghép danh động từ, Paṭhamā số ít', rootOrStem: 'sa + citta + pari- + ava- + √dai (thanh lọc)', meaning: 'Sự thanh lọc, gạn đục khơi trong cho tự tâm' },
        { word: 'etaṃ', grammar: 'Đại từ chỉ định trung tánh, Paṭhamā số ít', rootOrStem: 'eta', meaning: 'Điều ấy, đây chính là' },
        { word: 'buddhānaṃ', grammar: 'Danh từ nam tánh, Chaṭṭhī số nhiều (rút gọn thành buddhāna)', rootOrStem: 'buddha', meaning: 'Của chư Phật (quá khứ, hiện tại, vị lai)' },
        { word: 'sāsanaṃ', grammar: 'Danh từ trung tánh, Paṭhamā số ít', rootOrStem: '√sās + ana', meaning: 'Lời răn dạy, giáo huấn tối thượng' }
      ]
    },
    quiz: [
      {
        id: 'q8-1',
        question: 'Trong câu "Etaṃ buddhāna sāsanaṃ", từ "buddhāna" ở biến cách nào?',
        options: ['Chủ cách số ít', 'Sở thuộc cách số nhiều (Của chư Phật)', 'Đối cách', 'Xuất xứ cách'],
        correctIndex: 1,
        explanation: '"buddhāna" là thể rút gọn của "buddhānaṃ" (Sở thuộc cách số nhiều: của chư Phật).'
      },
      {
        id: 'q8-2',
        question: 'Thành tố "Sacittapariyodapanaṃ" chỉ giai đoạn tu tập nào trong Tam Học?',
        options: ['Giới (Sīla)', 'Định & Tuệ (Samādhi & Paññā thanh lọc phiền não tiềm miên)', 'Cúng dường vật chất', 'Xây tháp bảo'],
        correctIndex: 1,
        explanation: 'Thanh lọc tâm ý là đỉnh cao của Định và Tuệ (Vipassanā) dứt sạch lậu hoặc.'
      }
    ]
  },

  // CATEGORY 5: Kinh Tụng & Tác Bạch
  {
    id: 'pali-09-ngu-gioi-pali',
    slug: 'tho-tri-ngu-gioi-pancasila',
    categoryId: 'kinh-tung-thien-mon',
    order: 9,
    title: 'Bài 9: Lời Tuyên Nguyện Thọ Trì Ngũ Giới (Pañcasīla)',
    paliTitle: 'Navamo Pāṭho: Pañcasīla Samādāna',
    description: 'Khảo cứu từng giới điều trong 5 giới căn bản của người cư sĩ Phật tử tại gia: Không sát sinh, Không trộm cắp, Không tà dâm, Không nói dối, Không uống rượu.',
    level: 'Căn bản',
    estimatedMinutes: 14,
    tags: ['Ngũ Giới', 'Pañcasīla', 'Giới luật', 'Sikkhāpada', 'Samādiyāmi'],
    summaryPoints: [
      'Pañcasīla: Năm điều học gìn giữ thân khẩu trong sạch, tạo nền tảng vững chắc cho thiền định và trí tuệ.',
      'Đuôi câu "sikkhāpadaṃ samādiyāmi" lặp lại ở cả 5 giới nghĩa là: "Con xin thọ trì điều học..."',
      'Mỗi giới ngăn chặn một hành vi tạo nghiệp bất thiện nặng nề dẫn đến sa đọa.'
    ],
    vocabulary: [
      {
        term: 'Sīlaṃ',
        ipa: '/siː.lɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Giới hạnh, đạo đức thanh tịnh, tính tình tốt lành',
        root: '√sīl (huân tập thói quen tốt)'
      },
      {
        term: 'Sikkhāpadaṃ',
        ipa: '/sik.kʰaː.pɐ.dɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Điều học, quy tắc rèn luyện đạo đức',
        note: 'sikkhā (học tập) + pada (bước, điều)'
      },
      {
        term: 'Veramaṇī',
        ipa: '/ʋeː.ɾɐ.mɐ.niː/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Sự kiêng cữ, sự tránh xa, sự từ bỏ dứt khoát',
        root: 'vi- + √ram (dừng lại, tránh xa)'
      },
      {
        term: 'Samādiyāmi',
        ipa: '/sɐ.maː.di.jaː.mi/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Con xin phát nguyện thọ trì, con nguyện thực hành nghiêm cẩn',
        root: 'saṃ- + ā- + √dā (tiếp nhận)'
      }
    ],
    grammarSections: [
      {
        title: 'Bảng 5 Giới Điều Pāḷi — Cấu Trúc & Phân Tích',
        explanation: 'Cả 5 giới đều có cấu trúc: [Hành vi xấu ở Xuất xứ cách -ā] + [Veramaṇī] + [Sikkhāpadaṃ] + [Samādiyāmi]:',
        table: {
          headers: ['Giới Thứ', 'Lời Tụng Pāḷi', 'Phân Tích Thành Tố', 'Dịch Nghĩa Việt'],
          rows: [
            ['1. Bất sát sinh', 'Pāṇātipātā veramaṇī sikkhāpadaṃ samādiyāmi', 'Pāṇa (sinh mạng) + atipātā (giết hại) + veramaṇī (kiêng tránh)', 'Con xin vâng giữ điều học kiêng tránh sát hại chúng sinh.'],
            ['2. Bất thâu đạo', 'Adinnādānā veramaṇī sikkhāpadaṃ samādiyāmi', 'A-dinna (không cho) + ādānā (lấy đi)', 'Con xin vâng giữ điều học kiêng tránh lấy của không cho.'],
            ['3. Bất tà dâm', 'Kāmesu micchācārā veramaṇī sikkhāpadaṃ samādiyāmi', 'Kāmesu (trong các dục) + micchācārā (hành vi tà vạy)', 'Con xin vâng giữ điều học kiêng tránh tà hạnh trong các dục.'],
            ['4. Bất vọng ngữ', 'Musāvādā veramaṇī sikkhāpadaṃ samādiyāmi', 'Musā (nói sai sự thật) + vādā (lời nói)', 'Con xin vâng giữ điều học kiêng tránh nói lời dối trá.'],
            ['5. Bất ẩm tửu', 'Surāmerayamajjapamādaṭṭhānā veramaṇī sikkhāpadaṃ samādiyāmi', 'Surā (rượu nấu) + meraya (rượu men) + majja (chất say) + pamādaṭṭhānā (chỗ phóng dật)', 'Con xin vâng giữ điều học kiêng tránh dùng rượu và các chất say làm say sưa phóng dật.']
          ]
        }
      }
    ],
    quiz: [
      {
        id: 'q9-1',
        question: 'Từ "Adinnādānā" trong giới thứ hai được ghép từ hai từ nào?',
        options: ['Adinna (vật không cho) + ādāna (sự lấy đi)', 'Adi (bắt đầu) + dāna (bố thí)', 'A (không) + dāna (cho)', 'Dinna (đã cho) + ādāna (nhận)'],
        correctIndex: 0,
        explanation: 'Adinna (không được cho) + ādāna (lấy đi) = trộm cắp, lấy của không cho.'
      },
      {
        id: 'q9-2',
        question: 'Mục đích chính của việc thọ giới thứ 5 (Surāmeraya...) là gì?',
        options: ['Tiết kiệm tiền bạc', 'Bảo vệ chánh niệm, ngăn ngừa sự phóng dật mê mờ (Pamāda)', 'Giữ dáng vóc', 'Tránh đau dạ dày'],
        correctIndex: 1,
        explanation: 'Chất say làm hoại mất Chánh niệm (Sati) và tỉnh giác, dẫn đến buông lung phóng dật (Pamāda).'
      }
    ]
  },

  {
    id: 'pali-10-kinh-rai-tam-tu-metta',
    slug: 'kinh-rai-tam-tu-metta-sutta',
    categoryId: 'kinh-tung-thien-mon',
    order: 10,
    title: 'Bài 10: Khảo Sát Kinh Rải Tâm Từ (Karaṇīyametta Sutta)',
    paliTitle: 'Dasamo Pāṭho: Karaṇīyamettasutta Vicaya',
    description: 'Học các thuật ngữ rải tâm từ vô lượng Mettā: "Sabbe sattā bhavantu sukhitattā..." (Nguyện cho tất cả chúng sinh đều được an lạc, thái bình).',
    level: 'Trung cấp',
    estimatedMinutes: 16,
    tags: ['Mettā', 'Tâm Từ', 'Karaṇīyametta Sutta', 'Brahmavihāra', 'Từ Bi Hỷ Xả'],
    summaryPoints: [
      'Mettā: Tình thương yêu vô điều kiện, không phân biệt ranh giới thân sơ hay oán thù.',
      'Sabbe sattā: Tất cả chúng sinh trong 31 cõi hữu tình.',
      'Sukhī hotu / Sukhitattā: Được an lạc nơi thân và thanh tịnh nơi tâm.'
    ],
    vocabulary: [
      {
        term: 'Mettā',
        ipa: '/meːt.taː/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Tâm từ, tình thương thuần khiết mong chúng sinh an lạc',
        root: '√mitt (bạn bè, thân thiết)'
      },
      {
        term: 'Sabbe sattā',
        ipa: '/sɐb.beː sɐt.taː/',
        partOfSpeech: 'Danh từ (Nāma)',
        vietnamese: 'Tất cả chúng sinh (Paṭhamā số nhiều)',
        note: 'sabba (tất cả) + satta (chúng sinh hữu tình)'
      },
      {
        term: 'Sukhitattā',
        ipa: '/su.kʰi.tɐt.taː/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        vietnamese: 'Có tâm an lạc, tràn đầy hỷ lạc thanh tịnh',
        note: 'sukhita (an lạc) + atta (tự thân)'
      },
      {
        term: 'Avera',
        ipa: '/ɐ.ʋeː.ɾɐ/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        vietnamese: 'Không oan trái, không hận thù, hòa ái',
        note: 'a- (không) + vera (hận thù)'
      }
    ],
    grammarSections: [],
    verseAnalysis: {
      originalPali: 'Sukhino vā khemino hontu,\nSabbe sattā bhavantu sukhitattā.\nYe keci pāṇabhūtatthi,\nTasa vā thāvarā vā anavasesā.',
      vietnamese: 'Nguyện chúng sinh an lạc và thái bình,\nNguyện cho muôn loài tâm hồn được hoan hỷ.\nTất cả những sinh linh hiện hữu,\nDù yếu đuối hay kiên cường, không trừ một ai.',
      english: 'May all beings be happy and secure, may they be happy-minded. Whatever living beings there are, weak or strong, without exception.',
      context: 'Đức Phật ban bài kinh này cho các vị tỳ-kheo hành thiền trong rừng sâu bị các Chư thiên dạ-xoa quấy nhiễu.',
      breakdown: [
        { word: 'Sukhino', grammar: 'Tính từ nam tánh, Paṭhamā số nhiều', rootOrStem: 'sukhin', meaning: 'Có sự an vui, hạnh phúc' },
        { word: 'vā', grammar: 'Liên từ', rootOrStem: 'vā', meaning: 'Hay là' },
        { word: 'khemino', grammar: 'Tính từ nam tánh, Paṭhamā số nhiều', rootOrStem: 'khemin', meaning: 'Được an toàn, thái bình' },
        { word: 'hontu', grammar: 'Động từ mệnh lệnh cách (Pañcamī), ngôi 3 số nhiều', rootOrStem: '√hū (là, trở nên)', meaning: 'Hãy là, nguyện được là' },
        { word: 'sabbe', grammar: 'Tính từ số nhiều', rootOrStem: 'sabba', meaning: 'Tất cả' },
        { word: 'sattā', grammar: 'Danh từ nam tánh, Paṭhamā số nhiều', rootOrStem: 'satta', meaning: 'Chúng sinh hữu tình' },
        { word: 'bhavantu', grammar: 'Động từ mệnh lệnh cách, ngôi 3 số nhiều', rootOrStem: '√bhū', meaning: 'Nguyện thành tựu, nguyện trở nên' },
        { word: 'sukhitattā', grammar: 'Tính từ ghép số nhiều', rootOrStem: 'sukhita + atta', meaning: 'Có tâm an lạc thanh nhàn' }
      ]
    },
    quiz: [
      {
        id: 'q10-1',
        question: 'Câu kệ "Sabbe sattā bhavantu sukhitattā" mang ý nghĩa gì?',
        options: ['Nguyện cho tôi thành công', 'Nguyện cho tất cả chúng sinh đều được an lạc', 'Nguyện cho mưa thuận gió hòa', 'Nguyện cho tai qua nạn khỏi'],
        correctIndex: 1,
        explanation: '"Sabbe sattā" = tất cả chúng sinh, "bhavantu sukhitattā" = nguyện được hưởng sự an lạc thái bình.'
      },
      {
        id: 'q10-2',
        question: 'Động từ "Bhavantu" ở thể thức (thì/thể) nào trong văn phạm Pāḷi?',
        options: ['Quá khứ', 'Mệnh lệnh / Chúc nguyện cách (Imperative/Benedictive) số nhiều', 'Tương lai', 'Điều kiện cách'],
        correctIndex: 1,
        explanation: 'Đuôi "-ntu" ở ngôi thứ 3 số nhiều biểu đạt lời nguyện ước, chúc nguyện chân thành.'
      }
    ]
  }
];

export function findLessonById(id: string): PaliLesson | undefined {
  if (!id || typeof id !== 'string') return undefined;
  const clean = id.trim().toLowerCase();
  return PALI_LESSONS.find(l => l.id.toLowerCase() === clean || l.slug.toLowerCase() === clean);
}

export function getLessonsByCategory(categoryId: string): PaliLesson[] {
  if (!categoryId || typeof categoryId !== 'string') return [];
  const clean = categoryId.trim().toLowerCase();
  return PALI_LESSONS.filter(l => l.categoryId.toLowerCase() === clean);
}
