# Technical Blueprint: Milestone 2 Interactive & Audio Engine
**Document**: `analysis.md`  
**Agent**: Explorer 1 (`.agents/explorer_m2_1/`)  
**Milestone**: `m2_frontend_components_responsive`  
**Date**: 2026-08-17  

---

## 1. Executive Summary & Component Scope

This document details the architectural blueprints, interface contracts, mathematical formulations, SVG markup structures, and responsive implementation specifications for the 4 core interactive and audio components of `macatung.dev`:

1. **Web Audio Synthesizer** (`resources/js/audio/soundEffects.ts`): Procedural, zero-external-asset Web Audio API engine providing sound effects for hops, mystic talismans, UI clicks, terminal keystrokes, and milestone success fanfares, with resilient autoplay handling and persistence.
2. **Interactive Jiangshi Mascot** (`resources/js/Components/mascot/MacatungMascot.vue`): Cyber-folklore Jiangshi SVG illustration with 4 mood states (`normal`, `caffeine`, `sleepy`, `rage`), 450ms squash-and-stretch hop physics, touch/tap & keyboard triggers, speech quotes rotation, milestone celebrations (confetti + fanfare every 10 hops), and persistent hop counter.
3. **Floating Talisman & Firefly Particles Canvas** (`resources/js/Components/mascot/TalismanCanvas.vue`): High-performance HTML5 2D Canvas background particle engine featuring floating yellow paper talismans with glowing tech runes, embers, fireflies, mouse repulsion within 100px radius, screen boundary wrapping, and robust lifecycle hooks.
4. **Midnight Clock & Live Status Pill** (`resources/js/Components/mascot/MidnightClock.vue`): Real-time digital clock (`HH:mm:ss`) with live pulsing neon indicator, Midnight Code Mode (22:00–05:00) vs Daylight Prep badge, dynamic caffeine level calculator, and latency ping.

---

## 2. Web Audio Synthesizer Blueprint (`soundEffects.ts`)

### 2.1 Interface & Class Contract

```typescript
// resources/js/types/portfolio.ts & soundEffects.ts

export interface ISoundEngine {
  isMuted(): boolean;
  toggleMute(): boolean;
  playHop(intensity?: number): void;
  playTalisman(): void;
  playClick(): void;
  playTerminalKey(): void;
  playSuccess(): void;
}
```

### 2.2 Audio Context Autoplay Resilience & SSR Safety

Browsers (Chrome, Safari, iOS WebKit) enforce autoplay policies requiring user gestures before an `AudioContext` can produce sound.
- **Lazy Instantiation**: `AudioContext` is created only on the first sound invocation or explicitly unlocked on user interaction.
- **State Auto-Resume**: If `ctx.state === 'suspended'`, `ctx.resume()` is called.
- **SSR Guard**: All accesses to `window`, `localStorage`, and `AudioContext` are guarded behind `typeof window !== 'undefined'`.
- **LocalStorage Key**: `'macatung_sound_muted'` (`"true"` | `"false"`).

```typescript
private getContext(): AudioContext | null {
  if (this.muted) return null;
  if (typeof window === 'undefined') return null;

  const AudioCtx = window.AudioContext || (window as unknown as { webkitAudioContext: typeof AudioContext }).webkitAudioContext;
  if (!AudioCtx) return null;

  if (!this.ctx || this.ctx.state === 'closed') {
    try {
      this.ctx = new AudioCtx();
    } catch {
      return null;
    }
  }

  if (this.ctx && this.ctx.state === 'suspended') {
    this.ctx.resume().catch(() => {});
  }

  return this.ctx;
}
```

### 2.3 Mathematical Formulations & Envelope Shaping

```
   [Oscillator] ----(Frequency Sweep f(t))----> [GainNode] ----(Gain Decay g(t))----> [ctx.destination]
```

