const USERS=[{id:1,username:'admin',password:'lab2026',nama:'Administrator',role:'admin'},{id:2,username:'superadmin',password:'super@123',nama:'Super Admin',role:'superadmin'},{id:3,username:'labmanager',password:'mgr2026',nama:'Manajer Lab',role:'manager'}];
const SK='labroom_session';
let GLOBAL_ROOMS = [];
let GLOBAL_BOOKINGS = [];

const lr=()=>GLOBAL_ROOMS;
const sr=l=> { /* No op */ };
const gs=()=>{try{return JSON.parse(sessionStorage.getItem(SK)||'null');}catch(e){return null;}};
const ss=u=>sessionStorage.setItem(SK,JSON.stringify(u));
const cs=()=>sessionStorage.removeItem(SK);
const lb=()=>GLOBAL_BOOKINGS;
const sb=l=> { /* No op */ };
const tm=t=>{const[h,m]=t.split(':').map(Number);return h*60+m;};
const gc=(ruangan,tanggal,mulai,selesai,excId=null)=>{const s=tm(mulai),e=tm(selesai);return lb().filter(b=>{if(b.id===excId)return false;if(b.ruangan!==ruangan||b.tanggal!==tanggal||b.status==='rejected')return false;return s<tm(b.jamSelesai)&&e>tm(b.jamMulai);});};

async function fetchData() {
    try {
        const rR = await fetch('/api/rooms');
        GLOBAL_ROOMS = await rR.json();
        const rB = await fetch('/api/bookings');
        GLOBAL_BOOKINGS = await rB.json();
    } catch(e) { console.error('Gagal mengambil data dari API', e); }
}

function togglePw(){const i=document.getElementById('inp-pw');const a=document.getElementById('eye-off');const b=document.getElementById('eye-on');if(i.type==='password'){i.type='text';a.style.display='none';b.style.display='';}else{i.type='password';b.style.display='none';a.style.display='';}}
async function doLogin(){
  const u=document.getElementById('inp-user').value.trim(),p=document.getElementById('inp-pw').value;
  const err=document.getElementById('lerr'),btn=document.getElementById('lbtn');
  err.classList.remove('show');btn.disabled=true;
  btn.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:spin .7s linear infinite;width:16px;height:16px"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Memverifikasi...';
  
  const f=USERS.find(x=>x.username===u&&x.password===p);
  if(f){
    ss(f);
    await fetchData();
    document.getElementById('login-page').classList.add('hidden');document.getElementById('admin-page').classList.add('active');document.getElementById('logged-user').textContent=f.nama;renderAdmin();
  } else {
    document.getElementById('lerr-txt').textContent='Username atau password salah. Coba lagi.';err.classList.add('show');document.getElementById('inp-user').classList.add('err');document.getElementById('inp-pw').classList.add('err');
  }
  btn.disabled=false;btn.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg> Masuk ke Dashboard';
}
function doLogout(){cs();document.getElementById('admin-page').classList.remove('active');document.getElementById('login-page').classList.remove('hidden');document.getElementById('inp-user').value='';document.getElementById('inp-pw').value='';document.getElementById('inp-user').classList.remove('err');document.getElementById('inp-pw').classList.remove('err');document.getElementById('lerr').classList.remove('show');showToast('Berhasil keluar dari sistem.','success');}

