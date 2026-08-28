#!/usr/bin/env node

/**
 * CLI Verification Script: Theravāda Canonical Content Integrity Validator
 *
 * Validates:
 * 1. Database Seeder Parsing (`database/seeders/TheravadaContentSeeder.php`)
 * 2. Mandatory Schema Fields (`site_domain`, `title`, `pali_title`, `slug`, `category`, `excerpt`, `author`, `content`)
 * 3. Slug Uniqueness & URL-safe kebab-case format
 * 4. Vietnamese Word Count Calculation (stripping code blocks, HTML, markdown links)
 * 5. Minimum Word Count Threshold (>1000 words for deep-dive canonical articles)
 * 6. Category Conformance ('phap-hoc', 'lich-su', 'phap-hanh', 'kinh-tung')
 * 7. Markdown & Internal Cross-Link Integrity (`/theravada/kinh/{slug}`)
 *
 * CLI Usage:
 *   node scripts/verify_theravada_content.js [options]
 *
 * Options:
 *   --file=<path>          Custom seeder path (default: database/seeders/TheravadaContentSeeder.php)
 *   --min=<number>         Minimum total articles required (default: 50)
 *   --require-expansion    Strictly assert that all 15 deep-dive expansion articles are present
 *   --json                 Output result in JSON format
 *   --help                 Display CLI help
 *
 * Exit Codes:
 *   0: All validations PASSED
 *   1: One or more validation errors detected
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const PROJECT_ROOT = path.resolve(__dirname, '..');

// ============================================================================
// ANSI Styling
// ============================================================================
const colors = {
  reset: '\x1b[0m',
  bold: '\x1b[1m',
  dim: '\x1b[2m',
  green: '\x1b[32m',
  red: '\x1b[31m',
  yellow: '\x1b[33m',
  cyan: '\x1b[36m',
  magenta: '\x1b[35m',
  blue: '\x1b[34m',
  gray: '\x1b[90m',
  bgGreen: '\x1b[42m',
  bgRed: '\x1b[41m',
};

function c(text, colorName) {
  return `${colors[colorName] || ''}${text}${colors.reset}`;
}

// ============================================================================
// Canonical 15 Deep-Dive Expansion Slugs (Targeted for >1000w requirement)
// ============================================================================
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

export const VALID_CATEGORIES = new Set([
  'phap-hoc',
  'phap-hanh',
  'lich-su',
  'kinh-tung',
]);

// ============================================================================
// Word Count Utility
// ============================================================================
export function calculateVietnameseWordCount(rawContent) {
  if (!rawContent || typeof rawContent !== 'string') return 0;

  // 1. Strip fenced code blocks (Mermaid, code snippets, etc.)
  const noCode = rawContent.replace(/```[\s\S]*?```/g, '');

  // 2. Strip inline HTML tags
  const noHtml = noCode.replace(/<[^>]+>/g, ' ');

  // 3. Strip Markdown links [text](url) -> text
  const noMdLinks = noHtml.replace(/\[([^\]]+)\]\([^)]+\)/g, '$1');

  // 4. Tokenize and count whitespace-separated words
  const words = noMdLinks.trim().split(/\s+/).filter(Boolean);
  return words.length;
}

// ============================================================================
// Seeder Content Parser
// ============================================================================
export function parseSeederFile(filePath) {
  if (!fs.existsSync(filePath)) {
    throw new Error(`Seeder file not found: ${filePath}`);
  }

  const content = fs.readFileSync(filePath, 'utf-8');

  // Split into article blocks
  // An article block starts with `[` and contains `'site_domain' => 'theravada'`
  const blocks = content.split(/(?=\[\s*['"]site_domain['"]\s*=>\s*['"]theravada['"])/);

  const articles = [];

  for (let i = 1; i < blocks.length; i++) {
    const block = blocks[i];

    // Helper to extract a single-quoted or double-quoted scalar field
    const getField = (name) => {
      const regex = new RegExp(`['"]${name}['"]\\s*=>\\s*(?:'((?:\\\\'|[^'])*)'|"((?:\\\\"|[^"])*)")`, 'm');
      const m = block.match(regex);
      if (!m) return null;
      const rawVal = m[1] !== undefined ? m[1] : m[2];
      return rawVal.replace(/\\'/g, "'").replace(/\\"/g, '"');
    };

    // Helper to extract Heredoc / Nowdoc content: 'content' => <<< 'EOF' ... EOF
    const getContentField = () => {
      const heredocRegex = /['"]content['"]\s*=>\s*<<<[ ']*([A-Za-z0-9_]+)[' ]*\r?\n([\s\S]*?)\r?\n\s*\1/m;
      const m = block.match(heredocRegex);
      if (m) {
        return m[2];
      }
      // Fallback for quoted string content
      return getField('content');
    };

    // Helper to extract tags array
    const getTags = () => {
      const tagsMatch = block.match(/['"]tags['"]\s*=>\s*(?:\[([\s\S]*?)\]|array\s*\(([\s\S]*?)\))/);
      if (!tagsMatch) return [];
      const rawTags = tagsMatch[1] || tagsMatch[2] || '';
      const itemRegex = /['"]([^'"]+)['"]/g;
      const tags = [];
      let itemMatch;
      while ((itemMatch = itemRegex.exec(rawTags)) !== null) {
        tags.push(itemMatch[1]);
      }
      return tags;
    };

    // Helper to extract reading_time_min
    const getReadingTime = () => {
      const m = block.match(/['"]reading_time_min['"]\s*=>\s*(\d+)/);
      return m ? parseInt(m[1], 10) : null;
    };

    const site_domain = getField('site_domain');
    const title = getField('title');
    const pali_title = getField('pali_title');
    const slug = getField('slug');
    const category = getField('category');
    const excerpt = getField('excerpt');
    const author = getField('author');
    const rawContent = getContentField();
    const tags = getTags();
    const reading_time_min = getReadingTime();

    articles.push({
      index: i,
      site_domain,
      title,
      pali_title,
      slug,
      category,
      excerpt,
      author,
      content: rawContent,
      tags,
      reading_time_min,
      word_count: calculateVietnameseWordCount(rawContent || ''),
    });
  }

  return articles;
}

// ============================================================================
// Comprehensive Verification Engine
// ============================================================================
export function verifyArticles(articles, options = {}) {
  const { minArticles = 50, requireExpansionArticles = false } = options;
  const errors = [];
  const warnings = [];
  const slugMap = new Map();
  const validSlugs = new Set(articles.map((a) => a.slug).filter(Boolean));

  // Check 1: Minimum total article count
  if (articles.length < minArticles) {
    errors.push(`Expected at least ${minArticles} articles, but found ${articles.length}.`);
  }

  // Check 2: Article Schema & Field Conformance
  articles.forEach((article, idx) => {
    const id = article.slug || `Article #${article.index || idx + 1}`;

    // site_domain
    if (article.site_domain !== 'theravada') {
      errors.push(`[${id}] Invalid site_domain: expected 'theravada', got '${article.site_domain}'.`);
    }

    // title
    if (!article.title || article.title.trim().length < 3) {
      errors.push(`[${id}] Missing or empty 'title'.`);
    }

    // pali_title
    if (!article.pali_title || article.pali_title.trim().length < 2) {
      errors.push(`[${id}] Missing or empty 'pali_title'.`);
    }

    // slug format
    if (!article.slug) {
      errors.push(`[Article #${idx + 1}] Missing 'slug'.`);
    } else {
      if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(article.slug)) {
        errors.push(`[${id}] Invalid slug format: '${article.slug}' (must be lower kebab-case).`);
      }
      if (slugMap.has(article.slug)) {
        errors.push(
          `[${id}] Duplicate slug collision detected! First seen at #${slugMap.get(article.slug)}, repeated at #${article.index}.`
        );
      } else {
        slugMap.set(article.slug, article.index || idx + 1);
      }
    }

    // category
    if (!article.category) {
      errors.push(`[${id}] Missing 'category'.`);
    } else if (!VALID_CATEGORIES.has(article.category)) {
      errors.push(
        `[${id}] Invalid category '${article.category}'. Must be one of: ${[...VALID_CATEGORIES].join(', ')}.`
      );
    }

    // excerpt
    if (!article.excerpt || article.excerpt.trim().length < 10) {
      errors.push(`[${id}] Missing or too short 'excerpt' (<10 chars).`);
    }

    // author
    if (!article.author || article.author.trim().length < 3) {
      errors.push(`[${id}] Missing or too short 'author'.`);
    }

    // content
    if (!article.content || article.content.trim().length === 0) {
      errors.push(`[${id}] Empty 'content' body.`);
    }

    // Word Count Verification:
    // If this is an expansion article or if total >= 65, assert word_count > 1000
    const isExpansion = EXPANSION_SLUGS.includes(article.slug);
    if (isExpansion) {
      if (article.word_count < 1000) {
        errors.push(
          `[${id}] Deep-dive article word count too low: ${article.word_count} words (required > 1000 words).`
        );
      }
    } else {
      // General article content check
      if (article.word_count < 200) {
        warnings.push(`[${id}] Article content is short: ${article.word_count} words.`);
      }
    }

    // Internal Markdown Links Validation
    if (article.content) {
      const linkRegex = /\[([^\]]+)\]\(\/theravada\/kinh\/([a-z0-9-]+)\)/g;
      let linkMatch;
      while ((linkMatch = linkRegex.exec(article.content)) !== null) {
        const targetSlug = linkMatch[2];
        if (!validSlugs.has(targetSlug)) {
          warnings.push(`[${id}] Internal link targets unknown slug: '/theravada/kinh/${targetSlug}'.`);
        }
      }
    }
  });

  // Check 3: If expansion is required, verify all 15 expansion slugs are present
  if (requireExpansionArticles) {
    for (const expSlug of EXPANSION_SLUGS) {
      if (!slugMap.has(expSlug)) {
        errors.push(`Missing mandatory expansion article with slug: '${expSlug}'.`);
      }
    }
  }

  return {
    valid: errors.length === 0,
    totalArticles: articles.length,
    errors,
    warnings,
    stats: computeStats(articles),
  };
}

function computeStats(articles) {
  if (articles.length === 0) return { min: 0, max: 0, avg: 0, totalWords: 0, categories: {} };

  const wordCounts = articles.map((a) => a.word_count);
  const totalWords = wordCounts.reduce((sum, val) => sum + val, 0);
  const min = Math.min(...wordCounts);
  const max = Math.max(...wordCounts);
  const avg = Math.round(totalWords / articles.length);

  const categories = {};
  articles.forEach((a) => {
    const cat = a.category || 'unknown';
    categories[cat] = (categories[cat] || 0) + 1;
  });

  const expansionCount = articles.filter((a) => EXPANSION_SLUGS.includes(a.slug)).length;

  return {
    min,
    max,
    avg,
    totalWords,
    categories,
    expansionCount,
  };
}

// ============================================================================
// CLI Option Parsing & Main Execution
// ============================================================================
function parseArgs() {
  const args = process.argv.slice(2);
  const options = {
    file: path.resolve(PROJECT_ROOT, 'database/seeders/TheravadaContentSeeder.php'),
    minArticles: 50,
    requireExpansion: false,
    json: false,
    help: false,
  };

  for (let i = 0; i < args.length; i++) {
    const arg = args[i];
    if (arg === '--help' || arg === '-h') {
      options.help = true;
    } else if (arg.startsWith('--file=')) {
      options.file = path.resolve(process.cwd(), arg.split('=')[1]);
    } else if (arg.startsWith('--min=')) {
      options.minArticles = parseInt(arg.split('=')[1], 10);
    } else if (arg === '--require-expansion') {
      options.requireExpansion = true;
    } else if (arg === '--json') {
      options.json = true;
    }
  }

  return options;
}

export async function main() {
  const options = parseArgs();

  if (options.help) {
    console.log(`
${c('☸ Theravāda Content Integrity Verification CLI', 'bold')}

${c('Usage:', 'cyan')}
  node scripts/verify_theravada_content.js [options]

${c('Options:', 'cyan')}
  --file=<path>          Custom seeder path (default: database/seeders/TheravadaContentSeeder.php)
  --min=<number>         Minimum total articles required (default: 50)
  --require-expansion    Strictly assert that all 15 deep-dive expansion articles are present
  --json                 Output results as JSON
  --help                 Show this help message
`);
    process.exit(0);
  }

  let articles;
  try {
    articles = parseSeederFile(options.file);
  } catch (err) {
    if (options.json) {
      console.log(JSON.stringify({ valid: false, error: err.message }, null, 2));
    } else {
      console.error(c(`\n✖ Fatal Error reading seeder: ${err.message}`, 'red'));
    }
    process.exit(1);
  }

  const isExpanded = articles.length >= 65 || options.requireExpansion;
  const verification = verifyArticles(articles, {
    minArticles: options.minArticles,
    requireExpansionArticles: isExpanded,
  });

  const { stats, errors, warnings } = verification;

  if (options.json) {
    console.log(JSON.stringify({
      valid: verification.valid,
      totalArticles: articles.length,
      stats,
      errors,
      warnings,
    }, null, 2));
    process.exit(verification.valid ? 0 : 1);
  }

  console.log(c('═'.repeat(74), 'cyan'));
  console.log(c(' ☸ THERAVĀDA CONTENT EXPANSION & INTEGRITY VERIFICATION CLI ☸', 'bold'));
  console.log(c('═'.repeat(74), 'cyan'));
  console.log(` ${c('Target Seeder:', 'gray')} ${options.file}`);
  console.log(` ${c('Parsed Articles:', 'gray')} ${articles.length} records\n`);

  // Print Summary Table
  console.log(c('─'.repeat(74), 'gray'));
  console.log(c(' 📊 CONTENT METRICS & STATISTICS', 'bold'));
  console.log(c('─'.repeat(74), 'gray'));
  console.log(`  • Total Articles          : ${c(String(articles.length), 'bold')}`);
  console.log(`  • Deep-Dive Expansions    : ${c(String(stats.expansionCount) + '/15', 'cyan')}`);
  console.log(`  • Total Vietnamese Words  : ${c(stats.totalWords.toLocaleString(), 'bold')} words`);
  console.log(`  • Average Word Count      : ${c(stats.avg.toLocaleString(), 'green')} words/article`);
  console.log(`  • Min / Max Word Count    : ${stats.min} min / ${stats.max} max words`);
  console.log(
    `  • Category Distribution   : ${Object.entries(stats.categories)
      .map(([k, v]) => `${k}=${v}`)
      .join(', ')}`
  );

  // Warnings
  if (warnings.length > 0) {
    console.log(c('\n ⚠ WARNINGS:', 'yellow'));
    warnings.slice(0, 10).forEach((w) => console.log(`   ${c('!', 'yellow')} ${w}`));
    if (warnings.length > 10) {
      console.log(`   ... and ${warnings.length - 10} more warnings.`);
    }
  }

  // Errors
  if (errors.length > 0) {
    console.log(c('\n ✖ VALIDATION FAILURES:', 'red'));
    errors.forEach((e) => console.log(`   ${c('✖', 'red')} ${e}`));
  }

  console.log(c('═'.repeat(74), 'gray'));

  if (verification.valid) {
    console.log(
      c(' ✔ VERIFICATION PASSED: All Theravāda canonical articles adhere to schema & word count standards! ', 'bgGreen') +
        '\n'
    );
    process.exit(0);
  } else {
    console.log(
      c(` ✖ VERIFICATION FAILED: ${errors.length} error(s) detected. Please fix content seeder. `, 'bgRed') +
        '\n'
    );
    process.exit(1);
  }
}

// Auto-run if executed directly via CLI
if (process.argv[1] && path.resolve(process.argv[1]) === path.resolve(__filename)) {
  main().catch((err) => {
    console.error(c(`Unexpected CLI execution failure: ${err.stack || err}`, 'red'));
    process.exit(1);
  });
}
