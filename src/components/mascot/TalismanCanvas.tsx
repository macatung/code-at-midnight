import React, { useEffect, useRef } from 'react';

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

export const TalismanCanvas: React.FC = () => {
  const canvasRef = useRef<HTMLCanvasElement | null>(null);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let animationFrameId: number;
    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    const handleResize = () => {
      if (!canvas) return;
      width = canvas.width = window.innerWidth;
      height = canvas.height = window.innerHeight;
    };

    window.addEventListener('resize', handleResize);

    // Create subtle particles
    const particleCount = Math.min(24, Math.floor(window.innerWidth / 50));
    const particles: Particle[] = [];

    // Clean tech rune symbols (no Chinese characters)
    const runeSymbols = ['0 BUG', '</>', '⚡', 'DEV', '☕', 'HOP', '12AM'];

    for (let i = 0; i < particleCount; i++) {
      const isTalisman = i % 4 === 0;
      const isEmber = i % 4 === 1;

      particles.push({
        x: Math.random() * width,
        y: Math.random() * height,
        size: isTalisman ? Math.random() * 6 + 12 : Math.random() * 1.5 + 1.2,
        speedX: (Math.random() - 0.5) * 0.3,
        speedY: isTalisman ? (Math.random() * 0.25 + 0.1) : -(Math.random() * 0.3 + 0.08),
        type: isTalisman ? 'talisman' : isEmber ? 'ember' : 'firefly',
        rotation: Math.random() * Math.PI * 2,
        rotSpeed: (Math.random() - 0.5) * 0.01,
        opacity: Math.random() * 0.35 + 0.15,
        pulseSpeed: Math.random() * 0.015 + 0.008,
        color: isTalisman
          ? '#ffd166'
          : isEmber
          ? '#ff4d6d'
          : Math.random() > 0.5
          ? '#00f5a0'
          : '#00d2ff',
      });
    }

    let mouseX = -1000;
    let mouseY = -1000;

    const handleMouseMove = (e: MouseEvent) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    };

    window.addEventListener('mousemove', handleMouseMove);

    // Render loop
    let tick = 0;
    const render = () => {
      tick++;
      ctx.clearRect(0, 0, width, height);

      particles.forEach((p, idx) => {
        // Move
        p.x += p.speedX;
        p.y += p.speedY;
        p.rotation += p.rotSpeed;

        // Subtle mouse repulsion
        const dx = p.x - mouseX;
        const dy = p.y - mouseY;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 100) {
          const angle = Math.atan2(dy, dx);
          const force = (100 - dist) / 100;
          p.x += Math.cos(angle) * force * 1.2;
          p.y += Math.sin(angle) * force * 1.2;
        }

        // Screen wrap
        if (p.x < -30) p.x = width + 20;
        if (p.x > width + 30) p.x = -20;
        if (p.y < -30) p.y = height + 20;
        if (p.y > height + 30) p.y = -20;

        // Draw Talisman or Firefly
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate(p.rotation);

        if (p.type === 'talisman') {
          // Yellow paper talisman
          const w = p.size;
          const h = p.size * 2.2;
          const currentOpacity = p.opacity * (0.85 + Math.sin(tick * p.pulseSpeed) * 0.15);

          ctx.globalAlpha = currentOpacity;

          // Paper body
          ctx.fillStyle = '#eed060';
          ctx.fillRect(-w / 2, -h / 2, w, h);

          // Red border & header
          ctx.strokeStyle = '#b91c1c';
          ctx.lineWidth = 0.8;
          ctx.strokeRect(-w / 2 + 1, -h / 2 + 1, w - 2, h - 2);

          // Code icon circle
          ctx.fillStyle = '#b91c1c';
          ctx.beginPath();
          ctx.arc(0, -h / 2 + 4, 1.8, 0, Math.PI * 2);
          ctx.fill();

          // Symbol
          const symbol = runeSymbols[idx % runeSymbols.length];
          ctx.fillStyle = '#7f1d1d';
          ctx.font = `bold ${Math.max(5.5, Math.floor(w * 0.32))}px monospace`;
          ctx.textAlign = 'center';
          ctx.fillText(symbol, 0, 2);

        } else {
          // Subtle firefly
          const currentOpacity = p.opacity * (0.5 + Math.sin(tick * p.pulseSpeed * 2) * 0.3);
          ctx.globalAlpha = currentOpacity;

          const glowRad = p.size * 2.5;
          const grad = ctx.createRadialGradient(0, 0, 0, 0, 0, glowRad);
          grad.addColorStop(0, p.color);
          grad.addColorStop(1, 'transparent');

          ctx.fillStyle = grad;
          ctx.beginPath();
          ctx.arc(0, 0, glowRad, 0, Math.PI * 2);
          ctx.fill();

          ctx.fillStyle = '#ffffff';
          ctx.beginPath();
          ctx.arc(0, 0, p.size * 0.6, 0, Math.PI * 2);
          ctx.fill();
        }

        ctx.restore();
      });

      animationFrameId = requestAnimationFrame(render);
    };

    render();

    return () => {
      window.removeEventListener('resize', handleResize);
      window.removeEventListener('mousemove', handleMouseMove);
      cancelAnimationFrame(animationFrameId);
    };
  }, []);

  return (
    <canvas
      ref={canvasRef}
      className="fixed inset-0 pointer-events-none z-0"
      style={{ opacity: 0.6 }}
    />
  );
};
