<div class="max-w-4xl mx-auto p-2 bg-white rounded-xl shadow-xl card hover:shadow-2xl transition-all duration-300">
    <!-- Header avec logo et titre -->
    <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4 relative overflow-hidden group">
        <div
            class="flex items-center space-x-4 bg-gradient-primary p-4 rounded-xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center space-x-3">
                <i
                    class="fas fa-file-alt text-white text-3xl transform transition-transform duration-300 group-hover:scale-110"></i>
                <h2
                    class="ml-3 text-2xl font-bold text-white bg-clip-text bg-gradient-to-r from-white to-purple-200 transition-colors duration-300 group-hover:text-purple-300">
                    Édition d'un Titre
                </h2>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button
                class="p-2 bg-purple-100 text-purple-700 rounded-full hover:bg-purple-200 transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
    </div>

    <form wire:submit.prevent="update" class="space-y-6 fade-in" style="animation-delay: 0.2s;">
        @csrf

        <!-- Champs principaux -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-1">
                <div class="relative">
                    <label class="block text-gray-800 font-semibold mb-2 text-lg">Exercice (Année)</label>
                    <select id="exercice" name="exercice" wire:model="exercice"
                        class="form-control select2 w-full border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-300 @error('exercice') is-invalid @enderror"
                        data-placeholder="Sélectionner une année">
                        @php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 2;
                            $endYear = $currentYear + 3;
                        @endphp
                        @for ($year = $startYear; $year <= $endYear; $year++)
                            <option value="{{ $year }}" {{ $exercice == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                    <i
                        class="fas fa-calendar-alt absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                    @error('exercice')
                        <div class="text-danger text-sm mt-1 animate-pulse">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-span-1">
                <div class="relative">
                    <label class="block text-gray-800 font-semibold mb-2 text-lg">Zone</label>
                    <select name="zone_id" wire:model="zone_id"
                        class="form-control select2 w-full border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-300 @error('zone_id') is-invalid @enderror"
                        data-placeholder="Sélectionner une zone" required>
                        <option value=""></option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}" {{ $zone_id == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>
                    <i class="fas fa-globe absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                    @error('zone_id')
                        <div class="text-danger text-sm mt-1 animate-pulse">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-span-1">
                <div class="relative">
                    <label class="block text-gray-800 font-semibold mb-2 text-lg">Nom</label>
                    <input type="text" name="nom" wire:model="nom"
                        class="form-control w-full border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-300 @error('nom') is-invalid @enderror"
                        placeholder="UFA 07004 AAC 1" required>
                    <i class="fas fa-font absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                    @error('nom')
                        <div class="text-danger text-sm mt-1 animate-pulse">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-span-1">
                <div class="relative">
                    <label class="block text-gray-800 font-semibold mb-2 text-lg">Localisation</label>
                    <input type="text" name="localisation" wire:model="localisation"
                        class="form-control w-full border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-300 @error('localisation') is-invalid @enderror"
                        placeholder="Nkondjock-nkongsamba" required>
                    <i
                        class="fas fa-map-marker-alt absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                    @error('localisation')
                        <div class="text-danger text-sm mt-1 animate-pulse">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <hr class="my-6 border-t border-gray-300">

        <!-- Détails des Ressources (fixe, un seul ensemble) -->
        <div class="mb-4 px-3 py-2 bg-gray-100 rounded-xl shadow-lg card hover:shadow-xl transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center mb-4">
                <i class="fas fa-tree text-success animate-bounce-slow"></i>
                Détails des Ressources
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="col-span-1">
                    <div class="relative">
                        <label class="block text-gray-800 font-semibold mb-2 text-lg">Sélectionner une essence</label>
                        <select wire:model="essence_id"
                            class="form-control select2 w-full border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-300 @error('essence_id') is-invalid @enderror"
                            data-placeholder="Sélectionner une essence">
                            <option value=""></option>
                            @foreach ($essences as $essence)
                                <option value="{{ $essence->id }}"
                                    {{ $essence_id == $essence->id ? 'selected' : '' }}>
                                    {{ $essence->nom_local }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-leaf absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                        @error('essence_id')
                            <div class="text-danger text-sm mt-1 animate-pulse">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="relative">
                        <label class="block text-gray-800 font-semibold mb-2 text-lg">Sélectionner une forme</label>
                        <select wire:model="forme_id"
                            class="form-control select2 w-full border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-300 @error('forme_id') is-invalid @enderror"
                            data-placeholder="Sélectionner une forme">
                            <option value=""></option>
                            @foreach ($formes as $forme)
                                <option value="{{ $forme->id }}" {{ $forme_id == $forme->id ? 'selected' : '' }}>
                                    {{ $forme->designation }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-cube absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                        @error('forme_id')
                            <div class="text-danger text-sm mt-1 animate-pulse">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-1">
                    <div class="relative">
                        <label class="block text-gray-800 font-semibold mb-2 text-lg">Sélectionner un type</label>
                        <select wire:model="type_id"
                            class="form-control select2 w-full border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-300 @error('type_id') is-invalid @enderror"
                            data-placeholder="Sélectionner un type">
                            <option value=""></option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" {{ $type_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->code }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-cogs absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                        @error('type_id')
                            <div class="text-danger text-sm mt-1 animate-pulse">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-span-1 flex items-center gap-2">
                    <div class="flex-1 w-full">
                        <div class="relative">
                            <label class="block text-gray-800 font-semibold mb-2 text-lg">Volume</label>
                            <div class="flex items-center">
                                <input type="number" wire:model="volume"
                                    class="form-control w-full border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary transition-all duration-300 @error('volume') is-invalid @enderror"
                                    step="0.0000001" min="0" placeholder="50,023" required>
                                <span class="ml-2 text-gray-600">m³</span>
                            </div>
                            <i
                                class="fas fa-tachometer-alt absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                            @error('volume')
                                <div class="text-danger text-sm mt-1 animate-pulse">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer text-right py-4">
            <button type="submit"
                class="bg-gradient-success text-white px-6 py-3 rounded-xl shadow-md hover:shadow-xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center">
                <i class="fas fa-save mr-2"></i>
                Mettre à jour
            </button>
        </div>
    </form>
</div>
