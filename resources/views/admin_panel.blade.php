<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>LabRoom — Admin Panel | Universitas Tanjungpura</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/style.css') }}?v=1779328944">
</head>
<body>

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
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="3"/><path d="M8 21h8M12 17v4"/></svg>
        </div>
        <div class="ltxt">Lab<span>Room</span></div>
      </div>
      <div class="lsub">Portal Admin — Sistem Pemesanan Ruangan Lab Terpadu</div>
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

<div id="admin-page">
  <div class="inst-bar">
    <span style="display:flex;align-items:center;gap:6px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:13px;height:13px"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/></svg>
      Universitas Tanjungpura — Laboratorium Terpadu Fakultas Teknik
    </span>
    <span><span class="idot"></span>Panel Admin Aktif</span>
  </div>

  <div class="topbar">
    <div class="logo">
      <div class="lem2"><svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="3"/><path d="M8 21h8M12 17v4"/></svg></div>
      <div class="ltxts">
        <div class="lmain">Lab<span>Room</span></div>
        <div class="abadge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>Admin Panel</div>
      </div>
    </div>
    <div class="tr">
      <div class="uchip">
        <div class="uav"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <span class="uname" id="logged-user">—</span>
      </div>
      <button class="bsm danger" onclick="doLogout()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Keluar
      </button>
    </div>
  </div>

  <div class="abody">
    <div class="aside">
      <div class="asl"><div class="asll">Menu Utama</div>
        <div class="asi active" id="nav-dashboard" onclick="navTo('dashboard',this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard</div>
      </div>
      <div class="adiv"></div>
      <div class="asl"><div class="asll">Reservasi</div>
        <div class="asi" id="nav-all" onclick="navTo('all',this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Semua<span class="ab gold" id="a-total">0</span></div>
        <div class="asi" id="nav-pending" onclick="navTo('pending',this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Menunggu<span class="ab red" id="a-pending">0</span></div>
        <div class="asi" id="nav-approved" onclick="navTo('approved',this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>Disetujui<span class="ab green" id="a-approved">0</span></div>
        <div class="asi" id="nav-rejected" onclick="navTo('rejected',this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Ditolak<span class="ab dim" id="a-rejected">0</span></div>
      </div>
      <div class="adiv"></div>
      <div class="asl"><div class="asll">Ruangan</div><div id="aside-rooms"></div></div>
    </div>

    <div class="main">
      <div class="psec active" id="sec-dashboard">
        <div class="ph">
          <div><div class="pt">Dashboard</div><div class="ps">Ringkasan pemesanan dan kelola reservasi terbaru</div></div>
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
          <div class="stitle"><div class="stitle-l">Reservasi Terbaru</div><button class="bsec" onclick="navToFilter('all')" style="font-size:11.5px;padding:4px 12px">Lihat Semua →</button></div>
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

      <div class="psec" id="sec-list">
        <div class="ph">
          <div><div class="pt" id="list-title">Semua Pemesanan</div><div class="ps" id="list-sub">Daftar seluruh pemesanan ruangan</div></div>
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
    </div>
  </div>

  <div class="afoot">
    <span>© 2026 <strong style="color:rgba(200,168,75,.65)">LabRoom Admin</strong> — Universitas Tanjungpura</span>
    <span>Sistem Informasi Pemesanan Ruangan Lab Terpadu v2.0</span>
  </div>
</div>

<!-- MODALS -->
@include('admin.modals')
<script src="{{ asset('js/admin/script.js') }}?v=1779328944"></script>
</body>
</html>
