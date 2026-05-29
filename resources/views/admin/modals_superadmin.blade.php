<!-- Modal Tambah Admin Custom -->
<div class="ov" id="kadmin-add-overlay">
  <div class="modal" style="max-width:440px">
    <div class="mhd">
      <div class="mttl">✨ Tambah Admin Baru</div>
      <div class="msub">Buat akun admin baru untuk mengelola LabRoom</div>
    </div>
    
    <div class="mf">
      <div class="ml">Nama Lengkap</div>
      <input type="text" id="kadmin-add-name" class="fci" placeholder="Contoh: Ahmad Fauzi" />
    </div>

    <div class="mf">
      <div class="ml">Username</div>
      <input type="text" id="kadmin-add-username" class="fci" placeholder="Contoh: fauzi_admin" />
    </div>

    <div class="mf">
      <div class="ml">Password</div>
      <input type="password" id="kadmin-add-pw" class="fci" placeholder="Masukkan password minimal 6 karakter" />
    </div>

    <div id="kadmin-add-error" style="display:none;padding:10px;background:#FDF0EE;color:#C0392B;border-radius:8px;font-size:12px;margin-bottom:15px;border:1px solid rgba(192,57,43,0.1)"></div>

    <div class="mac">
      <button class="mb svc" onclick="saveNewAdminAccount()" id="kadmin-add-btn">Tambah Admin</button>
      <button class="mb cnc" onclick="closeAddAdminModal()">Batal</button>
    </div>
  </div>
</div>

<!-- Modal Edit Admin Custom -->
<div class="ov" id="kadmin-edit-overlay">
  <div class="modal" style="max-width:440px">
    <div class="mhd">
      <div class="mttl">✏️ Edit Akun Admin</div>
      <div class="msub">Perbarui nama, username, atau reset password admin ini</div>
    </div>
    
    <input type="hidden" id="kadmin-edit-id" />

    <div class="mf">
      <div class="ml">Nama Lengkap</div>
      <input type="text" id="kadmin-edit-name" class="fci" />
    </div>

    <div class="mf">
      <div class="ml">Username</div>
      <input type="text" id="kadmin-edit-username" class="fci" />
    </div>

    <div class="mf">
      <div class="ml">Password Baru (Opsional)</div>
      <input type="password" id="kadmin-edit-pw" class="fci" placeholder="Kosongkan jika tidak ingin diganti" />
    </div>

    <div id="kadmin-edit-error" style="display:none;padding:10px;background:#FDF0EE;color:#C0392B;border-radius:8px;font-size:12px;margin-bottom:15px;border:1px solid rgba(192,57,43,0.1)"></div>

    <div class="mac">
      <button class="mb svc" onclick="saveEditedAdminAccount()" id="kadmin-edit-btn">Simpan Perubahan</button>
      <button class="mb cnc" onclick="closeEditAdminModal()">Batal</button>
    </div>
  </div>
</div>

<!-- ================================================================
     Modal Ganti Profil Super Admin
     Superadmin dapat mengubah username & password akunnya sendiri
     ================================================================ -->
<div class="ov" id="sa-profile-overlay">
  <div class="modal" style="max-width:460px">
    <div class="mhd">
      <div class="mttl" style="display:flex;align-items:center;gap:8px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:18px;height:18px;color:#c8a84b"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        Ganti Profil Super Admin
      </div>
      <div class="msub">Perbarui username dan/atau password akun Anda</div>
    </div>

    {{-- Username baru --}}
    <div class="mf">
      <div class="ml">Username Baru</div>
      <input type="text" id="sa-profile-username" class="fci"
             placeholder="Kosongkan jika tidak ingin diganti" autocomplete="off" />
    </div>

    {{-- Password baru --}}
    <div class="mf">
      <div class="ml">Password Baru</div>
      <input type="password" id="sa-profile-pw" class="fci"
             placeholder="Kosongkan jika tidak ingin diganti" autocomplete="new-password" />
    </div>

    {{-- Konfirmasi password baru --}}
    <div class="mf">
      <div class="ml">Konfirmasi Password Baru</div>
      <input type="password" id="sa-profile-pw-confirm" class="fci"
             placeholder="Ulangi password baru" autocomplete="new-password" />
    </div>

    {{-- Divider --}}
    <div style="height:1px;background:var(--cream2,#ede8df);margin:4px 0 16px"></div>

    {{-- Password sekarang (wajib diisi) --}}
    <div class="mf">
      <div class="ml" style="color:#c0392b">
        Password Saat Ini <span style="font-weight:400;font-size:11px">(wajib diisi untuk konfirmasi)</span>
      </div>
      <input type="password" id="sa-profile-current-pw" class="fci"
             placeholder="Masukkan password Anda yang sekarang" autocomplete="current-password" />
    </div>

    <div id="sa-profile-error"
         style="display:none;padding:10px;background:#FDF0EE;color:#C0392B;border-radius:8px;
                font-size:12px;margin-bottom:15px;border:1px solid rgba(192,57,43,0.1)"></div>
    <div id="sa-profile-success"
         style="display:none;padding:10px;background:#EFF9F0;color:#1e7e34;border-radius:8px;
                font-size:12px;margin-bottom:15px;border:1px solid rgba(30,126,52,0.15)"></div>

    <div class="mac">
      <button class="mb svc" id="sa-profile-btn" onclick="saveSuperAdminProfile()">
        Simpan Perubahan
      </button>
      <button class="mb cnc" onclick="closeSaProfileModal()">Batal</button>
    </div>
  </div>
</div>

<!-- Custom Confirmation Modal (Premium replacement for window.confirm) -->
<div class="ov" id="custom-confirm-overlay" style="z-index: 10000;">
  <div class="modal" style="max-width:380px; text-align:center; padding:32px 24px">
    <div style="width:56px; height:56px; background:#FDF0EE; color:#E74C3C; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; border:1px solid rgba(231,76,60,0.15)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:24px;height:24px"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    
    <div class="mttl" id="custom-confirm-title" style="font-size:18px; margin-bottom:8px; font-weight:700">Hapus Akun?</div>
    <div class="msub" id="custom-confirm-body" style="font-size:13px; line-height:1.6; margin-bottom:24px; color:#555">Apakah Anda yakin? Tindakan ini tidak dapat dibatalkan.</div>
    
    <div style="display:flex; gap:12px; justify-content:center">
      <button class="mb svc" id="custom-confirm-yes-btn" style="background:#E74C3C; border-color:#E74C3C; color:#fff; min-width:110px">Hapus</button>
      <button class="mb cnc" id="custom-confirm-no-btn" style="min-width:110px">Batal</button>
    </div>
  </div>
</div>
