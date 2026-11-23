# Instruksi Agent: Redesain Struk Thermal 80mm (Gaya Indomaret / 7-Eleven)

> Proyek: Sistem POS & Bengkel **Pradan Motor** (Laravel, Blade, CSS cetak)
> Fokus: **hanya tampilan & hasil cetak struk**. Jangan menyentuh logika kasir, stok, atau database.
> Target kertas: **thermal roll 80 mm** (standar 80 x 80, lebar 80 mm, diameter gulungan ±80 mm)
> Bahasa struk: Bahasa Indonesia

---

## 1. Tujuan

- Hasil cetak struk terlihat **rapi, padat, dan profesional** seperti struk minimarket (Indomaret / 7-Eleven).
- Kolom angka **lurus**, tidak terpotong, tidak ada margin berlebih, tidak ada halaman kosong.
- Satu template, ukuran tetap **80 mm**.

---

## 2. Ruang Lingkup & Aturan

- **Dikerjakan:** view struk, CSS cetak, helper format teks/angka, konfigurasi identitas toko untuk struk, test struk.
- **JANGAN ubah:**
  - migration & skema database
  - logika transaksi, stok, validasi, nomor `TRX-YYYYMMDD-XXXX`
  - nama rute yang sudah ada
  - hak akses struk (kasir hanya miliknya, admin semua)
- Data struk tetap memakai **snapshot** `nama_item` & `harga_satuan` dari `detail_penjualan`.
- Layout struk **berdiri sendiri** (tanpa sidebar/topbar/layout aplikasi).
- Tanpa library baru. Tanpa gambar/logo berat.

---

## 3. Langkah Awal (Discovery)

- Temukan file struk yang sudah ada (kemungkinan `resources/views/struk/penjualan.blade.php`) dan rute cetaknya (`kasir.transaksi.struk`, `admin.riwayat.struk`).
- Catat: CSS `@page` / `@media print` lama, pilihan lebar 58/80, dan tombol toolbar.
- Cek `config/toko.php` (nama, alamat, telepon, footer). Jika belum ada, buat.
- **Jika sebelumnya ada opsi 58mm, sederhanakan ke 80mm saja** (hapus pilihan lebar yang tidak dipakai, atau biarkan default 80 tanpa UI pilihan).

---

## 4. Spesifikasi Ukuran (80 mm)

- Kertas: **80 mm**. Area cetak aman: **72 mm** (margin kiri-kanan ±4 mm).
- Printer thermal 80mm umumnya 576 dot (≈ 72 mm). Rancang konten untuk lebar **72 mm**.
- Grid teks: **48 karakter per baris** (font monospace, kolom fixed).
- `@page { size: 80mm auto; margin: 0; }`
- Wadah struk: `width: 72mm; margin: 0 auto;` (di layar) dan `margin: 0 4mm` / padding 0 4mm saat print.
- Tinggi mengikuti isi (`auto`). Tidak ada `min-height`, tidak ada `100vh`.
- Tambah ruang kosong ±10 mm di bawah footer agar aman dari auto-cutter.

---

## 5. Gaya Visual (Referensi Struk Minimarket)

- **Font:** monospace (`'Courier New', Courier, monospace`) agar kolom lurus.
  - Ukuran dasar: **12px** (≈ 48 kolom di 72 mm). Uji, lalu sesuaikan agar tepat 48 kolom tanpa wrap.
  - `line-height: 1.25`, `letter-spacing: 0`.
  - Gunakan satuan `ch` untuk lebar kolom agar rapi, mis. `width: 48ch`.
- **Warna:** hitam murni (`#000`) di atas putih. Tanpa abu-abu, tanpa background, tanpa bayangan, tanpa border tebal.
- **Penekanan:** hanya `font-weight: bold` untuk nama toko dan baris TOTAL. Hindari italic dan huruf miring.
- **Pemisah:** garis putus-putus `----` (48 karakter) atau `border-top: 1px dashed #000`. Jangan campur keduanya. Pakai satu gaya konsisten.
- **Rata teks:**
  - header & footer: tengah
  - nama item: kiri
  - angka: kanan (kolom harga & subtotal rata kanan)
