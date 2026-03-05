<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=500, user-scalable=no">
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Orins | Stok</title>
    <link href="<?= PV::ASSETS_URL ?>css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= PV::ASSETS_URL ?>plugins/bootstrap-5.1/bootstrap.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="<?= PV::ASSETS_URL ?>assets/img/favicon.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap" rel="stylesheet">
    <!-- FONT -->

    <?php $fontStyle = "'Titillium Web', sans-serif;" ?>

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
    </style>
</head>

<main>
    <!-- Main page content-->
    <div style="margin:auto; width: 190mm; font-family: system-ui;">
        <table class="text-sm" style="margin: auto;">
            <thead>
                <tr class="border-bottom">
                    <th class="text-start pe-1">Code</th>
                    <th class="text-start pe-1">Tipe</th>
                    <th class="text-start pe-1">Brand</th>
                    <th class="text-start pe-1">Model</th>
                    <th class="text-end">Stok T/G (sds0 sds1)</th>
                </tr>
            </thead>
            <?php foreach ($data['barang'] as $key => $g) { ?>
                <tr>
                    <td colspan="10" class="border-bottom pt-2"><small class="fw-bold"><?= $key ?></small></td>
                </tr>
                <?php foreach ($g as $a) {
                    if (isset($data['stok'][$a['id']])) { ?>
                        <tr>
                            <td class="pe-1">
                                <?= $a['code_myob'] ?>
                            </td>
                            <td class="pe-1">
                                <?= strtoupper($a['tipe']) <> "" ? strtoupper($a['tipe']) :  "PRODUKSI"  ?>
                            </td>
                            <td class="pe-1">
                                <?= strtoupper($a['brand']) <> "" ? strtoupper($a['brand']) : strtoupper($this->dToko[$this->userData['id_toko']]['inisial']) ?>
                            </td>
                            <td class="pe-1">
                                <?= strtoupper($a['model']) ?><?= $a['product_name'] ?>
                            </td>
                            <td class="text-end">
                                <?php
                                $q = isset($data['stok'][$a['id']]['qty']) ? (int)$data['stok'][$a['id']]['qty'] : 0;
                                $qg = isset($data['stok_gudang'][$a['id']]['qty']) ? (int)$data['stok_gudang'][$a['id']]['qty'] : 0;
                                $s0 = isset($data['stok_sds'][$a['id'] . '#0']['qty']) ? (int)$data['stok_sds'][$a['id'] . '#0']['qty'] : 0;
                                $s0g = isset($data['stok_sds_gudang'][$a['id'] . '#0']['qty']) ? (int)$data['stok_sds_gudang'][$a['id'] . '#0']['qty'] : 0;
                                $s1 = isset($data['stok_sds'][$a['id'] . '#1']['qty']) ? (int)$data['stok_sds'][$a['id'] . '#1']['qty'] : 0;
                                $s1g = isset($data['stok_sds_gudang'][$a['id'] . '#1']['qty']) ? (int)$data['stok_sds_gudang'][$a['id'] . '#1']['qty'] : 0;
                                echo number_format($q) . '/' . number_format($qg) . ' (' . number_format($s0) . '/' . number_format($s0g) . ' ' . number_format($s1) . '/' . number_format($s1g) . ')';
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </table>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $(document).ready(function() {
        window.print();
    });
</script>