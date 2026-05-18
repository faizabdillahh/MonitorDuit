<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="MonitorDuit Logo">
</p>

<h1 align="center">MonitorDuit 💸</h1>

<p align="center">
  <strong>Aplikasi Pencatatan Keuangan Pribadi Cerdas dengan Integrasi AI Pemindai Struk</strong>
</p>

---

## 📌 Tentang MonitorDuit

**MonitorDuit** adalah aplikasi pencatatan pengeluaran pribadi modern yang dirancang untuk menghilangkan kerumitan input data manual. Dengan bantuan **Kecerdasan Buatan (Google Gemini AI)**, pengguna cukup memotret struk belanja, dan sistem akan secara otomatis mengekstrak nominal, nama merchant, serta tanggal transaksi. 

Aplikasi ini dibangun menggunakan arsitektur *monolith* modern (*Laravel + Vue.js + Inertia.js*) dan didesain dengan antarmuka bergaya *Glassmorphism* dan *Dark Mode* interaktif menggunakan Tailwind CSS v4.

---

## ✨ Fitur Utama

- 🤖 **AI Receipt Scanner:** Menggunakan model `gemini-2.5-flash` untuk membaca teks pada struk belanja dan mengubahnya menjadi data transaksi terstruktur.
- 📊 **Dashboard & Statistik:** Visualisasi pengeluaran bulanan dan harian menggunakan grafik interaktif (Bar & Doughnut Chart) yang akurat.
- 🌓 **Smart Theme System:** Dukungan penuh untuk Mode Terang (*Light Mode*) dan Mode Gelap (*Dark Mode*) dengan preferensi yang tersimpan di basis data pengguna.
- 🏷️ **Manajemen Kategori:** Personalisasi kategori pengeluaran dengan warna dan ikon kustom.
- 📥 **Export Laporan:** Mengunduh riwayat transaksi ke dalam format *Excel (.xlsx)* yang diformat secara profesional.
- 🔐 **Autentikasi Aman:** Sistem pendaftaran, masuk, verifikasi email, dan pengaturan profil bawaan yang diperkuat oleh sistem keamanan Laravel.

---

## 🛠️ Tech Stack

MonitorDuit dibangun di atas ekosistem teknologi terkini:

- **Backend:** [Laravel 12](https://laravel.com/) (PHP 8.2+)
- **Frontend:** [Vue 3](https://vuejs.org/) (Composition API)
- **Routing/Bridge:** [Inertia.js](https://inertiajs.com/)
- **Styling:** [Tailwind CSS v4](https://tailwindcss.com/)
- **Database:** MySQL / MariaDB
- **AI Integration:** Google Gemini API (google-gemini-php)
- **Charts:** Chart.js & vue-chartjs
- **Excel Export:** Maatwebsite Excel

---

## 🚀 Panduan Instalasi (Development)

Ikuti langkah-langkah di bawah ini untuk menjalankan MonitorDuit di komputer lokal Anda:

### 1. Prasyarat Sistem
Pastikan komputer Anda sudah terinstal perangkat lunak berikut:
- PHP >= 8.2
- Composer
- Node.js & npm (Disarankan v20+)
- MySQL atau MariaDB (XAMPP / Laragon)

### 2. Kloning Repositori
```bash
git clone https://github.com/username/monitorduit.git
cd monitorduit
```

### 3. Instalasi Dependensi Backend (PHP)
```bash
composer install
```

### 4. Instalasi Dependensi Frontend (Node.js)
```bash
npm install
```

### 5. Konfigurasi Environment (File .env)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan atur koneksi basis data Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=monitorduit
DB_USERNAME=root
DB_PASSWORD=
```
Jangan lupa untuk menyertakan **Gemini API Key** Anda agar fitur *scan* struk dapat berjalan:
```env
GEMINI_API_KEY=masukkan_api_key_anda_disini
```
*(Anda bisa mendapatkan Gemini API Key gratis di [Google AI Studio](https://aistudio.google.com/app/apikey))*

### 6. Generate Application Key & Migrasi Database
```bash
php artisan key:generate
php artisan migrate --seed
```
*(Perintah `--seed` opsional, digunakan untuk mengisi kategori default ke dalam database)*

### 7. Buat Tautan Storage (Untuk Menyimpan Foto Struk)
```bash
php artisan storage:link
```

### 8. Jalankan Server
Buka 2 tab terminal untuk menjalankan *backend* dan *frontend* secara bersamaan:

**Terminal 1 (Backend - Laravel):**
```bash
php artisan serve
```

**Terminal 2 (Frontend - Vite):**
```bash
npm run dev
```

Aplikasi kini dapat diakses melalui browser di alamat: `http://localhost:8000`

---

## 🏗️ Menjalankan Worker (Penting untuk Fitur AI)

Karena pemrosesan gambar oleh AI membutuhkan waktu beberapa detik, MonitorDuit menggunakan sistem *Queue* (Antrean) latar belakang untuk mencegah layar pengguna *freeze*.

Agar fitur unggah struk berfungsi, Anda **wajib** menjalankan *queue worker* di terminal yang aktif:
```bash
php artisan queue:work
```
*(Pada environment `.env`, pastikan `QUEUE_CONNECTION=database` atau `redis`)*

---

## 🎨 Panduan Desain (Brand Guidelines)

Desain MonitorDuit tidak dibuat sembarangan. Aplikasi ini mengikuti aturan antarmuka yang sangat ketat untuk menciptakan pengalaman pengguna (*User Experience*) kelas premium.

Seluruh panduan terkait tipografi (Plus Jakarta Sans), palet warna (Emerald & Slate), elevasi (Shadows), dan pergerakan (Animasi) didokumentasikan dengan lengkap pada file `brand_guidelines.md`.

---

## 🧑‍💻 Arsitektur Folder Penting

- `app/Services/` - Berisi logika berat aplikasi (seperti `ReceiptScanService` untuk memanggil API Gemini dan `StatisticsService` untuk kalkulasi grafik).
- `app/Jobs/` - Berisi *Job* untuk pemrosesan latar belakang (`ScanReceiptJob`).
- `resources/js/Pages/` - Halaman Vue.js (Tampilan aplikasi).
- `resources/js/Layouts/` - Kerangka utama aplikasi (`AppLayout` dan `GuestLayout`).

---

## 📄 Lisensi

Proyek ini merupakan proyek sumber terbuka di bawah [MIT license](https://opensource.org/licenses/MIT). Silakan gunakan, pelajari, dan kembangkan sesuka Anda!
