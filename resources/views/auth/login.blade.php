<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@lang('Log in') | {{ 'Admin' . ' ' . config('app.name', 'COMCAM') }}</title>

    <!-- Favicon -->
    <link href="{{ asset('favicon.png') }}" rel="icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('back/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('back/modules/fontawesome/css/all.min.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('back/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('back/css/components.css') }}">

</head>

<body>

    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div
                        class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="login-brand">
                            <img class="align-center" src="{{ asset('logo.jpg') }}" alt="logo" width="100"
                                class="shadow-light rounded-circle">
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h1 class="text-primary text-center">@lang('Log in')</h1>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="{{ route('login') }}" class="needs-validation"
                                    novalidate="">
                                    @csrf

                                    <!-- Email Address -->
                                    <div class="form-group">
                                        <x-input-label for="email" :value="__('Email')" />
                                        <x-text-input id="email" class="block mt-1 w-full" type="email"
                                            name="email" :value="old('email')" required autofocus
                                            autocomplete="username" />
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group">
                                        <div class="d-block">
                                            <div class="float-right">
                                                @if (Route::has('password.request'))
                                                    <a class="underline text-small"
                                                        href="{{ route('password.request') }}">
                                                        {{ __('Forgot your password?') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <x-input-label for="password" :value="__('Password')" />
                                        <div class="input-group">
                                            <x-text-input id="password" class="form-control" type="password"
                                                name="password" required autocomplete="current-password" />
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Remember Me -->
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <label for="remember_me" class="inline-flex items-center">
                                                <input id="remember_me" type="checkbox"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                    name="remember">
                                                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" style="background: rgb(69,132,103); color:white"
                                            class="btn btn-lg btn-block" tabindex="4">
                                            @lang('Log in to get started')
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="simple-footer">
                            Copyright © COMCAM {{ date('Y') }}
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

    <!-- Password Visibility Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        });
    </script>

</body>

</html>
