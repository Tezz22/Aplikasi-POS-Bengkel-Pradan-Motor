@extends('layouts.app')
@section('title', 'Profil')
@section('content')
<div class="page-heading"><div><h1>Profil</h1><p>Informasi akun yang sedang digunakan.</p></div></div>
<div class="card">
	<h2>Informasi Akun</h2>
	<div class="form-grid">
		<div class="field"><label>Nama lengkap</label><input value="{{ auth()->user()->name }}" readonly></div>
		<div class="field"><label>Email</label><input value="{{ auth()->user()->email }}" readonly></div>
		<div class="field"><label>Peran</label><input value="{{ auth()->user()->role === 'admin' ? 'Administrator' : 'Kasir' }}" readonly></div>
	</div>
</div>
<div class="card">
	<h2>Ganti Password</h2>
	@if (session('status'))
		<p role="status">{{ session('status') }}</p>
	@endif
	<form method="POST" action="{{ route('profile.password.update') }}">
		@csrf
		<div class="form-grid">
			<div class="field">
				<label for="current_password">Password saat ini</label>
				<input id="current_password" type="password" name="current_password" autocomplete="current-password" required>
				@error('current_password') <small>{{ $message }}</small> @enderror
			</div>
			<div class="field">
				<label for="password">Password baru</label>
				<input id="password" type="password" name="password" autocomplete="new-password" minlength="8" required>
				@error('password') <small>{{ $message }}</small> @enderror
			</div>
			<div class="field">
				<label for="password_confirmation">Konfirmasi password baru</label>
				<input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required>
			</div>
		</div>
		<div class="form-actions">
			<button class="btn" type="submit">Simpan Password Baru</button>
		</div>
	</form>
</div>
@endsection