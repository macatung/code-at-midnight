import React, { useEffect } from 'react';
import type { Project } from '../../types/portfolio';
import { sound } from '../../audio/soundEffects';
import { X, ExternalLink, Sparkles, CheckCircle2, Moon, Activity, Cpu } from 'lucide-react';
import { GithubIcon } from '../ui/Icons';

interface ProjectModalProps {
  project: Project | null;
  onClose: () => void;
}

export const ProjectModal: React.FC<ProjectModalProps> = ({ project, onClose }) => {
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        sound.playClick();
        onClose();
      }
    };
    if (project) {
      window.addEventListener('keydown', handleKeyDown);
      document.body.style.overflow = 'hidden';
    }
    return () => {
      window.removeEventListener('keydown', handleKeyDown);
      document.body.style.overflow = 'auto';
    };
  }, [project, onClose]);

  if (!project) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-10 animate-fadeIn">
      {/* Backdrop */}
      <div
        className="fixed inset-0 bg-black/80 backdrop-blur-md transition-opacity"
        onClick={() => {
          sound.playClick();
          onClose();
        }}
      />

      {/* Modal Card */}
      <div className="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-3xl bg-midnight-900 border border-emerald-500/40 shadow-2xl shadow-emerald-500/10 z-10 text-slate-200">
        
        {/* Modal Header Banner */}
        <div className={`p-6 sm:p-8 bg-gradient-to-br ${project.coverGradient} border-b border-slate-800 relative`}>
          <button
            onClick={() => {
              sound.playClick();
              onClose();
            }}
            className="absolute top-4 right-4 p-2 rounded-full bg-black/50 hover:bg-black/80 text-slate-300 hover:text-white transition-colors border border-slate-700"
            aria-label="Close modal"
          >
            <X className="w-5 h-5" />
          </button>

          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-black/40 border border-emerald-500/30 text-xs font-mono text-emerald-300 mb-3">
            <Sparkles className="w-3.5 h-3.5 text-talisman-yellow" />
            <span className="uppercase">{project.category} Artifact</span>
          </div>

          <h3 className="text-2xl sm:text-4xl font-display font-extrabold text-white tracking-tight">
            {project.title}
          </h3>
          <p className="text-slate-300 font-mono text-sm sm:text-base mt-2">
            {project.tagline}
          </p>
        </div>

        {/* Modal Body */}
        <div className="p-6 sm:p-8 space-y-6">
          
          {/* Key Metrics */}
          <div className="grid grid-cols-3 gap-3">
            {project.metrics.map((metric) => (
              <div
                key={metric.label}
                className="p-3.5 rounded-2xl bg-midnight-950/80 border border-slate-800 text-center"
              >
                <div className="font-display font-bold text-lg sm:text-2xl text-phantom-mint">
                  {metric.value}
                </div>
                <div className="text-[11px] font-mono text-slate-400 mt-0.5">{metric.label}</div>
              </div>
            ))}
          </div>

          {/* Detailed Description */}
          <div className="space-y-2">
            <h4 className="text-xs font-mono uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
              <Activity className="w-4 h-4 text-cyan-400" />
              <span>Project Overview</span>
            </h4>
            <p className="text-slate-300 text-sm sm:text-base leading-relaxed">
              {project.description}
            </p>
          </div>

          {/* Architecture Highlights */}
          <div className="space-y-3">
            <h4 className="text-xs font-mono uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
              <Cpu className="w-4 h-4 text-purple-400" />
              <span>Architecture & Engineering Highlights</span>
            </h4>
            <div className="space-y-2">
              {project.architectureHighlights.map((highlight, idx) => (
                <div key={idx} className="flex items-start gap-2.5 text-xs sm:text-sm text-slate-300 font-mono">
                  <CheckCircle2 className="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5" />
                  <span>{highlight}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Tech Stack Pills */}
          <div className="space-y-2">
            <h4 className="text-xs font-mono uppercase tracking-wider text-slate-400">Enchanted Tech Stack</h4>
            <div className="flex flex-wrap gap-2">
              {project.techStack.map((tech) => (
                <span
                  key={tech}
                  className="px-3 py-1 rounded-xl bg-slate-800/80 border border-slate-700 text-xs font-mono text-emerald-300 font-semibold"
                >
                  {tech}
                </span>
              ))}
            </div>
          </div>

          {/* Midnight Fact */}
          <div className="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-3">
            <Moon className="w-5 h-5 text-talisman-yellow flex-shrink-0 mt-0.5" />
            <div className="text-xs sm:text-sm font-mono text-amber-200">
              <strong className="text-amber-100 font-bold block mb-0.5">🌙 Midnight Lore:</strong>
              {project.midnightFact}
            </div>
          </div>

          {/* Action Links */}
          <div className="flex flex-wrap items-center gap-3 pt-4 border-t border-slate-800">
            {project.liveUrl && (
              <a
                href={project.liveUrl}
                target="_blank"
                rel="noreferrer"
                onClick={() => sound.playClick()}
                className="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-midnight-950 font-bold font-mono text-xs sm:text-sm flex items-center gap-2 shadow-glow-mint hover:scale-105 transition-all"
              >
                <ExternalLink className="w-4 h-4" />
                <span>Live Interactive Demo</span>
              </a>
            )}

            {project.githubUrl && (
              <a
                href={project.githubUrl}
                target="_blank"
                rel="noreferrer"
                onClick={() => sound.playClick()}
                className="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-mono text-xs sm:text-sm flex items-center gap-2 border border-slate-700 transition-colors"
              >
                <GithubIcon className="w-4 h-4" />
                <span>View Source on GitHub</span>
              </a>
            )}
          </div>

        </div>

      </div>
    </div>
  );
};
