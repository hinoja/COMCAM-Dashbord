<div class="max-w-7xl mx-auto p-6 bg-white rounded-xl shadow-xl card hover:shadow-2xl transition-all duration-300">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
        <h2 class="text-2xl font-bold text-gray-800 bg-clip-text bg-gradient-to-r from-purple-600 to-purple-800">
            Exportation de Bois au Port de Douala {{ $year }}
        </h2>
    </div>

    <!-- Statistiques globales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <p class="text-sm text-gray-600">Volume Total</p>
            <p class="text-xl font-semibold text-success">{{ number_format($totalVolume, 0, ',', ' ') }} m³</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <p class="text-sm text-gray-600">Nombre de Titres</p>
            <p class="text-xl font-semibold text-gray-800">{{ $totalTitres }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <p class="text-sm text-gray-600">Nombre de Transactions</p>
            <p class="text-xl font-semibold text-gray-800">{{ $totalTransactions }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <p class="text-sm text-gray-600">Nombre de Destinations</p>
            <p class="text-xl font-semibold text-gray-800">{{ $totalDestinations }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <p class="text-sm text-gray-600">Nombre d’Exportateurs</p>
            <p class="text-xl font-semibold text-gray-800">{{ $totalExportateurs }}</p>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Volume mensuel (Histogramme) -->
        <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Volume d’Exportation Mensuel</h3>
            <canvas id="monthlyExportChart" class="w-full h-64"></canvas>
        </div>

        <!-- % des conditionnements (Camembert) -->
        <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">% des Conditionnements</h3>
            <canvas id="conditionnementChart" class="w-full h-64"></canvas>
        </div>

        <!-- Top 10 des essences (Barres) -->
        <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 10 des Essences</h3>
            <canvas id="topEssencesChart" class="w-full h-64"></canvas>
        </div>

        <!-- Top 10 des exportateurs (Barres) -->
        <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 10 des Exportateurs</h3>
            <canvas id="topExportateursChart" class="w-full h-64"></canvas>
        </div>

        <!-- Top 10 des destinations (Barres) -->
        <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 10 des Destinations</h3>
            <canvas id="topDestinationsChart" class="w-full h-64"></canvas>
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@livewireScripts

<script>
    document.addEventListener('livewire:load', function() {
        // Données récupérées depuis Livewire
        const totalVolume = @json($totalVolume);
        const totalTitres = @json($totalTitres);
        const totalTransactions = @json($totalTransactions);
        const totalDestinations = @json($totalDestinations);
        const totalExportateurs = @json($totalExportateurs);
        const monthlyExportData = @json($monthlyExportVolume);
        const topExportateurs = @json($topExportateurs);
        const topEssences = @json($topEssences);
        const topDestinations = @json($topDestinations);
        const conditionnementDistribution = @json($conditionnementDistribution);

        // Graphique 1 : Statistiques globales (Barres)
        const ctx1 = document.getElementById('totalVolumeChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Volume Total (m³)', 'Titres', 'Transactions', 'Destinations', 'Exportateurs'],
                datasets: [{
                    label: 'Statistiques',
                    data: [totalVolume, totalTitres, totalTransactions, totalDestinations,
                        totalExportateurs
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Statistiques Globales',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Valeur'
                        }
                    }
                }
            }
        });

        // Graphique 2 : Volume Exportation Mensuel (Histogramme)
        const ctx2 = document.getElementById('monthlyExportChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: Object.keys(monthlyExportData),
                datasets: [{
                    label: 'Volume Exporté (m³)',
                    data: Object.values(monthlyExportData),
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Volume d’Exportation Mensuel',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Volume (m³)'
                        }
                    }
                }
            }
        });

        // Graphique 3 : Top 10 des Exportateurs (Barres horizontales)
        const ctx3 = document.getElementById('topExportateursChart').getContext('2d');
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: Object.keys(topExportateurs),
                datasets: [{
                    label: 'Volume Exporté (m³)',
                    data: Object.values(topExportateurs),
                    backgroundColor: '#1cc88a',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Barres horizontales
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Top 10 des Exportateurs',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Volume (m³)'
                        }
                    }
                }
            }
        });

        // Graphique 4 : Top 10 des Essences (Barres verticales)
        const ctx4 = document.getElementById('topEssencesChart').getContext('2d');
        new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: Object.keys(topEssences),
                datasets: [{
                    label: 'Volume Exporté (m³)',
                    data: Object.values(topEssences),
                    backgroundColor: '#36b9cc',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Top 10 des Essences',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Volume (m³)'
                        }
                    }
                }
            }
        });

        // Graphique 5 : Top 10 des Destinations (Barres verticales)
        const ctx5 = document.getElementById('topDestinationsChart').getContext('2d');
        new Chart(ctx5, {
            type: 'bar',
            data: {
                labels: Object.keys(topDestinations),
                datasets: [{
                    label: 'Volume Exporté (m³)',
                    data: Object.values(topDestinations),
                    backgroundColor: '#f6c23e',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Top 10 des Destinations',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Volume (m³)'
                        }
                    }
                }
            }
        });

        // Graphique 6 : % des Conditionnements (Camembert)
        const ctx6 = document.getElementById('conditionnementChart').getContext('2d');
        new Chart(ctx6, {
            type: 'pie',
            data: {
                labels: Object.keys(conditionnementDistribution),
                datasets: [{
                    data: Object.values(conditionnementDistribution),
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0',
                        '#9966ff'
                    ] // Couleurs pour plusieurs conditionnements
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: '% des Conditionnements',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
