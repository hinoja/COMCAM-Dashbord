<div>
    <!-- Liste des sociétés à droite -->
    <div class="col-lg-8 col-md-7 col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0"><i class="fas fa-list mr-2 text-primary"></i>Liste des Sociétés</h4>
                <div class="input-group w-50">
                    <input type="text" class="form-control shadow-sm" placeholder="Rechercher une société...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col">Nom</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($societes as $societe)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $societe->acronym }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button wire:click="showEditForm({{ $societe }})" type="button"
                                                class="btn btn-sm btn-primary mr-1" data-toggle="tooltip"
                                                title="Modifier"><i class="fas fa-edit"></i></button>
                                            <button wire:click="showDeleteForm({{ $societe }})" type="button"
                                                class="btn btn-sm btn-danger" data-toggle="tooltip" title="Supprimer"><i
                                                    class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach


                            <!-- Répéter les lignes pour les autres sociétés -->
                        </tbody>
                    </table>
                    <div class="card-footer text-right">
                        <nav class="d-inline-block">
                            {{ $societes->links() }}
                        </nav>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
