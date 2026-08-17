import type { SkillCategory } from '../types/portfolio';

export const skillsData: SkillCategory[] = [
  {
    title: 'Frontend Sorcery & UI/UX',
    iconName: 'Layout',
    badge: 'Pixel Perfection',
    skills: [
      { name: 'React 18 / Next.js', level: 96, rune: '⚛️', tag: 'Core', description: 'Server components, hooks, concurrent rendering & performance optimization.' },
      { name: 'TypeScript', level: 94, rune: '📘', tag: 'Type-Safe', description: 'Strict typing, generic witchcraft, AST transformations, and zero runtime surprises.' },
      { name: 'TailwindCSS & CSS3', level: 95, rune: '🎨', tag: 'Styling', description: 'Custom design systems, fluid layouts, container queries, animations, glassmorphism.' },
      { name: 'Three.js & Canvas VFX', level: 82, rune: '🔮', tag: 'Creative', description: '3D shaders, WebGL particle systems, kinetic typography & interactive physics.' },
      { name: 'Web Audio API', level: 88, rune: '🎵', tag: 'Audio', description: 'Bespoke procedural sound design, frequency synthesizers & dynamic acoustic feedback.' },
    ],
  },
  {
    title: 'Backend Alchemy & Systems',
    iconName: 'Server',
    badge: 'Sub-Millisecond',
    skills: [
      { name: 'Node.js / Bun / Deno', level: 92, rune: '🟢', tag: 'Runtime', description: 'Asynchronous event loops, worker threads, streaming protocols & microservices.' },
      { name: 'Go (Golang)', level: 89, rune: '🐹', tag: 'High-Perf', description: 'Goroutine concurrency, high-throughput microservices, memory layout optimization.' },
      { name: 'Python (FastAPI / AI)', level: 86, rune: '🐍', tag: 'AI/Backend', description: 'High-speed async APIs, LangChain agents, data pipelines & ML model wrappers.' },
      { name: 'PostgreSQL & Timescale', level: 90, rune: '🐘', tag: 'Database', description: 'Complex CTEs, indexing strategies, time-series partitioning, pgvector embeddings.' },
      { name: 'Redis & Message Queues', level: 93, rune: '⚡', tag: 'Caching', description: 'In-memory caching, pub/sub channels, distributed locks, BullMQ job queues.' },
    ],
  },
  {
    title: 'Cloud Rituals & DevOps',
    iconName: 'Cloud',
    badge: '99.99% Uptime',
    skills: [
      { name: 'Docker & Kubernetes', level: 88, rune: '🐳', tag: 'Containers', description: 'Multi-stage builds, Helm charts, auto-scaling clusters, zero-downtime rollouts.' },
      { name: 'AWS & Cloudflare Edge', level: 87, rune: '☁️', tag: 'Infrastructure', description: 'Workers, serverless lambdas, S3 storage, edge caching & CDN rule tuning.' },
      { name: 'CI/CD & Git Witchcraft', level: 94, rune: '🐙', tag: 'Automation', description: 'GitHub Actions, automated mutation testing, semantic releases, preview deploys.' },
      { name: 'Monitoring & Grafana', level: 85, rune: '📊', tag: 'Observability', description: 'Prometheus metrics, OpenTelemetry distributed tracing, synthetic midnight alerts.' },
    ],
  },
  {
    title: 'Dark Arts, AI & Architecture',
    iconName: 'Sparkles',
    badge: 'Midnight Flow',
    skills: [
      { name: 'System Architecture', level: 93, rune: '🏛️', tag: 'Design', description: 'Event-driven systems, domain-driven design, CQRS, fault-tolerant topologies.' },
      { name: 'LLM Agents & RAG', level: 89, rune: '🧠', tag: 'GenAI', description: 'Function calling, embeddings retrieval, context window management, local models.' },
      { name: 'Performance Profiling', level: 91, rune: '⏱️', tag: 'Optimization', description: 'Flamegraphs, memory leak exorcism, bundle footprint pruning, Web Vitals.' },
      { name: 'Midnight Coffee Brewing', level: 100, rune: '☕', tag: 'Secret Lore', description: 'Converting dark roasted Robusta into 10,000 lines of bug-free code.' },
    ],
  },
];
