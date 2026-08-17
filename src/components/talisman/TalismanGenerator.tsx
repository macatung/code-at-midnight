import React, { useState } from 'react';
import { talismanPresets } from '../../data/talismanData';
import type { TalismanPreset } from '../../types/portfolio';
import { sound } from '../../audio/soundEffects';
import confetti from 'canvas-confetti';
import { Sparkles, Copy, Check, Wand2, Heart } from 'lucide-react';

export const TalismanGenerator: React.FC = () => {
  const [selectedPreset, setSelectedPreset] = useState<TalismanPreset>(talismanPresets[0]);
  const [customName, setCustomName] = useState<string>('Developer');
  const [customWish, setCustomWish] = useState<string>('');
  const [colorStyle, setColorStyle] = useState<'yellow' | 'crimson' | 'cyan' | 'purple'>('yellow');
  const [isSealed, setIsSealed] = useState<boolean>(false);
  const [copied, setCopied] = useState<boolean>(false);

  const handleSelectPreset = (preset: TalismanPreset) => {
    sound.playClick();
    setSelectedPreset(preset);
    setColorStyle(preset.colorScheme);
    setIsSealed(false);
  };

  const handleBlessTalisman = () => {
    sound.playTalisman();
    setIsSealed(true);

    const colors = colorStyle === 'yellow'
      ? ['#ffd166', '#f4b41a', '#e63946']
      : colorStyle === 'crimson'
      ? ['#ff0054', '#ef233c', '#ffd166']
      : colorStyle === 'cyan'
      ? ['#00f5a0', '#00d2ff', '#ffffff']
      : ['#9d4edd', '#c77dff', '#00f5a0'];

    confetti({
      particleCount: 50,
      spread: 70,
      origin: { y: 0.65 },
      colors,
    });
  };

  const handleCopyTalisman = () => {
    const textToCopy = `
╔═════════════════════════════════════════════════════╗
║             📜 BÙA HỘ MỆNH LẬP TRÌNH 📜              ║
║         ${selectedPreset.runeTop}            ║
║  Chủ sở hữu: ${customName || 'Developer'}             ║
╠═════════════════════════════════════════════════════╣
║  ${selectedPreset.codeSnippet}
╠═════════════════════════════════════════════════════╣
║  Ý nghĩa: ${selectedPreset.meaning}
║  Khắc bùa bởi: macatung.dev — Code at midnight      ║
╚═════════════════════════════════════════════════════╝
    `;
    navigator.clipboard.writeText(textToCopy.trim());
    sound.playClick();
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  // Color schemes for talisman paper
  const themeStyles = {
    yellow: {
      bg: 'from-amber-200 via-amber-300 to-yellow-400',
      border: 'border-red-700',
      seal: '#c9182b',
      textMain: 'text-red-950',
      textRune: 'text-red-800',
      badgeBg: 'bg-red-800 text-amber-100',
    },
    crimson: {
      bg: 'from-rose-700 via-red-700 to-rose-800',
      border: 'border-amber-300',
      seal: '#ffd166',
      textMain: 'text-amber-100',
      textRune: 'text-amber-200',
      badgeBg: 'bg-amber-300 text-rose-950',
    },
    cyan: {
      bg: 'from-emerald-400 via-teal-400 to-cyan-500',
      border: 'border-slate-900',
      seal: '#0f172a',
      textMain: 'text-slate-950',
      textRune: 'text-slate-900',
      badgeBg: 'bg-slate-950 text-cyan-300',
    },
    purple: {
      bg: 'from-purple-700 via-fuchsia-700 to-indigo-800',
      border: 'border-amber-300',
      seal: '#ffd166',
      textMain: 'text-amber-100',
      textRune: 'text-amber-200',
      badgeBg: 'bg-amber-300 text-purple-950',
    },
  }[colorStyle];

  return (
    <section id="talisman-gen" className="py-24 relative border-t border-slate-800/80 bg-midnight-950/40">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-16 space-y-3">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border border-slate-800 text-xs font-mono text-emerald-300">
            <Sparkles className="w-3.5 h-3.5 text-talisman-yellow" />
            <span>// ENCHANTED TALISMAN FORGE</span>
          </div>
          <h2 className="font-display font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
            Thỉnh Bùa <span className="text-talisman-yellow text-glow-talisman">Lập Trình</span>
          </h2>
          <p className="text-slate-400 font-mono text-xs sm:text-base">
            Tự tạo và thỉnh lá bùa chú công nghệ độc quyền của <strong>Ma Cà Tưng</strong> để bảo vệ dự án khỏi 100% bug & downtime!
          </p>
        </div>

        {/* Generator Grid: Controls on Left, Live Talisman on Right */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          
          {/* Controls Form */}
          <div className="lg:col-span-7 space-y-6">
            
            {/* Presets Selector */}
            <div className="space-y-2">
              <label className="text-xs font-mono uppercase tracking-wider text-slate-300 flex items-center gap-1.5 font-bold">
                <Wand2 className="w-4 h-4 text-emerald-400" />
                <span>1. Chọn loại bùa linh nghiệm:</span>
              </label>
              <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                {talismanPresets.map((preset) => (
                  <button
                    key={preset.id}
                    onClick={() => handleSelectPreset(preset)}
                    className={`p-3 rounded-2xl border text-left font-mono transition-all text-xs flex flex-col justify-between ${
                      selectedPreset.id === preset.id
                        ? 'bg-midnight-900 border-emerald-500/50 text-white shadow-sm'
                        : 'glass-panel border-slate-800 text-slate-400 hover:text-slate-200'
                    }`}
                  >
                    <span className="font-bold text-slate-200">{preset.title}</span>
                    <span className="text-[10px] text-slate-500 truncate mt-1">{preset.runeTop}</span>
                  </button>
                ))}
              </div>
            </div>

            {/* Customizer Inputs */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="space-y-1.5">
                <label className="text-xs font-mono text-slate-400">
                  Tên Lập trình viên / Chủ bùa:
                </label>
                <input
                  type="text"
                  value={customName}
                  onChange={(e) => setCustomName(e.target.value)}
                  placeholder="Ví dụ: Dev Đẹp Trai, Senior Lead..."
                  className="w-full px-4 py-2.5 rounded-xl bg-midnight-900 border border-slate-800 text-white placeholder:text-slate-600 focus:outline-none focus:border-emerald-500/50 font-mono text-xs"
                />
              </div>

              <div className="space-y-1.5">
                <label className="text-xs font-mono text-slate-400">
                  Lời chúc / Nguyện ước tùy chọn:
                </label>
                <input
                  type="text"
                  value={customWish}
                  onChange={(e) => setCustomWish(e.target.value)}
                  placeholder="Ví dụ: Cầu mong sprint này về đích sớm"
                  className="w-full px-4 py-2.5 rounded-xl bg-midnight-900 border border-slate-800 text-white placeholder:text-slate-600 focus:outline-none focus:border-emerald-500/50 font-mono text-xs"
                />
              </div>
            </div>

            {/* Color Palette Selector */}
            <div className="space-y-2">
              <label className="text-xs font-mono uppercase tracking-wider text-slate-300 font-bold">
                2. Chọn màu giấy bùa:
              </label>
              <div className="flex flex-wrap gap-2">
                {[
                  { id: 'yellow', label: 'Vàng Cung Đình (Classic)', bg: 'bg-amber-400' },
                  { id: 'crimson', label: 'Đỏ Chu Sa (Blood Moon)', bg: 'bg-rose-700' },
                  { id: 'cyan', label: 'Xanh Ngọc Ma Cà Tưng', bg: 'bg-emerald-400' },
                  { id: 'purple', label: 'Tím Huyền Bí (Cyber)', bg: 'bg-purple-700' },
                ].map((color) => (
                  <button
                    key={color.id}
                    onClick={() => {
                      sound.playClick();
                      setColorStyle(color.id as any);
                    }}
                    className={`px-3 py-1.5 rounded-xl text-xs font-mono flex items-center gap-2 border transition-all ${
                      colorStyle === color.id
                        ? 'bg-slate-800 border-white text-white shadow-sm'
                        : 'bg-midnight-900 border-slate-800 text-slate-400 hover:text-slate-200'
                    }`}
                  >
                    <span className={`w-3 h-3 rounded-full ${color.bg}`} />
                    <span>{color.label}</span>
                  </button>
                ))}
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex flex-wrap items-center gap-3 pt-2">
              <button
                onClick={handleBlessTalisman}
                className="px-6 py-3 rounded-2xl bg-amber-400 hover:bg-amber-300 text-midnight-950 font-bold font-mono text-xs sm:text-sm flex items-center gap-2 shadow-md hover:scale-105 active:scale-95 transition-all"
              >
                <Sparkles className="w-4 h-4" />
                <span>Khai Quang & Thỉnh Bùa! ⚡</span>
              </button>

              <button
                onClick={handleCopyTalisman}
                className="px-5 py-3 rounded-2xl glass-panel border border-slate-700 hover:border-emerald-500/50 text-slate-200 hover:text-white font-mono text-xs sm:text-sm flex items-center gap-2 transition-colors"
              >
                {copied ? <Check className="w-4 h-4 text-emerald-400" /> : <Copy className="w-4 h-4" />}
                <span>{copied ? 'Đã sao chép!' : 'Copy Mã Bùa'}</span>
              </button>
            </div>

            {/* Description Info */}
            <div className="p-4 rounded-2xl bg-midnight-900/80 border border-slate-800 text-xs font-mono text-slate-400">
              <strong className="text-emerald-300 block mb-1">Ý nghĩa linh nghiệm:</strong>
              {customWish || selectedPreset.meaning}
            </div>

          </div>

          {/* Right Column: The Visual Talisman Card (Clean Tech Runes, No Chinese Text) */}
          <div className="lg:col-span-5 flex justify-center">
            <div
              className={`relative w-72 sm:w-80 min-h-[460px] p-6 rounded-2xl bg-gradient-to-b ${themeStyles.bg} border-4 ${themeStyles.border} shadow-2xl flex flex-col justify-between select-none animate-talisman-flutter origin-top`}
            >
              {/* Paper Top Notch */}
              <div className="absolute top-2 left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-midnight-950/20" />

              {/* Talisman Top Section: Seal & Header */}
              <div className="text-center pt-3 space-y-1">
                {/* Modern Tech Seal Stamp with Code Symbol */}
                <div className="inline-flex items-center justify-center w-12 h-12 rounded-xl border-2 border-dashed border-red-800 bg-red-800/20 mx-auto font-mono">
                  <span className="font-bold text-xs text-red-950 tracking-wider">
                    &lt;/&gt;
                  </span>
                </div>

                <div className="text-xs font-mono font-bold tracking-wider text-red-950 mt-1">
                  {selectedPreset.runeTop}
                </div>

                <div className="h-0.5 w-3/4 mx-auto bg-red-800/30 my-2" />
              </div>

              {/* Talisman Center Section: Code Glyphs */}
              <div className="my-auto text-center space-y-3 px-2">
                <div className={`px-2.5 py-1 rounded ${themeStyles.badgeBg} font-mono text-xs font-extrabold tracking-wider inline-block`}>
                  {selectedPreset.title}
                </div>

                {/* The Code Spell */}
                <div className="p-3 rounded-xl bg-black/10 backdrop-blur-sm border border-red-900/20 font-mono text-xs font-bold text-red-950 text-left leading-relaxed">
                  <div className="text-[10px] text-red-800 mb-1 opacity-80">// Chủ bùa: {customName || 'Developer'}</div>
                  <code>{selectedPreset.codeSnippet}</code>
                </div>

                {/* Tech Glyph Wave */}
                <div className="text-red-900 text-xs font-mono font-bold tracking-widest">
                  &lt;/&gt; ⚡ &lt;/&gt; ⚡ &lt;/&gt;
                </div>
              </div>

              {/* Talisman Bottom Section: Stamp & macatung.dev watermark */}
              <div className="text-center pt-2 border-t border-red-900/30">
                <div className="flex items-center justify-between text-[10px] font-mono font-bold text-red-950">
                  <span>macatung.dev</span>
                  <span className="flex items-center gap-0.5">
                    <Heart className="w-3 h-3 text-red-700 fill-red-700" />
                    <span>0 BUG</span>
                  </span>
                </div>
                <div className="text-[9px] font-mono text-red-900/80 italic mt-0.5">
                  Code at midnight protocol
                </div>
              </div>

              {/* Sealed Stamp Ribbon if Khai Quang */}
              {isSealed && (
                <div className="absolute inset-0 bg-black/15 backdrop-blur-[1px] rounded-2xl flex items-center justify-center pointer-events-none animate-fadeIn">
                  <div className="p-3 rounded-2xl bg-red-800 text-amber-100 font-mono font-extrabold text-xs border-2 border-amber-300 shadow-2xl -rotate-12 uppercase tracking-wider text-center">
                    ✓ ĐÃ KHAI QUANG<br />
                    <span className="text-[10px] font-normal">ZERO_BUG_GUARANTEED</span>
                  </div>
                </div>
              )}

            </div>
          </div>

        </div>

      </div>
    </section>
  );
};
