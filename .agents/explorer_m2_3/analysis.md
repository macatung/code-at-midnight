# Technical Blueprint: Portfolio Layout, Sections, Anti-Collision Responsive System & Home Integration

**Author**: Explorer 3 (Milestone 2 — Frontend Components & Responsive Polish)  
**Target Directory**: `resources/js/Components/` & `resources/js/Pages/`  
**Date**: 2026-08-17  
**Status**: COMPLETE / READY FOR WORKER IMPLEMENTATION  

---

## 1. Executive Summary

This blueprint delivers the comprehensive technical specification for the structural layout, content sections, responsive anti-collision engine, and master page integration of `macatung.dev` for **Milestone 2**.

### Core Objectives:
1. **Layout Shell**: Sticky `Navbar.vue` with mobile drawer and live status, `Footer.vue` with Web Audio "Hop-to-Top" and confetti Easter egg, `SoundToggle.vue` with dynamic waveform feedback, and `Icons.vue` dynamic Lucide icon wrapper.
2. **Section Arsenal**:
   - `HeroSection.vue`: Neon gradient headline `"Code at midnight."`, mascot hero stage, action CTAs, trust badges.
   - `AboutSection.vue`: 4 developer stat cards, 3-tab manifesto panel (Triết Lý 00:00 AM, Day vs Night, Khắc Bùa Chất Lượng).
   - `SkillsSection.vue`: 4 categories, 18 skills with interactive progress bars (82%–100%), runes, and verification pledge.
   - `ExperienceSection.vue`: Chronological timeline, achievements, tech pills, and Midnight Quest Lore callouts.
   - `ContactSection.vue`: Summoning Altar UI layout, direct spectral channels, and Inertia form readiness.
3. **Master Page Integration (`Home.vue`)**: Single-page smooth-scrolling assembly with section anchors (`#hero`, `#about`, `#projects`, `#skills`, `#experience`, `#talisman`, `#terminal`, `#contact`), ambient particle canvas, and synchronized audio/hop state.
4. **Responsive & Anti-Collision System**: Zero-tolerance layout bug prevention across 360px, 390px, 768px, 1024px, and 1440px+ viewports with fluid typography, `overflow-x-hidden` containment, and minimum 44x44px touch targets.

---

## 2. Layout Components Technical Architecture

### 2.1 `Navbar.vue` (`resources/js/Components/layout/Navbar.vue`)

The main sticky navigation bar positioned at `sticky top-0 z-40`. It provides global branding, primary section links, live time/status badge, sound toggle, and a responsive mobile drawer menu.

#### Component Specification & API:
```vue
<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import MidnightClock from '@/Components/mascot/MidnightClock.vue';
import SoundToggle from '@/Components/layout/SoundToggle.vue';
import Icons from '@/Components/ui/Icons.vue';

interface NavItem {
  name: string;
  href: string;
  badge?: string;
}

const navItems: NavItem[] = [
  { name: 'Dự Án', href: '#projects' },
  { name: 'Kỹ Năng', href: '#skills' },
  { name: 'Kinh Nghiệm', href: '#experience' },
  { name: 'Triết Lý', href: '#about' },
  { name: 'Khắc Bùa', href: '#talisman', badge: 'HOT' },
  { name: 'Terminal', href: '#terminal', badge: 'CLI' },
  { name: 'Triệu Hồi', href: '#contact' },
];

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
};
</script>
```

#### Template Structure:
- **Root Wrapper**: `<header>` with `:class="['sticky top-0 z-40 transition-all duration-300', isScrolled ? 'bg-midnight-950/90 backdrop-blur-md border-b border-white/10 shadow-lg' : 'bg-transparent border-b border-transparent']">`
- **Container**: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between`
- **Brand Identity**:
  - Logo Box: `w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-midnight-900 border border-phantom-mint/40 flex items-center justify-center text-phantom-mint font-mono font-bold text-sm shadow-glow-mint hover:scale-105 transition-transform`
  - Brand Text: `font-display font-bold text-lg sm:text-xl tracking-tight text-white flex items-center gap-1.5`
  - Tag: `macatung<span class="text-phantom-mint">.dev</span>`
- **Center Desktop Links**:
  - `hidden lg:flex items-center gap-1 xl:gap-2`
  - Each item: `px-3 py-1.5 rounded-lg text-xs xl:text-sm font-sans text-slate-300 hover:text-phantom-mint hover:bg-white/5 transition-colors relative flex items-center gap-1.5`
  - Badge for special tabs (e.g. `HOT` in talisman-yellow, `CLI` in phantom-purple).
- **Right Utilities**:
  - `MidnightClock` widget (`hidden md:flex`)
  - `SoundToggle` button
  - Action CTA button: `"Triệu Hồi"` linking to `#contact` with `hidden sm:inline-flex px-3.5 py-1.5 rounded-lg bg-phantom-mint/10 border border-phantom-mint/30 text-phantom-mint text-xs font-mono font-semibold hover:bg-phantom-mint hover:text-midnight-950 transition-all shadow-glow-mint`
  - Mobile Menu Toggle Button: `lg:hidden p-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5 min-h-[44px] min-w-[44px] flex items-center justify-center`
