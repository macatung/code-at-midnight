/**
 * Pāḷi Canonical Learning Module Data (Học Tiếng Pāḷi Tipiṭaka)
 * Preserving authentic Theravāda language, grammar paradigms, vocabulary & verse analysis.
 * Designed pedagogically from 0 to mastery for beginners.
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

export interface PaliBeginnerStep {
  step: string;
  explanation: string;
  example?: string;
}

export interface PaliBeginnerGuide {
  title: string;
  introduction: string;
  coreConcept: string;
  stepByStep: PaliBeginnerStep[];
  commonMistakes?: string[];
  memoryTips?: string[];
}

export interface PaliPracticeExercise {
  instruction: string;
  paliText: string;
  hint?: string;
  solution: string;
  breakdown?: string;
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
  beginnerGuide?: PaliBeginnerGuide;
  vocabulary: PaliVocabularyItem[];
  grammarSections: PaliGrammarSection[];
  verseAnalysis?: PaliVerseAnalysis;
  practiceExercises?: PaliPracticeExercise[];
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
  // ==========================================================================
  // CATEGORY 1: Bảng Chữ Cái & Phát Âm (Akkharamālā & Uccāraṇa)
  // ==========================================================================
  {
    id: 'pali-01-nguyen-am-phu-am',
    slug: 'nguyen-am-va-phu-am-pali',
    categoryId: 'bang-chu-cai-phat-am',
    order: 1,
    title: 'Bài 1: Hệ Thống 41 Mẫu Tự Pāḷi (Sara & Vyañjana)',
    paliTitle: 'Paṭhamo Pāṭho: Akkharamālā (Sara ca Vyañjana)',
    description: 'Khảo cứu cấu trúc mẫu tự Pāḷi từ con số 0: 8 nguyên âm, 33 phụ âm phân bổ theo 5 vị trí cơ quan phát âm (thanh quản, vòm miệng, uốn lưỡi, răng, môi) và âm mũi Niggahīta.',
    level: 'Căn bản',
    estimatedMinutes: 12,
    tags: ['Mẫu tự', 'Nguyên âm', 'Phụ âm', 'Sara', 'Vyañjana', 'Niggahīta'],
    summaryPoints: [
      'Pāḷi có tổng cộng đúng 41 mẫu tự (Akkhara): 8 nguyên âm (Sara) và 33 phụ âm (Vyañjana).',
      '8 nguyên âm gồm: a, ā, i, ī, u, ū, e, o. Trong đó a, i, u là nguyên âm ngắn (Rassa - 1 sát-na); ā, ī, ū, e, o là nguyên âm dài (Dīgha - 2 sát-na).',
      '33 phụ âm gồm 25 phụ âm có nhóm (Vagga) theo 5 vị trí phát âm, cộng với 8 phụ âm không nhóm (Avagga: y, r, l, v, s, h, ḷ, ṃ).',
      'Pāḷi là ngôn ngữ ngữ âm (phonetic): chữ viết phản ánh chính xác 100% âm thanh phát ra, không có chữ câm (silent letters).'
    ],
    beginnerGuide: {
      title: 'Nhập Môn Pāḷi Cho Người Chưa Có Căn Bản',
      introduction: 'Chào mừng bạn đến với ngôn ngữ của Đức Phật Thích Ca Mâu Ni! Tiếng Pāḷi vốn không có bảng mẫu tự riêng biệt (chữ hình vẽ riêng). Trong lịch sử, kinh văn Pāḷi được ghi bằng chữ Sinhala (Sri Lanka), Thái, Miến Điện, Khmer, và ngày nay được Latinh hóa (Romanized Pāḷi) trên toàn thế giới. Với người Việt Nam, việc dùng chữ cái Latin giúp chúng ta học phát âm cực kỳ nhanh chóng.',
      coreConcept: 'Nguyên tắc vàng: Đọc đúng mặt chữ, phân biệt rạch ròi giữa âm ngắn (1 nhịp) và âm dài có dấu gạch ngang trên đầu (2 nhịp), cùng các phụ âm uốn cong lưỡi (có dấu chấm dưới ṭ, ḍ, ṇ, ḷ).',
      stepByStep: [
        {
          step: '1. Nhận diện dấu gạch trên đầu (Macron)',
          explanation: 'Ký hiệu ā, ī, ū có dấu gạch ngang biểu thị nguyên âm dài. Ví dụ: "a" đọc ngắn dứt khoát như "a" tiếng Việt, còn "ā" ngân dài gấp đôi "a-a".',
          example: 'Dhammā (ngân dài âm mā), Buddha (âm a ngắn dứt khoát).'
        },
        {
          step: '2. Nhận diện dấu chấm dưới (Underdot)',
          explanation: 'Các phụ âm ṭ, ṭh, ḍ, ḍh, ṇ, ḷ có dấu chấm dưới gọi là âm quặt lưỡi (Muddhaja): bạn cần uốn cong đầu lưỡi chạm vào vòm họng cứng phía trên rồi bật âm ra.',
          example: 'Tipiṭaka (chữ ṭ uốn cong lưỡi), Pāḷi (chữ ḷ cong đầu lưỡi).'
        },
        {
          step: '3. Nhận diện âm mũi Niggahīta (ṃ / ŋ)',
          explanation: 'Chữ m có dấu chấm dưới (ṃ) gọi là Niggahīta, phát âm tương tự âm "ng" trong tiếng Việt nhưng giữ hơi đọng lại ở khoang mũi.',
          example: 'Buddhaṃ (Bút-đăng), Saṅghaṃ (Xăng-găng).'
        }
      ],
      commonMistakes: [
        'Nhầm lẫn giữa t (chân răng phẳng) và ṭ (uốn lưỡi chạm nóc vòm họng).',
        'Đọc lướt qua nguyên âm dài ā, ī, ū như nguyên âm ngắn làm mất tính trang nghiêm của câu kinh.',
        'Thêm dấu thanh tiếng Việt (sắc, huyền, hỏi, ngã) thay vì giữ độ phẳng tự nhiên có nhịp của Pāḷi.'
      ],
      memoryTips: [
        'Câu thần chú 8 nguyên âm: "A - Á, I - Í, U - Ú, Ê - Ô" (ngắn đi trước, dài đi sau).',
        '5 vị trí phát âm đi từ sâu trong cổ họng ra ngoài môi: Họng -> Vòm họng -> Nóc vòm cứng (uốn lưỡi) -> Chân răng -> Hai môi.'
      ]
    },
    vocabulary: [
      {
        term: 'Akkharo',
        ipa: '/ɐk.kʰɐ.ɾoː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Mẫu tự, chữ cái, cái bất hoại (không thể tiêu diệt)',
        root: 'a- (không) + √khar (tiêu hoại)',
        note: 'Chỉ các ký tự lưu truyền Chánh Pháp muôn đời bất hoại.',
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
        example: 'Aṭṭha sarā (Tám nguyên âm Pāḷi).'
      },
      {
        term: 'Vyañjanaṃ',
        ipa: '/ʋjɐɲ.ɟɐ.nɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Phụ âm, ký hiệu làm sáng tỏ ý nghĩa',
        root: 'vi- + √añj (làm sáng tỏ)',
        note: 'Gồm 33 phụ âm cần phối hợp với nguyên âm để phát âm trọn vẹn.',
        example: 'Tettiṃsā vyañjanā (Ba mươi ba phụ âm).'
      },
      {
        term: 'Niggahītaṃ',
        ipa: '/niɡ.ɡɐ.ɦiː.tɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Âm mũi Niggahīta (ký hiệu ṃ hoặc ŋ)',
        root: 'ni- + √gah (kềm chế, giữ hơi lại)',
        note: 'Phát âm đóng miệng và cho luồng hơi thoát nhẹ qua khoang mũi.',
        example: 'Buddhaṃ saraṇaṃ gacchāmi.'
      },
      {
        term: 'Kaṇṭhajo',
        ipa: '/kɐn.tʰɐ.ɟoː/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Âm họng, phát sinh từ cuống họng',
        root: 'kaṇṭha (cổ họng) + √jan (sinh ra)',
        note: 'Gồm các âm: k, kh, g, gh, ṅ và nguyên âm a, ā.'
      },
      {
        term: 'Oṭṭhajo',
        ipa: '/ot.tʰɐ.ɟoː/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Âm môi, phát sinh từ hai làn môi',
        root: 'oṭṭha (môi) + √jan (sinh ra)',
        note: 'Gồm các âm: p, ph, b, bh, m và nguyên âm u, ū.'
      }
    ],
    grammarSections: [
      {
        title: '1. Hệ Thống 8 Nguyên Âm (Aṭṭha Sarā) & Độ Dài Âm Tiết',
        explanation: 'Nguyên âm Pāḷi được phân chia dựa trên thời lượng phát âm (Māttā - sát-na phát âm):\n- Nguyên Âm Ngắn (Rassa Sara): a, i, u (phát âm 1 nhịp, gọn gàng, dứt khoát).\n- Nguyên Âm Dài (Dīgha Sara): ā, ī, ū, e, o (phát âm 2 nhịp, ngân dài gấp đôi nguyên âm ngắn).\n- Lưu ý đặc biệt với "e" và "o": Khi đứng trước một phụ âm đơn, chúng là âm dài; khi đứng trước phụ âm kép (Saññoga), chúng tự động được phát âm ngắn lại.',
        table: {
          headers: ['Loại Nguyên Âm', 'Ký Tự Pāḷi', 'Thời Lượng (Māttā)', 'Tương Đương Tiếng Việt', 'Ví Dụ Trong Kinh Văn'],
          rows: [
            ['Ngắn (Rassa)', 'a', '1 nhịp', 'a (ngắn)', 'Mano (Ý), Citta (Tâm)'],
            ['Dài (Dīgha)', 'ā', '2 nhịp', 'a-a (ngân dài)', 'Dhammā (Các pháp), Vācā (Lời nói)'],
            ['Ngắn (Rassa)', 'i', '1 nhịp', 'i (ngắn)', 'Ti-saraṇa (Tam quy), Muni (Bậc hiền triết)'],
            ['Dài (Dīgha)', 'ī', '2 nhịp', 'i-i (ngân dài)', 'Sīla (Giới hạnh), Nīti (Đạo lý)'],
            ['Ngắn (Rassa)', 'u', '1 nhịp', 'u (ngắn)', 'Dukkha (Khổ não), Guru (Tôn sư)'],
            ['Dài (Dīgha)', 'ū', '2 nhịp', 'u-u (ngân dài)', 'Rūpa (Sắc pháp), Mūla (Cội rễ)'],
            ['Biến thể (Dài/Ngắn)', 'e', '2 nhịp / 1 nhịp', 'ê / e', 'Eko (Một mình), Khetta (Ruộng phước)'],
            ['Biến thể (Dài/Ngắn)', 'o', '2 nhịp / 1 nhịp', 'ô / o', 'Loko (Thế gian), Oṭṭha (Môi)']
          ]
        },
        tip: 'Khi đọc tụng, hãy giữ trọn vẹn độ ngân của các chữ ā, ī, ū để tạo nên nhịp điệu thiền vị sâu lắng.'
      },
      {
        title: '2. Bảng 5 Nhóm Phụ Âm (Vagga Vyañjana) & 5 Cơ Quan Phát Âm',
        explanation: '25 phụ âm có nhóm được sắp xếp thành ma trận 5x5 hoàn hảo theo thứ tự từ cuống họng ra ngoài môi. Mỗi nhóm gồm 5 cột:\n1. Bất thanh vô khí (Unvoiced Unaspirated): Phát âm nhẹ, không rung dây thanh, không bật hơi mạnh.\n2. Bất thanh hữu khí (Unvoiced Aspirated): Có chữ "h" đi kèm, bật luồng hơi mạnh từ cuống họng.\n3. Hữu thanh vô khí (Voiced Unaspirated): Rung dây thanh quản rõ ràng.\n4. Hữu thanh hữu khí (Voiced Aspirated): Rung dây thanh kết hợp bật hơi mạnh mẽ.\n5. Âm mũi (Nasal): Luồng hơi thoát lên khoang mũi.',
        table: {
          headers: ['Nhóm (Vagga)', 'Cơ Quan Phát Âm', 'Cột 1 (Vô khí)', 'Cột 2 (Hữu khí)', 'Cột 3 (Hữu thanh)', 'Cột 4 (Hữu thanh+khí)', 'Cột 5 (Âm mũi)'],
          rows: [
            ['1. Ka-vagga', 'Cuống họng (Kaṇṭhaja)', 'k (ca)', 'kh (khơ)', 'g (gờ)', 'gh (g-h)', 'ṅ (ngờ)'],
            ['2. Ca-vagga', 'Vòm họng trên (Tāluja)', 'c (chờ)', 'ch (ch-h)', 'j (dờ/gi)', 'jh (j-h)', 'ñ (nhờ)'],
            ['3. Ṭa-vagga', 'Vòm nóc cứng uốn lưỡi (Muddhaja)', 'ṭ (t uốn lưỡi)', 'ṭh (th uốn)', 'ḍ (đ uốn)', 'ḍh (đ-h)', 'ṇ (n uốn)'],
            ['4. Ta-vagga', 'Chân răng phẳng (Dantaja)', 't (tờ phẳng)', 'th (thờ phẳng)', 'd (đờ)', 'dh (đ-h)', 'n (nờ phẳng)'],
            ['5. Pa-vagga', 'Hai bờ môi (Oṭṭhaja)', 'p (pờ)', 'ph (phờ)', 'b (bờ)', 'bh (b-h)', 'm (mờ)']
          ]
        },
        tip: 'Phụ âm có "h" đi kèm (kh, gh, ch, jh, ṭh, ḍh, th, dh, ph, bh) luôn là âm bật hơi mạnh (Aspirated). Hãy để bàn tay trước miệng: khi đọc "kh", bạn sẽ thấy luồng hơi ấm phà vào lòng bàn tay.'
      },
      {
        title: '3. Tám Phụ Âm Không Nhóm (Avagga) & Âm Mũi Niggahīta (ṃ)',
        explanation: 'Gồm các phụ âm linh hoạt và âm mũi không thuộc 5 nhóm trên:\n- y: phát âm như "d" miền Nam hoặc "y" trôi chảy.\n- r: âm rung đầu lưỡi nhẹ.\n- l: âm "l" êm dịu chạm răng trên.\n- v: phát âm giữa âm "v" và "w".\n- s: âm xát răng nhẹ (như "x" nhẹ trong tiếng Việt).\n- h: âm hắt hơi thanh quản.\n- ḷ: phụ âm quặt lưỡi đặc trưng của Pāḷi (cong đầu lưỡi lên vòm nóc họng rồi bật ra).\n- ṃ (Niggahīta): đóng khẩu hình và ngâm hơi lên khoang mũi.',
        tip: 'Chữ "ḷ" xuất hiện ngay trong tên gọi của ngôn ngữ: "Pāḷi" — hãy uốn cong lưỡi khi phát âm từ này.'
      }
    ],
    practiceExercises: [
      {
        instruction: 'Hãy nhận diện cơ quan phát âm của các phụ âm trong từ "Buddha":',
        paliText: 'B - u - d - dh - a',
        hint: 'Chữ B thuộc nhóm môi (Pa-vagga), chữ d và dh thuộc nhóm răng (Ta-vagga).',
        solution: 'B là âm môi (Oṭṭhaja), u là nguyên âm ngắn, d là âm răng hữu thanh vô khí, dh là âm răng hữu thanh hữu khí (bật hơi), a là nguyên âm họng ngắn.',
        breakdown: 'B (Môi) -> u (Môi) -> d (Răng) -> dh (Răng bật hơi) -> a (Họng)'
      },
      {
        instruction: 'Đọc và tách âm tiết của từ "Tipiṭaka":',
        paliText: 'Ti - pi - ṭa - ka',
        hint: 'Chú ý chữ ṭ có dấu chấm dưới là âm uốn lưỡi.',
        solution: 'Ti (ngắn) + pi (ngắn) + ṭa (uốn cong lưỡi) + ka (ngắn). Cả 4 âm tiết đều là nguyên âm ngắn Lahu.'
      }
    ],
    quiz: [
      {
        id: 'q1-1',
        question: 'Ngôn ngữ Pāḷi có tổng cộng bao nhiêu mẫu tự (Akkhara)?',
        options: ['26 mẫu tự', '41 mẫu tự (8 nguyên âm, 33 phụ âm)', '54 mẫu tự', '32 mẫu tự'],
        correctIndex: 1,
        explanation: 'Pāḷi gồm đúng 41 mẫu tự chuẩn: 8 nguyên âm (Sara) và 33 phụ âm (Vyañjana).'
      },
      {
        id: 'q1-2',
        question: 'Ba nguyên âm ngắn (Rassa Sara) có thời lượng 1 nhịp phát âm là những chữ nào?',
        options: ['ā, ī, ū', 'a, i, u', 'e, o, a', 'k, c, ṭ'],
        correctIndex: 1,
        explanation: 'Ba nguyên âm ngắn (Rassa) phát âm dứt khoát trong 1 sát-na là: a, i, u.'
      },
      {
        id: 'q1-3',
        question: 'Ký tự Niggahīta (ṃ) trong từ "Buddhaṃ" được phát âm như thế nào?',
        options: ['Âm nuốt cuống họng', 'Âm mũi, giữ hơi đọng nhẹ qua khoang mũi tương tự "ng"', 'Âm gió răng', 'Âm câm không phát âm'],
        correctIndex: 1,
        explanation: 'Niggahīta (ṃ) là âm mũi, đóng luồng hơi miệng và thoát nhẹ qua mũi tương tự "ng" ngắn.'
      },
      {
        id: 'q1-4',
        question: 'Ký hiệu có dấu chấm dưới như trong "ṭ", "ḍ", "ṇ", "ḷ" yêu cầu cơ quan phát âm hoạt động thế nào?',
        options: ['Mím chặt hai môi', 'Uốn cong đầu lưỡi chạm vòm nóc cứng phía trên', 'Cắn nhẹ hai hàm răng', 'Mở to khẩu hình hết cỡ'],
        correctIndex: 1,
        explanation: 'Dấu chấm dưới biểu thị nhóm âm Muddhaja (uốn lưỡi): người đọc cần uốn cong đầu lưỡi chạm vào nóc vòm họng trên.'
      }
    ]
  },

  // ==========================================================================
  // CATEGORY 1: Bài 2: Quy Tắc Trọng Âm & Đọc Tụng Chuẩn Pāḷi
  // ==========================================================================
  {
    id: 'pali-02-quy-tac-phat-am',
    slug: 'quy-tac-phat-am-chuan-va-trong-am',
    categoryId: 'bang-chu-cai-phat-am',
    order: 2,
    title: 'Bài 2: Quy Tắc Trọng Âm & Đọc Tụng Chuẩn Pāḷi',
    paliTitle: 'Dutiyo Pāṭho: Uccāraṇavidhi & Garulahu',
    description: 'Quy tắc phân định âm nặng (Garu), âm nhẹ (Lahu), cách xử lý phụ âm đôi (Saññoga) và nhịp phách khi tụng kinh điển Theravāda truyền thống.',
    level: 'Căn bản',
    estimatedMinutes: 14,
    tags: ['Phát âm', 'Trọng âm', 'Âm nặng', 'Garu', 'Lahu', 'Phụ âm đôi'],
    summaryPoints: [
      'Garu (Âm Nặng/Trọng Âm): Là âm tiết chứa nguyên âm dài (ā, ī, ū, e, o) HOẶC nguyên âm ngắn đứng trước phụ âm kép hay âm mũi Niggahīta.',
      'Lahu (Âm Nhẹ/Khinh Âm): Là âm tiết chỉ chứa nguyên âm ngắn đứng riêng lẻ đơn độc.',
      'Khi gặp phụ âm đôi (ví dụ: dd, kk, mm, ññ), phụ âm thứ nhất khép âm tiết trước làm âm đóng, phụ âm thứ hai mở đầu âm tiết sau.',
      'Pāḷi không có dấu sắc/huyền như tiếng Việt, giai điệu tụng được tạo nên từ sự hòa quyện nhịp nhàng giữa Garu và Lahu.'
    ],
    beginnerGuide: {
      title: 'Bí Quyết Tụng Đọc Pāḷi Chuẩn Xác & Trang Nghiêm',
      introduction: 'Rất nhiều người mới học tiếng Pāḷi thường đọc theo thói quen gán dấu thanh tiếng Việt (sắc, huyền, hỏi, ngã, nặng) khiến câu kinh nghe bị méo tiếng hoặc thiếu nhịp phách. Trong văn phạm Pāḷi cổ điển, âm luật được quy định bằng sự tương phản giữa Garu (Trọng) và Lahu (Khinh).',
      coreConcept: 'Garu mang độ nặng và thời lượng 2 nhịp. Lahu mang độ nhẹ và thời lượng 1 nhịp. Tụng kinh Pāḷi chính là bản hòa ca của các nhịp 2-1-2-2-1.',
      stepByStep: [
        {
          step: '1. Quy tắc tách phụ âm đôi (Saññoga)',
          explanation: 'Khi thấy hai phụ âm đứng liền nhau (như dd trong Buddha, mm trong Dhamma, tth trong Attha), hãy chia đôi: chữ thứ nhất kéo về âm trước, chữ thứ hai sang âm sau.',
          example: 'Bud-dho (nhấn Bud dứt khoát, sau đó mở dho), Dham-mo (nhấn Dham, sau đó mở mo).'
        },
        {
          step: '2. Xác định vị trí trọng âm (Stress)',
          explanation: 'Trọng âm trong từ Pāḷi thường nằm ở âm Garu gần cuối từ (âm kế cuối). Nếu âm kế cuối là Lahu nhẹ, trọng âm sẽ lùi về âm thứ 3 từ dưới lên.',
          example: 'Bha-ga-va-to: Âm "to" là âm cuối, âm "va" là Lahu, âm "ga" là Lahu -> nhấn nhẹ vào "Bha" và dứt khoát ở "to".'
        }
      ],
      commonMistakes: [
        'Đọc lướt phụ âm đôi như phụ âm đơn: ví dụ đọc "Dhammo" thành "Da-mo" thay vì "Dham-mo".',
        'Tự ý thêm dấu sắc thành "Bút-đá" thay vì đọc phẳng trang nghiêm "Bud-dho".'
      ],
      memoryTips: [
        'Garu = Gánh nặng (2 nhịp). Lahu = Lông hồng (1 nhịp nhẹ).',
        'Cứ thấy phụ âm đôi hoặc dấu gạch ngang trên đầu -> Chắc chắn là Garu!'
      ]
    },
    vocabulary: [
      {
        term: 'Garu',
        ipa: '/ɡɐ.ɾu/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        vietnamese: 'Nặng, trọng yếu, tôn kính, âm tiết mang trọng âm',
        note: 'Có thời lượng 2 nhịp phát âm (Dīgha hoặc âm khép).',
        example: 'Garulo (Bậc tôn sư đáng kính).'
      },
      {
        term: 'Lahu',
        ipa: '/lɐ.ɦu/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        vietnamese: 'Nhẹ, thanh thoát, khinh âm',
        note: 'Thời lượng 1 nhịp phát âm dứt khoát.',
        example: 'Lahutā (Trạng thái nhẹ nhàng thanh tịnh của tâm sở).'
      },
      {
        term: 'Saññogo',
        ipa: '/sɐɲ.ɲoː.ɡoː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Phụ âm ghép đôi, sự liên kết liên âm',
        root: 'saṃ- + √yuj (kết hợp lại)',
        example: 'Phụ âm đôi trong "Dham-ma", "San-gha".'
      },
      {
        term: 'Uccāraṇaṃ',
        ipa: '/ut.t͡ɕaː.ɾɐ.nɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Sự phát âm, cách xướng âm chuẩn xác',
        root: 'ud- + √car (bật ra, xướng lên)',
        example: 'Suddha-uccāraṇaṃ (Phát âm thuần khiết).'
      }
    ],
    grammarSections: [
      {
        title: '1. Bảng Phân Biệt Âm Tiết Garu (Nặng) & Lahu (Nhẹ)',
        explanation: 'Một âm tiết được xếp vào Garu khi thỏa mãn một trong các điều kiện sau:\n1. Chứa nguyên âm dài tự nhiên: ā, ī, ū, e, o.\n2. Chứa nguyên âm ngắn nhưng theo sau là phụ âm đôi (Saññoga).\n3. Chứa nguyên âm ngắn nhưng theo sau là âm mũi Niggahīta (ṃ).',
        table: {
          headers: ['Loại Âm Tiết', 'Điều Kiện Nhận Diện', 'Ký Hiệu Âm Luật', 'Ví Dụ Cụ Thể', 'Giải Thích Cú Pháp'],
          rows: [
            ['Garu (Dài tự nhiên)', 'Chứa ā, ī, ū, e, o', '— (Dài)', 'Vā-cā, Sī-la, Lō-ka', 'Nguyên âm tự thân có trường độ 2 sát-na.'],
            ['Garu (Do phụ âm đôi)', 'Nguyên âm ngắn + Saññoga', '— (Nặng)', 'Dham-mo, Bud-dho, Dit-thi', 'Phụ âm thứ nhất khép âm tiết, làm nặng âm trước.'],
            ['Garu (Do Niggahīta)', 'Nguyên âm ngắn + ṃ', '— (Nặng)', 'Saṃ-gha, Dhaṃ-maṃ', 'Âm mũi đọng lại làm tăng trường độ.'],
            ['Lahu (Khinh âm)', 'Nguyên âm ngắn đứng đơn', '∪ (Ngắn)', 'Ca-ra-ti, Ma-no, Pa-ti', 'Phát âm thanh thoát đúng 1 sát-na.']
          ]
        }
      },
      {
        title: '2. Quy Tắc Tách Âm Tiết Khi Gặp Phụ Âm Kép (Saññoga)',
        explanation: 'Trong tiếng Pāḷi, các từ có phụ âm kép xuất hiện liên tục để tạo âm vang hùng tráng. Hãy luyện tập phân tách theo bảng sau:',
        table: {
          headers: ['Từ Pāḷi Gốc', 'Tách Âm Tiết Chuẩn', 'Phiên Âm Đọc Nhịp', 'Nghĩa Giáo Lý'],
          rows: [
            ['Buddho', 'Bud + dho', 'Bút-đ-hô (nhấn Bud)', 'Đấng Tự Giác Ngộ'],
            ['Dhammo', 'Dham + mo', 'Đhăm-mô (nhấn Dham)', 'Chánh Pháp vi diệu'],
            ['Saṅgho', 'Saṅ + gho', 'Xăng-g-hô (nhấn Saṅ)', 'Tăng già hòa hợp'],
            ['Nibbānaṃ', 'Nib + bā + naṃ', 'Níp-ba-năng (ngân bā)', 'Niết bàn tịch tịnh'],
            ['Anattā', 'A + nat + tā', 'A-nát-ta (ngân tā)', 'Vô ngã, bất khả hoán']
          ]
        },
        tip: 'Khi tụng câu kinh đảnh lễ: "Namo Tassa Bhagavato Arahato Sammāsambuddhassa" -> Tách âm: "Na-mo Tas-sa Bha-ga-va-to A-ra-ha-to Sam-mā-sam-bud-dhas-sa".'
      }
    ],
    practiceExercises: [
      {
        instruction: 'Xác định các âm tiết Garu và Lahu trong từ "Nibbānaṃ":',
        paliText: 'Nib - bā - naṃ',
        hint: 'Âm "Nib" có phụ âm b khép, "bā" có nguyên âm dài ā, "naṃ" có âm mũi ṃ.',
        solution: 'Cả 3 âm tiết đều là Garu! Nib (Garu do phụ âm đôi) - bā (Garu do nguyên âm dài ā) - naṃ (Garu do Niggahīta ṃ).'
      }
    ],
    quiz: [
      {
        id: 'q2-1',
        question: 'Từ "Dhammo" được phân tách âm tiết chuẩn xác theo văn phạm Pāḷi như thế nào?',
        options: ['Dha + mmo', 'Dham + mo', 'Dh + am + mo', 'Dha + m + mo'],
        correctIndex: 1,
        explanation: 'Phụ âm đôi "mm" được tách đôi: chữ m thứ nhất khép âm "Dham", chữ m thứ hai mở đầu âm "mo".'
      },
      {
        id: 'q2-2',
        question: 'Trường hợp nào sau đây tạo thành một âm tiết Garu (âm nặng/trọng)?',
        options: ['Chỉ có nguyên âm ngắn a đứng đơn lẻ', 'Nguyên âm dài (ā, ī, ū) hoặc nguyên âm ngắn đứng trước phụ âm kép', 'Chữ cái đứng ở đầu câu', 'Nguyên âm i đứng ở cuối câu'],
        correctIndex: 1,
        explanation: 'Âm Garu là âm chứa nguyên âm dài (ā, ī, ū, e, o) hoặc âm có phụ âm kép/Niggahīta khép theo sau.'
      },
      {
        id: 'q2-3',
        question: 'Trong câu tụng "Namo Tassa", âm "Tas" trong từ "Tassa" là loại âm tiết gì?',
        options: ['Lahu (Âm nhẹ 1 nhịp)', 'Garu (Âm nặng do có phụ âm s khép âm tiết)', 'Âm câm không đọc', 'Âm gió thanh quản'],
        correctIndex: 1,
        explanation: 'Vì có phụ âm đôi "ss", âm "Tas" trở thành âm đóng mang tính chất Garu (âm nặng, cần nhấn rõ ràng).'
      }
    ]
  },

  // ==========================================================================
  // CATEGORY 2: Ngữ Pháp Pāḷi Căn Bản (Pāḷi Saddanīti Mūla)
  // ==========================================================================
  {
    id: 'pali-03-danh-tu-8-bien-cach',
    slug: 'danh-tu-va-8-bien-cach-vibhatti',
    categoryId: 'ngu-phap-can-ban',
    order: 3,
    title: 'Bài 3: Danh Từ & 8 Biến Cách Pāḷi (Aṭṭha Vibhatti)',
    paliTitle: 'Tatiyo Pāṭho: Nāmapada & Aṭṭhavibhatti',
    description: 'Nền tảng cốt lõi nhất của ngữ pháp Pāḷi: Thấu suốt bản chất 8 biến cách (Vibhatti), 3 giống (Liṅga), 2 số (Vacana) của danh từ Nam tánh tận cùng -a (Buddha, Dhamma, Purisa).',
    level: 'Căn bản',
    estimatedMinutes: 18,
    tags: ['Danh từ', 'Biến cách', 'Vibhatti', 'Nam tánh', 'Aṭṭhavibhatti'],
    summaryPoints: [
      'Pāḷi là ngôn ngữ biến cách (Inflected language): vai trò ngữ pháp của danh từ trong câu được biểu thị qua đuôi biến cách (Vibhatti) chứ không phụ thuộc vào vị trí đứng.',
      '8 Biến Cách gồm: 1. Chủ cách (Paṭhamā), 2. Đối cách (Dutiyā), 3. Sở dụng cách (Tatiyā), 4. Chỉ định cách (Catutthī), 5. Xuất xứ cách (Pañcamī), 6. Sở thuộc cách (Chaṭṭhī), 7. Định vị cách (Sattamī), 8. Hô cách (Ālapana).',
      'Mỗi biến cách luôn có 2 dạng số: Số ít (Ekavacana) và Số nhiều (Bahuvacana).',
      'Danh từ Nam tánh tận cùng bằng mẫu tự "-a" (A-kārantā Pulliṅga) là dạng thức phổ biến nhất trong Tam Tạng Kinh Điển.'
    ],
    beginnerGuide: {
      title: 'Giải Mã Bản Chất 8 Biến Cách Cho Người Mới Bắt Đầu',
      introduction: 'Trong tiếng Việt, chúng ta dùng các giới từ như "của", "bằng", "ở tại", "đến từ" để chỉ mối quan hệ giữa các từ. Ví dụ: "Bằng Chánh Pháp", "Của Đức Phật", "Tại ngôi làng". Trong tiếng Pāḷi, người ta KHÔNG dùng nhiều giới từ rời rạc như vậy, mà họ thay đổi cái đuôi của danh từ. Quá trình gắn đuôi ấy gọi là BIẾN CÁCH (Vibhatti).',
      coreConcept: 'Gốc từ là "Buddha" (Đức Phật). Khi làm chủ ngữ -> biến thành "Buddho". Khi làm tân ngữ bị tác động -> biến thành "Buddhaṃ". Khi là công cụ "nhờ Đức Phật" -> biến thành "Buddhena". Khi mang nghĩa sở hữu "của Đức Phật" -> biến thành "Buddhassa".',
      stepByStep: [
        {
          step: '1. Cách 1: Paṭhamā (Chủ cách - Ai làm gì?)',
          explanation: 'Chỉ chủ thể thực hiện hành động. Số ít thêm đuôi -o, số nhiều thêm đuôi -ā.',
          example: 'Buddho (Đức Phật), Buddhā (Chư Phật).'
        },
        {
          step: '2. Cách 2: Dutiyā (Đối cách - Bị ai tác động?)',
          explanation: 'Chỉ tân ngữ trực tiếp hứng chịu hành động. Số ít thêm đuôi -ṃ, số nhiều thêm đuôi -e.',
          example: 'Buddhaṃ (Đến Đức Phật), Buddhe (Đến chư Phật).'
        },
        {
          step: '3. Cách 3: Tatiyā (Sở dụng cách - Bằng phương tiện gì?)',
          explanation: 'Chỉ phương tiện, công cụ, người đồng hành. Số ít thêm -ena, số nhiều thêm -ehi.',
          example: 'Dhammena (Bằng Chánh Pháp), Buddhehi (Cùng với chư Phật).'
        },
        {
          step: '4. Cách 6: Chaṭṭhī (Sở thuộc cách - Của ai?)',
          explanation: 'Chỉ quan hệ sở hữu. Số ít thêm -assa, số nhiều thêm -ānaṃ.',
          example: 'Buddhassa (Của Đức Phật), Buddhānaṃ (Của chư Phật).'
        }
      ],
      commonMistakes: [
        'Nhầm lẫn giữa Cách 4 (Chỉ định cách: cho/đến ai) và Cách 6 (Sở thuộc cách: của ai) vì ở số ít chúng đều có đuôi "-assa". Cần dựa vào ngữ cảnh động từ.',
        'Dịch sai chủ ngữ khi câu Pāḷi bị đảo trật tự từ: Hãy luôn tìm từ có đuôi Cách 1 (-o, -ā) để xác định ai là chủ ngữ!'
      ],
      memoryTips: [
        'Bảng vần ghi nhớ số ít: "-o (chủ), -aṃ (đối), -ena (bằng), -assa (của), -asmā (từ), -asmiṃ (tại)".'
      ]
    },
    vocabulary: [
      {
        term: 'Buddho',
        ipa: '/bud.dʰoː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Đức Phật (Chủ cách số ít)',
        note: 'Gốc từ "Buddha" + đuôi Paṭhamā số ít "-o".',
        example: 'Buddho lokaṃ passati (Đức Phật quán chiếu thế gian).'
      },
      {
        term: 'Dhammaṃ',
        ipa: '/dʰɐm.mɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Chánh Pháp (Đối cách số ít - tân ngữ trực tiếp)',
        note: 'Gốc từ "Dhamma" + đuôi Dutiyā số ít "-ṃ".',
        example: 'Puriso dhammaṃ suṇāti (Người thiện nam lắng nghe Chánh Pháp).'
      },
      {
        term: 'Dhammena',
        ipa: '/dʰɐm.meː.nɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Bằng Chánh Pháp, theo đúng Chánh Đạo (Sở dụng cách)',
        note: 'Đuôi "-ena" chỉ phương tiện công cụ hoặc phương thức sống.',
        example: 'Dhammena jīvati (Sống chánh mạng đúng theo Chánh Pháp).'
      },
      {
        term: 'Buddhassa',
        ipa: '/bud.dʰɐs.sɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Của Đức Phật / Cúng dường đến Đức Phật',
        note: 'Đuôi "-assa" là Chaṭṭhī (của) hoặc Catutthī (cho/đến).',
        example: 'Buddhassa sāvako (Bậc Thánh đệ tử của Đức Thế Tôn).'
      },
      {
        term: 'Gāmasmā',
        ipa: '/ɡaː.mɐs.maː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Từ ngôi làng, rời khỏi làng (Xuất xứ cách)',
        note: 'Gốc "Gāma" + đuôi Pañcamī "-asmā / -amhā / -ā".',
        example: 'Bhikkhu gāmasmā nikkhamati (Vị tỳ-kheo rời khỏi ngôi làng).'
      },
      {
        term: 'Loke / Lokasmiṃ',
        ipa: '/loː.keː / loː.kɐs.miŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Ở trong thế gian, nơi cõi đời (Định vị cách)',
        note: 'Gốc "Loka" + đuôi Sattamī "-e / -asmiṃ / -amhi".',
        example: 'Buddho loke uppajjati (Đức Phật thị hiện ở thế gian).'
      }
    ],
    grammarSections: [
      {
        title: 'Bảng 8 Biến Cách Mẫu Của Danh Từ Nam Tánh Tận Cùng Bằng "-a" (Buddha - Purisa)',
        explanation: 'Đây là bảng biến cách căn bản và quan trọng nhất trong toàn bộ văn phạm Pāḷi. Bất kỳ danh từ nam tánh tận cùng bằng "-a" nào (như Dhamma, Sangha, Nara, Deva, Loka, Magga) đều biến cách chính xác theo khuôn mẫu sau:',
        table: {
          headers: ['Biến Cách (Vibhatti)', 'Chức Năng Ngữ Pháp', 'Số Ít (Ekavacana)', 'Số Nhiều (Bahuvacana)', 'Ý Nghĩa Dịch Nghĩa'],
          rows: [
            ['1. Paṭhamā (Chủ cách)', 'Chủ từ thực hiện hành động', 'Buddho', 'Buddhā', 'Đức Phật / Chư Phật'],
            ['2. Dutiyā (Đối cách)', 'Tân ngữ trực tiếp (bị tác động)', 'Buddhaṃ', 'Buddhe', 'Đến Đức Phật / Đến chư Phật'],
            ['3. Tatiyā (Sở dụng cách)', 'Phương tiện, công cụ (bằng, nhờ, với)', 'Buddhena', 'Buddhehi / Buddhebhi', 'Bởi Đức Phật / Nhờ chư Phật'],
            ['4. Catutthī (Chỉ định cách)', 'Tân ngữ gián tiếp (cho, cúng dường đến)', 'Buddhassa / Buddhāya', 'Buddhānaṃ', 'Cho Đức Phật / Cúng dường chư Phật'],
            ['5. Pañcamī (Xuất xứ cách)', 'Nơi xuất phát, rời xa (từ, do)', 'Buddhasmā / Buddhamhā / Buddhā', 'Buddhehi / Buddhebhi', 'Từ Đức Phật / Do chư Phật'],
            ['6. Chaṭṭhī (Sở thuộc cách)', 'Chỉ quyền sở hữu (của)', 'Buddhassa', 'Buddhānaṃ', 'Của Đức Phật / Của chư Phật'],
            ['7. Sattamī (Định vị cách)', 'Vị trí, thời gian (nơi, ở, trên, trong)', 'Buddhasmiṃ / Buddhamhi / Buddhe', 'Buddhesu', 'Nơi Đức Phật / Trong chư Phật'],
            ['8. Ālapana (Hô cách)', 'Kêu gọi, xưng hô tôn kính', 'Buddha / Buddhā', 'Buddhā', 'Kính bạch Đức Phật!']
          ]
        },
        tip: 'Mẹo nhận diện nhanh số nhiều:\n- Cách 2 số nhiều là "-e" (Buddhe).\n- Cách 6 số nhiều là "-ānaṃ" (Buddhānaṃ).\n- Cách 7 số nhiều là "-esu" (Buddhesu).'
      },
      {
        title: 'Phân Tích Cú Pháp Câu Thực Tế Với Biến Cách',
        explanation: 'Hãy quan sát cách các biến cách phối hợp trong một câu Pāḷi hoàn chỉnh:\n"Puriso dhammena saha gāmasmā vihāraṃ gacchati."\n- Puriso (Cách 1 - Chủ từ): Người đàn ông\n- dhammena saha (Cách 3 - Sở dụng cách đi với saha): cùng với Chánh Pháp\n- gāmasmā (Cách 5 - Xuất xứ cách): từ ngôi làng\n- vihāraṃ (Cách 2 - Đối cách chỉ nơi đến): đến tu viện\n- gacchati (Động từ ngôi 3 số ít): đang đi.',
        tip: 'Dù đổi trật tự từ thành: "Gāmasmā puriso vihāraṃ gacchati dhammena saha", câu văn vẫn mang nguyên vẹn ý nghĩa vì đuôi biến cách đã cố định vai trò của từng chữ!'
      }
    ],
    practiceExercises: [
      {
        instruction: 'Xác định biến cách của từ in đậm trong câu: "Sāvako **dhammena** jīvati."',
        paliText: 'dhammena',
        hint: 'Đuôi "-ena" chỉ phương tiện công cụ (Sở dụng cách).',
        solution: 'Tatiyā Vibhatti (Sở dụng cách số ít) -> Dịch: "Vị đệ tử sống bằng/theo Chánh Pháp".'
      },
      {
        instruction: 'Điền đuôi biến cách phù hợp để tạo câu: "Vị tỳ-kheo (Bhikkhu) lắng nghe Chánh Pháp (Dhamma)"',
        paliText: 'Bhikkhu [Dhamma...] suṇāti.',
        hint: 'Dhamma đóng vai trò tân ngữ trực tiếp (Đối cách số ít).',
        solution: 'Dhammaṃ (Dutiyā số ít) -> Câu hoàn chỉnh: "Bhikkhu dhammaṃ suṇāti".'
      }
    ],
    quiz: [
      {
        id: 'q3-1',
        question: 'Trong câu "Puriso dhammaṃ suṇāti", từ "dhammaṃ" đang ở biến cách nào?',
        options: ['Chủ cách (Paṭhamā)', 'Đối cách (Dutiyā - tân ngữ trực tiếp)', 'Sở dụng cách (Tatiyā)', 'Sở thuộc cách (Chaṭṭhī)'],
        correctIndex: 1,
        explanation: 'Đuôi "-ṃ" trong "dhammaṃ" là biến cách Dutiyā (Đối cách số ít), đóng vai trò tân ngữ chịu tác động của hành động lắng nghe.'
      },
      {
        id: 'q3-2',
        question: 'Dạng số nhiều Sở thuộc cách (chỉ quan hệ sở hữu "của các vị...") của danh từ "Dhamma" là gì?',
        options: ['Dhammānaṃ', 'Dhammesu', 'Dhamme', 'Dhammena'],
        correctIndex: 0,
        explanation: 'Đuôi Sở thuộc cách số nhiều của danh từ tận cùng -a là "-ānaṃ", do đó "Dhammānaṃ" có nghĩa là "của các Pháp".'
      },
      {
        id: 'q3-3',
        question: 'Biến cách nào trong Pāḷi mang ý nghĩa "bằng phương tiện...", "nhờ vào..." (đuôi -ena ở số ít)?',
        options: ['Paṭhamā (Chủ cách)', 'Tatiyā (Sở dụng cách)', 'Sattamī (Định vị cách)', 'Pañcamī (Xuất xứ cách)'],
        correctIndex: 1,
        explanation: 'Tatiyā vibhatti (Sở dụng cách / Công cụ cách) có đuôi "-ena", chỉ phương tiện hay công cụ thực hiện.'
      },
      {
        id: 'q3-4',
        question: 'Từ "Buddhasmiṃ" hoặc "Buddhe" thuộc biến cách thứ mấy và mang nghĩa là gì?',
        options: ['Cách 1: Đức Phật', 'Cách 5: Từ Đức Phật', 'Cách 7 (Sattamī - Định vị cách): Nơi Đức Phật / Trong Đức Phật', 'Cách 8: Kính lạy Đức Phật'],
        correctIndex: 2,
        explanation: 'Sattamī Vibhatti (Định vị cách / Vị trí cách) có đuôi "-asmiṃ / -amhi / -e" ở số ít, chỉ nơi chốn, thời gian hoặc đối tượng quy hướng.'
      }
    ]
  },

  // ==========================================================================
  // CATEGORY 2: Bài 4: Động Từ Thì Hiện Tại (Vattamānā Ākhyāta)
  // ==========================================================================
  {
    id: 'pali-04-dong-tu-thoi-hien-tai',
    slug: 'dong-tu-va-thoi-hien-tai-akhyata',
    categoryId: 'ngu-phap-can-ban',
    order: 4,
    title: 'Bài 4: Động Từ Thì Hiện Tại (Vattamānā Ākhyāta)',
    paliTitle: 'Catuttho Pāṭho: Ākhyātapada & Vattamānā Kāla',
    description: 'Nắm vững quy tắc chia động từ thì hiện tại trong Pāḷi theo 3 ngôi (Purisa) và tiếp vĩ ngữ chia kinh điển: ti - anti, si - tha, mi - ma.',
    level: 'Căn bản',
    estimatedMinutes: 16,
    tags: ['Động từ', 'Ākhyāta', 'Hiện tại', 'Vattamānā', 'Chia động từ'],
    summaryPoints: [
      'Động từ Pāḷi cấu tạo từ: Căn động từ (Dhātu) + Yếu tố biến tố trung tố (Vikarana) + Biến tố ngôi thì (Vibhatti).',
      'Thứ tự 3 ngôi trong văn phạm Pāḷi: Ngôi thứ 3 (Paṭhama purisa - Người ấy/Họ), Ngôi thứ 2 (Majjhima purisa - Bạn/Các bạn), Ngôi thứ 1 (Uttama purisa - Tôi/Chúng tôi).',
      'Bộ đuôi thì hiện tại chủ động (Parassapada): ti - anti, si - tha, mi - ma.',
      'Quy tắc biến âm quan trọng: Trước đuôi "-mi" và "-ma", nguyên âm "a" ngắn luôn biến thành "ā" dài (ví dụ: gaccha -> gacchāmi).'
    ],
    beginnerGuide: {
      title: 'Hiểu Cấu Trúc Động Từ Pāḷi Như Trò Chơi Ghép Hình',
      introduction: 'Nếu danh từ biến đổi đuôi theo Biến Cách (Vibhatti) thì động từ biến đổi đuôi theo Ngôi (Purisa) và Số (Vacana). Một khi nắm được 6 chiếc đuôi kỳ diệu: ti, anti, si, tha, mi, ma, bạn sẽ có thể tự tin đọc hiểu hàng ngàn câu kinh trong Tam Tạng.',
      coreConcept: 'Công thức giải phẫu động từ: [Căn Động Từ (Gốc Nghĩa)] + [Biến Tố Thì] = Động Từ Hoàn Chỉnh.',
      stepByStep: [
        {
          step: '1. Ngôi thứ 3 (Người ấy / Họ đang làm...)',
          explanation: 'Số ít dùng đuôi "-ti", số nhiều dùng đuôi "-anti".',
          example: 'SoRecord so (Anh ấy đi) / Te gacchanti (Họ đi).'
        },
        {
          step: '2. Ngôi thứ 2 (Bạn / Các bạn đang làm...)',
          explanation: 'Số ít dùng đuôi "-si", số nhiều dùng đuôi "-tha".',
          example: 'Tvaṃ gacchasi (Bạn đi) / Tumhe gacchatha (Các bạn đi).'
        },
        {
          step: '3. Ngôi thứ 1 (Tôi / Chúng tôi đang làm...)',
          explanation: 'Số ít dùng đuôi "-mi", số nhiều dùng đuôi "-ma" (nhớ kéo dài âm ā trước nó).',
          example: 'Ahaṃ gacchāmi (Tôi đi) / Mayaṃ gacchāma (Chúng tôi đi).'
        }
      ],
      commonMistakes: [
        'Quên kéo dài nguyên âm trước -mi và -ma: viết "gacchami" là sai, phải viết đúng là "gacchāmi".',
        'Nhầm ngôi: Trong tiếng Anh "First person" là "Tôi", nhưng trong Pāḷi "Paṭhama purisa" (Ngôi thứ nhất theo thứ tự kinh điển) lại là "Người ấy / Họ" (tương đương Ngôi thứ 3 trong ngữ pháp phương Tây).'
      ],
      memoryTips: [
        'Học thuộc bài ca 6 chữ: "ti - anti, si - tha, mi - ma". Lặp lại 5 lần là thuộc mãi mãi!'
      ]
    },
    vocabulary: [
      {
        term: 'Gacchati',
        ipa: '/ɡɐt.t͡ɕʰɐ.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Đi, bước đi (Ngôi thứ 3 số ít)',
        root: '√gam (đi)',
        example: 'Bhikkhu gāmaṃRecord (Vị tỳ-kheo đi vào ngôi làng).'
      },
      {
        term: 'Deseti',
        ipa: '/deː.seː.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Thuyết giảng, chỉ dạy Chánh Pháp',
        root: '√dis (chỉ rõ, giảng giải)',
        example: 'Satthā dhammaṃ deseti (Bậc Đạo Sư thuyết giảng Chánh Pháp).'
      },
      {
        term: 'Passati',
        ipa: '/pɐs.sɐ.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Thấy, quán chiếu bằng tuệ giác',
        root: '√dis / pas (thấy)',
        example: 'Paññāya passati (Quán thấy sáng tỏ bằng trí tuệ).'
      },
      {
        term: 'Gacchāmi',
        ipa: '/ɡɐt.t͡ɕʰaː.mi/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Con xin đi đến, con nguyện nương tựa (Ngôi 1 số ít)',
        root: '√gam + mi',
        note: 'Xuất hiện trong câu Quy Y Tam Bảo kinh điển.',
        example: 'Buddhaṃ saraṇaṃ gacchāmi (Con xin nương tựa Đức Phật).'
      },
      {
        term: 'Suṇāti',
        ipa: '/su.naː.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Lắng nghe, tiếp thu âm thanh Chánh Pháp',
        root: '√su (nghe)',
        example: 'Upāsako dhammaṃ suṇāti (Người cư sĩ lắng nghe Pháp).'
      },
      {
        term: 'Karoti',
        ipa: '/kɐ.ɾoː.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Làm, tạo tác, thực hiện hành động',
        root: '√kar (làm, tạo tác)',
        example: 'Kusalaṃ karoti (Thực hiện hành động thiện lành).'
      }
    ],
    grammarSections: [
      {
        title: 'Bảng Đuôi Chia Động Từ Hiện Tại (Vattamānā Parassapada) & Đại Từ Đi Kèm',
        explanation: 'Quy tắc chia mẫu cho căn động từ √gam (Gaccha- : Đi):',
        table: {
          headers: ['Ngôi (Purisa)', 'Đại Từ Tương Ứng', 'Số Ít (Ekavacana)', 'Số Nhiều (Bahuvacana)', 'Ví Dụ Câu Hoàn Chỉnh'],
          rows: [
            ['Ngôi 3 (Paṭhama - Người ấy/Họ)', 'So / Sā / Te', '-ti (Gacchati)', '-anti (Gacchanti)', 'So gacchati (Vị ấy đi) / Te gacchanti (Họ đi)'],
            ['Ngôi 2 (Majjhima - Bạn/Các bạn)', 'Tvaṃ / Tumhe', '-si (Gacchasi)', '-tha (Gacchatha)', 'Tvaṃ gacchasi (Bạn đi) / Tumhe gacchatha (Các bạn đi)'],
            ['Ngôi 1 (Uttama - Tôi/Chúng tôi)', 'Ahaṃ / Mayaṃ', '-mi (Gacchāmi)', '-ma (Gacchāma)', 'Ahaṃ gacchāmi (Tôi đi) / Mayaṃ gacchāma (Chúng tôi đi)']
          ]
        },
        tip: 'Quy tắc bắt buộc: Trước đuôi "-mi" và "-ma", nguyên âm "a" ngắn luôn biến thành nguyên âm "ā" dài: Vada -> Vadāmi, Tittha -> Tiṭṭhāma.'
      },
      {
        title: 'Bảng Tra Cứu Các Động Từ Phổ Biến Nhất Trong Tam Tạng',
        explanation: 'Các động từ thường gặp trong các bài kinh Nikāya:',
        table: {
          headers: ['Căn Động Từ', 'Dạng Ngôi 3 Số Ít (-ti)', 'Dạng Ngôi 1 Số Ít (-mi)', 'Nghĩa Tiếng Việt'],
          rows: [
            ['√gam (đi)', 'Gacchati', 'Gacchāmi', 'Đi, bước đến, quy y'],
            ['√su (nghe)', 'Suṇāti', 'Suṇāmi', 'Lắng nghe Chánh Pháp'],
            ['√pas (thấy)', 'Passati', 'Passāmi', 'Quán thấy, nhìn thấy'],
            ['√bhās (nói)', 'Bhāsati', 'Bhāsāmi', 'Nói năng, phát ngôn'],
            ['√kar (làm)', 'Karoti', 'Karomi', 'Hành động, tạo nghiệp'],
            ['√bhū (là, trở thành)', 'Bhavati', 'Bhavāmi', 'Là, hiện hữu, trở nên'],
            ['√jīv (sống)', 'Jīvati', 'Jīvāmi', 'Mưu sinh, sinh sống']
          ]
        }
      }
    ],
    practiceExercises: [
      {
        instruction: 'Chia động từ trong ngoặc theo đúng chủ ngữ: "Mayaṃ dhammaṃ (suṇāti)."',
        paliText: 'Mayaṃ dhammaṃ ...',
        hint: 'Chủ ngữ "Mayaṃ" là Ngôi thứ 1 số nhiều (Chúng tôi).',
        solution: 'suṇāma -> Câu hoàn chỉnh: "Mayaṃ dhammaṃ suṇāma" (Chúng tôi lắng nghe Chánh Pháp).'
      },
      {
        instruction: 'Dịch câu sau sang tiếng Pāḷi: "Tôi đi đến ngôi chùa"',
        paliText: 'Ahaṃ vihāraṃ ...',
        hint: 'Động từ đi ngôi thứ 1 số ít là gacchāmi.',
        solution: 'Ahaṃ vihāraṃ gacchāmi.'
      }
    ],
    quiz: [
      {
        id: 'q4-1',
        question: 'Đuôi chia động từ thì hiện tại ngôi thứ nhất số ít ("Tôi...") là gì?',
        options: ['-ti', '-si', '-mi (ví dụ: gacchāmi)', '-anti'],
        correctIndex: 2,
        explanation: 'Đuôi ngôi thứ nhất số ít là "-mi", như trong lời tuyên thệ quy y "Buddhaṃ saraṇaṃ gacchāmi" (Con xin nương tựa Đức Phật).'
      },
      {
        id: 'q4-2',
        question: 'Dịch câu sau sang Pāḷi: "Chư tỳ-kheo lắng nghe Chánh Pháp" (Biết: Bhikkhū = Chư Tăng số nhiều, Dhammaṃ = Pháp, Suṇāti = nghe)?',
        options: ['Bhikkhū dhammaṃ suṇāti', 'Bhikkhū dhammaṃ suṇanti', 'Bhikkhū dhammaṃ suṇasi', 'Bhikkhū dhammaṃ suṇāma'],
        correctIndex: 1,
        explanation: 'Vì chủ ngữ "Bhikkhū" là ngôi thứ 3 số nhiều, động từ phải chia đuôi "-anti" -> "suṇanti".'
      },
      {
        id: 'q4-3',
        question: 'Khi gắn đuôi "-mi" hoặc "-ma" vào thân động từ kết thúc bằng nguyên âm "a", hiện tượng gì xảy ra?',
        options: ['Nguyên âm "a" bị lược bỏ hoàn toàn', 'Nguyên âm "a" ngắn biến thành nguyên âm "ā" dài', 'Thêm phụ âm "h" vào giữa', 'Không có gì thay đổi'],
        correctIndex: 1,
        explanation: 'Quy tắc âm luật Pāḷi quy định nguyên âm "a" ngắn trước "-mi" và "-ma" luôn kéo dài thành "ā" (Gaccha -> Gacchāmi).'
      }
    ]
  },

  // ==========================================================================
  // CATEGORY 3: Từ Vựng & Thuật Ngữ Cốt Lõi (Dhamma Vohāra & Mūla Padāni)
  // ==========================================================================
  {
    id: 'pali-05-tam-bao-tam-quy-y',
    slug: 'tam-bao-va-tam-quy-y-tisarana',
    categoryId: 'tu-vung-cot-loi',
    order: 5,
    title: 'Bài 5: Tam Bảo & Lời Tuyên Ngôn Tam Quy Y (Ti-saraṇa)',
    paliTitle: 'Pañcamo Pāṭho: Ratanattaya ca Tisaraṇagamana',
    description: 'Phân tích ngữ nghĩa uyên áo và cấu trúc ngữ pháp từng từ trong câu tụng Tam Quy Y: Buddha, Dhamma, Saṅgha và Saraṇaṃ gacchāmi.',
    level: 'Căn bản',
    estimatedMinutes: 14,
    tags: ['Tam Bảo', 'Tiratana', 'Tam Quy', 'Tisarana', 'Buddha', 'Dhamma', 'Sangha'],
    summaryPoints: [
      'Ratanattaya (Tam Bảo): Ba viên ngọc báu vô thượng gồm Phật Bảo (Buddharatana), Pháp Bảo (Dhammaratana) và Tăng Bảo (Saṅgharatana).',
      'Saraṇa: Nơi nương náu an toàn tuyệt đối, dập tắt mọi hiểm họa của vòng luân hồi sinh tử (Saṃsāra).',
      'Cấu trúc ngữ pháp câu quy y sử dụng thể Hai Đối Cách (Double Accusative): [Buddhaṃ] + [Saraṇaṃ] + [Gacchāmi].'
    ],
    beginnerGuide: {
      title: 'Thấu Suốt Ý Nghĩa Bản Tuyên Ngôn Quy Y Tam Bảo',
      introduction: 'Mỗi buổi lễ hay thời khóa thiền môn Theravāda đều bắt đầu bằng lời tụng Tam Quy Y (Ti-saraṇa-gamana). Hiểu rõ cấu trúc ngữ pháp và nguồn gốc căn từ sẽ giúp tâm thức bạn tràn đầy hỷ lạc và niềm tin trong sáng (Saddhā).',
      coreConcept: 'Quy y không phải là cầu xin sự cứu rỗi thần quyền, mà là hành động chủ động đem hết tâm trí hướng về Trí Tuệ (Phật), Chân Lý Thực Tại (Pháp) và Tinh Hoa Giới Đức Hòa Hợp (Tăng).',
      stepByStep: [
        {
          step: '1. Phân tích thành tố "Buddhaṃ saraṇaṃ gacchāmi"',
          explanation: 'Buddhaṃ (Đối cách: đến Đức Phật), Saraṇaṃ (Đối cách: như là nơi nương tựa), Gacchāmi (Động từ ngôi 1: con xin đi đến).',
          example: 'Dịch sát ngữ pháp: "Con đi đến Đức Phật như một nơi nương tựa an ổn vững bền."'
        },
        {
          step: '2. Các lần lặp lại: Dutiyampi & Tatiyampi',
          explanation: 'Dutiya (thứ hai) + pi (cũng) = Lần thứ nhì con cũng quy y. Tatiya (thứ ba) + pi (cũng) = Lần thứ ba con cũng quy y.',
          example: 'Dutiyampi Buddhaṃ saraṇaṃ gacchāmi.'
        }
      ]
    },
    vocabulary: [
      {
        term: 'Buddha',
        ipa: '/bud.dʰɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Bậc Giác Ngộ hoàn toàn, Bậc Tỉnh Thức tự mình thấu suốt Tứ Thánh Đế',
        root: '√budh (tỉnh thức, giác ngộ)'
      },
      {
        term: 'Dhamma',
        ipa: '/dʰɐm.mɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Chân lý thực tại, Giáo pháp nâng đỡ người thực hành không rơi vào bốn khổ cảnh',
        root: '√dhar (trì giữ, nâng đỡ)'
      },
      {
        term: 'Saṅgha',
        ipa: '/sɐŋ.ɡʰɐ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Đoàn thể Tăng chúng hòa hợp thanh tịnh gồm 4 đôi 8 bậc Thánh đệ tử',
        root: 'saṃ- + √han (hội tụ, hòa hợp)'
      },
      {
        term: 'Saraṇaṃ',
        ipa: '/sɐ.ɾɐ.nɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Nơi nương tựa vững chãi, chốn trú ẩn an toàn dập tắt phiền não',
        root: '√sri (nương tựa)'
      }
    ],
    grammarSections: [
      {
        title: 'Cấu Trúc Ngữ Pháp Câu Tụng Tam Quy',
        explanation: 'Mỗi câu quy y gồm 2 tân ngữ ở Đối cách (Dutiyā Vibhatti) và 1 động từ chia ngôi 1 số ít (gacchāmi):',
        table: {
          headers: ['Câu Tụng Pāḷi', 'Tân Ngữ 1', 'Tân Ngữ 2', 'Động Từ', 'Ý Nghĩa Dịch Trọn Vẹn'],
          rows: [
            ['Buddhaṃ saraṇaṃ gacchāmi', 'Buddhaṃ (Đức Phật)', 'Saraṇaṃ (nơi nương tựa)', 'Gacchāmi (Con xin đi đến)', 'Con đem hết lòng thành kính nương tựa Đức Phật.'],
            ['Dhammaṃ saraṇaṃ gacchāmi', 'Dhammaṃ (Chánh Pháp)', 'Saraṇaṃ (nơi nương tựa)', 'Gacchāmi (Con xin đi đến)', 'Con đem hết lòng thành kính nương tựa Chánh Pháp.'],
            ['Saṅghaṃ saraṇaṃ gacchāmi', 'Saṅghaṃ (Chư Tăng)', 'Saraṇaṃ (nơi nương tựa)', 'Gacchāmi (Con xin đi đến)', 'Con đem hết lòng thành kính nương tựa Tăng Chúng.']
          ]
        }
      }
    ],
    verseAnalysis: {
      originalPali: 'Buddhaṃ saraṇaṃ gacchāmi.\nDhammaṃ saraṇaṃ gacchāmi.\nSaṅghaṃ saraṇaṃ gacchāmi.',
      vietnamese: 'Con đem hết lòng thành kính nương tựa Đức Phật.\nCon đem hết lòng thành kính nương tựa Chánh Pháp.\nCon đem hết lòng thành kính nương tựa Tăng Chúng.',
      english: 'I go to the Buddha for refuge. I go to the Dhamma for refuge. I go to the Sangha for refuge.',
      context: 'Lời tuyên ngôn Tam Quy Y nền tảng của mọi người con Phật trên con đường giải thoát giác ngộ.',
      breakdown: [
        { word: 'Buddhaṃ', grammar: 'Danh từ nam tánh, Dutiyā số ít', rootOrStem: 'buddha', meaning: 'Đến Đức Phật' },
        { word: 'Dhammaṃ', grammar: 'Danh từ nam tánh, Dutiyā số ít', rootOrStem: 'dhamma', meaning: 'Đến Chánh Pháp' },
        { word: 'Saṅghaṃ', grammar: 'Danh từ nam tánh, Dutiyā số ít', rootOrStem: 'saṅgha', meaning: 'Đến Tăng chúng' },
        { word: 'saraṇaṃ', grammar: 'Danh từ trung tánh, Dutiyā số ít', rootOrStem: 'saraṇa', meaning: 'Như nơi nương tựa an ổn' },
        { word: 'gacchāmi', grammar: 'Động từ thì hiện tại, ngôi 1 số ít', rootOrStem: '√gam + mi', meaning: 'Con xin đi đến, con nương tựa' }
      ]
    },
    practiceExercises: [
      {
        instruction: 'Hoàn thành câu tụng quy y lần thứ hai với Pháp Bảo: "Dutiyampi ..."',
        paliText: 'Dutiyampi ... saraṇaṃ gacchāmi.',
        hint: 'Dùng danh từ Dhamma ở Đối cách (Dutiyā Vibhatti).',
        solution: 'Dhammaṃ -> Câu hoàn chỉnh: "Dutiyampi Dhammaṃ saraṇaṃ gacchāmi."',
        breakdown: 'Dutiyampi (Lần thứ hai cũng) + Dhammaṃ (Đến Chánh Pháp) + saraṇaṃ (Nơi nương tựa) + gacchāmi (Con đi đến)'
      },
      {
        instruction: 'Phân tích các thành phần cú pháp trong câu: "Saṅghaṃ saraṇaṃ gacchāmi"',
        paliText: 'Saṅghaṃ saraṇaṃ gacchāmi',
        hint: 'Saṅghaṃ là tân ngữ trực tiếp, saraṇaṃ là tân ngữ chỉ mục đích nương tựa.',
        solution: 'Saṅghaṃ (Đối cách tân ngữ) + saraṇaṃ (Đối cách chỉ thể thức nương tựa) + gacchāmi (Động từ chia ngôi 1 số ít thì hiện tại).',
        breakdown: 'Saṅghaṃ (Danh từ đối cách) -> saraṇaṃ (Danh từ đối cách) -> gacchāmi (Động từ ngôi 1)'
      }
    ],
    quiz: [
      {
        id: 'q5-1',
        question: 'Thuật ngữ "Saraṇa" trong Tam Quy Y bắt nguồn từ căn ngữ mang ý nghĩa gì?',
        options: ['Chiến đấu', 'Nơi nương tựa, bảo hộ, che chở an ổn', 'Vật chất thế gian', 'Quyền lực tối thượng'],
        correctIndex: 1,
        explanation: 'Saraṇa có nghĩa là chốn nương náu an lành, chở che tâm thức khỏi nỗi sợ hãi sinh tử vô minh.'
      },
      {
        id: 'q5-2',
        question: 'Từ "Dutiyampi" trong câu tụng có cấu tạo ngữ pháp như thế nào?',
        options: ['Dutiya (thứ hai) + tiếp vĩ từ pi (cũng) = Lần thứ nhì con cũng...', 'Động từ quá khứ', 'Danh từ số nhiều', 'Đại từ chỉ định'],
        correctIndex: 0,
        explanation: '"Dutiya" là số thứ tự thứ hai, kết hợp với bất biến từ "-pi" (cũng) biểu thị sự tái khẳng định lời phát nguyện.'
      },
      {
        id: 'q5-3',
        question: 'Cấu trúc cú pháp của câu "Buddhaṃ saraṇaṃ gacchāmi" sử dụng thể thức ngữ pháp nào?',
        options: ['Hai Đối Cách (Double Accusative)', 'Chủ cách đi kèm Tính từ', 'Sở thuộc cách chỉ quyền sở hữu', 'Xuất xứ cách chỉ nơi chốn'],
        correctIndex: 0,
        explanation: 'Câu quy y sử dụng thể Hai Đối Cách (Double Accusative): đi đến đối tượng A (Buddhaṃ) như là mục đích B (Saraṇaṃ).'
      }
    ]
  },

  // ==========================================================================
  // CATEGORY 3: Bài 6: Tứ Thánh Đế & Bát Chánh Đạo
  // ==========================================================================
  {
    id: 'pali-06-tu-thanh-de-bat-chanh-dao',
    slug: 'tu-thanh-de-va-bat-chanh-dao-cattari-ariyasaccani',
    categoryId: 'tu-vung-cot-loi',
    order: 6,
    title: 'Bài 6: Tứ Thánh Đế & Bát Chánh Đạo (Cattāri Ariyasaccāni)',
    paliTitle: 'Chaṭṭho Pāṭho: Cattāri Ariyasaccāni & Ariyo Aṭṭhaṅgiko Maggo',
    description: 'Khảo cứu hệ thống thuật ngữ Pāḷi cốt tủy trong bài kinh đầu tiên Chuyển Pháp Luân: Bốn Sự Thật Cao Thượng và Tám Chi Phần Con Đường Giác Ngộ.',
    level: 'Căn bản',
    estimatedMinutes: 18,
    tags: ['Tứ Diệu Đế', 'Bát Chánh Đạo', 'Ariyasacca', 'Magga', 'Dukkha', 'Nibbana'],
    summaryPoints: [
      'Cattāri Ariyasaccāni: 1. Dukkha Sacca (Khổ đế), 2. Samudaya Sacca (Tập đế), 3. Nirodha Sacca (Diệt đế), 4. Magga Sacca (Đạo đế).',
      'Bát Chánh Đạo được phân loại thành Tam Học: Tuệ Học (Paññā), Giới Học (Sīla), và Định Học (Samādhi).',
      'Tiếp đầu ngữ "Sammā-" mang ý nghĩa: Chân chánh, toàn vẹn, hoàn hảo theo chiều hướng đưa đến giải thoát phiền não.'
    ],
    beginnerGuide: {
      title: 'Bản Đồ Giáo Lý Tứ Đế Dưới Lăng Kính Ngôn Ngữ Pāḷi',
      introduction: 'Tứ Thánh Đế chính là trái tim của Phật Giáo. Đức Phật ví mình như vị đại lương y: chỉ rõ căn bệnh (Khổ), tìm ra nguyên nhân gây bệnh (Tập), xác nhận trạng thái hết bệnh (Diệt), và kê đơn thuốc điều trị dứt điểm (Đạo).',
      coreConcept: 'Nắm vững các thuật ngữ Pāḷi gốc giúp bạn không bị lạc lối giữa các bản dịch khác nhau và hiểu đúng ý Phật dạy trong Kinh Điển.',
      stepByStep: [
        {
          step: '1. Dukkha Sacca (Khổ Đế)',
          explanation: 'Dukkha gồm "du" (khó khăn, bất toàn) + "kha" (khoảng trống, rỗng không). Ý chỉ bản chất vô thường, biến hoại không đem lại sự thỏa mãn vĩnh cửu.',
          example: 'Jātipi dukkhā, jarāpi dukkhā, maraṇampi dukkhaṃ (Sanh là khổ, già là khổ, chết là khổ).'
        },
        {
          step: '2. Samudaya Sacca (Tập Đế)',
          explanation: 'Sam-u-daya (nguồn gốc sinh khởi): Chính là Taṇhā (Khát ái) gồm Dục ái, Hữu ái và Phi hữu ái.',
          example: 'Yāyaṃ taṇhā ponobhavikā (Chính lòng khát ái dẫn đến tái sinh).'
        },
        {
          step: '3. Nirodha Sacca (Diệt Đế)',
          explanation: 'Ni-rodha (sự dập tắt không còn sót): Trạng thái vắng bóng hoàn toàn tham sân si, chứng ngộ Nibbāna.',
          example: 'Taṇhāya asesavirāganirodho (Sự đoạn diệt ly tham hoàn toàn không còn dư tàn của khát ái).'
        },
        {
          step: '4. Magga Sacca (Đạo Đế)',
          explanation: 'Ariyo Aṭṭhaṅgiko Maggo: Con đường Thánh gồm 8 chi phần chân chánh.',
          example: 'Sammādiṭṭhi ... Sammāsamādhi.'
        }
      ]
    },
    vocabulary: [
      {
        term: 'Ariyasaccaṃ',
        ipa: '/ɐ.ɾi.jɐ.sɐt.t͡ɕɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Chân lý cao thượng của bậc Thánh (Thánh Đế)',
        note: 'ariya (thánh thiện, cao thượng) + sacca (chân lý sự thật)'
      },
      {
        term: 'Taṇhā',
        ipa: '/tɐɲ.ɦaː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nữ tánh (Itthiliṅga)',
        vietnamese: 'Khát ái, sự bám víu khao khát (Cội nguồn của Khổ)',
        note: 'Gồm Kāmataṇhā (dục ái), Bhavataṇhā (hữu ái), Vibhavataṇhā (phi hữu ái).'
      },
      {
        term: 'Nirodho',
        ipa: '/ni.ɾoː.dʰoː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Sự tịch diệt, dập tắt hoàn toàn khổ đau (Niết-bàn)',
        root: 'ni- + √rudh (ngăn chặn, dập tắt)'
      },
      {
        term: 'Sammādiṭṭhi',
        ipa: '/sɐm.maː.dit.tʰi/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nữ tánh (Itthiliṅga)',
        vietnamese: 'Chánh Kiến — Thấy biết như thực về Tứ Thánh Đế và Nghiệp quả',
        note: 'Chi phần đứng đầu mở đường cho Bát Chánh Đạo.'
      }
    ],
    grammarSections: [
      {
        title: 'Bảng 8 Chi Phần Bát Chánh Đạo Phân Theo Tam Học (Tisikkhā)',
        explanation: 'Tam học Giới - Định - Tuệ bao trùm 8 chi phần con đường Thánh:',
        table: {
          headers: ['Nhóm Tam Học', 'Chi Phần Pāḷi', 'Hán Việt', 'Ý Nghĩa Giáo Lý'],
          rows: [
            ['Tuệ Học (Paññā)', '1. Sammādiṭṭhi', 'Chánh Kiến', 'Hiểu biết đúng đắn về Tứ Đế, Nghiệp báo, Vô thường, Vô ngã'],
            ['Tuệ Học (Paññā)', '2. Sammāsaṅkappo', 'Chánh Tư Duy', 'Tư duy ly dục (Nekkhamma), vô sân (Abyāpāda), bất hại (Avihiṃsā)'],
            ['Giới Học (Sīla)', '3. Sammāvācā', 'Chánh Ngữ', 'Tránh nói dối, nói lời đâm thọc, nói lời thô ác, nói lời phù phiếm'],
            ['Giới Học (Sīla)', '4. Sammākammanto', 'Chánh Nghiệp', 'Tránh sát sinh, tránh trộm cắp, tránh tà hạnh trong các dục'],
            ['Giới Học (Sīla)', '5. Sammā-ājīvo', 'Chánh Mạng', 'Nuôi mạng bằng nghề nghiệp chân chính, trong sạch'],
            ['Định Học (Samādhi)', '6. Sammāvāyāmo', 'Chánh Tinh Tấn', 'Tứ Chánh Cần: Ngăn ác, diệt ác, sinh thiện, tăng trưởng thiện'],
            ['Định Học (Samādhi)', '7. Sammāsati', 'Chánh Niệm', 'Tứ Niệm Xứ: Quán Thân, Thọ, Tâm, Pháp'],
            ['Định Học (Samādhi)', '8. Sammāsamādhi', 'Chánh Định', 'Bốn tầng thiền Sắc giới (Sơ thiền đến Tứ thiền)']
          ]
        }
      }
    ],
    verseAnalysis: {
      originalPali: 'Idaṃ kho pana, bhikkhave, dukkhaṃ ariyasaccaṃ:\nJātipi dukkhā, jarāpi dukkhā, maraṇampi dukkhaṃ;\nSokaparidevadukkhadomanassupāyāsāpi dukkhā;\nAppiyehi sampayogo dukkho, piyehi vippayogo dukkho,\nYampicchaṃ na labhati tampi dukkhaṃ;\nSaṅkhittena pañcupādānakkhandhā dukkhā.',
      vietnamese: 'Này các Tỳ-kheo, đây là Thánh Đế về Khổ:\nSanh là khổ, già là khổ, bệnh là khổ, chết là khổ;\nSầu, bi, ưu, não là khổ;\nOán táng hội (gặp kẻ không ưa) là khổ, Ái biệt ly (xa người thân yêu) là khổ,\nCầu không được là khổ;\nTóm lại, chấp thủ vào Năm Uẩn chính là khổ.',
      english: 'Now this, monks, is the noble truth of suffering: birth is suffering, aging is suffering, death is suffering; sorrow, lamentation, pain, grief, and despair are suffering; association with the disliked is suffering, separation from the liked is suffering, not getting what one wants is suffering; in short, the five clinging-aggregates are suffering.',
      context: 'Trích đoạn kinh Chuyển Pháp Luân (Dhammacakkappavattana Sutta) — bài pháp đầu tiên Đức Phật chuyển bánh xe Pháp tại Vườn Lộc Uyển.',
      breakdown: [
        { word: 'idaṃ', grammar: 'Đại từ chỉ định trung tánh, Paṭhamā số ít', rootOrStem: 'ima', meaning: 'Đây chính là' },
        { word: 'bhikkhave', grammar: 'Danh từ hô cách số nhiều (Ālapana)', rootOrStem: 'bhikkhu', meaning: 'Này các Tỳ-kheo' },
        { word: 'dukkhaṃ', grammar: 'Danh từ trung tánh, Paṭhamā số ít', rootOrStem: 'dukkha', meaning: 'Sự khổ não, bất toàn' },
        { word: 'ariyasaccaṃ', grammar: 'Danh từ ghép, Paṭhamā số ít', rootOrStem: 'ariya + sacca', meaning: 'Chân lý cao thượng của bậc Thánh' },
        { word: 'jāti-pi', grammar: 'Danh từ nữ tánh + Bất biến từ pi', rootOrStem: 'jāti + pi', meaning: 'Sự sanh ra cũng là...' },
        { word: 'maraṇam-pi', grammar: 'Danh từ trung tánh + Bất biến từ pi', rootOrStem: 'maraṇa + pi', meaning: 'Sự chết chóc cũng là...' },
        { word: 'pañcupādānakkhandhā', grammar: 'Danh từ ghép số nhiều', rootOrStem: 'pañca + upādāna + khandha', meaning: 'Năm uẩn chấp thủ' }
      ]
    },
    practiceExercises: [
      {
        instruction: 'Xếp các chi phần Bát Chánh Đạo sau vào đúng nhóm Tam Học: Sammāvācā, Sammādiṭṭhi, Sammāsati',
        paliText: 'Sammāvācā, Sammādiṭṭhi, Sammāsati',
        hint: 'Sammāvācā thuộc Giới, Sammādiṭṭhi thuộc Tuệ, Sammāsati thuộc Định.',
        solution: 'Sammāvācā -> Giới Học (Sīla); Sammādiṭṭhi -> Tuệ Học (Paññā); Sammāsati -> Định Học (Samādhi).',
        breakdown: 'Giới (Chánh ngữ) - Tuệ (Chánh kiến) - Định (Chánh niệm)'
      },
      {
        instruction: 'Dịch thuật ngữ "Cattāri Ariyasaccāni" sang tiếng Việt và phân tích cấu tạo từ:',
        paliText: 'Cattāri Ariyasaccāni',
        hint: 'Cattāri = Bốn (số đếm), ariya = cao thượng, sacca = chân lý.',
        solution: 'Bốn Sự Thật Cao Thượng (Tứ Thánh Đế / Tứ Diệu Đế).',
        breakdown: 'Cattāri (Bốn) + Ariya (Thánh/Cao thượng) + Saccāni (Những chân lý/Sự thật)'
      }
    ],
    quiz: [
      {
        id: 'q6-1',
        question: 'Nguyên nhân sinh khởi khổ đau (Tập đế - Samudaya Sacca) được Đức Phật chỉ rõ là gì?',
        options: ['Avijjā (Vô minh)', 'Taṇhā (Khát ái)', 'Dosa (Sân hận)', 'Māna (Ngã mạn)'],
        correctIndex: 1,
        explanation: 'Trong bài kinh Chuyển Pháp Luân, Đức Phật tuyên bố: Taṇhā (Khát ái) chính là cội rễ sinh khởi khổ đau.'
      },
      {
        id: 'q6-2',
        question: 'Hai chi phần cấu thành nhóm Tuệ học (Paññā) trong Bát Chánh Đạo là gì?',
        options: ['Sammāvācā & Sammākammanta', 'Sammādiṭṭhi & Sammāsaṅkappa', 'Sammāsati & Sammāsamādhi', 'Sammāvāyāma & Sammā-ājīva'],
        correctIndex: 1,
        explanation: 'Sammādiṭṭhi (Chánh Kiến) và Sammāsaṅkappa (Chánh Tư Duy) thuộc nhóm Tuệ học.'
      },
      {
        id: 'q6-3',
        question: 'Cụm từ đúc kết bản chất của Khổ Đế trong Kinh Chuyển Pháp Luân là gì?',
        options: ['Saṅkhittena pañcupādānakkhandhā dukkhā (Tóm lại, năm uẩn chấp thủ là khổ)', 'Sabbapāpassa akaraṇaṃ', 'Sabbe sattā bhavantu sukhitattā', 'Buddhaṃ saraṇaṃ gacchāmi'],
        correctIndex: 0,
        explanation: 'Đức Phật đúc kết: Saṅkhittena pañcupādānakkhandhā dukkhā — chính sự chấp thủ bám víu vào năm uẩn (Sắc, Thọ, Tưởng, Hành, Thức) là cội nguồn của mọi nỗi khổ đau.'
      }
    ]
  },

  // ==========================================================================
  // CATEGORY 4: Khảo Sát Kệ Ngôn & Kinh Điển (Gāthā & Sutta Vicaya)
  // ==========================================================================
  {
    id: 'pali-07-kinh-phap-cu-ke-so-1',
    slug: 'kinh-phap-cu-ke-so-1-yamakavagga',
    categoryId: 'phan-tich-ke-ngon',
    order: 7,
    title: 'Bài 7: Khảo Sát Kệ Pháp Cú Số 1 (Dhammapada Yamakavagga)',
    paliTitle: 'Sattamo Pāṭho: Dhammapada Gāthā 1 Vicaya',
    description: 'Phân tích ngữ pháp chi tiết từng từ trong bài kệ mở đầu kinh Pháp Cú: "Manopubbaṅgamā dhammā, manoseṭṭhā manomayā..." và đối chiếu ý nghĩa giáo lý Tâm Dẫn Đầu.',
    level: 'Trung cấp',
    estimatedMinutes: 20,
    tags: ['Dhammapada', 'Pháp Cú', 'Kệ số 1', 'Phân tích ngữ pháp', 'Yamakavagga'],
    summaryPoints: [
      'Kệ số 1 thuộc Phẩm Song Yếu (Yamakavagga), khẳng định tâm ý là chủ thể dẫn đầu mọi hành vi và nghiệp báo.',
      'Từ ghép (Samāsa) Pāḷi xuất hiện dày đặc: Manopubbaṅgamā, Manoseṭṭhā, Manomayā.',
      'Hình ảnh ẩn dụ: Cỗ xe theo sau vết chân con bò kéo xe (cakkaṃva vahato padaṃ).'
    ],
    beginnerGuide: {
      title: 'Học Cách Đọc & Dịch Từng Chữ Kệ Pháp Cú Số 1',
      introduction: 'Kinh Pháp Cú (Dhammapada) là viên ngọc quý của văn học Phật giáo thế giới. Bài kệ số 1 mở đầu bằng tuyên ngôn về sức mạnh tối thượng của Tâm Ý (Mano).',
      coreConcept: 'Nếu tâm ô nhiễm bởi tham sân si, lời nói và hành động sẽ mang lại quả báo khổ đau bám riết không rời, như bánh xe lăn theo chân con vật kéo.',
      stepByStep: [
        {
          step: '1. Phân tích dòng 1: "Manopubbaṅgamā dhammā, manoseṭṭhā manomayā"',
          explanation: 'Dhammā (chủ từ số nhiều: các pháp tâm sở), Manopubbaṅgamā (do ý dẫn đầu), Manoseṭṭhā (do ý làm chủ tối thượng), Manomayā (do ý tạo tác thành).',
          example: 'Ý dẫn đầu các pháp, ý làm chủ, ý tạo.'
        },
        {
          step: '2. Phân tích dòng 2: "Manasā ce paduṭṭhena, bhāsati vā karoti vā"',
          explanation: 'Manasā paduṭṭhena (bằng tâm ý bị ô nhiễm), ce (nếu như), bhāsati vā (hoặc nói năng), karoti vā (hoặc tạo tác hành động).',
          example: 'Nếu với ý ô nhiễm, nói lên hay hành động.'
        },
        {
          step: '3. Phân tích dòng 3: "Tato naṃ dukkhamanveti, cakkaṃva vahato padaṃ"',
          explanation: 'Tato (từ nhân đó), naṃ (kẻ ấy), dukkham (nỗi khổ), anveti (đuổi theo sau), cakkaṃ-va (như bánh xe), vahato padaṃ (theo dấu chân con vật kéo).',
          example: 'Khổ não bước theo sau, như xe chân vật kéo.'
        }
      ]
    },
    vocabulary: [
      {
        term: 'Mano',
        ipa: '/mɐ.noː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Ý, tâm trí, năng lực nhận biết của tâm',
        note: 'Biến cách của danh từ gốc Manas.'
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
        vietnamese: 'Bị ô nhiễm, độc hại, vẩn đục bởi phiền não',
        root: 'pa- + √dus (làm hư hỏng)'
      },
      {
        term: 'Anveti',
        ipa: '/ɐn.ʋeː.ti/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Đi theo sau, bám đuổi theo sát nút',
        root: 'anu- + √i (đi theo sau)'
      }
    ],
    grammarSections: [
      {
        title: 'Phân Tích Cú Pháp Từ Ghép (Samāsa) Trong Kệ 1',
        explanation: 'Bài kệ sử dụng 3 từ ghép tuyệt đẹp liên tiếp để mô tả bản chất của tâm:\n1. Mano-pubba-ṅgamā = Mano (ý) + pubba (trước) + √gam (đi) -> Những pháp có ý đi trước dẫn đường.\n2. Mano-seṭṭhā = Mano (ý) + seṭṭha (tối thượng, bậc nhất) -> Những pháp xem ý là tối thượng làm chủ.\n3. Mano-mayā = Mano (ý) + maya (tạo thành từ) -> Những pháp do tâm ý nhào nặn tạo tác nên.',
        tip: 'Hậu tố "-maya" mang nghĩa "được làm bằng, tạo thành do": như "suvaṇṇa-maya" (làm bằng vàng), "mano-maya" (do ý tạo thành).'
      }
    ],
    verseAnalysis: {
      originalPali: 'Manopubbaṅgamā dhammā, manoseṭṭhā manomayā;\nManasā ce paduṭṭhena, bhāsati vā karoti vā;\nTato naṃ dukkhamanveti, cakkaṃva vahato padaṃ.',
      vietnamese: 'Ý dẫn đầu các pháp, Ý làm chủ, ý tạo;\nNếu với ý ô nhiễm, Nói lên hay hành động,\nKhổ não bước theo sau, Như xe chân vật kéo.',
      english: 'Mind precedes all mental states. Mind is their chief; they are all mind-wrought. If with an impure mind a person speaks or acts, suffering follows him like the wheel that follows the foot of the ox.',
      context: 'Đức Phật thuyết kệ này tại Kỳ Viên Tịnh Xá (Jetavana) nhân sự tích đại trưởng lão Cakkhupāla (Đại đức Mù) chứng quả A-la-hán.',
      breakdown: [
        { word: 'Mano-pubbaṅgamā', grammar: 'Tính từ ghép, Paṭhamā số nhiều', rootOrStem: 'mano + pubba + √gam', meaning: 'Có ý dẫn đầu, do ý đi trước' },
        { word: 'dhammā', grammar: 'Danh từ nam tánh, Paṭhamā số nhiều', rootOrStem: 'dhamma', meaning: 'Các pháp, các trạng thái tâm thức' },
        { word: 'mano-seṭṭhā', grammar: 'Tính từ ghép, Paṭhamā số nhiều', rootOrStem: 'mano + seṭṭha', meaning: 'Có ý là tối thượng, ý làm chủ' },
        { word: 'mano-mayā', grammar: 'Tính từ ghép, Paṭhamā số nhiều', rootOrStem: 'mano + maya', meaning: 'Do ý tạo tác nên' },
        { word: 'manasā', grammar: 'Danh từ trung tánh, Tatiyā số ít', rootOrStem: 'manas', meaning: 'Với tâm ý, bằng tâm ý' },
        { word: 'ce', grammar: 'Bất biến từ liên từ (Nipāta)', rootOrStem: 'ce', meaning: 'Nếu, giả sử như' },
        { word: 'paduṭṭhena', grammar: 'Quá khứ phân từ, Tatiyā số ít', rootOrStem: 'pa- + √dus', meaning: 'Bị ô nhiễm, bất thiện' },
        { word: 'bhāsati', grammar: 'Động từ thì hiện tại, ngôi 3 số ít', rootOrStem: '√bhās (nói)', meaning: 'Nói năng, phát ngôn' },
        { word: 'vā', grammar: 'Bất biến từ liên từ', rootOrStem: 'vā', meaning: 'Hay là, hoặc là' },
        { word: 'karoti', grammar: 'Động từ thì hiện tại, ngôi 3 số ít', rootOrStem: '√kar (làm)', meaning: 'Hành động, tạo tác' },
        { word: 'tato', grammar: 'Phó từ chỉ nguyên nhân', rootOrStem: 'ta- + to', meaning: 'Do nhân ấy, từ đó' },
        { word: 'naṃ', grammar: 'Đại từ chỉ người, Dutiyā số ít', rootOrStem: 'ta', meaning: 'Người ấy, kẻ ấy' },
        { word: 'dukkhaṃ', grammar: 'Danh từ trung tánh, Paṭhamā số ít', rootOrStem: 'dukkha', meaning: 'Sự khổ não, nỗi thống khổ' },
        { word: 'anveti', grammar: 'Động từ thì hiện tại, ngôi 3 số ít', rootOrStem: 'anu + √i', meaning: 'Bám theo sau, đuổi theo' },
        { word: 'cakkaṃ-va', grammar: 'Danh từ cakkaṃ + Bất biến từ iva', rootOrStem: 'cakka + iva', meaning: 'Như bánh xe cỗ xe' },
        { word: 'vahato', grammar: 'Hiện tại phân từ, Chaṭṭhī số ít', rootOrStem: '√vah (kéo, chở)', meaning: 'Của con vật đang kéo xe' },
        { word: 'padaṃ', grammar: 'Danh từ trung tánh, Dutiyā số ít', rootOrStem: 'pada', meaning: 'Dấu chân, bước chân' }
      ]
    },
    practiceExercises: [
      {
        instruction: 'Xác định biến cách của từ "manasā" trong câu "Manasā ce paduṭṭhena":',
        paliText: 'Manasā ce paduṭṭhena',
        hint: 'Đuôi -ā của danh từ Manas thuộc Cách 3 (Sở dụng cách - Tatiyā Vibhatti).',
        solution: 'Manasā là Tatiyā Vibhatti (Sở dụng cách / Công cụ cách): "bằng tâm ý, với tâm ý".',
        breakdown: 'Manas (gốc) + -ā (đuôi Tatiyā) -> Manasā (bằng tâm ý)'
      },
      {
        instruction: 'Dịch câu đối xứng trong Kệ Pháp Cú số 2: "Manasā ce pasannena bhāsati vā karoti vā":',
        paliText: 'Manasā ce pasannena ...',
        hint: 'Pasannena là bằng tâm ý thanh tịnh trong sáng (trái nghĩa với paduṭṭhena).',
        solution: 'Nếu với tâm ý thanh tịnh trong sạch, nói lên hay hành động.',
        breakdown: 'Manasā (với tâm ý) + ce (nếu) + pasannena (thanh tịnh) + bhāsati (nói) + vā (hoặc) + karoti (làm) + vā (hoặc)'
      }
    ],
    quiz: [
      {
        id: 'q7-1',
        question: 'Trong câu "Manopubbaṅgamā dhammā", từ "dhammā" đóng vai trò ngữ pháp gì?',
        options: ['Chủ từ số nhiều (Paṭhamā bahuvacana)', 'Tân ngữ số ít', 'Sở thuộc cách', 'Động từ'],
        correctIndex: 0,
        explanation: '"dhammā" là danh từ số nhiều ở Cách 1 (Chủ cách): "Các trạng thái tâm thức..."'
      },
      {
        id: 'q7-2',
        question: 'Cụm từ "cakkaṃva vahato padaṃ" sử dụng hình ảnh ẩn dụ gì?',
        options: ['Như bóng theo hình', 'Như bánh xe lăn theo dấu chân con vật kéo cỗ xe', 'Như dòng nước trôi', 'Như ngọn lửa cháy'],
        correctIndex: 1,
        explanation: 'Ẩn dụ bánh xe bò lăn theo dấu chân con vật kéo biểu thị nghiệp xấu ác bám riết theo người tạo nghiệp.'
      },
      {
        id: 'q7-3',
        question: 'Hậu tố "-mayā" trong thuật ngữ "Manomayā" biểu đạt ý nghĩa cấu tạo từ gì?',
        options: ['Được làm bằng, tạo thành bởi (do tâm ý nhào nặn tạo tác)', 'Đến từ phương xa', 'Thuộc về thời quá khứ', 'Chỉ sự phủ định'],
        correctIndex: 0,
        explanation: 'Hậu tố "-maya" mang ý nghĩa "được tạo tác bởi / làm bằng": Manomayā = do tâm ý tạo thành.'
      }
    ]
  },

  // ==========================================================================
  // CATEGORY 4: Bài 8: Khảo Sát Kệ Pháp Cú Số 183
  // ==========================================================================
  {
    id: 'pali-08-kinh-phap-cu-ke-so-183',
    slug: 'kinh-phap-cu-ke-so-183-buddhavagga',
    categoryId: 'phan-tich-ke-ngon',
    order: 8,
    title: 'Bài 8: Khảo Sát Kệ Pháp Cú Số 183 — Tôn Chỉ Chư Phật',
    paliTitle: 'Aṭṭhamo Pāṭho: Dhammapada Gāthā 183 (Sabbapāpassa Akaraṇaṃ)',
    description: 'Phân tích bài kệ đúc kết toàn bộ tôn chỉ giáo pháp của muôn đời chư Phật: Tránh mọi điều ác, thành tựu hạnh lành, thanh lọc tâm ý.',
    level: 'Trung cấp',
    estimatedMinutes: 16,
    tags: ['Pháp Cú 183', 'Buddhasāsana', 'Lời Phật Dạy', 'Kusala', 'Akusala'],
    summaryPoints: [
      'Gāthā 183 thuộc Phẩm Phật Đà (Buddhavagga), là bài kệ tụng Ovāda Pātimokkha nổi tiếng.',
      'Sử dụng danh động từ tận cùng "-anaṃ" (Akaraṇaṃ, Sacittapariyodapanaṃ).',
      'Đúc kết tiến trình tu tập hoàn chỉnh: Giới (không làm ác) -> Định & Hạnh lành (làm điều thiện) -> Tuệ (thanh lọc tâm ý).'
    ],
    beginnerGuide: {
      title: 'Tôn Chỉ Vượt Thời Gian Của Mọi Đức Phật Trong Quá Khứ & Tương Lai',
      introduction: 'Nếu có ai hỏi: "Đạo Phật dạy điều gì tóm tắt nhất?", câu trả lời chính là bài kệ Pháp Cú 183 này. Đây là bài kệ mà chư Phật trong ba đời quá khứ, hiện tại, vị lai đều tuyên thuyết.',
      coreConcept: 'Ba giai đoạn thực hành: Không làm điều ác (Giới) -> Làm các việc lành (Định & Phước) -> Giữ tâm trong sạch (Tuệ Minh Sát).',
      stepByStep: [
        {
          step: '1. "Sabbapāpassa akaraṇaṃ"',
          explanation: 'Sabba-pāpassa (của tất cả điều ác) + a-karaṇaṃ (sự không làm, kiêng tránh).',
          example: 'Không làm mọi điều ác.'
        },
        {
          step: '2. "Kusalassa upasampadā"',
          explanation: 'Kusalassa (của điều thiện) + upasampadā (sự vun bồi, thành tựu trọn vẹn).',
          example: 'Thành tựu các hạnh lành.'
        },
        {
          step: '3. "Sacittapariyodapanaṃ"',
          explanation: 'Sa-citta (tâm của chính mình) + pariyodapanaṃ (sự tẩy rửa, thanh lọc cho trong sạch hoàn toàn).',
          example: 'Giữ tâm ý trong sạch.'
        },
        {
          step: '4. "Etaṃ buddhāna sāsanaṃ"',
          explanation: 'Etaṃ (điều này) + buddhānaṃ (của chư Phật) + sāsanaṃ (lời giáo huấn, tôn chỉ).',
          example: 'Chính lời chư Phật dạy.'
        }
      ]
    },
    vocabulary: [
      {
        term: 'Sabbapāpaṃ',
        ipa: '/sɐb.bɐ.paː.pɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Mọi điều ác, tất cả hành vi bất thiện gây tổn hại',
        note: 'sabba (tất cả) + pāpa (điều ác)'
      },
      {
        term: 'Kusalaṃ',
        ipa: '/ku.sɐ.lɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Điều thiện, phước báu, thiện xảo diệt trừ phiền não',
        root: '√kus (cắt đứt cỏ rác phiền não)'
      },
      {
        term: 'Sacittaṃ',
        ipa: '/sɐ.t͡ɕit.tɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Tâm của chính mình (Tự tâm)',
        note: 'sa (của mình) + citta (tâm)'
      },
      {
        term: 'Sāsanaṃ',
        ipa: '/saː.sɐ.nɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Lời giáo huấn, Chánh Pháp truyền thừa, Tôn chỉ đạo pháp',
        root: '√sās (dạy bảo, răn dạy)'
      }
    ],
    grammarSections: [
      {
        title: 'Cấu Trúc Danh Động Từ Đuôi "-ana" Trong Kệ 183',
        explanation: 'Trong tiếng Pāḷi, khi muốn biến động từ thành danh từ chỉ hành động (Gerund / Verbal Noun), người ta thêm tiếp vĩ ngữ "-ana" (hoặc "-anaṃ"):\n- √kar (làm) -> Karaṇaṃ (hành động làm) -> Thêm tiền tố phủ định "a-" -> Akaraṇaṃ (sự không làm).\n- Pari-ava-√dai (rửa sạch) -> Pariyodapanaṃ (sự thanh lọc gạn đục khơi trong).',
        tip: 'Khi đi với danh động từ, đối tượng chịu tác động thường ở Sở thuộc cách Chaṭṭhī (-assa): "Sabba-pāpassa akaraṇaṃ" = Sự không làm (đối với) tất cả điều ác.'
      }
    ],
    verseAnalysis: {
      originalPali: 'Sabbapāpassa akaraṇaṃ,\nKusalassa upasampadā;\nSacittapariyodapanaṃ,\nEtaṃ buddhāna sāsanaṃ.',
      vietnamese: 'Không làm mọi điều ác,\nThành tựu các hạnh lành,\nGiữ tâm ý trong sạch,\nChính lời chư Phật dạy.',
      english: 'To avoid all evil, to cultivate good, and to cleanse one\'s mind — this is the teaching of the Buddhas.',
      context: 'Bài kệ này là bản tóm tắt Giới Bổn Ovāda Pātimokkha mà Đức Phật tuyên thuyết trước 1.250 vị Thánh Tăng A-la-hán vào ngày rằm tháng Magha.',
      breakdown: [
        { word: 'Sabba-pāpassa', grammar: 'Danh từ ghép, Chaṭṭhī số ít', rootOrStem: 'sabba + pāpa', meaning: 'Của tất cả điều ác, bất thiện' },
        { word: 'a-karaṇaṃ', grammar: 'Danh động từ, Paṭhamā số ít', rootOrStem: 'a- + √kar + ana', meaning: 'Sự không làm, kiêng tránh' },
        { word: 'kusalassa', grammar: 'Danh từ trung tánh, Chaṭṭhī số ít', rootOrStem: 'kusala', meaning: 'Của điều thiện, sự trong lành' },
        { word: 'upasampadā', grammar: 'Danh từ nữ tánh, Paṭhamā số ít', rootOrStem: 'upa- + saṃ- + √pad', meaning: 'Sự thành tựu, vun bồi trọn vẹn' },
        { word: 'sa-citta-pariyodapanaṃ', grammar: 'Danh từ ghép danh động từ, Paṭhamā số ít', rootOrStem: 'sa + citta + √dai', meaning: 'Sự thanh lọc cho tự tâm thuần khiết' },
        { word: 'etaṃ', grammar: 'Đại từ chỉ định, Paṭhamā số ít', rootOrStem: 'eta', meaning: 'Điều này, đây chính là' },
        { word: 'buddhānaṃ', grammar: 'Danh từ nam tánh, Chaṭṭhī số nhiều (rút gọn buddhāna)', rootOrStem: 'buddha', meaning: 'Của chư Phật' },
        { word: 'sāsanaṃ', grammar: 'Danh từ trung tánh, Paṭhamā số ít', rootOrStem: '√sās + ana', meaning: 'Lời răn dạy, giáo huấn tối thượng' }
      ]
    },
    practiceExercises: [
      {
        instruction: 'Phân tích cấu trúc phủ định trong từ "akaraṇaṃ":',
        paliText: 'a - karaṇaṃ',
        hint: 'Tiếp đầu ngữ "a-" đặt trước phụ âm mang ý nghĩa phủ định (không).',
        solution: 'a- (tiếp đầu ngữ phủ định = không) + karaṇaṃ (danh động từ = sự làm, hành động) -> "Sự không làm, kiêng tránh".',
        breakdown: 'a- (Không) + karaṇaṃ (Hành động, việc làm) = Sự không tạo tác'
      },
      {
        instruction: 'Tìm các từ ở Sở thuộc cách (Chaṭṭhī Vibhatti) trong bài kệ Pháp Cú 183:',
        paliText: 'Sabbapāpassa akaraṇaṃ, kusalassa upasampadā, sacittapariyodapanaṃ, etaṃ buddhāna sāsanaṃ.',
        hint: 'Chú ý các đuôi -assa và -āna.',
        solution: 'Sabbapāpassa (của tất cả điều ác), kusalassa (của điều thiện), buddhāna (của chư Phật).',
        breakdown: 'Sabbapāpassa (Chaṭṭhī số ít) - kusalassa (Chaṭṭhī số ít) - buddhāna (Chaṭṭhī số nhiều)'
      }
    ],
    quiz: [
      {
        id: 'q8-1',
        question: 'Trong câu "Etaṃ buddhāna sāsanaṃ", từ "buddhāna" ở biến cách nào?',
        options: ['Chủ cách số ít', 'Sở thuộc cách số nhiều (Của chư Phật)', 'Đối cách', 'Xuất xứ cách'],
        correctIndex: 1,
        explanation: '"buddhāna" là thể rút gọn trong văn vần của "buddhānaṃ" (Sở thuộc cách số nhiều: của chư Phật).'
      },
      {
        id: 'q8-2',
        question: 'Thành tố "Sacittapariyodapanaṃ" chỉ giai đoạn tu tập đỉnh cao nào trong Tam Học?',
        options: ['Giới (Sīla)', 'Định & Tuệ (Samādhi & Paññā thanh lọc phiền não tiềm miên)', 'Cúng dường phẩm vật', 'Xây cất bảo tháp'],
        correctIndex: 1,
        explanation: 'Thanh lọc tâm ý là đỉnh cao của Định và Tuệ Minh Sát (Vipassanā) dứt sạch các lậu hoặc tiềm miên.'
      },
      {
        id: 'q8-3',
        question: 'Tiếp vĩ ngữ "-ana" (như trong karaṇa, pariyodapana) có chức năng ngữ pháp gì trong tiếng Pāḷi?',
        options: ['Biến động từ thành danh từ chỉ hành động (Danh động từ - Gerund / Verbal Noun)', 'Chia động từ ở thì quá khứ', 'Tạo ra đại từ xưng hô', 'Biến danh từ thành tính từ'],
        correctIndex: 0,
        explanation: 'Hậu tố "-ana" biến căn động từ thành danh động từ (Verbal Noun) biểu thị quá trình thực hiện hành động.'
      }
    ]
  },

  // ==========================================================================
  // CATEGORY 5: Kinh Tụng & Tác Bạch Thiền Môn (Vandana & Sīla Samādāna)
  // ==========================================================================
  {
    id: 'pali-09-ngu-gioi-pali',
    slug: 'tho-tri-ngu-gioi-pancasila',
    categoryId: 'kinh-tung-thien-mon',
    order: 9,
    title: 'Bài 9: Lời Tuyên Nguyện Thọ Trì Ngũ Giới (Pañcasīla)',
    paliTitle: 'Navamo Pāṭho: Pañcasīla Samādāna',
    description: 'Khảo cứu ngữ nghĩa và cấu trúc 5 giới điều căn bản của người cư sĩ Phật tử tại gia: Không sát sinh, Không trộm cắp, Không tà dâm, Không nói dối, Không uống rượu.',
    level: 'Căn bản',
    estimatedMinutes: 16,
    tags: ['Ngũ Giới', 'Pañcasīla', 'Giới luật', 'Sikkhāpada', 'Samādiyāmi'],
    summaryPoints: [
      'Pañcasīla: Năm điều học gìn giữ thân và khẩu thanh tịnh, là nền móng kiên cố cho thiền định và trí tuệ giải thoát.',
      'Đuôi câu "sikkhāpadaṃ samādiyāmi" lặp lại ở cả 5 giới nghĩa là: "Con xin phát nguyện vâng giữ điều học..."',
      'Cấu trúc ngữ pháp: [Hành vi xấu ở Xuất xứ cách -ā] + [Veramaṇī (kiêng tránh)] + [Sikkhāpadaṃ (điều học)] + [Samādiyāmi (con xin thọ trì)].'
    ],
    beginnerGuide: {
      title: 'Hiểu Đúng Bản Chất Của 5 Giới Phật Giáo',
      introduction: 'Trong Phật giáo Theravāda, giới (Sīla) không phải là những điều răn cấm đoán áp đặt bởi một vị thần linh, mà là những nguyên tắc đạo đức tự nguyện (Sikkhāpada - Điều học rèn luyện) mà hành giả phát nguyện thực hành để bảo vệ an vui cho chính mình và tha nhân.',
      coreConcept: 'Samādiyāmi xuất phát từ căn √dā (tiếp nhận) + tiền tố saṃ- và ā- -> Nghĩa là "Con xin tự nguyện đón nhận và nghiêm cẩn thực hành".',
      stepByStep: [
        {
          step: '1. Giới 1: Pāṇātipātā veramaṇī sikkhāpadaṃ samādiyāmi',
          explanation: 'Pāṇa (sinh mạng) + atipātā (sát hại) + veramaṇī (tránh xa).',
          example: 'Con xin vâng giữ điều học kiêng tránh sát hại chúng sinh.'
        },
        {
          step: '2. Giới 2: Adinnādānā veramaṇī sikkhāpadaṃ samādiyāmi',
          explanation: 'Adinna (vật không cho) + ādānā (lấy đi).',
          example: 'Con xin vâng giữ điều học kiêng tránh lấy của không cho.'
        },
        {
          step: '3. Giới 3: Kāmesu micchācārā veramaṇī sikkhāpadaṃ samādiyāmi',
          explanation: 'Kāmesu (trong các dục) + micchācārā (hành vi tà vạy).',
          example: 'Con xin vâng giữ điều học kiêng tránh tà hạnh trong các dục.'
        },
        {
          step: '4. Giới 4: Musāvādā veramaṇī sikkhāpadaṃ samādiyāmi',
          explanation: 'Musā (nói sai sự thật) + vādā (lời nói).',
          example: 'Con xin vâng giữ điều học kiêng tránh nói lời dối trá.'
        },
        {
          step: '5. Giới 5: Surāmerayamajjapamādaṭṭhānā veramaṇī sikkhāpadaṃ samādiyāmi',
          explanation: 'Surā (rượu nấu) + meraya (rượu men) + majja (chất say) + pamādaṭṭhānā (chỗ phóng dật).',
          example: 'Con xin vâng giữ điều học kiêng tránh dùng rượu và các chất say làm say sưa phóng dật.'
        }
      ]
    },
    vocabulary: [
      {
        term: 'Sīlaṃ',
        ipa: '/siː.lɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Giới hạnh, đạo đức thanh tịnh, thói quen thiện lành',
        root: '√sīl (huân tập thói quen tốt)'
      },
      {
        term: 'Sikkhāpadaṃ',
        ipa: '/sik.kʰaː.pɐ.dɐŋ/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Trung tánh (Napuṃsakaliṅga)',
        vietnamese: 'Điều học, quy tắc rèn luyện đạo đức tự nguyện',
        note: 'sikkhā (sự học tập) + pada (bước, điều)'
      },
      {
        term: 'Veramaṇī',
        ipa: '/ʋeː.ɾɐ.mɐ.niː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nữ tánh (Itthiliṅga)',
        vietnamese: 'Sự kiêng cữ, sự tránh xa, từ bỏ dứt khoát',
        root: 'vi- + √ram (dừng lại, tránh xa)'
      },
      {
        term: 'Samādiyāmi',
        ipa: '/sɐ.maː.di.jaː.mi/',
        partOfSpeech: 'Động từ (Ākhyāta)',
        vietnamese: 'Con xin phát nguyện thọ trì, con nguyện thực hành nghiêm cẩn',
        root: 'saṃ- + ā- + √dā (tiếp nhận thực hành)'
      }
    ],
    grammarSections: [
      {
        title: 'Bảng 5 Giới Điều Pāḷi — Cấu Trúc & Phân Tích Thành Tố',
        explanation: 'Cả 5 giới đều tuân theo công thức cú pháp mẫu mực:',
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
    verseAnalysis: {
      originalPali: '1. Pāṇātipātā veramaṇī sikkhāpadaṃ samādiyāmi.\n2. Adinnādānā veramaṇī sikkhāpadaṃ samādiyāmi.\n3. Kāmesu micchācārā veramaṇī sikkhāpadaṃ samādiyāmi.\n4. Musāvādā veramaṇī sikkhāpadaṃ samādiyāmi.\n5. Surāmerayamajjapamādaṭṭhānā veramaṇī sikkhāpadaṃ samādiyāmi.',
      vietnamese: '1. Con xin vâng giữ điều học kiêng tránh sát hại chúng sinh.\n2. Con xin vâng giữ điều học kiêng tránh trộm cắp lấy của không cho.\n3. Con xin vâng giữ điều học kiêng tránh tà hạnh trong các dục.\n4. Con xin vâng giữ điều học kiêng tránh nói lời dối trá sai sự thật.\n5. Con xin vâng giữ điều học kiêng tránh dùng rượu và các chất say gây phóng dật.',
      english: '1. I undertake the training rule to abstain from taking life.\n2. I undertake the training rule to abstain from taking what is not given.\n3. I undertake the training rule to abstain from sexual misconduct.\n4. I undertake the training rule to abstain from false speech.\n5. I undertake the training rule to abstain from fermented and distilled intoxicants that cause heedlessness.',
      context: 'Năm điều học căn bản (Pañcasīla) mà hàng Phật tử tại gia thọ trì trước Tam Bảo để thanh lọc ba nghiệp Thân - Khẩu - Ý.',
      breakdown: [
        { word: 'pāṇātipātā', grammar: 'Danh từ ghép, Pañcamī (Xuất xứ cách) số ít', rootOrStem: 'pāṇa + atipāta', meaning: 'Khỏi việc sát hại sinh mạng' },
        { word: 'adinnādānā', grammar: 'Danh từ ghép, Pañcamī số ít', rootOrStem: 'adinna + ādāna', meaning: 'Khỏi việc lấy của không cho' },
        { word: 'kāmesu', grammar: 'Danh từ nam tánh, Sattamī (Định vị cách) số nhiều', rootOrStem: 'kāma', meaning: 'Trong các đối tượng dục lạc' },
        { word: 'micchācārā', grammar: 'Danh từ ghép, Pañcamī số ít', rootOrStem: 'micchā + cāra', meaning: 'Khỏi các hành vi tà vạy sai trái' },
        { word: 'musāvādā', grammar: 'Danh từ ghép, Pañcamī số ít', rootOrStem: 'musā + vāda', meaning: 'Khỏi lời nói dối trá' },
        { word: 'surā-meraya-majja-pamādaṭṭhānā', grammar: 'Danh từ ghép phức, Pañcamī số ít', rootOrStem: 'surā + meraya + majja + pamāda + ṭhāna', meaning: 'Khỏi việc dùng rượu men, rượu nấu, chất say làm say sưa phóng dật' },
        { word: 'veramaṇī', grammar: 'Danh từ nữ tánh, Paṭhamā số ít', rootOrStem: 'vi- + √ram', meaning: 'Sự kiêng tránh, từ bỏ dứt khoát' },
        { word: 'sikkhāpadaṃ', grammar: 'Danh từ trung tánh, Dutiyā số ít', rootOrStem: 'sikkhā + pada', meaning: 'Điều học rèn luyện' },
        { word: 'samādiyāmi', grammar: 'Động từ thì hiện tại, ngôi 1 số ít', rootOrStem: 'saṃ- + ā- + √dā', meaning: 'Con xin phát nguyện vâng giữ thọ trì' }
      ]
    },
    practiceExercises: [
      {
        instruction: 'Ghép từ Pāḷi đúng nghĩa cho giới thứ 4: "Musāvādā ... sikkhāpadaṃ samādiyāmi"',
        paliText: 'Musāvādā ... sikkhāpadaṃ samādiyāmi',
        hint: 'Từ mang nghĩa "sự kiêng tránh, từ bỏ" xuất hiện ở mọi giới điều.',
        solution: 'veramaṇī -> Câu hoàn chỉnh: "Musāvādā veramaṇī sikkhāpadaṃ samādiyāmi."',
        breakdown: 'Musāvādā (Khỏi nói dối) + veramaṇī (Sự kiêng tránh) + sikkhāpadaṃ (Điều học) + samādiyāmi (Con xin thọ trì)'
      },
      {
        instruction: 'Giải thích ý nghĩa biến cách Pañcamī (đuôi -ā) trong "Pāṇātipātā":',
        paliText: 'Pāṇātipātā',
        hint: 'Pañcamī biểu thị sự xa rời, rời bỏ một hành vi tiêu cực.',
        solution: 'Đuôi "-ā" là Xuất xứ cách (Pañcamī Vibhatti), diễn tả ý nghĩa "rời khỏi, kiêng tránh khỏi, dứt khoát từ bỏ" hành vi sát hại sinh mạng.',
        breakdown: 'Pāṇātipāta (Sát sinh) + -ā (Xuất xứ cách) -> Pāṇātipātā (Kiêng xa việc sát sinh)'
      }
    ],
    quiz: [
      {
        id: 'q9-1',
        question: 'Từ "Adinnādānā" trong giới thứ hai được ghép từ những thành tố nào?',
        options: ['Adinna (vật không cho) + ādāna (sự lấy đi)', 'Adi (bắt đầu) + dāna (bố thí)', 'A (không) + dāna (cho)', 'Dinna (đã cho) + ādāna (nhận)'],
        correctIndex: 0,
        explanation: 'Adinna (vật không được người chủ cho) + ādāna (sự lấy đi) = trộm cắp, lấy của không cho.'
      },
      {
        id: 'q9-2',
        question: 'Mục đích chính yếu của việc thọ giới thứ 5 (kiêng rượu và chất say) là gì?',
        options: ['Tiết kiệm chi phí', 'Bảo vệ chánh niệm (Sati) và tỉnh giác, ngăn ngừa sự phóng dật mê mờ (Pamāda)', 'Giữ gìn vóc dáng', 'Tránh thức khuya'],
        correctIndex: 1,
        explanation: 'Rượu và chất say làm hoại mất Chánh niệm (Sati) và sự tỉnh giác, dẫn đến buông lung phóng dật (Pamāda).'
      },
      {
        id: 'q9-3',
        question: 'Động từ "Samādiyāmi" trong lời thọ giới mang ý nghĩa gì?',
        options: ['Con xin phát nguyện tự nguyện vâng giữ thọ trì', 'Con xin sám hối lỗi lầm', 'Con xin cầu xin tha thứ', 'Con xin dâng cúng tịnh tài'],
        correctIndex: 0,
        explanation: '"Samādiyāmi" (saṃ- + ā- + √dā) nghĩa là con xin hoan hỷ tự nguyện tiếp nhận và nghiêm túc thực hành điều học.'
      }
    ]
  },

  // ==========================================================================
  // CATEGORY 5: Bài 10: Khảo Sát Kinh Rải Tâm Từ (Karaṇīyametta Sutta)
  // ==========================================================================
  {
    id: 'pali-10-kinh-rai-tam-tu-metta',
    slug: 'kinh-rai-tam-tu-metta-sutta',
    categoryId: 'kinh-tung-thien-mon',
    order: 10,
    title: 'Bài 10: Khảo Sát Kinh Rải Tâm Từ (Karaṇīyametta Sutta)',
    paliTitle: 'Dasamo Pāṭho: Karaṇīyamettasutta Vicaya',
    description: 'Khảo sát các câu kinh rải tâm từ vô lượng Mettā: "Sabbe sattā bhavantu sukhitattā..." (Nguyện cho tất cả chúng sinh đều được an lạc, thái bình).',
    level: 'Trung cấp',
    estimatedMinutes: 18,
    tags: ['Mettā', 'Tâm Từ', 'Karaṇīyametta Sutta', 'Brahmavihāra', 'Từ Bi Hỷ Xả'],
    summaryPoints: [
      'Mettā: Tình thương yêu vô điều kiện, không phân biệt ranh giới thân sơ hay oán thù.',
      'Sabbe sattā: Tất cả chúng sinh trong khắp 31 cõi luân hồi.',
      'Sukhī hotu / Sukhitattā: Được an lạc nơi thân và thanh tịnh nơi tâm.',
      'Động từ thể nguyện ước (Imperative/Benedictive: Hontu, Bhavantu) biểu đạt tâm từ trải rộng vô biên.'
    ],
    beginnerGuide: {
      title: 'Rải Tâm Từ Mettā — Pháp Hành Bảo Hộ Tối Thượng',
      introduction: 'Kinh Rải Tâm Từ (Karaṇīyametta Sutta) là một trong những bài kinh hộ trì (Paritta) thiêng liêng nhất của Phật giáo Theravāda. Khi tâm tràn ngập từ tâm, người hành thiền sẽ ngủ ngon, thức dậy an lành, được chư thiên và nhân loại yêu mến, không bị khí giới hay độc dược làm hại.',
      coreConcept: 'Rải tâm từ là trải rộng sóng năng lượng thiện lành, không oán thù, không bất bình, bao trùm khắp muôn loài không trừ một ai.',
      stepByStep: [
        {
          step: '1. "Sukhino vā khemino hontu"',
          explanation: 'Sukhino (có sự an vui), khemino (được an toàn thái bình), hontu (nguyện hãy là như vậy).',
          example: 'Nguyện chúng sinh an lạc và thái bình.'
        },
        {
          step: '2. "Sabbe sattā bhavantu sukhitattā"',
          explanation: 'Sabbe (tất cả), sattā (chúng sinh hữu tình), bhavantu (nguyện trở nên), sukhitattā (có tâm hồn an lạc hoan hỷ).',
          example: 'Nguyện cho tất cả chúng sinh tâm hồn được an lạc.'
        }
      ]
    },
    vocabulary: [
      {
        term: 'Mettā',
        ipa: '/meːt.taː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nữ tánh (Itthiliṅga)',
        vietnamese: 'Tâm từ, tình thương thuần khiết mong chúng sinh an lạc',
        root: '√mitt (bạn bè, thân thiết)'
      },
      {
        term: 'Sabbe sattā',
        ipa: '/sɐb.beː sɐt.taː/',
        partOfSpeech: 'Danh từ (Nāma)',
        gender: 'Nam tánh (Pulliṅga)',
        vietnamese: 'Tất cả chúng sinh hữu tình (Chủ cách số nhiều)',
        note: 'sabba (tất cả) + satta (chúng sinh)'
      },
      {
        term: 'Sukhitattā',
        ipa: '/su.kʰi.tɐt.taː/',
        partOfSpeech: 'Tính từ (Guṇanāma)',
        vietnamese: 'Có tự thân an lạc, tràn đầy hỷ lạc thanh tịnh',
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
    grammarSections: [
      {
        title: 'Thể Thức Nguyện Ước (Mệnh Lệnh Cách Pañcamī Vibhatti) Trong Kinh Mettā',
        explanation: 'Động từ trong câu rải tâm từ không chia ở thì hiện tại thông thường mà chia ở thể Mệnh Lệnh / Chúc Nguyện Cách (Imperative / Benedictive Mood):\n- Đuôi "-tu" (số ít): "Sukhī hotu" (Nguyện cho người ấy được an lạc).\n- Đuôi "-ntu" (số nhiều): "Sukhino hontu", "Sabbe sattā bhavantu sukhitattā" (Nguyện cho tất cả chúng sinh đều được an lạc).',
        tip: 'Khi tụng "bhavantu", hãy gửi trọn vẹn luồng tâm niệm chúc phúc chân thành đến muôn loài.'
      }
    ],
    verseAnalysis: {
      originalPali: 'Sukhino vā khemino hontu,\nSabbe sattā bhavantu sukhitattā.\nYe keci pāṇabhūtatthi,\nTasa vā thāvarā vā anavasesā.',
      vietnamese: 'Nguyện chúng sinh an lạc và thái bình,\nNguyện cho muôn loài tâm hồn được hoan hỷ.\nTất cả những sinh linh hiện hữu,\nDù yếu đuối hay kiên cường, không trừ một ai.',
      english: 'May all beings be happy and secure, may they be happy-minded. Whatever living beings there are, weak or strong, without exception.',
      context: 'Đức Phật ban bài kinh này cho các vị tỳ-kheo hành thiền trong rừng sâu bị các Chư thiên dạ-xoa quấy nhiễu.',
      breakdown: [
        { word: 'Sukhino', grammar: 'Tính từ nam tánh, Paṭhamā số nhiều', rootOrStem: 'sukhin', meaning: 'Có sự an vui, hạnh phúc' },
        { word: 'vā', grammar: 'Liên từ', rootOrStem: 'vā', meaning: 'Hay là' },
        { word: 'khemino', grammar: 'Tính từ nam tánh, Paṭhamā số nhiều', rootOrStem: 'khemin', meaning: 'Được an toàn, thái bình' },
        { word: 'hontu', grammar: 'Động từ mệnh lệnh cách, ngôi 3 số nhiều', rootOrStem: '√hū (là, trở nên)', meaning: 'Nguyện hãy là, nguyện được là' },
        { word: 'sabbe', grammar: 'Tính từ số nhiều', rootOrStem: 'sabba', meaning: 'Tất cả' },
        { word: 'sattā', grammar: 'Danh từ nam tánh, Paṭhamā số nhiều', rootOrStem: 'satta', meaning: 'Chúng sinh hữu tình' },
        { word: 'bhavantu', grammar: 'Động từ mệnh lệnh cách, ngôi 3 số nhiều', rootOrStem: '√bhū', meaning: 'Nguyện thành tựu, nguyện trở nên' },
        { word: 'sukhitattā', grammar: 'Tính từ ghép số nhiều', rootOrStem: 'sukhita + atta', meaning: 'Có tâm hồn an lạc thanh nhàn' }
      ]
    },
    practiceExercises: [
      {
        instruction: 'Phân tích ngữ pháp của câu chúc nguyện: "Sabbe sattā bhavantu sukhitattā"',
        paliText: 'Sabbe sattā bhavantu sukhitattā',
        hint: 'Sabbe sattā là chủ ngữ số nhiều, bhavantu là động từ mệnh lệnh cách số nhiều, sukhitattā là bổ ngữ tính từ.',
        solution: 'Sabbe (Tính từ số nhiều: Tất cả) + sattā (Danh từ Chủ cách số nhiều: chúng sinh) + bhavantu (Động từ Mệnh lệnh cách ngôi 3 số nhiều: nguyện hãy thành tựu) + sukhitattā (Tính từ số nhiều: có tâm hồn an lạc thái bình).',
        breakdown: 'Sabbe sattā (Chủ từ) -> bhavantu (Động từ chúc nguyện) -> sukhitattā (Trạng thái an lạc)'
      },
      {
        instruction: 'Dịch câu chúc tâm từ dành cho một cá nhân: "Ahaṃ avero homi, abyāpajjo homi, anīgho homi, sukhī attānaṃ pariharāmi":',
        paliText: 'Ahaṃ avero homi ...',
        hint: 'Avero = không hận thù, abyāpajjo = không oan trái, anīgho = không khổ não, sukhī attānaṃ pariharāmi = gìn giữ tự thân an lạc.',
        solution: 'Nguyện cho con không hận thù, không oan trái, không khổ não, giữ gìn thân tâm luôn được an lạc.',
        breakdown: 'Ahaṃ (Con) + avero (Không thù hận) + homi (Nguyện được là) + sukhī (An vui) + pariharāmi (Tự gìn giữ thân tâm)'
      }
    ],
    quiz: [
      {
        id: 'q10-1',
        question: 'Câu kệ "Sabbe sattā bhavantu sukhitattā" mang ý nghĩa cao quý gì?',
        options: ['Nguyện cho riêng tôi thành đạt', 'Nguyện cho tất cả chúng sinh đều được an lạc hoan hỷ', 'Nguyện mưa thuận gió hòa', 'Nguyện tai qua nạn khỏi'],
        correctIndex: 1,
        explanation: '"Sabbe sattā" = tất cả chúng sinh, "bhavantu sukhitattā" = nguyện tâm hồn luôn được an lạc thái bình.'
      },
      {
        id: 'q10-2',
        question: 'Động từ "Bhavantu" ở thể thức (thì/thể) nào trong văn phạm Pāḷi?',
        options: ['Quá khứ', 'Mệnh lệnh / Chúc nguyện cách (Imperative/Benedictive) ngôi thứ 3 số nhiều', 'Tương lai', 'Điều kiện cách'],
        correctIndex: 1,
        explanation: 'Đuôi "-ntu" ở ngôi thứ 3 số nhiều biểu đạt lời nguyện ước, chúc phúc chân thành bao trùm muôn loài.'
      },
      {
        id: 'q10-3',
        question: 'Pháp thực hành Mettā (Rải Tâm Từ) thuộc về nhóm Tứ Vô Lượng Tâm (Brahmavihāra) nào?',
        options: ['Từ (Mettā) - Bi (Karuṇā) - Hỷ (Muditā) - Xả (Upekkhā)', 'Tứ Diệu Đế', 'Tứ Niệm Xứ', 'Tứ Chánh Cần'],
        correctIndex: 0,
        explanation: 'Mettā (Tâm Từ) là chi phần đứng đầu trong Bốn Tâm Cao Thượng (Brahmavihāra - Tứ Vô Lượng Tâm) đưa tâm thức mở rộng bao la không bờ bến.'
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

export function getAdjacentLessons(lessonIdOrSlug: string): { prevLesson: PaliLesson | null; nextLesson: PaliLesson | null } {
  if (!lessonIdOrSlug || typeof lessonIdOrSlug !== 'string') {
    return { prevLesson: null, nextLesson: null };
  }
  const clean = lessonIdOrSlug.trim().toLowerCase();
  const currentIndex = PALI_LESSONS.findIndex(
    (l) => l.id.toLowerCase() === clean || l.slug.toLowerCase() === clean
  );

  if (currentIndex === -1) {
    return { prevLesson: null, nextLesson: null };
  }

  const prevLesson = currentIndex > 0 ? PALI_LESSONS[currentIndex - 1] : null;
  const nextLesson = currentIndex < PALI_LESSONS.length - 1 ? PALI_LESSONS[currentIndex + 1] : null;

  return { prevLesson, nextLesson };
}
