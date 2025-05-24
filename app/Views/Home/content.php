<header class="py-5 mb-3 bg-gradient-primary-to-secondary">
    <div class="text-center">
        <h1 class="text-white">Welcome to Orins PRO</h1>
        <p class="lead mb-0 text-white-75">Order Information System - Professional</p>
    </div>
</header>
<!-- Main page content-->
<div class="container-sm">
    <div class="row mx-1">
        <div class="col px-1 mb-2">
            <div class="card shadow-sm">
                <div class="card-header">CS Freq Top #5</div>
                <div class="card-body">
                    <canvas id="myChart" class="chartjs-render-monitor" style="display: block;"></canvas>
                </div>
            </div>
        </div>
        <div class="col px-1 mb-2">
            <div class="card shadow-sm">
                <div class="card-header">Production Freq Top #5</div>
                <div class="card-body">
                    <canvas id="myChart2" class="chartjs-render-monitor" style="display: block;"></canvas>
                </div>
            </div>
        </div>
        <div class="col px-1">
            <div class="card shadow-sm">
                <div class="card-header">Driver Freq Top #2</div>
                <div class="card-body">
                    <canvas id="myChart3" class="chartjs-render-monitor" style="display: block;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/cdn.jsdelivr.net_npm_chart.js"></script>
<script>
    $(document).ready(function() {
        const ctx = document.getElementById('myChart');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($data['cs']) ?>,
                datasets: [{
                    data: <?= json_encode($data['cs_data']) ?>,
                    borderWidth: 1
                }]
            },
        });

        const ctx2 = document.getElementById('myChart2');
        new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: <?= json_encode($data['pro']) ?>,
                datasets: [{
                    data: <?= json_encode($data['pro_data']) ?>,
                    borderWidth: 1
                }]
            },
        });

        const ctx3 = document.getElementById('myChart3');
        new Chart(ctx3, {
            type: 'pie',
            data: {
                labels: <?= json_encode($data['dr']) ?>,
                datasets: [{
                    data: <?= json_encode($data['dr_data']) ?>,
                    borderWidth: 1
                }]
            },
        });
    });
</script>