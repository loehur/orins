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
        $total = 0;
        foreach ($data['mutasi'] as $do) {
            $db = $data['barang'][$do['id_barang']];
            $total += $do['jumlah'] ?>
            <tr>
                <td><?= $db['grup'] ?></td>
                <td><?= $db['brand'] ?></td>
                <td><?= strtoupper($db['product_name']) ?><?= $db['model'] ?></td>
                <td class="text-end"><?= $do['qty'] ?></td>
                <td class="text-end"><?= number_format($do['jumlah']) ?></td>
            </tr>
        <?php } ?>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:right">Total:</th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</main>

<script src="<?= PV::ASSETS_URL ?>js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        new DataTable('#dt_tb', {
            "order": [],
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "pageLength": 50,
            "scrollY": 560,
            "dom": "lfrti",

            footerCallback: function(row, data, start, end, display) {
                let api = this.api();
                // Remove the formatting to get integer data for summation
                let intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i :
                        0;
                };

                // Total over all pages
                total = api
                    .column(4)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                qty = api
                    .column(3)
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Total over this page
                pageTotal = api
                    .column(4, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                pageQty = api
                    .column(3, {
                        page: 'current'
                    })
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Update footer
                api.column(3).footer().innerHTML =
                    pageQty + '/' + qty;

                api.column(4).footer().innerHTML =
                    pageTotal + '/' + total;

            }
        });
    })
</script>