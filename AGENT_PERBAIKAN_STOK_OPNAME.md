# Instruksi Agent: Perbaikan Modul Stok Opname

> Proyek: Sistem POS & Bengkel **Pradan Motor** (Laravel + Blade + Tailwind CSS)
> Referensi alur: `alur.md` (bagian 4.C Stok Opname, bagian 7 Komponen UI)
> Bahasa UI: **Bahasa Indonesia**

---

## 1. Tujuan

- Ubah label badge status selisih menjadi **"Ada Selisih"**.
- Rapikan tampilan & alur modul Stok Opname agar **user friendly** dan modern.
- Logika bisnis, skema database, dan rute **tidak boleh berubah**.

---

## 2. Aturan Wajib (Guardrails)

- JANGAN ubah:
  - struktur tabel / migration (`ms_penyesuaian`, `ms_penyesuaian_detail`)
  - nama rute (`admin.stok-opname*`)
  - rumus delta, rollback, dan proteksi stok negatif
  - `DB::transaction()` + `lockForUpdate()` di controller
  - aturan validasi (tanggal wajib, `stok_fisik` ≥ 0, alasan wajib maks. 500 karakter)
- Gunakan komponen yang sudah ada: `<x-page-header>`, `<x-summary-card>`, `<x-table>`, `<x-modal>`, `<x-button-action>`, `<x-badge>`, `<x-input>`, `<x-select>`.
- Jika butuh varian baru, **extend** `<x-badge>` (jangan buat komponen duplikat).
- Gunakan Tailwind CSS saja. Tanpa library eksternal baru kecuali sudah ada di proyek.
- Alpine.js / JS vanilla boleh dipakai untuk interaksi, ikuti yang sudah dipakai di proyek.
- Semua teks UI dalam Bahasa Indonesia.

---

## 3. Langkah Awal (Discovery)

- Cari file terkait, lalu catat path aslinya sebelum mengedit:
  - View: `resources/views/admin/stok-opname*` (index, modal tambah, modal detail, modal hapus)
  - Controller: `app/Http/Controllers/Admin/StokOpnameController.php`
  - Model: `Opname`, `OpnameDetail` (cek nama model sebenarnya)
  - Komponen: `resources/views/components/badge.blade.php`
  - Laporan mutasi stok: `Admin\LaporanController` + view-nya
  - Test: `tests/Feature/AdminCrudTest.php`
- Jalankan `grep -rn "Semua Sesuai\|Ada Selisih\|Sesuai (0)" resources app tests` untuk menemukan semua tempat label status dipakai.

---

## 4. Tugas Utama: Badge Status

### 4.1 Badge di tabel daftar sesi (index)

| Kondisi sesi | Badge lama | Badge baru |
|---|---|---|
| Ada ≥ 1 item `stok_fisik != stok_sistem` | `X Ada Selisih` (merah) | **`Ada Selisih`** (merah) + info jumlah item di bawah/samping, mis. `3 dari 5 item` |
| Semua item sama | `Semua Sesuai` (hijau) | **`Sesuai`** (hijau) |

- Tambahkan ikon kecil (SVG) di dalam badge: peringatan untuk "Ada Selisih", centang untuk "Sesuai".
- Jangan hanya mengandalkan warna. Teks + ikon harus selalu tampil (aksesibilitas).
- Buat accessor/helper di model header agar logika tidak tersebar di Blade, contoh:
  - `jumlah_item`, `jumlah_selisih`, `punya_selisih` (boolean)
  - Gunakan `withCount` / `loadCount` atau query agregat, hindari N+1.

### 4.2 Badge di modal detail (per baris barang)

| Selisih (`fisik - sistem`) | Badge |
|---|---|
| > 0 | `Lebih +X` (hijau) |
| < 0 | `Kurang -X` (merah) |
| = 0 | `Sesuai` (abu-abu) |

### 4.3 Konsistensi