let cur='dashboard',clf='all',cdf='all',dsq='',lsq='';
function navTo(sec,btn){
  document.querySelectorAll('.asi').forEach(b=>b.classList.remove('active'));
  if(btn) btn.classList.add('active');
  cur=sec;
  document.querySelectorAll('.psec').forEach(s=>s.classList.remove('active'));
  
  if(sec==='manage-admins') {
    const secAdm = document.getElementById('sec-manage-admins');
    if(secAdm) secAdm.classList.add('active');
    return;
  }
  
  if(sec==='dashboard'){document.getElementById('sec-dashboard').classList.add('active');}
  else{
    document.getElementById('sec-list').classList.add('active');
    clf=sec==='all'?'all':sec==='pending'?'pending':sec==='approved'?'approved':'rejected';
    const T={all:'Semua Pemesanan',pending:'Menunggu Konfirmasi',approved:'Pemesanan Disetujui',rejected:'Pemesanan Ditolak'};
    const S={all:'Daftar seluruh pemesanan ruangan',pending:'Reservasi menunggu keputusan admin',approved:'Reservasi yang telah disetujui',rejected:'Reservasi yang telah ditolak'};
    document.getElementById('list-title').textContent=T[sec]||'Semua Pemesanan';document.getElementById('list-sub').textContent=S[sec]||'';
    ['all','pending','approved','rejected'].forEach(f=>{const b=document.getElementById('lf-'+f);if(b)b.className='fb'+(f===clf?' active':'');});
    renderListTable();
  }
}
function navToFilter(f){navTo(f==='all'?'all':f,document.getElementById('nav-'+(f==='all'?'all':f)));}
async function refreshAdmin(btn){
  if(btn){
    btn.disabled=true;
    const old=btn.innerHTML;
    btn.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:spin .7s linear infinite;width:14px;height:14px"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Memuat...';
    await renderAdmin();
    btn.disabled=false;
    btn.innerHTML=old;
    showToast('Data berhasil diperbarui','success');
  } else {
    await renderAdmin();
  }
}
async function renderAdmin(){
  await fetchData();
  const list=lb();const p=list.filter(b=>b.status==='pending').length;const ap=list.filter(b=>b.status==='approved').length;const re=list.filter(b=>b.status==='rejected').length;
  ['s-total','a-total'].forEach(id=>{const el=document.getElementById(id);if(el)el.textContent=list.length;});
  ['s-pending','a-pending'].forEach(id=>{const el=document.getElementById(id);if(el)el.textContent=p;});
  ['s-approved','a-approved'].forEach(id=>{const el=document.getElementById(id);if(el)el.textContent=ap;});
  ['s-rejected','a-rejected'].forEach(id=>{const el=document.getElementById(id);if(el)el.textContent=re;});
  document.getElementById('df-cnt-all').textContent=list.length;document.getElementById('df-cnt-pending').textContent=p;document.getElementById('df-cnt-approved').textContent=ap;document.getElementById('df-cnt-rejected').textContent=re;
  document.getElementById('lf-cnt-all').textContent=list.length;document.getElementById('lf-cnt-pending').textContent=p;document.getElementById('lf-cnt-approved').textContent=ap;document.getElementById('lf-cnt-rejected').textContent=re;
  renderRoomsPanel();renderDashTable();renderListTable();
}
function buildRows(data){
  if(!data.length)return'<div class="es"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><p>Tidak ada data pemesanan</p></div>';
  const fd=d=>{const[y,m,dd]=d.split('-');return`${dd}/${m}/${y}`;};
  return data.map(b=>{
    const tc=b.status==='pending'?'pending':b.status==='approved'?'approved':'rejected';
    const tl=b.status==='pending'?'Menunggu':b.status==='approved'?'Disetujui':'Ditolak';
    const cf=b.status==='pending'?gc(b.ruangan,b.tanggal,b.jamMulai,b.jamSelesai,b.id):[];
    const cb=cf.length?'<span class="cbg">⚠ Bentrok</span>':'';
    const ac=`<div style="display:flex;gap:4px">
      <button class="ab2 vw" onclick="openModal(${b.id})">${b.status==='pending'?'Tinjau':'Detail'}</button>
      <button class="ab2 wa" onclick="openNotifyModal(${b.id}, '${b.status}')" style="background:#25D366;color:white;border:none;padding:4px 8px;border-radius:6px;cursor:pointer;display:flex;align-items:center" title="Kirim WA">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.99 2.007A9.956 9.956 0 002.05 11.96c0 1.76.46 3.484 1.333 5.004L2 22l5.232-1.361A9.952 9.952 0 0011.99 22c5.514 0 9.993-4.479 9.993-9.994 0-2.67-1.04-5.18-2.928-7.069A9.925 9.925 0 0011.99 2.007zm0 18.31a8.264 8.264 0 01-4.21-1.153l-.302-.178-3.105.807.83-3.021-.197-.31a8.275 8.275 0 01-1.27-4.402c0-4.575 3.722-8.297 8.297-8.297a8.245 8.245 0 015.868 2.43 8.247 8.247 0 012.428 5.87c-.002 4.575-3.724 8.254-8.339 8.254z"/></svg>
      </button>
      <button class="ab2 vw" onclick="openEditModal(${b.id})" style="background:var(--navy);color:white;border:none;padding:4px 8px;border-radius:6px;cursor:pointer" title="Edit">✏️</button>
    </div>`;
    return`<div class="brow"><div class="c"><div class="cn">${b.nama}${cb}</div><div class="cs">${b.kontak}</div></div><div class="c col-inst" style="font-size:12.5px;color:var(--text2)">${b.instansi||'—'}</div><div class="c" style="font-size:12.5px">${b.ruangan}</div><div class="c col-time">${fd(b.tanggal)}<br><span style="color:var(--text3);font-size:11.5px">${b.jamMulai}–${b.jamSelesai}</span></div><div class="c"><span class="tag ${tc}"><span class="tdot"></span>${tl}</span></div><div class="acts">${ac}</div></div>`;
  }).join('');
}
function renderDashTable(){let d=lb();if(cdf!=='all')d=d.filter(b=>b.status===cdf);if(dsq)d=d.filter(b=>b.nama.toLowerCase().includes(dsq)||b.ruangan.toLowerCase().includes(dsq)||(b.instansi||'').toLowerCase().includes(dsq));d.sort((a,b)=>b.id-a.id);document.getElementById('dash-blist').innerHTML=buildRows(d.slice(0,10));}
function setDashFilter(f,btn){cdf=f;document.querySelectorAll('#dash-fbar .fb').forEach(b=>b.className='fb');const m={all:'fb active',pending:'fb active-amber',approved:'fb active-green',rejected:'fb active-red'};btn.className=m[f]||'fb active';renderDashTable();}
function setDashSearch(q){dsq=q.toLowerCase();renderDashTable();}
function renderListTable(){let d=lb();if(clf!=='all')d=d.filter(b=>b.status===clf);if(lsq)d=d.filter(b=>b.nama.toLowerCase().includes(lsq)||b.ruangan.toLowerCase().includes(lsq)||(b.instansi||'').toLowerCase().includes(lsq));d.sort((a,b)=>b.id-a.id);document.getElementById('list-blist').innerHTML=buildRows(d);}
function setListFilter(f,btn){clf=f;['all','pending','approved','rejected'].forEach(x=>{const b=document.getElementById('lf-'+x);if(b)b.className='fb';});const m={all:'fb active',pending:'fb active-amber',approved:'fb active-green',rejected:'fb active-red'};btn.className=m[f]||'fb active';renderListTable();}
function setListSearch(q){lsq=q.toLowerCase();renderListTable();}

