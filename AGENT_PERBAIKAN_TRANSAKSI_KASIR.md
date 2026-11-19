# Instruksi Agent: Perbaikan Halaman Transaksi Kasir (Stok Dinamis + Struk Thermal)

> Proyek: Sistem POS & Bengkel **Pradan Motor** (Laravel, Blade, Tailwind CSS, MySQL)
> Referensi alur: `alur_1.md` (bagian 4.B Alur Transaksi Kasir POS, bagian 5 Rute, bagian 7 Komponen UI)
> Bahasa UI: **Bahasa Indonesia**

---

## 1. Tujuan

- **Stok dinamis:** stok di halaman transaksi kasir selalu mengikuti data di database, bukan data statis/hardcode.
- **Struk pembelian:** buat template cetak struk yang rapi untuk **printer thermal 58mm dan 80mm**.
- Rapikan alur kasir (setelah bayar → cetak struk) agar cepat dan minim salah input.

---

## 2. Aturan Wajib (Guardrails)

- JANGAN ubah:
  - skema database / migration yang sudah ada
  - nama rute yang sudah ada (`kasir.transaksi`, `kasir.transaksi.store`, dst.)
  - logika `DB::transaction()` + `lockForUpdate()` di `KasirController@storeTransaksi`
  - validasi server: stok kurang → tolak, nominal bayar kurang → tolak
  - format nomor `TRX-YYYYMMDD-XXXX` dan snapshot `nama_item` / `harga_satuan`
- **Validasi server tetap jadi sumber kebenaran terakhir.** Pengecekan stok di sisi klien hanya untuk kenyamanan.
- Pakai komponen yang sudah ada (`<x-page-header>`, `<x-modal>`, `<x-badge>`, `<x-input>`, dst.) dan Tailwind CSS.
- Pakai Alpine.js / JS vanilla sesuai yang sudah dipakai proyek. Tidak menambah library besar baru.
- Anti double-submit global di `layouts/app.blade.php` jangan dirusak. Pastikan form kasir tetap kompatibel.
- Semua teks UI dalam Bahasa Indonesia.

---

## 3. Langkah Awal (Discovery)

- Temukan file asli sebelum mengedit:
  - `app/Http/Controllers/KasirController.php` (`transaksi`, `storeTransaksi`, `riwayat`)
  - View halaman kasir: `resources/views/kasir/transaksi*`
  - View riwayat kasir & admin: `resources/views/kasir/riwayat*`, `resources/views/admin/riwayat*`
  - `routes/web.php`
  - Model: `Barang`, `Jasa`, `Penjualan`, `DetailPenjualan`, `Pelanggan`
  - `resources/views/layouts/app.blade.php`
- Cek apakah data barang di halaman kasir:
  - di-hardcode di JS/Blade, atau
  - diambil sekali saat page load lalu tidak diperbarui.
- Cek apakah tombol cetak struk / `@media print` sudah ada. Jika sudah, **perbarui**, jangan buat duplikat.
- Catat nilai ambang "stok menipis" yang dipakai di dashboard, lalu pakai nilai yang sama agar konsisten.

---

## 4. Tugas A: Stok Dinamis dari Database

### 4.1 Sumber data

- Hapus semua data stok statis/hardcode di view dan JS kasir.
- Controller `transaksi()` mengirim barang dari DB (kolom: `id_barang`, `nama_barang`, `satuan`, `harga_jual`, `stok`, kategori). Gunakan `select` seperlunya + eager load kategori.
- Tambah endpoint JSON **read-only** untuk menyegarkan stok:
  - `GET /kasir/transaksi/stok` → nama rute `kasir.transaksi.stok`
  - Middleware sama dengan modul kasir (`auth`).
  - Respons: daftar `{ id_barang, stok }` (ringan, tanpa data lain).
  - Tidak boleh mengubah data apa pun.

### 4.2 Perilaku di antarmuka

