<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>AMELIA — Admin Panel</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(() => {
  try {
    const savedTheme = localStorage.getItem('admin-theme');
    if (savedTheme === 'light' || savedTheme === 'dark') {
      document.documentElement.setAttribute('data-theme', savedTheme);
    }
  } catch (_) {}
})();
</script>

<style>
:root {
  --bg:#0a0f1a;--bg2:#0f1624;--bg3:#151e2d;--border:rgba(255,255,255,0.06);
  --accent:#06b6d4;--accent-glow:rgba(6,182,212,0.12);--green:#10b981;
  --red:#ef4444;--amber:#f59e0b;--muted:#6b7b8d;--text:#e2e8f0;--sidebar-w:240px;
  --topbar-bg:rgba(10,15,26,0.85);--row-hover:rgba(255,255,255,0.02);--progress-track:rgba(255,255,255,0.05);
}
:root[data-theme='light'] {
  --bg:#f3f6fb;--bg2:#ffffff;--bg3:#edf2f8;--border:rgba(15,23,42,0.12);
  --accent:#0284c7;--accent-glow:rgba(2,132,199,0.14);--green:#059669;
  --red:#dc2626;--amber:#d97706;--muted:#64748b;--text:#0f172a;
  --topbar-bg:rgba(255,255,255,0.82);--row-hover:rgba(15,23,42,0.04);--progress-track:rgba(15,23,42,0.08);
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Space Grotesk',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden;transition:background .2s,color .2s;}
#sidebar{position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;z-index:1000;transition:transform .3s;}
.sidebar-logo{padding:22px 20px 18px;border-bottom:1px solid var(--border);}
.logo-badge{display:flex;align-items:center;gap:10px;}
.logo-icon{width:36px;height:36px;background:linear-gradient(135deg,var(--accent),#0284c7);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;color:#fff;}
.logo-text{font-size:1.1rem;font-weight:800;letter-spacing:2px;}
.logo-sub{font-size:.65rem;color:var(--muted);letter-spacing:1px;margin-top:1px;}
.sidebar-nav{flex:1;padding:12px 10px;overflow-y:auto;}
.nav-section-label{font-size:.6rem;font-weight:700;letter-spacing:2px;color:var(--muted);padding:14px 10px 6px;text-transform:uppercase;}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;cursor:pointer;color:var(--muted);font-size:.85rem;font-weight:500;transition:all .2s;margin-bottom:2px;border:none;background:none;width:100%;text-align:left;text-decoration:none;}
.nav-item:hover{background:rgba(255,255,255,0.04);color:var(--text);}
.nav-item.active{background:var(--accent-glow);color:var(--accent);font-weight:600;}
.nav-item .nav-icon{width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:.85rem;background:rgba(255,255,255,0.04);flex-shrink:0;}
.nav-item.active .nav-icon{background:var(--accent-glow);color:var(--accent);}
.sidebar-footer{padding:16px;border-top:1px solid var(--border);}
.admin-badge{display:flex;align-items:center;gap:10px;}
.avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:999;}
.sidebar-overlay.show{display:block;}
@media(max-width:992px){#sidebar{transform:translateX(-100%);}#sidebar.open{transform:translateX(0);}#mainContent{margin-left:0!important;}}
#mainContent{margin-left:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column;transition:margin-left .3s;}
.topbar{position:sticky;top:0;background:var(--topbar-bg);backdrop-filter:blur(16px);border-bottom:1px solid var(--border);padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between;z-index:900;}
.topbar-left{display:flex;align-items:center;gap:14px;}
.topbar-title{font-size:1rem;font-weight:700;}
.hamburger-btn{background:none;border:none;color:var(--muted);font-size:1.1rem;cursor:pointer;padding:6px;display:none;}
@media(max-width:992px){.hamburger-btn{display:block;}}
.topbar-right{display:flex;align-items:center;gap:12px;}
.theme-toggle-btn{height:34px;display:inline-flex;align-items:center;gap:8px;padding:0 12px;border-radius:9px;border:1px solid var(--border);background:var(--bg2);color:var(--muted);font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;}
.theme-toggle-btn:hover{color:var(--accent);border-color:var(--accent);}
.clock-badge{font-family:'JetBrains Mono',monospace;font-size:.72rem;color:var(--muted);background:var(--bg3);padding:6px 12px;border-radius:8px;border:1px solid var(--border);}
.live-dot{display:flex;align-items:center;gap:6px;font-size:.72rem;color:var(--green);font-weight:600;}
.live-dot::before{content:'';width:7px;height:7px;border-radius:50%;background:var(--green);animation:pulse 1.5s infinite;}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(1.3);}}
.content-area{flex:1;padding:24px;}
.page-section{display:none;}
.page-section.active{display:block;}
.card-glass{background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:20px;}
.card-title-sm{font-size:.75rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:16px;}
.stat-card{background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:20px;position:relative;overflow:hidden;transition:transform .2s,border-color .2s;}
.stat-card:hover{transform:translateY(-2px);border-color:rgba(6,182,212,0.2);}
.stat-glow{position:absolute;top:-30px;right:-30px;width:100px;height:100px;border-radius:50%;opacity:.06;filter:blur(20px);}
.stat-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;font-size:.95rem;}
.stat-value{font-size:1.6rem;font-weight:800;line-height:1;margin-bottom:4px;}
.stat-label{font-size:.72rem;color:var(--muted);font-weight:500;text-transform:uppercase;letter-spacing:.5px;}
.stat-trend{font-size:.72rem;margin-top:8px;font-weight:500;}
.table-custom{width:100%;border-collapse:collapse;font-size:.83rem;}
.table-custom th{padding:10px 14px;font-size:.68rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);background:var(--bg);position:sticky;top:0;white-space:nowrap;}
.table-custom td{padding:12px 14px;border-bottom:1px solid var(--border);vertical-align:middle;}
.table-custom tr:hover td{background:var(--row-hover);}
.table-wrap{overflow-x:auto;border-radius:12px;border:1px solid var(--border);}
.badge-status{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;letter-spacing:.5px;}
.badge-active{background:rgba(16,185,129,.12);color:var(--green);}
.badge-suspended{background:rgba(239,68,68,.12);color:var(--red);}
.badge-inactive{background:rgba(107,123,141,.12);color:var(--muted);}
.btn-accent{background:linear-gradient(135deg,var(--accent),#0284c7);color:#fff;border:none;padding:8px 18px;border-radius:10px;font-size:.8rem;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:7px;}
.btn-accent:hover{transform:translateY(-1px);}
.btn-outline{background:none;color:var(--muted);border:1px solid var(--border);padding:8px 16px;border-radius:10px;font-size:.8rem;font-weight:500;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:7px;}
.btn-outline:hover{border-color:var(--accent);color:var(--accent);}
.btn-sm-icon{width:30px;height:30px;border-radius:8px;border:1px solid var(--border);background:var(--bg3);color:var(--muted);display:flex;align-items:center;justify-content:center;font-size:.75rem;cursor:pointer;transition:all .15s;}
.btn-sm-icon:hover{border-color:var(--accent);color:var(--accent);}
.input-custom{background:var(--bg3);border:1px solid var(--border);color:var(--text);border-radius:10px;padding:8px 14px;font-size:.82rem;font-family:inherit;transition:border-color .2s;width:100%;outline:none;}
.input-custom:focus{border-color:var(--accent);}
.input-custom::placeholder{color:var(--muted);}
.input-custom option{background:var(--bg2);}
.toggle-wrap{display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid var(--border);}
.toggle-wrap:last-child{border-bottom:none;}
.toggle-info .toggle-label{font-size:.87rem;font-weight:600;}
.toggle-info .toggle-desc{font-size:.75rem;color:var(--muted);margin-top:2px;}
.switch{width:44px;height:24px;background:var(--bg3);border-radius:12px;border:1px solid var(--border);cursor:pointer;position:relative;transition:background .2s;flex-shrink:0;}
.switch::after{content:'';position:absolute;width:18px;height:18px;border-radius:50%;background:var(--muted);top:2px;left:2px;transition:all .2s;}
.switch.on{background:var(--accent-glow);border-color:var(--accent);}
.switch.on::after{left:22px;background:var(--accent);}
#toastContainer{position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;}
.toast-custom{display:flex;align-items:center;gap:10px;background:var(--bg2);border:1px solid var(--border);padding:12px 18px;border-radius:12px;font-size:.83rem;font-weight:500;box-shadow:0 8px 32px rgba(0,0,0,0.4);animation:slideIn .3s ease;min-width:220px;max-width:340px;}
@keyframes slideIn{from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);}}
.modal-content{background:var(--bg2)!important;border:1px solid var(--border)!important;border-radius:16px!important;color:var(--text)!important;}
.modal-header{border-bottom:1px solid var(--border)!important;padding:18px 22px!important;}
.modal-title{font-size:.95rem;font-weight:700;}
.modal-footer{border-top:1px solid var(--border)!important;padding:14px 22px!important;}
:root[data-theme='dark'] .btn-close{filter:invert(1)!important;}
.section-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;}
.section-title{font-size:1.05rem;font-weight:700;}
.chart-container{position:relative;height:220px;}
.error-banner{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:12px;padding:14px 18px;font-size:.82rem;color:var(--red);display:none;margin-bottom:16px;}
@media(max-width:576px){.content-area{padding:16px;}.clock-badge{display:none;}}
</style>
</head>
<body>

