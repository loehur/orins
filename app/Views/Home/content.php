<style>
    body {
        font: 400 1em/1.5 "Neuton";
        text-align: center;
        margin: 0;
    }

    p {
        letter-spacing: 0.1em;
    }

    p span.title {
        font: 1000 2em/1 "Oswald", sans-serif;
        letter-spacing: 0.2em;
        padding: 0.25em 0 0.1em;
        text-shadow: 0 0 80px rgba(255, 255, 255, 0.5);
        /* Clip Background Image */
        background: url(https://i.ibb.co/RDTnNrT/animated-text-fill.png) repeat-y;
        -webkit-background-clip: text;
        background-clip: text;
        /* Animate Background Image */
        -webkit-text-fill-color: transparent;
        -webkit-animation: aitf 80s linear infinite;
        /* Activate hardware acceleration for smoother animations */
        -webkit-transform: translate3d(0, 0, 0);
        -webkit-backface-visibility: hidden;
    }

    /* Animate Background Image */
    @-webkit-keyframes aitf {
        0% {
            background-position: 0% 50%;
        }

        100% {
            background-position: 100% 50%;
        }
    }

    /*
 * Animation module with all animation code
 */
    .anim-text-flow,
    .anim-text-flow-hover:hover {
        /*
   * Animation variables
   */
        /*
   * Elements settings
   */
        /*
   * Keyframe loop
   */
        /*
   * Element animation delay loop
   */
    }

    .anim-text-flow span,
    .anim-text-flow-hover:hover span {
        animation-name: anim-text-flow-keys;
        animation-duration: 50s;
        animation-iteration-count: infinite;
        animation-direction: alternate;
        animation-fill-mode: forwards;
    }

    @keyframes anim-text-flow-keys {
        0% {
            color: #a5d65c;
        }

        5% {
            color: #5cd66a;
        }

        10% {
            color: #5ca7d6;
        }

        15% {
            color: #915cd6;
        }

        20% {
            color: #5cd6a3;
        }

        25% {
            color: #5cd6b8;
        }

        30% {
            color: #d65cad;
        }

        35% {
            color: #d6665c;
        }

        40% {
            color: #915cd6;
        }

        45% {
            color: #5cd670;
        }

        50% {
            color: #5c85d6;
        }

        55% {
            color: #d6c85c;
        }

        60% {
            color: #d68f5c;
        }

        65% {
            color: #5cd6a9;
        }

        70% {
            color: #5cd6a5;
        }

        75% {
            color: #5c9dd6;
        }

        80% {
            color: #d6a35c;
        }

        85% {
            color: #a1d65c;
        }

        90% {
            color: #ce5cd6;
        }

        95% {
            color: #5c89d6;
        }

        100% {
            color: #d69b5c;
        }
    }

    .anim-text-flow span:nth-of-type(1),
    .anim-text-flow-hover:hover span:nth-of-type(1) {
        animation-delay: -19.8s;
    }

    .anim-text-flow span:nth-of-type(2),
    .anim-text-flow-hover:hover span:nth-of-type(2) {
        animation-delay: -19.6s;
    }

    .anim-text-flow span:nth-of-type(3),
    .anim-text-flow-hover:hover span:nth-of-type(3) {
        animation-delay: -19.4s;
    }

    .anim-text-flow span:nth-of-type(4),
    .anim-text-flow-hover:hover span:nth-of-type(4) {
        animation-delay: -19.2s;
    }

    .anim-text-flow span:nth-of-type(5),
    .anim-text-flow-hover:hover span:nth-of-type(5) {
        animation-delay: -19s;
    }

    .anim-text-flow span:nth-of-type(6),
    .anim-text-flow-hover:hover span:nth-of-type(6) {
        animation-delay: -18.8s;
    }

    .anim-text-flow span:nth-of-type(7),
    .anim-text-flow-hover:hover span:nth-of-type(7) {
        animation-delay: -18.6s;
    }

    .anim-text-flow span:nth-of-type(8),
    .anim-text-flow-hover:hover span:nth-of-type(8) {
        animation-delay: -18.4s;
    }

    .anim-text-flow span:nth-of-type(9),
    .anim-text-flow-hover:hover span:nth-of-type(9) {
        animation-delay: -18.2s;
    }

    .anim-text-flow span:nth-of-type(10),
    .anim-text-flow-hover:hover span:nth-of-type(10) {
        animation-delay: -18s;
    }

    .anim-text-flow span:nth-of-type(11),
    .anim-text-flow-hover:hover span:nth-of-type(11) {
        animation-delay: -17.8s;
    }

    .anim-text-flow span:nth-of-type(12),
    .anim-text-flow-hover:hover span:nth-of-type(12) {
        animation-delay: -17.6s;
    }

    .anim-text-flow span:nth-of-type(13),
    .anim-text-flow-hover:hover span:nth-of-type(13) {
        animation-delay: -17.4s;
    }

    .anim-text-flow span:nth-of-type(14),
    .anim-text-flow-hover:hover span:nth-of-type(14) {
        animation-delay: -17.2s;
    }

    .anim-text-flow span:nth-of-type(15),
    .anim-text-flow-hover:hover span:nth-of-type(15) {
        animation-delay: -17s;
    }

    .anim-text-flow span:nth-of-type(16),
    .anim-text-flow-hover:hover span:nth-of-type(16) {
        animation-delay: -16.8s;
    }

    .anim-text-flow span:nth-of-type(17),
    .anim-text-flow-hover:hover span:nth-of-type(17) {
        animation-delay: -16.6s;
    }

    .anim-text-flow span:nth-of-type(18),
    .anim-text-flow-hover:hover span:nth-of-type(18) {
        animation-delay: -16.4s;
    }

    .anim-text-flow span:nth-of-type(19),
    .anim-text-flow-hover:hover span:nth-of-type(19) {
        animation-delay: -16.2s;
    }

    .anim-text-flow span:nth-of-type(20),
    .anim-text-flow-hover:hover span:nth-of-type(20) {
        animation-delay: -16s;
    }

    .anim-text-flow span:nth-of-type(21),
    .anim-text-flow-hover:hover span:nth-of-type(21) {
        animation-delay: -15.8s;
    }

    .anim-text-flow span:nth-of-type(22),
    .anim-text-flow-hover:hover span:nth-of-type(22) {
        animation-delay: -15.6s;
    }

    .anim-text-flow span:nth-of-type(23),
    .anim-text-flow-hover:hover span:nth-of-type(23) {
        animation-delay: -15.4s;
    }

    .anim-text-flow span:nth-of-type(24),
    .anim-text-flow-hover:hover span:nth-of-type(24) {
        animation-delay: -15.2s;
    }

    .anim-text-flow span:nth-of-type(25),
    .anim-text-flow-hover:hover span:nth-of-type(25) {
        animation-delay: -15s;
    }

    .anim-text-flow span:nth-of-type(26),
    .anim-text-flow-hover:hover span:nth-of-type(26) {
        animation-delay: -14.8s;
    }

    .anim-text-flow span:nth-of-type(27),
    .anim-text-flow-hover:hover span:nth-of-type(27) {
        animation-delay: -14.6s;
    }

    .anim-text-flow span:nth-of-type(28),
    .anim-text-flow-hover:hover span:nth-of-type(28) {
        animation-delay: -14.4s;
    }

    .anim-text-flow span:nth-of-type(29),
    .anim-text-flow-hover:hover span:nth-of-type(29) {
        animation-delay: -14.2s;
    }

    .anim-text-flow span:nth-of-type(30),
    .anim-text-flow-hover:hover span:nth-of-type(30) {
        animation-delay: -14s;
    }

    .anim-text-flow span:nth-of-type(31),
    .anim-text-flow-hover:hover span:nth-of-type(31) {
        animation-delay: -13.8s;
    }

    .anim-text-flow span:nth-of-type(32),
    .anim-text-flow-hover:hover span:nth-of-type(32) {
        animation-delay: -13.6s;
    }

    .anim-text-flow span:nth-of-type(33),
    .anim-text-flow-hover:hover span:nth-of-type(33) {
        animation-delay: -13.4s;
    }

    .anim-text-flow span:nth-of-type(34),
    .anim-text-flow-hover:hover span:nth-of-type(34) {
        animation-delay: -13.2s;
    }

    .anim-text-flow span:nth-of-type(35),
    .anim-text-flow-hover:hover span:nth-of-type(35) {
        animation-delay: -13s;
    }

    .anim-text-flow span:nth-of-type(36),
    .anim-text-flow-hover:hover span:nth-of-type(36) {
        animation-delay: -12.8s;
    }

    .anim-text-flow span:nth-of-type(37),
    .anim-text-flow-hover:hover span:nth-of-type(37) {
        animation-delay: -12.6s;
    }

    .anim-text-flow span:nth-of-type(38),
    .anim-text-flow-hover:hover span:nth-of-type(38) {
        animation-delay: -12.4s;
    }

    .anim-text-flow span:nth-of-type(39),
    .anim-text-flow-hover:hover span:nth-of-type(39) {
        animation-delay: -12.2s;
    }

    .anim-text-flow span:nth-of-type(40),
    .anim-text-flow-hover:hover span:nth-of-type(40) {
        animation-delay: -12s;
    }

    .anim-text-flow span:nth-of-type(41),
    .anim-text-flow-hover:hover span:nth-of-type(41) {
        animation-delay: -11.8s;
    }

    .anim-text-flow span:nth-of-type(42),
    .anim-text-flow-hover:hover span:nth-of-type(42) {
        animation-delay: -11.6s;
    }

    .anim-text-flow span:nth-of-type(43),
    .anim-text-flow-hover:hover span:nth-of-type(43) {
        animation-delay: -11.4s;
    }

    .anim-text-flow span:nth-of-type(44),
    .anim-text-flow-hover:hover span:nth-of-type(44) {
        animation-delay: -11.2s;
    }

    .anim-text-flow span:nth-of-type(45),
    .anim-text-flow-hover:hover span:nth-of-type(45) {
        animation-delay: -11s;
    }

    .anim-text-flow span:nth-of-type(46),
    .anim-text-flow-hover:hover span:nth-of-type(46) {
        animation-delay: -10.8s;
    }

    .anim-text-flow span:nth-of-type(47),
    .anim-text-flow-hover:hover span:nth-of-type(47) {
        animation-delay: -10.6s;
    }

    .anim-text-flow span:nth-of-type(48),
    .anim-text-flow-hover:hover span:nth-of-type(48) {
        animation-delay: -10.4s;
    }

    .anim-text-flow span:nth-of-type(49),
    .anim-text-flow-hover:hover span:nth-of-type(49) {
        animation-delay: -10.2s;
    }

    .anim-text-flow span:nth-of-type(50),
    .anim-text-flow-hover:hover span:nth-of-type(50) {
        animation-delay: -10s;
    }

    .anim-text-flow span:nth-of-type(51),
    .anim-text-flow-hover:hover span:nth-of-type(51) {
        animation-delay: -9.8s;
    }

    .anim-text-flow span:nth-of-type(52),
    .anim-text-flow-hover:hover span:nth-of-type(52) {
        animation-delay: -9.6s;
    }

    .anim-text-flow span:nth-of-type(53),
    .anim-text-flow-hover:hover span:nth-of-type(53) {
        animation-delay: -9.4s;
    }

    .anim-text-flow span:nth-of-type(54),
    .anim-text-flow-hover:hover span:nth-of-type(54) {
        animation-delay: -9.2s;
    }

    .anim-text-flow span:nth-of-type(55),
    .anim-text-flow-hover:hover span:nth-of-type(55) {
        animation-delay: -9s;
    }

    .anim-text-flow span:nth-of-type(56),
    .anim-text-flow-hover:hover span:nth-of-type(56) {
        animation-delay: -8.8s;
    }

    .anim-text-flow span:nth-of-type(57),
    .anim-text-flow-hover:hover span:nth-of-type(57) {
        animation-delay: -8.6s;
    }

    .anim-text-flow span:nth-of-type(58),
    .anim-text-flow-hover:hover span:nth-of-type(58) {
        animation-delay: -8.4s;
    }

    .anim-text-flow span:nth-of-type(59),
    .anim-text-flow-hover:hover span:nth-of-type(59) {
        animation-delay: -8.2s;
    }

    .anim-text-flow span:nth-of-type(60),
    .anim-text-flow-hover:hover span:nth-of-type(60) {
        animation-delay: -8s;
    }

    .anim-text-flow span:nth-of-type(61),
    .anim-text-flow-hover:hover span:nth-of-type(61) {
        animation-delay: -7.8s;
    }

    .anim-text-flow span:nth-of-type(62),
    .anim-text-flow-hover:hover span:nth-of-type(62) {
        animation-delay: -7.6s;
    }

    .anim-text-flow span:nth-of-type(63),
    .anim-text-flow-hover:hover span:nth-of-type(63) {
        animation-delay: -7.4s;
    }

    .anim-text-flow span:nth-of-type(64),
    .anim-text-flow-hover:hover span:nth-of-type(64) {
        animation-delay: -7.2s;
    }

    .anim-text-flow span:nth-of-type(65),
    .anim-text-flow-hover:hover span:nth-of-type(65) {
        animation-delay: -7s;
    }

    .anim-text-flow span:nth-of-type(66),
    .anim-text-flow-hover:hover span:nth-of-type(66) {
        animation-delay: -6.8s;
    }

    .anim-text-flow span:nth-of-type(67),
    .anim-text-flow-hover:hover span:nth-of-type(67) {
        animation-delay: -6.6s;
    }

    .anim-text-flow span:nth-of-type(68),
    .anim-text-flow-hover:hover span:nth-of-type(68) {
        animation-delay: -6.4s;
    }

    .anim-text-flow span:nth-of-type(69),
    .anim-text-flow-hover:hover span:nth-of-type(69) {
        animation-delay: -6.2s;
    }

    .anim-text-flow span:nth-of-type(70),
    .anim-text-flow-hover:hover span:nth-of-type(70) {
        animation-delay: -6s;
    }

    .anim-text-flow span:nth-of-type(71),
    .anim-text-flow-hover:hover span:nth-of-type(71) {
        animation-delay: -5.8s;
    }

    .anim-text-flow span:nth-of-type(72),
    .anim-text-flow-hover:hover span:nth-of-type(72) {
        animation-delay: -5.6s;
    }

    .anim-text-flow span:nth-of-type(73),
    .anim-text-flow-hover:hover span:nth-of-type(73) {
        animation-delay: -5.4s;
    }

    .anim-text-flow span:nth-of-type(74),
    .anim-text-flow-hover:hover span:nth-of-type(74) {
        animation-delay: -5.2s;
    }

    .anim-text-flow span:nth-of-type(75),
    .anim-text-flow-hover:hover span:nth-of-type(75) {
        animation-delay: -5s;
    }

    .anim-text-flow span:nth-of-type(76),
    .anim-text-flow-hover:hover span:nth-of-type(76) {
        animation-delay: -4.8s;
    }

    .anim-text-flow span:nth-of-type(77),
    .anim-text-flow-hover:hover span:nth-of-type(77) {
        animation-delay: -4.6s;
    }

    .anim-text-flow span:nth-of-type(78),
    .anim-text-flow-hover:hover span:nth-of-type(78) {
        animation-delay: -4.4s;
    }

    .anim-text-flow span:nth-of-type(79),
    .anim-text-flow-hover:hover span:nth-of-type(79) {
        animation-delay: -4.2s;
    }

    .anim-text-flow span:nth-of-type(80),
    .anim-text-flow-hover:hover span:nth-of-type(80) {
        animation-delay: -4s;
    }

    .anim-text-flow span:nth-of-type(81),
    .anim-text-flow-hover:hover span:nth-of-type(81) {
        animation-delay: -3.8s;
    }

    .anim-text-flow span:nth-of-type(82),
    .anim-text-flow-hover:hover span:nth-of-type(82) {
        animation-delay: -3.6s;
    }

    .anim-text-flow span:nth-of-type(83),
    .anim-text-flow-hover:hover span:nth-of-type(83) {
        animation-delay: -3.4s;
    }

    .anim-text-flow span:nth-of-type(84),
    .anim-text-flow-hover:hover span:nth-of-type(84) {
        animation-delay: -3.2s;
    }

    .anim-text-flow span:nth-of-type(85),
    .anim-text-flow-hover:hover span:nth-of-type(85) {
        animation-delay: -3s;
    }

    .anim-text-flow span:nth-of-type(86),
    .anim-text-flow-hover:hover span:nth-of-type(86) {
        animation-delay: -2.8s;
    }

    .anim-text-flow span:nth-of-type(87),
    .anim-text-flow-hover:hover span:nth-of-type(87) {
        animation-delay: -2.6s;
    }

    .anim-text-flow span:nth-of-type(88),
    .anim-text-flow-hover:hover span:nth-of-type(88) {
        animation-delay: -2.4s;
    }

    .anim-text-flow span:nth-of-type(89),
    .anim-text-flow-hover:hover span:nth-of-type(89) {
        animation-delay: -2.2s;
    }

    .anim-text-flow span:nth-of-type(90),
    .anim-text-flow-hover:hover span:nth-of-type(90) {
        animation-delay: -2s;
    }

    .anim-text-flow span:nth-of-type(91),
    .anim-text-flow-hover:hover span:nth-of-type(91) {
        animation-delay: -1.8s;
    }

    .anim-text-flow span:nth-of-type(92),
    .anim-text-flow-hover:hover span:nth-of-type(92) {
        animation-delay: -1.6s;
    }

    .anim-text-flow span:nth-of-type(93),
    .anim-text-flow-hover:hover span:nth-of-type(93) {
        animation-delay: -1.4s;
    }

    .anim-text-flow span:nth-of-type(94),
    .anim-text-flow-hover:hover span:nth-of-type(94) {
        animation-delay: -1.2s;
    }

    .anim-text-flow span:nth-of-type(95),
    .anim-text-flow-hover:hover span:nth-of-type(95) {
        animation-delay: -1s;
    }

    .anim-text-flow span:nth-of-type(96),
    .anim-text-flow-hover:hover span:nth-of-type(96) {
        animation-delay: -0.8s;
    }

    .anim-text-flow span:nth-of-type(97),
    .anim-text-flow-hover:hover span:nth-of-type(97) {
        animation-delay: -0.6s;
    }

    .anim-text-flow span:nth-of-type(98),
    .anim-text-flow-hover:hover span:nth-of-type(98) {
        animation-delay: -0.4s;
    }

    .anim-text-flow span:nth-of-type(99),
    .anim-text-flow-hover:hover span:nth-of-type(99) {
        animation-delay: -0.2s;
    }

    .anim-text-flow span:nth-of-type(100),
    .anim-text-flow-hover:hover span:nth-of-type(100) {
        animation-delay: 0s;
    }

    .txt {
        display: block;
    }
</style>

<div class="container py-5">
    <p>
        <span class='title'>
            ORINS PRO
        </span>
        <span class="txt anim-text-flow"> Order Information System Professional</span>
    </p>
</div>
<!-- Main page content-->
<div class="container pb-3">
    <div class="row mx-0">
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
                <div class="card-header">Delivery Freq Top #5</div>
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
    $('.txt').html(function(i, html) {
        var chars = $.trim(html).split("");

        return '<span>' + chars.join('</span><span>') + '</span>';
    });

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