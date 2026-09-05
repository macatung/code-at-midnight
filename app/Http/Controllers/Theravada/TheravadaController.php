<?php

namespace App\Http\Controllers\Theravada;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TheravadaController extends Controller
{
    /**
     * Daily Dhammapada Verses Collection
     */
    protected array $dhammapadaVerses = [
        [
            'verse_number' => 1,
            'pali' => 'Manopubbaṅgamā dhammā, manoseṭṭhā manomayā; Manasā ce paduṭṭhena, bhāsati vā karoti vā; Tato naṃ dukkhamanveti, cakkaṃva vahato padaṃ.',
            'vietnamese' => 'Ý dẫn đầu các pháp, Ý làm chủ, ý tạo; Nếu với ý ô nhiễm, Nói lên hay hành động, Khổ não bước theo sau, Như xe chân vật kéo.',
            'chapter' => 'Phẩm Song Yếu (Yamakavagga)'
        ],
        [
            'verse_number' => 2,
            'pali' => 'Manopubbaṅgamā dhammā, manoseṭṭhā manomayā; Manasā ce pasannena, bhāsati vā karoti vā; Tato naṃ sukhamanveti, chāyāva anapāyinī.',
            'vietnamese' => 'Ý dẫn đầu các pháp, Ý làm chủ, ý tạo; Nếu với ý thanh tịnh, Nói lên hay hành động, An lạc bước theo sau, Như bóng không rời hình.',
            'chapter' => 'Phẩm Song Yếu (Yamakavagga)'
        ],
        [
            'verse_number' => 5,
            'pali' => 'Na hi verena verāni, sammantīdha kudācanaṃ; Averena ca sammanti, esa dhammo sanantano.',
            'vietnamese' => 'Hận thù diệt hận thù, Đời này không thể có; Từ bi diệt hận thù, Là định luật ngàn thu.',
            'chapter' => 'Phẩm Song Yếu (Yamakavagga)'
        ],
        [
            'verse_number' => 21,
            'pali' => 'Appamādo amatapadaṃ, pamādo maccuno padaṃ; Appamattā na mīyanti, ye pamattā yathā matā.',
            'vietnamese' => 'Không phóng dật: đường sống; Phóng dật: đường tử vong; Không phóng dật: không chết; Phóng dật như chết rồi.',
            'chapter' => 'Phẩm Không Phóng Dật (Appamādavagga)'
        ],
        [
            'verse_number' => 183,
            'pali' => 'Sabbapāpassa akaraṇaṃ, kusalassa upasampadā; Sacittapariyodapanaṃ, etaṃ buddhāna sāsanaṃ.',
            'vietnamese' => 'Không làm mọi điều ác, Thành tựu các hạnh lành, Giữ tâm ý trong sạch, Chính lời chư Phật dạy.',
            'chapter' => 'Phẩm Phật Đà (Buddhavagga)'
        ],
    ];

    /**
     * Theravada Home
     */
    public function index(): Response
    {
        $articles = Article::query()
            ->where('site_domain', 'theravada')
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->get();

        // Pick verse of the day based on day of year
        $dayOfYear = (int) date('z');
        $dailyVerse = $this->dhammapadaVerses[$dayOfYear % count($this->dhammapadaVerses)];

        $categories = [
            [
                'slug' => 'phap-hoc',
                'name' => 'Pháp Học (Pariyatti)',
                'pali' => 'Pariyatti Dhamma',
                'description' => 'Khảo cứu Tam Tạng Pāḷi, Tứ Thánh Đế, Bát Chánh Đạo, Thập Nhị Duyên Khởi và giáo lý uyên áo.',
                'icon' => 'BookOpen',
                'count' => $articles->where('category', 'phap-hoc')->count()
            ],
            [
                'slug' => 'phap-hanh',
                'name' => 'Pháp Hành (Paṭipatti)',
                'pali' => 'Paṭipatti Dhamma',
                'description' => 'Thực hành Thiền Tứ Niệm Xứ (Satipaṭṭhāna), Minh Sát Tuệ Vipassanā và Chánh niệm đời sống.',
                'icon' => 'Activity',
                'count' => $articles->where('category', 'phap-hanh')->count()
            ],
            [
                'slug' => 'kinh-tung',
                'name' => 'Kinh Tụng & Paritta',
                'pali' => 'Sutta & Paritta',
                'description' => 'Các bản kinh hộ trì Pāḷi — Việt thiêng liêng: Kinh Chuyển Pháp Luân, Kinh Từ Bi, Kinh Châu Báu.',
                'icon' => 'Compass',
                'count' => $articles->where('category', 'kinh-tung')->count()
            ],
            [
                'slug' => 'lich-su',
                'name' => 'Lịch Sử Phật Giáo',
                'pali' => 'Sāsana Itihāsa',
                'description' => 'Biên niên sử Đức Phật Gotama, 6 kỳ kết tập Tam Tạng, Đại đế Asoka và hành trình truyền bá Chánh Pháp.',
                'icon' => 'Landmark',
                'count' => $articles->where('category', 'lich-su')->count()
            ],
        ];

        return Inertia::render('Theravada/Index', [
            'articles' => $articles,
            'dailyVerse' => $dailyVerse,
            'categories' => $categories,
            'title' => 'Ma Tọa Thiền — Phật Giáo Nguyên Thủy & Thiền Vipassanā',
        ]);
    }