<div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar(false)"></div>

<div id="sidebar">
  <div class="sidebar-logo">
    <div class="logo-badge">
      <div class="logo-icon"><i class="fa-solid fa-chart-line"></i></div>
      <div><div class="logo-text">AMELIA</div><div class="logo-sub">ADMIN PANEL</div></div>
    </div>
  </div>
  <div class="sidebar-nav">
    <div class="nav-section-label" id="navSectionMain">Menu Utama</div>
    <button class="nav-item active" onclick="showPage('page-dashboard', this)">
      <span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span><span id="navDashboardText">Dashboard</span>
    </button>
    <button class="nav-item" onclick="showPage('page-users', this)">
      <span class="nav-icon"><i class="fa-solid fa-users"></i></span><span id="navUsersText">Kelola User</span>
    </button>
    <button class="nav-item" onclick="showPage('page-transactions', this)">
      <span class="nav-icon"><i class="fa-solid fa-arrow-right-arrow-left"></i></span><span id="navTransactionsText">Transaksi</span>
    </button>
    <button class="nav-item" onclick="showPage('page-reports', this)">
      <span class="nav-icon"><i class="fa-solid fa-chart-bar"></i></span><span id="navReportsText">Laporan</span>
    </button>
    <div class="nav-section-label" id="navSectionSystem">Sistem</div>
    <button class="nav-item" onclick="showPage('page-announcement', this)">
      <span class="nav-icon"><i class="fa-solid fa-bullhorn"></i></span><span>Broadcast Banner</span>
    </button>
    <button class="nav-item" onclick="showPage('page-audit-log', this)">
      <span class="nav-icon"><i class="fa-solid fa-shield-halved"></i></span><span>Audit Log</span>
    </button>
    <button class="nav-item" onclick="showPage('page-settings', this)">
      <span class="nav-icon"><i class="fa-solid fa-gear"></i></span><span id="navSettingsText">Pengaturan</span>
    </button>
    <div class="nav-section-label" id="navSectionAccess">Akses</div>
    <a href="{{ route('dashboard') }}" class="nav-item">
      <span class="nav-icon"><i class="fa-solid fa-arrow-left"></i></span><span id="navBackText">Kembali ke Trading</span>
    </a>
    <a href="{{ route('logout') }}" class="nav-item"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      <span class="nav-icon"><i class="fa-solid fa-right-from-bracket"></i></span><span id="navLogoutText">Logout</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
  </div>
  <div class="sidebar-footer">
    <div class="admin-badge">
      <div class="avatar" style="background:linear-gradient(135deg,#06b6d4,#0284c7);color:#fff">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
      </div>
      <div>
        <div style="font-size:.82rem;font-weight:600">{{ Auth::user()->name }}</div>
        <div style="font-size:.7rem;color:var(--muted)">{{ Auth::user()->email }}</div>
      </div>
    </div>
  </div>
</div>

