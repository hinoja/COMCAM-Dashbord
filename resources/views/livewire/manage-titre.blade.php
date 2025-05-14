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
        <div class="col-md-3 col-12">
            <div class="form-group">
                <label class="form-label fw-bold mb-2">Recherche</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                    <input type="text" wire:model.live.debounce.600ms="search" class="form-control"
                        placeholder="Rechercher par nom...">
                </div>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="form-group">
                <label class="form-label fw-bold mb-2">Essence</label>
                <select wire:model.live="essenceFilter" class="form-select">
                    <option value="">Toutes les essences</option>
                    @foreach ($essences as $essence)
                        <option value="{{ $essence->id }}">{{ $essence->nom_local }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="form-group">
                <label class="form-label fw-bold mb-2">Forme</label>
                <select wire:model.live="formeFilter" class="form-select">
                    <option value="">Toutes les formes</option>
                    @foreach ($formes as $forme)
                        <option value="{{ $forme->id }}">{{ $forme->designation }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="form-group">
                <label class="form-label fw-bold mb-2">Type</label>
                <select wire:model.live="typeFilter" class="form-select">
                    <option value="">Tous les types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->id }}">{{ $type->code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="form-group">
                <label class="form-label fw-bold mb-2">Par page</label>
                <select wire:model.live="perPage" class="form-select">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
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
                @forelse($titres as $titre)
                    @php
                        $essenceCount = $titre->essence->count();
                        $rowspan = $essenceCount > 0 ? $essenceCount : 1;
                    @endphp

                    @if ($essenceCount > 0)
                        @foreach ($titre->essence as $index => $essence)
                            <tr
                                class="transition-all hover:bg-gray-50 @if ($index % 2 == 1) bg-light @endif">
                                @if ($index === 0)
                                    <td class="p-3" rowspan="{{ $rowspan }}">{{ $loop->parent->iteration }}</td>
                                    <td class="p-3" rowspan="{{ $rowspan }}">{{ $titre->exercice }}</td>
                                    <td class="p-3" rowspan="{{ $rowspan }}">{{ $titre->nom }}</td>
                                    <td class="p-3" rowspan="{{ $rowspan }}">{{ $titre->localisation }}</td>
                                    <td class="p-3" rowspan="{{ $rowspan }}">{{ $titre->zone->name ?? '-' }}
                                    </td>
                                @endif
                                <td class="p-3">{{ $essence->nom_local }}</td>
                                <td class="p-3">{{ $essence->formeEssence->forme->designation ?? '-' }}</td>
                                <td class="p-3">{{ $essence->formeEssence->type->code ?? '-' }}</td>
                                <td class="p-3">
                                    {{ number_format($essence->pivot->volume, 3, ',', ' ') }}

                                </td>
                                <td class="p-3">
                                    @php
                                        $volumeInitial = $essence->pivot->volume;
                                        $volumeRestant = $essence->pivot->VolumeRestant;
                                        $pourcentage = ($volumeRestant / $volumeInitial) * 100;
                                    @endphp
                                    <span class="@if ($volumeRestant > $volumeInitial || $volumeRestant <= 0)
                                            text-danger font-weight-bold
                                         @elseif ($pourcentage <= 30)
                                            text-warning font-weight-bold
                                         @else
                                            text-success font-weight-bold
                                         @endif">
                                        {{ number_format($volumeRestant, 3, ',', ' ') }}

                                        <small class="d-block text-muted">
                                            ({{ number_format($pourcentage, 1) }}%)
                                        </small>
                                    </span>
                                </td>
                                @if ($index === 0)
                                    <td class="p-3" rowspan="{{ $rowspan }}">
                                        <div class="btn-group" role="group">
                                            {{-- <button wire:click="showDetails({{ $titre->id }})"
                                                class="btn btn-sm btn-info mr-2" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </button> --}}
                                            <a href="{{ route('admin.titre.edit', $titre) }}"
                                                class="mr-2 btn btn-sm btn-primary me-2" title="Éditer">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button wire:click="delete({{ $titre->id }})"
                                                class="mr-2 btn btn-sm btn-danger me-2" title="Supprimer"
                                                onclick="return confirm('Confirmer la suppression ? Toutes les transactions associées à ce titre seront également supprimées.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr class="transition-all hover:bg-gray-50">
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">{{ $titre->exercice }}</td>
                            <td class="p-3">{{ $titre->nom }}</td>
                            <td class="p-3">{{ $titre->localisation }}</td>
                            <td class="p-3">{{ $titre->zone->name ?? '-' }}</td>
                            <td class="p-3">-</td>
                            <td class="p-3">-</td>
                            <td class="p-3">-</td>
                            <td class="p-3">-</td>
                            <td class="p-3">-</td>
                            <td class="p-3">
                                <div class="btn-group" role="group">
                                    {{-- <button wire:click="showDetails({{ $titre->id }})"
                                        class="btn btn-sm btn-info mr-2" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </button> --}}

                                    <button wire:click="deleteTransaction({{ $titre->id }})"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')"
                                        title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-search fa-2x mb-3 opacity-75"></i>
                                <p class="fs-5">Aucun titre trouvé avec ces critères</p>
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
            Affichage de {{ $titres->firstItem() }} à {{ $titres->lastItem() }} sur {{ $titres->total() }} résultats
        </div>
        <div class="pagination-wrapper">
            {{ $titres->links() }}
        </div>
    </div>



    <!-- Modale pour afficher les détails du titre -->
    @if ($selectedTitre)
        <div class="modal fade fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
            wire:ignore.self>
            <div class="bg-white rounded-xl shadow-2xl w-11/12 md:w-1/2 p-6 transform transition-all duration-300 scale-100 animate-fadeIn"
                role="dialog" aria-labelledby="modal-title">
                <!-- En-tête de la modale -->
                <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
                    <h3 id="modal-title"
                        class="text-xl font-bold text-gray-800 bg-clip-text bg-gradient-to-r from-purple-600 to-purple-800">
                        Détails du Titre : {{ $selectedTitre->nom }}
                    </h3>
                    <button wire:click="closeDetails"
                        class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-all duration-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Corps de la modale -->
                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            <label class="font-medium text-gray-700">Exercice :</label>
                            <p class="text-gray-900 mt-1">{{ $selectedTitre->exercice }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            <label class="font-medium text-gray-700">Nom :</label>
                            <p class="text-gray-900 mt-1">{{ $selectedTitre->nom }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            <label class="font-medium text-gray-700">Localisation :</label>
                            <p class="text-gray-900 mt-1">{{ $selectedTitre->localisation }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            <label class="font-medium text-gray-700">Zone :</label>
                            <p class="text-gray-900 mt-1">{{ $selectedTitre->zone->name ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Tableau des essences -->
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold mb-3">Essences associées</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-gray-300 p-2 text-left">Essence</th>
                                        <th class="border border-gray-300 p-2 text-left">Volume</th>
                                        <th class="border border-gray-300 p-2 text-left">Volume Restant</th>
                                        <th class="border border-gray-300 p-2 text-left">Forme</th>
                                        <th class="border border-gray-300 p-2 text-left">Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedTitre->essence as $essence)
                                        <tr>
                                            <td class="border border-gray-300 p-2">{{ $essence->nom_local }}</td>
                                            <td class="border border-gray-300 p-2">
                                                {{ number_format($essence->pivot->volume, 3, ',', ' ') }} m³

                                            </td>
                                            <td
                                                class="border border-gray-300 p-2 @if ($essence->pivot->VolumeRestant <= 0) text-danger @endif">
                                                {{ number_format($essence->pivot->VolumeRestant, 3, '.', ' ') }} m³
                                            </td>
                                            <td class="border border-gray-300 p-2">
                                                {{ $essence->formeEssence->forme->designation ?? '-' }}</td>
                                            <td class="border border-gray-300 p-2">
                                                {{ $essence->formeEssence->type->code ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="border border-gray-300 p-2 text-center">Aucune
                                                essence associée</td>
                                        </tr>
                                    @endforelse
                                    <tr class="bg-gray-100 font-semibold">
                                        <td class="border border-gray-300 p-2">Total</td>
                                        <td class="border border-gray-300 p-2">
                                            {{ number_format($selectedTitre->essence->sum('pivot.volume'), 3, ',', ' ') }}

                                            m³</td>
                                        <td class="border border-gray-300 p-2">
                                            {{ number_format($selectedTitre->essence->sum('pivot.VolumeRestant'), 3,',', ' ') }}
                                            m³</td>
                                        <td class="border border-gray-300 p-2">-</td>
                                        <td class="border border-gray-300 p-2">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pied de la modale -->
                <div class="flex justify-end mt-6">
                    <button wire:click="closeDetails"
                        class="px-4 py-2 bg-gradient-primary text-white rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif


</div>
