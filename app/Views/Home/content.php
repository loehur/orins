<header class="py-5 mb-4 bg-gradient-primary-to-secondary">
    <div class="container-xl px-4">
        <div class="text-center">
            <h1 class="text-white">Welcome to Orins PRO</h1>
            <p class="lead mb-0 text-white-75">Order Information System - Professional</p>
        </div>
    </div>
</header>
<!-- Main page content-->
<div class="container-sm d-none">
    <div class="row">
        <div class="col-auto">
            <div class="card">
                <div class="card-header">Monthly Performance</div>
                <div class="card-body">
                    <canvas id="myChart" width="400" class="chartjs-render-monitor" style="display: block;"></canvas>
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
            type: 'doughnut',
            data: {
                labels: ['Tuminah', 'Jamilah', 'Sutijah'],
                datasets: [{
                    data: [40, 45, 35],
                    borderWidth: 1
                }]
            },
        });
    });
</script>