<div id="mainContent">
  <div class="topbar">
    <div class="topbar-left">
      <button class="hamburger-btn" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
      <span class="topbar-title" id="pageTitle">Dashboard</span>
    </div>
    <div class="topbar-right">
      <button id="langToggleBtn" class="theme-toggle-btn" type="button" onclick="toggleLanguage()" aria-label="Ganti bahasa">
        <i class="fa-solid fa-language"></i>
        <span id="langToggleText">ID</span>
      </button>
      <button id="themeToggleBtn" class="theme-toggle-btn" type="button" onclick="toggleTheme()" aria-label="Ganti tema admin">
        <i id="themeToggleIcon" class="fa-solid fa-moon"></i>
        <span id="themeToggleText">Gelap</span>
      </button>
      <span class="live-dot" id="liveIndicator">LIVE DB</span>
      <span class="clock-badge" id="clockDisplay">—</span>
    </div>
  </div>

  <div class="content-area">

    <!-- ERROR BANNER GLOBAL -->
    <div id="globalError" class="error-banner">
      <i class="fa-solid fa-triangle-exclamation me-2"></i>
      <span id="globalErrorMsg"></span>
    </div>

    <!-- PAGE: DASHBOARD -->
    <div id="page-dashboard" class="page-section active">
      <div class="row g-3 mb-4" id="kpiRow">
        <div class="col-12 text-center py-4"><small id="dashLoadingText" style="color:var(--muted)">Memuat data dari database...</small></div>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="card-glass">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="card-title-sm mb-0" id="dashVol7Label">Volume Trading 7 Hari</span>
              <span class="badge-status badge-active">BBCA</span>
            </div>
            <div class="chart-container"><canvas id="chartVolume"></canvas></div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card-glass">
            <div class="card-title-sm" id="dashDistLabel">Distribusi Transaksi</div>
            <div class="chart-container"><canvas id="chartStocks"></canvas></div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-lg-5">
          <div class="card-glass">
            <div class="card-title-sm" id="dashTopTradersLabel">Top 5 Traders</div>
            <div id="topTraders"><div class="text-center py-4" style="color:var(--muted);font-size:.82rem">Memuat...</div></div>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="card-glass">
            <div class="card-title-sm" id="dashLatestTxLabel">Transaksi Terbaru</div>
            <div id="liveFeed"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- PAGE: USERS -->
    <div id="page-users" class="page-section">
      <div class="section-header">
        <div>
          <div class="section-title" id="usersTitle">Kelola User</div>
          <div id="userCount" style="font-size:.78rem;color:var(--muted);margin-top:3px">Memuat...</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn-outline" id="usersRefreshBtn" onclick="loadData()"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
          <button class="btn-accent" id="usersAddBtn" onclick="openCreateUserModal()"><i class="fa-solid fa-user-plus"></i> Tambah User</button>
        </div>
      </div>
      <div class="card-glass mb-3">
        <div class="row g-2 align-items-center">
          <div class="col-md-5">
            <input type="text" id="userSearch" class="input-custom" placeholder="🔍  Cari nama atau email..." oninput="renderUserTable()">
          </div>
          <div class="col-md-3">
            <select id="userFilter" class="input-custom" onchange="renderUserTable()">
              <option value="all">Semua Status</option>
              <option value="active">Aktif</option>
              <option value="suspended">Suspended</option>
              <option value="inactive">Nonaktif</option>
            </select>
          </div>
        </div>
      </div>
      <div class="table-wrap">
        <table class="table-custom">
          <thead>
            <tr>
              <th>#</th><th>User</th><th class="text-end">Saldo</th>
              <th class="text-end">Lot</th><th>Saham</th><th>Status</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody id="userTableBody">
            <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">Memuat...</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PAGE: TRANSAKSI -->
    <div id="page-transactions" class="page-section">
      <div class="section-header">
        <div>
          <div class="section-title" id="txTitle">Riwayat Transaksi</div>
          <div id="txCount" style="font-size:.78rem;color:var(--muted);margin-top:3px">Memuat...</div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button class="btn-outline" id="txRefreshBtn" onclick="loadData()"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
          <button class="btn-accent" id="txExportBtn" onclick="exportTxCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
        </div>
      </div>
      <div class="card-glass mb-3">
        <div class="row g-2">
          <div class="col-md-3">
            <select id="txTypeFilter" class="input-custom" onchange="renderTxTable()">
              <option value="all">Semua Tipe</option>
              <option value="BUY">BUY</option>
              <option value="SELL">SELL</option>
            </select>
          </div>
          <div class="col-md-3">
            <input type="date" id="txDateFilter" class="input-custom" onchange="renderTxTable()">
          </div>
        </div>
      </div>
      <div class="table-wrap">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Waktu</th><th>User</th><th>Tipe</th><th>Saham</th>
              <th class="text-end">Lot</th><th class="text-end">Harga</th><th class="text-end">Total</th>
            </tr>
          </thead>
          <tbody id="txTableBody">
            <tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">Memuat...</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PAGE: LAPORAN -->
    <div id="page-reports" class="page-section">
      <div class="section-header">
        <div class="section-title" id="reportsTitle">Laporan & Analitik</div>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-lg-8">
          <div class="card-glass">
            <div class="card-title-sm">Volume Harian 30 Hari</div>
            <div class="chart-container"><canvas id="chartDailyVol"></canvas></div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card-glass">
            <div class="card-title-sm">Top Saham (Lot)</div>
            <div class="chart-container"><canvas id="chartTopStocks"></canvas></div>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-lg-6">
          <div class="card-glass">
            <div class="card-title-sm">Pertumbuhan User</div>
            <div class="chart-container"><canvas id="chartUserGrowth"></canvas></div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card-glass">
            <div class="card-title-sm">BUY vs SELL Ratio</div>
            <div class="chart-container"><canvas id="chartBuySell"></canvas></div>
          </div>
        </div>
      </div>
    </div>

    <!-- PAGE: SETTINGS -->
    <div id="page-settings" class="page-section">
      <div class="section-header">
        <div class="section-title" id="settingsTitle">Pengaturan Sistem</div>
        <button class="btn-accent" id="settingsSaveBtn" onclick="saveSettings()"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
      </div>
      <div class="row g-3">
        <div class="col-lg-6">
          <div class="card-glass">
            <div class="card-title-sm">Aturan Trading</div>
            <div class="toggle-wrap">
              <div class="toggle-info"><div class="toggle-label">Jam Trading (09.00 – 15.30)</div><div class="toggle-desc">Batasi transaksi sesuai jam bursa</div></div>
              <div class="switch on" id="togTradingHours" onclick="toggleSwitch(this)"></div>
            </div>
            <div class="toggle-wrap">
              <div class="toggle-info"><div class="toggle-label">Izinkan Trading Weekend</div><div class="toggle-desc">Buka akses di hari Sabtu & Minggu</div></div>
              <div class="switch" id="togWeekend" onclick="toggleSwitch(this)"></div>
            </div>
            <div class="toggle-wrap">
              <div class="toggle-info"><div class="toggle-label">Mode Maintenance</div><div class="toggle-desc">Nonaktifkan semua aktivitas user</div></div>
              <div class="switch" id="togMaintenance" onclick="toggleSwitch(this)"></div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card-glass">
            <div class="card-title-sm">Parameter Transaksi</div>
            <div class="mb-3">
              <label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Minimal Lot</label>
              <input type="number" id="setMinLot" class="input-custom" value="1" min="1">
            </div>
            <div class="mb-3">
              <label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Maksimal Lot</label>
              <input type="number" id="setMaxLot" class="input-custom" value="100" min="1">
            </div>
            <div class="mb-3">
              <label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Komisi Platform (%)</label>
              <input type="number" id="setCommission" class="input-custom" value="0.15" step="0.01" min="0">
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="card-glass">
            <div class="card-title-sm">Info Sistem</div>
            <div class="row g-3">
              <div class="col-md-3 col-6">
                <div style="font-size:.7rem;color:var(--muted);margin-bottom:4px">Versi Panel</div>
                <div style="font-family:'JetBrains Mono',monospace;font-size:.85rem;color:var(--accent)">v2.5.0</div>
              </div>
              <div class="col-md-3 col-6">
                <div style="font-size:.7rem;color:var(--muted);margin-bottom:4px">Saham Aktif</div>
                <div style="font-family:'JetBrains Mono',monospace;font-size:.85rem;color:var(--green)">BBCA</div>
              </div>
              <div class="col-md-3 col-6">
                <div style="font-size:.7rem;color:var(--muted);margin-bottom:4px">Harga BBCA</div>
                <div id="sysStockPrice" style="font-family:'JetBrains Mono',monospace;font-size:.85rem">Loading...</div>
              </div>
              <div class="col-md-3 col-6">
                <div style="font-size:.7rem;color:var(--muted);margin-bottom:4px">Database</div>
                <div style="font-family:'JetBrains Mono',monospace;font-size:.85rem;color:var(--green)" id="dbStatus">● Connected</div>
              </div>
            </div>
          </div>
        </div>
    </div>

    <!-- ================= PAGE: ANNOUNCEMENT BROADCAST ================= -->
    <div id="page-announcement" class="page-section">
      <div class="section-header">
        <div>
          <div class="section-title">📢 Broadcast Banner Pengumuman</div>
          <div style="font-size:.78rem;color:var(--muted)">Pesan yang di-broadcast akan langsung muncul di bagian atas halaman user</div>
        </div>
        <button class="btn-accent" onclick="saveAnnouncement()"><i class="fa-solid fa-paper-plane me-1"></i>Simpan & Broadcast</button>
      </div>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="card-glass">
            <div class="card-title-sm">Pesan Broadcast</div>
            <div class="mb-3">
              <label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Teks Pengumuman</label>
              <textarea id="ancMessage" class="input-custom" rows="4" placeholder="Tulis pengumuman penting untuk seluruh pengguna..."></textarea>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Tipe Banner</label>
                <select id="ancType" class="input-custom">
                  <option value="info">💡 Informasi (Biru)</option>
                  <option value="success">✅ Pengumuman Positif (Hijau)</option>
                  <option value="warning">⚠️ Peringatan (Kuning)</option>
                  <option value="danger">🚨 Darurat / Maintenance (Merah)</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Status Banner</label>
                <div class="toggle-wrap py-2" style="border:none;">
                  <div class="toggle-info"><div class="toggle-label">Tampilkan di User Dashboard</div></div>
                  <div class="switch on" id="swAncEnabled" onclick="toggleSwitch('swAncEnabled')"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card-glass">
            <div class="card-title-sm">Preview Banner User</div>
            <div id="ancPreviewBox" style="padding:12px 14px;border-radius:10px;font-size:.82rem;background:rgba(6,182,212,0.1);border:1px solid rgba(6,182,212,0.25);color:var(--accent);">
              <span id="ancPreviewText">🔔 Pengumuman: RUPSLB BBCA & Pembagian Dividen Interim diselenggarakan bulan ini!</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ================= PAGE: AUDIT LOG ================= -->
    <div id="page-audit-log" class="page-section">
      <div class="section-header">
        <div>
          <div class="section-title">🛡️ Log Aktivitas Audit Sistem</div>
          <div style="font-size:.78rem;color:var(--muted)">Rekam jejak login, perubahan saldo, dan aktivitas admin/user</div>
        </div>
        <button class="btn-outline" onclick="loadAuditLogs()"><i class="fa-solid fa-arrows-rotate me-1"></i>Refresh Log</button>
      </div>

      <div class="card-glass">
        <div class="table-wrap">
          <table class="table-custom">
            <thead>
              <tr>
                <th>ID</th>
                <th>Pengguna</th>
                <th>Aktivitas Sistem</th>
                <th>IP Address</th>
                <th>Waktu (WIB)</th>
              </tr>
            </thead>
            <tbody id="auditLogTbody">
              <tr><td colspan="5" class="text-center c-muted py-3">Memuat audit log...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>

<!-- TOAST -->
<div id="toastContainer"></div>

<!-- CONFIRM MODAL -->
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="confirmTitle">Konfirmasi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body py-3"><p id="confirmMsg" style="font-size:.88rem;color:var(--muted)"></p></div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" id="confirmCancelBtn" data-bs-dismiss="modal">Batal</button>
        <button type="button" id="confirmBtn" class="btn-accent">Ya, Lanjutkan</button>
      </div>
    </div>
  </div>
</div>

<!-- CREATE USER MODAL -->
<div class="modal fade" id="createUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2" style="color:var(--accent)"></i>Tambah User Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3"><label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Nama Lengkap</label><input type="text" id="newUserName" class="input-custom" placeholder="Budi Santoso"></div>
        <div class="mb-3"><label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Email</label><input type="email" id="newUserEmail" class="input-custom" placeholder="email@contoh.com"></div>
        <div class="mb-3"><label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Password</label><input type="password" id="newUserPassword" class="input-custom" placeholder="Min. 8 karakter"></div>
        <div class="mb-3"><label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Saldo Awal (Rp)</label><input type="number" id="newUserBalance" class="input-custom" value="100000000"></div>
        <div class="mb-3"><label class="form-label" style="font-size:.78rem;color:var(--muted);font-weight:600">Role</label><select id="newUserRole" class="input-custom"><option value="user">User</option><option value="admin">Admin</option></select></div>
        <div id="createUserError" class="alert alert-danger py-2" style="font-size:.8rem;display:none;border-radius:10px"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-outline" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn-accent" onclick="createUser()"><i class="fa-solid fa-check me-1"></i>Buat User</button>
      </div>
    </div>
  </div>
</div>

<script>
/* ============================================================
   CONFIG & STATE
   ============================================================ */
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const BASE = '/api/admin';

let USERS = [], TRANSACTIONS = [], chartInstances = {};
let currentPrice = 0, previousClose = 0;
const THEME_KEY = 'admin-theme';
const LANG_KEY = 'app-lang';
let currentLang = 'id';

