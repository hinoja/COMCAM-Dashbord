<div>
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-striped table-bordered align-middle mb-0">
                    <thead class="bg-primary">
                        <tr class="text-xs text-white font-semibold uppercase tracking-wider">
                            <th style="color: white" class="p-3 text-nowrap">N°</th>
                            <th style="color: white" class="p-3 text-nowrap">Acronyme</th>
                            <th style="color: white" class="p-3 text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($societes as $societe)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $societe->acronym }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button style="background: blue" wire:click="showEditForm({{ $societe }})"
                                            type="button" class="btn btn-sm btn-primary mr-1" data-toggle="tooltip"
                                            title="Modifier"><i class="fas fa-edit"></i></button>
                                        <button style="background: red" wire:click="showDeleteForm({{ $societe }})"
                                            type="button" class="btn btn-sm btn-danger" data-toggle="tooltip"
                                            title="Supprimer"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-right">
            <nav class="d-inline-block">
                {{ $societes->links() }}
            </nav>
        </div>
    </div>

    <!-- Modal de suppression -->
    <div class="modal fade" id="deleteSocieteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($societeToDelete)
                        <p>Êtes-vous sûr de vouloir supprimer la société "{{ $societeToDelete->acronym }}" ?</p>
                        <p class="text-danger"><small>Cette action est irréversible.</small></p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Supprimer</button>
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
</div>