- **Mobile Drawer Menu**:
  - Full-width slide-down sheet: `lg:hidden fixed inset-x-0 top-16 sm:top-20 bg-midnight-950/95 backdrop-blur-xl border-b border-white/10 p-6 flex flex-col gap-4 shadow-2xl transition-all`
  - Mobile links: `text-base font-medium text-slate-200 hover:text-phantom-mint py-2.5 px-3 rounded-lg hover:bg-white/5 flex items-center justify-between`
  - Integrated `MidnightClock` & Mobile Contact button.

---

### 2.2 `Footer.vue` (`resources/js/Components/layout/Footer.vue`)

The midnight footer with back-to-top sound action, interactive heart Easter egg with confetti, and spectral links.

#### Component Specification & API:
```vue
<script setup lang="ts">
import { ref } from 'vue';
import confetti from 'canvas-confetti';
import { sound } from '@/audio/soundEffects';
import Icons from '@/Components/ui/Icons.vue';

const heartClicks = ref(0);

const scrollToTop = () => {
  sound.playHop(1.5);
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  });
};

const triggerHeartEasterEgg = (e: MouseEvent) => {
  heartClicks.value++;
  sound.playSuccess();
  
  const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
  const x = (rect.left + rect.width / 2) / window.innerWidth;
  const y = (rect.top + rect.height / 2) / window.innerHeight;

  confetti({
    particleCount: 45,
    spread: 60,
    origin: { x, y },
    colors: ['#00f5a0', '#ffd166', '#ff0054', '#9d4edd'],
    disableForReducedMotion: true,
  });
};
</script>
```

#### Template Layout & Sections:
1. **Top Bar with Hop-to-Top**:
   - `flex flex-col sm:flex-row items-center justify-between pb-8 border-b border-white/5 gap-4`
   - Left: Brand signature with tagline: *"Code at midnight. Built for the restless builders of the digital void."*
   - Right: Hop-to-Top Button:
     ```html
     <button
       type="button"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-midnight-900 border border-white/10 text-slate-300 hover:text-phantom-mint hover:border-phantom-mint/40 transition-all text-xs font-mono group min-h-[44px]"
       @click="scrollToTop"
     >
       <span>Về Đầu Trang</span>
       <Icons name="ChevronUp" :size="16" class="group-hover:-translate-y-1 transition-transform" />
     </button>
     ```
2. **Spectral Navigation Columns**:
   - Column 1: *Grimoire & Lore* (`#hero`, `#about`, `#projects`)
   - Column 2: *Tech Arsenal* (`#skills`, `#experience`, `#talisman`)
   - Column 3: *Spectral Network* (GitHub, LinkedIn, Telegram, Discord, Email)
   - Column 4: *Midnight Status* (Current Time GMT+7, System Latency, 100% Uptime Pledge)
3. **Bottom Legal & Easter Egg Bar**:
   - `pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 font-mono gap-4`
   - Copyright: `© 2026 macatung.dev. All midnight rights reserved.`
   - Easter Egg:
     ```html
     <div class="flex items-center gap-1.5 text-slate-400 select-none">
       <span>Crafted with</span>
       <button
         type="button"
         class="text-talisman-cinnabar hover:scale-125 active:scale-95 transition-transform cursor-pointer p-1"
         title="Click for Midnight Love"
         @click="triggerHeartEasterEgg"
       >
         ❤️
       </button>
       <span>& Midnight Robusta</span>
       <span v-if="heartClicks > 0" class="text-phantom-mint font-bold ml-1">({{ heartClicks }})</span>
     </div>
     ```

---

### 2.3 `SoundToggle.vue` (`resources/js/Components/layout/SoundToggle.vue`)

Dynamic sound toggle component with equalizer visual feedback and local storage synchronization.

#### Component Specification & API:
```vue
<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { sound } from '@/audio/soundEffects';
import Icons from '@/Components/ui/Icons.vue';

const isMuted = ref(false);

onMounted(() => {
  isMuted.value = sound.isMuted();
});

const handleToggle = () => {
  isMuted.value = sound.toggleMute();
};
</script>
```

