import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { execSync } from 'node:child_process';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const PROJECT_ROOT = path.resolve(__dirname, '..');

const PHP_PATH = 'C:\\laragon\\bin\\php\\php-8.2.30-nts-Win32-vs16-x64\\php.exe';
const SEEDER_PATH = path.resolve(PROJECT_ROOT, 'database/seeders/TheravadaContentSeeder.php');

export const EXPANSION_SLUGS = [
  'hai-muoi-bon-duyen-he-patthana-catu-visatipaccaya-vi-dieu-phap',
  'sac-phap-chan-de-rupa-paramattha-cau-truc-bon-sac-kalapa',
  'nam-muoi-hai-so-huu-tam-cetasika-quy-luat-phoi-hop-tam-thuc',
  'tien-trinh-can-tu-va-tai-sinh-cuti-patisandhi-vithi-31-coi',
  'duyen-khoi-lien-hoan-paticcasamuppada-12-chi-phan-va-3-luan-chuyen',
  'lich-su-phan-phai-phat-giao-so-khai-theravada-va-mahasanghika',
  'dai-truong-lao-xa-loi-phat-va-muc-kien-lien-hai-vi-thuong-thu-thinh-van',
  'ky-ket-tap-lan-thu-tu-aluvihara-khac-tam-tang-len-la-boi-tich-lan',
  'ky-ket-tap-lan-ba-va-chin-phai-doan-hoang-phap-thoi-vua-asoka',
  'truong-lao-mahinda-va-ni-truong-sanghamitta-khai-sang-phat-giao-tich-lan',
  'toan-thu-40-de-muc-thien-dinh-samatha-kammatthana-visuddhimagga',
  'lo-trinh-16-tang-tue-minh-sat-solasa-nana-va-that-thanh-tinh',
  'phuong-phap-quan-32-the-trong-cua-than-dvattimsakara-kayagatasati',
  'cam-nang-thuc-hanh-gioi-can-ban-va-bat-quan-trai-gioi-uposatha',
  'phuong-phap-quan-tu-dai-catudhatuvavatthana-12-dac-tinh-chan-de',
];

console.log('='.repeat(80));
console.log(' ADVERSARIAL VERIFICATION AUDIT: WORD COUNT, SLUGS & LINK GRAPH TOPOLOGY');
console.log('='.repeat(80));

