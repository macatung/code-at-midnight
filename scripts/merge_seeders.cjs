const fs = require('fs');
const path = require('path');

// 1. Restore clean original seeder
const seederPath = path.resolve(__dirname, '../database/seeders/TheravadaContentSeeder.php');

// Let's use git to restore clean seeder first
const { execSync } = require('child_process');
execSync('git checkout HEAD -- database/seeders/TheravadaContentSeeder.php');

let content = fs.readFileSync(seederPath, 'utf-8');

const replacements = [
  ['/theravada/kinh/tien-trinh-tam-thuc-citta-vithi-qua-ngu-mon-va-y-mon', '/theravada/kinh/tien-trinh-tam-thuc-citta-vithi-17-sat-na-nhan-dien-y-nghi'],
  ['/theravada/kinh/kinh-chau-bau-ratana-sutta-song-ngu-pali-viet', '/theravada/kinh/kinh-chau-bau-ratana-sutta-giai-tru-tam-tai-pali-viet'],
  ['/theravada/kinh/tu-niem-xu-satipatthana-con-duong-doc-nhat-thanh-tinh-tam', '/theravada/kinh/thien-tu-niem-xu-satipatthana-huong-dan-thuc-hanh-vipassana'],
  ['/theravada/kinh/lich-su-sau-ky-dai-ket-tap-tam-tang-thanh-dien-pali', '/theravada/kinh/lich-su-sau-ky-ket-tap-tam-tang-kinh-dien-pali-chasangayana'],
  ['/theravada/kinh/kinh-dai-bat-niet-ban-mahaparinibbana-sutta-loi-di-huan-cuoi-cung', '/theravada/kinh/cuoc-doi-duc-phat-gotama-tu-dan-sanh-den-nhap-niet-ban'],
  ['/theravada/kinh/nam-trien-cai-nivarana-chuong-ngai-thien-dinh', '/theravada/kinh/nam-trien-cai-panca-nivarana-va-phap-tri-lieu-thuc-tien'],
  ['/theravada/kinh/kinh-dai-niem-xu-satipatthana-sutta-than-tho-tam-phap', '/theravada/kinh/thien-tu-niem-xu-satipatthana-huong-dan-thuc-hanh-vipassana'],
  ['/theravada/kinh/kinh-dai-niem-xu-mahasatipatthana-sutta-pali-viet', '/theravada/kinh/thien-tu-niem-xu-satipatthana-huong-dan-thuc-hanh-vipassana'],
];

function extractWorker(filePath) {
  let fileContent = fs.readFileSync(filePath, 'utf-8');
  replacements.forEach(([from, to]) => {
    fileContent = fileContent.replaceAll(from, to);
  });
  const startIdx = fileContent.indexOf('return [');
  const afterStart = fileContent.slice(startIdx + 8);
  const lastCloseIdx = afterStart.lastIndexOf('];');
  return afterStart.slice(0, lastCloseIdx).trim();
}

let pariyatti = extractWorker('d:/Work/macatung/.agents/worker_pariyatti_1/articles.php');
let history = extractWorker('d:/Work/macatung/.agents/worker_history_1/articles.php');
let practice = extractWorker('d:/Work/macatung/.agents/worker_practice_1/articles.php');

// Renumber comments
pariyatti = pariyatti
  .replace('1. 24 DUYÊN HỆ', '39. 24 DUYÊN HỆ')
  .replace('2. SẮC PHÁP CHÂN ĐẾ', '40. SẮC PHÁP CHÂN ĐẾ')
  .replace('3. 52 SỞ HỮU TÂM', '41. 52 SỞ HỮU TÂM')
  .replace('4. TIẾN TRÌNH CẬN TỬ & TÁI SINH', '42. TIẾN TRÌNH CẬN TỬ & TÁI SINH')
  .replace('5. DUYÊN KHỞI LIÊN HOÀN', '43. DUYÊN KHỞI LIÊN HOÀN');

history = history
  .replace('1. LỊCH SỬ PHÂN PHÁI', '44. LỊCH SỬ PHÂN PHÁI')
  .replace('2. ĐẠI TRƯỞNG LÃO', '45. ĐẠI TRƯỞNG LÃO')
  .replace('3. KỲ KẾT TẬP TAM TẠNG LẦN IV', '46. KỲ KẾT TẬP TAM TẠNG LẦN IV')
  .replace('4. KỲ KẾT TẬP TAM TẠNG LẦN III', '47. KỲ KẾT TẬP TAM TẠNG LẦN III')
  .replace('5. TRƯỞNG LÃO MAHINDA', '48. TRƯỞNG LÃO MAHINDA');

practice = practice
  .replace('1. TOÀN THƯ 40 ĐỀ MỤC', '49. TOÀN THƯ 40 ĐỀ MỤC')
  .replace('2. LỘ TRÌNH 16 TẦNG TUỆ', '50. LỘ TRÌNH 16 TẦNG TUỆ')
  .replace('3. PHƯƠNG PHÁP QUÁN 32 THỂ TRỌNG', '51. PHƯƠNG PHÁP QUÁN 32 THỂ TRỌNG')
  .replace('4. CẨM NANG THỰC HÀNH GIỚI', '52. CẨM NANG THỰC HÀNH GIỚI')
  .replace('5. PHƯƠNG PHÁP QUÁN TỨ ĐẠI', '53. PHƯƠNG PHÁP QUÁN TỨ ĐẠI');

// Since pariyatti, history, practice already end with `],`, we just join them with `\n\n`
const insertion = `\n\n${pariyatti}\n\n${history}\n\n${practice}\n`;

// Update docstring
content = content.replace(/Featuring \d+ deeply enriched/, 'Featuring 53 deeply enriched');

// Locate the footer
const footerPattern = /\r?\n\s*\];\r?\n\s*foreach\s*\(\$articles as \$data\)/;
const match = content.match(footerPattern);
if (!match) {
  throw new Error('Could not find footer pattern in seeder');
}

const beforeFooter = content.slice(0, match.index);
const afterFooter = content.slice(match.index);

const merged = beforeFooter + insertion + afterFooter;

fs.writeFileSync(seederPath, merged, 'utf-8');
console.log('Successfully merged 15 articles into TheravadaContentSeeder.php without double commas');
