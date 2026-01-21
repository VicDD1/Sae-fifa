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
        <div class="year-selector">
    <label for="categoryYear"><strong>Year:</strong></label>
    <select id="categoryYear">
        @foreach($categoryYears as $year)
            <option value="{{ $year }}">{{ $year }}</option>
        @endforeach
    </select>
</div>
        <div id="by-category" class="tab-content">
            <div class="chart-container">
                <x-chartjs-component :chart="$chartByCategory" class="chart-category" />
                <div id="category-legend"></div>
            </div>
        </div>
    </div>

    <script>
    let chartInstance = null;
    
    function buildCategoryLegend() {
        // Find the canvas
        let chartEl = document.querySelector('#by-category canvas');
        if (!chartEl) {
            chartEl = document.querySelector('.chart-category canvas');
        }
        if (!chartEl) {
            chartEl = document.querySelector('canvas[id*="MonthlySalesByCategory"]');
        }
        
        const legendContainer = document.getElementById('category-legend');
        
        if (!chartEl || !legendContainer) {
            return;
        }
        
        // Get the chart instance
        chartInstance = Chart.getChart(chartEl);
        
        if (chartInstance && chartInstance.data && chartInstance.data.datasets) {
            // Clear existing content
            legendContainer.innerHTML = '';
            
            // Build legend items
            chartInstance.data.datasets.forEach((dataset, index) => {
                const color = dataset.borderColor || dataset.backgroundColor;
                
                const item = document.createElement('div');
                item.className = 'legend-item';
                item.dataset.index = index;
                
                // Set initial state based on dataset visibility
                if (dataset.hidden) {
                    item.classList.add('legend-item-hidden');
                }
                
                const colorBox = document.createElement('span');
                colorBox.className = 'legend-color';
                colorBox.style.background = color;
                
                const label = document.createElement('span');
                label.className = 'legend-label';
                label.textContent = dataset.label;
                
                item.appendChild(colorBox);
                item.appendChild(label);
                
                // Add click handler to toggle visibility
                item.addEventListener('click', function() {
                    const datasetIndex = parseInt(this.dataset.index);
                    const meta = chartInstance.getDatasetMeta(datasetIndex);
                    
                    // Toggle visibility
                    meta.hidden = !meta.hidden;
                    
                    // Update legend item appearance
                    if (meta.hidden) {
                        this.classList.add('legend-item-hidden');
                    } else {
                        this.classList.remove('legend-item-hidden');
                    }
                    
                    // Update the chart
                    chartInstance.update();
                });
                
                legendContainer.appendChild(item);
            });
        }
    }
    
    // Tab switching
    document.querySelectorAll('.tab-link').forEach(link => {
        link.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            // Switch tabs
            document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('current'));
            document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('current'));
            document.getElementById(tabId).classList.add('current');
            this.classList.add('current');
            
            // Build legend for category tab
            if (tabId === 'by-category') {
                setTimeout(buildCategoryLegend, 100);
                setTimeout(buildCategoryLegend, 300);
            }
        });
    });
    
    // Build on page load
    window.addEventListener('load', () => {
        setTimeout(buildCategoryLegend, 500);
    });
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(buildCategoryLegend, 500);
        });
    } else {
        setTimeout(buildCategoryLegend, 500);
    }
    </script>
    <script>
    const categoryDataByYear = @json($categoryDataByYear);

    function updateCategoryChart(year) {
        if (!chartInstance) return;

        chartInstance.data.datasets = categoryDataByYear[year];
        chartInstance.update();

        // rebuild legend because datasets changed
        setTimeout(buildCategoryLegend, 100);
    }

    document.getElementById('categoryYear')?.addEventListener('change', function () {
        updateCategoryChart(this.value);
    });
</script>
</body>
</html>