#### Visual Equalizer Waves:
- **Active State (Sound ON)**:
  - Button styling: `px-3 py-1.5 rounded-lg border border-phantom-mint/30 bg-phantom-mint/10 text-phantom-mint text-xs font-mono flex items-center gap-2 hover:bg-phantom-mint/20 transition-all min-h-[44px] min-w-[44px]`
  - Animated 3-bar equalizer:
    ```html
    <div class="flex items-center gap-0.5 h-3">
      <span class="w-0.5 bg-phantom-mint rounded-full animate-[equalizer_0.8s_ease-in-out_infinite]" style="height: 60%" />
      <span class="w-0.5 bg-phantom-mint rounded-full animate-[equalizer_0.6s_ease-in-out_infinite_0.2s]" style="height: 100%" />
      <span class="w-0.5 bg-phantom-mint rounded-full animate-[equalizer_1.0s_ease-in-out_infinite_0.4s]" style="height: 40%" />
    </div>
    <span class="hidden sm:inline">SFX: BẬT</span>
    ```
- **Muted State (Sound OFF)**:
  - Button styling: `px-3 py-1.5 rounded-lg border border-white/10 bg-midnight-900 text-slate-400 text-xs font-mono flex items-center gap-2 hover:text-slate-200 transition-all min-h-[44px] min-w-[44px]`
  - Static icon: `<Icons name="VolumeX" :size="14" class="text-slate-500" />` + `<span class="hidden sm:inline">SFX: TẮT</span>`

---

### 2.4 `Icons.vue` (`resources/js/Components/ui/Icons.vue`)

Lightweight, dynamic icon resolver that maps icon names to Lucide icons with safe fallbacks and customizable size/stroke.

#### Component Specification & API:
```vue
<script setup lang="ts">
import { computed } from 'vue';
import * as LucideIcons from 'lucide-vue-next';

interface Props {
  name: string;
  size?: number | string;
  strokeWidth?: number | string;
  class?: string;
}

const props = withDefaults(defineProps<Props>(), {
  size: 20,
  strokeWidth: 2,
  class: '',
});

// Map custom aliases if needed
const resolvedIcon = computed(() => {
  const iconName = props.name;
  const directMatch = (LucideIcons as Record<string, any>)[iconName];
  if (directMatch) return directMatch;

  // Fallback map
  const fallbackMap: Record<string, any> = {
    Code: LucideIcons.Code2,
    Terminal: LucideIcons.TerminalSquare,
    Coffee: LucideIcons.Coffee,
    Bug: LucideIcons.Bug,
    Zap: LucideIcons.Zap,
    Moon: LucideIcons.Moon,
    Layout: LucideIcons.LayoutGrid,
    Server: LucideIcons.Server,
    Cloud: LucideIcons.CloudLightning,
    Sparkles: LucideIcons.Sparkles,
  };

  return fallbackMap[iconName] || LucideIcons.HelpCircle;
});
</script>

<template>
  <component
    :is="resolvedIcon"
    :size="size"
    :stroke-width="strokeWidth"
    :class="props.class"
  />
</template>
```

---

## 3. Core Section Components Architecture

### 3.1 `HeroSection.vue` (`resources/js/Components/hero/HeroSection.vue`)

The crown-jewel entry section establishing the atmospheric midnight aesthetic and hosting the interactive mascot.

#### Section Props & State:
```vue
<script setup lang="ts">
import { ref } from 'vue';
import MacatungMascot from '@/Components/mascot/MacatungMascot.vue';
import Icons from '@/Components/ui/Icons.vue';
import { sound } from '@/audio/soundEffects';

const emit = defineEmits<{
  (e: 'hop', count: number): void;
}>();

const heroHopCount = ref(0);

const handleMascotHop = (count: number) => {
  heroHopCount.value = count;
  emit('hop', count);
};

const handleCtaClick = (soundType: 'click' | 'talisman') => {
  if (soundType === 'talisman') sound.playTalisman();
  else sound.playClick();
};
</script>
```

