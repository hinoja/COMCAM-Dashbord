@extends('layouts.back')

@section('title', 'Modifier une transaction')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-primary mr-2"></i> Modifier une transaction
            </h1>
            <a href="{{ route('admin.transaction.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <!-- Card principale -->
                <div class="card shadow-lg rounded-lg border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-edit mr-2"></i>
                            Détails de la Transaction
                        </h4>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                    @livewire('edit-transaction', ['id' => $transaction->id])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    @livewireStyles()
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
    <style>
        .fade-in {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
@endpush

@push('js')
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation des select avec Tom Select
            document.querySelectorAll('select:not([wire\\:model])').forEach(function(el) {
                new TomSelect(el, {
                    plugins: ['dropdown_input']
                });
            });

            // Validation personnalisée du formulaire
            $('form').on('submit', function(e) {
                let isValid = true;
                $(this).find('select, input').each(function() {
                    if ($(this).prop('required') && !$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de validation',
                        text: 'Veuillez remplir tous les champs obligatoires',
                        confirmButtonColor: '#4e73df'
                    });
                }
            });
        });
    </script>
@endpush