- Teks huruf besar untuk label ringkasan (`TOTAL`, `TUNAI`, `KEMBALI`).

---

## 6. Struktur & Isi Struk (Urutan dari Atas)

1. **Header** (tengah)
   - Nama bengkel (tebal, boleh 1 tingkat lebih besar, mis. `14px`)
   - Tagline/jenis usaha (opsional dari config)
   - Alamat (wrap otomatis, maks. 2 baris)
   - Telepon
2. Pemisah
3. **Info transaksi** (2 kolom: kiri–kanan)
   - Baris 1: tanggal & jam `15/11/2026 14:30` (kiri), nomor nota (kanan)
   - Baris 2: `Kasir : <nama>` (kiri), `Plg : <nama/Umum>` (kanan)
   - Jika pelanggan punya plat nomor: baris tambahan `Plat : AE 1234 XY`
4. Pemisah
5. **Rincian item**: format 2 baris per item (gaya minimarket):
   - Baris 1: nama item (kiri, boleh wrap, tidak dipotong)
   - Baris 2: `  qty x harga` (kiri, indent 2 spasi) dan subtotal (kanan)
   - Jasa servis diberi prefiks `[JASA]` pada nama
6. Pemisah
7. **Ringkasan** (label kiri, angka kanan)
   - `Total Item` (jumlah qty seluruh baris)
   - `TOTAL` (tebal)
   - `TUNAI`
   - `KEMBALI`
8. Pemisah
9. **Footer** (tengah)
   - `TERIMA KASIH ATAS KUNJUNGAN ANDA`
   - Catatan kebijakan (dari config, opsional)
   - Teks kecil penutup (opsional)
10. Ruang kosong bawah ±10 mm

### Contoh target tampilan (48 kolom, monospace)

```
                  PRADAN MOTOR
          Bengkel & Suku Cadang Motor
         Jl. Contoh Raya No. 12, Madiun
               Telp. 0351-123456
------------------------------------------------
15/11/2026 14:30               TRX-20261115-0001
Kasir : Budi                          Plg : Umum
------------------------------------------------
Oli MPX2 0.8L
  2 x 65.000                             130.000
Busi NGK CPR9EA
  1 x 28.000                              28.000
Kampas Rem Depan Vario 125 (Original)
  Honda Genuine Parts
  1 x 85.000                              85.000
[JASA] Tune Up Injeksi
  1 x 50.000                              50.000
------------------------------------------------
Total Item                                     5
TOTAL                                    293.000
TUNAI                                    300.000
KEMBALI                                    7.000
------------------------------------------------
        TERIMA KASIH ATAS KUNJUNGAN ANDA
         Barang yang sudah dibeli tidak
               dapat dikembalikan
```

> Contoh di atas hanya ilustrasi tata letak. Hasil akhir harus lurus pada **48 kolom**, dengan data asli dari database.

---

## 7. Aturan Format Data

- **Rupiah:** titik sebagai pemisah ribuan, **tanpa** awalan `Rp` pada baris item (gaya minimarket): `65.000`. Pada ringkasan boleh tanpa `Rp` juga, konsisten.
- **Tanggal & jam:** `d/m/Y H:i`, zona waktu `Asia/Jakarta`.
- **Nama item panjang:**
  - wrap per kata ke baris berikutnya (maks. 48 karakter per baris), jangan dipotong di tengah kata.
  - baris lanjutan diberi indent 0, sedangkan baris `qty x harga` tetap di baris terakhir.
- **Nama pelanggan/kasir panjang:** potong dengan `…` agar kolom kanan tidak terdorong (maks. ±20 karakter).
- **Qty desimal:** tidak dipakai (integer).
- Karakter non-ASCII (mis. emoji, aksara khusus) dibersihkan agar tidak rusak di printer.
- Buat helper terpusat, mis. `App\Support\StrukFormatter`:
  - `rupiah(int)`, `baris(kiri, kanan, lebar = 48)`, `tengah(teks, lebar = 48)`, `bungkus(teks, lebar = 48)`.
  - Dipakai oleh view agar logika tidak tersebar di Blade.

