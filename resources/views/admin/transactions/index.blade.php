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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('js')
    @livewireScripts

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
@endpush