    /**
     * Show Article / Sutta
     */
    public function show(string $slug): Response
    {
        $article = Article::query()
            ->with('pairedArticle')
            ->where('site_domain', 'theravada')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $pairedArticle = null;
        if ($article->pairedArticle && $article->pairedArticle->is_published) {
            $paired = $article->pairedArticle;
            $pairedArticle = [
                'id' => $paired->id,
                'title' => $paired->title,
                'slug' => $paired->slug,
                'excerpt' => $paired->excerpt,
                'site_domain' => $paired->site_domain,
                'reading_time_min' => $paired->reading_time_min ?? 5,
                'url' => '/blog/' . $paired->slug,
                'main_domain_url' => 'https://' . config('app.base_domain', 'macatung.dev') . '/blog/' . $paired->slug,
            ];
        }

        $related = Article::query()
            ->where('site_domain', 'theravada')
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return Inertia::render('Theravada/Show', [
            'article' => $article,
            'paired_article' => $pairedArticle,
            'related' => $related,
            'title' => "{$article->title} — Ma Tọa Thiền",
        ]);
    }

    /**
     * Category Filter
     */
    public function category(string $category): Response
    {
        $articles = Article::query()
            ->where('site_domain', 'theravada')
            ->where('category', $category)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->get();

        $categoryNames = [
            'phap-hoc' => 'Pháp Học (Pariyatti)',
            'phap-hanh' => 'Pháp Hành (Paṭipatti — Vipassanā)',
            'kinh-tung' => 'Tam Tạng & Kinh Tụng Pāḷi (Sutta)',
            'lich-su' => 'Lịch Sử Phật Giáo (Sāsana Itihāsa)',
        ];

        return Inertia::render('Theravada/Category', [
            'categorySlug' => $category,
            'categoryName' => $categoryNames[$category] ?? ucfirst($category),
            'articles' => $articles,
            'title' => ($categoryNames[$category] ?? ucfirst($category)) . ' — Ma Tọa Thiền',
        ]);
    }

    /**
     * Pali Glossary Page
     */
    public function glossary(): Response
    {
        return Inertia::render('Theravada/Glossary', [
            'title' => 'Từ Điển Thuật Ngữ Phật Học Pāḷi — Ma Tọa Thiền',
        ]);
    }

    /**
     * Pali Learning Module Page
     */
    public function paliLearning(): Response
    {
        return Inertia::render('Theravada/PaliLearning', [
            'title' => 'Học Tiếng Pāḷi — Bảng Chữ Cái, Ngữ Pháp & Kệ Ngôn Tipiṭaka — Ma Tọa Thiền',
        ]);
    }

