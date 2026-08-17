import type { Project } from '../types/portfolio';

export const projectsData: Project[] = [
  {
    id: 'midnight-terminal-os',
    title: 'Nocturne OS — Cloud Micro-Workspace',
    tagline: 'Browser-based ambient midnight IDE with real-time collaborative execution',
    description: 'An ultra-low-latency web terminal and distributed code sandbox designed specifically for night developers. Features WebAssembly-powered runtime, live audio synthesis ambient waves, and P2P pairing.',
    category: 'fullstack',
    coverGradient: 'from-emerald-950 via-teal-900 to-slate-950',
    tags: ['WebAssembly', 'Next.js', 'Rust', 'WebSockets', 'TailwindCSS'],
    techStack: ['React', 'TypeScript', 'Rust (WASM)', 'Node.js', 'Docker', 'Redis'],
    metrics: [
      { label: 'Active Night Owls', value: '42.5k+' },
      { label: 'Cold-Start Latency', value: '< 18ms' },
      { label: 'WASM Speedup', value: '3.8x' },
    ],
    liveUrl: 'https://macatung.dev/demo/nocturne',
    githubUrl: 'https://github.com/macatung/nocturne-os',
    featured: true,
    architectureHighlights: [
      'Multi-tenant container orchestration powered by lightweight Firecracker microVMs',
      'Real-time CRDT synchronization for seamless multi-cursor pair programming',
      'Custom Web Audio synthesizer that generates binaural focus frequencies'
    ],
    midnightFact: 'Built over 14 straight midnight sessions between 1:00 AM and 4:30 AM with 32 cups of Vietnamese Robusta.'
  },
  {
    id: 'phantom-dex-protocol',
    title: 'Phantom Flow — Realtime DeFi Liquidity Engine',
    tagline: 'Sub-millisecond order-book aggregator and automated arbitrage matrix',
    description: 'High-frequency algorithmic liquidity protocol tracking decentralized exchange depth across 6 EVM networks. Implements custom memory-mapped caching and smart routing algorithms.',
    category: 'ai-web3',
    coverGradient: 'from-purple-950 via-indigo-900 to-slate-950',
    tags: ['Solidity', 'Go', 'GraphQL', 'TimescaleDB', 'Ethers.js'],
    techStack: ['Go (Golang)', 'Solidity', 'React', 'Tailwind', 'PostgreSQL', 'Grafana'],
    metrics: [
      { label: 'Total Volume Tracked', value: '$180M+' },
      { label: 'Routing Efficiency', value: '+4.2%' },
      { label: 'Block Query Latency', value: '12ms' },
    ],
    liveUrl: 'https://macatung.dev/demo/phantom-flow',
    githubUrl: 'https://github.com/macatung/phantom-flow',
    featured: true,
    architectureHighlights: [
      'Zero-allocation Go concurrency workers processing 85,000 tx/sec websocket streams',
      'Smart contract slippage protector with gas-optimized assembly opcodes',
      'Interactive visual graph explorer with WebGL nodes rendering 5000+ pools simultaneously'
    ],
    midnightFact: 'Caught a critical flash-loan vulnerability during a 3:00 AM testnet simulation before mainnet deployment.'
  },
  {
    id: 'jiangshi-ui-engine',
    title: 'Grimoire UI — Bewitching Design System',
    tagline: 'Component library with physics-based talisman cards and kinetic typography',
    description: 'An open-source aesthetic React design system engineered with micro-interactions, spring physics, accessible keyboard navigation, and dark-first neon luminescence tokens.',
    category: 'creative',
    coverGradient: 'from-amber-950 via-yellow-950 to-slate-950',
    tags: ['Design System', 'Framer Motion', 'TailwindCSS', 'Storybook', 'a11y'],
    techStack: ['React 18', 'TypeScript', 'TailwindCSS', 'CSS Houdini', 'Storybook'],
    metrics: [
      { label: 'GitHub Stars', value: '3,800+' },
      { label: 'Weekly NPM Downloads', value: '65k+' },
      { label: 'Lighthouse Score', value: '100/100' },
    ],
    liveUrl: 'https://macatung.dev/demo/grimoire-ui',
    githubUrl: 'https://github.com/macatung/grimoire-ui',
    featured: true,
    architectureHighlights: [
      'Zero-runtime overhead token engine using modern CSS Custom Properties & container queries',
      'Fluid physics-driven dragging and tilt transforms with hardware acceleration',
      'Full WAI-ARIA compliance with automated screen-reader announcements'
    ],
    midnightFact: 'Designed the signature "Talisman Foil" shine effect while looking at moonlight reflecting on coffee.'
  },
  {
    id: 'spectral-ai-agents',
    title: 'Spectral Agents — Autonomous Code Alchemist',
    tagline: 'Multi-agent LLM orchestrator for automated test synthesis & refactoring',
    description: 'An autonomous agent framework that analyzes complex AST graphs, identifies edge-case bugs in PRs, and automatically generates reproducible test suites with mutation testing.',
    category: 'ai-web3',
    coverGradient: 'from-rose-950 via-pink-950 to-slate-950',
    tags: ['Python', 'FastAPI', 'LangChain', 'OpenAI', 'Tree-Sitter'],
    techStack: ['Python 3.11', 'FastAPI', 'Tree-Sitter', 'Redis Queue', 'React UI'],
    metrics: [
      { label: 'Bug Discovery Rate', value: '94.2%' },
      { label: 'Test Coverage Boost', value: '+35%' },
      { label: 'PR Review Speed', value: '4x faster' },
    ],
    liveUrl: 'https://macatung.dev/demo/spectral-agents',
    githubUrl: 'https://github.com/macatung/spectral-agents',
    featured: false,
    architectureHighlights: [
      'AST-aware code chunking via Tree-Sitter grammars for precise context injection',
      'Dual-agent verification loop: Generator and Adversarial Auditor',
      'Streaming token response with live syntax highlighting and diff rendering'
    ],
    midnightFact: 'Agent hallucinated at 2:00 AM once and wrote a poem inside a unit test docstring.'
  },
  {
    id: 'hyper-cache-kv',
    title: 'Kitsune KV — In-Memory Ephemeral Store',
    tagline: 'Zero-GC lockless caching engine with distributed gossip replication',
    description: 'An ultra-fast key-value cache built for high-throughput microservices. Supports sliding TTL, LRU eviction with bitmask indexing, and zero-copy binary serialization.',
    category: 'tools',
    coverGradient: 'from-cyan-950 via-blue-950 to-slate-950',
    tags: ['Rust', 'Systems', 'Distributed', 'gRPC', 'Protobuf'],
    techStack: ['Rust', 'Tokio', 'gRPC', 'FlatBuffers', 'Prometheus'],
    metrics: [
      { label: 'Operations / sec', value: '1.2M req/s' },
      { label: 'p99 Latency', value: '85 μs' },
      { label: 'Memory Footprint', value: '-40% vs Redis' },
    ],
    liveUrl: 'https://macatung.dev/demo/kitsune-kv',
    githubUrl: 'https://github.com/macatung/kitsune-kv',
    featured: false,
    architectureHighlights: [
      'Lock-free concurrent hash map utilizing atomic compare-and-swap (CAS) primitives',
      'Custom memory allocator tuned for fixed-size key-value records',
      'SWIM-based gossip protocol for automatic node discovery and failure detection'
    ],
    midnightFact: 'Optimized memory layout by packing struct fields to eliminate 8 bytes of alignment padding.'
  },
  {
    id: 'macatung-dev-v1',
    title: 'macatung.dev — The Midnight Grimoire',
    tagline: 'The official interactive portfolio & digital sanctuary for night engineers',
    description: 'Featuring the hopping "Ma Cà Tưng" mascot, synthesized Web Audio sound effects, custom interactive talisman generator, and embedded developer terminal.',
    category: 'creative',
    coverGradient: 'from-teal-950 via-slate-900 to-midnight-950',
    tags: ['React', 'TypeScript', 'TailwindCSS', 'Web Audio', 'Canvas'],
    techStack: ['React 18', 'TypeScript', 'TailwindCSS', 'Web Audio API', 'Vite'],
    metrics: [
      { label: 'Performance Score', value: '100/100' },
      { label: 'Bundle Size (Gzip)', value: '< 45kb' },
      { label: 'Joy & Vibe Index', value: '999%' },
    ],
    liveUrl: 'https://macatung.dev',
    githubUrl: 'https://github.com/macatung/macatung.dev',
    featured: false,
    architectureHighlights: [
      'Pure Web Audio oscillator synthesis with 0 external sound asset HTTP requests',
      'GPU-accelerated Canvas particle engine running at buttery smooth 60fps',
      'Fully responsive keyboard navigable interactive terminal'
    ],
    midnightFact: 'You are looking at it right now! Give the mascot a poke to see him hop.'
  }
];
