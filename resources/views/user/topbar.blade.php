<div class="topbar">
  <a href="/" class="logo">
    <div class="logo-emblem">
      <img src="/images/logo_untan.png" alt="Untan" style="width:100%;height:100%;object-fit:contain">
    </div>
    <div class="logo-texts">
      <div class="logo-main">Sisi<span>ru</span></div>
      <div class="logo-sub">SISTEM RESERVASI RUANGAN</div>
    </div>
  </a>
  
  <!-- Desktop Nav Links -->
  <div class="topbar-right desktop-nav">
    <a href="/" class="nav-link">Beranda</a>
    <div class="topbar-divider"></div>
    <a href="#reservation-section" class="nav-link">Reservasi Ruangan</a>
    <a href="https://navigasi-terpadu-upa.vercel.app/" class="nav-link" target="_blank" rel="noopener noreferrer">Profil Ruangan</a>
    <div class="topbar-divider"></div>
    <button onclick="openStatusModal()" class="nav-link nav-link-history">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:14px;height:14px"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Riwayat Reservasi
    </button>
    <button onclick="refreshUserData(this)" class="nav-link-refresh" title="Segarkan Data">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:14px;height:14px"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
    </button>
  </div>

  <!-- Mobile Nav Trigger -->
  <div class="topbar-right mobile-nav-trigger">
    <button onclick="refreshUserData(this)" class="nav-link-refresh" title="Segarkan Data" style="margin-right:8px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:14px;height:14px"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
    </button>
    <button class="user-hamburger" onclick="toggleUserMobileMenu()" aria-label="Menu">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
  </div>
</div>

<!-- Mobile Nav Drawer -->
<div class="user-mobile-menu" id="user-mobile-menu">
  <div class="umm-header">
    <div class="umm-logo">
      <img src="/images/logo_untan.png" alt="Untan" style="width:28px;height:28px;object-fit:contain">
      <span style="font-family:var(--display);font-weight:600;font-size:16px;color:var(--navy)">Sisi<span style="color:var(--gold)">ru</span></span>
    </div>
    <button class="umm-close" onclick="toggleUserMobileMenu(false)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:20px;height:20px"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="umm-body">
    <a href="/" class="umm-link" onclick="toggleUserMobileMenu(false)">Beranda</a>
    <a href="#reservation-section" class="umm-link" onclick="toggleUserMobileMenu(false)">Reservasi Ruangan</a>
    <a href="https://navigasi-terpadu-upa.vercel.app/" class="umm-link" target="_blank" rel="noopener noreferrer" onclick="toggleUserMobileMenu(false)">Profil Ruangan</a>
    <button onclick="toggleUserMobileMenu(false); openStatusModal()" class="umm-link umm-btn-history">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:14px;height:14px;margin-right:6px"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Riwayat Reservasi
    </button>
  </div>
</div>
<div class="user-mobile-overlay" id="user-mobile-overlay" onclick="toggleUserMobileMenu(false)"></div>