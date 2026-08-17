import { ref, computed, onMounted, onUnmounted } from 'vue';
import { playCelestialChime } from '@/audio/soundEffects';

export type TimePhaseId = 'midnight' | 'dawn' | 'afternoon' | 'twilight';

export interface TimePhaseConfig {
  id: TimePhaseId;
  name: string;
  vietnameseName: string;
  subtitle: string;
  timeRange: string;
  icon: string;
  greeting: string;
  statusBeacon: string;
  caffeineLevel: number;
  accentHex: string;
  accentGlow: string;
  accentBorder: string;
  gradientClass: string;
  particlePalette: string[];
  mascotState: 'Normal' | 'Caffeine' | 'Sleepy' | 'Rage';
  mascotAccessory: 'none' | 'coffee' | 'sunglasses' | 'talisman';
  tagline: string;
  themeDescription: string;
}

export const TIME_PHASES: Record<TimePhaseId, TimePhaseConfig> = {
  midnight: {
    id: 'midnight',
    name: 'Midnight Void',
    vietnameseName: 'Đêm Sâu Ma Thuật',
    subtitle: 'Code at Midnight — Maximum Focus & Deep Flow',
    timeRange: '00:00 — 05:59',
    icon: 'Moon',
    greeting: 'Chào buổi đêm, lữ khách bóng tối!',
    statusBeacon: '🟢 SẴN SÀNG NHẬN QUEST 00:00 AM',
    caffeineLevel: 100,
    accentHex: '#00f5a0',
    accentGlow: 'rgba(0, 245, 160, 0.35)',
    accentBorder: 'rgba(0, 245, 160, 0.4)',
    gradientClass: 'from-emerald-400 via-teal-300 to-cyan-400',
    particlePalette: ['#00f5a0', '#00d2ff', '#ffd166', '#7000ff'],
    mascotState: 'Caffeine',
    mascotAccessory: 'talisman',
    tagline: 'Vạn vật say ngủ, dòng code thức giấc',
    themeDescription: 'Thời điểm tâm trí hòa cùng tần số vũ trụ, zero distraction, năng suất cực đại.'
  },
  dawn: {
    id: 'dawn',
    name: 'Golden Dawn',
    vietnameseName: 'Rạng Đông Hổ Phách',
    subtitle: 'Dawn Caster — Fresh Mind & Morning Forge',
    timeRange: '06:00 — 11:59',
    icon: 'Sunrise',
    greeting: 'Chào buổi sáng rực rỡ, khởi đầu ngày mới!',
    statusBeacon: '🟡 ĐANG TIẾP NĂNG LƯỢNG SÁNG & STANDUP',
    caffeineLevel: 45,
    accentHex: '#ffd166',
    accentGlow: 'rgba(255, 209, 102, 0.35)',
    accentBorder: 'rgba(255, 209, 102, 0.4)',
    gradientClass: 'from-amber-400 via-yellow-300 to-orange-400',
    particlePalette: ['#ffd166', '#f59e0b', '#fbbf24', '#00f5a0'],
    mascotState: 'Normal',
    mascotAccessory: 'coffee',
    tagline: 'Tách cà phê sớm, nạp trọn linh khí',
    themeDescription: 'Không khí trong lành, tư duy minh mẫn, chuẩn bị kiến trúc và lên kế hoạch tác chiến.'
  },
  afternoon: {
    id: 'afternoon',
    name: 'High-Noon Forge',
    vietnameseName: 'Chính Ngọ Cyber',
    subtitle: 'Cyber Architect — Shipping & Production Flow',
    timeRange: '12:00 — 17:59',
    icon: 'Sun',
    greeting: 'Chào buổi chiều năng suất, ship tính năng!',
    statusBeacon: '🔵 ĐANG SHIP TÍNH NĂNG CAO ĐIỂM & REVIEW',
    caffeineLevel: 65,
    accentHex: '#00d2ff',
    accentGlow: 'rgba(0, 210, 255, 0.35)',
    accentBorder: 'rgba(0, 210, 255, 0.4)',
    gradientClass: 'from-cyan-400 via-sky-300 to-blue-500',
    particlePalette: ['#00d2ff', '#38bdf8', '#818cf8', '#00f5a0'],
    mascotState: 'Normal',
    mascotAccessory: 'sunglasses',
    tagline: 'Tập trung cao độ, bứt phá tiến độ',
    themeDescription: 'Tối ưu hóa hiệu năng, review code chuẩn chỉ, triển khai các giải pháp kiên cố.'
  },
  twilight: {
    id: 'twilight',
    name: 'Twilight Dusk',
    vietnameseName: 'Hoàng Hôn Tím',
    subtitle: 'Twilight Alchemist — Night-Shift Warmup',
    timeRange: '18:00 — 23:59',
    icon: 'Sunset',
    greeting: 'Hoàng hôn buông xuống, bóng đêm trỗi dậy!',
    statusBeacon: '🟣 ĐANG KHỞI ĐỘNG CA ĐÊM & REFACTOR',
    caffeineLevel: 80,
    accentHex: '#c084fc',
    accentGlow: 'rgba(192, 132, 252, 0.35)',
    accentBorder: 'rgba(192, 132, 252, 0.4)',
    gradientClass: 'from-purple-400 via-fuchsia-300 to-pink-400',
    particlePalette: ['#c084fc', '#f43f5e', '#e879f9', '#ffd166'],
    mascotState: 'Caffeine',
    mascotAccessory: 'talisman',
    tagline: 'Ánh đèn neon bật, phù phép bắt đầu',
    themeDescription: 'Chuyển giao năng lượng từ ngày sang đêm, vươn vai khởi động cho ca thâu đêm.'
  }
};