---

## 8. Implementasi CSS (Panduan)

- Struktur: gunakan baris berbasis flex/grid dengan `ch` agar presisi, contoh pola:

```css
@page { size: 80mm auto; margin: 0; }

.struk {
  width: 72mm;
  margin: 0 auto;
  padding: 3mm 0 10mm;
  font-family: 'Courier New', Courier, monospace;
  font-size: 12px;
  line-height: 1.25;
  color: #000;
  background: #fff;
}
.struk .tengah { text-align: center; }
.struk .baris  { display: flex; justify-content: space-between; gap: 1ch; }
.struk .tebal  { font-weight: 700; }
.struk .garis  { border-top: 1px dashed #000; margin: 1.5mm 0; }
.struk .item-nama { word-break: break-word; }
.struk .angka  { text-align: right; white-space: nowrap; }

@media print {
  html, body { margin: 0; padding: 0; background: #fff; }
  .struk { margin: 0; padding: 0 4mm 10mm; width: 80mm; box-sizing: border-box; }
  .no-print { display: none !important; }
  .baris, .item { break-inside: avoid; }
}
```

- Pastikan `-webkit-print-color-adjust: exact` agar hitam pekat, tanpa efek dither.
- Tidak ada `overflow: hidden` yang memotong teks. Tidak ada scrollbar saat cetak.
- Di layar: tampilkan pratinjau struk di tengah (lebar 80mm, bayangan lembut) dengan toolbar `no-print` berisi `Cetak` dan `Kembali`. Pratinjau tidak boleh ikut tercetak.

---

## 9. Pengujian

- Test (Feature):
  - Struk memuat: nama toko, nomor nota, kasir, tiap `nama_item`, `qty`, subtotal, total, tunai, kembali.
  - Struk memuat tanda `[JASA]` untuk item jasa.
  - Struk tetap memakai snapshot saat harga master diubah.
  - Hak akses: kasir lain 403, admin 200, guest redirect login.
  - Unit test `StrukFormatter`: `baris()` selalu menghasilkan tepat 48 karakter, `bungkus()` tidak memotong kata, `rupiah()` benar (`1234567` → `1.234.567`).
- Jalankan: `php vendor/phpunit/phpunit/phpunit` (seluruh test lama harus tetap hijau).
- Uji manual (wajib):
  - Cetak ke printer thermal 80mm asli atau "Save as PDF" ukuran 80mm.
  - Kasus uji: 1 item, 10+ item, nama item sangat panjang, keranjang campuran (barang + jasa), pelanggan umum, pelanggan dengan plat nomor, total di atas 1 juta.
  - Pengaturan dialog cetak: kertas 80mm, margin **None**, skala **100%**, header/footer browser **off**.

---

## 10. Kriteria Selesai (Checklist)

- [ ] Struk berukuran 80 mm, tanpa pilihan lebar lain.
- [ ] Tata letak mengikuti urutan header → info → item → ringkasan → footer.
- [ ] Item memakai format 2 baris (nama, lalu `qty x harga` + subtotal rata kanan).
- [ ] Kolom angka lurus (grid 48 karakter), tidak ada teks terpotong.
- [ ] Hanya satu gaya pemisah, hitam murni, tanpa elemen dekoratif.
- [ ] Nama item panjang ter-wrap rapi per kata.
- [ ] Tidak ada halaman kosong dan tidak ada margin berlebih saat cetak.
- [ ] Ruang kosong bawah cukup untuk auto-cutter.
- [ ] Pratinjau layar tidak ikut tercetak (`no-print`).
- [ ] Identitas toko dari `config/toko.php` / `.env`.
- [ ] Logika kasir, stok, rute, dan database **tidak berubah**.
- [ ] Semua test hijau.

---

## 11. Format Laporan Akhir dari Agent

Berikan ringkasan singkat berbentuk poin:

- File yang diubah/ditambah.
- Ringkasan perubahan per file.
- Hasil `phpunit`.
- Hal yang belum dikerjakan (jika ada) beserta alasannya.