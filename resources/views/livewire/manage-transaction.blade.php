<div>
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

        <!-- Filtres -->
        <div class="row g-3 mb-4 bg-light p-4 rounded-lg shadow-sm">

            <div class="col-md col-3">
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
            <div class="col-md col-3 mt-4">
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
            <div class="col-md col-3 mt-4">
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
            <div class="col-md col-5 mt-4">
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
            <div class="col-md col-6 mt-4">
                <div class="form-group">
                    <label class="form-label fw-bold mb-2">Titre</label>
                    <select wire:model.live="titreFilter" class="form-select tomselect" id="titre-select">
                        <option value="">Tous les titres</option>
                        @foreach ($titres as $titre)
                            <option value="{{ $titre->id }}">{{ $titre->nom }}</option>
                        @endforeach
                    </select>
                </div>
                @if($titreFilter)
                    <a href="{{ route('admin.transaction.export-by-titre', ['titre_id' => $titreFilter]) }}"
                       class="btn btn-success btn-sm mt-2">
                        <i class="fas fa-file-excel me-2"></i>
                        Exporter les transactions
                    </a>
                @endif
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
                        <th style="color: white" class="pe-4 py-3 align-middle text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr class="transition-all hover:bg-gray-50">
                            <td class="p-3">{{ $loop->iteration }}</td>
                            <td class="p-3">{{ $transaction->date }}</td>
                            <td class="p-3">{{ $transaction->exercice }}</td>
                            <td class="p-3">{{ $transaction->numero }}</td>
                            <td class="p-3">{{ $transaction->societe->acronym ?? '-' }}</td>
                            <td class="p-3">{{ $transaction->destination }}</td>
                            <td class="p-3">{{ $transaction->pays }}</td>
                            <td class="p-3">{{ $transaction->titre->nom ?? '-' }}</td>
                            <td class="p-3">{{ $transaction->essence->nom_local ?? '-' }}</td>
                            <td class="p-3">{{ $transaction->essence->formeEssence->forme->designation ?? '-' }}
                            </td>
                            <td class="p-3">{{ $transaction->essence->formeEssence->type->code ?? '-' }}</td>
                            <td class="p-3">{{ number_format($transaction->volume, 2, ',', '.') }}</td>
                            <td class="p-3">
                                <div class="btn-group" role="group">
                                    {{-- <button wire:click="showDetails({{ $transaction->id }})"
                                        class="btn btn-sm btn-primary" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </button> --}}
                                    <a href="{{ route('admin.transaction.edit', $transaction->id) }}"
                                        class="mr-2 btn btn-sm btn-primary me-2" title="Éditer">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button wire:click="deleteTransaction({{ $transaction->id }})"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')"
                                        title="Supprimer">
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
                                    <p class="fs-5">Aucune transaction trouvée avec ces critères</p>
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

        <!-- Modale pour afficher les détails de la transaction -->
        @if ($selectedTransaction)
            <div class="modal fade" id="transactionDetailsModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Détails de la transaction</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Détails de la transaction ici -->
                            <p>Date : {{ $selectedTransaction->date }}</p>
                            <p>Société : {{ $selectedTransaction->societe }}</p>
                            <!-- Ajoutez d'autres champs -->
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @push('js')
        <script>
            document.addEventListener('livewire:load', function() {
                Livewire.on('showTransactionDetails', function() {
                    $('#transactionDetailsModal').modal('show'); // Utilise jQuery avec Bootstrap
                });
            });
        </script>
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
                            Livewire.emit('deleteTransaction', id); // Déclenche la suppression
                        }
                    });
                });
            });
        </script>
    @endpush
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    @endpush
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('livewire:load', function() {
            new TomSelect('#titre-select', {
                placeholder: 'Sélectionner un titre',
                searchField: ['nom'],
                valueField: 'id',
                labelField: 'nom',
                allowEmptyOption: true,
                create: false
            });
        });
    </script>
    @endpush
</div>

