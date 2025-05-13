<div>
    <!-- Graphiques -->
    <div class="row">
        <!-- Volume Mensuel -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Volume d'Exportation Mensuel</h5>
                </div>
                <div class="card-body">
                    <div id="monthlyChart"></div>
                </div>
            </div>
        </div>

        <!-- Top 10 Destinations -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Top 10 des Destinations</h5>
                </div>
                <div class="card-body">
                    <div id="destinationsChart"></div>
                </div>
            </div>
        </div>

        <!-- Top 10 Essences -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Top 10 des Essences</h5>
                </div>
                <div class="card-body">
                    <div id="essencesChart"></div>
                </div>
            </div>
        </div>

        <!-- Top 10 Exportateurs -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Top 10 des Exportateurs</h5>
                </div>
                <div class="card-body">
                    <div id="exportateursChart"></div>
                </div>
            </div>
        </div>

        <!-- Conditionnements -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Distribution des Conditionnements</h5>
                </div>
                <div class="card-body">
                    <div id="conditionnementChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:load', function() {
        // Options communes pour les graphiques en barres
        const commonBarOptions = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            fill: {
                opacity: 1
            }
        };

        // Graphique mensuel
        const monthlyOptions = {
            ...commonBarOptions,
            series: @json($monthlyChartData['series']),
            xaxis: @json($monthlyChartData['xaxis']),
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                }
            },
            colors: ['#28a745']
        };

        // Graphique des destinations
        const destinationsOptions = {
            ...commonBarOptions,
            series: @json($destinationsChartData['series']),
            xaxis: @json($destinationsChartData['xaxis']),
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                }
            },
            colors: ['#17a2b8']
        };

        // Graphique des essences
        const essencesOptions = {
            ...commonBarOptions,
            series: @json($essencesChartData['series']),
            xaxis: @json($essencesChartData['xaxis']),
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                }
            },
            colors: ['#007bff']
        };

        // Graphique des exportateurs
        const exportateursOptions = {
            ...commonBarOptions,
            series: @json($exportateursChartData['series']),
            xaxis: @json($exportateursChartData['xaxis']),
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                }
            },
            colors: ['#ffc107']
        };

        // Graphique des conditionnements (camembert)
        const conditionnementOptions = {
            chart: {
                type: 'pie',
                height: 350
            },
            series: @json($conditionnementChartData['series']),
            labels: @json($conditionnementChartData['labels']),
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            colors: ['#dc3545', '#fd7e14', '#6f42c1', '#20c997', '#6c757d']
        };

        // Initialisation des graphiques
        const monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), monthlyOptions);
        const destinationsChart = new ApexCharts(document.querySelector("#destinationsChart"), destinationsOptions);
        const essencesChart = new ApexCharts(document.querySelector("#essencesChart"), essencesOptions);
        const exportateursChart = new ApexCharts(document.querySelector("#exportateursChart"), exportateursOptions);
        const conditionnementChart = new ApexCharts(document.querySelector("#conditionnementChart"), conditionnementOptions);

        monthlyChart.render();
        destinationsChart.render();
        essencesChart.render();
        exportateursChart.render();
        conditionnementChart.render();

        // Mise à jour des graphiques lors des événements Livewire
        Livewire.on('updateCharts', data => {
            monthlyChart.updateOptions({
                series: data.monthlyChartData.series,
                xaxis: data.monthlyChartData.xaxis
            });

            destinationsChart.updateOptions({
                series: data.destinationsChartData.series,
                xaxis: data.destinationsChartData.xaxis
            });

            essencesChart.updateOptions({
                series: data.essencesChartData.series,
                xaxis: data.essencesChartData.xaxis
            });

            exportateursChart.updateOptions({
                series: data.exportateursChartData.series,
                xaxis: data.exportateursChartData.xaxis
            });

            conditionnementChart.updateOptions({
                series: data.conditionnementChartData.series,
                labels: data.conditionnementChartData.labels
            });
        });
    });
</script>
@endpush
