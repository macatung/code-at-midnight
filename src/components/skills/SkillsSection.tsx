import React, { useState } from 'react';
import { skillsData } from '../../data/skillsData';
import { sound } from '../../audio/soundEffects';
import { Code2, Layout, Server, Cloud, Sparkles, CheckCircle } from 'lucide-react';

export const SkillsSection: React.FC = () => {
  const [activeCategoryIndex, setActiveCategoryIndex] = useState<number>(0);

  const categoryIcons = [Layout, Server, Cloud, Sparkles];

  const activeCategory = skillsData[activeCategoryIndex];

  return (
    <section id="skills" className="py-24 relative border-t border-slate-800/80 bg-midnight-950/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border border-slate-700 text-xs font-mono text-emerald-300">
            <Code2 className="w-3.5 h-3.5 text-talisman-yellow" />
            <span>// MYSTIC ARSENAL</span>
          </div>
          <h2 className="font-display font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
            Enchanted Tech <span className="text-phantom-mint text-glow-mint">Runes</span>
          </h2>
          <p className="text-slate-400 font-mono text-sm sm:text-base">
            Bộ công cụ và thần chú lập trình được mài giũa qua hàng nghìn giờ code đêm.
          </p>
        </div>

        {/* Category Selector Tabs */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-3 mb-10">
          {skillsData.map((cat, idx) => {
            const Icon = categoryIcons[idx] || Code2;
            const isActive = idx === activeCategoryIndex;
            return (
              <button
                key={cat.title}
                onClick={() => {
                  sound.playClick();
                  setActiveCategoryIndex(idx);
                }}
                className={`p-4 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between ${
                  isActive
                    ? 'bg-midnight-900 border-emerald-500/50 shadow-glow-mint'
                    : 'glass-panel border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white'
                }`}
              >
                <div className="flex items-center justify-between mb-3">
                  <div
                    className={`p-2 rounded-xl ${
                      isActive ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-900 text-slate-400'
                    }`}
                  >
                    <Icon className="w-5 h-5" />
                  </div>
                  <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-500">
                    0{idx + 1}
                  </span>
                </div>
                <div>
                  <h3 className={`font-display font-bold text-sm ${isActive ? 'text-white' : 'text-slate-300'}`}>
                    {cat.title}
                  </h3>
                  <span className="text-[11px] font-mono text-talisman-yellow">{cat.badge}</span>
                </div>
              </button>
            );
          })}
        </div>

        {/* Active Category Skills List */}
        <div className="rounded-3xl glass-panel-glow border border-emerald-500/30 p-6 sm:p-8">
          <div className="flex items-center justify-between border-b border-slate-800 pb-4 mb-6">
            <h3 className="font-display font-bold text-xl text-white flex items-center gap-2">
              <span className="text-phantom-mint">⚡</span>
              <span>{activeCategory.title}</span>
            </h3>
            <span className="text-xs font-mono text-slate-400 hidden sm:inline-block">
              Proficiency Benchmark: Production Tested
            </span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {activeCategory.skills.map((skill) => (
              <div
                key={skill.name}
                onMouseEnter={() => sound.playTerminalKey()}
                className="p-5 rounded-2xl bg-midnight-900/90 border border-slate-800/80 hover:border-emerald-500/40 hover-card-glow transition-all"
              >
                <div className="flex items-center justify-between mb-2">
                  <div className="flex items-center gap-2.5">
                    <span className="text-xl" role="img" aria-label={skill.name}>
                      {skill.rune}
                    </span>
                    <div>
                      <h4 className="font-display font-bold text-white text-base">{skill.name}</h4>
                      <span className="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                        {skill.tag}
                      </span>
                    </div>
                  </div>
                  <div className="text-right">
                    <span className="font-mono font-bold text-sm text-phantom-mint">{skill.level}%</span>
                  </div>
                </div>

                {/* Progress bar */}
                <div className="w-full bg-slate-950 rounded-full h-2 overflow-hidden border border-slate-800 my-3">
                  <div
                    className="bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400 h-2 rounded-full transition-all duration-700 ease-out"
                    style={{ width: `${skill.level}%` }}
                  />
                </div>

                <p className="text-xs font-mono text-slate-400 leading-relaxed">
                  {skill.description}
                </p>
              </div>
            ))}
          </div>

          {/* Guarantee Badge */}
          <div className="mt-8 p-4 rounded-2xl bg-midnight-950/80 border border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-mono">
            <div className="flex items-center gap-2 text-slate-300">
              <CheckCircle className="w-4 h-4 text-emerald-400 flex-shrink-0" />
              <span>Tất cả kỹ năng đều được kiểm chứng qua dự án thực tế với traffic cao & uptime 99.99%.</span>
            </div>
            <span className="text-talisman-yellow font-bold uppercase tracking-wider text-[11px] whitespace-nowrap">
              ✦ Verified by Midnight Grimoire
            </span>
          </div>

        </div>

      </div>
    </section>
  );
};