- Tampilkan **sisa stok tersedia** = `stok DB − qty di keranjang`, dan update seketika saat qty berubah atau item dihapus dari keranjang.
- Badge stok pada tiap kartu/baris barang (pakai `<x-badge>` dengan ikon + teks):
  - `Habis` (merah): stok = 0
  - `Menipis` (kuning): stok ≤ ambang
  - `Tersedia` (hijau): selain itu
- Barang dengan stok 0:
  - tampil redup, tombol tambah **disabled**, ada label "Stok habis".
- Qty di keranjang:
  - tidak boleh melebihi stok. Tombol `+` disabled saat batas tercapai, input angka otomatis di-clamp.
  - tampilkan pesan kecil "Maks. N tersedia" saat batas tercapai.
  - jasa servis tidak dibatasi stok.
- **Sinkronisasi berkala:**
  - panggil `kasir.transaksi.stok` tiap ±15–30 detik (saat tab aktif, gunakan `visibilitychange`) dan saat tab kembali difokuskan.
  - perbarui stok di UI tanpa mengosongkan keranjang.
  - jika stok barang di keranjang turun di bawah qty (kasir lain baru menjual), **turunkan qty otomatis** ke sisa stok dan tampilkan peringatan: "Stok 'X' berubah, jumlah disesuaikan menjadi N."
  - jika gagal fetch, jangan error keras. Tampilkan indikator kecil "Stok mungkin tidak terbaru".
- Setelah transaksi **berhasil**: keranjang dikosongkan dan stok di UI langsung disegarkan dari DB.
- Jika server menolak karena stok kurang (`ValidationException`):
  - pertahankan isi keranjang + input pembayaran (`old()`),
  - tampilkan pesan error jelas di dekat tombol bayar,
  - segarkan stok otomatis.
- Pencarian barang: filter instan berdasarkan nama/kategori, tidak mereset keranjang.

### 4.3 Perbaikan UX kasir (pendukung)

- Total, subtotal, dan kembalian dihitung langsung, format Rupiah (`Rp 150.000`).
- Input uang bayar:
  - tombol cepat: `Uang Pas`, `+10rb`, `+20rb`, `+50rb`, `+100rb`.
  - tombol "Proses Pembayaran" **disabled** jika keranjang kosong atau uang bayar < total.
  - tampilkan "Kurang Rp X" (merah) atau "Kembalian Rp X" (hijau) real-time.
- Keranjang kosong: tampil empty state, bukan area kosong.
- Fokus otomatis ke kolom pencarian saat halaman dibuka.
- Responsif untuk tablet (perangkat kasir umum).

---

## 5. Tugas B: Template Struk & Cetak Thermal

### 5.1 Rute & controller

- Tambah rute cetak (nama rute disesuaikan pola yang ada):
  - Kasir: `GET /kasir/transaksi/{penjualan}/struk` → `kasir.transaksi.struk`
  - Admin (riwayat): `GET /admin/riwayat/{penjualan}/struk` → `admin.riwayat.struk`
- Hak akses:
  - Kasir hanya boleh mencetak struk **miliknya sendiri**.
  - Admin boleh mencetak semua.
  - Selain itu: 403.
- Query string opsional `?lebar=58` atau `?lebar=80` (default dari config). Nilai lain → default.
- Eager load: `penjualan.detail`, `user`, `pelanggan`.
- Sumber data struk memakai **snapshot** `nama_item` & `harga_satuan` dari `detail_penjualan`, bukan data master terbaru.

### 5.2 Konfigurasi identitas toko

- Buat `config/toko.php` dengan nilai dari `.env` (jangan hardcode di view):
  - `nama` (default: "Pradan Motor"), `alamat`, `telepon`, `footer` (default: "Terima kasih atas kunjungan Anda"), `catatan` (opsional), `lebar_default` (58 atau 80).
- Tambahkan contoh variabel ke `.env.example`: `TOKO_NAMA`, `TOKO_ALAMAT`, `TOKO_TELEPON`, `TOKO_FOOTER`, `STRUK_LEBAR`.

### 5.3 View struk

