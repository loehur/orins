<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<div class="mb-1">
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
    </div>
</div>
<div class="row mx-0 mb-3">
    <div class="col text-center px-0 m-auto" style="max-width: 100px;">
        <label for="qtyIn">Jumlah&nbsp;&nbsp;&nbsp;</label><br>
        <input type="number" min="1" value="1" name="jumlah" class="form-control float-end text-center border-0 shadow-none" id="qtyIn" required>
    </div>
</div>
<div class="row mx-0">
    <div class="col px-0 mb-2" style="min-width: 200px;">
        <input type="text" name="note" placeholder="Catatan Utama" class="form-control border-0 shadow-none form-control-sm">
    </div>
</div>
<?php foreach ($data['spkNote'] as $key => $d) { ?>
    <div class="row mx-0">
        <div class="col px-0 mb-2" style="min-width: 200px;">
            <input type="text" name="d-<?= $key ?>" placeholder="Catatan - <?= $data['divisi'][$key]['divisi'] ?>" class="form-control border-0 shadow-none form-control-sm">
        </div>
    </div>
<?php  } ?>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });
</script>