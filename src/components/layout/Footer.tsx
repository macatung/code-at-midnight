import React from 'react';
import { sound } from '../../audio/soundEffects';
import confetti from 'canvas-confetti';
import { ArrowUp, Heart, Coffee, MessageSquare } from 'lucide-react';
import { GithubIcon, LinkedinIcon } from '../ui/Icons';

export const Footer: React.FC = () => {
  const scrollToTop = () => {
    sound.playHop(1.8);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };

  const handleEasterEgg = () => {
    sound.playSuccess();
    confetti({
      particleCount: 50,
      spread: 60,
      origin: { y: 0.9 },
      colors: ['#ffd166', '#00f5a0', '#ff0054'],
    });
  };

  return (
    <footer className="relative border-t border-slate-800/80 bg-midnight-950/90 pt-16 pb-12 overflow-hidden">
      
      {/* Subtle top glow */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 h-px bg-gradient-to-r from-transparent via-emerald-500/40 to-transparent" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div className="flex flex-col md:flex-row items-center justify-between gap-8 pb-12 border-b border-slate-800/80">
          
          {/* Brand Info */}
          <div className="text-center md:text-left space-y-2">
            <div className="flex items-center justify-center md:justify-start gap-2">
              <span className="font-display font-bold text-xl text-white">
                macatung<span className="text-phantom-mint">.dev</span>
              </span>
              <span className="px-2 py-0.5 rounded text-[10px] font-mono uppercase bg-emerald-500/10 text-emerald-300 border border-emerald-500/30">
                Code at midnight
              </span>
            </div>
            <p className="text-xs font-mono text-slate-400 max-w-sm">
              Trang thông tin & portfolio của Ma Cà Tưng — Nơi các ý tưởng kỳ ảo được hiện thực hóa bằng công nghệ hiện đại.
            </p>
          </div>

          {/* Center Links */}
          <div className="flex flex-wrap items-center justify-center gap-6 text-xs font-mono text-slate-400">
            <a
              href="#about"
              onClick={() => sound.playClick()}
              className="hover:text-phantom-mint transition-colors"
            >
              // Origin
            </a>
            <a
              href="#projects"
              onClick={() => sound.playClick()}
              className="hover:text-phantom-mint transition-colors"
            >
              // Grimoire
            </a>
            <a
              href="#skills"
              onClick={() => sound.playClick()}
              className="hover:text-phantom-mint transition-colors"
            >
              // Talismans
            </a>
            <a
              href="#terminal"
              onClick={() => sound.playClick()}
              className="hover:text-phantom-mint transition-colors"
            >
              // Terminal
            </a>
            <a
              href="#talisman-gen"
              onClick={() => sound.playClick()}
              className="hover:text-talisman-yellow transition-colors"
            >
              // Thỉnh Bùa
            </a>
          </div>

          {/* Hop To Top Button */}
          <div>
            <button
              onClick={scrollToTop}
              className="px-4 py-2.5 rounded-2xl glass-panel-glow border border-emerald-500/40 text-emerald-300 hover:text-white font-mono text-xs flex items-center gap-2 shadow-glow-mint hover:scale-105 active:scale-95 transition-all"
            >
              <ArrowUp className="w-4 h-4 animate-bounce" />
              <span>Hop to Top 🦘</span>
            </button>
          </div>

        </div>

        {/* Bottom Bar */}
        <div className="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-mono text-slate-500">
          
          {/* Copyright with Easter Egg */}
          <div className="flex items-center gap-1.5 flex-wrap justify-center">
            <span>© 2026 macatung.dev • Crafted with</span>
            <button
              onClick={handleEasterEgg}
              className="text-rose-500 hover:scale-125 transition-transform"
              title="Click me for a midnight blessing!"
            >
              <Heart className="w-3.5 h-3.5 fill-rose-500" />
            </button>
            <span>&</span>
            <span className="text-amber-400 flex items-center gap-1">
              <Coffee className="w-3.5 h-3.5 inline" /> Dark Robusta
            </span>
          </div>

          {/* Socials */}
          <div className="flex items-center gap-4 text-slate-400">
            <a
              href="https://github.com/macatung"
              target="_blank"
              rel="noreferrer"
              onClick={() => sound.playClick()}
              className="hover:text-white transition-colors"
            >
              <GithubIcon className="w-4 h-4" />
            </a>
            <a
              href="https://linkedin.com"
              target="_blank"
              rel="noreferrer"
              onClick={() => sound.playClick()}
              className="hover:text-cyan-400 transition-colors"
            >
              <LinkedinIcon className="w-4 h-4" />
            </a>
            <a
              href="https://t.me"
              target="_blank"
              rel="noreferrer"
              onClick={() => sound.playClick()}
              className="hover:text-blue-400 transition-colors"
            >
              <MessageSquare className="w-4 h-4" />
            </a>
          </div>

        </div>

      </div>
    </footer>
  );
};
