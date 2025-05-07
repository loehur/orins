<main>
    <!-- Main page content-->
    <div class="container">
        <form action="<?= PV::BASE_URL ?>Retur_Barang_C/add" method="POST">
            <div class="row mb-2 mx-0">
                <div class="col-auto px-1 mb-2">
                    <div class="autocomplete">
                        <label>Tujuan</label><br>
                        <input value="Gudang" readonly class="form-control form-control-sm">
                    </div>
                </div>
                <div class="col-auto px-1 mb-2">
                    <label>Note</label><br>
                    <input class="border-bottom border-0" name="note" maxlength="100" required>
                </div>
                <div class="col-auto px-1 mb-2 text-center">
                    <label>Tanggal</label><br>
                    <input type="date" name="tanggal" class="text-center border-bottom border-0" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>">
                </div>
                <div class="col mt-auto mb-2">
                    <button type="submit" class="btn btn-outline-success">Create</button>
                </div>
            </div>
        </form>

        <table class="table table-sm">
            <?php foreach ($data['input'] as $a) { ?>
                <tr>
                    <td class="align-middle">
                        <a href="<?= PV::BASE_URL ?>Retur_Barang_C/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a>
                    </td>
                    <td>
                        <?= $a['id'] ?>
                    </td>
                    <td class="">
                        <?= $a['note'] ?>
                    </td>
                    <td class="align-middle">
                        <?php if ($a['cek'] == 0) { ?>
                            <span class="text-warning"><i class="fa-regular fa-circle"></i> Checking</span>
                        <?php } else { ?>
                            <span class="text-success"><i class="fa-solid fa-check"></i></span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</main>
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