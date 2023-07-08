<?php foreach ($data as $gd) { ?>
    <div class="col-md-6">
        <div class="form-check">
            <input class="form-check-input" name="detail_group[]" type="checkbox" value="<?= $gd['id'] ?>">
            <label class="form-check-label">
                <?= $gd['detail'] ?>
            </label>
        </div>
    </div>
<?php } ?>