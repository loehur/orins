<link rel="stylesheet" href="<?= PV::ASSETS_URL ?>css/selectize.bootstrap3.min.css" rel="stylesheet" />

<main>
    <!-- Main page content-->
    <div class="container">

        <div class="row mb-2 mx-0">
            <div class="col px-1 mb-2">
                <label>Barang</label><br>
                <select name="barang" class="tize border-0" id="barang">
                    <option></option>
                    <?php foreach ($data['barang'] as $key => $br) { ?>
                        <option value="<?= $key ?>"><?= trim($br['brand'] . " " . $br['model']) ?></option>
                    <?php } ?>
                </select>
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

    $("#barang").change(function() {
        var get = $(this).val();
        if (get != "") {
            $('#data').load('<?= PV::BASE_URL ?>Barang_Riwayat/data/' + get);
        }
    })
</script>