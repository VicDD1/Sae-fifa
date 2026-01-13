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

<h2>Sales by Location</h2>
<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const map = L.map('map');

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    fetch('/api/sales/localisation')
        .then(res => res.json())
        .then(data => {
            console.log("Total rows received:", data.length);

            const bounds = L.latLngBounds([]);

            data.forEach(point => {
                if (!point.latitude || !point.longitude) return;

                const lat = parseFloat(point.latitude);
                const lon = parseFloat(point.longitude);
                const amount = parseFloat(point.montant_regle) || 0;

                if (isNaN(lat) || isNaN(lon)) return;

                // Optional: color by amount
                const color = amount > 1000 ? 'red' : '#ff7800';

                L.circleMarker([lat, lon], {
                    radius: Math.max(Math.sqrt(amount/100), 1), // minimum radius 5
                    color: color,
                    fillOpacity: 0.7
                })
                .addTo(map)
                .bindPopup(`Ville: ${point.ville_adresse}<br>Vente: ${amount.toFixed(2)} €`);

                bounds.extend([lat, lon]);
            });

            if (bounds.isValid()) {
                map.fitBounds(bounds); // auto-zoom and center
            } else {
                map.setView([46.5, 2.5], 6); // fallback
            }
        })
        .catch(err => console.error("Error fetching sales data:", err));
</script>

</body>
</html>
