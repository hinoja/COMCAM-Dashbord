<div class="card-body p-4">
    <!-- Messages -->


    <style>
        /* Reset et styles de base */
        * {
            box-sizing: border-box;
            margin: 0;
        }

        /* Styles globaux */
        :root {
            --primary: #4e73df;
            --secondary: #858796;
            --success: #1cc88a;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
            border: none;
        }

        .form-select,
        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #d1d3e2;
            padding: 0.65rem 1rem;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn {
            border-radius: 0.35rem;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .shadow-sm {
            box-shadow: var(--shadow);
        }

        .rounded-lg {
            border-radius: 0.75rem;
        }
    </style>
    <!-- Filtres -->
    <div class="row g-3 mb-4 bg-light p-4 rounded-lg shadow-sm">
        <div class="col-md-3 col-12 mb-3 mb-md-0">
            <div class="form-group h-100 d-flex flex-column justify-content-end">
                <label class="form-label fw-bold mb-2">
                    <i class="fas fa-search me-1 text-primary"></i>Recherche
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                    <input type="text" wire:model.live.debounce.600ms="search" class="form-control"
                        placeholder="Rechercher par nom...">
                </div>
            </div>
        </div>
        <div class="col-md col-6 mb-3 mb-md-0">
            <div class="form-group h-100 d-flex flex-column justify-content-end">
                <label class="form-label fw-bold mb-2">
                    <i class="fas fa-leaf me-1 text-success"></i>Essence
                </label>
                <select wire:model.live="essenceFilter" class="form-select">
                    <option value="">Toutes les essences</option>
                    @foreach ($essences as $essence)
                        <option value="{{ $essence->id }}">{{ $essence->nom_local }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md col-6 mb-3 mb-md-0">
            <div class="form-group h-100 d-flex flex-column justify-content-end">
                <label class="form-label fw-bold mb-2">
                    <i class="fas fa-cube me-1 text-info"></i>Forme
                </label>
                <select wire:model.live="formeFilter" class="form-select">
                    <option value="">Toutes les formes</option>
                    @foreach ($formes as $forme)
                        <option value="{{ $forme->id }}">{{ $forme->designation }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md col-6 mb-3 mb-md-0">
            <div class="form-group h-100 d-flex flex-column justify-content-end">
                <label class="form-label fw-bold mb-2">
                    <i class="fas fa-tags me-1 text-warning"></i>Type
                </label>
                <select wire:model.live="typeFilter" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}">{{ $type->code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg col-md-12 col-12 mt-3 mb-2 d-flex align-items-end">
            <button type="button" wire:click="resetFilters" class="btn w-50 mr-3"
                style="background: linear-gradient(90deg, #4e73df 0%, #6fcf97 100%); color: #fff; border: none;">
                <i class="fas fa-undo"></i> Réinitialiser
            </button>
            <button type="button" wire:click="showTitresSensibles" style="background: rgb(196, 107, 107)"
                class="btn btn-danger flex-fill" title="Afficher uniquement les titres critiques"
                style="min-width: 140px;">
                <i class="fas fa-exclamation-triangle me-3"></i> Titres critiques
            </button>
        </div>

    </div>


    <!-- Tableau -->
    <div class="table-responsive shadow-sm rounded-lg bg-white">
        <table class="table table-hover table-striped table-bordered align-middle mb-0">
            <thead class="bg-primary">
                <tr class="text-xs text-white font-semibold uppercase tracking-wider">
                    <th style="color: white" class="p-3 text-nowrap">N°</th>
                    <th style="color: white" class="p-3 text-nowrap">Exercice</th>
                    <th style="color: white" class="p-3 text-nowrap">Nom</th>
                    <th style="color: white" class="p-3 text-nowrap">Localisation</th>
                    <th style="color: white" class="p-3 text-nowrap">Zone</th>
                    <th style="color: white" class="p-3 text-nowrap">Essence</th>
                    <th style="color: white" class="p-3 text-nowrap">Forme</th>
                    <th style="color: white" class="p-3 text-nowrap">Type</th>
                    <th style="color: white" class="p-3 text-nowrap">Volume (m³)</th>
                    <th style="color: white" class="p-3 text-nowrap">Volume Restant (m³)</th>
                    <th style="color: white" class="pe-4 py-3 align-middle text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($rows as $row)
                    @php
                        $volumeInitial = $row->volume;
                        $volumeRestant = $row->VolumeRestant;
                        $pourcentage = $volumeInitial > 0 ? ($volumeRestant / $volumeInitial) * 100 : 0;
                        $progressColor =
                            $volumeRestant > $volumeInitial || $volumeRestant <= 0
                                ? 'bg-danger'
                                : ($pourcentage <= 30
                                    ? 'bg-warning'
                                    : 'bg-success');
                    @endphp
                    <tr class="transition-all hover:bg-gray-50 @if ($loop->odd) bg-light @endif">
                        <td class="p-3">{{ $loop->iteration + ($rows->currentPage() - 1) * $rows->perPage() }}</td>
                        <td class="p-3">{{ $row->exercice }}</td>
                        <td class="p-3">{{ $row->titre_nom }}</td>
                        <td class="p-3">{{ $row->localisation }}</td>
                        <td class="p-3">{{ $row->zone_nom }}</td>
                        <td class="p-3">{{ $row->essence_nom }}</td>
                        <td class="p-3">{{ $row->forme_nom }}</td>
                        <td class="p-3">{{ $row->type_code }}</td>
                        <td class="p-3" class="badge bg-primary fs-6" style="color: white"> <span
                                class="badge bg-primary fs-6">{{ number_format($volumeInitial, 3, ',', ' ') }}</span>
                        </td>
                        <td class="p-3">
                            @php
                                // Définir la couleur selon le volume restant
                                $isCritical = $volumeRestant <= 0 || $pourcentage <= 5;
                                $valueColor = $isCritical ? '#dc3545' : '#198754'; // rouge si critique, vert sinon
                                $bgColor = $isCritical ? '#f8d7da' : '#d1e7dd'; // fond rouge clair si critique, vert clair sinon
                                // Barre de progression : rouge si critique, jaune si <=30%, vert sinon
                                $progressBarColor = $isCritical
                                    ? '#dc3545'
                                    : ($pourcentage <= 30
                                        ? '#ffc107'
                                        : '#6fcf97');
                            @endphp
                            <span class="fw-bold"
                                style="color: {{ $valueColor }}; background: {{ $bgColor }}; border-radius: 0.35rem; padding: 0.2em 0.7em;">
                                {{ number_format($volumeRestant, 3, ',', ' ') }}
                            </span>
                            <div class="progress mt-1" style="height: 6px; background: #e9ecef;">
                                <div class="progress-bar" role="progressbar"
                                    style="width: {{ min(max($pourcentage, 0), 100) }}%; background: {{ $progressBarColor }};"
                                    aria-valuenow="{{ $pourcentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted" style="color: {{ $isCritical ? '#dc3545' : '' }}">
                                {{ number_format($pourcentage, 1) }}%
                            </small>
                        </td>
                        <td class="p-3">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.titre.edit', $row->titre_id) }}"
                                    class="mr-2 btn btn-sm btn-primary me-2" title="Éditer">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="showDeleteForm({{ $row->titre_id }})"
                                    class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                              
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-search fa-2x mb-3 opacity-75"></i>
                                <p class="fs-5">Aucun résultat trouvé avec ces critères</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination et infos -->
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="text-muted">
            Affichage de {{ $rows->firstItem() }} à {{ $rows->lastItem() }} sur {{ $rows->total() }} résultats
        </div>
        <div class="pagination-wrapper">
            {{ $rows->links() }}
        </div>
    </div>


    {{-- MOdal de suppression --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 0.75rem;">
                <div class="modal-header"
                    style="background: linear-gradient(90deg, #4e73df 0%, #6fcf97 100%); color: #fff;">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        @lang('Suppression du Titre')
                    </h5>
                    <button type="button" class="close" wire:click="closeModal" aria-label="Close"
                        style="color: #fff;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background: #f8d7da; color: #842029; border-radius: 0.5rem;">
                    <p>
                        <strong>@lang('Êtes-vous sûr de vouloir supprimer ce titre ?')</strong>
                    </p>
                    <p>
                        <i class="fas fa-info-circle me-1"></i>
                        @lang('Confirmer la suppression ? Toutes les transactions associées à ce titre seront également supprimées.')
                    </p>
                </div>
                <div class="modal-footer" style="background: #f8f9fc; border-top: 1px solid #e9ecef;">
                    <button type="button" style="background: #f8f9fc; border-top: 1px solid #e9ecef;" class="btn "
                        data-bs-dismiss="modal" wire:click="closeModal">@lang('Annuler')</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteTitre"
                        wire:loading.attr="disabled" style="background: #e74a3b; border: none;">
                        <span wire:loading wire:target="deleteTitre">
                            <i class="fas fa-spinner fa-spin mr-1"></i> @lang('Suppression...')
                        </span>
                        <span wire:loading.remove wire:target="deleteTitre">
                            <i class="fas fa-trash mr-1"></i> @lang('Supprimer')
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('openDeleteModal', () => {
                $('#deleteModal').modal('show');

            });

            Livewire.on('closeModal', () => {
                ['editModal', 'deleteModal'].forEach(modalId => {
                    $('#editModal').modal('hide');
                    $('#deleteModal').modal('hide');

                });
            });
        });
    </script>
@endpush

</div>
