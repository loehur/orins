<div class="mb-2">
    <div class="row">
        <?php foreach ($data['detail'] as $key => $d) {
        ?>
            <div class="col-md-6 pb-2">
                <label class="form-label mb-0 pb-0"><?= $d['name'] ?></label>
                <select class="border tize" name="f-<?= $key ?>" required>
                    <option></option>
                    <?php foreach ($d['item'] as $i) {
                        if (isset($data['varian'][$i['id_detail_item']]) && count($data['varian'][$i['id_detail_item']]) > 0) {
                            foreach ($data['varian'][$i['id_detail_item']] as $vr) { ?>
                                <option value="<?= $i['id_detail_item'] ?>-<?= strtoupper($i['detail_item']) ?>#<?= $vr['id_varian'] ?>-<?= $vr['varian'] ?>"><?= strtoupper($i['detail_item']) ?>-<?= strtoupper($vr['varian']) ?></option>
                            <?php } ?>
                        <?php } else {
                        ?>
                            <option value="<?= $i['id_detail_item'] ?>-<?= strtoupper($i['detail_item']) ?>#"><?= strtoupper($i['detail_item']) ?></option>
                    <?php }
                    } ?>
                </select>
            </div>
        <?php  } ?>
        <div class="col-md-6 pb-2">
            <label class="form-label mb-0 pb-0" required>Jumlah</label>
            <input type="number" min="1" value="1" name="jumlah" class="form-control form-control-sm" required>
        </div>
    </div>
</div>
<div class="mb-2">
    <label class="form-label mb-0 pb-0" required>Catatan Utama</label>
    <input type="text" name="note" class="form-control form-control-sm">
</div>
<?php foreach ($data['spkNote'] as $key => $d) { ?>
    <div class="mb-2">
        <label class="form-label mb-0 pb-0" required>Catatan <b><?= $this->model('Arr')->get($this->dDvsAll, "id_divisi", "divisi", $key) ?></b></label>
        <input type="text" name="d-<?= $key ?>" class="form-control form-control-sm">
    </div>
<?php  } ?>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });
</script>