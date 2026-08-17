import React, { useState } from 'react';
import { developerStats } from '../../data/experienceData';
import { sound } from '../../audio/soundEffects';
import { Moon, Sun, Coffee, Zap, Shield, Bug, Sparkles, Code } from 'lucide-react';

export const AboutSection: React.FC = () => {
  const [activeTab, setActiveTab] = useState<'manifesto' | 'day_vs_night' | 'values'>('manifesto');

  const statIcons: Record<string, React.ReactNode> = {
    Coffee: <Coffee className="w-5 h-5 text-amber-400" />,
    Bug: <Bug className="w-5 h-5 text-rose-400" />,
    Zap: <Zap className="w-5 h-5 text-phantom-mint" />,
    Moon: <Moon className="w-5 h-5 text-talisman-yellow" />,
  };

  return (
    <section id="about" className="py-24 relative border-t border-slate-800/80 bg-midnight-950/40">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border border-slate-700 text-xs font-mono text-emerald-300">
            <Sparkles className="w-3.5 h-3.5 text-talisman-yellow" />
            <span>// ORIGIN & PHILOSOPHY</span>
          </div>
          <h2 className="font-display font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
            The Midnight Developer <span className="text-phantom-mint text-glow-mint">Manifesto</span>
          </h2>
          <p className="text-slate-400 text-sm sm:text-base font-mono">
            Vũ trụ kỳ bí của Ma Cà Tưng: Biến cà phê nửa đêm thành những dòng mã hoàn mỹ.
          </p>
        </div>

        {/* Developer Stats Grid */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-16">
          {developerStats.map((stat) => (
            <div
              key={stat.label}
              onMouseEnter={() => sound.playTerminalKey()}
              className="p-6 rounded-2xl glass-panel border border-slate-800 hover-card-glow flex flex-col justify-between group"
            >
              <div className="flex items-center justify-between mb-4">
                <div className="p-2.5 rounded-xl bg-slate-900 border border-slate-700/80 group-hover:border-emerald-500/50 transition-colors">
                  {statIcons[stat.iconName]}
                </div>
                {stat.unit && (
                  <span className="text-[11px] font-mono font-bold text-slate-500 uppercase px-2 py-0.5 rounded bg-slate-900/50">
                    {stat.unit}
                  </span>
                )}
              </div>
              <div>
                <h3 className="text-2xl sm:text-3xl font-display font-bold text-white group-hover:text-phantom-mint transition-colors">
                  {stat.value}
                </h3>
                <p className="text-xs font-mono font-semibold text-slate-300 mt-1">{stat.label}</p>
                <p className="text-[11px] text-slate-500 mt-1">{stat.description}</p>
              </div>
            </div>
          ))}
        </div>

        {/* Tabbed Interactive Philosophy Container */}
        <div className="rounded-3xl glass-panel-glow border border-slate-800 p-6 sm:p-8">
          
          {/* Tabs */}
          <div className="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-4 mb-8">
            <button
              onClick={() => {
                setActiveTab('manifesto');
                sound.playClick();
              }}
              className={`px-4 py-2 rounded-xl text-xs sm:text-sm font-mono font-semibold transition-all flex items-center gap-2 ${
                activeTab === 'manifesto'
                  ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-glow-mint'
                  : 'text-slate-400 hover:text-slate-200'
              }`}
            >
              <Moon className="w-4 h-4" />
              <span>1. Triết Lý 00:00 AM</span>
            </button>

            <button
              onClick={() => {
                setActiveTab('day_vs_night');
                sound.playClick();
              }}
              className={`px-4 py-2 rounded-xl text-xs sm:text-sm font-mono font-semibold transition-all flex items-center gap-2 ${
                activeTab === 'day_vs_night'
                  ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40'
                  : 'text-slate-400 hover:text-slate-200'
              }`}
            >
              <Sun className="w-4 h-4" />
              <span>2. Day vs Midnight Mode</span>
            </button>

            <button
              onClick={() => {
                setActiveTab('values');
                sound.playClick();
              }}
              className={`px-4 py-2 rounded-xl text-xs sm:text-sm font-mono font-semibold transition-all flex items-center gap-2 ${
                activeTab === 'values'
                  ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40'
                  : 'text-slate-400 hover:text-slate-200'
              }`}
            >
              <Code className="w-4 h-4" />
              <span>3. Khắc Bùa Chất Lượng</span>
            </button>
          </div>

          {/* Tab 1: Manifesto */}
          {activeTab === 'manifesto' && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8 items-center animate-fadeIn">
              <div className="space-y-4">
                <h3 className="font-display font-bold text-2xl text-white flex items-center gap-2">
                  <span>Tại sao lại là</span>
                  <span className="text-talisman-yellow italic">"Code at midnight"?</span>
                </h3>
                <p className="text-slate-300 text-sm sm:text-base leading-relaxed">
                  Khi đồng hồ điểm 12 giờ đêm, thế giới xung quanh dần chìm vào giấc ngủ. Tiếng thông báo tắt lịm, không còn những cuộc họp bất chợt hay tin nhắn gián đoạn. Đó là khoảnh khắc vàng để trí tuệ đạt trạng thái <strong>Deep Flow</strong> — nơi mọi bài toán hóc búa nhất về kiến trúc hệ thống và UI/UX được giải quyết chỉ sau một ngụm cà phê.
                </p>
                <p className="text-slate-300 text-sm sm:text-base leading-relaxed">
                  Giống như chú <strong>Ma Cà Tưng</strong> năng động với lá bùa trên trán, tôi biến từng đêm thành một buổi luyện kim mã nguồn: viết code tinh gọn, tối ưu từng mili-giây, và tạo ra những sản phẩm mang lại niềm hứng khởi bất tận cho người dùng.
                </p>
              </div>

              <div className="p-6 rounded-2xl bg-midnight-900/90 border border-slate-800 font-mono text-xs text-slate-300 space-y-2">
                <div className="flex items-center justify-between text-slate-500 border-b border-slate-800 pb-2">
                  <span>// midnight_protocol.ts</span>
                  <span className="text-emerald-400">● RUNNING</span>
                </div>
                <pre className="text-emerald-300 leading-relaxed overflow-x-auto">
{`const midnightProtocol = async () => {
  const world = await Universe.sleep();
  const coffee = RobustaDark.brew({ temp: 92, strength: 'MAX' });
  
  while (isNight && coffee.level > 0) {
    const feature = await Architect.craft({
      quality: 'PIXEL_PERFECT',
      latency: 'SUB_MILLISECOND',
      tests: '100% COVERAGE',
      bugs: 0
    });
    
    await Git.push({ branch: 'production', safe: true });
    console.log('⚡ Shipped at 03:30 AM with 0 bugs!');
  }
};`}
                </pre>
              </div>
            </div>
          )}

          {/* Tab 2: Day vs Night Mode Comparison */}
          {activeTab === 'day_vs_night' && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fadeIn">
              {/* Daytime */}
              <div className="p-6 rounded-2xl bg-slate-900/60 border border-amber-500/20 space-y-4">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2 text-amber-400 font-bold font-mono text-sm">
                    <Sun className="w-4 h-4" />
                    <span>BAN NGÀY (DAYLIGHT REALM)</span>
                  </div>
                  <span className="text-xs font-mono text-slate-500">09:00 - 17:00</span>
                </div>
                <ul className="space-y-2.5 text-xs sm:text-sm text-slate-400 font-mono">
                  <li className="flex items-start gap-2">
                    <span className="text-rose-400">✕</span>
                    <span>14 cuộc họp ngắn "sync 5 phút" kéo dài 45 phút</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <span className="text-rose-400">✕</span>
                    <span>68 tin nhắn Slack / Teams hỏi "Ủa sao tính năng này..."</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <span className="text-rose-400">✕</span>
                    <span>Context switching liên tục, khó tập trung suy nghĩ sâu</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <span className="text-rose-400">✕</span>
                    <span>Hiệu suất lập trình thực tế: ~35%</span>
                  </li>
                </ul>
              </div>

              {/* Midnight Mode */}
              <div className="p-6 rounded-2xl bg-midnight-900 border border-emerald-500/40 shadow-glow-mint space-y-4">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-2 text-emerald-300 font-bold font-mono text-sm">
                    <Moon className="w-4 h-4 text-talisman-yellow" />
                    <span>NỬA ĐÊM (MIDNIGHT MODE)</span>
                  </div>
                  <span className="text-xs font-mono text-emerald-400 font-bold">00:00 - 05:00</span>
                </div>
                <ul className="space-y-2.5 text-xs sm:text-sm text-slate-200 font-mono">
                  <li className="flex items-start gap-2">
                    <span className="text-emerald-400 font-bold">✓</span>
                    <span>0 cuộc họp, 0 gián đoạn, hoàn toàn tĩnh lặng</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <span className="text-emerald-400 font-bold">✓</span>
                    <span>1 cốc cà phê Robusta đậm đặc + playlist Lofi / Synthwave</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <span className="text-emerald-400 font-bold">✓</span>
                    <span>Deep Flow State: Giải quyết xong module phức tạp trong 2 tiếng</span>
                  </li>
                  <li className="flex items-start gap-2">
                    <span className="text-emerald-400 font-bold">✓</span>
                    <span>Hiệu suất lập trình: 1000% bứt phá</span>
                  </li>
                </ul>
              </div>
            </div>
          )}

          {/* Tab 3: Core Values */}
          {activeTab === 'values' && (
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 animate-fadeIn">
              <div className="p-5 rounded-2xl bg-midnight-900/80 border border-slate-800 space-y-3">
                <div className="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                  <Shield className="w-5 h-5" />
                </div>
                <h4 className="font-display font-bold text-white text-base">Type Safety & Resilience</h4>
                <p className="text-xs text-slate-400 leading-relaxed font-mono">
                  Không để bug lọt vào production. TypeScript nghiêm ngặt, unit tests chặt chẽ và error handling chuẩn xác như bùa trừ tà.
                </p>
              </div>

              <div className="p-5 rounded-2xl bg-midnight-900/80 border border-slate-800 space-y-3">
                <div className="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                  <Zap className="w-5 h-5" />
                </div>
                <h4 className="font-display font-bold text-white text-base">Sub-millisecond Speed</h4>
                <p className="text-xs text-slate-400 leading-relaxed font-mono">
                  Tối ưu hóa từng byte dữ liệu. Render 60 FPS mượt mà, cold-start dưới 20ms, cấu trúc dữ liệu không lãng phí bộ nhớ.
                </p>
              </div>

              <div className="p-5 rounded-2xl bg-midnight-900/80 border border-slate-800 space-y-3">
                <div className="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                  <Sparkles className="w-5 h-5" />
                </div>
                <h4 className="font-display font-bold text-white text-base">Bespoke Aesthetics</h4>
                <p className="text-xs text-slate-400 leading-relaxed font-mono">
                  Giao diện không nhàm chán. Tỉ mỉ từng vi chuyển động (micro-animations), âm thanh tương tác và trải nghiệm người dùng quyến rũ.
                </p>
              </div>
            </div>
          )}

        </div>

      </div>
    </section>
  );
};
