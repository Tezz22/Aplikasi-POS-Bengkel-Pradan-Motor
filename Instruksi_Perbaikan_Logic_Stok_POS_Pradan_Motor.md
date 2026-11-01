# Instruksi Perbaikan Logic Master Data Barang, Barang Masuk, dan Stok

## POS Bengkel Pradan Motor

Dokumen ini menjadi instruksi kerja untuk memperbaiki alur dan logic
inventaris pada aplikasi POS Bengkel Pradan Motor.

> **ATURAN MUTLAK: JANGAN MENGUBAH, MENGHAPUS, MEMBUAT ULANG, ATAU
> MENJALANKAN REFRESH TERHADAP FILE MIGRATION YANG SUDAH ADA.**
>
> Seluruh perbaikan harus dilakukan pada level kode aplikasi: Model,
> Controller, Service (jika diperlukan), Form Request/validasi, route,
> dan Blade/view/JavaScript. Pertahankan struktur tabel, nama kolom,
> tipe data, foreign key, dan relasi database yang sudah tersedia. Jika
> ada kebutuhan yang ternyata tidak dapat dipenuhi oleh skema saat ini,
> hentikan dan laporkan kendalanya terlebih dahulu. Jangan membuat
> migration baru tanpa persetujuan eksplisit.

------------------------------------------------------------------------

## 1. Tujuan Perbaikan

Pastikan seluruh halaman inventaris menggunakan satu sumber data barang
yang sama, yaitu tabel master barang (`ms_barang`, atau nama tabel
aktual yang sudah digunakan proyek).

Pisahkan tanggung jawab setiap halaman:

1.  **Master Data Barang** mengelola identitas dan informasi barang.
2.  **Barang Masuk** mencatat pembelian/restock dari supplier dan
    menambah stok barang yang sudah terdaftar.
3.  **Penjualan** mengurangi stok barang.
4.  **Stok Opname** menyesuaikan stok sistem berdasarkan hasil
    penghitungan fisik.

Jangan membuat tabel atau sumber data barang kedua. Jangan menyimpan
salinan master barang di tabel transaksi.

## 2. Aturan Bisnis yang Wajib Diterapkan

### 2.1 Master Data Barang

-   Admin tetap dapat menambahkan barang baru melalui halaman Master
    Data Barang.
-   Form tambah barang hanya meminta informasi identitas dan atribut
    barang yang memang tersedia pada skema saat ini, misalnya kode
    barang, nama barang, kategori, harga modal, harga jual, dan stok
    minimum.
-   Saat barang baru dibuat melalui Master Data Barang, stok awal harus
    otomatis `0`.
-   Jangan menyediakan input stok bebas pada form tambah maupun edit
    Master Data Barang.
-   Jika kolom stok wajib diisi oleh database dan tidak memiliki
    default, isi nilai `0` dari controller saat proses pembuatan. Jangan
    mengubah migration.
-   Admin dapat mengubah informasi barang seperti nama, kategori, harga
    modal, harga jual, dan stok minimum sesuai kolom yang tersedia.
-   Perubahan informasi barang tidak boleh mengubah stok.
-   Halaman Master Data Barang harus menampilkan stok terkini dari
    record barang yang sama, bukan menghitung atau menyimpan stok
    terpisah di view.
-   Kasir hanya dapat melihat katalog dan stok sesuai pembatasan akses
    yang sudah ditetapkan proyek.

### 2.2 Barang Masuk

-   Barang Masuk hanya boleh memilih barang yang sudah terdaftar di
    Master Data Barang.
-   Gunakan dropdown/autocomplete yang mengambil data langsung dari
    master barang.
-   Jangan membuat input nama barang bebas yang kemudian menyimpan
    barang duplikat.
-   Jika barang belum terdaftar, admin harus mendaftarkannya terlebih
    dahulu melalui Master Data Barang. Setelah itu barang tersedia pada
    pilihan Barang Masuk.
-   Form Barang Masuk mencatat supplier, tanggal, pengguna yang
    melakukan input, serta detail barang dan jumlah sesuai struktur
    database yang sudah ada.
-   Saat transaksi Barang Masuk berhasil disimpan, stok barang bertambah
    sesuai kuantitas pada setiap detail pembelian.
-   Harga beli pada detail pembelian harus disimpan sebagai nilai
    transaksi saat pembelian dilakukan. Jangan mengandalkan harga modal
    master yang dapat berubah di kemudian hari untuk menghitung ulang
    riwayat pembelian.
-   Hitung subtotal setiap detail dan total transaksi di sisi server.
    Jangan mempercayai total yang dikirim dari browser.
