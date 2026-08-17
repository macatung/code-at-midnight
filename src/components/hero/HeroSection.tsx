import React from 'react';
import { MacatungMascot } from '../mascot/MacatungMascot';
import { sound } from '../../audio/soundEffects';
import { Sparkles, Terminal, ArrowRight, Send, ShieldCheck, Flame, Coffee } from 'lucide-react';
import { GithubIcon, LinkedinIcon } from '../ui/Icons';

interface HeroSectionProps {
  onOpenTerminal?: () => void;
}

export const HeroSection: React.FC<HeroSectionProps> = ({ onOpenTerminal }) => {
  const scrollToSection = (id: string) => {
    sound.playClick();
    const element = document.querySelector(id);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <section className="relative pt-32 pb-20 md:pt-36 md:pb-24 overflow-hidden">
      {/* Background ambient lighting - Subtle & Deep Dark */}
      <div className="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[140px] pointer-events-none" />
      <div className="absolute top-1/3 right-10 w-[350px] h-[350px] bg-cyan-500/5 rounded-full blur-[120px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          
          {/* Left Column: Hero Copy & CTAs */}
          <div className="lg:col-span-7 space-y-6 text-center lg:text-left">
            
            {/* Midnight Protocol Tag */}
            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-panel border border-slate-800 text-emerald-300 text-xs font-mono tracking-wider shadow-sm">
              <span className="flex h-2 w-2 relative">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-phantom-mint opacity-75"></span>
                <span className="relative inline-flex rounded-full h-2 w-2 bg-phantom-mint"></span>
              </span>
              <span className="font-bold">MIDNIGHT PROTOCOL ACTIVE</span>
              <span className="text-slate-600">|</span>
              <span className="text-talisman-yellow flex items-center gap-1">
                <Coffee className="w-3 h-3" /> 100% Robusta Flow
              </span>
            </div>

            {/* Slogan & Title */}
            <div className="space-y-2">
              <h1 className="font-display font-extrabold text-4xl sm:text-6xl xl:text-7xl tracking-tight text-white leading-none">
                <span className="block">Code at</span>
                <span className="bg-gradient-to-r from-phantom-mint via-phantom-cyan to-teal-200 bg-clip-text text-transparent text-glow-mint">
                  midnight.
                </span>
              </h1>
              <p className="font-mono text-sm sm:text-base text-talisman-yellow flex items-center justify-center lg:justify-start gap-2 font-semibold">
                <span>[macatung.dev]</span>
                <span className="text-slate-600">/</span>
                <span className="text-slate-300">The Night-Crawler Engineer & Digital Alchemist</span>
              </p>
            </div>

            {/* Bio Paragraph */}
            <p className="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto lg:mx-0 leading-relaxed">
              Chào bạn! Tôi là <strong className="text-white font-semibold">Ma Cà Tưng</strong> — Kỹ sư Full-Stack & Creative Developer chuyên thức đêm tạo ra những trải nghiệm web đỉnh cao, kiến trúc đám mây tốc độ cao và giao diện pixel-perfect được tối ưu hóa chuẩn xác.
            </p>

            {/* CTAs */}
            <div className="flex flex-wrap items-center justify-center lg:justify-start gap-3 pt-2">
              {/* Summon Me Button */}
              <button
                onClick={() => scrollToSection('#contact')}
                className="px-6 py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-midnight-950 font-bold font-mono text-sm flex items-center gap-2 shadow-lg hover:scale-105 active:scale-95 transition-all duration-200"
              >
                <Send className="w-4 h-4" />
                <span>Summon Me (Liên Hệ)</span>
                <ArrowRight className="w-4 h-4" />
              </button>

              {/* View Projects */}
              <button
                onClick={() => scrollToSection('#projects')}
                className="px-6 py-3.5 rounded-2xl glass-panel border border-slate-800 hover:border-slate-700 text-slate-200 hover:text-white font-mono text-sm flex items-center gap-2 transition-all duration-200"
              >
                <Sparkles className="w-4 h-4 text-talisman-yellow" />
                <span>Xem Grimoire (Projects)</span>
              </button>

              {/* Launch CLI Button */}
              <button
                onClick={() => {
                  sound.playClick();
                  if (onOpenTerminal) onOpenTerminal();
                  else scrollToSection('#terminal');
                }}
                className="px-4 py-3.5 rounded-2xl glass-panel border border-slate-800 hover:border-cyan-500/30 text-cyan-300 hover:text-cyan-200 font-mono text-sm flex items-center gap-2 transition-all duration-200"
                title="Mở Terminal CLI"
              >
                <Terminal className="w-4 h-4" />
                <span>macatung-cli</span>
              </button>
            </div>

            {/* Social Proof & Quick Tech Pills */}
            <div className="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-4 text-xs font-mono text-slate-400">
              <div className="flex items-center gap-1.5">
                <ShieldCheck className="w-4 h-4 text-emerald-400" />
                <span>Zero Production Nightmares</span>
              </div>
              <div className="flex items-center gap-1.5">
                <Flame className="w-4 h-4 text-slate-300" />
                <span>High-Throughput Distributed Systems</span>
              </div>
              <div className="flex items-center gap-3 ml-2 border-l border-slate-800 pl-4">
                <a
                  href="https://github.com/macatung"
                  target="_blank"
                  rel="noreferrer"
                  className="text-slate-400 hover:text-white transition-colors"
                  title="GitHub"
                >
                  <GithubIcon className="w-4 h-4" />
                </a>
                <a
                  href="https://linkedin.com"
                  target="_blank"
                  rel="noreferrer"
                  className="text-slate-400 hover:text-cyan-400 transition-colors"
                  title="LinkedIn"
                >
                  <LinkedinIcon className="w-4 h-4" />
                </a>
              </div>
            </div>

          </div>

          {/* Right Column: Interactive Ma Cà Tưng Mascot Spotlight */}
          <div className="lg:col-span-5 flex flex-col items-center justify-center">
            <div className="relative w-full max-w-md p-6 rounded-3xl glass-panel border border-slate-800 flex flex-col items-center shadow-xl">
              
              {/* Corner Runes - Clean Tech Protocol Headers */}
              <span className="absolute top-3.5 left-4 text-[11px] font-mono text-emerald-400/70 select-none">
                0xMIDNIGHT
              </span>
              <span className="absolute top-3.5 right-4 text-[11px] font-mono text-talisman-yellow/80 select-none">
                // ZERO_BUG_PROTOCOL
              </span>

              {/* The Hero Mascot Component */}
              <div className="mt-4 mb-2">
                <MacatungMascot size="hero" showControls={true} />
              </div>

              {/* Interactive Talisman Callout */}
              <div className="w-full mt-4 p-3 rounded-2xl bg-midnight-950 border border-slate-800/80 flex items-center justify-between text-xs font-mono">
                <div className="flex items-center gap-2">
                  <span className="text-lg">📜</span>
                  <div>
                    <p className="text-white font-semibold">Bùa Hộ Mệnh 0 Bug</p>
                    <p className="text-slate-400 text-[11px]">Dán lên đầu, code xuyên màn đêm</p>
                  </div>
                </div>
                <button
                  onClick={() => scrollToSection('#talisman-gen')}
                  className="px-3 py-1.5 rounded-xl bg-talisman-yellow/15 hover:bg-talisman-yellow/25 text-talisman-yellow border border-talisman-yellow/30 transition-colors font-bold text-[11px]"
                >
                  Thỉnh Bùa →
                </button>
              </div>

            </div>
          </div>

        </div>
      </div>
    </section>
  );
};
