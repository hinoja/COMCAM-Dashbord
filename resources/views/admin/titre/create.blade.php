@extends('layouts.back')

@section('subtitle', __('Titre'))

@section('content')
    <div class="section-body mt-4">
        <div class="container">
            <h2 class="section-title text-primary">Liste des Titres</h2>
            <hr class="my-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-12">
                    <div class="card shadow">
                        <!-- Premier formulaire : Ajout d'un Nouveau Titre -->
                        <form method="POST" action="{{ route('admin.titre.store') }}">
                            @csrf
                            @foreach ($errors as $error)
                                <div class="alert alert-danger">{{ $error }}</div>
                            @endforeach
                            <div class="card-header bg-white">
                                <h4 class="card-title text-primary"><i class="fas fa-plus-circle mr-2"></i>Ajout d'un
                                    Nouveau Titre</h4>
                            </div>

                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="exercice" class="font-weight-bold"><i
                                                class="far fa-calendar-alt mr-1"></i> Exercice (Année)</label>
                                        <select id="exercice" name="exercice"
                                            class="form-control select-custom @error('exercice') is-invalid @enderror"
                                            required>
                                            <option value="" disabled>Sélectionner une année</option>
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
                                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="nom" class="font-weight-bold"><i class="far fa-file-alt mr-1"></i>
                                            Nom</label>
                                        <input type="text" id="nom" name="nom"
                                            class="form-control shadow-sm @error('nom') is-invalid @enderror"
                                            value="{{ old('nom') }}" placeholder="UFA 07004 AAC 1" required>
                                        @error('nom')
                                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="localisation" class="font-weight-bold"><i
                                            class="fas fa-map-marker-alt mr-1"></i> Localisation</label>
                                    <input type="text" id="localisation" name="localisation"
                                        class="form-control shadow-sm @error('localisation') is-invalid @enderror"
                                        value="{{ old('localisation') }}" placeholder="Nkondjock-nkongsamba" required>
                                    @error('localisation')
                                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="zone" class="font-weight-bold"><i
                                                class="fas fa-globe-africa mr-1"></i> Zone</label>
                                        <select id="zone" name="zone_id"
                                            class="form-control select-custom @error('zone') is-invalid @enderror" required>
                                            <option value="" selected disabled>Sélectionner une zone</option>
                                            @foreach ($zones as $zone)
                                                <option value="{{ $zone->id }}"
                                                    {{ old('zone') == $zone->id ? 'selected' : '' }}>
                                                    {{ $zone->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('zone')
                                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="essence" class="font-weight-bold"><i class="fas fa-tree mr-1"></i>
                                            Essence</label>
                                        <select id="essence" name="essence_id"
                                            class="form-control select-custom @error('essence') is-invalid @enderror"
                                            required>
                                            <option value="" disabled>Sélectionner une essence</option>
                                            @foreach ($essences as $essence)
                                                <option value="{{ $essence->id }}"
                                                    {{ old('essence') == $essence->id ? 'selected' : '' }}>
                                                    {{ $essence->nom_local }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('essence')
                                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="forme" class="font-weight-bold"><i class="fas fa-shapes mr-1"></i>
                                            Forme</label>
                                        <select id="forme" name="forme_id"
                                            class="form-control select-custom @error('forme') is-invalid @enderror"
                                            required>
                                            <option value="" selected disabled>Sélectionner une forme</option>
                                            @foreach ($formes as $forme)
                                                <option value="{{ $forme->id }}"
                                                    {{ old('forme') == $forme->id ? 'selected' : '' }}>
                                                    {{ $forme->designation }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('forme')
                                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="type" class="font-weight-bold"><i class="fas fa-tag mr-1"></i>
                                            Type</label>
                                        <select id="type" name="type_id"
                                            class="form-control select-custom @error('type') is-invalid @enderror" required>
                                            <option value="" selected disabled>Sélectionner un type</option>
                                            @foreach ($types as $type)
                                                <option value="{{ $type->id }}"
                                                    {{ old('type') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="volume" class="font-weight-bold"><i class="fas fa-cubes mr-1"></i>
                                        Volume</label>
                                    <div class="input-group">
                                        <input type="number" id="volume" name="volume"
                                            class="form-control shadow-sm @error('volume') is-invalid @enderror"
                                            value="{{ old('volume') }}" min="0" placeholder="500" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
                                    @error('volume')
                                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer bg-white text-right py-3">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <i class="fas fa-plus-circle mr-1"></i> Ajouter
                                </button>
                            </div>
                        </form>

                    </div>

                    <!-- Deuxième formulaire : Upload de fichier Excel -->
                    <div class="card shadow mt-4 mb-5">
                        <form method="POST" action="{{ route('import.titre.post') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header bg-white">
                                <h4 class="card-title text-primary"><i class="fas fa-file-excel mr-2"></i>Importer depuis
                                    Excel</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-0">
                                    <label for="file" class="font-weight-bold"><i class="fas fa-upload mr-1"></i>
                                        Fichier Excel</label>
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input @error('file') is-invalid @enderror" id="file"
                                            name="file" accept=".xlsx,.xls,.csv">
                                        <label class="custom-file-label" for="file">Choisir un fichier</label>
                                        @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white text-right py-3">
                                <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                    <i class="fas fa-upload mr-1"></i> Importer
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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
    <style>
        .ts-wrapper .ts-control {
            border: 1px solid #e4e6fc;
            border-radius: .25rem;
            padding: 0.6rem 1rem;
            height: calc(2.25rem + 2px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            background-color: #fdfdff;
            transition: all 0.3s;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #6777ef;
            box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.25);
        }

        .ts-dropdown {
            border-color: #e4e6fc;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .ts-dropdown .active {
            background-color: #6777ef;
            color: #fff;
        }

        .ts-dropdown .create {
            color: #28a745;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Configuration commune pour tous les select TomSelect
        const tomSelectConfig = {
            plugins: ['remove_button'],
            render: {
                option: function(data, escape) {
                    return '<div class="py-2 px-3">' + escape(data.text) + '</div>';
                },
                item: function(data, escape) {
                    return '<div>' + escape(data.text) + '</div>';
                }
            }
        };

        // Initialisation de tous les champs select avec la classe select-custom
        document.querySelectorAll('.select-custom').forEach(function(element) {
            let config = {
                ...tomSelectConfig
            };

            // Configuration spécifique pour le champ essence
            if (element.id === 'essence') {
                config.create = true;
                config.sortField = {
                    field: "text",
                    direction: "asc"
                };
                config.placeholder = "Sélectionner une essence...";
            }

            new TomSelect(element, config);
        });

        // Afficher le nom du fichier sélectionné
        document.querySelector('.custom-file-input').addEventListener('change', function() {
            var fileName = this.value.split('\\').pop();
            document.querySelector('.custom-file-label').textContent = fileName || 'Choisir un fichier';
        });
    </script>
    <script>
        // Définition des relations forme-type
        const typeRelations = {
            'Grume': ['5N'],
            'Débité': ['6.1', '6.2'],
            'PS': ['PS']
        };

        // Lorsque la page est chargée
        $(document).ready(function() {
            // Écouteur d'événement sur le changement de forme
            $('#forme').on('change', function() {
                const selectedForme = $(this).find('option:selected').text();
                const typeSelect = $('#type');

                // Vider les options actuelles
                typeSelect.empty();
                typeSelect.append('<option value="" disabled selected>Sélectionner un type</option>');

                // Ajouter les nouvelles options en fonction de la forme sélectionnée
                if (typeRelations[selectedForme]) {
                    typeRelations[selectedForme].forEach(type => {
                        // Rechercher l'ID correspondant dans la collection $types
                        undefined
                        if ('{{ $type->designation }}' === type) {
                            typeSelect.append(
                                `<option value="{{ $type->id }}">${type}</option>`);
                        }

                    });
                }
            });
        });
    </script>
@endpush
