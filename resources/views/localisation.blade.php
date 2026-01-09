<!DOCTYPE html>
<html>
<head>
    <title>carte des ventes </title>
<link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        #map {
            height: 600px;
            width: 100%;
        }
    </style>
</head>
<body>

<h2>Sales by Location</h2>
<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const map = L.map('map').setView([46.5, 2.5], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

 fetch('/api/sales/localisation')
    .then(res => res.json())
    .then(data => {
        console.log("Total rows received:", data.length);

        data.forEach((point, index) => {
            // CRITICAL CHECK: Skip if lat or lon is null or undefined
            if (point.latitude === null || point.longitude === null) {
                return; // Skip this iteration and go to the next one
            }

            const lat = parseFloat(point.latitude);
            const lon = parseFloat(point.longitude);
            const amount = parseFloat(point.montant_regle);

            // Double check that the conversion to float worked
            if (!isNaN(lat) && !isNaN(lon)) {
                L.circleMarker([lat, lon], {
                    radius: Math.sqrt(amount) || 5,
                    color: '#ff7800',
                    fillOpacity: 0.7
                })
                .addTo(map)
                .bindPopup(`Ville: ${point.ville_adresse}<br>Vente: ${amount} €`);
            }
        });
    });
</script>


</body>
</html>