let rmN='',rmS='available';
function openRoomMgmt(name){const rooms=lr();const r=rooms.find(x=>x.name===name);if(!r)return;rmN=name;rmS=r.status||'available';document.getElementById('rmgmt-title').textContent='Kelola: '+name;document.getElementById('rmgmt-sub').textContent='Edit kapasitas, fasilitas dan status ruangan';document.getElementById('rmgmt-cap').value=r.cap||'';document.getElementById('rmgmt-fasilitas').value=r.fasilitas||'';document.getElementById('rmgmt-until').value=r.closedUntil||'';pickStatus(rmS,false);document.getElementById('room-mgmt-overlay').classList.add('show');}
function closeRoomMgmt(){document.getElementById('room-mgmt-overlay').classList.remove('show');}
document.getElementById('room-mgmt-overlay').addEventListener('click',function(e){if(e.target===this)closeRoomMgmt();});
function pickStatus(st){rmS=st;document.querySelectorAll('.stp').forEach(b=>b.classList.remove('active'));const b=document.querySelector('.stp.'+st);if(b)b.classList.add('active');document.getElementById('rmgmt-until-wrap').style.display=(st==='maintenance'||st==='closed')?'block':'none';}
async function saveRoomMgmt(){
  const cap=document.getElementById('rmgmt-cap').value||'30';
  const fasilitas=document.getElementById('rmgmt-fasilitas').value.trim();
  const closedUntil=rmS==='available'?'':document.getElementById('rmgmt-until').value;
  try {
    await fetch('/api/rooms/'+rmN, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({cap, fasilitas, status: rmS, closedUntil})
    });
    closeRoomMgmt();await renderAdmin();showToast('Data ruangan berhasil disimpan!','success');
  } catch(e) { showToast('Gagal','error'); }
}

