@php($isAdmin = auth()->user()->role === 'admin')
<aside class="sidebar">
	<a class="brand" href="{{ $isAdmin ? route('admin.dashboard') : route('kasir.dashboard') }}">◈ PRADAN MOTOR</a>
	<nav class="nav">
		@if ($isAdmin)
			<div class="nav-label">Administrasi</div>
			<a href="{{ route('admin.dashboard') }}">▦ Dashboard Admin</a>
			<a href="{{ route('admin.pengguna') }}">♙ Master Data Pengguna</a>
			<a href="{{ route('admin.barang') }}">▧ Master Data Barang</a>
			<a href="{{ route('admin.kategori') }}">▦ Kategori Barang</a>
			<a href="{{ route('admin.supplier') }}">♧ Data Supplier</a>
			<a href="{{ route('admin.layanan') }}">✦ Data Layanan</a>
			<a href="{{ route('admin.pelanggan') }}">♙ Pelanggan</a>
			<a href="{{ route('admin.barang-masuk') }}">⇩ Barang Masuk</a>
			<a href="{{ route('admin.stok-opname') }}">✓ Stok Opname</a>
			<a href="{{ route('admin.laporan') }}">▥ Laporan Admin</a>
			<a href="{{ route('admin.profil') }}">◉ Profil</a>
		@else
			<div class="nav-label">Menu Kasir</div>
			<a href="{{ route('kasir.dashboard') }}">▦ Dashboard Kasir</a>
			<a href="{{ route('kasir.transaksi') }}">▤ Transaksi Penjualan</a>
			<a href="{{ route('kasir.pelanggan') }}">♙ Pelanggan</a>
			<a href="{{ route('kasir.stok') }}">▧ Stok Barang</a>
			<a href="{{ route('kasir.riwayat') }}">◷ Riwayat Transaksi</a>
			<a href="{{ route('kasir.laporan') }}">▥ Laporan</a>
			<a href="{{ route('kasir.profil') }}">◉ Profil</a>
		@endif
	</nav>
	<div class="sidebar-user">
		{{ auth()->user()->name }}<br>
		<small>{{ $isAdmin ? 'Administrator' : 'Kasir' }}</small>
	</div>
</aside>