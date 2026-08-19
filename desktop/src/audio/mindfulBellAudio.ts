/**
 * Mindful Bell Synthesizer (Tibetan Singing Bowl Web Audio API)
 * Zero external audio assets needed; generates pure, resonant harmonic acoustic bell.
 */
class MindfulBellAudio {
  private ctx: AudioContext | null = null;

  private initContext() {
    if (!this.ctx) {
      const AudioCtx = window.AudioContext || (window as any).webkitAudioContext;
      if (AudioCtx) {
        this.ctx = new AudioCtx();
      }
    }
    if (this.ctx && this.ctx.state === 'suspended') {
      this.ctx.resume();
    }
  }

  /**
   * Ring the resonant meditation bell with authentic harmonic overtones
   * @param fundamentalFreq Fundamental frequency (default 432Hz for deep peace, or 528Hz for clarity)
   * @param duration Sustain duration in seconds (default 5.5s)
   */
  public ringBell(fundamentalFreq: number = 432, duration: number = 5.5) {
    try {
      this.initContext();
      if (!this.ctx) return;

      const now = this.ctx.currentTime;
      const masterGain = this.ctx.createGain();
      masterGain.gain.setValueAtTime(0.35, now);
      masterGain.gain.exponentialRampToValueAtTime(0.0001, now + duration);
      masterGain.connect(this.ctx.destination);

      // Tibetan Singing Bowl Harmonic Profile:
      // 1. Fundamental Root (1.0x)
      // 2. Minor Third / Octave Overtone (2.76x)
      // 3. Perfect Fifth Overtone (5.4x)
      // 4. High Shimmer Strike (8.9x)
      const harmonics = [
        { mult: 1.0, gainVal: 0.8, decayMult: 1.0 },
        { mult: 2.76, gainVal: 0.45, decayMult: 0.85 },
        { mult: 5.4, gainVal: 0.2, decayMult: 0.6 },
        { mult: 8.9, gainVal: 0.08, decayMult: 0.3 },
      ];

      harmonics.forEach(({ mult, gainVal, decayMult }) => {
        if (!this.ctx) return;
        const osc = this.ctx.createOscillator();
        const oscGain = this.ctx.createGain();

        osc.type = 'sine';
        osc.frequency.setValueAtTime(fundamentalFreq * mult, now);

        // Gentle strike envelope
        oscGain.gain.setValueAtTime(0.001, now);
        oscGain.gain.exponentialRampToValueAtTime(gainVal, now + 0.04);
        oscGain.gain.exponentialRampToValueAtTime(0.0001, now + duration * decayMult);

        osc.connect(oscGain);
        oscGain.connect(masterGain);

        osc.start(now);
        osc.stop(now + duration * decayMult);
      });
    } catch (e) {
      console.warn('Audio playback not ready:', e);
    }
  }
}

export const mindfulBell = new MindfulBellAudio();
