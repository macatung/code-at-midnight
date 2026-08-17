import React, { useState, useEffect } from 'react';
import { sound } from '../../audio/soundEffects';
import { SoundToggle } from './SoundToggle';
import { MidnightClock } from '../mascot/MidnightClock';
import { Terminal, Sparkles, Menu, X, Code2, Layers, Compass, Mail } from 'lucide-react';

export const Navbar: React.FC = () => {
  const [scrolled, setScrolled] = useState<boolean>(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState<boolean>(false);

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navItems = [
    { label: 'Origin', href: '#about', icon: Compass },
    { label: 'Projects', href: '#projects', icon: Layers },
    { label: 'Skills', href: '#skills', icon: Code2 },
    { label: 'Experience', href: '#experience', icon: Sparkles },
    { label: 'Terminal', href: '#terminal', icon: Terminal },
    { label: 'Bùa Dev', href: '#talisman-gen', icon: Sparkles },
    { label: 'Summon Me', href: '#contact', icon: Mail, primary: true },
  ];

  const handleNavClick = (href: string) => {
    sound.playClick();
    setMobileMenuOpen(false);
    const element = document.querySelector(href);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <header
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        scrolled
          ? 'py-3 bg-midnight-950/80 backdrop-blur-xl border-b border-slate-800/80 shadow-2xl shadow-black/60'
          : 'py-5 bg-transparent'
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between gap-4">
          {/* Logo & Slogan */}
          <a
            href="#"
            onClick={(e) => {
              e.preventDefault();
              sound.playHop();
              window.scrollTo({ top: 0, behavior: 'smooth' });
            }}
            className="flex items-center gap-3 group focus:outline-none"
          >
            {/* Mini Hopping Mascot Icon */}
            <div className="relative w-10 h-10 rounded-xl bg-midnight-900 border border-emerald-500/40 p-1 flex items-center justify-center group-hover:border-phantom-mint transition-all shadow-glow-mint overflow-hidden">
              <svg
                viewBox="0 0 100 110"
                className="w-full h-full animate-hop group-hover:scale-110 transition-transform"
                fill="none"
              >
                {/* Hat */}
                <path d="M25 28 C25 12 75 12 75 28 L85 36 L15 36 Z" fill="#1e293b" stroke="#ffd166" strokeWidth="3" />
                <circle cx="50" cy="22" r="5" fill="#e63946" stroke="#ffd166" strokeWidth="2" />
                <line x1="50" y1="17" x2="50" y2="4" stroke="#00f5a0" strokeWidth="3" strokeLinecap="round" />
                {/* Head */}
                <circle cx="50" cy="62" r="32" fill="#d8f3dc" stroke="#00f5a0" strokeWidth="3" />
                {/* Eyes */}
                <circle cx="38" cy="60" r="5" fill="#00f5d4" />
                <circle cx="62" cy="60" r="5" fill="#00f5d4" />
                <circle cx="36.5" cy="58.5" r="1.5" fill="#ffffff" />
                <circle cx="60.5" cy="58.5" r="1.5" fill="#ffffff" />
                {/* Cheeks */}
                <ellipse cx="32" cy="70" rx="4" ry="2.5" fill="#ff0054" opacity="0.5" />
                <ellipse cx="68" cy="70" rx="4" ry="2.5" fill="#ff0054" opacity="0.5" />
                {/* Fangs Mouth */}
                <path d="M44 72 Q50 78 56 72" stroke="#0f172a" strokeWidth="2.5" fill="none" strokeLinecap="round" />
                {/* Talisman on Head */}
                <rect x="42" y="32" width="16" height="34" rx="2" fill="#ffd166" stroke="#c9182b" strokeWidth="1.5" />
                <circle cx="50" cy="38" r="3" fill="#c9182b" />
                <text x="50" y="55" textAnchor="middle" fill="#8f0e1d" fontSize="6.5" fontFamily="monospace" fontWeight="bold">0BUG</text>
              </svg>
            </div>

            <div>
              <div className="flex items-center gap-2">
                <span className="font-display font-bold text-lg tracking-tight text-white group-hover:text-phantom-mint transition-colors">
                  macatung<span className="text-phantom-mint">.dev</span>
                </span>
                <span className="px-1.5 py-0.5 rounded text-[10px] font-mono uppercase font-extrabold bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 hidden sm:inline-block">
                  v2.0
                </span>
              </div>
              <p className="text-[11px] font-mono text-slate-400 -mt-0.5 tracking-wide flex items-center gap-1">
                <span className="text-talisman-yellow">⚡</span>
                <span className="italic">Code at midnight</span>
              </p>
            </div>
          </a>

          {/* Desktop Nav Links */}
          <nav className="hidden lg:flex items-center gap-1 xl:gap-2">
            {navItems.map((item) => {
              const Icon = item.icon;
              return (
                <button
                  key={item.label}
                  onClick={() => handleNavClick(item.href)}
                  className={`px-3 py-1.5 rounded-xl text-xs font-mono transition-all duration-200 flex items-center gap-1.5 ${
                    item.primary
                      ? 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-midnight-950 font-bold shadow-glow-mint ml-2'
                      : 'text-slate-300 hover:text-white hover:bg-slate-800/60'
                  }`}
                >
                  <Icon className={`w-3.5 h-3.5 ${item.primary ? 'text-midnight-950' : 'text-slate-400'}`} />
                  <span>{item.label}</span>
                </button>
              );
            })}
          </nav>

          {/* Right utilities: Midnight Clock & Sound FX */}
          <div className="flex items-center gap-2 sm:gap-3">
            <MidnightClock />
            <SoundToggle />

            {/* Mobile Menu Button */}
            <button
              onClick={() => {
                sound.playClick();
                setMobileMenuOpen(!mobileMenuOpen);
              }}
              className="lg:hidden p-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 hover:text-white"
              aria-label="Open Mobile Menu"
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Drawer */}
      {mobileMenuOpen && (
        <div className="lg:hidden mt-3 px-4 pt-2 pb-6 bg-midnight-950/95 backdrop-blur-2xl border-b border-slate-800 animate-fadeIn">
          <div className="flex flex-col gap-2">
            {navItems.map((item) => {
              const Icon = item.icon;
              return (
                <button
                  key={item.label}
                  onClick={() => handleNavClick(item.href)}
                  className={`w-full text-left px-4 py-3 rounded-xl text-sm font-mono flex items-center justify-between ${
                    item.primary
                      ? 'bg-emerald-500 text-midnight-950 font-bold shadow-glow-mint'
                      : 'bg-slate-900/50 text-slate-200 hover:bg-slate-800'
                  }`}
                >
                  <div className="flex items-center gap-2.5">
                    <Icon className="w-4 h-4" />
                    <span>{item.label}</span>
                  </div>
                  <span className="text-xs text-slate-500 font-mono">→</span>
                </button>
              );
            })}
          </div>
        </div>
      )}
    </header>
  );
};
