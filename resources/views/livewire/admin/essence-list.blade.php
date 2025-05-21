<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Colonne de gauche : Formulaire -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4> <i class="fas fa-plus"></i> {{ $isEditing ? "Modifier l'essence" : 'Ajouter une essence' }}
                    </h4>
                </div>

                <form wire:submit.prevent="{{ $isEditing ? 'update' : 'save' }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="code">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                wire:model="code" placeholder="Ex: SAP">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nom_local">Nom Local <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom_local') is-invalid @enderror"
                                wire:model="nom_local" placeholder="Ex: Sapelli">
                            @error('nom_local')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        @if ($isEditing)
                            <button style="background: rgb(194, 193, 193)" type="button" class="btn " wire:click="cancel">
                                <i class="fas fa-times mr-1"></i>Annuler
                            </button>
                        @endif
                        <button style="background: rgb(69,132,103);" type="submit"
                            class="btn btn-primary  float-right">
                            <i class="fas fa-save mr-1"></i>
                            {{ $isEditing ? 'Mettre à jour' : 'Enregistrer' }}
                        </button> <br>
                    </div>
                    <br>
                </form>
            </div>

            <!-- Carte pour l'import/export Excel -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Import/Export Excel</h4>
                </div>
                <div class="card-body">
                    <!-- Bouton Export -->
                    <a href="{{ route('admin.essence.export') }}" class="btn btn-success btn-block mb-3">
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
                        <button type="submit" class="btn btn-primary btn-block" style="background:rgb(69,132,103);">
                            <i class="fas fa-upload mr-2"></i>Importer Excel
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Colonne de droite : Liste -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Liste des Essences</h4>
                    <div class="card-header-form">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Rechercher..."
                                wire:model.live="search">
                            <div class="input-group-btn">
                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Code</th>
                                    <th>Nom Local</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($essences as $essence)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $essence->code }}</td>
                                        <td>{{ $essence->nom_local }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary"
                                                wire:click="edit({{ $essence->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                wire:click="confirmDelete({{ $essence->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
                <div class="card-footer">
                    {{ $essences->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
