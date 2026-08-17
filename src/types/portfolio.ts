export interface Project {
  id: string;
  title: string;
  tagline: string;
  description: string;
  category: 'fullstack' | 'creative' | 'ai-web3' | 'tools';
  coverGradient: string;
  tags: string[];
  techStack: string[];
  metrics: { label: string; value: string }[];
  liveUrl?: string;
  githubUrl?: string;
  featured?: boolean;
  architectureHighlights: string[];
  midnightFact: string;
}

export interface SkillCategory {
  title: string;
  iconName: string;
  badge: string;
  skills: {
    name: string;
    level: number; // 1-100
    rune: string;
    tag: string;
    description: string;
  }[];
}

export interface ExperienceItem {
  id: string;
  period: string;
  role: string;
  company: string;
  location: string;
  type: 'Full-time' | 'Contract' | 'Open Source' | 'Venture';
  summary: string;
  achievements: string[];
  technologies: string[];
  midnightQuest: string;
}

export interface DeveloperStat {
  label: string;
  value: string;
  unit?: string;
  iconName: string;
  description: string;
}

export interface TalismanPreset {
  id: string;
  title: string;
  runeTop: string;
  codeSnippet: string;
  meaning: string;
  colorScheme: 'yellow' | 'crimson' | 'cyan' | 'purple';
}
