<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Serv5 - Select Portal</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-pattern {
            background-color: #0f172a;
            background-image: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
        }
        .glow {
            box-shadow: 0 0 40px -10px rgba(245, 158, 11, 0.4);
        }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-4 text-white antialiased relative overflow-x-hidden">
    
    <!-- Background decorations -->
    <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] bg-blue-600/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-20%] right-[-10%] w-[50%] h-[50%] bg-orange-500/20 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-4xl w-full relative z-10 flex flex-col items-center">
        <!-- Logo Area -->
        <div class="mb-12 text-center flex flex-col items-center">
            <div class="w-28 h-28 flex items-center justify-center mb-6 transform transition hover:scale-105">
                <img src="{{ asset('logo.svg') }}" alt="Serv5 Logo" class="w-full h-full object-contain drop-shadow-2xl" />
            </div>
            <h1 class="text-4xl md:text-5xl font-black mb-4 tracking-tight">Welcome to <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600">Serv5</span></h1>
            <p class="text-slate-400 text-lg max-w-lg mx-auto">Please select your destination portal to continue.</p>
        </div>

        <!-- Portals Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 w-full px-4">
            
            <!-- Admin Portal Card -->
            <a href="/admin" class="group relative bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 p-8 rounded-3xl hover:bg-slate-800 transition-all duration-300 hover:shadow-2xl hover:shadow-orange-500/10 overflow-hidden transform hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-500/20 to-transparent rounded-full blur-2xl -mr-16 -mt-16 transition-opacity group-hover:opacity-100 opacity-50"></div>
                
                <div class="w-14 h-14 bg-orange-500/20 border border-orange-500/30 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-white mb-3 flex items-center gap-2">
                    Admin Portal
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500 group-hover:text-orange-400 group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </h2>
                <p class="text-slate-400 text-sm leading-relaxed">Access the Filament admin dashboard to manage website content, system settings, and overview all operations.</p>
            </a>

            <!-- User Portal Card -->
            <a href="/user" class="group relative bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 p-8 rounded-3xl hover:bg-slate-800 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/10 overflow-hidden transform hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/20 to-transparent rounded-full blur-2xl -mr-16 -mt-16 transition-opacity group-hover:opacity-100 opacity-50"></div>
                
                <div class="w-14 h-14 bg-blue-500/20 border border-blue-500/30 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-white mb-3 flex items-center gap-2">
                    User Portal
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500 group-hover:text-blue-400 group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </h2>
                <p class="text-slate-400 text-sm leading-relaxed">Log in to your personal user account to access your services, track orders, and view support tickets.</p>
            </a>

        </div>

        <!-- Link to Main Website -->
        <div class="mt-12 w-full max-w-md px-4">
            <a href="http://localhost:3000" class="group relative w-full flex items-center justify-center gap-3 bg-gradient-to-r from-orange-500 to-orange-600 p-4 rounded-2xl hover:from-orange-400 hover:to-orange-500 transition-all duration-300 shadow-[0_0_20px_rgba(249,115,22,0.3)] hover:shadow-[0_0_30px_rgba(249,115,22,0.5)] transform hover:-translate-y-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="text-white font-bold text-lg">GO TO OUR WEBSITE</span>
            </a>
        </div>
    </div>
</body>
</html>
