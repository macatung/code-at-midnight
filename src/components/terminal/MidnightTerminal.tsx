import React, { useState, useRef, useEffect } from 'react';
import { sound } from '../../audio/soundEffects';
import confetti from 'canvas-confetti';
import { Terminal as TermIcon, CornerDownLeft, Maximize2, Minimize2, Copy, Check } from 'lucide-react';

interface TerminalLine {
  type: 'input' | 'output' | 'error' | 'success' | 'ascii';
  text: string;
}

export const MidnightTerminal: React.FC = () => {
  const [input, setInput] = useState<string>('');
  const [history, setHistory] = useState<string[]>([]);
  const [historyIndex, setHistoryIndex] = useState<number>(-1);
  const [copied, setCopied] = useState<boolean>(false);
  const [isExpanded, setIsExpanded] = useState<boolean>(false);

  const [lines, setLines] = useState<TerminalLine[]>([
    { type: 'output', text: '╔══════════════════════════════════════════════════════════════╗' },
    { type: 'output', text: '║   🔮 MACATUNG-CLI v2.4.0 [NOCTURNE PROTOCOL: ACTIVE]         ║' },
    { type: 'output', text: '║   Slogan: "Code at midnight" | Domain: macatung.dev          ║' },
    { type: 'output', text: '╚══════════════════════════════════════════════════════════════╝' },
    { type: 'output', text: 'Type "help" to summon available commands.' },
  ]);

  const terminalEndRef = useRef<HTMLDivElement | null>(null);
  const inputRef = useRef<HTMLInputElement | null>(null);

  useEffect(() => {
    terminalEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [lines]);

  const handleCommand = (cmd: string) => {
    const trimmed = cmd.trim();
    if (!trimmed) return;

    sound.playTerminalKey();
    setHistory((prev) => [...prev, trimmed]);
    setHistoryIndex(-1);

    const newLines: TerminalLine[] = [...lines, { type: 'input', text: `$ ${trimmed}` }];
    const lower = trimmed.toLowerCase();

    switch (lower) {
      case 'help':
        newLines.push(
          { type: 'output', text: '⚡ AVAILABLE MIDNIGHT COMMANDS:' },
          { type: 'output', text: '  • whoami / bio      : Learn about Ma Cà Tưng' },
          { type: 'output', text: '  • projects / ls     : Explore the grimoire artifacts' },
          { type: 'output', text: '  • skills            : Inspect tech runes & stack' },
          { type: 'output', text: '  • hop               : Trigger a mascot jump' },
          { type: 'output', text: '  • coffee            : Brew a virtual cup of Vietnamese Robusta' },
          { type: 'output', text: '  • talisman          : Summon an ASCII developer talisman' },
          { type: 'output', text: '  • slogan            : Echo the midnight motto' },
          { type: 'output', text: '  • summon / contact  : Transmit a signal to Ma Cà Tưng' },
          { type: 'output', text: '  • sudo rm -rf bugs  : Exorcise all bugs from production' },
          { type: 'output', text: '  • clear             : Cleanse the terminal display' }
        );
        break;

      case 'whoami':
      case 'bio':
        newLines.push(
          { type: 'success', text: '👤 NAME: Ma Cà Tưng (The Hopping Vampire Developer)' },
          { type: 'output', text: '🌙 ALIAS: Night-Crawler & Lead Full-Stack Sorcerer' },
          { type: 'output', text: '📍 DOMAIN: macatung.dev' },
          { type: 'output', text: '⚡ FOCUS: High-performance web apps, zero-latency architectures & pixel-perfect UI/UX.' },
          { type: 'output', text: '☕ FUEL: Dark Roast Vietnamese Robusta (Strength: 999%)' }
        );
        break;

      case 'projects':
      case 'ls':
      case 'ls projects':
        newLines.push(
          { type: 'output', text: '📜 ARTIFACTS IN GRIMOIRE:' },
          { type: 'output', text: '  [1] Nocturne OS         - Cloud micro-workspace (React, Rust WASM)' },
          { type: 'output', text: '  [2] Phantom Flow        - DeFi liquidity engine (Go, Solidity, GraphQL)' },
          { type: 'output', text: '  [3] Grimoire UI         - Physics talisman design system (React 18)' },
          { type: 'output', text: '  [4] Spectral AI Agents  - Autonomous test synthesizer (Python, AST)' },
          { type: 'output', text: '  [5] Kitsune KV          - Ephemeral zero-copy store (Rust Tokio)' },
          { type: 'output', text: '  [6] macatung.dev v2.0   - The official midnight portfolio' },
          { type: 'output', text: 'Tip: Scroll to #projects section to inspect interactive modals.' }
        );
        break;

      case 'skills':
        newLines.push(
          { type: 'output', text: '🔮 TECH STACK MATRIX:' },
          { type: 'output', text: '  [Frontend] : React 18, Next.js, TypeScript, TailwindCSS, Three.js, Web Audio API' },
          { type: 'output', text: '  [Backend]  : Node.js, Go (Golang), Python FastAPI, PostgreSQL, Redis' },
          { type: 'output', text: '  [DevOps]   : Docker, Kubernetes, AWS, Cloudflare Workers, GitHub Actions' },
          { type: 'output', text: '  [Secret]   : Midnight Coffee Brewing (100% Mastery)' }
        );
        break;

      case 'hop':
        sound.playHop(1.5);
        newLines.push(
          { type: 'success', text: '🦘 *TƯNG TƯNG TƯNG!* Ma Cà Tưng vừa nhảy lên 1 bước cao vút!' },
          { type: 'output', text: '   +1 Hop point added to your ledger!' }
        );
        break;

      case 'coffee':
        sound.playTalisman();
        newLines.push(
          { type: 'ascii', text: '      (  )   (   )  )\n     ) (   )  (  (\n     ( )  (    ) )\n     _____________\n    <_____________> ___\n    |             |/ _ \\\n    |  ROBUSTA   | | | |\n    |   DARK     | |_| |\n    |_____________|\\___/\n    \\___________/' },
          { type: 'success', text: '☕ Ly cà phê Robusta đen đậm đặc 90°C đã sẵn sàng. Trí não hồi phục 100% năng lượng!' }
        );
        break;

      case 'talisman':
        sound.playTalisman();
        newLines.push(
          { type: 'ascii', text: '    .--------------------.\n    |  // ZERO_BUG_PROTO |\n    |--------------------|\n    |  const safe = true;|\n    |  while (coding) {  |\n    |    shipPerfection()|\n    |  }                 |\n    |--------------------|\n    |  ⚡ MACATUNG.DEV ⚡ |\n    \'--------------------\'' },
          { type: 'success', text: '📜 Bùa 0 Bug đã kích hoạt! Bảo vệ bạn khỏi mọi cú sập server.' }
        );
        break;

      case 'slogan':
        newLines.push(
          { type: 'success', text: '🌙 "Code at midnight."' },
          { type: 'output', text: 'When the world is fast asleep, the true creators awaken.' }
        );
        break;

      case 'summon':
      case 'contact':
        newLines.push(
          { type: 'output', text: '📬 TRANSMISSION CHANNELS:' },
          { type: 'output', text: '  • Email    : summon@macatung.dev' },
          { type: 'output', text: '  • GitHub   : github.com/macatung' },
          { type: 'output', text: '  • Telegram : @macatung_dev' },
          { type: 'output', text: 'Scroll down to #contact to cast a direct signal.' }
        );
        break;

      case 'sudo rm -rf bugs':
      case 'rm -rf bugs':
        sound.playSuccess();
        confetti({ particleCount: 50, spread: 70, origin: { y: 0.6 } });
        newLines.push(
          { type: 'success', text: '✨ PURGE IN PROGRESS...' },
          { type: 'output', text: '[OK] Banishing 4,192 null pointer exceptions into the void.' },
          { type: 'output', text: '[OK] Purging race conditions from memory.' },
          { type: 'success', text: '🎉 SUCCESS: 0 bugs remaining. Production is completely clean!' }
        );
        break;

      case 'clear':
        setLines([]);
        setInput('');
        return;

      default:
        newLines.push({
          type: 'error',
          text: `zsh: command not found: "${trimmed}". Type "help" for a list of commands.`,
        });
        break;
    }

    setLines(newLines);
    setInput('');
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter') {
      handleCommand(input);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (history.length > 0) {
        const nextIndex = historyIndex === -1 ? history.length - 1 : Math.max(0, historyIndex - 1);
        setHistoryIndex(nextIndex);
        setInput(history[nextIndex]);
      }
    } else if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (historyIndex !== -1) {
        const nextIndex = historyIndex + 1;
        if (nextIndex < history.length) {
          setHistoryIndex(nextIndex);
          setInput(history[nextIndex]);
        } else {
          setHistoryIndex(-1);
          setInput('');
        }
      }
    }
  };

  const copyTerminalOutput = () => {
    const fullText = lines.map((l) => l.text).join('\n');
    navigator.clipboard.writeText(fullText);
    setCopied(true);
    sound.playClick();
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <section id="terminal" className="py-24 relative border-t border-slate-800/60">
      <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto mb-12 space-y-3">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border border-slate-800 text-xs font-mono text-emerald-300">
            <TermIcon className="w-3.5 h-3.5 text-talisman-yellow" />
            <span>// REPL PLAYGROUND</span>
          </div>
          <h2 className="font-display font-extrabold text-3xl sm:text-4xl text-white tracking-tight">
            macatung<span className="text-phantom-mint text-glow-mint">-cli</span>
          </h2>
          <p className="text-slate-400 font-mono text-xs sm:text-sm">
            Trải nghiệm terminal tương tác trực tiếp. Gõ <code className="text-emerald-300 font-bold">help</code> hoặc <code className="text-talisman-yellow font-bold">coffee</code> để khám phá!
          </p>
        </div>

        {/* Terminal Window Box */}
        <div
          className={`rounded-3xl bg-midnight-950 border border-slate-800 shadow-2xl overflow-hidden transition-all duration-300 ${
            isExpanded ? 'max-h-[700px]' : 'max-h-[500px]'
          } flex flex-col`}
        >
          {/* Terminal Title Bar */}
          <div className="px-4 py-3 bg-midnight-900 border-b border-slate-800 flex items-center justify-between">
            <div className="flex items-center gap-2">
              <div className="w-3 h-3 rounded-full bg-slate-700 hover:opacity-100 cursor-pointer" />
              <div className="w-3 h-3 rounded-full bg-slate-700 hover:opacity-100 cursor-pointer" />
              <div className="w-3 h-3 rounded-full bg-emerald-500/80 hover:opacity-100 cursor-pointer" />
              <span className="ml-2 text-xs font-mono text-slate-400 flex items-center gap-1.5">
                <TermIcon className="w-3.5 h-3.5 text-emerald-400" />
                <span>macatung@midnight-sanctuary: ~ (zsh)</span>
              </span>
            </div>

            <div className="flex items-center gap-2">
              <button
                onClick={copyTerminalOutput}
                className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors"
                title="Copy Terminal Logs"
              >
                {copied ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
              </button>
              <button
                onClick={() => {
                  sound.playClick();
                  setIsExpanded(!isExpanded);
                }}
                className="p-1.5 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors"
                title={isExpanded ? 'Thu nhỏ' : 'Phóng to'}
              >
                {isExpanded ? <Minimize2 className="w-3.5 h-3.5" /> : <Maximize2 className="w-3.5 h-3.5" />}
              </button>
            </div>
          </div>

          {/* Terminal Output Area */}
          <div
            className="p-5 overflow-y-auto font-mono text-xs sm:text-sm space-y-1.5 text-slate-300 flex-1 leading-relaxed cursor-text"
            onClick={() => inputRef.current?.focus()}
          >
            {lines.map((line, idx) => {
              if (line.type === 'input') {
                return (
                  <div key={idx} className="text-phantom-mint font-bold pt-1">
                    {line.text}
                  </div>
                );
              }
              if (line.type === 'error') {
                return (
                  <div key={idx} className="text-rose-400">
                    {line.text}
                  </div>
                );
              }
              if (line.type === 'success') {
                return (
                  <div key={idx} className="text-emerald-300 font-semibold">
                    {line.text}
                  </div>
                );
              }
              if (line.type === 'ascii') {
                return (
                  <pre key={idx} className="text-amber-300 font-mono text-[11px] leading-tight select-none">
                    {line.text}
                  </pre>
                );
              }
              return (
                <div key={idx} className="text-slate-300">
                  {line.text}
                </div>
              );
            })}

            {/* Input Row */}
            <div className="flex items-center gap-2 pt-2 text-emerald-400">
              <span className="font-bold select-none">$</span>
              <input
                ref={inputRef}
                type="text"
                value={input}
                onChange={(e) => setInput(e.target.value)}
                onKeyDown={handleKeyDown}
                placeholder="type a command... (e.g. coffee, hop, projects)"
                className="flex-1 bg-transparent text-slate-100 placeholder:text-slate-600 focus:outline-none font-mono text-xs sm:text-sm"
                autoComplete="off"
                spellCheck="false"
              />
              <button
                onClick={() => handleCommand(input)}
                className="p-1 rounded bg-slate-800 text-slate-400 hover:text-emerald-300"
                title="Execute"
              >
                <CornerDownLeft className="w-3.5 h-3.5" />
              </button>
            </div>

            <div ref={terminalEndRef} />
          </div>

          {/* Terminal Quick Hint Bar */}
          <div className="px-4 py-2 bg-midnight-900/60 border-t border-slate-800/80 flex flex-wrap items-center gap-2 text-[11px] font-mono text-slate-500">
            <span className="text-slate-400 font-semibold">Quick click:</span>
            {['help', 'whoami', 'coffee', 'hop', 'projects', 'sudo rm -rf bugs'].map((c) => (
              <button
                key={c}
                onClick={() => handleCommand(c)}
                className="px-2 py-0.5 rounded bg-slate-800/80 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 transition-colors border border-slate-700/50"
              >
                {c}
              </button>
            ))}
          </div>

        </div>

      </div>
    </section>
  );
};
