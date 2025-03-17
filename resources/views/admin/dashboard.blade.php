@extends('layouts.back')

@section('subtitle', __('Dashboard'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title text-primary">Dashboard</h2>
    </div>
    <div class="section-body mt-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12 col-md-12 col-12">
                    <div class="card shadow-lg">
                        @livewire('dashboard-titres')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .card {
            background-color: #e8f5e9;
            /* Vert clair pour le fond de la carte */
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .bg-gray-50 {
            background-color: #f8f9fc;
            /* Gris clair pour les statistiques */
        }

        .text-success {
            color: #1cc88a;
            /* Vert pour les volumes */
        }

        .shadow-md {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .shadow-lg {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        canvas {
            max-height: 300px;
            /* Hauteur maximale pour les graphiques */
        }
    </style>
@endpush

{{-- @push('js')
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
@endpush --}}
