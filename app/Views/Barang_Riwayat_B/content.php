<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<main>
    <!-- Main page content-->
    <div class="container">

        <div class="row mb-2 mx-0">
            <div class="col-auto px-1 mb-2">
                <div class="input-group">
                    <span class="input-group-text text-primary">Bulan</span>
                    <input name="month" id="month" type="month" min="2024-12" max="<?= date("Y-m-d") ?>" value="<?= date("Y-m") ?>" placeholder="YYYY-MM" class="form-control" required>
                    <button onclick="cek()" class="btn btn-primary">Cek</button>
                </div>
            </div>
            <div class="col">
                <select name="barang" class="tize border-0" id="barang">
                    <option value="">Pilih Barang</option>
                    <?php foreach ($data['barang'] as $key => $br) { ?>
                        <option value="<?= $key ?>"><?= trim($br['brand'] . " " . $br['model']) ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div id="data" class="bg-light mx-1 px-2">
        </div>
    </div>
</main>

<script src="<?= PV::ASSETS_URL ?>js/jquery-3.7.0.min.js"></script>
<script src="<?= PV::ASSETS_URL ?>js/selectize.min.js"></script>
<script>
    $(document).ready(function() {
        $('select.tize').selectize();
    });

    function cek() {
        var get = $("#month").val();
        if (get != "") {
            $('#data').load('<?= PV::BASE_URL ?>Barang_Riwayat_B/riwayat_data/0/' + get);
        }
    }

    $("#barang").change(function() {
        var get = $(this).val();
        if (get != "") {
            $('#data').load('<?= PV::BASE_URL ?>Barang_Riwayat_B/riwayat_data/1/' + get);
        }
    })
</script>