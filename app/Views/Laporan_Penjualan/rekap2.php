<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<main class="container mt-3 pb-3">
    <div class="row mb-0 mx-0">
        <div class="col">
            <small class="fw-bold"><?= $data['range']['from'] ?> s/d <?= $data['range']['to'] ?></small>
        </div>
    </div>
    <table class="text-sm" id="dt_tb">
        <thead>
            <tr>
                <th>Group</th>
                <th>Brand</th>
                <th>Barang</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <?php
        foreach ($data['mutasi'] as $do) {
            $db = $data['barang'][$do['id_barang']] ?>
            <tr>
                <td><?= $db['grup'] ?></td>
                <td><?= $db['brand'] ?></td>
                <td><?= strtoupper($db['product_name']) ?><?= $db['model'] ?></td>
                <td class="text-end"><?= $do['qty'] ?></td>
                <td class="text-end"><?= number_format($do['jumlah']) ?></td>
            </tr>
        <?php }
        ?>
    </table>
</main>

<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dt_tb').dataTable({
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "pageLength": 50,
            "scrollY": 560,
            "dom": "lfrti"
        });
    })
</script>