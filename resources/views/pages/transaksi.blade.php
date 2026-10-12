@extends('layouts.app')
@section('title', 'Transaksi Penjualan')
@section('content')
<div class="page-heading"><div><h1>Transaksi Penjualan</h1><p>Kelola transaksi dan pembayaran.</p></div></div>
<div class="grid dashboard-grid"><div class="card"><h2>Pilih Barang</h2><p>Oli Mesin 1L — Rp 65.000</p><p>Kampas Rem — Rp 85.000</p></div><div class="card"><h2>Ringkasan Keranjang</h2><div class="field"><label>Pelanggan</label><select><option>Pelanggan umum</option><option>Budi Santoso</option></select></div><p>Oli Mesin 1L × 2 — Rp 130.000</p><p>Jasa servis × 1 — Rp 75.000</p><h3>Total Rp 205.000</h3><div class="field"><label>Metode pembayaran</label><select><option>Tunai</option><option>Transfer</option><option>QRIS</option></select></div><p><button class="btn">Proses Pembayaran</button></p></div></div>
@endsection