#### A. `playHop(intensity = 1.0)` — Hopping Pitch Sweep
- **Oscillator**: `type = 'sine'`
- **Base Frequency**: $f_0 = 220 + (\text{intensity} \times 40) \text{ Hz}$
- **Frequency Ramp**:
  - $t_0$: $f(t_0) = f_0$
  - $t_0 + 0.12\text{s}$: $f(t_0 + 0.12) = 2.8 \times f_0$ (exponential rise)
  - $t_0 + 0.25\text{s}$: $f(t_0 + 0.25) = 0.8 \times f_0$ (exponential fall)
- **Gain Envelope**:
  - $t_0$: $G(t_0) = 0.20$
  - $t_0 + 0.25\text{s}$: $G(t_0 + 0.25) = 0.01$ (exponential decay)
- **Stop**: $t_0 + 0.26\text{s}$

#### B. `playTalisman()` — Mystic Pentatonic Arpeggio
- **Oscillator**: `type = 'triangle'`
- **Frequencies & Offsets**:
  - Note 0: $D_5 = 587.33 \text{ Hz}$ at $t_0 + 0.00\text{s}$
  - Note 1: $A_5 = 880.00 \text{ Hz}$ at $t_0 + 0.05\text{s}$
  - Note 2: $D_6 = 1174.66 \text{ Hz}$ at $t_0 + 0.10\text{s}$
  - Note 3: $A_6 = 1760.00 \text{ Hz}$ at $t_0 + 0.15\text{s}$
- **Gain Envelope (per note $i$)**:
  - Attack: $G(t_i) = 0.08$
  - Decay: $G(t_i + 0.40\text{s}) = 0.001$ (exponential)
- **Stop**: $t_i + 0.45\text{s}$

#### C. `playClick()` — Tactile UI Tap
- **Oscillator**: `type = 'sine'`
- **Frequency Sweep**: $f(t_0) = 800 \text{ Hz} \to f(t_0 + 0.04\text{s}) = 300 \text{ Hz}$
- **Gain Envelope**: $G(t_0) = 0.12 \to G(t_0 + 0.04\text{s}) = 0.001$
- **Stop**: $t_0 + 0.05\text{s}$

#### D. `playTerminalKey()` — Randomized Switch Tactility
- **Oscillator**: `type = 'triangle'`
- **Random Frequency**: $f = 420 + (\text{random}() \times 80) \text{ Hz}$ ($420\text{ Hz} \sim 500\text{ Hz}$)
- **Gain Envelope**: $G(t_0) = 0.04 \to G(t_0 + 0.03\text{s}) = 0.001$
- **Stop**: $t_0 + 0.035\text{s}$

#### E. `playSuccess()` — Triumphant C-Major Fanfare
- **Oscillator**: `type = 'sine'`
- **Chords & Offsets**:
  - $C_5 = 523.25 \text{ Hz}$ at $t_0 + 0.00\text{s}$
  - $E_5 = 659.25 \text{ Hz}$ at $t_0 + 0.08\text{s}$
  - $G_5 = 783.99 \text{ Hz}$ at $t_0 + 0.16\text{s}$
  - $C_6 = 1046.50 \text{ Hz}$ at $t_0 + 0.24\text{s}$
- **Gain Envelope (per note $i$)**:
  - Attack: $G(t_i) = 0.15$
  - Decay: $G(t_i + 0.60\text{s}) = 0.001$
- **Stop**: $t_i + 0.65\text{s}$

---

## 3. Jiangshi Mascot Blueprint (`MacatungMascot.vue`)

### 3.1 Component Props, Emits & Mood Contract

```typescript
// Component Props
interface MascotProps {
  size?: 'sm' | 'md' | 'lg' | 'hero'; // default: 'hero'
  showControls?: boolean;            // default: true
}

// Component Emits
interface MascotEmits {
  (e: 'hop-count-change', count: number): void;
  (e: 'mood-change', mood: 'normal' | 'caffeine' | 'sleepy' | 'rage'): void;
}

type Mood = 'normal' | 'caffeine' | 'sleepy' | 'rage';
```

