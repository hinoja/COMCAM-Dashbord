@extends('layouts.back')

@section('subtitle', __('Societe'))

@section('content')
    <div class="section-body mt-4 attraction-bg">
        <div class="container">
            <!-- Header with Title and Total Count -->
            <div class="d-flex justify-content-between align-items-center mb-4 header-gradient rounded-lg shadow-lg p-3">
                <h2 class="section-title m-0 text-white font-weight-bold">
                    <i class="fas fa-building mr-2 text-gold"></i>Gestion des Sociétés
                </h2>
                <span class="badge badge-emerald px-3 py-2 shadow-sm">
                    {{ __('Total: ') }} ... sociétés
                </span>
            </div>

            <div class="row h-100">
                <!-- Left Column: Forms -->
                <div class="col-lg-4 col-md-5 col-12 d-flex flex-column">
                    <!-- Add New Company Card -->
                    <div class="card border-0 rounded-lg flex-grow-1">
                        <form class="h-100 d-flex flex-column">
                            <div class="card-header bg-white border-bottom py-3">
                                <h4 class="card-title mb-0 text-emerald font-weight-bold">
                                    <i class="fas fa-plus-circle mr-2"></i>Ajout d'une nouvelle Entreprise
                                </h4>
                            </div>
                            <div class="card-body p-4 flex-grow-1">
                                <div class="form-group">
                                    <label for="acronyme" class="font-weight-bold text-dark">
                                        <i class="fas fa-tag mr-1 text-muted"></i> Acronyme
                                    </label>
                                    <input type="text" id="acronyme" class="form-control border-emerald" placeholder="Ex: SARL, SA, SAS..." required>
                                    <small class="form-text text-muted mt-2">
                                        Entrez l'acronyme de l'entreprise (obligatoire).
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top py-3 mt-auto">
                                <button type="submit" style="background: rgb(69,132,103)" class="btn btn-action-visible btn-lg btn-block">
                                    <i class="fas fa-plus-circle mr-1"></i> Ajouter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Import from Excel Card -->
                    <div class="card border-0 rounded-lg mt-4 flex-grow-1">
                        <form method="POST" action="{{ route('import.societe.post') }}" enctype="multipart/form-data" class="h-100 d-flex flex-column">
                            @csrf
                            <div class="card-header bg-white border-bottom py-3">
                                <h4 class="card-title mb-0 text-emerald font-weight-bold">
                                    <i class="fas fa-file-excel mr-2"></i>Importer depuis Excel
                                </h4>
                            </div>
                            <div class="card-body p-4 flex-grow-1">
                                <div class="form-group mb-3">
                                    <label for="file" class="font-weight-bold text-dark">
                                        <i class="fas fa-upload mr-1 text-muted"></i> Fichier Excel
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.xls" required>
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
                                    <i class="fas fa-lightbulb mr-1"></i> <strong>Conseil:</strong> Assurez-vous que votre fichier Excel contient au minimum une colonne "Acronyme".
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top py-3 mt-auto">
                                <button type="submit" style="background:rgb(69,132,103)"  class="btn btn-action-visible btn-lg btn-block">
                                    <i class="fas fa-upload mr-1"></i> Importer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Societies List -->
                <div class="col-lg-8 col-md-8 col-12">
                    @livewire('admin.societe-list')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        /* Premium Color Palette */
        .text-emerald { color: #047857; } /* Emerald-700 */
        .text-gold { color: #d4af37; } /* Luxurious gold accent */
        .bg-emerald { background-color: #047857; }
        .border-emerald { border-color: #047857; }

        /* Visible action buttons */
        .btn-action-visible {
            background-color: #047857;
            color: white;
            font-weight: bold;
            border: none;
            box-shadow: 0 4px 6px rgba(4, 120, 87, 0.3);
        }

        .btn-action-visible:hover {
            background-color: #065f46;
            color: white;
        }

        .badge-emerald {
            background-color: #d1fae5;
            color: #047857;
            font-weight: bold;
        }

        /* Header Gradient */
        .header-gradient {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        }

        /* Subtle Background Texture */
        .attraction-bg {
            background: #f8fafc;
            min-height: 100vh;
        }

        /* Card styles - with full height support */
        .card {
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        /* Ensure the row takes full height */
        .section-body .row.h-100 {
            min-height: 80vh;
        }
    </style>
@endpush

@push('js')
    <script>
        $(document).ready(function() {
            // Display selected file name
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Choisir un fichier');
            });

            // Enable Bootstrap tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
