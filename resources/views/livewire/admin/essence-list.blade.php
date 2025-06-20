<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="background: #e6f4ea; color: #256029; border-color: #b7e0c2;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: #256029;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Colonne de gauche : Formulaire -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="background: #f8faf9;">
                <div class="card-header" style="background: #458467; color: #fff;">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> {{ $isEditing ? "Modifier l'essence" : 'Ajouter une essence' }}
                    </h4>
                </div>

                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="code" style="color: #458467;">Code <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                wire:model="code" placeholder="Ex: SAP" style="border-color: #b7e0c2;">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nom_local" style="color: #458467;">Nom Local <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom_local') is-invalid @enderror"
                                wire:model="nom_local" placeholder="Ex: Sapelli" style="border-color: #b7e0c2;">
                            @error('nom_local')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-0">
                        @if ($isEditing)
                            <button style="background: #c2c1c1; color: #fff;" type="button" class="btn"
                                wire:click="cancel">
                                <i class="fas fa-times mr-1"></i>Annuler
                            </button>
                        @endif
                        <button style="background: #458467; border: none;" type="submit"
                            class="btn btn-primary float-right">
                            <i class="fas fa-save mr-1"></i>
                            {{ $isEditing ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                        <br>
                    </div>
                    <br>
                </form>
            </div>

            @if (auth()->check() && auth()->user()->role_id === 1)
                <!-- Carte pour l'import/export Excel -->
                <div class="card mt-4 shadow-sm border-0" style="background: #f8faf9;">
                    <div class="card-header" style="background: #458467; color: #fff;">
                        <h4 class="mb-0">Import/Export Excel</h4>
                    </div>
                    <div class="card-body">
                        <!-- Bouton Export -->
                        <a href="{{ route('admin.essence.export') }}" class="btn btn-success btn-block mb-3"
                            style="background: #458467; border: none;">
                            <i class="fas fa-download mr-2"></i>Télécharger Excel
                        </a>

                        <!-- Formulaire Import -->
                        <form action="{{ route('admin.essence.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file" name="file"
                                        accept=".xlsx,.xls,.csv">
                                    <label class="custom-file-label" for="file">Choisir un fichier</label>
                                </div>
                                @error('file')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-block"
                                style="background: #458467; border: none;">
                                <i class="fas fa-upload mr-2"></i>Importer Excel
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Colonne de droite : Liste -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="background: #f8faf9;">
                <div class="card-header d-flex justify-content-between align-items-center"
                    style="background: #458467; color: #fff;">
                    <h4 class="mb-0">Liste des Essences</h4>
                    <div class="card-header-form">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Rechercher..."
                                wire:model.live="search" style="border-color: #b7e0c2;">
                            <div class="input-group-btn">
                                <button class="btn" style="background: #458467; color: #fff;"><i
                                        class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle mb-0"
                            style="background: #fff;">
                            <thead style="background: #458467;">
                                <tr class="text-xs text-white font-semibold uppercase tracking-wider">
                                    <th style="color: white" class="p-3 text-nowrap">N°</th>
                                    <th style="color: white" class="p-3 text-nowrap">Code</th>
                                    <th style="color: white" class="p-3 text-nowrap">Nom Local</th>
                                    <th style="color: white" class="p-3 text-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($essences as $essence)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $essence->code }}</td>
                                        <td>{{ $essence->nom_local }}</td>
                                        <td>
                                            <button class="btn btn-sm" style="background: #458467; color: #fff;"
                                                wire:click="edit({{ $essence->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button style="background: #e74c3c; color: #fff;"
                                                wire:click="showDeleteForm({{ $essence->id }})" type="button"
                                                class="btn btn-sm" data-toggle="tooltip" title="Supprimer"><i
                                                    class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Aucune essence trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    {{ $essences->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de suppression -->
    <div class="modal fade" id="deleteEssenceModal" tabindex="-1" aria-labelledby="deleteEssenceModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 0.75rem;">
                <div class="modal-header"
                    style="background: linear-gradient(90deg, #4e73df 0%, #6fcf97 100%); color: #fff;">
                    <h5 class="modal-title" id="deleteEssenceModalLabel">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        @lang('Suppression de l\'Essence')
                    </h5>
                    <button type="button" class="close" wire:click="closeModal" aria-label="Close"
                        style="color: #fff;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background: #f8d7da; color: #842029; border-radius: 0.5rem;">
                    @if ($essenceToDelete)
                        <p>
                            <strong>
                                @lang('Êtes-vous sûr de vouloir supprimer l\'essence :')
                                "<span class="text-dark">{{ $essenceToDelete->nom_local }}</span>" ?
                            </strong>
                        </p>
                        <p>
                            <i class="fas fa-info-circle me-1"></i>
                            @lang('Cette action est irréversible.')
                        </p>
                    @endif
                </div>
                <div class="modal-footer" style="background: #f8f9fc; border-top: 1px solid #e9ecef;">
                    <button type="button" style="background: #f8f9fc; border-top: 1px solid #e9ecef;" class="btn"
                        data-dismiss="modal" wire:click="closeModal">@lang('Annuler')</button>
                    <button type="button" class="btn btn-danger" wire:click="delete"
                        wire:loading.attr="disabled" style="background: #e74a3b; border: none;">
                        <span wire:loading wire:target="delete">
                            <i class="fas fa-spinner fa-spin mr-1"></i> @lang('Suppression...')
                        </span>
                        <span wire:loading.remove wire:target="delete">
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
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('openDeleteModal', () => {
                $('#deleteEssenceModal').modal('show');
            });

            Livewire.on('closeModal', () => {
                $('#deleteEssenceModal').modal('hide');
            });
        });
    </script>
@endpush
