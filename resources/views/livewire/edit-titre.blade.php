<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg">
    <!-- Header avec logo et titre -->
    <div class="flex items-center justify-between mb-6 border-b pb-4 relative overflow-hidden">
        <!-- Conteneur principal avec effet d'arrière-plan et animation -->
        <div
            class="flex items-center space-x-4 bg-gradient-to-r from-purple-100 to-purple-200 p-4 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg">
            <!-- Icône et titre -->
            <div class="flex items-center space-x-3">
                <i
                    class="fas fa-edit text-purple-600 text-3xl transform hover:scale-110 transition-transform duration-300"></i>
                <h2
                    class="ml-3 text-2xl font-bold text-gray-800 bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-purple-800 hover:text-purple-700 transition-colors duration-300">
                    Modification du Titre
                </h2>
            </div>
        </div>
    </div>

    @if($hasTransactions)
        <div class="alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Ce titre a des transactions associées. Certaines modifications sont restreintes.
            <br>
            Volume total des transactions : {{ number_format($totalTransactionVolume, 2) }} m³
        </div>
    @endif

    <form wire:submit.prevent="update" class="space-y-8 fade-in">
        @csrf

        <!-- Champs principaux -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2">
            <div class="row col-12">
                <div class="col-6">
                    <label class="block text-gray-700 font-medium mb-1">Exercice (Année)</label>
                    <select id="exercice" name="exercice" wire:model="exercice"
                        class="w-full p-3 border rounded-lg @error('exercice') is-invalid @enderror">
                        @php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 2;
                            $endYear = $currentYear + 3;
                        @endphp
                        @for ($year = $startYear; $year <= $endYear; $year++)
                            <option value="{{ $year }}"
                                {{ old('exercice', $exercice) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                    @error('exercice')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-6">
                    <label class="block text-gray-700 font-medium mb-1">Zone</label>
                    <select name="zone_id" wire:model="zone_id"
                        class="select-custom w-full p-3 border rounded-lg @error('zone_id') is-invalid @enderror"
                        required>
                        <option value="" disabled>Sélectionner une zone</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                        @endforeach
                    </select>
                    @error('zone_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row col-12">
                <div class="col-6">
                    <label class="block text-gray-700 font-medium mb-1">Nom</label>
                    <input type="text" name="nom" wire:model="nom"
                        class="w-full p-3 border rounded-lg @error('nom') is-invalid @enderror"
                        placeholder="UFA 07004 AAC 1" required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="md:col-span-2 col-6">
                    <label class="block text-gray-700 font-medium mb-1">Localisation</label>
                    <input type="text" name="localisation" wire:model="localisation"
                        class="w-full p-3 border rounded-lg @error('localisation') is-invalid @enderror"
                        placeholder="Nkondjock-nkongsamba" required>
                    @error('localisation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <br>
        <hr>

        <!-- Détails des Ressources -->
        <div class="flex items-center justify-between mb-4 px-3 py-2 bg-gray-50 rounded-t-lg shadow-sm">
            <!-- Titre -->
            <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                <i class="fas fa-tree text-green-500 mr-2"></i>
                Détails des Ressources
            </h3>

            <!-- Bouton d'ajout (désactivé si le titre a des transactions) -->
            @if(!$hasTransactions)
                <button type="button" wire:click="addDetail" style="color: white; background:green;"
                    class="flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-full transition-all duration-300 shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Ajouter
                </button>
            @endif
        </div>

        @foreach ($details as $index => $detail)
            <div class="grid grid-cols-0 md:grid-cols-4 gap-4 px-2 bg-gray-100 rounded-lg">
                <div class="row">
                    <div class="col-md-5">
                        <label>Sélectionner une essence</label>
                        <select wire:model="details.{{ $index }}.essence_id"
                            class="select-custom w-full p-3 border rounded-lg"
                            @if($hasTransactions && isset($detail['original_essence_id'])) disabled @endif>
                            <option value="" disabled selected>Sélectionner une essence</option>
                            @foreach ($essences as $essence)
                                <option value="{{ $essence->id }}">{{ $essence->nom_local }}</option>
                            @endforeach
                        </select>
                        @error('details.' . $index . '.essence_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mr-0">
                        <label>Sélectionner une forme</label>
                        <select wire:model="details.{{ $index }}.forme_id"
                            wire:change="resetType({{ $index }})"
                            class="select-custom w-full p-3 border rounded-lg"
                            @if($hasTransactions && isset($detail['original_essence_id'])) disabled @endif>
                            <option value="" disabled selected>Sélectionner une forme</option>
                            @foreach ($formes as $forme)
                                <option value="{{ $forme->id }}">{{ $forme->designation }}</option>
                            @endforeach
                        </select>
                        @error('details.' . $index . '.forme_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Modifier la section du select des types -->
                    <div class="col-md-3">
                        <label>Sélectionner un type</label>
                        <div class="relative">
                            <select wire:model="details.{{ $index }}.type_id"
                                class="select-custom w-full p-3 border rounded-lg pr-8"
                                wire:key="type-select-{{ $index }}-{{ $detail['forme_id'] }}"
                                @if($detail['forme_id'] == 1 || ($hasTransactions && isset($detail['original_essence_id']))) disabled @endif>
                                @if ($detail['forme_id'] == 1)
                                    <option value="1" selected>Non applicable</option>
                                @else
                                    <option value="" disabled selected>Sélectionner un type</option>
                                    @if ($detail['forme_id'] == 2)
                                        @foreach ($types->whereIn('id', [2, 3, 4, 5]) as $type)
                                            <option value="{{ $type->id }}">{{ $type->code }}</option>
                                        @endforeach
                                    @else
                                        @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->code }}</option>
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Volume avec unité et bouton de suppression -->
                <div class="flex items-center justify-between gap-4 mt-3">
                    <!-- Champ de saisie avec unité -->
                    <div class="flex-2 flex items-center">
                        <div class="relative flex-grow">
                            <input type="number" wire:model="details.{{ $index }}.volume"
                                class="w-48 p-3 border rounded-lg @error('details.' . $index . '.volume') border-red-500 @enderror focus:outline-none focus:ring-2 focus:ring-purple-500"
                                step="0.0000001" min="0" placeholder="50,023" required> m³
                        </div>
                        @error('details.' . $index . '.volume')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Bouton de suppression (désactivé si le titre a des transactions) -->
                    @if(!$hasTransactions || !isset($detail['original_essence_id']))
                        <button wire:click="removeDetail({{ $index }})" type="button"
                            class="p-3 ml-5 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-lg focus:outline-none transition-all duration-300">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endforeach

        <br><br>
        <!-- Tableau récapitulatif -->
        <h3 class="text-lg font-semibold text-gray-800 mb-4 px-3">Liste des Ressources</h3>
        <div class="col-12 px-3">
            <div class="overflow-x-auto">
                <table class="w-full border rounded-lg table-striped table-bordered table-hover shadow-lg bg-white">
                    <thead class="bg-gray-200 text-gray-700 uppercase text-sm font-semibold">
                        <tr>
                            <th class="p-4 text-left">Essence</th>
                            <th class="p-4 text-left">Forme</th>
                            <th class="p-4 text-left">Type</th>
                            <th class="p-4 text-left">Volume (m³)</th>
                            @if($hasTransactions)
                                <th class="p-4 text-left">Volume Restant (m³)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        @forelse ($details as $index => $detail)
                            <tr
                                class="{{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition-colors duration-200 border-b">
                                <td class="p-4">
                                    <span
                                        class="font-medium">{{ $essences->find($detail['essence_id'])->nom_local ?? 'N/A' }}</span>
                                </td>
                                <td class="p-4">{{ $formes->find($detail['forme_id'])->designation ?? 'N/A' }}</td>
                                <td class="p-4">{{ $types->find($detail['type_id'])->code ?? 'N/A' }}</td>
                                <td class="p-4">
                                    <span
                                        class="text-blue-600 font-medium">{{ number_format((float) $detail['volume'] ?? 0, 2, ',', ' ') }}
                                        m³</span>
                                </td>
                                @if($hasTransactions)
                                    <td class="p-4">
                                        <span
                                            class="text-green-600 font-medium">{{ number_format((float) $detail['volumeRestant'] ?? 0, 2, ',', ' ') }}
                                            m³</span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $hasTransactions ? 5 : 4 }}" class="p-4 text-center text-gray-500">Aucune ressource disponible
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="{{ $hasTransactions ? 3 : 3 }}" class="p-4 text-right font-semibold">Total Volume :</td>
                            <td class="p-4 font-semibold text-blue-700">
                                {{ number_format((float) collect($details)->sum('volume'), 2, ',', ' ') }} m³
                            </td>
                            @if($hasTransactions)
                                <td class="p-4 font-semibold text-green-700">
                                    {{ number_format((float) collect($details)->sum('volumeRestant'), 2, ',', ' ') }} m³
                                </td>
                            @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Bouton de soumission amélioré -->
        <div class="card-footer text-right py-3">
            <button style="background: green" type="submit"
                class="btn px-4 bg-green-600 hover:bg-green-700 text-white disabled:opacity-50"
                wire:loading.attr="disabled" wire:target="update">
                <span wire:loading.remove wire:target="update">
                    <i class="fas fa-save mr-1"></i> Mettre à jour
                </span>
                <span wire:loading wire:target="update">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Mise à jour...
                </span>
            </button>
        </div>
    </form>
</div>
