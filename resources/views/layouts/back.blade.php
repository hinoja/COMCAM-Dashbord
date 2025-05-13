<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <title>@yield('subtitle') | {{ 'Admin' . ' ' . config('app.name', 'COMCAM') }}</title>

    <!-- Favicon -->
    <link href="{{ asset('favicon.png') }}" rel="icon">

    <!-- Fonts -->
    <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('back/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/modules/fontawesome/css/all.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('back/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('back/css/components.css') }}">

    <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">

    @livewireStyles
    <style>
        /* Styles pour les notifications */
        .swal2-container {
            z-index: 9999 !important;
        }

        .notyf {
            z-index: 9998 !important;
        }

        .toast-container {
            z-index: 9997 !important;
        }

        #notify {
            z-index: 9996 !important;
        }

        .alert {
            z-index: 9995 !important;
        }
    </style>
    @stack('css')
    @notifyCss
</head>

<body>""

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('notify'))
        @notify(session('notify'))
    @endif

    <div id="app">

        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>

            @include('includes.back.navbar')

            @include('includes.back.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <style>

                    </style>
                    <!-- Alertes Bootstrap pour les messages flash -->

                    @if (session('success'))
                        <div class="notification alert-dismissible fade show mt-1 shadow-sm rounded-lg" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="notification alert-danger alert-dismissible fade show mt-1 shadow-sm rounded-lg"
                            role="alert">
                            <div class="d-flex align-items-center">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('message'))
                        <div class="notification alert-success alert-dismissible fade show mt-1 shadow-sm rounded-lg"
                            role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('message') }}
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Contenu principal -->
                    @yield('content')
                </section>
            </div>

            <footer class="main-footer">
                <div class="container">
                    <div class="footer-left"> Copyright © COMCAM {{ date('Y') }} </div>
                    <div class="footer-right">
                        <div class="bullet"></div> @lang('Made By') <a href="https://bvision-lte.com"
                            target="_blank">JanohiCorporation</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    @if (session()->has('success'))
        <script>
            const notyf = new Notyf();
            notyf.success('{{ session('success') }}');
        </script>
    @endif

    <!-- General JS Scripts -->
    <script src="{{ asset('back/modules/popper.js') }}"></script>
    <script src="{{ asset('back/modules/tooltip.js') }}"></script>
    <script src="{{ asset('back/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('back/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('back/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('back/modules/moment.min.js') }}"></script>
    <script src="{{ asset('back/js/stisla.js') }}"></script>
    <script>
        // Configuration globale de Toastr
        toastr.options = {
            positionClass: 'toast-top-right',
            preventDuplicates: true,
            closeButton: true,
            progressBar: true,
            timeOut: 5000,
            extendedTimeOut: 2000,
            zIndex: 9997
        };

        // Configuration de Notyf
        const notyf = new Notyf({
            position: {
                x: 'right',
                y: 'top',
            },
            types: [{
                    type: 'success',
                    className: 'notyf__toast--success',
                    backgroundColor: '#28a745',
                    icon: false
                },
                {
                    type: 'error',
                    className: 'notyf__toast--error',
                    backgroundColor: '#dc3545',
                    icon: false
                }
            ]
        });
    </script>
    <!-- Template JS File -->
    <script src="{{ asset('back/js/scripts.js') }}"></script>
    <script src="{{ asset('back/js/custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script> --}}
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    <!-- Include Toastr CSS (if using Toastr) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Include Toastr JS (if using Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @livewireScripts
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}
    @stack('js')

    @notifyJs

</body>

</html>
