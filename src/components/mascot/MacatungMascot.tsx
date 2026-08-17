import React, { useState, useEffect } from 'react';
import { sound } from '../../audio/soundEffects';
import confetti from 'canvas-confetti';
import { Coffee, Zap, Moon, Sparkles, Trophy } from 'lucide-react';

interface MacatungMascotProps {
  size?: 'sm' | 'md' | 'lg' | 'hero';
  showControls?: boolean;
  onHopCountChange?: (count: number) => void;
}

type Mood = 'normal' | 'caffeine' | 'sleepy' | 'rage';

export const MacatungMascot: React.FC<MacatungMascotProps> = ({
  size = 'hero',
  showControls = true,
  onHopCountChange,
}) => {
  const [hopCount, setHopCount] = useState<number>(0);
  const [isJumping, setIsJumping] = useState<boolean>(false);
  const [mood, setMood] = useState<Mood>('normal');
  const [speechBubble, setSpeechBubble] = useState<string>('Code at midnight...');
  const [isHovered, setIsHovered] = useState<boolean>(false);

  const quotes = [
    'Code at midnight... lúc cả thế giới đang ngủ 🌙',
    'Tưng tưng tưng! 1 ly cà phê là 1000 dòng code ☕',
    'Compile: 0 errors, 0 warnings. Tuyệt hảo! ✨',
    'Ma Cà Tưng đang canh gác server production 🛡️',
    'Đêm càng khuya, trí não càng bay bổng 🚀',
    'Bùa 0 Bug đã được kích hoạt! Vạn sự êm đềm ⚡',
    'Bug nào dám lộng hành trước 12:00 AM? 🔥',
  ];

  useEffect(() => {
    const savedHops = localStorage.getItem('macatung_hop_counter');
    if (savedHops) {
      setHopCount(parseInt(savedHops, 10));
    }
  }, []);

  const handleHopClick = () => {
    if (isJumping) return;

    setIsJumping(true);
    const newCount = hopCount + 1;
    setHopCount(newCount);
    localStorage.setItem('macatung_hop_counter', String(newCount));
    if (onHopCountChange) onHopCountChange(newCount);

    // Audio
    sound.playHop(mood === 'caffeine' ? 1.4 : 1.0);

    // Random quote
    const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
    setSpeechBubble(randomQuote);

    // Confetti on milestones (every 10 hops)
    if (newCount % 10 === 0) {
      sound.playSuccess();
      confetti({
        particleCount: 45,
        spread: 60,
        origin: { y: 0.7 },
        colors: ['#00f5a0', '#ffd166', '#00d2ff'],
      });
      setSpeechBubble(`🎉 XUẤT SẮC! Đạt ${newCount} cú nhảy Ma Cà Tưng!`);
    }

    setTimeout(() => {
      setIsJumping(false);
    }, 450);
  };

  // Dimensions
  const dimensions = {
    sm: { width: 80, height: 95 },
    md: { width: 140, height: 165 },
    lg: { width: 220, height: 260 },
    hero: { width: 280, height: 330 },
  }[size];

  // Animation speed based on mood
  const hopClass = isJumping
    ? 'animate-none -translate-y-12 scale-y-110 transition-transform duration-200 ease-out'
    : mood === 'caffeine'
    ? 'animate-hop-fast'
    : mood === 'sleepy'
    ? 'animate-float-slow'
    : 'animate-hop';

  // Eye color based on mood
  const eyeColor = {
    normal: '#00f5d4',
    caffeine: '#ffd166',
    sleepy: '#8b5cf6',
    rage: '#ff4d6d',
  }[mood];

  const talismanText = {
    normal: '0 BUG',
    caffeine: 'COFFEE',
    sleepy: '4:00 AM',
    rage: 'DEPLOY',
  }[mood];

  return (
    <div className="relative flex flex-col items-center select-none group">
      {/* Speech Bubble */}
      {size !== 'sm' && (
        <div
          className={`mb-3 px-4 py-2 rounded-2xl glass-panel border border-slate-700/80 text-xs md:text-sm font-mono text-emerald-300 max-w-[280px] md:max-w-[320px] text-center shadow-lg transition-all duration-300 ${
            isHovered || isJumping ? 'scale-105 opacity-100 border-emerald-500/40' : 'opacity-90'
          }`}
        >
          <span className="inline-block animate-pulse mr-1.5 text-talisman-yellow">💬</span>
          {speechBubble}
        </div>
      )}

      {/* Interactive Mascot Stage */}
      <div
        className="relative cursor-pointer flex flex-col items-center justify-center p-2 focus:outline-none"
        onClick={handleHopClick}
        onMouseEnter={() => setIsHovered(true)}
        onMouseLeave={() => setIsHovered(false)}
        role="button"
        tabIndex={0}
        aria-label="Click Ma Cà Tưng to hop!"
        onKeyDown={(e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            handleHopClick();
          }
        }}
      >
        {/* Subtle Glow Halo behind character (toned down) */}
        <div
          className={`absolute w-36 h-36 rounded-full blur-3xl transition-opacity duration-500 pointer-events-none ${
            mood === 'caffeine'
              ? 'bg-amber-500/15 opacity-50'
              : mood === 'rage'
              ? 'bg-rose-500/15 opacity-50'
              : mood === 'sleepy'
              ? 'bg-purple-500/15 opacity-40'
              : 'bg-emerald-500/15 opacity-50'
          }`}
        />

        {/* Mascot SVG */}
        <div className={`transition-all duration-300 ${hopClass}`}>
          <svg
            width={dimensions.width}
            height={dimensions.height}
            viewBox="0 0 240 280"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            className="filter drop-shadow-[0_12px_24px_rgba(0,0,0,0.6)]"
          >
            <defs>
              {/* Robe Gradient */}
              <linearGradient id="robeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stopColor="#111724" />
                <stop offset="50%" stopColor="#0a0e17" />
                <stop offset="100%" stopColor="#060910" />
              </linearGradient>

              {/* Hat Gradient */}
              <linearGradient id="hatGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stopColor="#1e293b" />
                <stop offset="100%" stopColor="#0f172a" />
              </linearGradient>

              {/* Talisman Paper Gradient */}
              <linearGradient id="talismanGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stopColor="#ffe57f" />
                <stop offset="50%" stopColor="#ffd166" />
                <stop offset="100%" stopColor="#f4b41a" />
              </linearGradient>

              {/* Neon Jade Trim */}
              <linearGradient id="neonTrim" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stopColor="#00f5a0" />
                <stop offset="100%" stopColor="#00d2ff" />
              </linearGradient>

              {/* Ghost Skin */}
              <radialGradient id="ghostSkin" cx="50%" cy="40%" r="60%">
                <stop offset="0%" stopColor="#d8f3dc" />
                <stop offset="75%" stopColor="#b7e4c7" />
                <stop offset="100%" stopColor="#95d5b2" />
              </radialGradient>
            </defs>

            {/* --- MASCOT BODY --- */}

            {/* Outstretched Jiangshi Arms (Hopping posture) */}
            <g className="animate-talisman-flutter origin-center">
              {/* Left Arm & Sleeve */}
              <path
                d="M75 145 C45 145, 30 148, 18 152 C14 153, 12 165, 18 168 C35 174, 55 172, 75 168 Z"
                fill="url(#robeGrad)"
                stroke="#00f5a0"
                strokeWidth="1.5"
              />
              {/* Left Hand / Glove */}
              <circle cx="16" cy="160" r="10" fill="#1e293b" stroke="#00f5d4" strokeWidth="1.5" />
              <path d="M12 155 L6 158 M12 160 L5 162 M12 165 L6 166" stroke="#00f5d4" strokeWidth="1.5" strokeLinecap="round" />

              {/* Right Arm & Sleeve */}
              <path
                d="M165 145 C195 145, 210 148, 222 152 C226 153, 228 165, 222 168 C205 174, 185 172, 165 168 Z"
                fill="url(#robeGrad)"
                stroke="#00f5a0"
                strokeWidth="1.5"
              />
              {/* Right Hand / Glove */}
              <circle cx="224" cy="160" r="10" fill="#1e293b" stroke="#00f5d4" strokeWidth="1.5" />
              <path d="M228 155 L234 158 M228 160 L235 162 M228 165 L234 166" stroke="#00f5d4" strokeWidth="1.5" strokeLinecap="round" />
            </g>

            {/* Main Cyber Jiangshi Robe / Hoodie */}
            <path
              d="M75 130 C75 130, 95 125, 120 125 C145 125, 165 130, 165 130 L180 235 C180 242, 172 248, 160 248 L80 248 C68 248, 60 242, 60 235 Z"
              fill="url(#robeGrad)"
              stroke="rgba(0, 245, 160, 0.4)"
              strokeWidth="2"
            />

            {/* Chest Badge */}
            <path
              d="M100 135 L120 160 L140 135"
              fill="none"
              stroke="url(#neonTrim)"
              strokeWidth="2"
              strokeLinecap="round"
            />
            {/* Hexagon Core Rune */}
            <polygon
              points="120,165 132,172 132,186 120,193 108,186 108,172"
              fill="#060910"
              stroke="#ffd166"
              strokeWidth="1.5"
            />
            <text x="120" y="182" textAnchor="middle" fill="#00f5a0" fontSize="10" fontFamily="monospace" fontWeight="bold">
              {'{ }'}
            </text>

            {/* Cute Hopping Feet */}
            <ellipse cx="98" cy="248" rx="14" ry="7" fill="#0f172a" stroke="#00f5a0" strokeWidth="1.5" />
            <ellipse cx="142" cy="248" rx="14" ry="7" fill="#0f172a" stroke="#00f5a0" strokeWidth="1.5" />

            {/* --- MASCOT HEAD & FACE --- */}

            {/* Cute Pale Jiangshi Head */}
            <circle
              cx="120"
              cy="95"
              r="48"
              fill="url(#ghostSkin)"
              stroke="#00f5a0"
              strokeWidth="2"
            />

            {/* Modern Gaming Headphones over Ears */}
            <path d="M68 95 C68 62, 172 62, 172 95" fill="none" stroke="#334155" strokeWidth="7" strokeLinecap="round" />
            {/* Headphone Ear Cups */}
            <rect x="64" y="80" width="12" height="30" rx="6" fill="#0f172a" stroke="#00f5d4" strokeWidth="1.5" />
            <rect x="164" y="80" width="12" height="30" rx="6" fill="#0f172a" stroke="#00f5d4" strokeWidth="1.5" />

            {/* Jiangshi Official Hat */}
            <g>
              {/* Hat Crown */}
              <path
                d="M78 68 C80 32, 160 32, 162 68 Z"
                fill="url(#hatGrad)"
                stroke="#ffd166"
                strokeWidth="2"
              />
              {/* Hat Brim */}
              <ellipse
                cx="120"
                cy="68"
                rx="52"
                ry="14"
                fill="#1e293b"
                stroke="#ffd166"
                strokeWidth="2"
              />
              {/* Golden Jewel on Hat */}
              <circle cx="120" cy="50" r="6" fill="#ef233c" stroke="#ffd166" strokeWidth="1.5" />
              {/* Cyber Antenna */}
              <line x1="120" y1="44" x2="120" y2="24" stroke="#00f5a0" strokeWidth="2.5" strokeLinecap="round" />
              <circle cx="120" cy="22" r="3.5" fill="#00f5a0" />
            </g>

            {/* Cute Blushing Cheeks */}
            <ellipse cx="92" cy="115" rx="7" ry="4" fill="#ff4d6d" opacity="0.35" />
            <ellipse cx="148" cy="115" rx="7" ry="4" fill="#ff4d6d" opacity="0.35" />

            {/* Glowing Expressive Eyes */}
            {mood === 'sleepy' ? (
              <g stroke={eyeColor} strokeWidth="3" strokeLinecap="round" fill="none">
                <path d="M96 102 Q105 108 114 102" />
                <path d="M126 102 Q135 108 144 102" />
              </g>
            ) : mood === 'rage' ? (
              <g>
                <polygon points="95,96 112,102 96,105" fill={eyeColor} />
                <polygon points="145,96 128,102 144,105" fill={eyeColor} />
              </g>
            ) : (
              <g>
                <circle cx="102" cy="102" r="7" fill={eyeColor} />
                <circle cx="100" cy="100" r="2.5" fill="#ffffff" />
                <circle cx="138" cy="102" r="7" fill={eyeColor} />
                <circle cx="136" cy="100" r="2.5" fill="#ffffff" />
              </g>
            )}

            {/* Cute Mouth with Fangs */}
            <path
              d="M112 120 Q120 128 128 120"
              stroke="#0f172a"
              strokeWidth="2.5"
              fill="none"
              strokeLinecap="round"
            />
            <polygon points="114,120 117,125 119,120" fill="#ffffff" />
            <polygon points="121,120 123,125 126,120" fill="#ffffff" />

            {/* --- THE TALISMAN (Clean Code / Tech Glyph - NO CHINESE CHARACTERS) --- */}
            <g className="animate-talisman-flutter origin-top">
              {/* Talisman Paper */}
              <rect
                x="105"
                y="55"
                width="30"
                height="62"
                rx="3"
                fill="url(#talismanGrad)"
                stroke="#c9182b"
                strokeWidth="1.5"
                className="filter drop-shadow-[0_4px_8px_rgba(0,0,0,0.3)]"
              />

              {/* Red Tech Seal at Top (Code Icon '</>') */}
              <circle cx="120" cy="65" r="5" fill="#c9182b" />
              <text x="120" y="68" textAnchor="middle" fill="#ffe57f" fontSize="5" fontFamily="monospace" fontWeight="bold">
                &lt;/&gt;
              </text>

              {/* Decorative Tech Lines */}
              <line x1="112" y1="74" x2="128" y2="74" stroke="#c9182b" strokeWidth="1.5" strokeLinecap="round" />
              <line x1="114" y1="78" x2="126" y2="78" stroke="#c9182b" strokeWidth="1.5" strokeLinecap="round" />

              {/* Code Glyph on Talisman */}
              <text
                x="120"
                y="92"
                textAnchor="middle"
                fill="#8f0e1d"
                fontSize="6.5"
                fontFamily="monospace"
                fontWeight="900"
              >
                {talismanText}
              </text>

              {/* Tech Rune Circuit at Bottom */}
              <path
                d="M112 98 L120 102 L128 98 M120 102 L120 108"
                stroke="#c9182b"
                strokeWidth="1.2"
                fill="none"
                strokeLinecap="round"
              />
            </g>

            {/* Floating Coffee Icon */}
            {mood === 'caffeine' && (
              <g className="animate-bounce" transform="translate(170, 90)">
                <circle cx="15" cy="15" r="14" fill="#070b14" stroke="#ffd166" strokeWidth="1.5" />
                <text x="15" y="21" textAnchor="middle" fontSize="14">☕</text>
              </g>
            )}
          </svg>
        </div>

        {/* Dynamic Shadow underneath */}
        <div
          className={`w-32 h-4 rounded-full bg-black/70 blur-md transition-all duration-300 ${
            isJumping ? 'scale-50 opacity-20' : 'scale-100 opacity-70'
          }`}
        />

        {/* Click hint pill */}
        <div className="mt-2 text-[11px] font-mono text-slate-400 group-hover:text-phantom-mint flex items-center gap-1 transition-colors">
          <Sparkles className="w-3 h-3 text-talisman-yellow" />
          <span>Click to Hop! ({hopCount} hops)</span>
        </div>
      </div>

      {/* Mood Selector Controls */}
      {showControls && (
        <div className="mt-3 flex items-center gap-1.5 p-1 rounded-xl glass-panel border border-slate-800 shadow-lg">
          <button
            onClick={() => {
              setMood('normal');
              sound.playClick();
              setSpeechBubble('Ma Cà Tưng: Trạng thái tiêu chuẩn, sẵn sàng code!');
            }}
            className={`px-2.5 py-1 rounded-lg text-xs font-mono flex items-center gap-1 transition-all ${
              mood === 'normal'
                ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'
                : 'text-slate-400 hover:text-slate-200'
            }`}
            title="Normal Mode"
          >
            <Zap className="w-3 h-3" />
            <span>Normal</span>
          </button>

          <button
            onClick={() => {
              setMood('caffeine');
              sound.playHop(1.5);
              setSpeechBubble('100% Robusta! Nhịp tim 180, tốc độ gõ 200 WPM! ☕⚡');
            }}
            className={`px-2.5 py-1 rounded-lg text-xs font-mono flex items-center gap-1 transition-all ${
              mood === 'caffeine'
                ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40'
                : 'text-slate-400 hover:text-slate-200'
            }`}
            title="Caffeine Overdrive Mode"
          >
            <Coffee className="w-3 h-3" />
            <span>Caffeine</span>
          </button>

          <button
            onClick={() => {
              setMood('sleepy');
              sound.playClick();
              setSpeechBubble('4:00 AM rồi... nhưng còn 1 pull request phải merge 🥱');
            }}
            className={`px-2.5 py-1 rounded-lg text-xs font-mono flex items-center gap-1 transition-all ${
              mood === 'sleepy'
                ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40'
                : 'text-slate-400 hover:text-slate-200'
            }`}
            title="4:00 AM Sleepy Mode"
          >
            <Moon className="w-3 h-3" />
            <span>4 AM</span>
          </button>

          <button
            onClick={() => {
              setMood('rage');
              sound.playHop(1.8);
              setSpeechBubble('🔥 EMERGENCY DEPLOY! Server down lúc nửa đêm!');
            }}
            className={`px-2.5 py-1 rounded-lg text-xs font-mono flex items-center gap-1 transition-all ${
              mood === 'rage'
                ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40'
                : 'text-slate-400 hover:text-slate-200'
            }`}
            title="Blood Moon Rage"
          >
            <Trophy className="w-3 h-3" />
            <span>Deploy</span>
          </button>
        </div>
      )}
    </div>
  );
};
