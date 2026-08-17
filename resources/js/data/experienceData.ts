import type { ExperienceItem, DeveloperStat } from '../types/portfolio';

export const experienceData: ExperienceItem[] = [
  {
    id: 'lead-midnight-architect',
    period: '2024 — Present',
    role: 'Lead Full-Stack & Creative Systems Architect',
    company: 'Nocturne Labs / Stealth',
    location: 'Remote (GMT+7 Midnight Realm)',
    type: 'Full-time',
    summary: 'Architecting ultra-low latency real-time web applications, AI-integrated workflows, and micro-frontend design systems.',
    achievements: [
      'Engineered a distributed collaborative editor scaling to 100k+ concurrent night owls with p99 sync latency < 25ms.',
      'Designed and published open-source UI libraries with over 60k weekly downloads across the developer community.',
      'Reduced cloud infrastructure expenditure by 38% through intelligent edge compute routing and Rust WASM optimization.'
    ],
    technologies: ['Vue 3', 'Laravel 11', 'TypeScript', 'Go', 'Rust WASM', 'Docker', 'WebSockets', 'TailwindCSS'],
    midnightQuest: 'Exorcised a phantom race condition in a distributed queue that only manifested during lunar eclipses (and daylight savings).'
  },
  {
    id: 'senior-fullstack-engineer',
    period: '2022 — 2024',
    role: 'Senior Full-Stack Engineer',
    company: 'Aetheria Cloud Matrix',
    location: 'Hybrid / Ho Chi Minh City',
    type: 'Full-time',
    summary: 'Spearheaded frontend and API modernization for high-throughput enterprise SaaS platform serving global fintech clients.',
    achievements: [
      'Migrated legacy monolithic UI to modern modular architecture, accelerating page initial loads by 64%.',
      'Established automated end-to-end testing pipeline improving deploy confidence from 82% to 99.8%.',
      'Mentored 8 junior and mid-level engineers in type systems, clean architecture, and performance benchmarking.'
    ],
    technologies: ['Vue 3 / React', 'Node.js / PHP', 'PostgreSQL', 'Redis', 'AWS', 'GraphQL', 'Jest/Playwright'],
    midnightQuest: 'Rebuilt the core analytics dashboard in a single night after discovering a 500ms render bottleneck.'
  },
  {
    id: 'creative-developer-contract',
    period: '2020 — 2022',
    role: 'Creative Frontend & UI/UX Specialist',
    company: 'Vortex Interactive Studios',
    location: 'Remote',
    type: 'Contract',
    summary: 'Created award-winning 3D web experiences, dynamic interactive landing pages, and bespoke brand web apps for tech startups.',
    achievements: [
      'Delivered 14 bespoke interactive marketing sites, earning 3 Awwwards / FWA nominations.',
      'Implemented Web Audio interactive soundscapes and GPU-accelerated canvas shaders.',
      'Maintained a strict 60 FPS performance benchmark across mobile and low-tier devices.'
    ],
    technologies: ['Three.js', 'Canvas 2D', 'Vue', 'GSAP', 'WebGL', 'Web Audio API'],
    midnightQuest: 'Synthesized 8-bit chip tunes live in browser memory using raw mathematical sine wave equations.'
  },
  {
    id: 'indie-hacker-origins',
    period: '2018 — 2020',
    role: 'Night Crawler & Indie Software Hacker',
    company: 'The Midnight Lair',
    location: 'Earth',
    type: 'Open Source',
    summary: 'Built open-source developer tooling, CLI utilities, and niche web games while learning the deep secrets of the web runtime.',
    achievements: [
      'Authored 10+ developer productivity CLI tools with 15k+ total stars and active open-source contributors.',
      'Mastered the alchemy of turning coffee into pull requests at 3:00 AM.'
    ],
    technologies: ['JavaScript', 'TypeScript', 'Node.js', 'Linux', 'Git', 'Vim'],
    midnightQuest: 'Spawned the legend of "Ma Cà Tưng" — the hopping vampire developer who codes when the moon is high.'
  }
];

export const developerStats: DeveloperStat[] = [
  {
    label: 'Midnight Coffees Brewed',
    value: '2,840+',
    unit: 'Cups',
    iconName: 'Coffee',
    description: '100% Dark Vietnamese Robusta fuel'
  },
  {
    label: 'Bugs Exorcised',
    value: '4,192',
    unit: 'Squashed',
    iconName: 'Bug',
    description: 'Banished into the void forever'
  },
  {
    label: 'Hop Velocity',
    value: '60',
    unit: 'FPS',
    iconName: 'Zap',
    description: 'Silky smooth frame rendering'
  },
  {
    label: 'Code Shipped at 12AM',
    value: '99.8%',
    unit: 'Deploy',
    iconName: 'Moon',
    description: 'When the city sleeps, we deploy'
  }
];
