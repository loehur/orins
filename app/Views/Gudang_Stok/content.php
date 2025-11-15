<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container text-sm">
        <a href="<?= PV::BASE_URL ?>Gudang_Stok/print" target="_blank"><span class="btn btn-sm btn-primary">Print Stok</span></a>
        <table id="tb_barang" class="hover text-sm stripe">
            <thead>
                <th>Barang</th>
                <th class="text-end">U</th>
                <th class="text-end">D</th>
                <th class="text-end">O</th>
                <th>Stok</th>
            </thead>
            <?php foreach ($data['barang'] as $a) {
                if (isset($data['stok'][$a['id']])) { ?>
                    <tr>
                        <td class="">
                            <small><?= strtoupper($a['grup'] . " " . $a['tipe']) ?></small><br>
                            <small class="fw-bold"><span class="text-success"><?= $a['id'] ?></span> <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?></small>
                        </td>
                        <td class="text-end align-top">
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_1" data-tb="master_barang"><?= $a['harga_1'] ?></span>
                        </td>
                        <td class="text-end align-top">
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_2" data-tb="master_barang"><?= $a['harga_2'] ?></span>
                        </td>
                        <td class="text-end align-top">
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_3" data-tb="master_barang"><?= $a['harga_3'] ?></span>
                        </td>
                        <td class="text-end align-top">
                            <?= isset($data['stok'][$a['id']]) ? number_format($data['stok'][$a['id']]['qty'], 0) : 0 ?>/<?= isset($data['stok_gudang'][$a['id']]) ? number_format($data['stok_gudang'][$a['id']]['qty'], 0) : 0 ?><br>
                            <span class="text-success"><?= isset($data['stok_sds'][$a['id'] . "#0"]) ? number_format($data['stok_sds'][$a['id'] . "#0"]['qty'], 0) : 0 ?>/<?= isset($data['stok_sds_gudang'][$a['id'] . "#0"]) ? number_format($data['stok_sds_gudang'][$a['id'] . "#0"]['qty'], 0) : 0 ?></span>
                            -<span class="text-primary"><?= isset($data['stok_sds'][$a['id'] . "#1"]) ? number_format($data['stok_sds'][$a['id'] . "#1"]['qty'], 0) : 0 ?>/<?= isset($data['stok_sds_gudang'][$a['id'] . "#1"]) ? number_format($data['stok_sds_gudang'][$a['id'] . "#1"]['qty'], 0) : 0 ?></span>
                        </td>
                    </tr>
                <?php } else { ?>
                    <?php
                    if (isset($data['stok_gudang'][$a['id']]) && $this->userData['id_toko'] == 1) { ?>
                        <tr>
                            <td class="">
                                <small><?= strtoupper($a['grup'] . " " . $a['tipe']) ?></small><br>
                                <small class="fw-bold"><span class="text-success"><?= $a['id'] ?></span> <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?></small>
                            </td>
                            <td class="text-end align-top">
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_1" data-tb="master_barang"><?= $a['harga_1'] ?></span>
                            </td>
                            <td class="text-end align-top">
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_2" data-tb="master_barang"><?= $a['harga_2'] ?></span>
                            </td>
                            <td class="text-end align-top">
                                <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_3" data-tb="master_barang"><?= $a['harga_3'] ?></span>
                            </td>
                            <td class="text-end align-top">
                                <?= isset($data['stok'][$a['id']]) ? number_format($data['stok'][$a['id']]['qty'], 0) : 0 ?>/<?= isset($data['stok_gudang'][$a['id']]) ? number_format($data['stok_gudang'][$a['id']]['qty'], 0) : 0 ?><br>
                                <span class="text-success"><?= isset($data['stok_sds'][$a['id'] . "#0"]) ? number_format($data['stok_sds'][$a['id'] . "#0"]['qty'], 0) : 0 ?>/<?= isset($data['stok_sds_gudang'][$a['id'] . "#0"]) ? number_format($data['stok_sds_gudang'][$a['id'] . "#0"]['qty'], 0) : 0 ?></span>
                                -<span class="text-primary"><?= isset($data['stok_sds'][$a['id'] . "#1"]) ? number_format($data['stok_sds'][$a['id'] . "#1"]['qty'], 0) : 0 ?>/<?= isset($data['stok_sds_gudang'][$a['id'] . "#1"]) ? number_format($data['stok_sds_gudang'][$a['id'] . "#1"]['qty'], 0) : 0 ?></span>
                            </td>
                        </tr>
                    <?php } ?>
            <?php }
            } ?>
        </table>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tb_barang').dataTable({
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "pageLength": -1,
            "scrollY": 600,
            "dom": "lfrti"
        });
    })

    var click = 0;
</script>