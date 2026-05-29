<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sisiru — Admin Panel | Universitas Tanjungpura</title>
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/style.css') }}?v=1780000001">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

{{--
  =====================================================================
  ROLE-BASED UNIFIED ADMIN PANEL — ARCHITECTURE NOTES
  =====================================================================

  Dua peran yang didukung:
    • 'admin'       → Admin Biasa  : kelola ruangan, status, fasilitas
    • 'super_admin' → Super Admin  : semua menu Admin + "Kelola Admin"

  Cara kerja:
  1. Backend merender variabel Blade:  $adminRole  ('admin' | 'super_admin')
                                        $adminUser  (object/array nama user)
  2. JS membaca data-role dari <body> → menyembunyikan / menampilkan
     elemen yang punya atribut [data-super-only] secara otomatis.
  3. Semua proteksi sejati TETAP di backend (middleware / Gate / Policy).
     Semua yang di sini hanya UI convenience — bukan keamanan.

  Untuk menambah menu Super Admin baru:
    → Tambahkan atribut  data-super-only="1"  ke elemen yang diinginkan.
  =====================================================================
--}}


</head>

{{-- data-role dibaca oleh JS untuk show/hide elemen Super Admin --}}
<body data-role="{{ $adminRole ?? 'admin' }}">

<!-- ================================================================
     LOGIN PAGE
     ================================================================ -->
<div id="login-page">
  <div class="lbg"></div><div class="lbg"></div><div class="lgrid"></div>
  <div class="lbox">
    <div class="lcard">
      <div class="luniv">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        Universitas Tanjungpura
      </div>
      <div class="llogo">
        <div class="lem">
          <img src="/images/logo_untan.png" alt="Untan" style="width:100%;height:100%;object-fit:contain">
        </div>
        <div class="ltxt">Sisi<span>ru</span></div>
      </div>
      <div class="lsub">Portal Admin — Sistem Reservasi Ruangan Lab Terpadu</div>
      <div class="ldiv"></div>
      <div class="lf">
        <label class="llbl">Username</label>
        <div class="lfw"><input class="linp" id="inp-user" type="text" placeholder="Masukkan username Anda" onkeydown="if(event.key==='Enter')document.getElementById('inp-pw').focus()"/></div>
      </div>
      <div class="lf">
        <label class="llbl">Password</label>
        <div class="lfw">
          <input class="linp" id="inp-pw" type="password" placeholder="Masukkan password Anda" onkeydown="if(event.key==='Enter')doLogin()" style="padding-right:42px"/>
          <button class="tpw" onclick="togglePw()" type="button">
            <svg id="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            <svg id="eye-on" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      <div class="lerr" id="lerr">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span id="lerr-txt">Username atau password salah.</span>
      </div>
      <button class="lbtn" id="lbtn" onclick="doLogin()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Masuk ke Dashboard
      </button>
    </div>
  </div>
</div>

<!-- ================================================================
     ADMIN PAGE
     ================================================================ -->
