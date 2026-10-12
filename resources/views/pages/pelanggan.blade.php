@extends('layouts.app')
@section('title', 'Pelanggan')
@section('content')
<div class="page-heading"><div><h1>Pelanggan</h1><p>Kelola kontak pelanggan</p></div><a class="btn" href="#form-data">＋ Tambah Pelanggan</a></div>
<div class="card table-card"><div class="table-head"><strong>Daftar Data</strong><input class="search" placeholder="Cari data..." data-search="#tbl"></div><div class="table-wrap"><table id="tbl"><thead><tr><th>Nama</th><th>Telepon</th><th>Email</th><th>Alamat</th></tr></thead><tbody><tr><td>Budi Santoso</td><td>081234567890</td><td>budi@email.com</td><td>Madiun</td></tr></tbody></table></div></div><br><div class="card" id="form-data"><h2>Form Tambah Pelanggan</h2><div class="form-grid"><div class="field"><label>Nama / Keterangan</label><input placeholder="Masukkan nama"></div><div class="field"><label>Kode / Kontak</label><input placeholder="Masukkan kode"></div></div><div class="form-actions"><button class="btn" type="button">Simpan</button></div></div>
@endsection