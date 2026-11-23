<?php

namespace Tests\Unit;

use App\Support\StrukFormatter;
use PHPUnit\Framework\TestCase;

class StrukFormatterTest extends TestCase
{
    public function test_rupiah_memformat_angka_dengan_titik_tanpa_awalan_rp(): void
    {
        $this->assertEquals('1.234.567', StrukFormatter::rupiah(1234567));
        $this->assertEquals('65.000', StrukFormatter::rupiah(65000));
        $this->assertEquals('0', StrukFormatter::rupiah(0));
        $this->assertEquals('0', StrukFormatter::rupiah(null));
        $this->assertEquals('250.000', StrukFormatter::rupiah(250000.0));
    }

    public function test_baris_selalu_menghasilkan_tepat_48_karakter(): void
    {
        // 1. Kasus umum (kiri dan kanan normal)
        $baris1 = StrukFormatter::baris('Kasir : Budi', 'Plg : Umum', 48);
        $this->assertEquals(48, strlen($baris1));
        $this->assertStringStartsWith('Kasir : Budi', $baris1);
        $this->assertStringEndsWith('Plg : Umum', $baris1);

        // 2. Baris item qty x harga dan subtotal
        $barisItem = StrukFormatter::baris('  2 x 65.000', '130.000', 48);
        $this->assertEquals(48, strlen($barisItem));
        $this->assertStringStartsWith('  2 x 65.000', $barisItem);
        $this->assertStringEndsWith('130.000', $barisItem);

        // 3. Teks kiri sangat panjang melebihi batas 48 karakter
        $barisPanjang = StrukFormatter::baris('Nama Suku Cadang Sangat Panjang Sekali Melebihi Batas Kolom Cetak Struk 80mm', '950.000', 48);
        $this->assertEquals(48, strlen($barisPanjang));
        $this->assertStringEndsWith('950.000', $barisPanjang);

        // 4. Kiri kosong
        $barisKosongKiri = StrukFormatter::baris('', '293.000', 48);
        $this->assertEquals(48, strlen($barisKosongKiri));
        $this->assertStringEndsWith('293.000', $barisKosongKiri);

        // 5. Kanan kosong
        $barisKosongKanan = StrukFormatter::baris('TOTAL ITEM', '', 48);
        $this->assertEquals(48, strlen($barisKosongKanan));
        $this->assertStringStartsWith('TOTAL ITEM', $barisKosongKanan);

        // 6. Kanan sangat panjang mendekati atau melebihi lebar
        $barisKananPanjang = StrukFormatter::baris('Kiri', 'KananSangatPanjangYangMelebihiTotalLebarEmpatPuluhDelapanKarakterIni', 48);
        $this->assertEquals(48, strlen($barisKananPanjang));
    }

    public function test_bungkus_tidak_memotong_kata(): void
    {
        $teks = 'Kampas Rem Depan Vario 125 (Original) Honda Genuine Parts';
        $lines = StrukFormatter::bungkus($teks, 48);

        $this->assertNotEmpty($lines);
        foreach ($lines as $line) {
            $this->assertLessThanOrEqual(48, strlen($line));
        }

        // Pastikan kata utuh tidak terpotong di tengah
        $this->assertEquals('Kampas Rem Depan Vario 125 (Original) Honda', $lines[0]);
        $this->assertEquals('Genuine Parts', $lines[1]);
    }

    public function test_tengah_memusatkan_teks_dan_tepat_48_karakter(): void
    {
        $teks = 'PRADAN MOTOR';
        $hasil = StrukFormatter::tengah($teks, 48);

        $this->assertEquals(48, strlen($hasil));
        $this->assertStringContainsString($teks, $hasil);

        // Teks kosong tetap 48 karakter spasi
        $kosong = StrukFormatter::tengah('', 48);
        $this->assertEquals(48, strlen($kosong));
        $this->assertEquals(str_repeat(' ', 48), $kosong);
    }

    public function test_garis_menghasilkan_karakter_berulang_sesuai_lebar(): void
    {
        $garis = StrukFormatter::garis(48, '-');
        $this->assertEquals(48, strlen($garis));
        $this->assertEquals(str_repeat('-', 48), $garis);
    }

    public function test_potong_memotong_teks_dengan_elipsis(): void
    {
        $namaPendek = 'Budi';
        $this->assertEquals('Budi', StrukFormatter::potong($namaPendek, 16));

        $namaPanjang = 'Muhammad Sholahuddin Al Ayyubi';
        $hasilPotong = StrukFormatter::potong($namaPanjang, 16);
        $this->assertLessThanOrEqual(16, strlen($hasilPotong));
        $this->assertStringEndsWith('...', $hasilPotong);
    }

    public function test_format_nama_item_menambahkan_prefix_jasa(): void
    {
        $this->assertEquals('[JASA] Tune Up Injeksi', StrukFormatter::formatNamaItem('Tune Up Injeksi', 'jasa'));
        $this->assertEquals('[JASA] Ganti Oli', StrukFormatter::formatNamaItem('[JASA] Ganti Oli', 'jasa'));
        $this->assertEquals('Oli MPX2 0.8L', StrukFormatter::formatNamaItem('Oli MPX2 0.8L', 'barang'));
    }

    public function test_pembersihan_karakter_non_ascii_dan_spasi(): void
    {
        $teksKotor = "Oli Racing \t 🏎️ Pro \n Super";
        $bersih = StrukFormatter::cleanAscii($teksKotor);
        $this->assertStringNotContainsString('🏎️', $bersih);
        $this->assertStringContainsString('Oli Racing', $bersih);
        $this->assertStringContainsString('Pro', $bersih);
    }
}

