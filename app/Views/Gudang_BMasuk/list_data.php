<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<?php $d = $data['input']; ?>

<style>
    td {
        align-content: center;
    }
</style>

<main>
    <!-- Main page content-->
    <div class="container">
        <div class="border px-2 py-2 pb-0 shadow-sm">
            <table class="mb-2 text-sm mb-0">
                <tr>
                    <td>Tanggal</td>
                    <td>: <?= $d['tanggal'] ?></td>
                </tr>
                <tr>
                    <td>Note</td>
                    <td>: <?= $d['note'] ?></td>
                </tr>
                <tr>
                    <td>
                        Status
                    </td>
                    <td>:
                        <?php if ($d['cek'] == 0) { ?>
                            <span class="badge bg-warning">Checking</span> <span data-ref="<?= $d['id'] ?>" style="cursor: pointer;" class="btn p-0 text-success update_bol">Verify ?</span>
                        <?php } else { ?>
                            <?php if ($d['cek'] == 1) { ?>
                                <span class="badge bg-success">VERIFIED</span>
                            <?php } else { ?>
                                <span class="badge bg-danger">REJECTED</span>
                            <?php } ?>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="border 0 pb-0 shadow-sm mt-2">
            <table class="table table-sm text-sm my-0">
                <?php
                $no = 0;
                foreach ($data['mutasi'] as $a) {
                    $no++; ?>
                    <tr id="tr<?= $a['id'] ?>">
                        <td class="text-end">
                            <?= $no ?>
                        </td>
                        <td class="">
                            <?= $data['barang'][$a['id_barang']]['code'] ?>
                        </td>
                        <td class="">
                            <?= $data['barang'][$a['id_barang']]['nama'] ?>
                        </td>
                        <td>
                            <?php
                            if ($a['sn_c'] == 1) {
                                if ($a['sn'] == "") { ?>
                                    <span class="text-danger">Menunggu SN</span>
                            <?php }
                            } ?>
                            <?= $a['sn'] ?>
                        </td>
                        <td>
                            <?php if ($a['sds'] == 0) { ?>
                                <span class="badge bg-primary">TOKO</span>
                            <?php } else { ?>
                                <span class="badge bg-danger">SDS</span>
                            <?php } ?>
                        </td>
                        <td class="text-end">
                            <?= $a['qty'] ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/autocomplete.js"></script>

<script>
    $(".update_bol").on('click', function() {
        var ref = $(this).attr('data-ref');
        $.ajax({
            url: '<?= PV::BASE_URL ?>Gudang_BMasuk/update',
            data: {
                ref: ref
            },
            type: 'POST',
            dataType: 'html',
            success: function(res) {
                if (res == 0) {
                    content();
                } else {
                    alert(res);
                }
            },
        });
    });
</script>