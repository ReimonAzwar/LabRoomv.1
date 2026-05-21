<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LabRoom — Sistem Pemesanan Ruangan Lab Terpadu | Universitas Tanjungpura</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/user/style.css') }}?v=1779328846">
</head>
<body>

<!-- INSTITUTIONAL TOP BAR -->
@include('user.inst_bar')
<!-- TOPBAR -->
@include('user.topbar')
<!-- HERO -->
@include('user.hero')
<!-- MAIN -->
<div class="main-layout">

  <!-- SIDEBAR -->
  <div class="sidebar">

    <!-- Room Status -->
    <div class="s-card anim-ready">
      <div class="s-card-header">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <div class="s-card-title">Status Ruangan Hari Ini</div>
      </div>
      <div class="s-card-body" id="room-status-list"></div>
    </div>

    <!-- Info -->
    <div class="s-card anim-ready">
      <div class="s-card-header">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div class="s-card-title">Informasi</div>
      </div>
      <div class="s-card-body">
        <div class="info-row">
          <div class="iicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
          <div class="itext"><strong>Jam Operasional</strong>Senin–Jumat, 07:00–17:00 WIB</div>
        </div>
        <div class="info-row">
          <div class="iicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 016.19 16a19.79 19.79 0 01-3.07-8.63A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg></div>
          <div class="itext"><strong>Kontak Admin</strong>(0561) 123-456 ext. 200</div>
        </div>
        <div class="info-row">
          <div class="iicon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div>
          <div class="itext"><strong>Kapasitas</strong>Lab: 20–30 orang · Seminar: 60 orang</div>
        </div>
      </div>
    </div>

    <!-- Notice -->
    <div class="notice-card anim-ready">
      <div class="notice-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 17H2a3 3 0 004-3V9a7 7 0 0114 0v5a3 3 0 004 3zM13.73 21a2 2 0 01-3.46 0"/></svg>
        Ketentuan Pemesanan
      </div>
      <div class="notice-item">Pengajuan minimal H-1 sebelum pemakaian</div>
      <div class="notice-item">Pemesanan berlaku setelah dikonfirmasi admin</div>
      <div class="notice-item">Pembatalan harap melapor sebelum H-1</div>
      <div class="notice-item">Ruangan harus ditinggalkan dalam kondisi bersih</div>
    </div>

  </div>

  <!-- MAIN COL -->
  <div class="main-col">
    <div class="form-panel" id="reservation-section">
      <div class="form-panel-header">
        <div class="fph-left">
          <div class="fph-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="3"/><path d="M8 21h8M12 17v4"/></svg>
          </div>
          <div>
            <div class="fph-title">Formulir Pemesanan Ruangan</div>
            <div class="fph-sub">
              <span class="fph-sdot"></span>
              Asisten LabBot siap membantu Anda
            </div>
          </div>
        </div>
        <div class="fph-step-indicator">
          <div class="step-dot active" id="step1"></div>
          <div class="step-dot" id="step2"></div>
          <div class="step-dot" id="step3"></div>
        </div>
      </div>

      <!-- BOT MESSAGES -->
      <div class="bot-messages" id="messages"></div>

    </div>
  </div>

</div>

<!-- ROOM CALENDAR POPUP -->
<div class="rc-overlay" id="rc-overlay">
  <div class="rc-modal">
    <div class="rc-modal-header">
      <div>
        <div class="rc-modal-title" id="rc-title">Jadwal Ruangan</div>
        <div class="rc-modal-sub" id="rc-sub">—</div>
      </div>
      <button onclick="closeRoomCal()" class="rc-close-btn">✕ Tutup</button>
    </div>
    <div class="rc-nav">
      <button onclick="rcNav(-1)" class="rc-nav-btn">&#8249;</button>
      <div id="rc-month-lbl" class="rc-month-lbl"></div>
      <button onclick="rcNav(1)" class="rc-nav-btn">&#8250;</button>
    </div>
    <div id="rc-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:3px;margin-bottom:12px"></div>
    <div class="rc-legend">
      <div class="rc-legend-item"><div class="rc-legend-dot" style="background:var(--green)"></div><span>Tersedia</span></div>
      <div class="rc-legend-item"><div class="rc-legend-dot" style="background:var(--gold)"></div><span>Ada reservasi</span></div>
    </div>
    <div id="rc-detail" style="display:none">
      <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:9px" id="rc-det-title"></div>
      <div id="rc-det-list"></div>
    </div>
  </div>
</div>

<!-- FOOTER -->
@include('user.footer')
<script src="{{ asset('js/user/script.js') }}?v=1779328846"></script>

<div class="rc-overlay" id="status-overlay">
  <div class="rc-modal" style="max-width:440px">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px">
      <div>
        <div class="rc-modal-title">Riwayat Reservasi</div>
        <div class="rc-modal-sub" id="history-sub">Menampilkan seluruh permohonan Anda</div>
      </div>
      <div style="display:flex;gap:8px">
        <button onclick="refreshUserData(this)" class="rc-close-btn" title="Refresh" style="padding:4px 8px;min-height:auto">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:14px;height:14px"><path d="M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
        </button>
        <button onclick="closeStatusModal()" class="rc-close-btn" style="padding:4px 10px;min-height:auto">✕</button>
      </div>
    </div>
    
    <div id="status-result" style="max-height:300px;overflow-y:auto;padding-right:4px;margin-bottom:18px"></div>

    <div style="background:var(--cream);padding:14px;border-radius:12px;border:1px dashed var(--border)">
      <div style="font-size:11px;font-weight:700;color:var(--text2);margin-bottom:8px;text-transform:uppercase">Cari Riwayat Lain</div>
      <div style="display:flex;gap:8px">
        <input class="fc-input" id="check-q" type="tel" placeholder="Masukkan ID atau No. WA" style="flex:1;font-size:12.5px" oninput="this.value=this.value.replace(/[^0-9]/g,'')" onkeydown="if(event.key==='Enter')doSearchHistory()"/>
        <button class="fc-submit" onclick="doSearchHistory()" style="width:auto;padding:0 15px;height:40px;margin-top:0;font-size:13px">Cari</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>