    /**
     * Dedicated Pali Lesson Detail Show Page
     */
    public function paliLessonShow(string $slug): Response|RedirectResponse
    {
        $validLessons = [
            'nguyen-am-va-phu-am-pali' => [
                'id' => 'pali-01-nguyen-am-phu-am',
                'title' => 'Bài 1: Hệ Thống 41 Mẫu Tự Pāḷi (Sara & Vyañjana)',
                'pali_title' => 'Paṭhamo Pāṭho: Akkharamālā (Sara ca Vyañjana)',
                'category_id' => 'bang-chu-cai-phat-am',
                'order' => 1,
            ],
            'quy-tac-phat-am-chuan-va-trong-am' => [
                'id' => 'pali-02-quy-tac-phat-am',
                'title' => 'Bài 2: Quy Tắc Trọng Âm & Đọc Tụng Chuẩn Pāḷi',
                'pali_title' => 'Dutiyo Pāṭho: Uccāraṇavidhi & Garulahu',
                'category_id' => 'bang-chu-cai-phat-am',
                'order' => 2,
            ],
            'danh-tu-va-8-bien-cach-vibhatti' => [
                'id' => 'pali-03-danh-tu-8-bien-cach',
                'title' => 'Bài 3: Danh Từ & 8 Biến Cách Pāḷi (Aṭṭha Vibhatti)',
                'pali_title' => 'Tatiyo Pāṭho: Nāmapada & Aṭṭhavibhatti',
                'category_id' => 'ngu-phap-can-ban',
                'order' => 3,
            ],
            'dong-tu-va-thoi-hien-tai-akhyata' => [
                'id' => 'pali-04-dong-tu-thoi-hien-tai',
                'title' => 'Bài 4: Động Từ Thì Hiện Tại (Vattamānā Ākhyāta)',
                'pali_title' => 'Catuttho Pāṭho: Ākhyātapada & Vattamānā Kāla',
                'category_id' => 'ngu-phap-can-ban',
                'order' => 4,
            ],
            'tam-bao-va-tam-quy-y-tisarana' => [
                'id' => 'pali-05-tam-bao-tam-quy-y',
                'title' => 'Bài 5: Tam Bảo & Lời Tuyên Ngôn Tam Quy Y (Ti-saraṇa)',
                'pali_title' => 'Pañcamo Pāṭho: Ratanattaya ca Tisaraṇagamana',
                'category_id' => 'tu-vung-cot-loi',
                'order' => 5,
            ],
            'tu-thanh-de-va-bat-chanh-dao-cattari-ariyasaccani' => [
                'id' => 'pali-06-tu-thanh-de-bat-chanh-dao',
                'title' => 'Bài 6: Tứ Thánh Đế & Bát Chánh Đạo (Cattāri Ariyasaccāni)',
                'pali_title' => 'Chaṭṭho Pāṭho: Cattāri Ariyasaccāni & Ariyo Aṭṭhaṅgiko Maggo',
                'category_id' => 'tu-vung-cot-loi',
                'order' => 6,
            ],
            'kinh-phap-cu-ke-so-1-yamakavagga' => [
                'id' => 'pali-07-kinh-phap-cu-ke-so-1',
                'title' => 'Bài 7: Khảo Sát Kệ Pháp Cú Số 1 (Dhammapada Yamakavagga)',
                'pali_title' => 'Sattamo Pāṭho: Dhammapada Gāthā 1 Vicaya',
                'category_id' => 'phan-tich-ke-ngon',
                'order' => 7,
            ],
            'kinh-phap-cu-ke-so-183-buddhavagga' => [
                'id' => 'pali-08-kinh-phap-cu-ke-so-183',
                'title' => 'Bài 8: Khảo Sát Kệ Pháp Cú Số 183 — Tôn Chỉ Chư Phật',
                'pali_title' => 'Aṭṭhamo Pāṭho: Dhammapada Gāthā 183 (Sabbapāpassa Akaraṇaṃ)',
                'category_id' => 'phan-tich-ke-ngon',
                'order' => 8,
            ],
            'tho-tri-ngu-gioi-pancasila' => [
                'id' => 'pali-09-ngu-gioi-pali',
                'title' => 'Bài 9: Lời Tuyên Nguyện Thọ Trì Ngũ Giới (Pañcasīla)',
                'pali_title' => 'Navamo Pāṭho: Pañcasīla Samādāna',
                'category_id' => 'kinh-tung-thien-mon',
                'order' => 9,
            ],
            'kinh-rai-tam-tu-metta-sutta' => [
                'id' => 'pali-10-kinh-rai-tam-tu-metta',
                'title' => 'Bài 10: Khảo Sát Kinh Rải Tâm Từ (Karaṇīyametta Sutta)',
                'pali_title' => 'Dasamo Pāṭho: Karaṇīyamettasutta Vicaya',
                'category_id' => 'kinh-tung-thien-mon',
                'order' => 10,
            ],
        ];

        $cleanSlug = trim(strtolower($slug));
        if (!isset($validLessons[$cleanSlug])) {
            // Check if slug is a lesson ID alias (e.g. pali-01-nguyen-am-phu-am)
            foreach ($validLessons as $s => $l) {
                if (strtolower($l['id']) === $cleanSlug) {
                    $prefix = request()->is('theravada/*') ? '/theravada' : '';
                    return redirect($prefix . '/hoc-pali/' . $s, 301);
                }
            }
            abort(404, 'Không tìm thấy bài học Pāḷi tương ứng.');
        }

        $currentLesson = $validLessons[$cleanSlug];
        $orderedSlugs = array_keys($validLessons);
        $currentIndex = array_search($cleanSlug, $orderedSlugs);

        $prevSlug = $currentIndex > 0 ? $orderedSlugs[$currentIndex - 1] : null;
        $nextSlug = $currentIndex < count($orderedSlugs) - 1 ? $orderedSlugs[$currentIndex + 1] : null;

        $prevLesson = $prevSlug ? array_merge($validLessons[$prevSlug], ['slug' => $prevSlug]) : null;
        $nextLesson = $nextSlug ? array_merge($validLessons[$nextSlug], ['slug' => $nextSlug]) : null;

        // Related lessons in the same category
        $relatedLessons = [];
        foreach ($validLessons as $s => $l) {
            if ($s !== $cleanSlug && $l['category_id'] === $currentLesson['category_id']) {
                $relatedLessons[] = array_merge($l, ['slug' => $s]);
            }
        }

        return Inertia::render('Theravada/PaliLessonShow', [
            'slug' => $cleanSlug,
            'lessonMeta' => array_merge($currentLesson, ['slug' => $cleanSlug]),
            'prevLesson' => $prevLesson,
            'nextLesson' => $nextLesson,
            'relatedLessons' => $relatedLessons,
            'title' => "{$currentLesson['title']} — Học Tiếng Pāḷi — Ma Tọa Thiền",
        ]);
    }

