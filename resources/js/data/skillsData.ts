export interface ArchitectureCapability {
  name: string;
  rune: string;
  tag: string;
  description: string;
}

export interface ArchitectureCategory {
  title: string;
  iconName: string;
  badge: string;
  skills: ArchitectureCapability[];
}

export type SkillCategory = ArchitectureCategory;
export type SkillItem = ArchitectureCapability;

export const skillsData: ArchitectureCategory[] = [
  {
    title: 'AI Agents & LLM Architecture',
    iconName: 'Sparkles',
    badge: 'Autonomous Systems',
    skills: [
      { name: 'Multi-Agent Orchestration', rune: '🤖', tag: 'Core AI', description: 'Autonomous agents, router agents, human-in-the-loop workflows & task delegation.' },
      { name: 'Tool & Function Calling', rune: '⚡', tag: 'Automation', description: 'Real-time database queries, API ERP integration, automated refunding & ticket solving.' },
      { name: 'RAG & Vector Databases', rune: '🧠', tag: 'GenAI', description: 'Semantic search, Qdrant / PgVector embeddings, knowledge graph retrieval & token optimization.' },
      { name: 'Google Gemini & OpenAI APIs', rune: '🔮', tag: 'LLM APIs', description: 'Interactions API, Live API, prompt engineering, structured JSON outputs & safety guards.' },
      { name: 'Python (FastAPI / LangChain)', rune: '🐍', tag: 'AI Backend', description: 'Async AI microservices, LlamaIndex data pipelines & ML prediction wrappers.' },
    ],
  },
  {
    title: 'Backend Mastery & Distributed Systems',
    iconName: 'Server',
    badge: 'High-Throughput',
    skills: [
      { name: 'PHP 8.3+ & Laravel 11/12', rune: '🐘', tag: 'Expert', description: 'Complex domain logic, Inertia fullstack, Eloquent optimization & microservices architecture.' },
      { name: 'Filament Admin 3 & Livewire 3', rune: '⚡', tag: 'Admin UI', description: 'Rapid enterprise dashboards, dynamic tables, custom form widgets & role permissions.' },
      { name: 'Redis Caching & Atomic Locks', rune: '⚡', tag: 'Realtime', description: 'Distributed locks, rate-limiting, pub/sub channels & high-speed session states.' },
      { name: 'RabbitMQ & Message Queues', rune: '🐇', tag: 'Queue', description: 'Asynchronous event streaming, dead-letter exchanges & worker load balancing.' },
      { name: 'PostgreSQL & MySQL Database Design', rune: '🗄️', tag: 'Database', description: 'Complex indexing strategies, partition tables, query optimization & ACID transactions.' },
    ],
  },
  {
    title: 'Telecom, GIS & Network Systems',
    iconName: 'Layout',
    badge: 'Specialized Infra',
    skills: [
      { name: 'GIS & Spatial Data (QGIS)', rune: '🗺️', tag: 'Spatial', description: 'Digital mapping of nationwide fiber infrastructure, PostGIS spatial queries & route optimization.' },
      { name: 'NMS & Telecom Protocols', rune: '📡', tag: 'Protocols', description: 'SNMP, Telnet, SSH equipment monitoring, SDH/DWDM performance metrics & auto-discovery.' },
      { name: 'Elasticsearch & Log Analytics', rune: '🔍', tag: 'Big Data', description: 'High-volume log indexing, real-time anomaly detection & telemetry aggregation.' },
      { name: 'Video Transcoding & Streaming', rune: '🎬', tag: 'Streaming', description: 'FFmpeg hardware acceleration, HLS/DASH adaptive bitrate streaming & CDN caching.' },
    ],
  },
  {
    title: 'Frontend Sorcery, Cloud & DevOps',
    iconName: 'Cloud',
    badge: '99.99% Uptime',
    skills: [
      { name: 'Vue 3 & ReactJS / React Native', rune: '⚛️', tag: 'Frontend', description: 'Composition API, state management (Pinia), mobile apps & responsive interfaces.' },
      { name: 'TypeScript Strict & Modern CSS', rune: '📘', tag: 'Type-Safe', description: 'Zero runtime errors, fluid TailwindCSS design systems & glassmorphism aesthetics.' },
      { name: 'Docker & Multi-Stage Builds', rune: '🐳', tag: 'Containers', description: 'Alpine production images, docker-compose orchestration & isolated microservices.' },
      { name: 'CI/CD GitHub Actions & Cloudflare', rune: '🌐', tag: 'DevOps', description: 'Automated testing pipelines, CDN rule caching, DDoS protection & zero-downtime deploys.' },
    ],
  },
];
