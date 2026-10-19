<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Pradan Motor POS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body class="min-h-screen bg-gray-50 px-4 py-8 text-gray-900 antialiased flex items-center justify-center">
    <main class="w-full max-w-[490px] rounded-2xl border border-gray-100 bg-white px-7 py-10 shadow-lg sm:px-12 sm:py-12">
        <header class="mb-10 text-center">
            <h1 class="text-2xl font-extrabold tracking-tight text-black">PRADAN MOTOR</h1>
            <p class="mt-2 text-xs leading-relaxed text-gray-700 sm:text-sm">
                Silahkan masuk untuk mengakses sistem POS &amp; Manajemen
            </p>
        </header>

        <div class="mx-auto w-full max-w-[312px]">
            <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
                @csrf
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-900">Alamat Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="masukkan email"
                        @if ($errors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif
                        class="w-full rounded-lg border {{ $errors->has('email') ? 'border-rose-500' : 'border-gray-300' }} bg-gray-200/80 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20"
                    >
                    @if ($errors->has('email'))
                        <ul id="email-error" role="alert" class="mt-1.5 space-y-1 text-xs font-medium text-rose-700">
                            @foreach ($errors->get('email') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-900">Password</label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="masukkan password"
                            @if ($errors->has('password')) aria-invalid="true" aria-describedby="password-error" @endif
                            class="w-full rounded-lg border {{ $errors->has('password') ? 'border-rose-500' : 'border-gray-300' }} bg-gray-200/80 px-3.5 py-2.5 pr-11 text-sm text-gray-900 shadow-sm transition focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20"
                        >
                        <button
                            id="toggle-password"
                            type="button"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 transition hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-600"
                        >
                            <svg data-eye-open xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6.25 9.75-6.25S21.75 12 21.75 12 18.25 18.25 12 18.25 2.25 12 2.25 12Z" />
                                <circle cx="12" cy="12" r="2.75" />
                            </svg>
                            <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="hidden h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.9A10.9 10.9 0 0 1 12 5.75c6.25 0 9.75 6.25 9.75 6.25a16.4 16.4 0 0 1-3.1 3.7M6.2 6.2C3.6 7.8 2.25 12 2.25 12s3.5 6.25 9.75 6.25c1.3 0 2.45-.3 3.45-.8" />
                            </svg>
                        </button>
                    </div>
                    @if ($errors->has('password'))
                        <ul id="password-error" role="alert" class="mt-1.5 space-y-1 text-xs font-medium text-rose-700">
                            @foreach ($errors->get('password') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <label class="flex cursor-pointer select-none items-center gap-2 pt-0.5 text-xs text-gray-800">
                    <input
                        type="checkbox"
                        name="remember"
                        class="h-[18px] w-[18px] rounded border-gray-400 text-blue-600 focus:ring-blue-600"
                    >
                    <span>Ingat saya di perangkat ini</span>
                </label>

                <button
                    type="submit"
                    class="w-full rounded-full bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/30 focus:ring-offset-2"
                >
                    Masuk ke Aplikasi
                </button>
            </form>
        </div>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('toggle-password');
        const eyeOpen = passwordToggle.querySelector('[data-eye-open]');
        const eyeClosed = passwordToggle.querySelector('[data-eye-closed]');

        passwordToggle.addEventListener('click', () => {
            const showPassword = passwordInput.type === 'password';

            passwordInput.type = showPassword ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', String(showPassword));
            passwordToggle.setAttribute('aria-label', showPassword ? 'Sembunyikan password' : 'Tampilkan password');
            eyeOpen.classList.toggle('hidden', showPassword);
            eyeClosed.classList.toggle('hidden', !showPassword);
        });
    </script>
</body>
</html>
