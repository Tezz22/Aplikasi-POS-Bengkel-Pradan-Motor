@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('content')
<div class="page-heading"><div><h1>Riwayat Transaksi</h1><p>Telusuri transaksi sebelumnya</p></div></div>
<div class="card table-card"><div class="table-head"><strong>Daftar Data</strong><input class="search" placeholder="Cari data..." data-search="#tbl"></div><div class="table-wrap"><table id="tbl"><thead><tr><th>Kode</th><th>Tanggal</th><th>Kasir</th><th>Jumlah Item</th><th>Total</th><th>Status</th></tr></thead><tbody><tr><td>TRX-001</td><td>12 Okt 2026</td><td>Joo</td><td>3</td><td>Rp 850.000</td><td><span class="badge">Selesai</span></td></tr></tbody></table></div></div>
@endsection