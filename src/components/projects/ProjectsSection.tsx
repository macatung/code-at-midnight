import React, { useState } from 'react';
import { projectsData } from '../../data/projectsData';
import type { Project } from '../../types/portfolio';
import { ProjectModal } from './ProjectModal';
import { sound } from '../../audio/soundEffects';
import { Sparkles, ExternalLink, Eye, Layers } from 'lucide-react';
import { GithubIcon } from '../ui/Icons';

export const ProjectsSection: React.FC = () => {
  const [activeCategory, setActiveCategory] = useState<string>('all');
  const [selectedProject, setSelectedProject] = useState<Project | null>(null);

  const categories = [
    { id: 'all', label: 'All Artifacts' },
    { id: 'fullstack', label: 'Full-Stack Systems' },
    { id: 'creative', label: 'Creative UI / Web Audio' },
    { id: 'ai-web3', label: 'AI & Web3 Protocols' },
    { id: 'tools', label: 'Developer Tooling' },
  ];

  const filteredProjects = activeCategory === 'all'
    ? projectsData
    : projectsData.filter((p) => p.category === activeCategory);

  const handleOpenProject = (project: Project) => {
    sound.playTalisman();
    setSelectedProject(project);
  };

  return (
    <section id="projects" className="py-24 relative">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Section Header */}
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
          <div className="space-y-3">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border border-slate-700 text-xs font-mono text-emerald-300">
              <Layers className="w-3.5 h-3.5 text-talisman-yellow" />
              <span>// ARTIFACTS & SYSTEMS</span>
            </div>
            <h2 className="font-display font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
              The Midnight <span className="text-phantom-mint text-glow-mint">Grimoire</span>
            </h2>
            <p className="text-slate-400 font-mono text-sm sm:text-base max-w-xl">
              Những kiệt tác phần mềm và sản phẩm mã nguồn được đúc kết dưới ánh trăng nửa đêm.
            </p>
          </div>

          {/* Category Filter Pills */}
          <div className="flex flex-wrap gap-1.5 p-1 rounded-2xl glass-panel border border-slate-800">
            {categories.map((cat) => (
              <button
                key={cat.id}
                onClick={() => {
                  sound.playClick();
                  setActiveCategory(cat.id);
                }}
                className={`px-3.5 py-1.5 rounded-xl text-xs font-mono font-medium transition-all ${
                  activeCategory === cat.id
                    ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-glow-mint'
                    : 'text-slate-400 hover:text-slate-200'
                }`}
              >
                {cat.label}
              </button>
            ))}
          </div>
        </div>

        {/* Projects Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredProjects.map((project) => (
            <div
              key={project.id}
              className="rounded-3xl glass-panel border border-slate-800/80 hover:border-emerald-500/40 hover-card-glow flex flex-col justify-between overflow-hidden group transition-all duration-300"
            >
              <div>
                {/* Project Header Banner */}
                <div
                  className={`h-40 p-6 bg-gradient-to-br ${project.coverGradient} relative flex flex-col justify-between overflow-hidden`}
                >
                  <div className="flex items-center justify-between z-10">
                    <span className="px-2.5 py-0.5 rounded-md bg-black/50 backdrop-blur-sm text-[11px] font-mono uppercase tracking-wider text-emerald-300 border border-emerald-500/30">
                      {project.category}
                    </span>
                    {project.featured && (
                      <span className="px-2 py-0.5 rounded-md bg-talisman-yellow/20 backdrop-blur-sm text-[10px] font-mono uppercase font-bold text-talisman-yellow border border-talisman-yellow/40 flex items-center gap-1">
                        <Sparkles className="w-3 h-3" /> Featured
                      </span>
                    )}
                  </div>

                  <div className="z-10">
                    <h3 className="font-display font-bold text-xl text-white group-hover:text-phantom-mint transition-colors">
                      {project.title}
                    </h3>
                  </div>

                  {/* Corner Talisman Rune watermark */}
                  <div className="absolute -bottom-4 -right-4 font-mono text-5xl font-extrabold text-white/5 select-none pointer-events-none">
                    0xMID
                  </div>
                </div>

                {/* Content */}
                <div className="p-6 space-y-4">
                  <p className="text-slate-300 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                    {project.description}
                  </p>

                  {/* Metrics snippet */}
                  <div className="grid grid-cols-3 gap-2 py-2 border-y border-slate-800/80">
                    {project.metrics.map((m, idx) => (
                      <div key={idx} className="text-center">
                        <div className="text-xs font-display font-bold text-phantom-mint">{m.value}</div>
                        <div className="text-[10px] font-mono text-slate-500 truncate">{m.label}</div>
                      </div>
                    ))}
                  </div>

                  {/* Tech stack badges */}
                  <div className="flex flex-wrap gap-1.5">
                    {project.tags.slice(0, 4).map((tag) => (
                      <span
                        key={tag}
                        className="px-2 py-0.5 rounded-md bg-slate-900 border border-slate-800 text-[11px] font-mono text-slate-400"
                      >
                        {tag}
                      </span>
                    ))}
                  </div>
                </div>
              </div>

              {/* Card Footer Actions */}
              <div className="p-6 pt-0 flex items-center justify-between border-t border-slate-800/60 mt-4">
                <button
                  onClick={() => handleOpenProject(project)}
                  className="text-xs font-mono text-emerald-300 hover:text-emerald-200 font-bold flex items-center gap-1.5 group-hover:translate-x-1 transition-transform"
                >
                  <Eye className="w-3.5 h-3.5" />
                  <span>Inspect Details →</span>
                </button>

                <div className="flex items-center gap-2">
                  {project.githubUrl && (
                    <a
                      href={project.githubUrl}
                      target="_blank"
                      rel="noreferrer"
                      onClick={(e) => {
                        e.stopPropagation();
                        sound.playClick();
                      }}
                      className="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white transition-colors"
                      title="GitHub"
                    >
                      <GithubIcon className="w-3.5 h-3.5" />
                    </a>
                  )}
                  {project.liveUrl && (
                    <a
                      href={project.liveUrl}
                      target="_blank"
                      rel="noreferrer"
                      onClick={(e) => {
                        e.stopPropagation();
                        sound.playClick();
                      }}
                      className="p-2 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 transition-colors"
                      title="Live Demo"
                    >
                      <ExternalLink className="w-3.5 h-3.5" />
                    </a>
                  )}
                </div>
              </div>

            </div>
          ))}
        </div>

      </div>

      {/* Modal Popup */}
      <ProjectModal
        project={selectedProject}
        onClose={() => setSelectedProject(null)}
      />
    </section>
  );
};
