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
  document.querySelectorAll('.asi').forEach(b=>b.classList.remove('active'));btn.classList.add('active');cur=sec;
  document.querySelectorAll('.psec').forEach(s=>s.classList.remove('active'));
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
    const ac=b.status==='pending'?`<button class="ab2 vw" onclick="openModal(${b.id})">Tinjau</button><button class="ab2 ar" onclick="qd(${b.id},'rejected')">Tolak</button>`:`<button class="ab2 vw" onclick="openModal(${b.id})">Detail</button>`;
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
  pl.innerHTML=rooms.map(r=>{const ux=r.closedUntil?' s/d '+r.closedUntil.split('-').reverse().join('/'):'';return`<div class="rsc" style="flex-direction:column;align-items:flex-start;gap:12px"><div><div class="rsn">${r.name}</div><div class="rsd">Kapasitas: ${r.cap} orang</div>${r.fasilitas?`<div class="rsd" style="margin-top:6px;font-size:11.5px;color:var(--text2);line-height:1.5">${r.fasilitas}</div>`:''}</div><div style="display:flex;align-items:center;gap:8px;width:100%;justify-content:space-between;border-top:1px solid var(--border);padding-top:10px;margin-top:4px"><span class="rstag ${r.status||'available'}">${stL[r.status]||r.status}${ux}</span><button class="ber" onclick="openRoomMgmt('${r.name}')">Edit</button></div></div>`;}).join('');
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
  document.getElementById('rc-day-list').innerHTML=list.map(b=>{const tc=b.status==='approved'?'approved':b.status==='pending'?'pending':'rejected';const tl=b.status==='approved'?'Disetujui':b.status==='pending'?'Menunggu':'Ditolak';return`<div class="rci"><div><div class="rcii"><strong>${b.nama}</strong> · ${b.instansi||'—'} <span class="tsm ${tc}">${tl}</span></div><div class="rcit">ðŸ• ${b.jamMulai}–${b.jamSelesai} | ${b.keperluan}</div></div><div class="rcia"><button class="rcb ed" onclick="openEdit(${b.id})">Edit</button><button class="rcb dl" onclick="delBk(${b.id})">Hapus</button></div></div>`;}).join('');
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
  if(cf.length){w.classList.add('show');document.getElementById('m-warn-text').innerHTML='⚠️ï¸ Bentrok dengan: '+cf.map(c=>`<strong>${c.nama}</strong> (${c.jamMulai}–${c.jamSelesai})`).join(', ')+'.';}
  else w.classList.remove('show');
  const ap=document.getElementById('m-approve');const rj=document.getElementById('m-reject');
  if(b.status==='pending'){ap.style.display='';rj.style.display='';ap.onclick=()=>decide(id,'approved');rj.onclick=()=>decide(id,'rejected');}
  else{ap.style.display='none';rj.style.display='none';}
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



function showToast(msg,type='success'){const ic={success:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>',error:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',warn:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'};const t=document.createElement('div');t.className='toast '+type;t.innerHTML=(ic[type]||ic.success)+msg;document.body.appendChild(t);setTimeout(()=>{t.style.opacity='0';t.style.transform='translateY(8px)';t.style.transition='all .3s';setTimeout(()=>t.remove(),300);},3200);}

const sty=document.createElement('style');sty.textContent='@keyframes spin{to{transform:rotate(360deg)}}';document.head.appendChild(sty);
(async function(){const s=gs();if(s){document.getElementById('login-page').classList.add('hidden');document.getElementById('admin-page').classList.add('active');document.getElementById('logged-user').textContent=s.nama;await renderAdmin();}})();
