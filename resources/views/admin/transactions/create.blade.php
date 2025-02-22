@extends('layouts.back')

@section('subtitle', __('Transactions'))

@section('content')
    <div class="section-body">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">
                    <i class="fas fa-exchange-alt text-primary mr-2"></i>
                    Nouvelle Transaction
                </h2>
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

                        <form method="POST" action="{{ route('admin.transaction.store') }}" class="needs-validation" >
                            @csrf
                            <div class="card-body">
                                <!-- Alertes d'erreur -->
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Veuillez corriger les erreurs suivantes
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <!-- Section: Informations Générales -->
                                <div class="p-3 bg-light rounded-lg mb-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Informations Générales
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    Exercice
                                                </label>
                                                <select name="exercice"
                                                    class="form-control select2 @error('exercice') is-invalid @enderror">
                                                    <option disabled value="">Sélectionner une année</option>
                                                    @php
                                                    $currentYear = date('Y'); // Année en cours
                                                    $startYear = $currentYear - 2; // Début de la plage d'années
                                                    $endYear = $currentYear + 3; // Fin de la plage d'années
                                                @endphp
                                                @for ($year = $startYear; $year <= $endYear; $year++)
                                                    <option value="{{ $year }}"
                                                        {{ old('exercice', $currentYear) == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endfor
                                                </select>
                                                @error('exercice')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="far fa-calendar mr-1"></i>
                                                    Date
                                                </label>
                                                @php
                                                    $date=now();
                                                @endphp
                                                <input type="date" name="date" value={{ $date}}
                                                    class="form-control @error('date') is-invalid @enderror"
                                                    value="{{ old('date') }}">
                                                @error('date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-hashtag mr-1"></i>
                                                    Numéro
                                                </label>
                                                <input type="number" name="numero" placeholder="423"
                                                    class="form-control @error('numero') is-invalid @enderror"
                                                    value="{{ old('numero') }}">
                                                @error('numero')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section: Détails du Titre -->
                                <div class="p-3 bg-light rounded-lg mb-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-file-alt mr-2"></i>
                                        Détails du Titre
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    Titre
                                                </label>
                                                <select name="titre_id"
                                                    class="form-control select2 @error('titre_id') is-invalid @enderror">
                                                    <option value="">Sélectionner un titre</option>
                                                    @foreach ($titres as $titre)
                                                        <option value="{{ $titre->id }}"
                                                            {{ old('titre_id') == $titre->id ? 'selected' : '' }}>
                                                            {{ $titre->nom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('titre_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    Destination
                                                </label>
                                                <input type="text" name="destination" placeholder="Maroua"
                                                    class="form-control @error('destination') is-invalid @enderror"
                                                    value="{{ old('destination') }}">
                                                @error('destination')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section: Caractéristiques -->
                                <div class="p-3 bg-light rounded-lg mb-4">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-cube mr-2"></i>
                                        Caractéristiques
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-box mr-1"></i>
                                                    Conditionnement
                                                </label>
                                                <select name="conditionnemment_id"
                                                    class="form-control select2 @error('conditionnemment_id') is-invalid @enderror">
                                                    <option value="">Sélectionner un conditionnement</option>
                                                    @foreach ($conditionnements as $conditionnement)
                                                        <option value="{{ $conditionnement->id }}"
                                                            {{ old('conditionnemment_id') == $conditionnement->id ? 'selected' : '' }}>
                                                            {{ $conditionnement->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('conditionnemment_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-tree mr-1"></i>
                                                    Essence
                                                </label>
                                                <select name="essence_id"
                                                    class="form-control select2 @error('essence_id') is-invalid @enderror">
                                                    <option value="">Sélectionner une essence</option>
                                                    @foreach ($essences as $essence)
                                                        <option value="{{ $essence->id }}"
                                                            {{ old('essence_id') == $essence->id ? 'selected' : '' }}>
                                                            {{ $essence->nom_local }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('essence_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-building mr-1"></i>
                                                    Société
                                                </label>
                                                <select name="societe_id"
                                                    class="form-control select2 @error('societe_id') is-invalid @enderror">
                                                    <option value="">Sélectionner une société</option>
                                                    @foreach ($societes as $societe)
                                                        <option value="{{ $societe->id }}"
                                                            {{ old('societe_id') == $societe->id ? 'selected' : '' }}>
                                                            {{ $societe->acronym }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('societe_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-globe mr-1"></i>
                                                    Pays
                                                </label>
                                                <input type="text" name="pays" placeholder="Nigeria"
                                                    class="form-control @error('pays') is-invalid @enderror"
                                                    value="{{ old('pays') }}">
                                                @error('pays')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-shapes mr-1"></i>
                                                    Forme
                                                </label>
                                                <select name="forme_id"
                                                    class="form-control select2 @error('forme_id') is-invalid @enderror">
                                                    <option value="">Sélectionner une forme</option>
                                                    @foreach ($formes as $forme)
                                                        <option value="{{ $forme->id }}"
                                                            {{ old('forme_id') == $forme->id ? 'selected' : '' }}>
                                                            {{ $forme->designation }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('forme_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    Type
                                                </label>
                                                <select name="type_id"
                                                    class="form-control select2 @error('type_id') is-invalid @enderror">
                                                    <option value="">Sélectionner un type</option>
                                                    @foreach ($types as $type)
                                                        <option value="{{ $type->id }}"
                                                            {{ old('type_id') == $type->id ? 'selected' : '' }}>
                                                            {{ $type->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('type_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-0">
                                                <label class="font-weight-bold text-muted">
                                                    <i class="fas fa-cubes mr-1"></i>
                                                    Volume (m³)
                                                </label>
                                                <input type="number" name="volume" placeholder="500"
                                                    class="form-control @error('volume') is-invalid @enderror"
                                                    value="{{ old('volume') }}" step="0.01">
                                                @error('volume')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-white border-top-0 text-right py-3">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-save mr-2"></i>
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Card Import Excel -->
                    <div class="card shadow-lg rounded-lg border-0 mt-4">
                        <div class="card-header bg-gradient-success text-white py-3">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-file-excel mr-2"></i>
                                Import Excel
                            </h4>
                        </div>

                        <form method="POST" action="{{ route('import.titre.post') }}" enctype="multipart/form-data">
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

                            <div class="card-footer bg-white border-top-0 text-right py-3">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-upload mr-2"></i>
                                    Importer
                                </button>
                            </div>
                        </form>
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