const I18N = {
  id: {
    navSectionMain: 'Menu Utama',
    navDashboardText: 'Dashboard',
    navUsersText: 'Kelola User',
    navTransactionsText: 'Transaksi',
    navReportsText: 'Laporan',
    navSectionSystem: 'Sistem',
    navSettingsText: 'Pengaturan',
    navSectionAccess: 'Akses',
    navBackText: 'Kembali ke Trading',
    navLogoutText: 'Logout',
    pageDashboard: 'Dashboard',
    pageUsers: 'Kelola User',
    pageTransactions: 'Transaksi',
    pageReports: 'Laporan',
    pageSettings: 'Pengaturan',
    themeDark: 'Gelap',
    themeLight: 'Terang',
    dashLoadingText: 'Memuat data dari database...',
    dashVol7Label: 'Volume Trading 7 Hari',
    dashDistLabel: 'Distribusi Transaksi',
    dashTopTradersLabel: 'Top 5 Traders',
    dashLatestTxLabel: 'Transaksi Terbaru',
    usersTitle: 'Kelola User',
    usersRefreshBtn: 'Refresh',
    usersAddBtn: 'Tambah User',
    usersSearchPh: '🔍  Cari nama atau email...',
    txTitle: 'Riwayat Transaksi',
    txRefreshBtn: 'Refresh',
    txExportBtn: 'Export CSV',
    reportsTitle: 'Laporan & Analitik',
    settingsTitle: 'Pengaturan Sistem',
    settingsSaveBtn: 'Simpan',
    confirmTitle: 'Konfirmasi',
    confirmCancelBtn: 'Batal',
    confirmYesBtn: 'Ya, Lanjutkan',
    dbConnecting: '● Menghubungkan...',
    dbConnected: '● Terhubung',
    dbError: '● Error',
    liveDb: 'LIVE DB',
    loadDataFailed: 'Gagal memuat data: ',
    loadSettingsFailed: 'Gagal memuat pengaturan: ',
    forbiddenAdmin: 'Akses ditolak. Anda tidak memiliki izin admin.',
    requestFailed: 'Request gagal',
    saveSettingsSuccess: 'Pengaturan berhasil disimpan.'
    ,noUserFound: 'Tidak ada user ditemukan'
    ,usersFoundSuffix: ' user ditemukan'
    ,nameEmailRequired: 'Nama dan email wajib diisi.'
    ,passwordMin: 'Password minimal 8 karakter.'
    ,userAddedSuffix: ' berhasil ditambahkan.'
    ,changeRoleTitle: 'Ubah Role'
    ,changeRoleQuestion: 'Ubah role user ini ke '
    ,userDeletedTitle: 'Hapus User'
    ,userDeletedQuestion: 'Yakin hapus '
    ,userDeletedQuestionSuffix: '? Data transaksinya juga akan terhapus.'
    ,userDeletedSuffix: ' dihapus.'
    ,suspendUser: 'Suspend User'
    ,activateUser: 'Aktifkan User'
    ,changeStatusQuestion: 'Ubah status '
    ,changeStatusQuestionSuffix: ' ke '
    ,statusUpdatedPrefix: 'Status diubah ke '
    ,resetBalanceTitle: 'Reset Saldo'
    ,resetBalanceQuestion: 'Reset saldo '
    ,resetBalanceQuestionSuffix: ' ke Rp 100.000.000?'
    ,balanceResetPrefix: 'Saldo '
    ,balanceResetSuffix: ' direset.'
    ,noTransactionData: 'Tidak ada transaksi'
    ,showingText: 'Menampilkan '
    ,ofText: ' dari '
    ,transactionText: ' transaksi'
    ,noDataExport: 'Tidak ada data untuk diekspor.'
    ,csvDownloaded: 'CSV berhasil diunduh.'
    ,statusActive: 'Aktif'
    ,statusSuspended: 'Suspended'
    ,statusInactive: 'Nonaktif'
  },
  en: {
    navSectionMain: 'Main Menu',
    navDashboardText: 'Dashboard',
    navUsersText: 'Manage Users',
    navTransactionsText: 'Transactions',
    navReportsText: 'Reports',
    navSectionSystem: 'System',
    navSettingsText: 'Settings',
    navSectionAccess: 'Access',
    navBackText: 'Back to Trading',
    navLogoutText: 'Logout',
    pageDashboard: 'Dashboard',
    pageUsers: 'Manage Users',
    pageTransactions: 'Transactions',
    pageReports: 'Reports',
    pageSettings: 'Settings',
    themeDark: 'Dark',
    themeLight: 'Light',
    dashLoadingText: 'Loading data from database...',
    dashVol7Label: '7-Day Trading Volume',
    dashDistLabel: 'Transaction Distribution',
    dashTopTradersLabel: 'Top 5 Traders',
    dashLatestTxLabel: 'Latest Transactions',
    usersTitle: 'Manage Users',
    usersRefreshBtn: 'Refresh',
    usersAddBtn: 'Add User',
    usersSearchPh: '🔍  Search name or email...',
    txTitle: 'Transaction History',
    txRefreshBtn: 'Refresh',
    txExportBtn: 'Export CSV',
    reportsTitle: 'Reports & Analytics',
    settingsTitle: 'System Settings',
    settingsSaveBtn: 'Save',
    confirmTitle: 'Confirmation',
    confirmCancelBtn: 'Cancel',
    confirmYesBtn: 'Yes, Continue',
    dbConnecting: '● Connecting...',
    dbConnected: '● Connected',
    dbError: '● Error',
    liveDb: 'LIVE DB',
    loadDataFailed: 'Failed to load data: ',
    loadSettingsFailed: 'Failed to load settings: ',
    forbiddenAdmin: 'Access denied. You do not have admin permission.',
    requestFailed: 'Request failed',
    saveSettingsSuccess: 'Settings saved successfully.'
    ,noUserFound: 'No users found'
    ,usersFoundSuffix: ' users found'
    ,nameEmailRequired: 'Name and email are required.'
    ,passwordMin: 'Password must be at least 8 characters.'
    ,userAddedSuffix: ' added successfully.'
    ,changeRoleTitle: 'Change Role'
    ,changeRoleQuestion: 'Change this user role to '
    ,userDeletedTitle: 'Delete User'
    ,userDeletedQuestion: 'Delete '
    ,userDeletedQuestionSuffix: '? Their transactions will also be removed.'
    ,userDeletedSuffix: ' deleted.'
    ,suspendUser: 'Suspend User'
    ,activateUser: 'Activate User'
    ,changeStatusQuestion: 'Change status of '
    ,changeStatusQuestionSuffix: ' to '
    ,statusUpdatedPrefix: 'Status changed to '
    ,resetBalanceTitle: 'Reset Balance'
    ,resetBalanceQuestion: 'Reset balance of '
    ,resetBalanceQuestionSuffix: ' to Rp 100,000,000?'
    ,balanceResetPrefix: 'Balance of '
    ,balanceResetSuffix: ' reset.'
    ,noTransactionData: 'No transactions'
    ,showingText: 'Showing '
    ,ofText: ' of '
    ,transactionText: ' transactions'
    ,noDataExport: 'No data to export.'
    ,csvDownloaded: 'CSV downloaded successfully.'
    ,statusActive: 'Active'
    ,statusSuspended: 'Suspended'
    ,statusInactive: 'Inactive'
  }
};

function t(key) {
  return I18N[currentLang]?.[key] || I18N.id[key] || key;
}

function setTextById(id, key) {
  const el = document.getElementById(id);
  if (el) el.textContent = t(key);
}

function toggleSidebar(forceState) {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  if (!sidebar) return;
  const isOpen = forceState !== undefined ? forceState : !sidebar.classList.contains('open');
  if (isOpen) {
    sidebar.classList.add('open');
    if (overlay) overlay.classList.add('show');
  } else {
    sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('show');
  }
}

function showPage(pageId, btnEl) {
  document.querySelectorAll('.page-section').forEach(sec => {
    sec.classList.remove('active');
    sec.style.display = 'none';
  });

  const target = document.getElementById(pageId);
  if (target) {
    target.classList.add('active');
    target.style.display = 'block';
  }

  if (btnEl) {
    document.querySelectorAll('.sidebar-nav .nav-item').forEach(btn => btn.classList.remove('active'));
    btnEl.classList.add('active');
  }

  const pageTitle = pageTitleById(pageId);
  const titleEl = document.getElementById('pageTitle');
  if (titleEl) titleEl.textContent = pageTitle;

  if (pageId === 'page-announcement') loadAnnouncementAdmin();
  if (pageId === 'page-audit-log') loadAuditLogs();

  toggleSidebar(false);
}

function pageTitleById(pageId) {
  const map = {
    'page-dashboard': 'Dashboard Overview',
    'page-users': 'Kelola User',
    'page-transactions': 'Riwayat Transaksi',
    'page-reports': 'Laporan Analitik',
    'page-announcement': 'Broadcast Banner Pengumuman',
    'page-audit-log': 'Log Aktivitas Audit Sistem',
    'page-settings': 'Pengaturan Sistem'
  };
  return map[pageId] || 'Dashboard Overview';
}