### 3.2 Dimensions Matrix
| Size | Width (px) | Height (px) | Speech Bubble | Controls |
|---|---|---|---|---|
| `sm` | 80 | 95 | Hidden | Hidden |
| `md` | 140 | 165 | Visible (`max-w-[240px]`) | Optional |
| `lg` | 220 | 260 | Visible (`max-w-[280px]`) | Visible |
| `hero` | 280 | 330 | Visible (`max-w-[320px]`) | Visible |

### 3.3 SVG Anatomy Hierarchy (`viewBox="0 0 240 280"`)

```
<svg viewBox="0 0 240 280">
  <defs>
    ├── linearGradient #robeGrad       (#111724 -> #0a0e17 -> #060910)
    ├── linearGradient #hatGrad        (#1e293b -> #0f172a)
    ├── linearGradient #talismanGrad   (#ffe57f -> #ffd166 -> #f4b41a)
    ├── linearGradient #neonTrim       (#00f5a0 -> #00d2ff)
    └── radialGradient #ghostSkin      (#d8f3dc -> #b7e4c7 -> #95d5b2)
  </defs>

  <!-- 1. Outstretched Arms (Hopping Posture) -->
  <g class="animate-talisman-flutter origin-center">
    ├── Left Arm Path (M75 145 C45 145...) + Glove Circle (cx=16 cy=160 r=10) + Claw Fingers
    └── Right Arm Path (M165 145 C195 145...) + Glove Circle (cx=224 cy=160 r=10) + Claw Fingers

  <!-- 2. Main Jiangshi Robe / Hoodie -->
  <path d="M75 130 C75 130, 95 125, 120 125 C145 125, 165 130, 165 130 L180 235..." fill="url(#robeGrad)" stroke="rgba(0, 245, 160, 0.4)" strokeWidth="2" />
  ├── Collar V-Trim Path (M100 135 L120 160 L140 135)
  └── Hexagon Core Rune (polygon points="120,165 132,172 132,186 120,193 108,186 108,172") with text "{ }"

  <!-- 3. Hopping Feet -->
  <ellipse cx="98" cy="248" rx="14" ry="7" fill="#0f172a" stroke="#00f5a0" strokeWidth="1.5" />
  <ellipse cx="142" cy="248" rx="14" ry="7" fill="#0f172a" stroke="#00f5a0" strokeWidth="1.5" />

  <!-- 4. Pale Ghost Head & Headphones -->
  <circle cx="120" cy="95" r="48" fill="url(#ghostSkin)" stroke="#00f5a0" strokeWidth="2" />
  ├── Headphone Headband (path d="M68 95 C68 62, 172 62, 172 95" strokeWidth="7")
  └── Headphone Ear Cups (rect x=64 y=80 w=12 h=30, rect x=164 y=80 w=12 h=30)

  <!-- 5. Jiangshi Hat & Cyber Antenna -->
  <g>
    ├── Hat Crown Path (M78 68 C80 32, 160 32, 162 68 Z) fill="url(#hatGrad)" stroke="#ffd166"
    ├── Hat Brim Ellipse (cx=120 cy=68 rx=52 ry=14) fill="#1e293b" stroke="#ffd166"
    ├── Golden Jewel Circle (cx=120 cy=50 r=6 fill="#ef233c" stroke="#ffd166")
    └── Cyber Antenna Line (x1=120 y1=44 x2=120 y2=24 stroke="#00f5a0") + Jade Tip (circle cx=120 cy=22 r=3.5)

  <!-- 6. Blushing Cheeks -->
  <ellipse cx="92" cy="115" rx="7" ry="4" fill="#ff4d6d" opacity="0.35" />
  <ellipse cx="148" cy="115" rx="7" ry="4" fill="#ff4d6d" opacity="0.35" />

  <!-- 7. Dynamic Eyes (Per Mood) -->
  <g>
    ├── normal:   circle cx=102,138 r=7 fill="#00f5d4" + pupil white highlights
    ├── caffeine: circle cx=102,138 r=7 fill="#ffd166" + pupil white highlights
    ├── sleepy:   path d="M96 102 Q105 108 114 102" & "M126 102 Q135 108 144 102" stroke="#8b5cf6"
    └── rage:     polygon points="95,96 112,102 96,105" & "145,96 128,102 144,105" fill="#ff4d6d"

  <!-- 8. Cute Mouth & Fangs -->
  <path d="M112 120 Q120 128 128 120" stroke="#0f172a" strokeWidth="2.5" fill="none" strokeLinecap="round" />
  ├── Left Fang (polygon points="114,120 117,125 119,120" fill="#ffffff")
  └── Right Fang (polygon points="121,120 123,125 126,120" fill="#ffffff")

  <!-- 9. Forehead Talisman (Tech Glyph) -->
  <g class="animate-talisman-flutter origin-top">
    ├── Yellow Paper (rect x=105 y=55 w=30 h=62 rx=3 fill="url(#talismanGrad)" stroke="#c9182b")
    ├── Red Tech Seal Circle (cx=120 cy=65 r=5 fill="#c9182b") with text "</>"
    ├── Horizontal Tech Lines (y=74, y=78)
    ├── Dynamic Mood Text ("0 BUG" | "COFFEE" | "4:00 AM" | "DEPLOY")
    └── Circuit Rune Path at Bottom (M112 98 L120 102 L128 98 M120 102 L120 108)

  <!-- 10. Caffeine Companion Badge -->
  <g v-if="mood === 'caffeine'" class="animate-bounce" transform="translate(170, 90)">
    <circle cx="15" cy="15" r="14" fill="#070b14" stroke="#ffd166" strokeWidth="1.5" />
    <text x="15" y="21" textAnchor="middle" fontSize="14">☕</text>
  </g>
</svg>
```

