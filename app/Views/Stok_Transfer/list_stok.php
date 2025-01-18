<?php foreach ($data['stok'] as $key => $d) { ?>
    <?php if ($d['qty'] > 0) { ?>
        <form id="<?= $key ?>" action="<?= PV::BASE_URL ?>Stok_Transfer/add_mutasi/<?= $data['ref'] ?>" method="POST">
            <div class="row mb-2 mx-1 text-sm border-bottom">
                <div class="col text-center px-1 mb-2 text-end" id="col_qty">
                    <input type="hidden" min="1" value="<?= $d['qty'] ?>" readonly class="text-center border-bottom border-0" name="qty" style="text-transform: uppercase;">
                    <span><?= $d['qty'] ?></span>
                </div>
                <div class="col-auto px-1 mb-2 text-center">
                    <input type="hidden" name="sds" value="<?= $d['sds'] ?>">
                    <input type="hidden" name="kode" value="<?= $d['id_barang'] ?>">
                    <input type="hidden" name="sds_" value="<?= $d['sds'] == 1 ? "SDS" : "" ?>" readonly class="border-bottom border-0 text-center">
                    <span><?= $d['sds'] == 1 ? "SDS" : "ABF" ?></span>
                </div>
                <div class="col-auto px-1 mb-2">
                    <input type="hidden" name="sn" value="<?= $d['sn'] ?>" readonly class="border-bottom border-0 w-100">
                    <input type="hidden" name="sn_" value="<?= $d['sn'] == "" ? "-" : $d['sn'] ?>" readonly class="border-bottom border-0 w-100">
                    <span><?= $d['sn'] == "" ? "-" : $d['sn'] ?></span>
                </div>
                <div class="col-auto text-end px-1 mb-2 text-end" id="col_qty">
                    <input id="qty" required type="number" min="1" value="<?= strlen($d['sn']) > 0 ? 1 : "" ?>" max="<?= $d['qty'] ?>" class="px-2 text-center border-bottom border-0" name="qty" style="text-transform: uppercase;">
                </div>
                <div class="col-auto pe-0 text-end mb-2">
                    <button type="submit" class="btn btn-sm btn-outline-success">Add</button>
                </div>
            </div>
        </form>
    <?php } ?>
<?php } ?>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $("form").on("submit", function(e) {
        e.preventDefault();
        var id = $(this).attr('id');
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(result) {
                if (result == 0) {
                    $("#" + id).remove();
                    $('#list_transfer').load('<?= PV::BASE_URL ?>Stok_Transfer/list_transfer/<?= $data['ref'] ?>');
                } else {
                    alert(result)
                }
            },
        });
    });
</script>