<?php $d = $data['input'] ?>

<form class="ajax" action="<?= PV::BASE_URL ?>Audit_BMasuk/update_surat" method="POST">
    <div class="row mb-2 mx-0">
        <input name="id" type="hidden" value="<?= $d['id'] ?>">
        <div class="col px-1 mb-2">
            <label class="text-primary text-sm" style="min-width: 250px;">Supplier</label><br>
            <input readonly class="w-100 border-bottom border-0" value="<?= $data['supplier'][$d['id_sumber']]['nama'] ?>" required name="view_sup" style="text-transform: uppercase;">
            <input readonly type="hidden" class="w-100 border-bottom border-0" value="<?= $d['id_sumber'] ?>" required name="supplier" style="text-transform: uppercase;">
        </div>
        <div class="col-auto px-1 mb-2">
            <label class="text-primary text-sm">Tanggal</label><br>
            <input readonly type="date" name="tanggal" value="<?= $d['tanggal'] ?>" class="text-center border-bottom border-0" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>">
        </div>
        <div class="col px-1 mb-2" style="min-width: 100px;">
            <label class="text-primary text-sm">No. DO</label><br>
            <input readonly class="w-100 border-bottom border-0" value="<?= $d['no_po'] ?>" required name="no_po" style="text-transform: uppercase;">
        </div>
        <div class="col px-1 mb-2">
            <label class="text-primary text-sm" style="min-width: 100px;">No. Faktur</label><br>
            <input class="w-100 border-bottom border-0" required value="<?= $d['no_faktur'] ?>" name="no_fak" style="text-transform: uppercase;">
        </div>
    </div>
    <div class="row mb-2 mx-0">
        <div class="col px-1 mb-2">
            <label class="text-primary text-sm">Note</label><br>
            <input readonly class="border-bottom border-0 w-100" required value="<?= $d['note'] ?>" name="note">
        </div>
        <div class="col-auto mt-auto mb-2 px-1">
            <button type="submit" class="btn btn-primary bg-gradient">Update</button>
        </div>
    </div>
</form>

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
                    location.reload(true)
                } else {
                    alert(result)
                }
            },
        });
    });
</script>