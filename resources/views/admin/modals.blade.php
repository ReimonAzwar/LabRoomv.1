<div class="ov" id="room-mgmt-overlay">
  <div class="modal" style="max-width:520px">
    <div class="mhd"><div class="mttl" id="rmgmt-title">Kelola Ruangan</div><div class="msub" id="rmgmt-sub">Edit fasilitas dan status</div></div>
    <div class="mf"><div class="ml">Kapasitas (Orang)</div><input id="rmgmt-cap" type="number" min="1" max="200" class="fci"/></div>
    <div class="mf"><div class="ml">Fasilitas</div><textarea id="rmgmt-fasilitas" rows="3" placeholder="contoh: 30 PC, Proyektor, AC, WiFi" class="fci" style="resize:vertical"></textarea></div>
    <div class="mf"><div class="ml">Status Ruangan</div>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button class="stp available" onclick="pickStatus('available')">✔ Tersedia</button>
        <button class="stp maintenance" onclick="pickStatus('maintenance')">🔧 Maintenance</button>
        <button class="stp closed" onclick="pickStatus('closed')">🚫 Ditutup</button>
      </div>
    </div>
    <div class="mf" id="rmgmt-until-wrap" style="display:none"><div class="ml">Sampai Tanggal</div><input id="rmgmt-until" type="date" class="fci"/><div style="font-size:11.5px;color:var(--text3);margin-top:5px">Kosongkan = tidak ada batas waktu</div></div>
    <div class="mac"><button class="mb svc" onclick="saveRoomMgmt()">Simpan Perubahan</button><button class="mb cnc" onclick="closeRoomMgmt()">Batal</button></div>
  </div>
</div>

<div class="ov" id="room-cal-overlay">
  <div class="modal" style="max-width:640px">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px">
      <div><div class="mttl" id="rc-title">Jadwal Ruangan</div><div class="msub" id="rc-sub">Klik tanggal untuk detail</div></div>
      <button class="mb cnc" onclick="closeRoomCalendar()" style="flex:none;padding:7px 16px;font-size:12px;min-width:auto">✕ Tutup</button>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding:10px 14px;background:var(--cream);border-radius:10px">
      <button onclick="rcChangeMonth(-1)" style="background:var(--white);border:1.5px solid var(--border2);border-radius:8px;color:var(--text2);cursor:pointer;padding:5px 14px;font-size:14px;font-family:var(--font);transition:all .15s" onmouseover="this.style.background='var(--navy)';this.style.color='var(--gold3)'" onmouseout="this.style.background='var(--white)';this.style.color='var(--text2)'">&#8249;</button>
      <div id="rc-month-label" style="font-size:15px;font-weight:700;color:var(--navy);font-family:var(--display)"></div>
      <button onclick="rcChangeMonth(1)" style="background:var(--white);border:1.5px solid var(--border2);border-radius:8px;color:var(--text2);cursor:pointer;padding:5px 14px;font-size:14px;font-family:var(--font);transition:all .15s" onmouseover="this.style.background='var(--navy)';this.style.color='var(--gold3)'" onmouseout="this.style.background='var(--white)';this.style.color='var(--text2)'">&#8250;</button>
    </div>
    <div id="rc-cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:3px;margin-bottom:14px"></div>
    <div style="display:flex;gap:14px;font-size:11.5px;color:var(--text3);margin-bottom:14px">
      <span style="display:flex;align-items:center;gap:5px"><span style="width:8px;height:8px;border-radius:50%;background:var(--green);display:inline-block"></span>Tersedia</span>
      <span style="display:flex;align-items:center;gap:5px"><span style="width:8px;height:8px;border-radius:50%;background:var(--gold);display:inline-block"></span>Ada reservasi</span>
    </div>
    <div id="rc-day-detail" style="display:none">
      <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px" id="rc-day-title"></div>
      <div id="rc-day-list"></div>
    </div>
  </div>
</div>

<div class="ov" id="edit-overlay">
  <div class="modal" style="max-width:460px">
    <div class="mhd"><div class="mttl">Edit Reservasi</div><div class="msub" id="edit-sub">—</div></div>
    <div class="mf"><div class="ml">Nama Pemohon</div><input class="fci" id="edit-nama" type="text"/></div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:14px">
      <div><div class="ml">Tanggal</div><input class="fci" id="edit-tanggal" type="date"/></div>
      <div><div class="ml">Jam Mulai</div><input class="fci" id="edit-mulai" type="time"/></div>
      <div><div class="ml">Jam Selesai</div><input class="fci" id="edit-selesai" type="time"/></div>
    </div>
    <div class="mf"><div class="ml">Keperluan</div><input class="fci" id="edit-kep" type="text"/></div>
    <div class="mac"><button class="mb svc" onclick="saveEdit()">Simpan</button><button class="mb cnc" onclick="closeEdit()">Batal</button></div>
  </div>
</div>

