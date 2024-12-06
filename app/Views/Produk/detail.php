<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />
<div class="col">
    <select name="detail_group[]" multiple class="tize border-0 w-100" required id="barang">
        <option></option>
        <?php foreach ($data as $gd) { ?>
            <option value="<?= $gd['id'] ?>"> <?= $gd['detail'] ?> <?= $gd['note'] <> "" ? "(" . $gd['note'] . ")" : "" ?></option>
        <?php } ?>
    </select>
</div>

<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });
</script>