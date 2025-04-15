@extends('layouts.back')

@section('subtitle', __('Add User'))

@section('content')
    {{-- <x-admin.section-header :title="__('Add New User')" :previousTitle="__('Users list')" :previousRouteName="route('admin.users.index')" /> --}}

    <div class="section-body mt-4 attraction-bg">
        <div class="container ">
            <!-- Header with Title and Total Count -->
            <div class="d-flex justify-content-between align-items-center mb-4 header-gradient rounded-lg shadow-lg p-3">
                <h2 class="section-title m-0 text-white font-weight-bold">
                    <i class="fas fa-building mr-2 text-gold"></i> {{ __('Add New User') }}
                </h2>

            </div>
        </div>
        <div class="row h-100">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-user-plus mr-2"></i>@lang('New User Form')</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.store') }}" method="POST">
                            @csrf

                            <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('Name')</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" name="name" placeholder="micheal" value="{{ old('name') }}"
                                        class="form-control @error('name') is-invalid @enderror" required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('Email')</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="email" placeholder="xyz@mail.com" name="email"
                                        value="{{ old('email') }}"
                                        class="form-control @error('email') is-invalid @enderror" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('Role')</label>
                                <div class="col-sm-12 col-md-7">
                                    <select name="role_id" class="form-control @error('role_id') is-invalid @enderror" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}

                            <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('Password')</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">@lang('Confirm Password')</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button style="background: rgb(69,132,103)" type="submit"
                                        class="btn btn-primary btn-rounded">
                                        <i class="fas fa-save mr-2"></i>@lang('Create User')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        /* Premium Color Palette */
        .text-emerald { color: #047857; } /* Emerald-700 */
        .text-gold { color: #d4af37; } /* Luxurious gold accent */
        .bg-emerald { background-color: #047857; }
        .border-emerald { border-color: #047857; }

        /* Visible action buttons */
        .btn-action-visible {
            background-color: #047857;
            color: white;
            font-weight: bold;
            border: none;
            box-shadow: 0 4px 6px rgba(4, 120, 87, 0.3);
        }

        .btn-action-visible:hover {
            background-color: #065f46;
            color: white;
        }

        .badge-emerald {
            background-color: #d1fae5;
            color: #047857;
            font-weight: bold;
        }

        /* Header Gradient */
        .header-gradient {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        }

        /* Subtle Background Texture */
        .attraction-bg {
            background: #f8fafc;
            min-height: 100vh;
        }

        /* Card styles - with full height support */
        .card {
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        /* Ensure the row takes full height */
        .section-body .row.h-100 {
            min-height: 80vh;
        }
    </style>
@endpush