<div class="ov" id="overlay">
  <div class="modal">
    <div class="mhd"><div class="mttl" id="m-title">Detail Pemesanan</div><div class="msub" id="m-sub">—</div></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="mf"><div class="ml">Nama Lengkap</div><div class="mv" id="m-nama">—</div></div>
      <div class="mf"><div class="ml">Instansi / Fakultas</div><div class="mv" id="m-inst">—</div></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="mf"><div class="ml">Kontak</div><div class="mv" id="m-kontak">—</div></div>
      <div class="mf"><div class="ml">Ruangan</div><div class="mv" id="m-room">—</div></div>
    </div>
    <div class="mf"><div class="ml">Tanggal &amp; Waktu</div><div class="mv" id="m-time">—</div></div>
    <div class="mf"><div class="ml">Keperluan</div><div class="mv" id="m-kep">—</div></div>
    <div class="mtl"><div class="mtll">Peta jadwal ruangan pada hari tersebut:</div><div class="mtlb" id="m-tl-bar"></div><div class="mtlt"><span>07:00</span><span>09:00</span><span>11:00</span><span>13:00</span><span>15:00</span><span>17:00</span></div></div>
    <div class="mwarn" id="m-warn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span id="m-warn-text">—</span></div>
    <div class="mac">
      <button class="mb apc" id="m-approve">✔ Setujui</button>
      <button class="mb rjc" id="m-reject">✕ Tolak</button>
      <button class="mb cnc" onclick="closeModal()">Tutup</button>
    </div>
  </div>
</div>

<div class="ov" id="notif-overlay">
  <div class="modal" style="max-width:480px">
    <div class="mhd">
      <div class="mttl">📱 Kirim Notifikasi WhatsApp</div>
      <div class="msub" id="notif-sub">—</div>
    </div>
    <div class="mf" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <div class="ml">Status Keputusan</div>
        <div class="mv" id="notif-status-badge">—</div>
      </div>
      <div>
        <div class="ml">Nomor WhatsApp</div>
        <div class="mv" id="notif-kontak-display" style="font-size:14px;font-weight:700;color:var(--green)">—</div>
      </div>
    </div>
    <div class="mf">
      <div class="ml">Catatan Tambahan (Opsional)</div>
      <textarea id="notif-note" class="fci" rows="2" placeholder="Tulis alasan penolakan atau pesan tambahan..." style="resize:vertical" oninput="updateNotifPreview()"></textarea>
    </div>
    <div class="mf">
      <div class="ml">Pratinjau Pesan WA</div>
      <textarea id="notif-preview" class="fci" rows="6" style="background:var(--cream2);font-size:12px;line-height:1.5;resize:vertical" oninput="void(0)"></textarea>
      <div style="font-size:10px;color:var(--text3);margin-top:5px">Pesan akan dikirim otomatis via Fonnte atau via link wa.me manual.</div>
    </div>
    <div id="notif-result" style="display:none;padding:10px 14px;border-radius:9px;font-size:13px;font-weight:600;margin-bottom:4px"></div>
    <div class="mac">
      <button class="mb svc" id="notif-send-btn" style="background:linear-gradient(135deg, var(--green), #15693e);border-color:var(--green);color:white">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width:16px;height:16px;margin-right:5px"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.99 2.007A9.956 9.956 0 002.05 11.96c0 1.76.46 3.484 1.333 5.004L2 22l5.232-1.361A9.952 9.952 0 0011.99 22c5.514 0 9.993-4.479 9.993-9.994 0-2.67-1.04-5.18-2.928-7.069A9.925 9.925 0 0011.99 2.007zm0 18.31a8.264 8.264 0 01-4.21-1.153l-.302-.178-3.105.807.83-3.021-.197-.31a8.275 8.275 0 01-1.27-4.402c0-4.575 3.722-8.297 8.297-8.297a8.245 8.245 0 015.868 2.43 8.247 8.247 0 012.428 5.87c-.002 4.575-3.724 8.254-8.339 8.254z"/></svg>
        Kirim & Simpan
      </button>
      <button class="mb cnc" onclick="closeNotifyModal()">Batal</button>
    </div>
  </div>
</div>

<!-- MODAL EDIT RESERVASI -->
<div class="ov" id="edit-overlay">
  <div class="modal" style="max-width:550px">
    <div class="mhd">
      <div class="mttl">✏️ Edit Reservasi</div>
      <div class="msub">ID Reservasi: <span id="edit-id-lbl">—</span></div>
    </div>
    <div class="mf" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <div class="ml">Nama Pemesan</div>
        <input type="text" id="edit-nama" class="fci">
      </div>
      <div>
        <div class="ml">Instansi/Fakultas</div>
        <input type="text" id="edit-instansi" class="fci">
      </div>
    </div>
    <div class="mf">
        <div class="ml">Pilih Ruangan</div>
        <select id="edit-ruangan" class="fci"></select>
    </div>
    <div class="mf" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
      <div>
        <div class="ml">Tanggal</div>
        <input type="date" id="edit-tanggal" class="fci">
      </div>
      <div>
        <div class="ml">Jam Mulai</div>
        <input type="time" id="edit-mulai" class="fci" min="07:00" max="17:00" placeholder="07:00">
      </div>
      <div>
        <div class="ml">Jam Selesai</div>
        <input type="time" id="edit-selesai" class="fci" min="07:00" max="17:00" placeholder="17:00">
      </div>
    </div>
    <div class="mf">
      <div class="ml">Keperluan</div>
      <textarea id="edit-keperluan" class="fci" rows="2"></textarea>
    </div>
    <div id="edit-error" style="display:none;padding:10px;background:#FDF0EE;color:#C0392B;border-radius:8px;font-size:12px;margin-bottom:15px;border:1px solid rgba(192,57,43,0.1)"></div>
    <div class="mac">
      <button class="mb svc" onclick="saveEdit()" id="edit-save-btn">Simpan Perubahan</button>
      <button class="mb cnc" onclick="closeEditModal()">Batal</button>
    </div>
  </div>
</div>