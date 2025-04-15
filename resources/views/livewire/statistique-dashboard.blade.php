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
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:load', function() {
        // Options communes
        const commonOptions = {
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
            ...commonOptions,
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
            ...commonOptions,
            series: @json($destinationsChartData['series']),
            xaxis: @json($destinationsChartData['xaxis']),
            yaxis: {
                title: {
                    text: 'Volume (m³)'
                }
            },
            colors: ['#17a2b8']
        };

        // Initialisation des graphiques
        const monthlyChart = new ApexCharts(document.querySelector("#monthlyChart"), monthlyOptions);
        const destinationsChart = new ApexCharts(document.querySelector("#destinationsChart"), destinationsOptions);

        monthlyChart.render();
        destinationsChart.render();

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
        });
    });
</script>
@endpush



