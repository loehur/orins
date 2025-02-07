<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container text-sm">
        <table id="tb_barang" class="hover text-sm">
            <thead>
                <th>Head</th>
                <th>Nama</th>
                <th class="text-end">Umum</th>
                <th class="text-end">R/D</th>
                <th class="text-end">Olshop</th>
                <th>Stok</th>
            </thead>
            <?php foreach ($data['barang'] as $a) {
                if (isset($data['stok'][$a['id']])) { ?>
                    <tr>
                        <td class="">
                            <?= strtoupper($a['grup'] . " " . $a['tipe']) ?>
                        </td>
                        <td>
                            <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?>
                        </td>
                        <td class="text-end">
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_1" data-tb="master_barang"><?= $a['harga_1'] ?></span>
                        </td>
                        <td class="text-end">
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_2" data-tb="master_barang"><?= $a['harga_2'] ?></span>
                        </td>
                        <td class="text-end">
                            <span class="cell_edit" data-id="<?= $a['id'] ?>" data-primary="id" data-col="harga_3" data-tb="master_barang"><?= $a['harga_3'] ?></span>
                        </td>
                        <td class="text-end">
                            <?= $data['stok'][$a['id']]['qty'] ?>/<?= $data['stok_gudang'][$a['id']]['qty'] ?>
                        </td>
                    </tr>
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
            "pageLength": 50,
            "scrollY": 600,
            "dom": "lfrti"
        });
    })

    var click = 0;
</script>