### 3.4 Hop Physics, State & Celebration Engine
- **Hop Animation State**: `isJumping = ref(false)`
- **Squash-Stretch Dynamics**:
  - Jump Class: `-translate-y-12 scale-y-110 transition-transform duration-200 ease-out`
  - Hop Class in Idle:
    - Normal: `animate-hop`
    - Caffeine: `animate-hop-fast`
    - Sleepy: `animate-float-slow`
    - Rage: `animate-hop`
- **Dynamic Ground Shadow**:
  - Idle: `w-32 h-4 rounded-full bg-black/70 blur-md scale-100 opacity-70`
  - Jumping: `scale-50 opacity-20` (contracts and fades during elevation)
- **Persistent Hop Counter**:
  - Read from `localStorage.getItem('macatung_hop_counter')` on mount.
  - Increment on every tap and write back to `localStorage`.
- **Milestone Celebration (Every 10 Hops)**:
  - When `hopCount % 10 === 0`:
    1. Call `sound.playSuccess()`
    2. Invoke `confetti({ particleCount: 45, spread: 60, origin: { y: 0.7 }, colors: ['#00f5a0', '#ffd166', '#00d2ff'] })`
    3. Update speech quote: `"🎉 XUẤT SẮC! Đạt ${newCount} cú nhảy Ma Cà Tưng!"`

---

## 4. Talisman & Firefly Particles Canvas Blueprint (`TalismanCanvas.vue`)

### 4.1 Particle System Architecture & Types

```typescript
interface Particle {
  x: number;
  y: number;
  size: number;
  speedX: number;
  speedY: number;
  type: 'talisman' | 'firefly' | 'ember';
  rotation: number;
  rotSpeed: number;
  opacity: number;
  pulseSpeed: number;
  color: string;
}
```

### 4.2 Mathematical Formulas for Dynamics