- File: `resources/views/struk/penjualan.blade.php` (layout **sendiri**, tanpa sidebar/topbar/app layout).
- Isi struk, urut dari atas:
  1. Nama bengkel (tebal, tengah), alamat, telepon
  2. Garis pemisah
  3. No. nota, tanggal & jam (WIB, format `15/11/2026 14:30`), kasir, pelanggan (+ plat nomor jika ada; "Umum" jika kosong)
  4. Garis pemisah
  5. Rincian item. Per item:
     - baris 1: nama item (boleh wrap ke baris berikutnya, tidak dipotong)
     - baris 2: `qty x harga` rata kiri, subtotal rata kanan
     - tandai jasa servis, misalnya kelompok "SUKU CADANG" dan "JASA SERVIS" bila keranjang campuran
  6. Garis pemisah
  7. TOTAL (tebal), BAYAR, KEMBALI
  8. Garis pemisah
  9. Footer terima kasih + catatan (mis. "Barang yang sudah dibeli tidak dapat dikembalikan" bila diisi di config)
- Angka format Rupiah, rata kanan, font monospace agar kolom lurus.
- Jangan menampilkan data sensitif (email kasir, ID internal database).

### 5.4 CSS cetak (thermal)

- Pakai `@page` dan `@media print`:

```css
/* 58mm */
.struk-58 { width: 48mm; }
/* 80mm */
.struk-80 { width: 72mm; }

@page { size: 58mm auto; margin: 0; }   /* ganti 80mm untuk kertas 80 */
@media print {
  html, body { margin: 0; padding: 0; background: #fff; }
  .no-print { display: none !important; }
}
```

- Ukuran `@page` dibuat dinamis sesuai lebar terpilih (render `@page` di Blade sesuai `$lebar`).
- Ketentuan:
  - lebar konten area cetak: sekitar 48mm (kertas 58mm) dan 72mm (kertas 80mm), margin kiri-kanan ±2mm.
  - font monospace (mis. `'Courier New', monospace`), ukuran 11–12px untuk 58mm dan 12–13px untuk 80mm.
  - warna hitam murni di atas putih, tanpa background, tanpa bayangan, tanpa gambar berat.
  - tidak ada page-break di tengah struk (`break-inside: avoid`), tinggi mengikuti isi (`auto`).
  - beri ruang kosong ±8–10mm di bawah footer agar pemotong (auto-cutter) tidak memotong teks.
- Di layar (bukan print), tampilkan **pratinjau** struk di tengah halaman dengan toolbar `no-print`:
  - tombol `Cetak`, `Kembali`
  - pilihan lebar kertas `58mm | 80mm` (ingat pilihan terakhir di `localStorage`, aman bila gagal diakses)
  - `?autoprint=1` → otomatis `window.print()` setelah halaman selesai dimuat, lalu opsional tutup/kembali setelah `afterprint`.

### 5.5 Integrasi alur

- Setelah transaksi sukses, modal kembalian menampilkan:
  - tombol utama **"Cetak Struk"** (membuka rute struk di tab/iframe tersembunyi + `autoprint`),
  - tombol sekunder "Transaksi Baru".
- Opsi "Cetak otomatis setelah bayar" (toggle, tersimpan di `localStorage`).
- Halaman riwayat kasir & admin: tombol `print` (`<x-button-action type="print">`) mengarah ke rute struk.
- Cetak lewat iframe tersembunyi agar halaman kasir tidak berpindah (fallback: tab baru).

### 5.6 Catatan kompatibilitas printer thermal

- **Jalur utama (wajib):** cetak via browser (`window.print()`) ke printer thermal yang sudah **terpasang driver** di Windows/OS (umumnya USB).
  - Di dialog cetak: set ukuran kertas sesuai (58mm/80mm), margin **None**, skala **100%**, nonaktifkan header/footer browser.
  - Untuk cetak tanpa dialog di Chrome/Edge (PC kasir), jalankan browser dengan flag `--kiosk-printing`, dan jadikan printer thermal sebagai printer default.