- Ganti label yang sama di: kartu ringkasan, laporan mutasi stok, tampilan cetak (jika ada), dan test yang meng-assert teks lama.

---

## 5. Perbaikan UX / UI

### 5.1 Halaman Index

- **Summary cards** (4 kartu yang sudah ada), perbaiki:
  - Ikon + warna konsisten. Kartu "Item Selisih" memakai aksen merah/oranye bila nilainya > 0.
  - Angka diformat ribuan (`number_format`).
- **Filter status** di atas tabel (tab/pill): `Semua` · `Ada Selisih` · `Sesuai`.
  - Boleh via query string `?status=` (sisi server) atau filter klien, pilih yang paling sederhana mengikuti pola `<x-table>`.
- **Filter tanggal** (opsional): rentang tanggal mulai–akhir, seperti pola di modul laporan.
- Pencarian tetap aktif (`searchable="true"`), placeholder jelas, mis. "Cari barang, keterangan, atau petugas…".
- Kolom tabel:
  - No. sesi · Tanggal (contoh `15 Nov 2026, 14.30`) · Item (cuplikan 2 nama + `+N lainnya`) · Status · Keterangan · Petugas · Aksi.
  - Keterangan panjang dipotong (`truncate`) dengan `title` tooltip.
- **Empty state**: ilustrasi/ikon + teks "Belum ada sesi stok opname" + tombol "Mulai Stok Opname".
- Urutkan sesi terbaru di atas. Tambahkan pagination bila belum ada.
- Tombol aksi memakai `<x-button-action>` dengan `title` / `aria-label` (Lihat detail, Revisi, Batalkan).

### 5.2 Modal Input Stok Opname

- Judul jelas + deskripsi singkat: "Catat hasil hitung fisik. Stok sistem akan disesuaikan otomatis."
- **Dropdown barang**: bisa dicari (searchable), format `Nama Barang (Stok Sistem: 12 Botol)`.
- Setelah barang dipilih, tampilkan panel ringkas:
  - Stok sistem saat ini · Satuan · Kategori.
- **Selisih live preview** saat user mengetik `stok_fisik`:
  - `fisik - sistem` ditampilkan real-time dengan badge: `Lebih +X` / `Kurang -X` / `Sesuai`.
  - Jika ada selisih, tampilkan hint: "Stok sistem akan berubah dari 10 menjadi 8".
- **Alasan selisih**:
  - Textarea dengan penghitung karakter (`0/500`).
  - Tambahkan *quick chips* yang bisa diklik untuk mengisi cepat: `Barang pecah/bocor`, `Hilang`, `Salah hitung penerimaan`, `Barang aus/rusak`, `Barang ditemukan`, `Sesuai hasil hitung`.
  - Alasan tetap **wajib** (aturan bisnis tidak diubah).
- **Mode batch (multi item)**: tombol "+ Tambah Barang", tiap baris punya tombol hapus; cegah barang yang sama dipilih dua kali dalam satu sesi.
- Tombol submit:
  - Label jelas: "Simpan Stok Opname".
  - Saat submit: disabled + indikator loading agar tidak double submit.
- Validasi:
  - Pesan error tampil di bawah field terkait (Bahasa Indonesia).
  - Jika validasi gagal, modal **terbuka kembali** dengan input lama (`old()`).
  - Input `stok_fisik`: `type="number" min="0" inputmode="numeric"`.

### 5.3 Modal Detail

- Header ringkas: No. sesi, tanggal, petugas, keterangan, badge status sesi.
- Mini-ringkasan: `Total item` · `Ada selisih` · `Sesuai`.
- Tabel rincian: Barang · Kategori · Stok Sistem · Stok Fisik · Selisih (badge) · Alasan.
- Urutkan item **yang ada selisih di atas**.
- Baris bersisih diberi highlight halus (mis. `bg-red-50`), tetap terbaca dengan kontras baik.
- Tabel scrollable horizontal di layar kecil.