#### Layout Blueprint & Sub-elements:
```
┌────────────────────────────────────────────────────────────────────────┐
│ [Live Badge: 🟢 SẴN SÀNG NHẬN QUEST 00:00 AM]                         │
│                                                                        │
│  H1: Code at                                                           │
│      [neon gradient] midnight. [/neon]                                 │
│                                                                        │
│  Subtitle:                                                             │
│  "Kỹ Sư Hệ Thống & Nhà Giả Kim Sáng Tạo hoạt động khi thành phố ngủ.  │
│   Biến cà phê Robusta thành kiến trúc phân tán & UI mê hoặc."          │
│                                                                        │
│  ┌───────────────────────────┐      ┌───────────────────────────────┐ │
│  │ [Khám Phá Dự Án ✨]       │      │  [ Interactive Mascot Stage ] │ │
│  │ [Triệu Hồi Ngay 📜]       │      │  🧛‍♂️ Ma Cà Tưng                │ │
│  │ [Mở Terminal CLI ⚡]       │      │  - Squash & Stretch physics   │ │
│  └───────────────────────────┘      │  - Mood state toggle          │ │
│                                     │  - Speech quotes bubble       │ │
│  ┌────────────────────────────────┐ └───────────────────────────────┘ │
│  │ Trust Badges:                  │                                   │
│  │ [⚡ < 18ms Latency]            │                                   │
│  │ [☕ 100% Midnight Robusta]     │                                   │
│  │ [🛡️ 0 Bug In Production]       │                                   │
│  └────────────────────────────────┘                                   │
└────────────────────────────────────────────────────────────────────────┘
```

#### Tailwind Recipe:
- Section container: `relative min-h-[calc(100vh-5rem)] flex items-center justify-center py-12 lg:py-20`
- Two-column grid on desktop: `grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center w-full`
- Content Column: `lg:col-span-7 flex flex-col items-center lg:items-start text-center lg:text-left`
- Mascot Column: `lg:col-span-5 flex justify-center items-center`
- Hero Title Typography:
  ```html
  <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-display font-extrabold tracking-tight text-white leading-[1.1] mb-6">
    Code at <span class="text-transparent bg-clip-text bg-gradient-to-r from-phantom-mint via-phantom-cyan to-talisman-yellow text-glow-mint">midnight</span>.
  </h1>
  ```
- Action Buttons Flex: `flex flex-wrap items-center justify-center lg:justify-start gap-4 mb-10`

---

### 3.2 `AboutSection.vue` (`resources/js/Components/about/AboutSection.vue`)

The developer manifesto and stats section delivering origin lore and core technical standards.

#### Component Specification & Data Integration:
```vue
<script setup lang="ts">
import { ref } from 'vue';
import { developerStats } from '@/data/experienceData';
import Icons from '@/Components/ui/Icons.vue';
import { sound } from '@/audio/soundEffects';

type TabKey = 'philosophy' | 'daynight' | 'quality';

const activeTab = ref<TabKey>('philosophy');

const setTab = (tab: TabKey) => {
  activeTab.value = tab;
  sound.playClick();
};
</script>
```

#### 4 Developer Stat Cards:
Grid: `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-16`
- Card Markup:
  ```html
  <div
    v-for="stat in developerStats"
    :key="stat.label"
    class="p-6 rounded-2xl glass-panel hover-card-glow border border-white/5 relative overflow-hidden group"
  >
    <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity text-slate-200">
      <Icons :name="stat.iconName" :size="96" />
    </div>
    <div class="flex items-center gap-3 mb-3">
      <div class="w-10 h-10 rounded-xl bg-midnight-900 border border-white/10 flex items-center justify-center text-phantom-mint">
        <Icons :name="stat.iconName" :size="20" />
      </div>
      <span class="text-xs font-mono uppercase tracking-wider text-slate-400">{{ stat.unit }}</span>
    </div>
    <div class="text-3xl sm:text-4xl font-display font-bold text-white mb-1 tracking-tight">
      {{ stat.value }}
    </div>
    <div class="text-sm font-semibold text-slate-200 mb-1">{{ stat.label }}</div>
    <p class="text-xs text-slate-400 font-sans leading-relaxed">{{ stat.description }}</p>
  </div>
  ```

#### 3-Tab Manifesto Panel:
1. **Tab Navigation Bar**:
   - `flex items-center justify-start sm:justify-center gap-2 p-1.5 rounded-2xl bg-midnight-900/80 border border-white/10 mb-8 overflow-x-auto no-scrollbar`
   - Tabs:
     - `Triết Lý 00:00 AM` (Icon: `Moon`)
     - `Ngày vs Đêm` (Icon: `Zap`)
     - `Khắc Bùa Chất Lượng` (Icon: `Sparkles`)
2. **Tab 1: Triết Lý 00:00 AM**:
   - Deep Focus narrative: 3 core tenets:
     * *Vùng Tĩnh Lặng Tuyệt Đối*: Khi notifications tắt ngấm, não bộ bước vào trạng thái Ultra-Flow.
     * *Kiến Trúc Trước Code*: Mọi dòng mã đều được phác thảo cẩn thận, không có "spaghetti code".
     * *Tôn Trọng Người Dùng*: Tốc độ tải trang dưới 100ms và không gây khó chịu cho thị giác.
