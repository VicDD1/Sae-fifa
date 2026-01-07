<!DOCTYPE html>
<html>
<head>
    <title>Sales Map</title>

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
        if (!data.length) {
            console.info('No sales to display');
            return;
        }

        data.forEach(point => {
            L.circleMarker([point.latitude, point.longitude], {
                radius: Math.sqrt(point.total_sales) / 20,
                fillOpacity: 0.7
            })
            .addTo(map)
            .bindPopup(`Ventes: ${point.total_sales} €`);
        });
    });
</script>


</body>
</html>
