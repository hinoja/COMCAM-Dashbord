<div>
    <style>
        .action-buttons {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 10px;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        .remove-btn {
            background-color: #ff4444;
            color: white;
        }

        .remove-btn:hover {
            background-color: #cc0000;
        }

        .duplicate-btn {
            background-color: #00C851;
            color: white;
        }

        .duplicate-btn:hover {
            background-color: #007E33;
        }

        .add-transaction-btn {
            background-color: #4285F4;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
        }

        .add-transaction-btn:hover {
            background-color: #3367D6;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>

    <style>
        .transaction-row {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fc;
            position: relative;
            transition: all 0.3s ease;
        }

        .transaction-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .remove-btn,
        .duplicate-btn {
            position: absolute;
            top: 10px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .remove-btn {
            right: 40px;
            color: #dc3545;
        }

        .duplicate-btn {
            right: 10px;
            color: #28a745;
        }

        .add-btn {
            background: #28a745;
            color: white;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
        }

        .volume-rest {
            color: #4e73df;
            font-weight: bold;
        }

        .volume-rest.warning {
            color: #dc3545;
        }

        .accordion-button {
            background: #f8f9fc;
            font-weight: bold;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
        }

        .modal-custom {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1060;
            width: 100%;
            max-width: 600px;
        }
    </style>

    <form wire:submit.prevent="save" class="needs-validation">
        @csrf
        <div class="card-body">
            <!-- Notifications -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle mr-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- General Information -->
            <div class="p-3 bg-light rounded-lg mb-4">
                <h5 class="text-primary mb-3">
                    <i class="fas fa-info-circle mr-2"></i> Informations Générales
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Exercice</label>
                            <select wire:model.defer="exercice"
                                class="form-control @error('exercice') is-invalid @enderror">
                                <option value="">Sélectionner une année</option>
                                @php
                                    $currentYear = date('Y');
                                    $startYear = $currentYear - 0;
                                    $endYear = $currentYear + 2;
                                @endphp
                                @for ($year = $startYear; $year <= $endYear; $year++)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            @error('exercice')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-muted">Date</label>
                            <input type="date" wire:model.defer="date"
                                class="form-control @error('date') is-invalid @enderror">
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Transaction Button -->
            <button type="button" wire:click="addTransaction" class="add-btn">
                <i class="fas fa-plus-circle mr-2"></i> Ajouter une transaction
            </button>


            <!-- Transactions -->
            <div id="transactions-accordion">
                @foreach ($transactions as $index => $transaction)
                    <div class="transaction-row" wire:key="transaction-{{ $transaction['id'] }}">
                        @if (count($transactions) > 1)
                            <button type="button" wire:click="removeTransaction({{ $index }})"
                                class="action-btn remove-btn text-white" title="Supprimer cette transaction">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        @endif
                        <button type="button" wire:click="duplicateTransaction({{ $index }})"
                            class="duplicate-btn action-btn   text-white">
                            <i class="fas fa-copy"></i>
                             @if(count($transactions) >= $maxTransactions) disabled @endif
                        </button>

                        <div class="accordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $transaction['id'] }}" aria-expanded="true"
                                        aria-controls="collapse-{{ $transaction['id'] }}">
                                        Transaction {{ $transaction['numero'] }}
                                    </button>
                                </h2>
                                <div id="collapse-{{ $transaction['id'] }}" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <!-- Title Details -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Essence</label>
                                                    <select wire:model="transactions.{{ $index }}.essence_id"
                                                        wire:change="updateTitresForEssence({{ $index }}, $event.target.value)"
                                                        class="form-control">
                                                        <option value="">Sélectionner une essence</option>
                                                        @foreach ($essences as $essence)
                                                            <option value="{{ $essence['id'] }}">
                                                                {{ $essence['nom_local'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Titre</label>
                                                    <select wire:model="transactions.{{ $index }}.titre_id"
                                                        class="form-control">
                                                        <option value="">Sélectionner un titre</option>
                                                        @foreach ($transaction['titres'] as $titre)
                                                            <option value="{{ $titre['id'] }}">{{ $titre['nom'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold text-muted">Pays</label>
                                                    <input wire:model.defer="transactions.{{ $index }}.pays"
                                                        type="text" placeholder="Nigeria"
                                                        class="form-control @error('transactions.{{ $index }}.pays') is-invalid @enderror">
                                                    @error('transactions.{{ $index }}.pays')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold text-muted">Destination</label>
                                                    <input
                                                        wire:model.defer="transactions.{{ $index }}.destination"
                                                        type="text" placeholder="Maroua"
                                                        class="form-control @error('transactions.{{ $index }}.destination') is-invalid @enderror">
                                                    @error('transactions.{{ $index }}.destination')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Characteristics -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold text-muted">Société</label>
                                                    <select
                                                        wire:model.defer="transactions.{{ $index }}.societe_id"
                                                        class="form-control @error('transactions.{{ $index }}.societe_id') is-invalid @enderror">
                                                        <option value="">Sélectionner une société</option>
                                                        @foreach ($societes as $societe)
                                                            <option value="{{ $societe['id'] }}">
                                                                {{ $societe['acronym'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('transactions.{{ $index }}.societe_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Forme</label>
                                                    <select wire:model="transactions.{{ $index }}.forme_id"
                                                        wire:change="updateTypesForForme({{ $index }}, $event.target.value)"
                                                        class="form-control @error('transactions.' . $index . '.forme_id') is-invalid @enderror">
                                                        <option value="">Sélectionner une forme</option>
                                                        @foreach ($formes as $forme)
                                                            <option value="{{ $forme['id'] }}">
                                                                {{ $forme['designation'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('transactions.' . $index . '.forme_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Type</label>
                                                    <select wire:model="transactions.{{ $index }}.type_id"
                                                        class="form-control @error('transactions.' . $index . '.type_id') is-invalid @enderror">
                                                        <option value="">Sélectionner un type</option>
                                                        @foreach ($transaction['filteredTypes'] as $type)
                                                            <option value="{{ $type['id'] }}"
                                                                @selected($type['id'] == ($transaction['type_id'] ?? null))>
                                                                {{ $type['code'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('transactions.' . $index . '.type_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold text-muted">Conditionnement</label>
                                                    <select
                                                        wire:model.defer="transactions.{{ $index }}.conditionnemment_id"
                                                        class="form-control @error('transactions.{{ $index }}.conditionnemment_id') is-invalid @enderror">
                                                        <option value="">Sélectionner un conditionnement</option>
                                                        @foreach ($conditionnements as $conditionnement)
                                                            <option value="{{ $conditionnement['id'] }}">
                                                                {{ $conditionnement['code'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('transactions.{{ $index }}.conditionnemment_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="font-weight-bold text-muted">Volume (m³/Kg)</label>
                                                    <input type="number"
                                                        wire:model="transactions.{{ $index }}.volume"
                                                        placeholder="500"
                                                        class="form-control @error('transactions.{{ $index }}.volume') is-invalid @enderror"
                                                        step="0.00001">
                                                    @error('transactions.{{ $index }}.volume')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer border-top-0 text-right py-3">
                <button type="submit" class="btn btn-primary btn-lg px-5" style="background: green"
                    @if (collect($transactions)->pluck('errors')->flatten()->filter()->isNotEmpty()) disabled @endif>
                    <i class="fas fa-save mr-2"></i> Enregistrer toutes les transactions
                </button>
            </div>
        </div>
    </form>

    <!-- Depassement Modal -->
    @if ($showDepassementModal)
        <div class="modal-overlay" wire:ignore.self>
            <div class="modal-custom">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Avertissement de Dépassement
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p class="alert alert-warning">
                            <strong>Attention !</strong> Les transactions suivantes dépassent le volume disponible :
                        </p>
                        @foreach ($depassementDetails as $detail)
                            <div class="mb-3">
                                <h6>Transaction {{ $detail['transaction']['numero'] }}</h6>
                                <ul>
                                    <li>Volume en excès : <strong>{{ number_format($detail['depassement'], 2) }}
                                            m³</strong></li>
                                    <li>Volume initial restant :
                                        <strong>{{ number_format($detail['volumeRestant'], 2) }} m³</strong>
                                    </li>
                                </ul>
                            </div>
                        @endforeach
                        <p class="font-weight-bold">Êtes-vous sûr de vouloir poursuivre cette opération malgré le
                            dépassement ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" wire:click="closeDepassementModal">
                            Annuler
                        </button>
                        <button type="button" class="btn-confirm" wire:click="confirmSaveWithDepassement">
                            Confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif


</div>

<script>
    document.addEventListener('livewire:init', () => {

                Livewire.on('titresUpdated', (index) => {
                    // Force Livewire à mettre à jour le DOM
                    Livewire.dispatch('refreshComponent');
                });
                Livewire.on('redirectToList', () => {
                    window.location.href = "{{ route('admin.transaction.index') }}";
                });

                Livewire.on('typesUpdated', (index) => {
                    // Force Livewire à mettre à jour le DOM
                    Livewire.dispatch('refreshComponent');

                });
            }
</script>
