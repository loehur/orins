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
                <input name="supplier_c" id="supplier_c" value="<?= $d['id_supplier'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
            </div>
            <div class="col-auto px-1 mb-2">
                <div class="autocomplete">
                    <label>Suppiler</label><br>
                    <input name="supplier" value="<?= $d['supplier'] ?>" class="border-bottom border-0" id="supplier" readonly style="text-transform: uppercase;">
                </div>
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
        </div>

        <table class="table table-sm mx-1 table-hover">
            <?php
            $no = 0;
            foreach ($data['mutasi'] as $a) {
                $no++; ?>
                <tr id="tr<?= $a['id'] ?>">
                    <td class="align-middle text-end">
                        <?php if ($a['stat'] == 0) {
                            if ($a['sn_c'] == 1) {
                                if ($a['sn'] == "") { ?>
                                    <span class="text-danger">Menunggu SN</span>
                                <?php } else { ?>
                                    <span style="cursor: pointer;" data-id="<?= $a['id'] ?>" data-col="stat" data-tb="master_mutasi" data-val="1" data-primary="id" class="update_bol btn btn-sm btn-outline-success"><i class="fa-solid fa-check"></i> Verify</span>
                                <?php }
                            } else { ?>
                                <span style="cursor: pointer;" data-id="<?= $a['id'] ?>" data-col="stat" data-tb="master_mutasi" data-val="1" data-primary="id" class="update_bol btn btn-sm btn-outline-success"><i class="fa-solid fa-check"></i> Verify</span>
                            <?php } ?>
                        <?php } else { ?>
                            <span class="text-success"><i class="fa-solid fa-check"></i> Verified</span>
                        <?php } ?>
                    </td>
                    <td class="text-end">
                        <?= $no ?>
                    </td>
                    <td class="">
                        <?= $a['kode_barang'] ?>
                    </td>
                    <td class="">
                        <?= $a['nama'] ?>
                    </td>
                    <td class="text-end">
                        <?= $a['qty'] ?>
                    </td>
                    <td>
                        <?= $a['sn'] ?>
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