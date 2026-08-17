import React from 'react';
import { experienceData } from '../../data/experienceData';
import { sound } from '../../audio/soundEffects';
import { Briefcase, MapPin, CheckCircle2, Moon } from 'lucide-react';

export const ExperienceSection: React.FC = () => {
  return (
    <section id="experience" className="py-24 relative border-t border-slate-800/80">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border border-slate-700 text-xs font-mono text-emerald-300">
            <Briefcase className="w-3.5 h-3.5 text-talisman-yellow" />
            <span>// CAREER EXPEDITION</span>
          </div>
          <h2 className="font-display font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
            The Midnight <span className="text-phantom-mint text-glow-mint">Chronicles</span>
          </h2>
          <p className="text-slate-400 font-mono text-sm sm:text-base">
            Hành trình chinh phục các dự án quy mô lớn và giải quyết các bài toán hóc búa nhất.
          </p>
        </div>

        {/* Timeline */}
        <div className="relative border-l-2 border-slate-800 ml-4 sm:ml-32 space-y-12">
          {experienceData.map((item) => (
            <div
              key={item.id}
              onMouseEnter={() => sound.playTerminalKey()}
              className="relative pl-6 sm:pl-10 group"
            >
              {/* Timeline marker icon */}
              <div className="absolute -left-[17px] top-1.5 w-8 h-8 rounded-full bg-midnight-950 border-2 border-emerald-500 flex items-center justify-center shadow-glow-mint group-hover:scale-125 transition-transform">
                <div className="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse" />
              </div>

              {/* Period badge for desktop (left aligned) */}
              <div className="hidden sm:block absolute -left-36 top-2 font-mono text-xs text-talisman-yellow font-bold text-right w-24">
                {item.period}
              </div>

              {/* Experience Card */}
              <div className="p-6 sm:p-8 rounded-3xl glass-panel border border-slate-800/80 hover:border-emerald-500/40 hover-card-glow transition-all">
                
                {/* Header inside card */}
                <div className="flex flex-wrap items-center justify-between gap-2 mb-3">
                  <div>
                    {/* Period badge for mobile */}
                    <span className="sm:hidden inline-block font-mono text-xs text-talisman-yellow font-bold mb-1">
                      {item.period}
                    </span>
                    <h3 className="font-display font-bold text-xl sm:text-2xl text-white group-hover:text-phantom-mint transition-colors">
                      {item.role}
                    </h3>
                    <div className="flex flex-wrap items-center gap-3 text-xs font-mono text-slate-400 mt-1">
                      <span className="text-slate-200 font-semibold">{item.company}</span>
                      <span>•</span>
                      <span className="flex items-center gap-1">
                        <MapPin className="w-3 h-3 text-cyan-400" />
                        {item.location}
                      </span>
                    </div>
                  </div>

                  <span className="px-3 py-1 rounded-full text-[11px] font-mono uppercase font-bold bg-emerald-500/10 text-emerald-300 border border-emerald-500/30">
                    {item.type}
                  </span>
                </div>

                {/* Summary */}
                <p className="text-slate-300 text-xs sm:text-sm leading-relaxed mb-4">
                  {item.summary}
                </p>

                {/* Key Achievements */}
                <div className="space-y-2 mb-4">
                  {item.achievements.map((ach, idx) => (
                    <div key={idx} className="flex items-start gap-2.5 text-xs sm:text-sm text-slate-300 font-mono">
                      <CheckCircle2 className="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                      <span>{ach}</span>
                    </div>
                  ))}
                </div>

                {/* Technologies */}
                <div className="flex flex-wrap gap-1.5 mb-4">
                  {item.technologies.map((tech) => (
                    <span
                      key={tech}
                      className="px-2.5 py-0.5 rounded-lg bg-midnight-950 border border-slate-800 text-[11px] font-mono text-slate-400"
                    >
                      {tech}
                    </span>
                  ))}
                </div>

                {/* Midnight Quest Box */}
                <div className="p-3.5 rounded-2xl bg-midnight-950/90 border border-emerald-500/20 flex items-start gap-2.5 text-xs font-mono">
                  <Moon className="w-4 h-4 text-talisman-yellow flex-shrink-0 mt-0.5" />
                  <div>
                    <span className="text-emerald-300 font-bold">Midnight Quest Lore: </span>
                    <span className="text-slate-300">{item.midnightQuest}</span>
                  </div>
                </div>

              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
};
