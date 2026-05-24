# Monev Pembelajaran - WordPress Plugin

[![WordPress Version](https://img.shields.io/badge/WordPress-5.0+-21759B.svg?style=flat&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-7.4+-777BB4.svg?style=flat&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

**Monev Pembelajaran** adalah plugin WordPress khusus (custom) yang dirancang untuk memposting, mengelola, dan menampilkan berkas Monitoring dan Evaluasi (Monev) Pembelajaran per-Semester & per-Tahun Akademik. 

Plugin ini hadir dengan **desain premium yang didominasi warna biru** (Royal Navy & Ice Blue) yang sangat responsif, modern, dan profesional, serta terintegrasi penuh dengan pustaka media WordPress bawaan.

---

## ✨ Fitur Unggulan

* **Otomatis Seed Data (Tanpa Ribet)**: Begitu plugin diaktifkan, seluruh 9 Fakultas dan 30+ Program Studi bawaan Anda (Agroteknologi, Teknik Informatika, Psikologi, Hukum, Pascasarjana, Kesehatan, dll.) akan langsung terisi secara otomatis sebagai template default.
* **Integrasi WP Media Library**: Unggah berkas dokumen (PDF, Word, Excel, dll.) langsung dari jendela editor WordPress Anda menggunakan tombol pilih media standar WordPress.
* **Pengaturan Template Global yang Praktis**: Kelola, tambah, atau ubah struktur Fakultas & Program Studi Anda dengan mudah lewat halaman pengaturan khusus berformat teks terstruktur: `Fakultas | Prodi A, Prodi B, Prodi C`.
* **Desain Premium Dominan Biru**:
  * **Header**: Gradasi biru navy yang modern dan tegas (`linear-gradient(135deg, #1e3a8a, #2563eb)`).
  * **Fakultas**: Baris disorot dengan warna biru es muda (`#f8fafc` ke `#eff6ff`) dengan teks tebal.
  * **Program Studi**: Memiliki garis pohon vertikal (*tree indentation*) untuk kejelasan hierarki.
  * **Unduh**: Tombol pil berwarna biru terang dengan ikon download SVG bawaan yang responsif dan memiliki animasi hover halus.
* **Dukungan Elementor Terintegrasi**: Cukup pasang shortcode sekali, dan halaman Elementor Anda akan ter-update otomatis setiap kali Anda memposting Monev baru.

---

## 🛠️ Struktur Berkas Plugin

```text
wp-monev-pembelajaran/
├── assets/
│   ├── css/
│   │   ├── admin.css       # Gaya tampilan dashboard WordPress & Pengaturan
│   │   └── frontend.css    # Gaya tampilan tabel premium biru di halaman depan
│   └── js/
│       └── admin.js        # Logika tombol unggah & Media Uploader WP
├── includes/
│   ├── class-monev-cpt.php      # Registrasi CPT & Custom Meta Box data detail
│   ├── class-monev-settings.php # Logika admin page pengaturan template struktur
│   └── class-monev-shortcode.php# Penanganan render tabel & shortcode frontend
└── monev-pembelajaran.php       # Berkas utama bootstrap plugin (Meta data Author)
```

---

## 🚀 Panduan Instalasi

1. **Unduh/Salin berkas**: Unduh folder plugin `monev-pembelajaran` ke komputer Anda.
2. **Kompresi ke Zip**: Ubah folder tersebut menjadi berkas `.zip` (misal: `plugin-laporan-monev-pembelajaran-wordpress.zip`).
3. **Unggah ke WordPress**:
   * Masuk ke dashboard admin WordPress Anda.
   * Pergi ke menu **Plugins** -> **Add New** (Tambah Baru) -> **Upload Plugin** (Unggah Plugin).
   * Pilih berkas `.zip` yang sudah Anda buat tadi, kemudian klik **Install Now** (Instal Sekarang).
4. **Aktifkan**: Setelah proses instalasi selesai, klik **Activate Plugin** (Aktifkan Plugin).

---

## 📖 Cara Penggunaan

### 1. Membuat Postingan Monev Baru
1. Pergi ke menu **Monev Pembelajaran** -> **Tambah Baru** di sidebar admin WordPress.
2. Masukkan judul Semester sebagai nama postingan (contoh: `Semester Genap TA. 2024/2025`).
3. Pada tabel builder di bawah judul:
   * Kolom **File Upload**: Klik tombol **Pilih** untuk membuka Media Library dan mengunggah berkas PDF/dokumen Anda.
   * Kolom **Link Kustom**: Masukkan tautan eksternal (seperti tautan Google Drive/Dropbox) jika Anda tidak mengunggah berkas ke WordPress.
   * Kolom **Status Kustom**: Isi dengan keterangan teks (seperti: `Perkuliahan sudah selesai` atau `Belum tersedia`) jika tidak ada tautan unduhan.
4. Klik tombol **Publish** (Terbitkan) di sebelah kanan.

### 2. Menampilkan Tabel di Elementor (Satu Kali Pemasangan)
1. Buka halaman tempat Anda ingin menampilkan data Monev (misalnya halaman *"Monev Pembelajaran"*), lalu klik **Edit with Elementor**.
2. Cari widget **Shortcode** di panel widget Elementor sebelah kiri.
3. Tarik (*drag*) widget **Shortcode** ke dalam kolom atau section halaman Anda.
4. Ketik atau salin shortcode berikut di dalam input pengaturan widget:
   ```text
   [monev_pembelajaran]
   ```
5. Klik **Update** untuk menyimpan halaman.

> [!NOTE]
> Setelah langkah ini selesai, Anda **tidak perlu lagi mengedit Elementor** di masa mendatang. Setiap kali Anda memposting semester baru lewat dashboard WordPress, tabel di halaman depan akan otomatis ter-update dan menampilkan data terbaru di baris paling atas!

---

## ⚙️ Mengubah Struktur Fakultas & Prodi Default
Jika suatu hari terdapat perubahan nama fakultas, penambahan program studi baru, atau penggabungan jurusan:
1. Masuk ke menu **Monev Pembelajaran** -> **Template Struktur**.
2. Di area teks yang disediakan, ubah baris yang diinginkan dengan format:
   ```text
   Nama Fakultas | Prodi A, Prodi B, Prodi C
   ```
3. Klik **Simpan Perubahan**. Struktur baru ini akan otomatis diterapkan ke semua form postingan baru Anda ke depannya!

---

## 👥 Pembuat & Kontributor

* **Author**: muhammad as'ad muhibbin akbar
* **License**: GPLv2 or later

---
*Dibuat khusus untuk kemudahan pelaporan akademik universitas secara dinamis, responsif, dan elegan.*
