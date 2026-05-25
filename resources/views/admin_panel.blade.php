<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sisiru — Admin Panel | Universitas Tanjungpura</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<<<<<<< HEAD
<link rel="stylesheet" href="{{ asset('css/admin/style.css') }}?v=1779328999">
</head>
<body>
=======
<link rel="stylesheet" href="{{ asset('css/admin/style.css') }}?v=1779328944">
>>>>>>> a4c04fcdd11ae237f2f63ceff654c19e8b8055da

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
    <div class="logo">
      <div class="lem2"><img src="/images/logo_untan.png" alt="Untan" style="width:100%;height:100%;object-fit:contain"></div>
      <div class="ltxts">
<<<<<<< HEAD
        <div class="lmain">Sisi<span>ru</span></div>
        <div class="abadge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>Admin Panel</div>
=======
        <div class="lmain">Lab<span>Room</span></div>
        <div class="abadge">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
          Admin Panel
        </div>
>>>>>>> a4c04fcdd11ae237f2f63ceff654c19e8b8055da
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
      <button class="bsm danger" onclick="doLogout()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Keluar
      </button>
    </div>
  </div>

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

        {{-- Placeholder: tambahkan menu Super Admin lain di sini --}}
        {{-- Contoh:
        <div class="asi" id="nav-audit-log" data-super-only="1" onclick="navTo('audit-log',this)">
          <svg ...>...</svg>
          Log Aktivitas
        </div>
        --}}
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

        {{-- Admin cards grid --}}
        <div class="kadmin-grid" id="admin-grid">
          {{-- Diisi oleh JS (fetchAdminList()) --}}
          <div class="kadmin-empty" id="admin-grid-empty" style="grid-column:1/-1">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            Belum ada akun admin terdaftar.
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
<<<<<<< HEAD
<script src="{{ asset('js/admin/script.js') }}?v=1779328999"></script>
=======

{{-- Modal Tambah/Edit Admin — hanya dirender untuk Super Admin --}}
@if($adminRole === 'super_admin')
@include('admin.modals_superadmin')
{{-- Atau jika modal digabung dalam satu file:
     @include('admin.modals', ['role' => 'super_admin']) --}}
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

<script src="{{ asset('js/admin/script.js') }}?v=1779328944"></script>

{{-- Script khusus Super Admin --}}
@if($adminRole === 'super_admin')

@endif

>>>>>>> a4c04fcdd11ae237f2f63ceff654c19e8b8055da
</body>
</html>