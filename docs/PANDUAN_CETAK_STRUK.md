# Panduan Teknis Cetak Struk Printer Thermal POS

Dokumen ini menjelaskan tata cara instalasi, konfigurasi, operasional, dan pemecahan masalah cetak struk menggunakan printer thermal roll ukuran **58mm** dan **80mm** pada **Sistem POS Bengkel & Toko Pradan Motor**.

---

## 1. Arsitektur & Jalur Cetak Struk

Sistem POS Pradan Motor menggunakan arsitektur web modern untuk pencetakan struk:
1. **Web Print via Iframe Tersembunyi (Utama)**: Saat kasir menekan tombol *"Cetak Struk"*, sistem memuat template struk khusus (`/kasir/transaksi/{id}/struk?autoprint=1&lebar=58`) ke dalam elemen `<iframe>` tersembunyi tanpa mengalihkan halaman kasir.
2. **Template Monospace & CSS @page**: Tampilan struk menggunakan font monospace dengan satuan milimeter (`48mm` untuk roll 58mm dan `72mm` untuk roll 80mm) sehingga teks tidak meluber atau terpotong.
3. **Penyimpanan Preferensi Lokal**: Pilihan lebar kertas (58mm / 80mm) serta opsi *"Cetak otomatis setelah transaksi"* disimpan secara otomatis di peramban (`localStorage`).

---

## 2. Langkah Instalasi Driver & Konfigurasi Windows

### A. Instalasi Driver Printer Thermal
1. Sambungkan printer thermal (USB) ke komputer kasir dan nyalakan daya printer.
2. Pasang driver bawaan printer (misal: *POS-58*, *POS-80*, *Epson TM-T82*, *Xprinter*, *Panda*, atau driver generic *Generic / Text Only*).
3. Buka **Control Panel** $\rightarrow$ **Devices and Printers** (atau *Settings* $\rightarrow$ *Bluetooth & devices* $\rightarrow$ *Printers & scanners*).
4. Pastikan printer thermal terdeteksi dengan benar.

### B. Menyetel Ukuran Kertas Bawaan (Default Paper Size)
1. Klik kanan pada printer thermal $\rightarrow$ pilih **Printing Preferences**.
2. Masuk ke tab **Layout** / **Paper/Quality** $\rightarrow$ klik **Advanced...**.
3. Pada opsi **Paper Size**, pilih:
   - **58mm x 297mm** (atau *Roll Paper 58 x 297 mm*) untuk printer 58mm.
   - **80mm x 297mm** (atau *Roll Paper 80 x 297 mm*) untuk printer 80mm.
4. Pada tab **Device Settings**, jika printer memiliki pisau pemotong otomatis (*auto-cutter*), aktifkan opsi **Cut Paper: After Page / Document**.
5. Klik **Apply** dan **OK**.

### C. Menjadikan Printer Thermal sebagai Default
1. Klik kanan pada printer thermal $\rightarrow$ pilih **Set as default printer**.
2. Hal ini memastikan jendela dialog cetak selalu otomatis mengarahkan output ke printer kasir.

---

## 3. Pengaturan Dialog Cetak Browser (Google Chrome / Microsoft Edge)

Saat pertama kali dialog cetak browser muncul (`Ctrl + P` atau otomatis via tombol):
1. **Destination**: Pilih printer thermal yang sudah terpasang.
2. **Pages**: *All*.
3. **Layout**: *Portrait*.
4. **More Settings**:
   - **Paper Size**: Pilih `58mm` / `80mm` (sesuai kertas).
   - **Margins**: Pilih **None** (Sangat Penting agar tidak ada margin putih besar di pinggir).
   - **Scale**: Pilih **100%** (atau *Fit to printable area* jika teks terlalu tepi).
   - **Options**:
     - **Headers and footers**: **Hapus centang / Uncheck** (agar URL dan tanggal browser tidak tercetak di bagian atas dan bawah struk).
     - **Background graphics**: Boleh dicentang atau tidak (struk dirancang dengan teks hitam murni).

