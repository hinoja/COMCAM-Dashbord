<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="background: #d1f7e6; color: #207055; border-color: #7ed6a7;">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: #207055;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="row">
        <!-- Colonne de gauche : Formulaire et Import Excel -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4" style="background: #f8faf9;">
                <div class="card-header" style="background: #458467; color: #fff;">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i>
                        {{ $isEditing ? "Modifier l'entreprise" : 'Ajouter une entreprise' }}
                    </h4>
                </div>
                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="acronym" style="color: #458467;">Acronyme <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('acronym') is-invalid @enderror"
                                wire:model="acronym" placeholder="Ex: ABC" style="border-color: #b7e0c2;">
                            @error('acronym')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Ajoute ici d'autres champs si besoin -->

                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @if ($isEditing)
                            <button style="background: #c2c1c1; color: #fff;" type="button" class="btn"
                                wire:click="closeModal()">
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
            @if (auth()->check() && auth()->user()->role_id < 2)
                <div class="card border-0 rounded-lg" style="background: #f8faf9;">
                    <form method="POST" action="{{ route('admin.societe.import') }}" enctype="multipart/form-data"
                        class="h-100 d-flex flex-column">
                        @csrf
                        <div class="card-header bg-white border-bottom py-3">
                            <h4 class="card-title mb-0 text-emerald font-weight-bold">
                                <i class="fas fa-file-excel mr-2"></i>Importer depuis Excel
                            </h4>
                        </div>
                        <div class="card-body p-4 ">
                            <div class="form-group mb-3">
                                <label for="file" class="font-weight-bold text-dark">
                                    <i class="fas fa-upload mr-1 text-muted"></i> Fichier Excel
                                </label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('file') is-invalid @enderror"
                                        id="file" name="file" accept=".xlsx,.xls" required>
                                    <label class="custom-file-label" for="file">Choisir un fichier</label>
                                </div>
                                @error('file')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle mr-1"></i> Formats acceptés: .xls, .xlsx
                                </small>
                            </div>
                            
                            <div class="alert alert-info small mb-0" role="alert">
                                <i class="fas fa-lightbulb mr-1"></i> <strong>Conseil:</strong> Assurez-vous que
                                votre
                                fichier Excel contient au minimum une colonne "Acronyme".
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top py-3 mt-auto">
                            <button type="submit" style="background:rgb(69,132,103)"
                                class="btn btn-action-visible btn-lg btn-block">
                                <i class="fas fa-upload mr-1"></i> Importer
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
        <!-- Colonne de droite : Liste -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="background: #f8faf9;">
                <div class="card-header d-flex justify-content-between align-items-center"
                    style="background: #458467; color: #fff;">
                    <h4 class="mb-0">Liste des Entreprises</h4>
                    <div class="card-header-form">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Rechercher une entreprise..."
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
                                    <th style="color: white" class="p-3 text-nowrap">Acronyme</th>
                                    <th style="color: white" class="p-3 text-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($societes as $societe)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $societe->acronym }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm" style="background: #458467; color: #fff;"
                                                wire:click="edit({{ $societe->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button style="background: #e74c3c; color: #fff;"
                                                wire:click="showDeleteForm({{ $societe->id }})" type="button"
                                                class="btn btn-sm" data-toggle="tooltip" title="Supprimer"><i
                                                    class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Aucune entreprise trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    {{ $societes->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de suppression -->
    <div class="modal fade" id="deleteSocieteModal" tabindex="-1" aria-labelledby="deleteSocieteModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 0.75rem;">
                <div class="modal-header"
                    style="background: linear-gradient(90deg, #4e73df 0%, #6fcf97 100%); color: #fff;">
                    <h5 class="modal-title" id="deleteSocieteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        @lang('Suppression de la Société')
                    </h5>
                    <button type="button" class="close" wire:click="closeModal" aria-label="Close"
                        style="color: #fff;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background: #f8d7da; color: #842029; border-radius: 0.5rem;">
                    @if ($societeToDelete)
                        <p>
                            <strong>
                                @lang('Êtes-vous sûr de vouloir supprimer la société') "{{ $societeToDelete->acronym }}" ?
                            </strong>
                        </p>
                        <p>
                            <i class="fas fa-info-circle me-1"></i>
                            @lang('Cette action est irréversible. Toutes les données associées à cette société seront également supprimées.')
                        </p>
                    @endif
                </div>
                <div class="modal-footer" style="background: #f8f9fc; border-top: 1px solid #e9ecef;">
                    <button type="button" style="background: #f8f9fc; border-top: 1px solid #e9ecef;" class="btn"
                        data-dismiss="modal" wire:click="closeModal">@lang('Annuler')</button>
                    <button type="button" class="btn btn-danger" wire:click="delete" wire:loading.attr="disabled"
                        style="background: #e74a3b; border: none;">
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
                $('#deleteSocieteModal').modal('show');
            });

            Livewire.on('closeModal', () => {
                $('#deleteSocieteModal').modal('hide');
            });
        });
    </script>
@endpush
