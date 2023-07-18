<div class="mb-2">
    <div class="row pe-3">
        <?php foreach ($data['detail'] as $key => $d) {
        ?>
            <div class="col-md-6 pe-0 mb-1">
                <select class="tize" name="f-<?= $key ?>" required>
                    <option value=""><?= $d['name'] ?></option>
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
        <div class="col-md-6 mb-1">
            <input type="number" min="1" value="1" name="jumlah" class="form-control" required>
        </div>
    </div>
</div>
<div class="col px-0 mb-1">
    <input type="text" name="note" placeholder="Catatan Utama" class="form-control form-control-sm">
</div>
<?php foreach ($data['spkNote'] as $key => $d) { ?>
    <div class="col px-0 mb-1">
        <input type="text" name="d-<?= $key ?>" placeholder="Catatan - <?= $this->model('Arr')->get($this->dDvsAll, "id_divisi", "divisi", $key) ?>" class="form-control form-control-sm">
    </div>
<?php  } ?>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });
</script>