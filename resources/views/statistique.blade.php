<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Statistiques</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.ico">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
</head>
<body>

<ul class="tabs">
    <li class="tab-link current" data-tab="all-sales">Ventes Totales</li>
    <li class="tab-link" data-tab="by-category">Ventes par Catégorie</li>
</ul>

<div id="all-sales" class="tab-content current" style="width:80vw; height:60vh;">
    <x-chartjs-component :chart="$chart" />
</div>

<div id="by-category" class="tab-content" style="width:80vw; height:60vh;">
    <x-chartjs-component :chart="$chartByCategory" />
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

<style>
.tab-content { display:none; }
.tab-content.current { display:block; }
.tab-link.current { font-weight:bold; }
</style>
</body>
</html>