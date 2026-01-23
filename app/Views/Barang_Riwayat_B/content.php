<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<main>
    <!-- Main page content-->
    <div class="container">
        <div class="row mb-2 mx-0">
            <div class="col px-1 mb-2">
                <label>Barang</label><br>
                <select name="barang" class="tize border-0" id="barang">
                    <option></option>
                    <?php foreach ($data['barang'] as $key => $br) {
                        $code_split = str_split($br['code'], 2); ?>
                        <option value="<?= $key ?>"><?= $br['code'] ?> <?= trim($br['brand'] . " " . $br['model']) ?></span></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-auto px-1 mb-2">
                <label>Serial Number</label><br>
                <input name="sn" id="sn" class="form-control form-control-sm">
            </div>
            <div class="col-auto px-1 mb-2">
                <label>&nbsp;</label><br>
                <span id="cek" class="btn btn-sm btn-success">Cek</span>
            </div>
        </div>
        <div id="data" class="bg-light mx-1 px-2"></div>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    $("#cek").click(function() {
        var get = $("#barang").val();
        var sn = $("#sn").val();
        if (get != "") {
            $('#data').load('<?= PV::BASE_URL ?>Barang_Riwayat_B/data/' + get + '/' + sn);
        }
    })
</script>