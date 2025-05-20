<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/autocomplete.css" rel="stylesheet" />

<?php $d = $data['input']; ?>

<main>
    <!-- Main page content-->
    <div class="container">

        <div class="row">
            <div class="col-auto px-1 mb-2">
                <a href="<?= PV::BASE_URL ?>Audit_BMasuk"><button class="btn btn-outline pb-0 border-0"><i class="fa-solid fa-chevron-left"></i> <small>Back</small></button></a>
            </div>
        </div>

        <div class="border px-2 py-2 pb-0 shadow-sm">
            <table class="mb-2 text-sm mb-0">
                <tr>
                    <td>Tanggal</td>
                    <td>: <?= $d['tanggal'] ?></td>
                </tr>
                <tr>
                    <td class="pe-2">No. Faktur</td>
                    <td>: <?= $d['no_faktur'] ?></td>
                </tr>
                <tr>
                    <td>No. PO</td>
                    <td>: <?= $d['no_po'] ?></td>
                </tr>
                <tr>
                    <td>SDS</td>
                    <td>: <?= $d['sds'] == 1 ? "YES" : "NO" ?></td>
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
                                <span class="badge bg-success">VERIFIED</span> | <span class="text-danger reject_ref" data-ref="<?= $d['id'] ?>" style="cursor: pointer;">Reject</span>
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
                            <small><?= $no ?>.</small><br>
                            <span class="text-purple fw-500"><?= $a['qty'] ?>pcs</span>
                        </td>
                        <td class="">
                            <small><?= $data['barang'][$a['id_barang']]['code'] ?> <?= $data['barang'][$a['id_barang']]['code_myob'] ?><br></small>
                            <span class="text-purple fw-500"><?= $data['barang'][$a['id_barang']]['nama'] ?></span>
                        </td>
                        <td>
                            <?php
                            if ($a['sn_c'] == 1) {
                                if ($a['sn'] == "") { ?>
                                    <span class="text-danger">SN?</span>
                            <?php }
                            } ?>
                            <?= $a['sn'] ?>
                            <br>
                            <?php if ($a['stat'] == 0) { ?>
                                <span class="badge bg-warning">Checking</span>
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
        </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/autocomplete.js"></script>

<script>
    $(".update_bol").on('click', function() {
        var ref = $(this).attr('data-ref');
        $.ajax({
            url: '<?= PV::BASE_URL ?>Audit_BMasuk/update',
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

    $(".reject_ref").on('dblclick', function() {
        var ref = $(this).attr('data-ref');
        $.ajax({
            url: '<?= PV::BASE_URL ?>Audit_BMasuk/reject',
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