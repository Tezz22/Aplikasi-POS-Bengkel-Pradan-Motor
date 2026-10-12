<header class="topbar">
	<div>
		<small style="color:#788398">Selamat bekerja,</small><br>
		<strong>{{ auth()->user()->name ?? 'Admin Pradan' }}</strong>
	</div>
	<div class="topbar-actions" style="display:flex;align-items:center;gap:16px">
		<span>● Sistem operasional</span>
		<form method="POST" action="{{ route('logout') }}">
			@csrf
			<button class="btn" type="submit">Logout</button>
		</form>
	</div>
</header>