```
               [Screen Top y = -30]
         ▲               ▲
         │ (Ember)       │ (Firefly)
         │               │
  ◄─── [ Particle (x, y) ] ───► (Repulsion from Mouse)
         │
         │ (Talisman)
         ▼
              [Screen Bottom y = H + 30]
```

1. **Particle Density Scaling**:
   $$N = \min\left(24, \left\lfloor \frac{W_{\text{window}}}{50} \right\rfloor\right)$$
   (e.g., $N=7$ on mobile 360px, $N=24$ on desktop 1200px+).

2. **Kinematic Motion Updates**:
   $$x_{t+1} = x_t + v_x$$
   $$y_{t+1} = y_t + v_y$$
   $$\theta_{t+1} = \theta_t + \omega_{\text{rot}}$$

3. **Sinusoidal Opacity Oscillation**:
   $$\alpha_{\text{talisman}}(t) = \alpha_0 \cdot \left[0.85 + 0.15 \cdot \sin(t \cdot \omega_{\text{pulse}})\right]$$
   $$\alpha_{\text{firefly}}(t) = \alpha_0 \cdot \left[0.50 + 0.30 \cdot \sin(2t \cdot \omega_{\text{pulse}})\right]$$

4. **Radial Mouse Repulsion Field ($R_{\text{repel}} = 100\text{px}$)**:
   $$dx = x - x_{\text{mouse}}, \quad dy = y - y_{\text{mouse}}$$
   $$d = \sqrt{dx^2 + dy^2}$$
   $$\text{If } d < 100\text{px}: \quad \phi = \text{atan2}(dy, dx), \quad F = \frac{100 - d}{100}$$
   $$x \leftarrow x + 1.2 \cdot F \cdot \cos(\phi), \quad y \leftarrow y + 1.2 \cdot F \cdot \sin(\phi)$$

5. **Toroidal Boundary Wrap**:
   $$\text{Wrap } x: \quad \begin{cases} x = W + 20 & \text{if } x < -30 \\ x = -20 & \text{if } x > W + 30 \end{cases}$$
   $$\text{Wrap } y: \quad \begin{cases} y = H + 20 & \text{if } y < -30 \\ y = -20 & \text{if } y > H + 30 \end{cases}$$

### 4.3 Canvas Rendering Specifications
- **Talisman Drawing**:
  - Dimensions: width $w = \text{size}$, height $h = 2.2 \times w$.
  - Fill: `#eed060`, Stroke: `#b91c1c` (width 0.8px).
  - Red Seal: Circle at $(0, -h/2 + 4)$ with radius 1.8px.
  - Tech Runes: Array `['0 BUG', '</>', '⚡', 'DEV', '☕', 'HOP', '12AM']` drawn with font `bold ${Math.max(5.5, Math.floor(w * 0.32))}px monospace`.
- **Firefly & Ember Drawing**:
  - Radial Gradient from $(0,0,0)$ to $(0,0, 2.5 \times \text{size})$: stop 0 = particle color, stop 1 = `'transparent'`.
  - Core: White circle `#ffffff` of radius $0.6 \times \text{size}$.
- **Lifecycle Cleanup**:
  - `window.removeEventListener('resize', handleResize)`
  - `window.removeEventListener('mousemove', handleMouseMove)`
  - `cancelAnimationFrame(animationFrameId)` in `onUnmounted`.

---

## 5. Midnight Clock Blueprint (`MidnightClock.vue`)

### 5.1 Component Structure & Contracts

```vue
<!-- resources/js/Components/mascot/MidnightClock.vue -->
<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Moon, Sun, Coffee, Activity } from 'lucide-vue-next';

// State
const time = ref<Date>(new Date());
const caffeinePercent = ref<number>(98);
let timer: number | undefined;

// Formatting
const hours = computed(() => time.value.getHours());
const isMidnightHours = computed(() => hours.value >= 22 || hours.value <= 5);

const formatDigits = (n: number) => n.toString().padStart(2, '0');
const timeString = computed(() => 
  `${formatDigits(hours.value)}:${formatDigits(time.value.getMinutes())}:${formatDigits(time.value.getSeconds())}`
);

// Lifecycle
onMounted(() => {
  timer = window.setInterval(() => {
    const now = new Date();
    time.value = now;
    const h = now.getHours();
    if (h >= 22 || h <= 4) {
      caffeinePercent.value = 90 + (now.getMinutes() % 10);
    } else {
      caffeinePercent.value = 65 + (now.getMinutes() % 20);
    }
  }, 1000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>
```

