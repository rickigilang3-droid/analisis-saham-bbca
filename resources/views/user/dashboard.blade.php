<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>AMELIA — BBCA</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<script src="https://s3.tradingview.com/tv.js"></script>

<style>
  :root {
    --bg: #0a0f1a; --panel: #0d1526; --panel2: #111c30;
    --border: rgba(255,255,255,0.06); --muted: #6b7a8d;
    --accent: #00d4ff; --accent2: #7c3aed;
    --green: #10b981; --red: #f43f5e;
    --text: #dde6f0; --text2: #8899aa;
    --light-bg: #f0f4fa; --light-panel: #ffffff; --light-panel2: #f7f9fc;
    --light-border: #dde3ec; --light-muted: #6b7a8d;
    --light-text: #1a2234; --light-text2: #5a6a7e;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    font-family: 'Space Mono', 'Consolas', monospace;
    background: var(--bg);
    color: var(--text);
    transition: background .3s, color .3s;
  }
  .app { max-width: 1560px; margin: 0 auto; padding: 10px 14px; }

  .topbar {
    display: grid; grid-template-columns: 1fr auto; align-items: stretch;
    gap: 18px; padding: 24px 26px; margin-bottom: 20px;
    background: #101827; border-radius: 24px;
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 16px 32px rgba(0,0,0,0.16);
  }
  .topbar-brand-block {
    display: grid; gap: 10px;
    min-width: 0;
  }
  .brand-label {
    color: rgba(148,163,184,0.88); text-transform: uppercase;
    letter-spacing: 1.6px; font-size: 0.78rem; font-weight: 700;
  }
  .brand-title {
    font-family: 'Syne', sans-serif; font-size: 2rem;
    font-weight: 800; color: #f8fafc; letter-spacing: -0.04em;
  }
  .brand-subtitle {
    color: rgba(148,163,184,0.88); font-size: 0.95rem;
    max-width: 680px; line-height: 1.55;
  }
  .topbar-metrics {
    display: flex; flex-wrap: wrap; gap: 10px;
    margin-top: 6px;
  }
  .metric-card {
    background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
    color: rgba(241,245,249,0.92); padding: 12px 16px;
    border-radius: 14px; font-size: 0.82rem; min-width: 130px;
  }
  .metric-card strong {
    display: block; margin-top: 4px; font-size: 1rem; color: #f8fafc;
  }
  .topbar-meta {
    display: grid; gap: 16px; justify-items: end; text-align: right;
    min-width: 0;
  }
  .topbar-user-card {
    display: grid; gap: 14px; padding: 18px 20px;
    border-radius: 20px; background: rgba(255,255,255,0.02);
    border: 1px solid rgba(255,255,255,0.08);
    min-width: 320px;
  }
  .user-card-head {
    display: flex; align-items: center; gap: 14px;
  }
  .user-avatar {
    width: 48px; height: 48px; border-radius: 50%;
    background: rgba(148,163,184,0.1); display: grid;
    place-items: center; color: #94a3b8; font-size: 1.2rem;
  }
  .user-card-title {
    display: grid; gap: 4px;
  }
  .topbar-user-name {
    font-size: 1rem; font-weight: 800; color: #f8fafc;
  }
  .topbar-user-meta {
    font-size: 0.82rem; color: rgba(148,163,184,0.95);
  }
  .topbar-actions {
    display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;
  }
  .topbar-actions .btn-ghost {
    padding: 10px 14px; border-radius: 12px;
  }
  .btn-ghost {
    background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12);
    color: #e6f7ff; padding: 10px 14px; border-radius: 12px;
    font-size: 0.88rem; cursor: pointer; transition: all .2s ease;
    font-family: 'Space Mono', monospace;
  }
  .btn-ghost:hover {
    transform: translateY(-1px);
    border-color: rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.12);
    color: #fff;
  }

  .layout { display: grid; grid-template-columns: 260px 1fr 310px; gap: 12px; }
  .card-custom { background: var(--panel); border-radius: 10px; padding: 14px; border: 1px solid var(--border); }

  #tvchart { width: 100%; height: 100%; }

  .orderbook {
    flex-grow:1; overflow:auto; font-size: 12px;
    padding: 2px 0 0;
  }
  .order-row {
    display:flex; justify-content:space-between; align-items:center;
    gap:8px; padding: 5px 8px; border-bottom: 1px solid rgba(255,255,255,0.02);
    overflow:hidden;
    border: 1px solid rgba(255,255,255,0.03);
    background: rgba(255,255,255,0.015);
    border-radius: 8px;
    backdrop-filter: blur(4px);
  }
  .order-row:hover { background: rgba(255,255,255,0.035); }
  .order-row.buy span:first-child { color: var(--green); }
  .order-row.sell span:first-child { color: var(--red); }
  .order-row > span {
    position:relative;
    z-index:1;
    white-space:nowrap;
    flex-shrink:0;
  }
  .order-vol {
    min-width: 74px; color:#b9c2d0; text-align:left;
    font-variant-numeric: tabular-nums; letter-spacing: .1px;
  }
  .order-price {
    min-width: 78px; font-weight:800;
    font-variant-numeric: tabular-nums; letter-spacing: .2px;
    text-shadow: 0 1px 0 rgba(0,0,0,0.18);
  }
  .order-mid {
    background: linear-gradient(180deg, rgba(0,212,255,0.20), rgba(0,212,255,0.08));
    border: 1px solid rgba(0,212,255,0.38);
    color: #ebfdff;
    text-align:center; font-weight:800; padding:10px 0; font-size:13px; letter-spacing:1px;
    border-radius: 12px;
    box-shadow: 0 10px 24px rgba(0,212,255,0.08), 0 0 0 1px rgba(0,212,255,0.08) inset;
  }
  .orderbook-shell {
    display:grid; gap:10px;
    padding: 10px;
    border-radius: 14px;
    background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
    border: 1px solid rgba(255,255,255,0.04);
  }
  .orderbook-header {
    display:grid; grid-template-columns: 1fr auto 1fr; gap:10px; align-items:center;
    padding: 10px 12px;
    border-radius: 12px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.05);
  }
  .orderbook-summary {
    display:grid; gap:4px;
  }
  .orderbook-summary .label {
    font-size:10px; text-transform:uppercase; letter-spacing:1.2px; color:var(--muted);
    font-weight: 700;
  }
  .orderbook-summary .value {
    font-size:12px; font-weight:800; font-variant-numeric: tabular-nums;
  }
  .orderbook-summary.sell .value { color:#ffadc0; }
  .orderbook-summary.buy .value { color:#96f0c0; text-align:right; }
  .orderbook-summary.buy { text-align:right; }
  .orderbook-center {
    display:grid; justify-items:center; gap:2px;
    padding: 4px 8px;
  }
  .orderbook-center .mid-label {
    font-size:10px; text-transform:uppercase; letter-spacing:1.4px; color:var(--muted);
    font-weight: 700;
  }
  .orderbook-center .mid-value {
    font-size:12px; color:#8fe9ff; font-weight:700;
  }
  .orderbook-split { display:grid; grid-template-columns: 1fr 58px 1fr; gap: 8px; align-items:start; }
  .order-side { min-width:0; }
  .order-side-head {
    display:flex; align-items:center; justify-content:space-between; gap:8px;
    font-size:9px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase;
    margin-bottom:6px; padding: 7px 10px; border-radius: 10px;
    border: 1px solid transparent;
  }
  .order-side-head.sell { color: #ffadc0; background: rgba(244,63,94,0.14); border-color: rgba(244,63,94,0.28); }
  .order-side-head.buy { color: #96f0c0; background: rgba(16,185,129,0.14); border-color: rgba(16,185,129,0.28); }
  .order-side-head span:last-child {
    color: inherit; font-weight:700; letter-spacing:.5px; font-size:8px;
    background: rgba(255,255,255,0.08); padding: 2px 7px; border-radius: 999px;
  }
  .order-side-body { display:flex; flex-direction:column; gap:4px; }
  .order-mid-stack { display:grid; place-items:center; min-height:100%; }
  .order-mid-stack .order-mid { width:100%; }
  .history-head {
    display:grid; grid-template-columns: 72px 1fr 1fr; gap:8px;
    font-size:9px; font-weight:700; letter-spacing:1px; text-transform:uppercase;
    color: #9fb1c4; padding: 0 0 8px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 8px;
  }
  .history-row {
    display:grid; grid-template-columns: 72px 1fr 1fr; gap:8px;
    padding: 6px 0; border-bottom:1px solid rgba(255,255,255,0.06); align-items:center;
  }
  .history-row:last-child { border-bottom:none; }
  .history-type {
    display:inline-flex; align-items:center; justify-content:center; min-width: 56px;
    padding: 3px 8px; border-radius: 999px; font-size: 9px; font-weight: 700; letter-spacing: .6px;
  }
  .history-type.buy { color: #7ee2b8; background: rgba(16,185,129,0.10); border: 1px solid rgba(16,185,129,0.18); }
  .history-type.sell { color: #ff9aaa; background: rgba(244,63,94,0.10); border: 1px solid rgba(244,63,94,0.18); }

  .news-item { padding: 10px 0; border-bottom: 1px solid var(--border); }
  .news-item:last-child { border-bottom:none; }
  .news-title-link { display:block; font-size:11px; line-height:1.4; font-weight:400; text-decoration:none; color: var(--text); transition:.2s; }
  .news-title-link:hover { color: var(--accent); }

  .form-control-custom {
    background: rgba(255,255,255,0.03); border: 1px solid var(--border);
    color: inherit; border-radius: 6px; padding: .55rem .8rem;
    width: 100%; font-size: 13px; font-weight: 700; font-family: 'Space Mono', monospace;
    transition: .2s;
  }
  .form-control-custom:focus { border-color: var(--accent); outline: none; background: rgba(0,212,255,0.04); }
  .btn-buy  { background: var(--green); color:#fff; border-radius:6px; padding:11px; border:0; width:100%; font-weight:700; margin-top:8px; cursor:pointer; transition:.2s; font-family:'Space Mono',monospace; font-size:12px; letter-spacing:1px; }
  .btn-sell { background: var(--red);   color:#fff; border-radius:6px; padding:11px; border:0; width:100%; font-weight:700; margin-top:8px; cursor:pointer; transition:.2s; font-family:'Space Mono',monospace; font-size:12px; letter-spacing:1px; }
  .btn-buy:hover  { filter:brightness(1.12); transform:translateY(-1px); }
  .btn-sell:hover { filter:brightness(1.12); transform:translateY(-1px); }

  .modal-content { background: var(--panel); color: var(--text); border: 1px solid var(--border); }
  .sec-label { font-size: 10px; font-weight: 700; color: var(--muted); letter-spacing: 1.5px; margin-bottom: 10px; }

  /* AI PREDICTION */
  .pred-wrap { margin-top: 12px; }
  .ta-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 12px; }
  .ta-card { background: var(--panel2); border-radius: 8px; padding: 10px 11px; border: 1px solid var(--border); }
  .ta-label { font-size: 9px; color: var(--muted); margin-bottom: 4px; letter-spacing: .5px; }
  .ta-value { font-size: 14px; font-weight: 700; }
  .ta-sub   { font-size: 10px; margin-top: 2px; }

  .signal-bar {
    display:flex; align-items:center; gap:12px;
    background: rgba(0,212,255,0.05); border:1px solid rgba(0,212,255,0.15);
    border-radius: 8px; padding: 12px 14px; margin-bottom: 12px; transition:.3s;
  }
  .signal-icon { font-size: 24px; }
  .signal-text { flex:1; }
  .signal-main  { font-size: 15px; font-weight: 700; }
  .signal-desc  { font-size: 10px; color: var(--muted); margin-top:2px; }
  .signal-conf  { text-align:right; }
  .signal-pct   { font-size: 22px; font-weight: 700; }
  .signal-pct-label { font-size: 9px; color: var(--muted); }

  .indicators-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 12px; }
  .ind-item { background: var(--panel2); border-radius:6px; padding:8px 10px; border:1px solid var(--border); }
  .ind-name { font-size:9px; color:var(--muted); letter-spacing:.5px; }
  .ind-val  { font-size:12px; font-weight:700; margin-top:2px; }
  .ind-sig  { font-size:9px; margin-top:1px; font-weight:700; }

  .target-row { display:flex; gap:8px; margin-bottom:12px; flex-wrap:wrap; }
  .target-pill { flex:1; min-width:70px; text-align:center; border-radius:6px; padding:7px 6px; font-size:11px; font-weight:700; }
  .pill-s { background:rgba(16,185,129,0.1); color:#34d399; border:1px solid rgba(16,185,129,0.25); }
  .pill-b { background:rgba(0,212,255,0.08); color:#67e8f9; border:1px solid rgba(0,212,255,0.2); }
  .pill-r { background:rgba(244,63,94,0.1); color:#fb7185; border:1px solid rgba(244,63,94,0.25); }
  .pill-label { font-size:8px; font-weight:400; opacity:.7; display:block; margin-bottom:2px; }

  /* Gemini box — warna biru-hijau Google */
  .ai-box { background: rgba(52,168,83,0.07); border:1px solid rgba(52,168,83,0.2); border-radius:8px; padding:14px; }
  .ai-box-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
  .ai-box-title { font-size:10px; font-weight:700; color:#4ade80; letter-spacing:1px; }
  .gemini-badge {
    display:inline-flex; align-items:center; gap:4px;
    background: linear-gradient(135deg,rgba(26,115,232,0.15),rgba(52,168,83,0.15));
    border:1px solid rgba(66,184,131,0.3); border-radius:20px;
    padding:2px 8px; font-size:8px; font-weight:700; color:#4ade80;
  }
  .btn-ai {
    background: linear-gradient(135deg, #1a73e8, #34a853);
    color:#fff; border:none; border-radius:6px;
    padding:6px 14px; font-size:10px; font-weight:700;
    cursor:pointer; transition:.2s; font-family:'Space Mono',monospace; letter-spacing:.5px;
  }
  .btn-ai:hover { filter:brightness(1.15); transform:translateY(-1px); }
  .btn-ai:disabled { opacity:.5; cursor:not-allowed; transform:none; }
  .ai-result { font-size:11px; line-height:1.75; color:#b8c8da; min-height:60px; }
  .ai-result.loading { color:var(--muted); font-style:italic; }
  .ai-para { margin-bottom:8px; }
  .ai-para:last-child { margin-bottom:0; }
  .update-tag { font-size:9px; color:var(--muted); text-align:right; margin-top:8px; }

  /* COMMUNITY DISCUSSION MODS */
  .btn-tab-disc {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--muted);
    border-radius: 20px;
    padding: 3px 12px;
    font-size: 10px;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s;
  }
  .btn-tab-disc:hover, .btn-tab-disc.active {
    background: rgba(6, 182, 212, 0.12);
    border-color: var(--accent);
    color: var(--accent);
    font-weight: 600;
  }
  .disc-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #06b6d4, #3b82f6);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 11px;
    flex-shrink: 0;
  }
  .disc-badge-bull {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
    padding: 2px 7px;
    border-radius: 12px;
    font-size: 9px;
    font-weight: 600;
  }
  .disc-badge-bear {
    background: rgba(244, 63, 94, 0.15);
    color: #f43f5e;
    border: 1px solid rgba(244, 63, 94, 0.3);
    padding: 2px 7px;
    border-radius: 12px;
    font-size: 9px;
    font-weight: 600;
  }
  .disc-badge-neu {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
    padding: 2px 7px;
    border-radius: 12px;
    font-size: 9px;
    font-weight: 600;
  }

  /* LIGHT MODE */
  .light-mode {
    background: #eef3f9; color: #172033;
    --bg: #eef3f9; --panel: #ffffff;
    --panel2: #f5f8fd; --border: #d8e2ef;
    --muted: #64748b; --text: #172033; --text2: #4f617a;
  }
  .light-mode .card-custom {
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    border-color: var(--light-border);
    color: var(--light-text);
    box-shadow: 0 10px 26px rgba(15,23,42,0.05);
  }
  .light-mode .topbar {
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    border-color: var(--light-border);
    color: var(--light-text);
    box-shadow: 0 18px 42px rgba(15,23,42,0.08);
  }
  .light-mode .topbar .brand-label,
  .light-mode .topbar .brand-title,
  .light-mode .topbar .brand-subtitle,
  .light-mode .topbar .topbar-pill,
  .light-mode .topbar .topbar-user-name,
  .light-mode .topbar .topbar-user-meta,
  .light-mode .topbar .topbar-actions .btn-ghost {
    color: var(--light-text);
  }
  .light-mode .topbar .brand-label { color: var(--light-muted); }
  .light-mode .topbar .brand-subtitle { color: #475569; }
  .light-mode .topbar .topbar-pill {
    background: rgba(59,130,246,0.07);
    border-color: rgba(59,130,246,0.16);
    color: #1e40af;
  }
  .light-mode .topbar .topbar-user-card { background: rgba(255,255,255,0.96); border-color: var(--light-border); }
  .light-mode .topbar .btn-ghost {
    background: rgba(248,250,252,0.95);
    border-color: var(--light-border);
    color: var(--light-text);
  }
  .light-mode .topbar .btn-ghost:hover {
    background: #ffffff;
  }
  .light-mode .ta-card, .light-mode .ind-item { background: linear-gradient(180deg, #ffffff 0%, #f6f9fd 100%); border-color: var(--light-border); }
  .light-mode .form-control-custom { background: #ffffff; border-color: var(--light-border); color: var(--light-text); }
  .light-mode .news-title-link { color: var(--light-text); }
  .light-mode .signal-bar { background: rgba(37,99,235,0.06); border-color: rgba(37,99,235,0.18); }
  .light-mode .ai-box { background: rgba(16,185,129,0.05); border-color: rgba(16,185,129,0.16); }
  .light-mode .ai-result { color: #20324a; }
  .light-mode .orderbook { color: #20324a; }
  .light-mode .order-row { 
    border-bottom-color: rgba(15,23,42,0.06);
    background: rgba(59,130,246,0.02);
    border-color: rgba(15,23,42,0.08);
  }
  .light-mode .order-row:hover { background: rgba(59,130,246,0.08); }
  .light-mode .order-row > span {
    color: #0f1729 !important;
  }
  .light-mode .order-vol {
    color: #475569 !important;
    font-weight: 600;
  }
  .light-mode .order-price {
    color: #0f1729 !important;
    text-shadow: 0 1px 0 rgba(255,255,255,0.5);
    font-weight: 800;
  }
  .light-mode .order-row.sell span:first-child {
    color: #b82338 !important;
    font-weight: 700;
  }
  .light-mode .order-row.buy span:first-child {
    color: #10b981 !important;
    font-weight: 700;
  }
  .light-mode .order-mid {
    background: linear-gradient(180deg, rgba(37,99,235,0.14), rgba(37,99,235,0.06));
    border-color: rgba(37,99,235,0.24);
    color: #123b8a;
    box-shadow: 0 0 0 1px rgba(37,99,235,0.06) inset;
  }
  .light-mode .order-side-body .order-row > span {
    color: #0f1729 !important;
  }
  .light-mode .order-side-body .order-vol {
    color: #475569 !important;
  }
  .light-mode .order-side-body .order-price {
    color: #0f1729 !important;
  }
  .light-mode .history-head {
    color: #475569;
    border-bottom-color: rgba(15,23,42,0.10);
  }
  .light-mode .history-row {
    border-bottom-color: rgba(15,23,42,0.08);
  }
  .light-mode .history-type.buy {
    color: #0b7a43;
    background: rgba(16,185,129,0.10);
    border-color: rgba(16,185,129,0.20);
  }
  .light-mode .history-type.sell {
    color: #a61d3b;
    background: rgba(244,63,94,0.10);
    border-color: rgba(244,63,94,0.20);
  }
  .light-mode .badge-live {
    background: rgba(16,185,129,0.12);
    color: #0b7a43;
    border-color: rgba(16,185,129,0.22);
  }
  .light-mode .badge-sim {
    background: rgba(37,99,235,0.10);
    color: #1e40af;
    border-color: rgba(37,99,235,0.20);
  }
  .light-mode #priceStatusBadge .badge-live,
  .light-mode #priceStatusBadge .badge-sim {
    box-shadow: 0 1px 0 rgba(255,255,255,0.6) inset;
  }
  .light-mode .sec-label {
    color: #3b4558 !important;
  }
  .light-mode #realtime-date {
    color: #64748b !important;
  }
  .light-mode #realtime-time {
    color: #1a2234 !important;
  }
  .light-mode .metric-card {
    background: rgba(15,23,42,0.04);
    border-color: rgba(15,23,42,0.12);
    color: #1a2234;
  }
  .light-mode .metric-card strong {
    color: #0f1729;
  }
  .light-mode .topbar-user-meta {
    color: #64748b !important;
  }
  .light-mode #btnLaporan,
  .light-mode .btn-buy,
  .light-mode .btn-sell {
    color: #ffffff;
  }
  .light-mode .orderbook-shell {
    background: linear-gradient(180deg, rgba(59,130,246,0.02), rgba(16,185,129,0.02));
    border-color: rgba(15,23,42,0.10);
  }
  .light-mode .orderbook-header {
    background: rgba(59,130,246,0.04);
    border-color: rgba(59,130,246,0.12);
  }
  .light-mode .orderbook-summary .label {
    color: #3b4558 !important;
    font-weight: 700;
    font-size: 10px;
  }
  .light-mode .orderbook-summary .value {
    color: #0f1729 !important;
    font-weight: 800;
  }
  .light-mode .orderbook-summary.sell .value {
    color: #b82338 !important;
  }
  .light-mode .orderbook-summary.buy .value {
    color: #10b981 !important;
  }
  .light-mode .orderbook-center .mid-label {
    color: #3b4558 !important;
    font-weight: 700;
    font-size: 10px;
  }
  .light-mode .orderbook-center .mid-value {
    color: #1e40af !important;
    font-weight: 700;
    font-size: 12px;
  }
  .light-mode .order-side-head.sell {
    color: #b82338 !important;
    background: rgba(244,63,94,0.12);
    border-color: rgba(244,63,94,0.24);
    font-weight: 700;
  }
  .light-mode .order-side-head.buy {
    color: #10b981 !important;
    background: rgba(16,185,129,0.12);
    border-color: rgba(16,185,129,0.24);
    font-weight: 700;
  }
  .light-mode .order-side-head span:last-child {
    background: rgba(15,23,42,0.08);
    color: #334155;
  }
  .event-date-badge {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--border);
    text-align: center;
    padding: 6px 0;
    font-size: 10px;
    font-weight: 700;
  }

  ::-webkit-scrollbar { width: 4px; height: 4px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

  .badge-live { background: rgba(16,185,129,0.15); color:#34d399; font-size:8px; font-weight:700; padding:3px 8px; border-radius:20px; border:1px solid rgba(16,185,129,0.3); }
  .badge-sim  { background: rgba(0,212,255,0.12); color:#67e8f9; font-size:8px; font-weight:700; padding:3px 8px; border-radius:20px; border:1px solid rgba(0,212,255,0.25); }

  .c-green { color: var(--green) !important; }
  .c-red   { color: var(--red) !important; }
  .c-cyan  { color: var(--accent) !important; }
  .c-muted { color: var(--muted) !important; }

  @media(max-width:1200px) { .layout { grid-template-columns: 1fr 1fr; } .ta-grid { grid-template-columns: repeat(2,1fr); } }
  @media(max-width:768px)  {
    .topbar { grid-template-columns: 1fr; padding: 22px 18px; }
    .topbar-meta { justify-items: start; text-align: left; }
    .topbar-pills { justify-content: flex-start; }
    .controls { justify-content: flex-start; }
    .layout { grid-template-columns: 1fr; }
    .ta-grid { grid-template-columns: repeat(2,1fr); }
    .indicators-row { grid-template-columns: repeat(2,1fr); }
  }
</style>
</head>
<body>

{{-- Modal Laporan --}}
<div class="modal fade" id="finModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" style="font-size:14px;font-weight:700;letter-spacing:1px;">
          <i class="fa-solid fa-file-invoice-dollar me-2" style="color:var(--accent)"></i> LAPORAN KEUANGAN — BBCA
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4 border-end border-secondary border-opacity-10">
            <h6 class="c-muted mb-3" style="font-size:10px;letter-spacing:1px;">Q3 2024 SUMMARY</h6>
            <div class="mb-3">
              <small class="d-block c-muted">Net Income</small>
              <h4 class="fw-bold c-green">Rp 41.1 T <small class="fs-6"><i class="fa-solid fa-arrow-up"></i> 12.8%</small></h4>
            </div>
            <div class="mb-3">
              <small class="d-block c-muted mb-1">Valuasi</small>
              <div class="d-flex justify-content-between mt-1" style="font-size:13px;"><span>PER</span><span class="fw-bold">23.45x</span></div>
              <div class="d-flex justify-content-between" style="font-size:13px;"><span>PBV</span><span class="fw-bold">4.82x</span></div>
            </div>
          </div>
          <div class="col-md-8">
            <h6 class="c-muted mb-3" style="font-size:10px;letter-spacing:1px;">INCOME STATEMENT (TRILIUN RP)</h6>
            <table class="table table-sm table-dark table-hover" style="font-size:13px;--bs-table-bg:transparent;">
              <thead>
                <tr class="c-muted"><th>Indikator</th><th class="text-end">2023</th><th class="text-end">2024 (Est)</th></tr>
              </thead>
              <tbody>
                <tr><td>Pendapatan</td><td class="text-end">75.4</td><td class="text-end c-cyan">82.5</td></tr>
                <tr class="fw-bold border-top"><td>Laba Bersih</td><td class="text-end">48.6</td><td class="text-end c-green">54.3</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="app" id="mainApp">
  <div class="topbar">
      <div class="topbar-brand-block">
        <div class="brand-label" id="brandLabel">BBCA Investor Dashboard</div>
        <div class="brand-title">AMELIA</div>
        <div class="brand-subtitle" id="brandSubtitle">Data pasar, posisi portofolio, dan sinyal trading tersaji dalam tampilan yang bersih dan fokus.</div>
      </div>
      <div class="topbar-meta">
        <div style="text-align: right; display: flex; flex-direction: column; gap: 8px; justify-content: center;">
          <div style="font-size: 14px; color: #8899aa; font-weight: 700; letter-spacing: 0.5px;">
            <span id="realtime-date">-</span>
          </div>
          <div style="font-size: 18px; color: #f8fafc; font-weight: 800; letter-spacing: 1px; font-family: 'Space Mono', monospace;">
            <span id="realtime-time">--:--:--</span>
          </div>
        </div>
        <div class="topbar-user-card">
          <div class="user-card-head">
            <div class="user-avatar">
              @if (Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}?t={{ time() }}" alt="{{ Auth::user()->name }}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
              @else
                <i class="fa-regular fa-user"></i>
              @endif
            </div>
            <div class="user-card-title">
              <div class="topbar-user-name" id="greetingName">Halo, {{ Auth::user()->name }}</div>
              <div class="topbar-user-meta" id="userMetaStatus">{{ ucfirst(Auth::user()->role) }} · Akun Aktif</div>
            </div>
          </div>
          <div class="topbar-actions">
            <a href="{{ route('profile.edit') }}" class="btn-ghost" title="Profil Saya">
              <i class="fa-solid fa-user"></i>
            </a>
            <button id="btnLang" class="btn-ghost" title="Ganti Bahasa"><i class="fa-solid fa-language"></i></button>
            <button id="btnTheme" class="btn-ghost" title="Ubah Tema"><i class="fa-solid fa-moon"></i></button>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="btn-ghost" style="color:#f43f5e;border-color:rgba(244,63,94,0.3)">
                <i class="fa-solid fa-power-off"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="layout">

    {{-- KIRI: Orderbook + News + Watchlist + Kalender Event --}}
    <div style="display:flex;flex-direction:column;gap:12px;">
      <div class="card-custom" style="height:390px;display:flex;flex-direction:column;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="sec-label" id="orderbookLabel">ORDER BOOK</span>
          <button onclick="genOrders()" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:10px;padding:0;">
            <i class="fa-solid fa-rotate"></i>
          </button>
        </div>
        <div class="orderbook" id="orderbookContainer"></div>
      </div>
      <div class="card-custom" style="height:420px;display:flex;flex-direction:column;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="sec-label" id="headlinesLabel">LIVE HEADLINES</span>
          <span class="badge-live">● LIVE</span>
        </div>
        <div id="tradingview-news-compact" style="flex-grow:1;overflow-y:auto;"></div>
      </div>

      <div class="card-custom" style="min-height:280px;display:flex;flex-direction:column;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="sec-label" id="watchlistLabel">WATCHLIST & ALERTS</span>
          <button class="btn-ghost" id="watchlistRefreshBtn" style="font-size:10px;" onclick="refreshWatchlist()">Refresh</button>
        </div>
        <div id="watchlistContainer" style="flex-grow:1;overflow:auto;min-height:140px;">
          <div class="text-center c-muted mt-3" id="watchlistLoadingText" style="font-size:11px;">Memuat watchlist...</div>
        </div>
        <div class="mt-3">
          <input type="text" id="watchSymbol" class="form-control-custom mb-2" placeholder="Kode saham, misal BBCA" />
          <div class="d-flex gap-2">
            <input type="number" id="watchTarget" class="form-control-custom" placeholder="Target Rp" />
            <button class="btn-ai" id="watchlistAddBtn" style="flex:1;" onclick="addWatchlistItem()">Tambah</button>
          </div>
        </div>
      </div>

      {{-- *** BARU: KALENDER EVENT EMITEN *** --}}
      <div class="card-custom" style="min-height:280px;display:flex;flex-direction:column;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="sec-label" id="eventLabel">KALENDER EVENT EMITEN</span>
          <span id="eventMonthLabel" class="c-muted" style="font-size:10px;"></span>
        </div>
        <div id="eventCalendar" style="flex-grow:1;overflow:auto;">
          <div class="text-center c-muted mt-3" id="eventLoadingText" style="font-size:11px;">Memuat event...</div>
        </div>
      </div>
      {{-- *** END KALENDER EVENT EMITEN *** --}}

    </div>

    {{-- TENGAH: Chart + Info + AI Prediction + Diskusi --}}
    <div style="display:flex;flex-direction:column;gap:12px;">
      <div class="card-custom" style="height:530px;padding:0;overflow:hidden;">
        <div id="tvchart" style="width:100%;height:100%;"></div>
      </div>

      {{-- Info Row --}}
      <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <span style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;">BBCA</span>
            <span class="c-muted ms-2" style="font-size:11px;">Bank Central Asia</span>
          </div>
          <div class="text-end">
            <div style="font-size:1.15rem;font-weight:700;" id="livePriceHeader">Rp —</div>
            <div id="priceStatusBadge"></div>
          </div>
        </div>
        <hr class="my-2" style="opacity:.07;">
        <div class="d-flex justify-content-between text-center">
          <div><div class="sec-label" style="margin-bottom:3px;">MKT CAP</div><span style="font-size:12px;font-weight:700;">1,250 T</span></div>
          <div><div class="sec-label" style="margin-bottom:3px;">P/E RATIO</div><span style="font-size:12px;font-weight:700;">23.4x</span></div>
          <div><div class="sec-label" style="margin-bottom:3px;">DIV YIELD</div><span style="font-size:12px;font-weight:700;color:var(--green);">2.8%</span></div>
          <div><button class="btn-ghost" style="font-size:10px;" id="btnLaporan">Laporan</button></div>
        </div>
      </div>

      <div class="card-custom" style="padding:14px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="sec-label" id="fundamentalLabel">FUNDAMENTAL SNAPSHOT</span>
          <button class="btn-ghost" id="fundUpdateBtn" onclick="loadFundamentals()" style="font-size:10px;">Update</button>
        </div>
        <div class="row gx-2 gy-2" style="font-size:12px;">
          <div class="col-6"><div class="sec-label">Market Cap</div><div id="fundMarketCap">-</div></div>
          <div class="col-6"><div class="sec-label">Dividend Yield</div><div id="fundDivYield">-</div></div>
          <div class="col-6"><div class="sec-label">P/E</div><div id="fundPE">-</div></div>
          <div class="col-6"><div class="sec-label">PBV</div><div id="fundPBV">-</div></div>
          <div class="col-6"><div class="sec-label">EPS</div><div id="fundEPS">-</div></div>
          <div class="col-6"><div class="sec-label">Revenue/Shr</div><div id="fundRevenue">-</div></div>
        </div>
        <div class="update-tag" id="fundamentalNote">Data fundamental diambil dari server dan dapat disimpan untuk perbandingan prioritas.</div>
      </div>

      {{-- ============ AI PREDICTION PANEL ============ --}}
      <div class="card-custom pred-wrap">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="sec-label" id="aiPredLabel">AI PREDICTION — REALTIME</span>
          <span id="predStatusBadge" class="badge-sim">● SIMULATION</span>
        </div>

        <div class="ta-grid">
          <div class="ta-card">
            <div class="ta-label">HARGA</div>
            <div class="ta-value c-cyan" id="taPrice">Rp —</div>
            <div class="ta-sub" id="taChange">—</div>
          </div>
          <div class="ta-card">
            <div class="ta-label">MA7 / MA25</div>
            <div class="ta-value" id="taMACross">—</div>
            <div class="ta-sub" id="taMADesc">Menghitung...</div>
          </div>
          <div class="ta-card">
            <div class="ta-label">RSI (14)</div>
            <div class="ta-value" id="taRSI">—</div>
            <div class="ta-sub" id="taRSIDesc">—</div>
          </div>
          <div class="ta-card">
            <div class="ta-label">VOLUME SCORE</div>
            <div class="ta-value" id="taVol">—</div>
            <div class="ta-sub" id="taVolDesc">—</div>
          </div>
        </div>

        <div class="signal-bar" id="mainSignalBar">
          <div class="signal-icon" id="signalIcon">⏳</div>
          <div class="signal-text">
            <div class="signal-main" id="signalMain">Menghitung sinyal...</div>
            <div class="signal-desc" id="signalDesc">Menganalisis indikator teknikal BBCA</div>
          </div>
          <div class="signal-conf">
            <div class="signal-pct" id="signalConf">—</div>
            <div class="signal-pct-label">Confidence</div>
          </div>
        </div>

        <div class="indicators-row">
          <div class="ind-item">
            <div class="ind-name">BOLLINGER BANDS</div>
            <div class="ind-val" id="indBB">—</div>
            <div class="ind-sig" id="indBBSig">—</div>
          </div>
          <div class="ind-item">
            <div class="ind-name">MACD HIST</div>
            <div class="ind-val" id="indMACD">—</div>
            <div class="ind-sig" id="indMACDSig">—</div>
          </div>
          <div class="ind-item">
            <div class="ind-name">STOCHASTIC</div>
            <div class="ind-val" id="indStoch">—</div>
            <div class="ind-sig" id="indStochSig">—</div>
          </div>
          <div class="ind-item">
            <div class="ind-name">TREND STRENGTH</div>
            <div class="ind-val" id="indTrend">—</div>
            <div class="ind-sig" id="indTrendSig">—</div>
          </div>
          <div class="ind-item">
            <div class="ind-name">SUPPORT/RESIST</div>
            <div class="ind-val" id="indSR">—</div>
            <div class="ind-sig" id="indSRSig">—</div>
          </div>
          <div class="ind-item">
            <div class="ind-name">PREDIKSI 1 JAM</div>
            <div class="ind-val" id="indPred1h">—</div>
            <div class="ind-sig" id="indPred1hSig">—</div>
          </div>
        </div>

        <div class="target-row">
          <div class="target-pill pill-s"><span class="pill-label">Support</span><span id="tSupport">—</span></div>
          <div class="target-pill pill-b"><span class="pill-label">Target Beli</span><span id="tBuy">—</span></div>
          <div class="target-pill pill-b"><span class="pill-label">Target Jual</span><span id="tSell">—</span></div>
          <div class="target-pill pill-r"><span class="pill-label">Resistance</span><span id="tResist">—</span></div>
        </div>

        {{-- Gemini AI Deep Analysis --}}
        <div class="ai-box">
          <div class="ai-box-header">
            <div style="display:flex;align-items:center;gap:8px;">
              <span class="ai-box-title"> ANALISIS RICKI AI</span>
              <span class="gemini-badge">● Amelia 2.5 Flash</span>
            </div>
            <button class="btn-ai" id="btnAskAI" onclick="askGemini()">
              Analisis Sekarang
            </button>
          </div>
          <div class="ai-result" id="aiResult">
            Klik "Analisis Sekarang" untuk mendapatkan analisis mendalam dari Gemini AI tentang kondisi saham BBCA saat ini, termasuk sentimen pasar dan rekomendasi trading.
          </div>
        </div>

        <div class="update-tag" id="updateTime">Memuat data...</div>
      </div>
      {{-- END AI PREDICTION PANEL --}}

      {{-- *** BARU: VALUASI HARGA WAJAR (DCF MODEL) *** --}}
      <div class="card-custom mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="sec-label">VALUASI HARGA WAJAR (DCF MODEL)</span>
          <span class="disc-badge-bull" id="valStatusBadge">🟢 UNDERVALUED</span>
        </div>
        <div class="row text-center g-2 mb-3">
          <div class="col-4">
            <div class="sec-label" style="font-size:9px;margin-bottom:2px;">FAIR VALUE (DCF)</div>
            <div style="font-size:14px;font-weight:700;color:var(--accent);" id="dcfFairVal">Rp 11.200</div>
          </div>
          <div class="col-4">
            <div class="sec-label" style="font-size:9px;margin-bottom:2px;">DDM MODEL</div>
            <div style="font-size:14px;font-weight:700;color:#34d399;" id="ddmFairVal">Rp 10.950</div>
          </div>
          <div class="col-4">
            <div class="sec-label" style="font-size:9px;margin-bottom:2px;">MARGIN OF SAFETY</div>
            <div style="font-size:14px;font-weight:700;color:#10b981;" id="mosVal">+9.27%</div>
          </div>
        </div>
        <div style="font-size:10px;color:var(--text2);background:rgba(0,212,255,0.04);border:1px solid rgba(0,212,255,0.12);border-radius:6px;padding:8px 10px;line-height:1.5;">
          💡 <strong>Analisis Valuasi BBCA:</strong> Harga pasar saat ini (Rp 10.250) diperdagangkan di bawah Estimasi Harga Wajar DCF (Rp 11.200), memberikan ruang *Margin of Safety* positif sebesar **9.27%**.
        </div>
      </div>

      <div class="card-custom mb-3" id="performanceCard">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="sec-label" id="perfLabel">PORTOFOLIO PERFORMANCE</span>
          <button class="btn-ghost" id="perfRefreshBtn" onclick="loadPerformance()" style="font-size:10px;">Refresh</button>
        </div>
        <div class="d-flex justify-content-between text-center" style="font-size:12px;gap:10px;flex-wrap:wrap;">
          <div style="flex:1;min-width:110px;"><div class="sec-label">Nilai Sekarang</div><div id="perfValue">Rp —</div></div>
          <div style="flex:1;min-width:110px;"><div class="sec-label">Unrealized P/L</div><div id="perfPL">Rp —</div></div>
          <div style="flex:1;min-width:110px;"><div class="sec-label">14D Change</div><div id="perfChange">—</div></div>
        </div>
      </div>

      {{-- *** BARU: PAPAN PERINGKAT TRADER (LEADERBOARD) *** --}}
      <div class="card-custom mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="sec-label">PAPAN PERINGKAT TRADER (ROI %)</span>
          <button class="btn-ghost" style="font-size:10px;" onclick="loadLeaderboard()">Refresh</button>
        </div>
        <div id="leaderboardList" style="font-size:11px;">
          <div class="text-center c-muted py-2">Memuat papan peringkat...</div>
        </div>
      </div>

      {{-- *** BARU: DISKUSI KOMUNITAS MODERN *** --}}
      <div class="card-custom" style="min-height:450px;display:flex;flex-direction:column;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <span class="sec-label" id="communityLabel">DISKUSI KOMUNITAS — BBCA</span>
            <div style="font-size:10px;color:var(--muted);margin-top:-6px;margin-bottom:4px;">Forum Opini & Analisis Investor Realtime</div>
          </div>
          <button class="btn-ai" id="discussionToggleBtn" style="padding:4px 12px;font-size:11px;" onclick="toggleDiscussionForm()">+ Buat Post</button>
        </div>

        {{-- Filter Tabs --}}
        <div class="d-flex gap-2 my-2 pb-2" style="border-bottom:1px solid var(--border);overflow-x:auto;">
          <button class="btn-tab-disc active" onclick="filterDiscussions('ALL', this)">🔥 Semua Topik</button>
          <button class="btn-tab-disc" onclick="filterDiscussions('BULLISH', this)">📈 Bullish</button>
          <button class="btn-tab-disc" onclick="filterDiscussions('BEARISH', this)">📉 Bearish</button>
        </div>

        {{-- Form Buat Diskusi --}}
        <div id="discussionForm" style="display:none;margin-bottom:14px;padding:12px;background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:10px;">
          <div style="font-size:11px;font-weight:600;color:var(--accent);margin-bottom:8px;">📝 BAGIKAN OPINI & ANALISIS KAMU</div>
          <input type="text" id="discTitle" class="form-control-custom mb-2" placeholder="Judul topik diskusi (contoh: Breakout BBCA ke 10.500)...">
          <textarea id="discBody" class="form-control-custom mb-2" rows="3" style="resize:vertical;" placeholder="Tulis analisis teknikal / fundamental kamu..."></textarea>

          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
              <span style="font-size:10px;color:var(--muted);">Sentimen:</span>
              <select id="discSentiment" class="form-control-custom" style="padding:2px 8px;font-size:11px;width:auto;">
                <option value="BULLISH">📈 Bullish</option>
                <option value="NEUTRAL">⚖️ Neutral</option>
                <option value="BEARISH">📉 Bearish</option>
              </select>
            </div>
            <button class="btn-buy" id="discussionSubmitBtn" style="padding:5px 14px;font-size:11px;" onclick="postDiscussion()">Kirim Post</button>
          </div>
        </div>

        {{-- List Diskusi --}}
        <div id="discussionList" style="flex-grow:1;overflow-y:auto;max-height:520px;padding-right:4px;">
          <div class="text-center c-muted mt-4" id="discussionLoadingText" style="font-size:11px;">Memuat diskusi komunitas...</div>
        </div>
      </div>
      {{-- *** END DISKUSI KOMUNITAS *** --}}

    </div>

    {{-- KANAN: Trading Panel --}}
    <div>
      <div class="card-custom">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="sec-label" id="tradingPanelLabel">TRADING PANEL</span>
          <span class="badge-live">● ONLINE</span>
        </div>
        <div style="background:rgba(0,212,255,0.05);border:1px solid rgba(0,212,255,0.12);border-radius:8px;padding:12px;margin-bottom:14px;">
          <div style="font-size:9px;color:var(--muted);letter-spacing:1px;margin-bottom:4px;">BUYING POWER</div>
          <div style="font-size:1.1rem;font-weight:700;color:var(--accent);" id="demoBalance">Rp —</div>
        </div>
        <div class="mb-2">
          <div style="font-size:9px;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">SYMBOL</div>
          <input type="text" id="inputSymbol" class="form-control-custom text-end" value="BBCA">
        </div>
        <div class="mb-2">
          <div style="font-size:9px;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">HARGA (RP)</div>
          <input type="number" id="inputPrice" class="form-control-custom text-end" value="10250">
        </div>
        <div class="mb-3">
          <div style="font-size:9px;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">LOT</div>
          <input type="number" id="inputLot" class="form-control-custom text-end" value="1" min="1">
        </div>
        <div id="tradeError" class="alert alert-danger py-2 mb-2" style="font-size:11px;display:none;border-radius:6px;"></div>
        <div id="alertFeed" style="font-size:11px;min-height:70px;line-height:1.5;color:var(--text2);margin-bottom:12px;">
          <strong id="alertDefaultText" style="color:var(--accent);">Notifikasi harga akan muncul di sini saat harga mendekati level target atau stop loss.</strong>
        </div>
        <div class="row g-2">
          <div class="col-6"><button class="btn-buy"  onclick="trade('BUY')">▲ BUY</button></div>
          <div class="col-6"><button class="btn-sell" onclick="trade('SELL')">▼ SELL</button></div>
        </div>
        <hr style="opacity:.07;margin:16px 0;">
        <div class="sec-label" id="myPortfolioLabel">PORTOFOLIO SAYA</div>
        <div class="d-flex justify-content-between mb-1" style="font-size:12px;"><span class="c-muted">Lot:</span><span class="fw-bold" id="ownedLots">0</span></div>
        <div class="d-flex justify-content-between mb-1" style="font-size:12px;"><span class="c-muted">Avg Price:</span><span class="fw-bold" id="avgPrice">-</span></div>
        <div class="d-flex justify-content-between mb-1" style="font-size:12px;"><span class="c-muted">P/L:</span><span class="fw-bold" id="pnl">Rp 0 (0%)</span></div>
        <div class="mt-3 d-grid gap-2">
          <button class="btn-ghost w-100" id="exportHistoryBtn" style="font-size:10px;" onclick="exportCSV()"><i class="fa-solid fa-download me-1"></i> Download History</button>
          <button class="btn-ghost w-100" id="resetAccountBtn" style="font-size:10px;color:#f43f5e;border-color:rgba(244,63,94,0.25);" onclick="resetDemo()">Reset Akun</button>
        </div>
      </div>

      <div class="card-custom mt-3">
        <div class="sec-label" id="historyOrderLabel">HISTORY ORDER</div>
        <div id="tradeHistory" style="font-size:10px;max-height:200px;overflow:auto;">
          <div class="text-center c-muted mt-3" id="historyLoadingText" style="font-size:11px;">Memuat...</div>
        </div>
      </div>

      {{-- *** BARU: KALKULATOR RISK / REWARD (R:R) *** --}}
      <div class="card-custom mt-3">
        <div class="sec-label">KALKULATOR RISK / REWARD (R:R)</div>
        <div class="row g-2 mb-2">
          <div class="col-6">
            <div style="font-size:9px;color:var(--muted);margin-bottom:3px;">STOP LOSS (RP)</div>
            <input type="number" id="calcSL" class="form-control-custom text-end" value="10000" oninput="updateRRCalc()">
          </div>
          <div class="col-6">
            <div style="font-size:9px;color:var(--muted);margin-bottom:3px;">TARGET PROFIT (RP)</div>
            <input type="number" id="calcTP" class="form-control-custom text-end" value="10750" oninput="updateRRCalc()">
          </div>
        </div>
        <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:8px;padding:10px;font-size:11px;">
          <div class="d-flex justify-content-between mb-1"><span class="c-muted">Risk/Reward Ratio:</span><strong id="rrRatio" style="color:var(--accent);">1 : 2.00</strong></div>
          <div class="d-flex justify-content-between mb-1"><span class="c-muted">Potensi Profit:</span><strong id="rrProfit" class="c-green">+Rp 50.000 / lot</strong></div>
          <div class="d-flex justify-content-between"><span class="c-muted">Batas Potensial Rugi:</span><strong id="rrLoss" class="c-red">-Rp 25.000 / lot</strong></div>
        </div>
        <div class="mt-2 text-center">
          <button class="btn-ghost w-100" style="font-size:10px;color:var(--accent);border-color:rgba(6,182,212,0.25);" onclick="requestNotificationPermission()">
            🔔 Aktifkan Web Push Alert
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ==========================================================
   CONFIG
   ========================================================== */
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const API  = {
  DISCUSSIONS: '/api/discussions',
  EVENTS: '/api/events',
  TRADE: '/api/trade',
  PORTFOLIO: '/api/portfolio',
  HISTORY: '/api/trade/history',
  RESET: '/api/trade/reset',
  WATCHLIST: '/api/watchlist',
  QUOTE: '/api/market/quote',
  FUNDAMENTALS: '/api/market/fundamentals',
  BACKTEST: '/api/market/backtest',
  PERFORMANCE: '/api/market/performance',
  SENTIMENT: '/api/market/sentiment',
  AI: '/api/ai/analyze',
};

let currentPrice  = 0;
let previousClose = 0;
let priceChange   = 0;
let priceChangePercent = 0;
let priceStatus   = 'loading';
let fetchFailCount = 0;
let priceHistory  = [];
let watchlistItems = [];
let selectedSymbol = 'BBCA';
let portfolio = { balance: {{ (float) (Auth::user()->balance ?? 100000000) }}, holdings: [], lots: {{ (int) (Auth::user()->lots ?? 0) }}, avg_price: {{ (float) (Auth::user()->avg_price ?? 0) }}, total_value: 0 };
let tradeHistory = [];

const APP_LANG_KEY = 'app-lang';
let currentLang = localStorage.getItem(APP_LANG_KEY) === 'en' ? 'en' : 'id';

const I18N = {
  id: {
    brandLabel: 'BBCA Investor Dashboard',
    brandSubtitle: 'Data pasar, posisi portofolio, dan sinyal trading tersaji dalam tampilan yang bersih dan fokus.',
    userMetaStatus: 'Akun Aktif',
    greeting: 'Halo',
    btnLangTitle: 'Ganti Bahasa',
    btnThemeTitle: 'Ubah Tema',
    orderbookLabel: 'ORDER BOOK',
    headlinesLabel: 'LIVE HEADLINES',
    watchlistLabel: 'WATCHLIST & ALERTS',
    watchlistRefreshBtn: 'Refresh',
    watchlistAddBtn: 'Tambah',
    eventLabel: 'KALENDER EVENT EMITEN',
    btnLaporan: 'Laporan',
    fundamentalLabel: 'FUNDAMENTAL SNAPSHOT',
    fundUpdateBtn: 'Update',
    aiPredLabel: 'AI PREDICTION - REALTIME',
    btnAskAI: 'Analisis Sekarang',
    perfLabel: 'PORTOFOLIO PERFORMANCE',
    perfRefreshBtn: 'Refresh',
    communityLabel: 'DISKUSI KOMUNITAS - BBCA',
    discussionToggleBtn: '+ Post',
    discussionSubmitBtn: 'Kirim Diskusi',
    tradingPanelLabel: 'TRADING PANEL',
    myPortfolioLabel: 'PORTOFOLIO SAYA',
    exportHistoryBtn: 'Download History',
    resetAccountBtn: 'Reset Akun',
    historyOrderLabel: 'HISTORY ORDER',
    buyBtn: 'BUY',
    sellBtn: 'SELL',
    aiAnalyzingBtn: 'Menganalisis...',
    aiAnalyzingText: 'Ricki AI sedang menganalisis kondisi pasar BBCA...',
    aiNoResponse: 'Tidak ada respons dari AI. Coba lagi.',
    aiConnectionFail: 'Gagal menghubungi Gemini AI. Cek GEMINI_API_KEY di .env server.',
    aiAnalyzeAgain: 'Analisis Lagi',
    signalCalculating: 'Menghitung sinyal...',
    signalAnalyzing: 'Menganalisis indikator teknikal BBCA',
    confLabel: 'Confidence',
    neutral: 'Netral',
    highVolume: 'Volume Tinggi',
    lowVolume: 'Rendah',
    normalVolume: 'Normal',
    bullishCross: '▲ Bullish Cross',
    bearishCross: '▼ Bearish Cross',
    overbought: 'Overbought',
    oversold: 'Oversold',
    loadingData: 'Memuat data...',
    watchlistLoadingText: 'Memuat watchlist...',
    eventLoadingText: 'Memuat event...',
    discussionLoadingText: 'Memuat diskusi...',
    historyLoadingText: 'Memuat...',
    alertDefaultText: 'Notifikasi harga akan muncul di sini saat harga mendekati level target atau stop loss.',
    targetReached: 'sudah mencapai target',
    stopTouched: 'sudah menyentuh stop loss',
    fundamentalLoaded: 'Data fundamental untuk',
    loadFundamentalFailed: 'Gagal memuat fundamental. Coba lagi.',
    position: 'Pos',
    nearUpper: 'Dekat Atas',
    nearLower: 'Dekat Bawah',
    middle: 'Tengah',
    strongTrend: 'Tren Kuat',
    mediumTrend: 'Sedang',
    ranging: 'Ranging',
    updateLabel: 'Update',
    watchSymbolPh: 'Kode saham, misal BBCA',
    watchTargetPh: 'Target Rp',
    discTitlePh: 'Judul diskusi...',
    discBodyPh: 'Tulis pendapat kamu tentang BBCA...',
    loadingConnect: 'Menghubungkan...',
    statusLive: 'LIVE',
    statusClosed: 'LAST CLOSE',
    statusOffline: 'OFFLINE',
    statusSimulation: 'SIMULATION MODE',
    watchlistEmpty: 'Watchlist kosong. Tambahkan saham favoritmu.',
    watchlistLoadError: 'Tidak dapat memuat watchlist. Coba refresh.',
    watchlistDelete: 'Hapus',
    target: 'Target',
    stop: 'Stop',
    noNotes: 'Tidak ada catatan',
    noAlert: 'Tidak ada notifikasi harga baru. Pantau target dan stop loss watchlist.',
    fillSymbol: 'Isi kode saham terlebih dahulu.',
    addWatchlistFailed: 'Gagal tambah watchlist:',
    confirmDeleteWatch: 'Hapus item dari watchlist?',
    deleteWatchFailed: 'Gagal menghapus watchlist.',
    noTransaction: 'Belum ada transaksi.',
    invalidLot: 'Masukkan jumlah lot yang valid.',
    tradeFailed: 'Transaksi gagal.',
    networkRetry: 'Koneksi gagal. Coba lagi.',
    resetConfirm: 'Reset portofolio ke awal (Rp 100jt)?',
    resetFailed: 'Gagal reset.',
    noHistory: 'Belum ada data history!',
    noDiscussion: 'Belum ada diskusi. Jadilah yang pertama!',
    discussionLoadFailed: 'Gagal memuat diskusi.',
    discussionPostFailed: 'Gagal posting diskusi.',
    commentPostFailed: 'Gagal posting komentar.',
    discussionRequired: 'Judul dan isi wajib diisi.',
    commentPlaceholder: 'Tulis komentar...',
    send: 'Kirim',
    by: 'oleh',
    eventEmpty: 'Tidak ada agenda event emiten bulan ini.',
    eventLoadFailed: 'Gagal memuat event. Coba lagi.',
    timeSecondAgo: 'd lalu',
    timeMinuteAgo: 'm lalu',
    timeHourAgo: 'j lalu',
    timeDayAgo: ' hari lalu'
  },
  en: {
    brandLabel: 'BBCA Investor Dashboard',
    brandSubtitle: 'Market data, portfolio position, and trading signals in a clean and focused interface.',
    userMetaStatus: 'Active Account',
    greeting: 'Hi',
    btnLangTitle: 'Switch Language',
    btnThemeTitle: 'Toggle Theme',
    orderbookLabel: 'ORDER BOOK',
    headlinesLabel: 'LIVE HEADLINES',
    watchlistLabel: 'WATCHLIST & ALERTS',
    watchlistRefreshBtn: 'Refresh',
    watchlistAddBtn: 'Add',
    eventLabel: 'ISSUER EVENT CALENDAR',
    btnLaporan: 'Report',
    fundamentalLabel: 'FUNDAMENTAL SNAPSHOT',
    fundUpdateBtn: 'Update',
    aiPredLabel: 'AI PREDICTION - REALTIME',
    btnAskAI: 'Analyze Now',
    perfLabel: 'PORTFOLIO PERFORMANCE',
    perfRefreshBtn: 'Refresh',
    communityLabel: 'COMMUNITY DISCUSSIONS - BBCA',
    discussionToggleBtn: '+ Post',
    discussionSubmitBtn: 'Submit Discussion',
    tradingPanelLabel: 'TRADING PANEL',
    myPortfolioLabel: 'MY PORTFOLIO',
    exportHistoryBtn: 'Download History',
    resetAccountBtn: 'Reset Account',
    historyOrderLabel: 'ORDER HISTORY',
    buyBtn: 'BUY',
    sellBtn: 'SELL',
    aiAnalyzingBtn: 'Analyzing...',
    aiAnalyzingText: 'Ricki AI is analyzing BBCA market conditions...',
    aiNoResponse: 'No response from AI. Please try again.',
    aiConnectionFail: 'Failed to connect to Gemini AI. Check GEMINI_API_KEY in server .env.',
    aiAnalyzeAgain: 'Analyze Again',
    signalCalculating: 'Calculating signal...',
    signalAnalyzing: 'Analyzing BBCA technical indicators',
    confLabel: 'Confidence',
    neutral: 'Neutral',
    highVolume: 'High Volume',
    lowVolume: 'Low',
    normalVolume: 'Normal',
    bullishCross: '▲ Bullish Cross',
    bearishCross: '▼ Bearish Cross',
    overbought: 'Overbought',
    oversold: 'Oversold',
    loadingData: 'Loading data...',
    watchlistLoadingText: 'Loading watchlist...',
    eventLoadingText: 'Loading events...',
    discussionLoadingText: 'Loading discussions...',
    historyLoadingText: 'Loading...',
    alertDefaultText: 'Price notifications will appear here when price nears target or stop loss.',
    targetReached: 'has reached target',
    stopTouched: 'has touched stop loss',
    fundamentalLoaded: 'Fundamental data for',
    loadFundamentalFailed: 'Failed to load fundamentals. Try again.',
    position: 'Pos',
    nearUpper: 'Near Upper',
    nearLower: 'Near Lower',
    middle: 'Middle',
    strongTrend: 'Strong Trend',
    mediumTrend: 'Medium',
    ranging: 'Ranging',
    updateLabel: 'Updated',
    watchSymbolPh: 'Stock code, e.g. BBCA',
    watchTargetPh: 'Target Price',
    discTitlePh: 'Discussion title...',
    discBodyPh: 'Write your BBCA opinion...',
    loadingConnect: 'Connecting...',
    statusLive: 'LIVE',
    statusClosed: 'LAST CLOSE',
    statusOffline: 'OFFLINE',
    statusSimulation: 'SIMULATION MODE',
    watchlistEmpty: 'Watchlist is empty. Add your favorite stocks.',
    watchlistLoadError: 'Unable to load watchlist. Try refresh.',
    watchlistDelete: 'Delete',
    target: 'Target',
    stop: 'Stop',
    noNotes: 'No notes',
    noAlert: 'No new price alerts. Monitor your watchlist target and stop loss.',
    fillSymbol: 'Please fill the stock code first.',
    addWatchlistFailed: 'Failed to add watchlist:',
    confirmDeleteWatch: 'Remove this item from watchlist?',
    deleteWatchFailed: 'Failed to remove watchlist.',
    noTransaction: 'No transactions yet.',
    invalidLot: 'Please enter a valid lot amount.',
    tradeFailed: 'Transaction failed.',
    networkRetry: 'Connection failed. Please try again.',
    resetConfirm: 'Reset portfolio to default (Rp 100M)?',
    resetFailed: 'Reset failed.',
    noHistory: 'No history data yet!',
    noDiscussion: 'No discussions yet. Be the first to post!',
    discussionLoadFailed: 'Failed to load discussions.',
    discussionPostFailed: 'Failed to post discussion.',
    commentPostFailed: 'Failed to post comment.',
    eventEmpty: 'No issuer events scheduled for this month.',
    eventLoadFailed: 'Failed to load events. Try again.',
    discussionRequired: 'Title and content are required.',
    commentPlaceholder: 'Write a comment...',
    send: 'Send',
    by: 'by',
    timeSecondAgo: 's ago',
    timeMinuteAgo: 'm ago',
    timeHourAgo: 'h ago',
    timeDayAgo: ' days ago'
  }
};

function t(key) {
  return I18N[currentLang]?.[key] ?? I18N.id[key] ?? key;
}

function setText(id, key) {
  const el = document.getElementById(id);
  if (el) el.textContent = t(key);
}

function applyLanguage() {
  document.documentElement.lang = currentLang === 'en' ? 'en' : 'id';
  setText('brandLabel', 'brandLabel');
  setText('brandSubtitle', 'brandSubtitle');
  setText('orderbookLabel', 'orderbookLabel');
  setText('headlinesLabel', 'headlinesLabel');
  setText('watchlistLabel', 'watchlistLabel');
  setText('watchlistRefreshBtn', 'watchlistRefreshBtn');
  setText('watchlistAddBtn', 'watchlistAddBtn');
  setText('eventLabel', 'eventLabel');
  setText('btnLaporan', 'btnLaporan');
  setText('fundamentalLabel', 'fundamentalLabel');
  setText('fundUpdateBtn', 'fundUpdateBtn');
  setText('aiPredLabel', 'aiPredLabel');
  setText('btnAskAI', 'btnAskAI');
  setText('perfLabel', 'perfLabel');
  setText('perfRefreshBtn', 'perfRefreshBtn');
  setText('communityLabel', 'communityLabel');
  setText('discussionToggleBtn', 'discussionToggleBtn');
  setText('discussionSubmitBtn', 'discussionSubmitBtn');
  setText('tradingPanelLabel', 'tradingPanelLabel');
  setText('myPortfolioLabel', 'myPortfolioLabel');
  setText('historyOrderLabel', 'historyOrderLabel');

  const exportBtn = document.getElementById('exportHistoryBtn');
  if (exportBtn) exportBtn.innerHTML = '<i class="fa-solid fa-download me-1"></i> ' + t('exportHistoryBtn');
  setText('resetAccountBtn', 'resetAccountBtn');

  const buyBtn = document.querySelector('.btn-buy');
  const sellBtn = document.querySelector('.btn-sell');
  if (buyBtn) buyBtn.innerHTML = '▲ ' + t('buyBtn');
  if (sellBtn) sellBtn.innerHTML = '▼ ' + t('sellBtn');

  const wSym = document.getElementById('watchSymbol');
  const wTarget = document.getElementById('watchTarget');
  const dTitle = document.getElementById('discTitle');
  const dBody = document.getElementById('discBody');
  if (wSym) wSym.placeholder = t('watchSymbolPh');
  if (wTarget) wTarget.placeholder = t('watchTargetPh');
  if (dTitle) dTitle.placeholder = t('discTitlePh');
  if (dBody) dBody.placeholder = t('discBodyPh');

  const langBtn = document.getElementById('btnLang');
  if (langBtn) {
    langBtn.title = t('btnLangTitle') + ' (' + (currentLang === 'id' ? 'ID' : 'EN') + ')';
  }
  const themeBtn = document.getElementById('btnTheme');
  if (themeBtn) themeBtn.title = t('btnThemeTitle');

  const roleText = '{{ ucfirst(Auth::user()->role) }}';
  const userMeta = document.getElementById('userMetaStatus');
  if (userMeta) userMeta.textContent = roleText + ' · ' + t('userMetaStatus');
  const greetingName = document.getElementById('greetingName');
  if (greetingName) greetingName.textContent = t('greeting') + ', {{ Auth::user()->name }}';
  setText('watchlistLoadingText', 'watchlistLoadingText');
  setText('eventLoadingText', 'eventLoadingText');
  setText('discussionLoadingText', 'discussionLoadingText');
  setText('historyLoadingText', 'historyLoadingText');
  setText('alertDefaultText', 'alertDefaultText');
}

function toggleLanguage() {
  currentLang = currentLang === 'id' ? 'en' : 'id';
  localStorage.setItem(APP_LANG_KEY, currentLang);
  applyLanguage();
  renderPriceUI();
  renderHistory();
  renderWatchlist();
}

/* ==========================================================
   PORTFOLIO
   ========================================================== */
async function loadPortfolio() {
  try {
    const res = await fetch(API.PORTFOLIO, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error();
    const data = await res.json();
    portfolio = {
      balance: data.balance || 0,
      holdings: data.holdings || [],
    };
    portfolio.lots = portfolio.holdings.reduce((sum, item) => sum + (item.lot || 0), 0);
    portfolio.avg_price = portfolio.holdings.length > 0
      ? portfolio.holdings.reduce((sum, item) => sum + (item.avg_price || 0), 0) / portfolio.holdings.length
      : 0;
  } catch(e) { console.warn('Gagal load portfolio.', e); }
  updatePortfolioUI();
}

async function loadHistory() {
  const el = document.getElementById('tradeHistory');
  if (el) {
    el.innerHTML = '<div class="text-center c-muted mt-3" style="font-size:11px;">' + t('historyLoadingText') + '</div>';
  }

  try {
    const res = await fetch(API.HISTORY, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Gagal load history');
    const data = await res.json();
    tradeHistory = Array.isArray(data) ? data : [];
  } catch(e) {
    console.warn('Gagal load history.', e);
    tradeHistory = [];
  }

  renderHistory();
}

/* ==========================================================
   FETCH HARGA REAL BBCA
   ========================================================== */
const YAHOO_CHART_URL = 'https://query1.finance.yahoo.com/v8/finance/chart/BBCA.JK?interval=1m&range=5d&includePrePost=false';
const CORS_PROXIES = [
  u => 'https://api.allorigins.win/raw?url=' + encodeURIComponent(u),
  u => 'https://corsproxy.io/?' + encodeURIComponent(u),
  u => 'https://api.codetabs.com/v1/proxy?quest=' + encodeURIComponent(u),
];

async function fetchBBCARealPrice() {
  const targetUrl = YAHOO_CHART_URL + '&t=' + Date.now();
  for (let i = 0; i < CORS_PROXIES.length; i++) {
    try {
      const ctrl = new AbortController();
      const timer = setTimeout(() => ctrl.abort(), 5000);
      const res = await fetch(CORS_PROXIES[i](targetUrl), { signal: ctrl.signal });
      clearTimeout(timer);
      if (!res.ok) continue;
      const json = await res.json();
      const result = json?.chart?.result?.[0];
      const meta = result?.meta;
      if (!meta || typeof meta.regularMarketPrice !== 'number' || meta.regularMarketPrice <= 0) continue;
      const closes = result?.indicators?.quote?.[0]?.close?.filter(v => v != null) || [];
      if (closes.length > 10) priceHistory = closes.slice(-60).map(v => Math.round(v));
      const price     = Math.round(meta.regularMarketPrice);
      const prevClose = Math.round(meta.chartPreviousClose || meta.previousClose || meta.regularMarketPrice);
      const now = new Date();
      const wib = new Date(now.getTime() + now.getTimezoneOffset()*60000 + 7*3600000);
      const isMarketOpen = (wib.getDay()>=1 && wib.getDay()<=5) && (wib.getHours()>=9 && wib.getHours()<15);
      return { price, prevClose, isMarketOpen };
    } catch(err) { console.warn('Proxy ' + (i+1) + ' gagal:', err.message); }
  }
  return null;
}

async function updatePrice() {
  const result = await fetchBBCARealPrice();
  if (result) {
    currentPrice       = result.price;
    previousClose      = result.prevClose;
    priceChange        = result.price - result.prevClose;
    priceChangePercent = previousClose ? (priceChange / previousClose) * 100 : 0;
    priceStatus        = result.isMarketOpen ? 'live' : 'closed';
    fetchFailCount     = 0;
  } else {
    fetchFailCount++;
    if (currentPrice === 0) { currentPrice = 10250; previousClose = 10200; }
    if (fetchFailCount > 3) {
      priceStatus   = 'simulation';
      currentPrice += Math.floor(Math.random() * 51) - 25;
      if (priceHistory.length === 0) priceHistory = genSimPrices(currentPrice, 60);
      else { priceHistory.push(currentPrice); if (priceHistory.length > 60) priceHistory.shift(); }
      priceChange        = currentPrice - previousClose;
      priceChangePercent = previousClose ? (priceChange / previousClose) * 100 : 0;
    }
    if (fetchFailCount > 9) priceStatus = 'offline';
  }
  renderPriceUI();
  renderOrderbook(Math.round(currentPrice));
  updatePnL();
  checkAlerts();
  runTA();
}

/* ==========================================================
   RENDER PRICE UI
   ========================================================== */
function renderPriceUI() {
  const price  = Math.round(currentPrice);
  const hdrEl  = document.getElementById('livePriceHeader');
  const bdgEl  = document.getElementById('priceStatusBadge');
  if (hdrEl) {
    const sign  = priceChange >= 0 ? '+' : '';
    const clr   = priceChange >= 0 ? 'c-green' : 'c-red';
    const arrow = priceChange >= 0 ? 'fa-caret-up' : 'fa-caret-down';
    hdrEl.innerHTML = 'Rp ' + price.toLocaleString('id-ID') +
      ' <span class="' + clr + '" style="font-size:.6em;margin-left:4px">' +
      '<i class="fa-solid ' + arrow + '"></i> ' + sign + Math.round(priceChange) +
      ' (' + sign + priceChangePercent.toFixed(2) + '%)</span>';
  }
  if (bdgEl) {
    const cfg = {
      loading:    { text:t('loadingConnect'),  cls:'badge-sim',  icon:'fa-spinner fa-spin' },
      live:       { text:t('statusLive'),      cls:'badge-live', icon:'fa-signal' },
      closed:     { text:t('statusClosed'),    cls:'badge-sim',  icon:'fa-moon' },
      offline:    { text:t('statusOffline'),   cls:'',           icon:'fa-triangle-exclamation' },
      simulation: { text:t('statusSimulation'),cls:'badge-sim',  icon:'fa-robot' },
    };
    const c = cfg[priceStatus] || cfg.loading;
    bdgEl.innerHTML = '<span class="' + c.cls + '" style="font-size:7px;vertical-align:middle;margin-left:4px">' +
      '<i class="fa-solid ' + c.icon + ' me-1"></i>' + c.text + '</span>';
  }
  const priceInput = document.getElementById('inputPrice');
  if (priceInput) priceInput.value = price;
}

/* ==========================================================
   ORDERBOOK
   ========================================================== */
function renderOrderbook(price) {
  const c = document.getElementById('orderbookContainer');
  if (!c) return;
  const sellLevels = [];
  const buyLevels  = [];

  for (let i = 5; i >= 1; i--) {
    const p = price + (i * 25);
    const decay = 1 - ((6 - i) * 0.14);
    const v = Math.max(120, Math.round((3200 * decay) + (Math.random() * 450)));
    sellLevels.push({ price: p, volume: v });
  }

  for (let i = 1; i <= 5; i++) {
    const p = price - (i * 25);
    const decay = 1 - ((i - 1) * 0.14);
    const v = Math.max(120, Math.round((3200 * decay) + (Math.random() * 450)));
    buyLevels.push({ price: p, volume: v });
  }

  const maxSell = Math.max(...sellLevels.map(row => row.volume));
  const maxBuy  = Math.max(...buyLevels.map(row => row.volume));
  const totalSell = sellLevels.reduce((sum, row) => sum + row.volume, 0);
  const totalBuy  = buyLevels.reduce((sum, row) => sum + row.volume, 0);
  const bestAsk = sellLevels[0];
  const bestBid = buyLevels[0];

  let sellHtml = '<div class="order-side-head sell"><span>SELL</span><span>Total ' + totalSell.toLocaleString('id-ID') + '</span></div><div class="order-side-body">';
  let buyHtml  = '<div class="order-side-head buy"><span>BUY</span><span>Total ' + totalBuy.toLocaleString('id-ID') + '</span></div><div class="order-side-body">';

  sellLevels.forEach(row => {
    const bar = Math.min(100, (row.volume / maxSell) * 100);
    sellHtml += '<div class="order-row sell" style="position:relative;overflow:hidden;border-radius:6px;background:rgba(244,63,94,0.03);">' +
      '<span style="position:absolute;left:0;top:0;height:100%;width:' + bar + '%;background:linear-gradient(90deg, rgba(244,63,94,0.30), rgba(244,63,94,0.10));border-right:1px solid rgba(244,63,94,0.25);"></span>' +
      '<span class="order-vol">' + row.volume.toLocaleString('id-ID') + '</span>' +
      '<span class="order-price" style="color:#ffadc0">' + row.price.toLocaleString('id-ID') + '</span></div>';
  });

  buyLevels.forEach(row => {
    const bar = Math.min(100, (row.volume / maxBuy) * 100);
    buyHtml += '<div class="order-row buy" style="position:relative;overflow:hidden;border-radius:6px;background:rgba(16,185,129,0.03);">' +
      '<span style="position:absolute;right:0;top:0;height:100%;width:' + bar + '%;background:linear-gradient(90deg, rgba(16,185,129,0.10), rgba(16,185,129,0.30));border-left:1px solid rgba(16,185,129,0.25);"></span>' +
      '<span class="order-price" style="color:#96f0c0">' + row.price.toLocaleString('id-ID') + '</span>' +
      '<span class="order-vol" style="margin-left:auto;text-align:right;">' + row.volume.toLocaleString('id-ID') + '</span></div>';
  });

  sellHtml += '</div>';
  buyHtml += '</div>';

  c.innerHTML = '<div class="orderbook-split">' +
    '<div class="orderbook-shell" style="grid-column:1 / -1;">' +
      '<div class="orderbook-header">' +
        '<div class="orderbook-summary sell"><div class="label">Best Ask</div><div class="value">' + bestAsk.price.toLocaleString('id-ID') + ' · ' + bestAsk.volume.toLocaleString('id-ID') + '</div></div>' +
        '<div class="orderbook-center"><div class="mid-label">Market Depth</div><div class="mid-value">' + price.toLocaleString('id-ID') + '</div></div>' +
        '<div class="orderbook-summary buy"><div class="label">Best Bid</div><div class="value">' + bestBid.price.toLocaleString('id-ID') + ' · ' + bestBid.volume.toLocaleString('id-ID') + '</div></div>' +
      '</div>' +
      '<div class="orderbook-split">' +
        '<div class="order-side">' + sellHtml + '</div>' +
        '<div class="order-mid-stack"><div class="order-mid">' + price.toLocaleString('id-ID') + '</div></div>' +
        '<div class="order-side">' + buyHtml + '</div>' +
      '</div>' +
    '</div>' +
  '</div>';
}
function genOrders() { renderOrderbook(Math.round(currentPrice) || 10250); }

/* ==========================================================
   PORTFOLIO UI
   ========================================================== */
function updatePortfolioUI() {
  const b = document.getElementById('demoBalance');
  if (b) b.innerText = 'Rp ' + Math.round(portfolio.balance).toLocaleString('id-ID');
  const l = document.getElementById('ownedLots');
  if (l) l.innerText = portfolio.lots;
  const a = document.getElementById('avgPrice');
  if (a) a.innerText = portfolio.avg_price > 0 ? 'Rp ' + Math.round(portfolio.avg_price).toLocaleString('id-ID') : '-';
  updatePnL();
}

function updatePnL() {
  const el = document.getElementById('pnl');
  if (!el) return;
  if (portfolio.lots > 0 && portfolio.avg_price > 0 && currentPrice > 0) {
    const cur  = portfolio.lots * 100 * Math.round(currentPrice);
    const cost = portfolio.lots * 100 * portfolio.avg_price;
    const pnl  = cur - cost;
    const pct  = (pnl / cost) * 100;
    const clr  = pnl >= 0 ? 'c-green' : 'c-red';
    const sign = pnl >= 0 ? '+' : '';
    el.innerHTML = '<span class="' + clr + '">' + sign + 'Rp ' + Math.round(pnl).toLocaleString('id-ID') + ' (' + pct.toFixed(2) + '%)</span>';
  } else {
    el.innerText = 'Rp 0 (0%)';
  }
}

async function loadPerformance() {
  try {
    const res = await fetch(API.PERFORMANCE, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Gagal memuat performa.');
    const data = await res.json();

    const perfValue = document.getElementById('perfValue');
    const perfPL = document.getElementById('perfPL');
    const perfChange = document.getElementById('perfChange');

    if (perfValue) perfValue.innerText = data.total_value ? 'Rp ' + Math.round(data.total_value).toLocaleString('id-ID') : 'Rp -';
    if (perfPL) perfPL.innerText = data.unrealized ? 'Rp ' + Math.round(data.unrealized).toLocaleString('id-ID') : 'Rp -';
    if (perfChange) {
      const history = data.history || [];
      const change = history.length > 1 ? history[history.length - 1].value - history[0].value : 0;
      const sign = change >= 0 ? '+' : '';
      perfChange.innerText = sign + 'Rp ' + Math.round(change).toLocaleString('id-ID');
    }
  } catch (e) {
    console.warn('loadPerformance error:', e);
    document.getElementById('perfValue').innerText = 'Rp -';
    document.getElementById('perfPL').innerText = 'Rp -';
    document.getElementById('perfChange').innerText = '—';
  }
}

async function loadFundamentals() {
  const symbol = document.getElementById('inputSymbol')?.value.trim().toUpperCase() || 'BBCA';
  try {
    const res = await fetch(API.FUNDAMENTALS + '?symbol=' + encodeURIComponent(symbol), {
      headers: { 'Accept': 'application/json' }
    });
    if (!res.ok) throw new Error('Gagal memuat data fundamental.');
    const data = await res.json();
    const fund = data.fundamentals || {};
    document.getElementById('fundMarketCap').innerText = fund.market_cap ? 'Rp ' + Math.round(fund.market_cap).toLocaleString('id-ID') : '-';
    document.getElementById('fundDivYield').innerText = fund.dividend_yield ? fund.dividend_yield.toFixed(1) + '%' : '-';
    document.getElementById('fundPE').innerText = fund.trailing_pe ? fund.trailing_pe.toFixed(2) : '-';
    document.getElementById('fundPBV').innerText = fund.price_to_book ? fund.price_to_book.toFixed(2) : '-';
    document.getElementById('fundEPS').innerText = fund.eps ? fund.eps.toFixed(2) : '-';
    document.getElementById('fundRevenue').innerText = fund.revenue_per_share ? fund.revenue_per_share.toFixed(2) : '-';
    document.getElementById('fundamentalNote').innerText = t('fundamentalLoaded') + ' ' + symbol + ' berhasil dimuat.';
  } catch (e) {
    console.warn('loadFundamentals error:', e);
    document.getElementById('fundamentalNote').innerText = t('loadFundamentalFailed');
  }
}

async function loadWatchlist() {
  try {
    const res = await fetch(API.WATCHLIST, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) throw new Error('Gagal load watchlist');
    const data = await res.json();
    watchlistItems = data.watchlist || [];
    renderWatchlist();
    checkAlerts();
  } catch (e) {
    console.warn('Gagal load watchlist', e);
    const container = document.getElementById('watchlistContainer');
    if (container) {
      container.innerHTML = '<div class="text-center c-muted mt-3" style="font-size:11px;">' + t('watchlistLoadError') + '</div>';
    }
  }
}

function renderWatchlist() {
  const container = document.getElementById('watchlistContainer');
  if (!container) return;
  if (!watchlistItems || watchlistItems.length === 0) {
    container.innerHTML = '<div class="text-center c-muted mt-3" style="font-size:11px;">' + t('watchlistEmpty') + '</div>';
    return;
  }

  container.innerHTML = watchlistItems.map(item => {
    const locale = currentLang === 'en' ? 'en-US' : 'id-ID';
    const targetText = item.target_price ? 'Rp ' + Number(item.target_price).toLocaleString(locale) : '-';
    const stopText   = item.stop_loss ? 'Rp ' + Number(item.stop_loss).toLocaleString(locale) : '-';
    return '<div class="news-item">' +
      '<div class="d-flex justify-content-between align-items-center">' +
      '<strong>' + item.symbol + '</strong>' +
        '<button class="btn-ghost" style="font-size:10px;" onclick="removeWatchlist(' + item.id + ')">' + t('watchlistDelete') + '</button>' +
      '</div>' +
        '<div style="font-size:11px;margin-top:5px;">' + t('target') + ': ' + targetText + ' · ' + t('stop') + ': ' + stopText + '</div>' +
        '<div style="font-size:11px;color:var(--muted);margin-top:4px;">' + (item.notes || t('noNotes')) + '</div>' +
      '</div>';
  }).join('');
}

function checkAlerts() {
  const feed = document.getElementById('alertFeed');
  if (!feed) return;

  const messages = [];
  watchlistItems.forEach(item => {
    const target = Number(item.target_price || 0);
    const stop = Number(item.stop_loss || 0);
    if (target > 0 && currentPrice >= target) {
      messages.push('📈 ' + item.symbol + ' ' + t('targetReached') + ' ' + target.toLocaleString(currentLang === 'en' ? 'en-US' : 'id-ID'));
    }
    if (stop > 0 && currentPrice <= stop) {
      messages.push('⚠️ ' + item.symbol + ' ' + t('stopTouched') + ' ' + stop.toLocaleString(currentLang === 'en' ? 'en-US' : 'id-ID'));
    }
  });

  if (messages.length === 0) {
    feed.innerHTML = '<span class="c-muted">' + t('noAlert') + '</span>';
  } else {
    feed.innerHTML = messages.map(msg => '<div>' + msg + '</div>').join('');
  }
}

async function addWatchlistItem() {
  const symbol = document.getElementById('watchSymbol')?.value.trim().toUpperCase();
  const target = document.getElementById('watchTarget')?.value.trim();
  if (!symbol) {
    alert(t('fillSymbol'));
    return;
  }
  try {
    const res = await fetch(API.WATCHLIST, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF,
      },
      body: JSON.stringify({ symbol, target_price: target || null }),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Gagal menambahkan watchlist');
    document.getElementById('watchSymbol').value = '';
    document.getElementById('watchTarget').value = '';
    await loadWatchlist();
  } catch (e) {
    alert(t('addWatchlistFailed') + ' ' + (e.message || e));
  }
}

async function removeWatchlist(id) {
  if (!confirm(t('confirmDeleteWatch'))) return;
  try {
    const res = await fetch(API.WATCHLIST + '/' + id, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF,
      },
    });
    if (!res.ok) throw new Error('Gagal hapus watchlist');
    await loadWatchlist();
  } catch (e) {
    alert(t('deleteWatchFailed') + ' ' + (e.message || '')); 
  }
}

async function refreshWatchlist() {
  await loadWatchlist();
}

/* ==========================================================
   HISTORY
   ========================================================== */
function renderHistory() {
  const el = document.getElementById('tradeHistory');
  if (!el) return;
  if (tradeHistory.length > 0) {
    el.innerHTML = '<div class="history-head"><span>Type</span><span>Lot</span><span>Harga / Waktu</span></div>' +
      tradeHistory.map(h => {
        const typeClass = h.type === 'BUY' ? 'buy' : 'sell';
        return '<div class="history-row">' +
          '<div class="history-type ' + typeClass + ' fw-bold">' + h.type + '</div>' +
          '<div>' + h.lot + ' lot</div>' +
          '<div style="text-align:right;">Rp ' + Math.round(h.price).toLocaleString('id-ID') + '<br><span class="c-muted" style="font-size:9px;">' + h.time + '</span></div>' +
        '</div>';
      }).join('');
  } else {
    el.innerHTML = '<div class="text-center c-muted mt-3" style="font-size:11px;">' + t('noTransaction') + '</div>';
  }
}

/* ==========================================================
   TRADE
   ========================================================== */
async function trade(type) {
  const lot   = Number(document.getElementById('inputLot').value);
  const price = Math.round(currentPrice);
  const errEl = document.getElementById('tradeError');
  errEl.style.display = 'none';
  if (!lot || lot <= 0 || isNaN(lot)) { showError(t('invalidLot')); return; }
  try {
    const res = await fetch(API.TRADE, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ type, price, lot, stock: 'BBCA' })
    });
    const data = await res.json();
    if (!res.ok) { showError(data.message || t('tradeFailed')); return; }
    portfolio.balance   = data.balance;
    portfolio.lots      = data.lots;
    portfolio.avg_price = data.avg_price;
    updatePortfolioUI();
    await loadHistory();
  } catch(e) { showError(t('networkRetry')); }
}

function showError(msg) {
  const el = document.getElementById('tradeError');
  el.textContent = msg; el.style.display = 'block';
  setTimeout(() => el.style.display = 'none', 4000);
}

async function resetDemo() {
  if (!confirm(t('resetConfirm'))) return;
  try {
    const res = await fetch(API.RESET, { method:'POST', headers:{ 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
    if (res.ok) { portfolio = { balance: 100000000, lots: 0, avg_price: 0 }; tradeHistory = []; updatePortfolioUI(); renderHistory(); }
  } catch(e) { alert(t('resetFailed')); }
}

function exportCSV() {
  if (tradeHistory.length === 0) { alert(t('noHistory')); return; }
  let csv = 'Type,Lot,Price,Time\n';
  tradeHistory.forEach(r => { csv += r.type + ',' + r.lot + ',' + r.price + ',' + r.time + '\n'; });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
  a.download = 'history_saham_bbca.csv';
  document.body.appendChild(a); a.click(); document.body.removeChild(a);
}

/* ==========================================================
   NEWS
   ========================================================== */
async function initRealtimeNews() {
  const c = document.getElementById('tradingview-news-compact');
  if (!c) return;
  if (!c.innerHTML.trim()) c.innerHTML = '<div class="text-center mt-5 c-muted" style="font-size:11px;">Memuat berita...</div>';
  try {
    const rss = encodeURIComponent('https://www.cnbcindonesia.com/market/rss');
    const res = await fetch('https://api.rss2json.com/v1/api.json?rss_url=' + rss);
    const data = await res.json();
    if (data.status === 'ok' && data.items?.length > 0) {
      c.innerHTML = data.items.slice(0,8).map(item =>
        '<div class="news-item">' +
        '<div style="font-size:8px;color:var(--accent);font-weight:700;margin-bottom:2px;">' +
        new Date(item.pubDate).toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}) + ' WIB</div>' +
        '<a href="' + item.link + '" target="_blank" rel="noopener noreferrer" class="news-title-link">' + item.title + '</a></div>'
      ).join('');
    }
  } catch(e) { console.log('Gagal load berita:', e); }
}

/* ==========================================================
   TRADINGVIEW CHART
   ========================================================== */
function initTradingView() {
  const c = document.getElementById('tvchart');
  if (!c || typeof TradingView === 'undefined') return;
  c.innerHTML = '';
  new TradingView.widget({
    autosize: true, symbol: 'IDX:BBCA', interval: '1',
    timezone: 'Asia/Jakarta',
    theme: document.body.classList.contains('light-mode') ? 'light' : 'dark',
    style: '1', locale: 'id', enable_publishing: false,
    allow_symbol_change: true, container_id: 'tvchart',
    withdateranges: true, hide_side_toolbar: false,
    details: true, hotlist: true, calendar: true,
  });
}

/* ==========================================================
   TECHNICAL ANALYSIS
   ========================================================== */
const fmt = n => Math.round(n).toLocaleString('id-ID');

function genSimPrices(base, n) {
  const arr = []; let p = base;
  for (let i = 0; i < n; i++) { p += (Math.random() - 0.48) * 40; arr.push(Math.round(p)); }
  return arr;
}
function calcEMA(data, period) {
  const k = 2 / (period + 1); let ema = data[0];
  for (let i = 1; i < data.length; i++) ema = data[i] * k + ema * (1 - k);
  return ema;
}
function calcRSI(data, period = 14) {
  if (data.length < period + 1) return 50;
  let gains = 0, losses = 0;
  for (let i = data.length - period; i < data.length; i++) {
    const d = data[i] - data[i-1];
    if (d > 0) gains += d; else losses -= d;
  }
  if (losses === 0) return 100;
  const rs = (gains / period) / (losses / period);
  return 100 - 100 / (1 + rs);
}
function calcBB(data, period = 20) {
  if (data.length < period) return { upper: 0, lower: 0, mid: 0 };
  const sl = data.slice(-period);
  const mid = sl.reduce((a, b) => a + b, 0) / period;
  const std = Math.sqrt(sl.reduce((a, b) => a + (b - mid)**2, 0) / period);
  return { upper: mid + 2*std, lower: mid - 2*std, mid };
}
function calcMACD(data) {
  if (data.length < 26) return { macd:0, signal:0, hist:0 };
  const ema12 = calcEMA(data.slice(-26), 12);
  const ema26 = calcEMA(data.slice(-26), 26);
  const macd  = ema12 - ema26;
  const sig   = macd * 0.85;
  return { macd, signal: sig, hist: macd - sig };
}
function calcStoch(data, k = 14) {
  if (data.length < k) return 50;
  const sl = data.slice(-k);
  const high = Math.max(...sl), low = Math.min(...sl), cur = sl[sl.length-1];
  if (high === low) return 50;
  return ((cur - low) / (high - low)) * 100;
}

let lastTAData = null;

function runTA() {
  const prices = priceHistory.length >= 25 ? priceHistory : genSimPrices(currentPrice || 10250, 60);
  const cur    = prices[prices.length - 1];
  const pc     = previousClose || cur;
  const ma7    = prices.slice(-7).reduce((a,b)=>a+b,0) / 7;
  const ma25   = prices.slice(-25).reduce((a,b)=>a+b,0) / 25;
  const rsi    = calcRSI(prices);
  const bb     = calcBB(prices);
  const macd   = calcMACD(prices);
  const stoch  = calcStoch(prices);
  const chg    = cur - pc;
  const chgPct = pc ? (chg / pc) * 100 : 0;

  document.getElementById('taPrice').textContent = 'Rp ' + fmt(cur);
  const chgClr = chg >= 0 ? 'c-green' : 'c-red';
  const chgSign = chg >= 0 ? '+' : '';
  document.getElementById('taChange').innerHTML =
    '<span class="' + chgClr + '">' + chgSign + fmt(chg) + ' (' + chgSign + chgPct.toFixed(2) + '%)</span>';

  const maBull = ma7 > ma25;
  document.getElementById('taMACross').innerHTML =
    '<span class="' + (maBull ? 'c-green' : 'c-red') + '">' + Math.round(ma7).toLocaleString('id-ID') + '</span>';
  document.getElementById('taMADesc').textContent = maBull ? t('bullishCross') : t('bearishCross');
  document.getElementById('taMADesc').className   = 'ta-sub ' + (maBull ? 'c-green' : 'c-red');

  document.getElementById('taRSI').innerHTML =
    '<span class="' + (rsi>70?'c-red':rsi<30?'c-green':'') + '">' + rsi.toFixed(1) + '</span>';
  document.getElementById('taRSIDesc').textContent = rsi>70 ? t('overbought') : rsi<30 ? t('oversold') : t('neutral');
  document.getElementById('taRSIDesc').className   = 'ta-sub ' + (rsi>70?'c-red':rsi<30?'c-green':'');

  const volScore = Math.round(45 + Math.random() * 55);
  document.getElementById('taVol').innerHTML =
    '<span class="' + (volScore>70?'c-green':volScore>40?'':'c-red') + '">' + volScore + '/100</span>';
  document.getElementById('taVolDesc').textContent = volScore>70 ? t('highVolume') : volScore>40 ? t('normalVolume') : t('lowVolume');
  document.getElementById('taVolDesc').className   = 'ta-sub ' + (volScore>70?'c-green':volScore>40?'c-muted':'c-red');

  const bbPct = bb.upper - bb.lower > 0 ? ((cur - bb.lower) / (bb.upper - bb.lower)) * 100 : 50;
  document.getElementById('indBB').textContent    = t('position') + ' ' + bbPct.toFixed(0) + '%';
  document.getElementById('indBBSig').textContent = bbPct>80?t('nearUpper'):bbPct<20?t('nearLower'):t('middle');
  document.getElementById('indBBSig').className   = 'ind-sig ' + (bbPct>80?'c-red':bbPct<20?'c-green':'c-muted');

  document.getElementById('indMACD').textContent    = macd.hist.toFixed(0);
  document.getElementById('indMACDSig').textContent = macd.hist>0?'Bullish':'Bearish';
  document.getElementById('indMACDSig').className   = 'ind-sig ' + (macd.hist>0?'c-green':'c-red');

  document.getElementById('indStoch').textContent    = stoch.toFixed(1);
  document.getElementById('indStochSig').textContent = stoch>80?t('overbought'):stoch<20?t('oversold'):t('neutral');
  document.getElementById('indStochSig').className   = 'ind-sig ' + (stoch>80?'c-red':stoch<20?'c-green':'c-muted');

  const adx = 20 + Math.random() * 40;
  document.getElementById('indTrend').textContent    = adx.toFixed(0);
  document.getElementById('indTrendSig').textContent = adx>40?t('strongTrend'):adx>25?t('mediumTrend'):t('ranging');
  document.getElementById('indTrendSig').className   = 'ind-sig ' + (adx>40?'c-cyan':adx>25?'':'c-muted');

  const support = Math.round(cur * 0.972);
  const resist  = Math.round(cur * 1.028);
  document.getElementById('indSR').textContent    = fmt(support) + '/' + fmt(resist);
  document.getElementById('indSRSig').textContent = 'S/R Dynamic';
  document.getElementById('indSRSig').className   = 'ind-sig c-muted';

  const pred1h    = Math.round(cur + (maBull?1:-1) * cur * 0.003 + (Math.random()-.5)*cur*0.002);
  const pred1hChg = pred1h - cur;
  document.getElementById('indPred1h').textContent    = 'Rp ' + fmt(pred1h);
  document.getElementById('indPred1hSig').textContent = (pred1hChg>=0?'▲ +':'▼ ') + fmt(pred1hChg);
  document.getElementById('indPred1hSig').className   = 'ind-sig ' + (pred1hChg>=0?'c-green':'c-red');

  document.getElementById('tSupport').textContent = 'Rp ' + fmt(support);
  document.getElementById('tBuy').textContent     = 'Rp ' + fmt(Math.round(cur * 0.99));
  document.getElementById('tSell').textContent    = 'Rp ' + fmt(Math.round(cur * 1.01));
  document.getElementById('tResist').textContent  = 'Rp ' + fmt(resist);

  let bull = 0;
  if (maBull) bull++;
  if (rsi > 40 && rsi < 70) bull++;
  if (macd.hist > 0) bull++;
  if (stoch < 80) bull++;
  if (bbPct < 80) bull++;

  const bullPct = Math.round((bull / 5) * 100);
  const signal  = bullPct >= 60 ? 'BUY' : (bullPct <= 35 ? 'SELL' : 'HOLD');

  const sigCfg = {
    BUY:  { icon:'📈', clr:'#10b981', desc:'Sinyal beli kuat — mayoritas indikator bullish',    bg:'rgba(16,185,129,0.08)',  bd:'rgba(16,185,129,0.25)' },
    SELL: { icon:'📉', clr:'#f43f5e', desc:'Sinyal jual kuat — mayoritas indikator bearish',    bg:'rgba(244,63,94,0.08)',   bd:'rgba(244,63,94,0.25)' },
    HOLD: { icon:'⚖️', clr:'#f59e0b', desc:'Indikator campuran — disarankan tunggu konfirmasi', bg:'rgba(245,158,11,0.07)', bd:'rgba(245,158,11,0.25)' },
  };
  const sc = sigCfg[signal];

  const mainSigBar = document.getElementById('mainSignalBar');
  if (mainSigBar) {
    mainSigBar.style.background  = sc.bg;
    mainSigBar.style.borderColor = sc.bd;
  }
  if (document.getElementById('signalIcon')) document.getElementById('signalIcon').textContent = sc.icon;
  if (document.getElementById('signalMain')) {
    document.getElementById('signalMain').textContent = signal + ' BBCA';
    document.getElementById('signalMain').style.color = sc.clr;
  }
  if (document.getElementById('signalDesc')) document.getElementById('signalDesc').textContent = sc.desc;
  if (document.getElementById('signalConf')) {
    document.getElementById('signalConf').textContent = bullPct + '%';
    document.getElementById('signalConf').style.color = sc.clr;
  }

  const locale = currentLang === 'en' ? 'en-US' : 'id-ID';
  document.getElementById('updateTime').textContent = t('updateLabel') + ': ' + new Date().toLocaleTimeString(locale) + (currentLang === 'en' ? '' : ' WIB');

  lastTAData = { cur, ma7, ma25, rsi, bb, macd, stoch, bullPct, signal, chgPct, support, resist, pred1h, adx };
  return lastTAData;
}

/* ==========================================================
   GEMINI AI ANALYSIS
   ========================================================== */
async function askGemini() {
  const btn = document.getElementById('btnAskAI');
  const res = document.getElementById('aiResult');
  btn.disabled = true;
  btn.textContent = t('aiAnalyzingBtn');
  res.className = 'ai-result loading';
  res.textContent = ' ' + t('aiAnalyzingText');

  const ta = lastTAData || runTA();

  const prompt = `Kamu adalah analis saham profesional Indonesia. Analisis singkat saham BBCA berdasarkan data teknikal berikut:

- Harga saat ini: Rp ${fmt(ta.cur)}
- Perubahan hari ini: ${ta.chgPct.toFixed(2)}%
- RSI (14): ${ta.rsi.toFixed(1)} (${ta.rsi > 70 ? 'Overbought' : ta.rsi < 30 ? 'Oversold' : 'Netral'})
- MA7: ${fmt(ta.ma7)}, MA25: ${fmt(ta.ma25)} → ${ta.ma7 > ta.ma25 ? 'Bullish cross' : 'Bearish cross'}
- MACD Histogram: ${ta.macd.hist.toFixed(0)} (${ta.macd.hist > 0 ? 'Bullish' : 'Bearish'})
- Stochastic: ${ta.stoch.toFixed(1)} (${ta.stoch > 80 ? 'Overbought' : ta.stoch < 20 ? 'Oversold' : 'Netral'})
- Bollinger Band Position: ${((ta.cur - ta.bb.lower) / (ta.bb.upper - ta.bb.lower) * 100).toFixed(0)}%
- ADX (Trend Strength): ${ta.adx ? ta.adx.toFixed(0) : 'N/A'}
- Sinyal TA keseluruhan: ${ta.signal} (confidence ${ta.bullPct}%)
- Support: Rp ${fmt(ta.support)}, Resistance: Rp ${fmt(ta.resist)}
- Prediksi 1 jam: Rp ${fmt(ta.pred1h)}

Berikan analisis dalam Bahasa Indonesia, format 3 paragraf singkat: (1) kondisi teknikal sekarang, (2) outlook jangka pendek, (3) rekomendasi konkret dan level kunci yang perlu diperhatikan. Maksimal 130 kata total. Jangan pakai poin atau bullet. Tulis seperti analis profesional.`;

  try {
    const response = await fetch('/api/ai/analyze', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ prompt, ta })
    });

    const data = await response.json();

    const text = data?.candidates?.[0]?.content?.parts?.[0]?.text 
    || (data?.recommendation ? `${data.recommendation} — ${data.reason} (Target: ${data.prediction})` : '');

    if (text) {
      res.className = 'ai-result';
      const paras = text.trim().split('\n').filter(p => p.trim());
      res.innerHTML = paras.map(p => '<div class="ai-para">' + p + '</div>').join('');
    } else {
      res.className = 'ai-result';
      res.textContent = t('aiNoResponse');
    }
  } catch(e) {
    res.className = 'ai-result';
    res.innerHTML = '<span style="color:#f43f5e">' + t('aiConnectionFail') + '</span>';
  }

  btn.disabled = false;
  btn.textContent = t('aiAnalyzeAgain');
}

/* ==========================================================
   DISKUSI KOMUNITAS
   ========================================================== */
function toggleDiscussionForm() {
  const f = document.getElementById('discussionForm');
  f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

async function loadDiscussions() {
  try {
    const res = await fetch(API.DISCUSSIONS + '?symbol=BBCA', {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    renderDiscussions(data.discussions || []);
  } catch(e) {
    document.getElementById('discussionList').innerHTML =
      '<div class="text-center c-muted" style="font-size:11px;">' + t('discussionLoadFailed') + '</div>';
  }
}

let allDiscussions = [];
let currentDiscFilter = 'ALL';

function filterDiscussions(sentiment, btn) {
  currentDiscFilter = sentiment;
  document.querySelectorAll('.btn-tab-disc').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  renderDiscussions(allDiscussions);
}

function renderDiscussions(list) {
  allDiscussions = list;
  const el = document.getElementById('discussionList');
  let filtered = list;
  if (currentDiscFilter !== 'ALL') {
    filtered = list.filter(d => (d.sentiment || 'BULLISH') === currentDiscFilter);
  }

  if (!filtered.length) {
    el.innerHTML = '<div class="text-center c-muted mt-4" style="font-size:11px;">Tidak ada postingan dalam kategori ini.</div>';
    return;
  }

  el.innerHTML = filtered.map(d => {
    const userObj = d.user || {};
    const name = userObj.name || 'Anon Investor';
    const role = userObj.role || 'Trader';
    const avatar = userObj.avatar || (name.substring(0,2).toUpperCase());
    const sent = (d.sentiment || 'BULLISH').toUpperCase();
    const sentBadge = sent === 'BULLISH'
      ? '<span class="disc-badge-bull">📈 BULLISH</span>'
      : (sent === 'BEARISH' ? '<span class="disc-badge-bear">📉 BEARISH</span>' : '<span class="disc-badge-neu">⚖️ NEUTRAL</span>');

    const commentsHtml = (d.comments || []).map(c => `
      <div style="font-size:11px;padding:6px 0;border-bottom:1px solid rgba(255,255,255,0.04);display:flex;gap:8px;">
        <div class="disc-avatar" style="width:24px;height:24px;font-size:9px;">${(c.user?.name || 'US').substring(0,2).toUpperCase()}</div>
        <div style="flex:1;">
          <div class="d-flex justify-content-between">
            <strong style="color:var(--accent);font-size:10px;">${c.user?.name ?? 'Anon'}</strong>
            <span class="c-muted" style="font-size:9px;">${timeAgo(c.created_at)}</span>
          </div>
          <div style="color:var(--text2);margin-top:2px;">${c.body}</div>
        </div>
      </div>
    `).join('');

    return `
      <div class="news-item" id="disc-${d.id}" style="border:1px solid var(--border);border-radius:10px;padding:12px;margin-bottom:10px;background:rgba(255,255,255,0.015);">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="d-flex align-items-center gap-2">
            <div class="disc-avatar">${avatar}</div>
            <div>
              <div style="font-size:11px;font-weight:700;color:var(--text);">${name} <span class="c-muted fw-normal" style="font-size:9px;">· ${role}</span></div>
              <div class="c-muted" style="font-size:9px;">${timeAgo(d.created_at)}</div>
            </div>
          </div>
          ${sentBadge}
        </div>

        <div style="font-size:12px;font-weight:700;color:var(--text);margin-bottom:4px;">${d.title}</div>
        <div style="font-size:11px;color:var(--text2);line-height:1.5;margin-bottom:10px;">${d.body}</div>

        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top:1px solid rgba(255,255,255,0.04);">
          <div class="d-flex gap-3 align-items-center">
            <button onclick="likeDiscussion(${d.id})" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:11px;">
              ❤️ <span id="likes-${d.id}">${d.likes || 0}</span> Suka
            </button>
            <button onclick="toggleComments(${d.id})" style="background:none;border:none;cursor:pointer;color:var(--accent);font-size:11px;">
              💬 <span id="ccount-${d.id}">${d.comments?.length ?? 0}</span> Komentar
            </button>
          </div>
        </div>

        <div id="comments-${d.id}" style="display:none;margin-top:10px;padding-top:10px;border-top:1px solid var(--border);">
          <div id="comment-list-${d.id}">${commentsHtml || '<div class="c-muted" style="font-size:10px;padding:4px 0;">Belum ada komentar. Tulis komentar pertama!</div>'}</div>
          <div class="d-flex gap-2 mt-2">
            <input type="text" id="comment-input-${d.id}" class="form-control-custom"
              placeholder="${t('commentPlaceholder')}" style="flex:1;padding:.35rem .7rem;font-size:11px;">
            <button class="btn-ai" style="padding:5px 12px;font-size:10px;"
              onclick="postComment(${d.id})">${t('send')}</button>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

function toggleComments(id) {
  const el = document.getElementById('comments-' + id);
  el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

async function postDiscussion() {
  const title = document.getElementById('discTitle').value.trim();
  const body  = document.getElementById('discBody').value.trim();
  const sentiment = document.getElementById('discSentiment')?.value || 'BULLISH';

  if (!title || !body) { alert(t('discussionRequired')); return; }
  try {
    const res = await fetch(API.DISCUSSIONS, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ title, body, sentiment, stock_symbol: 'BBCA' })
    });
    if (!res.ok) throw new Error();
    document.getElementById('discTitle').value = '';
    document.getElementById('discBody').value  = '';
    document.getElementById('discussionForm').style.display = 'none';
    await loadDiscussions();
  } catch(e) { alert(t('discussionPostFailed')); }
}

async function postComment(discussionId) {
  const input = document.getElementById('comment-input-' + discussionId);
  const body  = input.value.trim();
  if (!body) return;
  try {
    const res = await fetch(API.DISCUSSIONS + '/' + discussionId + '/comments', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ body })
    });
    if (!res.ok) throw new Error();
    input.value = '';
    await loadDiscussions();
  } catch(e) { alert(t('commentPostFailed')); }
}

async function likeDiscussion(id) {
  try {
    const res = await fetch(API.DISCUSSIONS + '/' + id + '/like', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    const data = await res.json();
    document.getElementById('likes-' + id).textContent = data.likes;
  } catch(e) {}
}
      '<div class="text-center c-muted" style="font-size:11px;">' + t('discussionLoadFailed') + '</div>';
/* ==========================================================
   KALENDER EVENT EMITEN
   ========================================================== */
let currentEventMonth = new Date();

async function loadEmitenEvents(offset = 0) {
  if (offset !== 0) {
    currentEventMonth.setMonth(currentEventMonth.getMonth() + offset);
  }

  const year = currentEventMonth.getFullYear();
  const monthStr = String(currentEventMonth.getMonth() + 1).padStart(2, '0');
  const monthReq = `${year}-${monthStr}`;

  const locale = currentLang === 'en' ? 'en-US' : 'id-ID';
  const label = currentEventMonth.toLocaleString(locale, { month: 'long', year: 'numeric' });

  const labelEl = document.getElementById('eventMonthLabel');
  if (labelEl) {
    labelEl.className = 'c-muted d-flex align-items-center gap-1';
    labelEl.innerHTML =
      `<button class="btn-ghost p-1" style="border:none;font-size:9px;border-radius:4px;" onclick="loadEmitenEvents(-1)"><i class="fa-solid fa-chevron-left"></i></button>` +
      `<span style="display:inline-block;min-width:90px;text-align:center;font-weight:700;">${label}</span>` +
      `<button class="btn-ghost p-1" style="border:none;font-size:9px;border-radius:4px;" onclick="loadEmitenEvents(1)"><i class="fa-solid fa-chevron-right"></i></button>`;
  }

  const calendarEl = document.getElementById('eventCalendar');
  if (calendarEl) {
    calendarEl.innerHTML = '<div class="text-center c-muted mt-3" style="font-size:11px;">' + t('eventLoadingText') + '</div>';
  }

    try {
    const res = await fetch(API.EVENTS + '?symbol=BBCA&month=' + monthReq, {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
      credentials: 'same-origin'
    });
    const data = await res.json();
    const eventsList = Array.isArray(data) ? data : (data.events || []);
    renderEmitenEvents(eventsList);
  } catch (e) {
    if (calendarEl) {
      calendarEl.innerHTML = '<div class="text-center c-muted mt-3" style="font-size:11px;">' + t('eventLoadFailed') + '</div>';
    }
  }
}

function renderEmitenEvents(events) {
  const el = document.getElementById('eventCalendar');
  if (!el) return;

  if (!events || !events.length) {
    el.innerHTML = '<div class="text-center c-muted mt-3" style="font-size:11px;">' + t('eventEmpty') + '</div>';
    return;
  }

  const typeColor = {
    dividen: 'c-green',
    rups:    'c-cyan',
    laporan: 'c-muted',
    lainnya: '',
  };
  const typeIcon = {
    dividen: '💰',
    rups:    '🏛️',
    laporan: '📋',
    lainnya: '📌',
  };

  const locale = currentLang === 'en' ? 'en-US' : 'id-ID';

  el.innerHTML = events.map(ev => {
    const dateStr = ev.event_date ? String(ev.event_date).split('T')[0].split(' ')[0] : '';
    const d = dateStr ? new Date(dateStr + 'T00:00:00') : new Date();
    const tgl = d.toLocaleDateString(locale, { day: '2-digit', month: 'short' });
    const evtType = (ev.type || 'lainnya').toLowerCase();
    const clr = typeColor[evtType] || '';
    const ico = typeIcon[evtType] || '📌';
    return `
      <div class="news-item d-flex align-items-start gap-2">
        <div class="event-date-badge">
          ${tgl.replace(' ', '<br>')}
        </div>
        <div>
          <div style="font-size:11px;font-weight:700;">${ico} ${ev.title}</div>
          <div style="font-size:10px;" class="${clr}">${evtType.toUpperCase()}${ev.value ? ' · Rp ' + Number(ev.value).toLocaleString('id-ID') : ''}</div>
          ${ev.description ? `<div style="font-size:10px;color:var(--muted);margin-top:2px;">${ev.description}</div>` : ''}
        </div>
      </div>
    `;
  }).join('');
}
/* Helper: waktu relatif */
function timeAgo(dateStr) {
  const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
  if (diff < 60)   return diff + t('timeSecondAgo');
  if (diff < 3600) return Math.floor(diff / 60) + t('timeMinuteAgo');
  if (diff < 86400) return Math.floor(diff / 3600) + t('timeHourAgo');
  return Math.floor(diff / 86400) + t('timeDayAgo');
}

/* ==========================================================
   THEME + MODAL
   ========================================================== */
document.getElementById('btnTheme').addEventListener('click', function () {
  document.body.classList.toggle('light-mode');
  const icon = this.querySelector('i');
  icon.classList.toggle('fa-moon');
  icon.classList.toggle('fa-sun');
  initTradingView();
});

document.getElementById('btnLang').addEventListener('click', function () {
  toggleLanguage();
});

/* ==========================================================
   REALTIME CLOCK
   ========================================================== */
function updateRealtimeClock() {
  const now = new Date();
  
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const seconds = String(now.getSeconds()).padStart(2, '0');
  const timeStr = `${hours}:${minutes}:${seconds}`;
  
  const days = currentLang === 'en'
    ? ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
    : ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  const months = currentLang === 'en'
    ? ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
    : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  const dayName = days[now.getDay()];
  const date = now.getDate();
  const month = months[now.getMonth()];
  const year = now.getFullYear();
  const dateStr = `${dayName}, ${date} ${month} ${year}`;
  
  document.getElementById('realtime-time').textContent = timeStr;
  document.getElementById('realtime-date').textContent = dateStr;
}

updateRealtimeClock();
setInterval(updateRealtimeClock, 1000);

/* ==========================================================
   RISK / REWARD CALCULATOR & LEADERBOARD & PUSH ALERT
   ========================================================== */
function updateRRCalc() {
  const curPrice = parseFloat(document.getElementById('inputPrice')?.value || currentPrice || 10250);
  const sl = parseFloat(document.getElementById('calcSL')?.value || 10000);
  const tp = parseFloat(document.getElementById('calcTP')?.value || 10750);
  const lot = parseFloat(document.getElementById('inputLot')?.value || 1);

  const risk = Math.max(1, curPrice - sl);
  const reward = Math.max(1, tp - curPrice);
  const ratio = (reward / risk).toFixed(2);

  const profitTotal = (tp - curPrice) * 100 * lot;
  const lossTotal = (curPrice - sl) * 100 * lot;

  document.getElementById('rrRatio').textContent = '1 : ' + ratio;
  document.getElementById('rrProfit').textContent = '+' + 'Rp ' + fmt(Math.round(profitTotal)) + ' (' + lot + ' lot)';
  document.getElementById('rrLoss').textContent = '-' + 'Rp ' + fmt(Math.round(lossTotal)) + ' (' + lot + ' lot)';
}

async function loadLeaderboard() {
  try {
    const res = await fetch('/api/trade/leaderboard', { headers: { 'Accept': 'application/json' } });
    const data = await res.json();
    const list = data.leaderboard || [];

    const el = document.getElementById('leaderboardList');
    if (!el) return;

    el.innerHTML = list.map(t => `
      <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid rgba(255,255,255,0.04);">
        <div class="d-flex align-items-center gap-2">
          <div class="disc-avatar" style="width:26px;height:26px;font-size:10px;">${t.avatar}</div>
          <div>
            <div style="font-weight:700;font-size:11px;">${t.name} <span style="font-size:9px;" class="c-muted">${t.badge}</span></div>
            <div class="c-muted" style="font-size:9px;">${t.role}</div>
          </div>
        </div>
        <div class="text-end">
          <div class="c-green" style="font-weight:700;">+${t.roi.toFixed(1)}%</div>
          <div class="c-muted" style="font-size:9px;">Rp ${fmt(t.balance)}</div>
        </div>
      </div>
    `).join('');
  } catch(e) {}
}

async function loadValuation() {
  try {
    const res = await fetch('/api/market/valuation?symbol=BBCA', { headers: { 'Accept': 'application/json' } });
    const data = await res.json();

    if (data.dcf_fair_value) {
      document.getElementById('dcfFairVal').textContent = 'Rp ' + fmt(data.dcf_fair_value);
      document.getElementById('ddmFairVal').textContent = 'Rp ' + fmt(data.ddm_fair_value);
      document.getElementById('mosVal').textContent = '+' + data.margin_of_safety + '%';
      document.getElementById('valStatusBadge').textContent = '🟢 ' + data.status;
    }
  } catch(e) {}
}

function requestNotificationPermission() {
  if (!("Notification" in window)) {
    alert("Browser Anda belum mendukung fitur Web Push Notification.");
    return;
  }
  Notification.requestPermission().then(permission => {
    if (permission === "granted") {
      alert("✅ Web Push Alert Berhasil Diaktifkan! Notifikasi sinyal & target harga akan muncul langsung di browser Anda.");
      new Notification("BBCA Trading Alert Enabled", {
        body: "Sistem notifikasi aktif! Anda akan menerima update target harga & sinyal AI real-time.",
        icon: "/favicon.ico"
      });
    } else {
      alert("Izin notifikasi tidak diberikan.");
    }
  });
}

/* ==========================================================
   INIT
   ========================================================== */
document.addEventListener('DOMContentLoaded', function () {
  applyLanguage();
  updatePortfolioUI();
  const finModal = new bootstrap.Modal(document.getElementById('finModal'));
  document.getElementById('btnLaporan').addEventListener('click', () => finModal.show());

  initTradingView();
  initRealtimeNews();
  loadPortfolio();
  loadHistory();
  loadWatchlist();
  loadFundamentals();
  loadPerformance();
  updatePrice();

  // *** BARU: load diskusi, event, leaderboard, & valuation ***
  loadDiscussions();
  loadEmitenEvents();
  loadLeaderboard();
  loadValuation();
  updateRRCalc();

  document.getElementById('inputSymbol')?.addEventListener('change', () => {
    loadFundamentals();
  });

  setInterval(updatePrice, 5000);
  setInterval(loadPortfolio, 15000);
  setInterval(initRealtimeNews, 120000);
  setInterval(loadWatchlist, 30000);
  setInterval(loadPerformance, 30000);
  setInterval(genOrders, 3000);
  setInterval(loadHistory, 15000);

  // *** BARU: interval refresh diskusi & event ***
  setInterval(loadDiscussions, 30000);
  setInterval(loadEmitenEvents, 60000);
  setInterval(loadLeaderboard, 60000);
});
</script>
</body>
</html>