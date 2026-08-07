<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tenant Console — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: { 50:'#eef2ff', 100:'#e0e7ff', 500:'#6366f1', 600:'#4f46e5', 700:'#4338ca', 800:'#3730a3', 900:'#312e81' }
                    }
                }
            }
        }
    </script>
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; }

        .page-bg {
            background-image: url('/assets/img/wood-table-bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }
        .page-bg::before {
            content: "";
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(30,27,75,.45) 0%, rgba(49,46,129,.35) 50%, rgba(76,29,149,.45) 100%);
            pointer-events: none;
            z-index: 0;
        }

        .glass {
            background: rgba(255,255,255,.88);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border: 1px solid rgba(255,255,255,.6);
        }

        .field input { transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease; }
        .field input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99,102,241,.15); }

        .ring-focus:focus { outline:none; box-shadow: 0 0 0 4px rgba(99,102,241,.25); }
        .btn-grad {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        }
        .btn-grad:hover { filter: brightness(1.05); transform: translateY(-1px); box-shadow: 0 10px 25px -10px rgba(79,70,229,.6); }
        .btn-grad:active { transform: translateY(0); }

        @keyframes fadeUp { from { opacity:0; transform: translateY(12px); } to { opacity:1; transform: translateY(0); } }
        .fade-up { animation: fadeUp .55s ease-out both; }

        /* Prevent iOS zoom on input focus */
        @supports (-webkit-touch-callout: none) {
            .field input { font-size: 16px !important; }
        }
    </style>
</head>

<body class="page-bg min-h-screen text-slate-800 antialiased">

    <div class="relative z-10 min-h-screen flex items-center
                px-4 py-6
                sm:px-8 sm:py-8
                lg:px-12 lg:py-10
                xl:px-16">

        <div class="w-full
                    max-w-sm mx-auto
                    sm:max-w-sm sm:mx-0
                    lg:max-w-md
                    xl:max-w-md
                    fade-up">

            {{-- Brand header --}}
            <div class="text-center
                        mb-5
                        sm:mb-6
                        lg:mb-6">
                <h1 class="text-xl font-bold text-white drop-shadow-md
                           sm:text-2xl
                           lg:text-2xl">
                    {{ env('APP_NAME') }}
                </h1>
                <p class="mt-1 text-xs text-white/80 drop-shadow
                          sm:text-sm">
                    Tenant Console — Administrator Panel
                </p>
            </div>

            {{-- Card --}}
            <div class="glass rounded-3xl shadow-2xl shadow-black/20
                        p-6
                        sm:p-7
                        lg:p-8">
                <div class="mb-5 lg:mb-5">
                    <h2 class="text-lg font-bold text-slate-900 sm:text-xl">Welcome back</h2>
                    <p class="mt-1 text-xs text-slate-500 sm:text-sm">Sign in to continue to the Tenant Console.</p>
                </div>

                {{-- Error --}}
                @if ($errors->any())
                    <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-sm text-rose-700">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                        <div class="leading-snug">{{ $errors->first() }}</div>
                    </div>
                @endif

                <form action="{{ route('tenant.auth') }}" method="POST" class="space-y-4 sm:space-y-5" autocomplete="on">
                    @csrf

                    <div class="field">
                        <label for="email" class="block mb-2 text-xs font-semibold text-slate-700 sm:text-sm">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-300 bg-white
                                      sm:py-3" />
                    </div>

                    <div class="field">
                        <label for="password" class="block mb-2 text-xs font-semibold text-slate-700 sm:text-sm">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                   class="w-full px-3.5 py-2.5 pr-10 text-sm rounded-xl border border-slate-300 bg-white
                                          sm:py-3" />
                            <button type="button" id="togglePwd" aria-label="Show password"
                                    class="absolute right-1.5 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-300
                                           sm:right-2">
                                <svg id="eyeOff" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.879l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                <svg id="eyeOn" class="w-4 h-4 hidden sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="inline-flex items-center gap-2 text-xs text-slate-600 select-none cursor-pointer sm:text-sm">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 sm:w-4 sm:h-4">
                            Remember me
                        </label>
                        <a href="#" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 sm:text-sm">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-grad w-full py-2.5 rounded-xl text-white font-semibold text-sm ring-focus mt-2 inline-flex items-center justify-center gap-2
                                               sm:py-3 sm:text-base">
                        <span>Sign in to Tenant Console</span>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>

            <p class="mt-4 text-center text-[11px] text-white/80 drop-shadow sm:text-xs sm:mt-5">
                &copy; {{ date('Y') }} {{ env('APP_NAME') }} — Internal use only.
            </p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function () {
            const pwd = document.getElementById('password');
            const btn = document.getElementById('togglePwd');
            const eyeOn = document.getElementById('eyeOn');
            const eyeOff = document.getElementById('eyeOff');
            btn.addEventListener('click', function () {
                const showing = pwd.type === 'text';
                pwd.type = showing ? 'password' : 'text';
                eyeOn.classList.toggle('hidden', showing);
                eyeOff.classList.toggle('hidden', !showing);
            });
        })();
    </script>

    @if (session('success'))
        <script>
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });
        </script>
    @endif

    @if (session('msg'))
        <script>
            Swal.fire({ toast: true, position: 'top-end', icon: @json(session('icon') ?? 'success'), title: @json(session('msg')), showConfirmButton: false, timer: 3000, timerProgressBar: true });
        </script>
    @endif

</body>

</html>