function applyLanguage(lang, rerenderTitle = true) {
  currentLang = lang === 'en' ? 'en' : 'id';
  document.documentElement.lang = currentLang;
  try { localStorage.setItem(LANG_KEY, currentLang); } catch (_) {}

  setTextById('navSectionMain', 'navSectionMain');
  setTextById('navDashboardText', 'navDashboardText');
  setTextById('navUsersText', 'navUsersText');
  setTextById('navTransactionsText', 'navTransactionsText');
  setTextById('navReportsText', 'navReportsText');
  setTextById('navSectionSystem', 'navSectionSystem');
  setTextById('navSettingsText', 'navSettingsText');
  setTextById('navSectionAccess', 'navSectionAccess');
  setTextById('navBackText', 'navBackText');
  setTextById('navLogoutText', 'navLogoutText');
  setTextById('dashLoadingText', 'dashLoadingText');
  setTextById('dashVol7Label', 'dashVol7Label');
  setTextById('dashDistLabel', 'dashDistLabel');
  setTextById('dashTopTradersLabel', 'dashTopTradersLabel');
  setTextById('dashLatestTxLabel', 'dashLatestTxLabel');
  setTextById('usersTitle', 'usersTitle');
  setTextById('txTitle', 'txTitle');
  setTextById('reportsTitle', 'reportsTitle');
  setTextById('settingsTitle', 'settingsTitle');
  setTextById('confirmTitle', 'confirmTitle');
  setTextById('confirmCancelBtn', 'confirmCancelBtn');
  setTextById('confirmBtn', 'confirmYesBtn');

  const usersRefreshBtn = document.getElementById('usersRefreshBtn');
  if (usersRefreshBtn) usersRefreshBtn.innerHTML = '<i class="fa-solid fa-rotate-right"></i> ' + t('usersRefreshBtn');
  const usersAddBtn = document.getElementById('usersAddBtn');
  if (usersAddBtn) usersAddBtn.innerHTML = '<i class="fa-solid fa-user-plus"></i> ' + t('usersAddBtn');
  const txRefreshBtn = document.getElementById('txRefreshBtn');
  if (txRefreshBtn) txRefreshBtn.innerHTML = '<i class="fa-solid fa-rotate-right"></i> ' + t('txRefreshBtn');
  const txExportBtn = document.getElementById('txExportBtn');
  if (txExportBtn) txExportBtn.innerHTML = '<i class="fa-solid fa-file-csv"></i> ' + t('txExportBtn');
  const settingsSaveBtn = document.getElementById('settingsSaveBtn');
  if (settingsSaveBtn) settingsSaveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> ' + t('settingsSaveBtn');

  const userSearch = document.getElementById('userSearch');
  if (userSearch) userSearch.placeholder = t('usersSearchPh');
  const liveIndicator = document.getElementById('liveIndicator');
  if (liveIndicator) liveIndicator.textContent = t('liveDb');

  const langText = document.getElementById('langToggleText');
  if (langText) langText.textContent = currentLang.toUpperCase();

  if (rerenderTitle) {
    const active = document.querySelector('.page-section.active');
    if (active) {
      document.getElementById('pageTitle').textContent = pageTitleById(active.id);
    }
  }

  updateThemeToggleUi();
}

function localeCode() {
  return currentLang === 'en' ? 'en-US' : 'id-ID';
}

function initializeLanguage() {
  let saved = 'id';
  try { saved = localStorage.getItem(LANG_KEY) || 'id'; } catch (_) {}
  applyLanguage(saved, true);
}

function toggleLanguage() {
  applyLanguage(currentLang === 'id' ? 'en' : 'id', true);
}

function getTheme() {
  const activeTheme = document.documentElement.getAttribute('data-theme');
  return activeTheme === 'light' ? 'light' : 'dark';
}

function chartPalette() {
  const isLight = getTheme() === 'light';
  return {
    tick: isLight ? '#64748b' : '#6b7b8d',
    label: isLight ? '#0f172a' : '#e2e8f0',
    grid: isLight ? 'rgba(15,23,42,0.08)' : 'rgba(255,255,255,0.04)',
    tooltipBg: isLight ? '#ffffff' : '#1a2332',
    tooltipText: isLight ? '#0f172a' : '#e2e8f0',
    tooltipBorder: isLight ? 'rgba(15,23,42,0.16)' : 'rgba(255,255,255,0.1)'
  };
}

function updateThemeToggleUi() {
  const icon = document.getElementById('themeToggleIcon');
  const text = document.getElementById('themeToggleText');
  if (!icon || !text) return;

  const isLight = getTheme() === 'light';
  icon.className = 'fa-solid ' + (isLight ? 'fa-sun' : 'fa-moon');
  text.textContent = isLight ? t('themeLight') : t('themeDark');
}

function applyTheme(theme, rerender = true) {
  const selected = theme === 'light' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', selected);
  try { localStorage.setItem(THEME_KEY, selected); } catch (_) {}
  updateThemeToggleUi();
  if (rerender) refreshActiveSection();
}

function initializeTheme() {
  let saved = null;
  try { saved = localStorage.getItem(THEME_KEY); } catch (_) {}
  applyTheme(saved === 'light' ? 'light' : getTheme(), false);
}

function toggleTheme() {
  applyTheme(getTheme() === 'dark' ? 'light' : 'dark');
}

/* ============================================================
   API LAYER — Fix utama: tambah credentials + X-Requested-With
   ============================================================ */
async function apiFetch(path, opts = {}) {
    const res = await fetch(BASE + path, {
        credentials: 'include',                          // ← PENTING: kirim session cookie
        headers: {
            'Content-Type':     'application/json',
            'X-CSRF-TOKEN':     CSRF,
            'X-Requested-With': 'XMLHttpRequest',        // ← PENTING: Sanctum stateful
            'Accept':           'application/json',
            ...opts.headers
        },
        ...opts
    });

    // Session expired / belum login → redirect ke login
    if (res.status === 401) {
        window.location.href = '/login';
        return;
    }

    // Forbidden (bukan admin)
    if (res.status === 403) {
      throw new Error(t('forbiddenAdmin'));
    }

    if (!res.ok) {
        const err = await res.json().catch(() => ({ message: 'Error ' + res.status }));
        throw new Error(err.message || t('requestFailed'));
    }

    return res.json();
}

async function loadData() {
    try {
        setDbStatus('connecting');
        hideGlobalError();

        [USERS, TRANSACTIONS] = await Promise.all([
            apiFetch('/users'),
            apiFetch('/transactions')
        ]);

        setDbStatus('connected');
        refreshActiveSection();
    } catch (err) {
        console.error('API Error:', err);
        setDbStatus('error');
        showGlobalError(err.message);
        toast(t('loadDataFailed') + err.message, 'error');
    }
}

async function loadSettings() {
    try {
        const s = await apiFetch('/settings');
        document.getElementById('togTradingHours').classList.toggle('on', s.trading_hours == 1);
        document.getElementById('togWeekend').classList.toggle('on',      s.weekend_trading == 1);
        document.getElementById('togMaintenance').classList.toggle('on',  s.maintenance == 1);
        if (s.min_lot)    document.getElementById('setMinLot').value    = s.min_lot;
        if (s.max_lot)    document.getElementById('setMaxLot').value    = s.max_lot;
        if (s.commission) document.getElementById('setCommission').value = s.commission;
    } catch (e) {
        console.warn('Gagal load settings:', e.message);
        toast(t('loadSettingsFailed') + e.message, 'error');
    }
}

/* ============================================================
   DB STATUS HELPERS
   ============================================================ */
function setDbStatus(state) {
    const el    = document.getElementById('dbStatus');
    const live  = document.getElementById('liveIndicator');
    const map   = {
        connecting: [t('dbConnecting'), 'var(--amber)'],
        connected:  [t('dbConnected'),  'var(--green)'],
        error:      [t('dbError'),      'var(--red)'],
    };
    if (!el) return;
    el.textContent  = map[state][0];
    el.style.color  = map[state][1];
    if (live) live.style.color = state === 'connected' ? 'var(--green)' : 'var(--red)';
}

function showGlobalError(msg) {
    const el = document.getElementById('globalError');
    document.getElementById('globalErrorMsg').textContent = msg;
    el.style.display = 'block';
}

function hideGlobalError() {
    document.getElementById('globalError').style.display = 'none';
}

/* ============================================================
   LIVE PRICE
   ============================================================ */
const YAHOO_URL = 'https://query1.finance.yahoo.com/v8/finance/chart/BBCA.JK?interval=1m&range=1d&includePrePost=false';
const PROXIES = [
    u => 'https://api.allorigins.win/raw?url=' + encodeURIComponent(u),
    u => 'https://corsproxy.io/?' + encodeURIComponent(u),
];

async function fetchPrice() {
    for (const px of PROXIES) {
        try {
            const ctrl = new AbortController();
            setTimeout(() => ctrl.abort(), 5000);
            const r = await fetch(px(YAHOO_URL + '&t=' + Date.now()), { signal: ctrl.signal });
            if (!r.ok) continue;
            const j = await r.json();
            const m = j?.chart?.result?.[0]?.meta;
            if (!m?.regularMarketPrice) continue;
            currentPrice  = Math.round(m.regularMarketPrice);
            previousClose = Math.round(m.chartPreviousClose || m.regularMarketPrice);
            const change  = currentPrice - previousClose;
            const pct     = previousClose ? (change / previousClose * 100) : 0;
            const sign    = change >= 0 ? '+' : '';
            const color   = change >= 0 ? 'var(--green)' : 'var(--red)';
            const el = document.getElementById('sysStockPrice');
            if (el) el.innerHTML = 'Rp ' + currentPrice.toLocaleString(localeCode()) +
                ' <span style="font-size:.8em;margin-left:4px;color:' + color + '">(' + sign + pct.toFixed(2) + '%)</span>';
            return;
        } catch (e) {}
    }
    const el = document.getElementById('sysStockPrice');
        if (el && currentPrice === 0) { currentPrice = 10250; el.textContent = 'Rp 10.250 (Offline)'; }
}

