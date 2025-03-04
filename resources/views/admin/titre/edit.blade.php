@extends('layouts.back')

@section('subtitle', __('Éditer un Titre'))

@section('content')
    <div class="section-body mt-4">
        <div class="container p-5">
            <div class="d-flex justify-content-between align-items-center p-3 rounded-lg shadow-md"
                 style="background: linear-gradient(45deg, #2d6a4f 0%, #13855c 100%); color: white;">
                <!-- Logo et titre -->
                <div class="d-flex align-items-center">
                    <i class="fas fa-book-open fa-2x mr-3"></i>
                    <h2 class="m-0 text-xl font-bold">Édition du Titre</h2>
                </div>
                <!-- Badge avec le total des titres (optionnel) -->
                <span class="badge badge-light p-2 rounded-lg" style="background-color: #a8d5ba; color: black;">
                    Total: {{ $totalTitres ?? 'N/A' }} titres
                </span>
            </div>
            <hr class="my-4 border-gray-300">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-12">
                    <div class="card shadow-lg hover:shadow-xl transition-all duration-300 p-1">
                        @livewire('edit-titre', ['id' => $titre->id])
                    </div>
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
            border-radius: 0.5rem;
            background: #fff;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: calc(2.25rem + 2px);
            padding-left: 0.75rem;
            color: #6e707e;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px);
            right: 2.5rem; /* Ajusté pour les icônes */
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #4e73df;
        }

        /* Style des inputs */
        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #e3e6f0;
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            background: #fff;
        }

        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Style des icônes dans les champs */
        .form-control + i, .select2-container + i {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #6e707e;
            pointer-events: none;
        }

        /* Ajustement pour Select2 avec icône */
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            display: none; /* Masquer la flèche par défaut de Select2 */
        }

        /* Style du custom file input */
        .custom-file-label {
            border-radius: 0.5rem;
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

        /* Animation des boutons et cards */
        .btn, .card {
            transition: all 0.3s ease;
        }

        .btn:hover, .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
    </style>
    @livewireStyles
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:load', function() {
            // Initialisation de Select2 sur tous les selects
            $('.select2').select2({
                theme: 'classic',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Sélectionner une option';
                },
                allowClear: true,
                dropdownAutoWidth: true
            });

            // Ajustement pour les icônes avec Select2
            $('.select2').on('select2:open', function() {
                document.querySelector('.select2-container--open .select2-dropdown').style.width = 'auto';
            });

            // Synchronisation avec Livewire pour les champs Select2
            Livewire.hook('message.processed', () => {
                $('.select2').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            theme: 'classic',
                            width: '100%',
                            placeholder: $(this).data('placeholder') || 'Sélectionner une option',
                            allowClear: true,
                            dropdownAutoWidth: true
                        });
                    }
                });
            });

            // Mise à jour des valeurs Select2 via Livewire
            Livewire.on('updateSelect', (data) => {
                const $select = $(`[name="${data.name}"]`);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.val(data.value).trigger('change');
                }
            });

            // Affichage du nom du fichier sélectionné (si applicable)
            $('.custom-file-input').on('change', function() {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Choisir un fichier');
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
                $(this).find('.select2, .form-control').each(function() {
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
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
    @livewireScripts
@endpush