3. **Tab 2: Ngày vs Đêm (Day vs Night Matrix)**:
   - High-contrast comparison table:
     | Yếu Tố | Ban Ngày (Day Mode) | Nửa Đêm (Midnight Mode) |
     |---|---|---|
     | **Năng Lượng** | 3 cuộc họp, 10 email, 15 ping Slack | 100% Cognitive RAM tập trung giải quyết Core Bug |
     | **Tốc Độ Xử Lý** | 1 Pull Request / 2 ngày | 3 Microservices / 1 đêm |
     | **Nhiên Liệu** | Trà sữa hoặc nước lọc | Robusta nguyên chất 100% rang đậm |
     | **Cảm Giác** | Làm việc theo deadline | Đam mê sáng tạo thuần túy |
4. **Tab 3: Khắc Bùa Chất Lượng (The 4 Sacred Rules)**:
   - 4 Cards:
     1. `Type-Safety Tuyệt Đối`: TypeScript strict mode 100%, không `any`.
     2. `Sub-Millisecond Execution`: Tối ưu thuật toán O(1) hoặc O(log n), zero memory leak.
     3. `Aesthetic Perfection`: Glassmorphic UI, kinetic micro-interactions, responsive 360px–4k.
     4. `Chịu Tải Bất Diệt`: Kiến trúc sẵn sàng cho 100k+ concurrent users.

---

### 3.3 `SkillsSection.vue` (`resources/js/Components/skills/SkillsSection.vue`)

Tech Rune Arsenal displaying 18 skills categorized into 4 domains with animated proficiency gauges and runes.

#### Component Specification & Data Integration:
```vue
<script setup lang="ts">
import { ref } from 'vue';
import { skillsData } from '@/data/skillsData';
import Icons from '@/Components/ui/Icons.vue';
import { sound } from '@/audio/soundEffects';

const hoveredSkill = ref<string | null>(null);

const onSkillHover = (skillName: string) => {
  hoveredSkill.value = skillName;
  sound.playClick();
};
</script>
```

#### Category Layout:
- 4 Categories rendered in a 2x2 grid: `grid grid-cols-1 lg:grid-cols-2 gap-8`
- Category Container: `p-6 sm:p-8 rounded-3xl glass-panel border border-white/5 relative overflow-hidden flex flex-col justify-between`
- Header: Category Icon + Title + Badge pill (e.g. `"Pixel Perfection"`, `"Sub-Millisecond"`, `"99.99% Uptime"`, `"Midnight Flow"`).

#### Skill Row Item Blueprint:
```html
<div
  v-for="skill in category.skills"
  :key="skill.name"
  class="group/skill p-3 rounded-xl hover:bg-white/5 transition-all cursor-default"
  @mouseenter="onSkillHover(skill.name)"
>
  <div class="flex items-center justify-between mb-1.5">
    <div class="flex items-center gap-2.5">
      <span class="text-lg select-none">{{ skill.rune }}</span>
      <span class="text-sm font-semibold text-slate-200 group-hover/skill:text-phantom-mint transition-colors">
        {{ skill.name }}
      </span>
      <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-midnight-900 border border-white/10 text-slate-400">
        {{ skill.tag }}
      </span>
    </div>
    <span class="text-xs font-mono font-bold text-phantom-mint">
      {{ skill.level }}%
    </span>
  </div>
  
  <!-- Interactive Proficiency Bar -->
  <div class="w-full h-2 bg-midnight-900 rounded-full overflow-hidden border border-white/5 p-[1px]">
    <div
      class="h-full rounded-full bg-gradient-to-r from-phantom-mint via-phantom-cyan to-talisman-yellow transition-all duration-1000 ease-out shadow-glow-mint"
      :style="{ width: `${skill.level}%` }"
    />
  </div>
  <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
    {{ skill.description }}
  </p>
</div>
```

#### Verification Pledge Card:
Boxed footer banner at the bottom of the section:
- `p-6 rounded-2xl glass-panel-talisman border border-talisman-yellow/20 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left`
- Left: Seal badge `"📜 LỜI THỀ BÙA CHÚ"`
- Text: *"Mọi kỹ năng trên đều được tôi luyện qua 8+ năm thực chiến tải cao, cam kết code thật - chạy thật - chịu tải thật."*

---

### 3.4 `ExperienceSection.vue` (`resources/js/Components/experience/ExperienceSection.vue`)

The chronological career timeline and midnight chronicles.

#### Component Specification & Data Integration:
```vue
<script setup lang="ts">
import { experienceData } from '@/data/experienceData';
import Icons from '@/Components/ui/Icons.vue';
</script>
```

