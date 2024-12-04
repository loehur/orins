<?php foreach ($data['stok'] as $d) { ?>
    <form action="<?= PV::BASE_URL ?>Stok_Transfer/add_mutasi/<?= $data['ref'] ?>" method="POST">
        <div class="row mb-2 mx-0">
            <div class="col-auto px-1 mb-2 text-center">
                <label class="fw-bold">SDS</label><br>
                <input type="hidden" name="sds" value="<?= $d['sds'] ?>">
                <input type="hidden" name="kode" value="<?= $d['kode_barang'] ?>">
                <input name="sds_" value="<?= $d['sds'] == 1 ? "YES" : "NO" ?>" readonly class="border-bottom border-0 text-center">
            </div>
            <div class="col px-1 mb-2">
                <label class="fw-bold">SN</label><br>
                <input type="hidden" name="sn" value="<?= $d['sn'] ?>" readonly class="border-bottom border-0 w-100">
                <input name="sn_" value="<?= $d['sn'] == "" ? "-" : $d['sn'] ?>" readonly class="border-bottom border-0 w-100">
            </div>
            <div class="col-auto text-center px-1 mb-2 text-end" id="col_qty">
                <label class="fw-bold">Tersedia</label><br>
                <input type="number" min="1" value="<?= $d['qty'] ?>" readonly class="text-center border-bottom border-0" name="qty" style="text-transform: uppercase;">
            </div>
            <div class="col-auto text-center px-1 mb-2 text-end" id="col_qty">
                <label class="fw-bold">Qty</label><br>
                <input id="qty" required type="number" min="1" value="<?= strlen($d['sn']) > 0 ? 1 : "" ?>" max="<?= $d['qty'] ?>" class="px-2 text-center border-bottom border-0" name="qty" style="text-transform: uppercase;">
            </div>
            <div class="col mt-auto mb-2">
                <button type="submit" class="btn btn-outline-success">Add</button>
            </div>
        </div>
    </form>
<?php } ?>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script>
    $("form").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            data: $(this).serialize(),
            type: $(this).attr("method"),
            success: function(result) {
                if (result == 0) {
                    content();
                } else {
                    alert(result)
                }
            },
        });
    });
</script>