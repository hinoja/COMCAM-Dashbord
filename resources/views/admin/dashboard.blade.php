@extends('layouts.back')

@section('subtitle', __('Tableau de bord'))

@section('content')
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Tableau de bord - Statistiques {{ $year }}</h4>
                    <div class="d-flex align-items-center">
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center">
                            <select name="year" class="form-control mr-2" onchange="this.form.submit()">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                            <button type="submit" style="background: #63ed7a"  class="btn ">Filtrer</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistiques générales -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-primary text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Volume Total</h6>
                                            <h3 class="mt-2 mb-0">{{ number_format($stats['totalVolume'], 2, ',', ' ') }} m³</h3>
                                        </div>
                                        <i class="fas fa-chart-bar fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-success text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Transactions</h6>
                                            <h3 class="mt-2 mb-0">{{ number_format($stats['totalTransactions'], 0, ',', ' ') }}</h3>
                                        </div>
                                        <i class="fas fa-exchange-alt fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-info text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Titres</h6>
                                            {{-- <h3 class="mt-2 mb-0">{{ $stats['totalTitres'],}}</h3> --}}
                                            <h3 class="mt-2 mb-0">{{ number_format($stats['totalTitres'], 0, ',', ' ') }}</h3>
    <small class="text-muted">({{ number_format($stats['titresActifs'], 0, ',', ' ') }} actifs cette année)</small>
                                        </div>
                                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-warning text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Essences</h6>
                                            <h3 class="mt-2 mb-0">{{ number_format($stats['totalEssences'], 0, ',', ' ') }}</h3>
                                        </div>
                                        <i class="fas fa-tree fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card bg-danger text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Sociétés</h6>
                                            <h3 class="mt-2 mb-0">{{ number_format($stats['totalSocietes'], 0, ',', ' ') }}</h3>
                                        </div>
                                        <i class="fas fa-building fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card bg-secondary text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Destinations</h6>
                                            <h3 class="mt-2 mb-0">{{ number_format($stats['totalDestinations'], 0, ',', ' ') }}</h3>
                                        </div>
                                        <i class="fas fa-map-marker-alt fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card bg-dark text-white shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-0">Pays</h6>
                                            <h3 class="mt-2 mb-0">{{ number_format($stats['totalPays'], 0, ',', ' ') }}</h3>
                                        </div>
                                        <i class="fas fa-globe fa-3x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphiques -->
                    <div class="row">
                        <!-- Première ligne: Graphiques principaux -->
                        <div class="col-lg-8 mb-4">
                            <!-- Volume par mois -->
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Volume d'exportation mensuel ({{ $year }})</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-toggle="collapse" data-target="#collapseVolumeParMois" aria-expanded="true">
                                            <i class="fas fa-minus text-white"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse show" id="collapseVolumeParMois">
                                    <div class="card-body chart-container">
                                        <canvas id="volumeParMoisChart" height="220"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-4">
                            <!-- Évolution annuelle -->
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Évolution annuelle</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-toggle="collapse" data-target="#collapseEvolutionAnnuelle" aria-expanded="true">
                                            <i class="fas fa-minus text-white"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse show" id="collapseEvolutionAnnuelle">
                                    <div class="card-body chart-container">
                                        <canvas id="evolutionAnnuelleChart" height="220"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deuxième ligne: Graphiques de répartition -->
                        <div class="col-lg-4 mb-4">
                            <!-- Volume par forme -->
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Répartition par forme</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-toggle="collapse" data-target="#collapseVolumeParForme" aria-expanded="true">
                                            <i class="fas fa-minus text-white"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse show" id="collapseVolumeParForme">
                                    <div class="card-body chart-container">
                                        <canvas id="volumeParFormeChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-4">
                            <!-- Volume par conditionnement -->
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Répartition par conditionnement</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-toggle="collapse" data-target="#collapseVolumeParConditionnement" aria-expanded="true">
                                            <i class="fas fa-minus text-white"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse show" id="collapseVolumeParConditionnement">
                                    <div class="card-body chart-container">
                                        <canvas id="volumeParConditionnementChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-4">
                            <!-- Top 10 destinations -->
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Top 10 des destinations</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-toggle="collapse" data-target="#collapseTopDestinations" aria-expanded="true">
                                            <i class="fas fa-minus text-white"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse show" id="collapseTopDestinations">
                                    <div class="card-body chart-container">
                                        <canvas id="topDestinationsChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Troisième ligne: Top 10 -->
                        <div class="col-lg-6 mb-4">
                            <!-- Top 10 essences -->
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Top 10 des essences</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-toggle="collapse" data-target="#collapseTopEssences" aria-expanded="true">
                                            <i class="fas fa-minus text-white"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse show" id="collapseTopEssences">
                                    <div class="card-body chart-container">
                                        <canvas id="topEssencesChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <!-- Top 10 sociétés -->
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Top 10 des sociétés</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-toggle="collapse" data-target="#collapseTopSocietes" aria-expanded="true">
                                            <i class="fas fa-minus text-white"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse show" id="collapseTopSocietes">
                                    <div class="card-body chart-container">
                                        <canvas id="topSocietesChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .card {
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
    .card-header {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.125);
    }
    .card-body {
        padding: 1rem;
    }
    .chart-container {
        position: relative;
        height: 100%;
        width: 100%;
        min-height: 200px;
    }
    .opacity-50 {
        opacity: 0.5;
    }
    .btn-tool {
        background: transparent;
        color: white;
        border: none;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1;
        border-radius: 0.2rem;
    }
    .btn-tool:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    .btn-tool:focus {
        outline: none;
        box-shadow: none;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .col-lg-4, .col-lg-6, .col-lg-8 {
            margin-bottom: 1rem;
        }
        .chart-container {
            min-height: 180px;
        }
    }

    /* Custom scrollbar for better UX */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script>
    // Fonction pour gérer les boutons de réduction/agrandissement des cartes
    $(document).ready(function() {
        $('.btn-tool').on('click', function() {
            $(this).find('i').toggleClass('fa-minus fa-plus');
        });

        // Ajuster la hauteur des graphiques lors du redimensionnement de la fenêtre
        function resizeCharts() {
            $('.chart-container').each(function() {
                const width = $(this).width();
                // Ajuster la hauteur en fonction de la largeur pour maintenir un ratio agréable
                const height = Math.max(200, Math.min(300, width * 0.6));
                $(this).css('height', height + 'px');
            });
        }

        // Appeler la fonction au chargement et lors du redimensionnement
        resizeCharts();
        $(window).resize(resizeCharts);
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Configuration globale de Chart.js
        Chart.defaults.font.family = "'Nunito', 'Segoe UI', 'Arial'";
        Chart.defaults.font.size = 11;
        Chart.defaults.plugins.tooltip.padding = 8;
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.7)';
        Chart.defaults.plugins.tooltip.titleFont.size = 12;
        Chart.defaults.plugins.tooltip.titleFont.weight = 'bold';
        Chart.defaults.plugins.legend.position = 'bottom';
        Chart.defaults.plugins.legend.labels.boxWidth = 12;
        Chart.defaults.plugins.legend.labels.padding = 10;
        Chart.defaults.plugins.title.display = false;
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;

        // Données des graphiques
        const chartData = @json($chartData);

        // Volume par mois
        new Chart(document.getElementById('volumeParMoisChart'), {
            type: 'line',
            data: {
                labels: chartData.volumeParMois.labels,
                datasets: [{
                    label: 'Volume (m³)',
                    data: chartData.volumeParMois.data,
                    backgroundColor: 'rgba(78, 115, 223, 0.2)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: '#fff',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Volume (m³)',
                            font: {
                                size: 10
                            }
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10
                            },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });

        // Top 10 essences
        new Chart(document.getElementById('topEssencesChart'), {
            type: 'bar',
            data: {
                labels: chartData.topEssences.labels,
                datasets: [{
                    label: 'Volume (m³)',
                    data: chartData.topEssences.data,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    title: {
                        display: false
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

        // Top 10 sociétés
        new Chart(document.getElementById('topSocietesChart'), {
            type: 'bar',
            data: {
                labels: chartData.topSocietes.labels,
                datasets: [{
                    label: 'Volume (m³)',
                    data: chartData.topSocietes.data,
                    backgroundColor: 'rgba(23, 162, 184, 0.7)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    title: {
                        display: false
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

        // Top 10 destinations
        new Chart(document.getElementById('topDestinationsChart'), {
            type: 'bar',
            data: {
                labels: chartData.topDestinations.labels,
                datasets: [{
                    label: 'Volume (m³)',
                    data: chartData.topDestinations.data,
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false
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

        // Volume par forme
        new Chart(document.getElementById('volumeParFormeChart'), {
            type: 'pie',
            data: {
                labels: chartData.volumeParForme.labels,
                datasets: [{
                    data: chartData.volumeParForme.data,
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(23, 162, 184, 0.7)',
                        'rgba(108, 117, 125, 0.7)'
                    ],
                    borderColor: [
                        'rgba(220, 53, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%',
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value.toLocaleString('fr-FR')} m³ (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Volume par conditionnement
        new Chart(document.getElementById('volumeParConditionnementChart'), {
            type: 'doughnut',
            data: {
                labels: chartData.volumeParConditionnement.labels,
                datasets: [{
                    data: chartData.volumeParConditionnement.data,
                    backgroundColor: [
                        'rgba(108, 117, 125, 0.7)',
                        'rgba(0, 123, 255, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)'
                    ],
                    borderColor: [
                        'rgba(108, 117, 125, 1)',
                        'rgba(0, 123, 255, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value.toLocaleString('fr-FR')} m³ (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Évolution annuelle
        new Chart(document.getElementById('evolutionAnnuelleChart'), {
            type: 'bar',
            data: {
                labels: chartData.evolutionAnnuelle.labels,
                datasets: [{
                    label: 'Volume (m³)',
                    data: chartData.evolutionAnnuelle.data,
                    backgroundColor: 'rgba(52, 58, 64, 0.7)',
                    borderColor: 'rgba(52, 58, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Volume (m³)',
                            font: {
                                size: 10
                            }
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