#### Timeline DOM Blueprint:
- Vertical continuous line: `relative border-l-2 border-phantom-mint/20 ml-4 sm:ml-8 pl-6 sm:pl-10 space-y-12`
- Node Icon Anchor:
  ```html
  <div class="absolute -left-[17px] top-1.5 w-8 h-8 rounded-full bg-midnight-950 border-2 border-phantom-mint flex items-center justify-center text-phantom-mint shadow-glow-mint">
    <Icons name="Zap" :size="14" />
  </div>
  ```
- Timeline Card Content:
  1. **Header Row**:
     - Period Badge: `px-3 py-1 rounded-full bg-phantom-mint/10 text-phantom-mint font-mono text-xs font-bold border border-phantom-mint/30`
     - Type Badge: `px-2.5 py-0.5 rounded-full bg-white/5 text-slate-400 font-mono text-[11px]`
  2. **Role & Company**:
     - Role: `text-xl sm:text-2xl font-display font-bold text-white mt-2`
     - Company & Location: `text-sm font-sans text-slate-300 flex items-center gap-2 mt-1`
  3. **Summary**: `text-sm text-slate-400 mt-3 leading-relaxed`
  4. **Key Achievements**:
     - Bullet list with checkmark/talisman icons:
       ```html
       <ul class="mt-4 space-y-2">
         <li v-for="(ach, i) in item.achievements" :key="i" class="text-xs sm:text-sm text-slate-300 flex items-start gap-2.5">
           <span class="text-phantom-mint mt-0.5">✦</span>
           <span>{{ ach }}</span>
         </li>
       </ul>
       ```
  5. **Tech Stack Pills**:
     - `flex flex-wrap gap-1.5 mt-4`
     - Each pill: `px-2.5 py-1 rounded-lg bg-midnight-900 border border-white/5 text-slate-300 text-xs font-mono`
  6. **Midnight Quest Lore Callout**:
     ```html
     <div class="mt-5 p-4 rounded-xl glass-panel-talisman border border-talisman-yellow/20 flex items-start gap-3">
       <span class="text-xl">🌙</span>
       <div>
         <span class="text-xs font-mono font-bold text-talisman-yellow uppercase tracking-wider block mb-0.5">
           Midnight Quest Lore
         </span>
         <p class="text-xs text-slate-300 italic leading-relaxed">
           "{{ item.midnightQuest }}"
         </p>
       </div>
     </div>
     ```

---

### 3.5 `ContactSection.vue` (`resources/js/Components/contact/ContactSection.vue`)

The Summoning Altar UI layout providing direct spectral channels and preparing form integration for Milestone 3.

#### Component Specification & API:
```vue
<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import confetti from 'canvas-confetti';
import { sound } from '@/audio/soundEffects';
import Icons from '@/Components/ui/Icons.vue';

const projectTypes = [
  'Full-Stack Web App',
  'Creative UI/UX & Web Audio',
  'High-Throughput Microservice',
  'AI Agents & Automation',
  'Tech Lead / Architecture Consulting',
  'Other Quest',
];

// Inertia Form bindings ready for M3
const form = useForm({
  name: '',
  email: '',
  project_type: 'Full-Stack Web App',
  coffee_offering: '1 Ly Cà Phê Muối Nửa Đêm',
  message: '',
});

const isSubmitting = ref(false);
const submittedSuccess = ref(false);
const copySuccess = ref(false);

const copyEmail = async () => {
  try {
    await navigator.clipboard.writeText('dev@macatung.dev');
    copySuccess.value = true;
    sound.playClick();
    setTimeout(() => {
      copySuccess.value = false;
    }, 2500);
  } catch {
    // Fallback
  }
};

const handleClientSubmit = () => {
  // In M2: UI interactive simulation with sound + confetti
  // In M3: form.post(route('contact.store'))
  isSubmitting.value = true;
  sound.playTalisman();

  setTimeout(() => {
    isSubmitting.value = false;
    submittedSuccess.value = true;
    sound.playSuccess();
    confetti({
      particleCount: 60,
      spread: 70,
      origin: { y: 0.6 },
      colors: ['#00f5a0', '#ffd166', '#ff0054'],
    });
  }, 600);
};
</script>
```

#### Layout Blueprint:
```
┌────────────────────────────────────────────────────────────────────────┐
│  Section Header: BÀN THỜ TRIỆU HỒI — Gửi Tín Hiệu Xuyên Màn Đêm        │
├───────────────────────────────────┬────────────────────────────────────┤
│  Left Column: Direct Channels     │  Right Column: Summoning Form      │
│  - Email: dev@macatung.dev [Copy] │  - Tên Lữ Khách (Name)             │
│  - Realm: GMT+7 Midnight Zone     │  - Địa Chỉ Thần Giao (Email)       │
│  - Status: 🟢 Sẵn Sàng Nhận Quest │  - Loại Quest (Project Type pills) │
│  - Social Runes:                  │  - Lễ Vật Cà Phê (Coffee Offering) │
│    [GitHub] [LinkedIn] [X]        │  - Chi Tiết Nhiệm Vụ (Message)     │
│  - Cam Kết Hồi Đáp < 24h          │  - [ TRIỆU HỒI MA CÀ TƯNG 🚀 ]     │
└───────────────────────────────────┴────────────────────────────────────┘
```

