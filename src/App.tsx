import React from 'react';
import { TalismanCanvas } from './components/mascot/TalismanCanvas';
import { Navbar } from './components/layout/Navbar';
import { HeroSection } from './components/hero/HeroSection';
import { AboutSection } from './components/about/AboutSection';
import { ProjectsSection } from './components/projects/ProjectsSection';
import { SkillsSection } from './components/skills/SkillsSection';
import { ExperienceSection } from './components/experience/ExperienceSection';
import { MidnightTerminal } from './components/terminal/MidnightTerminal';
import { TalismanGenerator } from './components/talisman/TalismanGenerator';
import { ContactSection } from './components/contact/ContactSection';
import { Footer } from './components/layout/Footer';

export const App: React.FC = () => {
  return (
    <div className="min-h-screen bg-midnight-950 text-slate-100 relative selection:bg-phantom-mint selection:text-midnight-950">
      {/* Background Floating Talisman & Firefly Particles Canvas */}
      <TalismanCanvas />

      {/* Cyber Grid Background Pattern Overlay */}
      <div className="fixed inset-0 bg-grid-pattern opacity-60 pointer-events-none z-0" />

      {/* Main Page Layout */}
      <div className="relative z-10 flex flex-col min-h-screen">
        {/* Sticky Header Navbar with live clock & sound controller */}
        <Navbar />

        {/* Main Content Sections */}
        <main className="flex-grow">
          <HeroSection />
          <AboutSection />
          <ProjectsSection />
          <SkillsSection />
          <ExperienceSection />
          <MidnightTerminal />
          <TalismanGenerator />
          <ContactSection />
        </main>

        {/* Footer with Hop-to-Top and Easter Eggs */}
        <Footer />
      </div>
    </div>
  );
};

export default App;
