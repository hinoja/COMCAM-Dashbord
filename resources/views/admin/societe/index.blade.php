@extends('layouts.back')

@section('subtitle', __('Societe'))

@section('content')
    <div class="section-body mt-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title m-0"><i class="fas fa-building mr-2 text-primary"></i>Gestion des Sociétés</h2>
                <span class="badge badge-primary">{{ __('Total: ') }} 301 sociétés</span>
            </div>
            <hr class="mb-4">

            <div class="row">
                <!-- Formulaires à gauche -->
                <div class="col-lg-4 col-md-5 col-12 mb-4">
                    <div class="card shadow-sm">
                        <form>
                            <div class="card-header bg-white">
                                <h4 class="card-title mb-0"><i class="fas fa-plus-circle mr-2 text-success"></i>Ajout d'une
                                    nouvelle Entreprise</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="acronyme" class="font-weight-bold"><i
                                            class="fas fa-tag mr-1 text-muted"></i> Acronyme</label>
                                    <input type="text" id="acronyme" class="form-control shadow-sm"
                                        placeholder="Ex: SARL, SA, SAS..." required>
                                    <small class="form-text text-muted">Entrez l'acronyme de l'entreprise
                                        (obligatoire).</small>
                                </div>


                            </div>
                            <div class="card-footer bg-white">
                                <button type="submit" class="btn btn-primary btn-block shadow-sm"><i
                                        class="fas fa-plus-circle mr-1"></i> Ajouter</button>
                            </div>
                        </form>
                    </div>

                    <div class="card shadow-sm mt-4">
                        <form method="POST" action="{{ route('import.societe.post') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header bg-white">
                                <h4 class="card-title mb-0"><i class="fas fa-file-excel mr-2 text-success"></i>Importer
                                    depuis Excel</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="file" class="font-weight-bold"><i
                                            class="fas fa-upload mr-1 text-muted"></i> Fichier Excel</label>
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
                                    <i class="fas fa-lightbulb mr-1"></i> <strong>Conseil:</strong> Assurez-vous que votre
                                    fichier Excel contient au minimum une colonne "Acronyme" et respecte le format attendu.
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <button type="submit" class="btn btn-success btn-block shadow-sm"><i
                                        class="fas fa-upload mr-1"></i> Importer</button>
                            </div>
                        </form>
                    </div>
                </div>

                @livewire('admin.societe-list')
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Affichage du nom du fichier sélectionné
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Choisir un fichier');
            });

            // Activation des tooltips Bootstrap
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
