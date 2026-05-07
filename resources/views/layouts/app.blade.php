<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Sistem Kredit</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

@vite('resources/css/app.css')

</head>

<body class="bg-gray-100 font-sans">

<!-- ================= SIDEBAR ================= -->
<div class="fixed top-0 left-0 w-60 h-screen bg-gray-950 text-white">

    <h2 class="p-5 font-bold text-lg">RN Control System</h2>

    <div class="flex flex-col">
        <a href="/dashboard" class="px-5 py-3 text-gray-300 hover:bg-gray-800">Dashboard</a>
        <a href="/pengajuan" class="px-5 py-3 text-gray-300 hover:bg-gray-800">Pengajuan</a>
        @if(auth()->user()->role == 'admin')
<a href="/users" class="px-5 py-3 text-gray-300 hover:bg-gray-800">
    User
</a>
@endif
    </div>

</div>

<!-- ================= HEADER ================= -->
<div class="ml-60 bg-red-600 text-white px-6 py-4 flex justify-between items-center">

    <!-- KIRI -->
    <div class="font-semibold text-lg tracking-wide">
        Sistem Kredit
    </div>

    <!-- KANAN -->
    <div class="flex items-center gap-4">

        <!-- USER -->
        <div class="flex items-center gap-3 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-sm">

            <!-- AVATAR -->
            <div class="w-7 h-7 flex items-center justify-center rounded-full bg-white/90 text-red-600 text-sm font-semibold">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>

             <!-- NAMA -->
            <div class="text-sm">
                <span class="font-medium">{{ auth()->user()->name }}</span>
                <span class="text-red-100 text-xs ml-1">
                    {{ auth()->user()->role ?? 'staff' }}
                </span>
            </div>

        </div>

        <!-- LOGOUT -->
        <form method="POST" action="{{ route('logout') }}">
    @csrf

    <button
        type="submit"
        class="flex items-center gap-2 bg-white text-red-600 px-3 py-2 rounded-lg text-sm hover:bg-gray-100 transition"
    >
        <i class="fa-solid fa-right-from-bracket"></i>
        Logout
    </button>
</form>

    </div>

</div>

<!-- ================= CONTENT ================= -->
<div class="ml-60 p-6 relative z-10 space-y-6">
    @yield('content')
</div>

<!-- ================= WATERMARK ================= -->
<div class="fixed inset-0 pointer-events-none flex items-end justify-end pr-10 pb-10 z-0">

    <div class="opacity-10 select-none">

        <!-- TULISAN -->
        <div class="text-center mb-1">
            <span class="text-gray-600 text-[10px] tracking-[4px] font-bold">
                POWERED BY
            </span>
        </div>

        <!-- LOGO -->
        <img src="/images/rn-logo.png"
             class="w-[420px]">

    </div>

</div>

</body>
</html>