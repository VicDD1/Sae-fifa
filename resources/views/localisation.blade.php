<!DOCTYPE html>
<html>
<head>
    <title>Carte des ventes</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
 <link rel="stylesheet" href="{{ asset('css/statistique.css') }}">
    <style>
        #map {
            height: 800px;
            width: 100%;
        }
    </style>
</head>
<body>
<label for="yearSelect"><strong>Year:</strong></label>
<select id="yearSelect"></select>
<h2>Sales by Location</h2>
<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const map = L.map('map');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let allData = [];
    let markersLayer = L.layerGroup().addTo(map);

    fetch('/api/sales/localisation')
        .then(res => res.json())
        .then(data => {
            allData = data;

            // Get unique years
            const years = [...new Set(data.map(d => d.year))].sort();

            // Populate dropdown
            const yearSelect = document.getElementById('yearSelect');
            years.forEach(year => {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                yearSelect.appendChild(option);
            });

            // Default: first year
            drawMarkers(years[0]);

            yearSelect.addEventListener('change', e => {
                drawMarkers(e.target.value);
            });
        });

    function drawMarkers(selectedYear) {
        markersLayer.clearLayers();
        const bounds = L.latLngBounds([]);

        allData
            .filter(p => p.year == selectedYear)
            .forEach(point => {
                if (!point.latitude || !point.longitude) return;

                const lat = parseFloat(point.latitude);
                const lon = parseFloat(point.longitude);
                const amount = parseFloat(point.montant_regle) || 0;

                if (isNaN(lat) || isNaN(lon)) return;

                const color = amount > 1000 ? 'red' : '#ff7800';

                const marker = L.circleMarker([lat, lon], {
                    radius: Math.max(Math.sqrt(amount / 100), 4),
                    color: color,
                    fillOpacity: 0.7
                })
                .bindPopup(`
                    <strong>${point.ville_adresse}</strong><br>
                    Year: ${point.year}<br>
                    Sale: ${amount.toFixed(2)} €
                `);

                markersLayer.addLayer(marker);
                bounds.extend([lat, lon]);
            });

        if (bounds.isValid()) {
            map.fitBounds(bounds);
        } else {
            map.setView([46.5, 2.5], 6);
        }
    }
</script>

</body>
</html>
