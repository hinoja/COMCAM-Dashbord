@extends('layouts.back')

@section('subtitle', __('Users list'))

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/back/modules/datatables/datatables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/back/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <style>
        /* Insérez ici le CSS personnalisé ci-dessus */
        .btn-rounded {
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: transform 0.2s ease;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')

    <div class="section-body">
        <div class="row">
            <div class="container">
                <!-- Header with Title and Total Count -->
                <div class="d-flex justify-content-between align-items-center mb-4 header-gradient rounded-lg shadow-lg p-3">
                    <h2 class="section-title m-0 text-white font-weight-bold">
                        <small class="mb-0">
                            <i class="fas fa-users-cog mr-2"></i>@lang('User Management') </small>
                        <span style="font-size: 10px" class="badge badge-emerald  text-sm shadow-sm">
                            {{ count($users) }} Utilisateurs
                        </span>
                    </h2>


                    <button class="badge badge-emerald px-3 py-2 shadow-sm" href="{{ route('admin.users.create') }}"
                        class="btn btn-add">
                        <i class="fas fa-user-plus mr-2"></i>@lang('Add User')
                    </button>
                </div>
            </div>
            <div class="col-12">
                <div class="card">

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
                                                    <div class="avatar avatar-sm mr-3 bg-primary text-white rounded-circle">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $user->name }}</h6>
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
                                                                <button type="submit" class="btn btn-icon "
                                                                    style="background: yellow;color:white""
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
                                                                    style="background: green;color:white"
                                                                    data-toggle="tooltip" title="Débloquer l'utilisateur">
                                                                    <i class="fas fa-lock-open"></i>
                                                                </button>
                                                            </form>
                                                        @endif

                                                        {{-- <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-icon btn-danger"
                                                                    data-toggle="tooltip"
                                                                    title="Supprimer l'utilisateur">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form> --}}
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
@push('css')
    <style>
        /* Premium Color Palette */
        .text-emerald {
            color: #047857;
        }

        /* Emerald-700 */
        .text-gold {
            color: #d4af37;
        }

        /* Luxurious gold accent */
        .bg-emerald {
            background-color: #047857;
        }

        .border-emerald {
            border-color: #047857;
        }

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
