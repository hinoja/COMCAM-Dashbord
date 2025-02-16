@extends('layouts.back')

@section('subtitle', __('Societe'))

@section('content')
    <div class="section-body mt-4">
        <div class="container">
            <h2 class="section-title text-primary">Liste des Titres</h2>
            <hr class="my-4">

            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-12">
                    <div class="card shadow">
                        <!-- Premier formulaire : Ajout d'un Nouveau Titre -->
                        <form>
                            <div class="card-header bg-white">
                                <h4 class="card-title text-primary"><i class="fas fa-plus-circle mr-2"></i>Ajout d'un Nouveau
                                    Titre</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="exercice" class="font-weight-bold"><i
                                                class="far fa-calendar-alt mr-1"></i> Exercice (Année)</label>
                                        <select id="exercice" class="form-control custom-select shadow-sm" required>
                                            <option value="" selected disabled>Sélectionner une année</option>
                                            @php
                                                $currentYear = date('Y');
                                                $startYear = $currentYear - 5;
                                                $endYear = $currentYear + 5;
                                            @endphp
                                            @for ($year = $startYear; $year <= $endYear; $year++)
                                                <option value="{{ $year }}"
                                                    {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="nom" class="font-weight-bold"><i class="far fa-file-alt mr-1"></i>
                                            Nom</label>
                                        <input type="text" id="nom" class="form-control shadow-sm" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="localisation" class="font-weight-bold"><i
                                            class="fas fa-map-marker-alt mr-1"></i> Localisation</label>
                                    <input type="text" id="localisation" class="form-control shadow-sm" required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="zone" class="font-weight-bold"><i
                                                class="fas fa-globe-africa mr-1"></i> Zone</label>
                                        <select id="zone" class="form-control custom-select shadow-sm" required>
                                            <option value="" selected disabled>Sélectionner une zone</option>
                                            <!-- Options à remplir dynamiquement -->
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="essence" class="font-weight-bold"><i class="fas fa-tree mr-1"></i>
                                            Essence</label>
                                        <select id="essence" class="form-control custom-select shadow-sm" required>
                                            <option value="" selected disabled>Sélectionner une essence</option>
                                            <!-- Options à remplir dynamiquement -->
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="forme" class="font-weight-bold"><i class="fas fa-shapes mr-1"></i>
                                            Forme</label>
                                        <select id="forme" class="form-control custom-select shadow-sm" required>
                                            <option value="" selected disabled>Sélectionner une forme</option>
                                            <!-- Options à remplir dynamiquement -->
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="type" class="font-weight-bold"><i class="fas fa-tag mr-1"></i>
                                            Type</label>
                                        <select id="type" class="form-control custom-select shadow-sm" required>
                                            <option value="" selected disabled>Sélectionner un type</option>
                                            <!-- Options à remplir dynamiquement -->
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="volume" class="font-weight-bold"><i class="fas fa-cubes mr-1"></i>
                                        Volume</label>
                                    <div class="input-group">
                                        <input type="number" id="volume" class="form-control shadow-sm" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">m³</span>
                                        </div>
                                    </div>
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
                                        <input type="file" class="custom-file-input @error('file') is-invalid @enderror"
                                            id="file" name="file" required>
                                        <label class="custom-file-label" for="file">Choisir un fichier</label>
                                        @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white text-right py-3">
                                <button type="submit" class="btn btn-success px-4 shadow-sm">
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

@push('js')
    <script>
        $(document).ready(function() {
            // Afficher le nom du fichier sélectionné
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName);
            });
        });
    </script>
@endpush