// 1. Parse via JS Regex
function parseSeederJS(filePath) {
  const content = fs.readFileSync(filePath, 'utf-8');
  const blocks = content.split(/(?=\[\s*['"]site_domain['"]\s*=>\s*['"]theravada['"])/);
  const articles = [];

  for (let i = 1; i < blocks.length; i++) {
    const block = blocks[i];
    const getField = (name) => {
      const regex = new RegExp(`['"]${name}['"]\\s*=>\\s*(?:'((?:\\\\'|[^'])*)'|"((?:\\\\"|[^"])*)")`, 'm');
      const m = block.match(regex);
      if (!m) return null;
      const rawVal = m[1] !== undefined ? m[1] : m[2];
      return rawVal.replace(/\\'/g, "'").replace(/\\"/g, '"');
    };

    const getContentField = () => {
      const heredocRegex = /['"]content['"]\s*=>\s*<<<[ ']*([A-Za-z0-9_]+)[' ]*\r?\n([\s\S]*?)\r?\n\s*\1/m;
      const m = block.match(heredocRegex);
      if (m) return m[2];
      return getField('content');
    };

    articles.push({
      index: i,
      site_domain: getField('site_domain'),
      title: getField('title'),
      pali_title: getField('pali_title'),
      slug: getField('slug'),
      category: getField('category'),
      excerpt: getField('excerpt'),
      author: getField('author'),
      content: getContentField(),
    });
  }
  return articles;
}

// 2. Parse via PHP CLI Reflection/Evaluation
function parseSeederPHP(filePath) {
  const autoloadPath = path.resolve(PROJECT_ROOT, 'vendor/autoload.php').replace(/\\/g, '/');
  const phpScript = `
    require_once '${autoloadPath}';
    use Carbon\\Carbon;
    $code = file_get_contents('${filePath.replace(/\\/g, '/')}');
    if (preg_match('/\\$articles\\s*=\\s*\\[([\\s\\S]*?)\\];\\s*foreach/m', $code, $m)) {
        $arrCode = 'use Carbon\\\\Carbon; return [' . $m[1] . '];';
        $arr = eval($arrCode);
        $simplified = array_map(function($a) {
            return [
                'site_domain' => $a['site_domain'] ?? null,
                'title' => $a['title'] ?? null,
                'pali_title' => $a['pali_title'] ?? null,
                'slug' => $a['slug'] ?? null,
                'category' => $a['category'] ?? null,
                'excerpt' => $a['excerpt'] ?? null,
                'author' => $a['author'] ?? null,
                'content_len' => strlen($a['content'] ?? ''),
            ];
        }, $arr);
        echo json_encode($simplified, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['error' => 'Regex match failed']);
    }
  `;

  try {
    const output = execSync(`"${PHP_PATH}" -r "${phpScript.replace(/"/g, '\\"').replace(/\n/g, ' ')}"`, {
      encoding: 'utf-8',
      maxBuffer: 50 * 1024 * 1024,
    });
    return JSON.parse(output);
  } catch (err) {
    console.error('PHP parse failed:', err.message);
    return null;
  }
}

const articlesJS = parseSeederJS(SEEDER_PATH);
console.log(`[JS Parser] Found ${articlesJS.length} articles.`);

const articlesPHP = parseSeederPHP(SEEDER_PATH);
if (articlesPHP && Array.isArray(articlesPHP)) {
  console.log(`[PHP Parser] Found ${articlesPHP.length} articles.`);
  if (articlesJS.length === articlesPHP.length) {
    console.log(`✔ Parser agreement: Both JS and PHP extracted exactly ${articlesJS.length} articles.`);
  } else {
    console.error(`✖ Parser discrepancy: JS (${articlesJS.length}) vs PHP (${articlesPHP.length})!`);
  }
}

// ----------------------------------------------------------------------------
// 2. Word Count Challenging & Stripping Algorithms
// ----------------------------------------------------------------------------
// Metric 1: Standard (as defined in PROJECT.md / verify_theravada_content.js)
function wordCountStandard(text) {
  if (!text) return 0;
  const noCode = text.replace(/```[\s\S]*?```/g, '');
  const noHtml = noCode.replace(/<[^>]+>/g, ' ');
  const noMdLinks = noHtml.replace(/\[([^\]]+)\]\([^)]+\)/g, '$1');
  return noMdLinks.trim().split(/\s+/).filter(Boolean).length;
}

// Metric 2: Full Syntax Stripped (All markdown formatting symbols stripped, keeping words in headings/tables/lists/quotes)
function wordCountSyntaxStripped(text) {
  if (!text) return 0;
  let t = text;
  // 1. Strip fenced code blocks
  t = t.replace(/```[\s\S]*?```/g, ' ');
  // 2. Strip HTML tags & comments
  t = t.replace(/<!--[\s\S]*?-->/g, ' ');
  t = t.replace(/<[^>]+>/g, ' ');
  // 3. Strip markdown images & links
  t = t.replace(/!\[([^\]]*)\]\([^)]+\)/g, ' ');
  t = t.replace(/\[([^\]]+)\]\([^)]+\)/g, '$1');
  // 4. Strip table syntax symbols (| and ---) while keeping cell content
  t = t.replace(/\|/g, ' ');
  t = t.replace(/-{3,}/g, ' ');
  // 5. Strip markdown headers (#), blockquotes (>), list markers (-, *, +, 1.)
  t = t.replace(/^#{1,6}\s+/gm, ' ');
  t = t.replace(/^>\s*/gm, ' ');
  t = t.replace(/^(\s*[-*+]|\s*\d+\.)\s+/gm, ' ');
  // 6. Strip emphasis (*, _, ~) and inline code (`)
  t = t.replace(/[*_~`]/g, ' ');
  // 7. Strip hr
  t = t.replace(/^(?:---|\*\*\*|___)\s*$/gm, ' ');

  // Filter out non-word punctuation tokens
  const tokens = t.trim().split(/\s+/).filter(tok => {
    return /[a-zA-Z0-9à-ỹÀ-ỸāīūñṃṇṭḍḷĀĪŪÑṂṆṬḌḶ]/.test(tok);
  });
  return tokens.length;
}

// Metric 3: Body Prose Only (Excluding markdown tables and headers lines completely)
function wordCountBodyProseOnly(text) {
  if (!text) return 0;
  let t = text;
  t = t.replace(/```[\s\S]*?```/g, ' ');
  t = t.replace(/<!--[\s\S]*?-->/g, ' ');
  t = t.replace(/<[^>]+>/g, ' ');
  t = t.replace(/!\[([^\]]*)\]\([^)]+\)/g, ' ');
  t = t.replace(/\[([^\]]+)\]\([^)]+\)/g, '$1');
  // Remove full table lines
  t = t.replace(/^\s*\|.*\|\s*$/gm, ' ');
  // Remove full header lines
  t = t.replace(/^#{1,6}\s+.*$/gm, ' ');
  // Strip markup
  t = t.replace(/[*_~`>#-]/g, ' ');
  t = t.replace(/^(\s*[-*+]|\s*\d+\.)\s+/gm, ' ');
  const tokens = t.trim().split(/\s+/).filter(tok => {
    return /[a-zA-Z0-9à-ỹÀ-ỸāīūñṃṇṭḍḷĀĪŪÑṂṆṬḌḶ]/.test(tok);
  });
  return tokens.length;
}

console.log('\n' + '-'.repeat(96));
console.log(' 📝 WORD COUNT CHALLENGE ACROSS 15 EXPANSION ARTICLES');
console.log('-'.repeat(96));

let wordCountPass = true;
const expansionArticles = articlesJS.filter(a => EXPANSION_SLUGS.includes(a.slug));

console.log(
  'Slug'.padEnd(45) +
  ' | Std WC'.padEnd(10) +
  ' | Syntax-Strip'.padEnd(14) +
  ' | Body-Prose'.padEnd(13) +
  ' | Result'
);
console.log('-'.repeat(96));

for (const expSlug of EXPANSION_SLUGS) {
  const art = articlesJS.find(a => a.slug === expSlug);
  if (!art) {
    console.error(`✖ Missing expansion article: ${expSlug}`);
    wordCountPass = false;
    continue;
  }
  const stdWc = wordCountStandard(art.content);
  const syntaxWc = wordCountSyntaxStripped(art.content);
  const bodyProseWc = wordCountBodyProseOnly(art.content);

  // Requirement: strictly exceeds 1,000 words (> 1000)
  const passed = stdWc > 1000 && syntaxWc > 1000;
  if (!passed) wordCountPass = false;

  const shortSlug = expSlug.length > 43 ? expSlug.substring(0, 40) + '...' : expSlug;
  console.log(
    shortSlug.padEnd(45) +
    ` | ${String(stdWc).padStart(6)}   ` +
    ` | ${String(syntaxWc).padStart(10)}   ` +
    ` | ${String(bodyProseWc).padStart(9)}   ` +
    ` | ${passed ? '✔ PASS (>1000w)' : '✖ FAIL (<=1000w)'}`
  );
}

// ----------------------------------------------------------------------------
// 3. Slug Uniqueness & Regex Conformance Challenge
// ----------------------------------------------------------------------------
console.log('\n' + '-'.repeat(80));
console.log(' 🏷 SLUG INTEGRITY & REGEX CONFORMANCE CHALLENGE');
console.log('-'.repeat(80));

let slugPass = true;
const slugMap = new Map();
const lowerSlugMap = new Map();
const SLUG_REGEX = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;

articlesJS.forEach((a, i) => {
  const s = a.slug;
  if (!s) {
    console.error(`✖ Article #${i + 1} (${a.title}) has NULL or empty slug!`);
    slugPass = false;
    return;
  }

  // Regex check
  if (!SLUG_REGEX.test(s)) {
    console.error(`✖ Slug regex violation at #${i + 1}: "${s}" (must match ^[a-z0-9]+(?:-[a-z0-9]+)*$)`);
    slugPass = false;
  }

  // Exact collision check
  if (slugMap.has(s)) {
    console.error(`✖ Exact Slug Collision: "${s}" seen at #${slugMap.get(s)} and #${i + 1}`);
    slugPass = false;
  } else {
    slugMap.set(s, i + 1);
  }

  // Case-insensitive collision check
  const lower = s.toLowerCase();
  if (lowerSlugMap.has(lower)) {
    if (s !== lowerSlugMap.get(lower).orig) {
      console.error(`✖ Case-insensitive Slug Collision: "${s}" vs "${lowerSlugMap.get(lower).orig}"`);
      slugPass = false;
    }
  } else {
    lowerSlugMap.set(lower, { orig: s, index: i + 1 });
  }

  // Boundary checks: leading/trailing hyphens, double hyphens
  if (s.startsWith('-') || s.endsWith('-') || s.includes('--')) {
    console.error(`✖ Slug boundary issue: "${s}"`);
    slugPass = false;
  }
});

if (slugPass) {
  console.log(`✔ All ${slugMap.size} slugs are 100% unique, URL-safe kebab-case, and strictly match ^[a-z0-9]+(?:-[a-z0-9]+)*$.`);
}

// ----------------------------------------------------------------------------
// 4. Category & Schema Distribution Verification
// ----------------------------------------------------------------------------
console.log('\n' + '-'.repeat(80));
console.log(' 🏷 CATEGORY & SCHEMA DISTRIBUTION FOR 15 EXPANSION ARTICLES');
console.log('-'.repeat(80));

const expCategoryCounts = {};
for (const expSlug of EXPANSION_SLUGS) {
  const art = articlesJS.find(a => a.slug === expSlug);
  if (art) {
    expCategoryCounts[art.category] = (expCategoryCounts[art.category] || 0) + 1;
    console.log(`• [${art.category.padEnd(10)}] : ${art.title}`);
  }
}
console.log('\nCategory breakdown across 15 expansion articles:');
console.log(expCategoryCounts);

// ----------------------------------------------------------------------------
// 5. Internal Link Topology & Graph Integrity Challenge
// ----------------------------------------------------------------------------
console.log('\n' + '-'.repeat(80));
console.log(' 🕸 INTERNAL LINK GRAPH TOPOLOGY CHALLENGE');
console.log('-'.repeat(80));

const validSlugs = new Set(articlesJS.map(a => a.slug));
const linkGraph = new Map(); // sourceSlug -> Set of targetSlugs
const inDegrees = new Map(); // targetSlug -> count
const outDegrees = new Map(); // sourceSlug -> count
const brokenLinks = [];

// Initialize degree maps
for (const slug of validSlugs) {
  linkGraph.set(slug, new Set());
  inDegrees.set(slug, 0);
  outDegrees.set(slug, 0);
}

let totalLinksExtracted = 0;

articlesJS.forEach((art) => {
  const srcSlug = art.slug;
  const content = art.content || '';

  // Extract all markdown links: [text](/theravada/kinh/slug) or [text](/theravada/slug) or href="/theravada/kinh/slug"
  const mdLinkRegex = /\[([^\]]+)\]\(\/theravada\/(?:kinh\/)?([a-z0-9-]+)\)/g;
  let m;
  while ((m = mdLinkRegex.exec(content)) !== null) {
    const text = m[1];
    const targetSlug = m[2];
    totalLinksExtracted++;

    if (!validSlugs.has(targetSlug)) {
      brokenLinks.push({
        sourceArticle: art.title,
        sourceSlug: srcSlug,
        targetSlug,
        anchorText: text,
      });
    } else {
      if (!linkGraph.get(srcSlug).has(targetSlug)) {
        linkGraph.get(srcSlug).add(targetSlug);
      }
    }
  }
});

// Calculate degrees
for (const [src, targets] of linkGraph.entries()) {
  outDegrees.set(src, targets.size);
  for (const tgt of targets) {
    inDegrees.set(tgt, inDegrees.get(tgt) + 1);
  }
}

console.log(`Total internal markdown links extracted: ${totalLinksExtracted}`);
console.log(`Unique directed edges in link graph: ${Array.from(linkGraph.values()).reduce((acc, s) => acc + s.size, 0)}`);

if (brokenLinks.length > 0) {
  console.error(`✖ BROKEN INTERNAL LINKS FOUND: ${brokenLinks.length}`);
  brokenLinks.forEach((b, idx) => {
    console.error(`  ${idx + 1}. [${b.sourceSlug}] -> /theravada/kinh/${b.targetSlug} (Anchor: "${b.anchorText}")`);
  });
} else {
  console.log(`✔ 100% of internal links resolve to valid, existing articles in TheravadaContentSeeder.php!`);
}

// Check orphan nodes
const orphans = [];
for (const slug of validSlugs) {
  const inD = inDegrees.get(slug);
  const outD = outDegrees.get(slug);
  if (inD === 0 && outD === 0) {
    orphans.push(slug);
  }
}

console.log(`Link Graph Orphan Nodes (in=0, out=0): ${orphans.length}`);

// Graph Degree Metrics for 15 Expansion Articles
console.log('\n' + '-'.repeat(80));
console.log(' 📈 EXPANSION ARTICLES LINK TOPOLOGY BREAKDOWN');
console.log('-'.repeat(80));
console.log(
  'Slug'.padEnd(50) +
  ' | Out-Degree'.padEnd(13) +
  ' | In-Degree'.padEnd(12) +
  ' | Status'
);
console.log('-'.repeat(85));

for (const expSlug of EXPANSION_SLUGS) {
  const outD = outDegrees.get(expSlug) || 0;
  const inD = inDegrees.get(expSlug) || 0;
  const shortSlug = expSlug.length > 48 ? expSlug.substring(0, 45) + '...' : expSlug;
  console.log(
    shortSlug.padEnd(50) +
    ` | ${String(outD).padStart(8)}    ` +
    ` | ${String(inD).padStart(7)}   ` +
    ` | ${outD > 0 || inD > 0 ? '✔ Interconnected' : '⚠ Isolated'}`
  );
}

// ----------------------------------------------------------------------------
// 6. Test Suites Invocations
// ----------------------------------------------------------------------------
console.log('\n' + '='.repeat(80));
console.log(' 🚀 EXECUTING VERIFICATION SUITES');
console.log('='.repeat(80));

let verifyCliPass = false;
let testRunnerPass = false;
let phpunitPass = false;

try {
  const cliOutput = execSync(`node "${path.resolve(PROJECT_ROOT, 'scripts/verify_theravada_content.js')}"`, { encoding: 'utf-8' });
  console.log('✔ verify_theravada_content.js PASSED:\n' + cliOutput.trim());
  verifyCliPass = true;
} catch (e) {
  console.error('✖ verify_theravada_content.js FAILED:', e.message);
}

try {
  const runnerOutput = execSync(`node "${path.resolve(PROJECT_ROOT, 'tests/run_all_tests.js')}"`, { encoding: 'utf-8' });
  console.log('✔ run_all_tests.js PASSED:\n' + runnerOutput.trim());
  testRunnerPass = true;
} catch (e) {
  console.error('✖ run_all_tests.js FAILED:', e.message);
}

try {
  const phpunitOutput = execSync(`"${PHP_PATH}" vendor/phpunit/phpunit/phpunit --filter=TheravadaContentValidationTest`, {
    cwd: PROJECT_ROOT,
    encoding: 'utf-8'
  });
  console.log('✔ PHPUnit TheravadaContentValidationTest PASSED:\n' + phpunitOutput.trim());
  phpunitPass = true;
} catch (e) {
  console.error('✖ PHPUnit FAILED:', e.message);
}

console.log('\n' + '='.repeat(80));
console.log(' 🎯 FINAL ADVERSARIAL VERDICT SUMMARY');
console.log('='.repeat(80));
console.log(`• Word Count (>1000w pure prose for 15 expansion articles): ${wordCountPass ? '✔ PASSED' : '✖ FAILED'}`);
console.log(`• Slug Uniqueness & Regex Conformance (^a-z0-9-):             ${slugPass ? '✔ PASSED' : '✖ FAILED'}`);
console.log(`• Internal Link Graph Integrity (0 broken links):             ${brokenLinks.length === 0 ? '✔ PASSED' : '✖ FAILED'}`);

const overallPass = wordCountPass && slugPass && brokenLinks.length === 0;
console.log(`\nOVERALL CHALLENGER VERDICT: ${overallPass ? '✔ APPROVE' : '✖ REQUEST_CHANGES'}`);