    /**
     * Buddhist Interactive Apps Hub
     */
    public function apps(): Response
    {
        return Inertia::render('Theravada/Apps', [
            'title' => 'Ứng Dụng Pháp Bảo & Tọa Thiền Chánh Niệm — Ma Tọa Thiền',
        ]);
    }

    /**
     * JSON Feed endpoint for AI Content Agents & RSS aggregators
     */
    public function feedJson()
    {
        $articles = Article::query()
            ->where('site_domain', 'theravada')
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->take(20)
            ->get();

        return response()->json([
            'version' => 'https://jsonfeed.org/version/1.1',
            'title' => 'Ma Tọa Thiền — Phật Giáo Nguyên Thủy Theravāda',
            'home_page_url' => 'https://theravada.macatung.dev',
            'feed_url' => 'https://theravada.macatung.dev/feed.json',
            'description' => 'Tuyển tập kinh điển Pāḷi Tipiṭaka, pháp hành thiền Vipassanā và bài pháp chuyển hóa thân tâm.',
            'items' => $articles->map(function ($article) {
                return [
                    'id' => (string) $article->id,
                    'url' => 'https://theravada.macatung.dev/kinh/' . $article->slug,
                    'title' => $article->title,
                    'pali_title' => $article->pali_title,
                    'content_html' => $article->content,
                    'summary' => $article->excerpt,
                    'date_published' => $article->published_at ? $article->published_at->toIso8601String() : null,
                    'author' => [
                        'name' => $article->author ?: 'Ma Tọa Thiền'
                    ],
                    'tags' => $article->tags,
                    'category' => $article->category,
                ];
            })
        ]);
    }
}