---

## 4. Master Page Integration Architecture (`Home.vue`)

### 4.1 Page Layout & Component Tree

The master entry point for Inertia.js (`resources/js/Pages/Home.vue`).

```
Home.vue
├── Head (Title, Meta description, OpenGraph)
├── TalismanCanvas (Fixed background particle canvas z-0)
├── Ambient Glow Blobs (z-0 blur layers)
├── Navbar (Sticky top navigation, z-40)
│   ├── MidnightClock
│   └── SoundToggle
├── Main Content Wrapper (z-10 relative)
│   ├── HeroSection (#hero)
│   ├── AboutSection (#about)
│   ├── ProjectsSection (#projects)
│   │   └── ProjectModal
│   ├── SkillsSection (#skills)
│   ├── ExperienceSection (#experience)
│   ├── TalismanGenerator (#talisman)
│   ├── MidnightTerminal (#terminal)
│   └── ContactSection (#contact)
└── Footer (z-10 relative)
```

### 4.2 Smooth Anchor Navigation & Scroll Offsets
To prevent the sticky navbar (height ~64px-80px) from covering section titles when users jump via anchor links, all sections configure `scroll-mt-24` (`scroll-margin-top: 6rem`):
```html
<section id="projects" class="scroll-mt-24 py-20">...</section>
<section id="skills" class="scroll-mt-24 py-20">...</section>
<section id="experience" class="scroll-mt-24 py-20">...</section>
<section id="about" class="scroll-mt-24 py-20">...</section>
<section id="talisman" class="scroll-mt-24 py-20">...</section>
<section id="terminal" class="scroll-mt-24 py-20">...</section>
<section id="contact" class="scroll-mt-24 py-20">...</section>
```

### 4.3 Global Shared State & Sync
- **Hop Count**: Synchronized between Hero Mascot, Navbar logo click, Terminal `hop` command, and Footer Hop-to-Top.
- **Sound Engine**: Global singleton `sound` (`soundEffects.ts`) imported directly across components, reactive to `localStorage` muting preference.

---

## 5. Responsive & Anti-Collision System Specification

### 5.1 Breakpoint Audit & Collision Prevention Matrix

| Breakpoint | Viewport Width | Typical Devices | Potential Collision Risk | Anti-Collision Strategy & Utility Recipe |
|---|---|---|---|---|
| **Mobile XS** | `360px – 389px` | Galaxy S8/S9, iPhone SE (1st/2nd), small Androids | Hero heading line breaking awkwardly; stat card number truncation; terminal prompt overflow; talisman paper clipping | - `text-3xl` leading-[1.15]<br>- `grid-cols-1` for stats<br>- `min-w-0 break-words` on all flex parents<br>- Tab list `overflow-x-auto no-scrollbar` |
| **Mobile Standard** | `390px – 480px` | iPhone 12/13/14/15 Pro, Pixel 7, Galaxy S23 | Button group overlapping; project card metrics wrapping onto 3 lines; modal padding clipping | - `flex-col sm:flex-row` for button groups<br>- `grid-cols-2` for project metrics<br>- Modal padding `p-4 sm:p-6` with `max-h-[90vh] overflow-y-auto` |
| **Tablet Portrait** | `768px – 834px` | iPad Mini, iPad 10th Gen, iPad Air | Mascot overlapping hero text; 4 stats cards squishing to unreadable width; timeline dates colliding with titles | - Hero switches to `lg:grid-cols-12` (stacks cleanly in tablet portrait)<br>- Stats use `grid-cols-2 lg:grid-cols-4`<br>- Timeline padding `pl-8` |
| **Tablet Landscape** | `835px – 1024px` | iPad Pro 11", Surface Pro | Desktop navbar links overflowing; 3-column project grid squished | - Navbar links use compact padding `px-2.5 text-xs xl:text-sm`<br>- Projects grid `grid-cols-1 md:grid-cols-2 lg:grid-cols-3` |
| **Desktop Standard** | `1280px – 1440px` | MacBook Pro 14"/16", 1080p / 1440p Monitors | Excessive white space or unbalanced column heights | - `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`<br>- Balanced flex & grid gap spacing `gap-8 lg:gap-12` |
| **Ultrawide** | `1920px+` | 2K / 4K Ultrawide Displays | Elements stretching infinitely across screen | - Strict `max-w-7xl` container containment with centered margins |

