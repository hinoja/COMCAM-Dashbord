@extends('layouts.back')

@section('subtitle', __('Liste des Transactions'))

@section('content')
    <div class="section-body mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title text-primary">Liste des Transactions</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.transaction.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajouter une Transaction
                </a>

                <a href="{{ route('admin.transaction.export') }}" class="btn btn-success">
                    <i class="fas fa-download mr-2"></i>Exporter Excel
                </a>
            </div>

        </div>

        <div class="container-fluid">
            <hr class="my-4">
            <div class="row justify-content-center">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="card shadow">
                        @livewire('manage-transaction')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    @livewireStyles()
    <style>
        /* Surlignage des résultats de recherche */
        .highlight {
            background-color: #fefcbf;
            font-weight: bold;
        }

        /* Style moderne pour le tableau */
        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table-hover tbody tr {
            transition: background-color 0.2s ease;
        }

        /* Pagination personnalisée */
        .pagination .page-link {
            border-radius: 50%;
            margin: 0 3px; 
            border: none;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .pagination .page-item.active .page-link { 
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #ccc;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
        }

        /* Boutons d'action */
        .btn-group .btn {
            transition: transform 0.2s ease;
        }

        .btn-group .btn:hover {
            transform: scale(1.1);
        }

        /* Filtres */
        .form-group {
            transition: all 0.3s ease;
        }

        .form-group:focus-within label {
            color: #007bff;
        }

        .tom-select .ts-control {
            border-radius: 0.375rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('js')
    @livewireScripts

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
@endpush
