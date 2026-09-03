<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru — Saham BBCA Analytics & Trading Suite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap');
        
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-sizing: border-box;
        }
        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Ambient Glow Animations */
        @keyframes orb-float-1 {
            0%, 100% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(40px, -60px) scale(1.15); }
        }
        @keyframes orb-float-2 {
            0%, 100% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(-50px, 50px) scale(1.2); }
        }
        @keyframes orb-float-3 {
            0%, 100% { transform: translate(0px, 0px) scale(1); }
            50% { transform: translate(30px, 40px) scale(0.9); }
        }
        .animate-orb-1 { animation: orb-float-1 14s ease-in-out infinite; }
        .animate-orb-2 { animation: orb-float-2 18s ease-in-out infinite; }
        .animate-orb-3 { animation: orb-float-3 16s ease-in-out infinite; }

        /* Pulse Live Dot */
        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .pulse-live { animation: pulse-ring 2s cubic-bezier(0.45, 0, 0.55, 1) infinite; }

        /* Marquee Ticker */
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: 200%;
            animation: marquee 25s linear infinite;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }

        /* Card Entrance Animation */
        @keyframes card-entrance {
            0% { opacity: 0; transform: translateY(24px) scale(0.97); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        .animate-card { animation: card-entrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        /* Glassmorphism Classes */
        .glass-panel {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-input {
            background: rgba(2, 6, 23, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.12);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-input:focus {
            border-color: rgba(56, 189, 248, 0.5);
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.2), inset 0 0 10px rgba(56, 189, 248, 0.1);
            outline: none;
        }

        /* Autofill Styling */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff;
            -webkit-box-shadow: 0 0 0px 1000px rgba(2, 6, 23, 0.95) inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Button Glow Hover */
        .btn-glow {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .btn-glow::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 60%);
            transform: scale(0);
            transition: transform 0.5s ease;
        }
        .btn-glow:hover::before {
            transform: scale(1);
        }
        .btn-glow:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body class="relative min-h-screen bg-[#030712] text-slate-100 flex items-center justify-center p-3 sm:p-5 lg:p-8 overflow-x-hidden select-none">

    <!-- Interactive Particle Background Canvas -->
    <canvas id="particleCanvas" class="absolute inset-0 z-0 pointer-events-none opacity-40"></canvas>

    <!-- Animated Ambient Color Orbs -->
    <div class="absolute top-1/4 left-10 w-96 h-96 bg-cyan-500/20 rounded-full blur-[120px] animate-orb-1 pointer-events-none"></div>
    <div class="absolute bottom-10 right-10 w-[28rem] h-[28rem] bg-violet-600/20 rounded-full blur-[140px] animate-orb-2 pointer-events-none"></div>
    <div class="absolute top-1/2 right-1/3 w-80 h-80 bg-emerald-500/15 rounded-full blur-[100px] animate-orb-3 pointer-events-none"></div>

    <!-- MAIN CONTAINER -->
    <div class="relative z-10 w-full max-w-5xl animate-card">
        
        <!-- TOP MARQUEE TICKER BAR -->
        <div class="mb-4 overflow-hidden rounded-xl border border-white/10 glass-panel py-2 px-3 text-xs shadow-lg">
            <div class="animate-marquee items-center gap-6 whitespace-nowrap">
                <span class="flex items-center gap-1.5 font-bold text-white"><span class="text-cyan-400">BBCA</span> Rp 6.775 <span class="text-emerald-400 font-semibold">▲ +1,50%</span></span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="text-slate-400">BBRI</span> Rp 4.920 <span class="text-emerald-400">▲ +0,82%</span></span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="text-slate-400">BMRI</span> Rp 5.150 <span class="text-emerald-400">▲ +1,18%</span></span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="text-slate-400">TLKM</span> Rp 2.780 <span class="text-rose-400">▼ -0,71%</span></span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="text-slate-400">ASII</span> Rp 4.370 <span class="text-emerald-400">▲ +0,46%</span></span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="text-slate-400">BBNI</span> Rp 4.120 <span class="text-emerald-400">▲ +1,23%</span></span>
                <!-- Duplicate for Seamless Infinite Loop -->
                <span class="flex items-center gap-1.5 font-bold text-white"><span class="text-cyan-400">BBCA</span> Rp 6.775 <span class="text-emerald-400 font-semibold">▲ +1,50%</span></span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="text-slate-400">BBRI</span> Rp 4.920 <span class="text-emerald-400">▲ +0,82%</span></span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="text-slate-400">BMRI</span> Rp 5.150 <span class="text-emerald-400">▲ +1,18%</span></span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="text-slate-400">TLKM</span> Rp 2.780 <span class="text-rose-400">▼ -0,71%</span></span>
                <span class="flex items-center gap-1.5 text-slate-300"><span class="text-slate-400">ASII</span> Rp 4.370 <span class="text-emerald-400">▲ +0,46%</span></span>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1.02fr_0.98fr] items-stretch">

            <!-- LEFT PANEL: PLATFORM PERKS & TRADING SUITE OVERVIEW -->
            <section class="rounded-3xl glass-panel p-6 sm:p-7 shadow-[0_32px_64px_rgba(0,0,0,0.6)] flex flex-col justify-between border border-cyan-500/20">
                
                <div>
                    <!-- Top Status Bar -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2.5">
                            <div class="w-3 h-3 rounded-full bg-emerald-400 pulse-live"></div>
                            <span class="font-extrabold text-sm tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-emerald-400">MEMBERSHIP PRIVILEGES</span>
                        </div>
                        <span class="rounded-full border border-cyan-400/30 bg-cyan-500/10 px-3 py-0.5 text-[10px] font-bold uppercase tracking-widest text-cyan-300">
                            GRATIS DAFFA
                        </span>
                    </div>

                    <!-- Welcome Bonus Badge Card -->
                    <div class="rounded-2xl bg-gradient-to-br from-cyan-950/80 via-slate-950/90 to-violet-950/80 p-5 border border-cyan-500/30 mb-5 shadow-inner">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl border border-emerald-500/30">
                                🎁
                            </div>
                            <div>
                                <p class="text-xs uppercase font-extrabold tracking-widest text-cyan-400">BONUS SALDO DEMO AWAL</p>
                                <p class="mono text-2xl font-black text-white">Rp 100.000.000</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Dapatkan modal Virtual Demo Trading sebesar **Rp 100 Juta** secara instan begitu akun Anda berhasil dibuat untuk melatih strategi trading BBCA secara bebas risiko.
                        </p>
                    </div>

                    <!-- Feature List Items -->
                    <div class="space-y-3.5">
                        <div class="flex items-start gap-3 rounded-xl bg-slate-900/60 p-3 border border-white/5">
                            <div class="w-8 h-8 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-brain"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-white">Analisis Pasar Saham AI (Ricki AI)</h4>
                                <p class="text-[11px] text-slate-400 leading-snug mt-0.5">Prediksi sinyal teknikal (RSI, MA, MACD) & rekomendasi trading harian BBCA otomatis.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-xl bg-slate-900/60 p-3 border border-white/5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-white">Komunitas Investor & Trader Expert</h4>
                                <p class="text-[11px] text-slate-400 leading-snug mt-0.5">Berbagi insight, ikuti diskusi pasar, dan pantau posisi ROI trader papan atas.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-xl bg-slate-900/60 p-3 border border-white/5">
                            <div class="w-8 h-8 rounded-lg bg-violet-500/20 text-violet-400 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fa-solid fa-calculator"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-white">Valuasi Fair Value & Calculator R:R</h4>
                                <p class="text-[11px] text-slate-400 leading-snug mt-0.5">Hitung Margin of Safety DCF/DDM & kalkulasi position sizing lot trading akurat.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Trust Badges -->
                <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                    <span><i class="fa-solid fa-shield-halved text-emerald-400 me-1"></i> ENKRIPSI 256-BIT</span>
                    <span><i class="fa-solid fa-bolt text-cyan-400 me-1"></i> REGISTRASI INSTAN</span>
                    <span><i class="fa-solid fa-circle-check text-blue-400 me-1"></i> TANPA BIAYA</span>
                </div>

            </section>

            <!-- RIGHT PANEL: ANIMATED REGISTER FORM -->
            <section class="rounded-3xl glass-panel p-6 sm:p-7 lg:p-8 shadow-[0_32px_64px_rgba(0,0,0,0.6)] flex flex-col justify-between border border-violet-500/20">
                
                <div>
                    <!-- Form Header -->
                    <div class="text-center mb-6">
                        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500/20 via-cyan-500/20 to-violet-500/20 text-emerald-300 border border-white/10 shadow-lg shadow-emerald-500/10">
                            <i class="fa-solid fa-user-plus text-2xl"></i>
                        </div>
                        <h1 class="text-2xl font-extrabold text-white tracking-tight">Buat Akun Baru</h1>
                        <p class="text-xs text-slate-400 mt-1">Daftar dalam 30 detik & langsung nikmati fitur trading suite.</p>
                    </div>

                    <!-- Flash Message / Errors -->
                    @if ($errors->any())
                        <div class="mb-4 rounded-2xl border border-rose-500/30 bg-rose-500/15 p-3 text-xs text-rose-200 flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- REGISTER FORM -->
                    <form method="POST" action="{{ route('register') }}" class="space-y-3.5" id="registerForm">
                        @csrf

                        <!-- Full Name Input -->
                        <div>
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                                <i class="fa-solid fa-user me-1 text-cyan-400"></i> Nama Lengkap
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                placeholder="Nama lengkap kamu"
                                class="w-full rounded-2xl glass-input px-4 py-2.5 text-sm text-white placeholder:text-slate-500">
                        </div>

                        <!-- Email Input -->
                        <div>
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                                <i class="fa-solid fa-envelope me-1 text-cyan-400"></i> Alamat Email
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="nama@email.com"
                                class="w-full rounded-2xl glass-input px-4 py-2.5 text-sm text-white placeholder:text-slate-500">
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                                <i class="fa-solid fa-lock me-1 text-violet-400"></i> Password
                            </label>
                            <div class="relative">
                                <input type="password" id="passInput" name="password" required oninput="checkPasswordStrength()"
                                    placeholder="Minimal 8 karakter"
                                    class="w-full rounded-2xl glass-input px-4 py-2.5 text-sm text-white placeholder:text-slate-500 pe-11">
                                <button type="button" onclick="togglePass('passInput', 'eye1')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
                                    <i class="fa-solid fa-eye" id="eye1"></i>
                                </button>
                            </div>
                            <!-- Strength Indicator Bar -->
                            <div class="mt-1.5 flex items-center gap-1.5">
                                <div class="flex-1 h-1 bg-slate-800 rounded-full overflow-hidden">
                                    <div id="strengthBar" class="h-full w-0 bg-rose-500 transition-all duration-300"></div>
                                </div>
                                <span id="strengthText" class="text-[10px] font-bold text-slate-500">Kekuatan: -</span>
                            </div>
                        </div>

                        <!-- Password Confirmation Input -->
                        <div>
                            <label class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                                <i class="fa-solid fa-shield me-1 text-emerald-400"></i> Konfirmasi Password
                            </label>
                            <div class="relative">
                                <input type="password" id="passConfirmInput" name="password_confirmation" required oninput="checkMatch()"
                                    placeholder="Ulangi password kamu"
                                    class="w-full rounded-2xl glass-input px-4 py-2.5 text-sm text-white placeholder:text-slate-500 pe-11">
                                <button type="button" onclick="togglePass('passConfirmInput', 'eye2')" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
                                    <i class="fa-solid fa-eye" id="eye2"></i>
                                </button>
                            </div>
                            <p id="matchText" class="text-[10px] font-bold text-slate-500 mt-1" style="display:none;"></p>
                        </div>

                        <!-- Terms & Conditions Checkbox -->
                        <div class="pt-1">
                            <label class="flex items-start gap-2 text-xs text-slate-300 cursor-pointer">
                                <input type="checkbox" required checked class="h-4 w-4 mt-0.5 rounded border-white/10 bg-slate-950 text-cyan-500 focus:ring-cyan-500/20" />
                                <span class="leading-tight text-[11px] text-slate-400">Saya menyetujui <a href="#" class="text-cyan-400 underline">Syarat & Ketentuan</a> serta Kebijakan Privasi platform Saham.id.</span>
                            </label>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <button type="submit" id="submitBtn"
                            class="btn-glow w-full rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-600 to-cyan-600 px-4 py-3.5 text-sm font-extrabold text-white shadow-xl shadow-emerald-500/20 transition-all duration-300 hover:shadow-emerald-500/40 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 mt-2">
                            <span>Daftar Akun Sekarang</span>
                            <i class="fa-solid fa-rocket text-xs"></i>
                        </button>
                    </form>
                </div>

                <!-- Footer Login Link -->
                <div class="mt-5 pt-3 border-t border-white/10 text-center text-xs text-slate-400">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="font-bold text-cyan-400 hover:text-cyan-300 transition hover:underline">
                        Masuk di Sini <i class="fa-solid fa-right-to-bracket text-[10px] ms-0.5"></i>
                    </a>
                </div>
            </section>

        </div>
    </div>

    <!-- JS ANIMATIONS & FORM INTERACTIVITY -->
    <script>
    // Particle Canvas Animation
    (function initParticles() {
        const canvas = document.getElementById('particleCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        });

        const particles = Array.from({ length: 45 }, () => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.4,
            vy: (Math.random() - 0.5) * 0.4,
            r: Math.random() * 2 + 1,
            alpha: Math.random() * 0.6 + 0.2
        }));

        function render() {
            ctx.clearRect(0, 0, width, height);
            
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                p.x += p.vx;
                p.y += p.vy;

                if (p.x < 0) p.x = width;
                if (p.x > width) p.x = 0;
                if (p.y < 0) p.y = height;
                if (p.y > height) p.y = 0;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(56, 189, 248, ${p.alpha})`;
                ctx.fill();

                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dist = Math.hypot(p.x - p2.x, p.y - p2.y);
                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.strokeStyle = `rgba(56, 189, 248, ${0.15 * (1 - dist / 120)})`;
                        ctx.lineWidth = 0.8;
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(render);
        }
        render();
    })();

    // Password Toggle Helper
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-solid fa-eye-slash text-cyan-400';
        } else {
            input.type = 'password';
            icon.className = 'fa-solid fa-eye text-slate-400';
        }
    }

    // Password Strength Meter
    function checkPasswordStrength() {
        const pass = document.getElementById('passInput').value;
        const bar = document.getElementById('strengthBar');
        const txt = document.getElementById('strengthText');
        if (!pass) {
            bar.style.width = '0%';
            txt.textContent = 'Kekuatan: -';
            txt.className = 'text-[10px] font-bold text-slate-500';
            return;
        }

        let score = 0;
        if (pass.length >= 8) score += 40;
        if (/[A-Z]/.test(pass)) score += 20;
        if (/[0-9]/.test(pass)) score += 20;
        if (/[^A-Za-z0-9]/.test(pass)) score += 20;

        bar.style.width = score + '%';
        if (score < 40) {
            bar.className = 'h-full bg-rose-500 transition-all duration-300';
            txt.textContent = 'Kekuatan: Lemah';
            txt.className = 'text-[10px] font-bold text-rose-400';
        } else if (score < 80) {
            bar.className = 'h-full bg-amber-500 transition-all duration-300';
            txt.textContent = 'Kekuatan: Sedang';
            txt.className = 'text-[10px] font-bold text-amber-400';
        } else {
            bar.className = 'h-full bg-emerald-500 transition-all duration-300';
            txt.textContent = 'Kekuatan: Sangat Kuat';
            txt.className = 'text-[10px] font-bold text-emerald-400';
        }
        checkMatch();
    }

    // Check Password Match
    function checkMatch() {
        const p1 = document.getElementById('passInput').value;
        const p2 = document.getElementById('passConfirmInput').value;
        const txt = document.getElementById('matchText');

        if (!p2) { txt.style.display = 'none'; return; }
        txt.style.display = 'block';

        if (p1 === p2) {
            txt.textContent = '✓ Password cocok!';
            txt.className = 'text-[10px] font-bold text-emerald-400 mt-1';
        } else {
            txt.textContent = '✕ Password belum cocok';
            txt.className = 'text-[10px] font-bold text-rose-400 mt-1';
        }
    }
    </script>
</body>
</html>