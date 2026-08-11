<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <title>{{ env('APP_NAME') }} | Sistem Akademik</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link href="/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="/assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link id="pagestyle" href="/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>

<body class="bg-gray-200">
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100"
            style="background-image: url('/assets/img/baground.png');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">
                                        {{ env('APP_NAME') }} | Sistem Akademik
                                    </h4>
                                    <small class="d-block text-center text-white opacity-8 mb-2" style="font-size: 12px; letter-spacing: .03em;">
                                        {{ \App\Models\Profil::namaLembaga() }}
                                    </small>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-center mt-2 mb-3" style="font-size: 12px;">
                                    Silakan Login Pada Form di bawah ini
                                </p>
                                <form method="POST" action="/auth/login" class="text-start">
                                    @csrf
                                    <div
                                        class="input-group input-group-outline my-3 mt-2 {{ old('username') ? 'is-filled' : '' }}">
                                        <label class="form-label">Nama Pengguna</label>
                                        <input type="text" name="username" value="{{ old('username') }}"
                                            class="form-control">
                                    </div>
                                    <div class="input-group input-group-outline mb-3 mt-2">
                                        <label class="form-label">Kata Sandi</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Masuk</button>
                                    </div>
                                    <p class="mt-4 text-sm text-center">
                                        Belum punya akun?
                                        <a href="/" class="text-primary text-gradient font-weight-bold">Daftar</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer position-absolute bottom-2 py-2 w-100">
                <div class="container">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-12 col-md-6 my-auto">
                            <div class="copyright text-center text-sm text-white text-lg-start">
                                ©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>, dibuat dengan <i class="fa fa-heart" aria-hidden="true"></i> oleh
                                <a href="" class="font-weight-bold text-white" target="_blank">Tim Asta Brata</a>
                                untuk web yang lebih baik.
                            </div>
                        </div>
                        <div class="col-12 col-md-6"></div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <script src="/assets/js/core/popper.min.js"></script>
    <script src="/assets/js/core/bootstrap.min.js"></script>
    <script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), {
                damping: '0.5'
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif

    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>

</html>
