/**
 * Web Audio Procedural Synthesis for Oracle Divination
 * Sounds: Bamboo tube rattle, Yin-Yang coin clink, Magic revelation gong
 */

class OracleAudioSynth {
  private ctx: AudioContext | null = null;
  private isMuted: boolean = false;

  private getContext(): AudioContext | null {
    if (typeof window === 'undefined') return null;
    if (!this.ctx) {
      const AudioCtx = window.AudioContext || (window as any).webkitAudioContext;
      if (AudioCtx) {
        this.ctx = new AudioCtx();
      }
    }
    if (this.ctx && this.ctx.state === 'suspended') {
      this.ctx.resume();
    }
    return this.ctx;
  }

  public setMuted(muted: boolean) {
    this.isMuted = muted;
  }

  // 1. Bamboo Stick Rattle Sound (Tiếng que xăm tre va chạm xào xạc)
  public playBambooRattle() {
    if (this.isMuted) return;
    const ctx = this.getContext();
    if (!ctx) return;

    const now = ctx.currentTime;
    // Series of short wooden percussion clicks
    for (let i = 0; i < 6; i++) {
      const timeOffset = now + (i * 0.05) + (Math.random() * 0.02);
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      const filter = ctx.createBiquadFilter();

      filter.type = 'bandpass';
      filter.frequency.setValueAtTime(800 + Math.random() * 600, timeOffset);
      filter.Q.setValueAtTime(4, timeOffset);

      osc.type = 'triangle';
      osc.frequency.setValueAtTime(220 + Math.random() * 180, timeOffset);

      gain.gain.setValueAtTime(0.08, timeOffset);
      gain.gain.exponentialRampToValueAtTime(0.001, timeOffset + 0.04);

      osc.connect(filter);
      filter.connect(gain);
      gain.connect(ctx.destination);

      osc.start(timeOffset);
      osc.stop(timeOffset + 0.05);
    }
  }

  // 2. Yin-Yang Coin Toss Sound (Tiếng đồng xu âm dương kêu leng keng)
  public playCoinToss() {
    if (this.isMuted) return;
    const ctx = this.getContext();
    if (!ctx) return;

    const now = ctx.currentTime;
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();

    osc.type = 'sine';
    osc.frequency.setValueAtTime(1400, now);
    osc.frequency.exponentialRampToValueAtTime(2200, now + 0.08);

    gain.gain.setValueAtTime(0.12, now);
    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);

    osc.connect(gain);
    gain.connect(ctx.destination);

    osc.start(now);
    osc.stop(now + 0.26);
  }

  // 3. Sacred Oracle Revelation Gong (Tiếng chuông / chiêng mở quẻ linh thiêng)
  public playRevelationGong() {
    if (this.isMuted) return;
    const ctx = this.getContext();
    if (!ctx) return;

    const now = ctx.currentTime;
    const freqs = [528, 792, 1056]; // Solfeggio 528Hz Miracle frequency chord

    freqs.forEach(freq => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();

      osc.type = 'sine';
      osc.frequency.setValueAtTime(freq, now);

      gain.gain.setValueAtTime(0.1, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 1.2);

      osc.connect(gain);
      gain.connect(ctx.destination);

      osc.start(now);
      osc.stop(now + 1.3);
    });
  }
}

export const oracleAudio = new OracleAudioSynth();
