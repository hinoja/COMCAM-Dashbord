<div>
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }

        .modal-custom {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1060;
            width: 100%;
            max-width: 500px;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-header.bg-warning {
            background-color: #ffc107;
            color: #856404;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            padding: 1rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem;
            border-top: 1px solid #dee2e6;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .btn-confirm {
            background-color: #ffc107;
            color: #856404;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            margin-left: 0.5rem;
        }

        body.modal-open {
            overflow: hidden;
        }
    </style>

    <form wire:submit.prevent="save" class="needs-validation">
        <style>
            /* Dans votre fichier CSS principal */
            .modal-backdrop {
                opacity: 0.5 !important;
                background-color: #000;
            }

            .alert-success {
                border-left: 4px solid #28a745;
            }

            .modal-header.bg-warning {
                background-color: #ffc107;
                color: #856404;
            }
        </style>
        @csrf
        <div class="card-body">
            <!-- Alerte de succès -->
            @if ($showSuccessAlert)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    Transaction enregistrée avec succès !
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <!-- Alertes d'erreur -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Veuillez corriger les erreurs suivantes
                    {{-- <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul> --}}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
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
                            <select wire:model="exercice"
                                class="form-control select2 @error('exercice') is-invalid @enderror">
                                <option disabled value="">Sélectionner une année</option>
                                @php
                                    $currentYear = date('Y');
                                    $startYear = $currentYear - 0;
                                    $endYear = $currentYear + 2;
                                @endphp
                                @for ($year = $startYear; $year <= $endYear; $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
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
                            <input type="date" wire:model="date"
                                class="form-control @error('date') is-invalid @enderror">
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">
                                <i class="fas fa-hashtag mr-1"></i>
                                Numero
                            </label>
                            <input wire:model="numero" type="number" placeholder="423"
                                class="form-control @error('numero') is-invalid @enderror">
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
                                <i class="fas fa-tree mr-1"></i>
                                Essence
                            </label>
                            <select wire:model="essence_id"
                                class="form-control select2 @error('essence_id') is-invalid @enderror">
                                <option value="">Sélectionner une essence</option>
                                @foreach ($essences as $essence)
                                    <option value="{{ $essence->id }}">{{ $essence->nom_local }}</option>
                                @endforeach
                            </select>
                            @error('essence_id')
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
                            <input wire:model="pays" type="text" placeholder="Nigeria"
                                class="form-control @error('pays') is-invalid @enderror">
                            @error('pays')
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
                            <input wire:model="destination" type="text" placeholder="Maroua"
                                class="form-control @error('destination') is-invalid @enderror">
                            @error('destination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">
                                <i class="fas fa-tag mr-1"></i>
                                Titre
                            </label>
                            <select wire:model="titre_id"
                                class="form-control select2 @error('titre_id') is-invalid @enderror">
                                <option value="">Sélectionner un titre</option>
                                @foreach ($titres as $titre)
                                    <option value="{{ $titre->id }}">{{ $titre->nom }}</option>
                                @endforeach
                            </select>
                            @error('titre_id')
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
                                <i class="fas fa-building mr-1"></i>
                                Société
                            </label>
                            <select wire:model="societe_id"
                                class="form-control select2 @error('societe_id') is-invalid @enderror">
                                <option value="">Sélectionner une société</option>
                                @foreach ($societes as $societe)
                                    <option value="{{ $societe->id }}">{{ $societe->acronym }}</option>
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
                                <i class="fas fa-shapes mr-1"></i>
                                Forme
                            </label>
                            <select wire:model="forme_id"
                                class="form-control select2 @error('forme_id') is-invalid @enderror">
                                <option value="">Sélectionner une forme</option>
                                @foreach ($formes as $forme)
                                    <option value="{{ $forme->id }}">{{ $forme->designation }}</option>
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
                                <i class="fas fa-box mr-1"></i>
                                Conditionnement
                            </label>
                            <select wire:model="conditionnemment_id"
                                class="form-control select2 @error('conditionnemment_id') is-invalid @enderror">
                                <option value="">Sélectionner un conditionnement</option>
                                @foreach ($conditionnements as $conditionnement)
                                    <option value="{{ $conditionnement->id }}">{{ $conditionnement->code }}</option>
                                @endforeach
                            </select>
                            @error('conditionnemment_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">
                                <i class="fas fa-tree mr-1"></i>
                                Essence
                            </label>
                            <select wire:model="essence_id"
                                class="form-control select2 @error('essence_id') is-invalid @enderror">
                                <option value="">Sélectionner une essence</option>
                                @foreach ($essences as $essence)
                                    <option value="{{ $essence->id }}">{{ $essence->nom_local }}</option>
                                @endforeach
                            </select>
                            @error('essence_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> --}}
                </div>
                <div class="row">

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">
                                <i class="fas fa-tag mr-1"></i>
                                Type
                            </label>
                            <select wire:model="type_id" class="form-control @error('type_id') is-invalid @enderror">
                                <option value="">Sélectionner un type</option>
                                @foreach ($filteredTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->code }}</option>
                                @endforeach
                            </select>
                            @error('type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-muted">
                                <i class="fas fa-cubes mr-1"></i>
                                Volume (m³/Kg)
                            </label>
                            <input type="number" wire:model="volume" placeholder="500"
                                class="form-control @error('volume') is-invalid @enderror" step="0.00001">
                            @error('volume')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- <div class="row"> --}}

                {{-- </div> --}}
            </div>


        </div>

        <div class="card-footer border-top-0 text-right py-3">
            <button type="submit" style="background: green" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-save mr-2"></i>
                Enregistrer
            </button>
        </div>
    </form>

    <!-- Modal de dépassement -->
    @if ($showDepassementModal)
    <div class="modal-overlay">
        <div class="modal-custom">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Avertissement
                    </h5>
                </div>
                <div class="modal-body">
                    <p class="alert alert-warning">
                        <strong>Attention !</strong> La transaction que vous essayez d'enregistrer dépasse le volume disponible.
                    </p>
                    <p>Détails du dépassement :</p>
                    <ul>
                        <li>Volume en excès : <strong>{{ number_format($depassementValue, 2) }} m³</strong></li>
                        @if ($volumeRestantGrume !== null)
                            <li>Volume initial restant en Grume : <strong>{{ number_format($volumeRestantGrume, 2) }} m³</strong></li>
                        @endif
                        @if ($volumeRestantDebite !== null)
                            <li>Volume initial restant en Débité (5N) : <strong>{{ number_format($volumeRestantDebite, 2) }} m³</strong></li>
                        @endif
                    </ul>
                    <p class="font-weight-bold">Êtes-vous sûr de vouloir poursuivre cette opération malgré le dépassement ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn-cancel"
                            wire:click="closeDepassementModal">
                        Annuler
                    </button>
                    <button type="button"
                            class="btn-confirm"
                            wire:click="confirmSaveWithDepassement">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@push('scripts')
<script>
document.addEventListener('livewire:load', function () {
    Livewire.on('showDepassementModal', function () {
        document.body.classList.add('modal-open');
    });

    Livewire.on('hideDepassementModal', function () {
        document.body.classList.remove('modal-open');
    });
});
</script>
@endpush

@push('js')
    <script>
        $('.select2').on('change', function() {
            @this.set($(this).attr('wire:model'), this.value);
        });
    </script>
@endpush