/* ============================================================
   NAVIGASI
   ============================================================ */
function showPage(pageId, navEl) {
    document.querySelectorAll('.page-section').forEach(p => p.classList.remove('active'));
    document.getElementById(pageId).classList.add('active');
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    if (navEl) navEl.classList.add('active');
  document.getElementById('pageTitle').textContent = pageTitleById(pageId);
    hideGlobalError();
    if (pageId === 'page-settings') loadSettings();
    else loadData();
    if (window.innerWidth <= 992) toggleSidebar(false);
}

function refreshActiveSection() {
    const active = document.querySelector('.page-section.active');
    if (!active) return;
    const id = active.id;
    if (id === 'page-dashboard')    initDashboardCharts();
    if (id === 'page-users')        renderUserTable();
    if (id === 'page-transactions') renderTxTable();
    if (id === 'page-reports')      initReportCharts();
}

function toggleSidebar(force) {
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('sidebarOverlay');
    const shouldOpen = force !== undefined ? force : !sb.classList.contains('open');
    sb.classList.toggle('open', shouldOpen);
    ov.classList.toggle('show', shouldOpen);
}

/* ============================================================
   UTILITIES
   ============================================================ */
const fmt         = n => 'Rp ' + Math.round(n).toLocaleString(localeCode());
const fmtShort    = n => n >= 1e12 ? 'Rp '+(n/1e12).toFixed(1)+'T' : n >= 1e9 ? 'Rp '+(n/1e9).toFixed(1)+'M' : n >= 1e6 ? 'Rp '+(n/1e6).toFixed(0)+'Jt' : fmt(n);
const fmtTime     = d => new Date(d).toLocaleString(localeCode(),{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'});
const avatarColor = name => { let h=0; for(let i=0;i<name.length;i++) h=name.charCodeAt(i)+((h<<5)-h); return `hsl(${Math.abs(h)%360},50%,35%)`; };

function toast(msg, type='info') {
    const el = document.createElement('div');
    el.className = 'toast-custom';
    const icons  = { success:'fa-check-circle', error:'fa-circle-xmark', info:'fa-circle-info' };
    const colors = { success:'var(--green)', error:'var(--red)', info:'var(--accent)' };
    el.innerHTML = `<i class="fa-solid ${icons[type]||icons.info}" style="color:${colors[type]||colors.info};font-size:16px"></i><span>${msg}</span>`;
    document.getElementById('toastContainer').appendChild(el);
    setTimeout(() => { el.style.opacity='0'; el.style.transform='translateX(40px)'; el.style.transition='.3s'; setTimeout(()=>el.remove(),300); }, 3500);
}

function confirmAction(title, msg, callback) {
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMsg').textContent   = msg;
    const m   = new bootstrap.Modal(document.getElementById('confirmModal'));
    const btn = document.getElementById('confirmBtn');
    const nb  = btn.cloneNode(true);
    btn.parentNode.replaceChild(nb, btn);
    nb.id = 'confirmBtn';
    nb.addEventListener('click', () => { callback(); m.hide(); });
    m.show();
}

function toggleSwitch(el) { el.classList.toggle('on'); }

/* ============================================================
   DASHBOARD
   ============================================================ */
function initDashboardCharts() {
    const totalUsers  = USERS.length;
    const activeUsers = USERS.filter(u => u.status === 'active').length;
    const todayTx     = TRANSACTIONS.filter(t => new Date(t.time).toDateString() === new Date().toDateString()).length;
    const totalVol    = TRANSACTIONS.reduce((s, t) => s + parseFloat(t.total || 0), 0);

    document.getElementById('kpiRow').innerHTML = `
      <div class="col-6 col-lg-3"><div class="stat-card">
        <div class="stat-glow" style="background:var(--accent)"></div>
        <div class="stat-icon" style="background:var(--accent-glow);color:var(--accent)"><i class="fa-solid fa-users"></i></div>
        <div class="stat-value">${totalUsers}</div><div class="stat-label">Total User</div>
        <div class="stat-trend" style="color:var(--green)"><i class="fa-solid fa-arrow-up me-1"></i>${activeUsers} aktif</div>
      </div></div>
      <div class="col-6 col-lg-3"><div class="stat-card">
        <div class="stat-glow" style="background:var(--green)"></div>
        <div class="stat-icon" style="background:rgba(16,185,129,.12);color:var(--green)"><i class="fa-solid fa-arrow-right-arrow-left"></i></div>
        <div class="stat-value">${TRANSACTIONS.length}</div><div class="stat-label">Total Transaksi</div>
        <div class="stat-trend" style="color:var(--muted)">${todayTx} hari ini</div>
      </div></div>
      <div class="col-6 col-lg-3"><div class="stat-card">
        <div class="stat-glow" style="background:var(--amber)"></div>
        <div class="stat-icon" style="background:rgba(245,158,11,.12);color:var(--amber)"><i class="fa-solid fa-coins"></i></div>
        <div class="stat-value">${fmtShort(totalVol)}</div><div class="stat-label">Volume Trading</div>
        <div class="stat-trend" style="color:var(--green)">Realtime DB</div>
      </div></div>
      <div class="col-6 col-lg-3"><div class="stat-card">
        <div class="stat-glow" style="background:#8b5cf6"></div>
        <div class="stat-icon" style="background:rgba(139,92,246,.12);color:#8b5cf6"><i class="fa-solid fa-percent"></i></div>
        <div class="stat-value">0.15%</div><div class="stat-label">Komisi Platform</div>
        <div class="stat-trend" style="color:var(--muted)">Konfigurasi</div>
      </div></div>`;

    // Volume 7 hari
    const days=[], volData=[];
    for(let i=6;i>=0;i--){
        const d=new Date(Date.now()-i*86400000);
        days.push(d.toLocaleDateString('id-ID',{weekday:'short'}));
        volData.push(TRANSACTIONS.filter(t=>new Date(t.time).toDateString()===d.toDateString()).reduce((s,t)=>s+parseFloat(t.total||0),0));
    }
    destroyChart('chartVolume');
    chartInstances['chartVolume'] = new Chart(document.getElementById('chartVolume'),{
        type:'line',
        data:{labels:days,datasets:[{label:'Volume',data:volData,borderColor:'#06b6d4',backgroundColor:'rgba(6,182,212,0.08)',fill:true,tension:.4,pointBackgroundColor:'#06b6d4',pointRadius:4,borderWidth:2.5}]},
        options:chartOpts('Rp')
    });

    // BUY vs SELL doughnut
    const buyCount  = TRANSACTIONS.filter(t=>t.type==='BUY').length;
    const sellCount = TRANSACTIONS.filter(t=>t.type==='SELL').length;
    destroyChart('chartStocks');
    chartInstances['chartStocks'] = new Chart(document.getElementById('chartStocks'),{
        type:'doughnut',
        data:{labels:['BUY','SELL'],datasets:[{data:[buyCount||0,sellCount||0],backgroundColor:['#10b981','#ef4444'],borderWidth:0,hoverOffset:8}]},
      options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{position:'bottom',labels:{color:chartPalette().tick,font:{size:11},padding:12,usePointStyle:true,pointStyleWidth:8}}}}
    });

    // Top 5 traders
    const userTxCount={};
    TRANSACTIONS.forEach(t=>{ if(t.userId) userTxCount[t.userId]=(userTxCount[t.userId]||0)+1; });
    const top5=Object.entries(userTxCount).sort((a,b)=>b[1]-a[1]).slice(0,5);
    document.getElementById('topTraders').innerHTML = top5.length
        ? top5.map(([uid,count],i)=>{
            const u=USERS.find(x=>x.id==uid);
            if(!u) return '';
            const pct=(count/top5[0][1])*100;
            return `<div class="d-flex align-items-center gap-3 py-2 ${i<4?'border-bottom':''}" style="border-color:var(--border)!important">
              <span style="font-weight:900;color:var(--muted);width:20px;font-size:.8rem">#${i+1}</span>
              <div class="avatar" style="background:${avatarColor(u.name)};color:#fff;width:32px;height:32px;font-size:.7rem">${u.name.charAt(0)}</div>
              <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${u.name}</div>
                <div style="height:4px;background:var(--progress-track);border-radius:2px;margin-top:4px">
                  <div style="height:100%;width:${pct}%;background:var(--accent);border-radius:2px;transition:.5s"></div>
                </div>
              </div>
              <div style="font-weight:800;font-size:.85rem;color:var(--accent)">${count}</div>
            </div>`;
          }).join('')
        : '<div class="text-center py-3" style="color:var(--muted);font-size:.82rem">Belum ada transaksi</div>';

    // Live feed (10 terbaru)
    document.getElementById('liveFeed').innerHTML = TRANSACTIONS.length
        ? TRANSACTIONS.slice(0,10).map(t=>`
          <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:var(--border)!important;font-size:.82rem">
            <div>
              <span class="fw-bold" style="color:${t.type==='BUY'?'var(--green)':'var(--red)'}">${t.type}</span>
              <span class="ms-2">${t.userName||'—'}</span>
              <span class="ms-1 fw-bold" style="color:var(--accent)">${t.stock||'BBCA'}</span>
            </div>
            <div class="text-end">
              <div class="fw-bold">${t.lot} lot @ ${fmt(t.price||0)}</div>
              <div style="color:var(--muted);font-size:.72rem">${fmtTime(t.time)}</div>
            </div>
          </div>`).join('')
        : '<div class="text-center py-3" style="color:var(--muted);font-size:.82rem">Belum ada transaksi</div>';
}

