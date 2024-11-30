<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<style>
    td {
        align-content: center;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container">
        <table class="table table-sm">
            <?php foreach ($data['input'] as $a) { ?>
                <tr>
                    <td class="align-middle">
                        <?php if ($a['cek'] == 0) { ?>
                            <span data-id="<?= $a['id'] ?>" data-col="cek" data-tb="master_input" data-val="1" data-primary="id" style="cursor: pointer;" class="btn btn-sm btn-outline-success update_bol"><i class="fa-solid fa-check"></i> Verify</span>
                        <?php } else { ?>
                            <a href="<?= PV::BASE_URL ?>Audit_BMasuk/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a>
                        <?php } ?>
                    </td>
                    <td>
                        <?= $a['id'] ?>
                    </td>
                    <td class="">
                        <?= $a['tanggal'] ?>
                    </td>
                    <td class="">
                        <?= strtoupper($a['supplier']) ?>
                    </td>
                    <td>
                        <?= $a['no_faktur'] ?>
                    </td>
                    <td>
                        <?= $a['no_po'] ?>
                    </td>
                    <td>
                        <?= $a['sds'] == 1 ? "SDS-<b>YES</b>" : "SDS-NO" ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
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