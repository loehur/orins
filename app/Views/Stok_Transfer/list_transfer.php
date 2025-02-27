<table class="table table-sm mx-1 bg-light text-sm">
    <?php
    $no = 0;
    foreach ($data['mutasi'] as $a) {
        $no++; ?>
        <tr id="tr<?= $a['id'] ?>">
            <td class="text-end">
                <?= $no ?>#
            </td>
            <td class="">
                <?= $data['barang'][$a['id_barang']]['nama'] ?>
            </td>
            <td class="">
                <?= $a['sds'] == 0 ? "ABF" : "SDS" ?>
            </td>
            <td class="">
                <?= $a['sn'] == "" ? "NO-SN" : $a['sn'] ?>
            </td>
            <td class="text-end">
                <?= $a['qty'] ?>
            </td>
            <td class="align-middle text-end">
                <?php if ($a['stat'] == 0) { ?>
                    <span data-id="<?= $a['id'] ?>" data-primary="id" data-tb="master_mutasi" class="cell_delete text-danger" style="cursor: pointer;"><i class="fa-regular fa-trash-can"></i></span>
                <?php } else { ?>
                    <?php if ($a['stat'] == 1) { ?>
                        <span class="text-success"><i class="fa-solid fa-check"></i></span>
                    <?php } else { ?>
                        <span class="badge bg-danger">Rejected</span>
                    <?php } ?>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $(".cell_delete").dblclick(function() {
        var id = $(this).attr('data-id');
        var primary = $(this).attr('data-primary');
        var tb = $(this).attr('data-tb');

        $.ajax({
            url: '<?= PV::BASE_URL ?>Functions/deleteCell',
            data: {
                'id': id,
                'primary': primary,
                'tb': tb
            },
            type: 'POST',
            dataType: 'html',
            success: function(res) {
                if (res == 0) {
                    $("#tr" + id).remove();
                }
            },
        });
    });
</script>