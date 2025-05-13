@extends('layouts.back')

@section('subtitle', __('Transactions'))

@section('content')
    <div class="section-body">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center p-3 rounded"
                style="background-color: #2d6a4f; color: white;">
                <!-- Logo et titre -->
                <div class="d-flex align-items-center">
                    <i class="fas fa-book-open fa-2x mr-3"></i> <!-- Icône pour "Titres" -->
                    <h2 class="m-0">Ajout Transaction(s)</h2>
                </div>
                <!-- Badge avec le total des titres -->
                <span class="badge badge-light p-2" style="background-color: #a8d5ba; color: black;">
                    Total: {{ $totalTitres ?? 'N/A' }} titres
                </span>
            </div>

            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <!-- Card principale -->
                    <div class="card shadow-lg rounded-lg border-0">
                        <div class="card-header bg-primary text-white py-3">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-plus-circle mr-2"></i>
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
                        @livewire('add-transaction')

                    </div>

                    <!-- Card Import Excel -->
                    @if (auth()->user()->role_id === 1)
                        <div class="card shadow-lg rounded-lg border-0 mt-4">
                            <div class="card-header bg-gradient-success text-white py-3">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-file-excel mr-2"></i>
                                    Import Excel
                                </h4>
                            </div>

                            <form method="POST" action="{{ route('admin.transaction.import') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('file') is-invalid @enderror"
                                            id="file" name="file" accept=".xlsx,.xls,.csv">
                                        <label class="custom-file-label" for="file">Choisir un fichier</label>
                                        @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="card-footer  border-top-0 text-right py-3">
                                    <button type="submit" style="background:green; color:white" class="btn   btn-lg px-5">
                                        <i class="fas fa-upload mr-2"></i>
                                        Importer
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Styles personnalisés */
        .bg-gradient-primary {
            background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #1cc88a 0%, #13855c 100%);
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* Style Select2 personnalisé */
        .select2-container--default .select2-selection--single {
            height: calc(2.25rem + 2px);
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(2.25rem + 2px);
            padding-left: 0.75rem;
            color: #6e707e;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px);
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #4e73df;
        }

        /* Style des inputs */
        .form-control {
            border-radius: 0.35rem;
            border: 1px solid #e3e6f0;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Style du custom file input */
        .custom-file-label {
            border-radius: 0.35rem;
            border: 1px solid #e3e6f0;
        }

        .custom-file-input:focus~.custom-file-label {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Style des sections */
        .bg-light {
            background-color: #f8f9fc !important;
        }

        /* Animation des boutons */
        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialisation de Select2 sur tous les selects
            $('.select2').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Sélectionner une option',
                allowClear: true
            });

            // Affichage du nom du fichier sélectionné
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName ||
                    'Choisir un fichier');
            });

            // Animation des cards au chargement
            $('.card').each(function(index) {
                $(this).delay(100 * index).animate({
                    opacity: 1,
                    top: 0
                }, 500);
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

            // Reset du formulaire
            $('.btn-reset').on('click', function() {
                $('form')[0].reset();
                $('.select2').val(null).trigger('change');
            });
        });
    </script>
@endpush
