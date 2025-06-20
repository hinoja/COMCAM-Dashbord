{{-- filepath: h:\Laravel Projet\ComcamNewApplication\resources\views\admin\users\index.blade.php --}}
@extends('layouts.back')

@section('subtitle', __('Users list'))

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/back/modules/datatables/datatables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/back/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <style>
        body {
            background: #f8fafc;
        }

        .header-gradient {
            background:#047857  ;
            color: #fff;
            box-shadow: 0 4px 24px rgba(4, 120, 87, 0.15);
        }

        .badge-emerald {
            background-color: #d1fae5;
            color: #047857;
            font-weight: bold;
            border-radius: 12px;
            font-size: 13px;
        }

        .btn-add-user {
            background: linear-gradient(90deg, #d4af37 0%, #047857 100%);
            color: #fff !important;
            border-radius: 20px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.15);
            transition: background 0.3s;
        }

        .btn-add-user:hover {
            background: linear-gradient(90deg, #047857 0%, #d4af37 100%);
            color: #fff !important;
        }

        .table thead th {
            background: #047857;
            color: #fff;
            border: none;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f1f5f9;
        }

        .avatar {
            width: 38px;
            height: 38px;
            font-size: 1.1rem;
            background: linear-gradient(135deg, #047857 0%, #d4af37 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 12px;
            box-shadow: 0 2px 8px rgba(4, 120, 87, 0.08);
        }

        .badge-danger,
        .badge-success,
        .badge-info {
            font-size: 13px;
            border-radius: 8px;
            padding: 5px 12px;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: transform 0.2s, background 0.2s;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(4, 120, 87, 0.08);
        }

        .btn-icon[style*="background: #047857"] {
            background: #047857 !important;
        }

        .btn-icon[style*="background: green"] {
            background: #d4af37 !important;
        }

        .btn-icon:hover {
            transform: translateY(-2px) scale(1.08);
            filter: brightness(1.1);
        }

        .card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 6px 24px rgba(4, 120, 87, 0.08);
        }

        .section-title {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .table td,
        .table th {
            vertical-align: middle !important;
        }

        .shadow-lg {
            box-shadow: 0 8px 32px rgba(4, 120, 87, 0.12) !important;
        }
    </style>
@endpush

@section('content')
    <div class="section-body">
        <div class="row">
            <div class="container">
                <!-- Header with Title and Total Count -->
                <div class="d-flex justify-content-between align-items-center mb-4 header-gradient rounded-lg shadow-lg p-4">
                    <h2 class="section-title m-0">
                        <i class="fas fa-users-cog mr-2"></i>@lang('User Management')
                        <span class="badge badge-emerald ml-2">
                            {{ count($users) }} Utilisateurs
                        </span>
                    </h2>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-add-user px-4 py-2 shadow-sm">
                        <i class="fas fa-user-plus mr-2"></i>@lang('Add User')
                    </a>
                </div>
            </div>
            <div class="col-12">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Email')</th>
                                        <th>@lang('Role')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 font-weight-bold">{{ $user->name }}</h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Inscrit {{ $user->created_at?->diffForHumans() ?? 'N/A' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if ($user->role_id === 1)
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-shield-alt mr-1"></i>
                                                        Administrateur
                                                    </span>
                                                @else
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-user mr-1"></i>
                                                        Utilisateur
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->is_active)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Actif
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-ban mr-1"></i>
                                                        Bloqué
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    @if ($user->id !== auth()->id())
                                                        @if ($user->is_active)
                                                            <form method="POST"
                                                                action="{{ route('admin.users.status', $user->id) }}"
                                                                class="mr-1">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-icon"
                                                                    style="background: #047857; color: white;"
                                                                    data-toggle="tooltip" title="Bloquer l'utilisateur">
                                                                    <i class="fas fa-lock"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form method="POST"
                                                                action="{{ route('admin.users.status', $user->id) }}"
                                                                class="mr-1">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-icon"
                                                                    style="background: #d4af37; color: white;"
                                                                    data-toggle="tooltip" title="Débloquer l'utilisateur">
                                                                    <i class="fas fa-lock-open"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/back/modules/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/back/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}">
    </script>
    <script src="{{ asset('assets/back/js/page/modules-datatables.js') }}"></script>
@endpush