/* ============================================================
   USER TABLE
   ============================================================ */
function renderUserTable() {
    const search = (document.getElementById('userSearch').value||'').toLowerCase();
    const filter = document.getElementById('userFilter').value;
    const list   = USERS.filter(u=>{
        if(filter!=='all' && u.status!==filter) return false;
        if(search && !u.name.toLowerCase().includes(search) && !u.email.toLowerCase().includes(search)) return false;
        return true;
    });
    const statusMap = {
      active:    [t('statusActive'),'badge-active'],
      suspended: [t('statusSuspended'),'badge-suspended'],
      inactive:  [t('statusInactive'),'badge-inactive']
    };
    const roleBadge = r => r==='admin'
        ? '<span class="badge bg-danger" style="font-size:.65rem">Admin</span>'
        : '<span class="badge bg-info text-dark" style="font-size:.65rem">User</span>';

    document.getElementById('userTableBody').innerHTML = list.length
        ? list.map((u,i)=>`
          <tr>
            <td style="color:var(--muted)">${i+1}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar" style="background:${avatarColor(u.name)};color:#fff;font-size:.75rem">${u.name.charAt(0)}</div>
                <div>
                  <div class="fw-bold" style="font-size:.85rem">${u.name}</div>
                  <div style="font-size:.72rem;color:var(--muted)">${u.email}</div>
                </div>
              </div>
            </td>
            <td class="text-end" style="font-weight:600;font-size:.82rem">${fmt(u.balance||0)}</td>
            <td class="text-end">${u.lots||0}</td>
            <td><span style="font-weight:700;color:var(--accent);font-size:.85rem">${u.stock||'BBCA'}</span></td>
            <td>
              <div class="d-flex flex-column gap-1">
                ${roleBadge(u.role)}
                <span class="badge-status ${statusMap[u.status]?.[1]||'badge-inactive'}">${statusMap[u.status]?.[0]||'—'}</span>
              </div>
            </td>
            <td>
              <div class="d-flex gap-1 flex-wrap">
                <button class="btn-sm-icon" onclick="editUserRole(${u.id},'${u.role}')" title="Toggle Role"><i class="fa-solid fa-user-shield"></i></button>
                <button class="btn-sm-icon" onclick="toggleUserStatus(${u.id})" title="${u.status==='active'?'Suspend':'Aktifkan'}"><i class="fa-solid fa-${u.status==='active'?'ban':'check'}"></i></button>
                <button class="btn-sm-icon" onclick="resetUserBalance(${u.id})" title="Reset Saldo"><i class="fa-solid fa-rotate-left"></i></button>
                <button class="btn-sm-icon" style="color:var(--red)" onclick="deleteUser(${u.id})" title="Hapus"><i class="fa-solid fa-trash"></i></button>
              </div>
            </td>
          </tr>`).join('')
        : '<tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">' + t('noUserFound') + '</td></tr>';

      document.getElementById('userCount').textContent = list.length + t('usersFoundSuffix');
}

function openCreateUserModal() {
    ['newUserName','newUserEmail','newUserPassword'].forEach(id=>document.getElementById(id).value='');
    document.getElementById('newUserBalance').value='100000000';
    document.getElementById('newUserRole').value='user';
    document.getElementById('createUserError').style.display='none';
    new bootstrap.Modal(document.getElementById('createUserModal')).show();
}

async function createUser() {
    const name     = document.getElementById('newUserName').value.trim();
    const email    = document.getElementById('newUserEmail').value.trim();
    const password = document.getElementById('newUserPassword').value;
    const balance  = parseInt(document.getElementById('newUserBalance').value)||100000000;
    const role     = document.getElementById('newUserRole').value||'user';
    const errEl    = document.getElementById('createUserError');
    errEl.style.display='none';

    if(!name||!email) { errEl.textContent=t('nameEmailRequired'); errEl.style.display='block'; return; }
    if(!password||password.length<8) { errEl.textContent=t('passwordMin'); errEl.style.display='block'; return; }

    try {
        await apiFetch('/users',{ method:'POST', body:JSON.stringify({name,email,password,balance,role,stock:'BBCA'}) });
        bootstrap.Modal.getInstance(document.getElementById('createUserModal')).hide();
        toast(name + t('userAddedSuffix'),'success');
        loadData();
    } catch(e) { errEl.textContent=e.message; errEl.style.display='block'; }
}

async function editUserRole(id, currentRole) {
    const newRole = currentRole==='admin'?'user':'admin';
    confirmAction(t('changeRoleTitle'), t('changeRoleQuestion') + newRole + '?', async()=>{
        try { await apiFetch('/users/'+id,{method:'PUT',body:JSON.stringify({role:newRole})}); toast('Role diubah ke '+newRole+'.','success'); loadData(); }
        catch(e) { toast(e.message,'error'); }
    });
}

async function deleteUser(id) {
    const u = USERS.find(x=>x.id===id);
    if(!u) return;
    confirmAction(t('userDeletedTitle'), t('userDeletedQuestion') + u.name + t('userDeletedQuestionSuffix'), async()=>{
      try { await apiFetch('/users/'+id,{method:'DELETE'}); toast(u.name + t('userDeletedSuffix'),'success'); loadData(); }
        catch(e) { toast(e.message,'error'); }
    });
}

async function toggleUserStatus(id) {
    const u = USERS.find(x=>x.id===id);
    if(!u) return;
    const ns = u.status==='active'?'suspended':'active';
    const label = ns==='suspended' ? t('suspendUser') : t('activateUser');
    confirmAction(label, t('changeStatusQuestion') + u.name + t('changeStatusQuestionSuffix') + ns + '?', async()=>{
      try { await apiFetch('/users/'+id,{method:'PUT',body:JSON.stringify({status:ns})}); toast(t('statusUpdatedPrefix') + ns + '.', ns==='active'?'success':'info'); loadData(); }
        catch(e) { toast(e.message,'error'); }
    });
}

async function resetUserBalance(id) {
    const u = USERS.find(x=>x.id===id);
    if(!u) return;
    confirmAction(t('resetBalanceTitle'), t('resetBalanceQuestion') + u.name + t('resetBalanceQuestionSuffix'), async()=>{
      try { await apiFetch('/users/'+id,{method:'PUT',body:JSON.stringify({balance:100000000,reset_portfolio:true})}); toast(t('balanceResetPrefix') + u.name + t('balanceResetSuffix'),'success'); loadData(); }
        catch(e) { toast(e.message,'error'); }
    });
}

/* ============================================================
   TRANSAKSI TABLE
   ============================================================ */
function renderTxTable() {
    const typeFilter = document.getElementById('txTypeFilter').value;
    const dateFilter = document.getElementById('txDateFilter').value;
    const list = TRANSACTIONS.filter(t=>{
        if(typeFilter!=='all' && t.type!==typeFilter) return false;
        if(dateFilter && new Date(t.time).toDateString()!==new Date(dateFilter).toDateString()) return false;
        return true;
    });
    document.getElementById('txTableBody').innerHTML = list.length
        ? list.slice(0,100).map(t=>`
            <tr>
              <td style="white-space:nowrap;font-size:.78rem;color:var(--muted)">${fmtTime(t.time)}</td>
              <td style="font-size:.85rem;font-weight:500">${t.userName||'—'}</td>
              <td><span class="fw-bold" style="color:${t.type==='BUY'?'var(--green)':'var(--red)'};font-size:.8rem">${t.type}</span></td>
              <td class="fw-bold" style="color:var(--accent)">${t.stock||'BBCA'}</td>
              <td class="text-end">${t.lot}</td>
              <td class="text-end" style="font-size:.8rem">${fmt(t.price||0)}</td>
              <td class="text-end fw-bold">${fmt(t.total||0)}</td>
            </tr>`).join('')
        : '<tr><td colspan="7" class="text-center py-4" style="color:var(--muted)">' + t('noTransactionData') + '</td></tr>';
      document.getElementById('txCount').textContent = t('showingText') + Math.min(list.length,100) + t('ofText') + list.length + t('transactionText');
}

function exportTxCSV() {
      if(!TRANSACTIONS.length){ toast(t('noDataExport'),'error'); return; }
    let csv="Waktu,User,Tipe,Saham,Lot,Harga,Total\n";
    TRANSACTIONS.forEach(t=>{ csv+=`"${fmtTime(t.time)}","${t.userName||''}","${t.type}","${t.stock||''}",${t.lot},${t.price||0},${t.total||0}\n`; });
    const a=document.createElement('a');
    a.href=URL.createObjectURL(new Blob([csv],{type:'text/csv;charset=utf-8;'}));
    a.download='transaksi_admin_'+new Date().toISOString().slice(0,10)+'.csv';
    a.click();
    toast(t('csvDownloaded'),'success');
}

