@extends('layouts.back')

@section('subtitle', __('Titre'))

@section('content')
    <div class="section-body mt-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center p-3 rounded"
                style="background-color: #2d6a4f; color: white;">
                <!-- Logo et titre -->
                <div class="d-flex align-items-center">
                    <i class="fas fa-book-open fa-2x mr-3"></i> <!-- Icône pour "Titres" -->
                    <h2 class="m-0">Gestion des Titres</h2>
                </div>
                <!-- Badge avec le total des titres -->
                <a class="badge badge-light p-2" href="{{ route('admin.titre.index') }}"
                    style="background-color: #a8d5ba; color: black;">
                    Liste des Titres
                </a>
            </div>
            <hr class="my-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-12">
                    <div class="card shadow p-3">
                        <!-- Premier formulaire : Ajout d'un Nouveau Titre -->
                        @livewire('add-titre')
                    </div>

                    @if (auth()->user() && auth()->user()->role_id === 1)
                        <!-- Deuxième formulaire : Upload de fichier Excel -->
                        <div class="card shadow mt-4 mb-5">
                            <form method="POST" action="{{ route('import.titre.post') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="card-header bg-white">
                                    <h4 class="card-title text-primary"><i class="fas fa-file-excel mr-2"></i>Importer
                                        depuis Excel</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-0">
                                        <label for="file" class="font-weight-bold"><i class="fas fa-upload mr-1"></i>
                                            Fichier Excel</label>
                                        <div class="custom-file">
                                            <input type="file"
                                                class="custom-file-input @error('file') is-invalid @enderror" id="file"
                                                name="file" accept=".xlsx,.xls,.csv">
                                            <label class="custom-file-label" for="file">Choisir un fichier</label>
                                            @error('file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white text-right py-3">
                                    <button type="submit" class="btn px-4" style="color: white; background:green;">
                                        <i class="fas fa-upload mr-1"></i> Importer
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
    <style>
        .ts-wrapper .ts-control {
            border: 1px solid #e4e6fc;
            border-radius: .25rem;
            padding: 0.6rem 1rem;
            height: calc(2.25rem + 2px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            background-color: #fdfdff;
            transition: all 0.3s;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #6777ef;
            box-shadow: 0 0 0 0.2rem rgba(103, 119, 239, 0.25);
        }

        .ts-dropdown {
            border-color: #e4e6fc;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .ts-dropdown .active {
            background-color: #6777ef;
            color: #fff;
        }

        .ts-dropdown .create {
            color: #28a745;
        }
    </style>
    @livewireStyles()
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('livewire:load', function() {
            // Configuration globale de TomSelect (déjà définie ci-dessus)
            const tomSelectConfig = {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Sélectionner...",
                allowEmptyOption: true,
                onChange: function(value) {
                    Livewire.dispatch('updateSelect', {
                        name: this.input.name,
                        value: value
                    });
                }
            };

            const customConfigs = {
                'exercice': {
                    placeholder: "Sélectionner une année..."
                },
                'zone_id': {
                    placeholder: "Sélectionner une zone..."
                },
                'details.essence_id': {
                    create: true,
                    placeholder: "Sélectionner une essence...",
                    render: {
                        option: function(data, escape) {
                            return '<div class="py-2 px-3">' + escape(data.text) + '</div>';
                        },
                        item: function(data, escape) {
                            return '<div>' + escape(data.text) + '</div>';
                        }
                    }
                },
                'details.forme_id': {
                    placeholder: "Sélectionner une forme..."
                },
                'details.type_id': {
                    placeholder: "Sélectionner un type..."
                }
            };

            // function initTomSelect() {
            //     document.querySelectorAll('.select-custom').forEach(function(element) {
            //         const fieldName = element.getAttribute('name') || element.id.replace(/-/g, '.');
            //         let config = {
            //             ...tomSelectConfig
            //         };

            //         if (customConfigs[fieldName]) {
            //             config = {
            //                 ...config,
            //                 ...customConfigs[fieldName]
            //             };
            //         }

            //         if (element.tomselect) {
            //             element.tomselect.destroy();
            //         }

            //         new TomSelect(element, config);
            //     });
            // }

            // initTomSelect();
            // Livewire.hook('message.processed', () => initTomSelect());
        });
    </script>
@endpush
