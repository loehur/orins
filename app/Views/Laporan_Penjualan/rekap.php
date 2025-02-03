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
                <th>Item Produksi</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <?php
        $total = 0;
        foreach ($data['order'] as $do) {
            $total += $do['jumlah']; ?>
            <tr>
                <td><?= $data['produk'][$do['id_produk']]['produk'] ?></td>
                <td class="text-end"><?= $do['qty'] ?></td>
                <td class="text-end"><?= number_format($do['jumlah']) ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td></td>
            <td></td>
            <td><?= number_format($total) ?></td>
        </tr>
    </table>
</main>

<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dt_tb').dataTable({
            "order": [],
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