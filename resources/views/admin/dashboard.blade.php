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
                        @livewire('statistique-dashboard')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    {{-- @livewireStyles --}}
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- @livewireScripts --}}

    <script>
        document.addEventListener('livewire:load', function() {
            let charts = {
                monthlyVolume: null,
                destinations: null,
                typeDistribution: null
            };

            function initializeCharts(data) {
                // Volume Mensuel
                if (charts.monthlyVolume) charts.monthlyVolume.destroy();
                const monthlyCtx = document.getElementById('monthlyVolumeChart');
                if (monthlyCtx) {
                    charts.monthlyVolume = new Chart(monthlyCtx, {
                        type: 'line',
                        data: {
                            labels: data.monthlyLabels || [],
                            datasets: [{
                                label: 'Volume mensuel',
                                data: data.monthlyData || [],
                                borderColor: '#28a745',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }

                // Destinations
                if (charts.destinations) charts.destinations.destroy();
                const destinationsCtx = document.getElementById('destinationsChart');
                if (destinationsCtx) {
                    charts.destinations = new Chart(destinationsCtx, {
                        type: 'bar',
                        data: {
                            labels: data.destinationsLabels || [],
                            datasets: [{
                                label: 'Top destinations',
                                data: data.destinationsData || [],
                                backgroundColor: '#17a2b8'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            }

            // Écouter les événements Livewire
            Livewire.on('statisticsUpdated', data => {
                initializeCharts(data);
            });

            // Initialisation au chargement
            if (typeof initialData !== 'undefined') {
                initializeCharts(initialData);
            }
        });
    </script>
@endpush

