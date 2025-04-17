<div class="card">
    <div class="card-header">
        <h4>Ajouter une Essence</h4>
    </div>

    <form wire:submit.prevent="save">
        <div class="card-body">
            <div class="row">
                <!-- Code -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="code">Code <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('code') is-invalid @enderror" 
                               wire:model="code"
                               placeholder="Ex: SAP">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Nom Local -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nom_local">Nom Local <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nom_local') is-invalid @enderror" 
                               wire:model="nom_local"
                               placeholder="Ex: Sapelli">
                        @error('nom_local')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i>
                Enregistrer
            </button>
        </div>
    </form>
</div>