<div id="admin-page">

  <!-- ── Institution bar ─────────────────────────────────────────── -->
  <div class="inst-bar">
    <span style="display:flex;align-items:center;gap:6px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:13px;height:13px"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/></svg>
      Universitas Tanjungpura — Laboratorium Terpadu Fakultas Teknik
    </span>
    <span><span class="idot"></span>Panel Admin Aktif</span>
  </div>

  <!-- ── Top bar ──────────────────────────────────────────────────── -->
  <div class="topbar">
    <div style="display:flex;align-items:center">
      <button class="hamburger" id="btn-hamburger" onclick="toggleSidebar()" aria-label="Menu navigasi">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div class="logo">
        <div class="lem2"><img src="/images/logo_untan.png" alt="Untan" style="width:100%;height:100%;object-fit:contain"></div>
        <div class="ltxts">
          <div class="lmain">Sisi<span>ru</span></div>
          <div class="abadge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>Admin Panel</div>
        </div>
      </div>
    </div>
    <div class="tr">
      <div class="uchip">
        <div class="uav">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div style="display:flex;flex-direction:column;gap:2px;line-height:1">
          <span class="uname" id="logged-user">—</span>
          {{-- Role badge: Blade merender class & teks sesuai role --}}
          <span class="role-badge {{ $adminRole === 'super_admin' ? 'super' : 'admin' }}" id="role-badge-topbar">
            @if($adminRole === 'super_admin')
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:9px;height:9px"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              Super Admin
            @else
              Admin
            @endif
          </span>
        </div>
      </div>
      {{-- Tombol Ganti Profil — hanya tampil untuk Super Admin --}}
      @if($adminRole === 'super_admin')
      <button class="bsm" onclick="openSaProfileModal()" title="Ganti username / password"
              style="background:var(--gold-bg,#fdf6e3);color:var(--gold2,#b8860b);border:1px solid rgba(184,134,11,0.25);gap:5px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:13px;height:13px"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M16 3.13a4 4 0 010 7.75" style="display:none"/></svg>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:13px;height:13px"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Ganti Profil
      </button>
      @endif
      <button class="bsm danger" onclick="doLogout()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Keluar
      </button>
    </div>
  </div>

  <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar(false)"></div>

  <!-- ── Body: sidebar + main ────────────────────────────────────── -->
  <div class="abody">

    <!-- ══ SIDEBAR ═══════════════════════════════════════════════════ -->
    <div class="aside">

      {{-- ── Menu Utama ── --}}
      <div class="asl">
        <div class="asll">Menu Utama</div>
        <div class="asi active" id="nav-dashboard" onclick="navTo('dashboard',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard
        </div>
      </div>

      <div class="adiv"></div>

      {{-- ── Reservasi ── --}}
      <div class="asl">
        <div class="asll">Reservasi</div>
        <div class="asi" id="nav-all" onclick="navTo('all',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Semua<span class="ab gold" id="a-total">0</span>
        </div>
        <div class="asi" id="nav-pending" onclick="navTo('pending',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Menunggu<span class="ab red" id="a-pending">0</span>
        </div>
        <div class="asi" id="nav-approved" onclick="navTo('approved',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
          Disetujui<span class="ab green" id="a-approved">0</span>
        </div>
        <div class="asi" id="nav-rejected" onclick="navTo('rejected',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Ditolak<span class="ab dim" id="a-rejected">0</span>
        </div>
      </div>

      <div class="adiv"></div>

      {{-- ── Ruangan ── --}}
      <div class="asl">
        <div class="asll">Ruangan</div>
        <div id="aside-rooms"></div>
      </div>

      {{-- ════════════════════════════════════════════════════════════
           SUPER ADMIN ONLY — disembunyikan via CSS, diungkap oleh JS
           ════════════════════════════════════════════════════════════ --}}
      <div class="adiv" data-super-only="1"></div>

      <div class="asl" data-super-only="1">
        <div class="asll" style="display:flex;align-items:center;gap:5px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px;color:#c8a84b"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          Super Admin
        </div>

        {{-- Kelola Admin --}}
        <div class="asi" id="nav-manage-admins" data-super-only="1" onclick="navTo('manage-admins',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
          Kelola Admin
          <span class="ab gold" id="a-admins">0</span>
        </div>

        {{-- Statistik & Tren --}}
        <div class="asi" id="nav-stats" data-super-only="1" onclick="navTo('stats',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          Statistik &amp; Tren
        </div>

        {{-- Log Aktivitas --}}
        <div class="asi" id="nav-activity-logs" data-super-only="1" onclick="navTo('activity-logs',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Log Aktivitas
        </div>

        {{-- Ekspor Laporan --}}
        <div class="asi" id="nav-reports" data-super-only="1" onclick="navTo('reports',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Ekspor Laporan
        </div>

        {{-- Pengaturan Sistem --}}
        <div class="asi" id="nav-settings" data-super-only="1" onclick="navTo('settings',this)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
          Pengaturan Sistem
        </div>
      </div>

    </div><!-- /aside -->

    <!-- ══ MAIN CONTENT ════════════════════════════════════════════ -->
    <div class="main">

      {{-- ── Dashboard ──────────────────────────────────────────── --}}
      <div class="psec active" id="sec-dashboard">
        <div class="ph">
          <div>
            <div class="pt">Dashboard</div>
            <div class="ps">Ringkasan pemesanan dan kelola reservasi terbaru</div>
          </div>
          <div class="phact">
            <button class="bsec" onclick="refreshAdmin(this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Segarkan
            </button>
          </div>
        </div>
        <div class="stats">
          <div class="stat" onclick="navToFilter('all')"><div class="slbl">Total Pemesanan</div><div class="sval" id="s-total">0</div><div class="ssub">Semua waktu</div><div class="sico" style="--sib:var(--gold-bg)"><svg viewBox="0 0 24 24" fill="none" stroke="var(--gold2)" stroke-width="1.5" stroke-linecap="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div></div>
          <div class="stat" onclick="navToFilter('pending')"><div class="slbl">Menunggu</div><div class="sval amber" id="s-pending">0</div><div class="ssub">Perlu ditinjau</div><div class="sico" style="--sib:var(--amber-bg)"><svg viewBox="0 0 24 24" fill="none" stroke="var(--amber)" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div></div>
          <div class="stat" onclick="navToFilter('approved')"><div class="slbl">Disetujui</div><div class="sval green" id="s-approved">0</div><div class="ssub">Total disetujui</div><div class="sico" style="--sib:var(--green-bg)"><svg viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg></div></div>
          <div class="stat" onclick="navToFilter('rejected')"><div class="slbl">Ditolak</div><div class="sval red" id="s-rejected">0</div><div class="ssub">Total ditolak</div><div class="sico" style="--sib:var(--red-bg)"><svg viewBox="0 0 24 24" fill="none" stroke="var(--red)" stroke-width="1.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div></div>
        </div>
        <div style="margin-bottom:24px">
          <div class="stitle"><div class="stitle-l">Status &amp; Fasilitas Ruangan</div></div>
          <div id="room-panel-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(290px,1fr));gap:10px"></div>
        </div>
        <div>
          <div class="stitle">
            <div class="stitle-l">Reservasi Terbaru</div>
            <button class="bsec" onclick="navToFilter('all')" style="font-size:11.5px;padding:4px 12px">Lihat Semua →</button>
          </div>
          <div class="fbar" id="dash-fbar">
            <button class="fb active" id="df-all" onclick="setDashFilter('all',this)">Semua <span class="fc" id="df-cnt-all">0</span></button>
            <button class="fb" id="df-pending" onclick="setDashFilter('pending',this)">⏳ Menunggu <span class="fc" id="df-cnt-pending">0</span></button>
            <button class="fb" id="df-approved" onclick="setDashFilter('approved',this)">✓ Disetujui <span class="fc" id="df-cnt-approved">0</span></button>
            <button class="fb" id="df-rejected" onclick="setDashFilter('rejected',this)">✕ Ditolak <span class="fc" id="df-cnt-rejected">0</span></button>
            <input class="fsc" placeholder="Cari nama, ruangan..." oninput="setDashSearch(this.value)" style="max-width:200px"/>
          </div>
          <div class="btbl">
            <div class="thd"><div>Nama Pemohon</div><div class="col-inst">Instansi / Asal</div><div>Ruangan</div><div class="col-time">Tanggal &amp; Waktu</div><div>Status</div><div>Aksi</div></div>
            <div id="dash-blist"></div>
          </div>
        </div>
      </div>

      {{-- ── Semua Pemesanan ─────────────────────────────────────── --}}
      <div class="psec" id="sec-list">
        <div class="ph">
          <div>
            <div class="pt" id="list-title">Semua Pemesanan</div>
            <div class="ps" id="list-sub">Daftar seluruh pemesanan ruangan</div>
          </div>
          <div class="phact">
            <button class="bsec" onclick="refreshAdmin(this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Segarkan
            </button>
          </div>
        </div>
        <div class="fbar">
          <button class="fb active" id="lf-all" onclick="setListFilter('all',this)">Semua <span class="fc" id="lf-cnt-all">0</span></button>
          <button class="fb" id="lf-pending" onclick="setListFilter('pending',this)">⏳ Menunggu <span class="fc" id="lf-cnt-pending">0</span></button>
          <button class="fb" id="lf-approved" onclick="setListFilter('approved',this)">✓ Disetujui <span class="fc" id="lf-cnt-approved">0</span></button>
          <button class="fb" id="lf-rejected" onclick="setListFilter('rejected',this)">✕ Ditolak <span class="fc" id="lf-cnt-rejected">0</span></button>
          <input class="fsc" placeholder="Cari nama, ruangan, instansi..." oninput="setListSearch(this.value)"/>
        </div>
        <div class="btbl">
          <div class="thd"><div>Nama Pemohon</div><div class="col-inst">Instansi / Asal</div><div>Ruangan</div><div class="col-time">Tanggal &amp; Waktu</div><div>Status</div><div>Aksi</div></div>
          <div id="list-blist"></div>
        </div>
      </div>

      {{-- ══════════════════════════════════════════════════════════
           SUPER ADMIN SECTION: Kelola Admin
           Hanya di-render saat role === super_admin (Blade + CSS guard)
           ══════════════════════════════════════════════════════════ --}}
      @if($adminRole === 'super_admin')
      <div class="psec" id="sec-manage-admins">
        <div class="ph">
          <div>
            <div class="pt" style="display:flex;align-items:center;gap:8px">
              Kelola Admin
              <span class="role-badge super" style="font-size:9px">Super Admin</span>
            </div>
            <div class="ps">Tambah, edit, atau hapus akun Admin yang mengelola sistem</div>
          </div>
          <div class="phact">
            <button class="bsec" onclick="refreshAdmins(this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Segarkan
            </button>
            <button class="bsm" onclick="openAddAdminModal()" style="background:var(--navy,#1a2a4a);color:#fff;border:none">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Tambah Admin
            </button>
          </div>
        </div>

        {{-- Filter / search bar --}}
        <div class="fbar" style="margin-bottom:16px">
          <input class="fsc" id="admin-search" placeholder="Cari nama atau username admin..." oninput="filterAdminList(this.value)" style="max-width:300px"/>
          <span style="font-size:12px;color:#8fa0bc;margin-left:auto;align-self:center">
            Total: <strong id="admin-count">0</strong> admin
          </span>
        </div>

        <div id="admin-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px"></div>
        <div id="admin-grid-empty" style="display:none;text-align:center;padding:40px;color:var(--text3);background:var(--white);border-radius:12px;border:1px dashed var(--border)">
          Tidak ada data admin ditemukan.
        </div>
      </div>

      {{-- ── Statistik & Tren ────────────────────────────────────────── --}}
      <div class="psec" id="sec-stats">
        <div class="ph">
          <div>
            <div class="pt" style="display:flex;align-items:center;gap:8px">
              Statistik &amp; Tren
              <span class="role-badge super" style="font-size:9px">Super Admin</span>
            </div>
            <div class="ps">Analisis performa persetujuan admin dan utilisasi laboratorium</div>
          </div>
          <div class="phact">
            <button class="bsec" onclick="loadStatsData(this)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Segarkan
            </button>
          </div>
        </div>

        {{-- Mini Cards Grid --}}
        <div class="stats" style="margin-bottom:24px">
          <div class="stat" style="animation:none"><div class="slbl">Approval Rate</div><div class="sval green" id="stat-approval-rate">0%</div><div class="ssub">Total persetujuan</div><div class="sico" style="--sib:var(--green-bg)"><svg viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="1.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg></div></div>
          <div class="stat" style="animation:none"><div class="slbl">Ruangan Favorit</div><div class="sval gold" id="stat-favorite-room" style="font-size:18px;line-height:2.2;font-family:var(--font);font-weight:700">—</div><div class="ssub">Pemesanan terbanyak</div><div class="sico" style="--sib:var(--gold-bg)"><svg viewBox="0 0 24 24" fill="none" stroke="var(--gold2)" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="3"/><path d="M8 21h8M12 17v4"/></svg></div></div>
          <div class="stat" style="animation:none"><div class="slbl">Instansi Teraktif</div><div class="sval amber" id="stat-favorite-inst" style="font-size:18px;line-height:2.2;font-family:var(--font);font-weight:700">—</div><div class="ssub">Pemohon tersering</div><div class="sico" style="--sib:var(--amber-bg)"><svg viewBox="0 0 24 24" fill="none" stroke="var(--amber)" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div></div>
          <div class="stat" style="animation:none"><div class="slbl">Rata-rata Durasi</div><div class="sval" style="font-size:24px;line-height:1.7">2.0 Jam</div><div class="ssub">Per sesi reservasi</div><div class="sico" style="--sib:var(--gold-bg)"><svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div></div>
        </div>

        {{-- Charts Grid --}}
        <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:20px;margin-bottom:24px">
          <div class="btbl" style="padding:20px;background:var(--white)">
            <div class="stitle"><div class="stitle-l">Tren Pemesanan Bulanan</div></div>
            <div style="height:250px;position:relative"><canvas id="chart-monthly-trends"></canvas></div>
          </div>
          <div class="btbl" style="padding:20px;background:var(--white)">
            <div class="stitle"><div class="stitle-l">Utilisasi Ruangan</div></div>
            <div style="height:250px;position:relative"><canvas id="chart-room-breakdown"></canvas></div>
          </div>
        </div>

        {{-- Rates Table --}}
        <div class="btbl">
          <div class="thd" style="grid-template-columns:1.5fr 1fr 1fr 1.5fr">
            <div>Nama Admin</div>
            <div>Persetujuan</div>
            <div>Penolakan</div>
            <div>Approval Rate</div>
          </div>
          <div id="stats-admin-list">
            {{-- Diisi JS --}}
          </div>
        </div>
      </div>

      {{-- ── Log Aktivitas ───────────────────────────────────────────── --}}
      <div class="psec" id="sec-activity-logs">
        <div class="ph">
          <div>
            <div class="pt" style="display:flex;align-items:center;gap:8px">
              Log Aktivitas
              <span class="role-badge super" style="font-size:9px">Super Admin</span>
            </div>
            <div class="ps">Audit trail seluruh tindakan admin yang tercatat di dalam sistem</div>
          </div>
          <div class="phact">
            <button class="bsec" onclick="loadActivityLogs(1)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
              Segarkan
            </button>
          </div>
        </div>

        {{-- Filter Bar --}}
        <div class="fbar" style="margin-bottom:16px;padding:16px;background:var(--white);border-radius:14px;box-shadow:var(--shadow);gap:12px">
          <input class="fsc" id="log-search" placeholder="Cari aksi atau detail..." style="max-width:250px" />
          
          <select class="fsc" id="log-admin-filter" style="max-width:180px">
            <option value="">Semua Admin</option>
          </select>

          <select class="fsc" id="log-action-filter" style="max-width:180px">
            <option value="">Semua Aksi</option>
            <option value="approve_booking">Menyetujui Pemesanan</option>
            <option value="reject_booking">Menolak Pemesanan</option>
            <option value="update_room">Mengubah Ruangan</option>
            <option value="update_booking">Mengubah Reservasi</option>
            <option value="delete_booking">Menghapus Reservasi</option>
            <option value="update_settings">Mengubah Pengaturan</option>
          </select>

          <div style="display:flex;align-items:center;gap:6px">
            <input type="date" id="log-start-date" class="fsc" style="max-width:140px;padding:6px 10px" />
            <span style="font-size:12px;color:var(--text3)">s/d</span>
            <input type="date" id="log-end-date" class="fsc" style="max-width:140px;padding:6px 10px" />
          </div>

          <button class="bprim" onclick="loadActivityLogs(1)" style="padding:8px 16px;font-size:12.5px">Cari</button>
          <button class="bsec" onclick="resetLogFilters()" style="padding:8px 16px;font-size:12.5px">Reset</button>
        </div>

        {{-- Logs Table --}}
        <div class="btbl">
          <div class="thd" style="grid-template-columns:1.2fr 1fr 1.3fr 2.5fr .8fr">
            <div>Waktu Kejadian</div>
            <div>Pelaku (Admin)</div>
            <div>Aksi</div>
            <div>Rincian Aktivitas</div>
            <div>IP Address</div>
          </div>
          <div id="log-list">
            {{-- Diisi JS --}}
          </div>
        </div>

        {{-- Pagination Footer --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;padding:0 4px">
          <span style="font-size:12.5px;color:var(--text3)" id="log-pagination-info">
            Menampilkan 0 - 0 dari 0 log
          </span>
          <div style="display:flex;gap:6px">
            <button class="bsec" id="log-prev-btn" onclick="prevLogPage()" style="padding:6px 12px;font-size:12px">Sebelumnya</button>
            <button class="bsec" id="log-next-btn" onclick="nextLogPage()" style="padding:6px 12px;font-size:12px">Selanjutnya</button>
          </div>
        </div>
      </div>

      {{-- ── Ekspor Laporan ──────────────────────────────────────────── --}}
      <div class="psec" id="sec-reports">
        <div class="ph">
          <div>
            <div class="pt" style="display:flex;align-items:center;gap:8px">
              Ekspor Laporan
              <span class="role-badge super" style="font-size:9px">Super Admin</span>
            </div>
            <div class="ps">Unduh data pemesanan laboratorium lengkap sesuai kebutuhan audit</div>
          </div>
        </div>

        <div style="display:flex;justify-content:center;align-items:center;min-height:calc(100vh - 220px)">
          <div style="width:100%;max-width:600px" class="btbl">
            <div style="padding:28px;background:var(--white)">
            <div class="stitle" style="margin-bottom:20px"><div class="stitle-l">Konfigurasi Ekspor Laporan</div></div>
            
            <div class="mf" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
              <div>
                <div class="ml">Mulai Tanggal</div>
                <input type="date" id="rep-start-date" class="fci" />
              </div>
              <div>
                <div class="ml">Sampai Tanggal</div>
                <input type="date" id="rep-end-date" class="fci" />
              </div>
            </div>

            <div class="mf">
              <div class="ml">Pilih Ruangan</div>
              <select id="rep-room" class="fci">
                <option value="">Semua Ruangan</option>
              </select>
            </div>

            <div class="mf">
              <div class="ml">Status Pemesanan</div>
              <select id="rep-status" class="fci">
                <option value="">Semua Status</option>
                <option value="pending">Menunggu</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
              </select>
            </div>

            <div style="height:1px;background:var(--cream2);margin:24px 0"></div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
              <button class="bprim" onclick="downloadCsvReport()" style="padding:12px;justify-content:center">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:16px;height:16px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Unduh CSV / Excel
              </button>
              <button class="bsec" onclick="printPdfReport()" style="padding:12px;justify-content:center;background:var(--cream)">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:16px;height:16px"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Cetak / Simpan PDF
              </button>
            </div>
          </div>
        </div>
        </div>
      </div>

      {{-- ── Pengaturan Sistem ────────────────────────────────────────── --}}
      <div class="psec" id="sec-settings">
        <div class="ph">
          <div>
            <div class="pt" style="display:flex;align-items:center;gap:8px">
              Pengaturan Sistem
              <span class="role-badge super" style="font-size:9px">Super Admin</span>
            </div>
            <div class="ps">Kelola setelan inti operasional sistem LabRoom Untan</div>
          </div>
        </div>

        <div style="display:flex;justify-content:center;align-items:center;min-height:calc(100vh - 220px)">
          <div style="width:100%;max-width:600px" class="btbl">
            <div style="padding:28px;background:var(--white)">
            <div class="stitle" style="margin-bottom:20px"><div class="stitle-l">Konfigurasi Global</div></div>

            <div class="mf">
              <div class="ml">Nama Aplikasi / Portal</div>
              <input type="text" id="set-system-name" value="Sisiru — Portal Reservasi Terpadu" class="fci" />
            </div>

            <div class="mf" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
              <div>
                <div class="ml">Jam Operasional Mulai</div>
                <input type="time" id="set-op-start" value="07:00" class="fci" />
              </div>
              <div>
                <div class="ml">Jam Operasional Selesai</div>
                <input type="time" id="set-op-end" value="17:00" class="fci" />
              </div>
            </div>

            <div class="mf" style="margin-top:18px">
              <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                  <div class="ml" style="margin-bottom:2px">Notifikasi WhatsApp</div>
                  <div style="font-size:12px;color:var(--text3)">Kirim pesan otomatis via Gateway Fonnte saat persetujuan</div>
                </div>
                <label class="switch-container" style="position:relative;display:inline-block;width:44px;height:24px">
                  <input type="checkbox" id="set-wa-toggle" checked style="opacity:0;width:0;height:0" onchange="this.nextElementSibling.style.background = this.checked ? 'var(--green)' : '#cbd5e1'" />
                  <span class="slider" style="position:absolute;cursor:pointer;inset:0;background-color:#cbd5e1;transition:.3s;border-radius:24px"></span>
                </label>
              </div>
            </div>

            <div style="height:1px;background:var(--cream2);margin:24px 0"></div>
            
            <div class="stitle" style="margin-bottom:20px"><div class="stitle-l">Informasi Publik</div></div>
            
            <div class="mf" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
              <div>
                <div class="ml">WA / Telepon Kontak</div>
                <input type="text" id="set-contact-phone" class="fci" placeholder="(0561) 123456" />
              </div>
              <div>
                <div class="ml">Email Kontak</div>
                <input type="email" id="set-contact-email" class="fci" placeholder="kampus@polnep.ac.id" />
              </div>
            </div>

            <div class="mf">
              <div class="ml">Alamat Lengkap</div>
              <textarea id="set-contact-address" class="fci" rows="2" style="resize:vertical"></textarea>
            </div>

            <div class="mf">
              <div class="ml">Informasi Kapasitas Lab & Ruangan</div>
              <input type="text" id="set-lab-capacity" class="fci" placeholder="Lab: 20-30 orang, Seminar: 60 orang" />
            </div>

            <div class="mf">
              <div class="ml">URL Embed Google Maps (src="...")</div>
              <textarea id="set-gmaps-url" class="fci" rows="2" style="resize:vertical" placeholder="https://www.google.com/maps/embed?..."></textarea>
            </div>

            <div style="height:1px;background:var(--cream2);margin:24px 0"></div>

            <button class="bprim" id="settings-save-btn" onclick="saveSystemSettings()" style="width:100%;padding:12px;justify-content:center">
              Simpan Pengaturan
            </button>
          </div>
        </div>
        </div>
      </div>
      @endif
      {{-- /sec-manage-admins --}}

    </div><!-- /main -->
  </div><!-- /abody -->

  <!-- ── Footer ──────────────────────────────────────────────────── -->
  <div class="afoot">
    <div class="afoot-content">
      <span>Laboratorium Keilmuan Dasar Universitas Tanjungpura © 2026 All Rights Reserved</span>
    </div>
    <button class="scroll-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Kembali ke atas">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="18 15 12 9 6 15"/></svg>
    </button>
  </div>
</div><!-- /admin-page -->

<!-- ================================================================
     MODALS (shared + role-specific)
     ================================================================ -->
@include('admin.modals')
{{-- Modal Tambah/Edit Admin — hanya dirender untuk Super Admin --}}
@if($adminRole === 'super_admin')
@include('admin.modals_superadmin')
@endif

<!-- ================================================================
     SCRIPTS
     ================================================================ -->
<script>
/**
 * LabRoom — Admin Role Config
 * Dibaca oleh script.js untuk mengaktifkan/menonaktifkan fitur Super Admin.
 *
 * JANGAN simpan informasi sensitif di sini — ini hanya untuk UI.
 * Semua otorisasi HARUS dicek di backend.
 */
window.LABROOM_ADMIN = {
  role:     '{{ $adminRole ?? "admin" }}',        // 'admin' | 'super_admin'
  username: '{{ $adminUser->username ?? "" }}',
  name:     '{{ $adminUser->name ?? "" }}',
  isSuperAdmin: {{ $adminRole === 'super_admin' ? 'true' : 'false' }},
};
</script>

<script src="{{ asset('js/admin/script.js') }}?v=1780000001"></script>
</body>
</html>