<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<style>
    td {
        align-content: center;
    }
</style>

<main>
    <?php
    $total_setor = 0;
    ?>
    <!-- Main page content-->
    <div class="container">
        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-success">Setoran Pecahan</th>
            </tr>
            <?php foreach ($data['split'] as $a) {
                $total_setor += $a['jumlah']; ?>
                <tr>
                    <td class="align-middle">
                        <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                    </td>
                    <td>
                        <?= $a['ref'] ?>
                    </td>
                    <td class="text-end">
                        <?= number_format($a['jumlah']) ?>
                    </td>
                    <td class="text-end" style="width:70px">
                        <?= $a['st'] == 1 ? "VERIFIED" : "<span class='text-primary' style='cursor:pointer'>Verify</span>" ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10" class="text-danger">Pengeluaran</th>
            </tr>
            <?php foreach ($data['pengeluaran'] as $ref => $keluar) { ?>
                <?php
                $jumlah_keluar = 0;
                foreach ($keluar as $a) { ?>
                    <tr>
                        <td>
                            <?= date('d/m/y H:i', strtotime($a['insertTime'])) ?>
                        </td>
                        <td>
                            <?= $a['note'] ?>
                        </td>
                        <td class="text-end">
                            <?= number_format($a['jumlah']) ?>
                        </td>
                        <td class="text-end" style="width:200px">
                            <?= $a['status_setoran'] == 1 ? "VERIFIED" : "<span class='text-primary' style='cursor:pointer'>Verify</span>" ?>
                        </td>
                        <td class="text-end">
                            <?= $a['status_setoran'] == 1 ? "VERIFIED" : "<span class='text-dark'>Reimburse</span>" ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
        </table>
    </div>
    <div class="row mx-0">
        <div class="col text-end fw-bold pt-2">
            Total Saldo <?= number_format($total_setor) ?>
        </div>
        <div class="col"><span class="btn btn-primary">Setor</span></div>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>

<script>
    var click = 0;
    $(".update_bol").on('click', function() {
        var id = $(this).attr('data-id');
        var primary = $(this).attr('data-primary');
        var col = $(this).attr('data-col');
        var tb = $(this).attr('data-tb');
        var value = $(this).attr('data-val');;

        $.ajax({
            url: '<?= PV::BASE_URL ?>Functions/updateCell',
            data: {
                'id': id,
                'value': value,
                'col': col,
                'primary': primary,
                'tb': tb
            },
            type: 'POST',
            dataType: 'html',
            success: function(res) {
                if (res == 0) {
                    content();
                }
            },
        });
    });
</script>