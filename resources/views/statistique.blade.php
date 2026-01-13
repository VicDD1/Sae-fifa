<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Statistiques</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/statistique.css') }}">
</head>
<body>
    <div class="container">
        <ul class="tabs">
            <li class="tab-link current" data-tab="all-sales">Ventes Totales</li>
            <li class="tab-link" data-tab="by-category">Ventes par Catégorie</li>
        </ul>

        <div id="all-sales" class="tab-content current">
            <div class="chart-container">
                <x-chartjs-component :chart="$chart" />
            </div>
        </div>

        <div id="by-category" class="tab-content">
            <div class="chart-container">
<x-chartjs-component :chart="$chartByCategory" :options="[
    'plugins' => [
        'legend' => [
            'display' => false // turn off canvas legend
        ],
    ],
    'responsive' => true,
    'maintainAspectRatio' => false,

]" class="chart-category" />
<div id="category-legend" class="custom-legend"></div>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.tab-link').forEach(link => {
        link.addEventListener('click', function() {
            const tab = this.dataset.tab;
            document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('current'));
            document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('current'));
            document.getElementById(tab).classList.add('current');
            this.classList.add('current');
        });
    });
    </script>
   <script>
document.querySelectorAll('.tab-link').forEach(link => {
    link.addEventListener('click', function() {
        const tabId = this.dataset.tab;
        
        // 1. Switch tabs
        document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('current'));
        document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('current'));
        document.getElementById(tabId).classList.add('current');
        this.classList.add('current');

        // 2. If we switched to the category tab, build the legend
        if (tabId === 'by-category') {
            const chartEl = document.querySelector('.chart-category canvas');
            const legendContainer = document.getElementById('category-legend');
            
            // Wait a tiny bit for the tab transition to finish
            setTimeout(() => {
                const chart = Chart.getChart(chartEl);
                if (chart && chart.data.datasets) {
                    legendContainer.innerHTML = chart.data.datasets.map((dataset) => {
                        const color = dataset.borderColor || dataset.backgroundColor;
                        return `
                            <div class="legend-item">
                                <span class="legend-color" style="background:${color}"></span>
                                <span class="legend-label">${dataset.label}</span>
                            </div>`;
                    }).join('');
                }
            }, 100);
        }
    });
});
</script>
</body>
</html>
