<?php $d = $data['input'] ?>

<form class="ajax" action="<?= PV::BASE_URL ?>Gudang_Input/update" method="POST">
    <div class="row mb-2 mx-0">
        <input name="id" type="hidden" value="<?= $d['id'] ?>">
        <div class="col-auto px-1 mb-2">
            <label>Supplier</label><br>
            <select name="supplier" required class="border-0 tize" id="tujuan" style="text-transform: uppercase; width:200px">
                <option></option>
                <?php foreach ($data['supplier'] as $tj) { ?>
                    <option value="<?= $tj['id'] ?>" <?= $d['id_sumber'] == $tj['id'] ? "selected" : "" ?>><?= strtoupper($tj['nama']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-auto px-1 mb-2 text-center">
            <label>Tanggal</label><br>
            <input type="date" name="tanggal" value="<?= $d['tanggal'] ?>" class="text-center border-bottom border-0" value="<?= date('Y-m-d'); ?>" max="<?= date('Y-m-d'); ?>">
        </div>
        <div class="col-auto px-1 mb-2">
            <label>No. DO</label><br>
            <input class="border-bottom border-0" value="<?= $d['no_po'] ?>" required name="no_po" style="text-transform: uppercase;">
        </div>
        <div class="col-auto px-1 mb-2">
            <label>No. Faktur</label><br>
            <input class=" border-bottom border-0" required value="<?= $d['no_faktur'] ?>" name="no_fak" style="text-transform: uppercase;">
        </div>
    </div>
    <div class="row mb-2 mx-0">
        <div class="col px-1 mb-2">
            <label>Note</label><br>
            <input class="border-bottom border-0 w-100" required value="<?= $d['note'] ?>" name="note">
        </div>
        <div class="col-auto mt-auto mb-2 px-1">
            <button type="submit" class="btn btn-outline-primary">Update</button>
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