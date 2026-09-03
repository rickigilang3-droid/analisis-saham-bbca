<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Saham BBCA Analytics & Trading Suite</title>
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

        /* Shimmer Banner Glow */
        @keyframes border-shimmer {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .shimmer-card {
            background-size: 200% 200%;
            animation: border-shimmer 6s ease infinite;
        }

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

        /* Sparkline Path Draw */
        .chart-path {
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            transition: stroke-dashoffset 1.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .chart-path.drawn { stroke-dashoffset: 0; }

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

        <div class="grid gap-5 lg:grid-cols-[1.05fr_0.95fr] items-stretch">

            <!-- LEFT PANEL: REALTIME LIVE MARKET INSTRUMENT CARD -->
            <section class="rounded-3xl glass-panel p-5 sm:p-6 shadow-[0_32px_64px_rgba(0,0,0,0.6)] flex flex-col justify-between border border-cyan-500/20">
                
                <div>
                    <!-- Top Status Bar -->
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-3 h-3 rounded-full bg-emerald-400 pulse-live"></div>
                            <span class="font-extrabold text-sm tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-emerald-400">ANALISIS SAHAM BBCA</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span id="liveStatusBadge" class="rounded-full border border-emerald-400/30 bg-emerald-500/10 px-3 py-0.5 text-[10px] font-bold uppercase tracking-widest text-emerald-300">
                                <i class="fa-solid fa-bolt me-1"></i>LIVE IDX
                            </span>
                        </div>
                    </div>

                    <!-- BBCA Instrument Overview Card -->
                    <div class="rounded-2xl bg-slate-950/80 p-4 border border-white/5 mb-5 shadow-inner">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="mono text-lg font-black tracking-widest text-cyan-400">BBCA</span>
                                    <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400 bg-slate-800 px-2 py-0.5 rounded">BEI / IDX</span>
                                </div>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">PT Bank Central Asia Tbk</p>
                            </div>
                            <div class="text-right">
                                <p class="mono text-3xl font-extrabold text-white tracking-tight" id="livePrice">6.775</p>
                                <div id="livePercentContainer" class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-emerald-500/15 px-3 py-0.5 text-xs font-bold text-emerald-300 border border-emerald-500/30">
                                    <span id="liveTrend">▲</span>
                                    <span id="livePercent">+100 (+1,50%)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Realtime Animated SVG Chart -->
                        <div class="mt-5 h-28 relative overflow-hidden rounded-xl bg-slate-900/50 p-1">
                            <svg id="sparkline" viewBox="0 0 400 112" class="w-full h-full overflow-visible" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.35"/>
                                        <stop offset="100%" stop-color="#38bdf8" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <path id="sparkArea" fill="url(#chartGrad)" d=""/>
                                <path id="sparkPath" class="chart-path" fill="none" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d=""/>
                                <circle id="sparkDot" cx="400" cy="56" r="4.5" fill="#38bdf8" class="animate-pulse" filter="drop-shadow(0 0 8px #38bdf8)"/>
                            </svg>
                            <!-- Y-axis High/Low -->
                            <div class="absolute left-2 top-2 text-[9px] mono text-slate-500 font-semibold" id="yHigh">6.800</div>
                            <div class="absolute left-2 bottom-2 text-[9px] mono text-slate-500 font-semibold" id="yLow">6.625</div>
                        </div>

                        <!-- Realtime Stats Grid -->
                        <div class="grid grid-cols-4 gap-2 mt-4">
                            <div class="rounded-xl bg-slate-900/90 p-2 text-center border border-white/5">
                                <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold mb-0.5">OPEN</p>
                                <p id="liveOpen" class="mono text-xs font-bold text-slate-200">6.700</p>
                            </div>
                            <div class="rounded-xl bg-slate-900/90 p-2 text-center border border-white/5">
                                <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold mb-0.5">HIGH</p>
                                <p id="liveHigh" class="mono text-xs font-bold text-emerald-400">6.800</p>
                            </div>
                            <div class="rounded-xl bg-slate-900/90 p-2 text-center border border-white/5">
                                <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold mb-0.5">LOW</p>
                                <p id="liveLow" class="mono text-xs font-bold text-rose-400">6.625</p>
                            </div>
                            <div class="rounded-xl bg-slate-900/90 p-2 text-center border border-white/5">
                                <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold mb-0.5">VALUATION</p>
                                <p class="mono text-xs font-bold text-cyan-300">11.200</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Animated Equalizer Volume Spectrum & Badge -->
                <div>
                    <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mb-2">
                        <span><i class="fa-solid fa-chart-simple text-cyan-400 me-1"></i> PASAR AKUMULASI ASING</span>
                        <span class="text-emerald-400 font-bold">NET BUY +128.4 M</span>
                    </div>
                    <div class="flex items-end gap-1 h-9 px-1" id="volumeBars">
                        <!-- Generated by JS -->
                    </div>
                </div>

            </section>

            <!-- RIGHT PANEL: HIGH-TECH ANIMATED LOGIN FORM -->
            <section class="rounded-3xl glass-panel p-6 sm:p-7 lg:p-8 shadow-[0_32px_64px_rgba(0,0,0,0.6)] flex flex-col justify-between border border-violet-500/20">
                
                <div>
                    <!-- Form Header -->
                    <div class="text-center mb-6">
                        <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-500/20 via-violet-500/20 to-emerald-500/20 text-cyan-300 border border-white/10 shadow-lg shadow-cyan-500/10">
                            <i class="fa-solid fa-chart-line text-2xl"></i>
                        </div>
                        <h1 class="text-2xl font-extrabold text-white tracking-tight">Selamat Datang Kembali</h1>
                        <p class="text-xs text-slate-400 mt-1">Masuk untuk mengakses terminal analisis & trading simulator BBCA.</p>
                    </div>

                    <!-- Flash Message / Errors -->
                    @if ($errors->any())
                        <div class="mb-4 rounded-2xl border border-rose-500/30 bg-rose-500/15 p-3 text-xs text-rose-200 flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-4 rounded-2xl border border-emerald-500/30 bg-emerald-500/15 p-3 text-xs text-emerald-200 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- QUICK DEMO LOGIN BUTTONS -->
                    <div class="mb-5 p-3 rounded-2xl bg-slate-950/60 border border-white/5">
                        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400 text-center mb-2">⚡ DEMO QUICK AUTO-FILL</p>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="fillDemo('user')" class="rounded-xl border border-cyan-500/30 bg-cyan-500/10 px-3 py-2 text-xs font-bold text-cyan-300 transition hover:bg-cyan-500/20 hover:scale-[1.02] flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-user text-cyan-400"></i> Demo Trader
                            </button>
                            <button type="button" onclick="fillDemo('admin')" class="rounded-xl border border-violet-500/30 bg-violet-500/10 px-3 py-2 text-xs font-bold text-violet-300 transition hover:bg-violet-500/20 hover:scale-[1.02] flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-user-shield text-violet-400"></i> Demo Admin
                            </button>
                        </div>
                    </div>

                    <!-- LOGIN FORM -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
                        @csrf

                        <!-- Email Input -->
                        <div>
                            <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-300">
                                <i class="fa-solid fa-envelope me-1 text-cyan-400"></i> Alamat Email
                            </label>
                            <input type="email" id="emailInput" name="email" value="{{ old('email') }}" required autofocus
                                placeholder="nama@email.com"
                                class="w-full rounded-2xl glass-input px-4 py-3 text-sm text-white placeholder:text-slate-500">
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[11px] font-bold uppercase tracking-wider text-slate-300">
                                    <i class="fa-solid fa-lock me-1 text-violet-400"></i> Password
                                </label>
                                <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-cyan-400 transition hover:text-cyan-300 hover:underline">Lupa Password?</a>
                            </div>
                            <div class="relative">
                                <input type="password" id="passwordInput" name="password" required
                                    placeholder="••••••••"
                                    class="w-full rounded-2xl glass-input px-4 py-3 text-sm text-white placeholder:text-slate-500 pe-11">
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white transition">
                                    <i class="fa-solid fa-eye" id="passwordEyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/10 bg-slate-950 text-cyan-500 focus:ring-cyan-500/20" />
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <!-- SUBMIT BUTTON -->
                        <button type="submit" id="submitBtn"
                            class="btn-glow w-full rounded-2xl bg-gradient-to-r from-cyan-500 via-blue-600 to-violet-600 px-4 py-3.5 text-sm font-extrabold text-white shadow-xl shadow-cyan-500/20 transition-all duration-300 hover:shadow-cyan-500/40 hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                            <span>Masuk Sekarang</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </form>
                </div>

                <!-- Footer Register Link -->
                <div class="mt-6 pt-4 border-t border-white/10 text-center text-xs text-slate-400">
                    Belum memiliki akun? 
                    <a href="{{ route('register') }}" class="font-bold text-cyan-400 hover:text-cyan-300 transition hover:underline">
                        Daftar Akun Baru <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ms-0.5"></i>
                    </a>
                </div>
            </section>

        </div>
    </div>

    <!-- JS ANIMATIONS & INTERACTIVITY -->
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

    // Password Eye Toggle
    function togglePasswordVisibility() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('passwordEyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-solid fa-eye-slash text-cyan-400';
        } else {
            input.type = 'password';
            icon.className = 'fa-solid fa-eye text-slate-400';
        }
    }

    // Auto-fill Demo Login
    function fillDemo(type) {
        const emailInput = document.getElementById('emailInput');
        const passwordInput = document.getElementById('passwordInput');
        if (type === 'admin') {
            emailInput.value = 'admin@saham.id';
            passwordInput.value = 'password';
        } else {
            emailInput.value = 'user@saham.id';
            passwordInput.value = 'password';
        }
        
        // Highlight animation effect on inputs
        [emailInput, passwordInput].forEach(el => {
            el.classList.add('ring-2', 'ring-cyan-400');
            setTimeout(() => el.classList.remove('ring-2', 'ring-cyan-400'), 1000);
        });
    }

    // Live Chart & Market Data Simulation Sync
    (function initMarketChart() {
        const W = 400, H = 112, PAD = 8;
        const sparkPath = document.getElementById('sparkPath');
        const sparkArea = document.getElementById('sparkArea');
        const sparkDot = document.getElementById('sparkDot');
        const priceEl = document.getElementById('livePrice');

        let history = [6625, 6650, 6675, 6700, 6725, 6750, 6725, 6750, 6775];
        let isFirstDraw = true;

        function buildSparkline(values) {
            if (values.length < 2) return;
            const min = Math.min(...values);
            const max = Math.max(...values);
            const range = max - min || 1;
            const n = values.length;

            const pts = values.map((v, i) => {
                const x = PAD + (i / (n - 1)) * (W - PAD * 2);
                const y = (H - PAD) - ((v - min) / range) * (H - PAD * 2);
                return [x, y];
            });

            let d = `M${pts[0][0].toFixed(1)},${pts[0][1].toFixed(1)}`;
            for (let i = 1; i < pts.length; i++) {
                const prev = pts[i - 1];
                const curr = pts[i];
                const cpX = (prev[0] + curr[0]) / 2;
                d += ` C${cpX.toFixed(1)},${prev[1].toFixed(1)} ${cpX.toFixed(1)},${curr[1].toFixed(1)} ${curr[0].toFixed(1)},${curr[1].toFixed(1)}`;
            }

            const last = pts[pts.length - 1];
            const first = pts[0];
            const areaClose = `L${last[0].toFixed(1)},${H} L${first[0].toFixed(1)},${H} Z`;
            
            if (sparkArea) sparkArea.setAttribute('d', d + areaClose);
            if (sparkPath) {
                sparkPath.setAttribute('d', d);
                if (isFirstDraw) {
                    const len = sparkPath.getTotalLength();
                    sparkPath.style.strokeDasharray = len;
                    sparkPath.style.strokeDashoffset = len;
                    requestAnimationFrame(() => {
                        sparkPath.classList.add('drawn');
                    });
                    isFirstDraw = false;
                }
            }

            if (sparkDot) {
                sparkDot.setAttribute('cx', last[0].toFixed(1));
                sparkDot.setAttribute('cy', last[1].toFixed(1));
            }
        }

        function buildVolumeBars() {
            const container = document.getElementById('volumeBars');
            if (!container) return;
            const bars = Array.from({ length: 30 }, (_, i) => 0.2 + Math.sin(i / 2.5) * 0.35 + Math.random() * 0.4);
            container.innerHTML = bars.map((h, i) => {
                const height = Math.round(h * 30) + 6;
                const isLast = i === bars.length - 1;
                return `<div style="flex:1; height:${height}px; background:${isLast ? '#38bdf8' : 'rgba(56, 189, 248, 0.25)'}; border-radius:2px; transition:height 0.4s ease"></div>`;
            }).join('');
        }

        async function fetchQuote() {
            try {
                const res = await fetch('/api/market/quote?symbol=BBCA', { headers: { 'Accept': 'application/json' } });
                if (res.ok) {
                    const data = await res.json();
                    if (data.success && data.price > 0) {
                        const price = Math.round(data.price);
                        if (priceEl) priceEl.textContent = price.toLocaleString('id-ID');
                        history.push(price);
                        if (history.length > 25) history.shift();
                        buildSparkline(history);
                        buildVolumeBars();
                    }
                }
            } catch(e) {}
        }

        document.addEventListener('DOMContentLoaded', () => {
            buildSparkline(history);
            buildVolumeBars();
            fetchQuote();
            setInterval(fetchQuote, 5000);
        });
    })();
    </script>
</body>
</html>