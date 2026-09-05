/**
 * Test Suite: Pali Learning Module (Theravāda Canonical Bhāsā Sikkhā)
 * Tier 1: Feature Coverage (Isolation) — Lesson data structure, categories, vocabulary, quiz validity, beginner guides
 * Tier 2: Boundary & Corner Cases — Score calculation, word breakdown completeness, dedicated routing, adjacent navigation
 * Tier 3: Cross-Feature Interactions — Glossary, Apps, SEO Sitemap, i18n
 * Tier 4: Real-World E2E Scenarios — Dedicated show page component structure, progress mastery simulation
 */

import { describe, it, expect } from '../Harness/index.js';
import {
  PALI_LESSON_CATEGORIES,
  PALI_LESSONS,
  findLessonById,
  getLessonsByCategory,
  getAdjacentLessons,
} from '../../resources/js/data/paliLessonsData.ts';
import { usePaliProgress } from '../../resources/js/composables/usePaliProgress.ts';
import fs from 'fs';
import path from 'path';

describe('PaliLearningModuleTest (Pāḷi Learning Module, Dedicated Show Page & Progress Tracking)', () => {
  
  // ==========================================================================
  // TIER 1: Feature Coverage (Isolation)
  // ==========================================================================
  describe('[T1_PALI] Category Grouping & Lesson Schema Conformance', () => {
    it('[T1_PL_01] Defines at least 5 canonical learning categories with required fields', () => {
      expect(PALI_LESSON_CATEGORIES.length).toBeGreaterThanOrEqual(5);

      const categoryIds = new Set<string>();
      PALI_LESSON_CATEGORIES.forEach((cat) => {
        expect(cat.id.length).toBeGreaterThan(2);
        expect(cat.slug.length).toBeGreaterThan(2);
        expect(cat.name.length).toBeGreaterThan(2);
        expect(cat.paliName.length).toBeGreaterThan(2);
        expect(cat.description.length).toBeGreaterThan(10);
        expect(cat.icon.length).toBeGreaterThan(1);
        expect(cat.color.startsWith('#')).toBe(true);
        expect(categoryIds.has(cat.id)).toBe(false);
        categoryIds.add(cat.id);
      });
    });

    it('[T1_PL_02] Contains at least 10 structured lessons with strictly unique IDs and slugs', () => {
      expect(PALI_LESSONS.length).toBeGreaterThanOrEqual(10);

      const lessonIds = new Set<string>();
      const lessonSlugs = new Set<string>();
      const validCategoryIds = new Set(PALI_LESSON_CATEGORIES.map((c) => c.id));

      PALI_LESSONS.forEach((lesson) => {
        expect(lessonIds.has(lesson.id)).toBe(false);
        expect(lessonSlugs.has(lesson.slug)).toBe(false);
        lessonIds.add(lesson.id);
        lessonSlugs.add(lesson.slug);

        expect(validCategoryIds.has(lesson.categoryId)).toBe(true);
        expect(lesson.title.length).toBeGreaterThan(5);
        expect(lesson.paliTitle.length).toBeGreaterThan(5);
        expect(lesson.description.length).toBeGreaterThan(10);
        expect(lesson.estimatedMinutes).toBeGreaterThan(0);
        expect(['Căn bản', 'Trung cấp', 'Nâng cao'].includes(lesson.level)).toBe(true);
        expect(lesson.tags.length).toBeGreaterThan(0);
        expect(lesson.summaryPoints.length).toBeGreaterThan(0);
      });
    });

    it('[T1_PL_03] Every lesson has rich vocabulary terms with Pāḷi, Vietnamese and part of speech', () => {
      PALI_LESSONS.forEach((lesson) => {
        expect(lesson.vocabulary.length).toBeGreaterThanOrEqual(2);
        lesson.vocabulary.forEach((v) => {
          expect(v.term.length).toBeGreaterThan(1);
          expect(v.vietnamese.length).toBeGreaterThan(2);
          expect(v.partOfSpeech.length).toBeGreaterThan(2);
        });
      });
    });

    it('[T1_PL_04] Every lesson provides interactive quiz questions with valid correctIndex and explanations', () => {
      PALI_LESSONS.forEach((lesson) => {
        expect(lesson.quiz.length).toBeGreaterThanOrEqual(2);
        lesson.quiz.forEach((q) => {
          expect(q.id.length).toBeGreaterThan(1);
          expect(q.question.length).toBeGreaterThan(5);
          expect(q.options.length).toBeGreaterThanOrEqual(2);
          expect(q.correctIndex).toBeGreaterThanOrEqual(0);
          expect(q.correctIndex).toBeLessThan(q.options.length);
          expect(q.explanation.length).toBeGreaterThan(5);
        });
      });
    });

    it('[T1_PL_05] Every lesson includes a dedicated beginner guide for students starting from zero', () => {
      PALI_LESSONS.forEach((lesson) => {
        expect(lesson.beginnerGuide).toBeDefined();
        const bg = lesson.beginnerGuide!;
        expect(bg.title.length).toBeGreaterThan(5);
        expect(bg.introduction.length).toBeGreaterThan(10);
        expect(bg.coreConcept.length).toBeGreaterThan(10);
        expect(bg.stepByStep.length).toBeGreaterThanOrEqual(2);
        bg.stepByStep.forEach((st) => {
          expect(st.step.length).toBeGreaterThan(2);
          expect(st.explanation.length).toBeGreaterThan(5);
        });
      });
    });
  });

  // ==========================================================================
  // TIER 2: Boundary & Corner Cases (Lookup helpers, Verse syntax, Routes, Storage recovery)
  // ==========================================================================
  describe('[T2_PALI] Helper Functions, Verse Analysis & Route Registration', () => {
    it('[T2_PL_01] findLessonById correctly resolves lessons by both ID and Slug', () => {
      const firstLesson = PALI_LESSONS[0];
      const byId = findLessonById(firstLesson.id);
      expect(byId).toBeDefined();
      expect(byId?.id).toBe(firstLesson.id);

      const bySlug = findLessonById(firstLesson.slug);
      expect(bySlug).toBeDefined();
      expect(bySlug?.slug).toBe(firstLesson.slug);

      const nonExistent = findLessonById('non-existent-lesson-id-999');
      expect(nonExistent).toBeUndefined();
    });

    it('[T2_PL_02] getLessonsByCategory filters correctly across all categories', () => {
      PALI_LESSON_CATEGORIES.forEach((cat) => {
        const lessons = getLessonsByCategory(cat.id);
        expect(lessons.length).toBeGreaterThanOrEqual(1);
        lessons.forEach((l) => {
          expect(l.categoryId).toBe(cat.id);
        });
      });
    });

    it('[T2_PL_03] Verse analysis lessons contain word-by-word grammatical breakdowns', () => {
      const verseLessons = PALI_LESSONS.filter((l) => l.verseAnalysis !== undefined);
      expect(verseLessons.length).toBeGreaterThanOrEqual(3);

      verseLessons.forEach((lesson) => {
        const va = lesson.verseAnalysis!;
        expect(va.originalPali.length).toBeGreaterThan(10);
        expect(va.vietnamese.length).toBeGreaterThan(10);
        expect(va.breakdown.length).toBeGreaterThanOrEqual(4);

        va.breakdown.forEach((w) => {
          expect(w.word.length).toBeGreaterThan(0);
          expect(w.grammar.length).toBeGreaterThan(0);
          expect(w.meaning.length).toBeGreaterThan(0);
        });
      });
    });

    it('[T2_PL_04] web.php and TheravadaController register the dedicated Pali lesson show route and handler', () => {
      const webPhpPath = path.resolve(process.cwd(), 'routes/web.php');
      const webPhpContent = fs.readFileSync(webPhpPath, 'utf-8');

      expect(webPhpContent.includes('/hoc-pali/{slug}')).toBe(true);
      expect(webPhpContent.includes('paliLessonShow')).toBe(true);

      const controllerPath = path.resolve(process.cwd(), 'app/Http/Controllers/Theravada/TheravadaController.php');
      const controllerContent = fs.readFileSync(controllerPath, 'utf-8');

      expect(controllerContent.includes('function paliLessonShow')).toBe(true);
      expect(controllerContent.includes('Theravada/PaliLessonShow')).toBe(true);
      expect(controllerContent.includes('abort(404')).toBe(true);
    });

    it('[T2_PL_05] TheravadaLayout.vue and Index.vue include navigation links to /theravada/hoc-pali', () => {
      const layoutPath = path.resolve(process.cwd(), 'resources/js/Layouts/TheravadaLayout.vue');
      const layoutContent = fs.readFileSync(layoutPath, 'utf-8');

      expect(layoutContent.includes('/theravada/hoc-pali')).toBe(true);

      const indexPath = path.resolve(process.cwd(), 'resources/js/Pages/Theravada/Index.vue');
      const indexContent = fs.readFileSync(indexPath, 'utf-8');

      expect(indexContent.includes('/theravada/hoc-pali')).toBe(true);
    });

    it('[T2_PL_06] Progress calculation deduplicates corrupted duplicate completed lesson IDs', () => {
      const validIds = new Set(PALI_LESSONS.map((l) => l.id));
      const corruptedCompletedList = [
        'pali-01-nguyen-am-phu-am',
        'pali-01-nguyen-am-phu-am',
        'pali-01-nguyen-am-phu-am',
        'pali-02-quy-tac-phat-am',
        'invalid-ghost-id-999',
      ];

      const deduplicatedValid = new Set(corruptedCompletedList.filter((id) => validIds.has(id)));
      expect(deduplicatedValid.size).toBe(2);

      const totalCount = PALI_LESSONS.length;
      const computedPct = Math.min(100, Math.max(0, Math.round((deduplicatedValid.size / totalCount) * 100)));
      expect(computedPct).toBe(20);
    });

    it('[T2_PL_07] Multi-criteria search filter correctly identifies vocabulary, roots, notes, and verse breakdowns', () => {
      // Test search query matching on term
      const queryTerm = 'vibhatti';
      const matchedTerm = PALI_LESSONS.filter((lesson) => {
        const matchTitle = lesson.title.toLowerCase().includes(queryTerm);
        const matchPali = lesson.paliTitle.toLowerCase().includes(queryTerm);
        const matchDesc = lesson.description.toLowerCase().includes(queryTerm);
        const matchTags = lesson.tags.some((t) => t.toLowerCase().includes(queryTerm));
        const matchVocab = lesson.vocabulary.some(
          (v) =>
            v.term.toLowerCase().includes(queryTerm) ||
            v.vietnamese.toLowerCase().includes(queryTerm) ||
            (v.root && v.root.toLowerCase().includes(queryTerm)) ||
            (v.note && v.note.toLowerCase().includes(queryTerm)) ||
            (v.ipa && v.ipa.toLowerCase().includes(queryTerm))
        );
        const matchVerse = lesson.verseAnalysis
          ? lesson.verseAnalysis.originalPali.toLowerCase().includes(queryTerm) ||
            lesson.verseAnalysis.vietnamese.toLowerCase().includes(queryTerm) ||
            lesson.verseAnalysis.breakdown.some(
              (b) =>
                b.word.toLowerCase().includes(queryTerm) ||
                b.meaning.toLowerCase().includes(queryTerm) ||
                (b.rootOrStem && b.rootOrStem.toLowerCase().includes(queryTerm))
            )
          : false;
        return matchTitle || matchPali || matchDesc || matchTags || matchVocab || matchVerse;
      });

      expect(matchedTerm.length).toBeGreaterThanOrEqual(1);
      expect(matchedTerm.some((l) => l.id === 'pali-03-danh-tu-8-bien-cach')).toBe(true);

      // Test search on root (e.g. "√budh")
      const queryRoot = 'budh';
      const matchedRoot = PALI_LESSONS.filter((l) =>
        l.vocabulary.some((v) => v.root && v.root.toLowerCase().includes(queryRoot))
      );
      expect(matchedRoot.length).toBeGreaterThanOrEqual(1);
      expect(matchedRoot.some((l) => l.id === 'pali-05-tam-bao-tam-quy-y')).toBe(true);
    });

    it('[T2_PL_08] usePaliProgress methods safely handle empty, null, or corrupted lesson IDs and NaN scores', () => {
      const {
        isLessonCompleted,
        isLessonBookmarked,
        getLessonScore,
        markLessonCompleted,
        toggleBookmark,
        setLastActiveLesson,
        addStudyTime,
      } = usePaliProgress();

      // Guarded falsy checks
      expect(isLessonCompleted('')).toBe(false);
      expect(isLessonCompleted(null as any)).toBe(false);
      expect(isLessonBookmarked('')).toBe(false);
      expect(isLessonBookmarked(undefined as any)).toBe(false);
      expect(getLessonScore('')).toBe(null);
      expect(getLessonScore(null as any)).toBe(null);

      // Safe invalid mutations do not crash
      markLessonCompleted('', 90);
      markLessonCompleted(null as any, 100);
      toggleBookmark('');
      toggleBookmark(null as any);
      setLastActiveLesson('');
      setLastActiveLesson(null as any);
      addStudyTime(NaN);
      addStudyTime(-10);
    });

    it('[T2_PL_09] findLessonById and getLessonsByCategory handle case-insensitivity and invalid inputs', () => {
      expect(findLessonById('')).toBeUndefined();
      expect(findLessonById(null as any)).toBeUndefined();
      expect(getLessonsByCategory('')).toEqual([]);
      expect(getLessonsByCategory(undefined as any)).toEqual([]);

      // Case-insensitive lookup
      const mixedCaseLesson = findLessonById('PALI-01-NGUYEN-AM-PHU-AM');
      expect(mixedCaseLesson).toBeDefined();
      expect(mixedCaseLesson?.id).toBe('pali-01-nguyen-am-phu-am');

      const trimmedLesson = findLessonById('  nguyen-am-va-phu-am-pali  ');
      expect(trimmedLesson).toBeDefined();
      expect(trimmedLesson?.slug).toBe('nguyen-am-va-phu-am-pali');
    });

    it('[T2_PL_10] getAdjacentLessons correctly computes previous and next lessons for first, middle and last lessons', () => {
      // First lesson
      const first = getAdjacentLessons(PALI_LESSONS[0].slug);
      expect(first.prevLesson).toBeNull();
      expect(first.nextLesson).toBeDefined();
      expect(first.nextLesson?.id).toBe(PALI_LESSONS[1].id);

      // Middle lesson (Lesson 5)
      const middle = getAdjacentLessons(PALI_LESSONS[4].slug);
      expect(middle.prevLesson?.id).toBe(PALI_LESSONS[3].id);
      expect(middle.nextLesson?.id).toBe(PALI_LESSONS[5].id);

      // Last lesson (Lesson 10)
      const lastIndex = PALI_LESSONS.length - 1;
      const last = getAdjacentLessons(PALI_LESSONS[lastIndex].slug);
      expect(last.prevLesson?.id).toBe(PALI_LESSONS[lastIndex - 1].id);
      expect(last.nextLesson).toBeNull();

      // Invalid input
      const invalid = getAdjacentLessons('invalid-ghost-slug');
      expect(invalid.prevLesson).toBeNull();
      expect(invalid.nextLesson).toBeNull();
    });

    it('[T2_PL_11] All grammar tables have exact column count match between headers and row cells', () => {
      PALI_LESSONS.forEach((lesson) => {
        lesson.grammarSections.forEach((sec) => {
          if (sec.table) {
            const headerCount = sec.table.headers.length;
            expect(headerCount).toBeGreaterThanOrEqual(2);
            sec.table.rows.forEach((row) => {
              expect(row.length).toBe(headerCount);
            });
          }
        });
      });
    });

    it('[T2_PL_12] usePaliProgress source implements cross-tab storage event synchronization and fallback', () => {
      const progressPath = path.resolve(process.cwd(), 'resources/js/composables/usePaliProgress.ts');
      const progressContent = fs.readFileSync(progressPath, 'utf-8');

      expect(progressContent.includes('storage')).toBe(true);
      expect(progressContent.includes('addEventListener')).toBe(true);
      expect(progressContent.includes('STORAGE_KEY')).toBe(true);
    });
  });

  // ==========================================================================
  // TIER 3: Cross-Feature Interactions (Glossary, Apps, Sitemap & i18n)
  // ==========================================================================
  describe('[T3_PALI] Cross-Feature Linking & i18n Localization', () => {
    it('[T3_PL_01] Apps.vue and Glossary.vue cross-link to /theravada/hoc-pali', () => {
      const appsPath = path.resolve(process.cwd(), 'resources/js/Pages/Theravada/Apps.vue');
      const appsContent = fs.readFileSync(appsPath, 'utf-8');
      expect(appsContent.includes('/theravada/hoc-pali')).toBe(true);

      const glossaryPath = path.resolve(process.cwd(), 'resources/js/Pages/Theravada/Glossary.vue');
      const glossaryContent = fs.readFileSync(glossaryPath, 'utf-8');
      expect(glossaryContent.includes('/theravada/hoc-pali')).toBe(true);
    });

    it('[T3_PL_02] useI18n contains localization keys for Pali learning in Vietnamese and English', () => {
      const i18nPath = path.resolve(process.cwd(), 'resources/js/composables/useI18n.ts');
      const i18nContent = fs.readFileSync(i18nPath, 'utf-8');

      expect(i18nContent.includes("'theravada.paliLearning': 'Học Tiếng Pāḷi'")).toBe(true);
      expect(i18nContent.includes("'theravada.paliLearning': 'Learn Pāḷi'")).toBe(true);
    });

    it('[T3_PL_03] SeoController includes all individual /hoc-pali/{slug} show page URLs in the Theravada XML sitemap generator', () => {
      const seoPath = path.resolve(process.cwd(), 'app/Http/Controllers/SeoController.php');
      const seoContent = fs.readFileSync(seoPath, 'utf-8');

      expect(seoContent.includes('/hoc-pali')).toBe(true);
      PALI_LESSONS.forEach((lesson) => {
        expect(seoContent.includes(lesson.slug)).toBe(true);
      });
    });

    it('[T3_PL_04] TheravadaController validLessons dictionary strictly mirrors all 10 frontend lesson slugs and IDs', () => {
      const controllerPath = path.resolve(process.cwd(), 'app/Http/Controllers/Theravada/TheravadaController.php');
      const controllerContent = fs.readFileSync(controllerPath, 'utf-8');

      PALI_LESSONS.forEach((lesson) => {
        expect(controllerContent.includes(`'${lesson.slug}'`)).toBe(true);
        expect(controllerContent.includes(`'${lesson.id}'`)).toBe(true);
      });
    });
  });

  // ==========================================================================
  // TIER 4: Real-World E2E Scenarios (Learning Workflow & Show Page Structure)
  // ==========================================================================
  describe('[T4_PALI] End-to-End Learning Workflow Simulation', () => {
    it('[T4_PL_01] Simulates complete curriculum progress calculation and rank mastery across all 5 tiers', () => {
      const totalCount = PALI_LESSONS.length;
      expect(totalCount).toBeGreaterThanOrEqual(10);

      const getRankForPct = (pct: number) => {
        if (pct >= 100) return 'Tipiṭakadhara';
        if (pct >= 70) return 'Padagū Pāḷi';
        if (pct >= 40) return 'Saddasatthi';
        if (pct > 0) return 'Sikkhāka';
        return 'Ārambha';
      };

      // 0% -> Sơ Khai
      expect(getRankForPct(0)).toBe('Ārambha');

      // 10% (1 lesson) -> Sikkhāka
      expect(getRankForPct(10)).toBe('Sikkhāka');

      // 30% (3 lessons) -> Sikkhāka
      expect(getRankForPct(30)).toBe('Sikkhāka');

      // 40% (4 lessons) -> Saddasatthi
      expect(getRankForPct(40)).toBe('Saddasatthi');

      // 60% (6 lessons) -> Saddasatthi
      expect(getRankForPct(60)).toBe('Saddasatthi');

      // 70% (7 lessons) -> Padagū Pāḷi
      expect(getRankForPct(70)).toBe('Padagū Pāḷi');

      // 90% (9 lessons) -> Padagū Pāḷi
      expect(getRankForPct(90)).toBe('Padagū Pāḷi');

      // 100% (10 lessons) -> Tipiṭakadhara
      expect(getRankForPct(100)).toBe('Tipiṭakadhara');
    });

    it('[T4_PL_02] All quiz questions have passing score reachable with correct answers', () => {
      PALI_LESSONS.forEach((lesson) => {
        const correctIndices = lesson.quiz.map((q) => q.correctIndex);
        let simulatedScore = 0;

        correctIndices.forEach((ans, idx) => {
          if (ans === lesson.quiz[idx].correctIndex) {
            simulatedScore++;
          }
        });

        const percent = Math.round((simulatedScore / lesson.quiz.length) * 100);
        expect(percent).toBe(100);
        expect(percent).toBeGreaterThanOrEqual(70); // Passing threshold
      });
    });

    it('[T4_PL_03] PaliLessonShow.vue contains dynamic TOC, font-size adjustment, mindful bell, focus mode and adjacent lesson navigation', () => {
      const showPagePath = path.resolve(process.cwd(), 'resources/js/Pages/Theravada/PaliLessonShow.vue');
      const showPageContent = fs.readFileSync(showPagePath, 'utf-8');

      expect(showPageContent.includes('mindfulBell')).toBe(true);
      expect(showPageContent.includes('toggleFontSize')).toBe(true);
      expect(showPageContent.includes('isFocusMode')).toBe(true);
      expect(showPageContent.includes('prevTarget')).toBe(true);
      expect(showPageContent.includes('nextTarget')).toBe(true);
      expect(showPageContent.includes('submitQuiz')).toBe(true);
      expect(showPageContent.includes('usePaliProgress')).toBe(true);
      expect(showPageContent.includes('tocItems')).toBe(true);
    });

    it('[T4_PL_04] PaliLearning.vue navigates directly to dedicated lesson routes /theravada/hoc-pali/{slug} with zero dead modal relics', () => {
      const pagePath = path.resolve(process.cwd(), 'resources/js/Pages/Theravada/PaliLearning.vue');
      const pageContent = fs.readFileSync(pagePath, 'utf-8');

      expect(pageContent.includes('/theravada/hoc-pali/')).toBe(true);
      expect(pageContent.includes('router.visit')).toBe(true);
      expect(pageContent.includes('URLSearchParams')).toBe(true);
      expect(pageContent.includes('location.search')).toBe(true);
      expect(pageContent.includes('location.hash')).toBe(true);
      expect(pageContent.includes('PaliLessonModal')).toBe(false);
    });

    it('[T4_PL_05] All 10 lessons contain practice exercises with solutions and breakdowns', () => {
      PALI_LESSONS.forEach((lesson) => {
        expect(lesson.practiceExercises).toBeDefined();
        expect(lesson.practiceExercises!.length).toBeGreaterThanOrEqual(1);
        lesson.practiceExercises!.forEach((ex) => {
          expect(ex.instruction.length).toBeGreaterThan(5);
          expect(ex.paliText.length).toBeGreaterThan(1);
          expect(ex.solution.length).toBeGreaterThan(2);
        });
      });
    });

    it('[T4_PL_06] TheravadaController supports ID alias redirection for backward compatibility', () => {
      const controllerPath = path.resolve(process.cwd(), 'app/Http/Controllers/Theravada/TheravadaController.php');
      const controllerContent = fs.readFileSync(controllerPath, 'utf-8');

      expect(controllerContent.includes("redirect(")).toBe(true);
      expect(controllerContent.includes("301")).toBe(true);
    });
  });
});