// Calculate phase from date
export function getPhaseFromDate(date: Date): TimePhaseId {
  const hour = date.getHours();
  if (hour >= 0 && hour < 6) return 'midnight';
  if (hour >= 6 && hour < 12) return 'dawn';
  if (hour >= 12 && hour < 18) return 'afternoon';
  return 'twilight';
}

// Global Singleton State
const currentTime = ref<Date>(new Date());
const manualOverridePhase = ref<TimePhaseId | null>(null);
const transitionToast = ref<{
  visible: boolean;
  phaseId: TimePhaseId;
  message: string;
  subtitle: string;
} | null>(null);

let timerId: number | undefined;
let listenersCount = 0;
let lastDetectedPhase: TimePhaseId | null = null;
let toastTimeoutId: number | undefined;

// Load persisted override from sessionStorage if available
if (typeof window !== 'undefined') {
  try {
    const saved = sessionStorage.getItem('macatung_time_travel_phase');
    if (saved && (saved in TIME_PHASES)) {
      manualOverridePhase.value = saved as TimePhaseId;
    }
  } catch {
    // Ignore storage restrictions
  }
}

export function useTimeCycle() {
  const pad = (n: number) => String(n).padStart(2, '0');

  const formattedTime = computed(() => {
    const d = currentTime.value;
    return `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
  });

  const realPhaseId = computed<TimePhaseId>(() => getPhaseFromDate(currentTime.value));

  const activePhaseId = computed<TimePhaseId>(() => {
    return manualOverridePhase.value || realPhaseId.value;
  });

  const activePhase = computed<TimePhaseConfig>(() => {
    return TIME_PHASES[activePhaseId.value];
  });

  const isTimeTravelActive = computed(() => manualOverridePhase.value !== null);

  // Trigger phase transition toast & celestial sound
  function triggerPhaseTransition(newPhaseId: TimePhaseId, isManual: boolean = false) {
    const phase = TIME_PHASES[newPhaseId];
    
    // Play celestial chime audio
    try {
      playCelestialChime(newPhaseId);
    } catch {
      // Audio autoplay restrictions handled inside synth
    }

    // Show toast
    if (toastTimeoutId) clearTimeout(toastTimeoutId);
    transitionToast.value = {
      visible: true,
      phaseId: newPhaseId,
      message: isManual
        ? `⚡ Du hành thời gian: ${phase.name} (${phase.vietnameseName})`
        : `🌙 Chuyển giao phân kỳ: ${phase.name} (${phase.vietnameseName})`,
      subtitle: phase.subtitle
    };

    toastTimeoutId = window.setTimeout(() => {
      if (transitionToast.value) {
        transitionToast.value.visible = false;
      }
    }, 4500);
  }

  function setPhaseOverride(phaseId: TimePhaseId) {
    if (manualOverridePhase.value === phaseId) return;
    manualOverridePhase.value = phaseId;
    try {
      sessionStorage.setItem('macatung_time_travel_phase', phaseId);
    } catch {}
    triggerPhaseTransition(phaseId, true);
  }

  function resetToRealTime() {
    if (manualOverridePhase.value === null) return;
    manualOverridePhase.value = null;
    try {
      sessionStorage.removeItem('macatung_time_travel_phase');
    } catch {}
    const real = realPhaseId.value;
    triggerPhaseTransition(real, false);
  }

  onMounted(() => {
    listenersCount++;
    if (listenersCount === 1) {
      lastDetectedPhase = realPhaseId.value;
      timerId = window.setInterval(() => {
        currentTime.value = new Date();
        const currentReal = getPhaseFromDate(currentTime.value);
        if (!manualOverridePhase.value && lastDetectedPhase && currentReal !== lastDetectedPhase) {
          lastDetectedPhase = currentReal;
          triggerPhaseTransition(currentReal, false);
        }
      }, 1000);
    }
  });

  onUnmounted(() => {
    listenersCount--;
    if (listenersCount <= 0) {
      listenersCount = 0;
      if (timerId) {
        clearInterval(timerId);
        timerId = undefined;
      }
    }
  });

  return {
    currentTime,
    formattedTime,
    realPhaseId,
    activePhaseId,
    activePhase,
    isTimeTravelActive,
    manualOverridePhase,
    transitionToast,
    TIME_PHASES,
    setPhaseOverride,
    resetToRealTime,
    getPhaseFromDate
  };
}
