<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<main>
    <!-- Main page content-->
    <div class="container">
        <form action="<?= PV::BASE_URL ?>Gudang_Penjualan/add" method="POST">
            <div class="row mb-2 mx-0">
                <div class="col-auto px-1 mb-2">
                    <label>Tujuan</label><br>
                    <select name="tujuan" required class="border-0 tize" id="tujuan" style="text-transform: uppercase; width:200px">
                        <option></option>
                        <?php foreach ($data['tujuan'] as $tj) { ?>
                            <option value="<?= $tj['id_pelanggan'] ?>"><?= strtoupper($tj['nama']) ?></option>
                        <?php } ?>
                    </select>
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
                        <a href="<?= PV::BASE_URL ?>Gudang_Penjualan/list/<?= $a['id'] ?>"><i class="fa-solid fa-list-ol"></i></a>
                    </td>
                    <td>
                        <?= $a['id'] ?>
                    </td>
                    <td class="">
                        <?= $a['tanggal'] ?>
                    </td>
                    <td class="">
                        <?= strtoupper($data['tujuan'][$a['id_target']]['nama']) ?>
                    </td>
                    <td class="align-middle">
                        <?php if ($a['cek'] == 0) { ?>
                            <span class="text-warning"><i class="fa-regular fa-circle"></i> Checking</span>
                            <button type="button" class="btn btn-sm btn-outline-primary ms-2 btn-sinkron" data-ref="<?= $a['id'] ?>">
                                <i class="fa-solid fa-sync"></i> Sinkron
                            </button>
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
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

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

    $(document).on("click", ".btn-sinkron", function() {
        var ref = $(this).data('ref');
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "<?= PV::BASE_URL ?>Audit_BMasuk/reject",
            data: { ref: ref },
            type: "POST",
            success: function(result) {
                if (result == 0) {
                    content();
                } else {
                    alert(result);
                    btn.prop('disabled', false).html('<i class="fa-solid fa-sync"></i> Sinkron');
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
                btn.prop('disabled', false).html('<i class="fa-solid fa-sync"></i> Sinkron');
            }
        });
    });
</script>