-   Jika satu transaksi berisi beberapa barang, proses penyimpanan
    header, detail, dan penambahan stok harus konsisten sebagai satu
    kesatuan.

### 2.3 Penjualan

-   Ketika transaksi penjualan berhasil disimpan, stok barang berkurang
    sesuai kuantitas yang terjual.
-   Validasi stok tersedia sebelum mengurangi stok.
-   Tolak transaksi jika stok tidak mencukupi.
-   Jangan mengurangi stok sebelum transaksi dipastikan valid.
-   Pastikan pengurangan stok tidak terjadi dua kali akibat submit ganda
    atau pemanggilan logic yang berulang.
-   Jika transaksi penjualan dibatalkan/dihapus melalui fitur yang
    memang diizinkan, kembalikan stok sesuai detail penjualan yang
    dibatalkan. Ikuti aturan bisnis dan hak akses yang sudah ada.

### 2.4 Stok Opname

-   Stok Opname menjadi mekanisme koreksi stok berdasarkan jumlah fisik.
-   Jangan menggunakan halaman Master Data Barang untuk koreksi stok.
-   Saat opname disimpan, hitung selisih antara stok fisik dan stok
    sistem, lalu terapkan penyesuaian sesuai struktur dan logic yang
    tersedia.
-   Simpan informasi pelaksana dan riwayat penyesuaian menggunakan
    tabel/kolom yang sudah ada.
-   Pastikan penyesuaian tidak diterapkan dua kali.

### 2.5 Penghapusan Barang Masuk

-   Jangan menghapus transaksi Barang Masuk secara langsung tanpa
    memperhitungkan dampaknya terhadap stok.
-   Jika fitur hapus Barang Masuk tetap tersedia, sebelum menghapus
    transaksi hitung seluruh kuantitas pada detail pembeliannya.
-   Kurangi kembali stok barang terkait sesuai kuantitas yang sebelumnya
    ditambahkan oleh transaksi tersebut.
-   Tolak penghapusan jika pengurangan balik akan menyebabkan stok
    negatif. Tampilkan pesan yang menjelaskan bahwa stok sudah digunakan
    pada transaksi lain dan perlu penanganan terlebih dahulu.
-   Hapus header dan detail transaksi dalam satu database transaction.
-   Cegah pembatalan/penghapusan transaksi yang sama diproses lebih dari
    sekali.
-   Jangan menghapus master barang ketika menghapus transaksi Barang
    Masuk.

## 3. Alur Penggunaan yang Diharapkan

### Skenario A --- Mendaftarkan barang baru

1.  Admin membuka menu Master Data Barang.
2.  Admin memilih Tambah Barang.
3.  Admin mengisi identitas barang, kategori, harga, dan atribut lain
    yang tersedia.
4.  Sistem menyimpan barang dengan stok `0`.
5.  Barang langsung muncul pada daftar Master Data Barang.
6.  Barang tersedia pada pilihan Barang Masuk.

### Skenario B --- Menambahkan stok barang

1.  Admin membuka menu Barang Masuk.
2.  Admin memilih supplier.
3.  Admin memilih barang dari daftar Master Data Barang.
4.  Admin mengisi kuantitas dan harga beli aktual.
5.  Admin dapat menambahkan beberapa baris detail barang.
6.  Sistem memvalidasi seluruh data.
7.  Sistem menyimpan transaksi pembelian dan detailnya.
8.  Sistem menambahkan stok setiap barang sesuai kuantitas yang masuk.
9.  Halaman Master Data Barang menampilkan stok terbaru secara otomatis.

### Skenario C --- Barang terjual

1.  Kasir memilih barang dari katalog.
2.  Sistem menampilkan stok terkini.
3.  Sistem memvalidasi stok saat transaksi diproses.
4.  Jika stok mencukupi dan transaksi berhasil, stok dikurangi sesuai
    kuantitas terjual.
5.  Master Data Barang dan katalog kasir menampilkan stok terbaru.

### Skenario D --- Barang belum terdaftar

1.  Admin tidak menemukan barang pada pilihan Barang Masuk.
2.  Admin membuka Master Data Barang.
3.  Admin mendaftarkan barang tersebut.
4.  Sistem menyimpan barang dengan stok `0`.
5.  Admin kembali ke Barang Masuk dan memilih barang yang baru
    didaftarkan.
6.  Admin melanjutkan pencatatan pembelian.

------------------------------------------------------------------------

## 4. Ketentuan Teknis Implementasi

### 4.1 Audit struktur proyek sebelum mengubah kode

Sebelum melakukan perubahan:

-   Periksa migration yang sudah ada hanya untuk membaca struktur
    database.
