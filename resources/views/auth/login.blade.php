<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Saham.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap');
        * { font-family: 'DM Sans', sans-serif; }
        .mono { font-family: 'DM Mono', monospace; }

        @keyframes pulse-dot {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
            50% { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
        }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }

        @keyframes tick-up {
            0% { transform: translateY(0); opacity: 1; }
            50% { transform: translateY(-4px); opacity: 0.7; }
            100% { transform: translateY(0); opacity: 1; }
        }
        .price-tick { animation: tick-up 0.3s ease-out; }

        @keyframes fade-in {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fade-in 0.5s ease-out forwards; }

        .chart-path {
            stroke-dasharray: 1000;
            stroke-dashoffset: 1000;
            transition: stroke-dashoffset 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .chart-path.drawn { stroke-dashoffset: 0; }

        @keyframes float-dot {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-2px); }
        }
        .float-dot { animation: float-dot 2s ease-in-out infinite; }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: white;
            -webkit-box-shadow: 0 0 0px 1000px rgb(2 6 23 / 0.8) inset;
            transition: background-color 5000s ease-in-out 0s;
        }

        .sparkline-area {
            opacity: 0;
            transition: opacity 0.8s ease 0.4s;
        }
        .sparkline-area.visible { opacity: 1; }
    </style>
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(34,211,238,0.14),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(167,139,250,0.18),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_45%,_#111827_100%)] text-white flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <div class="w-full max-w-6xl fade-in">
        <div class="grid gap-4 lg:grid-cols-[1.1fr_0.9fr] items-stretch">

            {{-- MARKET PANEL --}}
            <section class="rounded-2xl border border-white/8 bg-slate-900/60 p-4 backdrop-blur-xl shadow-[0_24px_48px_rgba(0,0,0,0.5)]">

                {{-- Header bar --}}
                <div class="mb-4 flex items-center justify-between rounded-xl border border-white/8 bg-slate-800/60 px-3 py-2 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 pulse-dot"></span>
                        <span class="font-semibold text-white">Saham.id</span>
                        <span class="tracking-widest text-slate-500 text-[10px] uppercase">Live</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="liveStatusBadge" class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-0.5 text-[10px] uppercase tracking-widest text-emerald-300">Sync</span>
                        <span class="rounded-full bg-slate-950/80 px-2.5 py-0.5 text-[10px] uppercase tracking-widest text-slate-400">IDX</span>
                    </div>
                </div>

                {{-- Price card --}}
                <div class="rounded-xl bg-slate-950/70 p-4 mb-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="mono text-xs uppercase tracking-widest text-slate-500 mb-0.5">BBCA</p>
                            <p class="text-xs text-slate-500">Bank Central Asia</p>
                        </div>
                        <div class="text-right">
                            <p class="mono text-3xl font-semibold text-white leading-none" id="livePrice">4.960</p>
                            <div id="livePercentContainer" class="mt-1.5 inline-flex items-center gap-1 rounded-full bg-rose-500/15 px-2.5 py-1 text-xs font-medium text-rose-300">
                                <span id="liveTrend">▼</span>
                                <span id="livePercent">-2.27%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Sparkline chart --}}
                    <div class="mt-4 h-28 relative overflow-hidden rounded-lg">
                        <svg id="sparkline" viewBox="0 0 400 112" class="w-full h-full" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#60a5fa" stop-opacity="0.18"/>
                                    <stop offset="100%" stop-color="#60a5fa" stop-opacity="0"/>
                                </linearGradient>
                                <clipPath id="chartClip">
                                    <rect x="0" y="0" width="400" height="112"/>
                                </clipPath>
                            </defs>
                            <path id="sparkArea" class="sparkline-area" fill="url(#areaGrad)" d=""/>
                            <path id="sparkPath" class="chart-path" fill="none" stroke="#60a5fa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" clip-path="url(#chartClip)" d=""/>
                            <circle id="sparkDot" cx="400" cy="56" r="3.5" fill="#38bdf8" class="float-dot"/>
                        </svg>
                        {{-- Y-axis labels --}}
                        <div class="absolute left-0 top-0 h-full flex flex-col justify-between py-1 pointer-events-none">
                            <span id="yHigh" class="mono text-[9px] text-slate-600">5.050</span>
                            <span id="yMid" class="mono text-[9px] text-slate-600">4.950</span>
                            <span id="yLow" class="mono text-[9px] text-slate-600">4.850</span>
                        </div>
                    </div>

                    {{-- Stats row --}}
                    <div class="grid grid-cols-4 gap-2 mt-4">
                        <div class="rounded-lg bg-slate-900/80 p-2 text-center">
                            <p class="text-[9px] uppercase tracking-widest text-slate-600 mb-1">Open</p>
                            <p id="liveOpen" class="mono text-xs font-semibold text-white">4.950</p>
                        </div>
                        <div class="rounded-lg bg-slate-900/80 p-2 text-center">
                            <p class="text-[9px] uppercase tracking-widest text-slate-600 mb-1">High</p>
                            <p id="liveHigh" class="mono text-xs font-semibold text-emerald-400">5.050</p>
                        </div>
                        <div class="rounded-lg bg-slate-900/80 p-2 text-center">
                            <p class="text-[9px] uppercase tracking-widest text-slate-600 mb-1">Low</p>
                            <p id="liveLow" class="mono text-xs font-semibold text-rose-400">4.850</p>
                        </div>
                        <div class="rounded-lg bg-slate-900/80 p-2 text-center">
                            <p class="text-[9px] uppercase tracking-widest text-slate-600 mb-1">Mkt Cap</p>
                            <p id="liveMarketCap" class="mono text-xs font-semibold text-slate-300">611T</p>
                        </div>
                    </div>
                </div>

                {{-- Volume bars (decorative) --}}
                <div class="flex items-end gap-0.5 h-10 px-1" id="volumeBars">
                    <!-- generated by JS -->
                </div>
            </section>

            {{-- LOGIN PANEL --}}
            <section class="flex flex-col justify-center rounded-[30px] border border-white/10 bg-slate-900/70 p-6 shadow-[0_30px_60px_rgba(0,0,0,0.45)] backdrop-blur-2xl sm:p-7 lg:p-8">

                <div class="mb-6 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-500/20 to-emerald-400/20 text-emerald-300 ring-1 ring-white/10">
                        <span class="text-base font-bold">Ricki Analis</span>
                    </div>
                    <div class="mb-3 flex justify-center gap-2">
                        <span class="rounded-full border border-emerald-400/20 bg-emerald-500/10 px-2.5 py-1 text-[10px] font-medium uppercase tracking-[0.25em] text-emerald-300">Aman</span>
                        <span class="rounded-full border border-violet-400/20 bg-violet-500/10 px-2.5 py-1 text-[10px] font-medium uppercase tracking-[0.25em] text-violet-300">Modern</span>
                    </div>
                    <h1 class="text-2xl font-semibold text-white">Masuk ke akun Anda</h1>
                    <p class="mt-2 text-sm text-slate-400">Pantau portofolio dan aksi pasar BBCA secara real-time.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-red-500/20 bg-red-500/10 px-3 py-2.5 text-sm text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-2.5 text-sm text-emerald-300">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="kamu@email.com"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-violet-400/40 focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition @error('email') border-red-400/50 ring-2 ring-red-500/20 @enderror">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Password</label>
                        <input type="password" name="password" required
                            placeholder="••••••••"
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/80 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-violet-400/40 focus:outline-none focus:ring-2 focus:ring-violet-500/20 transition @error('password') border-red-400/50 ring-2 ring-red-500/20 @enderror">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 text-xs text-slate-400">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/10 bg-slate-950/80 text-violet-500 focus:ring-violet-500/20" />
                            <span>Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-violet-400 transition hover:text-violet-300">Lupa password?</a>
                    </div>

                    <button type="submit"
                        class="w-full rounded-2xl bg-gradient-to-r from-violet-600 to-emerald-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-500/20 transition hover:from-violet-500 hover:to-emerald-400 active:scale-[0.99]">
                        Masuk sekarang
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-slate-500">
                    Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-violet-400 transition hover:text-violet-300">Daftar sekarang</a>
                </p>

                <div class="mt-5 rounded-2xl border border-white/10 bg-slate-950/50 p-3">
                    <div class="flex flex-wrap justify-center gap-2 text-[10px] uppercase tracking-[0.25em] text-slate-500">
                        <span>BBRI <span class="text-emerald-500">▲ 4.920</span></span>
                        <span>TLKM <span class="text-rose-400">▼ 2.780</span></span>
                        <span>BMRI <span class="text-emerald-500">▲ 5.150</span></span>
                        <span>ASII <span class="text-slate-500">— 4.370</span></span>
                    </div>
                </div>
            </section>

        </div>
    </div>

    <script>
    (function () {
        const W = 400, H = 112, PAD = 8;
        const HISTORY_MAX = 40;

        const priceEl = document.getElementById('livePrice');
        const percentEl = document.getElementById('livePercent');
        const trendEl = document.getElementById('liveTrend');
        const pctContainer = document.getElementById('livePercentContainer');
        const sparkPath = document.getElementById('sparkPath');
        const sparkArea = document.getElementById('sparkArea');
        const sparkDot = document.getElementById('sparkDot');
        const openEl = document.getElementById('liveOpen');
        const highEl = document.getElementById('liveHigh');
        const lowEl = document.getElementById('liveLow');
        const mktCapEl = document.getElementById('liveMarketCap');
        const yHighEl = document.getElementById('yHigh');
        const yMidEl = document.getElementById('yMid');
        const yLowEl = document.getElementById('yLow');
        const liveStatusBadge = document.getElementById('liveStatusBadge');

        // Seed history using a realistic BBCA-like starting point
        let history = [4960, 4970, 4975, 4970, 4985, 4990, 4980, 4995, 5005, 5010];
        let lastClose = 5010;
        let currentPrice = 5010;
        let currentOpen = 4980;
        let currentHigh = 5035;
        let currentLow = 4950;
        let isFirstDraw = true;
        let lastFetchedPrice = null;
        let lastFetchedAt = 0;
        let smoothedPrice = currentPrice;

        function fmt(v) { return Math.round(v).toLocaleString('id-ID'); }

        function formatMktCap(v) {
            if (!v || !isFinite(v)) return '–';
            if (v >= 1e12) return (v / 1e12).toFixed(2) + 'T';
            if (v >= 1e9) return (v / 1e9).toFixed(2) + 'B';
            if (v >= 1e6) return (v / 1e6).toFixed(2) + 'M';
            return v.toLocaleString('id-ID');
        }

        function buildSparkline(values) {
            if (values.length < 2) return;
            const min = Math.min(...values);
            const max = Math.max(...values);
            const range = max - min || 0.001;
            const n = values.length;

            const pts = values.map((v, i) => {
                const x = PAD + (i / (n - 1)) * (W - PAD * 2);
                const y = (H - PAD) - ((v - min) / range) * (H - PAD * 2);
                return [x, y];
            });

            // Smooth path using cubic bezier
            let d = `M${pts[0][0].toFixed(1)},${pts[0][1].toFixed(1)}`;
            for (let i = 1; i < pts.length; i++) {
                const prev = pts[i - 1];
                const curr = pts[i];
                const cpX = (prev[0] + curr[0]) / 2;
                d += ` C${cpX.toFixed(1)},${prev[1].toFixed(1)} ${cpX.toFixed(1)},${curr[1].toFixed(1)} ${curr[0].toFixed(1)},${curr[1].toFixed(1)}`;
            }

            // Area fill (close to bottom)
            const last = pts[pts.length - 1];
            const first = pts[0];
            const areaClose = `L${last[0].toFixed(1)},${H} L${first[0].toFixed(1)},${H} Z`;
            sparkArea.setAttribute('d', d + areaClose);
            sparkArea.classList.add('visible');

            // Stroke path with draw animation on first render
            sparkPath.setAttribute('d', d);
            if (isFirstDraw) {
                const len = sparkPath.getTotalLength();
                sparkPath.style.strokeDasharray = len;
                sparkPath.style.strokeDashoffset = len;
                requestAnimationFrame(() => {
                    sparkPath.style.transition = 'stroke-dashoffset 1.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    sparkPath.style.strokeDashoffset = 0;
                });
                isFirstDraw = false;
            }

            // Move dot to latest point
            sparkDot.setAttribute('cx', last[0].toFixed(1));
            sparkDot.setAttribute('cy', last[1].toFixed(1));

            // Y-axis labels
            yHighEl.textContent = Math.round(max).toLocaleString('id-ID');
            yMidEl.textContent = Math.round((max + min) / 2).toLocaleString('id-ID');
            yLowEl.textContent = Math.round(min).toLocaleString('id-ID');
        }

        function buildVolumeBars() {
            const container = document.getElementById('volumeBars');
            const bars = Array.from({ length: 28 }, (_, i) => {
                const base = 0.22 + Math.sin(i / 3.4 + currentPrice / 500) * 0.16 + (i % 7 === 0 ? 0.1 : 0.02);
                return Math.max(0.14, Math.min(0.95, base));
            });
            container.innerHTML = bars.map((h, i) => {
                const height = Math.round(h * 36) + 4;
                const opacity = 0.15 + h * 0.35;
                const isLast = i === bars.length - 1;
                return `<div style="flex:1; height:${height}px; background:${isLast ? '#60a5fa' : `rgba(96,165,250,${opacity})`}; border-radius:1px; transition:height 0.3s"></div>`;
            }).join('');
        }

        function pulsePrice() {
            priceEl.classList.remove('price-tick');
            void priceEl.offsetWidth;
            priceEl.classList.add('price-tick');
        }

        function updateUI(price, prevClose, open, high, low, mktCap) {
            if (!isFinite(price)) return;

            const targetPrice = Math.round(price);
            smoothedPrice = Math.round(smoothedPrice + (targetPrice - smoothedPrice) * 0.35);
            currentPrice = smoothedPrice;
            if (prevClose) lastClose = prevClose;
            if (open) currentOpen = open;
            if (high) currentHigh = Math.max(currentHigh, high);
            if (low) currentLow = Math.min(currentLow, low);

            history.push(currentPrice);
            if (history.length > HISTORY_MAX) history.shift();

            const diff = currentPrice - lastClose;
            const pct = lastClose ? (diff / lastClose) * 100 : 0;
            const pos = pct >= 0;

            priceEl.textContent = fmt(currentPrice);
            percentEl.textContent = `${pos ? '+' : ''}${pct.toFixed(2)}%`;
            trendEl.textContent = pos ? '▲' : '▼';
            liveStatusBadge.textContent = 'Sync';
            liveStatusBadge.className = `rounded-full border px-2.5 py-0.5 text-[10px] uppercase tracking-widest ${pos ? 'border-emerald-400/20 bg-emerald-500/10 text-emerald-300' : 'border-rose-400/20 bg-rose-500/10 text-rose-300'}`;

            pctContainer.className = `mt-1.5 inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium ${pos ? 'bg-emerald-500/15 text-emerald-300' : 'bg-rose-500/15 text-rose-300'}`;

            openEl.textContent = fmt(currentOpen);
            highEl.textContent = fmt(currentHigh);
            lowEl.textContent = fmt(currentLow);
            if (mktCap) mktCapEl.textContent = formatMktCap(mktCap);

            pulsePrice();
            buildSparkline(history);
            buildVolumeBars();
        }

        async function fetchQuote() {
            try {
                const res = await fetch('/api/market/quote?symbol=BBCA', { headers: { 'Accept': 'application/json' } });
                const contentType = res.headers.get('content-type') || '';
                if (!res.ok || !contentType.includes('application/json')) {
                    throw new Error('Quote endpoint is unavailable');
                }

                const d = await res.json();
                if (!d.success || !d.price) throw new Error('Quote payload invalid');

                const toNum = v => { const n = parseFloat(v); return isFinite(n) ? n : null; };
                const price = toNum(d.price);
                const prevClose = toNum(d.previous_close) ?? price;
                const open = toNum(d.open) ?? price;
                const high = toNum(d.high) ?? price;
                const low = toNum(d.low) ?? price;
                const mktCap = toNum(d.market_cap);

                if (price) {
                    lastFetchedPrice = price;
                    lastFetchedAt = Date.now();
                    updateUI(price, prevClose, open, high, low, mktCap);
                    return;
                }
            } catch (e) {
                console.warn('Quote fetch failed, using cached market value:', e);
                liveStatusBadge.textContent = 'Retry';
                liveStatusBadge.className = 'rounded-full border border-amber-400/20 bg-amber-500/10 px-2.5 py-0.5 text-[10px] uppercase tracking-widest text-amber-300';
            }

            if (lastFetchedPrice) {
                updateUI(lastFetchedPrice, lastClose, currentOpen, Math.max(currentHigh, lastFetchedPrice), Math.min(currentLow, lastFetchedPrice), null);
            } else {
                simulateMovement();
            }
        }

        function simulateMovement() {
            const drift = (Math.random() - 0.5) * 8;
            const nextPrice = Math.max(4800, Math.min(5200, currentPrice + drift));
            const roundedPrice = Math.round(nextPrice / 25) * 25;

            updateUI(roundedPrice, lastClose, currentOpen,
                Math.max(currentHigh, roundedPrice),
                Math.min(currentLow, roundedPrice),
                null);
        }

        document.addEventListener('DOMContentLoaded', () => {
            buildSparkline(history);
            buildVolumeBars();
            fetchQuote();
            setInterval(() => {
                fetchQuote();
            }, 4000);
        });
    })();
    </script>
</body>
</html>