/* ============================================================
   LAPORAN
   ============================================================ */
function initReportCharts() {
    const days30=[],vol30=[];
    for(let i=29;i>=0;i--){
        const d=new Date(Date.now()-i*86400000);
        days30.push(d.toLocaleDateString('id-ID',{day:'2-digit',month:'short'}));
        vol30.push(TRANSACTIONS.filter(t=>new Date(t.time).toDateString()===d.toDateString()).reduce((s,t)=>s+parseFloat(t.total||0),0));
    }
    destroyChart('chartDailyVol');
    chartInstances['chartDailyVol'] = new Chart(document.getElementById('chartDailyVol'),{
        type:'bar',
        data:{labels:days30,datasets:[{data:vol30,backgroundColor:vol30.map((_,i)=>i===vol30.length-1?'#06b6d4':'rgba(6,182,212,0.25)'),borderRadius:4}]},
        options:chartOpts('Rp')
    });

    const stockTotals={};
    TRANSACTIONS.forEach(t=>{stockTotals[t.stock]=(stockTotals[t.stock]||0)+(t.lot||0);});
    const topStocks=Object.entries(stockTotals).sort((a,b)=>b[1]-a[1]).slice(0,5);
    destroyChart('chartTopStocks');
    chartInstances['chartTopStocks'] = new Chart(document.getElementById('chartTopStocks'),{
        type:'bar',
        data:{labels:topStocks.map(s=>s[0]),datasets:[{data:topStocks.map(s=>s[1]),backgroundColor:['#06b6d4','#10b981','#f59e0b','#8b5cf6','#ef4444'],borderRadius:6}]},
      options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{color:chartPalette().grid},ticks:{color:chartPalette().tick,font:{size:10}}},y:{grid:{display:false},ticks:{color:chartPalette().label,font:{size:12,weight:'bold'}}}}}
    });

    const userGrowthMap={};
    USERS.forEach(u=>{
        const d=new Date(u.created_at||Date.now()).toLocaleDateString('id-ID',{day:'2-digit',month:'short'});
        userGrowthMap[d]=(userGrowthMap[d]||0)+1;
    });
    const days=[],grow=[];
    let cum=0;
    for(let i=29;i>=0;i--){
        const d=new Date(Date.now()-i*86400000);
        const key=d.toLocaleDateString('id-ID',{day:'2-digit',month:'short'});
        cum+=(userGrowthMap[key]||0);
        days.push(key); grow.push(cum);
    }
    destroyChart('chartUserGrowth');
    chartInstances['chartUserGrowth']=new Chart(document.getElementById('chartUserGrowth'),{
        type:'line',
        data:{labels:days,datasets:[{data:grow,borderColor:'#10b981',backgroundColor:'rgba(16,185,129,0.08)',fill:true,tension:.4,pointRadius:0,borderWidth:2.5}]},
        options:{...chartOpts(''),plugins:{...chartOpts('').plugins,legend:{display:false}}}
    });

    const buyPerDay=[], sellPerDay=[];
    days.forEach(label=>{
        buyPerDay.push(TRANSACTIONS.filter(t=>new Date(t.time).toLocaleDateString('id-ID',{day:'2-digit',month:'short'})===label&&t.type==='BUY').length);
        sellPerDay.push(TRANSACTIONS.filter(t=>new Date(t.time).toLocaleDateString('id-ID',{day:'2-digit',month:'short'})===label&&t.type==='SELL').length);
    });
    destroyChart('chartBuySell');
    chartInstances['chartBuySell']=new Chart(document.getElementById('chartBuySell'),{
        type:'bar',
        data:{labels:days,datasets:[{label:'BUY',data:buyPerDay,backgroundColor:'rgba(16,185,129,0.6)',borderRadius:3},{label:'SELL',data:sellPerDay,backgroundColor:'rgba(239,68,68,0.6)',borderRadius:3}]},
      options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'top',labels:{color:chartPalette().tick,font:{size:11}}}},scales:{x:{grid:{display:false},ticks:{color:chartPalette().tick,font:{size:9},maxRotation:0}},y:{grid:{color:chartPalette().grid},ticks:{color:chartPalette().tick,font:{size:10},stepSize:1}}}}
    });
}

/* ============================================================
   ANNOUNCEMENT & AUDIT LOGS
   ============================================================ */
async function loadAnnouncementAdmin() {
  try {
    const data = await apiFetch('/announcement');
    document.getElementById('ancMessage').value = data.message || '';
    document.getElementById('ancType').value    = data.type || 'info';
    const sw = document.getElementById('swAncEnabled');
    if (data.enabled) sw.classList.add('on'); else sw.classList.remove('on');
    updateAncPreview();
  } catch(e) {}
}

async function saveAnnouncement() {
  const message = document.getElementById('ancMessage').value.trim();
  const type    = document.getElementById('ancType').value;
  const enabled = document.getElementById('swAncEnabled').classList.contains('on');

  if (!message) { toast('Teks pengumuman tidak boleh kosong', 'error'); return; }

  try {
    await apiFetch('/announcement', {
      method: 'POST',
      body: JSON.stringify({ message, type, enabled })
    });
    toast('📢 Broadcast banner berhasil diperbarui!', 'success');
  } catch(e) { toast(e.message, 'error'); }
}

function updateAncPreview() {
  const msg = document.getElementById('ancMessage').value || 'Tulis pengumuman...';
  document.getElementById('ancPreviewText').textContent = msg;
}

async function loadAuditLogs() {
  try {
    const data = await apiFetch('/logs');
    const tbody = document.getElementById('auditLogTbody');
    const logs = data.logs || [];

    if (!logs.length) {
      tbody.innerHTML = '<tr><td colspan="5" class="text-center c-muted py-3">Belum ada data audit log.</td></tr>';
      return;
    }

    tbody.innerHTML = logs.map(l => `
      <tr>
        <td>#${l.id}</td>
        <td><strong>${l.user}</strong></td>
        <td>${l.action}</td>
        <td><span class="badge-status badge-active" style="font-family:'JetBrains Mono',monospace;">${l.ip}</span></td>
        <td class="c-muted">${new Date(l.time).toLocaleTimeString('id-ID')} WIB</td>
      </tr>
    `).join('');
  } catch(e) {}
}

/* ============================================================
   SETTINGS
   ============================================================ */
async function saveSettings() {
    const payload = {
        trading_hours:   document.getElementById('togTradingHours').classList.contains('on') ? 1 : 0,
        weekend_trading: document.getElementById('togWeekend').classList.contains('on') ? 1 : 0,
        maintenance:     document.getElementById('togMaintenance').classList.contains('on') ? 1 : 0,
        min_lot:         parseInt(document.getElementById('setMinLot').value)||1,
        max_lot:         parseInt(document.getElementById('setMaxLot').value)||100,
        commission:      parseFloat(document.getElementById('setCommission').value)||0.15,
    };
    try {
        await apiFetch('/settings',{method:'POST',body:JSON.stringify(payload)});
        toast(t('saveSettingsSuccess'),'success');
    } catch(e) { toast(e.message,'error'); }
}

/* ============================================================
   CHART HELPERS
   ============================================================ */
function chartOpts(prefix) {
  const cp = chartPalette();
    return {
        responsive:true, maintainAspectRatio:false,
        plugins:{
            legend:{display:false},
            tooltip:{
        backgroundColor:cp.tooltipBg,titleColor:cp.tooltipText,bodyColor:cp.tooltipText,
        borderColor:cp.tooltipBorder,borderWidth:1,padding:10,cornerRadius:8,
                callbacks:{label:ctx=>(prefix?prefix+' ':'')+Math.round(ctx.raw).toLocaleString('id-ID')}
            }
        },
        scales:{
      x:{grid:{color:cp.grid},ticks:{color:cp.tick,font:{size:10},maxRotation:0}},
      y:{grid:{color:cp.grid},ticks:{color:cp.tick,font:{size:10},
                callback:v=>prefix?(v>=1e9?(v/1e9).toFixed(1)+'M':v>=1e6?(v/1e6).toFixed(0)+'Jt':v>=1e3?(v/1e3).toFixed(0)+'Rb':v):v
            }}
        }
    };
}

function destroyChart(id) {
    if(chartInstances[id]){ chartInstances[id].destroy(); delete chartInstances[id]; }
}

/* ============================================================
   CLOCK + INIT
   ============================================================ */
function updateClock() {
    const now = new Date();
    document.getElementById('clockDisplay').textContent =
        now.toLocaleDateString('id-ID',{weekday:'short',day:'numeric',month:'short'})+' — '+now.toLocaleTimeString('id-ID');
}

document.addEventListener('DOMContentLoaded',()=>{
  initializeLanguage();
  initializeTheme();
    loadData();
    fetchPrice();
    updateClock();
    setInterval(updateClock, 1000);
    setInterval(fetchPrice,  5000);
    setInterval(loadData,    30000);
});
</script>
</body>
</html>