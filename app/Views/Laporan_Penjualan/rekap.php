<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/dataTables.dataTables.min.css" rel="stylesheet" />
<style>
    .dt-search {
        float: right !important;
    }
</style>

<main class="container mt-3 pb-3">
    <div class="row mb-2">
        <div class="col text-center">
            <?= $data['range']['from'] ?> s/d <?= $data['range']['to'] ?>
        </div>
    </div>
    <table class="table table-sm text-sm" id="dt_tb">
        <tr>
            <th>Item Produksi</th>
            <th class="text-end">Qty</th>
            <th class="text-end">Total</th>
        </tr>
        <?php
        foreach ($data['order'] as $do) { ?>
            <tr>
                <td><?= $data['produk'][$do['id_produk']]['produk'] ?></td>
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
        $('#tb_dt').dataTable({
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "pageLength": 50,
            "scrollY": 600,
            "dom": "lfrti"
        });
    })
</script>