<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koperasi CU Betang Asi</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="overflow-hidden bg-red-700">

<!-- BACKGROUND -->
<div class="relative h-screen w-screen overflow-hidden bg-red-700">

    <!-- Pattern Background -->
    <div class="absolute inset-0 opacity-10">
        <img
            src="{{ asset('images/bg-dayak.png') }}"
            class="w-full h-full object-cover"
        >
    </div>

    <!-- CONTENT -->
    <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-6 text-white">

        <!-- LOGO -->
        <img
            src="{{ asset('images/logo-cu.png') }}"
            class="w-36 h-36 object-contain mb-8 drop-shadow-2xl"
        >

        <!-- TITLE -->
        <h1 class="text-6xl md:text-8xl font-extrabold leading-tight drop-shadow-lg">
            Koperasi CU Betang Asi
        </h1>

        <!-- SUBTITLE -->
        <p class="mt-8 text-xl md:text-3xl text-red-100 leading-relaxed max-w-3xl">
            Sistem Manajemen Pengajuan Kredit Diatas Simpanan
        </p>

        <p class="mt-3 text-red-200 text-lg md:text-xl">
            Koperasi CU Betang Asi
        </p>

        <!-- BUTTON -->
        <button
            onclick="openLogin()"
            class="mt-14 bg-white text-red-700 hover:bg-red-50 px-12 py-5 rounded-3xl text-2xl font-bold shadow-2xl transition duration-300"
        >
            Login
        </button>

    </div>

</div>

<!-- LOGIN MODAL -->
<div
    id="loginModal"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 px-5"
>

    <div class="bg-white rounded-[40px] shadow-2xl w-full max-w-lg p-10 relative animate-fadeIn">

        <!-- CLOSE -->
        <button
            onclick="closeLogin()"
            class="absolute top-5 right-6 text-gray-400 hover:text-red-700 text-3xl"
        >
            ×
        </button>

        <!-- LOGO -->
        <div class="flex flex-col items-center mb-8">

            <img
                src="{{ asset('images/logo-cu.png') }}"
                class="w-24 h-24 object-contain mb-3"
            >

            <h2 class="text-3xl font-bold text-gray-800 text-center">
                Login
            </h2>

            <p class="text-gray-500 mt-2 text-center">
                Silakan masuk untuk melanjutkan
            </p>

        </div>

        <!-- SESSION -->
        @if (session('status'))
            <div class="mb-5 text-sm text-green-600 text-center">
                {{ session('status') }}
            </div>
        @endif

        <!-- FORM -->
        <form method="POST" action="{{ route('login') }}">

            @csrf

            <!-- USERNAME -->
            <div class="mb-6">

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    class="w-full rounded-2xl border-gray-300 py-4 px-5 focus:border-red-600 focus:ring-red-600"
                >

                @error('username')
                    <div class="text-red-500 text-sm mt-2">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <!-- PASSWORD -->
            <div class="mb-6">

                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Password
                </label>

                <div class="relative">

                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        class="w-full rounded-2xl border-gray-300 py-4 px-5 pr-20 focus:border-red-600 focus:ring-red-600"
                    >

                    <button
                        type="button"
                        onclick="togglePassword()"
                        id="toggleText"
                        class="absolute inset-y-0 right-0 px-5 text-sm font-semibold text-gray-500 hover:text-red-700"
                    >
                        Show
                    </button>

                </div>

                @error('password')
                    <div class="text-red-500 text-sm mt-2">
                        {{ $message }}
                    </div>
                @enderror

            </div>

            <!-- REMEMBER -->
            <div class="flex items-center mb-8">

                <input
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                >

                <span class="ml-2 text-gray-600">
                    Remember me
                </span>

            </div>

            <!-- BUTTON -->
            <button
                type="submit"
                class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-4 rounded-2xl text-lg transition"
            >
                Login
            </button>

        </form>

        <!-- FOOTER -->
        <div class="mt-8 text-center text-sm text-gray-400">
            © 2026 RN Control Systems
        </div>

    </div>

</div>

<script>
function openLogin() {
    document.getElementById('loginModal').classList.remove('hidden');
    document.getElementById('loginModal').classList.add('flex');
}

function closeLogin() {
    document.getElementById('loginModal').classList.add('hidden');
    document.getElementById('loginModal').classList.remove('flex');
}

function togglePassword() {

    const password = document.getElementById('password');
    const toggleText = document.getElementById('toggleText');

    if (password.type === 'password') {

        password.type = 'text';
        toggleText.innerText = 'Hide';

    } else {

        password.type = 'password';
        toggleText.innerText = 'Show';

    }
}
</script>

</body>
</html>