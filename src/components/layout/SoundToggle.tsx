import React, { useState } from 'react';
import { sound } from '../../audio/soundEffects';
import { Volume2, VolumeX } from 'lucide-react';

export const SoundToggle: React.FC = () => {
  const [isMuted, setIsMuted] = useState<boolean>(sound.isMuted());

  const handleToggle = () => {
    const nextState = sound.toggleMute();
    setIsMuted(nextState);
  };

  return (
    <button
      onClick={handleToggle}
      className={`p-2 rounded-xl border transition-all duration-200 flex items-center justify-center ${
        isMuted
          ? 'bg-slate-900/60 border-slate-700/60 text-slate-400 hover:text-slate-200'
          : 'bg-emerald-950/40 border-emerald-500/40 text-emerald-300 shadow-glow-mint'
      }`}
      title={isMuted ? 'Bật âm thanh (Sound Effects: OFF)' : 'Tắt âm thanh (Sound Effects: ON)'}
      aria-label="Toggle Sound Effects"
    >
      {isMuted ? (
        <VolumeX className="w-4 h-4" />
      ) : (
        <div className="flex items-center gap-1">
          <Volume2 className="w-4 h-4 animate-pulse" />
          <span className="text-[10px] font-mono font-bold hidden sm:inline">SFX</span>
        </div>
      )}
    </button>
  );
};