-   Periksa Model barang, kategori, supplier, pembelian, detail
    pembelian, penjualan, detail penjualan, dan stok opname.
-   Periksa nama tabel, primary key, foreign key, nama kolom, tipe data,
    dan relasi yang benar-benar digunakan.
-   Periksa controller dan route untuk Master Data Barang, Barang Masuk,
    Penjualan, dan Stok Opname.
-   Periksa Blade/view dan JavaScript form yang berkaitan.
-   Jangan berasumsi nama kolom atau primary key sama dengan contoh pada
    dokumen ini. Ikuti struktur aktual proyek.
-   Jangan mengganti nama route, tabel, kolom, atau endpoint yang sudah
    digunakan tanpa alasan kuat dan tanpa memastikan seluruh
    pemanggilnya diperbarui.

### 4.2 Konsistensi database

-   Gunakan `DB::transaction()` untuk proses yang melibatkan penyimpanan
    transaksi, detail, dan perubahan stok.
-   Gunakan locking yang sesuai, misalnya `lockForUpdate()`, ketika
    membaca stok sebelum menguranginya agar dua transaksi bersamaan
    tidak menyebabkan stok negatif.
-   Lakukan validasi ulang di server, meskipun browser sudah melakukan
    validasi.
-   Gunakan relasi Eloquent yang sudah tersedia jika sesuai dengan
    struktur proyek.
-   Gunakan operasi increment/decrement atau update atomik yang aman dan
    konsisten.
-   Pastikan kegagalan menyimpan salah satu detail menyebabkan seluruh
    proses transaksi dibatalkan, termasuk perubahan stok.
-   Hindari query berulang yang tidak diperlukan; gunakan eager loading
    untuk daftar barang beserta kategori jika relevan.

### 4.3 Validasi input

Validasi minimal yang perlu diterapkan sesuai kolom aktual:

-   Barang harus benar-benar ada pada master barang.
-   ID supplier harus valid.
-   Kuantitas harus bilangan bulat positif.
-   Harga beli harus numerik dan tidak negatif, sesuai aturan bisnis
    proyek.
-   Detail barang tidak boleh kosong.
-   Barang yang sama dalam satu transaksi harus ditangani secara
    konsisten: boleh digabung atau ditolak sebagai duplikat, tetapi
    jangan sampai stok dihitung keliru.
-   Pastikan format tanggal dan nilai uang valid.
-   Jangan menerima `stok` dari request tambah/edit master barang untuk
    mengubah stok.
-   Jangan menerima total transaksi dari browser sebagai nilai final
    tanpa menghitung ulang di server.

### 4.4 Keamanan dan otorisasi

-   Pertahankan pembatasan role Admin dan Kasir yang sudah ditetapkan
    proyek.
-   Hanya Admin yang boleh mengelola Master Data Barang, Barang Masuk,
    Supplier, Kategori, dan Stok Opname.
-   Kasir hanya boleh menggunakan fitur operasional yang telah
    diizinkan, termasuk membaca katalog/stok dan melakukan penjualan.
-   Jangan hanya menyembunyikan tombol di Blade. Pastikan
    route/controller juga memeriksa otorisasi.
-   Jangan mengubah sistem autentikasi atau role di luar kebutuhan
    perbaikan ini.

### 4.5 Antarmuka pengguna

-   Pertahankan desain, warna, layout, komponen, dan gaya antarmuka yang
    sudah ada.
-   Pada form Master Data Barang, hilangkan input stok manual jika
    tersedia.
-   Pada form Barang Masuk, gunakan pilihan barang dari Master Data
    Barang.
-   Tampilkan informasi stok saat ini pada pilihan barang jika
    memungkinkan.
-   Setelah transaksi Barang Masuk berhasil, tampilkan notifikasi
    berhasil dan data stok terbaru.
-   Berikan pesan validasi yang jelas jika barang tidak ditemukan,
    kuantitas tidak valid, stok tidak cukup, atau penghapusan transaksi
    tidak dapat dilakukan.
-   Jangan mengubah tampilan halaman lain yang tidak berkaitan.

------------------------------------------------------------------------

## 5. Ketentuan Khusus: Dilarang Mengubah Migration

Ini adalah batasan utama pengerjaan:

1.  Jangan mengedit file apa pun di `database/migrations`.
2.  Jangan membuat migration baru.
3.  Jangan menjalankan `php artisan migrate:fresh`.
4.  Jangan menjalankan `php artisan migrate:refresh`.
5.  Jangan menjalankan rollback migration.
6.  Jangan menghapus atau membuat ulang tabel.
7.  Jangan mengubah struktur database secara manual melalui SQL/GUI.
8.  Jangan mengubah tipe data, nama kolom, primary key, foreign key,
    index, atau default database.
