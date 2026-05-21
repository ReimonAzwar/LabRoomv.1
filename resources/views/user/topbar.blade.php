<div class="topbar">
  <a href="#" class="logo">
    <div class="logo-emblem">
      <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="3"/><path d="M8 21h8M12 17v4"/></svg>
    </div>
    <div class="logo-texts">
      <div class="logo-main">Lab<span>Room</span></div>
      <div class="logo-sub">Sistem Pemesanan Ruangan</div>
    </div>
  </a>
  <div class="topbar-right">
    <a href="#" class="nav-link">Beranda</a>
    <div class="topbar-divider"></div>
    <a href="#reservation-section" class="nav-link">Reservasi Ruangan</a>
    <div class="topbar-divider"></div>
    <button onclick="openStatusModal()" class="nav-link" style="background:var(--gold-bg);color:var(--gold);border:1px solid var(--border);font-weight:700;display:flex;align-items:center;gap:6px;padding:6px 14px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:14px;height:14px"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Riwayat Reservasi
    </button>
    <button onclick="refreshUserData(this)" class="nav-link" title="Segarkan Data" style="padding:6px 10px;background:var(--gold-bg);border:1px solid var(--border);color:var(--gold)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:16px;height:16px"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
    </button>
  </div>
</div>