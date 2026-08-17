import React, { useState } from 'react';
import { sound } from '../../audio/soundEffects';
import confetti from 'canvas-confetti';
import { Send, Mail, MessageSquare, Sparkles, CheckCircle2, Coffee } from 'lucide-react';
import { GithubIcon, LinkedinIcon } from '../ui/Icons';

export const ContactSection: React.FC = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    projectType: 'Full-Stack Web App',
    coffeeOffering: '1 Ly Robusta Đen Đậm Đặc ☕',
    message: '',
  });

  const [submitted, setSubmitted] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.message) return;

    setLoading(true);
    sound.playTalisman();

    setTimeout(() => {
      setLoading(false);
      setSubmitted(true);
      sound.playSuccess();

      confetti({
        particleCount: 80,
        spread: 70,
        origin: { y: 0.6 },
        colors: ['#00f5a0', '#ffd166', '#ff0054', '#00d2ff'],
      });
    }, 600);
  };

  return (
    <section id="contact" className="py-24 relative">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto mb-16 space-y-4">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border border-slate-700 text-xs font-mono text-emerald-300">
            <Mail className="w-3.5 h-3.5 text-talisman-yellow" />
            <span>// SUMMONING ALTAR</span>
          </div>
          <h2 className="font-display font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
            Summon <span className="text-phantom-mint text-glow-mint">Ma Cà Tưng</span>
          </h2>
          <p className="text-slate-400 font-mono text-sm sm:text-base">
            Bạn có ý tưởng dự án tham vọng hoặc muốn đồng hành cùng một kỹ sư tận tâm? Hãy phát tín hiệu triệu hồi ngay!
          </p>
        </div>

        {/* Grid: Contact Form on Left, Direct Channels on Right */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* Summoning Form */}
          <div className="lg:col-span-7 rounded-3xl glass-panel-glow border border-emerald-500/30 p-6 sm:p-8 relative">
            
            {submitted ? (
              <div className="py-12 text-center space-y-4 animate-fadeIn">
                <div className="w-16 h-16 rounded-full bg-emerald-500/20 border-2 border-emerald-400 mx-auto flex items-center justify-center text-emerald-300 shadow-glow-mint">
                  <CheckCircle2 className="w-8 h-8" />
                </div>
                <h3 className="font-display font-bold text-2xl text-white">
                  Tín Hiệu Triệu Hồi Đã Được Tiếp Nhận! ⚡
                </h3>
                <p className="text-slate-300 font-mono text-sm max-w-md mx-auto leading-relaxed">
                  Cảm ơn <strong className="text-emerald-300">{formData.name}</strong>. Ma Cà Tưng đã nhận được phong thư cùng lời thỉnh cầu. Tôi sẽ hồi đáp email của bạn ngay trong chu kỳ mặt trăng tiếp theo!
                </p>
                <div className="pt-4">
                  <button
                    onClick={() => {
                      sound.playClick();
                      setSubmitted(false);
                      setFormData({
                        name: '',
                        email: '',
                        projectType: 'Full-Stack Web App',
                        coffeeOffering: '1 Ly Robusta Đen Đậm Đặc ☕',
                        message: '',
                      });
                    }}
                    className="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-mono text-xs transition-colors"
                  >
                    Gửi thêm một thông điệp khác
                  </button>
                </div>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className="space-y-5">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  
                  {/* Name */}
                  <div className="space-y-1.5">
                    <label className="text-xs font-mono text-slate-300 font-bold">
                      Tên của bạn / Tổ chức: *
                    </label>
                    <input
                      type="text"
                      required
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      placeholder="Ví dụ: Alex Nguyễn"
                      className="w-full px-4 py-3 rounded-xl bg-midnight-900 border border-slate-800 text-white placeholder:text-slate-600 focus:outline-none focus:border-emerald-500 font-mono text-xs sm:text-sm"
                    />
                  </div>

                  {/* Email */}
                  <div className="space-y-1.5">
                    <label className="text-xs font-mono text-slate-300 font-bold">
                      Địa chỉ Email phản hồi: *
                    </label>
                    <input
                      type="email"
                      required
                      value={formData.email}
                      onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                      placeholder="alex@company.com"
                      className="w-full px-4 py-3 rounded-xl bg-midnight-900 border border-slate-800 text-white placeholder:text-slate-600 focus:outline-none focus:border-emerald-500 font-mono text-xs sm:text-sm"
                    />
                  </div>

                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  
                  {/* Project Type */}
                  <div className="space-y-1.5">
                    <label className="text-xs font-mono text-slate-300 font-bold">
                      Mục đích / Loại Dự án:
                    </label>
                    <select
                      value={formData.projectType}
                      onChange={(e) => setFormData({ ...formData, projectType: e.target.value })}
                      className="w-full px-4 py-3 rounded-xl bg-midnight-900 border border-slate-800 text-white focus:outline-none focus:border-emerald-500 font-mono text-xs sm:text-sm"
                    >
                      <option value="Full-Stack Web App">Full-Stack Web Application</option>
                      <option value="Creative UI/UX & Web Audio">Creative UI/UX & Web Audio</option>
                      <option value="High-Throughput Microservice">High-Throughput Microservice</option>
                      <option value="AI Agents & Automation">AI Agents & Workflow Automation</option>
                      <option value="Tech Lead / Architecture Consulting">Tư vấn Kiến trúc & Tech Lead</option>
                      <option value="Other Quest">Ý tưởng độc đáo khác</option>
                    </select>
                  </div>

                  {/* Coffee Offering */}
                  <div className="space-y-1.5">
                    <label className="text-xs font-mono text-slate-300 font-bold flex items-center gap-1">
                      <Coffee className="w-3.5 h-3.5 text-amber-400" />
                      <span>Lễ vật cà phê đính kèm:</span>
                    </label>
                    <select
                      value={formData.coffeeOffering}
                      onChange={(e) => setFormData({ ...formData, coffeeOffering: e.target.value })}
                      className="w-full px-4 py-3 rounded-xl bg-midnight-900 border border-slate-800 text-amber-200 focus:outline-none focus:border-emerald-500 font-mono text-xs sm:text-sm"
                    >
                      <option value="1 Ly Robusta Đen Đậm Đặc ☕">1 Ly Robusta Đen Đậm Đặc ☕</option>
                      <option value="X2 Espresso Shot Không Đường ☕⚡">X2 Espresso Shot Không Đường ☕⚡</option>
                      <option value="1 Ly Bạc Xỉu Sữa Đặc Đêm Khuya 🥛">1 Ly Bạc Xỉu Sữa Đặc Đêm Khuya 🥛</option>
                      <option value="Full Thùng Nước Tăng Lực Monster 🔋">Full Thùng Monster Energy 🔋</option>
                    </select>
                  </div>

                </div>

                {/* Message */}
                <div className="space-y-1.5">
                  <label className="text-xs font-mono text-slate-300 font-bold">
                    Lời triệu hồi / Mô tả dự án: *
                  </label>
                  <textarea
                    required
                    rows={4}
                    value={formData.message}
                    onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                    placeholder="Mô tả mục tiêu, timeline dự kiến hoặc bất kỳ điều gì bạn muốn chia sẻ..."
                    className="w-full px-4 py-3 rounded-xl bg-midnight-900 border border-slate-800 text-white placeholder:text-slate-600 focus:outline-none focus:border-emerald-500 font-mono text-xs sm:text-sm resize-none"
                  />
                </div>

                {/* Submit button */}
                <button
                  type="submit"
                  disabled={loading}
                  className="w-full py-3.5 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-500 text-midnight-950 font-bold font-mono text-sm flex items-center justify-center gap-2 shadow-glow-mint hover:scale-[1.01] active:scale-[0.99] transition-all disabled:opacity-50"
                >
                  {loading ? (
                    <span>Đang niệm phép gửi thư... ⏳</span>
                  ) : (
                    <>
                      <Send className="w-4 h-4" />
                      <span>Cast Summon Spell (Phát Tín Hiệu) ⚡</span>
                    </>
                  )}
                </button>
              </form>
            )}

          </div>

          {/* Direct Channels Cards */}
          <div className="lg:col-span-5 space-y-4">
            
            <div className="p-6 rounded-3xl glass-panel border border-slate-800 space-y-4">
              <h3 className="font-display font-bold text-xl text-white flex items-center gap-2">
                <Sparkles className="w-5 h-5 text-talisman-yellow" />
                <span>Direct Spectral Channels</span>
              </h3>
              <p className="text-xs sm:text-sm text-slate-400 font-mono leading-relaxed">
                Thích trao đổi trực tiếp qua các kênh liên lạc tức thời? Bạn có thể liên hệ Ma Cà Tưng qua các đường dẫn dưới đây:
              </p>

              <div className="space-y-2.5 pt-2">
                <a
                  href="mailto:summon@macatung.dev"
                  onClick={() => sound.playClick()}
                  className="p-3.5 rounded-2xl bg-midnight-900/90 border border-slate-800 hover:border-emerald-500/50 flex items-center justify-between group transition-all"
                >
                  <div className="flex items-center gap-3">
                    <div className="p-2 rounded-xl bg-emerald-500/10 text-emerald-400">
                      <Mail className="w-4 h-4" />
                    </div>
                    <div>
                      <div className="text-xs font-mono font-bold text-white group-hover:text-phantom-mint">Email</div>
                      <div className="text-[11px] font-mono text-slate-400">summon@macatung.dev</div>
                    </div>
                  </div>
                  <span className="text-xs font-mono text-slate-500 group-hover:text-white">→</span>
                </a>

                <a
                  href="https://github.com/macatung"
                  target="_blank"
                  rel="noreferrer"
                  onClick={() => sound.playClick()}
                  className="p-3.5 rounded-2xl bg-midnight-900/90 border border-slate-800 hover:border-emerald-500/50 flex items-center justify-between group transition-all"
                >
                  <div className="flex items-center gap-3">
                    <div className="p-2 rounded-xl bg-slate-800 text-slate-200">
                      <GithubIcon className="w-4 h-4" />
                    </div>
                    <div>
                      <div className="text-xs font-mono font-bold text-white group-hover:text-phantom-mint">GitHub</div>
                      <div className="text-[11px] font-mono text-slate-400">github.com/macatung</div>
                    </div>
                  </div>
                  <span className="text-xs font-mono text-slate-500 group-hover:text-white">→</span>
                </a>

                <a
                  href="https://t.me"
                  target="_blank"
                  rel="noreferrer"
                  onClick={() => sound.playClick()}
                  className="p-3.5 rounded-2xl bg-midnight-900/90 border border-slate-800 hover:border-cyan-500/50 flex items-center justify-between group transition-all"
                >
                  <div className="flex items-center gap-3">
                    <div className="p-2 rounded-xl bg-cyan-500/10 text-cyan-400">
                      <MessageSquare className="w-4 h-4" />
                    </div>
                    <div>
                      <div className="text-xs font-mono font-bold text-white group-hover:text-cyan-300">Telegram</div>
                      <div className="text-[11px] font-mono text-slate-400">@macatung_dev</div>
                    </div>
                  </div>
                  <span className="text-xs font-mono text-slate-500 group-hover:text-white">→</span>
                </a>

                <a
                  href="https://linkedin.com"
                  target="_blank"
                  rel="noreferrer"
                  onClick={() => sound.playClick()}
                  className="p-3.5 rounded-2xl bg-midnight-900/90 border border-slate-800 hover:border-blue-500/50 flex items-center justify-between group transition-all"
                >
                  <div className="flex items-center gap-3">
                    <div className="p-2 rounded-xl bg-blue-500/10 text-blue-400">
                      <LinkedinIcon className="w-4 h-4" />
                    </div>
                    <div>
                      <div className="text-xs font-mono font-bold text-white group-hover:text-blue-300">LinkedIn</div>
                      <div className="text-[11px] font-mono text-slate-400">linkedin.com/in/macatung</div>
                    </div>
                  </div>
                  <span className="text-xs font-mono text-slate-500 group-hover:text-white">→</span>
                </a>
              </div>
            </div>

            {/* Midnight SLA Badge */}
            <div className="p-4 rounded-2xl bg-midnight-900/60 border border-slate-800 flex items-center gap-3 text-xs font-mono text-slate-400">
              <span className="text-xl">🛡️</span>
              <div>
                <strong className="text-white block">Midnight SLA Commitment:</strong>
                Mọi tin nhắn đều được phản hồi trong vòng tối đa 24 giờ.
              </div>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
};