function autoExpire(){const rooms=lr();const today=new Date().toISOString().split('T')[0];let ch=false;rooms.forEach(r=>{if((r.status==='maintenance'||r.status==='closed')&&r.closedUntil&&r.closedUntil<today){r.status='available';r.closedUntil='';ch=true;}});if(ch)sr(rooms);return rooms;}
function renderRoomsPanel(){
  const rooms=autoExpire();
  const as=document.getElementById('aside-rooms');
  if(as)as.innerHTML=rooms.map(r=>{const sc=r.status==='available'?'var(--green)':r.status==='maintenance'?'var(--amber)':'var(--red)';return`<div class="asi" onclick="openRoomCalendar('${r.name}')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="width:14px;height:14px;flex-shrink:0"><rect x="2" y="3" width="20" height="14" rx="3"/><path d="M8 21h8M12 17v4"/></svg><span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px">${r.name}</span><span style="width:7px;height:7px;border-radius:50%;background:${sc};flex-shrink:0"></span><button onclick="event.stopPropagation();openRoomMgmt('${r.name}')" style="background:none;border:none;cursor:pointer;color:rgba(200,168,75,.4);padding:0 0 0 4px;font-size:14px;transition:color .15s" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='rgba(200,168,75,.4)'">⚙</button></div>`;}).join('');
  const pl=document.getElementById('room-panel-list');if(!pl)return;
  const stL={available:'Tersedia',maintenance:'Maintenance',closed:'Ditutup'};
  pl.innerHTML=rooms.map(r=>{const ux=r.closedUntil?' s/d '+r.closedUntil.split('-').reverse().join('/'):'';return`<div class="rsc"><div><div class="rsn">${r.name}</div><div class="rsd">Kapasitas: ${r.cap} orang</div>${r.fasilitas?`<div class="rsd" style="margin-top:3px;font-size:11px">${r.fasilitas}</div>`:''}</div><div style="display:flex;align-items:center;gap:8px;flex-shrink:0"><span class="rstag ${r.status||'available'}">${stL[r.status]||r.status}${ux}</span><button class="ber" onclick="openRoomMgmt('${r.name}')">Edit</button></div></div>`;}).join('');
  if(typeof rcRoom!=='undefined'&&rcRoom)renderRoomCalendar();
}
let rcRoom='',rcY=0,rcM=0,rcSD='';
function openRoomCalendar(room){rcRoom=room;const n=new Date();rcY=n.getFullYear();rcM=n.getMonth();rcSD='';document.getElementById('rc-title').textContent='Jadwal: '+room;document.getElementById('rc-sub').textContent='Klik tanggal untuk melihat detail reservasi';renderRoomCalendar();document.getElementById('room-cal-overlay').classList.add('show');}
function closeRoomCalendar(){document.getElementById('room-cal-overlay').classList.remove('show');document.getElementById('rc-day-detail').style.display='none';}
document.getElementById('room-cal-overlay').addEventListener('click',function(e){if(e.target===this)closeRoomCalendar();});
function rcChangeMonth(d){rcM+=d;if(rcM>11){rcM=0;rcY++;}if(rcM<0){rcM=11;rcY--;}renderRoomCalendar();}
function renderRoomCalendar(){
  const MN=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  document.getElementById('rc-month-label').textContent=MN[rcM]+' '+rcY;
  const days=['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
  const list=lb().filter(b=>b.ruangan===rcRoom&&b.status!=='rejected');
  const today=new Date().toISOString().split('T')[0];
  let h=days.map(d=>`<div class="rch">${d}</div>`).join('');
  const fd=new Date(rcY,rcM,1).getDay();const dim=new Date(rcY,rcM+1,0).getDate();
  for(let i=0;i<fd;i++)h+='<div class="rcd empty"></div>';
  for(let d=1;d<=dim;d++){const ds=`${rcY}-${String(rcM+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;const bks=list.filter(b=>b.tanggal===ds);let cls='rcd';if(bks.length)cls+=' hasbk';if(ds===today)cls+=' today';if(ds===rcSD)cls+=' sel';const dots=bks.length?`<div class="drow">${bks.slice(0,4).map(()=>'<span class="dd"></span>').join('')}</div>`:'';h+=`<div class="${cls}" onclick="rcSel('${ds}')"><div class="rcdn">${d}</div>${dots}</div>`;}
  document.getElementById('rc-cal-grid').innerHTML=h;if(rcSD)renderRcDay(rcSD);
}
function rcSel(ds){rcSD=ds;renderRoomCalendar();renderRcDay(ds);}
function renderRcDay(ds){
  const lbl=new Date(ds+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
  document.getElementById('rc-day-title').textContent=lbl;
  const list=lb().filter(b=>b.ruangan===rcRoom&&b.tanggal===ds&&b.status!=='rejected');
  const det=document.getElementById('rc-day-detail');det.style.display='block';
  if(!list.length){document.getElementById('rc-day-list').innerHTML='<div style="font-size:12.5px;color:var(--text3);padding:10px;background:var(--cream);border-radius:8px;text-align:center">Tidak ada reservasi pada hari ini.</div>';return;}
  list.sort((a,b)=>tm(a.jamMulai)-tm(b.jamMulai));
  document.getElementById('rc-day-list').innerHTML=list.map(b=>{const tc=b.status==='approved'?'approved':b.status==='pending'?'pending':'rejected';const tl=b.status==='approved'?'Disetujui':b.status==='pending'?'Menunggu':'Ditolak';return`<div class="rci"><div><div class="rcii"><strong>${b.nama}</strong> · ${b.instansi||'—'} <span class="tsm ${tc}">${tl}</span></div><div class="rcit">🕐 ${b.jamMulai}–${b.jamSelesai} | ${b.keperluan}</div></div><div class="rcia"><button class="rcb ed" onclick="openEdit(${b.id})">Edit</button><button class="rcb dl" onclick="delBk(${b.id})">Hapus</button></div></div>`;}).join('');
}
async function delBk(id){
  if(!confirm('Hapus reservasi ini?'))return;
  try {
    await fetch('/api/bookings/'+id, { method: 'DELETE' });
    await renderAdmin();if(rcRoom)renderRcDay(rcSD);showToast('Reservasi dihapus.','error');
  } catch(e) {}
}
let eid=null;
function openEdit(id){const b=lb().find(x=>x.id===id);if(!b)return;eid=id;document.getElementById('edit-sub').textContent=b.ruangan;document.getElementById('edit-nama').value=b.nama;document.getElementById('edit-tanggal').value=b.tanggal;document.getElementById('edit-mulai').value=b.jamMulai;document.getElementById('edit-selesai').value=b.jamSelesai;document.getElementById('edit-kep').value=b.keperluan;document.getElementById('edit-overlay').classList.add('show');}
function closeEdit(){document.getElementById('edit-overlay').classList.remove('show');eid=null;}
document.getElementById('edit-overlay').addEventListener('click',function(e){if(e.target===this)closeEdit();});
async function saveEdit(){
  const list=lb();const b=list.find(x=>x.id===eid);if(!b){closeEdit();return;}
  const nm=document.getElementById('edit-nama').value.trim();const tg=document.getElementById('edit-tanggal').value;const ml=document.getElementById('edit-mulai').value;const sl=document.getElementById('edit-selesai').value;const kp=document.getElementById('edit-kep').value.trim();
  if(!nm||!tg||!ml||!sl||!kp){showToast('Mohon lengkapi semua data.','warn');return;}
  if(tm(ml)>=tm(sl)){showToast('Jam mulai harus sebelum jam selesai.','warn');return;}
  const cf=gc(b.ruangan,tg,ml,sl,eid);if(cf.length){showToast('Jadwal bentrok dengan reservasi lain!','error');return;}
  try {
    await fetch('/api/bookings/'+eid, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({nama:nm, tanggal:tg, jamMulai:ml, jamSelesai:sl, keperluan:kp})
    });
    closeEdit();await renderAdmin();if(rcRoom&&rcSD)renderRcDay(rcSD);showToast('Reservasi berhasil diperbarui!','success');
  } catch(e){}
}

const TLS=7*60,TLE=17*60,TLD=TLE-TLS;
const pct=m=>Math.max(0,Math.min(100,((m-TLS)/TLD)*100));
let aid=null;
function openModal(id){
  const list=lb();const b=list.find(x=>x.id===id);if(!b)return;aid=id;
  const fd=d=>{const[y,m,dd]=d.split('-');return`${dd}/${m}/${y}`;};
  const df=d=>new Date(d+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
  document.getElementById('m-title').textContent='Pemesanan: '+b.ruangan;
  document.getElementById('m-sub').textContent='Diajukan oleh '+b.nama+' · '+(b.instansi||'—');
  document.getElementById('m-nama').textContent=b.nama;document.getElementById('m-inst').textContent=b.instansi||'—';
  document.getElementById('m-kontak').textContent=b.kontak;document.getElementById('m-room').textContent=b.ruangan;
  document.getElementById('m-time').textContent=df(b.tanggal)+', '+b.jamMulai+' – '+b.jamSelesai;document.getElementById('m-kep').textContent=b.keperluan;
  const oth=list.filter(x=>x.ruangan===b.ruangan&&x.tanggal===b.tanggal&&x.id!==b.id&&x.status!=='rejected');
  let tl=oth.map(x=>{const s=pct(tm(x.jamMulai)),e=pct(tm(x.jamSelesai));return`<div class="mtls bkd" style="left:${s}%;width:${Math.max(e-s,1)}%">${x.jamMulai}–${x.jamSelesai}</div>`;}).join('');
  const cs=pct(tm(b.jamMulai)),ce=pct(tm(b.jamSelesai));
  tl+=`<div class="mtls cur" style="left:${cs}%;width:${Math.max(ce-cs,1)}%">${b.jamMulai}–${b.jamSelesai}</div>`;
  document.getElementById('m-tl-bar').innerHTML=tl;
  const cf=gc(b.ruangan,b.tanggal,b.jamMulai,b.jamSelesai,b.id);
  const w=document.getElementById('m-warn');
  if(cf.length){w.classList.add('show');document.getElementById('m-warn-text').innerHTML='⚠️ Bentrok dengan: '+cf.map(c=>`<strong>${c.nama}</strong> (${c.jamMulai}–${c.jamSelesai})`).join(', ')+'.';}
  else w.classList.remove('show');
  const ap=document.getElementById('m-approve');const rj=document.getElementById('m-reject');
  if(b.status==='pending'){
    ap.style.display='';rj.style.display='';
    ap.onclick=()=>openNotifyModal(id,'approved');rj.onclick=()=>openNotifyModal(id,'rejected');
  } else {
    ap.style.display='none';rj.style.display='none';
  }
  document.getElementById('overlay').classList.add('show');
}
function closeModal(){document.getElementById('overlay').classList.remove('show');aid=null;}
async function decide(id,st){
  try {
    await fetch('/api/bookings/'+id+'/status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({status:st})
    });
    closeModal();await renderAdmin();showToast(st==='approved'?'Pemesanan berhasil disetujui!':'Pemesanan ditolak.',st==='approved'?'success':'error');
  } catch(e){}
}

let editId=null;
function openEditModal(id){
  const b=lb().find(x=>x.id===id); if(!b) return;
  editId=id;
  document.getElementById('edit-id-lbl').textContent=id;
  document.getElementById('edit-nama').value=b.nama;
  document.getElementById('edit-instansi').value=b.instansi;
  document.getElementById('edit-tanggal').value=b.tanggal;
  document.getElementById('edit-mulai').value=b.jamMulai;
  document.getElementById('edit-selesai').value=b.jamSelesai;
  document.getElementById('edit-keperluan').value=b.keperluan;
  
  const rSel=document.getElementById('edit-ruangan');
  rSel.innerHTML=lr().map(r=>`<option value="${r.name}" ${r.name===b.ruangan?'selected':''}>${r.name}</option>`).join('');
  
  document.getElementById('edit-error').style.display='none';
  document.getElementById('edit-overlay').classList.add('show');
}
function closeEditModal(){ document.getElementById('edit-overlay').classList.remove('show'); editId=null; }

async function saveEdit(){
  const btn=document.getElementById('edit-save-btn');
  const err=document.getElementById('edit-error');
  const payload={
    nama: document.getElementById('edit-nama').value,
    instansi: document.getElementById('edit-instansi').value,
    ruangan: document.getElementById('edit-ruangan').value,
    tanggal: document.getElementById('edit-tanggal').value,
    jamMulai: document.getElementById('edit-mulai').value,
    jamSelesai: document.getElementById('edit-selesai').value,
    keperluan: document.getElementById('edit-keperluan').value
  };

  // 1. Conflict & Time Range Check
  const sT = payload.jamMulai;
  const eT = payload.jamSelesai;
  if(sT < '07:00' || eT > '17:00' || sT >= eT){
    err.textContent='⚠️ Gagal: Jam operasional adalah 07:00 - 17:00.';
    err.style.display='block'; return;
  }

  const conflicts=gc(payload.ruangan, payload.tanggal, payload.jamMulai, payload.jamSelesai, editId);
  if(conflicts.length){
    err.textContent='⚠️ Gagal: Ruangan sudah terisi pada jam tersebut.';
    err.style.display='block'; return;
  }

  btn.disabled=true; btn.textContent='Menyimpan...';
  try {
    const res=await fetch('/api/bookings/'+editId,{
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
      body:JSON.stringify(payload)
    });
    if(!res.ok) throw new Error('Gagal update ke server');
    showToast('Reservasi berhasil diperbarui!','success');
    await fetchData(); renderAdmin(); closeEditModal();
  } catch(e){
    err.textContent='⚠️ Error: '+e.message; err.style.display='block';
  } finally { btn.disabled=false; btn.textContent='Simpan Perubahan'; }
}

function openNotifyModal(id, st){
  notifId=id; notifSt=st;
  const list=lb(); notifBooking=list.find(x=>x.id===id);
  if(!notifBooking) return;
  
  // Hanya tutup modal detail jika sedang terbuka
  const detOverlay = document.getElementById('detail-overlay');
  if(detOverlay && detOverlay.classList.contains('show')) closeModal();

  document.getElementById('notif-sub').textContent='Kepada: '+notifBooking.nama;
  document.getElementById('notif-kontak-display').innerHTML = '<strong>' + notifBooking.kontak + '</strong>';

  const bdg=document.getElementById('notif-status-badge');
  if(st==='approved'){
    bdg.innerHTML='<span style="color:var(--green);font-weight:700;font-size:14px">✓ Disetujui</span>';
  } else if(st==='rejected'){
    bdg.innerHTML='<span style="color:var(--red);font-weight:700;font-size:14px">✕ Ditolak</span>';
  } else {
    bdg.innerHTML='<span style="color:var(--amber);font-weight:700;font-size:14px">⏳ Menunggu</span>';
  }

  document.getElementById('notif-note').value='';
  document.getElementById('notif-result').style.display='none';
  
  // RESET BUTTON STATE (Fix loading bug)
  const sendBtn = document.getElementById('notif-send-btn');
  sendBtn.disabled = false;
  sendBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;margin-right:5px"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.99 2.007A9.956 9.956 0 002.05 11.96c0 1.76.46 3.484 1.333 5.004L2 22l5.232-1.361A9.952 9.952 0 0011.99 22c5.514 0 9.993-4.479 9.993-9.994 0-2.67-1.04-5.18-2.928-7.069A9.925 9.925 0 0011.99 2.007zm0 18.31a8.264 8.264 0 01-4.21-1.153l-.302-.178-3.105.807.83-3.021-.197-.31a8.275 8.275 0 01-1.27-4.402c0-4.575 3.722-8.297 8.297-8.297a8.245 8.245 0 015.868 2.43 8.247 8.247 0 012.428 5.87c-.002 4.575-3.724 8.254-8.339 8.254z"/></svg> Kirim & Simpan';

  updateNotifPreview();
  document.getElementById('notif-send-btn').onclick=()=>sendNotificationAndSave();
  document.getElementById('notif-overlay').classList.add('show');
}

function closeNotifyModal(){
  document.getElementById('notif-overlay').classList.remove('show');
  notifId=null;notifSt='';notifBooking=null;
}
document.getElementById('notif-overlay').addEventListener('click',function(e){if(e.target===this)closeNotifyModal();});

function updateNotifPreview(){
  if(!notifBooking)return;
  const note=document.getElementById('notif-note').value.trim();
  const tgl=new Date(notifBooking.tanggal+'T00:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
  let msg=`Halo ${notifBooking.nama},\n\n`;
  msg+=`Pesan resmi dari LabRoom Untan.\n`;
  msg+=`Reservasi *${notifBooking.ruangan}* pada *${tgl}* pukul ${notifBooking.jamMulai}–${notifBooking.jamSelesai} WIB telah `;
  if(notifSt==='approved') msg+=`*DISETUJUI* ✅.`;
  else if(notifSt==='rejected') msg+=`*DITOLAK* ❌.`;
  else msg+=`*DIPROSES* ⏳.`;
  if(note) msg+=`\n\nCatatan:\n"${note}"`;
  msg+=`\n\nTerima kasih.`;
  document.getElementById('notif-preview').value=msg;
}

async function sendNotificationAndSave(){
  const btn=document.getElementById('notif-send-btn');
  const resultEl=document.getElementById('notif-result');
  btn.disabled=true;
  btn.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:spin .7s linear infinite;width:14px;height:14px;margin-right:6px"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Mengirim...';
  resultEl.style.display='none';

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const headers = {'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken};

  try {
    // 1. Save booking status
    const note = document.getElementById('notif-note').value.trim();
    const statusRes = await fetch('/api/bookings/'+notifId+'/status',{
      method:'POST', headers, body:JSON.stringify({
        status: notifSt,
        alasan_penolakan: note
      })
    });
    if(!statusRes.ok) throw new Error('Gagal menyimpan status reservasi');

    // 2. Send notification (WhatsApp Only)
    const msg = document.getElementById('notif-preview').value;
    const kontak = notifBooking.kontak;
    
    const notifRes = await fetch('/api/notify/whatsapp',{
      method:'POST', headers,
      body: JSON.stringify({phone:kontak, message:msg})
    });
    const notifData = await notifRes.json();
    
    // Handle fallback link for WhatsApp if API fails
    if(!notifData.success && notifData.fallback_url){
        window.open(notifData.fallback_url, '_blank');
    }

    if(notifData.success || notifData.fallback_url){
      resultEl.style.cssText='display:block;background:var(--green-bg);color:var(--green);border:1px solid rgba(26,127,75,.25);padding:10px 14px;border-radius:9px;font-size:13px;font-weight:600;margin-bottom:4px';
      resultEl.textContent=notifData.success ? '✓ WhatsApp Terkirim Otomatis' : '✓ Link WhatsApp Terbuka';
      await renderAdmin();
      setTimeout(()=>{
        closeNotifyModal();
        showToast(notifSt==='approved'?'Reservasi disetujui & notifikasi terkirim!':'Reservasi ditolak & notifikasi terkirim!', notifSt==='approved'?'success':'error');
      }, 1800);
    } else {
      resultEl.style.cssText='display:block;background:var(--amber-bg);color:var(--amber);border:1px solid rgba(200,134,10,.25);padding:10px 14px;border-radius:9px;font-size:13px;font-weight:600;margin-bottom:4px';
      resultEl.textContent='⚠ Gagal mengirim WA: '+notifData.message;
      btn.disabled=false;
      btn.innerHTML='Kirim & Simpan';
    }
  } catch(e){
    resultEl.style.cssText='display:block;background:var(--red-bg);color:var(--red);border:1px solid rgba(192,57,43,.25);padding:10px 14px;border-radius:9px;font-size:13px;font-weight:600;margin-bottom:4px';
    resultEl.textContent='✕ Error: '+e.message;
    btn.disabled=false;
    btn.innerHTML='Kirim & Simpan';
  }
}

async function qd(id,st){
  try {
    await fetch('/api/bookings/'+id+'/status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({status:st})
    });
    await renderAdmin();showToast('Pemesanan ditolak.','error');
  } catch(e){}
}
document.getElementById('overlay').addEventListener('click',function(e){if(e.target===this)closeModal();});

const DN=['Ahmad Fauzi','Lestari Putri','Roni Wijaya','Hani Safitri','Dodi Kurnia'];
const DI=['Teknik Sipil — Umum','Manajemen — Untan','Akuntansi — Umum','Hukum — Untan','Kedokteran — Umum'];
const DRN=['Lab Komputer A','Lab Jaringan','Lab Elektronika','Ruang Seminar','Ruang Rapat'];
const TM=[['08:00','10:00'],['10:00','12:00'],['13:00','15:00'],['15:00','17:00']];
let di=0;
async function addDemoBooking(){
  const i=di%5;const t=TM[di%4];di++;
  try {
    await fetch('/api/bookings', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        room_name: DRN[i],
        nama: DN[i],
        instansi: DI[i],
        kontak: '08'+Math.floor(Math.random()*9e9+1e9),
        tanggal: '2026-04-15',
        jamMulai: t[0],
        jamSelesai: t[1],
        keperluan: 'Kegiatan akademik (data demo)'
      })
    });
    await renderAdmin();showToast('Booking demo berhasil masuk!','success');
  } catch(e){}
}

function showToast(msg,type='success'){const ic={success:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>',error:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',warn:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'};const t=document.createElement('div');t.className='toast '+type;t.innerHTML=(ic[type]||ic.success)+msg;document.body.appendChild(t);setTimeout(()=>{t.style.opacity='0';t.style.transform='translateY(8px)';t.style.transition='all .3s';setTimeout(()=>t.remove(),300);},3200);}

const sty=document.createElement('style');sty.textContent='@keyframes spin{to{transform:rotate(360deg)}}';document.head.appendChild(sty);

/* ── State Super Admin ───────────────────────────────────────────── */
let _admins = [];
let _adminSearchQ = '';

document.addEventListener('DOMContentLoaded', () => {
  if (!window.LABROOM_ADMIN?.isSuperAdmin) return;
  fetchAdminList();
});

async function fetchAdminList() {
  try {
    const res = await fetch('/api/admin/accounts', {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    _admins = data.admins ?? [];
    renderAdminGrid(_admins);
    updateAdminBadge(_admins.length);
  } catch (e) {
    console.error('[LabRoom] fetchAdminList gagal:', e);
    showAdminGridError('Gagal memuat daftar admin. Coba segarkan halaman.');
  }
}

function renderAdminGrid(admins) {
  const grid  = document.getElementById('admin-grid');
  const empty = document.getElementById('admin-grid-empty');
  const count = document.getElementById('admin-count');
  if(!grid) return;

  grid.querySelectorAll('.kadmin-card').forEach(el => el.remove());

  const q = _adminSearchQ.toLowerCase();
  const filtered = q
    ? admins.filter(a =>
        (a.name     ?? '').toLowerCase().includes(q) ||
        (a.username ?? '').toLowerCase().includes(q)
      )
    : admins;

  if (count) count.textContent = filtered.length;
  if (empty) empty.style.display = filtered.length ? 'none' : 'block';

  filtered.forEach(admin => {
    const card = buildAdminCard(admin);
    grid.appendChild(card);
  });
}

function buildAdminCard(admin) {
  const card = document.createElement('div');
  card.className = 'kadmin-card';
  card.dataset.id = admin.id;

  const initials = (admin.name ?? admin.username ?? '?')
    .split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() ?? '').join('');

  const isSelf = admin.username === window.LABROOM_ADMIN?.username;

  card.innerHTML = `
    <div class="kadmin-avatar">${initials}</div>
    <div class="kadmin-info">
      <div class="kadmin-name">${escHtml(admin.name ?? admin.username)}</div>
      <div class="kadmin-username">@${escHtml(admin.username)}</div>
    </div>
    <div class="kadmin-actions">
      <button
        class="bsm"
        onclick="openEditAdminModal(${admin.id})"
        title="Edit admin"
        ${isSelf ? 'style="opacity:.4;pointer-events:none"' : ''}
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        Edit
      </button>
      <button
        class="bsm danger"
        onclick="confirmDeleteAdmin(${admin.id}, '${escAttr(admin.name ?? admin.username)}')"
        title="Hapus admin"
        ${isSelf ? 'style="opacity:.4;pointer-events:none"' : ''}
      >
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6l-1 14H6L5 6"/>
          <path d="M10 11v6M14 11v6"/>
          <path d="M9 6V4h6v2"/>
        </svg>
        Hapus
      </button>
    </div>
  `;
  return card;
}

function filterAdminList(q) {
  _adminSearchQ = q;
  renderAdminGrid(_admins);
}

function refreshAdmins(btn) {
  if (btn) {
    btn.disabled = true;
    setTimeout(() => { btn.disabled = false; }, 1500);
  }
  fetchAdminList();
}

function updateAdminBadge(n) {
  const badge = document.getElementById('a-admins');
  if (badge) badge.textContent = n;
}

function openAddAdminModal() {
  const name = window.prompt('Nama lengkap admin baru:');
  if (!name) return;
  const username = window.prompt('Username:');
  if (!username) return;
  const password = window.prompt('Password sementara:');
  if (!password) return;
  createAdminAccount({ name, username, password });
}

function openEditAdminModal(id) {
  const admin = _admins.find(a => a.id === id);
  if (!admin) return;
  const newName = window.prompt('Nama baru:', admin.name ?? '');
  if (newName === null) return;
  const newPassword = window.prompt('Password baru (kosongkan jika tidak diganti):', '');
  updateAdminAccount(id, {
    name: newName,
    ...(newPassword ? { password: newPassword } : {}),
  });
}

function confirmDeleteAdmin(id, displayName) {
  const ok = window.confirm(
    `Hapus akun admin "${displayName}"?\n\nTindakan ini tidak dapat dibatalkan.`
  );
  if (ok) deleteAdminAccount(id);
}

async function createAdminAccount(payload) {
  try {
    const res = await fetch('/api/admin/accounts', {
      method: 'POST',
      headers: jsonHeaders(),
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message ?? 'Gagal membuat akun');
    showToast('Akun admin berhasil dibuat.', 'success');
    fetchAdminList();
  } catch (e) {
    showToast(e.message, 'error');
  }
}

async function updateAdminAccount(id, payload) {
  try {
    const res = await fetch(`/api/admin/accounts/${id}`, {
      method: 'PUT',
      headers: jsonHeaders(),
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message ?? 'Gagal memperbarui akun');
    showToast('Akun admin berhasil diperbarui.', 'success');
    fetchAdminList();
  } catch (e) {
    showToast(e.message, 'error');
  }
}

async function deleteAdminAccount(id) {
  try {
    const res = await fetch(`/api/admin/accounts/${id}`, {
      method: 'DELETE',
      headers: jsonHeaders(),
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message ?? 'Gagal menghapus akun');
    showToast('Akun admin berhasil dihapus.', 'success');
    fetchAdminList();
  } catch (e) {
    showToast(e.message, 'error');
  }
}

function jsonHeaders() {
  return {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
  };
}

function escHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function escAttr(str) {
  return String(str).replace(/'/g,"\\'");
}

function showAdminGridError(msg) {
  const empty = document.getElementById('admin-grid-empty');
  if (empty) { empty.textContent = msg; empty.style.display = 'block'; }
}

(async function(){const s=gs();if(s){document.getElementById('login-page').classList.add('hidden');document.getElementById('admin-page').classList.add('active');document.getElementById('logged-user').textContent=s.nama;await renderAdmin();}})();