---

### 5.2 Fluid Typography & Line Break Rules

1. **Avoid Rigid Pixel Font Sizes on Headings**:
   - Hero Heading: `text-3xl xs:text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-display font-extrabold tracking-tight`
   - Section Heading: `text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-display font-bold tracking-tight`
   - Card Title: `text-lg sm:text-xl font-display font-bold`
   - Body Text: `text-sm sm:text-base leading-relaxed text-slate-400`
2. **Vietnamese Diacritics & Long Word Wrap**:
   - Apply `break-words hyphens-auto` to all paragraphs, headings, and descriptions.
   - For code blocks and CLI commands, apply `font-mono break-all whitespace-pre-wrap` to prevent horizontal blowouts.
3. **Flex Shrink Safety**:
   - Always add `min-w-0` to flex child containers containing text strings:
     ```html
     <div class="flex items-center gap-3 min-w-0">
       <div class="truncate text-sm font-medium">Long title here</div>
     </div>
     ```

---

### 5.3 Touch Target Sizing (Min 44x44px Rule)

All clickable elements on mobile viewports adhere to the WCAG 2.1 AA 44x44px target standard:
- **Mobile Menu Toggle**: `min-h-[44px] min-w-[44px] flex items-center justify-center p-2.5`
- **Sound Toggle**: `min-h-[44px] px-3.5 py-2`
- **Hop-to-Top**: `min-h-[44px] px-4 py-2.5`
- **Filter Pills**: `min-h-[44px] px-4 py-2`
- **Modal Close Button**: `w-11 h-11 flex items-center justify-center rounded-full bg-white/10`
- **Form Inputs & Dropdowns**: `h-12 px-4 rounded-xl`

---

### 5.4 Horizontal Scroll Prevention Recipe

To guarantee zero horizontal scrollbar on any mobile device:
1. Root element in `app.blade.php` and `Home.vue`:
   ```html
   <div class="min-h-screen bg-midnight-950 text-slate-100 overflow-x-hidden w-full relative">
   ```
2. Canvas element in `TalismanCanvas.vue`:
   ```html
   <canvas class="fixed inset-0 pointer-events-none z-0 w-full h-full" />
   ```
3. Negative margins on grids: Ensure parent containers have adequate horizontal padding (`px-4 sm:px-6 lg:px-8`) so nested `-m-x` does not spill outside `100vw`.

---

## 6. Complete Component Directory Structure

```
d:/Work/macatung/resources/js/
├── Components/
│   ├── layout/
│   │   ├── Navbar.vue
│   │   ├── Footer.vue
│   │   └── SoundToggle.vue
│   ├── ui/
│   │   └── Icons.vue
│   ├── hero/
│   │   └── HeroSection.vue
│   ├── about/
│   │   └── AboutSection.vue
│   ├── skills/
│   │   └── SkillsSection.vue
│   ├── experience/
│   │   └── ExperienceSection.vue
│   ├── contact/
│   │   └── ContactSection.vue
│   ├── mascot/
│   │   ├── MacatungMascot.vue
│   │   ├── TalismanCanvas.vue
│   │   └── MidnightClock.vue
│   ├── projects/
│   │   ├── ProjectsSection.vue
│   │   └── ProjectModal.vue
│   ├── talisman/
│   │   └── TalismanGenerator.vue
│   └── terminal/
│       └── MidnightTerminal.vue
└── Pages/
    └── Home.vue
```

---

## 7. Implementation Plan for Worker

| Phase | Target Components | Key Execution Steps |
|---|---|---|
| **Step 1** | `Icons.vue`, `SoundToggle.vue`, `Navbar.vue`, `Footer.vue` | Build reusable UI & layout wrappers; verify responsive mobile menu drawer and sound mute persistence. |
| **Step 2** | `HeroSection.vue`, `AboutSection.vue` | Assemble hero layout with mascot stage, CTAs, trust badges; build 4 stat cards and 3-tab manifesto panel. |
| **Step 3** | `SkillsSection.vue`, `ExperienceSection.vue` | Implement 4 skill categories with 18 skills and level bars; construct chronological timeline with Midnight Quest lore cards. |
| **Step 4** | `ContactSection.vue` | Construct Summoning Altar UI with spectral channels and Inertia form structure. |
| **Step 5** | `Home.vue` Integration | Assemble all components in order; add smooth scrolling anchors, ambient background lighting, and verify `npm run build`. |

---
*End of Analysis — Explorer 3 / Milestone 2*