9.  Jangan menghapus data yang sudah ada.
10. Jangan melakukan seeding ulang yang berpotensi menggandakan atau
    menghapus data tanpa persetujuan.

Jika logic yang diminta tidak dapat diterapkan karena struktur database
saat ini tidak mendukungnya, **jangan mencari jalan pintas dengan
mengubah database**. Laporkan: - bagian yang tidak dapat diterapkan; -
tabel dan kolom terkait; - alasan teknis; - alternatif yang masih bisa
dilakukan tanpa migration.

Tunggu persetujuan sebelum mengambil tindakan yang mengubah struktur
database.

------------------------------------------------------------------------

## 6. Skenario Pengujian Wajib

Lakukan pengujian setelah implementasi. Gunakan data uji yang aman dan
jangan menghapus data produksi.

  -----------------------------------------------------------------------
  No.                     Skenario                Hasil yang diharapkan
  ----------------------- ----------------------- -----------------------
  1                       Tambah barang baru dari Barang tersimpan dengan
                          Master Data             stok 0

  2                       Edit nama/harga barang  Informasi berubah, stok
                                                  tetap

  3                       Tambah Barang Masuk     Stok bertambah sesuai
                          untuk barang terdaftar  kuantitas

  4                       Barang Masuk dengan     Semua stok bertambah
                          beberapa detail barang  sesuai detail

  5                       Barang Masuk dengan ID  Ditolak, tidak ada stok
                          barang tidak valid      berubah

  6                       Barang Masuk dengan     Ditolak
                          kuantitas 0/negatif     

  7                       Barang Masuk gagal di   Seluruh perubahan
                          tengah proses           dibatalkan

  8                       Penjualan berhasil      Stok berkurang sesuai
                                                  kuantitas

  9                       Penjualan melebihi stok Ditolak, stok tidak
                                                  berubah

  10                      Dua transaksi mencoba   Stok tidak menjadi
                          memakai stok yang sama  negatif

  11                      Hapus/batalkan Barang   Stok dikurangi kembali
                          Masuk                   dengan aman atau
                                                  penghapusan ditolak

  12                      Stok Opname             Stok mengikuti hasil
                                                  penyesuaian tanpa
                                                  diterapkan dua kali

  13                      Buka Master Data        Stok terbaru tampil
                          setelah Barang Masuk    

  14                      Buka katalog kasir      Stok terbaru tampil
                          setelah penjualan       

  15                      Kasir mencoba membuka   Akses ditolak
                          route khusus Admin      
  -----------------------------------------------------------------------

------------------------------------------------------------------------

## 7. Kriteria Penerimaan

Pekerjaan dianggap selesai jika:

-   Master Data Barang menjadi satu-satunya sumber identitas barang.
-   Barang baru dapat didaftarkan tanpa menambah stok.
-   Stok awal barang baru otomatis `0`.
-   Barang Masuk hanya memilih barang yang sudah terdaftar.
-   Barang Masuk menambah stok secara otomatis dan konsisten.
-   Penjualan mengurangi stok secara otomatis dan aman.
-   Stok Opname menjadi jalur koreksi stok.
-   Semua halaman membaca stok dari sumber data yang sama.
-   Tidak ada duplikasi master barang.
-   Riwayat pembelian dan penjualan tetap tersimpan.
-   Hak akses Admin/Kasir tetap berlaku.
-   Tampilan utama tidak berubah di luar kebutuhan.
-   **Tidak ada satu pun file migration yang diubah atau ditambahkan.**
-   Tidak ada perintah migrate, rollback, refresh, atau fresh yang
    dijalankan.

## 8. Instruksi Pelaporan Setelah Selesai

Setelah implementasi, berikan laporan singkat berisi:

1.  File aplikasi yang diubah beserta alasan perubahannya.
2.  Logic yang diperbaiki pada Master Data Barang.
3.  Logic yang diperbaiki pada Barang Masuk.
4.  Logic stok pada Penjualan dan Stok Opname yang ikut disesuaikan.
5.  Validasi dan pengamanan yang ditambahkan.
6.  Hasil pengujian setiap skenario.
7.  Konfirmasi bahwa seluruh migration tetap tidak berubah.
8.  Kendala atau kebutuhan lanjutan yang belum dapat diselesaikan tanpa
    perubahan struktur database.

**Prioritas utama: perbaiki logic aplikasi dengan struktur database yang
sudah ada. Jangan mengubah migration dalam kondisi apa pun tanpa
persetujuan eksplisit.**
