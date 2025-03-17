
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('subtitle') | {{ 'Admin' . ' ' . config('app.name', 'COMCAM') }}</title>

    <!-- Favicon -->
    <link href="{{ asset('favicon.png') }}" rel="icon">

    <!-- Fonts -->
    <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('back/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/modules/fontawesome/css/all.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('back/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('back/css/components.css') }}">


    {{-- @livewireStyles --}}

    @stack('css')
    @notifyCss
</head>

<body class="font-sans text-gray-900 antialiased">
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div
                        class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="login-brand">
                            {{-- <img class="align-center" src="{{ asset('logo.jpg') }}" alt="logo" width="100"
                                    class="shadow-light rounded-circle"> --}}
                            <a href="/">
                                <img class="w-20 h-20 fill-current text-gray-500" class="align-center"
                                    src="{{ asset('logo.jpg') }}" alt="logo" width="100"
                                    class="shadow-light rounded-circle">
                            </a>
                        </div>

                        {{-- <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"> --}}
                            {{ $slot }}
                        {{-- </div> --}}
                    </div>
                    <div class="mt-5 text-muted text-center">
                        Don't have an account? <a href="auth-register.html">Create One</a>
                    </div>
                    <div class="simple-footer">
                        Copyright &copy; Stisla 2018
                    </div>
                </div>
            </div>
    </div>
    </section>
    </div>

                    <!-- General JS Scripts -->
                    <script src="{{ asset('back/modules/popper.js') }}"></script>
                    <script src="{{ asset('back/modules/tooltip.js') }}"></script>
                    <script src="{{ asset('back/modules/jquery.min.js') }}"></script>
                    <script src="{{ asset('back/modules/bootstrap/js/bootstrap.min.js') }}"></script>
                    <script src="{{ asset('back/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
                    <script src="{{ asset('back/modules/moment.min.js') }}"></script>
                    <script src="{{ asset('back/js/stisla.js') }}"></script>

                    <!-- Template JS File -->
                    <script src="{{ asset('back/js/scripts.js') }}"></script>
                    <script src="{{ asset('back/js/custom.js') }}"></script>

                    {{-- <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script> --}}
                    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
                    <!-- Include Toastr CSS (if using Toastr) -->
                    <link rel="stylesheet"
                        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
                    <!-- Include Toastr JS (if using Toastr) -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                    {{-- @livewireScripts --}}

                    @stack('js')

</body>

</html>
