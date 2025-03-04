<div>
    <form method="POST" action="{{ route('admin.transaction.store') }}" class="needs-validation">
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
                                $date = now();
                            @endphp
                            <input type="date" name="date" value={{ $date }}
                                class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}">
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
                                class="form-control @error('numero') is-invalid @enderror" value="{{ old('numero') }}">
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
                                class="form-control @error('pays') is-invalid @enderror" value="{{ old('pays') }}">
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

        <div class="card-footer  border-top-0 text-right py-3">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-save mr-2"></i>
                Enregistrer
            </button>
        </div>
    </form>
</div>
