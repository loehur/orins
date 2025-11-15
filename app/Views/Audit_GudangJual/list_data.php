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
            <div class="col-auto text-center px-1 mb-2">
                <label>Tujuan</label><br>
                <input name="sumber" value="<?= $data['toko'][$d['id_target']]['nama'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
            </div>
            <div class="col-auto text-center px-1 mb-2">
                <label>No. Ref</label><br>
                <input name="supplier_c" id="supplier_c" value="<?= $d['id'] ?>" readonly class="text-center border-bottom border-0" style="text-transform: uppercase; background-color:aliceblue">
            </div>
            <div class="col-auto px-1 mb-2 text-center">
                <label>Tanggal</label><br>
                <input type="date" name="tanggal" readonly class="text-center border-bottom border-0" value="<?= $d['tanggal'] ?>">
            </div>
            <div class="col text-end mt-auto">
                <?php if ($d['cek'] == 0) { ?>
                    <span data-ref="<?= $d['id'] ?>" style="cursor: pointer;" class="btn btn-outline-success update_bol"><i class="fa-solid fa-check"></i> Verify</span>
                <?php } else { ?>
                    CONFIRMED
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
                        <?= $data['barang'][$a['id_barang']]['code'] ?>&nbsp;
                        <?= $data['barang'][$a['id_barang']]['code_myob'] ?>
                    </td>
                    <td class="">
                        <?= $data['barang'][$a['id_barang']]['nama'] ?>
                        <?= $data['barang'][$a['id_barang']]['product_name'] ?>
                    </td>
                    <td class="text-end">
                        <?= $a['qty'] ?>
                    </td>
                    <td>
                        <?= $a['sn'] ?>
                    </td>
                    <td>
                        <?= $a['sds'] == 1 ? "SDS-YES" : "SDS-NO" ?>
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
            url: '<?= PV::BASE_URL ?>Barang_Masuk/update',
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