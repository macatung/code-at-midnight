import fs from 'fs';
import path from 'path';
import { Resvg } from '@resvg/resvg-js';

const decodeBrandDir = path.resolve('public/brand/decode');
const artifactDir = 'C:\\Users\\Admin\\.gemini\\antigravity\\brain\\35685d20-d7f7-4cb3-96bc-87041ee0e418';

const exportsList = [
  {
    input: 'decode-badge-avatar.svg',
    output: 'decode-badge-avatar.png',
    width: 1024,
  },
  {
    input: 'decode-badge-avatar.svg',
    output: 'decode-badge-avatar-512.png',
    width: 512,
  },
  {
    input: 'decode-logo-horizontal.svg',
    output: 'decode-logo-horizontal.png',
    width: 1680,
  },
  {
    input: 'decode-mascot-cad.svg',
    output: 'decode-mascot-cad.png',
    width: 960,
    transparent: true,
  },
  {
    input: 'og-decode-1200x630.svg',
    output: 'og-decode-1200x630.png',
    width: 1200,
  },
  {
    input: 'favicon.svg',
    output: 'favicon-decode-16x16.png',
    width: 16,
  },
  {
    input: 'favicon.svg',
    output: 'favicon-decode-32x32.png',
    width: 32,
  },
  {
    input: 'favicon.svg',
    output: 'favicon-decode-48x48.png',
    width: 48,
  },
  {
    input: 'favicon.svg',
    output: 'favicon-decode-192x192.png',
    width: 192,
  },
  {
    input: 'favicon.svg',
    output: 'apple-touch-icon.png',
    width: 180,
  },
  {
    input: 'decode-banner-youtube.svg',
    output: 'decode-banner-youtube.png',
    width: 2560,
  },
];

console.log('--- Rendering Vector Assets for Ma Giải Mã (decode.macatung.dev) ---');

for (const item of exportsList) {
  const svgPath = path.join(decodeBrandDir, item.input);
  if (!fs.existsSync(svgPath)) {
    console.error(`Missing input SVG: ${svgPath}`);
    continue;
  }
  const svg = fs.readFileSync(svgPath, 'utf8');
  const resvg = new Resvg(svg, {
    fitTo: {
      mode: 'width',
      value: item.width,
    },
    font: {
      loadSystemFonts: true,
      defaultFontFamily: 'Segoe UI',
    },
    background: item.transparent ? undefined : '#060913',
  });

  const pngData = resvg.render();
  const pngBuffer = pngData.asPng();

  const outPath = path.join(decodeBrandDir, item.output);
  fs.writeFileSync(outPath, pngBuffer);
  console.log(`Rendered: ${item.output} (${pngBuffer.length} bytes, width ${item.width}px)`);

  if (fs.existsSync(artifactDir)) {
    const artifactPath = path.join(artifactDir, item.output);
    fs.writeFileSync(artifactPath, pngBuffer);
  }
}

// Generate favicon-decode.ico
// Minimal valid ICO format wrapping PNG payload
const png32Buffer = fs.readFileSync(path.join(decodeBrandDir, 'favicon-decode-32x32.png'));
const png16Buffer = fs.readFileSync(path.join(decodeBrandDir, 'favicon-decode-16x16.png'));

// Write ICO header for 2 images (16x16 and 32x32)
function createIco(images) {
  const header = Buffer.alloc(6);
  header.writeUInt16LE(0, 0); // Reserved
  header.writeUInt16LE(1, 2); // Type 1 = ICO
  header.writeUInt16LE(images.length, 4); // Number of images

  let offset = 6 + images.length * 16;
  const entries = [];
  for (const img of images) {
    const entry = Buffer.alloc(16);
    entry.writeUInt8(img.width >= 256 ? 0 : img.width, 0);
    entry.writeUInt8(img.height >= 256 ? 0 : img.height, 1);
    entry.writeUInt8(0, 2); // Color palette
    entry.writeUInt8(0, 3); // Reserved
    entry.writeUInt16LE(1, 4); // Color planes
    entry.writeUInt16LE(32, 6); // Bits per pixel
    entry.writeUInt32LE(img.data.length, 8); // Size of image data
    entry.writeUInt32LE(offset, 12); // Offset to image data
    offset += img.data.length;
    entries.push(entry);
  }

  return Buffer.concat([header, ...entries, ...images.map((img) => img.data)]);
}

const icoBuffer = createIco([
  { width: 16, height: 16, data: png16Buffer },
  { width: 32, height: 32, data: png32Buffer },
]);

const icoPath = path.join(decodeBrandDir, 'favicon-decode.ico');
fs.writeFileSync(icoPath, icoBuffer);
console.log(`Generated ICO: ${icoPath} (${icoBuffer.length} bytes)`);

if (fs.existsSync(artifactDir)) {
  fs.writeFileSync(path.join(artifactDir, 'favicon-decode.ico'), icoBuffer);
}

console.log('--- Finished Exporting All Decode Brand Assets ---');
