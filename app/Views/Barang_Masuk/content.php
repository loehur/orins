<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<style>
    td {
        align-content: center;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container">
        <table class="table table-sm text-sm">
            <tr>
                <th colspan="10">Pengecekan</th>
            </tr>
            <?php foreach ($data['input'] as $a) { ?>
                <tr>
                    <td class="align-middle">
                        <a href="<?= PV::BASE_URL ?>Barang_Masuk/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a>
                    </td>
                    <td>
                        <?= $a['id'] ?>
                    </td>
                    <td class="">
                        <?= isset($data['toko'][$a['id_sumber']]['nama_toko']) ? $data['toko'][$a['id_sumber']]['nama_toko'] : "Gudang" ?>
                    </td>
                    <td>
                        <?= $a['no_faktur'] ?>
                    </td>
                    <td>
                        <?= $a['no_po'] ?>
                    </td>
                    <td>
                        <?= $a['cek'] == 1 ? '<i class="fa-solid fa-check text-success"></i>' : "<span class='text-warning'>Checking</span>" ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <div class="overflow-auto" style="height: 700px;">
            <table class="table table-sm text-sm">
                <tr>
                    <th colspan="10">Terverifikasi</th>
                </tr>
                <?php foreach ($data['input_done'] as $a) { ?>
                    <tr>
                        <td class="align-middle">
                            <a href="<?= PV::BASE_URL ?>Barang_Masuk/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a>
                        </td>
                        <td>
                            <?= $a['id'] ?>
                        </td>
                        <td class="">
                            <?= isset($data['toko'][$a['id_sumber']]['nama_toko']) ? $data['toko'][$a['id_sumber']]['nama_toko'] : "Gudang" ?>
                        </td>
                        <td>
                            <?= $a['no_faktur'] ?>
                        </td>
                        <td>
                            <?= $a['no_po'] ?>
                        </td>
                        <td>
                            <?= $a['cek'] == 1 ? '<i class="fa-solid fa-check text-success"></i>' : "<span class='text-warning'>Checking</span>" ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
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