---

## 4. Mode Cetak Instan Tanpa Dialog (Kiosk Printing)

Untuk operasional kasir cepat di mana nota langsung keluar dari printer seketika tombol cetak ditekan (tanpa muncul dialog print Chrome):

### Cara Mengaktifkan Silent Printing di Chrome / Edge:
1. Tutup seluruh jendela Google Chrome.
2. Klik kanan pada shortcut **Google Chrome** di Desktop $\rightarrow$ pilih **Properties**.
3. Pada kolom **Target**, tambahkan parameter `--kiosk-printing` di ujung path:
   ```text
   "C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk-printing
   ```
4. Klik **Apply** dan **OK**.
5. Buka Chrome melalui shortcut tersebut. Sekarang, perintah cetak akan langsung diteruskan ke printer default tanpa menampilkan dialog konfirmasi print.

---

## 5. Pemecahan Masalah (Troubleshooting)

| Gejala | Penyebab Umum | Solusi Perbaikan |
|---|---|---|
| **Struk terpotong di tepi kanan** | Ukuran di sistem dipilih 80mm padahal printer menggunakan kertas 58mm. | Buka menu pratinjau struk atau klik tombol toggle `58mm` pada toolbar struk. Sistem akan mengingat pilihan tersebut. |
| **Banyak ruang kosong di kiri-kanan (margin terlalu lebar)** | Margin browser disetel ke *Default* atau *Minimum*. | Pada dialog cetak browser, buka *More settings* dan ubah opsi **Margins** menjadi **None**. |
| **Ada tulisan URL, Judul Halaman, dan Tanggal di struk** | Opsi *Headers and footers* browser aktif. | Hapus centang pada opsi **Headers and footers** di pengaturan dialog cetak browser. |
| **Kertas keluar terlalu panjang (panjang kosong tak terpakai)** | Ukuran kertas printer diatur ke ukuran halaman tetap (misal: *Letter* atau *A4*). | Di *Printing Preferences* driver printer Windows, setel ukuran kertas ke **Roll Paper** atau **Continuous Paper**. |
| **Teks terlalu kecil atau rapat** | Skala cetak diatur ke persentase rendah (*Fit to Page* mengecilkan). | Di dialog cetak browser, setel **Scale** ke **100%** (Custom 100). |
| **Kertas tidak otomatis terpotong (auto-cutter tidak aktif)** | Fitur cutter pada driver belum diaktifkan. | Buka *Printing Preferences* $\rightarrow$ *Device Settings* $\rightarrow$ aktifkan *Cut Paper / Partial Cut* pada akhir pencetakan. Template sistem juga telah dilengkapi *cutter space* 10mm di bagian bawah. |

---

## 6. Integrasi Tingkat Lanjut (ESC/POS Langsung - Opsional)

Jika di kemudian hari bengkel membutuhkan komunikasi raw byte langsung ke printer tanpa melalui spooler driver Windows (misal untuk perangkat tablet Android atau port Serial/Network Ethernet):
- Protokol **ESC/POS** dapat diintegrasikan menggunakan library PHP `mike42/escpos-php` atau Web Serial API (`navigator.serial`).
- Perintah dasar ESC/POS:
  - Inisialisasi: `ESC @` (`\x1b\x40`)
  - Rata Tengah: `ESC a 1` (`\x1b\x61\x01`)
  - Cetak Tebal: `ESC E 1` (`\x1b\x45\x01`)
  - Pemotong Kertas: `GS V 66 0` (`\x1d\x56\x42\x00`)
- Saat ini, jalur browser print terstandarisasi dengan `@page 58mm/80mm` dan mode *kiosk printing* terbukti paling stabil, kompatibel dengan segala merk printer thermal USB (Xprinter, Eppos, Panda, Epson, Iware, Kasir POS), dan tidak memerlukan instalasi service latar belakang tambahan.

