/* ═══════════════════════════════════════
   CONSTANTS & STORAGE & SECURITY
═══════════════════════════════════════ */
function escapeHTML(str) {
    if (!str) return '';
    return String(str).replace(/[&<>'"]/g, 
        tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        }[tag] || tag)
    );
}

let GLOBAL_ROOMS = [];
let GLOBAL_BOOKINGS = [];

function loadRooms(){ return GLOBAL_ROOMS; }
function loadBookings(){ return GLOBAL_BOOKINGS; }
function saveBookings(l){ /* Not used directly for save anymore */ }
function getRoomInfo(name){return loadRooms().find(r=>r.name===name)||null;}
function isRoomBlocked(name,date){
  const r=getRoomInfo(name);if(!r)return false;
  if(r.status==='available')return false;
  if(!r.closedUntil)return true;
  return date<=r.closedUntil;
}
function toMin(t){const[h,m]=t.split(':').map(Number);return h*60+m;}
function getConflicts(ruangan,tanggal,mulai,selesai,excludeId=null){
  if(!ruangan||!tanggal||!mulai||!selesai)return[];
  const s=toMin(mulai),e=toMin(selesai);
  if(s>=e)return[];
  return loadBookings().filter(b=>{
    if(b.id===excludeId)return false;
    if(b.ruangan!==ruangan||b.tanggal!==tanggal||b.status==='rejected')return false;
    const bs=toMin(b.jamMulai),be=toMin(b.jamSelesai);
    return s<be&&e>bs;
  });
}

/* ─── SIDEBAR ─── */
function renderSidebar(){
  const rooms=loadRooms();
  const today=new Date().toISOString().split('T')[0];
  const bookings=loadBookings();
  document.getElementById('room-status-list').innerHTML=rooms.map(r=>{
    let dot,label;
    if(r.status==='maintenance'){dot='pt';label='Maintenance';}
    else if(r.status==='closed'){dot='bs';label='Ditutup';}
    else{
      const now=new Date();
      const hhmm=String(now.getHours()).padStart(2,'0')+':'+String(now.getMinutes()).padStart(2,'0');
      const busyNow=bookings.some(b=>b.ruangan===r.name&&b.tanggal===today&&b.status==='approved'&&toMin(b.jamMulai)<=toMin(hhmm)&&toMin(hhmm)<toMin(b.jamSelesai));
      const hasToday=bookings.some(b=>b.ruangan===r.name&&b.tanggal===today&&b.status!=='rejected');
      dot=busyNow?'bs':hasToday?'pt':'av';
      label=busyNow?'Sedang Terpakai':hasToday?'Ada Reservasi':'Tersedia';
    }
    return `<div class="room-row" onclick="openRoomCal('${r.name}')" title="Lihat jadwal ${r.name}">
      <span class="rdot ${dot}"></span>
      <span class="rname">${r.name}</span>
      <span class="rstatus">${label}</span>
    </div>`;
  }).join('');
}