### 5.4 Modal Revisi (Edit)

- Tampilkan nilai lama vs nilai baru berdampingan.
- Tampilkan live preview selisih seperti di modal input.
- Pesan error proteksi stok negatif ditampilkan jelas di dalam modal, bukan hilang setelah redirect.

### 5.5 Modal Hapus / Batalkan

- Tetap memakai varian `danger`.
- Tampilkan tabel prediksi pergerakan stok per barang: `Stok sekarang → Stok setelah dibatalkan`.
- Baris yang akan menyebabkan stok negatif diberi peringatan merah + keterangan "Tidak dapat dibatalkan, stok sudah terpakai transaksi lain".
- Tombol konfirmasi: "Ya, Batalkan Sesi". Tombol sekunder: "Kembali".

### 5.6 Umum

- Flash message sukses/error: gaya konsisten, mudah dibaca, bisa ditutup, hilang otomatis untuk pesan sukses.
- Responsif: cek tampilan 375px (mobile), 768px (tablet), 1280px (desktop).
- Aksesibilitas: `label` terhubung ke input, fokus keyboard terlihat, modal bisa ditutup dengan `Esc`, kontras warna memadai.
- Spacing, ukuran font, radius, dan warna mengikuti gaya halaman admin lain agar konsisten.

---

## 6. Perbaikan Teknis (Pendukung)

- Eager loading: `with(['user', 'detail.barang.kategori'])` di `index()` untuk mencegah N+1.
- Pindahkan logika hitung status/selisih ke model atau accessor, bukan di Blade.
- Pastikan dropdown barang mengirim data `stok` dan `satuan` agar live preview bekerja tanpa request tambahan.
- Jangan menambah dependensi Composer/NPM baru tanpa alasan kuat.

---

## 7. Pengujian

- Update test yang meng-assert teks lama (`Semua Sesuai`, `X Ada Selisih`) ke label baru.
- Tambah test:
  - Sesi dengan selisih menampilkan badge `Ada Selisih`.
  - Sesi tanpa selisih menampilkan badge `Sesuai`.
  - Filter status (jika diimplementasikan) mengembalikan data yang benar.
- Jalankan:

```bash
php vendor/phpunit/phpunit/phpunit
```

- Seluruh test harus **hijau** (sebelumnya 41 test, 248 asersi, boleh bertambah).
- Uji manual:
  - Buat opname dengan selisih lebih, kurang, dan sesuai.
  - Revisi opname, lalu coba skenario stok negatif.
  - Batalkan opname, lalu coba skenario rollback yang ditolak.

---

## 8. Kriteria Selesai (Checklist)

- [ ] Badge sesi tampil **"Ada Selisih"** (merah) dan **"Sesuai"** (hijau) dengan ikon.
- [ ] Badge detail tampil `Lebih +X` / `Kurang -X` / `Sesuai`.
- [ ] Label lama tidak tersisa di view, laporan, maupun test.
- [ ] Filter status `Semua / Ada Selisih / Sesuai` berfungsi.
- [ ] Modal input punya live preview selisih, quick chips alasan, dan loading state.
- [ ] Modal detail menampilkan ringkasan dan item bersisih di atas.
- [ ] Modal hapus menampilkan prediksi stok dan peringatan stok negatif.
- [ ] Empty state, flash message, dan pagination rapi.
- [ ] Tampilan responsif (mobile, tablet, desktop).
- [ ] Tidak ada perubahan pada migration, rute, rumus stok, dan aturan validasi.
- [ ] Semua test hijau.

---

## 9. Format Laporan Akhir dari Agent

Setelah selesai, berikan ringkasan singkat berbentuk poin:

- Daftar file yang diubah/ditambah.
- Ringkasan perubahan per file.
- Hasil `phpunit`.
- Hal yang tidak dikerjakan (jika ada) beserta alasannya.
