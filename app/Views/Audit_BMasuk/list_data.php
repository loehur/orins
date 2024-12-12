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
        <div class="row mb-2">
            <div class="col-auto mt-auto px-1 mb-2">
                <a href="<?= PV::BASE_URL ?>Audit_BMasuk"><button class="btn btn-outline pb-0 border-0"><i class="fa-solid fa-chevron-left"></i> <small>Back</small></button></a>
            </div>
            <div class="col-auto text-center px-1 mb-2">
                <label>Code Suppiler</label><br>
                <input name="supplier_c" id="supplier_c" value="<?= $d['id_sumber'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
            </div>
            <div class="col-auto px-1 mb-2 text-center">
                <label>Tanggal</label><br>
                <input type="date" name="tanggal" readonly class="text-center border-bottom border-0" value="<?= $d['tanggal'] ?>">
            </div>
            <div class="col-auto px-1 mb-2 text-end">
                <label>No. Faktur</label><br>
                <input class="text-end border-bottom border-0" value="<?= $d['no_faktur'] ?>" name="no_fak" readonly style="text-transform: uppercase;">
            </div>
            <div class="col-auto px-1 mb-2 text-end">
                <label>No. PO</label><br>
                <input class="text-end border-bottom border-0" value="<?= $d['no_po'] ?>" name="no_po" readonly style="text-transform: uppercase;">
            </div>
            <div class="col-auto px-1 mb-2">
                <div class="pt-4">
                    <input name="sds" class="form-check-input" type="checkbox" <?= $d['sds'] == 1 ? "checked" : "" ?> disabled>
                    <label class="form-check-label" for="flexCheckDefault">
                        SDS
                    </label>
                </div>
            </div>
            <div class="col text-end mt-auto">
                <?php if ($d['cek'] == 0) { ?>
                    <span data-ref="<?= $d['id'] ?>" style="cursor: pointer;" class="btn btn-outline-success update_bol"><i class="fa-solid fa-check"></i> Verify</span>
                <?php } else { ?>
                    VERIFIED
                <?php } ?>
            </div>
        </div>

        <table class="table table-sm mx-1 table-hover">
            <?php
            $no = 0;
            foreach ($data['mutasi'] as $a) {
                $no++; ?>
                <tr id="tr<?= $a['id'] ?>">
                    <td class="text-end">
                        <?= $no ?>
                    </td>
                    <td class="">
                        <?= $a['kode_barang'] ?>
                    </td>
                    <td class="">
                        <?= $data['barang_code'][$a['kode_barang']]['nama'] ?>
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
</script>