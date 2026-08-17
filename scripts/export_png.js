import fs from 'fs';
import path from 'path';
import { Resvg } from '@resvg/resvg-js';

const brandDir = path.resolve('public/brand');
const artifactDir = 'C:\\Users\\Admin\\.gemini\\antigravity\\brain\\122079ef-dd1a-4120-afaf-1a5c5665d73e';

const exportsList = [
  {
    input: 'macatung-mascot-icon.svg',
    output: 'macatung-mascot-icon.png',
    width: 1024,
  },
  {
    input: 'macatung-logo-horizontal.svg',
    output: 'macatung-logo-horizontal.png',
    width: 1560,
  },
  {
    input: 'macatung-mascot-transparent.svg',
    output: 'macatung-mascot-transparent.png',
    width: 1024,
  },
];

for (const item of exportsList) {
  const svgPath = path.join(brandDir, item.input);
  if (!fs.existsSync(svgPath)) {
    console.error(`Missing ${svgPath}`);
    continue;
  }
  const svg = fs.readFileSync(svgPath, 'utf8');
  const resvg = new Resvg(svg, {
    fitTo: {
      mode: 'width',
      value: item.width,
    },
    background: item.input.includes('transparent') ? undefined : '#04070d',
  });

  const pngData = resvg.render();
  const pngBuffer = pngData.asPng();

  const outPath = path.join(brandDir, item.output);
  fs.writeFileSync(outPath, pngBuffer);
  console.log(`Exported ${outPath} (${pngBuffer.length} bytes)`);

  if (fs.existsSync(artifactDir)) {
    const artifactPath = path.join(artifactDir, item.output);
    fs.writeFileSync(artifactPath, pngBuffer);
    console.log(`Copied to artifact ${artifactPath}`);
  }
}
