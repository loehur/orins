<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=500, user-scalable=no">
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Orins | Rekap Penjualan</title>
    <link href="<?= PV::ASSETS_URL ?>css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= PV::ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="<?= PV::ASSETS_URL ?>assets/img/favicon.png" />
    <script src="<?= PV::ASSETS_URL ?>js/feather.min.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="<?= PV::ASSETS_URL ?>plugins/fontawesome-free-6.4.0-web/css/all.css" rel="stylesheet">
    <link href="<?= PV::ASSETS_URL ?>plugins/toggle/css/bootstrap-toggle.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">
    <!-- FONT -->
    <?php $fontStyle = "'Titillium Web', sans-serif;" ?>
</head>

<style>
    html {
        height: 100%;
    }

    html .table {
        font-family: <?= $fontStyle ?>;
    }

    html .content {
        font-family: <?= $fontStyle ?>;
    }

    html body {
        font-family: <?= $fontStyle ?>;
    }

    main {
        margin-bottom: 20px;
    }

    .col-t {
        line-height: 100%;
    }
</style>

<main class="container mt-3 pb-3">
    <div class="row mb-2">
        <div class="col text-center">
            <?= $data['range']['from'] ?> s/d <?= $data['range']['to'] ?>
        </div>
    </div>
    <table class="table table-sm text-sm">
        <tr>
            <th>Item Produksi</th>
            <th class="text-end">Qty</th>
            <th class="text-end">Total</th>
        </tr>
        <?php
        foreach ($data['mutasi'] as $do) {
            $db = $data['barang'][$do['id_barang']] ?>
            <tr>
                <td><?= strtoupper($db['brand'] . " " . $db['model']) ?></td>
                <td class="text-end"><?= $do['qty'] ?></td>
                <td class="text-end"><?= number_format($do['jumlah']) ?></td>
            </tr>
        <?php }
        ?>
    </table>
</main>