- Tuliskan panduan singkat di `docs/PANDUAN_CETAK_STRUK.md`:
  - cara pasang driver, atur ukuran kertas, set printer default, mode kiosk-printing, dan troubleshooting (struk terpotong, margin terlalu besar, teks kecil).
- **Fase 2 (opsional, jangan dikerjakan kecuali diminta):** cetak langsung ESC/POS (Web Serial/WebUSB atau `mike42/escpos-php`). Cukup tulis catatan ringkas di panduan.

---

## 6. Perbaikan Teknis Pendukung

- Hindari N+1: eager load relasi di semua query terkait.
- Pindahkan perhitungan format (Rupiah, status stok) ke helper/accessor yang bisa dipakai ulang di view kasir dan struk.
- Pastikan seluruh waktu tampil dengan timezone `Asia/Jakarta`.
- Endpoint stok harus ringan (hanya kolom `id_barang`, `stok`) dan memakai `Cache-Control: no-store`.
- Jangan menambah dependensi Composer/NPM tanpa alasan kuat.

---

## 7. Pengujian

- Tambah test (Feature):
  - Halaman kasir menampilkan stok sesuai database. Ubah stok di DB lalu muat ulang, nilai ikut berubah.
  - `kasir.transaksi.stok` mengembalikan JSON stok terbaru, butuh login, dan tidak mengubah data.
  - Rute struk:
    - 200 + memuat `no_transaksi`, nama item, total, bayar, kembalian
    - kasir lain → 403, admin → 200, guest → redirect login
    - `?lebar=58` dan `?lebar=80` menghasilkan kelas/`@page` yang sesuai, nilai tidak valid memakai default
    - struk memakai snapshot (ubah harga master setelah transaksi, nilai struk tetap)
  - Transaksi dengan stok kurang tetap ditolak. Pemotongan stok tetap benar.
- Jalankan:

```bash
php vendor/phpunit/phpunit/phpunit
```

- Seluruh test lama (41 test, 248 asersi) harus tetap **hijau**.
- Uji manual:
  - Buka kasir di dua tab/akun berbeda, jual barang yang sama, pastikan stok di tab lain berubah dan qty keranjang menyesuaikan.
  - Cetak struk 58mm dan 80mm (pratinjau cetak browser atau printer thermal asli), cek teks tidak terpotong dan tidak ada halaman kosong.
  - Cek keranjang campuran (barang + jasa), nama item panjang, dan pelanggan umum.

---

## 8. Kriteria Selesai (Checklist)

- [ ] Tidak ada stok hardcode. Stok halaman kasir berasal dari database.
- [ ] Endpoint `kasir.transaksi.stok` berjalan dan UI menyegarkan stok berkala tanpa mengosongkan keranjang.
- [ ] Barang habis tidak bisa ditambahkan. Qty tidak bisa melebihi stok.
- [ ] Badge stok `Habis / Menipis / Tersedia` tampil dengan teks + ikon.
- [ ] Error stok dari server menjaga isi keranjang dan menyegarkan stok.
- [ ] Rute + view struk tersedia (kasir & admin), hak akses benar.
- [ ] Struk 58mm dan 80mm tercetak rapi di printer thermal (tanpa sidebar/topbar, tanpa margin berlebih).
- [ ] Tombol "Cetak Struk" muncul setelah bayar dan di halaman riwayat.
- [ ] Identitas toko diambil dari `config/toko.php` / `.env`.
- [ ] `docs/PANDUAN_CETAK_STRUK.md` dibuat.
- [ ] Tidak ada perubahan pada migration, logika transaksi, dan format nomor nota.
- [ ] Semua test hijau.

---

## 9. Format Laporan Akhir dari Agent

Setelah selesai, berikan ringkasan singkat berbentuk poin:

- Daftar file yang diubah/ditambah.
- Ringkasan perubahan per file.
- Hasil `phpunit`.
- Hal yang belum dikerjakan (jika ada) beserta alasannya.