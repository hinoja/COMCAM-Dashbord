<div class="card-body p-4">
    <!-- Messages -->
    @if (session('message') || session('success') || session('error'))
        <div class="alert {{ session('success') || session('message') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show mt-3 shadow-sm rounded-lg"
            role="alert">
            <div class="d-flex align-items-center">
                <i
                    class="fas {{ session('success') || session('message') ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-2"></i>
                {{ session('message') ?? (session('success') ?? session('error')) }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Styles -->
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

        .highlight {
            background-color: #fefcbf;
            font-weight: bold;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table th,
        .table td {
            vertical-align: middle;
            border: 1px solid #e3e6f0;
        }

        .table th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
        }

        .table-hover tbody tr {
            transition: background-color 0.2s ease;
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

        .pagination .page-link {
            border-radius: 50%;
            margin: 0 3px;
            color: var(--primary);
            border: none;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #ccc;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
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
                        placeholder="Numéro, titre, société, essence..." autocomplete="off">
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
        <div class="col-md col-6">
            <div class="form-group">
                <label class="form-label fw-bold mb-2">Société</label>
                <select wire:model.live="societeFilter" class="form-select">
                    <option value="">Toutes les sociétés</option>
                    @foreach ($societes as $societe)
                        <option value="{{ $societe->id }}">{{ $societe->acronym }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- <div class="col-md col-6">
            <div class="form-group">
                <label class="form-label fw-bold mb-2">Titre</label>
                <select wire:model.live="titreFilter" class="form-select">
                    <option value="">Tous les titres</option>
                    @foreach ($titres as $titre)
                        <option value="{{ $titre->id }}">{{ $titre->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div> --}}
        <div class="col-md col-6">
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
        <div class="col-md col-6 d-flex align-items-end">
            <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                <i class="fas fa-undo me-2"></i>Réinitialiser
            </button>
        </div>
    </div>

    <!-- Tableau -->
    <div class="table-responsive shadow-sm rounded-lg bg-white">
        <table class="table table-hover table-striped table-bordered align-middle mb-0">
            <thead class="bg-primary text-white">
                <tr class="text-xs font-semibold uppercase tracking-wider">
                    <th style="color: white" class="p-3 text-nowrap">N°</th>
                    <th style="color: white" class="p-3 text-nowrap">Date</th>
                    <th style="color: white" class="p-3 text-nowrap">Exercice</th>
                    <th style="color: white" class="p-3 text-nowrap">Numéro</th>
                    <th style="color: white" class="p-3 text-nowrap">Société</th>
                    <th style="color: white" class="p-3 text-nowrap">Destination</th>
                    <th style="color: white" class="p-3 text-nowrap">Pays</th>
                    <th style="color: white" class="p-3 text-nowrap">Titre</th>
                    <th style="color: white" class="p-3 text-nowrap">Essence</th>
                    <th style="color: white" class="p-3 text-nowrap">Forme</th>
                    <th style="color: white" class="p-3 text-nowrap">Type</th>
                    <th style="color: white" class="p-3 text-nowrap">Volume</th>
                    <th style="color: white" class="p-3 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr class="transition-all hover:bg-gray-50">
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $transaction->date }}</td>
                        <td class="p-3">{{ $transaction->exercice }}</td>
                        <td class="p-3">{!! str_ireplace($searchTerm, "<span class='highlight'>$searchTerm</span>", $transaction->numero) !!}</td>
                        <td class="p-3">{!! $transaction->societe
                            ? str_ireplace($searchTerm, "<span class='highlight'>$searchTerm</span>", $transaction->societe->acronym ?? '-')
                            : '-' !!}</td>
                        <td class="p-3">{!! str_ireplace($searchTerm, "<span class='highlight'>$searchTerm</span>", $transaction->destination) !!}</td>
                        <td class="p-3">{!! str_ireplace($searchTerm, "<span class='highlight'>$searchTerm</span>", $transaction->pays) !!}</td>
                        <td class="p-3">{!! $transaction->titre
                            ? str_ireplace($searchTerm, "<span class='highlight'>$searchTerm</span>", $transaction->titre->nom ?? '-')
                            : '-' !!}</td>
                        <td class="p-3">{!! $transaction->essence
                            ? str_ireplace($searchTerm, "<span class='highlight'>$searchTerm</span>", $transaction->essence->nom_local ?? '-')
                            : '-' !!}</td>
                        <td class="p-3">
                            {{ $transaction->essence && $transaction->essence->formeEssence && $transaction->essence->formeEssence->forme ? $transaction->essence->formeEssence->forme->designation : '-' }}
                        </td>
                        <td class="p-3">
                            {{ $transaction->essence && $transaction->essence->formeEssence && $transaction->essence->formeEssence->type ? $transaction->essence->formeEssence->type->code : '-' }}
                        </td>
                        <td class="p-3">{{ number_format($transaction->volume, 2, ',', '.') }}</td>
                        <td class="p-3 text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.transaction.edit', $transaction->id) }}"
                                    class="btn btn-sm btn-primary me-2" title="Éditer">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click="deleteTransaction({{ $transaction->id }})"
                                    class="btn btn-sm btn-danger ml-2" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-search fa-2x mb-3 opacity-75"></i>
                                <p class="fs-5 mb-3">Aucune transaction trouvée avec ces critères</p>
                                <button wire:click="resetFilters" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-undo me-2"></i>Réinitialiser les filtres
                                </button>
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
            Affichage de {{ $transactions->firstItem() }} à {{ $transactions->lastItem() }} sur
            {{ $transactions->total() }} résultats
        </div>
        <div class="pagination-wrapper">
            {{ $transactions->links() }}
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener('livewire:load', function() {
                Livewire.on('confirmDelete', function(id) {
                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Cette action est irréversible !",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Oui, supprimer !',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.emit('deleteTransaction', id);
                        }
                    });
                });
            });
        </script>
    @endpush
</div>