### 5.2 Responsive Layout & Breakdown

| Screen Breakpoint | Visible Widgets |
|---|---|
| **Mobile (< 640px)** | Live Time (`HH:mm:ss`) + Pulsing Neon Mint Dot |
| **Tablet (640px - 767px)** | Live Time + Divider + Midnight / Daylight Mode Badge (`Midnight Code Mode` / `Daylight Prep`) |
| **Desktop (768px - 1023px)** | Above + Divider + Caffeine Level (`98%`) with Coffee icon |
| **Wide Desktop (1024px+)** | Above + Divider + Latency Ping (`12ms`) with Activity icon |

### 5.3 Anti-Collision & CSS Protection
- Container class: `flex items-center gap-2 sm:gap-3 px-3 sm:px-3.5 py-1.5 rounded-full glass-panel border border-slate-700/60 shadow-inner text-xs font-mono select-none`
- Zero horizontal overflow: All responsive items use `hidden sm:flex`, `hidden md:flex`, `hidden lg:flex` without breaking container boundaries.

---

## 6. Implementation Guidance for the Worker

| File to Create/Update | Key Actions & Invariants |
|---|---|
| `resources/js/audio/soundEffects.ts` | 1. Implement `SoundEngine` class with `ISoundEngine` interface.<br>2. Export singleton `sound`.<br>3. Ensure strict SSR safety and browser autoplay recovery. |
| `resources/js/Components/mascot/MacatungMascot.vue` | 1. Implement SVG anatomy with all gradients and path elements.<br>2. Add 4 mood states with reactive eye and talisman text changes.<br>3. Bind click/touch and keydown handlers with 450ms hop timer.<br>4. Integrate `canvas-confetti` and `sound.playSuccess()` on multiples of 10 hops.<br>5. Persist hop count to `localStorage`. |
| `resources/js/Components/mascot/TalismanCanvas.vue` | 1. Implement 2D canvas particle loop with `requestAnimationFrame`.<br>2. Support yellow talismans, embers, and fireflies with mouse repulsion.<br>3. Clean up event listeners and animation frame on unmount. |
| `resources/js/Components/mascot/MidnightClock.vue` | 1. Real-time `HH:mm:ss` digital clock with 1-second interval.<br>2. Compute Midnight Mode vs Daylight Prep.<br>3. Implement responsive breakpoint visibility. |
| `resources/js/Components/layout/SoundToggle.vue` | 1. Volume toggle button calling `sound.toggleMute()`.<br>2. Animated pulse indicator when active. |

---

## 7. Verification Strategy

1. **Compilation Check**:
   ```powershell
   npm.cmd run build
   ```
   Must compile cleanly with zero TypeScript or Vue SFC syntax errors.
2. **Unit & Audio Verification**:
   - Verify `sound.isMuted()` defaults correctly and toggles with persistence.
   - Verify all 5 audio methods (`playHop`, `playTalisman`, `playClick`, `playTerminalKey`, `playSuccess`) execute without runtime exceptions even when context is uninitialized or suspended.
3. **Mascot Interaction Test**:
   - Click mascot: hop counter increments, hop class activates for 450ms, audio triggers.
   - Switch moods: eye shape and talisman text change immediately.
   - Hop 10 times: Confetti burst and fanfare audio fire.
4. **Canvas Performance Test**:
   - Move mouse: particles within 100px smoothly repel.
   - Leave page / unmount: no orphaned `requestAnimationFrame` loops.
