# 🏛️ LabRoom: Sistem Pemesanan Ruangan Lab Terpadu (WIP)

**LabRoom** adalah aplikasi manajemen laboratorium berbasis web yang dikembangkan untuk menyederhanakan proses reservasi ruangan di **Laboratorium Terpadu Universitas Tanjungpura**. Sistem ini menggabungkan antarmuka **Statistik Flow Chatbot** untuk memudahkan pengguna dalam mengajukan pemesanan secara terstruktur.

## 🛠️ Arsitektur Teknis
Sistem ini dirancang dengan struktur yang efisien untuk memastikan performa yang cepat dan manajemen data yang handal:

*   **Backend Framework**: Laravel 11.x (PHP 8.x)
*   **Database**: MySQL (Relational Database Management System)
*   **Frontend Logic**: Vanilla JavaScript dengan pendekatan **Modular Scripts**. 
*   **Chatbot Engine**: Berbasis **Rule-based Static Flow** (Alur Terstruktur), di mana asisten membimbing pengguna melalui urutan pertanyaan yang sudah ditentukan untuk meminimalisir kesalahan input.
*   **API Layer**: Integrasi data real-time antara antarmuka chatbot dan database backend.

## 🌟 Fungsi Utama
### 🤖 Asisten Reservasi (Static Chatbot)
Pengguna melakukan pemesanan melalui antarmuka pesan yang mengikuti alur statis otomatis:
1.  **Guided Flow**: Chatbot memberikan pertanyaan berurutan (Nama, Tanggal, Jam, dll).
2.  **Kategori Identitas**: Pilihan kategori pemohon (Civitas Untan / Umum).
3.  **Informasi Ruangan**: Menampilkan detail fasilitas dan status operasional secara langsung dalam percakapan.
4.  **Konfirmasi Instan**: Notifikasi pengajuan berhasil setelah seluruh alur statis terpenuhi.

### 🔐 Panel Administrator
Pusat kendali untuk mengelola seluruh data yang masuk melalui chatbot:
1.  **Approval System**: Meninjau (Review) dan mengubah status permohonan (Setujui/Tolak).
2.  **Room Control**: Mengelola kapasitas, fasilitas, dan status operasional laboratorium.
3.  **Conflict Monitoring**: Admin dapat melihat jika ada dua pemesanan pada waktu yang sama melalui visual timeline.

### 📍 Navigasi Terpadu
Modul navigasi visual untuk membantu pengguna menemukan lokasi laboratorium melalui denah gedung yang terintegrasi dan penautan lokasi ke Google Maps.

## 📖 Panduan Penggunaan
1.  **User**: Mengunjungi laman utama, mengikuti alur chatbot hingga selesai, dan menunggu verifikasi admin.
2.  **Admin**: Login ke `/admin`, masuk ke dashboard untuk memproses antrean reservasi, dan memperbarui status ruangan jika diperlukan.

## ⚙️ Instalasi Sistem
1.  **Clone Repository**
2.  **Install Dependensi** (`composer install`)
3.  **Konfigurasi `.env`** (Sesuaikan database MySQL)
4.  **Migrate & Seed** (`php artisan migrate --seed`)
5.  **Serve** (`php artisan serve`)
