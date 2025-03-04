<div class="card-body p-4">
    <!-- Messages -->
    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show mt-1 shadow-sm rounded-lg" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('message') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
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
        <div class="col-md col-6 mt-4">
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


        <div class="col-md col-6 mt-4">
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
        <div class="col-md-2 col-6 mt-4">
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
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="bg-primary text-white">
                <tr class="text-xs text-white font-semibold text-gray-600 uppercase tracking-wider">
                    <th style="color: white" class="p-3 text-nowrap">Exercice</th>
                    <th style="color: white" class="p-3 text-nowrap">Nom</th>
                    <th style="color: white" class="p-3 text-nowrap">Localisation</th>
                    <th style="color: white" class="p-3 text-nowrap">Zone</th>
                    <th style="color: white" class="p-3 text-nowrap">Essence</th>
                    <th style="color: white" class="p-3 text-nowrap">Forme</th>
                    <th style="color: white" class="p-3 text-nowrap">Type</th>
                    <th style="color: white" class="p-3 text-nowrap">Volume</th>
                    <th style="color: white" class="p-3 text-nowrap">Volume Rest.</th>
                    <th style="color: white" class="pe-4 py-3 align-middle text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($titres as $titre)
                    <tr class="transition-all hover:bg-gray-50">
                        <td class="p-3">{{ $titre->exercice }}</td>
                        <td class="p-3">{{ $titre->nom }}</td>
                        <td class="p-3">{{ $titre->localisation }}</td>
                        <td class="p-3">{{ $titre->zone->name ?? '-' }}</td>
                        <td class="p-3">{{ $titre->essence->nom_local ?? '-' }}</td>
                        <td class="p-3">{{ $titre->forme->designation ?? '-' }}</td>
                        <td class="p-3">{{ $titre->type->code ?? '-' }}</td>
                        <td class="p-3">{{ $titre->volume }}</td>
                        <td class="p-3">{{ $titre->VolumeRestant ?? '-' }}</td>
                        <td class="p-3">
                            <div class="btn-group" role="group">
                                <button wire:click="showDetails({{ $titre->id }})" class="btn btn-sm btn-info"
                                    title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="{{ route('admin.titre.edit', $titre) }}"
                                    class="mr-2 btn btn-sm btn-primary me-2" title="Éditer">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="delete({{ $titre->id }})" class="btn btn-sm btn-danger"
                                    title="Supprimer" onclick="return confirm('Confirmer la suppression ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
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
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm"
             wire:ignore.self>
            <div class="bg-white rounded-xl shadow-2xl w-11/12 md:w-1/2 p-6 transform transition-all duration-300 scale-100 animate-fadeIn"
                 role="dialog" aria-labelledby="modal-title">
                <!-- En-tête de la modale -->
                <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
                    <h3 id="modal-title" class="text-xl font-bold text-gray-800 bg-clip-text bg-gradient-to-r from-purple-600 to-purple-800">
                        Détails du Titre : {{ $selectedTitre->nom }}
                    </h3>
                    <button wire:click="closeDetails" class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-all duration-300">
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
                        <div class="p-3 bg-gray-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            <label class="font-medium text-gray-700">Essence :</label>
                            <p class="text-gray-900 mt-1">{{ $selectedTitre->essence->nom_local ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            <label class="font-medium text-gray-700">Forme :</label>
                            <p class="text-gray-900 mt-1">{{ $selectedTitre->forme->designation ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            <label class="font-medium text-gray-700">Type :</label>
                            <p class="text-gray-900 mt-1">{{ $selectedTitre->type->code ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            <label class="font-medium text-gray-700">Volume :</label>
                            <p class="text-success mt-1">{{ number_format((float) $selectedTitre->volume, 2, ',', ' ') }} m³</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                            <label class="font-medium text-gray-700">Volume Restant :</label>
                            <p class="text-success mt-1">{{ $selectedTitre->VolumeRestant ? number_format((float) $selectedTitre->VolumeRestant, 2, ',', ' ') . ' m³' : '-' }}</p>
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
    <style>
        /* Styles spécifiques à la modale des détails du titre */
        #titreDetailsModal .modal-content {
            border-radius: 0.75rem; /* Bordures arrondies plus prononcées */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15); /* Ombre plus marquée */
            transition: all 0.3s ease; /* Transition fluide pour les interactions */
        }

        #titreDetailsModal .modal-header {
            border-radius: 0.75rem 0.75rem 0 0; /* Bordures arrondies uniquement en haut */
            background: linear-gradient(45deg, #2d6a4f 0%, #1cc88a 100%); /* Gradient vert du thème */
            padding: 1.5rem; /* Espacement intérieur plus large */
        }

        #titreDetailsModal .modal-body {
            padding: 1.5rem; /* Espacement intérieur plus large */
        }

        #titreDetailsModal .modal-footer {
            border-radius: 0 0 0.75rem 0.75rem; /* Bordures arrondies uniquement en bas */
            background-color: #f8f9fc; /* Gris clair pour le footer */
            padding: 1.5rem; /* Espacement intérieur plus large */
        }

        #titreDetailsModal .form-group {
            margin-bottom: 1rem; /* Espacement entre les groupes */
        }

        /* Cartes pour les sections */
        #titreDetailsModal .form-group > div {
            display: flex;
            flex-direction: column;
            padding: 1rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        #titreDetailsModal .form-group > div:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); /* Ombre plus prononcée au survol */
            transform: translateY(-2px); /* Légère élévation au survol */
        }

        /* Cartes vertes (titres et volumes) */
        #titreDetailsModal .form-group:nth-child(odd) > div,
        #titreDetailsModal .form-group:nth-child(8) > div,
        #titreDetailsModal .form-group:nth-child(9) > div {
            background-color: #e8f5e9; /* Vert clair pour les cartes vertes */
            border: 1px solid #2d6a4f; /* Bordure verte */
        }

        /* Cartes grises (autres sections) */
        #titreDetailsModal .form-group:nth-child(even) > div {
            background-color: #f8f9fc; /* Gris clair pour les cartes grises */
            border: 1px solid #d1d3e2; /* Bordure grise */
        }

        /* Labels et valeurs */
        #titreDetailsModal .control-label {
            font-weight: bold;
            color: #000000; /* Noir pour les labels */
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        #titreDetailsModal .text-gray-900,
        #titreDetailsModal .text-success {
            color: #000000; /* Noir pour les valeurs */
            font-size: 1rem;
            line-height: 1.5;
        }

        #titreDetailsModal .text-success {
            color: #1cc88a; /* Vert pour les volumes */
        }

        /* Grille responsive */
        #titreDetailsModal .modal-body .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            #titreDetailsModal .modal-body .grid {
                grid-template-columns: repeat(2, 1fr); /* 2 colonnes sur desktop */
            }
        }

        /* Bouton "Fermer" */
        #titreDetailsModal .btn-primary {
            background: linear-gradient(45deg, #4e73df 0%, #224abe 100%); /* Gradient violet */
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
        }

        #titreDetailsModal .btn-primary:hover {
            background: linear-gradient(45deg, #3f5ed1 0%, #1a3aa0 100%); /* Violet plus sombre au survol */
            box-shadow: 0 6px 12px rgba(78, 115, 223, 0.3);
            transform: translateY(-2px);
        }

        /* Ajustements pour les transitions et animations */
        #titreDetailsModal .modal-content {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
    Expli
</div>