/* ─── ROOM CALENDAR ─── */
let rcRoom='',rcYear=0,rcMonth=0;
function openRoomCal(room){
  rcRoom=room;const now=new Date();rcYear=now.getFullYear();rcMonth=now.getMonth();
  const info=getRoomInfo(room);
  const stColor={available:'var(--green)',maintenance:'var(--amber)',closed:'var(--red)'};
  const stLbl={available:'Tersedia',maintenance:'Maintenance',closed:'Ditutup'};
  const st=info?.status||'available';
  const untilTxt=info?.closedUntil?` s/d ${info.closedUntil.split('-').reverse().join('/')}`:' ';
  document.getElementById('rc-title').textContent='Jadwal: '+room;
  document.getElementById('rc-sub').innerHTML=`<span style="color:${stColor[st]};font-weight:700">${stLbl[st]}${untilTxt}</span>${info?.fasilitas?` &nbsp;·&nbsp; ${info.fasilitas}`:''}`;
  renderRcGrid();
  document.getElementById('rc-overlay').classList.add('show');
}
function closeRoomCal(){document.getElementById('rc-overlay').classList.remove('show');}
function rcNav(d){
  rcMonth+=d;
  if(rcMonth<0){rcMonth=11;rcYear--;}
  if(rcMonth>11){rcMonth=0;rcYear++;}
  renderRcGrid();
}
function renderRcGrid(){
  const mn=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  document.getElementById('rc-month-lbl').textContent=mn[rcMonth]+' '+rcYear;
  const grid=document.getElementById('rc-grid');
  const days=['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
  let html=days.map(d=>`<div class="rc-head">${d}</div>`).join('');
  const first=new Date(rcYear,rcMonth,1).getDay();
  const dim=new Date(rcYear,rcMonth+1,0).getDate();
  const today=new Date().toISOString().split('T')[0];
  const bks=loadBookings().filter(b=>b.ruangan===rcRoom&&b.status!=='rejected');
  for(let i=0;i<first;i++) html+=`<div class="rc-day empty"></div>`;
  for(let d=1;d<=dim;d++){
    const ds=`${rcYear}-${String(rcMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const hasBks=bks.some(b=>b.tanggal===ds);
    const isToday=ds===today;
    let cls='rc-day';
    if(hasBks) cls+=' has-bookings';
    if(isToday) cls+=' today';
    const dot=hasBks?`<div class="rc-dot-row"><div class="rc-dot"></div></div>`:'';
    html+=`<div class="${cls}" onclick="rcShowDay('${ds}')"><div class="rc-day-num">${d}</div>${dot}</div>`;
  }
  grid.innerHTML=html;
  document.getElementById('rc-detail').style.display='none';
}
function rcShowDay(ds){
  const det=document.getElementById('rc-detail');
  const titEl=document.getElementById('rc-det-title');
  const listEl=document.getElementById('rc-det-list');
  const fmtDate=d=>{const[y,m,dd]=d.split('-');return `${dd}/${m}/${y}`;};
  titEl.textContent='Jadwal '+fmtDate(ds);
  const roomInfo=getRoomInfo(rcRoom);
  if(roomInfo&&roomInfo.status!=='available'&&isRoomBlocked(rcRoom,ds)){
    const lbl={maintenance:'🔧 Sedang Maintenance',closed:'🚫 Ruangan Ditutup'};
    const untilTxt=roomInfo.closedUntil?` sampai ${roomInfo.closedUntil.split('-').reverse().join('/')}`:' ';
    listEl.innerHTML=`<div style="font-size:12.5px;color:var(--red);padding:10px 12px;background:var(--red-bg);border-radius:8px;border:1px solid rgba(192,57,43,.2)">${lbl[roomInfo.status]||'Tidak Tersedia'}${untilTxt}</div>`;
    det.style.display='block';return;
  }
  const list=loadBookings().filter(b=>b.ruangan===rcRoom&&b.tanggal===ds&&b.status!=='rejected');
  if(!list.length){
    listEl.innerHTML=`<div style="font-size:12.5px;color:var(--green);padding:9px 12px;background:var(--green-bg);border-radius:8px;border:1px solid rgba(26,127,75,.2)">✓ Tidak ada reservasi — ruangan tersedia.</div>`;
    det.style.display='block';return;
  }
  list.sort((a,b)=>toMin(a.jamMulai)-toMin(b.jamMulai));
  const stC={approved:'color:var(--green)',pending:'color:var(--amber)',rejected:'color:var(--red)'};
  const stL={approved:'Disetujui',pending:'Menunggu',rejected:'Ditolak'};
  listEl.innerHTML=list.map(b=>`
    <div class="rc-item">
      <div style="display:flex;justify-content:space-between;align-items:center">
        <span style="font-size:13.5px;font-weight:700;color:var(--navy)">${b.jamMulai} – ${b.jamSelesai}</span>
        <span style="font-size:11px;font-weight:700;${stC[b.status]||'color:var(--text3)'}">${stL[b.status]||b.status}</span>
      </div>
      <div style="font-size:12.5px;color:var(--text2);margin-top:4px">${escapeHTML(b.keperluan)}</div>
    </div>`).join('');
  det.style.display='block';
}

/* ─── BOT MESSAGES ─── */
const MSG_EL=document.getElementById('messages');
const BOT_AV=`<div class="bot-av"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="3"/><path d="M8 21h8M12 17v4"/></svg></div>`;
function addBotMsg(html){
  const w=document.createElement('div');w.className='msg bot';
  w.innerHTML=`${BOT_AV}<div class="bbl">${html}</div>`;
  MSG_EL.appendChild(w);setTimeout(()=>MSG_EL.scrollTop=MSG_EL.scrollHeight,60);
}
function showTyping(){
  return new Promise(r=>{
    const w=document.createElement('div');w.className='msg bot';w.id='typing';
    w.innerHTML=`${BOT_AV}<div class="typing-bbl"><span class="tdot"></span><span class="tdot"></span><span class="tdot"></span></div>`;
    MSG_EL.appendChild(w);MSG_EL.scrollTop=MSG_EL.scrollHeight;
    setTimeout(()=>{w.remove();r();},850);
  });
}

/* ─── JENIS PEMOHON ─── */
let jenisPemohon='';
function selectJenis(jenis){
  jenisPemohon=jenis;
  const btnUmum=document.getElementById('jenis-umum');
  const btnUntan=document.getElementById('jenis-untan');
  const fieldInstansi=document.getElementById('field-instansi');
  const fieldFakultas=document.getElementById('field-fakultas');
  if(!btnUmum||!btnUntan)return;
  btnUmum.className='jenis-btn';
  btnUntan.className='jenis-btn';
  if(jenis==='umum'){
    btnUmum.className='jenis-btn active-umum';
    if(fieldInstansi)fieldInstansi.style.display='';
    if(fieldFakultas)fieldFakultas.style.display='none';
    const fFak=document.getElementById('f-fakultas');if(fFak)fFak.value='';
    document.getElementById('step1').classList.add('done');
    document.getElementById('step2').classList.add('active');
  } else {
    btnUntan.className='jenis-btn active-untan';
    if(fieldInstansi)fieldInstansi.style.display='none';
    if(fieldFakultas)fieldFakultas.style.display='';
    const fInst=document.getElementById('f-inst');if(fInst)fInst.value='';
    document.getElementById('step1').classList.add('done');
    document.getElementById('step2').classList.add('active');
  }
}
function getInstansiValue(){
  if(jenisPemohon==='umum'){const v=(document.getElementById('f-inst')||{}).value||'';return v.trim()?`${v.trim()} (Umum)`:'';}
  else if(jenisPemohon==='untan'){const v=(document.getElementById('f-fakultas')||{}).value||'';return v.trim()?`${v.trim()} — Untan`:'';}
  return '';
}

/* ─── INJECT FORM CARD ─── */
function injectFormCard(){
  const today=new Date();
  const todayStr=today.toISOString().split('T')[0];
  const rooms=loadRooms();
  const roomOpts=rooms.map(r=>{
    const blocked=isRoomBlocked(r.name,todayStr);
    const statusTxt=r.status==='maintenance'?' — Maintenance':r.status==='closed'?' — Ditutup':'';
    return `<option value="${r.name}" ${blocked?'disabled':''} style="${blocked?'color:var(--text3)':''}">
      ${r.name} (Maks. ${r.cap} orang)${statusTxt}</option>`;
  }).join('');
  const fakultasOptions=[
    'Fakultas Teknik','Fakultas MIPA','Fakultas Pertanian','Fakultas Kehutanan',
    'Fakultas Ekonomi & Bisnis','Fakultas Hukum','Fakultas Ilmu Sosial & Ilmu Politik',
    'Fakultas Kedokteran','Fakultas Keguruan & Ilmu Pendidikan','Pascasarjana','Lainnya',
  ].map(f=>`<option value="${f}">${f}</option>`).join('');

  const w=document.createElement('div');
  w.innerHTML=`
  <div class="form-card" id="booking-form">
    <div class="fc-inner">

      <!-- SECTION 1: DATA PEMOHON -->
      <div class="fc-section">
        <div class="fc-section-header">
          <div class="fc-section-num">1</div>
          <div class="fc-section-label">Data Pemohon</div>
        </div>

        <div class="fc-field" style="margin-bottom:16px">
          <label class="fc-label">Jenis Pemohon <span class="req">*</span></label>
          <div class="jenis-group">
            <button type="button" class="jenis-btn" id="jenis-umum" onclick="selectJenis('umum')">
              <div class="jenis-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:15px;height:15px"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>
              <div class="jenis-name">Umum / Luar</div>
              <span class="jenis-badge umum">Instansi</span>
            </button>
            <button type="button" class="jenis-btn" id="jenis-untan" onclick="selectJenis('untan')">
              <div class="jenis-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:15px;height:15px"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></div>
              <div class="jenis-name">Civitas Untan</div>
              <span class="jenis-badge untan">Fakultas</span>
            </button>
          </div>
          <div style="font-size:11.5px;color:var(--red);display:none;align-items:center;gap:4px;margin-top:4px" id="e-jenis">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Pilih jenis pemohon terlebih dahulu
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div class="fc-field">
            <label class="fc-label">Nama Lengkap <span class="req">*</span></label>
            <input class="fc-input" id="f-nama" type="text" placeholder="contoh: Budi Santoso" oninput="vld('f-nama','e-nama')"/>
            <div class="err-text" id="e-nama"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Nama tidak boleh kosong</div>
          </div>
          <div class="fc-field">
            <label class="fc-label">Nomor WhatsApp <span class="req">*</span></label>
            <input class="fc-input" id="f-kontak" type="tel" placeholder="contoh: 081234567890" oninput="this.value=this.value.replace(/[^0-9]/g,'');vld('f-kontak','e-kontak')"/>
            <div class="err-text" id="e-kontak"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Nomor WhatsApp tidak boleh kosong</div>
          </div>
        </div>

        <div class="fc-field" id="field-instansi" style="display:none">
          <label class="fc-label">Nama Instansi <span class="req">*</span> <span style="font-size:11px;color:var(--text3);font-weight:400">(Perusahaan, Lembaga, dll)</span></label>
          <input class="fc-input" id="f-inst" type="text" placeholder="contoh: PT. Borneo Teknologi, Dinas Pendidikan Kalbar" oninput="vld('f-inst','e-inst')"/>
          <div class="err-text" id="e-inst"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Instansi tidak boleh kosong</div>
        </div>

        <div class="fc-field" id="field-fakultas" style="display:none">
          <label class="fc-label">Fakultas / Bagian <span class="req">*</span></label>
          <select class="fc-input" id="f-fakultas" onchange="vld('f-fakultas','e-fakultas')">
            <option value="">— Pilih Fakultas —</option>
            ${fakultasOptions}
          </select>
          <div class="err-text" id="e-fakultas"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Pilih fakultas Anda</div>
        </div>
      </div>

      <!-- SECTION 2: DETAIL PEMESANAN -->
      <div class="fc-section">
        <div class="fc-section-header">
          <div class="fc-section-num">2</div>
          <div class="fc-section-label">Detail Pemesanan</div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
          <div class="fc-field">
            <label class="fc-label">Ruangan <span class="req">*</span></label>
            <select class="fc-input" id="f-ruangan" onchange="onRoomDateChange();showRoomInfo()">
              <option value="">— Pilih Ruangan —</option>
              ${roomOpts}
            </select>
            <div class="err-text" id="e-ruangan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Pilih ruangan</div>
          </div>
          <div class="fc-field">
            <label class="fc-label">
              Tanggal Pemakaian <span class="req">*</span>
              <span id="f-tgl-display" style="color:var(--gold2);font-weight:700;margin-left:6px;font-size:11px"></span>
            </label>
            <div class="user-cal-wrap" id="user-cal-wrap">
              <div class="user-cal-nav">
                <button type="button" onclick="userCalNavPrev()" class="user-cal-btn" id="ucal-prev">&#8249;</button>
                <div style="display:flex;gap:4px;align-items:center">
                  <button type="button" onclick="userCalSetMode('month')" class="ucal-hdr-btn" id="ucal-hdr-month"></button>
                  <button type="button" onclick="userCalSetMode('year')"  class="ucal-hdr-btn" id="ucal-hdr-year"></button>
                </div>
                <button type="button" onclick="userCalNavNext()" class="user-cal-btn" id="ucal-next">&#8250;</button>
              </div>
              <div id="user-cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px"></div>
              <div id="user-cal-preview" style="display:none;margin-top:8px;padding-top:8px;border-top:1px solid var(--cream2)">
                <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px">Jadwal ruangan hari ini:</div>
                <div id="user-cal-preview-list"></div>
              </div>
            </div>
            <input type="hidden" id="f-tanggal"/>
            <div class="err-text" id="e-tanggal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Pilih tanggal (min. H+1)</div>
          </div>
        </div>

        <!-- FASILITAS -->
        <div id="fasilitas-panel" class="fasilitas-panel">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div class="fasilitas-content">
            <div class="fasilitas-label">Fasilitas Ruangan</div>
            <div id="fasilitas-text" style="font-size:12.5px;color:var(--text2)">—</div>
          </div>
        </div>

        <!-- BLOCKED -->
        <div id="blocked-panel" class="blocked-panel">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
          <span id="blocked-text" style="font-size:12.5px;font-weight:600;color:var(--red)">Ruangan tidak tersedia</span>
        </div>

        <!-- TIME -->
        <div class="fc-field">
          <label class="fc-label">Rentang Jam Pemakaian <span class="req">*</span> <span style="font-size:11px;color:var(--text3);font-weight:400">(format 24 jam, 07:00 – 17:00 WIB)</span></label>
          <div class="time-row">
            <div class="time-input-wrap">
              <input class="fc-input" id="f-mulai" type="text" maxlength="5" placeholder="07:00" autocomplete="off"/>
              <svg class="time-clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="time-sep">→</div>
            <div class="time-input-wrap">
              <input class="fc-input" id="f-selesai" type="text" maxlength="5" placeholder="17:00" autocomplete="off"/>
              <svg class="time-clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
          </div>
          <div class="err-text" id="e-time"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Jam tidak valid. Mulai harus &lt; selesai dan dalam 07:00–17:00</div>
        </div>

        <!-- CONFLICT -->
        <div class="conflict-panel" id="conflict-panel">
          <div class="conflict-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Jadwal Bentrok! Waktu ini sudah dipesan:
          </div>
          <div id="conflict-items"></div>
        </div>
        <div class="ok-panel" id="ok-panel">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
          <span>Waktu tersedia — tidak ada bentrok jadwal.</span>
        </div>

        <!-- TIMELINE -->
        <div class="tl-wrap" id="tl-wrap">
          <div class="tl-label">Peta Jadwal: <span id="tl-title" style="font-weight:400;color:var(--text2)">—</span></div>
          <div class="tl-bar" id="tl-bar"></div>
          <div class="tl-ticks"><span>07:00</span><span>09:00</span><span>11:00</span><span>13:00</span><span>15:00</span><span>17:00</span></div>
          <div class="tl-legend">
            <div class="tl-legend-item"><div class="tl-legend-dot" style="background:rgba(192,57,43,.3)"></div>Sudah terpesan</div>
            <div class="tl-legend-item"><div class="tl-legend-dot" style="background:rgba(26,127,75,.3)"></div>Permintaan Anda (aman)</div>
            <div class="tl-legend-item"><div class="tl-legend-dot" style="background:rgba(192,57,43,.5)"></div>Permintaan Anda (bentrok)</div>
          </div>
        </div>
      </div>

      <!-- SECTION 3: KEPERLUAN -->
      <div class="fc-section" style="margin-bottom:0;padding-bottom:0;border-bottom:none">
        <div class="fc-section-header">
          <div class="fc-section-num">3</div>
          <div class="fc-section-label">Keperluan</div>
        </div>
        <div class="fc-field" style="margin-bottom:0">
          <label class="fc-label">Keperluan / Keterangan <span class="req">*</span></label>
          <input class="fc-input" id="f-kep" type="text" placeholder="contoh: Praktikum Basis Data, Seminar Penelitian, Rapat Koordinasi" oninput="vld('f-kep','e-kep')"/>
          <div class="err-text" id="e-kep"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Keperluan tidak boleh kosong</div>
          
          <div class="tpl-container">
            <div class="tpl-chip" onclick="setTpl('Praktikum Mata Kuliah')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>Praktikum</div>
            <div class="tpl-chip" onclick="setTpl('Penelitian / Tugas Akhir')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>Penelitian</div>
            <div class="tpl-chip" onclick="setTpl('Rapat / Kegiatan Organisasi')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Rapat</div>
            <div class="tpl-chip" onclick="setTpl('Ujian / Seminar')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>Seminar</div>
          </div>
        </div>
      </div>

    </div>
    <!-- FORM FOOTER WITH SUBMIT -->
    <div class="fc-form-footer">
      <button class="fc-submit" id="submit-btn" onclick="submitBooking()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2" fill="currentColor" stroke="none"/></svg>
        Kirim Permohonan Pemesanan
      </button>
      <div class="submit-note">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        Data Anda aman dan hanya digunakan untuk keperluan pemesanan ruangan
      </div>
    </div>
  </div>`;
  MSG_EL.appendChild(w);
  setTimeout(()=>{
    MSG_EL.scrollTop=MSG_EL.scrollHeight;
    initUserCal();
    initTimeInputs();
  },60);
  jenisPemohon='';
}

/* ─── RIWAYAT & CEK STATUS ─── */
function openStatusModal(){
  document.getElementById('status-overlay').classList.add('show');
  const myC = localStorage.getItem('labroom_my_contact');
  if(myC) {
    doSearchHistory(myC);
  } else {
    renderHistory();
  }
}
function closeStatusModal(){
  document.getElementById('status-overlay').classList.remove('show');
  document.getElementById('check-q').value='';
}
function renderHistory(filter = ''){
  const res=document.getElementById('status-result');
  const myIDs=JSON.parse(localStorage.getItem('labroom_my_bookings') || '[]');
  const myC=localStorage.getItem('labroom_my_contact');
  
  if(myIDs.length === 0 && !myC){
    res.innerHTML=`<div style="text-align:center;padding:30px 20px;color:var(--text3);font-size:13px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin-bottom:12px;opacity:.3"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      <br>Belum ada riwayat pemesanan yang tercatat.<br>Gunakan fitur cari di bawah dengan Nomor WA Anda.
    </div>`;
    return;
  }

  let myBookings = GLOBAL_BOOKINGS.filter(b => myIDs.includes(b.id));
  if(filter){
    const q = filter.toLowerCase();
    myBookings = myBookings.filter(b => 
      b.ruangan.toLowerCase().includes(q) || 
      b.id.toString().includes(q) ||
      b.nama.toLowerCase().includes(q) ||
      (b.instansi||'').toLowerCase().includes(q)
    );
  }
  myBookings.sort((a,b) => b.id - a.id);
  const limit = 20;
  const shown = myBookings.slice(0, limit);

  const filterHTML = `
    <div style="position:sticky;top:0;background:var(--white);z-index:5;padding-bottom:10px;margin-bottom:12px;border-bottom:1px solid var(--cream)">
      <div style="position:relative">
        <input type="text" placeholder="Filter riwayat..." oninput="renderHistory(this.value)" value="${filter}" style="width:100%;padding:8px 12px 8px 34px;border-radius:10px;border:1px solid var(--border);font-size:12px;background:var(--cream);color:var(--navy)"/>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text3)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </div>
    </div>
  `;

  let listHTML = shown.map(b => buildHistoryCard(b)).join('');
  if(myBookings.length > limit) {
    listHTML += `<div style="text-align:center;padding:12px;font-size:11px;color:var(--text3);font-style:italic">Menampilkan ${limit} reservasi terbaru</div>`;
  }

  res.innerHTML = filterHTML + '<div class="history-list">' + (shown.length ? listHTML : '<div style="text-align:center;padding:20px;font-size:12px;color:var(--text3)">Tidak ditemukan</div>') + '</div>';
}

function buildHistoryCard(b, isSearch = false){
  const stL={pending:'Menunggu',approved:'Disetujui',rejected:'Ditolak'};
  const stC={pending:'pending',approved:'approved',rejected:'rejected'};
  const fd=d=>new Date(d+'T00:00:00').toLocaleDateString('id-ID',{day:'numeric',month:'short',year:'numeric'});
  
  const myIDs = JSON.parse(localStorage.getItem('labroom_my_bookings') || '[]');
  const isSaved = myIDs.includes(b.id);

  return `
    <div class="history-item" ${isSearch ? 'style="border-left-color:var(--text3)"' : ''}>
      <div class="history-header">
        <span class="history-id">#${b.id}</span>
        <div style="display:flex;align-items:center;gap:6px">
          ${isSearch && !isSaved ? `<button onclick="saveToHistory(${b.id})" style="background:var(--gold-bg);border:none;color:var(--gold2);font-size:9px;font-weight:700;padding:2px 6px;border-radius:4px;cursor:pointer">+ Simpan</button>` : ''}
          <span class="status-badge ${stC[b.status]}">${stL[b.status]}</span>
        </div>
      </div>
      <div class="history-room">${escapeHTML(b.ruangan)}</div>
      <div class="history-time">${fd(b.tanggal)} • ${b.jamMulai} - ${b.jamSelesai}</div>
      ${isSearch ? `<div style="font-size:11px;color:var(--text3);margin-top:4px">Pemohon: ${escapeHTML(b.nama)}</div>` : ''}
    </div>
  `;
}

function saveToHistory(id){
  let myB = JSON.parse(localStorage.getItem('labroom_my_bookings') || '[]');
  if(!myB.includes(id)) myB.push(id);
  localStorage.setItem('labroom_my_bookings', JSON.stringify(myB));
  showToast('Berhasil disimpan ke riwayat saya.','success');
  renderHistory();
}

function doSearchHistory(qOverride){
  const q = qOverride || document.getElementById('check-q').value.trim();
  const res = document.getElementById('status-result');
  if(!q){showToast('Masukkan ID atau No. WA','warn');return;}
  
  // Search by ID or Contact
  const found = GLOBAL_BOOKINGS.filter(b => b.id == q || b.kontak.includes(q)).sort((a,b) => b.id - a.id);
  
  if(found.length === 0){
    if(qOverride) { renderHistory(); return; } 
    res.innerHTML=`<div style="text-align:center;padding:20px;color:var(--text3);font-size:13px">Data tidak ditemukan. Pastikan ID Reservasi atau Nomor WA benar.</div>`;
    return;
  }

  // If search was successful, update the "Saved Contact" for future auto-opens
  if(!isNaN(q) && q.length > 5) {
    localStorage.setItem('labroom_my_contact', q);
  }
  
  const title = qOverride ? 'Riwayat Reservasi Anda' : `Hasil Pencarian: "${q}"`;
  res.innerHTML = `
    <div style="position:sticky;top:0;background:var(--white);z-index:5;padding-bottom:10px;margin-bottom:10px;border-bottom:1px solid var(--cream);display:flex;justify-content:space-between;align-items:center">
       <span style="font-size:11px;font-weight:700;color:var(--gold2);text-transform:uppercase;letter-spacing:.05em">${title}</span>
       ${!qOverride ? `<button onclick="renderHistory()" style="background:none;border:none;color:var(--gold);font-size:11px;font-weight:700;cursor:pointer">✕ Batal</button>` : ''}
    </div>
    <div class="history-list">${found.map(b => buildHistoryCard(b, true)).join('')}</div>
  `;
}

async function refreshUserData(btn){
  if(btn){
    btn.disabled=true;
    const old = btn.innerHTML;
    btn.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:spin .7s linear infinite;width:14px;height:14px"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>';
    try {
      const resR = await fetch('/api/rooms');
      GLOBAL_ROOMS = await resR.json();
      const resB = await fetch('/api/bookings');
      GLOBAL_BOOKINGS = await resB.json();
      
      renderSidebar();
      if(typeof renderTimeline === 'function') renderTimeline();
      if(typeof checkConflict === 'function') checkConflict();
      
      if(document.getElementById('status-overlay').classList.contains('show')){
         const myC = localStorage.getItem('labroom_my_contact');
         if(myC) doSearchHistory(myC); else renderHistory();
      }
      showToast('Data berhasil diperbarui.','success');
    } catch(e){ console.error(e); showToast('Gagal memperbarui data.','error'); }
    btn.disabled=false;btn.innerHTML=old;
  }
}

function setTpl(txt){
  const el=document.getElementById('f-kep');
  if(el){
    el.value=txt;
    vld('f-kep','e-kep');
    el.classList.add('pulse');
    setTimeout(()=>el.classList.remove('pulse'),500);
  }
}

/* ─── USER CALENDAR ─── */
let userCalYear=0,userCalMonth=0,userCalMode='day';
function initUserCal(){
  const now=new Date();
  const min=new Date(now);min.setDate(now.getDate()+1);
  userCalYear=min.getFullYear();userCalMonth=min.getMonth();userCalMode='day';
  renderUserCal('');
  const defStr=min.toISOString().split('T')[0];
  userCalPickDate(defStr);
}
function ucalUpdateHeader(){
  const mn=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  const mBtn=document.getElementById('ucal-hdr-month');
  const yBtn=document.getElementById('ucal-hdr-year');
  if(!mBtn||!yBtn)return;
  if(userCalMode==='day'){mBtn.textContent=mn[userCalMonth];mBtn.style.color='var(--navy)';mBtn.style.fontWeight='600';yBtn.textContent=userCalYear;yBtn.style.color='var(--text2)';yBtn.style.fontWeight='500';}
  else if(userCalMode==='month'){mBtn.textContent=mn[userCalMonth];mBtn.style.color='var(--gold2)';mBtn.style.fontWeight='700';yBtn.textContent=userCalYear;yBtn.style.color='var(--text2)';yBtn.style.fontWeight='500';}
  else{mBtn.textContent=mn[userCalMonth];mBtn.style.color='var(--text2)';mBtn.style.fontWeight='500';yBtn.textContent=userCalYear;yBtn.style.color='var(--gold2)';yBtn.style.fontWeight='700';}
}
function userCalSetMode(mode){if(userCalMode===mode)userCalMode='day';else userCalMode=mode;renderUserCal(document.getElementById('f-tanggal')?.value||'');}
function userCalNavPrev(){if(userCalMode==='day'||userCalMode==='month'){userCalMonth--;if(userCalMonth<0){userCalMonth=11;userCalYear--;}}else{userCalYear--;}renderUserCal(document.getElementById('f-tanggal')?.value||'');}
function userCalNavNext(){if(userCalMode==='day'||userCalMode==='month'){userCalMonth++;if(userCalMonth>11){userCalMonth=0;userCalYear++;}}else{userCalYear++;}renderUserCal(document.getElementById('f-tanggal')?.value||'');}
function renderUserCal(selectedDate){ucalUpdateHeader();if(userCalMode==='month')renderUserCalMonths(selectedDate);else if(userCalMode==='year')renderUserCalYears(selectedDate);else renderUserCalDays(selectedDate);}
function renderUserCalDays(selectedDate){
  const grid=document.getElementById('user-cal-grid');grid.style.gridTemplateColumns='repeat(7,1fr)';
  const now=new Date();const minDate=new Date(now);minDate.setDate(now.getDate()+1);
  const minStr=minDate.toISOString().split('T')[0];const today=now.toISOString().split('T')[0];
  const ruangan=document.getElementById('f-ruangan')?.value||'';
  const bookings=ruangan?loadBookings().filter(b=>b.ruangan===ruangan&&b.status!=='rejected'):[];
  const dayNames=['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
  let html=dayNames.map(d=>`<div class="rc-head">${d}</div>`).join('');
  const firstDay=new Date(userCalYear,userCalMonth,1).getDay();
  const daysInMonth=new Date(userCalYear,userCalMonth+1,0).getDate();
  for(let i=0;i<firstDay;i++)html+=`<div class="rc-day rc-empty"></div>`;
  for(let d=1;d<=daysInMonth;d++){
    const ds=`${userCalYear}-${String(userCalMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const bks=bookings.filter(b=>b.tanggal===ds);
    const isSel=ds===selectedDate;const isToday=ds===today;const outOfRange=ds<minStr;
    let cls='rc-day';
    if(outOfRange)cls+=' rc-disabled';
    else if(isSel)cls+=' rc-selected';
    else if(bks.length)cls+=' rc-has-booking';
    if(isToday&&!isSel)cls+=' rc-today';
    const dot=bks.length&&!isSel?`<div class="rc-dot"></div>`:'';
    const clickFn=outOfRange?'':`onclick="userCalPickDate('${ds}')"`;
    html+=`<div class="${cls}" ${clickFn}><span class="rc-day-num">${d}</span>${dot}</div>`;
  }
  grid.innerHTML=html;if(selectedDate)userCalShowPreview(selectedDate);
}
function renderUserCalMonths(selectedDate){
  const grid=document.getElementById('user-cal-grid');grid.style.gridTemplateColumns='repeat(3,1fr)';
  const preview=document.getElementById('user-cal-preview');if(preview)preview.style.display='none';
  const mn=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
  const now=new Date();const minDate=new Date(now);minDate.setDate(now.getDate()+1);const minStr=minDate.toISOString().split('T')[0];
  let html='';
  mn.forEach((name,i)=>{
    const lastDay=new Date(userCalYear,i+1,0).getDate();
    const lastDs=`${userCalYear}-${String(i+1).padStart(2,'0')}-${String(lastDay).padStart(2,'0')}`;
    const outOfRange=lastDs<minStr;const isCur=i===userCalMonth;
    let cls='ucal-month-cell';if(outOfRange)cls+=' ucal-disabled';else if(isCur)cls+=' ucal-m-selected';
    const clickFn=outOfRange?'':`onclick="userCalPickMonth(${i})"`;
    html+=`<div class="${cls}" ${clickFn}>${name}</div>`;
  });grid.innerHTML=html;
}
function userCalPickMonth(m){userCalMonth=m;userCalMode='day';renderUserCal(document.getElementById('f-tanggal')?.value||'');}
function renderUserCalYears(selectedDate){
  const grid=document.getElementById('user-cal-grid');grid.style.gridTemplateColumns='repeat(3,1fr)';
  const preview=document.getElementById('user-cal-preview');if(preview)preview.style.display='none';
  const now=new Date();const minYear=now.getFullYear();const startY=userCalYear-4;
  let html='';
  for(let y=startY;y<=startY+8;y++){
    const outOfRange=y<minYear;const isCur=y===userCalYear;
    let cls='ucal-month-cell';if(outOfRange)cls+=' ucal-disabled';else if(isCur)cls+=' ucal-m-selected';
    const clickFn=outOfRange?'':`onclick="userCalPickYear(${y})"`;
    html+=`<div class="${cls}" ${clickFn}>${y}</div>`;
  }grid.innerHTML=html;
}
function userCalPickYear(y){userCalYear=y;userCalMode='month';renderUserCal(document.getElementById('f-tanggal')?.value||'');}
function userCalPickDate(ds){
  document.getElementById('f-tanggal').value=ds;
  const label=new Date(ds+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
  const disp=document.getElementById('f-tgl-display');if(disp)disp.textContent='→ '+label;
  const errEl=document.getElementById('e-tanggal');if(errEl)errEl.classList.remove('show');
  userCalMode='day';renderUserCal(ds);
  const ms=document.getElementById('f-mulai')?.value||'';const me=document.getElementById('f-selesai')?.value||'';
  onRoomDateChange();
  // step indicator
  document.getElementById('step2')?.classList.add('done');
  document.getElementById('step3')?.classList.add('active');
}
function userCalShowPreview(ds){
  const ruangan=document.getElementById('f-ruangan')?.value||'';
  const preview=document.getElementById('user-cal-preview');const listEl=document.getElementById('user-cal-preview-list');
  if(!preview||!listEl)return;
  if(!ruangan){preview.style.display='none';return;}
  const bks=loadBookings().filter(b=>b.ruangan===ruangan&&b.tanggal===ds&&b.status!=='rejected');
  if(!bks.length){preview.style.display='none';return;}
  bks.sort((a,b)=>toMin(a.jamMulai)-toMin(b.jamMulai));
  const stC={approved:'color:var(--green)',pending:'color:var(--amber)'};const stL={approved:'Disetujui',pending:'Menunggu'};
  listEl.innerHTML=bks.map(b=>`
    <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 9px;background:var(--white);border:1px solid var(--border);border-radius:6px;margin-bottom:3px;font-size:11.5px">
      <span style="font-weight:700;color:var(--navy)">${b.jamMulai}–${b.jamSelesai}</span>
      <span style="color:var(--text2);flex:1;padding:0 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escapeHTML(b.keperluan)}</span>
      <span style="font-weight:700;font-size:10.5px;${stC[b.status]||'color:var(--text3)'}">${stL[b.status]||b.status}</span>
    </div>`).join('');
  preview.style.display='block';
}


/* ─── TIME INPUT MASK (24-JAM) ─── */
function clampTime(h,m){
  // Batasi rentang operasional 07:00 - 17:00
  if(h < 7) return {h: 7, m: 0};
  if(h > 17) return {h: 17, m: 0};
  if(h === 17) return {h: 17, m: 0}; // Maksimal pukul 17:00
  
  m=Math.max(0,Math.min(59,m));
  return{h,m};
}
function initTimeInputs(){
  function maskTime(el,onChangeFn){
    el.addEventListener('input',function(){
      let v=this.value.replace(/[^0-9]/g,'');
      // Auto-insert titik dua setelah digit ke-2
      if(v.length>2)v=v.slice(0,2)+':'+v.slice(2,4);
      // Auto-correct: clamp saat sudah 4 digit (HH:MM lengkap)
      if(v.length===5){
        let h=parseInt(v.slice(0,2)),m=parseInt(v.slice(3,5));
        const c=clampTime(h,m);
        v=String(c.h).padStart(2,'0')+':'+String(c.m).padStart(2,'0');
      }
      this.value=v;
    });
    el.addEventListener('keydown',function(e){
      // Izinkan: backspace, delete, tab, arrows
      if([8,9,37,38,39,40,46].includes(e.keyCode))return;
      if(!/[0-9]/.test(e.key))e.preventDefault();
    });
    el.addEventListener('blur',function(){
      const v=this.value;
      const digits=v.replace(/[^0-9]/g,'');
      if(digits.length>=3){
        // Ada cukup digit untuk parse jam & menit
        let h=parseInt(digits.slice(0,2));
        let m=parseInt(digits.slice(2,4)||'0');
        const c=clampTime(h,m);
        this.value=String(c.h).padStart(2,'0')+':'+String(c.m).padStart(2,'0');
      } else if(digits.length>0&&digits.length<=2){
        // Hanya jam, menit dianggap 00
        let h=parseInt(digits);
        const c=clampTime(h,0);
        this.value=String(c.h).padStart(2,'0')+':00';
      } else {
        this.value='';
      }
      onChangeFn();
    });
  }
  const elMulai=document.getElementById('f-mulai');
  const elSelesai=document.getElementById('f-selesai');
  if(elMulai)maskTime(elMulai,onTimeChange);
  if(elSelesai)maskTime(elSelesai,onTimeChange);
}
function buildTimeSelects(){/* placeholder */}

/* ─── TIMELINE ─── */
const TL_S=7*60,TL_E=17*60,TL_D=TL_E-TL_S;
function pct(m){return Math.max(0,Math.min(100,((m-TL_S)/TL_D)*100));}
function renderTimeline(){
  const r=document.getElementById('f-ruangan')?.value;const d=getTanggalValue();
  const ms=document.getElementById('f-mulai')?.value;const me=document.getElementById('f-selesai')?.value;
  const wrap=document.getElementById('tl-wrap');const bar=document.getElementById('tl-bar');const ttl=document.getElementById('tl-title');
  if(!r||!d){if(wrap)wrap.classList.remove('show');return;}
  if(wrap)wrap.classList.add('show');
  if(ttl)ttl.textContent=`${r} · ${new Date(d+'T00:00:00').toLocaleDateString('id-ID',{weekday:'short',day:'numeric',month:'long',year:'numeric'})}`;
  const existing=loadBookings().filter(b=>b.ruangan===r&&b.tanggal===d&&b.status!=='rejected');
  let html='';
  existing.forEach(b=>{const s=pct(toMin(b.jamMulai)),e=pct(toMin(b.jamSelesai));html+=`<div class="tl-seg booked" style="left:${s}%;width:${Math.max(e-s,1)}%" title="${b.nama}: ${b.jamMulai}–${b.jamSelesai}">${b.jamMulai}–${b.jamSelesai}</div>`;});
  if(ms&&me&&toMin(ms)<toMin(me)){const cs=pct(toMin(ms)),ce=pct(toMin(me));const bad=getConflicts(r,d,ms,me).length>0;html+=`<div class="tl-seg ${bad?'new-bad':'new-ok'}" style="left:${cs}%;width:${Math.max(ce-cs,1)}%">${ms}–${me}</div>`;}
  if(bar)bar.innerHTML=html;
}

/* ─── VALIDATION ─── */
function vld(id,eid){
  const el=document.getElementById(id);if(!el)return;
  const ok=el.value.trim().length>0;
  el.classList.toggle('ok',ok);el.classList.toggle('err',!ok);
  const errEl=document.getElementById(eid);if(errEl)errEl.classList.toggle('show',!ok);
}
function showRoomInfo(){
  const r=document.getElementById('f-ruangan')?.value;
  const fasPanel=document.getElementById('fasilitas-panel');
  const blkPanel=document.getElementById('blocked-panel');
  if(!fasPanel||!blkPanel)return;
  if(!r){fasPanel.classList.remove('show');blkPanel.classList.remove('show');return;}
  const info=getRoomInfo(r);
  if(!info){fasPanel.classList.remove('show');blkPanel.classList.remove('show');return;}
  if(info.fasilitas){document.getElementById('fasilitas-text').textContent=info.fasilitas;fasPanel.classList.add('show');}
  else{fasPanel.classList.remove('show');}
  const d=getTanggalValue()||new Date().toISOString().split('T')[0];
  if(info.status!=='available'&&isRoomBlocked(r,d)){
    const lbl={maintenance:'🔧 Sedang Maintenance',closed:'🚫 Ruangan Ditutup'};
    const untilTxt=info.closedUntil?` sampai ${info.closedUntil.split('-').reverse().join('/')}`:' ';
    document.getElementById('blocked-text').textContent=(lbl[info.status]||'Tidak Tersedia')+untilTxt+' — tidak dapat dipesan';
    blkPanel.classList.add('show');
    const sb=document.getElementById('submit-btn');if(sb){sb.disabled=true;}
  } else {
    blkPanel.classList.remove('show');
    const sb=document.getElementById('submit-btn');if(sb){sb.disabled=false;}
  }
}
function checkConflict(){
  const r=document.getElementById('f-ruangan')?.value;const d=getTanggalValue();
  const ms=document.getElementById('f-mulai')?.value;const me=document.getElementById('f-selesai')?.value;
  const cp=document.getElementById('conflict-panel');const op=document.getElementById('ok-panel');
  if(cp)cp.classList.remove('show');if(op)op.classList.remove('show');
  if(!r||!d||!ms||!me)return;if(toMin(ms)>=toMin(me))return;
  const conflicts=getConflicts(r,d,ms,me);
  if(conflicts.length){
    if(cp){cp.classList.add('show');document.getElementById('conflict-items').innerHTML=conflicts.map(b=>`<div class="conflict-item"><strong>${escapeHTML(b.nama)}</strong> (${escapeHTML(b.instansi)||'—'}) — ${b.jamMulai}–${b.jamSelesai}</div>`).join('');}
    const fm=document.getElementById('f-mulai');const fs=document.getElementById('f-selesai');
    if(fm){fm.classList.add('err');fm.classList.remove('ok');}if(fs){fs.classList.add('err');fs.classList.remove('ok');}
  } else {
    if(op)op.classList.add('show');
    const fm=document.getElementById('f-mulai');const fs=document.getElementById('f-selesai');
    if(fm){fm.classList.remove('err');fm.classList.add('ok');}if(fs){fs.classList.remove('err');fs.classList.add('ok');}
  }
}
function onTimeChange(){
  const ms=document.getElementById('f-mulai')?.value;const me=document.getElementById('f-selesai')?.value;
  const errEl=document.getElementById('e-time');const sb=document.getElementById('submit-btn');
  let bad=false;
  if(ms&&me){
    const sMin = toMin(ms), eMin = toMin(me);
    if(sMin >= eMin || sMin < 7*60 || eMin > 17*60) bad=true;
  }
  if(errEl) {
    errEl.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:12px;height:12px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Jam operasional: 07:00 - 17:00 WIB`;
    errEl.classList.toggle('show',bad);
  }
  if(sb){if(bad){sb.disabled=true;}else{sb.disabled=false;}}
  checkConflict();renderTimeline();
}
function onRoomDateChange(){showRoomInfo();checkConflict();renderTimeline();const ms=document.getElementById('f-mulai')?.value||'';const me=document.getElementById('f-selesai')?.value||'';buildTimeSelects(ms,me);const ds=getTanggalValue();if(ds)renderUserCal(ds);}
function getTanggalValue(){return document.getElementById('f-tanggal')?.value||'';}
function validateDate(){
  const tanggal=getTanggalValue();const errEl=document.getElementById('e-tanggal');const today=new Date().toISOString().split('T')[0];
  if(!tanggal){if(errEl){errEl.querySelector('svg').style.display='inline';errEl.classList.add('show');}return false;}
  if(tanggal<=today){if(errEl){errEl.classList.add('show');}return false;}
  if(errEl)errEl.classList.remove('show');return true;
}

/* ─── SUBMIT ─── */
async function submitBooking(){
  if(!jenisPemohon){
    const ej=document.getElementById('e-jenis');if(ej)ej.style.display='flex';
    showToast('Pilih jenis pemohon (Umum / Untan).','warn');return;
  }
  const ej=document.getElementById('e-jenis');if(ej)ej.style.display='none';
  const baseFields=[{id:'f-nama',eid:'e-nama'},{id:'f-kontak',eid:'e-kontak'},{id:'f-ruangan',eid:'e-ruangan'},{id:'f-kep',eid:'e-kep'}];
  let ok=true;
  baseFields.forEach(f=>{
    const el=document.getElementById(f.id);if(!el)return;
    const v=el.value.trim().length>0;el.classList.toggle('err',!v);el.classList.toggle('ok',v);
    const errEl=document.getElementById(f.eid);if(errEl)errEl.classList.toggle('show',!v);
    if(!v)ok=false;
  });
  if(!validateDate())ok=false;
  if(jenisPemohon==='umum'){const el=document.getElementById('f-inst');if(!el||!el.value.trim()){if(el){el.classList.add('err');el.classList.remove('ok');}const err=document.getElementById('e-inst');if(err)err.classList.add('show');ok=false;}}
  else if(jenisPemohon==='untan'){const el=document.getElementById('f-fakultas');if(!el||!el.value.trim()){if(el){el.classList.add('err');el.classList.remove('ok');}const err=document.getElementById('e-fakultas');if(err)err.classList.add('show');ok=false;}}
  const ms=document.getElementById('f-mulai')?.value;const me=document.getElementById('f-selesai')?.value;
  if(!ms||!me||toMin(ms)>=toMin(me)||toMin(ms)<7*60||toMin(me)>17*60){const errEl=document.getElementById('e-time');if(errEl)errEl.classList.add('show');ok=false;}
  if(!ok){showToast('Mohon lengkapi semua data yang diperlukan.','warn');return;}
  const r=document.getElementById('f-ruangan').value;const d=getTanggalValue();
  if(isRoomBlocked(r,d)){const info=getRoomInfo(r);const lbl={maintenance:'sedang maintenance',closed:'ditutup'};const untilTxt=info?.closedUntil?` sampai ${info.closedUntil.split('-').reverse().join('/')}`:' ';showToast(`Ruangan ${lbl[info?.status]||'tidak tersedia'}${untilTxt}.`,'error');return;}
  if(getConflicts(r,d,ms,me).length>0){showToast('Jadwal bentrok! Pilih waktu lain.','error');return;}
  
  const instansiVal=getInstansiValue();
  const payload = {
    room_name: r,
    nama: document.getElementById('f-nama').value.trim(),
    instansi: instansiVal,
    kontak: document.getElementById('f-kontak').value.trim(),
    tanggal: d,
    jamMulai: ms,
    jamSelesai: me,
    keperluan: document.getElementById('f-kep').value.trim()
  };

  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.innerHTML = 'Mengirim...';

  try {
    const res = await fetch('/api/bookings', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if(data.success) {
      // Save ID to local storage for history
      let myB = JSON.parse(localStorage.getItem('labroom_my_bookings') || '[]');
      if(!myB.includes(data.booking_id)) myB.push(data.booking_id);
      localStorage.setItem('labroom_my_bookings', JSON.stringify(myB));
      localStorage.setItem('labroom_my_contact', payload.kontak);

      // Reload bookings to get the new state
      const booksRes = await fetch('/api/bookings');
      GLOBAL_BOOKINGS = await booksRes.json();
      renderSidebar();
      
      const fmtTgl=new Date(d+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
      document.getElementById('step3')?.classList.add('done');
      document.getElementById('booking-form').innerHTML=`
        <div class="success-state">
          <div class="success-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <div class="success-title">Permohonan Terkirim!</div>
          <div class="success-sub">Admin akan meninjau permohonan Anda dan menghubungi melalui<br><strong style="color:var(--navy)">${payload.kontak}</strong>.</div>
          <div class="success-receipt">
            <div class="receipt-row"><span class="receipt-label">Nomor Permohonan</span><span class="receipt-val">#${data.booking_id}</span></div>
            <div class="receipt-row"><span class="receipt-label">Ruangan</span><span class="receipt-val">${payload.room_name}</span></div>
            <div class="receipt-row"><span class="receipt-label">Tanggal</span><span class="receipt-val">${fmtTgl}</span></div>
            <div class="receipt-row"><span class="receipt-label">Waktu</span><span class="receipt-val">${payload.jamMulai} – ${payload.jamSelesai} WIB</span></div>
            <div class="receipt-row"><span class="receipt-label">Pemohon</span><span class="receipt-val">${payload.nama}</span></div>
            <div class="receipt-row"><span class="receipt-label">Instansi</span><span class="receipt-val">${payload.instansi}</span></div>
          </div>
          <span class="sbadge pending">● Menunggu Konfirmasi Admin</span>
          <div style="margin-top:22px">
            <button onclick="resetForm()" style="padding:12px 26px;border-radius:10px;background:linear-gradient(135deg,var(--navy),var(--navy3));border:1px solid var(--gold);color:var(--gold3);font-size:14px;font-weight:700;font-family:var(--font);cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .2s;letter-spacing:.02em" onmouseover="this.style.background='linear-gradient(135deg,var(--navy3),var(--slate))'" onmouseout="this.style.background='linear-gradient(135deg,var(--navy),var(--navy3))'">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="width:15px;height:15px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Buat Permohonan Baru
            </button>
          </div>
        </div>`;
    }
  } catch(e) {
    showToast('Terjadi kesalahan pada server.','error');
    btn.disabled = false;
    btn.innerHTML = 'Kirim Permohonan Pemesanan';
  }
}

/* ─── RESET FORM ─── */
async function resetForm(){
  window.location.reload();
}

/* ─── TOAST ─── */
function showToast(msg,type='success'){
  const icons={
    success:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>`,
    error:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
    warn:`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`
  };
  const t=document.createElement('div');t.className=`toast ${type}`;t.innerHTML=`${icons[type]}${msg}`;
  document.body.appendChild(t);setTimeout(()=>{t.style.opacity='0';t.style.transform='translateY(8px)';t.style.transition='all .3s';setTimeout(()=>t.remove(),300);},3000);
}

/* ─── SCROLL ANIMATION OBSERVER ─── */
const observer=new IntersectionObserver(entries=>{
  entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');observer.unobserve(e.target);}});
},{threshold:.1});
document.querySelectorAll('.anim-ready').forEach(el=>observer.observe(el));

/* ─── INIT ─── */
async function init(){
  try {
    const resR = await fetch('/api/rooms');
    GLOBAL_ROOMS = await resR.json();
    const resB = await fetch('/api/bookings');
    GLOBAL_BOOKINGS = await resB.json();
  } catch(e) { console.error('Gagal mengambil data dari API'); }
  
  renderSidebar();

  await showTyping();
  addBotMsg(`Selamat datang di <strong>Sistem Pemesanan Ruangan Laboratorium Terpadu</strong> Universitas Tanjungpura. 👋<br>Saya adalah <strong>LabBot</strong>, asisten pemesanan digital Anda.`);
  await showTyping();
  addBotMsg(`Silakan lengkapi formulir di bawah ini. Sistem akan memeriksa ketersediaan jadwal secara <strong>real-time</strong> dan otomatis mendeteksi konflik jadwal.`);
  await showTyping();
  injectFormCard();
}

init();