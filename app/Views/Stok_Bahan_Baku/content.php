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
                <th>Stok</th>
            </thead>
            <?php foreach ($data['barang'] as $a) {
                if (isset($data['stok'][$a['id']])) { ?>
                    <?php if (substr($a['code'], 0, 2) == "B1") { ?>
                        <tr>
                            <td class="">
                                <?= $a['tipe'] ?>
                            </td>
                            <td>
                                <?= strtoupper($a['brand'] . " " . $a['model']) ?><?= $a['product_name'] ?>
                            </td>
                            <td class="text-end">
                                <?= $data['stok'][$a['id']]['qty'] ?>
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
            "pageLength": 50,
            "scrollY": 600,
            "dom": "lfrti"
        });
    })

    var click = 0;
</script>