<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<?php $dPelanggan = $this->dPelanggan; ?>

<main>
    <div class="row mx-0 mt-3" style="margin-left:0px;max-width:600px">
        <div class="col pe-0">
            <select class="rounded tize" name="id_pelanggan" required>
                <option></option>
                <?php foreach ($data['list'] as $key => $lp) { ?>
                    <option value="<?= $key ?>" <?= ($data['id_pelanggan'] == $key ? "selected" : "") ?>><?= strtoupper($dPelanggan[$key]['nama']) ?> [ <?= number_format($lp) ?> ]</option>
                    <?php unset($dPelanggan[$key]); ?>
                <?php } ?>
                <?php foreach ($this->dPelanggan as $p) { ?>
                    <option value="<?= $p['id_pelanggan'] ?>" <?= ($data['id_pelanggan'] == $p['id_pelanggan'] ? "selected" : "") ?>><?= strtoupper($p['nama']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col pt-auto mt-auto pe-0">
            <button type="submit" class="cek btn btn-light border">Cek</button>
        </div>
    </div>
    <div id="data"></div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>

<script>
    $(document).ready(function() {
        $('select.tize').selectize();
        $('.cek').click(function() {
            id_pelanggan = $('select[name=id_pelanggan').val();
            if (id_pelanggan != '') {
                $('#data').load('<?= PV::BASE_URL ?>Deposit/dep_data/' + id_pelanggan);
            }
        })
    });
</script>