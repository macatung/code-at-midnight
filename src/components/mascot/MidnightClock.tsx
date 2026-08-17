import React, { useState, useEffect } from 'react';
import { Moon, Sun, Coffee, Activity } from 'lucide-react';

export const MidnightClock: React.FC = () => {
  const [time, setTime] = useState<Date>(new Date());
  const [caffeinePercent, setCaffeinePercent] = useState<number>(98);

  useEffect(() => {
    const timer = setInterval(() => {
      const now = new Date();
      setTime(now);
      // Fun dynamic caffeine calculation
      const hours = now.getHours();
      if (hours >= 22 || hours <= 4) {
        setCaffeinePercent(90 + (now.getMinutes() % 10));
      } else {
        setCaffeinePercent(65 + (now.getMinutes() % 20));
      }
    }, 1000);

    return () => clearInterval(timer);
  }, []);

  const hours = time.getHours();
  const isMidnightHours = hours >= 22 || hours <= 5;

  const formatDigits = (n: number) => n.toString().padStart(2, '0');
  const timeString = `${formatDigits(hours)}:${formatDigits(time.getMinutes())}:${formatDigits(time.getSeconds())}`;

  return (
    <div className="flex flex-wrap items-center gap-3 px-3.5 py-1.5 rounded-full glass-panel border border-slate-700/60 shadow-inner text-xs font-mono">
      {/* Live Time */}
      <div className="flex items-center gap-1.5 text-slate-200">
        <span className="relative flex h-2 w-2">
          <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-phantom-mint opacity-75"></span>
          <span className="relative inline-flex rounded-full h-2 w-2 bg-phantom-mint"></span>
        </span>
        <span className="font-bold tracking-wider text-phantom-mint">{timeString}</span>
      </div>

      <div className="h-3 w-px bg-slate-700 hidden sm:block" />

      {/* Midnight State Badge */}
      <div className="hidden sm:flex items-center gap-1.5">
        {isMidnightHours ? (
          <span className="flex items-center gap-1 text-emerald-300">
            <Moon className="w-3.5 h-3.5 text-talisman-yellow animate-pulse" />
            <span className="font-semibold text-[11px]">Midnight Code Mode</span>
          </span>
        ) : (
          <span className="flex items-center gap-1 text-amber-300">
            <Sun className="w-3.5 h-3.5 text-amber-400" />
            <span className="font-semibold text-[11px]">Daylight Prep</span>
          </span>
        )}
      </div>

      <div className="h-3 w-px bg-slate-700 hidden md:block" />

      {/* Caffeine Level */}
      <div className="hidden md:flex items-center gap-1.5 text-slate-400">
        <Coffee className="w-3.5 h-3.5 text-amber-400" />
        <span>Caffeine:</span>
        <span className="text-amber-300 font-bold">{caffeinePercent}%</span>
      </div>

      <div className="h-3 w-px bg-slate-700 hidden lg:block" />

      {/* Latency */}
      <div className="hidden lg:flex items-center gap-1 text-slate-400">
        <Activity className="w-3.5 h-3.5 text-cyan-400" />
        <span className="text-cyan-300">12ms</span>
      </div>
    </div>
  );
};
