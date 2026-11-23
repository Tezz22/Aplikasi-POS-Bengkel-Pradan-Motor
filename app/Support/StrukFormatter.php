<?php

namespace App\Support;

class StrukFormatter
{
    /**
     * Membersihkan karakter non-ASCII dan menormalkan karakter kontrol.
     */
    public static function cleanAscii(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        // Ganti tab dan enter dengan spasi tunggal
        $text = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $text);

        // Hapus karakter selain ASCII tercetak (0x20 - 0x7E)
        return preg_replace('/[^\x20-\x7E]/', '', $text);
    }

    /**
     * Format angka ke rupiah gaya minimarket (pemisah ribuan titik tanpa awalan Rp).
     * Contoh: 1234567 -> 1.234.567
     */
    public static function rupiah(int|float|null $angka): string
    {
        $val = (float) ($angka ?? 0);

        return number_format($val, 0, ',', '.');
    }

    /**
     * Membuat satu baris teks dengan kolom kiri dan kanan yang lurus.
     * Output selalu menghasilkan string dengan panjang tepat $lebar karakter.
     */
    public static function baris(string $kiri, string $kanan, int $lebar = 48): string
    {
        $kiri = rtrim(self::cleanAscii($kiri));
        $kanan = trim(self::cleanAscii($kanan));

        $kananLen = strlen($kanan);
        if ($kananLen >= $lebar) {
            return substr($kanan, 0, $lebar);
        }

        $maxKiri = $lebar - $kananLen - 1; // Sisakan minimal 1 spasi jeda
        if (strlen($kiri) > $maxKiri) {
            $kiri = substr($kiri, 0, $maxKiri);
        }

        $spasi = $lebar - strlen($kiri) - $kananLen;

        return $kiri . str_repeat(' ', max(1, $spasi)) . $kanan;
    }

    /**
     * Memusatkan teks di tengah kolom dengan padding spasi.
     * Output selalu menghasilkan string dengan panjang tepat $lebar karakter.
     */
    public static function tengah(string $teks, int $lebar = 48): string
    {
        $clean = trim(self::cleanAscii($teks));
        $len = strlen($clean);

        if ($len >= $lebar) {
            return substr($clean, 0, $lebar);
        }

        $sisa = $lebar - $len;
        $padKiri = (int) floor($sisa / 2);
        $padKanan = $sisa - $padKiri;

        return str_repeat(' ', $padKiri) . $clean . str_repeat(' ', $padKanan);
    }

    /**
     * Membungkus teks panjang menjadi array baris tanpa memotong kata di tengah.
     */
    public static function bungkus(string $teks, int $lebar = 48): array
    {
        $clean = self::cleanAscii($teks);
        if ($clean === '') {
            return [];
        }

        $words = preg_split('/\s+/', $clean);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }

            if ($currentLine === '') {
                if (strlen($word) > $lebar) {
                    $chunks = str_split($word, $lebar);
                    for ($i = 0; $i < count($chunks) - 1; $i++) {
                        $lines[] = $chunks[$i];
                    }
                    $currentLine = end($chunks);
                } else {
                    $currentLine = $word;
                }
            } else {
                if (strlen($currentLine) + 1 + strlen($word) <= $lebar) {
                    $currentLine .= ' ' . $word;
                } else {
                    $lines[] = $currentLine;
                    if (strlen($word) > $lebar) {
                        $chunks = str_split($word, $lebar);
                        for ($i = 0; $i < count($chunks) - 1; $i++) {
                            $lines[] = $chunks[$i];
                        }
                        $currentLine = end($chunks);
                    } else {
                        $currentLine = $word;
                    }
                }
            }
        }

        if ($currentLine !== '') {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    /**
     * Memotong string dengan akhiran elipsis tanpa melebihi batas panjang.
     */
    public static function potong(string $teks, int $max = 20, string $akhiran = '...'): string
    {
        $clean = self::cleanAscii($teks);
        if (strlen($clean) <= $max) {
            return $clean;
        }

        $cut = max(0, $max - strlen($akhiran));

        return substr($clean, 0, $cut) . $akhiran;
    }

    /**
     * Garis pemisah horizontal berupa karakter berulang.
     */
    public static function garis(int $lebar = 48, string $karakter = '-'): string
    {
        return str_repeat($karakter, $lebar);
    }

    /**
     * Memberikan penanda [JASA] pada nama item jika merupakan layanan/jasa.
     */
    public static function formatNamaItem(string $nama, string $jenisItem = 'barang'): string
    {
        $clean = self::cleanAscii($nama);
        if ($jenisItem === 'jasa' && !str_starts_with(strtoupper($clean), '[JASA]')) {
            return '[JASA] ' . $clean;
        }

        return $clean;
    }
}

